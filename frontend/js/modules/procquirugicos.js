window.Modules.procquirugicos = {
  data: [],
  filteredData: [],
  quirofanos: [],
  medicos: [],
  tiposProcedimiento: [],

  async init() {
    this.renderLayout();
    await Promise.all([
      this.loadQuirofanos(),
      this.loadMedicos(),
      this.loadTiposProcedimiento()
    ]);
    this.loadData();
  },

  renderLayout() {
    const contentArea = document.getElementById("contentArea");
    contentArea.innerHTML = `
      <div class="card" style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
        <div style="flex: 1; max-width: 400px;">
          <input type="text" id="procSearch" class="form-group" style="margin-bottom: 0; width: 100%;" placeholder="🔍 Buscar por ID, quirófano, médico o procedimiento...">
        </div>
        <button id="addProcBtn" class="btn btn-primary">
          <span>+</span> Registrar Procedimiento
        </button>
      </div>

      <div id="procTableContainer" class="table-container"></div>
    `;

    document.getElementById("procSearch").addEventListener("input", (e) => this.filter(e.target.value));
    document.getElementById("addProcBtn").addEventListener("click", () => this.showModal());
  },

  async loadQuirofanos() {
    try {
      const response = await fetch("../api/quirofanos/listar_quirofanos.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) {
        this.quirofanos = res.data;
      }
    } catch (error) {
      console.error("Error al cargar quirófanos:", error);
    }
  },

  async loadMedicos() {
    try {
      const response = await fetch("../api/medicos/listar_medicos.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) {
        this.medicos = res.data;
      }
    } catch (error) {
      console.error("Error al cargar médicos:", error);
    }
  },

  async loadTiposProcedimiento() {
    try {
      const response = await fetch("../api/tipoprocedimiento/listar_tipoprocedimiento.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) {
        this.tiposProcedimiento = res.data;
      }
    } catch (error) {
      console.error("Error al cargar tipos de procedimiento:", error);
    }
  },

  async loadData() {
    try {
      UI.showSkeleton("#procTableContainer");
      const response = await fetch("../api/procquirugicos/listar.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) {
        this.data = res.data;
        this.filteredData = [...this.data];
        this.renderTable();
      } else {
        UI.toast.show(res.message, "error");
      }
    } catch (error) {
      UI.toast.show("Error al cargar procedimientos", "error");
    }
  },

  getTipoAtencionLabel(tipo) {
    const value = Number(tipo);
    if (value === 1) return "Parto";
    if (value === 2) return "Cirugía";
    return "Sin clasificar";
  },

  getEstatusLabel(estatus) {
    const value = Number(estatus);
    if (value === 1) return "Completado";
    if (value === 2) return "Pendiente / En Proceso";
    return "Sin definir";
  },

  renderTable() {
    const container = document.getElementById("procTableContainer");

    if (this.filteredData.length === 0) {
      container.innerHTML = `<div style="padding: 2rem; text-align: center; color: var(--text-light);">No hay procedimientos registrados.</div>`;
      return;
    }

    let html = `
      <table>
        <thead>
          <tr>
            <th>Registro</th>
            <th>Tipo Atención</th>
            <th>Fecha</th>
            <th>Quirófano</th>
            <th>Médico</th>
            <th>Tipo Procedimiento</th>
            <th>ID1 (Interno)</th>
            <th>Estatus</th>
            <th style="text-align: right;">Acciones</th>
          </tr>
        </thead>
        <tbody>
    `;

    this.filteredData.forEach(item => {
      const fullName = `${item.NOMBRE || ""} ${item.APELLIDOPATERNO || ""} ${item.APELLIDOMATERNO || ""}`.trim();
      const fecha = item.FECHAPROCEDIMIENTO ? item.FECHAPROCEDIMIENTO.replace(" ", "T") : "";

      html += `
        <tr>
          <td><span style="font-weight: 600; color: var(--primary);">#${item.ID}</span></td>
          <td>${this.getTipoAtencionLabel(item.TIPO)}</td>
          <td style="font-size: 0.85rem;">${fecha ? new Date(fecha).toLocaleString() : "Sin fecha"}</td>
          <td>${item.NOMBREQUIROFANO || "N/A"}</td>
          <td>${fullName || "N/A"}</td>
          <td>${item.NOMBREPROCEDIMIENTO || "N/A"}</td>
          <td>${item.ID1 ?? "N/A"}</td>
          <td><span class="badge" style="background: var(--background); color: var(--text);">${this.getEstatusLabel(item.ESTATUS)}</span></td>
          <td style="text-align: right;">
            <button class="btn btn-secondary btn-sm" onclick="Modules.procquirugicos.showModal(${JSON.stringify(item).replace(/"/g, '&quot;')})">✏️</button>
            <button class="btn btn-secondary btn-sm" onclick="Modules.procquirugicos.confirmDelete(${item.ID})">🗑️</button>
          </td>
        </tr>
      `;
    });

    html += `</tbody></table>`;
    container.innerHTML = html;
  },

  filter(query) {
    const q = query.toLowerCase();
    this.filteredData = this.data.filter(item => {
      const fullName = `${item.NOMBRE || ""} ${item.APELLIDOPATERNO || ""} ${item.APELLIDOMATERNO || ""}`.toLowerCase();
      return (
        String(item.ID).includes(q) ||
        this.getTipoAtencionLabel(item.TIPO).toLowerCase().includes(q) ||
        (item.NOMBREQUIROFANO || "").toLowerCase().includes(q) ||
        fullName.includes(q) ||
        (item.NOMBREPROCEDIMIENTO || "").toLowerCase().includes(q) ||
        String(item.ID1 || "").includes(q)
      );
    });
    this.renderTable();
  },

  renderQuirofanoOptions(selectedId = null) {
    return this.quirofanos.map(q => `
      <option value="${q.ID}" ${selectedId == q.ID ? 'selected' : ''}>${q.NOMBREQUIROFANO}${q.NOMBREAREA ? ` - ${q.NOMBREAREA}` : ''}</option>
    `).join("");
  },

  renderMedicoOptions(selectedId = null) {
    return this.medicos.map(m => `
      <option value="${m.EXPEDIENTE}" ${selectedId == m.EXPEDIENTE ? 'selected' : ''}>${m.NOMBRE} ${m.APELLIDOPATERNO} ${m.APELLIDOMATERNO || ''} (${m.EXPEDIENTE})</option>
    `).join("");
  },

  renderTipoProcedimientoOptions(selectedId = null) {
    return this.tiposProcedimiento.map(t => `
      <option value="${t.ID}" ${selectedId == t.ID ? 'selected' : ''}>${t.NOMBREPROCEDIMIENTO}</option>
    `).join("");
  },

  getNextConsecutiveId() {
    if (!Array.isArray(this.data) || this.data.length === 0) {
      return 1;
    }

    const maxId = this.data.reduce((max, current) => {
      const currentId = Number(current.ID) || 0;
      return currentId > max ? currentId : max;
    }, 0);

    return maxId + 1;
  },

  showModal(item = null) {
    const isEdit = item !== null;
    const consecutiveId = isEdit ? item.ID : this.getNextConsecutiveId();
    const title = isEdit ? "Editar Procedimiento" : "Registrar Nuevo Procedimiento";

    const body = `
      <form id="procForm">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        
        
          <div class="form-group">
            <label for="p_id">ID del Registro</label>
            <input type="number" id="p_id" value="${consecutiveId}" required ${isEdit ? 'readonly' : ''}>
          </div>
          <div class="form-group">
            <label for="p_id1">ID1 (Interno)</label>
            <input type="number" id="p_id1" value="${item ? item.ID1 : ''}" required>
          </div>
          
          <div class="form-group">
            <label for="p_tipo">Tipo de Atención</label>
            <select id="p_tipo" required>
              <option value="">Seleccione una opción</option>
              <option value="1" ${isEdit && Number(item.TIPO) === 1 ? 'selected' : ''}>Parto</option>
              <option value="2" ${isEdit && Number(item.TIPO) === 2 ? 'selected' : ''}>Cirugía</option>
            </select>
          </div>

          <div class="form-group">
            <label for="p_estatus">Estatus</label>
            <select id="p_estatus" required>
              <option value="1" ${isEdit && Number(item.ESTATUS) === 1 ? 'selected' : ''}>Completado</option>
              <option value="2" ${isEdit && Number(item.ESTATUS) === 2 ? 'selected' : ''}>Pendiente / En Proceso</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="p_fecha">Fecha y Hora</label>
          <input type="datetime-local" id="p_fecha" value="${item && item.FECHAPROCEDIMIENTO ? item.FECHAPROCEDIMIENTO.replace(' ', 'T') : new Date((Date.now() - (6 * 60 * 60 * 1000))).toISOString().slice(0, 16)}" required>
        </div>

        <div class="form-group">
          <label for="p_quirofano">Quirófano</label>
          <select id="p_quirofano" required>
            <option value="">Seleccione un quirófano...</option>
            ${this.renderQuirofanoOptions(item ? item.QUIROFANOS_ID : null)}
          </select>
        </div>

        <div class="form-group">
          <label for="p_medico">Médico Responsable</label>
          <select id="p_medico" required>
            <option value="">Seleccione un médico...</option>
            ${this.renderMedicoOptions(item ? item.MEDICOS_EXPEDIENTE : null)}
          </select>
        </div>

        <div class="form-group">
          <label for="p_tipoProcedimiento">Tipo de Procedimiento</label>
          <select id="p_tipoProcedimiento" required>
            <option value="">Seleccione un tipo...</option>
            ${this.renderTipoProcedimientoOptions(item ? item.TIPOPROCEDIMIENTO_ID : null)}
          </select>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
      <button class="btn btn-primary" id="saveProcBtn">${isEdit ? 'Actualizar' : 'Registrar'}</button>
    `;

    UI.modal.show(title, body, footer);
    document.getElementById("saveProcBtn").addEventListener("click", () => this.save(isEdit));
  },

  async save(isEdit) {
    const payload = {
      id: document.getElementById("p_id").value,
      tipo: document.getElementById("p_tipo").value,
      fechaProcedimiento: document.getElementById("p_fecha").value.replace('T', ' '),
      estatus: document.getElementById("p_estatus").value,
      quirofanosId: document.getElementById("p_quirofano").value,
      medicosExpediente: document.getElementById("p_medico").value,
      tipoProcedimientoId: document.getElementById("p_tipoProcedimiento").value,
      id1: document.getElementById("p_id1").value
    };

    const endpoint = isEdit ? "editar.php" : "insertaar.php";

    try {
      const response = await fetch(`../api/procquirugicos/${endpoint}`, {
        method: isEdit ? "PUT" : "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
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
      UI.toast.show("Error al guardar procedimiento", "error");
    }
  },

  confirmDelete(id) {
    UI.modal.show(
      "Confirmar Eliminación",
      `<p>¿Estás seguro de eliminar este procedimiento <strong>#${id}</strong>?</p>`,
      `<button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
       <button class="btn btn-danger" onclick="Modules.procquirugicos.delete(${id})">Eliminar</button>`
    );
  },

  async delete(id) {
    try {
      const response = await fetch("../api/procquirugicos/eliminar.php", {
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
      UI.toast.show("Error al eliminar procedimiento", "error");
    }
  }
};