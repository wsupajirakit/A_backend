<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  $pr_id = (int) ($_POST['id'] ?? 0);

  if($adminUser !== null){
    $userAccessTokenData = array('uid' => $adminUser['user_id']);
    $sql = "DELETE FROM public_relations WHERE pr_id = :id";
    $res = $db->dbQuery($sql, array(':id' => $pr_id));
    $response = array('status' => 1, 'msg' => $txt_var['request_success']);
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>