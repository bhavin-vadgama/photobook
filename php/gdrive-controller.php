<?php

require_once './google_drive_api_class.php';

set_time_limit(0);

$client = new GoogleDriveAPIClass();
$client->initializeGClient();

if (isset($_GET['code'])) {
    try {
        $client->authenticate($_GET['code']);
        $client->setSessionParams();
        header('Location: ../gallery.php'); //return to work
    } catch (Exception $e) {
        echo $e;
        exit;
    }
} else {
    if (isset($_SESSION['google_drive_access_token'])) {
        try {
            $rs = array('response' => 'OK');
            header('Content-type: application/json');
            echo json_encode($rs);
        } catch (Exception $e) {
            echo $e;
            exit;
        }
    } else {
        try {
            $authUrl = $client->getAuthUrl();
            $rs = array('response' => 'LINK', 'url' => $authUrl);
            header('Content-type: application/json');
            echo json_encode($rs);
        } catch (Exception $e) {
            echo $e;
            exit;
        }
    }
}
