<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  
  $artist_name = htmlspecialchars(trim($_POST['artist_name']));
  $update = $_POST['update'];
  $ar_id = (int) ($_POST['id'] ?? 0);
  if($adminUser !== null){
    if($update=='info'){
      if(strlen($artist_name) > 0){
        $chk = $db->dbCount("SELECT COUNT(*) FROM artist WHERE artist_name = :nm AND artist_id != :id", array(':nm' => $artist_name, ':id' => $ar_id));
        if($chk == 0){
          $sql = "UPDATE artist SET artist_name = :nm WHERE artist_id = :id";
          $param = array(':nm' => $artist_name, ':id' => $ar_id);
          $res = $db->dbQuery($sql, $param);
          $l = array();
          if($res!==false){
            // update song member name map
            $songs = $db->dbColumn("SELECT song_id FROM song_artist WHERE artist_id = :id", array(':id' => $ar_id));
            for($i=0; $i < count($songs); $i++){ 
              // mapping artist name
              $artists = $db->dbQuery("SELECT a.artist_id, a.artist_name FROM artist a INNER JOIN song_artist sa ON a.artist_id = sa.artist_id WHERE sa.song_id = :id ORDER BY sa.is_lead_artist DESC", array(':id' => $songs[$i]));
              $artistName = "";
              for($j=0; $j < count($artists); $j++){ 
                $artistName .= $artists[$j]['artist_name'];
                if($j==0 && count($artists) > 1)
                  $artistName .= " " . $txt_var['feat'] . " ";
  
                if($j > 0 && $j < count($artists) - 1)
                  $artistName .= ", ";
              }
              $sql = "UPDATE song SET artist_name_map = :mp WHERE song_id = :id";
              $param = array(':mp' => $artistName, ':id' => $songs[$i]);
              $mpRes = $db->dbQuery($sql, $param);
            }

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
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>