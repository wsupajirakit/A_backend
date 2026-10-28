<?php
  require_once __DIR__.'/initialize.php';
  require_once __DIR__.'/../admin/assets/php-function/function.php';

  $webConfig = $siteConfig['web-config'];
  $postConfig = $siteConfig['post-config'];
  $active = "home";
  $siteMode = "normal";
  $htmlBodyClass = "";
  $htmlAppearance = $appearanceData["default"];
  $headerClass = "main-gradient shadow-sm";
  $showCover = true;
  $showAnnounceMessage = true;
  $showSponsor = true;
  $showSidebar = true;
  $pageTitle = $appData['about']['app_title'];

  $meta_lang = '';
  $meta_description = '';
  $meta_keywords = '';
  $meta_revisit_after = '3 days';
  $meta_robots = '';
  $meta_googlebot = '';
  $meta_author = '';

  $og_url = '';
  $og_type = '';
  $og_title = '';
  $og_description = '';
  $og_image = '';
  $og_image_secure = '';
  $og_image_type = '';
  $og_image_width = '';
  $og_image_height = '';

  $addtional_resources = array(
    array('js', 'assets/lib/jquery-ellipsis/jquery.ellipsis.min.js'),
    array('js', 'assets/lib/CSS-supports/0.4/CSS.supports.js'),
  );

  include 'header.php';

  # top chart data
  $lastestTopChart = $db->dbRow("SELECT * FROM top_chart ORDER BY top_chart_date DESC");
  if(null !== $lastestTopChart){
    $sql = "SELECT * FROM top_chart_song tcs INNER JOIN song s ON tcs.song_id = s.song_id WHERE tcs.top_chart_id = :tcid ORDER BY tcs.song_order ASC";
    $topChartData = $db->dbQuery($sql, array(':tcid' => $lastestTopChart['top_chart_id']));
  }

  # post data
  $nowPost = new DateTime();
  $nowPost = $nowPost->format('Y-m-d H:i:s');

  $articleData = $db->dbQuery("SELECT * FROM post WHERE publish_status = 1 AND publish_date <= :now ORDER BY publish_date DESC LIMIT 0, 4", array(':now' => $nowPost));
  # parse down post content
  for($i=0; $i < count($articleData); $i++){ 
    $articleData[$i]['post_content'] = parseContentHTML($articleData[$i]['post_content'], "o");
    $articleData[$i]['post_title'] = htmlspecialchars_decode($articleData[$i]['post_title']);

    $timeBkk = new DateTime($articleData[$i]['publish_date'], new DateTimeZone(date_default_timezone_get()));
    $timeBkk->setTimezone(new DateTimeZone('Asia/Bangkok'));
    $timePublish = $timeBkk->format('d/m/Y');

    $articleData[$i]['post_date_formatted'] = $timePublish;
  }
?>

<?php if(null!==$articleData && count($articleData) > 0){ 
    $postLink = GetArticleURL($articleData[0]['post_title'], $articleData[0]['post_slug']);
?>
<div class="post-content-container">
  <div class="home-lastest-post-header position-relative mb-3">
    <a href="<?= e($postLink) ?>">
      <figure class="m-0">
        <img src="<?= e($postConfig['cover_url'] . $articleData[0]['post_cover']) ?>" alt="<?= e($articleData[0]['post_title']) ?>" class="d-block w-100" title="<?= e($articleData[0]['post_title']) ?>" />
      </figure>
    </a>
    <div class="post-title w-100">
      <a href="<?= e($postLink) ?>">
        <h1><?= e($articleData[0]['post_title']) ?></h1>
      </a>
    </div>
  </div>
  <div class="home-lastest-post-body">
    <article class="text-justify"><?php // trusted HTML from admin editor
    echo getContentPreview($articleData[0]['post_content'], "auto"); ?></article>
    <div class="text-right">
      <a href="<?= e($postLink) ?>" class="small-2 font-italic text-muted">อ่านต่อ..</a>
    </div>
  </div>
</div>
<?php } ?>

<?php if(null !== $lastestTopChart){
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
  // .   '<td>'.$value["song_name"].'</td>'
        .   '<td><div class="d-flex">'.$_udFaw.'<span>'.e($value["song_name"]).'</span></div></td>'
  
  .   '<td>'.e($value["artist_name_map"]).'</td>'
  .   '<td class="text-center">' . e($_lwo) . '</td>'
  .   '<td class="text-center">' . e($_ocwn) . '</td>'
  .   '</tr>';
}


  $tmpl = file_get_contents(__DIR__.'/__tmpl_topChartTable.html');
  $tmplParam = array('{{_tc_date_}}' => ("อันดับเพลงฮิตประจำวันที่ " . toReadableDateTime(strtotime($lastestTopChart['top_chart_date']))), '{{_tc_list_}}' => $tcListString, '{{_logo_dir_}}' => '../');
  echo parseTemplate($tmpl, $tmplParam);
} ?>

<?php if(null!==$articleData && count($articleData) > 0){
  for($i=1; $i < count($articleData); $i++){ 
    $postLink = GetArticleURL($articleData[$i]['post_title'], $articleData[$i]['post_slug']);
?>
<div class="mb-3">
  <div class="row">
    <div class="col-md-5">
      <div class="home-post-cover position-relative">
        <div class="wrapper">
          <a href="<?= e($postLink) ?>">
            <figure>
              <img src="<?= e($postConfig['cover_url'] . $articleData[$i]['post_cover']) ?>" alt="<?= e($articleData[$i]['post_title']) ?>" class="d-block w-100" title="<?= e($articleData[$i]['post_title']) ?>" />
            </figure>
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <div class="home-post-header">
        <a href="<?= e($postLink) ?>" class="title">
          <h1><?= e($articleData[$i]['post_title']) ?></h1>
        </a>
        <small class="text-muted"><?= e($articleData[$i]['post_date_formatted']) ?></small>
      </div>
      <div class="home-post-body">
        <article class="post-preview-p mb-1 small-2 line-clamp-3 text-justify" data-ellip-line="3"><?= e(cleanContentTag(getContentPreview($articleData[$i]['post_content'], "auto"))) ?></article>
        <div class="text-right">
          <a href="<?= e($postLink) ?>" class="small-2 font-italic text-muted">อ่านต่อ..</a>
        </div>
      </div>
    </div>
  </div>
  
</div>  
<?php  }
  }
?>

<?php
  include 'footer.php';
?>