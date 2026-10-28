<?php
  require_once __DIR__.'/bootstrap.php';
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
  
  <title><?=$txt_var['forget_password'];?> : <?=$txt_var['system_title']. " - " .$siteConfig['web-config']['app_name'];?></title>
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
      $(document).on('submit', '#FormForgetPassword', function(event){
        ajaxFormSubmit(event);
      });
    })();
  </script>

  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Bai+Jamjuree:300,300i,400,400i,500,500i,700,700i|Pattaya" rel="stylesheet" />
</head>
<body class="cover-bg">
  <div class="container-fluid px-0 h-100">
    <div class="container h-100">
      <div class="row h-100">
        <div class="col-md-6 mx-auto h-100">
          <div class="d-flex align-items-center sign-container w-100">
            <div>
              <div class="p-3 bg-white">
                <div class="mb-3 text-muted d-flex align-items-center">
                  <figure class="m-0 mr-2">
                    <img src="../logo/favicon/favicon-32x32.png" alt="<?=$siteConfig['web-config']['app_name'];?>" class="d-block" />
                  </figure>
                  <small class="small-2"><?=$txt_var['system_title']. " - " .$siteConfig['web-config']['app_name'];?></small>
                </div>
                <h5 class="mb-4"><?=$txt_var['forget_password'];?></h5>
                <p><?=$txt_var['reset_password_help'];?></p>
                <form id="FormForgetPassword" method="post" action="webservice/ResetPassword.php" data-notice="message">
                  <div class="alert alert-success d-none" role="alert" id="forgetPassword-alert"></div>
                  <fieldset>
                    <div class="form-group">
                      <label for="email"><?=$txt_var['email'];?></label>
                      <input type="text" class="form-control" id="email" name="email" placeholder="<?=$txt_var['email'];?>" />
                    </div>
                    <button type="submit" class="btn btn-primary"><?=$txt_var['confirm'];?></button>
                    <div class="form-group mt-3 mb-0">
                      <a href="signin.php"><?=$txt_var['signin'];?></a>
                    </div>
                  </fieldset>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'loader_dom.html'; ?>
</body>
</html>