<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();
  
  $songs = trim($_POST['songs'])=="" ? array() : array_map('trim', explode(",", $_POST['songs']));
  $tc_date = trim($_POST['top_chart_date']);
  $tc_num = (string) (int) ($_POST['top_chart_num'] ?? 0);

  if($adminUser !== null){
    list($tc_day, $tc_month, $tc_year) = explode("/", $tc_date);
    $topchartConfig = $siteConfig['topchart-config'];
    $allowTcNum = explode(",", $topchartConfig['topchart_num']);
    if(checkdate($tc_month, $tc_day, $tc_year) && in_array($tc_num, $allowTcNum)){
      if(count($songs) > 0 && count($songs) == $tc_num){
        // check songs exist
        $songsId = array();
        for($i=0; $i < count($songs); $i++){ 
          $tmp = explode("_", $songs[$i]);
          $songsId[] = $tmp[0];
        }
        $songsId = array_map('intval', $songsId);
        $in  = str_repeat('?,', count($songsId) - 1) . '?';
        $chk = $db->dbCount("SELECT COUNT(*) FROM song WHERE song_id IN ($in)", $songsId);
        if($chk == count($songs)){
          // check date duplicate
          $dateFormatted = $tc_year . "-" . $tc_month . "-" .$tc_day;
          $chkDate = $db->dbCount("SELECT COUNT(*) FROM top_chart WHERE top_chart_date = :dt", array(':dt' => $dateFormatted));
          if($chkDate == 0){
            $db->pdo->beginTransaction();
            try{
              $userAccessTokenData = array('uid' => $adminUser['user_id']);

              $sql = "INSERT INTO top_chart (top_chart_date, user_id) VALUES (:dt, :uid)";
              $param = array(':dt' => $dateFormatted, ':uid' => $userAccessTokenData['uid']);
              $stmt = $db->pdo->prepare($sql);
              $stmt->execute($param);

              $id = $db->lastInsertID();
              for($i=0; $i < count($songs); $i++){ 
                list($_sid, $_lwo, $_ocwn) = array_map("trim", explode("_", $songs[$i]));
                $_lwo = intval($_lwo);
                $_lwo = ($_lwo == 0 || $_lwo == "") ? null : $_lwo;
                $_ocwn = ($_ocwn == 0 || $_ocwn == "") ? null : $_ocwn;
                $sql = "INSERT INTO top_chart_song (top_chart_id, song_id, song_order, last_week_order, on_chart_week_number) VALUES (:tid, :sid, :od, :lwo, :ocwn)";
                $param = array(':tid' => $id, ':sid' => $_sid, ':od' => $i+1, ':lwo' => $_lwo, ':ocwn' => $_ocwn);
                $stmt = $db->pdo->prepare($sql);
                $stmt->execute($param);
              }
              $db->pdo->commit();
              $response = array('status' => 1, 'msg' => $txt_var['request_success']);
            }catch(Exception $e){
              $db->pdo->rollBack();
              $response = array('status' => 2, 'msg' => $txt_var['request_error']);
            }
          }else{
            $response = array('status' => 2, 'msg' => str_replace("{n}", $tc_date, $txt_var['top_chart_date_duplicate']));
          }
        }else{
          $response = array('status' => 2, 'msg' => $txt_var['request_error']);
        }
      }else{
        $response = array('status' => 2, 'msg' => str_replace("{n}", $tc_num, $txt_var['top_chart_songs_num_notice']));
      }
    }else{
      $response = array('status' => 2, 'msg' => $txt_var['request_error']);
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>