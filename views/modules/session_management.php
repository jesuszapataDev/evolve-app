<?php
// PHP logic to get $idioma, $locale, and $traducciones remains the same
$idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
$locale = $idioma === 'ES' ? 'es-ES' : 'en-US';
// Assume $traducciones is loaded here
?>

<div class="container-fluid">
    <h4 class="page-title"><?= $traducciones['session_log_view_title'] ?? 'Session Audit Log' ?></h4>
    <div id="toolbar">
        <button class="btn btn-add-user" id="btnSessionConfig">
            <i class="mdi mdi-cog-outline"></i> <?= $traducciones['session_config'] ?? 'Session Config' ?>
        </button>
        <button class="btn btn-action-lipid" id="btnExportCSV">
            <i class="mdi mdi-file-export-outline"></i> <?= $traducciones['export_csv_button'] ?? 'Export to CSV' ?>
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="sessionAuditTable" class="table table-borderless">
                <thead>
                    <tr>
                        <th data-field="session_id"><?= $traducciones['session_id'] ?? 'Session ID' ?></th>
                        <th data-field="user_id"><?= $traducciones['user_id'] ?? 'User ID' ?></th>
                        <th data-field="user_name"><?= $traducciones['user_name'] ?? 'Username' ?></th>
                        <th data-field="user_type"><?= $traducciones['user_type'] ?? 'User Type' ?></th>
                        <th data-field="full_name"><?= $traducciones['full_name'] ?? 'Full Name' ?></th>
                        <th data-field="login_time"><?= $traducciones['login_time'] ?? 'Login Time' ?></th>
                        <th data-field="logout_time"><?= $traducciones['logout_time'] ?? 'Logout Time' ?></th>
                        <th data-field="actions" data-formatter="detalleFormatter" data-align="center">
                            <?= $traducciones['audit_table_column_actions'] ?? 'Details' ?>
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Detalles -->
<!-- Modal de Detalles -->
<div class="modal fade" id="sessionDetailModal" tabindex="-1" aria-labelledby="sessionDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="sessionDetailModalLabel">
                    <!-- Título dinámico desde JS -->
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row" id="sessionDetailContent">
                    <!-- Aquí se insertan los detalles dinámicos -->
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

<div class="modal fade" id="sessionConfigModal" tabindex="-1" aria-labelledby="sessionConfigLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="sessionConfigLabel">
                    <?= $traducciones['session_config'] ?? 'Session Configuration' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sessionConfigForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inactivityTime" class="form-label">
                            <?= $traducciones['inactivity_time'] ?? 'Inactivity Time (min)' ?> <span
                                class="text-danger">*</span>
                        </label>
                        <input type="text" min="1" max="99999" class="form-control number" id="inactivityTime"
                            name="inactivityTime" required>
                    </div>
                    <div class="text-muted fst-italic small">
                        <?= $traducciones['required_fields_note'] ?? '* Required fields' ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save-outline"></i>
                        <?= $traducciones['save'] ?></button>
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i class="mdi mdi-cancel"></i>
                        <?= $traducciones['cancel'] ?></button>

                </div>
            </form>
        </div>
    </div>
</div>
<script src="public/assets/js/logout.js"></script>

<script>
    const mensajes2 = {
        <?= $idioma ?>: <?= json_encode($traducciones, JSON_UNESCAPED_UNICODE) ?>
    };
    window.pageData = {
        idioma: '<?= $idioma ?>',
        locale: '<?= $locale ?>',
        translations: <?= json_encode($traducciones ?? []) ?>
    };
</script>

<script src="public/assets/js/modules/session-management.js" type="module"></script>