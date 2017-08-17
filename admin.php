<?php
//check client info
if (
    md5($_SERVER['HTTP_USER_AGENT']) != "93e8f6a8d4df3cb6af9902e296d15bc5"
){
//    echo "No auth"; exit;
}

require("lib/Utils.php");
$utils = new Utils();
?>

<!DOCTYPE html>
<html lang="en">
    <?php include('view/head.php'); ?>
    <body>
        <?php include('view/navbar.php'); ?>
        <div class="container marketing">
            <?php include('view/queue.php'); ?>
            <?php include('view/footer.php'); ?>
        </div>
    </body>
</html>