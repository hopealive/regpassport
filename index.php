<?php
/**
 * Description of index
 *
 * @author hopealive
 */
require("lib/Utils.php");
$utils = new Utils();
$viewParams = $utils->routing();
?>


<!DOCTYPE html>
<html lang="en">
    <?php include('view/head.php'); ?>
    <body>
        <?php include('view/navbar.php'); ?>
        <div class="container marketing">
            <?php include('view/testmodal.php'); ?>
            <?php include('view/queue.php'); ?>
            <?php include('view/mark.php'); ?>
            <?php include('view/signup.php'); ?>
            <?php include('view/contacts.php'); ?>
            <?php include('view/footer.php'); ?>
        </div>

        <!-- core JavaScript
        ================================================== -->

        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://getbootstrap.com/assets/js/vendor/popper.min.js"></script>
        <script src="https://getbootstrap.com/dist/js/bootstrap.min.js"></script>
        <script src="https://getbootstrap.com/assets/js/vendor/holder.min.js"></script>
        <script src="https://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>
        <script src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBY_9XnDXq5vlRkMlJNKQsUJOoyVxsZrj0&callback=initMap&language=uk&region=UK" async defer></script>

        <script src="js/jquery.cookie.js?<?php echo date("Ymdhis"); ?>"></script>
        <script src="js/custom.js?<?php echo date("Ymdhis"); ?>"></script>
    </body>
</html>


