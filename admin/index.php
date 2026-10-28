<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "home";
  $active_sm = "";
  $enableSearch = false;
  $breadcrumbList = array(
    array($txt_var['dashboard'], 'index.php', 'dashboard'),
  );
  require 'header.php'; 

  $now = new DateTime();
  $today = $now->format('Y-m-d');

  $countDj = $db->dbCount("SELECT COUNT(*) FROM radio_presenter WHERE rp_status = 1");
  $countArtist = $db->dbCount("SELECT COUNT(*) FROM artist");
  $countSong = $db->dbCount("SELECT COUNT(*) FROM song");
  $countPost = $db->dbCount("SELECT COUNT(*) FROM post");
  $sponsorData = $db->dbQuery("SELECT * FROM public_relations WHERE pr_type = 2 ORDER BY pr_date DESC");

  $lastestTopChart = $db->dbRow("SELECT * FROM top_chart ORDER BY top_chart_date DESC");
  if(null !== $lastestTopChart){
    $sql = "SELECT * FROM top_chart_song tcs INNER JOIN song s ON tcs.song_id = s.song_id WHERE tcs.top_chart_id = :tcid ORDER BY tcs.song_order ASC";
    $topChartData = $db->dbQuery($sql, array(':tcid' => $lastestTopChart['top_chart_id']));
  }
  $prConfig = $siteConfig['pr-config'];

  $rpConfig = $siteConfig['radiopresenter-config'];
  # radio program
  $lastestRadioProgram = $db->dbRow("SELECT * FROM radio_program WHERE :td BETWEEN rdo_program_start AND rdo_program_end AND radio_id = :rdo", array(':td' => $today, ':rdo' => 1));
  if($lastestRadioProgram!==null){
    $details = $db->dbQuery("SELECT * FROM radio_program_details WHERE rdo_program_id = :id ORDER BY rdo_program_details_time_start ASC", array(':id' => $lastestRadioProgram['rdo_program_id']));
    $tmpDj = array();
    $tmpDjId = array();
    $radioProgramData = array();
    foreach($details as $key => $value){
      if(!isset($radioProgramData[$value['rdo_program_details_day']]))
        $radioProgramData[$value['rdo_program_details_day']] = array();

      $detailsDj = $db->dbQuery("SELECT * FROM radio_program_details_radio_presenter WHERE rdo_program_details_id = :id", array(':id' => $value['rdo_program_details_id']));
      $djList = array();
      foreach($detailsDj as $key2 => $value2){
        if(!in_array($value2['rp_id'], $tmpDjId)){
          $d = $db->dbRow("SELECT rp_name AS name, rp_image AS image FROM radio_presenter WHERE rp_id = :id", array(':id' => $value2['rp_id']));
          $d['image'] = $rpConfig['rp_url'] . $d['image'];
          $tmpDj[$value2['rp_id']] = $d;
          $tmpDjId[$value2['rp_id']] = $value2['rp_id'];
        }
        $djList[] = $tmpDj[$value2['rp_id']];
      }

      $s = new DateTime($value['rdo_program_details_time_start']);
      $e = new DateTime($value['rdo_program_details_time_end']);
      $e->modify("+1 minute");
      $radioProgramData[$value['rdo_program_details_day']][] = array('dj' => $djList, 'time' => array('start' => $s->format("H:i"), 'end' => $e->format("H:i")));
    }
  }

?>
<div class="row">
  <div class="col-sm-6 col-lg-3 my-2">
    <div class="bg-white border p-3">
      <h6 class="text-secondary"><?=$txt_var['total_radio_presenter'];?></h6>
      <div class="d-flex">
        <div class="col p-0 text-success text-xl"><i class="fas fa-headset"></i></div>
        <div class="b-500">
          <div class="alert alert-success py-1" role="">
            <div><?= e(numberFormat($countDj, 0)) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3 my-2">
    <div class="bg-white border p-3">
      <h6 class="text-secondary"><?=$txt_var['total_artist'];?></h6>
      <div class="d-flex">
        <div class="col p-0 text-info text-xl"><i class="fas fa-microphone"></i></div>
        <div class="b-500">
          <div class="alert alert-info py-1" role="">
            <div><?= e(numberFormat($countArtist, 0)) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3 my-2">
    <div class="bg-white border p-3">
      <h6 class="text-secondary"><?=$txt_var['total_song'];?></h6>
      <div class="d-flex">
        <div class="col p-0 text-warning text-xl"><i class="fas fa-music"></i></div>
        <div class="b-500">
          <div class="alert alert-warning py-1" role="">
            <div><?= e(numberFormat($countSong, 0)) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3 my-2">
    <div class="bg-white border p-3">
      <h6 class="text-secondary"><?=$txt_var['total_post'];?></h6>
      <div class="d-flex">
        <div class="col p-0 text-danger text-xl"><i class="fas fa-newspaper"></i></div>
        <div class="b-500">
          <div class="alert alert-danger py-1" role="">
            <div><?= e(numberFormat($countPost, 0)) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col my-2">
    <div class="bg-white border">
      <div class="p-3">
        <h4 class="text-muted"><?=$txt_var['sponsor'];?></h4>
        <div class="mt-3">
          <?php if($sponsorData === null || count($sponsorData) == 0){?>
            <br /><br />
            <p class="col-12 text-center"><?=$txt_var['no_data_preview'];?></p>
          <?php }else{?>
          <div class="row">
            <?php foreach($sponsorData as $key => $value){?>
              <div class="mb-4 col-4 col-xs-3 col-md-2">
                <div class="card h-100 border-0">
                  <!-- <a href="post.php?read=<?= e($articleData[$i]['post_slug']) ?>"> -->
                    <img src="<?= e($prConfig['sponsor_url'] . $value['pr_image']) ?>" alt="<?= e($value['pr_title']) ?>" title="<?= e($value['pr_title']) ?>" class="rounded card-img-top" />
                  <!-- </a> -->
                  <div class="card-body px-0 py-2">
                    <h6 class="card-title mb-1"><?= e($value['pr_title']) ?></h6>
                  </div>
                  <div class="card-footer bg-transparent border-0 px-0 py-1">
                    <small class="text-muted"><i class="far fa-clock mr-1"></i><span><?= e(toReadableDateTime(strtotime($value['pr_date']))) ?></span></small>
                  </div>
                </div>
              </div>
            <?php }?>
          </div>
          <?php }?>

        </div>
      </div>
    
 
    </div>
  </div>
</div>

<div class="row masonry-grid">
  <div class="col-lg-6 my-2 masonry-grid-item masonry-grid-sizer">
    <div class="bg-white border">
      <div class="p-3">
        <h6 class="text-muted"><?=$txt_var['top_chart_lastest'];?></h6>
      </div>
      <div class="px-3 pb-3">
        <?php if($topChartData === null){?>
          <br />
          <p class="col-12 text-center"><?=$txt_var['no_data_preview'];?></p>
          <br />
        <?php }else{?>
          <p class="small-2">
            <?= e(str_replace('{n}', toReadableDateTime(strtotime($lastestTopChart['top_chart_date'])), $txt_var['top_chart_of_date'])) ?>
          </p>
          <table class="table small-2">
            <thead>
              <th>#</th>
              <th><?=$txt_var['song'];?></th>
              <th><?=$txt_var['artist'];?></th>
            </thead>
            <tbody>
              <?php foreach($topChartData as $key => $value){?>
              <tr>
                <td><?= e($value["song_order"]) ?></td>
                <td><?= e($value["song_name"]) ?></td>
                <td><?= e($value["artist_name_map"]) ?></td>
              </tr>
              <?php }?>
            </tbody>
          </table>
        <?php }?>
      </div>
    </div>
  </div>

  <div class="col-lg-6 my-2 masonry-grid-item">
    <div class="bg-white border">
      <div class="p-3">
        <h6 class="text-muted"><?=$txt_var['radio_program'];?></h6>
      </div>
      <div class="px-3 pb-3">
        <?php if($lastestRadioProgram === null){?>
          <br />
          <p class="col-12 text-center"><?=$txt_var['no_data_preview'];?></p>
          <br />
        <?php }else{?>
          <p class="small-2">
            <?php echo toReadableDateTime(strtotime($lastestRadioProgram['rdo_program_start'])) . " - " . toReadableDateTime(strtotime($lastestRadioProgram['rdo_program_end']));?>
          </p>
          <ul class="nav nav-tabs" id="dayTab" role="tablist">
            <?php for($i=0; $i < 7; $i++){?>
              <li class="nav-item">
                <a class="nav-link <?php if($i==0){?>active<?php }?>" id="day-<?= e($i) ?>-tab" data-toggle="tab" href="#day-<?= e($i) ?>" role="tab" aria-controls="day-<?= e($i) ?>" <?php if($i==0){?>aria-selected="true"<?php }else{?>aria-selected="false"<?php }?>><?=$txt_var['days_name'][$i];?></a>
              </li>
            <?php }?>
          </ul>

          <div class="tab-content" id="dayTabContent">
            <?php foreach($radioProgramData as $key => $value){?>
              <div class="tab-pane fade <?php if($key==0){?>show active<?php }?>" id="day-<?= e($key) ?>" role="tabpanel" aria-labelledby="day-<?= e($key) ?>-tab">
                <div class="py-2">
                  <table class="table">
                    <thead>
                      <tr>
                        <th><?=$txt_var['time'];?></th>
                        <th><?=$txt_var['radio_presenter'];?></th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php for ($i=0; $i < count($value); $i++){?>
                      <tr>
                        <td><span class="small-2"><?= e($value[$i]['time']['start']) ?> - <?= e($value[$i]['time']['end']) ?></span></td>
                        <td>
                          <?php for ($j=0; $j < count($value); $j++){?>
                            <div class="d-flex align-items-center mb-2">
                              <figure class="mb-0 mr-2" style="width: 30px;">
                                <img src="<?= e($value[$i]['dj'][$j]['image']) ?>" class="d-block w-100 rounded-circle" />
                              </figure>
                              <span class="small-2"><?= e($value[$i]['dj'][$j]['name']) ?></span>
                            </div>
                          <?php }?>
                        </td>
                      </tr>
                    <?php }?>
                    </tbody>
                  </table>
                </div>
              </div>
            <?php }?>
          </div>
        <?php }?>
      </div>
    </div>
  </div>
</div>
<?php
  require 'footer.php';
?>

