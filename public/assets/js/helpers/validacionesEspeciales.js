// --- CALCULATION ENGINE ---
const calculationEngine = {
  /**
   * Calculates the base cost of a single unit.
   * @param {number} materialCost - The cost of materials for one unit.
   * @param {number} laborCost - The cost of labor for one unit.
   * @returns {number} The total unit cost.
   */
  getUnitCost: function (materialCost, laborCost) {
    return (materialCost || 0) + (laborCost || 0)
  },

  /**
   * Calculates the profit amount for a given cost.
   * @param {number} cost - The base cost to apply the profit to.
   * @param {number} profitValue - The value of the profit (e.g., 15 for 15% or 100 for $100).
   * @param {string} profitType - The type of profit ('porcentaje' or 'monto').
   * @returns {number} The calculated profit amount.
   */
  getProfitAmount: function (cost, profitValue, profitType) {
    cost = cost || 0
    profitValue = profitValue || 0
    if (profitType === 'porcentaje') {
      return cost * (profitValue / 100)
    }
    return profitValue // Assumes 'monto' if not percentage
  },

  /**
   * Calculates the final sale price of a single unit.
   * @param {number} unitCost - The base cost of the unit.
   * @param {number} materialProfit - The profit amount from materials.
   * @param {number} laborProfit - The profit amount from labor.
   * @returns {number} The total unit price.
   */
  getUnitPrice: function (unitCost, materialProfit, laborProfit) {
    return (unitCost || 0) + (materialProfit || 0) + (laborProfit || 0)
  },

  /**
   * Calculates the total value based on a unit value and quantity.
   * @param {number} unitValue - The value of a single unit (can be cost or price).
   * @param {number} quantity - The number of units.
   * @returns {number} The total calculated value.
   */
  getTotal: function (unitValue, quantity) {
    return (unitValue || 0) * (quantity || 0) // Default quantity to 1 to avoid multiplying by zero
  },
}

document.addEventListener('DOMContentLoaded', () => {
  // Selecciona todos los botones que tienen la clase para alternar la visibilidad
  const toggleButtons = document.querySelectorAll('.toggle-password-button')

  toggleButtons.forEach((button) => {
    button.addEventListener('click', function () {
      // Obtiene el selector del input desde el data-attribute 'data-target-input'
      const targetInputSelector = this.dataset.targetInput
      if (!targetInputSelector) return // Si no hay data-attribute, no hace nada

      const targetInput = document.querySelector(targetInputSelector)
      const icon = this.querySelector('span.bi') // Busca el ícono dentro del botón

      console.log(targetInput)

      if (targetInput && icon) {
        // Alterna el tipo de input entre 'password' y 'text'
        if (targetInput.type === 'password') {
          targetInput.type = 'text'
          // Cambia el ícono a "ojo tachado" (asumiendo que usas Bootstrap Icons)
          icon.classList.remove('bi-eye')
          icon.classList.add('bi-eye-slash')
        } else {
          targetInput.type = 'password'
          // Cambia el ícono de vuelta a "ojo normal"
          icon.classList.remove('bi-eye-slash')
          icon.classList.add('bi-eye')
        }
      }
    })
  })

  document.addEventListener('input', (e) => {
    if (e.target.classList.contains('number', 'form-control')) {
      e.target.value = e.target.value.replace(/[^0-9\.,]/g, '')
    }
  })
})
