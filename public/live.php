<?php
  require_once __DIR__.'/initialize.php';
  require_once __DIR__.'/../admin/assets/php-function/function.php';

  $siteMode = "live-adio";
  $active = "live-radio";
  $htmlBodyClass = "";
  $htmlAppearance = $appearanceData["radio"];
  $headerClass = "header-transparent";
  $showCover = false;
  $showAnnounceMessage = false;
  $showSponsor = false;
  $showSidebar = false;
  $pageTitle = "ฟังวิทยุออนไลน์" . " : " . $appData['about']['app_title'];

  $addtional_resources = array(
    array('js', 'assets/lib/plyr/3.5.3/plyr.polyfilled.js'),
    array('css', 'assets/lib/plyr/3.5.3/plyr.css'),
  );

  $meta_lang = '';
  $meta_description = 'ฟังเพลงเพราะๆ ออนไลน์กับ '. $appData['about']['app_name'] . ' คลื่นวิทยุอันดับ 1';
  $meta_keywords = $meta_keywords_default . ',' . 'ฟังเพลงออนไลน์,ฟังวิทยุออนไลน์,เพลงเพราะ';
  $meta_revisit_after = '';
  $meta_robots = '';
  $meta_googlebot = '';
  $meta_author = '';

  $og_url = 'https://www.example.com/live.php';
  $og_type = 'music.radio_station';
  $og_title = 'ฟังเพลงเพราะๆ กับ ' . $appData['about']['app_name'];
  $og_description = 'ฟังเพลงเพราะๆ ออนไลน์กับ '. $appData['about']['app_name'] . 'คลื่นวิทยุอันดับ 1';
  $og_image = '';
  $og_image_secure = '';
  $og_image_type = '';
  $og_image_width = '';
  $og_image_height = '';

  include 'header.php';
?>
<div class="position-relative">
  <div class="radio-stream-icon mt-5">
    <div class="main-gradient rounded-circle shadow mx-auto circle-1 position-relative">
      <div class="square-parent">
        <div class="square-child">
          <div class="rounded-circle mx-auto circle-2">
            <div class="rounded-circle mx-auto circle-2">
              <div class="square-parent">
                <div class="square-child">
                  <div>
                    <figure class="m-0 p-0">
                      <img src="<?= e($appData['about']['logo_white']) ?>" alt="" class="w-100" />
                    </figure>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container mt-5">
    <audio  playsinline>
      <source src="<?= e($streamUrl) ?>">
    </audio>
  </div>
</div>
<!-- Plyr resources and browser polyfills are specified in the pen settings -->
<!-- </div> -->
<script>
(function(){
  // https://github.com/sampotts/plyr/#options
  const player = new Plyr('audio', {
    autoplay: true,
    controls: ['play', 'mute', 'volume', 'current-time']
  });

  // Expose player so it can be used from the console
  // window.player = player;

  $('html').css('height', '100%');
})();
</script>
  </body>
</html>