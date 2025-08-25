/**
 * session-audit-manager.js
 * Gestiona la lógica para la página de auditoría de sesiones.
 * - Inicializa la tabla de auditoría de sesiones.
 * - Maneja la visualización de detalles de sesión en un modal.
 * - Gestiona la configuración del tiempo de inactividad de la sesión.
 * - Controla la expulsión de sesiones activas.
 * - Permite la exportación de datos a CSV.
 */

// Obtiene los datos pasados desde el archivo PHP a través del objeto window
const { idioma, locale, translations } = window.pageData

// --- FUNCIONES DE UTILIDAD ---

/**
 * Oculta todos los modales de Bootstrap que estén actualmente abiertos y limpia el fondo.
 */
function hideAllModals() {
  document.querySelectorAll('.modal.show').forEach((modalEl) => {
    const modalInstance = bootstrap.Modal.getInstance(modalEl)
    if (modalInstance) modalInstance.hide()
  })
  document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove())
  document.body.classList.remove('modal-open')
  document.body.style.overflow = ''
  document.body.style.paddingRight = ''
}

/**
 * Formatea una cadena de fecha y hora a un formato localizado.
 * @param {string} dateStr - La cadena de fecha a formatear.
 * @returns {string} La fecha formateada o un guion si la entrada no es válida.
 */
function formatDateTime(dateStr) {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  if (isNaN(date.getTime())) return dateStr
  return typeof dayjs !== 'undefined'
    ? dayjs(dateStr).format('YYYY-MM-DD HH:mm:ss')
    : date.toLocaleString()
}

// --- FORMATEADORES PARA LA TABLA ---

/**
 * Genera el HTML para la columna de "Acciones" en la tabla.
 * @param {*} value - El valor de la celda (no se usa).
 * @param {object} row - El objeto de datos completo para la fila actual.
 * @returns {string} El HTML para los botones de acción.
 */
function detalleFormatter(value, row) {
  const isActive = row.session_status === 'active'
  const viewTitle = translations.audit_view_button_title || 'View Details'
  const kickTitle = translations.session_kick_button_title || 'Kick Session'

  return `
        <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">
            <button class="btn btn-view action-icon btn-sm btn-view-details" title="${viewTitle}">
                <i class="mdi mdi-eye-outline"></i>
            </button>
            <button class="btn btn-delete action-icon btn-sm btn-kick-session ${
              !isActive ? 'invisible pe-none' : ''
            }" title="${kickTitle}" ${!isActive ? 'disabled' : ''}>
                <i class="mdi mdi-logout"></i>
            </button>
        </div>
    `
}

// --- LÓGICA PRINCIPAL ---

/**
 * Muestra un modal con los detalles completos de una sesión.
 * @param {object} row - Los datos de la fila de la sesión seleccionada.
 */
function mostrarDetalle(row) {
  const modalLabel = document.getElementById('sessionDetailModalLabel')
  modalLabel.textContent =
    translations.session_detail_modal_title || 'Session Details'

  const labels = {
    session_id: translations.session_id || 'Session ID',
    user_id: translations.user_id || 'User ID',
    user_name: translations.user_name || 'Username',
    user_type: translations.user_type || 'User Type',
    full_name: translations.full_name || 'Full Name',
    login_time: translations.login_time || 'Login Time',
    logout_time: translations.logout_time || 'Logout Time',
    inactivity_duration:
      translations.inactivity_duration || 'Inactivity Duration',
    login_success: translations.login_success || 'Login Success',
    failure_reason: translations.failure_reason || 'Failure Reason',
    session_status: translations.session_status || 'Session Status',
    ip_address: translations.ip_address || 'IP Address',
    city: translations.city || 'City',
    region: translations.region || 'Region',
    country: translations.country || 'Country',
    zipcode: translations.audit_modal_field_client_zipcode || 'Zipcode',
    coordinates: translations.coordinates || 'Coordinates',
    hostname: translations.hostname || 'Hostname',
    os: translations.os || 'OS',
    browser: translations.browser || 'Browser',
    user_agent: translations.user_agent || 'User Agent',
    device_id: translations.device_id || 'Device ID',
    is_mobile: translations.is_mobile || 'Is Mobile',
    created_at: translations.created_at || 'Created At',
  }

  const camposOrdenados = [
    'session_id',
    'user_id',
    'user_type',
    'full_name',
    'user_name',
    'login_time',
    'logout_time',
    'inactivity_duration',
    'login_success',
    'failure_reason',
    'session_status',
    'ip_address',
    'city',
    'region',
    'country',
    'zipcode',
    'coordinates',
    'hostname',
    'os',
    'browser',
    'user_agent',
    'device_id',
    'is_mobile',
    'created_at',
  ]

  let html = ''
  camposOrdenados.forEach((key) => {
    const label = labels[key] || key
    const value = row[key] ?? '-'
    let displayValue = value

    if (['login_time', 'logout_time', 'created_at'].includes(key)) {
      displayValue = formatDateTime(value)
    } else if (key === 'inactivity_duration' && value !== '-') {
      const unidad = translations.seconds_unit || 'seconds'
      displayValue = `${value} ${unidad}`
    } else if (key === 'session_status') {
      const val = (value || '').toLowerCase()
      const statusLabel = translations['status_' + val] || value
      let colorClass = 'bg-secondary-subtle text-muted'
      if (val === 'active') colorClass = 'bg-info-subtle text-info'
      else if (val === 'expired') colorClass = 'bg-danger-subtle text-danger'
      else if (val === 'kicked') colorClass = 'bg-warning-subtle text-warning'
      displayValue = `<span class="badge ${colorClass}">${statusLabel}</span>`
    } else if (key === 'is_mobile') {
      const isMobile = value === true || value === 1 || value === '1'
      const mobileLabel = isMobile
        ? translations.is_mobile_yes || 'Mobile'
        : translations.is_mobile_no || 'Desktop'
      const colorClass = isMobile
        ? 'bg-success-subtle text-success'
        : 'bg-primary-subtle text-primary'
      displayValue = `<span class="badge ${colorClass}">${mobileLabel}</span>`
    } else if (key === 'login_success') {
      const isSuccess = value === true || value === 1 || value === '1'
      const successLabel = isSuccess
        ? translations.login_success_yes || 'Successful'
        : translations.login_success_no || 'Failed'
      const colorClass = isSuccess
        ? 'bg-success-subtle text-success'
        : 'bg-danger-subtle text-danger'
      displayValue = `<span class="badge ${colorClass}">${successLabel}</span>`
    } else if (key === 'failure_reason' && value !== '-') {
      const reasonLabel = translations['failure_' + value] || value
      displayValue = `<span class="badge bg-danger-subtle text-danger">${reasonLabel}</span>`
    }

    html += `<dt class="col-sm-4">${label}</dt><dd class="col-sm-8">${displayValue}</dd>`
  })

  document.getElementById('sessionDetailContent').innerHTML = html
  const detailModal = bootstrap.Modal.getOrCreateInstance(
    document.getElementById('sessionDetailModal')
  )
  detailModal.show()
}

/**
 * Inicia el proceso para expulsar la sesión de un usuario.
 * @param {object} row - Los datos de la fila de la sesión a expulsar.
 */
function expulsarSesion(row) {
  Swal.fire({
    title: translations.confirmKickTitle || 'Expel user?',
    text: translations.confirmKickText
      ? translations.confirmKickText.replace('%s', row.full_name)
      : `Force logout for ${row.full_name}?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: translations.confirmYes || 'Yes, kick',
    cancelButtonText: translations.confirmCancel || 'Cancel',
    confirmButtonColor: '#d33',
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`api/session-audit/kick/1`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ session_id: row.session_id }),
      })
        .then((response) => response.json())
        .then((res) => {
          if (res.value) {
            Swal.fire({
              icon: 'success',
              title: translations.kickSuccessTitle || 'Session ended',
              text: translations.kickSuccessText || 'User has been logged out.',
            })
            $('#sessionAuditTable').bootstrapTable('refresh')
          } else {
            Swal.fire({
              icon: 'error',
              title: translations.kickErrorTitle || 'Error',
              text: res.message || 'Could not force logout.',
            })
          }
        })
        .catch((error) => {
          console.error('Error al expulsar sesión:', error)
          Swal.fire('Error', 'Internal server error. Check console.', 'error')
        })
    }
  })
}

// --- INICIALIZACIÓN ---

document.addEventListener('DOMContentLoaded', () => {
  const tableId = '#sessionAuditTable'
  const $table = $(tableId)

  // Inicializa la tabla Bootstrap con opciones en JavaScript
  $table.bootstrapTable({
    url: 'api/session-audit',
    locale: locale,
    toolbar: '#toolbar',
    search: true,
    pagination: true,
    pageSize: 5,
    pageList: [5, 10, 20, 50, 100],
    showColumns: true,
    showPaginationSwitch: true,
    responseHandler: (res) => res.data || [],
    columns: [
      {
        field: 'session_id',
        title: translations.session_id || 'Session ID',
        sortable: true,
      },
      {
        field: 'user_id',
        title: translations.user_id || 'User ID',
        sortable: true,
      },
      {
        field: 'user_name',
        title: translations.user_name || 'Username',
        sortable: true,
      },
      {
        field: 'user_type',
        title: translations.user_type || 'User Type',
        formatter: (tipo) =>
          tipo ? tipo.charAt(0).toUpperCase() + tipo.slice(1) : '',
        sortable: true,
      },
      {
        field: 'full_name',
        title: translations.full_name || 'Full Name',
        sortable: true,
      },
      {
        field: 'login_time',
        title: translations.login_time || 'Login Time',
        sortable: true,
        formatter: formatDateTime,
      },
      {
        field: 'logout_time',
        title: translations.logout_time || 'Logout Time',
        sortable: true,
        formatter: formatDateTime,
      },
      {
        field: 'actions',
        title: translations.audit_table_column_actions || 'Details',
        align: 'center',
        formatter: detalleFormatter,
        events: {
          'click .btn-view-details': (e, value, row) => mostrarDetalle(row),
          'click .btn-kick-session': (e, value, row) => expulsarSesion(row),
        },
      },
    ],
  })

  // --- LÓGICA DE EVENTOS RESTAURADA ---

  // Listener para el botón de configuración de sesión
  document
    .getElementById('btnSessionConfig')
    ?.addEventListener('click', async () => {
      hideAllModals()
      const modal = new bootstrap.Modal(
        document.getElementById('sessionConfigModal')
      )
      try {
        const res = await fetch('api/session-config')
        const json = await res.json()
        document.getElementById('inactivityTime').value =
          json.data?.timeout_minutes || ''
      } catch (error) {
        console.error('Error al obtener configuración de sesión:', error)
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Could not load session config.',
        })
      }
      modal.show()
    })

  // Listener para el envío del formulario de configuración
  document
    .getElementById('sessionConfigForm')
    ?.addEventListener('submit', async function (e) {
      e.preventDefault()
      const timeout = parseInt(document.getElementById('inactivityTime').value)
      if (isNaN(timeout) || timeout < 1) {
        return Swal.fire({
          icon: 'warning',
          title: translations.invalid_timeout_title || 'Invalid Value',
          text:
            translations.invalid_timeout_text ||
            'Please enter a valid number greater than 0.',
        })
      }
      Swal.fire({
        title: translations.saving || 'Saving...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      })
      try {
        const response = await fetch('api/session-config', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ timeout_minutes: timeout }),
        })
        const result = await response.json()
        if (result.value) {
          Swal.fire({
            icon: 'success',
            title: translations.update_success_title || 'Updated!',
            text:
              translations.update_success_text ||
              'Session timeout updated successfully.',
          })
          bootstrap.Modal.getInstance(
            document.getElementById('sessionConfigModal')
          ).hide()
        } else {
          throw new Error(result.message)
        }
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: translations.update_error_title || 'Update Error',
          text:
            error.message ||
            translations.update_error_text ||
            'Could not update session timeout.',
        })
      }
    })

  // Listener para el botón de exportar a CSV
  document
    .getElementById('btnExportCSV')
    ?.addEventListener('click', async () => {
      Swal.fire({
        title: translations.exportLoadingTitle || 'Exporting...',
        text:
          translations.exportLoadingText || 'Generating file, please wait...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      })
      try {
        const response = await fetch('api/session-audit/export/1')
        const contentType = response.headers.get('Content-Type')
        if (response.ok && contentType && contentType.includes('text/csv')) {
          const blob = await response.blob()
          const url = window.URL.createObjectURL(blob)
          const a = document.createElement('a')
          a.href = url
          a.download = `${
            translations.csvFilenamePrefix_session_audit || 'session_audit'
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
            title: translations.noRecordsTitle || 'No Records',
            text:
              res.message ||
              translations.noRecordsText ||
              'No data available to export.',
          })
        }
      } catch (error) {
        console.error('Error de exportación:', error)
        Swal.fire({
          icon: 'error',
          title: translations.exportErrorTitle || 'Export Error',
          text:
            translations.exportErrorText ||
            'An error occurred while exporting.',
        })
      }
    })
})
