<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';
  $offset = max(0, (int) ($_GET['offset'] ?? 0));
  $limit = max(1, min(200, (int) ($_GET['limit'] ?? 20)));
  if($adminUser !== null){
    $postConfig = $siteConfig['post-config'];
    $extensions = explode(",", $postConfig['img_ext']);
    $mediaImageDirectory = __DIR__.'/../../public/rsc/content/img/';
    $result = array();
    $directory = new DirectoryIterator($mediaImageDirectory);
    // iterate
    foreach($directory as $fileinfo){
      if($fileinfo->isFile()){
        $extension = getFileExtension($fileinfo->getFilename());
        if(in_array($extension, $extensions)){
          $result[$fileinfo->getFilename()] = filemtime($mediaImageDirectory . $fileinfo->getFilename());
        }
      }
    }
    arsort($result);
    $resultFiles = array_keys($result);
    $resultFiles = array_slice($resultFiles,  $offset, $limit);
    $response = array('status' => 1, 'result' => array('image' => $resultFiles));
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' =>  $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>