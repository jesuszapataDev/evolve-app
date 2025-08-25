<!DOCTYPE html>
<html lang="en" data-topbar-color="dark">

<head>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>

<body class="authentication-bg authentication-bg-pattern bg-success-light">

    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-body p-4">
                            <a href="/" role="button">
                                <button class="btn btn-back mb-3 btn-back-translate">
                                    <i class="mdi mdi-arrow-left"></i> <span
                                        class="btn-back-text"><?= htmlspecialchars($traducciones['back_button']) ?></span>
                                </button>
                            </a>
                            <div class="text-center mb-4">
                                <div class="auth-logo">
                                    <a href="/" class="logo  text-center">
                                        <span class="logo-lg"><img src="/public/assets/images/logo-index.png" alt=""
                                                height="60"></span>
                                    </a>
                                    <a href="/" class="logo  text-center">
                                        <span class="logo-sm"><img src="/public/assets/images/logo-index.png" alt=""
                                                height="60"></span>
                                    </a>
                                </div>
                            </div>

                            <form id="email-form">
                                <div class="mb-3">
                                    <label class="form-label"
                                        for="email"><?= htmlspecialchars($traducciones['email_label']) ?></label>
                                    <input class="form-control" type="email" id="email"
                                        placeholder="<?= htmlspecialchars($traducciones['email_placeholder']) ?>">
                                </div>
                                <div class="text-center d-flex justify-content-end">
                                    <button class="btn btn-sign-up" type="submit"
                                        id="btn-check-email"><?= htmlspecialchars($traducciones['check_email_button']) ?></button>
                                </div>
                            </form>

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
                                            <form id="security-answers-form">
                                                <div class="mb-3">
                                                    <label
                                                        class="form-label"><?= htmlspecialchars($traducciones['question_1_label']) ?></label>
                                                    <input class="form-control" type="text" id="question1" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="answer1"><?= htmlspecialchars($traducciones['answer_1_label']) ?></label>
                                                    <input class="form-control" type="text" id="answer1" name="answer1"
                                                        placeholder="<?= htmlspecialchars($traducciones['enter_answer_placeholder']) ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label
                                                        class="form-label"><?= htmlspecialchars($traducciones['question_2_label']) ?></label>
                                                    <input class="form-control" type="text" id="question2" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="answer2"><?= htmlspecialchars($traducciones['answer_2_label']) ?></label>
                                                    <input class="form-control" type="text" id="answer2" name="answer2"
                                                        placeholder="<?= htmlspecialchars($traducciones['enter_answer_placeholder']) ?>">
                                                </div>
                                                <div class="text-center d-flex justify-content-end">
                                                    <button class="btn btn-action-lipid" type="submit"
                                                        id="btn-verify-answers"><?= htmlspecialchars($traducciones['verify_answers_button']) ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                            <form id="change-password-form">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="new_password"><?= htmlspecialchars($traducciones['new_password_label']) ?></label>
                                                    <input class="form-control" type="password" id="new_password"
                                                        placeholder="<?= htmlspecialchars($traducciones['new_password_placeholder']) ?>">
                                                </div>
                                                <div class="text-center d-grid">
                                                    <button class="btn btn-sign-up" type="submit"
                                                        id="btn-update-password"><?= htmlspecialchars($traducciones['update_password_button']) ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="module" src="public/assets/js/helpers/validarFormulario.js"></script>
    <script type="module" src="public/assets/js/helpers/validacionesEspeciales.js"></script>

    <script src="public/assets/js/imask.js"></script>

    <script src="public/assets/js/vendor.min.js"></script>

    <script src="public/assets/js/app.min.js"></script>

    <script src="public/assets/libs/select2/js/select2.min.js"></script>




    <script type="module">
        let currentUserId = null;



        import { validateFormFields, showLoader, hideLoader } from "/public/assets/js/helpers/helpers.js";

        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        const lang = '<?= htmlspecialchars($data['lang'] ?? 'en') ?>';
        const userType = '<?= htmlspecialchars($data['userType'] ?? 'User') ?>';
        const token = getQueryParam('token');
        if (token) {
            sessionStorage.setItem('reset_token', token);
            $(document).ready(function () {
                $('#changePasswordModal').modal('show');
            });
        }

        // Formulario email
        $('#email-form').submit(function (e) {
            e.preventDefault();
            if (!validateFormFields(e.target, ['email'])) return;
            showLoader();
            $.ajax({
                url: '/password-recovery/verify-email',
                type: 'POST',
                data: { email: $('#email').val(), lang: lang, user_type: userType },
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
                    hideLoader();
                }
            });
        });

        // Verificar respuestas
        $('#security-answers-form').submit(function (e) {
            e.preventDefault();
            if (!validateFormFields(e.target, ['answer1', 'answer2'])) return;
            showLoader();
            $.ajax({
                url: '/password-recovery/verify-answers',
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
                    hideLoader();
                }
            });
        });

        // Cambio de contraseña
        $('#change-password-form').submit(function (e) {
            e.preventDefault();

            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const newPassword = $('#new_password').val().trim();

            // Validar campos requeridos
            if (!validateFormFields(e.target, ['new_password'])) return;

            // Validación adicional de longitud mínima
            if (newPassword.length < 6) {
                Swal.fire('<?= htmlspecialchars($traducciones['alert_error_title']) ?>', '<?= $traducciones['password_too_short'] ?? 'Password must be at least 6 characters.' ?>', 'error');
                return;
            }

            // Bloquear múltiples envíos
            $submitBtn.prop('disabled', true);
            showLoader();

            // Construcción del objeto POST
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
                url: '/password-recovery/update-password',
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function (response) {
                    if (response.value === true) {
                        Swal.fire('<?= htmlspecialchars($traducciones['alert_success_title']) ?>', response.message, 'success')
                            .then(() => {
                                sessionStorage.removeItem('reset_token');
                                window.location.href = `/`; // Redirigir a login
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
                }
                ,
                complete: function () {
                    hideLoader();
                    $submitBtn.prop('disabled', false);
                }
            });
        });

    </script>

</body>

</html>