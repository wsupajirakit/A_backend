<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  $fname = trim($_POST['fullname']);
  $email = trim($_POST['email']);
  $phone = $_POST['phone'];
  
  if($adminUser !== null){
    if($fname=="" || strlen($fname)==0 || strlen($fname)>100 || !validateData('name', $fname) ){
      $response = array('status' => 2, 'msg' => $txt_var['name_invalid']);
    }else if($email=="" || strlen($email)==0 || strlen($email)>100 || !validateData('email', $email)){
      $response = array('status' => 2, 'msg' => $txt_var['email_invalid']);
    }else if($phone=="" || strlen($phone)==0 || strlen($phone)>15 || !validateData('email', $email)){
      $response = array('status' => 2, 'msg' => $txt_var['phone_help']);
    }else{
      $userAccessTokenData = array('uid' => $adminUser['user_id']);
      $sqlCheck = "SELECT COUNT(*) FROM user WHERE (email = :em OR phone = :pn) AND user_id != :uid";
      $paramCheck = array(':em' => $email, ':pn' => $phone, ':uid' => $userAccessTokenData['uid']);
      $check = $db->dbCount($sqlCheck, $paramCheck);
      if($check > 0){
        $response = array('status' => 2, 'msg' => $txt_var['phone_email_unavailable']);
      }else{
        $sql = "UPDATE user SET fullname = :fn, email = :em, phone = :pn WHERE user_id = :uid";
        $param = array(':fn' => $fname, ':em' => $email, ':pn' => $phone, ':uid' => $userAccessTokenData['uid']);
        $res = $db->dbQuery($sql, $param);
        $response = array('status' => 1, 'msg' => $txt_var['add_new_success']);
      }
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' =>  $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>