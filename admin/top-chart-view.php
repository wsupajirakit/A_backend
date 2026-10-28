<?php
  require_once __DIR__.'/check_user_session.php';
  require_once __DIR__.'/assets/php-function/pdo-database.php';
  require_once __DIR__.'/assets/php-function/function.php';
  require_once __DIR__.'/assets/message_var/th.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />

  <link rel="apple-touch-icon" sizes="57x57" href="../logo/favicon/apple-icon-57x57.png" />
  <link rel="apple-touch-icon" sizes="60x60" href="../logo/favicon/apple-icon-60x60.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="../logo/favicon/apple-icon-72x72.png" />
  <link rel="apple-touch-icon" sizes="76x76" href="../logo/favicon/apple-icon-76x76.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="../logo/favicon/apple-icon-114x114.png" />
  <link rel="apple-touch-icon" sizes="120x120" href="../logo/favicon/apple-icon-120x120.png" />
  <link rel="apple-touch-icon" sizes="144x144" href="../logo/favicon/apple-icon-144x144.png" />
  <link rel="apple-touch-icon" sizes="152x152" href="../logo/favicon/apple-icon-152x152.png" />
  <link rel="apple-touch-icon" sizes="180x180" href="../logo/favicon/apple-icon-180x180.png" />
  <link rel="icon" type="image/png" sizes="192x192"  href="../logo/favicon/android-icon-192x192.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="../logo/favicon/favicon-32x32.png" />
  <link rel="icon" type="image/png" sizes="96x96" href="../logo/favicon/favicon-96x96.png" />
  <link rel="icon" type="image/png" sizes="16x16" href="../logo/favicon/favicon-16x16.png" />
  <meta name="msapplication-TileColor" content="#ffffff" />
  <meta name="msapplication-TileImage" content="/ms-icon-144x144.png" />
  <meta name="theme-color" content="#ffffff" />

  <?php
    $tc_id = (int) ($_GET['id'] ?? 0);
    $qTopChart = $db->dbRow("SELECT * FROM top_chart WHERE top_chart_id = :id", array(':id' => $tc_id));
    if(null !== $qTopChart){
      $sql = "SELECT * FROM top_chart_song tcs INNER JOIN song s ON tcs.song_id = s.song_id WHERE tcs.top_chart_id = :tcid ORDER BY tcs.song_order ASC";
      $topChartData = $db->dbQuery($sql, array(':tcid' => $qTopChart['top_chart_id']));
      $tcListString = '';
      foreach($topChartData as $key => $value){ 
        $_lwo = empty($value['last_week_order']) ? "" : $value['last_week_order'];
        $_ocwn = empty($value['on_chart_week_number']) ? "" : $value['on_chart_week_number'];

        if($_lwo != ""){
          if(intval($value["song_order"]) < intval($_lwo)){
            $_udFaw = '<div class="tcod-icon mr-2"><i class="fas fa-chevron-circle-up tcod-up"></i></div>';
          }else if(intval($value["song_order"]) > intval($_lwo)){
            $_udFaw = '<div class="tcod-icon mr-2"><i class="fas fa-chevron-circle-down tcod-down"></i></div>';
          }else{
            $_udFaw = '<div class="tcod-icon mr-2"><i class="fas fa-minus-circle tcod-new"></i></div>';
          }
        }else{
          $_udFaw = '<div class="tcod-icon mr-2"><i class="fas fa-minus-circle tcod-new"></i></div>';
        }
        $tcListString 
        .=  '<tr>'
        .   '<td>'.e($value["song_order"]).'</td>'
        .   '<td><div class="d-flex">'.$_udFaw.'<span>'.e($value["song_name"]).'</span></div></td>'
        .   '<td>'.e($value["artist_name_map"]).'</td>'
        .   '<td class="text-center">' . e($_lwo) . '</td>'
        .   '<td class="text-center">' . e($_ocwn) . '</td>'
        .   '</tr>';
      }

      $dTitle = str_replace("{n}", toReadableDateTime(strtotime($qTopChart['top_chart_date'])), $txt_var['top_chart_of_date']);
      $tmpl = file_get_contents(__DIR__.'/../public/__tmpl_topChartTable.html');
      $tmplParam = array('{{_tc_date_}}' => e($dTitle), '{{_tc_list_}}' => $tcListString, '{{_logo_dir_}}' => '../');
    }
  ?>

  <title><?= e($dTitle) ?></title>

  <script src="assets/lib/jquery/3.3.1/jquery.min.js"></script>
  <script src="assets/lib/popper/1.14.3/popper.min.js"></script>
  <script src="assets/lib/html2canvas/1.0.0-rc.3/html2canvas.min1.0.0-alpha12.js"></script>

  <link rel="stylesheet" href="assets/lib/bootstrap/4.1.3/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/lib/bootstrap/bootstrap-custom.css" />
  <script src="assets/lib/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  
  <link rel="stylesheet" href="assets/lib/fontawesome/5.3.1/css/all.min.css" />

  <link rel="stylesheet" href="assets/css/style.css?v=1000001" />
  <link rel="stylesheet" href="../assets/css/template.css?v=1000002" />
  <link rel="stylesheet" href="../assets/css/theme.css?v=1000001" />

  <script src="assets/js/main.js"></script>

  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Bai+Jamjuree:300,300i,400,400i,500,500i,700,700i|Pattaya" rel="stylesheet" />
  
</head>
<body>
  <div class="container py-5">
    <h3><?= e($dTitle) ?></h3>
    <br />
    <div id="topChart">
<?php
  if(null !== $qTopChart){
?>
  <div id="topChartTable">
    <?php echo parseTemplate($tmpl, $tmplParam); ?>
  </div>
  <div class="btn-group" role="group" id="download-group">
    <button id="btnGroupDrop" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-download mr-2"></i><?=$txt_var['download_radio_program_image'];?></button>
    <div class="dropdown-menu" aria-labelledby="btnGroupDrop" id="btnGroupDropList"></div>
  </div>

  <script>
  	    // allowTaint: true, foreignObjectRendering: true, 
    window.onload = function(){
      showLoader(true);
      $('#download-group').button('toggle');
      $('.top-chart-container').removeClass('shadow-sm');
      $('.top-chart-container').removeClass('my-5');
      var targetTable = document.querySelector("#topChartTable");
      $(targetTable).css('width', '900px');
      html2canvas(targetTable, { scale: 3, }).then(canvas=>{
        $(targetTable).css('width', '');
        var url = canvas.toDataURL("image/png", 1.0);
        $('#capture').attr("src", url);
        var _link = document.createElement('A');
        _link.innerText = "<?=$txt_var['image'];?>";
        _link.className = "dropdown-item";
        _link.href = url;
        _link.download = "top chart " + <?= json_encode($dTitle, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?> + ".png";
        document.getElementById('btnGroupDropList').appendChild(_link);
        setTimeout( showLoader(false) , 300);
      });
    }
    $(document).ready(function($){
      // showLoader(true);
      // $('#download-group').button('toggle');
      // $('.top-chart-container').removeClass('shadow-sm');
      // $('.top-chart-container').removeClass('my-5');
      // var targetTable = document.querySelector("#topChartTable");
      // $(targetTable).css('width', '900px');
      // html2canvas(targetTable, { scale: 1, }).then(canvas=>{
      //   $(targetTable).css('width', '');
      //   var url = canvas.toDataURL("image/png", 1.0);
      //   $('#capture').attr("src", url);
      //   var _link = document.createElement('A');
      //   _link.innerText = "<?=$txt_var['image'];?>";
      //   _link.className = "dropdown-item";
      //   _link.href = url;
      //   _link.download = "top chart " + <?= json_encode($dTitle, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?> + ".png";
      //   document.getElementById('btnGroupDropList').appendChild(_link);
      //   setTimeout( showLoader(false) , 300);
    });
  </script>
<?php
  }
?>
  </div>
</div>
<div class="d-none">
  <img src="" id="capture" alt="">
</div>
<?php include 'loader_dom.html'; ?>
</body>
</html>