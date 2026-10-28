<?php
  require_once __DIR__.'/initialize.php';
  require_once __DIR__.'/../admin/assets/php-function/function.php';
  require_once __DIR__.'/../admin/assets/message_var/th.php';
  require_once __DIR__.'/../admin/assets/lib/Parsedown/Parsedown.php';
  $Parsedown = new Parsedown();

  $webConfig = $siteConfig['web-config'];
  $postConfig = $siteConfig['post-config'];
  $active = "post";
  $siteMode = "normal";
  $htmlBodyClass = "";
  $htmlAppearance = $appearanceData["default"];
  $headerClass = "main-gradient shadow-sm";
  $showCover = false;
  $showAnnounceMessage = false;
  $showSponsor = true;
  $showSidebar = true;
  $pageTitle = $appData['about']['app_title'];

  $addtional_resources = array(
    array('js', 'assets/lib/jquery-ellipsis/jquery.ellipsis.min.js'),
  );

  // check page parameter must be numeric only
  $page = isset($_GET['page']) ? $_GET['page'] : 1;
  if(!validateData('numeric', $page)){
    header("Location: all-post.php?page=1");
    die();
  }

  $meta_lang = '';
  $meta_description = 'บทความและ ข่าวสารจาก '. $appData['about']['app_name'];
  $meta_keywords = $meta_keywords_default . ',' . 'ข่าวสาร,บทความ,วงการบันเทิง,วงการเพลง,อัพเดทข่าวสาร';
  $meta_revisit_after = '7 days';
  $meta_robots = '';
  $meta_googlebot = '';
  $meta_author = '';

  $og_url = 'https://www.example.com/all-post.php';
  $og_type = 'website';
  $og_title = "บทความและ ข่าวสารจาก " . $appData['about']['app_name'];
  $og_description = '';
  $og_image = '';
  $og_image_secure = '';
  $og_image_type = '';
  $og_image_width = '';
  $og_image_height = '';

  include 'header.php';

  $postPerPage = 10;
  $postOffset = max(0, ((int) $page - 1) * $postPerPage);
  # post data
  $nowPost = time("Y-m-d H:i:s");
  $db->setPDOAttribute('EMULATE_PREPARES', false);
  $nowPost = time("Y-m-d H:i:s");
  $articleData = $db->dbQuery("SELECT * FROM post WHERE publish_status = 1 AND publish_date > :now ORDER BY publish_date DESC LIMIT :of, :lm", array(':now' => $nowPost, ':of' => $postOffset, ':lm' => $postPerPage));

  # parse down post content
  for($i=0; $i < count($articleData); $i++){ 
    $articleData[$i]['post_content'] = $Parsedown->text($articleData[$i]['post_content']);
    $articleData[$i]['post_title'] = htmlspecialchars_decode($articleData[$i]['post_title']);

    $timeBkk = new DateTime($articleData[$i]['publish_date'], new DateTimeZone(date_default_timezone_get()));
    $timeBkk->setTimezone(new DateTimeZone('Asia/Bangkok'));
    $timePublish = $timeBkk->format('d/m/Y');

    $articleData[$i]['post_date_formatted'] = $timePublish;
  }
?>

<div class="mb-3">
<?php if(null!==$articleData && count($articleData) > 0){ ?>
  <h1 class="text-center">บทความและ ข่าวสาร</h1>
  <br />
  <div>
    <div class="row">
  <?php
    for($i=0; $i < count($articleData); $i++){
      $postLink = GetArticleURL($articleData[$i]['post_title'], $articleData[$i]['post_slug']);
  ?>
    <div class="mb-4 col-md-4">
      
      <div class="card h-100 border-0">
        <a href="<?= e($postLink) ?>">
          <img src="<?= e($postConfig['cover_url'] . $articleData[$i]['post_cover']) ?>" alt="<?= e($articleData[$i]['post_title']) ?>" title="<?= e($articleData[$i]['post_title']) ?>" class="rounded card-img-top" />
        </a>
        <div class="card-body px-0 py-2">
          <h6 class="card-title mb-1"><a href="<?= e($postLink) ?>" class="text-dark"><?= e($articleData[$i]['post_title']) ?></a></h6>
        </div>
        <div class="card-footer bg-transparent border-0 px-0 py-1">
          <small class="text-muted"><i class="far fa-clock mr-1"></i><span><?= e($articleData[$i]['post_date_formatted']) ?></span></small>
        </div>
      </div>
    </div>
  
<?php } ?>
</div>
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

<?php
  # create pagination
  $count = $db->dbCount("SELECT COUNT(*) FROM post WHERE publish_status = 1 AND publish_date > :now", array(':now' => $nowPost));
  $total_page = $count / $postPerPage; // total page of content query result
  if($total_page >= 1){ // round total page number
    if(($count % $postPerPage) > 0.0){
      $total_page = ceil($total_page);
    }
  }else{
    $total_page = 0;
  }

  $current_page = $page;
  $page_length = 5; // amount of pagination button not include previous, next, first, last 
  $page_start = 0;  // first button of pagination
  $page_end = 0;    // last button of pagination
  $page_first = 1;  // first page
  $page_last = $total_page; // last page
  $page_prev = $current_page - 1; // prev page
  $page_next = $current_page + 1; // next page

  if($total_page > $page_length) {
    $page_center = ceil($page_length/2);
    $page_side = floor($page_length/2);

    $page_start = $current_page - $page_side;
    $page_end = $current_page + $page_side;

    if($page_start < 1){
      $page_start = 1;
      $page_end = $page_length;
    }

    if($page_end > $total_page){
      $page_start = $total_page - $page_length;
      $page_end = $total_page;
    }
  }
  else if($total_page > 0){
    $page_start = 1;
    $page_end = $total_page;
  }

  if($total_page > 0){
?>
<div id="paginationContainer" class="mt-5">
  <nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
      <!-- previous page & first page -->
      <!-- first -->
      <li class="page-item <?php if($current_page<=$page_first){ echo "disabled"; }?>">
        <a class="page-link" href="<?= e("all-post.php?page={$page_first}") ?>" aria-label="First Page">
          <span aria-hidden="true">&#171;</span>
        </a>
      </li>

      <!-- previous -->
      <li class="page-item <?php if($page_prev<$page_first){ echo "disabled";}?>">
        <a class="page-link" href="<?= e("all-post.php?page={$page_first}") ?>" aria-label="Previous">
          <span aria-hidden="true">&#8249;</span>
        </a>
      </li>
      
    <?php
    for ($i = $page_start; $i <= $page_end; $i++){ 
      if($i == $current_page){
        ?>
        <li class="page-item active" aria-current="page"><a class="page-link" href="<?= e("all-post.php?page={$i}") ?>"><?= e($i) ?> <span class="sr-only">(current)</span></a></li>
        <?php
      }else{
        ?>
        <li class="page-item"><a class="page-link" href="<?= e("all-post.php?page={$i}") ?>"><?= e($i) ?></a></li>
        <?php
      }
    }
    ?>
      <!-- next page & last page -->
      <!-- next -->
      <li class="page-item <?php if($page_next>$page_last){ echo "disabled";}?>">
        <a class="page-link" href="<?= e("all-post.php?page={$page_next}") ?>" aria-label="Next">
          <span aria-hidden="true">&#8250;</span>
        </a>
      </li>

      <!-- last -->
      <li class="page-item <?php if($current_page>=$page_last){ echo "disabled";}?>">
        <a class="page-link" href="<?= e("all-post.php?page={$page_last}") ?>" aria-label="Next">
          <span aria-hidden="true">&#187;</span>
        </a>
      </li>
    </ul>
  </nav>
</div>
<?php } ?>
</div>

<?php
  include 'footer.php';
?>
