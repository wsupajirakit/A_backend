<?php
  require_once __DIR__.'/initialize.php';
  require_once __DIR__.'/../admin/assets/php-function/function.php';

  $active = "radio-program";
  $siteMode = "normal";
  $htmlBodyClass = "";
  $htmlAppearance = $appearanceData["default"];
  $headerClass = "main-gradient shadow-sm";
  $showCover = false;
  $showAnnounceMessage = false;
  $showSponsor = true;
  $showSidebar = true;
  $pageTitle = "ตารางจัดรายการ" . " : " . $appData['about']['app_title'];

  $meta_lang = '';
  $meta_description = 'ตารางจัดรายการ '. $appData['about']['app_name'];
  $meta_keywords = $meta_keywords_default . ',' . 'ดีเจ,รายการ,ตารางจัดรายการ';
  $meta_revisit_after = '1 month';
  $meta_robots = '';
  $meta_googlebot = '';
  $meta_author = '';

  $og_url = 'https://www.example.com/radio-program.php';
  $og_type = 'music.radio_station';
  $og_title = "ตารางจัดรายการ - " . $appData['about']['app_name'];
  $og_description = '';
  $og_image = '';
  $og_image_secure = '';
  $og_image_type = '';
  $og_image_width = '';
  $og_image_height = '';

  include 'header.php';
  $radioProgramConfig = $siteConfig['radioprogram-config'];
  $radioList = json_decode($radioProgramConfig['radio_list'], true);
?>
<script>
$(document).ready(function($){
  // Escape DB text before inserting it as HTML
  function escHtml(s){ return String(s == null ? '' : s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
  var radioProgramTemplate = getHTML('#tmpl_radio_program_content');
  $(document).on('change', '#RadioSelect', function(event){
    event.preventDefault();
    var $self = this;
    var radio = $($self).val();
    $.ajax({
      url: 'ajax/radio-program.php',
      type: 'get',
      dataType: 'json',
      data: {radio: radio},
      beforeSend: function(){
        $('#RadioSelectLoader').show();
        $('#FormGetRadioProgram').find('fieldset').attr('disabled', 'disabled');
      }
    })
    .done(function(response){
      if(response.status==1){
        var pm = { "{{_program_dat_0}}": "", "{{_program_dat_1}}": "", "{{_program_dat_2}}": "", "{{_program_dat_3}}": "", "{{_program_dat_4}}": "", "{{_program_dat_5}}": "", "{{_program_dat_6}}": "", };

        $.each(response.result, function(index, val){
          let tbHTML = '<table class="table"><thead><tr><th>เวลา</th><th>ดีเจ</th></tr></thead><tbody>';
          for(var i = 0; i < val.length; i++){
            let djHTML = '';
            for(var j = 0; j < val[i].dj.length; j++){
              djHTML += ['<div class="d-flex align-items-center mb-2">',
              '<figure class="mb-0 mr-2" style="width: 30px;">',
              '<img src="'+escHtml(val[i].dj[j].image)+'" class="d-block w-100 rounded-circle" />',
              '</figure>',
              '<span class="small-2">',escHtml(val[i].dj[j].name),'</span>',
              '</div>'].join("");
            }
            tbHTML += '<tr><td><span class="small-2">'+escHtml(val[i].time.start)+' - '+escHtml(val[i].time.end)+'</span></td><td>'+djHTML+'</td></tr>';
          }
          tbHTML += '</tbody></table>';
          pm["{{_program_dat_"+index+"}}"] = tbHTML;
        });
        $('#RadioProgramContainer').html(parseTemplate(radioProgramTemplate, pm));
      }else{
        $('#RadioProgramContainer').html('<p class="text-center">ไม่พบข้อมูลตารางจัดรายการ</p>');
      }
    })
    .fail(function(){
    })
    .always(function(){
      $('#RadioSelectLoader').hide();
      $('#FormGetRadioProgram').find('fieldset').removeAttr('disabled');
    });
  });
  $('#RadioSelect').trigger('change');
});
</script>
<div class="mb-3">
  <h1 class="text-center">ตารางจัดรายการ</h1>
  <br />
  <div class="row">
    <div class="col-md-10 mx-auto">
      <form action="ajax/radio-program.php" method="get" id="FormGetRadioProgram">
        <fieldset class="p-0">
          <div class=" form-row">
            <!-- <div class="col-12">
              <div class="d-flex align-items-center mb-1">
                <label for="RadioProgramID" class="small-2 mb-0 mr-3">คลื่นวิทยุ / วันจัดรายการ</label>
                <div id="RadioSelectLoader" style="display: none;">
                  <div class="very_mini_loader">
                    <div class="loader mx-auto my">
                      <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
            <div class="col-md-6 col-lg-4 mb-2 d-none">
              <div class="d-flex align-items-center mb-1">
                <label for="RadioProgramID" class="small-2 mb-0 mr-3">คลื่นวิทยุ</label>
                <div id="RadioSelectLoader" style="display: none;">
                  <div class="very_mini_loader">
                    <div class="loader mx-auto my">
                      <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
              <select class="custom-select shadow-none" name="RadioSelect" id="RadioSelect">
                <?php for($i=0; $i < count($radioList); $i++){ ?>
                  <option value="<?= e($radioList[$i]['id']) ?>"><?= e($radioList[$i]['name']) ?></option>
                <?php } ?>
              </select>
            </div>
            <!-- <div class="col-md-6 col-lg-4">
              <select class="custom-select shadow-none" name="RadioSelect" id="RadioSelect">
                <?php for($i=0; $i < 7; $i++){ ?>
                  <option value="<?= e($i) ?>"><?=$txt_var['days_name'][$i];?></option>
                <?php } ?>
              </select>
            </div> -->
          </div>
        </fieldset>
      </form>
      <div id="RadioProgramContainer" class="mt-4">
          
      </div>
    </div>
  </div>
</div>
<template id="tmpl_radio_program_content">
<div>
  <ul class="nav nav-tabs" id="dayTab" role="tablist">
    <?php for($i=0; $i < 7; $i++){?>
      <li class="nav-item">
        <a class="nav-link <?php if($i==0){?>active<?php }?>" id="day-<?= e($i) ?>-tab" data-toggle="tab" href="#day-<?= e($i) ?>" role="tab" aria-controls="day-<?= e($i) ?>" <?php if($i==0){?>aria-selected="true"<?php }else{?>aria-selected="false"<?php }?>><?=$txt_var['days_name'][$i];?></a>
      </li>
    <?php }?>
  </ul>
  <div class="tab-content" id="dayTabContent">
    <?php for($i=0; $i < 7; $i++){?>
    <div class="tab-pane fade <?php if($i==0){?>show active<?php }?>" id="day-<?= e($i) ?>" role="tabpanel" aria-labelledby="day-<?= e($i) ?>-tab">
      <div class="py-2">
        {{_program_dat_<?= e($i) ?>}}
      </div>
    </div>
    <?php }?>
  </div>
</div>
</template>

<?php
  include 'footer.php';
?>