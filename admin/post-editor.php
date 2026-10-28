<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "post";
  $active_sm = "all-post";
  $enableSearch = false;
  $breadcrumbList = array(
    array($txt_var['post'], 'post.php', 'post')
  );
  if($_GET['action']=='new'){
    $active_sm = "add-post";
    $breadcrumbList[] = array($txt_var['add_new'], 'post-editor.php?action=new', 'add new post');
   
  }else if($_GET['action']=='update'){
    $active_sm = "update-post";
    $breadcrumbList[] = array($txt_var['update'], 'post-editor.php?action=update&id=' . $_GET['id'], 'update post');
  }
  
  $addtional_resources = array(
    array('js', 'assets/lib/tinymce/5.0.3/tinymce.min.js'),
    array('js', 'assets/lib/cropper/4.0.0/cropper.min.js'),
    array('css', 'assets/lib/cropper/4.0.0/cropper.min.css'),
    array('css', 'assets/lib/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css'),
    array('css', 'assets/lib/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css'),
    array('js', 'assets/lib/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js'),
    array('js', 'assets/lib/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.th.min.js'),
    array('js', 'assets/js/post.js'),
    array('js', 'assets/js/form.js'),
    array('js', 'assets/js/imageProcess.js'),
  );
  require 'header.php';
  $postConfig = $siteConfig['post-config'];
?>
<script>
  txt_var.upload_image_error    = "<?=$txt_var['upload_image_error'];?>";
  txt_var.crop_img    = "<?=$txt_var['crop_img'];?>";
  txt_var.image_size_notice_min = "<?=$txt_var['image_size_notice_min'];?>";
  txt_var.image_size_notice_max = "<?=$txt_var['image_size_notice_max'];?>";
  txt_var.image_size_notice_between = "<?=$txt_var['image_size_notice_between'];?>";
  txt_var.image_size_notice_exact = "<?=$txt_var['image_size_notice_exact'];?>";
  txt_var.file_not_support = "<?=$txt_var['file_not_support'];?>";
  txt_var.close = "<?=$txt_var['close'];?>";
  txt_var.select = "<?=$txt_var['select'];?>";
  txt_var.select_image = "<?=$txt_var['select_image'];?>";
</script>
<div class="row">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <?php if($_GET['action']=='new'){?>
      <h3><?=$txt_var['post_add_new'];?></h3>
      <?php }else if($_GET['action']=='update'){?>
      <h3><?=$txt_var['post_edit'];?></h3>
      <?php }?>
      <hr>
      <div>
        <div id="FormPostEditorContainer">
        <form id="FormPostEditor" action="webservice/PostSubmit.php" method="post" novalidate>
          <fieldset>
            <div class="form-row">
              <div class="col-md-6 mb-3">
                <label for="post-name"><?=$txt_var['post_name'];?></label>
                <input type="text" class="form-control" id="post-name" name="post-name" placeholder="<?=$txt_var['post_name'];?>" required />
              </div>
            </div>
            
            <div class="form-row">
              <div class="col-md-12 mb-2">
                <div><?=$txt_var['post_cover'];?></div>
              </div>
            </div>
            <div class="form-row" id="post-img-tag" style="display: none;">
              <div class="col-md-4 mb-2">
                <figure class="m-0 w-100"></figure>
              </div>
            </div>
            <div class="w-100"></div>  
            <div class="form-row">
              <div class="col-12 mb-3">
                <input type="file" name="post-img" id="post-img" class="d-none" accept="<?= e(createInputFileAcceptAttr($postConfig['img_ext'])) ?>" />
                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('post-img').click();"><?=$txt_var['select_image'];?></button>
              </div>
            </div>
            <div class="form-row d-none">
              <div class="col-md-6 mb-3">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="is-pin" name="is-pin" />
                  <label class="custom-control-label" for="is-pin"><?=$txt_var['post_pin'];?></label>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="col-md-6 mb-3">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="is-publish" name="is-publish" checked />
                  <label class="custom-control-label" for="is-publish"><?=$txt_var['post_publish'];?></label>
                </div>
              </div>
            </div>
            
            <label for="publish_date"><?=$txt_var['publish_date'];?></label>
            <div class="form-row">
              <div class="col-md-3">
                <div class="form-group">
                  <input type="text" name="publish_date" id="publish_date" class="form-control" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" required autocomplete="off" value="<?= e(date("d/m/Y")) ?>" placeholder="<?=$txt_var['publish_date'];?>" />
                </div>
              </div>

              <div class="col-6 col-md-1">
                <div class="form-group">
                  <select name="publish_hour" id="publish_hour" class="custom-select">
                    <?php for($i=0; $i < 24; $i++){ ?>
                    <option value="<?= e(sprintf("%02d", $i)) ?>"><?= e(sprintf("%02d", $i)) ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="col-6 col-md-1">
                <div class="form-group">
                  <select name="publish_minute" id="publish_minute" class="custom-select">
                    <?php for($i=0; $i < 60; $i++){ ?>
                    <option value="<?= e(sprintf("%02d", $i)) ?>"><?= e(sprintf("%02d", $i)) ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

            </div>
            <div class="form-row">
              <div class="col mb-3">
                <textarea name="post-content" id="post-content" cols="30" rows="20"></textarea>
              </div>
            </div>

            <div class="form-row">
              <div class="col-md-6 mb-3">
                <label for="post-slug"><?=$txt_var['post_slug'];?></label>
                <input type="text" class="form-control" id="post-slug" name="post-slug" placeholder="<?=$txt_var['post_slug'];?>" pattern="[-\w]" maxlength="100" />
              <small id="slugHelpBlock" class="form-text text-muted"><?=$txt_var['post_slug_name_notice'];?></small>
              </div>
            </div>

            <div class="form-row">
              <div class="col mt-3">
                <button type="submit" class="btn btn-primary" id="submit-post"><?=$txt_var['save'];?></button>
              </div>
            </div>
          </fieldset>
        </form>
        </div>
      </div>
    </div>
  </div>
</div>
<template id="tmpl_404">
<div class="row mx-0">
  <div class="col-md-6 mx-auto">
<?php
  $_404 = file_get_contents('__data-404-tmpl.html');
  $_404_pm = array('{{_back_}}' => $txt_var['back'], '{{_msg_}}' => $txt_var['data_not_found'], '{{_back_url_}}' => 'post.php');
  $_404 = parseTemplate($_404, $_404_pm);
  echo $_404;
?>
  </div>
</div>
</template>
<?php
  require 'footer.php';
?>