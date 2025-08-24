import { showAlert } from './helpers/alerts.js'

export function getBaseUrl() {
  const localBaseUrl = 'http://localhost/evolve-app' // Cambia esta URL según tu configuración local
  const prodBaseUrl = 'localhost' // Cambia esta URL por la de tu servidor de producción

  console.log(window.location.origin)

  // Verificamos si estamos en un entorno de localhost
  if (window.location.origin.includes('localhost')) {
    return localBaseUrl
  }

  // Si no estamos en localhost, asumimos que estamos en producción
  return prodBaseUrl
}

function validateUrl() {
  return getBaseUrl()
}

function handleRequest(url, method, data = null, showAlerts = true) {
  // Ya no se necesita showLoader() ni hideLoader() aquí.

  // Retornamos una nueva Promesa para mantener la compatibilidad con .then() y async/await
  return new Promise((resolve) => {
    // Obtenemos la URL base correcta
    const baseUrl = validateUrl()
    const fullUrl = `${baseUrl}${url}`

    console.log(fullUrl)

    $.ajax({
      url: fullUrl,
      method: method,
      // jQuery es inteligente con los datos. Si 'data' es un objeto, lo formatea
      // como x-www-form-urlencoded por defecto. Si queremos JSON:
      data: data ? JSON.stringify(data) : null,
      contentType: data
        ? 'application/json; charset=utf-8'
        : 'application/x-www-form-urlencoded; charset=UTF-8',
      dataType: 'json', // Le dice a jQuery que espere una respuesta JSON

      success(result) {
        // La lógica de 'success' es el equivalente al bloque 'try' después del 'await'
        console.log(result)

        // Asegura la estructura esperada
        if (result.hasOwnProperty('value')) {
          if (
            showAlerts &&
            result.hasOwnProperty('message') &&
            result.message !== ''
          ) {
            showAlert(result.value, result.message)
          }
          // La promesa se resuelve con el objeto estandarizado
          resolve({
            value: result.value,
            message: result.message || '',
            data: result.data || null,
            labels: result.labels || null,
          })
        } else {
          // Si la estructura no es válida, lo manejamos como un error
          const errorMessage = 'Estructura de respuesta no válida'
          console.error(errorMessage, result)
          if (showAlerts) showAlert(false, errorMessage)
          resolve({
            value: false,
            message: errorMessage,
            data: null,
          })
        }
      },

      error(xhr, status, err) {
        // La lógica de 'error' es el equivalente al bloque 'catch'
        console.error('Error en la petición AJAX:', {
          url: fullUrl,
          status,
          err,
          response: xhr.responseText,
        })
        const errorMessage =
          xhr.responseJSON?.message || err || 'No se pudo procesar la solicitud'

        if (showAlerts) {
          showAlert(false, errorMessage)
        }
        // La promesa también se resuelve en caso de error para no romper la cadena
        resolve({
          value: false,
          message: errorMessage,
          data: null,
          url: url,
        })
      },
    })
  })
}

async function handleRequestFetch(url, method, data = null, showAlerts = true) {
  // showLoader()
  try {
    // Obtenemos la URL base correcta
    const baseUrl = validateUrl()

    // Construimos la URL completa
    const fullUrl = `${baseUrl}${url}`

    const options = {
      method: method,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
    }

    if (data) {
      options.body = JSON.stringify(data)
    }

    const response = await fetch(fullUrl, options)

    if (!response.ok) {
      const errorResponse = await response.text() // Evita fallos si no es JSON
      throw {
        status: errorResponse.status,
        statusText: errorResponse.statusText,
        response: errorResponse,
        url: fullUrl,
      }
    }

    // let clone = response.clone()
    // let text = await clone.text()
    // console.log(text)
    // console.log(baseUrl, url)

    const result = await response.json()

    console.log(result)

    // Asegura la estructura esperada
    if (result.hasOwnProperty('value')) {
      if (result.hasOwnProperty('message') && result.message !== '')
        if (showAlerts) showAlert(result.value, result.message)

      return {
        ...result,
        value: result.value,
        message: result.message || '',
        data: result.data || null,
        labels: result.labels || null,
      }
    } else {
      throw new Error('Estructura de respuesta no válida')
    }
  } catch (error) {
    console.log(error)

    if (showAlerts)
      showAlert(false, error.message || 'No se pudo procesar la solicitud')
    return {
      value: false,
      message: error.message || 'Error desconocido',
      data: null,
      url: url,
    }
  } finally {
    // hideLoader()
  }
}

// Authentication Routes
export const login = async (credentials) =>
  await handleRequest('/api/login', 'POST', credentials)
export const logout = async () =>
  await handleRequest('/api/logout', 'POST', null, false)
export const register = async (userData) =>
  await handleRequest('/api/register', 'POST', userData)

// COUNTRIES
export const getAllCountries = async () =>
  await handleRequest('/countries', 'GET')

export const getCountryById = async (id) =>
  await handleRequest(`/countries/${id}`, 'GET')

export const createCountry = async (countryData) =>
  await handleRequest('/countries', 'POST', countryData)

export const updateCountry = async (id, countryData) =>
  await handleRequest(`/countries/${id}`, 'PUT', countryData)

export const deleteCountry = async (id) =>
  await handleRequest(`/countries/${id}`, 'DELETE')

// COUNTRIES FOR SELECT

export const getCountries = async () =>
  await handleRequestFetch(`/api/countries`, 'GET', null, false)

// VERIFICAR DUPLCIADOS

export const verifyEmail = async (email) =>
  await handleRequest(`/api/verify-email`, 'POST', { email }, false)

export const verifyPhone = async (phone) =>
  await handleRequest(`/api/verify-phone`, 'POST', { phone }, false)
// AUDIT LOGS

export async function getAuditLogById(id) {
  return await handleRequest(`/api/auditlog/${id}`, 'GET')
}
export async function getAllAuditLogs() {
  return await handleRequest('/api/auditlog', 'GET')
}

