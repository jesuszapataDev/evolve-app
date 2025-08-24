export function showAlert(value, message) {
  Swal.fire({
    icon: value ? 'success' : 'error',
    title: value ? traducciones.successTitle : traducciones.errorTitle,
    text: value ? traducciones.successText : traducciones.errorText,
    confirmButtonText: traducciones.confirmButton,
  })
}
