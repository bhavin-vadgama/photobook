<?php

session_start();
set_time_limit(0);
require_once './facebook_class.php';
require_once './logger_class.php';

$fbClass = new FacebookClass();
$fbClass->initializeFB();
$fbClass->makeTokenLongLived();
$fbClass->setAccessTokenDefault();

if (!$fbClass->checkForPermissions()) {
    echo 'Please accept all the permissions to continue';
    exit;
}

$fbClass->setSessionParams();

$log = new Logger('../user_log.txt');
$log->setTimestamp("D M d 'y h.i A");
$log->putLog(" user_id=> ".$_SESSION['user']['id']." | user_name=> ".$_SESSION['user']['name']." | user_email=> ".$_SESSION['user']['email'].PHP_EOL);


header('location: ../gallery.php');
exit;
