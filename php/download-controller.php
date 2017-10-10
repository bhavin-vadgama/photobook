<?php

set_time_limit(0);

require_once './facebook_class.php';
require_once './downloader_class.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['fb_access_token'])) {
    $fb = new FacebookClass();
    $fb->initializeFBWithSession();

    if (isset($_POST['get-zip'])) {
        $data = $_POST['get-zip'];
        $download = new Download($data, $_SESSION['user']['id'], $_SESSION['user']['name']);
        $response = $download->getAlbum(); //.'?fields=name,photo_count,photos{source}'
        $rs = array('response' => 'OK', 'zip' => "$response");
        header('Content-type: application/json');
        echo json_encode($rs);
    }
} else {
    echo 'Session expired! Please relogin to continue...';
    header('location: ../');
}
