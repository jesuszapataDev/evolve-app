<!DOCTYPE html>
<html lang="en" data-topbar-color="dark">

<head>
    <meta charset="utf-8" />
    <title>Auth Pages | Create Account | Sign In | Ubold - Responsive Bootstrap 5 Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />

    <link rel="shortcut icon" href="public/assets/images/favicon.ico">

    <script src="public/assets/js/head.js"></script>

    <link href="public/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <link href="public/assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <link href="public/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
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
                                        <form action="#">
                                            <div class="mb-3">
                                                <label for="emailaddress" class="form-label">Email address</label>
                                                <input class="form-control" type="email" id="emailaddress" required=""
                                                    placeholder="Enter your email">
                                            </div>

                                            <div class="mb-3">
                                                <a href="auth-recoverpw.html"
                                                    class="text-muted font-13 float-end">Forgot your password?</a>
                                                <label for="password" class="form-label">Password</label>
                                                <input class="form-control" type="password" required="" id="password"
                                                    placeholder="Enter your password">
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
                                        <p class="text-muted mb-4">Don't have an account? Create your account, it takes
                                            less than a minute</p>
                                        <form action="#">
                                            <div class="mb-3">
                                                <label for="fullname" class="form-label">Full Name</label>
                                                <input class="form-control" type="text" id="fullname"
                                                    placeholder="Enter your name" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="emailaddress2" class="form-label">Email address</label>
                                                <input class="form-control" type="email" id="emailaddress2" required
                                                    placeholder="Enter your email">
                                            </div>
                                            <div class="mb-3">
                                                <label for="password2" class="form-label">Password</label>
                                                <input class="form-control" type="password" required id="password2"
                                                    placeholder="Enter your password">
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
    <script src="public/assets/js/vendor.min.js"></script>

    <script src="public/assets/js/app.min.js"></script>
    <script type="module" src="public/assets/js/modules/login.js"></script>

    <script src="public/assets/js/pages/authentication.init.js"></script>

</body>

</html>