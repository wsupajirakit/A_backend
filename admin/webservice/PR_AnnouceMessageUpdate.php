<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  $message = htmlspecialchars(trim($_POST['message']));
  $prUrl = (isset($_POST['pr_url']) && $_POST['pr_url']!="") ? trim($_POST['pr_url']) : "#";
  $pr_id = (int) ($_POST['id'] ?? 0);
  if($adminUser !== null){
    if(strlen($message) > 0){
      $sql = "UPDATE public_relations SET pr_message = :ms, pr_url = :pu WHERE pr_id = :id AND pr_type = 3";
      $param = array(':ms' => $message, ':pu' => $prUrl, ':id' => $pr_id);
      $res = $db->dbQuery($sql, $param);
      if($res!==false){
        $response = array('status' => 1, 'msg' => $txt_var['request_success'], 'result' => array('pr_id' => $pr_id, 'pr_message' => $message, 'pr_url' => $prUrl));
      }else{
        $response = array('status' => 2, 'msg' => $txt_var['request_error']);
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