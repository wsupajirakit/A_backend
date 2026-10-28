<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  $message = htmlspecialchars(trim($_POST['message']));
  $prUrl = (isset($_POST['pr_url']) && $_POST['pr_url']!="") ? trim($_POST['pr_url']) : "#";

  if($adminUser !== null){
    if(strlen($message) > 0){
      $userAccessTokenData = array('uid' => $adminUser['user_id']);

      $now = new DateTime();
      $nowFormatted = $now->format('Y-m-d H:i:s');

      $sql = "INSERT INTO public_relations (pr_message, pr_type, pr_date, pr_url,  user_id) VALUES (:ms, :tp, :dt, :pu, :uid)";
      $param = array(':ms' => $message, ':tp' => 3, ':dt' => $nowFormatted, ':pu' => $prUrl, ':uid' => $userAccessTokenData['uid']);
      $res = $db->dbQuery($sql, $param);
      if($res!==false){
        $rowId = $db->lastInsertID();
        $response = array('status' => 1, 'msg' => $txt_var['request_success'], 'result' => array('pr_id' => $rowId, 'pr_message' => $message, 'pr_url' => $prUrl));
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