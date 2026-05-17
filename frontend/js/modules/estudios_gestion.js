/**
 * Gestión de Estudios Module - Hospital HIS
 */

window.Modules.estudios_gestion = {
  estudios: [],
  tipos: [],
  medicos: [],
  laboratorios: [],

  async init() {
    this.renderLayout();
    await Promise.all([this.loadTipos(), this.loadMedicos(), this.loadLaboratorios()]);
    this.loadData();
  },

  renderLayout() {
    const contentArea = document.getElementById("contentArea");
    contentArea.innerHTML = `
      <div class="card" style="margin-bottom: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
          <div style="flex: 1; max-width: 400px;">
            <input type="text" id="estudioSearch" class="form-group" style="margin-bottom: 0; width: 100%;" placeholder="🔍 Buscar por ID, paciente o estudio...">
          </div>
          <button id="addEstudioBtn" class="btn btn-primary">
            <span>+</span> Realizar Nuevo Estudio
          </button>
        </div>
      </div>
      
      <div id="estudioTableContainer" class="table-container"></div>
    `;

    document.getElementById("estudioSearch").addEventListener("input", (e) => this.filter(e.target.value));
    document.getElementById("addEstudioBtn").addEventListener("click", () => this.showModal());
  },

  async loadTipos() {
    try {
      const response = await fetch("../api/tipoestudios/listar_tipoestudios.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) this.tipos = res.data;
    } catch (error) {
      console.error("Error al cargar tipos de estudios:", error);
    }
  },

  async loadMedicos() {
    try {
      const response = await fetch("../api/medicos/listar_medicos.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) this.medicos = res.data;
    } catch (error) {
      console.error("Error al cargar médicos:", error);
    }
  },

  async loadLaboratorios() {
    try {
      const response = await fetch("../api/laboratorios/listar_laboratorios.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) this.laboratorios = res.data;
    } catch (error) {
      console.error("Error al cargar laboratorios:", error);
    }
  },

  async loadData() {
    try {
      UI.showSkeleton("#estudioTableContainer");
      const response = await fetch("../api/estudios/listar_estudios.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) {
        this.estudios = res.data;
        this.renderTable(this.estudios);
      }
    } catch (error) {
      UI.toast.show("Error al cargar estudios", "error");
    }
  },

  renderTable(data) {
    const container = document.getElementById("estudioTableContainer");
    if (data.length === 0) {
      container.innerHTML = `<div style="padding: 2rem; text-align: center; color: var(--text-light);">No hay estudios registrados.</div>`;
      return;
    }

    let html = `
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Estudio</th>
            <th>Médico Solicitante</th>
            <th>Fecha</th>
            <th>Estatus</th>
            <th style="text-align: right;">Acciones</th>
          </tr>
        </thead>
        <tbody>
    `;

    data.forEach(item => {
      const estatusClass = item.ESTATUS == 1 ? 'badge-primary' : 'badge-secondary';
      const estatusText = item.ESTATUS == 1 ? 'Completado' : 'Pendiente';

      html += `
        <tr>
          <td><span class="badge" style="background: var(--background); color: var(--text);">#${item.ID}</span></td>
          <td>
            <div style="font-weight: 600;">${item.NOMBREESTUDIO}</div>
            <div style="font-size: 0.75rem; color: var(--text-light);">${item.NOMBRELABORATORIO || ''}</div>
          </td>
          <td>${item.NOMBRE} ${item.APELLIDOPATERNO}</td>
          <td style="font-size: 0.85rem;">${new Date(item.FECHAESTUDIO).toLocaleString()}</td>
          <td><span class="badge ${estatusClass}" style="padding: 4px 10px;">${estatusText}</span></td>
          <td style="text-align: right;">
            <button class="btn btn-secondary btn-sm" onclick="Modules.estudios_gestion.showModal(${JSON.stringify(item).replace(/"/g, '&quot;')})">✏️</button>
            <button class="btn btn-secondary btn-sm" onclick="Modules.estudios_gestion.confirmDelete(${item.ID})">🗑️</button>
          </td>
        </tr>
      `;
    });

    html += `</tbody></table>`;
    container.innerHTML = html;
  },

  filter(query) {
    const q = query.toLowerCase();
    const filtered = this.estudios.filter(e => 
      e.ID.toString().includes(q) || 
      e.NOMBREESTUDIO.toLowerCase().includes(q) ||
      (e.NOMBRE + " " + e.APELLIDOPATERNO).toLowerCase().includes(q)
    );
    this.renderTable(filtered);
  },

  showModal(item = null) {
    const isEdit = item !== null;
    const title = isEdit ? "Editar Registro de Estudio" : "Registrar Nuevo Estudio";
    
    let labOptions = this.laboratorios.map(l => 
      `<option value="${l.ID}" ${isEdit && item.LABORATORIOS_ID == l.ID ? 'selected' : ''}>${l.NOMBRELABORATORIO}</option>`
    ).join('');

    let medicoOptions = this.medicos.map(m => 
      `<option value="${m.EXPEDIENTE}" ${isEdit && item.MEDICOS_EXPEDIENTE == m.EXPEDIENTE ? 'selected' : ''}>${m.NOMBRE} ${m.APELLIDOPATERNO} (${m.EXPEDIENTE})</option>`
    ).join('');

    const body = `
      <form id="estudioForm">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          ${isEdit ? `
          <div class="form-group">
            <label for="e_id">ID de Registro</label>
            <input type="number" id="e_id" value="${item.ID}" readonly>
          </div>` : ''}
          <div class="form-group" ${!isEdit ? 'style="grid-column: span 2;"' : ''}>
            <label for="e_estatus">Estatus</label>
            <select id="e_estatus">
              <option value="1" ${isEdit && item.ESTATUS == 1 ? 'selected' : ''}>Completado</option>
              <option value="2" ${isEdit && item.ESTATUS == 2 ? 'selected' : ''}>Pendiente / En Proceso</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="e_lab">Laboratorio</label>
          <select id="e_lab" required>
            <option value="">Seleccione un laboratorio...</option>
            ${labOptions}
          </select>
        </div>

        <div class="form-group">
          <label for="e_tipo">Tipo de Estudio</label>
          <select id="e_tipo" required disabled>
            <option value="">Primero seleccione un laboratorio...</option>
          </select>
        </div>

        <div class="form-group">
          <label for="e_medico">Médico Solicitante</label>
          <select id="e_medico" required>
            <option value="">Seleccione un médico...</option>
            ${medicoOptions}
          </select>
        </div>

        <div class="form-group">
          <label for="e_fecha">Fecha y Hora</label>
          <input type="datetime-local" id="e_fecha" value="${item ? item.FECHAESTUDIO.replace(' ', 'T') : new Date().toISOString().slice(0, 16)}" required>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
      <button class="btn btn-primary" id="saveEstudioBtn">${isEdit ? 'Actualizar' : 'Registrar'}</button>
    `;

    UI.modal.show(title, body, footer);

    const labSelect = document.getElementById("e_lab");
    const tipoSelect = document.getElementById("e_tipo");

    const updateTipoOptions = (labId, selectedTipoId = null) => {
      if (!labId) {
        tipoSelect.innerHTML = '<option value="">Primero seleccione un laboratorio...</option>';
        tipoSelect.disabled = true;
        return;
      }

      const filteredTipos = this.tipos.filter(t => t.LABORATORIOS_ID == labId);
      
      if (filteredTipos.length === 0) {
        tipoSelect.innerHTML = '<option value="">No hay estudios en este lab...</option>';
        tipoSelect.disabled = true;
      } else {
        tipoSelect.innerHTML = '<option value="">Seleccione un estudio...</option>' + 
          filteredTipos.map(t => `<option value="${t.ID}" ${selectedTipoId == t.ID ? 'selected' : ''}>${t.NOMBREESTUDIO} ($${parseFloat(t.COSTO).toFixed(2)})</option>`).join('');
        tipoSelect.disabled = false;
      }
    };

    labSelect.addEventListener("change", (e) => updateTipoOptions(e.target.value));

    // Si es edición, cargar los estudios del laboratorio guardado
    if (isEdit) {
      updateTipoOptions(item.LABORATORIOS_ID, item.TIPOESTUDIOS_ID);
    }

    document.getElementById("saveEstudioBtn").addEventListener("click", () => this.save(isEdit));
  },

  async save(isEdit) {
    const data = {
      tipoEstudiosId: document.getElementById("e_tipo").value,
      medicosExpediente: document.getElementById("e_medico").value,
      fechaEstudio: document.getElementById("e_fecha").value.replace('T', ' '),
      estatus: document.getElementById("e_estatus").value
    };

    if (isEdit) {
      data.id = document.getElementById("e_id").value;
    }

    const endpoint = isEdit ? "editar_estudios.php" : "insertar_estudios.php";
    const method = isEdit ? "PUT" : "POST";
    
    try {
      const response = await fetch(`../api/estudios/${endpoint}`, {
        method: method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
        credentials: "include"
      });
      const res = await response.json();
      if (res.ok) {
        UI.toast.show(res.message, "success");
        UI.modal.close();
        this.loadData();
      } else {
        UI.toast.show(res.message, "error");
      }
    } catch (error) {
      UI.toast.show("Error al guardar", "error");
    }
  },

  confirmDelete(id) {
    UI.modal.show(
      "Confirmar Eliminación",
      `<p>¿Estás seguro de eliminar este registro de estudio <strong>#${id}</strong>?</p>`,
      `<button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
       <button class="btn btn-danger" onclick="Modules.estudios_gestion.delete(${id})">Eliminar</button>`
    );
  },

  async delete(id) {
    try {
      const response = await fetch("../api/estudios/eliminar_estudios.php", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id }),
        credentials: "include"
      });
      const res = await response.json();
      if (res.ok) {
        UI.toast.show(res.message, "success");
        UI.modal.close();
        this.loadData();
      } else {
        UI.toast.show(res.message, "error");
      }
    } catch (error) {
      UI.toast.show("Error al eliminar", "error");
    }
  }
};
