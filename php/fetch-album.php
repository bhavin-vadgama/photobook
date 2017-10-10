<?php

require_once '../vendor/autoload.php';
require_once './facebook_class.php';
set_time_limit(0);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['fb_access_token'])) {
    $fb = new FacebookClass();
    $fb->initializeFBWithSession();

    if (isset($_POST['fetch-album'])) {
        $data = json_decode($_POST['fetch-album']);
        $response = $fb->getData($data->id . '?fields=name,photo_count,photos.limit(1000){source}');
        echo json_encode($response);
    }
} else {
    echo 'Session expired! Please relogin to continue...';
    header('location: ../');
}
