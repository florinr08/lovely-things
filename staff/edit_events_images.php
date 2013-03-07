<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/scripts/initialisation.php');
$privileges = PERMISSIONS_EDIT_EVENTS; // the privileges needed to see the site - refer to /secure/account_types.txt
beforeFileLoad();
?>
<?php
include_once(HOME_DIR . 'scripts/headTagsSnippet.php');
echo showDOCTYPE();
echo headTagsSnippet('ThePo!ntC.O.C. - Edit Events Images', false);
?>
  <link rel="stylesheet" href="<?php echo HOME_DIR; ?>css/edit_events.css" />
  <script src="<?php echo HOME_DIR; ?>scripts/edit_events.js"></script>
</head>
<!-- HEADTAGS -->
<body>

<!-- HEADER -->
<?php
include_once(HOME_DIR . 'scripts/headerSnippet.php');
echo headerSnippet();
?>
<!-- HEADER -->

<!-- CONTENT -->
<div id="content">
<div id="contentHolder" class="dotted">
<?php
include_once(HOME_DIR . 'scripts/navigationSnippet.php');
echo navigationSnippet();
?>
<div class="dottedBreak dotted"></div>
<h1>Edit Events Images</h1>
<div class="dottedBreak dotted"></div>
<?php
$events_dir = 'images/events/';

function genRandomString($length = 8) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
  $string = '';
  for ($i = 0; $i < $length; $i++) {
    $string .= $characters[mt_rand(0, strlen($characters) - 1)];
  }
  return $string;
}

function showImages() {
  global $mysqli, $events_dir;
  $return_value = false;
  if (@$result = $mysqli->query('SELECT id, file_extension, title FROM `events_images` ORDER BY datetime DESC')) {
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?add" class="custom_btn">Add image</a>', PHP_EOL;
    echo '<div id="events_images_container">', PHP_EOL;
    while (list($id, $file_extension, $title) = $result->fetch_row()) {
      echo '  <a href="', basename($_SERVER['PHP_SELF']), '?uri=', $id, '">';
      echo '<div style="background-image: url(\'/'.$events_dir, $id, '.', $file_extension, '\');" title="', $title, '"></div>';
      echo '</a>', PHP_EOL;
    }
    echo '</div>', PHP_EOL;
    $result->close();
    $return_value = true;
  }
  else {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Failed to retrieve list of images and could not connect to the database.', PHP_EOL;
    echo '</div>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?add" class="custom_btn">Add image</a>', PHP_EOL;
  }
  return $return_value;
}

function showImage($uri) {
  global $mysqli, $events_dir;
  echo '<a href="', basename($_SERVER['PHP_SELF']), '" class="custom_btn">Back</a>', PHP_EOL;
  if (@$stmt = $mysqli->prepare('SELECT id, file_extension, title, description FROM `events_images` WHERE id = ? LIMIT 1')) {
    $stmt->bind_param('s', $uri);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows) {
      $stmt->bind_result($id, $file_extension, $title, $description);
    }
    else {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  Failed to retrieve image.', PHP_EOL;
      echo '</div>', PHP_EOL;
      return false;
    }
    $stmt->fetch();
    $stmt->close();
    
    $filename = '/' . $events_dir . $id . '.' . $file_extension;
    echo '<div id="events_image_container">', PHP_EOL;
    echo '  <img src="', $filename, '" alt="', $title, '" title="', $title, '" />', PHP_EOL;
    echo '</div>', PHP_EOL;
    echo '<div id="events_image_descriptor">', PHP_EOL;
    echo '  <label for="events_image_uri">Copy URI:</label>', PHP_EOL;
    echo '  <input type="text" id="events_image_uri" value="', $filename, '" readonly="readonly" />', PHP_EOL;
    echo '  <p></p>', PHP_EOL;
    echo '  <label>Title:</label>', PHP_EOL;
    echo '  <input type="text" value="', $title, '" readonly="readonly" />', PHP_EOL;
    echo '  <p></p>', PHP_EOL;
    echo '  <label>Description:</label>', PHP_EOL;
    echo '  <textarea rows="2" cols="30" readonly="readonly">', PHP_EOL, $description, '</textarea>', PHP_EOL;
    echo '</div>', PHP_EOL;
    // echo '<a href="', basename($_SERVER['PHP_SELF']), '?edit=', $id, '" class="custom_btn">Edit</a>', PHP_EOL;
    echo '<div class="custom_btn disabled">Edit</div>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?delete=', $id, '" class="custom_btn delete">Delete</a>', PHP_EOL;
  }
  else {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Failed to retrieve image and could not connect to the database.', PHP_EOL;
    echo '</div>', PHP_EOL;
    return false;
  }
  return true;
}

function deleteImage($uri) {
  global $mysqli, $events_dir;
  if (@$stmt = $mysqli->prepare('SELECT id, file_extension FROM `events_images` WHERE id=? LIMIT 1')) {
    $stmt->bind_param('s', $uri);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $file_extension);
    $stmt->fetch();
    $stmt->close();
  }
  if (is_null($id) && is_null($file_extension)) {
    $id = '123';
    $file_extension = 'abc';
  }
  $filename = '../' . $events_dir . $id . '.' . $file_extension;
  
  if (isset($_GET['confirm'])) {
    if ($_GET['confirm']) {
      $failed_to_delete_image_file = false;
      $failed_to_delete_db_entry = false;
      if (file_exists($filename)) {
        if (!unlink($filename)) {
          $failed_to_delete_image_file = true;
        }
      }
      else {
        $failed_to_delete_image_file = true;
      }
      
      if (@$stmt = $mysqli->prepare('DELETE FROM `events_images` WHERE id=? LIMIT 1')) {
        $stmt->bind_param('s', $uri);
        $stmt->execute();
        if (!$stmt->affected_rows) {
          $failed_to_delete_db_entry = true;
        }
        $stmt->close();
      }
      else {
        $failed_to_delete_db_entry = true;
      }
      
      if ($failed_to_delete_image_file && $failed_to_delete_db_entry) {
        echo '<div class="feedback_msg failed">', PHP_EOL;
        echo '  Failed to delete image...', PHP_EOL;
        echo '</div>', PHP_EOL;
      }
      elseif (!$failed_to_delete_image_file && $failed_to_delete_db_entry) {
        echo '<div class="feedback_msg failed">', PHP_EOL;
        echo '  Successfully deleted image file but database entry remains! Please let the administrator know!', PHP_EOL;
        echo '</div>', PHP_EOL;
      }
      elseif ($failed_to_delete_image_file && !$failed_to_delete_db_entry) {
        echo '<div class="feedback_msg failed">', PHP_EOL;
        echo '  Successfully deleted database entry but image file remains! Please let the administrator know!', PHP_EOL;
        echo '</div>', PHP_EOL;
      }
      else {
        echo '<div class="feedback_msg success">', PHP_EOL;
        echo '  Successfully deleted image!', PHP_EOL;
        echo '</div>', PHP_EOL;
      }
    }
    else {
      echo '<div class="feedback_msg success">', PHP_EOL;
      echo '  Image was left unchanged.', PHP_EOL;
      echo '</div>', PHP_EOL;
    }
  }
  else {
    echo '<p>Are you sure you want to delete this image?</p>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?delete=', $_GET['delete'], '&confirm=1" class="custom_btn delete">Confirm delete</a>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?delete=', $_GET['delete'], '&confirm=0" class="custom_btn">Leave untouched</a>', PHP_EOL;
    return false;
  }
  return true;
}

function addImage($forceShowForm = false) {
  global $events_dir, $mysqli;
  if (isset($_FILES['events_image_bin']) && !$forceShowForm) {
    while (true) {
      $id = genRandomString();
      if (@$result = $mysqli->query('SELECT COUNT(*) FROM `events_images` WHERE id=\''.$id.'\'')) {
        list($num_items) = $result->fetch_row();
        $result->close();
        if ($num_items == '0') {
          break;
        }
      }
    }
    $file_extension = pathinfo($_FILES['events_image_bin']['name'], PATHINFO_EXTENSION);
    $_POST['title'] = str_replace('&', '&amp;', $_POST['title']);
    $_POST['description'] = str_replace('&', '&amp;', $_POST['description']);
    $title = $_POST['title'];
    $description = $_POST['description'];
    $imagefilename = '../' . $events_dir . $id . '.' . $file_extension;
    
    if ($_FILES['events_image_bin']['error'] == UPLOAD_ERR_NO_FILE) {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  No file was selected to upload. Please try again.', PHP_EOL;
      echo '</div>', PHP_EOL;
      return false;
    }
    
    if ($_FILES['events_image_bin']['error'] == UPLOAD_ERR_INI_SIZE) {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  The image file was too big. Please make sure the image is less than 2MB in size.', PHP_EOL;
      echo '</div>', PHP_EOL;
      return false;
    }
    
    if ($_FILES['events_image_bin']['error'] > 0) {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  An unspecified error occurred. If reporting this problem, please mention the error code ', $_FILES['events_image_bin']['error'], '.', PHP_EOL;
      echo '</div>', PHP_EOL;
      return false;
    }
    
    $imagetype = getimagesize($_FILES['events_image_bin']['tmp_name']);
    $imagetype = $imagetype[2];
    if (($imagetype != IMAGETYPE_JPEG) && ($imagetype != IMAGETYPE_PNG)) {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  The image type was incorrect. Please upload a JPEG or PNG file.', PHP_EOL;
      echo '</div>', PHP_EOL;
      return false;
    }
    
    if(move_uploaded_file($_FILES['events_image_bin']['tmp_name'], $imagefilename)) {
      
      // Get new sizes
      list($width, $height) = getimagesize($imagefilename);
      $width_n = 500;
      $height_n = ($height / $width) * $width_n;
      
      if ($width > $width_n) {
        // Resample
        $image_n = imagecreatetruecolor($width_n, $height_n);
        if ($imagetype == IMAGETYPE_JPEG) {
          $image = imagecreatefromjpeg($imagefilename);
        }
        elseif ($imagetype == IMAGETYPE_PNG) {
          $image = imagecreatefrompng($imagefilename);
        }
        imageinterlace($image, true);
        imagecopyresampled($image_n, $image, 0, 0, 0, 0, $width_n, $height_n, $width, $height);
        
        // Output
        if ($imagetype == IMAGETYPE_JPEG) {
          imagejpeg($image_n, $imagefilename, 100);
        }
        elseif ($imagetype == IMAGETYPE_PNG) {
          imagepng($image_n, $imagefilename, 9);
        }
        
        // Free up memory
        imagedestroy($image_n);
        imagedestroy($image);
      }
      
      if (@$stmt = $mysqli->prepare('INSERT INTO `events_images` (id, file_extension, title, description) VALUES(?, ?, ?, ?)')) {
        $stmt->bind_param('ssss', $id, $file_extension, $title, $description);
        $stmt->execute();
        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        if ($affected_rows) {
          echo '<div class="feedback_msg success">', PHP_EOL;
          echo '  The file image has been uploaded successfully.', PHP_EOL;
          echo '</div>', PHP_EOL;
          return true;
        }
      }
      else {
        if (file_exists($imagefilename)) {
          unlink($imagefilename);
        }
        echo '<div class="feedback_msg failed">', PHP_EOL;
        echo '  The image file could not be linked to the database.', PHP_EOL;
        echo '</div>', PHP_EOL;
        return false;
      }
    }
    else {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  The image file could not be uploaded.', PHP_EOL;
      echo '</div>', PHP_EOL;
      return false;
    }
  }
  else {
    echo '<a href="', basename($_SERVER['PHP_SELF']), '" class="custom_btn">Back</a>', PHP_EOL;
    echo '<form enctype="multipart/form-data" accept="image/jpeg,image/png" action="', basename($_SERVER['PHP_SELF']), '?add" method="post" class="item_form">', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>Upload image:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    <input type="file" name="events_image_bin" id="events_image_bin" size="40" />', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>Title:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    <input type="text" name="title" value="" />', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>Description / Keywords:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    <textarea rows="2" cols="30" name="description">', PHP_EOL, '</textarea>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="breakBoth"></div>', PHP_EOL;
    echo '  <input type="submit" value="Upload" id="save_btn" class="custom_btn" />', PHP_EOL;
    echo '</form>', PHP_EOL;
  }
  return false;
}

if (isset($_GET['uri'])) {
  showImage($_GET['uri']);
}
elseif (isset($_GET['add'])) {
  if (addImage()) {
    showImages();
  }
  elseif (isset($_FILES['events_image_bin'])) {
    addImage(true);
  }
}
elseif (isset($_GET['delete'])) {
  if (deleteImage($_GET['delete'])) {
    showImages();
  }
  else {
    showImage($_GET['delete']);
  }
}
else {
  showImages();
}
?>
<div class="dottedBreak dotted"></div>
</div>
</div>
<!-- CONTENT -->

<!-- FOOTER -->
<?php
include_once(HOME_DIR . 'scripts/footerSnippet.php');
echo footerSnippet();
?>
<!-- FOOTER -->

</body>
</html>