<?php
  header("Content-type: application/json");
  
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';


  $action = $_POST['action'];
  $post_id = (int) ($_POST['post_id'] ?? 0);
  $post_title = trim(htmlspecialchars($_POST['post_title']));
  $post_content = parseContentHTML($_POST['post_content'], "i");
  $post_image = $_POST['post_cover']=='object' ? $_FILES['post_image'] : $_POST['post_image'];
  $isPublish = $_POST['is_publish']=='on' ? 1 : 0;
  $isPin = $_POST['is_pin']=='on' ? 1 : 0;
  $publishDate = $_POST['publish_date'];
  $publishTime = $_POST['publish_time'];
  $postSlug = trim($_POST['post_slug']);
  if($adminUser !== null){
    $now = new DateTime();

    if(strlen($postSlug) == 0)
      $postSlug = getToken(12);

    $postConfig = $siteConfig["post-config"];
    $db = new DatabaseConnection();
    $checkImg = true;
    $isUpNew = false;
    if($_POST['post_cover']=='object'){
      $uploadDir = __DIR__."/../../public/rsc/content/cover/";
      $fileExt = safeUploadExtension($post_image);
      $newName = randomFileName();
      $targetFile = $uploadDir . $newName . "." . $fileExt;
      $validateFile = validateFileToUpload($post_image, 'image', $uploadDir, $postConfig["img_ext"], $postConfig["cover_size_min"], $postConfig["cover_size_max"], _FILE_UPLOAD_MAX_SIZE);
      if(1!=$validateFile){
        $checkImg = false;
      }else{
        if(move_uploaded_file($post_image["tmp_name"], $targetFile)===true){
          touch($targetFile, $now->getTimestamp());
          $post_image = $newName .  "." . $fileExt;
          $isUpNew = true;
        }else{
          $checkImg = false;
        }
      }
    }
    if($checkImg===true){
      list($pd, $pm, $py) = explode("/", $publishDate);
      list($phr, $pmn) = explode(":", $publishTime);

      if($action=='new'){
        $chkSlug = $db->dbCount("SELECT COUNT(*) FROM post WHERE post_slug = :sg", array(':sg' => $postSlug));
      }else if($action=='update'){
        $chkSlug = $db->dbCount("SELECT COUNT(*) FROM post WHERE post_slug = :sg AND post_id != :id", array(':sg' => $postSlug, ':id' => $post_id));
      }

      if(!checkdate($pm, $pd, $py) || !checktime($phr, $pmn, "00")){
        if($isUpNew===true){
          unlink($targetFile);
        }
        $response = array('status' => 2, 'msg' => $txt_var['request_error']);
      }
      else if(strlen($post_title)==0){
        if($isUpNew===true){
          unlink($targetFile);
        }
        $response = array('status' => 2, 'msg' => $txt_var['post_name_cannot_empty']);
      }
      else if(strlen($post_image)==0){
        if($isUpNew===true){
          unlink($targetFile);
        }
        $response = array('status' => 2, 'msg' => $txt_var['post_cover_cannot_empty']);
      }
      elseif(strlen($post_content)==0){
        if($isUpNew===true){
          unlink($targetFile);
        }
        $response = array('status' => 2, 'msg' => $txt_var['post_cannot_empty']);
      }
      else if($chkSlug > 0){
        if($isUpNew===true){
          unlink($targetFile);
        }
        $response = array('status' => 2, 'msg' => $txt_var['post_slug_cannot_repeat']);
      }
      else if(!validateData('slug', $postSlug)){
        if($isUpNew===true){
          unlink($targetFile);
        }
        $response = array('status' => 2, 'msg' => $txt_var['post_slug_name_notice']);
      }
      else{
        $_dt = $py . "-" .$pm . "-" . $pd . " " . $phr . ":" . $pmn . ":00";
        // convert time from user to utc timezone
        $timeBkk = new DateTime($_dt, new DateTimeZone('Asia/Bangkok'));
        $timeBkk->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $timePublish = $timeBkk->format('Y-m-d H:i:s');

        if($action=='new'){ // insert new post 
          $userAccessTokenData = array('uid' => $adminUser['user_id']);
          $sql = "INSERT INTO post (post_title, post_content, post_cover, publish_date, publish_status, pin_status, post_slug, user_id) VALUES (:tt, :ct, :cv, :pd, :ps, :pn, :sg, :uid)";
          $param = array(':tt' => $post_title, ':ct' => $post_content, ':cv' => $post_image, ':pd' => $timePublish, ':ps' => $isPublish, ':pn' => $isPin, ':sg' => $postSlug, ':uid' => $userAccessTokenData['uid']);
          $res = $db->dbQuery($sql, $param);
          if($res!==false){
            $response = array('status' => 1, 'msg' => $txt_var['request_success'], 'id' => $db->lastInsertID());
          }else{
            $response = array('status' => 2, 'msg' => $txt_var['request_error']);
          }
        }else if($action=='update'){ // update post 
          $sqlCheck = "SELECT * FROM post WHERE post_id = :pid";
          $paramCheck = array(':pid' => $post_id);
          $postCheck = $db->dbRow($sqlCheck, $paramCheck);
          if($postCheck!==null){
            if($postCheck['post_cover']!=$post_image){
              unlink(__DIR__."/../../public/rsc/content/cover/" . $postCheck['post_cover']);
            }
            $sql = "UPDATE post SET post_title = :tt, post_content = :ct, post_cover = :cv, publish_date = :pd, publish_status = :ps, pin_status = :pn, post_slug = :sg WHERE post_id = :pid";
            $param = array(':tt' => $post_title, ':ct' => $post_content, ':cv' => $post_image, ':pd' => $timePublish, ':ps' => $isPublish, ':pn' => $isPin, ':sg' => $postSlug, ':pid' => $post_id);
            $res = $db->dbQuery($sql, $param);
            if($res!==false){
              $response = array('status' => 1, 'id' => $post_id, 'log' => $post_content);
            }else{
              $response = array('status' => 2, 'msg' => $txt_var['request_error']);
            }
          }else{
            $response = array('status' => 2, 'msg' => $txt_var['request_error']);
          }
        }
      }
    }else{
      $response = array('status' => 2, 'msg' =>  $txt_var['request_error']);
    }
    
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' =>  $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>