<?php
  // session_start();
  //   die();
  require_once __DIR__.'/../admin/assets/php-function/function.php';
  require_once __DIR__.'/../admin/assets/php-function/pdo-database.php';
  require_once __DIR__.'/../admin/assets/message_var/th.php';
  $db = new DatabaseConnection();
  if(true === $showAnnounceMessage || true === $showSponsor || true === $showCover){
    $sql = "SELECT * FROM public_relations ORDER BY pr_date DESC";
    $prData = $db->dbQuery($sql);
    $coverData = $sponsorData = $announceMessageData = array();
    for($i=0; $i < count($prData); $i++){ 
      switch ($prData[$i]['pr_type']) {
        case 1:
          $coverData[] = $prData[$i];
          break;
        case 2:
          $sponsorData[] = $prData[$i];
          break;
        case 3:
          $announceMessageData[] = $prData[$i];
          break;
        default:
          break;
      }
    }
    $prConfig = $siteConfig['pr-config'];
  }
  if(!isset($coverData) || count($coverData)==0)
      $showCover = false;
  if(!isset($sponsorData) || count($sponsorData)==0)
    $showSponsor = false;
  if(!isset($announceMessageData) || count($announceMessageData)==0)
    $showAnnounceMessage = false;

  if(!isset($meta_lang) || $meta_lang == '' || $meta_lang === null)
    $meta_lang = 'th';

  if(!isset($meta_description) || $meta_description == '' || $meta_description === null)
    $meta_description = 'สถานีวิทยุอันดับ 1 95.50 MHz คลื่นนี้มีสิ่งดีๆ ให้คุณ';

  if(!isset($meta_keywords) || $meta_keywords == '' || $meta_keywords === null)
    $meta_keywords = 'สถานีวิทยุ,คลื่นวิทยุ,ฟังเพลงเพราะ,วิทยุออนไลน์,ศิลปิน,ดารา,นักร้อง,ข่าวสาร,วงการบันเทิง,xธานี';

  if(!isset($meta_revisit_after) || $meta_revisit_after == '' || $meta_revisit_after === null)
    $meta_revisit_after = '';

  if(!isset($meta_robots) || $meta_robots == '' || $meta_robots === null)
    $meta_robots = 'index, follow';

  if(!isset($meta_googlebot) || $meta_googlebot == '' || $meta_googlebot === null)
    $meta_googlebot = 'index, follow';

  if(!isset($meta_author) || $meta_author == '' || $meta_author === null)
    $meta_author = '';


  if(!isset($og_url) || $og_url == '' || $og_url === null)
    $og_url = 'https://www.example.com/';
  if(!isset($og_type) || $og_type == '' || $og_type === null)
    $og_type = 'website';
  if(!isset($og_title) || $og_title == '' || $og_title === null)
    $og_title = $appData['about']['app_name'];
  if(!isset($og_description) || $og_description == '' || $og_description === null)
    $og_description = $appData['about']['app_title'];
  if(!isset($og_image) || $og_image == '' || $og_image === null)
    $og_image = '';
  if(!isset($og_image_secure) || $og_image_secure == '' || $og_image_secure === null)
    $og_image_secure = '';
  if(!isset($og_image_type) || $og_image_type == '' || $og_image_type === null)
    $og_image_type = 'image/png';
  if(!isset($og_image_width) || $og_image_width == '' || $og_image_width === null)
    $og_image_width = 512;
  if(!isset($og_image_height) || $og_image_height == '' || $og_image_height === null)
    $og_image_height = 512;
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <meta http-equiv="content-language" content="<?= e($meta_lang) ?>" />
  <meta name="description" content="<?= e($meta_description) ?>" />
  <meta name="keywords" content="<?= e($meta_keywords) ?>" />
  <meta name="revisit-after" content="<?= e($meta_revisit_after) ?>" />
  <meta name="robots" content="<?= e($meta_robots) ?>" />
  <meta name="googlebot" content="<?= e($meta_googlebot) ?>" />
  <meta name="author" content="<?= e($meta_author) ?>" />

  <meta property="og:url"                 content="<?= e($og_url) ?>" />
  <meta property="og:type"                content="<?= e($og_type) ?>" />
  <meta property="og:title"               content="<?= e($og_title) ?>" />
  <meta property="og:description"         content="<?= e($og_description) ?>" />
  <meta property="og:image"               content="<?= e($og_image) ?>" />
  <meta property="og:image:secure_url"    content="<?= e($og_image_secure) ?>" />
  <meta property="og:image:type"          content="<?= e($og_image_type) ?>" />
  <meta property="og:image:width"         content="<?= e($og_image_width) ?>" />
  <meta property="og:image:height"        content="<?= e($og_image_height) ?>" />
  <meta property="og:locale"              content="th_TH" />
  <meta property="fb:app_id"              content="374362739843707" />

  <link rel="apple-touch-icon" sizes="57x57" href="logo/favicon/apple-icon-57x57.png" />
  <link rel="apple-touch-icon" sizes="60x60" href="logo/favicon/apple-icon-60x60.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="logo/favicon/apple-icon-72x72.png" />
  <link rel="apple-touch-icon" sizes="76x76" href="logo/favicon/apple-icon-76x76.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="logo/favicon/apple-icon-114x114.png" />
  <link rel="apple-touch-icon" sizes="120x120" href="logo/favicon/apple-icon-120x120.png" />
  <link rel="apple-touch-icon" sizes="144x144" href="logo/favicon/apple-icon-144x144.png" />
  <link rel="apple-touch-icon" sizes="152x152" href="logo/favicon/apple-icon-152x152.png" />
  <link rel="apple-touch-icon" sizes="180x180" href="logo/favicon/apple-icon-180x180.png" />
  <link rel="icon" type="image/png" sizes="192x192"  href="logo/favicon/android-icon-192x192.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="logo/favicon/favicon-32x32.png" />
  <link rel="icon" type="image/png" sizes="96x96" href="logo/favicon/favicon-96x96.png" />
  <link rel="icon" type="image/png" sizes="16x16" href="logo/favicon/favicon-16x16.png" />
  <meta name="msapplication-TileColor" content="#ffc1d6" />
  <meta name="msapplication-TileImage" content="/ms-icon-144x144.png" />
  <meta name="theme-color" content="#ffc1d6" />

  <title><?= e($pageTitle) ?></title>
  
  <script src="assets/lib/jquery/3.3.1/jquery.min.js"></script>

  <script src="assets/js/app.js"></script>
  <script src="assets/js/main.js"></script>

  <link rel="stylesheet" href="assets/lib/bootstrap/4.3.1/css/bootstrap.min.css" />
  <script src="assets/lib/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
  
  <link rel="stylesheet" href="assets/lib/fontawesome/5.3.1/css/all.min.css" />

  <?php if(true === $showCover){?>
    <link rel="stylesheet" href="assets/lib/slick/1.8.1/slick.css" />
  <script src="assets/lib/slick/1.8.1/slick.min.js"></script>
  <?php }?>

  <?php if(true === $showAnnounceMessage){?>
  <script src="assets/lib/jquery-marquee/1.4.0/jquery.marquee.min.js"></script>
  <?php }?>

  <?php if(isset($addtional_resources)){
    foreach($addtional_resources as $rsc_key => $rsc_value){
      switch($rsc_value[0]){
        case 'js':
          ?><script src="<?= e($rsc_value[1]) ?>"></script><?php
          break;
        case 'css':
          ?><link rel="stylesheet" href="<?= e($rsc_value[1]) ?>" /><?php
          break;
        default: break;
      }
    }
  }?>

  <link rel="stylesheet" href="assets/css/normalize.css?v=1000001" />
  <link rel="stylesheet" href="assets/css/theme.css?v=1000001" />
  <link rel="stylesheet" href="assets/css/template.css?v=1000002" />
  <link rel="stylesheet" href="assets/css/style.css?v=1000001" />
  <!-- <link rel="stylesheet" href="assets/css/post.css" /> -->
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Bai+Jamjuree:300,300i,400,400i,500,500i,700,700i|Pattaya" rel="stylesheet" />
  <!--[if lt IE 9]>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
  <![endif]-->
</head>
<body class="<?= e($htmlBodyClass) ?> bg <?= e($htmlAppearance['bg_class']) ?>" style="background-image: url(<?= e($htmlAppearance['bg_image']) ?>) ;">    
  <header class="header <?= e($headerClass) ?> w-100">

    <div class="hamburger">
      <label for="hamburger-check" class="chk-box text-white d-flex align-items-center justify-content-center cursor-pointer">
        <input type="checkbox" name="hamburger-check" id="hamburger-check" />
        <span class="chk-mark on-true">&#x2715;</span>
        <span class="chk-mark on-false">&#9776;</span>
      </label>
    </div>
    
    <div class="hdr-inner container h-100 position-relative">
      <div class="header-left h-100">
        <div class="hl-inner">
          <a href="index.php" class="main-logo mr-4" title="<?= e($appData['about']['app_name']) ?>">
            <img src="<?= e($appData['about']['logo']) ?>" alt="<?= e($appData['about']['app_name']) ?>" class="h-100" />
          </a>
          <a href="live.php" class="radio-shortcut text-white text-sm d-flex text-decoration-none">
            <!-- <div style="width: 45px; height: 45px;" style="display: none;">
              <svg class="listen-live-svg" viewBox="48 -5.0 51 49" style="display: block; fill: #fff; height: 100%; width: 100%;">
              <title></title>
              <g>
                <path class="st0" d="M1.2,19c0,3.5,1,6.8,2.8,9.6l-0.6,0.6c-1.9-2.9-3-6.4-3-10.1C0.4,8.8,8.7,0.6,18.8,0.6v0.8C9.1,1.4,1.2,9.3,1.2,19z"></path>
                <path class="st0" d="M4,28.6c3.1,4.8,8.6,8.1,14.8,8.1c9.7,0,17.6-7.9,17.6-17.6c0-9.7-7.9-17.6-17.6-17.6V0.6C29,0.6,37.3,8.8,37.3,19c0,10.2-8.3,18.4-18.4,18.4c-6.4,0-12.1-3.3-15.4-8.3L4,28.6z"></path>
                <path class="st0" d="M15.2,11.8L26,18.6c0.3,0.2,0.3,0.6,0,0.7l-10.9,6.8c-0.3,0.2-0.7,0-0.7-0.4V12.2C14.5,11.8,14.9,11.6,15.2,11.8z"></path>    
              </g>
              </svg>
            </div> -->
            <div style="width: 40px;" class="mr-2">
              <img src="assets/img/ui/play-live-radio-icon.png" alt="" class="img-fluid d-block" />
            </div>
            <div class="small">
              <div>ฟังเพลงเพราะกับ</div>
              <div class=""><?= e($appData['about']['app_name']) ?></div>
            </div>
          </a>
        </div>
      </div>
      <nav class="header-right">
        <div class="hr-inner">
          <div class="main-nav" id="main-nav">
            <div class="mn-inner w-100">
              <ul class="menu-nav no-bullets">
                <li><a href="index.php" class="nav-url text-white <?php if($active=='home'){?>active<?php }?>"><span>หน้าแรก</span></a></li>
                <li><a href="top-chart.php" class="nav-url text-white <?php if($active=='topchart'){?>active<?php }?>"><span>เพลงฮิตติดชาร์ท</span></a></li>
                <li><a href="radio-program.php" class="nav-url text-white <?php if($active=='radio-program'){?>active<?php }?>"><span>ตารางจัดรายการ</span></a></li>
                <li><a href="all-post.php" class="nav-url text-white <?php if($active=='post'){?>active<?php }?>"><span>ข่าวสาร</span></a></li>
                <li><a href="contact.php" class="nav-url text-white <?php if($active=='contact'){?>active<?php }?>"><span>ติดต่อเรา</span></a></li>
              </ul>
              <ul class="social-nav no-bullets">
                <?php
                if(isset($appData['social'])){
                  foreach ($appData['social'] as $key => $value){
                ?>
                <li>
                  <a href="<?= e($value['url']) ?>" class="sc nav-url text-white" title="<?= e($value['name']) ?>">
                    <span class="sc-ico"><i class="fab fa-<?= e($value['key']) ?>"></i></span>
                    <span class="sc-txt"><?= e($value['name']) ?></span>
                  </a>
                </li>
                <?php
                  }
                }
                ?>
              </ul>
            </div>
          </div>
        </div>
      </nav>
    </div>
  </header>
  <?php if(true === $showCover){?>
  <div class="cover-container">
    <div id="cover" class="page-cover mx-auto position-relative">
    <?php foreach($coverData as $key => $value){?>
    <div class="cover-item">
      <a href="<?= e($value['pr_url']) ?>" class="cover-link d-block" target="_blank" title="<?= e($value['pr_title']) ?>" rel="nofollow">
        <figure class="m-0">
          <img src="<?= e($prConfig['cover_url'] . $value['pr_image']) ?>" alt="<?= e($value['pr_title']) ?>" class="w-100 d-block" target="_blank" title="<?= e($value['pr_title']) ?>" rel="nofollow" />
        </figure>
      </a>
    </div>
    <?php }?>
    </div>
  </div>
  <?php }?>
  <?php if(true === $showAnnounceMessage){?>
    <div class="container px-0">
      <div class="announce-msg-container">
        
      <div id="announce-msg" class="announce-msg">
      <?php
      foreach($announceMessageData as $key => $value){
      ?>
      <a href="<?= e($value['pr_url']) ?>" target="_blank" title="<?= e($value['pr_title']) ?>" rel="nofollow" class="small text-white text-decoration-none">
        <?= e($value['pr_message']) ?>
      </a>
      <?php if($key != count($announceMessageData)-1){?>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <?php
        } 
      }
      ?>
      </div>
      </div>
    </div>
  <?php }?>

<?php if($siteMode=="normal"){?>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/th_TH/sdk.js#xfbml=1&version=v3.3&appId=374362739843707&autoLogAppEvents=1"></script>
  <main>
    <div class="container bg-white pt-3">
      <div class="row">
        <div class="<?php if(true === $showSidebar){?>col-md-9 col-lg-9 order-md-last<?php }else{?>col<?php }?>">
<?php }?>
