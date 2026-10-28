<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "top-chart";
  $action = $_GET['action'];
  $breadcrumbList = array(
    array($txt_var['top_chart'], 'top-chart.php', 'top-chart'),
  );
  if($action=='new'){
    $active_sm = "add-top-chart";
    $breadcrumbList[] = array($txt_var['add_new'], 'top-chart-form.php?action=new', 'add top-chart');
  }
  else if($action=='update'){
    $active_sm = "update-top-chart";
    $breadcrumbList[] = array($txt_var['update'], 'top-chart-form.php?action=update', 'update top-chart');
  }
  $enableSearch = false;
  
  $addtional_resources = array(
    array('js', 'assets/js/form.js'),
    array('js', 'assets/js/topChart.js'),
    array('js', 'assets/lib/jquery-ui/1.12.1/jquery-ui.js'),
    array('js', 'assets/lib/jquery-ui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js'),
    array('css', 'assets/lib/bootstrap-select/1.13.9/bootstrap-select.min.css'),
    array('js', 'assets/lib/bootstrap-select/1.13.9/bootstrap-select.min.js'),
    array('js', 'assets/lib/bootstrap-select/1.13.9/i18n/defaults-th_TH.js'),
    array('css', 'assets/lib/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css'),
    array('css', 'assets/lib/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css'),
    array('js', 'assets/lib/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js'),
    array('js', 'assets/lib/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.th.min.js'),
  );
  require 'header.php'; 
  $topchartConfig = $siteConfig['topchart-config'];
  $topchartNum = explode(",", $topchartConfig['topchart_num']);
  $songList = $db->dbQuery("SELECT * FROM song ORDER BY song_name ASC");
?>
<script>
  //txt_var.upload_image_error = "<?=$txt_var['upload_image_error'];?>";
  //txt_var.crop_img = "<?=$txt_var['crop_img'];?>";
  //txt_var.delete_data = "<?=$txt_var['delete_data'];?>";
  //txt_var.delete_data_confirm = "<?=$txt_var['delete_data_confirm'];?>";
  //txt_var.image_size_notice_min = "<?=$txt_var['image_size_notice_min'];?>";
  //txt_var.image_size_notice_max = "<?=$txt_var['image_size_notice_max'];?>";
  //txt_var.image_size_notice_between = "<?=$txt_var['image_size_notice_between'];?>";
  //txt_var.image_size_notice_exact = "<?=$txt_var['image_size_notice_exact'];?>";
  $(document).on('submit', '#FormTopChart', function(event) {
    event.preventDefault();
    ajaxFormSubmit(event).then(function(response){
      <?php if($action=='new'){?>
      if(response.status==1){
        setTimeout(() => {
          window.location.href = "top-chart.php";
        }, 1000);
      }
      <?php }?>
    },);
  });
</script>
<template id="tmpl-song-label">
  <?php
    $aLI_tmpl = file_get_contents('__top-chart-song-item-tmpl.html');
    $aLi_pm = array('{{_lwo_lb_}}' => $txt_var['last_top_chart_order'], '{{_ocwn_lb_}}' => $txt_var['on_top_chart_range']);
    $aLI_tmpl = parseTemplate($aLI_tmpl, $aLi_pm);
    echo $aLI_tmpl;
    // ob_start();
  ?>
  <!-- <li class="alert alert-primary alert-dismissible fade show song-lb song-{{song_id}} no-action-on-submit" data-song-id="{{song_id}}">
    <span class="badge badge-primary song-order"></span>
    <span>{{song_name}}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="removeSong('{{song_id}}');">
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
      <h3><?=$txt_var['add_top_chart'];?></h3>
      <?php }else if($action=='update'){?>
      <h3><?=$txt_var['update_top_chart'];?></h3>
      <?php }?>
      <hr>
      <div>
        <div class="row">
          <div class="col-md-6 mx-auto">
            <?php if($action=='new'){?>
              <form class="needs-validation" novalidate method="post" action="webservice/TopChartSubmit.php" id="FormTopChart" data-notice="dialog,popup">
                <fieldset>
                  <input type="hidden" id="songs" name="songs"  />
                  <div class="form-row">
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label><?=$txt_var['song_number'];?></label>
                        <select class="custom-select" id="top_chart_num" name="top_chart_num" required>
                          <?php for ($i=0; $i < count($topchartNum); $i++){ ?>
                            <option value="<?= e($topchartNum[$i]) ?>"><?= e($topchartNum[$i]) ?></option>
                          <?php }?>
                          <!-- <option value="10">10</option>
                          <option value="15">15</option>
                          <option value="20">20</option> -->
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label for="top_chart_date"><?=$txt_var['date'];?></label>
                        <input type="text" name="top_chart_date" id="top_chart_date" class="form-control" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" required autocomplete="off" value="<?= e(date("d/m/Y")) ?>"  />
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group">
                      </div>
                      <label><?=$txt_var['song'];?></label>
                      <ul id="displaySongtList" class="sortable no-bullets"></ul>
                    </div>
                  </div>
                  <input type="submit" class="d-none">
                </fieldset>
              </form>
              <div class="form-row">
                <div class="col-12">
                  <hr class="mt-0">
                  <div class="form-group">
                     <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text"><?=$txt_var['song'];?></div>
                      </div>
                      <select name="songs_select" id="songs_select" class="selectpicker custom-select form-control" data-style="border" data-live-search="true">
                        <?php foreach($songList as $key => $value){?>
                          <option value="<?= e($value['song_id']) ?>"><?= e($value['song_name']) ?> - <?= e($value['artist_name_map']) ?></option>
                        <?php }?>
                      </select>
                      <div class="input-group-append">
                        <button class="btn btn-success" type="button" id="addSong"><i class="fas fa-plus mr-2"></i><?=$txt_var['add_new'];?></button>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <button class="btn btn-primary" type="button" id="submitFormTopChart"><?=$txt_var['save'];?></button>
                  </div>
                </div>
              </div>
            <?php
            }else if($action=='update'){
              $tcData = $db->dbRow("SELECT * FROM top_chart WHERE top_chart_id = :id", array(':id' => $_GET['id']));
              if(null !== $tcData){
                $songData = $db->dbQuery("SELECT * FROM song s INNER JOIN top_chart_song ts ON s.song_id = ts.song_id WHERE ts.top_chart_id = :id ORDER BY ts.song_order ASC",  array(':id' => $_GET['id']));
                $aIdList = array();
                $aHTML = "";
                for($i=0; $i < count($songData); $i++){ 
                  $_lwo = empty($songData[$i]['last_week_order']) ? 0 : $songData[$i]['last_week_order'];
                  $_ocwn = empty($songData[$i]['on_chart_week_number']) ? 0 : $songData[$i]['on_chart_week_number'];
                  $aIdList[] = $songData[$i]['song_id'] . "_" . $_lwo . "_" . $_ocwn;
                  $aParam = array(
                    '{{song_name}}' => e($songData[$i]['song_name'] . " - " . $songData[$i]['artist_name_map']), 
                    '{{song_id}}' => (int) $songData[$i]['song_id'],
                    '{{_lwo_}}' => $_lwo == 0 ? "" : e($_lwo),
                    '{{_ocwn_}}' => $_ocwn == 0 ? "" : e($_ocwn),
                  );
                  $aHTML .= parseTemplate($aLI_tmpl, $aParam);
                }
                $aId = implode(",", $aIdList);

                list($tc_year, $tc_month, $tc_day) = explode("-", $tcData['top_chart_date']);
                $tc_date = $tc_day . "/" . $tc_month . "/" . $tc_year;
            ?>
              <form class="needs-validation" novalidate method="post" action="webservice/TopChartUpdate.php" id="FormTopChart">
                <fieldset>
                  <input type="hidden" name="id" id="id" value="<?= e($_GET['id']) ?>" />
                  <input type="hidden" id="songs" name="songs" value="<?= e($aId) ?>"  />
                  <div class="form-row">
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label><?=$txt_var['song_number'];?></label>
                        <select class="custom-select" id="top_chart_num" name="top_chart_num" required>
                          <?php for ($i=0; $i < count($topchartNum); $i++){ ?>
                            <option value="<?= e($topchartNum[$i]) ?>" <?php if(count($songData)==$topchartNum[$i]){?> selected <?php }?>><?= e($topchartNum[$i]) ?></option>
                          <?php }?>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label><?=$txt_var['date'];?></label>
                        <input type="text" name="top_chart_date" id="top_chart_date" class="form-control" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" required autocomplete="off" value="<?= e($tc_date) ?>"  />
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group">
                      </div>
                      <label><?=$txt_var['song'];?></label>
                      <ul id="displaySongtList" class="sortable no-bullets">
                        <?php echo $aHTML; ?>
                      </ul>
                    </div>
                  </div>
                  <input type="submit" class="d-none">
                </fieldset>
              </form>
              <div class="form-row">
                <div class="col-12">
                  <hr class="mt-0">
                  <div class="form-group">
                     <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text"><?=$txt_var['song'];?></div>
                      </div>
                      <select name="songs_select" id="songs_select" class="selectpicker custom-select form-control" data-style="border" data-live-search="true">
                        <?php foreach($songList as $key => $value){?>
                          <option value="<?= e($value['song_id']) ?>"><?= e($value['song_name']) ?> - <?= e($value['artist_name_map']) ?></option>
                        <?php }?>
                      </select>
                      <div class="input-group-append">
                        <button class="btn btn-success" type="button" id="addSong"><i class="fas fa-plus mr-2"></i><?=$txt_var['add_new'];?></button>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <button class="btn btn-primary" type="button" id="submitFormTopChart"><?=$txt_var['save'];?></button>
                  </div>
                </div>
              </div>
            <?php  }else{
                  $_404 = file_get_contents('__data-404-tmpl.html');
                  $_404_pm = array('{{_back_}}' => $txt_var['back'], '{{_msg_}}' => $txt_var['data_not_found'], '{{_back_url_}}' => 'top-chart.php');
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