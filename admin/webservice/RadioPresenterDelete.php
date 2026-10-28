<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  $rp_id = (int) ($_POST['id'] ?? 0);

  if($adminUser !== null){
    $userAccessTokenData = array('uid' => $adminUser['user_id']);
    $chk = $db->dbCount("SELECT COUNT(rp_id) FROM radio_program_details_radio_presenter WHERE rp_id = :id", array(':id' => $rp_id));
    if($chk == 0){
      $rpData = $db->dbRow("SELECT * FROM radio_presenter WHERE rp_id = :id", array(':id' => $rp_id));
      if($rpData !== null){
        $sql = "DELETE FROM radio_presenter WHERE rp_id = :id";
        $res = $db->dbQuery($sql, array(':id' => $rp_id));
        if($res !== false){
          unlink(__DIR__."/../../public/rsc/radio_presenter/" . $rpData['rp_image']);
        }
      }
      $response = array('status' => 1, 'msg' => $txt_var['request_success']);
    }else{
      $dis = $db->dbQuery("UPDATE radio_presenter SET rp_status = 0 WHERE rp_id = :id", array(':id' => $rp_id));
      $response = array('status' => 1, 'msg' => $txt_var['request_success']);
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>