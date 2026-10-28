<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "media-file";
  $active_sm = "media-image";
  $enableSearch = false;
  $breadcrumbList = array(
    array($txt_var['media_file'], 'media.php', 'media file'),
    array($txt_var['image'], 'media.php', 'media image'),
  );
  $addtional_resources = array(
    array('js', 'assets/js/form.js'),
    array('js', 'assets/js/mediaFile.js'),
    array('js', 'assets/js/imageProcess.js'),
    array('js', 'assets/lib/clipboard-js/2.0.4/clipboard.min.js'),
  );
  require 'header.php';
  $postConfig = $siteConfig['post-config'];
?>
<script>
  txt_var.delete_data = "<?=$txt_var['delete_data'];?>";
  txt_var.delete_data_confirm = "<?=$txt_var['delete_data_confirm'];?>";
</script>
<div class="row">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <h3><?=$txt_var['image'];?></h3>
      <div class="page-menu-container">
        <button type="button" class="btn btn-primary btn-sm" id="SelectMedia"><i class="fas fa-plus mr-1"></i><?=$txt_var['add_new'];?></button>
      </div>
      <hr>
      <div>
        <input type="file" multiple name="media_files" id="media_files" class="d-none" accept="<?= e(createInputFileAcceptAttr($postConfig['img_ext'])) ?>" />
        <div id="image-container" class="ms-grid"></div>
      </div>
    </div>
  </div>
</div>
<?php
  require 'footer.php';
?>