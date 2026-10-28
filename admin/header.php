<?php
  require_once __DIR__.'/check_user_session.php';
  require_once __DIR__.'/assets/php-function/pdo-database.php';
  require_once __DIR__.'/assets/php-function/function.php';
  require_once __DIR__.'/assets/message_var/th.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="csrf-token" content="<?= e($csrfToken) ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />

  <link rel="apple-touch-icon" sizes="57x57" href="../logo/favicon/apple-icon-57x57.png" />
  <link rel="apple-touch-icon" sizes="60x60" href="../logo/favicon/apple-icon-60x60.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="../logo/favicon/apple-icon-72x72.png" />
  <link rel="apple-touch-icon" sizes="76x76" href="../logo/favicon/apple-icon-76x76.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="../logo/favicon/apple-icon-114x114.png" />
  <link rel="apple-touch-icon" sizes="120x120" href="../logo/favicon/apple-icon-120x120.png" />
  <link rel="apple-touch-icon" sizes="144x144" href="../logo/favicon/apple-icon-144x144.png" />
  <link rel="apple-touch-icon" sizes="152x152" href="../logo/favicon/apple-icon-152x152.png" />
  <link rel="apple-touch-icon" sizes="180x180" href="../logo/favicon/apple-icon-180x180.png" />
  <link rel="icon" type="image/png" sizes="192x192"  href="../logo/favicon/android-icon-192x192.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="../logo/favicon/favicon-32x32.png" />
  <link rel="icon" type="image/png" sizes="96x96" href="../logo/favicon/favicon-96x96.png" />
  <link rel="icon" type="image/png" sizes="16x16" href="../logo/favicon/favicon-16x16.png" />
  <meta name="msapplication-TileColor" content="#ffffff" />
  <meta name="msapplication-TileImage" content="/ms-icon-144x144.png" />
  <meta name="theme-color" content="#ffffff" />

  <title><?=$txt_var['system_title']. " - " .$siteConfig['web-config']['app_name'];?></title>

  <script src="assets/lib/jquery/3.3.1/jquery.min.js"></script>
  <script src="assets/lib/popper/1.14.3/popper.min.js"></script>

  <link rel="stylesheet" href="assets/lib/bootstrap/4.1.3/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/lib/bootstrap/bootstrap-custom.css" />
  <script src="assets/lib/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  
  <link rel="stylesheet" href="assets/lib/fontawesome/5.3.1/css/all.min.css" />

  <link rel="stylesheet" href="assets/lib/simplebar/2.6.1/simplebar.css" />
  <script src="assets/lib/simplebar/2.6.1/simplebar.js"></script>  

  <link rel="stylesheet" href="assets/css/style.css?v=1000001" />
  <link rel="stylesheet" href="assets/css/template.css?v=1000001" />
  <link rel="stylesheet" href="assets/css/page.css?v=1000001" />
  <link rel="stylesheet" href="assets/css/loader.css?v=1000001" />

  <?php if(isset($addtional_resources)){
    foreach($addtional_resources as $rsc_key => $rsc_value){
      switch($rsc_value[0]){
        case 'js':
          ?><script src="<?=$rsc_value[1];?>"></script><?php
          break;
        case 'css':
          ?><link rel="stylesheet" href="<?=$rsc_value[1];?>" /><?php
          break;
        default: break;
      }
    }
  }?>

  <script src="assets/lib/masonry/4.2.2/masonry.pkgd.min.js"></script>
  <script src="assets/lib/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
  <script src="assets/lib/jquery-confirm/3.3.4/jquery-confirm-default.js"></script>
  <link rel="stylesheet" href="assets/lib/jquery-confirm/3.3.4/jquery-confirm.min.css" />
  <link rel="stylesheet" href="assets/lib/jquery-confirm/3.3.4/jquery-confirm-custom.css" />
  
  <script src="assets/js/index.js"></script>
  <script src="assets/js/main.js"></script>
  
  <script>
  var txt_var = {};
  txt_var.confirm         = "<?=$txt_var['confirm'];?>";
  txt_var.ok              = "<?=$txt_var['ok'];?>";
  txt_var.cancel          = "<?=$txt_var['cancel'];?>";
  txt_var.request_error   = "<?=$txt_var['request_error'];?>";
  txt_var.error           = "<?=$txt_var['error'];?>";
  txt_var.request_success = "<?=$txt_var['request_success'];?>";
  txt_var.success         = "<?=$txt_var['success'];?>";
  txt_var.close           = "<?=$txt_var['close'];?>";
  txt_var.save            = "<?=$txt_var['save'];?>";
  txt_var.add_new            = "<?=$txt_var['add_new'];?>";
  txt_var.update            = "<?=$txt_var['update'];?>";
  txt_var.delete            = "<?=$txt_var['delete'];?>";
  </script>

  <script>
    (function(){
      var refreshDataTime = 60000;
      delayRefreshUserData(refreshDataTime);

      function refreshUserData(){
        var $defer = $.Deferred();
        $.ajax({
          url: 'webservice/CheckRefresh.php',
          type: 'POST',
          dataType: 'json',
        })
        .done(function(response){
          if(response.status==0){
            $defer.reject(false);
            alert("Session expire!");
            window.location.href = 'logout.php';
          }else if(response.status==1){
            refreshDataTime = ((response.nextRefresh) - 60) * 1000;
            delayRefreshUserData(refreshDataTime);
            $defer.resolve(true);
            $defer.reject(false);
          }else{
            $defer.reject(false);
            delayRefreshUserData(5000);
          }
        })
        .fail(function(xhr){
          $defer.reject(false);
          if(xhr.status==401){ window.location.href = 'logout.php'; return; }
          delayRefreshUserData(5000);
        })
        return $defer.promise();
      }

      function delayRefreshUserData(time){
        setTimeout(()=>{
          refreshUserData().then(function(){}, function(){});
        }, time);
      }
    })();
  </script>

  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Bai+Jamjuree:300,300i,400,400i,500,500i,700,700i|Pattaya" rel="stylesheet" />
  
  <!--[if lt IE 9]>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
  <![endif]-->
</head>
<body id="body">
  <div>
    <?php include 'sidenav.php'; ?>
    <section class="page-container">
    <?php include 'topnav.php'; ?>
      <!-- breadcrumb navigation -->
      <div class="container-fluid mt-3">
        <div class="row">
          <div class="col">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0 py-1 text-sm">
                <?php
                  if(isset($breadcrumbList)){
                    for ($i=0; $i < count($breadcrumbList); $i++){ 
                      if($i==count($breadcrumbList)-1){
                        ?>
                          <li class="breadcrumb-item active" aria-current="<?=$breadcrumbList[$i][2];?>">
                            <?=$breadcrumbList[$i][0];?>
                          </li>
                        <?php
                      }else{
                        ?>
                          <li class="breadcrumb-item">
                            <a href="<?=$breadcrumbList[$i][1];?>"><?=$breadcrumbList[$i][0];?></a>
                          </li>
                        <?php
                      }
                    }
                  }
                ?>
              </ol>
            </nav>
          </div>
        </div>
      </div>
      
      <main ng-app="adminApp">
        <div class="container-fluid">