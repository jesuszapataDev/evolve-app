import { login, register } from '../apiConfig.js'
import { countrySelect } from '/evolve-app/public/assets/js/helpers/countrySelect.js'

document.addEventListener('DOMContentLoaded', () => {
  countrySelect('telephone', '[data-phone-select]', null)

  // El inicializador general se encarga de activar ambos formularios
  if (typeof inicializarValidacionReactiva === 'function') {
    inicializarValidacionReactiva()
  }

  // --- Manejador para el Formulario de Sign In ---
  const formSignIn = document.getElementById('signInForm')
  if (formSignIn) {
    formSignIn.addEventListener('validation:success', async (evento) => {
      console.log('✅ Formulario Sign In OK. Enviando:', evento.detail.datos)

      console.log(evento.detail.datos)

      let response = await login(evento.detail.datos)

      if (response.value) {
        window.location.href = 'home'
      }
    })
    formSignIn.addEventListener('validation:failed', () => {
      console.log('❌ Formulario Sign In con errores.')
    })
  }

  // --- Manejador para el Formulario de Sign Up ---
  const formSignUp = document.getElementById('signUpForm')
  if (formSignUp) {
    formSignUp.addEventListener('validation:success', async (evento) => {
      console.log('✅ Formulario Sign Up OK. Enviando:', evento.detail.datos)

      let response = await register({
        ...evento.detail.datos,
      })

      if (response.value) {
        formSignIn.reset() // Resetea el formulario de Sign In
      }
    })
    formSignUp.addEventListener('validation:failed', () => {
      console.log('❌ Formulario Sign Up con errores.')
    })
  }
})
