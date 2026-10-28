<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';


  $db = new DatabaseConnection();
  if($adminUser !== null){
    $sql = "SELECT * FROM radio_presenter WHERE rp_status = 1 ORDER BY rp_name ASC";
    $data = $db->dbQuery($sql);
    $rpList = array();
    foreach ($data as $key => $value){
      $rpList[] = array(
        'id'    =>  $value['rp_id'],
        'name'  =>  htmlspecialchars_decode($value['rp_name']),
        'img'   =>  $siteConfig['radiopresenter-config']['rp_url'] . $value['rp_image'],
        // 'birthdate'   =>  date("d/m/Y", strtotime($value['rp_birthdate'])),
        'birthdate'   =>  array('display' => toReadableDateTime(strtotime($value['rp_birthdate'])), 'timestamp' => strtotime($value['rp_birthdate'])),
        'age'   =>  getAge($value['rp_birthdate']),
      );
    }
    $response = array('data' => ($rpList));
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' =>  $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>