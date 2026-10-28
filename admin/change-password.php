<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "myprofile";
  $active_sm = "change-password";
  $enableSearch = false;
  $breadcrumbList = array(
    array($txt_var['profile'], 'change-password.php', 'profie'),
    array($txt_var['change_password'], 'change-password.php', 'change password'),
  );
  $addtional_resources = array(
    array('js', 'assets/js/form.js'),
    array('js', 'assets/js/profile.js'),
  );
  require 'header.php'; 
?>
<div class="row">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <h3><?=$txt_var['change_password'];?></h3>
      <hr>
      <div>
        <form class="needs-validation" novalidate method="post" action="webservice/ProfileChangePassword.php" id="FormChangePassword" data-clear-after-success="yes">
          <fieldset>
            <div class="form-row">
              <div class="col-md-6 mx-auto">
                <div class="form-group">
                  <label for="password"><?=$txt_var['password'];?></label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="<?=$txt_var['password'];?>" required pattern="[\w\s!#\$%&\(\)*\+,\-\./:;<=>\?@\[\\\]\^_`\{\|\}~\x22\x27]{6,60}" maxlength="60" />
                  <div class="invalid-feedback"><?=$txt_var['invalid_password'];?></div>
                </div>
                <div class="form-group">
                  <label for="new_password"><?=$txt_var['new_password'];?></label>
                  <input type="password" class="form-control" id="new_password" name="new_password" placeholder="<?=$txt_var['new_password'];?>" required pattern="[\w\s!#\$%&\(\)*\+,\-\./:;<=>\?@\[\\\]\^_`\{\|\}~\x22\x27]{6,60}" maxlength="60" />
                  <div class="invalid-feedback"><?=$txt_var['invalid_password'];?></div>
                </div>
                
                <div class="form-group">
                  <label for="confirm_password"><?=$txt_var['confirm_password'];?></label>
                  <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="<?=$txt_var['confirm_password'];?>" required pattern="[\w\s!#\$%&\(\)*\+,\-\./:;<=>\?@\[\\\]\^_`\{\|\}~\x22\x27]{6,60}" maxlength="60" />
                  <div class="invalid-feedback"><?=$txt_var['invalid_password'];?></div>
                </div>
                <div class="form-group">
                  <small id="passwordHelp" class="form-text text-muted">* <?=$txt_var['password_help'];?></small>
                </div>
               
                <div class="form-group">
                  <button class="btn btn-primary" type="submit"><?=$txt_var['save'];?></button>
                </div>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
  require 'footer.php';
?>