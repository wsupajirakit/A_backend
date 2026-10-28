<?php
  require_once __DIR__.'/initialize.php';
  require_once __DIR__.'/../admin/assets/php-function/function.php';

  $active = "topchart";
  $siteMode = "normal";
  $htmlBodyClass = "";
  $htmlAppearance = $appearanceData["default"];
  $headerClass = "main-gradient shadow-sm";
  $showCover = false;
  $showAnnounceMessage = false;
  $showSponsor = true;
  $showSidebar = true;
  $pageTitle = "เพลงฮิตติดชาร์ท" . " : " . $appData['about']['app_title'];

  $meta_lang = '';
  $meta_description = 'เพลงฮิตติดชาร์ท '. $appData['about']['app_name'];
  $meta_keywords = $meta_keywords_default . ',' . 'เพลงฮิต,เพลงดัง,เพลงฮิตติดชาร์ท';
  $meta_revisit_after = '7 days';
  $meta_robots = '';
  $meta_googlebot = '';
  $meta_author = '';

  $og_url = 'https://www.example.com/top-chart.php';
  $og_type = 'music.playlist';
  $og_title = "เพลงฮิตติดชาร์ท - " . $appData['about']['app_name'];
  $og_description = '';
  $og_image = '';
  $og_image_secure = '';
  $og_image_type = '';
  $og_image_width = '';
  $og_image_height = '';

  include 'header.php';

  $topChartDate = $db->dbColumn("SELECT top_chart_date FROM top_chart ORDER BY top_chart_date DESC");
?>
<script>
$(document).ready(function($){
  // Escape DB text before inserting it as HTML
  function escHtml(s){ return String(s == null ? '' : s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
  var topChartTableTemplate = getHTML('#tmpl_topchart_table');
  $(document).on('change', '#TopChartDateSelect', function(event){
    event.preventDefault();
    var $self = this;
    var date = $($self).val();
    $.ajax({
      url: 'ajax/top-chart.php',
      type: 'get',
      dataType: 'json',
      data: {date: date},
      beforeSend: function(){
        $('#TopChartDateSelectLoader').show();
        $('#FormGetTopChart').find('fieldset').attr('disabled', 'disabled');
      }
    })
    .done(function(response){
      if(response.status==1){
        var rowHTML = '';
        $.each(response.result.songs, function(index, val){
        if(val.lastChartNum != ""){
          if(parseInt(val.num) < parseInt(val.lastChartNum)){
            var $_udFaw = '<div class="tcod-icon mr-2"><i class="fas fa-chevron-circle-up tcod-up"></i></div>';
          }else if(parseInt(val.num) > parseInt(val.lastChartNum)){
            var $_udFaw = '<div class="tcod-icon mr-2"><i class="fas fa-chevron-circle-down tcod-down"></i></div>';
          }else{
            var $_udFaw = '<div class="tcod-icon mr-2"><i class="fas fa-minus-circle tcod-new"></i></div>';
          }
        }else{
          $_udFaw = '<div class="tcod-icon mr-2"><i class="fas fa-minus-circle tcod-new"></i></div>';
        }
         rowHTML += ['<tr>',
         '<td>'+escHtml(val.num)+'</td>',
         '<td><div class="d-flex">'+$_udFaw+'<span>'+escHtml(val.name)+'</span></div></td>',
         '<td>'+escHtml(val.artists)+'</td>',
         '<td class="text-center">'+escHtml(val.lastChartNum)+'</td>',
         '<td class="text-center">'+escHtml(val.onChartLong)+'</td>',
         '</tr>'].join("");
        });
        $('#TopChartContainer').html(topChartTableTemplate);
        $('#TopChartDate').text("อันดับเพลงฮิตประจำวันที่ " + response.result.date_formatted);
        $('#topChartBody').html(rowHTML);
      }
    })
    .fail(function(){
    })
    .always(function(){
      $('#TopChartDateSelectLoader').hide();
      $('#FormGetTopChart').find('fieldset').removeAttr('disabled');
    });
  });
  $('#TopChartDateSelect').trigger('change');
});
</script>
<div class="mb-3">
    <h1 class="text-center">เพลงฮิตติดชาร์ท</h1>
  <br />
  <div class="row">
    <div class="col-md-11 mx-auto">
      <form action="ajax/top-chart.php" method="get" id="FormGetTopChart">
        <fieldset class="p-0">
          <div class=" form-row">
            <div class="col-md-6 col-lg-4">
            <?php if($topChartDate!==null && count($topChartDate) > 0){?>
            
            <div class="d-flex align-items-center mb-1">
              <label for="topChartDate" class="small-2 mb-0 mr-3">อันดับเพลงฮิตประจำวันที่</label>
              <div id="TopChartDateSelectLoader" style="display: none;">
                <div class="very_mini_loader">
                  <div class="loader mx-auto my">
                    <svg class="circular" viewBox="25 25 50 50">
                      <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>
            <select class="custom-select shadow-none" name="TopChartDateSelect" id="TopChartDateSelect">
              <?php for($i=0; $i < count($topChartDate); $i++){?>
                <option value="<?= e($topChartDate[$i]) ?>"><?= e(toReadableDateTime(strtotime($topChartDate[$i]))) ?></option>
              <?php }?>
            </select>
            <?php }?>
            </div>
          </div>
        </fieldset>
      </form>
      <div id="TopChartContainer"></div>
    </div>
  </div>
</div>
<template id="tmpl_topchart_table">
<?php
  $tmpl = file_get_contents(__DIR__.'/__tmpl_topChartTable.html');
  $tmplParam = array('{{_tc_date_}}' => "", '{{_tc_list_}}' => '', '{{_logo_dir_}}' => '../');
  echo parseTemplate($tmpl, $tmplParam);
?>
</template>

<?php
  include 'footer.php';
?>