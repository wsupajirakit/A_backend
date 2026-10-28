<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();


  $page = $_POST['page'];
  $app_img = $_FILES['app_img'];

  $appearancePath = __DIR__.'/../data/web-client/appearance.json';
  if(file_exists($appearancePath)){
    $appearanceFile = fopen($appearancePath, 'r');
    if($appearanceFile){
      $appearanceData = fread($appearanceFile, filesize($appearancePath));
      $appearanceData = json_decode($appearanceData, true);
    }
    fclose($appearanceFile);
  }else{
    echo "Someting went wrong!";
    die();
  }

  if($adminUser !== null){
    $supportExt = "jpg,jpeg,png,gif,bmp";
    $uploadUrl = "https://www.example.com/assets/img/bg/";
    $uploadDir = __DIR__."/../../assets/img/bg/";
    $fileExt = safeUploadExtension($app_img);
    $newName = randomFileName();
    $targetFile = $uploadDir . $newName . "." . $fileExt;
    $validateFile = validateFileToUpload($app_img, 'image', $uploadDir, $supportExt, false, false, _FILE_UPLOAD_MAX_SIZE);
    if(1!=$validateFile){
      $response = array('status' => 2, 'msg' => $txt_var['request_error']);
    }
    else if(!isset($appearanceData[$page])){
      $response = array('status' => 2, 'msg' => $txt_var['request_error']);
    }
    else{
      // upload new image
      if(move_uploaded_file($app_img["tmp_name"], $targetFile)===true){
        $up_time = strtotime('now');
        touch($targetFile, $up_time);
        $app_imgName = $newName .  "." . $fileExt;
        
        unlink($uploadDir . $appearanceData[$page]['bg_image_filename']);
        $appearanceData[$page]['bg_image'] = $uploadUrl . $app_imgName;
        $appearanceData[$page]['bg_image_filename'] = $app_imgName;

        $fp = fopen($appearancePath, 'w');
        fwrite($fp, json_encode($appearanceData));
        fclose($fp);

        $response = array('status' => 1, 'msg' => $txt_var['request_success'], 'result' => array('bg_image' => $uploadUrl . $app_imgName, 'page' => $page, 'd' => $appearanceData));
      }else{
        $response = array('status' => 2, 'msg' => $txt_var['request_error']);
      }
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>