<!DOCTYPE html>
<html lang="en" data-topbar-color="dark">
<head>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>

<body class="authentication-bg authentication-bg-pattern">

    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-body p-4">
                            <a href="/login" role="button">
                                <button class="btn btn-back mb-3 btn-back-translate">
                                    <i class="mdi mdi-arrow-left"></i>
                                    <span class="btn-back-text"><?= htmlspecialchars($traducciones['back_button']) ?></span>
                                </button>
                            </a>

                            <div class="text-center mb-4">
                                <div class="auth-logo">
                                    <a href="/" class="logo text-center">
                                        <span class="logo-lg">
                                            <img src="public/assets/images/logo-index.png" alt="" height="60">
                                        </span>
                                    </a>
                                    <a href="/" class="logo text-center">
                                        <span class="logo-sm">
                                            <img src="public/assets/images/logo-index.png" alt="" height="60">
                                        </span>
                                    </a>
                                </div>
                            </div>

                            <!-- ====== FORM: EMAIL (validación reactiva) ====== -->
                            <form id="email-form" data-validation="reactive" novalidate>
                                <div class="mb-3">
                                    <label class="form-label" for="email">
                                        <?= htmlspecialchars($traducciones['email_label']) ?>
                                    </label>
                                    <input
                                        class="form-control"
                                        type="email"
                                        id="email"
                                        name="email"
                                        placeholder="<?= htmlspecialchars($traducciones['email_placeholder']) ?>"
                                        data-rules="noVacio|email"
                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>"
                                        data-message-email="<?= htmlspecialchars($traducciones['validation_email_format']) ?>"
                                    >
                                </div>
                                <div class="text-center d-flex justify-content-end">
                                    <button class="btn btn-sign-up" type="submit" id="btn-check-email">
                                        <?= htmlspecialchars($traducciones['check_email_button']) ?>
                                    </button>
                                </div>
                            </form>

                            <!-- ====== MODAL: SECURITY QUESTIONS (validación reactiva) ====== -->
                            <div class="modal fade" id="securityQuestionsModal" tabindex="-1"
                                 aria-labelledby="securityQuestionsModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="securityQuestionsModalLabel">
                                                <?= htmlspecialchars($traducciones['security_questions_modal_title']) ?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="security-answers-form" data-validation="reactive" novalidate>
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <?= htmlspecialchars($traducciones['question_1_label']) ?>
                                                    </label>
                                                    <input class="form-control" type="text" id="question1" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="answer1">
                                                        <?= htmlspecialchars($traducciones['answer_1_label']) ?>
                                                    </label>
                                                    <input
                                                        class="form-control"
                                                        type="text"
                                                        id="answer1"
                                                        name="answer1"
                                                        placeholder="<?= htmlspecialchars($traducciones['enter_answer_placeholder']) ?>"
                                                        data-rules="noVacio|longitudMinima:2"
                                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>"
                                                        data-message-longitud-minima="<?= htmlspecialchars($traducciones['validation_min_length_generic'] ?? 'Longitud mínima no válida.') ?>"
                                                    >
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <?= htmlspecialchars($traducciones['question_2_label']) ?>
                                                    </label>
                                                    <input class="form-control" type="text" id="question2" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="answer2">
                                                        <?= htmlspecialchars($traducciones['answer_2_label']) ?>
                                                    </label>
                                                    <input
                                                        class="form-control"
                                                        type="text"
                                                        id="answer2"
                                                        name="answer2"
                                                        placeholder="<?= htmlspecialchars($traducciones['enter_answer_placeholder']) ?>"
                                                        data-rules="noVacio|longitudMinima:2"
                                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>"
                                                        data-message-longitud-minima="<?= htmlspecialchars($traducciones['validation_min_length_generic'] ?? 'Longitud mínima no válida.') ?>"
                                                    >
                                                </div>
                                                <div class="text-center d-flex justify-content-end">
                                                    <button class="btn btn-action-lipid" type="submit" id="btn-verify-answers">
                                                        <?= htmlspecialchars($traducciones['verify_answers_button']) ?>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ====== MODAL: CHANGE PASSWORD (validación reactiva) ====== -->
                            <div class="modal fade" id="changePasswordModal" tabindex="-1"
                                 aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="changePasswordModalLabel">
                                                <?= htmlspecialchars($traducciones['change_password_modal_title']) ?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="change-password-form" data-validation="reactive" novalidate>
                                                <div class="mb-3">
                                                    <label class="form-label" for="new_password">
                                                        <?= htmlspecialchars($traducciones['new_password_label']) ?>
                                                    </label>
                                                    <div class="input-group input-group-merge">
                                                        <input
                                                            class="form-control"
                                                            type="password"
                                                            id="new_password"
                                                            name="new_password"
                                                            placeholder="<?= htmlspecialchars($traducciones['new_password_placeholder']) ?>"
                                                            data-rules="noVacio|longitudMinima:8"
                                                            data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>"
                                                            data-message-longitud-minima="<?= htmlspecialchars($traducciones['validation_min_length_password'] ?? 'La contraseña debe tener al menos 8 caracteres.') ?>"
                                                            data-error-container=".input-group"
                                                        >
                                                        <div class="input-group-text toggle-password-button" data-target-input="#new_password">
                                                            <span class="bi bi-eye"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center d-grid">
                                                    <button class="btn btn-sign-up" type="submit" id="btn-update-password">
                                                        <?= htmlspecialchars($traducciones['update_password_button']) ?>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /modals -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Helpers de validación -->
    <script type="module" src="public/assets/js/helpers/validarFormulario.js"></script>
    <script type="module" src="public/assets/js/helpers/validacionesEspeciales.js"></script>

    <script src="public/assets/js/imask.js"></script>
    <script src="public/assets/js/vendor.min.js"></script>
    <script src="public/assets/js/app.min.js"></script>
    <script src="public/assets/libs/select2/js/select2.min.js"></script>

    <script type="module">
        let currentUserId = null;

        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        const lang = '<?= htmlspecialchars($data['lang'] ?? 'en') ?>';
        const userType = '<?= htmlspecialchars($data['userType'] ?? 'User') ?>';

        // Manejo de token por query (flujo por token)
        const tokenFromUrl = getQueryParam('token');
        if (tokenFromUrl) {
            sessionStorage.setItem('reset_token', tokenFromUrl);
            $(document).ready(function () {
                $('#changePasswordModal').modal('show');
            });
        }

        /* ============================
         *  EMAIL FORM (validación reactiva)
         * ============================ */
        $('#email-form')
            .on('submit', function (e) {
                // Deja que el helper decida; evitamos doble manejo si el helper cancela
                e.preventDefault();
            })
            .on('validation:success', function (e) {
                const $form = $(this);
                $.ajax({
                    url: 'api/password-recovery/verify-email',
                    type: 'POST',
                    data: {
                        email: $('#email').val(),
                        lang: lang,
                        user_type: userType
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.value === true) {
                            if (response.data?.questions) {
                                currentUserId = response.data.userId;
                                $('#question1').val(response.data.questions.question1);
                                $('#question2').val(response.data.questions.question2);
                                $('#securityQuestionsModal').modal('show');
                            } else {
                                Swal.fire(
                                    '<?= htmlspecialchars($traducciones['alert_success_title']) ?>',
                                    response.message || '<?= htmlspecialchars($traducciones['alert_recovery_email_sent']) ?>',
                                    'success'
                                );
                            }
                        } else {
                            Swal.fire(
                                '<?= htmlspecialchars($traducciones['alert_error_title']) ?>',
                                response.message || '<?= htmlspecialchars($traducciones['alert_unexpected_error']) ?>',
                                'error'
                            );
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(
                            '<?= htmlspecialchars($traducciones['alert_error_title']) ?>',
                            xhr.responseJSON?.message || '<?= htmlspecialchars($traducciones['alert_unexpected_error']) ?>',
                            'error'
                        );
                    },
                    complete: function () {
                    }
                });
            });

        /* =================================
         *  SECURITY ANSWERS (validación reactiva)
         * ================================= */
        $('#security-answers-form')
            .on('submit', function (e) {
                e.preventDefault();
            })
            .on('validation:success', function () {
                $.ajax({
                    url: 'api/password-recovery/verify-answers',
                    type: 'POST',
                    data: {
                        userId: currentUserId,
                        answer1: $('#answer1').val(),
                        answer2: $('#answer2').val(),
                        lang: lang,
                        user_type: userType
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.value === true) {
                            $('#securityQuestionsModal').modal('hide');
                            $('#changePasswordModal').modal('show');
                        } else {
                            Swal.fire('<?= htmlspecialchars($traducciones['alert_error_title']) ?>', response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('<?= htmlspecialchars($traducciones['alert_error_title']) ?>', xhr.responseJSON?.message, 'error');
                    },
                    complete: function () {
                    }
                });
            });

        /* ============================
         *  CHANGE PASSWORD (validación reactiva)
         * ============================ */
        $('#change-password-form')
            .on('submit', function (e) {
                e.preventDefault();
            })
            .on('validation:success', function () {
                const $form = $(this);
                const $submitBtn = $form.find('button[type="submit"]');
                const newPassword = $('#new_password').val().trim();

                // (La longitud mínima ya la valida el helper con data-rules)
                $submitBtn.prop('disabled', true);

                const token = sessionStorage.getItem('reset_token');
                const postData = {
                    new_password: newPassword,
                    lang: lang,
                    user_type: userType
                };

                if (currentUserId) {
                    postData.userId = currentUserId;
                    sessionStorage.removeItem('reset_token');
                } else if (token) {
                    postData.token = token;
                }

                $.ajax({
                    url: 'api/password-recovery/update-password',
                    type: 'POST',
                    data: postData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.value === true) {
                            Swal.fire(
                                '<?= htmlspecialchars($traducciones['alert_success_title']) ?>',
                                response.message,
                                'success'
                            ).then(() => {
                                sessionStorage.removeItem('reset_token');
                                window.location.href = `/login`;
                            });
                        } else {
                            Swal.fire('<?= htmlspecialchars($traducciones['alert_error_title']) ?>', response.message, 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error →", {
                            status: status,
                            error: error,
                            response: xhr.responseText,
                            parsed: xhr.responseJSON
                        });
                        Swal.fire(
                            '<?= htmlspecialchars($traducciones['alert_error_title']) ?>',
                            xhr.responseJSON?.message || '<?= $traducciones['alert_unexpected_error'] ?? 'An unexpected error occurred.' ?>',
                            'error'
                        );
                    },
                    complete: function () {
                        $submitBtn.prop('disabled', false);
                    }
                });
            });


    </script>

</body>
</html>
