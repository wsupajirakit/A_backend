<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  $ar_id = (int) ($_POST['id'] ?? 0);

  if($adminUser !== null){
    $userAccessTokenData = array('uid' => $adminUser['user_id']);
    $chk = $db->dbCount("SELECT COUNT(*) FROM song_artist WHERE artist_id = :id", array(':id' => $ar_id));
    if($chk == 0){
      $sql = "DELETE FROM artist WHERE artist_id = :id";
      $res = $db->dbQuery($sql, array(':id' => $ar_id));
      $response = array('status' => 1, 'msg' => $txt_var['request_success']);
    }else{
      $response = array('status' => 2, 'msg' => $txt_var['unable_delete_data_relate']);
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>