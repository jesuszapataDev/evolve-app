import { getCountries } from '../apiConfig.js'

// Asegúrate de que IMask y jQuery ($) estén disponibles globalmente.

window.countrySelectMasks = window.countrySelectMasks || {}

export async function countrySelect(
  telInputId,
  containerSelector,
  defaultPhone,
  modalSelector
) {
  const telInput = document.getElementById(telInputId)
  if (!telInput) {
    return console.error(`No se encontró input con id "${telInputId}"`)
  }

  const oldContainerId = telInput.dataset.countrySelectContainerId
  if (oldContainerId) {
    const oldContainer = document.getElementById(oldContainerId)
    if (oldContainer) {
      const oldSelect = oldContainer.querySelector('select.country-select')
      if (oldSelect) {
        const $oldSelect = $(oldSelect)
        if ($oldSelect.hasClass('select2-hidden-accessible')) {
          $oldSelect.select2('destroy')
        }
      }
      if (window.countrySelectMasks[telInputId]) {
        window.countrySelectMasks[telInputId].destroy()
        delete window.countrySelectMasks[telInputId]
      }
      oldContainer.innerHTML = ''
    }
  }

  const container = document.querySelector(containerSelector)
  if (!container) {
    return console.error(`No se encontró el contenedor "${containerSelector}"`)
  }

  container.classList.add('mb-3', 'w-100')

  const containerId = `country-select-container-${telInputId}`
  container.id = containerId
  telInput.dataset.countrySelectContainerId = containerId

  const countrySelect = document.createElement('select')
  countrySelect.id = `country-select-${telInputId}`
  countrySelect.className = 'country-select form-select'
  container.appendChild(countrySelect)

  try {
    let countries = await getCountries()
    countries = countries.data

    function formatOptionWithFlag(option) {
      if (!option.id) return option.text
      const suffix = $(option.element).data('sufijo')
      const prefijoNormal = $(option.element).data('prefijo')
      const country_name = $(option.element).data('pais')
      return $(`
        <div class="country-option-container">
          <span class="country-sufijo">${suffix ? suffix.toUpperCase() : ''}</span>
          <div class="country-details">
            <span class="country-prefijo">${prefijoNormal || ''}</span>
            <span class="country-name">${country_name ? country_name.toUpperCase() : ''}</span>
          </div>
        </div>`)
    }

 function applyMask(id, value = '') {
  let country = countries.find((c) => String(c.country_id) === String(id))
  if (!country) {
    console.warn('No se encontró el país para ID:', country_id)
    return
  }

  let maskDb = country.phone_mask ? country.phone_mask.toLowerCase() : ''
  let prefijo = country.normalized_prefix
  let mask = getCleanMask(maskDb, prefijo)

  console.log('[MASK]', { maskDb, prefijo, mask, country })

  if (window.countrySelectMasks[telInputId]) {
    window.countrySelectMasks[telInputId].destroy()
  }

  telInput.value = value || ''
  telInput.placeholder = mask ? `e.g. ${mask}` : 'e.g. 0000000000'

  const phoneMask = IMask(telInput, {
    mask: mask,
    lazy: false,
    placeholderChar: '_',
  })
  window.countrySelectMasks[telInputId] = phoneMask

  phoneMask.updateValue()

  if (value) {
    const match = value.match(/\(\+\d+\)\s*(.*)/)
    if (match && match[1]) {
      phoneMask.value = `${match[1]}`
    } else {
      phoneMask.value = ''
    }
  }

  telInput.addEventListener(
    'focus',
    () => {
      const pos = telInput.value.indexOf(')') + 2
      setTimeout(() => {
        telInput.setSelectionRange(pos, pos)
      }, 10)
    },
    { once: true }
  )
  
}


    function getCleanMask(maskStr, prefijo) {
      let cleanPrefix = prefijo.replace('+', '')
      let maskBody = maskStr.replace(/^\+\d+\s*/, '')
      maskBody = maskBody.replace(/#/g, '0').replace(/\s+/g, '')
      return `(+${cleanPrefix}) ${maskBody}`
    }

    let options = []
    countries
      .sort((a, b) => a.prefijo - b.prefijo)
      .forEach((country) => {
        options.push(`
          <option 
            data-sufijo="${country.suffix}" 
            data-prefijo="${country.normalized_prefix}"
            data-pais="${country.country_name}" 
            data-mask="${country.phone_mask.toLowerCase()}" 
            value="${country.country_id}">
            ${country.suffix} (${country.normalized_prefix}) (${country.country_name})
          </option>`)
      })
    countrySelect.innerHTML = options.join('')

    const $selectElem = $(countrySelect)

    let select2Options = {
      width: '100%',
      placeholder: 'Select a country',
      templateResult: formatOptionWithFlag,
      templateSelection: formatOptionWithFlag,
    }

    let $modalBody = $selectElem.closest('.modal-body')
    let $modalParent = $selectElem.closest('.modal')
    if ($modalBody.length) {
      select2Options.dropdownParent = $modalBody
    } else if (modalSelector && $(modalSelector).length) {
      select2Options.dropdownParent = $(modalSelector)
    } else if ($modalParent.length) {
      select2Options.dropdownParent = $modalParent
    }

    console.log('Inicializando Select2 en el elemento:', $selectElem)
    $selectElem.select2(select2Options).on('change', function () {
      const selectedValue = $(this).val()
      applyMask(selectedValue, '')
    })

    let selectedCountryId = countrySelect.options[0].value
    let cleanDefaultPhone = defaultPhone ? defaultPhone.trim() : ''

    if (cleanDefaultPhone) {
      let prefijoMatch = cleanDefaultPhone.match(/\(\+(\d+)\)/)
      if (prefijoMatch) {
        let prefijo = `+${prefijoMatch[1]}`
        let country = countries.find((c) => c.normalized_prefix === prefijo)
        if (country) {
          selectedCountryId = country.country_id
        }
      }
    }

    $selectElem.val(selectedCountryId).trigger('change')

    if (cleanDefaultPhone) {
      applyMask(selectedCountryId, cleanDefaultPhone)
      $selectElem.trigger('change.select2')
    }
  } catch (error) {
    console.error('Falló la inicialización del selector de país:', error)
    container.innerHTML =
      '<div class="alert alert-danger">Error al cargar los países.</div>'
  }
}
