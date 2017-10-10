<?php

require_once './downloader_class.php';

$dd = new Download(null, null, null);

$dd->deleteUserData('../user_data/');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

session_unset();
session_destroy();
header('location: ../gallery.php');
