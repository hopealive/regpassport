<?php
//check client info
if (
    md5($_SERVER['HTTP_USER_AGENT']) != "93e8f6a8d4df3cb6af9902e296d15bc5"
){
    echo "No auth";
//    exit;
}

require("lib/Utils.php");
$utils = new Utils();
?>

<!DOCTYPE html>
<html lang="en">
    <?php echo $utils->renderBlock("head"); ?>
    <body>
        <?php echo $utils->renderBlock("navbar"); ?>
        <div class="container marketing">
            <?php echo $utils->renderBlock('queue'); ?>
            <?php echo $utils->renderBlock('footer'); ?>
        </div>


    </body>
</html>