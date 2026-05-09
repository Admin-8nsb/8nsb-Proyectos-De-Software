/**
 * Reportes Module - Hospital HIS
 * Centralized reporting module with sub-sections
 */

window.Modules.reportes = {
  currentView: 'menu', // 'menu', 'urgencias', 'estudios', 'procedimientos'
  hospitales: [],
  quirofanos: [],
  tiposProcedimiento: [],
  selectedHospital: '',
  selectedQuirofano: '',
  selectedTipoAtencion: '',
  selectedTipoProcedimiento: '',
  selectedFechaDesde: '',
  selectedFechaHasta: '',

  async init() {
    await this.loadHospitales();
    this.showMenu();
  },

  async loadHospitales() {
    try {
      const response = await fetch("../api/hospital/listar_hospital.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) this.hospitales = res.data;
    } catch (error) {
      console.error("Error al cargar hospitales:", error);
    }
  },

  showMenu() {
    this.currentView = 'menu';
    const contentArea = document.getElementById("contentArea");
    contentArea.innerHTML = `
      <div class="card" style="margin-bottom: 2rem;">
        <h2>📊 Panel de Reportes</h2>
        <p style="color: var(--text-light);">Selecciona el tipo de reporte que deseas visualizar.</p>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        <!-- Card Urgencias -->
        <div class="card report-card" style="cursor: pointer; transition: var(--transition); border-top: 4px solid var(--primary);" onclick="Modules.reportes.loadUrgenciasView()">
          <div style="font-size: 2rem; margin-bottom: 1rem;">🚑</div>
          <h3>Reporte de Urgencias</h3>
          <p style="font-size: 0.875rem; color: var(--text-light); margin-top: 0.5rem;">
            Estadísticas de ingresos, egresos y ocupación en tiempo real del área de urgencias.
          </p>
          <div style="margin-top: 1.5rem; color: var(--primary); font-weight: 600; font-size: 0.85rem;">
            Ver reporte →
          </div>
        </div>

        <!-- Card Estudios -->
        <div class="card report-card" style="cursor: pointer; transition: var(--transition); border-top: 4px solid #8b5cf6;" onclick="Modules.reportes.loadEstudiosView()">
          <div style="font-size: 2rem; margin-bottom: 1rem;">🧪</div>
          <h3>Reporte de Estudios</h3>
          <p style="font-size: 0.875rem; color: var(--text-light); margin-top: 0.5rem;">
            Resumen de estudios paraclínicos realizados por laboratorio y tipo (Rayos X, Sangre, etc).
          </p>
          <div style="margin-top: 1.5rem; color: #8b5cf6; font-weight: 600; font-size: 0.85rem;">
            Ver reporte →
          </div>
        </div>

        <!-- REPORTE 1: Medicina General -->
        <div class="card report-card" style="opacity: 0.7; border-top: 4px solid var(--secondary); background: #fcfcfc;">
          <div style="font-size: 2rem; margin-bottom: 1rem;">🩺</div>
          <h3>Consultas Medicina General</h3>
          <p style="font-size: 0.875rem; color: var(--text-light); margin-top: 0.5rem;">
            Número de consultas de medicina general o familiar por médico en consultorio.
          </p>
          <div style="margin-top: 1.5rem; color: var(--text-light); font-weight: 600; font-size: 0.85rem;">
            ⏳ Próximamente
          </div>
        </div>

        <!-- REPORTE 2: Especialistas -->
        <div class="card report-card" style="opacity: 0.7; border-top: 4px solid var(--secondary); background: #fcfcfc;">
          <div style="font-size: 2rem; margin-bottom: 1rem;">👨‍⚕️</div>
          <h3>Consultas de Especialistas</h3>
          <p style="font-size: 0.875rem; color: var(--text-light); margin-top: 0.5rem;">
            Número de consultas de médicos especialistas por especialidad y consultorio.
          </p>
          <div style="margin-top: 1.5rem; color: var(--text-light); font-weight: 600; font-size: 0.85rem;">
            ⏳ Próximamente
          </div>
        </div>

        <!-- REPORTE 3: Otros Consultorios -->
        <div class="card report-card" style="opacity: 0.7; border-top: 4px solid var(--secondary); background: #fcfcfc;">
          <div style="font-size: 2rem; margin-bottom: 1rem;">🏢</div>
          <h3>Otros Consultorios</h3>
          <p style="font-size: 0.875rem; color: var(--text-light); margin-top: 0.5rem;">
            Consultas o procedimientos de otros consultorios no incluidos anteriormente por tipo.
          </p>
          <div style="margin-top: 1.5rem; color: var(--text-light); font-weight: 600; font-size: 0.85rem;">
            ⏳ Próximamente
          </div>
        </div>

        <!-- REPORTE 4: Ingresos/Egresos Hospitalarios -->
        <div class="card report-card" style="opacity: 0.7; border-top: 4px solid var(--secondary); background: #fcfcfc;">
          <div style="font-size: 2rem; margin-bottom: 1rem;">🚪</div>
          <h3>Ingresos y Egresos Hosp.</h3>
          <p style="font-size: 0.875rem; color: var(--text-light); margin-top: 0.5rem;">
            Número de ingresos y egresos incluyendo piso general y servicios (UCI, UCIN, etc).
          </p>
          <div style="margin-top: 1.5rem; color: var(--text-light); font-weight: 600; font-size: 0.85rem;">
            ⏳ Próximamente
          </div>
        </div>

        <!-- REPORTE 5: Quirófanos -->
        <div class="card report-card" style="cursor: pointer; transition: var(--transition); border-top: 4px solid var(--secondary);" onclick="Modules.reportes.loadProcedimientosView()">
          <div style="font-size: 2rem; margin-bottom: 1rem;">🔪</div>
          <h3>Partos y Cirugías</h3>
          <p style="font-size: 0.875rem; color: var(--text-light); margin-top: 0.5rem;">
            Número de partos, cirugías y otros procedimientos realizados en los quirófanos.
          </p>
          <div style="margin-top: 1.5rem; color: var(--primary); font-weight: 600; font-size: 0.85rem;">
            Ver reporte →
          </div>
        </div>
      </div>
    `;
  },

  async loadUrgenciasView() {
    this.currentView = 'urgencias';
    this.renderViewWithFilter('🚑 Reporte Detallado de Urgencias', 'refreshUrgenciasBtn');
    document.getElementById("refreshUrgenciasBtn").addEventListener("click", () => this.fetchUrgenciasData());
    this.fetchUrgenciasData();
  },

  async loadEstudiosView() {
    this.currentView = 'estudios';
    this.renderViewWithFilter('🧪 Reporte de Estudios Paraclínicos', 'refreshEstudiosBtn');
    document.getElementById("refreshEstudiosBtn").addEventListener("click", () => this.fetchEstudiosData());
    this.fetchEstudiosData();
  },

  async loadProcedimientosView() {
    this.currentView = 'procedimientos';

    try {
      UI.showSkeleton("#contentArea");
      await Promise.all([this.loadQuirofanos(), this.loadTiposProcedimiento()]);
      this.renderProcedimientosView();

      document.getElementById("refreshProcedimientosBtn").addEventListener("click", () => this.fetchProcedimientosData());
      document.getElementById("filterHospital").addEventListener("change", (e) => {
        this.selectedHospital = e.target.value;
        this.fetchProcedimientosData();
      });
      document.getElementById("filterQuirofano").addEventListener("change", (e) => {
        this.selectedQuirofano = e.target.value;
        this.fetchProcedimientosData();
      });
      document.getElementById("filterTipoAtencion").addEventListener("change", (e) => {
        this.selectedTipoAtencion = e.target.value;
        this.fetchProcedimientosData();
      });
      document.getElementById("filterTipoProcedimiento").addEventListener("change", (e) => {
        this.selectedTipoProcedimiento = e.target.value;
        this.fetchProcedimientosData();
      });
      document.getElementById("filterFechaDesde").addEventListener("change", (e) => {
        this.selectedFechaDesde = e.target.value;
        this.fetchProcedimientosData();
      });
      document.getElementById("filterFechaHasta").addEventListener("change", (e) => {
        this.selectedFechaHasta = e.target.value;
        this.fetchProcedimientosData();
      });

      this.fetchProcedimientosData();
    } catch (error) {
      UI.toast.show("Error al cargar el reporte de procedimientos", "error");
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

  async loadTiposProcedimiento() {
    try {
      const response = await fetch("../api/tipoprocedimiento/listar_tipoprocedimiento.php", { credentials: "include" });
      const res = await response.json();
      if (res.ok) this.tiposProcedimiento = res.data;
    } catch (error) {
      console.error("Error al cargar tipos de procedimiento:", error);
    }
  },

  renderProcedimientosView() {
    const contentArea = document.getElementById("contentArea");
    const hospitalOptions = this.hospitales.map(h => 
      `<option value="${h.UNI_ORG}" ${this.selectedHospital === h.UNI_ORG ? 'selected' : ''}>${h.NOMUO}</option>`
    ).join('');

    const quirofanoOptions = this.quirofanos.map(q => 
      `<option value="${q.ID}" ${this.selectedQuirofano === String(q.ID) ? 'selected' : ''}>${q.NOMBREQUIROFANO}</option>`
    ).join('');

    const tipoProcedimientoOptions = this.tiposProcedimiento.map(t => 
      `<option value="${t.ID}" ${this.selectedTipoProcedimiento === String(t.ID) ? 'selected' : ''}>${t.NOMBREPROCEDIMIENTO}</option>`
    ).join('');

    contentArea.innerHTML = `
      <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; gap: 1rem;">
          <div>
            <button class="btn btn-secondary" style="margin-bottom: 0.5rem;" onclick="Modules.reportes.showMenu()">← Volver al Menú</button>
            <h2>🔪 Reporte de Partos, Cirugías y Otros Procedimientos</h2>
          </div>
          <button id="refreshProcedimientosBtn" class="btn btn-primary">🔄 Actualizar Datos</button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; background: var(--background); padding: 1rem; border-radius: 8px;">
          <div class="form-group" style="margin-bottom: 0;">
            <label for="filterHospital" style="font-weight: 600; font-size: 0.9rem;">Filtrar por Hospital</label>
            <select id="filterHospital" class="form-group" style="margin-bottom: 0; width: 100%;">
              <option value="">Todos los hospitales</option>
              ${hospitalOptions}
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="filterQuirofano" style="font-weight: 600; font-size: 0.9rem;">Filtrar por Quirófano</label>
            <select id="filterQuirofano" class="form-group" style="margin-bottom: 0; width: 100%;">
              <option value="">Todos los quirófanos</option>
              ${quirofanoOptions}
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="filterTipoAtencion" style="font-weight: 600; font-size: 0.9rem;">Tipo de Atención</label>
            <select id="filterTipoAtencion" class="form-group" style="margin-bottom: 0; width: 100%;">
              <option value="">Todos</option>
              <option value="1" ${this.selectedTipoAtencion === '1' ? 'selected' : ''}>Parto</option>
              <option value="2" ${this.selectedTipoAtencion === '2' ? 'selected' : ''}>Cirugía</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="filterTipoProcedimiento" style="font-weight: 600; font-size: 0.9rem;">Tipo de Procedimiento</label>
            <select id="filterTipoProcedimiento" class="form-group" style="margin-bottom: 0; width: 100%;">
              <option value="">Todos los procedimientos</option>
              ${tipoProcedimientoOptions}
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="filterFechaDesde" style="font-weight: 600; font-size: 0.9rem;">Fecha Desde</label>
            <input type="date" id="filterFechaDesde" class="form-group" style="margin-bottom: 0; width: 100%;" value="${this.selectedFechaDesde}">
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="filterFechaHasta" style="font-weight: 600; font-size: 0.9rem;">Fecha Hasta</label>
            <input type="date" id="filterFechaHasta" class="form-group" style="margin-bottom: 0; width: 100%;" value="${this.selectedFechaHasta}">
          </div>
        </div>
      </div>

      <div id="reportDataContainer">
        <p style="text-align: center; color: var(--text-light); padding: 3rem;">Cargando datos del reporte...</p>
      </div>
    `;
  },

  async fetchProcedimientosData() {
    try {
      const params = new URLSearchParams();
      if (this.selectedHospital) params.append("hospital_id", this.selectedHospital);
      if (this.selectedQuirofano) params.append("quirofano_id", this.selectedQuirofano);
      if (this.selectedTipoAtencion) params.append("tipo_atencion", this.selectedTipoAtencion);
      if (this.selectedTipoProcedimiento) params.append("tipo_procedimiento_id", this.selectedTipoProcedimiento);
      if (this.selectedFechaDesde) params.append("fecha_desde", this.selectedFechaDesde);
      if (this.selectedFechaHasta) params.append("fecha_hasta", this.selectedFechaHasta);

      const response = await fetch(`../api/reportes/procedimientos_quirofano.php?${params.toString()}`, { credentials: "include" });
      const res = await response.json();

      if (res.ok) {
        this.renderProcedimientosStats(res.data);
      } else {
        UI.toast.show(res.message, "error");
      }
    } catch (error) {
      UI.toast.show("Error al conectar con la API", "error");
    }
  },

  renderProcedimientosStats(data) {
    const container = document.getElementById("reportDataContainer");
    const resumen = data.resumen || {};
    const detallado = data.detallado || [];

    container.innerHTML = `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="border-left: 4px solid var(--primary);">
          <h3 style="color: var(--text-light); font-size: 0.9rem; text-transform: uppercase;">Total de Procedimientos</h3>
          <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary); margin: 0.5rem 0;">${resumen.total ?? 0}</div>
        </div>

        <div class="card" style="border-left: 4px solid #10b981;">
          <h3 style="color: var(--text-light); font-size: 0.9rem; text-transform: uppercase;">Partos</h3>
          <div style="font-size: 2.5rem; font-weight: 700; color: #10b981; margin: 0.5rem 0;">${resumen.partos ?? 0}</div>
        </div>

        <div class="card" style="border-left: 4px solid var(--danger);">
          <h3 style="color: var(--text-light); font-size: 0.9rem; text-transform: uppercase;">Cirugías</h3>
          <div style="font-size: 2.5rem; font-weight: 700; color: var(--danger); margin: 0.5rem 0;">${resumen.cirugias ?? 0}</div>
        </div>
      </div>

      <div class="card">
        <h3>📊 Detalle de Procedimientos</h3>
        <div class="table-container" style="margin-top: 1rem;">
          <table>
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Quirófano</th>
                <th>Tipo de Atención</th>
                <th>Tipo de Procedimiento</th>
                <th style="text-align: center;">Total</th>
              </tr>
            </thead>
            <tbody>
              ${detallado.map(item => `
                <tr>
                  <td>${item.FECHA ? new Date(item.FECHA + 'T00:00:00').toLocaleDateString() : 'Sin fecha'}</td>
                  <td>${item.NOMBREQUIROFANO || 'N/A'}</td>
                  <td>${item.TIPO_ATENCION || 'Sin clasificar'}</td>
                  <td>${item.NOMBREPROCEDIMIENTO || 'N/A'}</td>
                  <td style="text-align: center;"><span class="badge" style="background: #e5e7eb; color: #111827;">${item.total}</span></td>
                </tr>
              `).join('')}
              ${detallado.length === 0 ? '<tr><td colspan="5" style="text-align: center;">Sin datos</td></tr>' : ''}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  renderViewWithFilter(title, btnId) {
    const contentArea = document.getElementById("contentArea");
    let hospitalOptions = this.hospitales.map(h => 
      `<option value="${h.UNI_ORG}" ${this.selectedHospital === h.UNI_ORG ? 'selected' : ''}>${h.NOMUO}</option>`
    ).join('');

    contentArea.innerHTML = `
      <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
          <div>
            <button class="btn btn-secondary" style="margin-bottom: 0.5rem;" onclick="Modules.reportes.showMenu()">← Volver al Menú</button>
            <h2>${title}</h2>
          </div>
          <button id="${btnId}" class="btn btn-primary">🔄 Actualizar Datos</button>
        </div>

        <div style="background: var(--background); padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 1rem;">
          <label for="filterHospital" style="font-weight: 600; font-size: 0.9rem;">Filtrar por Hospital:</label>
          <select id="filterHospital" class="form-group" style="margin-bottom: 0; width: auto; min-width: 250px;">
            <option value="">Todos los hospitales</option>
            ${hospitalOptions}
          </select>
        </div>
      </div>

      <div id="reportDataContainer">
        <p style="text-align: center; color: var(--text-light); padding: 3rem;">Cargando datos del reporte...</p>
      </div>
    `;

    document.getElementById("filterHospital").addEventListener("change", (e) => {
      this.selectedHospital = e.target.value;
      if (this.currentView === 'urgencias') this.fetchUrgenciasData();
      if (this.currentView === 'estudios') this.fetchEstudiosData();
    });
  },

  async fetchUrgenciasData() {
    try {
      const url = `../api/reportes/urgencias.php?hospital_id=${this.selectedHospital}`;
      const response = await fetch(url, { credentials: "include" });
      const res = await response.json();

      if (res.ok) {
        this.renderUrgenciasStats(res.data);
      } else {
        UI.toast.show(res.message, "error");
      }
    } catch (error) {
      UI.toast.show("Error al conectar con la API", "error");
    }
  },

  renderUrgenciasStats(data) {
    const container = document.getElementById("reportDataContainer");
    container.innerHTML = `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="border-left: 4px solid var(--primary);">
          <h3 style="color: var(--text-light); font-size: 0.9rem; text-transform: uppercase;">Ingresos Totales</h3>
          <div id="statIngresos" style="font-size: 2.5rem; font-weight: 700; color: var(--primary); margin: 0.5rem 0;">${data.ingresos}</div>
        </div>
        
        <div class="card" style="border-left: 4px solid var(--danger);">
          <h3 style="color: var(--text-light); font-size: 0.9rem; text-transform: uppercase;">Egresos Totales</h3>
          <div id="statEgresos" style="font-size: 2.5rem; font-weight: 700; color: var(--danger); margin: 0.5rem 0;">${data.egresos}</div>
        </div>

        <div class="card" style="border-left: 4px solid #10b981;">
          <h3 style="color: var(--text-light); font-size: 0.9rem; text-transform: uppercase;">Pacientes en Área</h3>
          <div id="statActivos" style="font-size: 2.5rem; font-weight: 700; color: #10b981; margin: 0.5rem 0;">${Math.max(0, data.ingresos - data.egresos)}</div>
        </div>
      </div>

      <div class="card">
        <h3>🕒 Últimos Movimientos (Urgencias)</h3>
        <div id="urgRecientesTable" class="table-container" style="margin-top: 1rem;"></div>
      </div>
    `;
    this.renderUrgenciasTable(data.recientes);
  },

  async fetchEstudiosData() {
    try {
      const url = `../api/reportes/estudios.php?hospital_id=${this.selectedHospital}`;
      const response = await fetch(url, { credentials: "include" });
      const res = await response.json();

      if (res.ok) {
        this.renderEstudiosStats(res.data);
      } else {
        UI.toast.show(res.message, "error");
      }
    } catch (error) {
      UI.toast.show("Error al conectar con la API", "error");
    }
  },

  renderEstudiosStats(data) {
    const container = document.getElementById("reportDataContainer");
    
    let resumenHtml = data.resumen.map(r => `
      <div class="card" style="background: var(--background); border: none;">
        <h4 style="color: var(--text-light); font-size: 0.8rem; text-transform: uppercase;">${r.NOMBRELABORATORIO}</h4>
        <div style="font-size: 1.5rem; font-weight: 700; color: #8b5cf6;">${r.total_estudios} <span style="font-size: 0.9rem; font-weight: 400;">estudios</span></div>
      </div>
    `).join('');

    if (data.resumen.length === 0) {
      resumenHtml = '<p style="grid-column: 1/-1; text-align: center; color: var(--text-light);">No hay estudios registrados.</p>';
    }

    container.innerHTML = `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        ${resumenHtml}
      </div>

      <div class="card">
        <h3>📊 Desglose Detallado por Tipo de Estudio</h3>
        <div class="table-container" style="margin-top: 1rem;">
          <table>
            <thead>
              <tr>
                <th>Laboratorio / Depto</th>
                <th>Área Relacionada</th>
                <th>Tipo de Estudio</th>
                <th style="text-align: center;">Total Realizados</th>
              </tr>
            </thead>
            <tbody>
              ${data.detallado.map(d => `
                <tr>
                  <td><strong>${d.NOMBRELABORATORIO}</strong></td>
                  <td>${d.NOMBREAREA}</td>
                  <td>${d.NOMBREESTUDIO}</td>
                  <td style="text-align: center;"><span class="badge" style="background: #ede9fe; color: #8b5cf6; padding: 4px 12px;">${d.total}</span></td>
                </tr>
              `).join('')}
              ${data.detallado.length === 0 ? '<tr><td colspan="4" style="text-align: center;">Sin datos</td></tr>' : ''}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  renderUrgenciasTable(data) {
    const container = document.getElementById("urgRecientesTable");
    if (!data || data.length === 0) {
      container.innerHTML = `<p style="padding: 2rem; text-align: center; color: var(--text-light);">No hay movimientos registrados.</p>`;
      return;
    }

    let html = `
      <table>
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Fecha y Hora</th>
            <th>Habitación</th>
          </tr>
        </thead>
        <tbody>
    `;

    data.forEach(item => {
      const badge = item.tipo_mov === 'Ingreso' 
        ? '<span style="color: var(--primary); background: #eff6ff; padding: 4px 8px; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">Ingreso</span>' 
        : '<span style="color: var(--danger); background: #fef2f2; padding: 4px 8px; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">Egreso</span>';
      
      html += `
        <tr>
          <td>${badge}</td>
          <td>${new Date(item.fecha).toLocaleString()}</td>
          <td><strong>${item.NOMBREHABITACION}</strong></td>
        </tr>
      `;
    });

    html += `</tbody></table>`;
    container.innerHTML = html;
  },

  animateValue(id, start, end, duration) {
    const obj = document.getElementById(id);
    if (!obj) return;
    let startTimestamp = null;
    const step = (timestamp) => {
      if (!startTimestamp) startTimestamp = timestamp;
      const progress = Math.min((timestamp - startTimestamp) / duration, 1);
      obj.innerHTML = Math.floor(progress * (end - start) + start);
      if (progress < 1) window.requestAnimationFrame(step);
    };
    window.requestAnimationFrame(step);
  }
};
