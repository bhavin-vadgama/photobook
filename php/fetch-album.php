<?php

ini_set('max_execution_time', 0);
ini_set('request_terminate_timeout', 0);
require_once '../vendor/autoload.php';
require_once './facebook_class.php';


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
