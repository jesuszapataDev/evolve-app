<!-- Footer Start -->
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div>
                    <script>document.write(new Date().getFullYear())</script> © Ubold - <a
                        href="https://coderthemes.com/" target="_blank">Coderthemes.com</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-none d-md-flex gap-4 align-item-center justify-content-md-end footer-links">
                    <a href="javascript: void(0);">About</a>
                    <a href="javascript: void(0);">Support</a>
                    <a href="javascript: void(0);">Contact Us</a>
                </div>
            </div>
        </div>
    </div>

</footer>
<!-- end Footer -->


<script>
    const idioma = '<?= strtoupper($_SESSION["idioma"] ?? "ES") ?>';

    const mensajes = {
        ES: {
            successTitle: '<?= $traducciones["successTitle_helper"] ?>',
            errorTitle: '<?= $traducciones["errorTitle_helper"] ?>',
            successText: '<?= $traducciones["successText_helper"] ?>',
            errorText: '<?= $traducciones["errorText_helper"] ?>',
            confirmButtonText: '<?= $traducciones["confirmButtonText_helper"] ?>',

            createConfirmTitle: '<?= $traducciones["createConfirmTitle_helper"] ?>',
            createConfirmText: '<?= $traducciones["createConfirmText_helper"] ?>',
            createConfirmButton: '<?= $traducciones["createConfirmButton_helper"] ?>',

            saveConfirmTitle: '<?= $traducciones["saveConfirmTitle_helper"] ?>',
            saveConfirmText: '<?= $traducciones["saveConfirmText_helper"] ?>',
            saveConfirmButton: '<?= $traducciones["saveConfirmButton_helper"] ?>',
            confirmButton: '<?= $traducciones["confirmButtonText_helper"] ?>',

            deleteConfirmTitle: '<?= $traducciones["deleteConfirmTitle_helper"] ?>',
            deleteConfirmText: '<?= $traducciones["deleteConfirmText_helper"] ?>',
            deleteConfirmButton: '<?= $traducciones["deleteConfirmButton_helper"] ?>',

            defaultConfirmTitle: '<?= $traducciones["defaultConfirmTitle_helper"] ?>',
            defaultConfirmText: '<?= $traducciones["defaultConfirmText_helper"] ?>',
            defaultConfirmButton: '<?= $traducciones["defaultConfirmButton_helper"] ?>',

            cancelButtonText: '<?= $traducciones["cancelButtonText_helper"] ?>',
            addPanel: '<?= $traducciones["addPanel"] ?>',
            editPanel: '<?= $traducciones["editPanel"] ?>',

            createdTitle: '<?= $traducciones["createdTitle_helper"] ?>',
            createdText: '<?= $traducciones["createdText_helper"] ?>',
            savedTitle: '<?= $traducciones["savedTitle_helper"] ?>',
            savedText: '<?= $traducciones["savedText_helper"] ?>',
            deletedTitle: '<?= $traducciones["deletedTitle_helper"] ?>',
            deletedText: '<?= $traducciones["deletedText_helper"] ?>',
            noRecordsTitle: '<?= $traducciones['no_records_title'] ?>',
            noRecordsText: '<?= $traducciones['no_records_text'] ?>',
            exportErrorTitle: '<?= $traducciones['export_error_title'] ?>',
            exportErrorText: '<?= $traducciones['export_error_text'] ?>',
            exportLoadingTitle: '<?= $traducciones['export_loading_title'] ?>',
            exportLoadingText: '<?= $traducciones['export_loading_text'] ?>',
            csvFilenamePrefix: '<?= $traducciones['csv_filename_prefix_panel'] ?>',
        },
        EN: {
            successTitle: '<?= $traducciones["successTitle_helper"] ?>',
            errorTitle: '<?= $traducciones["errorTitle_helper"] ?>',
            successText: '<?= $traducciones["successText_helper"] ?>',
            errorText: '<?= $traducciones["errorText_helper"] ?>',
            confirmButtonText: '<?= $traducciones["confirmButtonText_helper"] ?>',
            confirmButton: '<?= $traducciones["confirmButtonText_helper"] ?>',
            createConfirmTitle: '<?= $traducciones["createConfirmTitle_helper"] ?>',
            createConfirmText: '<?= $traducciones["createConfirmText_helper"] ?>',
            createConfirmButton: '<?= $traducciones["createConfirmButton_helper"] ?>',

            saveConfirmTitle: '<?= $traducciones["saveConfirmTitle_helper"] ?>',
            saveConfirmText: '<?= $traducciones["saveConfirmText_helper"] ?>',
            saveConfirmButton: '<?= $traducciones["saveConfirmButton_helper"] ?>',
            addPanel: '<?= $traducciones["addPanel"] ?>',
            editPanel: '<?= $traducciones["editPanel"] ?>',

            deleteConfirmTitle: '<?= $traducciones["deleteConfirmTitle_helper"] ?>',
            deleteConfirmText: '<?= $traducciones["deleteConfirmText_helper"] ?>',
            deleteConfirmButton: '<?= $traducciones["deleteConfirmButton_helper"] ?>',

            defaultConfirmTitle: '<?= $traducciones["defaultConfirmTitle_helper"] ?>',
            defaultConfirmText: '<?= $traducciones["defaultConfirmText_helper"] ?>',
            defaultConfirmButton: '<?= $traducciones["defaultConfirmButton_helper"] ?>',

            cancelButtonText: '<?= $traducciones["cancelButtonText_helper"] ?>',

            createdTitle: '<?= $traducciones["createdTitle_helper"] ?>',
            createdText: '<?= $traducciones["createdText_helper"] ?>',
            savedTitle: '<?= $traducciones["savedTitle_helper"] ?>',
            savedText: '<?= $traducciones["savedText_helper"] ?>',
            deletedTitle: '<?= $traducciones["deletedTitle_helper"] ?>',
            deletedText: '<?= $traducciones["deletedText_helper"] ?>',
            noRecordsTitle: '<?= $traducciones['no_records_title'] ?>',
            noRecordsText: '<?= $traducciones['no_records_text'] ?>',
            exportErrorTitle: '<?= $traducciones['export_error_title'] ?>',
            exportErrorText: '<?= $traducciones['export_error_text'] ?>',
            exportLoadingTitle: '<?= $traducciones['export_loading_title'] ?>',
            exportLoadingText: '<?= $traducciones['export_loading_text'] ?>',
            csvFilenamePrefix: '<?= $traducciones['csv_filename_prefix_panel'] ?>',
        }
    };

    const t = mensajes[idioma];


</script>


<!-- Vendor js -->

<script src="public/assets/js/vendor.min.js"></script>
<script src="public/assets/js/app.min.js"></script>




<!-- TOOLTIPS -->
<script src="public/assets/js/poppers.js"></script>
<script src="public/assets/js/tippy.js"></script>

<!-- Plugins js-->
<script src="public/assets/libs/c3/c3.min.js"></script>
<script src="public/assets/libs/d3/d3.min.js"></script>

<script src="public/assets/libs/selectize/js/standalone/selectize.min.js"></script>

<!-- Dashboar 1 init js-->
<!-- <script src="public/assets/js/pages/dashboard-1.init.js"></script>-->
<script src="public/assets/libs/select2/js/select2.min.js"></script>

<!-- FOOTER IMPORTS -->

<script src="public/assets/libs/raphael/raphael.min.js"></script>
<script src="public/assets/libs/jquery-mask-plugin/jquery.mask.min.js"></script>
<script src="public/assets/libs/autonumeric/autoNumeric.min.js"></script>

<!-- Sweet Alerts -->
<script src="public/assets/libs/sweetalert2/sweetalert2.all.min.js"></script>

<!-- Init js -->
<script src="public/assets/js/pages/form-masks.init.js"></script>

<!-- Bootstrap Tables js -->
<script src="public/assets/libs/bootstrap-table/bootstrap-table.min.js"></script>

<script src="public/assets/js/pages/bootstrap-tables.init.js"></script>
<script src="public/assets/js/leaflet.js"></script>
<!-- App js -->
<script type="module" src="public/assets/js/app.js"></script>

<script src="public/assets/js/pages/bootstrap-tables.init.js"></script>
<script src="public/assets/js/cropper.min.js"></script>
<!-- Carga de jsPDF -->
<script src="public/assets/js/jspdf.umd.min.js"></script>
<script src="public/assets/js/bootstrap-table-es-ES.min.js"></script>

<!-- App js -->