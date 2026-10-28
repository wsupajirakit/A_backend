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
    
    $dt = $db->dbColumn("SELECT rdo_program_details_id FROM radio_program_details WHERE rdo_program_id = :id", array(':id' => $rp_id));
    $inDt  = str_repeat('?,', count($dt) - 1) . '?';
    if(!empty($dt)){ $db->dbQuery("DELETE FROM radio_program_details_radio_presenter WHERE rdo_program_details_id IN ($inDt)", $dt); }
    $db->dbQuery("DELETE FROM radio_program_details WHERE rdo_program_id = :id", array(':id' => $rp_id));
    $db->dbQuery("DELETE FROM radio_program WHERE rdo_program_id = :id", array(':id' => $rp_id));

    $response = array('status' => 1, 'msg' => $txt_var['request_success']);
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>