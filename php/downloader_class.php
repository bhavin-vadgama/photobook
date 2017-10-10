<?php

//require_once './facebook_class.php';
set_time_limit(0);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class Download
{
    protected $album_id; //must be in array format
    protected $user_id;
    protected $user_name;

    public function Download($album_id, $user_id, $user_name)
    {
        $this->album_id = $album_id;
        $this->user_id = $user_id;
        $this->user_name = $user_name;
    }

    public function setAlbumId($album_id)
    {
        $this->album_id = $album_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setUserName($user_name)
    {
        $this->user_name = $user_name;
    }

    public function getAlbumId()
    {
        return $this->album_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getUserName()
    {
        return $this->user_name;
    }

    public function getAlbum()
    {
        $albumIds = $this->album_id;
        $profileId = $this->user_name;
        $tmp_dir = __DIR__ . '../../user_data/';

        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir, 0777);
        }

        if (!is_dir($tmp_dir . $profileId)) {
            mkdir($tmp_dir . $profileId, 0777);
        }

        $zip = new \ZipArchive();
        $zipFile = $tmp_dir . $profileId . '.zip';
        if ($zip->open($zipFile, \ZipArchive::CREATE) !== true) {
            exit("cannot open <$zipFile>\n");
        }

        $tmp_dir .= $profileId . '/';

        $fb = new FacebookClass();
        $fb->initializeFBWithSession();

        foreach ($albumIds as $albumId) {

            $album = $fb->getData('/' . $albumId . '?fields=name,photo_count,photos.limit(1000){source}'); 

            if ($album['photo_count'] > 0) {
                $albumName = $album['name'];
                $albumName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $albumName);
                $albumName = mb_ereg_replace("([\.]{2,})", '', $albumName);

                $path = $tmp_dir . $albumName . '-' . $albumId . '/';
                if (!is_dir($path)) {
                    mkdir($path, 0777);
                }

                foreach ($album['photos']['data'] as $photo) {
                    $file = $photo['id'] . '.jpg';

                    copy($photo['source'], $path . $file);
                }

                $options = array('add_path' => $albumName . '-' . $albumId . '/', 'remove_all_path' => true);
                $zip->addGlob($path . '*.jpg', GLOB_BRACE, $options);
            }
        }

        $zip->close();
        $_SESSION['user']['zip'] = $zipFile;

        return $zipFile;
    }

    public function getZip($file)
    {
        $file_name = basename($file);

        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename=$file_name");
        header('Content-Length: ' . filesize($file));

        readfile($file);
        //unlink($file);
    }

    public function deleteUserData($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteUserData("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }
}
