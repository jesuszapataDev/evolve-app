<head>
    <meta charset="utf-8" />
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="public/assets/images/favicon.ico">

    <!-- Plugins css -->
    <link href="public/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
    <link href="public/assets/libs/selectize/css/selectize.bootstrap3.css" rel="stylesheet" type="text/css" />

    <!-- Theme Config Js -->
    <script src="public/assets/js/head.js"></script>

    <!-- Bootstrap css -->
    <link href="public/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- App css -->
    <link href="public/assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <!-- Icons css -->
    <link href="public/assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- CUSTOM STYLES -->
    <link href="public/assets/css/app-styles.css" rel="stylesheet" type="text/css" />

    <!-- SELECT2 -->

    <!-- select2 -->
    <link href="public/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <!-- SWEET ALERT -->
    <link href="public/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <script src="public/assets/libs/sweetalert2/sweetalert2.all.min.js"></script>

    <script>
        const traducciones = <?= json_encode($traducciones) ?>;

    </script>
</head>