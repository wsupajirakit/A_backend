<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  $prImage = $_FILES['sponsor_image'];
  $prTitle = htmlspecialchars(trim($_POST['pr_title']));
  $prUrl = (isset($_POST['pr_url']) && $_POST['pr_url']!="") ? trim($_POST['pr_url']) : "#";

  if($adminUser !== null){
    if(strlen($prTitle) > 0){

      $prConfig = $siteConfig['pr-config'];

      $userAccessTokenData = array('uid' => $adminUser['user_id']);

      $now = new DateTime();
      $nowFormatted = $now->format('Y-m-d H:i:s');

      $checkImg = true;
      $uploadDir = __DIR__."/../../public/rsc/sponsor/";
      $fileExt = safeUploadExtension($prImage);
      $newName = randomFileName();
      $targetFile = $uploadDir . $newName . "." . $fileExt;
      $validateFile = validateFileToUpload($prImage, 'image', $uploadDir, $prConfig["sponsor_ext"], $prConfig["sponsor_size_min"], $prConfig["sponsor_size_max"], _FILE_UPLOAD_MAX_SIZE);
      if(1!=$validateFile){
        $checkImg = false;
      }else{
        if(move_uploaded_file($prImage["tmp_name"], $targetFile)===true){
          $up_time = strtotime('now');
          touch($targetFile, $up_time);
          $prImageName = $newName .  "." . $fileExt;
        }else{
          $checkImg = false;
        }
      }

      if($checkImg===true){
        $sql = "INSERT INTO public_relations (pr_title, pr_image, pr_type, pr_date, pr_url, user_id) VALUES (:tt, :im, :tp, :dt, :pu, :uid)";
        $param = array(':tt' => $prTitle, ':im' => $prImageName, ':tp' => 2, ':dt' => $nowFormatted, ':pu' => $prUrl, ':uid' => $userAccessTokenData['uid']);
        $res = $db->dbQuery($sql, $param);
        if($res!==false){
          $rowId = $db->lastInsertID();
          $response = array('status' => 1, 'msg' => $txt_var['request_success'], 'result' => array('pr_id' => $rowId, 'pr_title' => $prTitle, 'pr_url' => $prUrl, 'pr_image' => $prConfig["sponsor_url"] . $prImageName));
        }else{
          unlink($targetFile);
          $response = array('status' => 2, 'msg' => $txt_var['request_error']);
        }
      }else{
        $response = array('status' => 2, 'msg' => $txt_var['request_error']);
      }
    }else{
      $response = array('status' => 2, 'msg' => $txt_var['please_fill_data']);
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>