<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "song";
  $action = $_GET['action'];
  $breadcrumbList = array(
    array($txt_var['song'], 'song.php', 'song'),
  );
  if($action=='new'){
    $active_sm = "add-song";
    $breadcrumbList[] = array($txt_var['add_new'], 'song-form.php?action=new', 'add song');
  }
  else if($action=='update'){
    $active_sm = "update-song";
    $breadcrumbList[] = array($txt_var['update'], 'song-form.php?action=update', 'update song');
  }
  $enableSearch = false;
  
  $addtional_resources = array(
    array('js', 'assets/js/form.js'),
    array('js', 'assets/js/song.js'),
    array('js', 'assets/lib/jquery-ui/1.12.1/jquery-ui.js'),
    array('js', 'assets/lib/jquery-ui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js'),
    array('css', 'assets/lib/bootstrap-select/1.13.9/bootstrap-select.min.css'),
    array('js', 'assets/lib/bootstrap-select/1.13.9/bootstrap-select.min.js'),
    array('js', 'assets/lib/bootstrap-select/1.13.9/i18n/defaults-th_TH.js'),
  );
  require 'header.php'; 
  $artistsList = $db->dbQuery("SELECT * FROM artist ORDER BY artist_name ASC");
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
  $(document).on('submit', '#FormSong', function(event) {
    event.preventDefault();
    ajaxFormSubmit(event).then(function(response){
      <?php if($action=='new'){?>
      if(response.status==1){
        setTimeout(() => {
          window.location.href = "song.php";
        }, 1000);
      }
      <?php }?>
    }, );
  });
</script>
<template id="tmpl-artist-label">
  <?php
    $aLI_tmpl = file_get_contents('__song-item-tmpl.html');
    echo $aLI_tmpl;
    // ob_start();
  ?>
<!--   <li class="alert alert-primary alert-dismissible fade show artist-lb no-action-on-submit" data-artist-id="{{artist_id}}">
  <span>{{artist_name}}</span>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="removeArtist('{{artist_id}}');">
    <span aria-hidden="true">&times;</span>
  </button>
</li> -->
<?php
  // ob_flush();
?>
</template>
<div class="row">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <?php if($action=='new'){?>
      <h3><?=$txt_var['add_song'];?></h3>
      <?php }else if($action=='update'){?>
      <h3><?=$txt_var['update_song'];?></h3>
      <?php }?>
      <hr>
      <div>
        <div class="row">
          <div class="col-md-6 mx-auto">
            <?php if($action=='new'){?>
              <form class="needs-validation" novalidate method="post" action="webservice/SongSubmit.php" id="FormSong" data-notice="dialog,popup">
                <fieldset>
                  <input type="hidden" id="artists" name="artists"  />
                  <div class="form-row">
                    <div class="col-12">
                      <label for="song_name"><?=$txt_var['song_name'];?></label>
                      <div class="form-group">
                        <input type="text" class="form-control" name="song_name" id="song_name" required maxlength="100" placeholder="<?=$txt_var['song_name'];?>"> 
                      </div>
                      
                      <label><?=$txt_var['artist'];?></label>
                      <ul id="displayArtistList" class="sortable no-bullets"></ul>

                    </div>
                  </div>
                  <input type="submit" class="d-none">
                </fieldset>
              </form>
              <div class="form-row">
                <div class="col-12">
                  <hr>
                  <div class="form-group">
                     <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text"><?=$txt_var['artist'];?></div>
                      </div>
                      <select name="artists_select" id="artists_select" class="selectpicker custom-select form-control" data-style="border" data-live-search="true">
                        <?php foreach($artistsList as $key => $value){?>
                          <option value="<?= e($value['artist_id']) ?>"><?= e($value['artist_name']) ?></option>
                        <?php }?>
                      </select>
                      <div class="input-group-append">
                        <button class="btn btn-success" type="button" id="addArtist"><i class="fas fa-plus mr-2"></i><?=$txt_var['add_new'];?></button>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <button class="btn btn-primary" type="button" id="submitFormSong"><?=$txt_var['save'];?></button>
                  </div>
                </div>
              </div>
            <?php
            }else if($action=='update'){
              $songData = $db->dbRow("SELECT * FROM song WHERE song_id = :id", array(':id' => $_GET['id']));
              if(null !== $songData){
                $artistData = $db->dbQuery("SELECT * FROM artist a INNER JOIN song_artist sa ON a.artist_id = sa.artist_id WHERE sa.song_id = :id ORDER BY sa.is_lead_artist DESC",  array(':id' => $_GET['id']));
                $aIdList = array();
                $aHTML = "";
                for($i=0; $i < count($artistData); $i++){ 
                  $aIdList[] = $artistData[$i]['artist_id'];
                  $aParam = array('{{artist_name}}' => e($artistData[$i]['artist_name']), '{{artist_id}}' => (int) $artistData[$i]['artist_id']);
                  $aHTML .= parseTemplate($aLI_tmpl, $aParam);
                }
                $aId = implode(",", $aIdList);
            ?>
              <form class="needs-validation" novalidate method="post" action="webservice/SongUpdate.php" id="FormSong">
                <fieldset>
                  <input type="hidden" id="artists" name="artists" value="<?= e($aId) ?>"  />
                  <input type="hidden" id="id" name="id" value="<?= e($_GET['id']) ?>"  />
                  <div class="form-row">
                    <div class="col-12">
                      <label for="song_name"><?=$txt_var['song_name'];?></label>
                      <div class="form-group">
                        <input type="text" class="form-control" name="song_name" id="song_name" required maxlength="100" placeholder="<?=$txt_var['song_name'];?>" value="<?= e($songData['song_name']) ?>"> 
                      </div>
                      
                      <label><?=$txt_var['artist'];?></label>
                      <ul id="displayArtistList" class="sortable no-bullets">
                        <?php echo $aHTML; ?>
                      </ul>

                    </div>
                  </div>
                  <input type="submit" class="d-none">
                </fieldset>
              </form>
              <div class="form-row">
                <div class="col-12">
                  <hr>
                  <div class="form-group">
                     <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text"><?=$txt_var['artist'];?></div>
                      </div>
                      <select name="artists_select" id="artists_select" class="selectpicker custom-select form-control" data-live-search="true" data-style="border">
                        <?php foreach($artistsList as $key => $value){?>
                          <option value="<?= e($value['artist_id']) ?>"><?= e($value['artist_name']) ?></option>
                        <?php }?>
                      </select>
                      <div class="input-group-append">
                        <button class="btn btn-success" type="button" id="addArtist"><i class="fas fa-plus mr-2"></i><?=$txt_var['add_new'];?></button>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <button class="btn btn-primary" type="button" id="submitFormSong"><?=$txt_var['save'];?></button>
                  </div>
                </div>
              </div>
            <?php
              }else{
                $_404 = file_get_contents('__data-404-tmpl.html');
                $_404_pm = array('{{_back_}}' => $txt_var['back'], '{{_msg_}}' => $txt_var['data_not_found'], '{{_back_url_}}' => 'song.php');
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