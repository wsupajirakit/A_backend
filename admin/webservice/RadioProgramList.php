<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';


  $db = new DatabaseConnection();
  if($adminUser !== null){
    $radioProgramConfig = $siteConfig['radioprogram-config'];
    $radioList = json_decode($radioProgramConfig['radio_list'], true);

    $sql = "SELECT * FROM radio_program ORDER BY rdo_program_end DESC";
    $data = $db->dbQuery($sql);
    $rpList = array();
    foreach ($data as $key => $value){
      $rdoData = searchArrayByValue($radioList, 'id', $value['radio_id'], true);

      $timeStartBkk = new DateTime($value['rdo_program_start'], new DateTimeZone(date_default_timezone_get()));
      $timeStartBkk->setTimezone(new DateTimeZone('Asia/Bangkok'));
      $timeStartStamp = $timeStartBkk->getTimestamp();

      $timeEndBkk = new DateTime($value['rdo_program_end'], new DateTimeZone(date_default_timezone_get()));
      $timeEndBkk->setTimezone(new DateTimeZone('Asia/Bangkok'));
      $timeEndStamp = $timeEndBkk->getTimestamp();

      $timeNowBkk = new DateTime(null, new DateTimeZone(date_default_timezone_get()));
      $timeNowBkk->setTimezone(new DateTimeZone('Asia/Bangkok'));
      $timeNowStamp = strtotime($timeNowBkk->format("Y-m-d "). "00:00:00");
      $isPresent = ($timeStartStamp <= $timeNowStamp && $timeEndStamp >= $timeNowStamp) ? true : false;


      $rpList[] = array(
        'id'              =>  $value['rdo_program_id'],
        'start_date'      =>  array('display' => toReadableDateTime(strtotime($value['rdo_program_start'])), 'timestamp' => strtotime($value['rdo_program_start'])),
        'end_date'        =>  array('display' => toReadableDateTime(strtotime($value['rdo_program_end'])), 'timestamp' => strtotime($value['rdo_program_end'])),
        'radio_id'      =>  $rdoData['id'],
        'radio_name'    =>  $rdoData['name'],
        'is_present' => $isPresent,
        'e' => $timeEndStamp,
        's' => $timeStartStamp,
        'n' => $timeNowStamp,
      );
    }
    $response = array('data' => ($rpList));
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' =>  $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>