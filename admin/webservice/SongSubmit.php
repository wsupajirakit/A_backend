<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  
  $artists = trim($_POST['artists'])=="" ? array() : array_map('trim', explode(",", $_POST['artists']));
  $song_name = trim(htmlspecialchars($_POST['song_name']));
  if($adminUser !== null){
    if(strlen($song_name) > 0){
      if(count($artists) > 0){
        $artists = array_map('intval', $artists);
        $in  = str_repeat('?,', count($artists) - 1) . '?';
        $chk = $db->dbCount("SELECT COUNT(*) FROM artist WHERE artist_id IN ($in)", $artists);
        if($chk == count($artists)){
          $db->pdo->beginTransaction();
          try{
            // mapping artist name
            $artistName = "";
            for($i=0; $i < count($artists); $i++){ 
              $a = $db->dbRow("SELECT * FROM artist a WHERE artist_id = :id", array(':id' => $artists[$i]));
      
              $artistName .= $a['artist_name'];
              if($i==0 && count($artists) > 1)
                $artistName .= " " . $txt_var['feat'] . " ";

              if($i > 0 && $i < count($artists) - 1)
                $artistName .= ", ";
            }

            $sql = "INSERT INTO song (song_name, artist_name_map) VALUES (:nm, :mp)";
            $param = array(':nm' => $song_name, ':mp' => $artistName);
            $stmt = $db->pdo->prepare($sql);
            $stmt->execute($param);

            $id = $db->lastInsertID();
            for($i=0; $i < count($artists); $i++){ 
              $sql = "INSERT INTO song_artist (song_id, artist_id, is_lead_artist) VALUES (:sid, :aid, :ld)";
              $param = array(':sid' => $id, ':aid' => $artists[$i], ':ld' => $i==0 ? 1 : 0);
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
          $response = array('status' => 2, 'msg' => $txt_var['request_error']);
        }
      }else{
        $response = array('status' => 2, 'msg' => $txt_var['please_select_artist']);
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