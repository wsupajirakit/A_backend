<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "radio-program";
  $action = $_GET['action'];
  $breadcrumbList = array(
    array($txt_var['radio_program'], 'radio-program.php', 'radio program'),
  );
  if($action=='new'){
    $active_sm = "add-radio-program";
    $breadcrumbList[] = array($txt_var['add_new'], 'radio-program-form.php?action=new', 'add radio program');
  }
  else if($action=='update'){
    $active_sm = "update-radio-program";
    $breadcrumbList[] = array($txt_var['update'], 'radio-program-form.php?action=update', 'update radio program');
  }
  $enableSearch = false;
  
  $addtional_resources = array(
    array('js', 'assets/js/form.js'),
    array('js', 'assets/js/rdoProgram.js'),
    array('js', 'assets/lib/jquery-ui/1.12.1/jquery-ui.js'),
    array('js', 'assets/lib/jquery-ui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js'),
    array('css', 'assets/lib/bootstrap-select/1.13.9/bootstrap-select.min.css'),
    array('js', 'assets/lib/bootstrap-select/1.13.9/bootstrap-select.min.js'),
    array('js', 'assets/lib/bootstrap-select/1.13.9/i18n/defaults-th_TH.js'),
    array('css', 'assets/lib/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css'),
    array('css', 'assets/lib/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css'),
    array('js', 'assets/lib/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js'),
    array('js', 'assets/lib/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.th.min.js')
  );
  require 'header.php'; 
  $radioProgramConfig = $siteConfig['radioprogram-config'];
  $radioList = json_decode($radioProgramConfig['radio_list'], true);
?>
<script>
  txt_var.delete = "<?=$txt_var['delete'];?>";
  txt_var.delete_confirm = "<?=$txt_var['delete_confirm'];?>";
  txt_var.radio_presenter = "<?=$txt_var['radio_presenter'];?>";
  txt_var.days_name = [];
  txt_var.days_name_abb = [];
  <?php for ($i = 0; $i < 7; $i++){?>
  txt_var.days_name[<?= e($i) ?>] = "<?=$txt_var['days_name'][$i];?>";
  txt_var.days_name_abb[<?= e($i) ?>] = "<?=$txt_var['days_name_abb'][$i];?>";
  <?php }?>
  $(document).on('submit', '#FormRadioProgram', function(event) {
    event.preventDefault();
    ajaxFormSubmit(event).then(function(response){
      <?php if($action=='new'){?>
      if(response.status==1){
        setTimeout(()=>{
          // window.location.href = "radio-program-form.php?action=update&id=" + response.result.id;
          window.location.href = "radio-program.php";
        }, 1000);
      }
      <?php }else if($action=='update'){?>
        // setTimeout(()=>{
        //   window.location.reload();
      <?php }?>
    }, );
  });
</script>
<div class="row">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <?php if($action=='new'){?>
      <h3><?=$txt_var['add_radio_program'];?></h3>
      <?php }else if($action=='update'){?>
      <h3><?=$txt_var['update_radio_program'];?></h3>
      <?php }?>
      <hr>
      <div>
        <div class="row">
          <div id="FormRadioProgramContainer">
          <?php if($action=='new'){?>
            <form class="needs-validation" novalidate method="post" action="webservice/RadioProgramSubmit.php" id="FormRadioProgram" data-notice="dialog,popup">
          <?php }else if($action=='update'){
            $id = $_GET['id'];
            $rpData = $db->dbRow("SELECT * FROM radio_program WHERE rdo_program_id = :id", array(':id' => $id));
            list($hrStart, $hrEnd) = array_map('intval', explode("-", $rpData['rdo_program_time_range']));
            $hrGap = $rpData['rdo_program_hour_gap'];

            $selectedRadio = searchArrayByValue($radioList, 'id', $rpData['radio_id'], true);
          ?>
            <form class="needs-validation" novalidate method="post" action="webservice/RadioProgramUpdate.php" id="FormRadioProgram" data-notice="popup,popup">
              <input type="hidden" name="id" value="<?= e($id) ?>" />
          <?php }?>
              <fieldset>
                <div class="col-12">
                <?php if($action=='new'){?>
                  <div class="mb-2 d-none">
                    <label for=""><?=$txt_var['radio_station'];?></label>
                    <div class="row">
                      <?php for($i=0; $i < count($radioList); $i++){?>
                        <div class="col-12 mb-1">
                          <div class="custom-control custom-radio">
                            <input type="radio" id="rdo_<?= e($radioList[$i]['id']) ?>" name="rdo_select" class="custom-control-input" value="<?= e($radioList[$i]['id']) ?>" required <?php if($i==0){?> checked <?php }?> />
                            <label class="custom-control-label" for="rdo_<?= e($radioList[$i]['id']) ?>"><?= e($radioList[$i]['name']) ?></label>
                          </div>
                        </div>
                      <?php }?>
                    </div>
                  </div>
                <?php }else if($action=='update'){?>
                  <div class="mb-2 d-none">
                    <label for=""><?=$txt_var['radio_station'];?></label>
                    <div class="row">
                      <div class="col-12 mb-1">
                        <span class="font-weight-bold"><?= e($selectedRadio['name']. " (" . $selectedRadio['wave'] . ")") ?></span>
                      </div>
                    </div>
                  </div>
                <?php }?>
                  <div class="row">
                    
                    <div class="col-md-3">
                      <label for="rdo_program_start"><?=$txt_var['start_date'];?></label>
                      <div class="">
                        <div class="">
                          <div class="form-group">
                            <?php
                              if($action=='new'){
                                $s = new DateTime();
                                $e = new DateTime();
                                $e->modify("+1 day");
                                $sdate = $s->format("d/m/Y");
                                $edate = $e->format("d/m/Y");
                              }else if($action=='update'){
                                $s = new DateTime($rpData['rdo_program_start']);
                                $e = new DateTime($rpData['rdo_program_end']);
                                $sdate = $s->format("d/m/Y");
                                $edate = $e->format("d/m/Y");
                              }
                            ?>
                            <input type="text" name="rdo_program_start" id="rdo_program_start" class="form-control rp_datepicker" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" required autocomplete="off" value="<?= e($sdate) ?>" placeholder="<?=$txt_var['start_date'];?>" />
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <label for="rdo_program_end"><?=$txt_var['end_date'];?></label>
                      <div class="">
                        <div class="">
                          <div class="form-group">
                            <input type="text" name="rdo_program_end" id="rdo_program_end" class="form-control rp_datepicker" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/[0-9]{4}" required autocomplete="off" value="<?= e($edate) ?>" placeholder="<?=$txt_var['end_date'];?>" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="col-12 d-flex" id="rpTableContainer">
                 <!--  <div>
                    <table class="table table-bordered radio-program-table">
                      <thead>
                        <tr>
                          <th class="text-center"  style="width: 50px;"><?=$txt_var['time'];?></th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      $jn = 60;
                      for($i = 0; $i < 24; $i++){
                        for($j = 0; $j < 60; $j+=$jn){?>
                        <tr>
                          <td class="p-1 small text-center no-select-here"><?= e(sprintf("%02d", $i) . ":" . sprintf("%02d", $j)) ?></td>
                        </tr>
                      <?php } }?>
                      </tbody>
                    </table>
                  </div>
                  <div class="w-100" style="overflow-x: auto;">
                    <div class="position-relative" id="radioProgramTableContainer">
                    <table class="table table-bordered radio-program-table table-fixed schedule-selector">
                      <thead>
                        <tr>
                          <?php for ($k=0; $k < 7; $k++){?>
                            <th class="px-1 text-center">
                              <span class="d-none d-sm-inline">
                                <?=$txt_var['days_name'][$k];?>
                              </span>
                              <span class="d-sm-none">
                                <?=$txt_var['days_name_abb'][$k];?>
                              </span>
                              </th>
                          <?php }?>
                        </tr>
                      </thead>
                      <tbody>
                      <?php for($i = 0; $i < 24; $i++){
                        for($j = 0; $j < 60; $j+=$jn){?>
                        <tr>
                          <?php for($k=0; $k < 7; $k++){
                            $dt1 = $k . "_" . sprintf("%02d", $i) . "_" . sprintf("%02d", $j);
                            $dt2 = $k . "_" . sprintf("%02d", $i) . "_" . sprintf("%02d", ($j+$jn-1));
                            $dtF = $dt1 . "-" . $dt2;
                          ?>
                            <td 
                            data-datetime="<?= e($dtF) ?>" 
                            class="<?= e($dtF) ?>">
                            </td>
                          <?php }?>
                        </tr>
                      <?php }}?>
                      </tbody>
                    </table>
                    </div>
                  </div> -->
                </div>
                <div id="datetimeInput"></div> 
                <?php if($action=='update'){?>
                <div id="datetimeInputRemove"></div> 
                <?php }?>
                <div class="col-12">
                  <div class="form-group">
                    <button class="btn btn-primary" type="submit"><?=$txt_var['save'];?></button>
                  </div>
                </div>
              </fieldset>
            </form>
          </div>
        </div>
        <hr>
      </div>
    </div>
  </div>
</div>
<template id="tmpl_rpDisplay">
  <div class="rpDisplay d-none _ d-md-block" style="z-index: {_zIndex_}; margin-right: {_marginRight_};">
    <div class="rpImg rounded-circle">
      <img src="{_img_}" class="d-block w-100 rounded-circle" />
    </div>
    <div class="rpName text-center small">
      <span>{_name_}</span>
    </div>
  </div>
</template>
<template id="tmpl_rpDisplayMore">
  <div class="rpDisplay {_rpDisplayClass_}">
    <div class="rpImg rounded-circle text-center small-2" style="line-height: 26px;">
      <div class="text-primary rounded-circle bg-white">
        <span class="d-md-none">{_num_total_}</span>
        <span class="d-none d-md-inline _">{_num_after_2_}</span>
      </div>
    </div>
  </div>
</template>
<template id="tmpl_rpControlMenu">
  <div class="rpControlMenu position-absolute p-1" id="rpControl_{_dt_}">
    <span class="text-white small mr-1 d-none badge badge-info font-weight-normal">{_dt_fmt_}</span>
    <button class="btn-sm btn btn-light small btnRpSetting" data-datetime="{_dt_}" data-radiopresenter="{_dj_}" {_up_id_}><i class="fas fa-cog" title="<?=$txt_var['details'];?>"></i></button>
  </div>
</template>
<template id="tmpl_rpdj">
  <div>
    <img src="{_img_}" class="rounded mr-2" style="width: 1.8rem;" />
    <span style="vertical-align: top;">{_name_}</span>
  </div>
</template>
<template id="tmpl_404">
<div class="row mx-0">
  <div class="col-md-6 mx-auto">
<?php
  $_404 = file_get_contents('__data-404-tmpl.html');
  $_404_pm = array('{{_back_}}' => $txt_var['back'], '{{_msg_}}' => $txt_var['data_not_found'], '{{_back_url_}}' => 'radio-program.php');
  $_404 = parseTemplate($_404, $_404_pm);
  echo $_404;
?>
  </div>
</div>
</template>
<?php
  require 'footer.php';
?>