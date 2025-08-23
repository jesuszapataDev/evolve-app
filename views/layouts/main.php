<!DOCTYPE html>
<html lang="en" data-topbar-color="dark">

<?php include __DIR__ . '/../partials/head.php'; ?>

<body>

    <!-- Begin page -->
    <div id="wrapper">


        <!-- ========== Menu ========== -->

        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        <!-- ========== Left menu End ========== -->





        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">

            <!-- ========== Topbar Start ========== -->
            <?php include __DIR__ . '/../partials/topbar.php' ?>
            <!-- ========== Topbar End ========== -->

            <div class="content">

                <!-- Start Content-->
                <?php include $content; ?>
                <!-- container -->

            </div> <!-- content -->

            <?php include __DIR__ . '/../partials/footer.php' ?>



        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- Theme Settings -->



</body>

</html>