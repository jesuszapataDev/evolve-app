<?php
// --- CONFIGURACIÓN DE IDIOMA EN PHP ---
$idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
$locale = $idioma === 'ES' ? 'es-ES' : 'en-US';
if (!in_array($idioma, ['EN', 'ES'])) {
    $idioma = 'EN';
}

?>

<!-- Contenedor principal de la página -->
<div class="container-fluid">
    <div class="card-body">
        <h4 class="page-title"><?= $traducciones['audit_log_view_title'] ?? 'Audit Log' ?></h4>

        <!-- Barra de herramientas para la tabla (inicialmente oculta) -->
        <div id="toolbar" class="d-none">
            <button id="exportAuditCsvBtn" class="btn btn-action-lipid">
                <span class="mdi mdi-file-export-outline"></span>
                <?= $traducciones['export_csv_button'] ?? 'Export CSV' ?>
            </button>
        </div>

        <!-- Tarjeta que contiene la tabla de auditoría -->
        <div class="card">
            <div class="card-body">
                <!--
                    NOTA: La tabla ya no tiene atributos 'data-*' para la inicialización.
                    Toda la configuración se realiza en el archivo JavaScript.
                -->
                <table id="auditLogTable" class="table-borderless">
                    <thead>
                        <tr>
                            <th data-field="audit_id" data-sortable="true" data-align="center">
                                <?= $traducciones['audit_table_column_audit_id'] ?? 'Audit ID' ?>
                            </th>
                            <th data-field="action_timestamp" data-sortable="true" data-formatter="timestampFormatter">
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
                            <th data-field="full_name" data-sortable="true">
                                <?= $traducciones['audit_table_column_full_name'] ?? 'Full Name' ?>
                            </th>
                            <th data-field="client_ip">
                                <?= $traducciones['audit_table_column_client_ip'] ?? 'IP Address' ?>
                            </th>
                            <th data-field="acciones" data-align="center" data-formatter="auditLogActionFormatter">
                                <?= $traducciones['dashboard_recent_records_actions_user'] ?? 'Actions' ?>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar los detalles de un registro de auditoría -->
    <div class="modal fade" id="auditDetailModal" tabindex="-1" aria-labelledby="auditDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="auditDetailModalLabel">
                        <?= $traducciones['audit_modal_title'] ?? 'Audit Log Details' ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dl class="row">
                        <!-- El contenido se inyectará dinámicamente desde JavaScript -->
                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_audit_id'] ?? 'Audit ID' ?></dt>
                        <dd class="col-sm-8"><span id="detail_audit_id"></span></dd>

                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_action_timestamp'] ?? 'Timestamp' ?>
                        </dt>
                        <dd class="col-sm-8"><span id="detail_action_timestamp"></span></dd>

                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_action_by'] ?? 'Action By' ?></dt>
                        <dd class="col-sm-8"><span id="detail_action_by"></span></dd>

                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_full_name'] ?? 'Full Name' ?></dt>
                        <dd class="col-sm-8"><span id="detail_full_name"></span></dd>

                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_table_name'] ?? 'Table Name' ?></dt>
                        <dd class="col-sm-8"><span id="detail_table_name"></span></dd>

                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_action_type'] ?? 'Action Type' ?></dt>
                        <dd class="col-sm-8"><span id="detail_action_type"></span></dd>

                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_client_ip'] ?? 'Client IP' ?></dt>
                        <dd class="col-sm-8"><span id="detail_client_ip"></span></dd>

                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_client_country'] ?? 'Country' ?></dt>
                        <dd class="col-sm-8"><span id="detail_client_country"></span></dd>

                        <dt class="col-sm-4">
                            <?= $traducciones['audit_modal_field_client_coordinates'] ?? 'Coordinates' ?>
                        </dt>
                        <dd class="col-sm-8">
                            <span id="detail_client_coordinates"></span>
                            <div id="audit_map_container"
                                style="width: 100%; height: 250px; display: none; border-radius: 5px; overflow: hidden; margin-top: 10px;">
                            </div>
                        </dd>

                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_user_agent'] ?? 'User Agent' ?></dt>
                        <dd class="col-sm-8">
                            <pre class="mb-0 small"><code id="detail_user_agent"></code></pre>
                        </dd>

                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_changes'] ?? 'Changes' ?></dt>
                        <dd class="col-sm-8">
                            <div id="detail_changes"></div>
                        </dd>

                        <dt class="col-sm-4"><?= $traducciones['audit_modal_field_full_row'] ?? 'Full Row Data' ?></dt>
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

<!-- Scripts -->
<script src="public/assets/js/logout.js"></script>

<!--
    PASO 1: Pasar datos de PHP a un objeto global de JavaScript.
    'json_encode' convierte el array de PHP en un objeto JSON seguro.
-->
<script>
    window.pageData = {
        locale: '<?= $locale ?>',
        translations: <?= json_encode($traducciones ?? []) ?>
    };
</script>

<script src="public/assets/js/modules/audit-log.js" type="module"></script>