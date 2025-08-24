<head>
    <meta charset="utf-8" />
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="public/assets/images/favicon.ico">


    <!-- jquery -->
    <script src="public/assets/libs/jquery/jquery.min.js"></script>

    <!-- Bootstrap -->
    <link href="public/assets/libs/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
    <link href="public/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />



    <!-- App css -->
    <link href="public/assets/libs/switchery/switchery.min.css" rel="stylesheet" />
<script src="public/assets/libs/switchery/switchery.min.js"></script>
    <link href="public/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    
    <link href="public/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <!-- icons -->
    <link href="public/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="public/assets/css/leaflet.css" rel="stylesheet" type="text/css" />
    <!-- Head js -->
    <script src="public/assets/js/head.js"></script>

    <!-- C3 css -->
    <link href="public/assets/libs/c3/c3.min.css" rel="stylesheet" type="text/css" />


    <!-- script js -->
    <!-- Plugins js -->
    <script src="public/assets/libs/jquery-mask-plugin/jquery.mask.min.js"></script>
    <script src="public/assets/libs/autonumeric/autoNumeric.min.js"></script>
    <!-- Sweet Alerts js -->
    <script src="public/assets/libs/sweetalert2/sweetalert2.all.min.js"></script>
    <!-- FLATPICKR -->
    <link href="public/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css">
    <script src="public/assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="public/assets/libs/flatpickr/l10n/es.js"></script>

    
    <!-- select2 -->
    <script src="public/assets/libs/select2/js/select2.min.js"></script>
    <link href="public/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css">

    <!-- Dayjs -->
    <script src="public/assets/libs/dayjs/dayjs.min.js"></script>
    <script src="public/assets/libs/dayjs/plugin/isSameOrBefore.js"></script>
    <script src="public/assets/libs/dayjs/plugin/isoWeek.js"></script>
    <script src="public/assets/libs/dayjs/plugin/relativeTime.js"></script>
    <script src="public/assets/libs/dayjs/plugin/utc.js"></script>


    <!-- Apexchartjs -->
    <script src="public/assets/libs/apexcharts/apexcharts.min.js"></script>


    <link href="public/assets/css/cropper.min.css" rel="stylesheet" />


    <script src="public/assets/js/imask.js"></script>
    <script src="public/assets/js/leaflet.js"></script>
    <!-- Init js-->
    <script src="public/assets/js/pages/form-masks.init.js"></script>

    <script>
        const traducciones = <?= json_encode($traducciones) ?>;

    </script>
</head>