<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';
  $action = $_GET['action'];
  $pid = (int) ($_GET['post_id'] ?? 0);
  if($adminUser !== null){
    $postConfig = $siteConfig["post-config"];
    $db = new DatabaseConnection();
    $post = null;
    if($action=='update'){
      $sql = "SELECT * FROM post WHERE post_id = :pid";
      $param = array(':pid' => $pid);
      $post = $db->dbRow($sql, $param);
      if(null !== $post){
        $post["post_content"] = parseContentHTML($post["post_content"], "o");
        $post["post_title"] = htmlspecialchars_decode($post["post_title"]);

        $timeBkk = new DateTime($post["publish_date"], new DateTimeZone(date_default_timezone_get()));
        $timeBkk->setTimezone(new DateTimeZone('Asia/Bangkok'));
        $timePublish = $timeBkk->format('Y-m-d H:i:s');

        $post["publish_date"] = $timePublish;

        unset($post['user_id']);
        unset($post['post_id']);
      }
    }
    $response = array('status' => 1, 'config' =>  $postConfig, 'post' => $post);
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' =>  $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>