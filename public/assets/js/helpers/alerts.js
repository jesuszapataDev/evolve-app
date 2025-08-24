export function showAlert(value, message) {
  Swal.fire({
    icon: value ? 'success' : 'error',
    title: value
      ? traducciones.successTitle_helper
      : traducciones.errorTitle_helper,
    text:
      message ||
      (value
        ? traducciones.successMessage_helper
        : traducciones.errorMessage_helper),
    confirmButtonText: traducciones.confirmButtonText_helper,
  })
}
