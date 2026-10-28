<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "artist";
  $action = $_GET['action'];
  $breadcrumbList = array(
    array($txt_var['artist'], 'artist.php', 'artist'),
  );
  if($action=='new'){
    $active_sm = "add-artist";
    $breadcrumbList[] = array($txt_var['add_new'], 'artist-form.php?action=new', 'add artist');
  }
  else if($action=='update'){
    $active_sm = "update-artist";
    $breadcrumbList[] = array($txt_var['update'], 'artist-form.php?action=update', 'update artist');
  }
  $enableSearch = false;
  
  $addtional_resources = array(
    array('js', 'assets/js/form.js'),
  );
  require 'header.php'; 
?>
<script>
  // txt_var.upload_image_error = "<?=$txt_var['upload_image_error'];?>";
  // txt_var.crop_img = "<?=$txt_var['crop_img'];?>";
  // txt_var.delete_data = "<?=$txt_var['delete_data'];?>";
  // txt_var.delete_data_confirm = "<?=$txt_var['delete_data_confirm'];?>";
  // txt_var.image_size_notice_min = "<?=$txt_var['image_size_notice_min'];?>";
  // txt_var.image_size_notice_max = "<?=$txt_var['image_size_notice_max'];?>";
  // txt_var.image_size_notice_between = "<?=$txt_var['image_size_notice_between'];?>";
  // txt_var.image_size_notice_exact = "<?=$txt_var['image_size_notice_exact'];?>";
  $(document).on('submit', '#FormArtist', function(event) {
    event.preventDefault();
    ajaxFormSubmit(event).then(function(response){
      <?php if($action=='new'){?>
      if(response.status==1){
        setTimeout(() => {
          window.location.href = "artist.php";
        }, 1000);
      }
      <?php }?>
    }, );
  });
</script>
<div class="row">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <?php if($action=='new'){?>
      <h3><?=$txt_var['add_artist'];?></h3>
      <?php }else if($action=='update'){?>
      <h3><?=$txt_var['update_artist'];?></h3>
      <?php }?>
      <hr>
      <div>
        <div class="row">
          <div class="col-md-6 mx-auto">
            <?php if($action=='new'){?>
              <form class="needs-validation" novalidate method="post" action="webservice/ArtistSubmit.php" id="FormArtist" data-notice="dialog,popup">
                <fieldset>
                  <div class="form-row">
                    <div class="col-12">
                      <label for="artist_name"><?=$txt_var['artist_name'];?></label>
                      <div class="form-group">
                        <input type="text" class="form-control" name="artist_name" id="artist_name" required maxlength="100" placeholder="<?=$txt_var['artist_name'];?>"> 
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
              $artistData = $db->dbRow("SELECT * FROM artist WHERE artist_id = :id", array(':id' => $_GET['id']));
              if(null !== $artistData){
            ?>
              <form class="needs-validation" novalidate method="post" action="webservice/ArtistUpdate.php" id="FormArtist">
                <fieldset>
                  <input type="hidden" name="id" value="<?= e($_GET['id']) ?>" />
                  <div class="form-row">
                    <div class="col-12">
                      <label for="artist_name"><?=$txt_var['artist_name'];?></label>
                      <input type="hidden" name="update" value="info" />
                      <div class="form-group">
                        <input type="text" class="form-control" name="artist_name" id="artist_name" required maxlength="100" placeholder="<?=$txt_var['artist_name'];?>" value="<?= e($artistData['artist_name']) ?>"> 
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
                $_404_pm = array('{{_back_}}' => $txt_var['back'], '{{_msg_}}' => $txt_var['data_not_found'], '{{_back_url_}}' => 'artist.php');
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