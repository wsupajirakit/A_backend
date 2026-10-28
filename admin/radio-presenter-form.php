<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "radio-presenter";
  $action = $_GET['action'];
  $breadcrumbList = array(
    array($txt_var['radio_presenter'], 'radio-presenter.php', 'radio-presenter'),
  );
  if($action=='new'){
    $active_sm = "add-radio-presenter";
    $breadcrumbList[] = array($txt_var['add_new'], 'radio-presenter-form.php?action=new', 'add radio presenter');
  }
  else if($action=='update'){
    $active_sm = "update-radio-presenter";
    $breadcrumbList[] = array($txt_var['update'], 'radio-presenter-form.php?action=update', 'update radio presenter');
  }
  $enableSearch = false;
  
  $addtional_resources = array(
    array('js', 'assets/lib/cropper/4.0.0/cropper.min.js'),
    array('css', 'assets/lib/cropper/4.0.0/cropper.min.css'),
    array('css', 'assets/lib/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css'),
    array('css', 'assets/lib/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css'),
    array('js', 'assets/lib/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js'),
    array('js', 'assets/lib/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.th.min.js'),
    array('js', 'assets/js/rp.js'),
    array('js', 'assets/js/form.js'),
    array('js', 'assets/js/imageProcess.js'),
  );
  require 'header.php'; 
  $rpConfig = $siteConfig['radiopresenter-config'];
?>
<script>
  txt_var.upload_image_error = "<?=$txt_var['upload_image_error'];?>";
  txt_var.crop_img = "<?=$txt_var['crop_img'];?>";
  txt_var.delete_data = "<?=$txt_var['delete_data'];?>";
  txt_var.delete_data_confirm = "<?=$txt_var['delete_data_confirm'];?>";
  txt_var.image_size_notice_min = "<?=$txt_var['image_size_notice_min'];?>";
  txt_var.image_size_notice_max = "<?=$txt_var['image_size_notice_max'];?>";
  txt_var.image_size_notice_between = "<?=$txt_var['image_size_notice_between'];?>";
  txt_var.image_size_notice_exact = "<?=$txt_var['image_size_notice_exact'];?>";
</script>
<div class="row">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <?php if($action=='new'){?>
      <h3><?=$txt_var['add_radio_presenter'];?></h3>
      <?php }else if($action=='update'){?>
      <h3><?=$txt_var['update_radio_presenter'];?></h3>
      <?php }?>
      <hr>
      <div>
        <div class="row">
          <div class="col-md-6 mx-auto">
            <?php if($action=='new'){?>
              <form class="needs-validation" novalidate method="post" action="webservice/RadioPresenterSubmit.php" id="FormRadioPresenter" data-notice="dialog,popup">
                <fieldset>
                  <div class="form-row">
                    <div class="col-12">

                      <div class="form-row">
                        <div class="col-md-12 mb-2">
                          <div><?=$txt_var['profile_image'];?></div>
                        </div>
                      </div>
                      <div class="form-row" id="rp-img-tag" style="display: none;">
                        <div class="col-md-4 mb-2">
                          <figure class="m-0 w-100"></figure>
                        </div>
                      </div>
                      <div class="w-100"></div>  
                      <div class="form-row">
                        <div class="col-12 mb-3">
                          <input type="file" name="rp-img" id="rp-img" class="d-none" accept="<?= e(createInputFileAcceptAttr($rpConfig['rp_ext'])) ?>" />
                          <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('rp-img').click();"><?=$txt_var['select_image'];?></button>
                        </div>
                      </div>
    
                      <label for="rp_name"><?=$txt_var['radio_presenter_name'];?></label>
                      <div class="form-group">
                        <input type="text" class="form-control" name="rp_name" id="rp_name" required maxlength="100" placeholder="<?=$txt_var['radio_presenter_name'];?>"> 
                      </div>
  
                      <label for="rp_bdate"><?=$txt_var['birthdate'];?></label>
                      <div class="form-group">
                        <input type="text" name="rp_bdate" id="rp_bdate" class="form-control" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" required autocomplete="off"  placeholder="<?=$txt_var['birthdate'];?>" />
                      </div>

                      <div class="form-group">
                        <button class="btn btn-primary" type="submit"><?=$txt_var['save'];?></button>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </form>
            <?php
            }else if($action=='update'){
              $rpData = $db->dbRow("SELECT * FROM radio_presenter WHERE rp_id = :id", array(':id' => $_GET['id']));
              if(null !== $rpData){
                $bd = date("d/m/Y", strtotime($rpData['rp_birthdate']));
            ?>
            <form class="needs-validation" novalidate method="post" action="webservice/RadioPresenterUpdate.php" id="FormRadioPresenter">
              <fieldset>
                <input type="hidden" name="id" value="<?= e($_GET['id']) ?>" />
                <input type="hidden" name="update" value="info" />
                <input type="hidden" name="tmpRpImage" id="tmpRpImage" value="<?= e($rpData['rp_image']) ?>" />
                <div class="form-row">
                  <div class="col-12">
                    <div class="form-row">
                      <div class="col-md-12 mb-2">
                        <div><?=$txt_var['profile_image'];?></div>
                      </div>
                    </div>
                    <div class="form-row" id="rp-img-tag">
                      <div class="col-md-4 mb-2">
                        <figure class="m-0 w-100">
                          <img src="<?= e($rpConfig['rp_url'] . $rpData['rp_image']) ?>" class="w-100" />
                        </figure>
                      </div>
                    </div>
                    <div class="w-100"></div>  
                    <div class="form-row">
                      <div class="col-12 mb-3">
                        <input type="file" name="rp-img" id="rp-img" class="d-none" accept="<?= e(createInputFileAcceptAttr($rpConfig['rp_ext'])) ?>" />
                        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('rp-img').click();"><?=$txt_var['select_image'];?></button>
                      </div>
                    </div>

                    <label for="rp_name"><?=$txt_var['radio_presenter_name'];?></label>
                    <div class="form-group">
                      <input type="text" class="form-control" name="rp_name" id="rp_name" required maxlength="100" placeholder="<?=$txt_var['rp_name'];?>" value="<?= e($rpData['rp_name']) ?>" /> 
                    </div>

                    <label for="rp_bdate"><?=$txt_var['birthdate'];?></label>
                    <div class="form-group">
                      <input type="text" name="rp_bdate" id="rp_bdate" class="form-control" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" required autocomplete="off"  placeholder="<?=$txt_var['birthdate'];?>" value="<?= e($bd) ?>" />
                    </div>

                    <div class="form-group">
                      <button class="btn btn-primary" type="submit"><?=$txt_var['save'];?></button>
                    </div>
                  </div>
                </div>
              </fieldset>
            </form>
            <?php
              }else{
                $_404 = file_get_contents('__data-404-tmpl.html');
                $_404_pm = array('{{_back_}}' => $txt_var['back'], '{{_msg_}}' => $txt_var['data_not_found'], '{{_back_url_}}' => 'radio-presenter.php');
                $_404 = parseTemplate($_404, $_404_pm);
                echo $_404;
              }
            }
            ?>
          </div>
        </div>
        <hr>
      </div>
    </div>
  </div>
</div>
<?php
  require 'footer.php';
?>