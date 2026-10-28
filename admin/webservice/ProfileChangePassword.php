<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  $pwd = $_POST['password'];
  $newPwd = $_POST['new_password'];
  $conPwd = $_POST['confirm_password'];
  
  if($adminUser !== null){
    $userAccessTokenData = array('uid' => $adminUser['user_id']);
    $userData = $db->dbRow("SELECT * FROM user WHERE user_id = :uid", array(':uid' => $userAccessTokenData['uid']));
    if(!password_verify($pwd, $userData['password'])){
      $response = array('status' => 2, 'msg' => $txt_var['password_invalid']);
    }else{
      if(strlen($newPwd) < 6 || strlen($newPwd) > 60 || strlen($conPwd) < 6 || strlen($conPwd) > 60){
        $response = array('status' => 2, 'msg' => $txt_var['invalid_password_lenght']);
      }else if(!validateData('password', $newPwd) || !validateData('password', $conPwd)){
        $response = array('status' => 2, 'msg' => $txt_var['invalid_input_data']);
      }else if($newPwd != $conPwd){
        $response = array('status' => 3, 'msg' => $txt_var['password_not_match']);
      }else{
        $pwd = password_hash($newPwd, PASSWORD_BCRYPT);
        $sql = "UPDATE user SET password = :pwd WHERE user_id = :uid";
        $param = array(':pwd' => $pwd, ':uid' => $userData['user_id']);
        $up = $db->dbQuery($sql, $param);
        $response = array('status' => 1, 'msg' => $txt_var['change_password_success']);
      }
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' =>  $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>