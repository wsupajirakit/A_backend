<?php
  header("Content-type: application/json");
  require_once __DIR__.'/../assets/php-function/pdo-database.php';
  require_once __DIR__.'/../bootstrap.php';
  require_once __DIR__.'/../assets/php-function/function.php';
  require_once __DIR__.'/../assets/message_var/th.php';


  $db = new DatabaseConnection();
  $pr_type = (int) ($_GET['pr_type'] ?? 0);

  if($adminUser !== null){
    $prConfig = $siteConfig['pr-config'];
    switch($pr_type){
      case 1:
        $result = array();
        $sql = "SELECT pr_id, pr_image, pr_url, pr_title FROM public_relations WHERE pr_type = 1 ORDER BY pr_date DESC";
        $data = $db->dbQuery($sql);
        if($data!==null){
          foreach ($data as $key => $value) {
           $data[$key]['pr_image'] = $prConfig['cover_url'] . $data[$key]['pr_image'];
           $data[$key]['pr_title'] = htmlspecialchars_decode($data[$key]['pr_title']);
          }
          $result = $data;  
        }
        $response = array('status' => 1, 'result' => $result, 'config' => $prConfig);
        break;
      case 2:
        $result = array();
        $sql = "SELECT pr_id, pr_image, pr_url, pr_title FROM public_relations WHERE pr_type = 2 ORDER BY pr_date DESC";
        $data = $db->dbQuery($sql);
        if($data!==null){
          foreach ($data as $key => $value) {
           $data[$key]['pr_image'] = $prConfig['sponsor_url'] . $data[$key]['pr_image'];
           $data[$key]['pr_title'] = htmlspecialchars_decode($data[$key]['pr_title']);
          }
          $result = $data;  
        }
        $response = array('status' => 1, 'result' => $result, 'config' => $prConfig);
        break;
      case 3:
        $result = array();
        $sql = "SELECT pr_id, pr_message, pr_url FROM public_relations WHERE pr_type = 3 ORDER BY pr_date DESC";
        $data = $db->dbQuery($sql);
        if($data!==null){
          foreach ($data as $key => $value) {
           $data[$key]['pr_message'] = htmlspecialchars_decode($data[$key]['pr_message']);
          }
          $result = $data;  
        }
        $response = array('status' => 1, 'result' => $result);
        break;
      case 4:
        $result = array();
        $sql = "SELECT pr_id, pr_image, pr_url, pr_title FROM public_relations WHERE pr_type = 4 ORDER BY pr_date DESC";
        $data = $db->dbQuery($sql);
        if($data!==null){
          foreach ($data as $key => $value) {
           $data[$key]['pr_image'] = $prConfig['app_popup_url'] . $data[$key]['pr_image'];
           $data[$key]['pr_title'] = htmlspecialchars_decode($data[$key]['pr_title']);
          }
          $result = $data;  
        }
        $response = array('status' => 1, 'result' => $result, 'config' => $prConfig);
        break;
      default:
        $response = array('status' => 2,);
        break;
    }
  }else{
    // if refresh token invalid
    $response = array('status' => 0, 'msg' => $txt_var['request_error_authen']);
  }
  echo json_encode($response);
?>