/**
 * users-manager.js
 * Manages all logic for the user management page.
 * - Initializes the user table.
 * - Handles create, edit, delete, and view operations.
 * - Manages form modals for user data manipulation.
 */

// Assume API functions are defined elsewhere, e.g., in apiConfig.js
import {
  getUserById,
  createUser,
  updateUser,
  deleteUser,
} from '../apiConfig.js'
import { initTimezoneSelect } from '../helpers/timezoneSelect.js'
import { countrySelect } from '/evolve-app/public/assets/js/helpers/countrySelect.js'

// Get data passed from PHP
const { locale, translations } = window.pageData

// --- TABLE FORMATTERS ---

function statusFormatter(value) {
  const isActive = value === '1' || value === 1
  const label = isActive
    ? translations.status_active || 'Active'
    : translations.status_inactive || 'Inactive'
  const colorClass = isActive
    ? 'bg-success-subtle text-success'
    : 'bg-danger-subtle text-danger'
  return `<span class="badge ${colorClass}">${label}</span>`
}

function roleFormatter(value) {
  const is_admin = value === 'administrator'
  const label = is_admin
    ? translations.role_admin || 'Administrator'
    : translations.role_user || 'User'
  const colorClass = is_admin
    ? 'bg-primary-subtle text-primary'
    : 'bg-secondary-subtle text-secondary'
  return `<span class="badge ${colorClass}">${label}</span>`
}

function actionsFormatter(value, row) {
  const viewTitle = translations.view_details_title || 'View Details'
  const editTitle = translations.edit_user_title || 'Edit User'
  const deleteTitle = translations.delete_user_title || 'Delete User'
  return `
        <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">
            <button class="btn btn-view action-icon btn-sm btn-view-details" data-user-id="${row.user_id}" title="${viewTitle}">
                <i class="mdi mdi-eye-outline"></i>
            </button>
            <button class="btn btn-edit action-icon btn-sm btn-edit-user" data-user-id="${row.user_id}" title="${editTitle}">
                <i class="mdi mdi-pencil-outline"></i>
            </button>
            <button class="btn btn-delete action-icon btn-sm btn-delete-user" data-user-id="${row.user_id}" title="${deleteTitle}">
                <i class="mdi mdi-delete-outline"></i>
            </button>
        </div>
    `
}

// --- MODAL AND FORM LOGIC ---

function showUserDetails(userId) {
  const userData = $('#usersTable').bootstrapTable('getRowByUniqueId', userId)
  if (!userData) return

  // Main user info
  const mainInfoHtml = `
        <div class="text-center mb-4">
            <h4 class="mb-1">${userData.first_name} ${userData.last_name}</h4>
            <p class="text-muted mb-2">${userData.email}</p>
            <div>
                ${roleFormatter(userData.rol)}
                ${statusFormatter(userData.status)}
            </div>
        </div>
    `

  // Detailed info list
  const detailsListHtml = `
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between">
                <strong>${translations.user_id || 'User ID'}:</strong>
                <span class="text-muted small">${userData.user_id}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <strong>${translations.sex || 'Sex'}:</strong>
                <span>${
                  userData.sex === 'm'
                    ? translations.sex_m || 'Male'
                    : translations.sex_f || 'Female'
                }</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <strong>${translations.telephone || 'Telephone'}:</strong>
                <span>${userData.telephone || '-'}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <strong>${translations.timezone || 'Timezone'}:</strong>
                <span>${userData.timezone || '-'}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <strong>${translations.created_at || 'Created At'}:</strong>
                <span>${userData.created_at || '-'}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <strong>${translations.updated_at || 'Updated At'}:</strong>
                <span>${userData.updated_at || '-'}</span>
            </li>
        </ul>
    `

  // Combine and inject into the modal
  document.getElementById('userDetailContent').innerHTML =
    mainInfoHtml + detailsListHtml
  const detailModal = bootstrap.Modal.getOrCreateInstance(
    document.getElementById('userDetailModal')
  )
  detailModal.show()
}

async function openUserFormModal(userId = null) {
  const form = document.getElementById('userForm')
  const modalLabel = document.getElementById('userFormModalLabel')
  const passwordField = document.getElementById('password')
  form.reset()
  document.getElementById('userId').value = ''

  initTimezoneSelect('timezoneSelect', '#editUserModal')

  if (userId) {
    // --- EDIT MODE ---
    modalLabel.textContent = translations.edit_user_title || 'Edit User'
    passwordField.setAttribute(
      'placeholder',
      translations.password_leave_blank || 'Leave blank to keep current'
    )
    passwordField.removeAttribute('required')

    // Placeholder: Replace with actual API call: getUserById(userId)
    const user = await getUserById(userId)
    const userData = user.data
    countrySelect(
      'telephone',
      '[data-phone-select]',
      userData.telephone,
      '.modal-body'
    )

    if (userData) {
      document.getElementById('firstName').value = userData.first_name
      document.getElementById('lastName').value = userData.last_name
      document.getElementById('email').value = userData.email
      document.getElementById('telephone').value = userData.telephone

      document.getElementById('rol').value = userData.rol
      document.getElementById('status').value = userData.status
      document.getElementById('timezone').value = userData.timezone
    }
  } else {
    countrySelect('telephone', '[data-phone-select]', null, '.modal-body')

    // --- CREATE MODE ---
    modalLabel.textContent = translations.add_user_title || 'Add User'
    passwordField.setAttribute('placeholder', '')
    passwordField.setAttribute('required', 'required')
  }

  const formModal = bootstrap.Modal.getOrCreateInstance(
    document.getElementById('userFormModal')
  )
  formModal.show()
}

function deleteUserRow(userId) {
  const userData = $('#usersTable').bootstrapTable('getRowByUniqueId', userId)
  Swal.fire({
    title: translations.confirm_delete_title || 'Are you sure?',
    text: `${translations.confirm_delete_text || 'You are about to delete'} ${
      userData.first_name
    } ${userData.last_name}`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: translations.confirm_yes_delete || 'Yes, delete it!',
    cancelButtonText: translations.cancel || 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      // Placeholder: Replace with actual API call: deleteUser(userId)
      console.log(`Deleting user ${userId}`)
      Swal.fire('Deleted!', 'The user has been deleted.', 'success')
      $('#usersTable').bootstrapTable('refresh')
    }
  })
}

// --- INITIALIZATION ---

document.addEventListener('DOMContentLoaded', () => {
  const $table = $('#usersTable')

  $table.bootstrapTable({
    url: 'api/users', // Replace with your actual API endpoint
    uniqueId: 'user_id',
    locale: locale,
    toolbar: '#toolbar',
    search: true,
    pagination: true,
    pageSize: 10,
    pageList: [10, 25, 50],
    showColumns: true,
    responseHandler: (res) => res.data || [],
    columns: [
      {
        field: 'first_name',
        title: translations.first_name || 'First Name',
        sortable: true,
      },
      {
        field: 'last_name',
        title: translations.last_name || 'Last Name',
        sortable: true,
      },
      {
        field: 'email',
        title: translations.email || 'Email',
        sortable: true,
      },
      {
        field: 'rol',
        title: translations.rol || 'Role',
        sortable: true,
        formatter: roleFormatter,
      },
      {
        field: 'status',
        title: translations.status || 'Status',
        sortable: true,
        align: 'center',
        formatter: statusFormatter,
      },
      {
        field: 'actions',
        title: translations.actions || 'Actions',
        align: 'center',
        formatter: actionsFormatter,
      },
    ],
  })

  // --- EVENT LISTENERS ---
  document.getElementById('btn-add-user').addEventListener('click', () => {
    openUserFormModal()
  })

  document.addEventListener('click', (e) => {
    if (e.target.closest('.btn-view-details')) {
      showUserDetails(e.target.closest('.btn-view-details').dataset.userId)
    }
    if (e.target.closest('.btn-edit-user')) {
      openUserFormModal(e.target.closest('.btn-edit-user').dataset.userId)
    }
    if (e.target.closest('.btn-delete-user')) {
      deleteUserRow(e.target.closest('.btn-delete-user').dataset.userId)
    }
  })

  document.getElementById('userForm').addEventListener('submit', function (e) {
    e.preventDefault()
    const userId = document.getElementById('userId').value
    const formData = new FormData(this)
    const data = Object.fromEntries(formData.entries())

    if (userId) {
      // Placeholder for updateUser(userId, data)
      console.log('Updating user:', data)
      Swal.fire('Success', 'User updated successfully!', 'success')
    } else {
      // Placeholder for createUser(data)
      console.log('Creating user:', data)
      Swal.fire('Success', 'User created successfully!', 'success')
    }

    bootstrap.Modal.getInstance(document.getElementById('userFormModal')).hide()
    $table.bootstrapTable('refresh')
  })
})
