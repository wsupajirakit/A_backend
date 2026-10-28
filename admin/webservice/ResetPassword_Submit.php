<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';
  
  $db = new DatabaseConnection();
  $userEmail = trim((string) ($_POST['email'] ?? ''));
  $code = (string) ($_POST['refcode'] ?? '');
  $newPwd = (string) ($_POST['new-password'] ?? '');
  $conPwd = (string) ($_POST['confirm-password'] ?? '');
  $now = strtotime('now');
  $checkValidity = true;
  if(strlen($userEmail)>0){
    if(validateData('email', $userEmail)){
      $sql = "SELECT * FROM user WHERE email = :em AND active_status = 1";
      $userData = $db->dbRow($sql, array(':em' => $userEmail));
      if($userData!==null){
        $refFile = __DIR__."/../data/user-token/reset-password/" . $userData['user_id'] . ".json";
        if(file_exists($refFile)){
          $myfile = fopen($refFile, "r");
          $refData = fread($myfile, filesize($refFile));
          $refData = json_decode($refData, true);
          fclose($myfile);
          if(!is_array($refData) || !hash_equals((string) $refData['token'], $code) || $now > $refData['expiration_stamp'] ||  $refData['user_id'] != $userData['user_id']){
            $checkValidity = false;
          }
        }else{
          $checkValidity = false;
        }
      }else{
        $checkValidity = false;
      }
      if($checkValidity===true){
        if(strlen($newPwd) < 6 || strlen($newPwd) > 60 || strlen($conPwd) < 6 || strlen($conPwd) > 60){
          $response = array('status' => 2, 'msg' => $txt_var['invalid_password_lenght']);
        }else if(!validateData('password', $newPwd) || !validateData('password', $conPwd)){
          $response = array('status' => 2, 'msg' => $txt_var['invalid_input_data']);
        }else if($newPwd != $conPwd){
          $response = array('status' => 3, 'msg' => $txt_var['password_not_match']);
        }else{
          $pwd = password_hash($newPwd, PASSWORD_DEFAULT);
          $sql = "UPDATE user SET password = :pwd WHERE user_id = :uid";
          $param = array(':pwd' => $pwd, ':uid' => $userData['user_id']);
          $up = $db->dbQuery($sql, $param);
          unlink($refFile);
          $response = array('status' => 1, 'msg' => $txt_var['change_password_success']);
        }
      }else{
        $response = array('status' => 2, 'msg' => $txt_var['reset_password_code_invalid_2']);
      }
    }else{
      $response = array('status' => 2, 'msg' => $txt_var['email_invalid']);
    }
  }else{
    $response = array('status' => 2, 'msg' => $txt_var['please_fill_data']);
  }
  echo json_encode($response);
?>