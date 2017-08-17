<?php
require("lib/Utils.php");

$utils = new Utils();

if (!isset($_GET['action'])) {
    echo json_encode([]);
}
switch ($_GET['action']) {
    case 'searchForMarking':
        echo $utils->searchForMarking();
        break;
    case 'list':
    default:
        echo $utils->getList();
        break;
}
?>
