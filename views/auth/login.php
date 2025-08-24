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

                            <div class="text-center mb-4">
                                <div class="auth-brand">
                                    <a href="./" class="logo logo-dark text-center">
                                        <span class="logo-lg">
                                            <img src="public/assets/images/logo-dark.png" alt="" height="22">
                                        </span>
                                    </a>

                                    <a href="./" class="logo logo-light text-center">
                                        <span class="logo-lg">
                                            <img src="public/assets/images/logo-light.png" alt="" height="22">
                                        </span>
                                    </a>
                                </div>
                            </div>

                            <ul class="nav nav-tabs" id="authTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="signin-tab" data-bs-toggle="tab"
                                        data-bs-target="#signin-pane" type="button" role="tab"
                                        aria-controls="signin-pane" aria-selected="true">Sign In</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="signup-tab" data-bs-toggle="tab"
                                        data-bs-target="#signup-pane" type="button" role="tab"
                                        aria-controls="signup-pane" aria-selected="false">Sign Up</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="authTabsContent">
                                <div class="tab-pane fade show active" id="signin-pane" role="tabpanel"
                                    aria-labelledby="signin-tab" tabindex="0">
                                    <div class="p-sm-3">
                                        <h4 class="mt-3">Sign In</h4>
                                        <p class="text-muted mb-4">Enter your email address and password to access
                                            account.</p>
                                        <form id="signInForm" data-validation="reactive" novalidate>
                                            <div class="mb-3">
                                                <label for="emailaddress" class="form-label">Email address</label>
                                                <input class="form-control" type="email" id="emailaddress" name="email"
                                                    placeholder="Enter your email" data-rules="noVacio|email"
                                                    data-message-no-vacio="<?= $traducciones['validation_required']; ?>"
                                                    data-message-email="<?= $traducciones['validation_email_format']; ?>">
                                            </div>
                                            <div class="mb-3">
                                                <a href="auth-recoverpw.html"
                                                    class="text-muted font-13 float-end">Forgot your password?</a>
                                                <label for="password" class="form-label">Password</label>
                                                <input class="form-control" type="password" id="password"
                                                    name="password" placeholder="Enter your password"
                                                    data-rules="noVacio"
                                                    data-message-no-vacio="<?= $traducciones['validation_required']; ?>">
                                            </div>
                                            <div class="mb-3">
                                                <button class="btn btn-primary btn-sm float-sm-end" type="submit"> Log
                                                    In </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="signup-pane" role="tabpanel" aria-labelledby="signup-tab"
                                    tabindex="0">
                                    <div class="p-sm-3">
                                        <h4 class="mt-3">Free Sign Up</h4>
                                        <p class="text-muted mb-4">Don't have an account? Create your account...</p>
                                        <form id="signUpForm" data-validation="reactive" novalidate>
                                            <div class="mb-3">
                                                <label for="fullname" class="form-label">Full Name</label>
                                                <input class="form-control" type="text" id="fullname"
                                                    name="nombre_completo" placeholder="Enter your name"
                                                    data-rules="noVacio|longitudMaxima:100"
                                                    data-message-no-vacio="<?= $traducciones['validation_required']; ?>"
                                                    data-message-longitud-maxima="<?= $traducciones['validation_max_length_name']; ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="emailaddress2" class="form-label">Email address</label>
                                                <input class="form-control" type="email" id="emailaddress2"
                                                    name="correo" placeholder="Enter your email"
                                                    data-rules="noVacio|email"
                                                    data-message-no-vacio="<?= $traducciones['validation_required']; ?>"
                                                    data-message-email="<?= $traducciones['validation_email_format']; ?>">
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="country-select">Country</label>
                                                    <div data-phone-select=""></div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="phone"
                                                        class="form-label"><?= $traducciones['telephone_label'] ?? 'Telephone' ?></label>
                                                    <input type="text" id="telephone" name="telephone"
                                                        class="form-control" data-rules="noVacio|longitudMinima:8"
                                                        data-message-no-vacio="<?= $traducciones['validation_required']; ?>"
                                                        data-message-longitud-minima="<?= $traducciones['validation_phone_min_length']; ?>"
                                                        data-validate-duplicate-url="/api/check/telephone"
                                                        data-message-duplicado="Este teléfono ya está registrado."
                                                        data-validate-masked="true">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="password2" class="form-label">Password</label>
                                                <input class="form-control" type="password" id="password2"
                                                    name="contrasena" placeholder="Enter your password"
                                                    data-rules="noVacio|longitudMinima:8"
                                                    data-message-no-vacio="<?= $traducciones['validation_required']; ?>"
                                                    data-message-longitud-minima="<?= $traducciones['validation_min_length']; ?>">
                                            </div>
                                            <div class="mb-0">
                                                <button class="btn btn-success btn-sm float-sm-end" type="submit"> Sign
                                                    Up </button>
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
    <script type="module" src="public/assets/js/helpers/validarFormulario.js"></script>

    <script src="public/assets/js/imask.js"></script>

    <script src="public/assets/js/vendor.min.js"></script>

    <script src="public/assets/js/app.min.js"></script>

    <script src="public/assets/libs/select2/js/select2.min.js"></script>

    <script type="module" src="public/assets/js/modules/login.js"></script>

</body>

</html>