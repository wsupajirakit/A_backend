<?php
  header("Content-type: application/json");

  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';



  $rp_name = trim(htmlspecialchars($_POST['rp_name']));
  $rp_image = $_FILES['rp_image'];
  $rp_bdate = $_POST['rp_bdate'];

  if($adminUser !== null){
    $now = new DateTime();

    $rpConfig = $siteConfig["radiopresenter-config"];
    $db = new DatabaseConnection();
    $checkImg = true;

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
      }else{
        $checkImg = false;
      }
    }

    if($checkImg===true){
      list($pd, $pm, $py) = explode("/", $rp_bdate);

      if(!checkdate($pm, $pd, $py)){
        unlink($targetFile);
        $response = array('status' => 2, 'msg' => $txt_var['request_error']);
      }
      else if(strlen($rp_name)==0){
        unlink($targetFile);
        $response = array('status' => 2, 'msg' => $txt_var['please_fill_data']);
      }
      else if(strlen($rp_image)==0){
        unlink($targetFile);
        $response = array('status' => 2, 'msg' => $txt_var['request_error']);
      }
      else{
        $_bd = date("Y-m-d", strtotime($py."-".$pm."-".$pd));
        $sql = "INSERT INTO radio_presenter (rp_name, rp_birthdate, rp_image) VALUES (:nm, :bd, :img)";
        $param = array(':nm' => $rp_name, ':bd' => $_bd, ':img' => $rp_image);
        $res = $db->dbQuery($sql, $param);
        if($res!==false){
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