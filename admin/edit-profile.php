<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "myprofile";
  $active_sm = "edit-profile";
  $enableSearch = false;
  $breadcrumbList = array(
    array($txt_var['profile'], 'edit-profile.php', 'profie'),
    array($txt_var['update'], 'edit-profile.php', 'edit profie'),
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
      <h3><?=$txt_var['edit_profile'];?></h3>
      <hr>
      <div>
        <!-- <form class="needs-validation" novalidate method="post" action="#" id="form-profile-image">
          <fieldset>
            <div class="form-row">
              <div class="col-md-6 mx-auto">
                <h5>Profile image</h5>
                <input type="file" name="NewProfileImage" id="NewProfileImage" required class="d-none" accept="image/png,image/jpg,image/jpeg,image/gif" />
                <div class="form-group">
                  <button class="btn btn-primary" id="SelectProfileImage"><i class="fas fa-folder-open mr-2"></i>Select</button>
                </div>
              </div>
            </div>
          </fieldset>
        </form> -->
        <form class="needs-validation" novalidate method="post" action="webservice/ProfileInfoUpdate.php" id="FormProfileInfo">
          <fieldset>
            <div class="form-row">
              <div class="col-md-6 mx-auto">
                <h5><?=$txt_var['profile_info'];?></h5>
                <br />
                <div class="form-group">
                  <label for="fullname"><?=$txt_var['fname_lname'];?></label>
                  <input type="text" class="form-control" id="fullname" name="fullname" placeholder="<?=$txt_var['fname_lname'];?>" required pattern="[^&<>]+" maxlength="100" value="<?= e($userData['fullname']) ?>" />
                  <div class="invalid-feedback"><?=$txt_var['name_invalid'];?></div>
                </div>
                <div class="form-group">
                  <label for="email"><?=$txt_var['email'];?></label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="<?=$txt_var['email'];?>" required maxlength="100" value="<?= e($userData['email']) ?>" />
                  <div class="invalid-feedback"><?=$txt_var['email_invalid'];?></div>
                </div>
                <div class="form-group">
                  <label for="phone"><?=$txt_var['phone'];?></label>
                  <input type="tel" class="form-control" id="phone" name="phone" placeholder="<?=$txt_var['phone'];?>" required pattern="[\d\+\-\(\)]+" maxlength="15" value="<?= e($userData['phone']) ?>" />
                  <div class="invalid-feedback"><?=$txt_var['phone_help'];?></div>
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
<script>
      // Example starter JavaScript for disabling form submissions if there are invalid fields
      // (function() {
      //   'use strict';
      //   window.addEventListener('load', function() {
      //     // Fetch all the forms we want to apply custom Bootstrap validation styles to
      //     var forms = document.getElementsByClassName('needs-validation');
      //     // Loop over them and prevent submission
      //     var validation = Array.prototype.filter.call(forms, function(form) {
      //       form.addEventListener('submit', function(event) {
      //           event.preventDefault();
      //           event.stopPropagation();
      //         form.classList.add('was-validated');
      </script>
<?php
  require 'footer.php';
?>
