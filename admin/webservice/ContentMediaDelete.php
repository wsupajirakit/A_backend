<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';

  $db = new DatabaseConnection();

  $mdFile = $_POST['media_file'];
  $mdType = $_POST['media_type'];

  if($adminUser !== null){
      $postConfig = $siteConfig['post-config'];
      if($mdType == 'image'){
        $listFile = explode(",", $mdFile);
        $uploadDir = __DIR__."/../../public/rsc/content/img/";
        for($i=0; $i < count($listFile); $i++){ 
         // Only plain file names inside the upload dir (no path traversal)
         if(!preg_match('/^[A-Za-z0-9_-]+\.[A-Za-z0-9]{1,5}$/', $listFile[$i])){ continue; }
         $targetFile = $uploadDir . $listFile[$i];
         if(file_exists($targetFile)){
          unlink($targetFile);
         }
        }
        $response = array('status' => 1, 'msg' => $txt_var['request_success']);
        
      }else{
        $response = array('status' => 2, 'msg' => $txt_var['request_error']);
      }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>