<?php

// Enabling error reporting
if ($settings['debug']) {
  error_reporting(E_ALL);
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 1);
}

$apiUser = 2394483784;
$apiKey = 9930311315510172050982376;
$convert = new MsWordToImageConvert($apiUser, $apiKey);


$data = array();

$data['description'] = '';
if (strlen($settings['description']) > 0)
  $data['description'] = $settings['description'] . '<br><br>';

// Adding current script name to ignore list
$data['ignores'] = $settings['ignores'];
$data['ignores'][] = basename('index.php');

// Use canonized path
$data['uploaddir'] = realpath($settings['base_path']);

// Is the directory there?
if (!is_dir($data['uploaddir'])) {
  // Not found
  die(sprintf('[%s:%d]: Upload path "%s" is not a directory.', pathinfo(__FILE__, PATHINFO_BASENAME), __LINE__, $data['uploaddir']));
} elseif (!is_readable($data['uploaddir'])) {
  // Not readable
  die(sprintf('[%s:%d]: Upload directory "%s" is not readable.', pathinfo(__FILE__, PATHINFO_BASENAME), __LINE__, $data['uploaddir']));
} elseif (!is_writable($data['uploaddir'])) {
  // Not writable
  die(sprintf('[%s:%d]: Upload directory "%s" is not writable.', pathinfo(__FILE__, PATHINFO_BASENAME), __LINE__, $data['uploaddir']));
}

// Detect maximum upload size, allowed by server
$data['max_upload_size'] = ini_get('upload_max_filesize');

// If debug is enabled, logging all variables
if ($settings['debug']) {
  // Displaying debug information
  echo '<h2>Settings:</h2>';
  echo '<pre>' . print_r($settings, true) . '</pre>';

  // Displaying debug information
  echo '<h2>Data:</h2>';
  echo '<pre>' . print_r($data, true) .  '</pre>';

}
?>
