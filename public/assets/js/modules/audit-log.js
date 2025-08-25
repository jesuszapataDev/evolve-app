/**
 * audit-log-manager.js
 * Gestiona la lógica para la página de visualización de logs de auditoría.
 * - Inicializa la tabla de auditoría con datos del servidor.
 * - Maneja la visualización de detalles de un log en un modal, incluyendo un mapa.
 * - Permite la exportación de todos los datos de la tabla a un archivo CSV.
 */

// Asumiendo que tienes un archivo de configuración de API para las llamadas fetch
import {
  getAllAuditLogs,
  getAuditLogById,
} from '/evolve-app/public/assets/js/apiConfig.js'

// Obtiene los datos pasados desde el archivo PHP (traducciones y locale)
const { locale, translations } = window.pageData

// --- FUNCIONES AUXILIARES PARA EL MODAL ---

/**
 * Crea y muestra una tabla con los cambios (valor antiguo y nuevo).
 * @param {HTMLElement} targetElement - El elemento contenedor donde se insertará la tabla.
 * @param {object} data - El objeto con los datos de los cambios.
 */
function createChangesTable(targetElement, data) {
  targetElement.innerHTML = '' // Limpia el contenido anterior
  if (!data || Object.keys(data).length === 0) {
    targetElement.textContent =
      translations.audit_no_changes_text || 'No changes recorded.'
    return
  }

  const table = document.createElement('table')
  table.className = 'table table-bordered table-sm mb-0'
  table.innerHTML = `
    <thead class="table-light">
      <tr>
        <th>${translations.audit_fiel_text || 'Field'}</th>
        <th>${translations.audit_old_value_text || 'Old Value'}</th>
        <th>${translations.audit_new_value_text || 'New Value'}</th>
      </tr>
    </thead>`

  const tbody = document.createElement('tbody')
  for (const key in data) {
    const row = tbody.insertRow()
    row.insertCell().textContent = key
    const cellOld = row.insertCell()
    cellOld.textContent = data[key].old ?? 'N/A'
    cellOld.classList.add('text-break') // Evita que el texto largo rompa el layout
    const cellNew = row.insertCell()
    cellNew.textContent = data[key].new ?? 'N/A'
    cellNew.classList.add('text-break')
  }
  table.appendChild(tbody)
  targetElement.appendChild(table)
}

/**
 * Crea y muestra una tabla simple de clave-valor.
 * @param {HTMLElement} targetElement - El elemento contenedor.
 * @param {object} data - El objeto con los datos a mostrar.
 */
function createKeyValueTable(targetElement, data) {
  targetElement.innerHTML = ''
  if (!data || Object.keys(data).length === 0) {
    targetElement.textContent =
      translations.audit_no_data_available || 'No data available.'
    return
  }

  const table = document.createElement('table')
  table.className = 'table table-bordered table-sm mb-0'
  table.innerHTML = `
    <thead class="table-light">
      <tr>
        <th>${translations.audit_property_text || 'Property'}</th>
        <th>${translations.audit_value_text || 'Value'}</th>
      </tr>
    </thead>`

  const tbody = document.createElement('tbody')
  for (const key in data) {
    const row = tbody.insertRow()
    row.insertCell().textContent = key
    const cellValue = row.insertCell()
    cellValue.textContent = data[key] ?? 'N/A'
    cellValue.classList.add('text-break')
  }
  table.appendChild(tbody)
  targetElement.appendChild(table)
}

// --- FORMATEADORES PARA LA TABLA (DEBEN SER GLOBALES) ---

/**
 * Formatea un timestamp a un formato legible.
 * @param {string} value - El valor del timestamp.
 * @returns {string} La fecha y hora formateadas.
 */
window.timestampFormatter = function (value) {
  if (!value) return '-'
  // Usamos Day.js si está disponible para un mejor formato, si no, un Date simple.
  return typeof dayjs !== 'undefined'
    ? dayjs(value).format('YYYY-MM-DD HH:mm:ss')
    : new Date(value).toLocaleString()
}

/**
 * Genera el HTML para el botón de "Ver detalles" en la columna de acciones.
 * @param {*} value - El ID del log de auditoría (inyectado desde el campo 'acciones').
 * @returns {string} El HTML del botón.
 */
window.auditLogActionFormatter = function (value) {
  const title = translations.audit_view_button_title || 'View Details'
  return `
    <button class="btn btn-view action-icon view-details" data-id="${value}" title="${title}">
        <i class="mdi mdi-eye-outline"></i>
    </button>`
}

// --- LÓGICA PRINCIPAL ---

// Se ejecuta cuando el DOM está completamente cargado
document.addEventListener('DOMContentLoaded', () => {
  const $table = $('#auditLogTable')
  let leafletMap = null // Variable para mantener la instancia del mapa

  /**
   * Carga los datos de auditoría desde la API y los muestra en la tabla.
   */
  async function loadAuditLogData() {
    $table.bootstrapTable('showLoading')
    try {
      const res = await getAllAuditLogs()
      // Mapear explícitamente el audit_id al campo 'acciones'.
      const rows = res.data.map((row) => ({
        ...row,
        acciones: row.audit_id,
        full_name: row.full_name || 'N/A',
      }))
      $table.bootstrapTable('load', rows)
    } catch (error) {
      console.error('Error al cargar los logs de auditoría:', error)
      $table.bootstrapTable('removeAll')
      Swal.fire(
        'Error',
        'No se pudieron cargar los registros de auditoría.',
        'error'
      )
    } finally {
      $table.bootstrapTable('hideLoading')
    }
  }

  /**
   * Muestra los detalles de un log de auditoría específico en el modal.
   * @param {string} auditId - El ID del log a consultar.
   */
  async function showAuditDetails(auditId) {
    try {
      const res = await getAuditLogById(auditId)
      if (!res.data) {
        Swal.fire(
          'Error',
          'No se pudieron encontrar los detalles del log.',
          'error'
        )
        return
      }
      const data = res.data

      // Rellena todos los campos del modal con los datos obtenidos de la API
      document.getElementById('detail_audit_id').textContent =
        data.audit_id || 'N/A'
      document.getElementById('detail_action_timestamp').textContent =
        window.timestampFormatter(data.action_timestamp)
      document.getElementById('detail_action_timezone').textContent =
        data.action_timezone || 'N/A'
      document.getElementById('detail_action_by').textContent =
        data.action_by || 'N/A'
      document.getElementById('detail_full_name').textContent =
        data.full_name || 'N/A'
      document.getElementById('detail_table_name').textContent =
        data.table_name || 'N/A'
      document.getElementById('detail_record_id').textContent =
        data.record_id || 'N/A'
      document.getElementById('detail_client_ip').textContent =
        data.client_ip || 'N/A'
      document.getElementById('detail_client_country').textContent =
        data.client_country || 'N/A'
      document.getElementById('detail_client_region').textContent =
        data.client_region || 'N/A'
      document.getElementById('detail_client_city').textContent =
        data.client_city || 'N/A'
      document.getElementById('detail_client_zipcode').textContent =
        data.client_zipcode || 'N/A'
      document.getElementById('detail_client_coordinates').textContent =
        data.client_coordinates || 'N/A'
      document.getElementById('detail_geo_ip_timestamp').textContent =
        data.geo_ip_timestamp
          ? window.timestampFormatter(data.geo_ip_timestamp)
          : 'N/A'
      document.getElementById('detail_geo_ip_timezone').textContent =
        data.geo_ip_timezone || 'N/A'
      document.getElementById('detail_client_hostname').textContent =
        data.client_hostname || 'N/A'
      document.getElementById('detail_client_os').textContent =
        data.client_os || 'N/A'
      document.getElementById('detail_client_browser').textContent =
        data.client_browser || 'N/A'
      document.getElementById('detail_domain_name').textContent =
        data.domain_name || 'N/A'
      document.getElementById('detail_server_hostname').textContent =
        data.server_hostname || 'N/A'
      document.getElementById('detail_request_uri').textContent =
        data.request_uri || 'N/A'
      document.getElementById('detail_user_agent').textContent =
        data.user_agent || 'N/A'

      // --- LÓGICA DE BADGES RESTAURADA ---
      const userTypeEl = document.getElementById('detail_user_type')
      const userType = data.user_type || 'N/A'
      let userTypeClass = 'bg-secondary'
      if (userType.toLowerCase() === 'admin') {
        userTypeClass = 'bg-primary'
      } else if (userType.toLowerCase() === 'user') {
        userTypeClass = 'bg-info'
      }
      userTypeEl.innerHTML = `<span class="badge ${userTypeClass}">${userType}</span>`

      const actionTypeEl = document.getElementById('detail_action_type')
      const actionType = data.action_type || 'N/A'
      let actionTypeClass = 'bg-dark'
      switch (actionType.toUpperCase()) {
        case 'CREATE':
          actionTypeClass = 'bg-success'
          break
        case 'UPDATE':
          actionTypeClass = 'bg-warning text-dark'
          break
        case 'DELETE':
          actionTypeClass = 'bg-danger'
          break
        case 'LOGIN':
          actionTypeClass = 'bg-info'
          break
      }
      actionTypeEl.innerHTML = `<span class="badge ${actionTypeClass}">${actionType}</span>`
      // --- FIN DE LÓGICA DE BADGES ---

      // Parsea y muestra las tablas de cambios y datos completos
      const changesData = data.changes ? JSON.parse(data.changes) : {}
      const fullRowData = data.full_row ? JSON.parse(data.full_row) : {}
      createChangesTable(document.getElementById('detail_changes'), changesData)
      createKeyValueTable(
        document.getElementById('detail_full_row'),
        fullRowData
      )

      // Obtener la instancia del modal justo antes de mostrarla
      const detailModalEl = document.getElementById('auditDetailModal')
      const detailModal = bootstrap.Modal.getOrCreateInstance(detailModalEl)
      detailModal.show()

      // Lógica del mapa Leaflet
      const mapContainer = document.getElementById('audit_map_container')
      if (leafletMap) {
        leafletMap.remove()
        leafletMap = null
      }

      if (data.client_coordinates && data.client_coordinates.includes(',')) {
        const [lat, lon] = data.client_coordinates
          .split(',')
          .map((v) => parseFloat(v.trim()))
        if (!isNaN(lat) && !isNaN(lon)) {
          mapContainer.style.display = 'block'
          leafletMap = L.map(mapContainer).setView([lat, lon], 13)
          L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
          ).addTo(leafletMap)
          L.marker([lat, lon]).addTo(leafletMap)
          setTimeout(() => leafletMap.invalidateSize(), 200)
        } else {
          mapContainer.style.display = 'none'
        }
      } else {
        mapContainer.style.display = 'none'
      }
    } catch (error) {
      console.error(`Error al obtener el log de auditoría ${auditId}:`, error)
      Swal.fire(
        'Error',
        'Error al cargar los detalles. Por favor, revisa la consola.',
        'error'
      )
    }
  }

  // --- INICIALIZACIÓN Y MANEJO DE EVENTOS ---

  $table.bootstrapTable({
    locale: locale,
    toolbar: '#toolbar',
    search: true,
    showColumns: true,
    pagination: true,
    showPaginationSwitch: true,
    pageList: [10, 25, 50, 100],
    pageSize: 10,
    sidePagination: 'client',
  })

  document.getElementById('toolbar').classList.remove('d-none')
  loadAuditLogData()

  document
    .getElementById('exportAuditCsvBtn')
    .addEventListener('click', async function () {
      Swal.fire({
        title: translations.export_loading_title || 'Exporting...',
        text:
          translations.export_loading_text || 'Generating file, please wait...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => Swal.showLoading(),
      })

      try {
        const response = await fetch('api/auditlog/export/1')
        const contentType = response.headers.get('Content-Type')

        if (response.ok && contentType && contentType.includes('text/csv')) {
          const blob = await response.blob()
          const url = window.URL.createObjectURL(blob)
          const a = document.createElement('a')
          a.href = url
          a.download = `${
            translations.csv_filename_prefix_audit_logs || 'audit_logs'
          }.csv`
          document.body.appendChild(a)
          a.click()
          a.remove()
          window.URL.revokeObjectURL(url)
          Swal.close()
        } else {
          const res = await response.json()
          Swal.fire({
            icon: 'info',
            title: translations.no_records_title || 'No Records',
            text:
              res.message ||
              translations.no_records_text ||
              'No data available to export.',
          })
        }
      } catch (error) {
        console.error('Export error:', error)
        Swal.fire({
          icon: 'error',
          title: translations.export_error_title || 'Export Error',
          text:
            translations.export_error_text ||
            'An error occurred while exporting.',
        })
      }
    })

  $(document).on('click', '.view-details', function () {
    const auditId = $(this).data('id')
    if (auditId) {
      showAuditDetails(auditId)
    }
  })
})
