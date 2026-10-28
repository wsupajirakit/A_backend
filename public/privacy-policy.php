<?php
  require_once __DIR__.'/initialize.php';
  require_once __DIR__.'/../admin/assets/php-function/function.php';

  $active = "privacy-policy";
  $siteMode = "normal";
  $htmlBodyClass = "";
  $htmlAppearance = $appearanceData["default"];
  $headerClass = "main-gradient shadow-sm";
  $showCover = false;
  $showAnnounceMessage = false;
  $showSponsor = false;
  $showSidebar = false;
  $pageTitle = "นโยบายความเป็นส่วนตัว" . " : " . $appData['about']['app_title'];

  $meta_lang = '';
  $meta_description = 'นโยบายความเป็นส่วนตัว '. $appData['about']['app_name'] . ' ' . $appData['about']['organization_name'];
  $meta_keywords = $meta_keywords_default . ',' . 'นโยบาย,ความเป็นส่วนตัว';
  $meta_revisit_after = '1 month';
  $meta_robots = '';
  $meta_googlebot = '';
  $meta_author = '';

  $og_url = 'https://www.example.com/privacy-policy.php';
  $og_type = 'website';
  $og_title = "นโยบายความเป็นส่วนตัว - " . $appData['about']['app_name'];
  $og_description = '';
  $og_image = '';
  $og_image_secure = '';
  $og_image_type = '';
  $og_image_width = '';
  $og_image_height = '';

  include 'header.php';

  $platform = (string) ($_GET['platform'] ?? '');
?>

<div class="mb-3">
  <?php
  if($platform == 'app'){
    include 'privacy-policy-app.html';
  }else{
    include 'privacy-policy-web.html';
  }
    
  ?>
  
</div>

<?php
  include 'footer.php';
?>