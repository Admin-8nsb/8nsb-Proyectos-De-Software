/**
 * Consultas Module - Hospital HIS
 */

window.Modules.consultas = {
  data: [],
  filteredData: [],
  medicos: [],
  consultorios: [],
  tiposServicio: [],

  async init() {
    this.renderLayout();
    await this.loadCatalogos();
    this.loadData();
  },

  renderLayout() {
    const contentArea = document.getElementById("contentArea");

    contentArea.innerHTML = `
      <div class="card" style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <div style="flex: 1; max-width: 400px;">
          <input type="text" id="consultasSearch" class="form-group" style="margin-bottom: 0; width: 100%;" placeholder="🔍 Buscar consulta...">
        </div>

        <button id="addConsultaBtn" class="btn btn-primary">
          <span>+</span> Registrar Consulta
        </button>
      </div>

      <div id="consultasTableContainer" class="table-container"></div>
    `;

    document.getElementById("consultasSearch")
      .addEventListener("input", e => this.filter(e.target.value));

    document.getElementById("addConsultaBtn")
      .addEventListener("click", () => this.showModal());
  },

  async loadCatalogos() {
    try {
      // Cargar médicos
      const resMedicos = await fetch("../api/medicos/listar_medicos.php", { credentials: "include" });
      const dataMedicos = await resMedicos.json();
      if (dataMedicos.ok) this.medicos = dataMedicos.data;

      // Cargar consultorios
      const resConsultorios = await fetch("../api/consultorios/listar_consultorios.php", { credentials: "include" });
      const dataConsultorios = await resConsultorios.json();
      if (dataConsultorios.ok) this.consultorios = dataConsultorios.data;

      // Cargar tipos de servicio excluyendo Medicina General (ID 1)
      const resServicios = await fetch("../api/tipos_servicios/listar_tipos_servicios.php?exclude_id=1", { credentials: "include" });
      const dataServicios = await resServicios.json();
      if (dataServicios.ok) this.tiposServicio = dataServicios.data;

    } catch (error) {
      console.error("Error cargando catálogos", error);
      UI.toast.show("Error cargando catálogos", "error");
    }
  },

  async loadData() {
    try {
      // Como no tenemos listar_consultas que incluya todos los campos JOIN, vamos a reutilizar la vista de otros_consultorios
      // para mostrar solo estas consultas
      const response = await fetch("../api/reportes/otros_consultorios.php", {
        credentials: "include"
      });

      const res = await response.json();

      if (res.ok) {
        this.data = res.data.registros;
        this.filteredData = [...this.data];
        this.renderTable();
      } else {
        UI.toast.show(res.message, "error");
      }

    } catch (error) {
      console.error("Error", error);
      UI.toast.show("Error al cargar consultas", "error");
    }
  },

  renderTable() {
    const container = document.getElementById("consultasTableContainer");

    if (this.filteredData.length === 0) {
      container.innerHTML = `
        <div style="padding:2rem;text-align:center;color:var(--text-light);">
          No hay consultas registradas.
        </div>
      `;
      return;
    }

    let html = `
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Servicio</th>
            <th>Consultorio</th>
            <th>Médico</th>
            <th>Atención</th>
            <th style="text-align: right;">Acciones</th>
          </tr>
        </thead>
        <tbody>
    `;

    this.filteredData.forEach(item => {
      // Normalizar nombres de campos si vienen de listar_consultas o reportes
      const medicoNom = item.MEDICO_NOMBRE || item.NOMBRE || '';
      const medicoPat = item.MEDICO_PATERNO || item.APELLIDOPATERNO || '';
      const consultorioNom = item.NOMBRECONSULTORIO || item.CONSULTORIO || '';
      const servicioNom = item.NOMBRESERVICIO || 'N/A';

      html += `
        <tr>
          <td><span style="font-weight: 600; color: var(--primary);">#${item.ID}</span></td>
          <td>${new Date(item.FECHACONSULTA).toLocaleString()}</td>
          <td><span style="font-weight:600;">${servicioNom}</span></td>
          <td>${consultorioNom}</td>
          <td>Dr. ${medicoNom} ${medicoPat}</td>
          <td><span class="badge" style="background: #e0f2fe; color: #0284c7; padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">${item.TIPOCONSULTA}</span></td>
          <td style="text-align: right;">
            <button class="btn btn-secondary btn-sm" onclick="Modules.consultas.showModal(${JSON.stringify(item).replace(/"/g, '&quot;')})">✏️</button>
            <button class="btn btn-secondary btn-sm" onclick="Modules.consultas.confirmDelete(${item.ID})">🗑️</button>
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
      const nomServicio = (item.NOMBRESERVICIO || 'N/A').toLowerCase();
      const nomConsultorio = (item.NOMBRECONSULTORIO || item.CONSULTORIO || '').toLowerCase();
      const nomMedico = ((item.MEDICO_NOMBRE || item.NOMBRE || '') + ' ' + (item.MEDICO_PATERNO || item.APELLIDOPATERNO || '')).toLowerCase();
      const atencion = (item.TIPOCONSULTA || '').toLowerCase();

      return (
        String(item.ID).includes(q) ||
        nomServicio.includes(q) ||
        nomConsultorio.includes(q) ||
        nomMedico.includes(q) ||
        atencion.includes(q)
      );
    });

    this.renderTable();
  },

  showModal(item = null) {
    const isEdit = item !== null;
    const title = isEdit ? "Editar Consulta" : "Registrar Consulta";

    const body = `
      <form id="consultaForm">

        <div class="form-group">
          <label>ID</label>
          <input type="text" id="cons_id" value="${isEdit ? item.ID : 'Autogenerado'}" disabled style="background: #f1f5f9; cursor: not-allowed; font-weight: 600; color: #64748b;">
        </div>

        <div class="form-group">
          <label>Fecha de Consulta</label>
          <input type="datetime-local" id="cons_fecha" value="${isEdit ? item.FECHACONSULTA.replace(' ', 'T').substring(0, 16) : ''}" required>
        </div>

        <div class="form-group">
          <label style="display: flex; justify-content: space-between; align-items: center;">
            <span>Tipo de Servicio (Otros Servicios)</span>
            <button type="button" class="btn btn-secondary btn-sm" style="padding: 2px 8px; font-size: 0.75rem;" id="quickAddServicioBtn">+ Nuevo</button>
          </label>
          <select id="cons_servicio" required>
            <option value="">Seleccione un servicio</option>
            ${this.tiposServicio.map(ts => `<option value="${ts.ID}" ${isEdit && item.TIPOSERVICIO_ID == ts.ID ? 'selected' : ''}>${ts.NOMBRESERVICIO}</option>`).join('')}
          </select>
        </div>

        <div class="form-group">
          <label>Consultorio</label>
          <select id="cons_consultorio" required>
            <option value="">Seleccione un consultorio</option>
            ${this.consultorios.map(c => `<option value="${c.ID}" ${isEdit && (item.CONSULTORIOS_ID == c.ID) ? 'selected' : ''}>${c.CONSULTORIO}</option>`).join('')}
          </select>
        </div>

        <div class="form-group">
          <label>Médico</label>
          <select id="cons_medico" required>
            <option value="">Seleccione un médico</option>
            ${this.medicos.map(m => `<option value="${m.EXPEDIENTE}" ${isEdit && (item.MEDICOS_EXPEDIENTE == m.EXPEDIENTE) ? 'selected' : ''}>${m.NOMBRE} ${m.APELLIDOPATERNO}</option>`).join('')}
          </select>
        </div>

        <div class="form-group">
          <label>Tipo de Atención</label>
          <select id="cons_atencion" required>
            <option value="Primera Vez" ${isEdit && item.TIPOCONSULTA === 'Primera Vez' ? 'selected' : ''}>Primera Vez</option>
            <option value="Subsecuente" ${isEdit && item.TIPOCONSULTA === 'Subsecuente' ? 'selected' : ''}>Subsecuente</option>
            <option value="Urgencia" ${isEdit && item.TIPOCONSULTA === 'Urgencia' ? 'selected' : ''}>Urgencia</option>
          </select>
        </div>

      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
      <button class="btn btn-primary" id="saveConsultaBtn">${isEdit ? 'Actualizar' : 'Registrar'}</button>
    `;

    UI.modal.show(title, body, footer);

    if (!isEdit) {
      // Set default date to now for new entries
      const now = new Date();
      now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
      document.getElementById("cons_fecha").value = now.toISOString().slice(0, 16);
    }

    document.getElementById("quickAddServicioBtn")
      .addEventListener("click", () => this.quickAddServicio());

    document.getElementById("saveConsultaBtn")
      .addEventListener("click", () => this.save(isEdit));
  },

  async quickAddServicio() {
    const nombre = prompt("Ingresa el nombre del nuevo servicio (ej. Alergología):");
    if (!nombre || nombre.trim() === "") return;

    try {
      const response = await fetch("../api/tipos_servicios/insertar_tipos_servicios.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ nombreservicio: nombre.trim(), descripcion: "" }),
        credentials: "include"
      });

      const res = await response.json();

      if (res.ok) {
        UI.toast.show(res.message, "success");
        await this.loadCatalogos(); // Recargar el catálogo dinámicamente
        
        // Actualizar el DOM del select
        const select = document.getElementById("cons_servicio");
        select.innerHTML = `
          <option value="">Seleccione un servicio</option>
          ${this.tiposServicio.map(ts => `<option value="${ts.ID}">${ts.NOMBRESERVICIO}</option>`).join('')}
        `;
        
        // Seleccionar automáticamente el servicio recién creado
        const newOption = Array.from(select.options).find(opt => opt.text.toLowerCase() === nombre.trim().toLowerCase());
        if (newOption) {
          select.value = newOption.value;
        }
      } else {
        UI.toast.show(res.message, "error");
      }
    } catch (e) {
      UI.toast.show("Error al crear servicio rápido", "error");
    }
  },

  async save(isEdit = false) {
    const data = {
      fechaConsulta: document.getElementById("cons_fecha").value.replace('T', ' '),
      tipoServicioId: parseInt(document.getElementById("cons_servicio").value),
      consultoriosId: parseInt(document.getElementById("cons_consultorio").value),
      medicosExpediente: parseInt(document.getElementById("cons_medico").value),
      tipoConsulta: document.getElementById("cons_atencion").value,
      estatus: 1
    };

    if (isEdit) {
      data.id = parseInt(document.getElementById("cons_id").value);
    }

    if (!data.fechaConsulta || !data.tipoServicioId || !data.consultoriosId || !data.medicosExpediente) {
      UI.toast.show("Todos los campos son obligatorios", "warning");
      return;
    }

    const endpoint = isEdit ? "editar.php" : "insertar_consultas.php";
    const method = isEdit ? "PUT" : "POST";

    try {
      const response = await fetch(`../api/consultas/${endpoint}`, {
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
      console.error(error);
      UI.toast.show("Error al registrar consulta", "error");
    }
  },

  confirmDelete(id) {
    const body = `<p>¿Estás seguro de que deseas eliminar la consulta <strong>#${id}</strong>? Esta acción no se puede deshacer.</p>`;
    const footer = `
      <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
      <button class="btn btn-danger" onclick="Modules.consultas.delete(${id})">Eliminar</button>
    `;
    UI.modal.show("Confirmar eliminación", body, footer);
  },

  async delete(id) {
    try {
      const response = await fetch("../api/consultas/eliminar.php", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id }),
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
