<?php
  require_once __DIR__.'/initialize.php';
  require_once __DIR__.'/../admin/assets/php-function/function.php';

  $active = "contact";
  $siteMode = "normal";
  $htmlBodyClass = "";
  $htmlAppearance = $appearanceData["default"];
  $headerClass = "main-gradient shadow-sm";
  $showCover = false;
  $showAnnounceMessage = false;
  $showSponsor = true;
  $showSidebar = true;
  $pageTitle = "ติดต่อเรา" . " : " . $appData['about']['app_title'];

  $meta_lang = '';
  $meta_description = 'ติดต่อเรา '. $appData['about']['app_name'] . ' ' . $appData['about']['organization_name'];
  $meta_keywords = $meta_keywords_default . ',' . 'ติดต่อ';
  $meta_revisit_after = '1 year';
  $meta_robots = '';
  $meta_googlebot = '';
  $meta_author = '';

  $og_url = 'https://www.example.com/contact.php';
  $og_type = 'website';
  $og_title = "ติดต่อเรา - " . $appData['about']['app_name'];
  $og_description = '';
  $og_image = '';
  $og_image_secure = '';
  $og_image_type = '';
  $og_image_width = '';
  $og_image_height = '';

  include 'header.php';
?>

<div class="mb-3">
  <h1 class="text-center">ติดต่อเรา</h1>
  <br />
  <div class="row">
    <div class="col-md-8 mx-auto">
      <table class="table table-borderless table-fixed w-100">
        <colgroup><col style="width: 30px;" /><col /></colgroup>
        <tr>
          <td class="pb-1"><div class="pink"><i class="fas fa-map-marker-alt"></i></div></td>
          <td class="pb-1"><div class="text-muted small-2">ที่อยู่</div></td>
        </tr>
        <tr>
          <td class="pt-0"></td>
          <td class="pt-0">
            <div><?= e($appData['about']['organization_name']) ?></div>
            <div><?= e($appData['contact']['full_address']) ?></div>
          </td>
        </tr>

        <tr>
          <td class="pb-1"><div class="pink"><i class="fas fa-phone"></i></div></td>
          <td class="pb-1"><div class="text-muted small-2">โทรศัพท์</div></td>
        </tr>
        <tr>
          <td class="pt-0"></td>
          <td class="pt-0">
            <div><?= e(implode(", ", $appData['contact']['phone'])) ?></div>
          </td>
        </tr>

        <tr>
          <td class="pb-1"><div class="pink"><i class="fas fa-envelope"></i></div></td>
          <td class="pb-1"><div class="text-muted small-2">อีเมล</div></td>
        </tr>
        <tr>
          <td class="pt-0"></td>
          <td class="pt-0">
            <div><?= e(implode(", ", $appData['contact']['email'])) ?></div>
          </td>
        </tr>

        <tr>
          <td class="pb-1"><div class="pink"><i class="fas fa-share-alt"></i></div></td>
          <td class="pb-1"><div class="text-muted small-2">โซเชียลมีเดีย</div></td>
        </tr>
        <tr>
          <td class="pt-0"></td>
          <td class="pt-0">
            <?php
            if(isset($appData['social'])){
              foreach($appData['social'] as $key => $value){
            ?>
            <div class="mt-3">
              <a href="<?= e($value['url']) ?>" class="text-decoration-none text-white d-block py-1 px-3 rounded contact-sc bg-<?= e($value['key']) ?>" target="_blank">
                <span class="ico d-inline-block mr-2"><i class="fab fa-<?= e($value['key']) ?>"></i></span>
                <span><?= e($value['name']) ?></span>
              </a>
            </div>
            <?php
              }
            } 
            ?>
          </td>
        </tr>

      </table>
    </div>
  </div>
</div>

<?php
  include 'footer.php';
?>