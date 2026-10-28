<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();


  $update = $_POST['update'];
  $pr_id = (int) ($_POST['id'] ?? 0);
  if($adminUser !== null){
    $prConfig = $siteConfig['pr-config'];
    if($update == 'info'){
      $prTitle = htmlspecialchars(trim($_POST['pr_title']));
      $prUrl = (isset($_POST['pr_url']) && $_POST['pr_url']!="") ? trim($_POST['pr_url']) : "#";
      if(strlen($prTitle) > 0){

        $sql = "UPDATE public_relations SET pr_title = :tt, pr_url = :pu WHERE pr_id = :id";
        $param = array(':tt' => $prTitle, ':pu' => $prUrl, ':id' => $pr_id);
        $res = $db->dbQuery($sql, $param);
        if($res!==false){
          $prRow = $db->dbRow("SELECT pr_image FROM public_relations WHERE pr_id = :id", array(':id' => $pr_id));
          $response = array('status' => 1, 'msg' => $txt_var['request_success'], 'result' => array('pr_id' => $pr_id, 'pr_title' => $prTitle, 'pr_url' => $prUrl, 'pr_image' => $prConfig['app_popup_url'] . $prRow['pr_image']),);
        }else{
          $response = array('status' => 2, 'msg' => $txt_var['request_error']);
        }
      }else{
        $response = array('status' => 2, 'msg' => $txt_var['please_fill_data']);
      }
    }else if($update == 'image'){
      $prImage = $_FILES['apppopup_image'];

      $uploadDir = __DIR__."/../../public/rsc/app/";
      $fileExt = safeUploadExtension($prImage);
      $newName = randomFileName();
      $targetFile = $uploadDir . $newName . "." . $fileExt;
      $validateFile = validateFileToUpload($prImage, 'image', $uploadDir, $prConfig["app_popup_ext"], $prConfig["app_popup_size_min"], $prConfig["app_popup_size_max"], _FILE_UPLOAD_MAX_SIZE);
      if(1!=$validateFile){
        $response = array('status' => 2, 'msg' => $txt_var['request_error']);
      }else{
        // upload new image
        if(move_uploaded_file($prImage["tmp_name"], $targetFile)===true){
          $up_time = strtotime('now');
          touch($targetFile, $up_time);
          $prImageName = $newName .  "." . $fileExt;
          
          $prOldImage = $db->dbRow("SELECT pr_image FROM public_relations WHERE pr_id = :id", array(':id' => $pr_id));

          $sql = "UPDATE public_relations SET pr_image = :im WHERE pr_id = :id";
          $param = array(':im' => $prImageName, ':id' => $pr_id);
          $res = $db->dbQuery($sql, $param);
          if($res!==false){
            $prRow = $db->dbRow("SELECT * FROM public_relations WHERE pr_id = :id", array(':id' => $pr_id));
            $response = array('status' => 1, 'msg' => $txt_var['request_success'], 'result' => array('pr_id' => $pr_id, 'pr_title' => $prRow['pr_title'], 'pr_url' => $prRow['pr_url'], 'pr_image' => $prConfig['app_popup_url'] . $prRow['pr_image']));
            // delete old image
            unlink($uploadDir . $prOldImage['pr_image']);
          }else{
            $response = array('status' => 2, 'msg' => $txt_var['request_error']);
          }
        }else{
          $response = array('status' => 2, 'msg' => $txt_var['request_error']);
        }
      }
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>