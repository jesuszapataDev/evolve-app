<head>
    <meta charset="utf-8" />
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="public/assets/images/favicon.ico">


    <!-- PRINCIPAL -->
    <link href="public/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Bootstrap -->
    <link href="public/assets/libs/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
    <link href="public/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />



    <!-- App css -->
    <link href="public/assets/libs/switchery/switchery.min.css" rel="stylesheet" />
    <link href="public/assets/css/app-styles.css" rel="stylesheet" />

    <!-- Sweetalert2 -->
    <link href="public/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <script src="public/assets/libs/sweetalert2/sweetalert2.all.min.js"></script>

    <!-- icons -->
    <link href="public/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="public/assets/css/leaflet.css" rel="stylesheet" type="text/css" />
    <!-- Head js -->
    <script src="public/assets/js/head.js"></script>


    <!-- FLATPICKR -->
    <link href="public/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css">
    <script src="public/assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="public/assets/libs/flatpickr/l10n/es.js"></script>


    <!-- select2 -->
    <link href="public/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css">

    <!-- Dayjs -->
    <script src="public/assets/libs/dayjs/dayjs.min.js"></script>
    <script src="public/assets/libs/dayjs/plugin/isSameOrBefore.js"></script>
    <script src="public/assets/libs/dayjs/plugin/isoWeek.js"></script>
    <script src="public/assets/libs/dayjs/plugin/relativeTime.js"></script>
    <script src="public/assets/libs/dayjs/plugin/utc.js"></script>



    <link href="public/assets/css/cropper.min.css" rel="stylesheet" />




    <!-- Init js-->


    <script>
        const traducciones = <?= json_encode($traducciones) ?>;
        var idioma = "<?= strtoupper($_SESSION["idioma"] ?? "ES") ?>";
    </script>
</head>