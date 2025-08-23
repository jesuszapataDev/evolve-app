/**
 * Objeto con reglas de validación comunes para reutilizar.
 */
const reglasDeValidacion = {
  // --- Estas reglas no cambian ---
  noVacio: (valor) => {
    if (typeof valor === 'boolean') return valor
    if (Array.isArray(valor)) return valor.length > 0
    return valor && valor.trim() !== ''
  },
  codigoAlfanumerico: (valor) => /^[A-Z0-9]+$/.test(valor),
  esNumeroPositivo: (valor) => {
    const num = Number(valor)
    return !isNaN(num) && num > 0
  },
  email: (valor) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor),
  formatoMoneda: (valor) => {
    return /^\d+(\.\d{1,2})?$/.test(valor) && parseFloat(valor) > 0
  },
  esAnioNoPasado: (valor) => {
    const anioActual = new Date().getFullYear()
    return Number(valor) >= anioActual
  },
  esNumeroEnRango: (min, max) => {
    const numMin = Number(min)
    const numMax = Number(max)
    return (valor) => {
      const numValor = Number(valor)
      return !isNaN(numValor) && numValor >= numMin && numValor <= numMax
    }
  },
  longitudMaxima: (max) => {
    const maxLen = Number(max)
    return (valor) => {
      return valor.length <= maxLen
    }
  },
  longitudExacta: (len) => {
    const longitudRequerida = Number(len)
    return (valor) => {
      return valor.length === longitudRequerida
    }
  },
  coincideCon: (idCampoOriginal) => {
    return (valorConfirmacion) => {
      const campoOriginal = document.getElementById(idCampoOriginal)
      if (!campoOriginal) return false
      return valorConfirmacion === campoOriginal.value
    }
  },
  longitudMinima: (min) => {
    const minLen = Number(min)
    return (valor) => {
      return valor.trim() === '' || valor.length >= minLen
    }
  },
  esCorreoOpcional: (valor) =>
    valor.trim() === '' || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor),
  esEnteroPositivo: (valor) =>
    valor.trim() === '' ||
    (Number.isInteger(Number(valor)) && Number(valor) >= 0),
  esCodigoPostalEEUU: (valor) =>
    valor.trim() === '' || /^\d{5}(-\d{4})?$/.test(valor),

  esUrlValida: (valor) => {
    if (valor.trim() === '') return true
    try {
      new URL(valor)
      return true
    } catch (e) {
      return false
    }
  },
  esCodigoMoneda: (valor) => {
    return valor.trim() === '' || /^[A-Z]{3}$/.test(valor)
  },
  esClaveOpenAi: (valor) => {
    return valor.trim() === '' || valor.startsWith('sk-')
  },
  esClaveStripePublica: (valor) => {
    return (
      valor.trim() === '' ||
      valor.startsWith('pk_test_') ||
      valor.startsWith('pk_live_')
    )
  },
  /**
   * Valida que la clave secreta de Stripe comience con 'sk_test_' o 'sk_live_'.
   * Permite que el campo esté vacío.
   */
  esClaveStripeSecreta: (valor) => {
    return (
      valor.trim() === '' ||
      valor.startsWith('sk_test_') ||
      valor.startsWith('sk_live_')
    )
  },
  /**
   * Valida que el secreto del webhook de Stripe comience con 'whsec_'.
   * Permite que el campo esté vacío.
   */
  esClaveStripeWebhook: (valor) => {
    return valor.trim() === '' || valor.startsWith('whsec_')
  },

  esTipoArchivo:
    (...tiposPermitidos) =>
    (fileList) => {
      if (!fileList || fileList.length === 0) return true // Válido si no se sube archivo

      // const tipos = tiposPermitidos.split(',')
      const file = fileList[0]

      return tiposPermitidos.includes(file.type)
    },

  /**
   * Crea una regla para validar el tamaño máximo de un archivo en Megabytes (MB).
   * @param {string|number} maxMB - Tamaño máximo en MB.
   */
  tamanoMaximoArchivo: (maxMB) => (fileList) => {
    if (!fileList || fileList.length === 0) return true // Válido si no se sube archivo
    const file = fileList[0]
    const maxSizeInBytes = Number(maxMB) * 1024 * 1024
    return file.size <= maxSizeInBytes
  },

  chequearDuplicidad: async (input) => {
    const valor = obtenerValorDelCampo(input)
    const url = input.dataset.validateDuplicateUrl
    const accion = input.dataset.validateAction
    const inputName = input.name

    if (!valor || !url || !accion || !inputName)
      return { esValido: true, mensaje: null }

    const idSelector = input.dataset.recordIdSelector
    let idInput = null
    let recordId = null

    if (idSelector) {
      // Buscamos el input que contiene el ID usando el selector proporcionado.
      idInput = document.querySelector(idSelector)
      if (idInput && idInput.value) {
        recordId = idInput.value
      }
    }

    try {
      const formData = new FormData()
      formData.append('accion', accion)
      formData.append(inputName, valor)

      // Si encontramos un ID, lo añadimos también a la petición.
      if (recordId && idInput) {
        formData.append(idInput.name, recordId)
      }

      const respuesta = await fetch(url, {
        method: 'POST',
        body: formData,
      })

      if (!respuesta.ok) {
        console.error('Error en la petición de duplicidad.')
        return { esValido: true, mensaje: null } // No bloquear al usuario por error del servidor
      }

      const data = await respuesta.json()

      // El campo es válido si el estado es 'disponible'
      const esValido = data.estado === 'disponible'
      // Usamos el mensaje de la API si existe, si no, será null
      const mensajeApi = data.mensaje || null

      return { esValido, mensaje: mensajeApi }
    } catch (error) {
      console.error('Fallo en la validación por fetch:', error)
      return { esValido: true, mensaje: null }
    }
  },
}

// --- FUNCIONES AUXILIARES (USO INTERNO) ---

const mostrarError = (input, mensaje) => {
  if (!input.id) {
    console.error('El input debe tener un ID para asociarle un error.', input)
    return
  }
  const errorId = `error-for-${input.id}`
  let errorElement = document.getElementById(errorId)

  // --- INICIO DE LA MEJORA ---

  // Por defecto, el mensaje se inserta después del propio input.
  let puntoDeInsercion = input

  // Buscamos el nuevo atributo para un contenedor específico.
  const selectorContenedor = input.dataset.errorContainer

  if (selectorContenedor) {
    // Si el atributo existe, buscamos el contenedor padre más cercano.
    const contenedor = input.closest(selectorContenedor)
    if (contenedor) {
      // Si lo encontramos, ese será nuestro nuevo punto de inserción.
      puntoDeInsercion = contenedor
    } else {
      console.warn(
        `No se encontró el contenedor de error '${selectorContenedor}' para el input '${input.id}'.`
      )
    }
  }
  // --- FIN DE LA MEJORA ---

  if (!errorElement) {
    errorElement = document.createElement('div')
    errorElement.id = errorId
    errorElement.className = 'error-message'
    // Insertamos el error DESPUÉS del punto de inserción (sea el input o el contenedor).
    puntoDeInsercion.insertAdjacentElement('afterend', errorElement)
  }

  errorElement.textContent = mensaje
  input.classList.add('error')
}

const limpiarError = (input) => {
  if (!input.id) return

  const errorId = `error-for-${input.id}`
  const errorElement = document.getElementById(errorId)

  // Si el elemento de error existe, lo eliminamos por completo.
  if (errorElement) {
    errorElement.remove()
  }

  // Quitamos la clase de error del input.
  input.classList.remove('error')
}

const limpiarErroresDelFormulario = (formulario) => {
  const mensajesDeError = formulario.querySelectorAll('.error-message')
  mensajesDeError.forEach((mensaje) => {
    const inputId = mensaje.id.replace('error-for-', '')
    const input = document.getElementById(inputId)
    if (input) {
      input.classList.remove('error')
    }
    mensaje.remove()
  })
}

const debounce = (func, delay = 300) => {
  let timeoutId
  return (...args) => {
    clearTimeout(timeoutId)
    timeoutId = setTimeout(() => {
      func.apply(this, args)
    }, delay)
  }
}

const obtenerValorDelCampo = (campo) => {
  // ✅ NUEVA LÓGICA: Si el campo tiene una instancia de IMask, usamos su valor sin máscara.
  if (window.jQuery && $(campo).data('mask')) {
    // Si la tiene, usamos .cleanVal() para obtener el valor sin máscara.
    return $(campo).cleanVal()
  }

  // El resto de la lógica es la misma de antes.

  switch (campo.type) {
    case 'checkbox':
      return campo.checked
    case 'radio':
      const radioSeleccionado = document.querySelector(
        `input[name="${campo.name}"]:checked`
      )
      return radioSeleccionado ? radioSeleccionado.value : ''
    case 'select-multiple':
      return Array.from(campo.selectedOptions).map((option) => option.value)
    case 'file':
      return campo.files
    default:
      return campo.value
  }
}

const validarInput = async (input) => {
  limpiarError(input)
  const reglasAttr = input.dataset.rules.split('|')
  const valor = obtenerValorDelCampo(input)
  let esValido = true

  // REGLAS SINCRONAS

  for (const reglaAttr of reglasAttr) {
    // Esta parte es la clave: separa la regla de sus parámetros.
    const [nombreRegla, params] = reglaAttr.split(':')
    const args = params ? params.split(',') : [] // Convierte "1,12" en ["1", "12"]

    let reglaFunc = reglasDeValidacion[nombreRegla]

    if (typeof reglaFunc === 'function' && args.length > 0) {
      // Si la regla es una "fábrica", la llamamos con los argumentos.
      reglaFunc = reglaFunc(...args)
    }

    // Si la regla final no es una función o la validación falla...
    if (typeof reglaFunc !== 'function' || !reglaFunc(valor)) {
      // Formatea el nombre del mensaje: esNumeroEnRango -> messageEsNumeroEnRango
      const nombreMensaje = `message${
        nombreRegla.charAt(0).toUpperCase() + nombreRegla.slice(1)
      }`

      const mensaje =
        input.dataset[nombreMensaje] || traducciones.validation_error

      mostrarError(input, mensaje)
      esValido = false

      console.log(input)
      break
    }
  }

  // 2. Ejecuta la validación asíncrona si el atributo existe
  if (input.dataset.validateDuplicateUrl) {
    input.classList.add('validating')

    // Llamamos a la regla pasándole el input completo
    const resultadoAsync = await reglasDeValidacion.chequearDuplicidad(input)

    input.classList.remove('validating')

    if (!resultadoAsync.esValido) {
      // Lógica para decidir qué mensaje mostrar
      const mensajeFinal =
        resultadoAsync.mensaje || // 1. Prioridad: Mensaje de la API
        input.dataset.messageDuplicado || // 2. Fallback: Mensaje del HTML
        'Este valor ya existe.' // 3. Fallback genérico

      mostrarError(input, mensajeFinal)
      return false
    }
  }
  return esValido
}

const inicializarValidacionReactiva = () => {
  const formularios = document.querySelectorAll(
    'form[data-validation="reactive"]'
  )

  $(document).on('show.bs.modal', function (e) {
    // Verificamos si el modal tiene un formulario con validación reactiva.
    const formulario = e.target.querySelector(
      'form[data-validation="reactive"]'
    )

    // ELIMINAR MENSAJES AL MOMENTO DE ABRIR UN MODAL, EVITAR MENSAJES DE VALIDACIÓN RESIDUALES
    if (formulario) {
      const inputs = formulario.querySelectorAll('[data-rules]')
      inputs.forEach((input) => {
        limpiarError(input)
      })
    }
  })

  formularios.forEach((formulario) => {
    const validarInputDebounced = debounce(validarInput, 400)

    // ✅ Usamos el evento 'input' para una respuesta más rápida.
    formulario.addEventListener(
      'input',
      (evento) => {
        const campo = evento.target

        // Validamos si el campo tiene reglas.
        if (
          campo &&
          (campo.matches('[data-rules]') ||
            campo.matches('[data-validate-duplicate-url]'))
        ) {
          // Llamamos a la versión con debounce.
          validarInputDebounced(campo)
        }
      },
      true
    )

    // El manejador del submit también necesita el selector corregido.
    formulario.addEventListener('submit', async (evento) => {
      evento.preventDefault()

      // ✅ CORRECCIÓN: Selecciona todos los campos que tengan CUALQUIER tipo de validación.
      const inputsAValidar = formulario.querySelectorAll(
        '[data-rules], [data-validate-duplicate-url]'
      )

      const promesasDeValidacion = []
      inputsAValidar.forEach((input) => {
        promesasDeValidacion.push(validarInput(input))
      })

      const resultados = await Promise.all(promesasDeValidacion)

      console.log('Resultados de validación:', resultados)

      const formularioEsValido = resultados.every((res) => res === true)

      if (formularioEsValido) {
        const formData = new FormData(formulario)
        const datos = Object.fromEntries(formData.entries())
        formulario.dispatchEvent(
          new CustomEvent('validation:success', {
            bubbles: true,
            detail: { datos, formData },
          })
        )
      } else {
        formulario.dispatchEvent(
          new CustomEvent('validation:failed', { bubbles: true })
        )
        formulario.querySelector('.error')?.focus()
      }
    })
  })
}

// --- Ejecutamos el inicializador cuando el DOM esté listo ---
document.addEventListener('DOMContentLoaded', inicializarValidacionReactiva)
