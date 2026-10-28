<?php
  header("Content-type: application/json");

  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';



  $rp_name = trim(htmlspecialchars($_POST['rp_name']));
  $rp_bdate = $_POST['rp_bdate'];
  $rp_id = (int) ($_POST['id'] ?? 0);
  $rp_image = $_POST['rp_image_type']=='object' ? $_FILES['rp_image'] : $_POST['rp_image'];

  if($adminUser !== null){
    $now = new DateTime();

    $rpConfig = $siteConfig["radiopresenter-config"];
    $db = new DatabaseConnection();
    $checkImg = true;

    if($_POST['rp_image_type']=='object'){
      $uploadDir = __DIR__."/../../public/rsc/radio_presenter/";
      $fileExt = safeUploadExtension($rp_image);
      $newName = randomFileName();
      $targetFile = $uploadDir . $newName . "." . $fileExt;
      $validateFile = validateFileToUpload($rp_image, 'image', $uploadDir, $rpConfig["rp_ext"], $rpConfig["rp_size_min"], $rpConfig["rp_size_max"], _FILE_UPLOAD_MAX_SIZE);
      if(1!=$validateFile){
        $checkImg = false;
      }else{
        if(move_uploaded_file($rp_image["tmp_name"], $targetFile)===true){
          touch($targetFile, $now->getTimestamp());
          $rp_image = $newName .  "." . $fileExt;
          $isUpNew = true;
        }else{
          $checkImg = false;
        }
      }
    }

    if($checkImg===true){
      list($pd, $pm, $py) = explode("/", $rp_bdate);

      if(!checkdate($pm, $pd, $py)){
        if($isUpNew===true){ unlink($targetFile); }
        $response = array('status' => 2, 'msg' => $txt_var['request_error']);
      }
      else if(strlen($rp_name)==0){
        if($isUpNew===true){ unlink($targetFile); }
        $response = array('status' => 2, 'msg' => $txt_var['please_fill_data']);
      }
      else if(strlen($rp_image)==0){
        if($isUpNew===true){ unlink($targetFile); }
        $response = array('status' => 2, 'msg' => $txt_var['request_error']);
      }
      else{
        $oldImage = $db->dbRow("SELECT rp_image FROM radio_presenter WHERE rp_id = :id", array(':id' => $rp_id));

        $_bd = date("Y-m-d", strtotime($py."-".$pm."-".$pd));
        $sql = "UPDATE radio_presenter SET rp_name = :nm, rp_birthdate = :bd, rp_image = :img WHERE rp_id = :id";
        $param = array(':nm' => $rp_name, ':bd' => $_bd, ':img' => $rp_image, ':id' => $rp_id);
        $res = $db->dbQuery($sql, $param);
        if($res!==false){
          if($isUpNew === true){
            unlink($uploadDir . $oldImage['rp_image']);
          }
          $response = array('status' => 1, 'msg' => $txt_var['request_success']);
        }else{
          $response = array('status' => 2, 'msg' => $txt_var['request_error']);
        }
      }
    }else{
      $response = array('status' => 2, 'msg' =>  $txt_var['request_error']);
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' =>  $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>