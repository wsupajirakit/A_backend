<?php
  require_once __DIR__.'/check_user_insession.php';
  require_once __DIR__.'/assets/php-function/function.php';
  require_once __DIR__.'/assets/message_var/th.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="<?= e($csrfToken) ?>">
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
  
  <title><?=$txt_var['signin'];?> : <?=$txt_var['system_title']. " - " .$siteConfig['web-config']['app_name'];?></title>
  <script src="assets/lib/jquery/3.3.1/jquery.min.js"></script>
  <link rel="stylesheet" href="assets/lib/bootstrap/4.1.3/css/bootstrap.min.css" />
  <script src="assets/lib/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/lib/fontawesome/5.3.1/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/single-page.css" />
  <link rel="stylesheet" href="assets/css/loader.css" />
  <script src="assets/js/main.js"></script>
  <script src="assets/js/form.js"></script>
  <script>
    var txt_var = {};
    txt_var.request_error = "<?=$txt_var['request_error'];?>";
    (function(){
      $(document).on('submit', '#FormSignin', function(event){
        ajaxFormSubmit(event).then((res)=>{
          $('.alert-success').hide();
          window.location.href = 'index.php';
        });
      });
    })();
  </script>

  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Bai+Jamjuree:300,300i,400,400i,500,500i,700,700i|Pattaya" rel="stylesheet" />
</head>
<body class="cover-bg">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4 offset-md-8 bg-white sign-container">
        <div class="py-3 px-2 mt-5">
          <div class="mb-3 text-muted d-flex align-items-center">
            <figure class="m-0 mr-2">
              <img src="../logo/favicon/favicon-32x32.png" alt="<?=$siteConfig['web-config']['app_name'];?>" class="d-block" />
            </figure>
            <small class="small-2"><?=$txt_var['system_title']. " - " .$siteConfig['web-config']['app_name'];?></small>
          </div>
          <h5 class="mb-4"><?=$txt_var['signin'];?></h5>
          <form id="FormSignin" method="post" action="webservice/CheckLogin.php" data-notice="message">
          <div class="alert alert-danger d-none" role="alert" id="signin-alert"></div>
            <fieldset>
              <div class="form-group">
                <label for="username"><?=$txt_var['username'];?></label>
                <input type="text" class="form-control" id="username" name="username" placeholder="<?=$txt_var['username'];?>" />
              </div>
            
              <div class="form-group">
                <label for="password"><?=$txt_var['password'];?></label>
                <input type="password" class="form-control" id="password" name="password" placeholder="<?=$txt_var['password'];?>" />
              </div>
              <div class="form-group">
                <a href="forget-password.php"><?=$txt_var['forget_password'];?>?</a>
              </div>  
  
              <button type="submit" class="btn btn-primary"><?=$txt_var['signin'];?></button>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php include 'loader_dom.html'; ?>
</body>
</html>