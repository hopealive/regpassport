<?php
/*
 * @hopealive
 * Shell, which disactivate bad users
 */

include("lib/Utils.php");
$utils = new Utils();

$users = $utils->getActiveUsersRegisteredDaysAgo();
if (empty($users)) {
    exit;
}

$userIds = array_column($users, 'u__id');
if (empty($userIds)) {
    exit;
}

$usersWithMarks = $utils->getLastMarksByUsers($userIds);
if ( empty($usersWithMarks) ){
    $userIdsWithMarks = [];
} else {
    $userIdsWithMarks = array_column($usersWithMarks, 'm__user_id');
}

$userIdsToDisactivate = array_diff($userIds, $userIdsWithMarks);
$utils->disactivateUsersByIds($userIdsToDisactivate);

