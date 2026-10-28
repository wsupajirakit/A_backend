<?php
  error_reporting( E_ALL );
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  
  $rpScehdule = $_POST['rp_schedule'];
  $rpRadio = (int) ($_POST['rdo_select'] ?? 0);
  if($adminUser !== null){
    $radioProgramConfig = $siteConfig['radioprogram-config'];
    $radioList = json_decode($radioProgramConfig['radio_list'], true);
    $rdoConf = searchArrayByValue($radioList, 'id', $rpRadio, true);
    // check radio
    if($rdoConf !== null && $rdoConf !== false){
      list($sd, $sm, $sy) = array_map("intval", explode("/", $_POST['rdo_program_start']));
      list($ed, $em, $ey) = array_map("intval", explode("/", $_POST['rdo_program_end']));
      if(count($rpScehdule) > 0){
        if(checkdate($sm, $sd, $sy) && checkdate($em, $ed, $ey) && strtotime($sy."-".$sm."-".$sd) < strtotime($ey."-".$em."-".$ed)){
          $rpStart  = $sy . "-" . $sm . "-" . $sd;
          $rpEnd    = $ey . "-" . $em . "-" . $ed;

          // collect schedule and dj data to process
          // variable to check schedule time 
          $scSortByDay = array();
          // variable to check dj exists and active
          $djCheckList = array();
          // variable to use to submit data
          $scTmp = array();
          foreach($rpScehdule as $key => $value){
            list($dt, $dj) = explode(":", $value);
            $dj = explode(",", $dj);
            $djCheckList = array_merge($djCheckList, $dj);

            $dNum = substr($dt, 0, 1);
            $dt = explode(",", $dt);

            // start time
            $time1 = explode("-", $dt[0]);  
            list($sD, $sHr, $sMn) = explode("_", $time1[0]);   
            $d1 = new DateTime($sHr.":".$sMn.":00"); 
            // end time
            $time2 = explode("-", $dt[count($dt)-1]);
            list($eD, $eHr, $eMn) = explode("_", $time2[1]);  
            $d2 = new DateTime($eHr.":".$eMn.":00"); 

            $t = array($d1->format('Y-m-d H:i:s'), $d2->format('Y-m-d H:i:s'));
            $scSortByDay[$dNum][] = $t;

            $scTmp[] = array('dj' => $dj, 'time' => $t, 'day' => $dNum);
          }
          // remove deplicate dj
          $djCheckList = array_values(array_unique($djCheckList));

          // check time format
          $checkTimeFormatEachDay = true;
          for($i=0; $i < count($scSortByDay); $i++){ 
            if(!checkScheduleTimeOverlap($scSortByDay[$i])){
              $checkTimeFormatEachDay = false;
              break;
            }
          }
          if($checkTimeFormatEachDay === true){
            // check is time range overlap
            $checkOverlapEachDay = true;
            for($i=0; $i < count($scSortByDay); $i++){ 
              if(!checkScheduleTimeOverlap($scSortByDay[$i])){
                $checkOverlapEachDay = false;
                break;
              }
            }
            // check is schedule range overlap with another
            if($checkOverlapEachDay === true){
              $checkOverlapWithOthersSchedule = $db->dbCount("SELECT COUNT(rdo_program_id) FROM radio_program WHERE ((:sdate BETWEEN rdo_program_start AND rdo_program_end) OR (:edate BETWEEN rdo_program_start AND rdo_program_end)) AND radio_id = :rdo", array(':sdate' => $rpStart, ':edate' => $rpEnd, ':rdo' => $rpRadio));
              if($checkOverlapWithOthersSchedule == 0){
                // check dj
                $inDj  = str_repeat('?,', count($djCheckList) - 1) . '?';
                $checkDj = $db->dbCount("SELECT COUNT(rp_id) FROM radio_presenter WHERE rp_id IN ($inDj) AND rp_status = 1", $djCheckList);
                if($checkDj == count($djCheckList)){
                  // ready to submit
                  $db->pdo->beginTransaction();
                  try{
                    $sql = "INSERT INTO radio_program (rdo_program_start, rdo_program_end, rdo_program_time_range, rdo_program_hour_gap, radio_id) VALUES (:st, :en, :rn, :gp, :rdo)";
                    $param = array(':st' => $rpStart, ':en' => $rpEnd, ':rn' => $rdoConf['onair'], ':gp' => $rdoConf['hour_gap'], ':rdo' => $rpRadio);
                    $stmt = $db->pdo->prepare($sql);
                    $stmt->execute($param);

                    $id = $db->lastInsertID();
                    for($i=0; $i < count($scTmp); $i++){ 
                      $ts = explode(" ", $scTmp[$i]['time'][0]);
                      $te = explode(" ", $scTmp[$i]['time'][1]);
                      $sql = "INSERT INTO radio_program_details (rdo_program_id, rdo_program_details_day,   rdo_program_details_time_start, rdo_program_details_time_end) VALUES (:rpid, :rpd, :rps, :rpe)";
                      $param = array(':rpid' => $id, ':rpd' => $scTmp[$i]['day'], ':rps' => $ts[1], ':rpe' => $te[1]);
                      $stmt = $db->pdo->prepare($sql);
                      $stmt->execute($param);
                      
                      $rpd_id = $db->lastInsertID();
                      for($j=0; $j < count($scTmp[$i]['dj']); $j++){ 
                        $sql = "INSERT INTO radio_program_details_radio_presenter (rp_id, rdo_program_details_id) VALUES (:rp_id, :rpd_id)";
                        $param = array(':rp_id' => $scTmp[$i]['dj'][$j], ':rpd_id' => $rpd_id);
                        $stmt = $db->pdo->prepare($sql);
                        $stmt->execute($param);
                      }
                    }
                    $db->pdo->commit();
                    $response = array('status' => 1, 'msg' => $txt_var['request_success'], 'result' => array('id' => $id));
                  }catch(Exception $e){
                    $db->pdo->rollBack();
                    $response = array('status' => 2, 'msg' => $txt_var['request_error']);
                  }
                }else{
                  $response = array('status' => 2, 'msg' => $txt_var['select_radio_predenter_invalid']);
                }
              }else{
                $response = array('status' => 2, 'msg' => $txt_var['radio_program_date_overlap']);
              }
            }else{
              $response = array('status' => 2, 'msg' => $txt_var['radio_program_date_invalid']);
            }
          }else{
            $response = array('status' => 2, 'msg' => $txt_var['request_error']);
          }
        }else{
          $response = array('status' => 2, 'msg' => $txt_var['request_error']);
        }
      }else{
        $response = array('status' => 2, 'msg' => $txt_var['radio_program_date_empty']);
      }
    }else{
      $response = array('status' => 2, 'msg' => $txt_var['radio_program_date_invalid']);
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>