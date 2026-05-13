window.Modules.procquirugicos = {
  data: [],
  filteredData: [],
  quirofanos: [],
  medicos: [],
  tiposProcedimiento: [],

  // Estado de filtros activos
  activeFilters: {
    search: "",
    tipoAtencion: "",
    quirofano: "",
    tipoProcedimiento: "",
    fechaDesde: "",
    fechaHasta: ""
  },

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
    <!-- Barra superior de filtros -->
    <div class="card" style="margin-bottom: 1rem; padding: 1rem;">
      <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        
        <!-- Búsqueda General -->
        <div style="flex: 2 1 250px;">
          <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-light); margin-bottom: 5px; display: block; text-transform: uppercase;">Búsqueda</label>
          <input type="text" id="procSearch" 
            style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--surface); color: var(--text);" 
            placeholder="🔍 ID, médico, procedimiento...">
        </div>

        <!-- Filtro Quirófano -->
        <div style="flex: 1 1 180px;">
          <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-light); margin-bottom: 5px; display: block; text-transform: uppercase;">Quirófano</label>
          <select id="filterQuirofano" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--surface); color: var(--text);">
            <option value="">Todos</option>
          </select>
        </div>

        <!-- Filtro Tipo Procedimiento -->
        <div style="flex: 1 1 180px;">
          <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-light); margin-bottom: 5px; display: block; text-transform: uppercase;">Procedimiento</label>
          <select id="filterTipoProcedimiento" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--surface); color: var(--text);">
            <option value="">Todos</option>
          </select>
        </div>

        <!-- Rango de Fechas -->
        <div style="flex: 1 1 150px;">
          <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-light); margin-bottom: 5px; display: block; text-transform: uppercase;">Desde</label>
          <input type="date" id="filterFechaDesde" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--surface); color: var(--text);">
        </div>

        <div style="flex: 1 1 150px;">
          <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-light); margin-bottom: 5px; display: block; text-transform: uppercase;">Hasta</label>
          <input type="date" id="filterFechaHasta" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--surface); color: var(--text);">
        </div>

        <!-- Botones de Acción -->
        <div style="flex: 1 1 auto; display: flex; gap: 0.5rem; justify-content: flex-end;">
          <button id="clearFiltersBtn" class="btn btn-secondary" style="height: 38px; white-space: nowrap;" title="Limpiar filtros">
            ✕ Limpiar
          </button>
          <button id="addProcBtn" class="btn btn-primary" style="height: 38px; white-space: nowrap;">
            + Nuevo
          </button>
        </div>
      </div>

      <!-- Resumen de filtros -->
      <div id="filterSummary" style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px dashed var(--border); font-size: 0.85rem; color: var(--text-light); display: none;">
        <span>Mostrando <strong id="filterCount" style="color: var(--primary);">0</strong> registros</span>
        <span id="filterActiveLabel" style="margin-left: 0.5rem; font-style: italic;"></span>
      </div>
    </div>

    <!-- Contenedor de la tabla -->
    <div id="procTableContainer" class="table-container" style="overflow-x: auto;"></div>
  `;

  this.setupEventListeners();
},

setupEventListeners() {
  const events = [
    { id: "procSearch", event: "input" },
    { id: "filterQuirofano", event: "change" },
    { id: "filterTipoProcedimiento", event: "change" },
    { id: "filterFechaDesde", event: "change" },
    { id: "filterFechaHasta", event: "change" }
  ];

  events.forEach(({ id, event }) => {
    const el = document.getElementById(id);
    if (el) el.addEventListener(event, () => this.applyFilters());
  });

  document.getElementById("clearFiltersBtn").addEventListener("click", () => this.clearFilters());
  document.getElementById("addProcBtn").addEventListener("click", () => this.showModal());
},

  // Rellena los selects de filtro con los catálogos ya cargados
  populateFilterSelects() {
    const selQuirofano = document.getElementById("filterQuirofano");
    const selTipoProc = document.getElementById("filterTipoProcedimiento");

    if (selQuirofano) {
      const currentVal = selQuirofano.value;
      selQuirofano.innerHTML = `<option value="">Todos</option>` +
        this.quirofanos.map(q => `<option value="${q.ID}">${q.NOMBREQUIROFANO}${q.NOMBREAREA ? ` - ${q.NOMBREAREA}` : ''}</option>`).join("");
      selQuirofano.value = currentVal;
    }

    if (selTipoProc) {
      const currentVal = selTipoProc.value;
      selTipoProc.innerHTML = `<option value="">Todos</option>` +
        this.tiposProcedimiento.map(t => `<option value="${t.ID}">${t.NOMBREPROCEDIMIENTO}</option>`).join("");
      selTipoProc.value = currentVal;
    }
  },

  applyFilters() {
    // Leer valores de los controles de filtro
    this.activeFilters.search            = (document.getElementById("procSearch")?.value || "").toLowerCase();
    this.activeFilters.tipoAtencion      = document.getElementById("filterTipoAtencion")?.value || "";
    this.activeFilters.quirofano         = document.getElementById("filterQuirofano")?.value || "";
    this.activeFilters.tipoProcedimiento = document.getElementById("filterTipoProcedimiento")?.value || "";
    this.activeFilters.fechaDesde        = document.getElementById("filterFechaDesde")?.value || "";
    this.activeFilters.fechaHasta        = document.getElementById("filterFechaHasta")?.value || "";

    const { search, tipoAtencion, quirofano, tipoProcedimiento, fechaDesde, fechaHasta } = this.activeFilters;

    this.filteredData = this.data.filter(item => {
      // Búsqueda de texto libre
      if (search) {
        const fullName = `${item.NOMBRE || ""} ${item.APELLIDOPATERNO || ""} ${item.APELLIDOMATERNO || ""}`.toLowerCase();
        const matchSearch =
          String(item.ID).includes(search) ||
          this.getTipoAtencionLabel(item.TIPO).toLowerCase().includes(search) ||
          (item.NOMBREQUIROFANO || "").toLowerCase().includes(search) ||
          fullName.includes(search) ||
          (item.NOMBREPROCEDIMIENTO || "").toLowerCase().includes(search) ||
          String(item.ID1 || "").includes(search);
        if (!matchSearch) return false;
      }

      // Filtro tipo de atención
      if (tipoAtencion && String(item.TIPO) !== tipoAtencion) return false;

      // Filtro quirófano
      if (quirofano && String(item.QUIROFANOS_ID) !== quirofano) return false;

      // Filtro tipo de procedimiento
      if (tipoProcedimiento && String(item.TIPOPROCEDIMIENTO_ID) !== tipoProcedimiento) return false;

      // Filtro rango de fechas
      if (fechaDesde || fechaHasta) {
        if (!item.FECHAPROCEDIMIENTO) return false;
        const fechaItem = new Date(item.FECHAPROCEDIMIENTO.replace(" ", "T"));
        if (fechaDesde) {
          const desde = new Date(fechaDesde + "T00:00:00");
          if (fechaItem < desde) return false;
        }
        if (fechaHasta) {
          const hasta = new Date(fechaHasta + "T23:59:59");
          if (fechaItem > hasta) return false;
        }
      }

      return true;
    });

    this.updateFilterSummary();
    this.renderTable();
  },

  clearFilters() {
    const ids = ["procSearch", "filterTipoAtencion", "filterQuirofano", "filterTipoProcedimiento", "filterFechaDesde", "filterFechaHasta"];
    ids.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = "";
    });

    this.activeFilters = { search: "", tipoAtencion: "", quirofano: "", tipoProcedimiento: "", fechaDesde: "", fechaHasta: "" };

    this.filteredData = [...this.data];
    this.updateFilterSummary();
    this.renderTable();
  },

  updateFilterSummary() {
    const summary = document.getElementById("filterSummary");
    const countEl = document.getElementById("filterCount");
    const labelEl = document.getElementById("filterActiveLabel");
    if (!summary) return;

    const { tipoAtencion, quirofano, tipoProcedimiento, fechaDesde, fechaHasta } = this.activeFilters;
    const hayFiltros = tipoAtencion || quirofano || tipoProcedimiento || fechaDesde || fechaHasta;

    summary.style.display = "block";
    countEl.textContent = this.filteredData.length;

    if (hayFiltros) {
      const partes = [];
      if (tipoAtencion) partes.push(this.getTipoAtencionLabel(tipoAtencion));
      if (quirofano) {
        const q = this.quirofanos.find(x => String(x.ID) === quirofano);
        if (q) partes.push(q.NOMBREQUIROFANO);
      }
      if (tipoProcedimiento) {
        const t = this.tiposProcedimiento.find(x => String(x.ID) === tipoProcedimiento);
        if (t) partes.push(t.NOMBREPROCEDIMIENTO);
      }
      if (fechaDesde && fechaHasta) partes.push(`${fechaDesde} → ${fechaHasta}`);
      else if (fechaDesde) partes.push(`desde ${fechaDesde}`);
      else if (fechaHasta) partes.push(`hasta ${fechaHasta}`);

      labelEl.textContent = `· Filtros activos: ${partes.join(", ")}`;
    } else {
      labelEl.textContent = "";
    }
  },

  async loadQuirofanos() {
    try {
      const response = await fetch("../api/quirofanos/listar_quirofanos.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) this.quirofanos = res.data;
    } catch (error) {
      console.error("Error al cargar quirófanos:", error);
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

  async loadTiposProcedimiento() {
    try {
      const response = await fetch("../api/tipoprocedimiento/listar_tipoprocedimiento.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) this.tiposProcedimiento = res.data;
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
        this.populateFilterSelects();
        this.updateFilterSummary();
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
      container.innerHTML = `
        <div style="padding: 2rem; text-align: center; color: var(--text-light);">
          No se encontraron procedimientos con los filtros aplicados.
        </div>`;
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
    if (!Array.isArray(this.data) || this.data.length === 0) return 1;
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
          <label for="p_tipoProcedimiento" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Tipo de Procedimiento</span>
            <button type="button" class="btn btn-secondary btn-sm" style="padding: 2px 8px; font-size: 0.75rem;" id="quickAddTipoProcBtn">+ Nuevo</button>
          </label>
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
    document.getElementById("quickAddTipoProcBtn").addEventListener("click", () => this.quickAddTipoProcedimiento());
  },

  async quickAddTipoProcedimiento() {
    const nombre = prompt("Ingresa el nombre del nuevo tipo de procedimiento (ej. Apendicectomía):");
    if (!nombre || nombre.trim() === "") return;

    try {
      const response = await fetch("../api/tipoprocedimiento/insertar_tipoprocedimiento.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ nombreprocedimiento: nombre.trim() }),
        credentials: "include"
      });

      const res = await response.json();

      if (res.ok) {
        UI.toast.show(res.message, "success");
        await this.loadTiposProcedimiento();

        // Actualizar select del modal
        const select = document.getElementById("p_tipoProcedimiento");
        select.innerHTML = `
          <option value="">Seleccione un tipo...</option>
          ${this.renderTipoProcedimientoOptions()}
        `;

        // Seleccionar automáticamente el recién creado
        const newOption = Array.from(select.options).find(
          opt => opt.text.toLowerCase() === nombre.trim().toLowerCase()
        );
        if (newOption) select.value = newOption.value;

        // Actualizar también el select del filtro
        this.populateFilterSelects();

      } else {
        UI.toast.show(res.message, "error");
      }
    } catch (e) {
      UI.toast.show("Error al crear tipo de procedimiento", "error");
    }
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