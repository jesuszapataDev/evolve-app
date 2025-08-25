<?php
// --- PHP LANGUAGE AND TRANSLATION SETUP ---
$idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
$locale = $idioma === 'ES' ? 'es-ES' : 'en-US';
// Assume $traducciones array is loaded here from a language file
?>

<div class="container-fluid">
    <h4 class="page-title"><?= $traducciones['users_view_title'] ?? 'User Management' ?></h4>

    <!-- Toolbar with "Add User" button -->
    <div id="toolbar">
        <button class="btn btn-add-user" id="btn-add-user">
            <i class="mdi mdi-plus"></i> <?= $traducciones['add_user_button'] ?? 'Add User' ?>
        </button>
    </div>

    <!-- Card containing the user table -->
    <div class="card">
        <div class="card-body">
            <table id="usersTable" class="table table-borderless">
                <!-- The table headers are defined in JavaScript for better control -->
            </table>
        </div>
    </div>
</div>

<!-- Modal for Viewing User Details -->
<div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="userDetailModalLabel">
                    <?= $traducciones['user_details_title'] ?? 'User Details' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row" id="userDetailContent">
                    <!-- Dynamic content will be injected here by JavaScript -->
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

<!-- Modal for Creating and Editing Users -->
<div class="modal fade" id="userFormModal" tabindex="-1" aria-labelledby="userFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="userFormModalLabel">
                    <!-- Title will be set to "Add User" or "Edit User" by JavaScript -->
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="userId" name="user_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label"><?= $traducciones['first_name'] ?? 'First Name' ?>
                                <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label"><?= $traducciones['last_name'] ?? 'Last Name' ?>
                                <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label"><?= $traducciones['email'] ?? 'Email' ?> <span
                                class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label" for="country-select">Country</label>
                            <div data-phone-select=""></div>
                        </div>

                        <div class="col-md-6">
                            <label for="telephone"
                                class="form-label"><?= $traducciones['telephone_label'] ?? 'Telephone' ?></label>
                            <input type="text" id="telephone" name="telephone" class="form-control"
                                data-rules="noVacio|longitudMinima:8"
                                data-message-no-vacio="<?= $traducciones['validation_required']; ?>"
                                data-message-longitud-minima="<?= $traducciones['validation_phone_min_length']; ?>"
                                data-validate-duplicate-url="/api/check/telephone"
                                data-message-duplicado="Este teléfono ya está registrado." data-validate-masked="true">
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sex" class="form-label"><?= $traducciones['sex'] ?? 'Sex' ?> <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="sex" name="sex" required>
                                <option value="m"><?= $traducciones['sex_m'] ?? 'Male' ?></option>
                                <option value="f"><?= $traducciones['sex_f'] ?? 'Female' ?></option>
                            </select>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rol" class="form-label"><?= $traducciones['rol'] ?? 'Role' ?> <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="user"><?= $traducciones['role_user'] ?? 'User' ?></option>
                                <option value="administrator"><?= $traducciones['role_admin'] ?? 'Administrator' ?>
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label"><?= $traducciones['status'] ?? 'Status' ?> <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="1"><?= $traducciones['status_active'] ?? 'Active' ?></option>
                                <option value="0"><?= $traducciones['status_inactive'] ?? 'Inactive' ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="timezone" class="form-label"><?= $traducciones['timezone'] ?? 'Timezone' ?></label>
                        <input type="text" class="form-control" id="timezone" name="timezone">
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password"
                                class="form-label"><?= $traducciones['password'] ?? 'Password' ?></label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">
                                <?= $traducciones['password_leave_blank'] ?? 'Leave blank to keep current password.' ?>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirmPassword"
                                class="form-label"><?= $traducciones['confirm_password'] ?? 'Confirm Password' ?></label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password">
                        </div>
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
<script src="public/assets/js/modules/users.js" type="module"></script>

<!-- Scripts -->
<script>
    // Pass PHP variables to a global JavaScript object
    window.pageData = {
        locale: '<?= $locale ?>',
        translations: <?= json_encode($traducciones ?? []) ?>
    };
</script>