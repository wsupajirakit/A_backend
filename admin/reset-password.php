<?php
  require_once __DIR__.'/bootstrap.php';
  require_once __DIR__.'/assets/php-function/function.php';
  require_once __DIR__.'/assets/message_var/th.php';
  require_once __DIR__.'/assets/php-function/pdo-database.php';
  $db = new DatabaseConnection();
  $em = trim($_GET['email']);
  $code = $_GET['refcode'];
  $now = strtotime('now');
  $checkValidity = true;
  $userData = $db->dbRow("SELECT * FROM user WHERE email = :em AND active_status = 1", array(':em' => $em));
  if($userData!==null){
    $refFile = "data/user-token/reset-password/" . $userData['user_id'] . ".json";
    if(file_exists($refFile)){
      $myfile = fopen($refFile, "r");
      $refData = fread($myfile, filesize($refFile));
      $refData = json_decode($refData, true);
      fclose($myfile);
      if($refData['token']!=$code || $now > $refData['expiration_stamp'] ||  $refData['user_id'] != $userData['user_id']){
        $checkValidity = false;
      }
    }else{
      $checkValidity = false;
    }
  }else{
    $checkValidity = false;
  }
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

  <title><?=$txt_var['change_password'];?> : <?=$txt_var['system_title']. " - " .$siteConfig['web-config']['app_name'];?></title>
  <script src="assets/lib/jquery/3.3.1/jquery.min.js"></script>
  <link rel="stylesheet" href="assets/lib/bootstrap/4.1.3/css/bootstrap.min.css" />
  <script src="assets/lib/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/lib/fontawesome/5.3.1/css/all.min.css" />
  
  <script src="assets/js/form.js"></script>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/single-page.css" />
  <link rel="stylesheet" href="assets/css/loader.css" />
  <script src="assets/js/main.js"></script>
  <script>
    var txt_var = {};
    txt_var.request_error = "<?=$txt_var['request_error'];?>";
    (function(){
      $(document).on('submit', '#FormResetPassword', function(event){
        $('#confirm-password, #new-password').removeClass('border-danger');
        ajaxFormSubmit(event).then(function(response){
        }, function(error){
          if(error.status == 'success' && error.response.status == 3){
            $('#confirm-password, #new-password').addClass('border-danger');
          }
        });
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
        <div class="col-md-6 offset-md-3 h-100">
          <div class="d-flex align-items-center sign-container" style="width: 100%;">
            <div>
              <div class="p-3 bg-white">
                <?php if($checkValidity===true){?>
                <div class="mb-3 text-muted d-flex align-items-center">
                  <figure class="m-0 mr-2">
                    <img src="../logo/favicon/favicon-32x32.png" alt="<?=$siteConfig['web-config']['app_name'];?>" class="d-block" />
                  </figure>
                  <small class="small-2"><?=$txt_var['system_title']. " - " .$siteConfig['web-config']['app_name'];?></small>
                </div>
                <h5 class="mb-4"><?=$txt_var['change_password'];?></h5>
                <form id="FormResetPassword" action="webservice/ResetPassword_Submit.php" method="post" novalidate data-notice="message" class="needs-validation">
                  <div class="alert alert-success d-none" role="alert" id="resetPassword-alert"></div>
                  <fieldset>
                    <input type="hidden" name="refcode" value="<?=$code;?>" />
                    <input type="hidden" name="email" value="<?=$em;?>" />
                    <div class="form-group">
                      <label for="new-password"><?=$txt_var['new_password'];?></label>
                      <input type="password" class="form-control" id="new-password" name="new-password" placeholder="<?=$txt_var['new_password'];?>" required pattern="[\w\s!#\$%&\(\)*\+,\-\./:;<=>\?@\[\\\]\^_`\{\|\}~\x22\x27]{6,60}" />
                      <div class="invalid-feedback"><?=$txt_var['invalid_password'];?></div>
                    </div>
                    <div class="form-group">
                      <label for="confirm-password"><?=$txt_var['confirm_password'];?></label>
                      <input type="password" class="form-control" id="confirm-password" name="confirm-password" placeholder="<?=$txt_var['confirm_password'];?>" required pattern="[\w\s!#\$%&\(\)*\+,\-\./:;<=>\?@\[\\\]\^_`\{\|\}~\x22\x27]{6,60}" />
                      <div class="invalid-feedback"><?=$txt_var['invalid_password'];?></div>
                    </div>
                    <div class="form-group">
                      <small id="passwordHelp" class="form-text text-muted">* <?=$txt_var['password_help'];?></small>
                    </div>
                  
                    <button type="submit" class="btn btn-primary"><?=$txt_var['change_password'];?></button>
                    <div class="form-group mt-3 mb-0">
                      <a href="signin.php"><?=$txt_var['signin'];?></a>
                    </div>
                  </fieldset>
                </form>
                <?php }else{?>
                  <h2 class="text-center text-warning"><?=$txt_var['sorry'];?>!</h2>
                  <br />
                  <p><?=$txt_var['reset_password_code_invalid'];?> <a href="forget-password.php"><?=$txt_var['click_here'];?></a></p>
                  <br />
                  <div class="form-group mt-3 mb-0">
                    <span><?=$txt_var['or'];?> </span><a href="signin.php"><?=$txt_var['signin'];?></a>
                  </div>
                <?php }?>
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