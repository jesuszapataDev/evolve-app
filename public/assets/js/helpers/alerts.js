export function showAlert(value, message) {
  Swal.fire({
    icon: value ? 'success' : 'error',
    title: value ? t.successTitle : t.errorTitle,
    text: value ? t.successText : t.errorText,
    confirmButtonText: t.confirmButton,
  })
}
