<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  
  $artist_name = htmlspecialchars(trim($_POST['artist_name']));

  if($adminUser !== null){
    if(strlen($artist_name) > 0){
      $chk = $db->dbCount("SELECT COUNT(*) FROM artist WHERE artist_name = :nm", array(':nm' => $artist_name));
      if($chk == 0){
        $sql = "INSERT INTO artist (artist_name) VALUES (:nm)";
        $param = array(':nm' => $artist_name);
        $res = $db->dbQuery($sql, $param);
        if($res!==false){
          $response = array('status' => 1, 'msg' => $txt_var['request_success']);
        }else{
          $response = array('status' => 2, 'msg' => $txt_var['request_error']);
        }
      }else{
        $response = array('status' => 2, 'msg' => $txt_var['artist_name_duplicate']);
      }
    }else{
      $response = array('status' => 2, 'msg' => $txt_var['please_fill_data']);
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>