<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "appearance";
  $active_sm = "";
  $enableSearch = false;
  $breadcrumbList = array(
    array($txt_var['appearance'], 'appearance.php', 'appearance'),
  );
  $addtional_resources = array(
    array('js', 'assets/js/form.js'),
    // array('js', 'assets/lib/angular/1.7.7/angular.min.js'),
    array('js', 'assets/js/appearance.js'),
    // array('js', 'assets/js/ctrl/pr.js'),
    array('js', 'assets/js/imageProcess.js'),
    array('js', 'assets/lib/cropper/4.0.0/cropper.min.js'),
    array('css', 'assets/lib/cropper/4.0.0/cropper.min.css'),
  );
  require 'header.php'; 
  $appearancePath = __DIR__.'/data/web-client/appearance.json';
  if(file_exists($appearancePath)){
    $appearanceFile = fopen($appearancePath, 'r');
    if($appearanceFile){
      $appearanceData = fread($appearanceFile, filesize($appearancePath));
      $appearanceData = json_decode($appearanceData, true);
    }
    fclose($appearanceFile);
  }else{
    echo "Someting went wrong!";
    die();
  }
?>
<script>
  txt_var.upload_image_error = "<?=$txt_var['upload_image_error'];?>";
  txt_var.change_background_image = "<?=$txt_var['change_background_image'];?>";
  txt_var.page = "<?=$txt_var['page'];?>";
  txt_var.appearance_page = "<?=$txt_var['appearance_page'];?>";
  txt_var.page_name = JSON.parse('<?=json_encode($txt_var['page_name']);?>');
  // txt_var.crop_img = "<?=$txt_var['crop_img'];?>";
  // txt_var.delete_data = "<?=$txt_var['delete_data'];?>";
  // txt_var.delete_data_confirm = "<?=$txt_var['delete_data_confirm'];?>";
  // txt_var.image_size_notice_min = "<?=$txt_var['image_size_notice_min'];?>";
  // txt_var.image_size_notice_max = "<?=$txt_var['image_size_notice_max'];?>";
  // txt_var.image_size_notice_between = "<?=$txt_var['image_size_notice_between'];?>";
  // txt_var.image_size_notice_exact = "<?=$txt_var['image_size_notice_exact'];?>";
  // txt_var.file_not_support = "<?=$txt_var['file_not_support'];?>";
</script>
<div class="row">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <h3><?=$txt_var['appearance'];?></h3>
      <div class="page-menu-container"></div>
      <hr>
     
      <div>
        <?php
          foreach($appearanceData as $key => $value){
            if($key != 'radio')
              continue;
        ?>
        <div>
          <div>
            <div class="alert alert-light fade show border">
              <div class="row">
                
                <div class="col-md-2 col-lg-3">
                  <div class="position-relative mb-2">
                    <div class="position-relative">
                      <div class="">
                        <img src="<?= e($value['bg_image']) ?>" class="w-100" id="bgPage_<?= e($key) ?>" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-10 col-lg-9">
                  <h5 class="text-dark"><?=$txt_var['appearance_page']  . " : " . $txt_var['page_name'][$key];?></h5>
                  <hr>
                    <form action="webservice/AppearanceUpdate.php" method="post" novalidate id="FormAppearance_<?= e($key) ?>" class="FormAppearance" data-notice="popup,popup">
                      <input type="hidden" name="page" value="<?= e($key) ?>" />
                      <input type="file" name="app_img" id="app_img_<?= e($key) ?>" class="d-none" accept="image/png,image/jpg,image/jpeg,image/bmp" data-page="<?= e($key) ?>" />
                      <button type="button" class="btn btn-primary text-md" aria-label="Change background image" data-page="<?= e($key) ?>" onclick="document.getElementById('app_img_<?= e($key) ?>').click();">
                        <span aria-hidden="true"><i class="fas fa-image"></i></span>
                        <span><?=$txt_var['change_background_image'];?></span>
                      </button> 
                    </form>
                </div>
              </div>
            </div>
 
          </div>
        </div>
        <?php
          }
        ?>
      </div>
    </div>
  </div>
</div>
<?php
  require 'footer.php';
?>