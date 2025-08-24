import { getApexLanguage } from './apiConfig.js'

const d = document

let donutChartElement, donutChartElement2

// NOTAS, ES NECESARIO LAS Z AL FINAL DE ALGUNAS FECHAS PARA MOSTRAR TODOS LOS LABELS DE LA GRÁFICA

export async function updateChart({
  type = '',
  chart = '',
  dateRange = '',
  records = '',
  biomarkers = '',
  id_biomarker = '',
  status = '',
  language = '',
}) {
  let chartData

  if (type === 'bar') {
    console.log('updating bar')

    const { options } = await procesarDatosGrafico(
      id_biomarker, // id_biomarker
      records, // array de registros filtrados
      biomarkers, // array de biomarcadores
      dateRange.min, // dateMin
      dateRange.max, // dateMax
      (status = status === 'in' ? true : false), // inRange (o false según corresponda)
      language
    )

    chart.destroy()
    const element = document.querySelector('#barlines-chart')
    if (element) {
      console.log(options)

      const chartBar = new ApexCharts(element, options)
      chartBar.render()

      let min = records[0]
      let max = records[records.length - 1]

      // no colocar la Z al final del time porque sino no hace el zoom correcto
      // Linea para ajustar registros al momento de actualizar el zoom
      // if (records.length > 0) {
      //   chartBar.zoomX(
      //     new Date(`${min.date}`).getTime(),
      //     new Date(`${max.date}T23:59:59`).getTime()
      //   )
      // }
    }
  }

  if (type === 'donut') {
    console.log('UPDATING DONUT')

    chartData = records

    let { in_range, out_range, in_count, out_count } = chartData

    if (!donutChartElement) {
      donutChart(chartData, language)
      return
    }
    donutChartElement.unload({ ids: ['In Range', 'Out Of Range'] })
    donutChartElement.load({
      columns: [
        ['In Range', in_range],
        ['Out Of Range', out_range],
      ],
    })
  }

  if (type === 'bar-admin') {
    console.log('UPDATING ADMIN BAR')

    chartData = prepararDatosGraficoAdmin(records)

    chart.updateOptions(chartData)
  }

  if (type === 'donut-admin') {
    console.log('UPDATING ADMIN DONUT')

    chartData = records
    let { in_range, out_range, in_count, out_count } = chartData

    if (!donutChartElement2) {
      donutChartAdmin(chartData, language)
      return
    }
    donutChartElement2.unload({ ids: ['In Range', 'Out Of Range'] })

    donutChartElement2.load({
      columns: [
        ['In Range', in_range],
        ['Out Of Range', out_range],
      ],
    })
  }
}

export const donutChart = (currentData) => {
  let { in_range, out_range, in_count, out_count } = currentData

  let total = in_count + out_count
  let series = [in_range, out_range]

  donutChartElement2 = c3.generate({
    bindto: '#donut-chart',
    data: {
      columns: [
        [language.inRange_admin, in_range],
        [language.outOfRange_admin, out_range],
      ],
      type: 'donut',
      colors: {
        [language.inRange_admin]: '#a5dfb4',
        [language.outOfRange_admin]: '#ebbcbe',
      },
    },
    donut: {
      title: `${
        total !== 0
          ? language.donutTitle_admin.replace('{in_range}', in_range)
          : language.noEntries_admin
      }`,
      order: null,
      width: 20,
      label: { show: false },
    },
    tooltip: {
      format: {
        value: (pct, ratio, id, index) => {
          const count = id === language.inRange_admin ? in_count : out_count
          return language.tooltipFormat_admin
            .replace('{pct}', pct)
            .replace('{count}', count)
            .replace('{total}', total)
        },
      },
    },
  })
}

export const donutChartAdmin = (currentData, lang) => {
  let { in_range, out_range, in_count, out_count } = currentData

  let total = in_count + out_count
  let series = [in_range, out_range]

  donutChartElement = c3.generate({
    bindto: '#donut-chart-admin',
    data: {
      columns: [
        [lang.inRange_admin, in_range],
        [lang.outOfRange_admin, out_range],
      ],
      type: 'donut',
      colors: {
        [lang.inRange_admin]: '#a5dfb4',
        [lang.outOfRange_admin]: '#ebbcbe',
      },
    },
    donut: {
      title: `${
        total !== 0
          ? lang.donutTitle_admin.replace('{in_range}', in_range)
          : lang.noEntries_admin
      }`,
      order: null,
      width: 20,
      label: { show: false },
    },
    tooltip: {
      format: {
        value: (pct, ratio, id, index) => {
          // En tooltip el id será el valor traducido, así que comprobamos por key
          const count =
            id === lang.inRange_admin
              ? in_count
              : id === lang.outOfRange_admin
              ? out_count
              : 0

          return lang.tooltipFormat_admin
            .replace('{pct}', pct)
            .replace('{count}', count)
            .replace('{total}', total)
        },
      },
    },
  })
}

// REQUISITO: Asegúrate de tener 'dayjs' y su plugin 'utc' importados en tu proyecto.
// import dayjs from 'dayjs';
// import utc from 'dayjs/plugin/utc';
// dayjs.extend(utc);

async function procesarDatosGrafico(
  id_biomarker,
  records,
  biomarkers,
  dateMin,
  dateMax,
  inRange
) {
  // 1. Buscar biomarcador y su info
  const biomarker = biomarkers.find(
    (bm) => String(bm.biomarker_id) === String(id_biomarker)
  )

  // 2. LÓGICA CONDICIONAL CON DAYJS
  // Se calcula la diferencia de días para decidir si se normaliza la hora.
  const diffInDays = dayjs.utc(dateMax).diff(dayjs.utc(dateMin), 'day')
  const normalizeTimeForRange = diffInDays > 1

  // 3. Ordenar todos los registros por fecha y hora
  const recordsFiltrados = records.sort(
    (a, b) =>
      new Date(`${a.date}T${a.time}Z`) - new Date(`${b.date}T${b.time}Z`)
  )

  // 4. ENRIQUECER LOS DATOS
  // Se crea un array base con toda la información necesaria para el gráfico y el tooltip.
  const seriesData = recordsFiltrados.map((rec) => {
    // NO COLOCAR Z AL FINAL PARA CUADRAR CORRECTAMENTE EL LABEL DE X Y COLOCAR LA FECHA CORRECTA
    const originalTimestamp = new Date(`${rec.date}T${rec.time}Z`).getTime()

    // La posición en 'x' depende de si se normaliza el rango o no.
    const positionTimestamp = normalizeTimeForRange
      ? new Date(`${rec.date}T00:00:00Z`).getTime()
      : originalTimestamp

    return {
      x: positionTimestamp,
      y: +rec.value,
      status: rec.status,
      originalDate: originalTimestamp, // Propiedad con la fecha/hora real para el tooltip
      x2: new Date(`${rec.date}T12:00:00Z`).getTime(),
      x3: new Date(`${rec.date}T06:00:00Z`).getTime(),
    }
  })

  // 5. Variables auxiliares para el gráfico
  const valores = seriesData.map((rec) => rec.y)
  const barColorInRange = '#a5dfb4'
  const barColorOutRange = '#ebbcbe'
  const lineColor = '#223976'
  const refMin = parseFloat(biomarker.reference_min)
  const refMax = +biomarker.reference_max
  const yMax = Math.max(refMax, ...valores)
  let apexLang = await getApexLanguage(language.lang)

  // 6. Opciones de ApexCharts
  const options = {
    series: [
      {
        name: language.inRange_chart,
        type: 'column',
        data: seriesData.map((s) => ({
          x: normalizeTimeForRange ? s.x2 : s.x,
          y: s.status === 'in_range' ? s.y : null,
        })),
      },
      {
        name: language.outRange_chart,
        type: 'column',
        data: seriesData.map((s) => ({
          x: s.x,
          y: s.status === 'out_range' ? s.y : null,
        })),
      },
      {
        name: `${language.yAxisRightText_chart} (${biomarker.unit})`,
        type: 'line',
        data: seriesData.map((s) => ({
          x: normalizeTimeForRange && s.status === 'in_range' ? s.x2 : s.x,
          y: s.y,
        })),
      },
    ],
    chart: {
      height: 378,
      type: 'line',
      offsetY: 10,
      defaultLocale: apexLang.name,
      locales: [apexLang],
      events: {
        legendClick: function (chartContext, seriesIndex, config) {
          if (seriesIndex > 1) return
          setTimeout(() => {
            const collapsed = chartContext.w.globals.collapsedSeries
            const isInRangeVisible = !collapsed.some((s) => s.index === 0)
            const isOutOfRangeVisible = !collapsed.some((s) => s.index === 1)
            let dataForLine
            if (isInRangeVisible && isOutOfRangeVisible) {
              dataForLine = recordsFiltrados
            } else if (isInRangeVisible) {
              dataForLine = recordsFiltrados.filter(
                (r) => r.status === 'in_range'
              )
            } else if (isOutOfRangeVisible) {
              dataForLine = recordsFiltrados.filter(
                (r) => r.status === 'out_range'
              )
            } else {
              dataForLine = recordsFiltrados
            }
            const newLineData = dataForLine.map((rec) => ({
              x: new Date(
                normalizeTimeForRange
                  ? `${rec.date}T00:00:00Z`
                  : `${rec.date}T${rec.time}Z`
              ).getTime(),
              y: +rec.value,
            }))
            chartContext.updateSeries([{}, {}, { data: newLineData }], false)
            if (!isInRangeVisible)
              chartContext.toggleSeries(language.inRange_chart)
            if (!isOutOfRangeVisible)
              chartContext.toggleSeries(language.outRange_chart)
          }, 100)
        },
      },
    },
    stroke: { width: [2, 2, 3] },
    plotOptions: { bar: { columnWidth: '70%', hideOverflowingLabels: false } },
    colors: [barColorInRange, barColorOutRange, lineColor],
    dataLabels: {
      enabled: true,
      enabledOnSeries: [2],
      style: { fontSize: '.8em' },
    },
    tooltip: {
      shared: false,
      intersect: true,

      x: {
        formatter: function (seriesName, opts) {
          const dataPointIndex = opts.dataPointIndex

          // ¡LA MAGIA! Usamos el 'dataPointIndex' para buscar en nuestro array 'seriesData'
          // que está disponible gracias al scope de la función (closure).
          const dataPoint = seriesData[dataPointIndex]

          if (!dataPoint) return ''

          // Leemos la fecha/hora real de nuestra propiedad personalizada
          const originalTimestamp = dataPoint.originalDate

          // Formateamos como antes
          const dateOptions = {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
          }
          const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
          }
          const displayDate = new Date(originalTimestamp).toLocaleDateString(
            'en-US',
            dateOptions
          )
          const displayTime = new Date(originalTimestamp).toLocaleTimeString(
            'en-US',
            timeOptions
          )

          return `${displayDate}, ${displayTime}`
        },
      },
      y: {
        formatter: function (val) {
          if (val === null) return
          return `${val.toFixed(2)} ${biomarker.unit}`
        },
      },
    },

    xaxis: {
      type: 'datetime',
      min: new Date(`${dateMin}T00:00:00Z`).getTime(),
      max: new Date(`${dateMax}T23:59:59Z`).getTime(),
      labels: {
        datetimeUTC: false,
        datetimeFormatter: {
          year: 'yyyy',
          month: 'MMM dd',
          day: 'MMM dd',

          hour: normalizeTimeForRange ? 'MMM dd' : 'hh:mm TT',
          minute: 'hh:mm TT',
          second: 'hh:mm TT',
        },
        showDuplicates: false,
      },
    },
    legend: { offsetY: 7 },
    grid: { padding: { bottom: 20 } },
    fill: {
      type: ['solid', 'solid', 'none'],
    },
    yaxis: {
      min: 0,
      max: yMax,
      title: {
        text:
          refMax >= Math.max(...valores)
            ? `${language.leftYAxisText_chart} (${refMin} - ${refMax})`
            : `${language.yAxisRightText_chart} (${biomarker.unit})`,
      },
      tickAmount: 5,
      labels: {
        formatter: function (value) {
          if (value === null || value === undefined) return 'N/A'
          return value.toFixed(2)
        },
      },
    },
    annotations: {
      yaxis: [
        {
          width: '100%',
          y: refMin,
          borderColor: '#cecece',
          label: {
            borderColor: '#6c8193',
            style: { color: '#fff', background: '#6c8193' },
            text: `${language.annotationsMin_chart}: ${refMin}`,
          },
          offsetY: 0,
        },
        {
          y: refMax,
          borderColor: '#cecece',
          label: {
            borderColor: '#6c8193',
            style: { color: '#fff', background: '#6c8193' },
            text: `${language.annotationsMax_chart}: ${refMax}`,
          },
          offsetY: 0,
        },
      ],
    },
  }

  return { options }
}

function prepararDatosGraficoAdmin(json) {
  const users = json.results.map((u) => u.username)
  const inRangeBar = json.results.map((u) => u.in_range_count)
  const outRangeBar = json.results.map((u) => u.out_range_count)

  // La línea azul usa los mismos datos que las barras rojas
  const avgLine = outRangeBar

  const totalCounts = json.results.map(
    (u) => u.in_range_count + u.out_range_count
  )
  const rightAxisMax = Math.max(...totalCounts)

  let biomarkerInfo = json.biomarker
  const leftYAxisText = `Reference range (${biomarkerInfo.reference_min}-${biomarkerInfo.reference_max} ${biomarkerInfo.unit})`
  const options = {
    series: [
      {
        name: 'In Range',
        type: 'bar',
        data: inRangeBar,
        color: '#a5dfb4',
      },
      {
        name: 'Out of Range',
        type: 'bar',
        data: outRangeBar,
        color: '#ebbcbe',
      },
      {
        name: 'Out Range Count',
        type: 'line',
        data: avgLine,
        color: '#223976',
      },
    ],
    chart: {
      height: 350,
      type: 'bar',
      stacked: false,
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '40%',
        barGap: 0.3,
        grouped: true,
      },
    },
    dataLabels: {
      enabled: true,
      enabledOnSeries: [2], // SOLO para la línea azul (índice 2)
      style: {
        fontSize: '13px',
        fontWeight: 'bold',
        colors: ['#223976'], // Letras blancas
      },
      background: {
        enabled: true,
        foreColor: '#223976', // Letras blancas (opcional, ya definido arriba)
        padding: 4,
        borderRadius: 1,
        borderWidth: 0,
        opacity: 1,
        dropShadow: {
          enabled: false,
        },
        // Lo importante: el color de fondo
        backgroundColor: '#223976', // El mismo azul de la línea, o el que prefieras
      },
      formatter: function (val, { seriesIndex }) {
        if (seriesIndex === 2 && val !== null) return val
        return ''
      },
      offsetY: -8,
    },
    stroke: {
      width: [0, 0, 2.5],
      curve: 'straight',
    },
    xaxis: {
      type: 'category',
      categories: users,
      labels: { rotate: -15 },
      text: json.biomarker.name,
    },
    yaxis: [
      {
        show: true,
        showAlways: true,
        title: { text: leftYAxisText },
        min: 0,
        max: rightAxisMax,
        labels: {
          formatter: (val) => {
            if (val === null) return 'N/A'
            if (val === undefined) return
            return Number.isInteger(val) ? val : val.toFixed(2)
          },
        },
      },
      {
        show: false,
        opposite: true,
        title: { text: `Out Range Count (${json.biomarker.unit})` },
        min: 0,
        max: rightAxisMax,
        labels: {
          formatter: (val) => {
            if (val === null) return 'N/A'
            if (val === undefined) return
            return Number.isInteger(val) ? val : val.toFixed(2)
          },
        },
      },
      {
        show: false,
        opposite: true,
        title: { text: `Out Range Count (${json.biomarker.unit})` },
        min: 0,
        max: rightAxisMax,
        labels: {
          formatter: (val) => {
            if (val === null) return 'N/A'
            if (val === undefined) return
            return Number.isInteger(val) ? val : val.toFixed(2)
          },
        },
      },
    ],
    tooltip: {
      shared: false,
      intersect: true,
      x: {
        formatter: (val) => val,
      },
      y: {
        formatter: function (val, { seriesIndex }) {
          if (seriesIndex === 0) return `${val}`
          if (seriesIndex === 1) return `${val}`
          if (seriesIndex === 2) return `${val}`
        },
      },
    },
    legend: {
      position: 'bottom',
      horizontalAlign: 'center',
    },
  }
  return options
}

export const timeFramesRecords = (records) => {
  const today = new Date()
  const currentDayRecords = []
  const currentWeekRecords = []
  const currentMonthRecords = []

  const startOfWeek = new Date(today)
  const dayOfWeek = today.getDay() // 0 (Domingo) - 6 (Sábado)
  startOfWeek.setDate(today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1)) // Ajusta al primer día de la semana (Lunes)
  startOfWeek.setHours(0, 0, 0, 0)

  const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1)
  startOfMonth.setHours(0, 0, 0, 0)

  records.forEach((record) => {
    const recordDate = new Date(record.date)
    recordDate.setHours(0, 0, 0, 0) // Normalizar la hora para la comparación

    // Por día
    if (recordDate.getTime() === today.getTime()) {
      currentDayRecords.push(record)
    }

    // Por semana
    if (recordDate >= startOfWeek && recordDate <= today) {
      currentWeekRecords.push(record)
    }

    // Por mes
    if (recordDate >= startOfMonth && recordDate <= today) {
      currentMonthRecords.push(record)
    }
  })

  // Aquí puedes definir cómo quieres mostrar o usar estos registros organizados.
  // Por ejemplo, podrías devolver un objeto con las listas:
  return {
    today: currentDayRecords,
    week: currentWeekRecords,
    month: currentMonthRecords,
  }
}

export function getDeviceType() {
  const width = window.innerWidth
  if (width < 768) {
    return 'Out of Range'
  } else if (width >= 768 && width < 992) {
    return 'Out Of Range'
  } else {
    return 'In Range'
  }
}

export function updateCentralLabel() {
  const deviceType = getDeviceType()
  let newLabel = 'Total'

  switch (deviceType) {
    case 'phone':
      newLabel = 'Móvil'
      break
    case 'tablet':
      newLabel = 'Tablet'
      break
    case 'pc':
      newLabel = 'Escritorio'
      break
  }

  return newLabel
}
