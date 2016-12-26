<?php
// Format file size
function formatSize ($bytes) {
  $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

  $bytes = max($bytes, 0);
  $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
  $pow = min($pow, count($units) - 1);

  $bytes /= pow(1024, $pow);

  return ceil($bytes) . ' ' . $units[$pow];
}

// Rotate a two-dimensional array. Used for file uploads
function diverseArray ($vector) {
  $result = array();
  foreach ($vector as $key1 => $value1)
    foreach ($value1 as $key2 => $value2)
      $result[$key2][$key1] = $value2;
  return $result;
}

function resize($newWidth, $targetFile, $originalFile) {

    $info = getimagesize($originalFile);
    $mime = $info['mime'];

    switch ($mime) {
          case 'image/png':
                  $image_create_func = 'imagecreatefrompng';
                  $image_save_func = 'imagepng';
                  $new_image_ext = 'png';
                  break;
            case 'image/jpeg':
                    $image_create_func = 'imagecreatefromjpeg';
                    $image_save_func = 'imagejpeg';
                    $new_image_ext = 'jpg';
                    break;

            case 'image/gif':
                    $image_create_func = 'imagecreatefromgif';
                    $image_save_func = 'imagegif';
                    $new_image_ext = 'gif';
                    break;

            default:
                    throw new Exception('Unknown image type.');
    }

    $img = $image_create_func($originalFile);
    list($width, $height) = getimagesize($originalFile);

    $newHeight = ($height / $width) * $newWidth;
    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    if (file_exists($targetFile)) {
            unlink($targetFile);
    }
    $image_save_func($tmp, "$targetFile");
}

// Handling file upload
function uploadFile ($file_data,$target_file) {
  global $settings, $data, $convert;

  $file_data['uploaded_file_name'] = basename($file_data['name']);
  $file_data['target_file_name'] = $target_file;
  $file_data['upload_target_file'] = $data['uploaddir'] . DIRECTORY_SEPARATOR . $file_data['target_file_name'];

  // Do now allow to overwriting files
  if (isReadableFile($file_data['upload_target_file'])) {
    echo 'File name already exists' . "\n";
    return false;
  }

  // Moving uploaded file OK
  if (move_uploaded_file($file_data['tmp_name'], $file_data['upload_target_file'])) {
    $convert->fromFile($file_data['upload_target_file']);
    $toPageCount = $convert->toPageCount();
    for($i=0; $i<$toPageCount; $i++){
      $base64String = $convert->toBase46EncodedString(\MsWordToImageConvert\OutputImageFormat::JPEG,$i);

      $info = pathinfo($file_data['target_file_name']);
      $file_name =  basename($file_data['target_file_name'],'.'.$info['extension']);

      file_put_contents('./'.$file_name."(".($i+1).").png",base64_decode($base64String));
      resize(610,'./'.$file_name.'('.($i+1).').png','./'.$file_name.'('.($i+1).').png');
    }
    // Please make sure input file is readable by your PHP process.
  } else {
    echo 'Error: unable to upload the file.';
  }
}

// Delete file
function deleteFile ($file) {
  global $data;

  if (in_array(substr($file, 1))) {
    $fqfn = $data['uploaddir'] . DIRECTORY_SEPARATOR . $file;
    if (!in_array($file, $data['ignores']) && isReadableFile($fqfn)) {
      unlink($fqfn);

    }
  }
}

// Mark/unmark file as hidden
function markUnmarkHidden ($file) {
  global $data;

  if (in_array(substr($file, 1))) {
    $fqfn = $data['uploaddir'] . DIRECTORY_SEPARATOR . $file;
    if (!in_array($file, $data['ignores']) && isReadableFile($fqfn)) {
      if (substr($file, 0, 1) === '.') {
        rename($fqfn, substr($fqfn, 1));

      } else {
        rename($fqfn, $data['uploaddir'] . DIRECTORY_SEPARATOR . '.' . $file);

      }

    }
  }
}

// Checks if the given file is a file and is readable
function isReadableFile ($file) {
  return (is_file($file) && is_readable($file));
}

// List files in a given directory, excluding certain files
function createArrayFromPath ($dir) {
  global $data;

  // Empty paths are not accepted
  if (empty($dir)) {
    die(sprintf('[%s:%d]: R.I.P.: Parameter "dir" cannot be empty.', __FUNCTION__, __LINE__));
  } // END - if

  $file_array = array();

  $dh = opendir($dir) or die(sprintf('[%s:%d]: R.I.P.: Cannot read directory "%s".', __FUNCTION__, __LINE__, $dir));

  while ($filename = readdir($dh)) {
    $fqfn = $dir . DIRECTORY_SEPARATOR . $filename;
    if (!is_dir($fqfn) && !in_array($filename, $data['ignores'])) {
      $file_array[] = $filename;
    }
  } //END - while

  ksort($file_array);

  $file_array = array_reverse($file_array, true);

  return $file_array;
}

// Removes old files
function removeOldFiles ($file,$dir) {
    unlink ($dir . DIRECTORY_SEPARATOR . $file);
}

// Detects base URL
function autoDetectBaseUrl () {
  // Detect protocol
  $protocol = 'http';
  if (
    ((isset($_SERVER['HTTPS'])) && (strtolower($_SERVER['HTTPS']) == 'on')) ||
    ((isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https'))
  ) $protocol = 'https';

  // Detect port
  $port = getenv('SERVER_PORT');
  if (
    (($port == 80) && ($protocol == 'http')) ||
    (($port == 443) && ($protocol == 'https'))
  ) $port = '';

  // Detect server name
  $server_name = getenv('SERVER_NAME');
  if ($server_name === false) $server_name = 'localhost';

  // Construct base URL
  $base_url = sprintf(
    '%s://%s%s%s',
    $protocol,
    $server_name,
    $port,
    dirname(getenv('SCRIPT_NAME'))
  );

  return $base_url;
}
?>
