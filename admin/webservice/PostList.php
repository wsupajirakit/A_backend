<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';
  $db = new DatabaseConnection();
  if($adminUser !== null){
    $sql = "SELECT * FROM post p INNER JOIN user u ON p.user_id = u.user_id ORDER BY p.post_id DESC";
    $data = $db->dbQuery($sql);
    $postList = array();
    foreach ($data as $key => $value){
      $nowBkk = new DateTime($value['publish_date']);
      $nowBkk->setTimezone(new DateTimeZone('Asia/Bangkok'));
      $nowBkkFormatted = $nowBkk->format('Y-m-d H:i:s');
      list($pDate, $pTime) = explode(" ", $nowBkkFormatted);
      $postList[] = array(
        'id'        =>  $value['post_id'],
        'author'    =>  $value['fullname'],
        'title'     =>  htmlspecialchars_decode($value['post_title']),
        // 'date'      =>  $nowBkkFormatted,
        'date'      =>  array('display' => toReadableDateTime(strtotime($pDate)), 'timestamp' => strtotime($nowBkkFormatted)),
        'img'       =>  $siteConfig['post-config']['cover_url'] . $value['post_cover'],
        'isPublish' =>  $value['publish_status'],
      );
    }
    $response = array('data' => ($postList));
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' =>  $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>