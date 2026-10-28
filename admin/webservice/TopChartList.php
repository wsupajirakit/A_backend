<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';
  $db = new DatabaseConnection();
  if($adminUser !== null){
    $sql = "SELECT * FROM top_chart ORDER BY top_chart_date DESC";
    $data = $db->dbQuery($sql);
    $tcList = array();
    foreach($data as $key => $value){
      $sql = "SELECT * FROM song s INNER JOIN top_chart_song ts ON s.song_id = ts.song_id WHERE ts.top_chart_id = :sid AND ts.song_order = 1";
      $param = array(':sid' => $value['top_chart_id']);
      $top1 = $db->dbRow($sql, $param);

      $num = $db->dbCount("SELECT COUNT(*) FROM top_chart_song WHERE top_chart_id = :sid", array(':sid' => $value['top_chart_id']));
      $tcList[] = array(
        'id'      =>  $value['top_chart_id'],
        // 'date'  =>  $tc_day . "/" . $tc_month . "/" . $tc_year,
        'date'  =>  array('display' => toReadableDateTime(strtotime($value['top_chart_date'])), 'timestamp' => strtotime($value['top_chart_date'])),
        'num_1_song'  =>  $top1['song_name'] . '-' .$top1['artist_name_map'],
        'num_song'  =>  $num,
      );
    }
    $response = array('data' => ($tcList));
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' =>  $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>