<?php
ini_set('error_reporting', E_ALL);

/**
 * Description of index
 *
 * @author gregzorb
 */
require("lib/Utils.php");
$utils = new Utils();

if ($_POST) {

    switch ($_POST['action']) {
        case "signup":
            $signupErrorMessage = "";
            $utils              = new Utils();
            $row                = [
                'firstname' => $_POST['firstname'],
                'patronymic' => $_POST['patronymic'],
                'surname' => $_POST['surname'],
            ];
            $result             = $utils->validateNewUser($row);
            if ($result['status'] == 'ok' && !empty($result['data'])) {
                if (!$utils->add($result['data'])) {
                    $signupErrorMessage = "Помилка збереження користувача";
                }
            } elseif (isset($result['message'])) {
                $signupErrorMessage = $result['message'];
            } else {
                $signupErrorMessage = 'Невідома помилка';
            }

            break;
        case "mark":
            $markErrorMessage = '';

            $userId = (int) $_POST['user-id'];

            //check if marked today
            //check if marked for last 1 hour
            //add mark
            if (!$utils->mark($userId)) {
                $markErrorMessage = 'Помилка відмічення';
            }
            break;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
    <?php echo $utils->renderBlock("head"); ?>
    <body>
        <?php echo $utils->renderBlock("navbar"); ?>
        <div class="container marketing">
            <?php echo $utils->renderBlock('testmodal'); ?>
            <?php echo $utils->renderBlock('queue'); ?>
            <?php include('blocks/search.php'); ?>
            <?php include('blocks/signup.php'); ?>
            <?php echo $utils->renderBlock('contacts'); ?>
            <?php echo $utils->renderBlock('footer'); ?>
        </div>

        <!-- core JavaScript
        ================================================== -->

        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBY_9XnDXq5vlRkMlJNKQsUJOoyVxsZrj0&callback=initMap&language=uk&region=UK" async defer></script>

        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

        <script src="https://getbootstrap.com/assets/js/vendor/popper.min.js"></script>
        <script src="https://getbootstrap.com/dist/js/bootstrap.min.js"></script>
        <script src="https://getbootstrap.com/assets/js/vendor/holder.min.js"></script>
        <script src="https://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>
        <script src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>


        <script src="js/jquery.cookie.js?<?php echo date("Ymdhis"); ?>"></script>
        <script src="js/custom.js?<?php echo date("Ymdhis"); ?>"></script>
    </body>
</html>


