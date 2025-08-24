
   <?php
    $idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
    $locale = $idioma === 'ES' ? 'es-ES' : 'en-US';
    if (!in_array($idioma, ['EN', 'ES'])) {
        $idioma = 'EN';
    }
    $archivo_idioma = APP_ROOT . '/lang/' . $idioma . '.php';
    if (file_exists($archivo_idioma)) {
        $traducciones = include $archivo_idioma;
    } else {
        $traducciones = [];
    }
    ?>
                <div class="container-fluid">
                    <div class="card-body">


                        <h4 class="page-title"><?= $traducciones['audit_log_view_title'] ?></h4>
                        <div id="toolbar" class="d-none">
                            <button id="exportAuditCsvBtn" class="btn btn-action-lipid">
                                <span class="mdi mdi-file-export-outline"></span>
                                <?= $traducciones['export_csv_button'] ?? 'Export CSV' ?>
                            </button>
                        </div>

                        <div class="card">
                        <div class="card-body">

                        
                            <table id="auditLogTable" data-toggle="table" data-toolbar="#toolbar" data-search="true"
                            data-show-refresh="true" data-show-columns="true" data-pagination="true"
                            data-show-pagination-switch="true" data-page-list="[10, 25, 50, 100]" data-page-size="5"
                            data-side-pagination="client"  data-locale="<?= $locale ?>"
                            class="table-borderless">
                            <thead>
                                <tr>
                                      <th data-field="audit_id" data-sortable="true" data-align="center">
            <?= $traducciones['audit_table_column_audit_id'] ?? 'Audit ID' ?>
        </th>
                                    <th data-field="action_timestamp" data-sortable="true"
                                        data-formatter="timestampFormatter">
                                        <?= $traducciones['audit_table_column_timestamp'] ?? 'Timestamp' ?>
                                    </th>
                                    <th data-field="table_name" data-sortable="true">
                                        <?= $traducciones['audit_table_column_table_name'] ?? 'Table' ?>
                                    </th>
                                    <th data-field="action_type" data-sortable="true">
                                        <?= $traducciones['audit_table_column_action_type'] ?? 'Action' ?>
                                    </th>
                                    <th data-field="action_by" data-sortable="true">
                                        <?= $traducciones['audit_table_column_action_by'] ?? 'User ID' ?>
                                    </th>
                                    <th data-field="client_ip">
                                        <?= $traducciones['audit_table_column_client_ip'] ?? 'IP Address' ?>
                                    </th>
                                    <th data-field="full_name" data-sortable="true">
    <?= $traducciones['audit_table_column_full_name'] ?? 'Full Name' ?>
</th>
<th data-field="user_type" data-sortable="true">
    <?= $traducciones['audit_table_column_user_type'] ?? 'User Type' ?>
</th>
<th data-field="client_country" data-sortable="true">
    <?= $traducciones['audit_table_column_client_country'] ?? 'Country' ?>
</th>
<th data-field="action_timezone" data-sortable="true">
    <?= $traducciones['audit_table_column_action_timezone'] ?? 'Timezone' ?>
</th>
<th data-field="client_region" data-sortable="true">
    <?= $traducciones['audit_table_column_client_region'] ?? 'Region' ?>
</th>
<th data-field="client_city" data-sortable="true">
    <?= $traducciones['audit_table_column_client_city'] ?? 'City' ?>
</th>
<th data-field="client_zipcode" data-sortable="true">
    <?= $traducciones['audit_table_column_client_zipcode'] ?? 'Postcode' ?>
</th>


                                    <th data-field="acciones" data-align="center"
                                        data-formatter="auditLogActionFormatter">
                                        <?= $traducciones['dashboard_recent_records_actions_user'] ?? 'Actions' ?>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                    </div>

                    <div class="modal fade" id="auditDetailModal" tabindex="-1" aria-labelledby="auditDetailModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header border-bottom-0">
                                    <h5 class="modal-title" id="auditDetailModalLabel">
                                        <?= $traducciones['audit_modal_title'] ?? 'Audit Log Details' ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_audit_id'] ?? 'Audit ID' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_audit_id"></span></dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_action_timestamp'] ?? 'Action Timestamp' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_action_timestamp"></span></dd>
                                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_action_timezone'] ?? 'Timezone' ?></dt>
                                        <dd class="col-sm-8"><span id="detail_action_timezone"></span></dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_action_by'] ?? 'Action By' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_action_by"></span></dd>
                                                                                <dt class="col-sm-4"><?= $traducciones['audit_modal_field_full_name'] ?? 'Full Name' ?></dt>
<dd class="col-sm-8"><span id="detail_full_name"></span></dd>

<dt class="col-sm-4"><?= $traducciones['audit_modal_field_user_type'] ?? 'User Type' ?></dt>
<dd class="col-sm-8"><span id="detail_user_type"></span></dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_table_name'] ?? 'Table Name' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_table_name"></span></dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_record_id'] ?? 'Record ID' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_record_id"></span></dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_action_type'] ?? 'Action Type' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_action_type"></span></dd>

                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_client_ip'] ?? 'Client IP' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_client_ip"></span></dd>


<dt class="col-sm-4"><?= $traducciones['audit_modal_field_client_country'] ?? 'Client Country' ?></dt>
<dd class="col-sm-8"><span id="detail_client_country"></span></dd>

<dt class="col-sm-4"><?= $traducciones['audit_modal_field_client_region'] ?? 'Region' ?></dt>
<dd class="col-sm-8"><span id="detail_client_region"></span></dd>

<dt class="col-sm-4"><?= $traducciones['audit_modal_field_client_city'] ?? 'City' ?></dt>
<dd class="col-sm-8"><span id="detail_client_city"></span></dd>

<dt class="col-sm-4"><?= $traducciones['audit_modal_field_client_zipcode'] ?? 'Postcode' ?></dt>
<dd class="col-sm-8"><span id="detail_client_zipcode"></span></dd>

<dt class="col-sm-4"><?= $traducciones['audit_modal_field_client_coordinates'] ?? 'Coordinates' ?></dt>
<dd class="col-sm-8">
    <span id="detail_client_coordinates"></span>
<div id="audit_map_container" style="width: 100%; height: 300px; display: none; border-radius: 5px; overflow: hidden;"></div>

</dd>
<dt class="col-sm-4"><?= $traducciones['audit_modal_field_geo_ip_timestamp'] ?? 'Geo IP Timestamp' ?></dt>
<dd class="col-sm-8"><span id="detail_geo_ip_timestamp"></span></dd>

<dt class="col-sm-4"><?= $traducciones['audit_modal_field_geo_ip_timezone'] ?? 'Geo IP Timezone' ?></dt>
<dd class="col-sm-8"><span id="detail_geo_ip_timezone"></span></dd>

                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_client_hostname'] ?? 'Client Hostname' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_client_hostname"></span></dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_client_os'] ?? 'Client OS' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_client_os"></span></dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_client_browser'] ?? 'Client Browser' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_client_browser"></span></dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_domain_name'] ?? 'Domain Name' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_domain_name"></span></dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_server_hostname'] ?? 'Server Hostname' ?>
                                        </dt>
                                        <dd class="col-sm-8"><span id="detail_server_hostname"></span></dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_request_uri'] ?? 'Request URI' ?>
                                        </dt>
                                        <dd class="col-sm-8">
                                            <pre class="mb-0"><code id="detail_request_uri"></code></pre>
                                        </dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_user_agent'] ?? 'User Agent' ?>
                                        </dt>
                                        <dd class="col-sm-8">
                                            <pre class="mb-0"><code id="detail_user_agent"></code></pre>
                                        </dd>
                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_changes'] ?? 'Changes' ?>
                                        </dt>
                                        <dd class="col-sm-8">
                                            <div id="detail_changes"></div>
                                        </dd>



                                        <dt class="col-sm-4">
                                            <?= $traducciones['audit_modal_field_full_row'] ?? 'Full Row Data' ?>
                                        </dt>
                                        <dd class="col-sm-8">
                                            <div id="detail_full_row"></div>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                                        <i class="mdi mdi-close"></i> <?= $traducciones['close'] ?? 'Close' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




<script src="public/assets/js/logout.js"></script>
    <script type="module">
        document.getElementById('exportAuditCsvBtn').addEventListener('click', function () {

            Swal.fire({
                title: idioma.export_loading_title,
                text: idioma.export_loading_text,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('api/auditlog/export/1')
                .then(async response => {
                    const contentType = response.headers.get("Content-Type");

                    if (contentType && contentType.includes("text/csv")) {
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);

                        const filename = `${idioma.csv_filename_prefix_audit_logs}.csv`;

                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);

                        Swal.close();
                    } else {
                        const res = await response.json();
                        Swal.fire({
                            icon: 'info',
                            title: idioma.no_records_title,
                            text: idioma.no_records_text
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: idioma.export_error_title,
                        text: idioma.export_error_text
                    });
                });


        });
        // Importa tu función real desde el archivo de configuración de API
        import {
            getAllAuditLogs,
            getAuditLogById // Asumo que tienes una función para obtener un solo log
        } from '/evolve-app/public/assets/js/apiConfig.js'


        function createChangesTable(targetElement, data) {
            targetElement.innerHTML = '';
            if (!data || Object.keys(data).length === 0) {
                targetElement.textContent = idioma.audit_no_changes_text;
                return;
            }

            // Ya no necesitamos la clase 'tabla-ajustable' aquí
            const table = document.createElement('table');
            table.className = 'table table-bordered table-sm mb-0';
            table.innerHTML = `
        <thead class="table-light">
            <tr>
                <th>${idioma.audit_fiel_text}</th>
                <th>${idioma.audit_old_value_text}</th>
                <th>${idioma.audit_new_value_text}</th>
            </tr>
        </thead>
    `;

            const tbody = document.createElement('tbody');
            for (const key in data) {
                const row = tbody.insertRow();
                row.insertCell().textContent = key;

                // Celda para "Valor Anterior"
                const cellOld = row.insertCell();
                cellOld.textContent = data[key].old ?? 'N/A';
                cellOld.classList.add('text-break'); // <--- AÑADIMOS LA CLASE DE BOOTSTRAP AQUÍ

                // Celda para "Valor Nuevo"
                const cellNew = row.insertCell();
                cellNew.textContent = data[key].new ?? 'N/A';
                cellNew.classList.add('text-break'); // <--- Y AQUÍ TAMBIÉN
            }
            table.appendChild(tbody);
            targetElement.appendChild(table);
        }


        function createKeyValueTable(targetElement, data) {
            targetElement.innerHTML = ''; // Limpiar contenido anterior
            if (!data || Object.keys(data).length === 0) {
                targetElement.textContent = idioma.audit_no_data_available;
                return;
            }

            const table = document.createElement('table');
            table.className = 'table table-bordered table-sm mb-0';
            table.innerHTML = `
        <thead class="table-light">
            <tr>
                 <th>${idioma.audit_property_text}</th>
                <th>${idioma.audit_value_text}</th>
            </tr>
        </thead>
    `;

            const tbody = document.createElement('tbody');
            for (const key in data) {
                const row = tbody.insertRow();
                row.insertCell().textContent = key;
                row.insertCell().textContent = data[key];
            }
            table.appendChild(tbody);
            targetElement.appendChild(table);
        }

        $(function () {
            const d = document;
            const detailModal = new bootstrap.Modal(d.getElementById('auditDetailModal'));
            const $table = $('#auditLogTable');

            // --- Lógica de Carga de Datos ---
            async function loadAuditLogData() {
                $table.bootstrapTable('showLoading');
                try {
                    const res = await getAllAuditLogs();
                    // Limpia y procesa los datos para la tabla
                    const rows = res.data.map(row => ({
    ...row,
    acciones: row.audit_id,
    client_hostname: row.client_hostname || 'N/A',
    user_agent: row.user_agent || 'N/A',
    client_os: row.client_os || 'N/A',
    client_browser: row.client_browser || 'N/A',
    domain_name: row.domain_name || 'N/A',
    full_name: row.full_name || 'N/A',
    user_type: row.user_type || 'N/A',
    client_country: row.client_country || 'N/A',
    action_timezone: row.action_timezone || 'N/A',
    client_region: row.client_region || 'N/A',
    client_city: row.client_city || 'N/A',
    geo_ip_timestamp: row.geo_ip_timestamp || 'N/A',
geo_ip_timezone: row.geo_ip_timezone || 'N/A',
    client_zipcode: row.client_zipcode || 'N/A',
    client_coordinates: row.client_coordinates || 'N/A',
    request_uri: row.request_uri || 'N/A',
    server_hostname: row.server_hostname || 'N/A',
    changes: JSON.stringify(row.changes || {}),
    full_row: JSON.stringify(row.full_row || '')
}));

                    $table.bootstrapTable('load', rows);
                } catch (error) {
                    console.error('Error al cargar los logs de auditoría:', error);
                    $table.bootstrapTable('removeAll');
                } finally {
                    $table.bootstrapTable('hideLoading');
                }
            }

            // --- Formateadores para la Tabla ---
            window.timestampFormatter = function (value) {
                if (!value) return '-';
                // Usar Day.js si está disponible, si no, un Date simple.
                return typeof dayjs !== 'undefined' ? dayjs(value).format('MM/DD/YYYY h:mm A') : new Date(value).toLocaleString();
            }

        window.auditLogActionFormatter = function (value, row) {
    return `
        <button class="btn btn-view action-icon view-details" data-id="${value}" title="<?= $traducciones['audit_view_button_title'] ?? 'View Details' ?>">
            <i class="mdi mdi-eye-outline"></i>
        </button>
    `;
}


            // --- Lógica para Abrir el Modal ---
            async function showAuditDetails(auditId) {
                try {
                    // **Paso clave: Llama a tu API para obtener los datos frescos de este log específico**
                    const res = await getAuditLogById(auditId);
                    if (!res.data) {
                        alert('No se pudieron encontrar los detalles del log.');
                        return;
                    }
                    const data = res.data;

                    // Rellena el modal con los datos obtenidos
                    const formatJson = (elementId, jsonString) => {
                        const element = d.getElementById(elementId);
                        try {
                            // Intenta parsear y re-stringify para un formato bonito
                            element.textContent = JSON.stringify(JSON.parse(jsonString), null, 2);
                        } catch (e) {
                            // Si falla (no es JSON válido), muestra el texto tal cual
                            element.textContent = jsonString || 'N/A';
                        }
                    };
                    d.getElementById('detail_full_name').textContent = data.full_name || 'N/A';
d.getElementById('detail_user_type').textContent = data.user_type || 'N/A';
d.getElementById('detail_client_country').textContent = data.client_country || 'N/A';
d.getElementById('detail_action_timezone').textContent = data.action_timezone || 'N/A';
d.getElementById('detail_client_region').textContent = data.client_region || 'N/A';
d.getElementById('detail_geo_ip_timestamp').textContent = data.geo_ip_timestamp || 'N/A';
d.getElementById('detail_geo_ip_timezone').textContent = data.geo_ip_timezone || 'N/A';

d.getElementById('detail_client_city').textContent = data.client_city || 'N/A';
d.getElementById('detail_client_zipcode').textContent = data.client_zipcode || 'N/A';

const coordText = data.client_coordinates || '';
d.getElementById('detail_client_coordinates').textContent = coordText;

const mapContainer = d.getElementById('audit_map_container');

// Limpiar contenedor si ya tenía un mapa
if (mapContainer && mapContainer._leaflet_id) {
    mapContainer._leaflet_id = null; // Forzar nuevo renderizado
    mapContainer.innerHTML = '';     // Vaciar contenedor
}

// Mostrar mapa solo si hay coordenadas válidas
if (coordText.includes(',')) {
    const [lat, lon] = coordText.split(',').map(v => parseFloat(v.trim()));

    if (!isNaN(lat) && !isNaN(lon) && (lat !== 0 || lon !== 0)) {
        mapContainer.style.display = 'block';
        const map = L.map(mapContainer).setView([lat, lon], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.marker([lat, lon]).addTo(map);

        setTimeout(() => map.invalidateSize(), 200); // Corrige el render en contenedor oculto
    } else {
        mapContainer.style.display = 'none';
    }
} else {
    mapContainer.style.display = 'none';
}


                    d.getElementById('detail_audit_id').textContent = data.audit_id;
                    d.getElementById('detail_action_timestamp').textContent = window.timestampFormatter(data.action_timestamp);
                    d.getElementById('detail_action_by').textContent = data.action_by;
                    d.getElementById('detail_table_name').textContent = data.table_name;
                    d.getElementById('detail_record_id').textContent = data.record_id;
                    d.getElementById('detail_action_type').textContent = data.action_type;
                    d.getElementById('detail_client_ip').textContent = data.client_ip;
                    d.getElementById('detail_client_hostname').textContent = data.client_hostname || 'N/A';
                    d.getElementById('detail_client_os').textContent = data.client_os || 'N/A';
                    d.getElementById('detail_client_browser').textContent = data.client_browser || 'N/A';
                    d.getElementById('detail_domain_name').textContent = data.domain_name || 'N/A';
                    d.getElementById('detail_server_hostname').textContent = data.server_hostname || 'N/A';
                    d.getElementById('detail_request_uri').textContent = data.request_uri || 'N/A';
                    d.getElementById('detail_user_agent').textContent = data.user_agent || 'N/A';



                    const changesData = data.changes ? JSON.parse(data.changes) : {};
                    const fullRowData = data.full_row ? JSON.parse(data.full_row) : {};

                    createChangesTable(d.getElementById('detail_changes'), changesData);
                    createKeyValueTable(d.getElementById('detail_full_row'), fullRowData);

                    detailModal.show();

                } catch (error) {
                    console.error(`Error al obtener el log de auditoría ${auditId}:`, error);
                    alert('Error al cargar los detalles. Por favor, revisa la consola.');
                }
            }

            // ---- INICIALIZACIÓN Y MANEJO DE EVENTOS ----

            // 1. Delegación de eventos en el documento principal
            $(document).on('click', '.view-details', function () {
                // 'this' es el botón que fue clickeado
                const auditId = $(this).data('id');

                console.log(auditId);

                if (auditId) {
                    showAuditDetails(auditId);
                }
            });

            // 2. Toolbar y carga inicial
            d.getElementById('toolbar').classList.remove('d-none');
            $table.on('refresh.bs.table', loadAuditLogData);
            loadAuditLogData();
        });
    </script>
</body>

</html>