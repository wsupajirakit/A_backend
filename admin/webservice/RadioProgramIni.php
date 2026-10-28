<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/lib/Parsedown/Parsedown.php';
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';


  $action = $_GET['action'];
  if($adminUser !== null){
    $db = new DatabaseConnection();
    
    $rdoProgramConfig = $siteConfig["radioprogram-config"];
    $radioList = json_decode($radioProgramConfig['radio_list'], true);

    $r = array();
    if($action=='new'){
      // only dj that active
      $rp = $db->dbQuery("SELECT rp_id AS id, rp_name AS name, rp_image AS image FROM radio_presenter WHERE rp_status = 1");
      $r['radio_presenter'] =  $rp;
    }
    else if($action=='update'){
      $tmpDjList = array();
      $id = (int) ($_GET['id'] ?? 0);
      $rpData = $db->dbRow("SELECT * FROM radio_program WHERE rdo_program_id = :id", array(':id' => $id));
      list($hrStart, $hrEnd) = array_map('intval', explode("-", $rpData['rdo_program_time_range']));
      $hrGap = $rpData['rdo_program_hour_gap'];

      $rpDetails = $db->dbQuery("SELECT * FROM radio_program_details WHERE rdo_program_id = :id", array(':id' => $id));
      $scheduleObj = array();
      foreach($rpDetails as $key => $value){
        $day = $value['rdo_program_details_day']; 
        $st = new DateTime($value['rdo_program_details_time_start']); 
        $ed = new DateTime($value['rdo_program_details_time_end']);
        $tmpDt = $st;
        $arrDt = array();
        while($tmpDt < $ed){ // loop from start to end time range
          $out = $day . "_" . $tmpDt->format("H_i"); 
          $tmpDt->modify('+'.(intval($hrGap)-1).' minutes'); 
          $arrDt[] = $out .= "-" . $day . "_" . $tmpDt->format("H_i"); 
          $tmpDt->modify('+1 minutes');
        }

        $dj = $db->dbColumn("SELECT rp_id FROM radio_program_details_radio_presenter WHERE rdo_program_details_id = :id", array(':id' => $value['rdo_program_details_id']));
        $scheduleObj[] = array('time' => $arrDt, 'rp' => $dj);

        // dj that active or previosly in this schedule
        $tmpDjList = array_merge($tmpDjList, $dj);
        $djIds = $tmpDjList ? array_map('intval', array_values($tmpDjList)) : array(0); // IN () needs at least one value
        $inDj  = str_repeat('?,', count($djIds) - 1) . '?';
        $rp = $db->dbQuery("SELECT rp_id AS id, rp_name AS name, rp_image AS image FROM radio_presenter WHERE rp_id IN ($inDj) OR rp_status = 1", $djIds);
        $r['radio_presenter'] =  $rp;
      }
      $tmpDjList = array_values(array_unique($tmpDjList));

      $r['schedule'] = $scheduleObj;
    }
    $response = array('status' => 1, 'config' =>  $rdoProgramConfig, 'result' => $r);
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' =>  $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>