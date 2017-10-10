<?php

require_once __DIR__ . '../../vendor/autoload.php';
require_once '../vendor/google/apiclient/src/Google/Client.php';
require_once '../vendor/google/apiclient-services/src/Google/Service/Drive.php';

set_time_limit(0);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class GoogleDriveAPIClass
{
    protected $gclient;
    protected $service;
    protected $clientId;
    protected $clientSecret;
    protected $scope;
    protected $redirectUri;
    protected $authCode;
    protected $accessToken;

    public function GoogleDriveAPIClass(
    $clientID = '<YOUR GOOGLE API APP ID>',
        $clientSecret = '<YOUR GOOGLE API APP SECRET>',
        $scope = array('https://www.googleapis.com/auth/drive')
    ) {
        $this->clientId = $clientID;
        $this->clientSecret = $clientSecret;
        $this->scope = $scope;
        $this->redirectUri = 'http://' . $_SERVER['HTTP_HOST'] . '/projects/rtc_fb/php/gdrive-controller.php';
    }

    public function initializeGClient()
    {
        $this->gclient = new Google_Client();
        $this->gclient->setApplicationName('<NAME OF YOUR APP>');
        $this->gclient->setRedirectUri($this->redirectUri);
        $this->gclient->setScopes($this->scope);
        $this->gclient->setClientId($this->clientId);
        $this->gclient->setClientSecret($this->clientSecret);
        $this->gclient->setAccessType('offline');
    }

    public function initializeGSerciveDrive()
    {
        $this->service = new Google_Service_Drive($this->gclient);
    }

    public function getServiceDrive()
    {
        return $this->service;
    }

    public function getAuthUrl()
    {
        return $this->gclient->createAuthUrl();
    }

    public function authenticate($authCode)
    {
        $this->authCode = $authCode;
        $this->accessToken = $this->gclient->authenticate($this->authCode);
    }

    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;
        $this->accessToken = $this->gclient->fetchAccessTokenWithAuthCode($this->authCode);
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken)
    {
        $this->gclient->setAccessToken($accessToken);
    }

    public function getRefreshToken()
    {
        if ($this->gclient->isAccessTokenExpired()) {
            $this->gclient->fetchAccessTokenWithRefreshToken($this->gclient->getRefreshToken());
        }
    }

    public function setSessionParams()
    {
        $_SESSION['google_drive_access_token'] = $this->accessToken;
    }

    public function createFolder($parent, $name)
    {
        $fileMetadata = new \Google_Service_Drive_DriveFile(array(
            'name' => $name,
            'parents' => array($parent),
            'mimeType' => 'application/vnd.google-apps.folder',));
        $id = $this->service->files->create($fileMetadata, array('fields' => 'id'));

        return $id->id;
    }

    public function listFilesFolders($search, $parentId, $type = 'all')
    {
        $query = '';
        $condition = $search != '' ? '=' : 'contains';

        $query .= $parentId != 'all' ? "'" . $parentId . "' in parents" : '';

        switch ($type) {
            case 'files':
                $query .= $query != '' ? ' and ' : '';
                $query .= "mimeType != 'application/vnd.google-apps.folder' 
                            and name " . $condition . " '" . $search . "'";
                break;

            case 'folders':
                $query .= $query != '' ? ' and ' : '';
                $query .= "mimeType = 'application/vnd.google-apps.folder' and name contains '" . $search . "'";
                break;
            default:
                $query .= '';
                break;
        }

        $query .= $query != '' ? ' and trashed = false' : 'trashed = false';
        $optParams = array('q' => $query, 'pageSize' => 1000);

        $this->initializeGSerciveDrive();
        $service = $this->getServiceDrive();
        $results = $this->service->files->listFiles($optParams);

        if (count($results->getFiles()) == 0) {
            return array();
        }

        $result = array();
        foreach ($results->getFiles() as $file) {
            $result[$file->getId()] = $file->getName();
        }

        return $result;
    }

    public function uploadFile($parentId, $filePath, $fileName = 'none')
    {
        if ($fileName == 'none') {
            $fileName = end(explode('/', $filePath));
        }

        $fileMetadata = new \Google_Service_Drive_DriveFile(array(
            'name' => $fileName,
            'parents' => array($parentId),
        ));

        $content = file_get_contents($filePath);

        $file = $this->service->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => 'image/jpeg',
            'uploadType' => 'multipart',
            'fields' => 'id',));

        return $file->id;
    }
}
