<?php

ini_set('max_execution_time', 0);
ini_set('request_terminate_timeout', 0);
require_once './google_drive_api_class.php';
require_once './facebook_class.php';

if (isset($_POST['move'])) {
    uploadAlbums($_POST['move']);
}

function uploadAlbums($albumIds)
{
    $google = new GoogleDriveAPIClass();

    $google->initializeGClient();
    $google->setAccessToken($_SESSION['google_drive_access_token']);

    $user_name = strtolower($_SESSION['user']['name']);
    $user_name = str_replace(' ', '_', $user_name);
    $folderName = 'facebook_' . $user_name . '_albums';
    
    if ($google->listFilesFolders($folderName, 'root', 'folders') == false) {
        $albumsFolder = $google->createFolder('root', $folderName);
    } else {
        $albumsFolder = array_flip($google->listFilesFolders($folderName, 'root', 'folders'))[$folderName];
    }

    $path = '../user_data/';

    if (!is_dir($path)) {
        mkdir($path, 0777);
    }

    $fb = new FacebookClass();
    $fb->initializeFBWithSession();

    foreach ($albumIds as $albumId) {
        $album = $fb->getData($albumId . '?fields=name,photo_count,photos.limit(1000){images}');
        if ($album['photo_count'] > 0) {
            $albumName = $album['name'];
            $albumName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $albumName);
            $albumName = mb_ereg_replace("([\.]{2,})", '', $albumName);

            
            if ($google->listFilesFolders($albumName, $albumsFolder, 'folders') != false) {
                $albumFolder = array_flip($google->listFilesFolders($albumName, $albumsFolder, 'folders'))[$albumName];

                $photoList = $google->listFilesFolders('', $albumFolder, 'files');

                foreach ($album['photos'] as $photo) {
                    $file = $photo['id'] . '.jpg';
                    if (!in_array($file, $photoList)) {
                        copy($photo['images'][0]['source'], $path . $file);
                        $google->uploadFile($albumFolder, $path . $file, $file);
                        unlink($path . $file);
                    }
                }
            } else {
                $albumFolder = $google->createFolder($albumsFolder, $albumName);
                foreach ($album['photos'] as $photo) {
                    $file = $photo['id'] . '.jpg';
                    copy($photo['images'][0]['source'], $path . $file);
                    $google->uploadFile($albumFolder, $path . $file, $file);
                    unlink($path . $file);
                }
            }
        }
    }
}
