<?php
  require_once __DIR__.'/initialize.php';
  require_once __DIR__.'/../admin/assets/php-function/function.php';
  require_once __DIR__.'/../admin/assets/php-function/pdo-database.php';
  require_once __DIR__.'/../admin/assets/message_var/th.php';

  $db = new DatabaseConnection();

  $slug = (string) ($_GET['s'] ?? '');
  $nowPost = time("Y-m-d H:i:s");
  $postData = $db->dbRow("SELECT * FROM post WHERE post_slug = :sg AND publish_status = 1 AND publish_date > :now", array(':sg' => $slug, ':now' => $nowPost));

  $webConfig = $siteConfig['web-config'];
  $postConfig = $siteConfig['post-config'];
  $active = "post";
  $siteMode = "normal";
  $htmlBodyClass = "";
  $htmlAppearance = $appearanceData["default"];
  $headerClass = "main-gradient shadow-sm";
  $showCover = false;
  $showAnnounceMessage = false;
  $showSponsor = false;
  $showSidebar = false;
  if($postData !== null){
    $postContent = parseContentHTML($postData['post_content'], "o");
    $postPreview = cleanContentTag(getContentPreview($postContent, "auto"));
    list($pDate, $pTime) = explode(" ", $postData['publish_date']);
    $postCover = $postConfig['cover_url'] . $postData['post_cover'];
    $postTitle = htmlspecialchars_decode($postData['post_title']);
    $pageTitle = $postTitle . " : " . $appData['about']['app_title'];
  }
  else{
    $pageTitle = $txt_var['data_not_found'] . " : " . $appData['about']['app_title'];
  }

  $addtional_resources = array(
    array('css', 'assets/css/post.css?v=10000002'),
  );

  $meta_lang = '';
  $meta_description = $postTitle  . ' - ' . $postPreview . ' | ' . 'บทความและ ข่าวสารจาก '. $appData['about']['app_name'];
  $meta_keywords = $meta_keywords_default . ',' . 'ข่าวสาร,บทความ,วงการบันเทิง,วงการเพลง,อัพเดทข่าวสาร,' . $postTitle;
  $meta_revisit_after = '';
  $meta_robots = '';
  $meta_googlebot = '';
  $meta_author = '';

  $og_url = 'https://www.example.com/post.php?read=' . str_replace(" ", "+", $postTitle) . '&s=' . $slug;
  $og_type = 'article';
  $og_title = $postTitle;
  $og_description = $postTitle  . ' - ' . $postPreview . ' | ' . 'บทความและ ข่าวสารจาก '. $appData['about']['app_name'];
  $og_image = $postCover;
  $og_image_secure = $postCover;
  $og_image_type = getImageMIMEType($postCover);
  list($cvW, $cvH) = getimagesize($postCover);
  $og_image_width = $cvW;
  $og_image_height = $cvH;

  include 'header.php';
?>
<div class="mb-3">

  <?php if($postData !== null){ ?>

    <div class="post-cover-container">
      <figure class="w-100">
        <img src="<?= e($postCover) ?>" alt="<?= e($postTitle) ?>" class="w-100" />
      </figure>
    </div>
    <div/  class="col-md-10 mx-auto">
    <h1 class="text-center"><?= e($postTitle) ?></h1>
    <br />
      <article id="post-content" class="text-justify">
        <?php // trusted HTML from admin editor ?>
        <?=$postContent;?>
      </article>
      <br />
      <br />
      <?php
      $timeBkk = new DateTime($postData['publish_date'], new DateTimeZone(date_default_timezone_get()));
      $timeBkk->setTimezone(new DateTimeZone('Asia/Bangkok'));
      $timePublish = $timeBkk->format('d/m/Y');
      ?>
      <p class="text-muted text-right small-2">เผยแพร่เมื่อ <?= e($timePublish) ?></p>
    </div>

  <?php }else{ ?>

    <div class="my-5" >
      <div class="row">
        <div class="col-md-10 mx-auto">
          <div id="no_data">
          <?php
            $tmpl = file_get_contents(__DIR__.'/__tmpl_noData.html');
            $tmplParam = array('{{_url_}}' => 'all-post.php', '{{_msg_}}' => $txt_var['data_not_found'], '{{_back_}}' => $txt_var['back']);
            echo parseTemplate($tmpl, $tmplParam);
          ?>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>

</div>

<?php
  include 'footer.php';
?>