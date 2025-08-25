export function initTimezoneSelect(selectId = 'timezoneSelect', modalSelector = null) {
  const $select = $('#' + selectId);
  if (!$select.length) {
    console.warn(`[timezoneSelect] No se encontró el elemento #${selectId}`);
    return;
  }

  // Destruir si ya estaba inicializado
  if ($select.hasClass('select2-hidden-accessible')) {
    $select.select2('destroy');
  }

  // Preparar opciones
  const select2Options = {
    width: '100%',
    placeholder: 'Select timezone',
    templateResult: formatTimezone,
    templateSelection: formatTimezone
  };

  // Asignar dropdownParent como en countrySelect
  const $modalBody = $select.closest('.modal-body');
  const $modal = $select.closest('.modal');
  if ($modalBody.length) {
    select2Options.dropdownParent = $modalBody;
  } else if (modalSelector && $(modalSelector).length) {
    select2Options.dropdownParent = $(modalSelector);
  } else if ($modal.length) {
    select2Options.dropdownParent = $modal;
  }

  // Inicializar Select2
  $select.select2(select2Options);
}

function formatTimezone(option) {
  if (!option.id) return option.text;

  const text = option.text || '';
  const gmtMatch = text.match(/\(GMT [^)]*\)/i);
  const zone = text.replace(/\(GMT [^)]*\)/i, '').trim();

  return $(`
    <div class="timezone-option">
      <span style="min-width: 90px; display: inline-block;">${gmtMatch ? gmtMatch[0] : ''}</span>
      <span>${zone}</span>
    </div>
  `);
}
