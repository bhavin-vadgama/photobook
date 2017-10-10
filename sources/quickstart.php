
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once('vendor/google/apiclient/src/Google/Client.php');
require_once('vendor/google/apiclient-services/src/Google/Service/Drive.php');


//define('APPLICATION_NAME', 'Drive API PHP Quickstart');
//define('CREDENTIALS_PATH', '~/.credentials/drive-php-quickstart.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/drive-php-quickstart.json

/*
if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');c
}
*/
/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
    
  $client = new Google_Client();
    $client->setApplicationName("rtc_photobook");
    $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/projects/rtc_fb/quickstart.php');
  //$client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(array('https://www.googleapis.com/auth/drive'));
  //$client->setAuthConfig(CLIENT_SECRET_PATH);
    $client->setClientId('411340050204-g8f4jtq1c55baedf1kellnv02hbsrrm5.apps.googleusercontent.com');
    $client->setClientSecret('77QVYnkALL3JLoNZY5iFOZIv');
  $client->setAccessType('offline');
/*
  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = json_decode(file_get_contents($credentialsPath), true);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));
*/
    // Exchange authorization code for an access token.
    header('Location: ' . $client->createAuthUrl());
    $authCode = $_GET['code'];
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
/*
    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, json_encode($accessToken));
    printf("Credentials saved to %s\n", $credentialsPath);*/
  //}
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    //file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
  }
  return str_replace('~', realpath($homeDirectory), $path);
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Drive($client);

// Print the names and IDs for up to 10 files.
$optParams = array(
  'pageSize' => 10,
  'fields' => 'nextPageToken, files(id, name)'
);
$results = $service->files->listFiles($optParams);

if (count($results->getFiles()) == 0) {
  print "No files found.\n";
} else {
  echo "Files:\n";
  foreach ($results->getFiles() as $file) {
    echo $file->getName().'-'. $file->getId();
  }
}
