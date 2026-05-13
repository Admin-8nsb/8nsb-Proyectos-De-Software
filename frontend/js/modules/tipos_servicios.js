/**
 * Tipos de Servicios Module - Hospital HIS
 */

window.Modules.tipos_servicios = {
  data: [],
  filteredData: [],

  init() {
    this.renderLayout();
    this.loadData();
  },

  renderLayout() {
    const contentArea = document.getElementById("contentArea");

    contentArea.innerHTML = `
      <div class="card" style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <div style="flex: 1; max-width: 400px;">
          <input type="text" id="servicioSearch" class="form-group" style="margin-bottom: 0; width: 100%;" placeholder="🔍 Buscar servicio...">
        </div>

        <button id="addServicioBtn" class="btn btn-primary">
          <span>+</span> Nuevo Servicio
        </button>
      </div>

      <div class="card" style="margin-bottom: 1rem; background: #e0f2fe; border-left: 4px solid #0ea5e9;">
        <p style="margin: 0; color: #0369a1; font-size: 0.9rem;">
          <strong>Nota:</strong> Los servicios agregados aquí aparecerán automáticamente como opciones al registrar "Otros Consultorios" y en los filtros de Reportes.
        </p>
      </div>

      <div id="servicioTableContainer" class="table-container"></div>
    `;

    document.getElementById("servicioSearch")
      .addEventListener("input", e => this.filter(e.target.value));

    document.getElementById("addServicioBtn")
      .addEventListener("click", () => this.showModal());
  },

  async loadData() {
    try {
      // Cargar todos (no excluimos el 1 aquí para que el admin pueda verlos)
      const response = await fetch("../api/tipos_servicios/listar_tipos_servicios.php", {
        credentials: "include"
      });

      const res = await response.json();

      if (res.ok) {
        this.data = res.data;
        this.filteredData = [...this.data];
        this.renderTable();
      } else {
        UI.toast.show(res.message, "error");
      }

    } catch {
      UI.toast.show("Error al cargar datos", "error");
    }
  },

  renderTable() {
    const container = document.getElementById("servicioTableContainer");

    if (this.filteredData.length === 0) {
      container.innerHTML = `
        <div style="padding:2rem;text-align:center;color:var(--text-light);">
          No hay servicios registrados.
        </div>
      `;
      return;
    }

    let html = `
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre del Servicio</th>
            <th>Descripción</th>
            <th>Tipo</th>
            <th style="text-align:right;">Acciones</th>
          </tr>
        </thead>
        <tbody>
    `;

    this.filteredData.forEach(item => {
      const isBase = item.ID <= 5;
      const typeBadge = isBase 
        ? `<span class="badge" style="background: #e2e8f0; color: #475569; padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">Servicio Base</span>`
        : `<span class="badge" style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">Dinámico</span>`;

      html += `
        <tr>
          <td><span style="font-weight: 600; color: var(--primary);">#${item.ID}</span></td>
          <td style="font-weight:600;">${item.NOMBRESERVICIO}</td>
          <td>${item.DESCRIPCION || '<em style="color:var(--text-light)">Sin descripción</em>'}</td>
          <td>${typeBadge}</td>
          <td style="text-align:right;">
            <button class="btn btn-secondary btn-sm"
              onclick="Modules.tipos_servicios.showModal(${JSON.stringify(item).replace(/"/g, '&quot;')})"
              ${item.ID == 1 ? 'disabled style="opacity:0.5; cursor:not-allowed;" title="No se puede editar Medicina General"' : ''}>✏️</button>

            <button class="btn btn-secondary btn-sm"
              onclick="Modules.tipos_servicios.confirmDelete(${item.ID})"
              ${isBase ? 'disabled style="opacity:0.5; cursor:not-allowed;" title="No se pueden eliminar los servicios base"' : ''}>🗑️</button>
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
      return (
        item.NOMBRESERVICIO.toLowerCase().includes(q) ||
        (item.DESCRIPCION && item.DESCRIPCION.toLowerCase().includes(q)) ||
        String(item.ID).includes(q)
      );
    });

    this.renderTable();
  },

  showModal(item = null) {
    const isEdit = item !== null;

    if (isEdit && item.ID == 1) {
      UI.toast.show("No se puede editar el servicio base", "warning");
      return;
    }

    const title = isEdit
      ? "Editar Tipo de Servicio"
      : "Registrar Tipo de Servicio";

    const body = `
      <form id="servicioForm">

        <div class="form-group">
          <label>ID</label>
          <input type="text" id="s_id" value="${item ? item.ID : 'Autogenerado'}" disabled style="background: #f1f5f9; cursor: not-allowed; font-weight: 600; color: #64748b;">
        </div>

        <div class="form-group">
          <label>Nombre del Servicio</label>
          <input type="text" id="s_nombre" value="${item ? item.NOMBRESERVICIO : ''}" required>
        </div>

        <div class="form-group">
          <label>Descripción</label>
          <textarea id="s_descripcion" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 8px;">${item ? (item.DESCRIPCION || '') : ''}</textarea>
        </div>

      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
      <button class="btn btn-primary" id="saveServicioBtn">
        ${isEdit ? "Actualizar" : "Registrar"}
      </button>
    `;

    UI.modal.show(title, body, footer);

    document.getElementById("saveServicioBtn")
      .addEventListener("click", () => this.save(isEdit));
  },

  async save(isEdit) {
    const data = {
      nombreservicio: document.getElementById("s_nombre").value.trim(),
      descripcion: document.getElementById("s_descripcion").value.trim()
    };

    if (isEdit) {
      data.id = parseInt(document.getElementById("s_id").value);
    }

    if (!data.nombreservicio || (isEdit && !data.id)) {
      UI.toast.show("El nombre del servicio es obligatorio", "warning");
      return;
    }

    const endpoint = isEdit
      ? "editar_tipos_servicios.php"
      : "insertar_tipos_servicios.php";

    const method = isEdit ? "PUT" : "POST";

    try {
      const response = await fetch(`../api/tipos_servicios/${endpoint}`, {
        method,
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

    } catch {
      UI.toast.show("Error al procesar", "error");
    }
  },

  confirmDelete(id) {
    if (id <= 5) {
      UI.toast.show("Los servicios base no se pueden eliminar.", "warning");
      return;
    }

    const body = `
      <p>¿Estás seguro de eliminar este Tipo de Servicio?</p>
      <p style="font-size: 0.85rem; color: var(--text-light);">Nota: Si este servicio ya tiene consultas registradas, el sistema no permitirá su eliminación.</p>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
      <button class="btn btn-danger" onclick="Modules.tipos_servicios.delete(${id})">
        Eliminar
      </button>
    `;

    UI.modal.show("Confirmar eliminación", body, footer);
  },

  async delete(id) {
    try {
      const response = await fetch("../api/tipos_servicios/eliminar_tipos_servicios.php", {
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

    } catch {
      UI.toast.show("Error al eliminar", "error");
    }
  }
};
