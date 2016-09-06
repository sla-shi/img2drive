<?php

require_once __DIR__ . '/vendor/autoload.php';

define('APPLICATION_NAME', 'Img2Drive');
define('CREDENTIALS_PATH', __DIR__ . '/.credentials/drive-credentials.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client-secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/drive-php-quickstart.json
define('SCOPES', implode(' ', array(Google_Service_Drive::DRIVE_FILE) ));  //DRIVE_METADATA_READONLY


if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  //$client->addScope("https://www.googleapis.com/auth/drive");
  $client->setAuthConfig(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');
  $client->setRedirectUri('http://localhost/oauth2callback');

  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = json_decode(file_get_contents($credentialsPath), true);
  } else {
    // Request authorization from the user.
    echo CLIENT_SECRET_PATH;
    $authUrl = $client->createAuthUrl();
    echo 'created auth';
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));

    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, json_encode($accessToken));
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
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

  
// Create the image file
$file = new Google_Service_Drive_DriveFile();
$file->setName('1.jpg');
$file->setDescription('A test image file');
$file->setMimeType('image/jpeg');

$data = file_get_contents('images/1.jpg');

// TODO: Check if the file exists and replace it

$createdFile = $service->files->create($file, array(
      'data' => $data,
      'mimeType' => 'image/jpeg',
      'uploadType' => 'multipart'
    ));

// TODO: Get the URL of the stored file

//echo $file->getDownloadUrl();
print_r($createdFile);
echo '-----';
print_r($file);

// Print the names and IDs for up to 10 files.
$optParams = array(
  'pageSize' => 10,
  'fields' => 'nextPageToken, files(id, name)'
);
$results = $service->files->listFiles($optParams);

if (count($results->getFiles()) == 0) {
  print "No files found.\n";
} else {
  print "Files:\n";
  foreach ($results->getFiles() as $file) {
    printf("%s (%s)\n", $file->getName(), $file->downloadUrl);
  }
}
