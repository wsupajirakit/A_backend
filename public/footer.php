<?php if($siteMode=="normal"){?>
        </div>
        <!-- SIDEBAR -->
        <?php if(true === $showSidebar){?>
        <div class="col-md-3 col-lg-3 order-md-first">
          <?php if(true === $showSponsor){?>
          <div id="sponsorContainer" class="row">
            <?php foreach($sponsorData as $key => $value){ ?>
            <div class="col-sm-6 col-md-12">
              <a href="<?= e($value['pr_url']) ?>" class="d-block shadow-sm" target="_blank" title="<?= e($value['pr_title']) ?>" rel="nofollow">
                <figure>
                  <img src="<?= e($prConfig['sponsor_url'] . $value['pr_image']) ?>" alt="<?= e($value['pr_title']) ?>" class="w-100 d-block" />
                </figure>
              </a>
            </div>
            <?php } ?>
          </div>
          <?php } ?>
        </div>
        <?php }?>
        <!-- SIDEBAR -->
      </div>
    </div>
  </main>
<?php }?>

  <footer class="footer main-gradient">
    <div class="footer-info w-100">
      <div class="container py-3">
        <div class="d-flex align-items-center mb-1">
          <figure class="m-0 mr-2" style="width: 30px;"><img src="<?= e($appData['about']['logo_no_text_white']) ?>" alt="<?= e($appData['about']['app_name']) ?>" class="img-fluid" /></figure>
          <h6 class="text-white mt-1"><?= e($appData['about']['app_name']) ?></h6>
        </div>
        <div class="row">
          <div class="col-md-4 mb-5 mb-md-0 text-white">
            <p class="small-2 mb-1 text-white">
              <span><?= e($appData['about']['organization_name']) ?></span><br />
              <span>เลขประจำตัวผู้เสียภาษี: <?= e($appData['about']['tax_id']) ?></span><br />
              <span><?= e($appData['contact']['full_address']) ?></span>
            </p>
            <p class="small-2 mb-1 text-white">
              <span>โทรศัพท์: <?= e(implode(", ", $appData['contact']['phone'])) ?></span>
            </p>
            <p class="small-2 mb-1 text-white">
              <span>อีเมล: <?= e(implode(", ", $appData['contact']['email'])) ?></span>
            </p>
            <ul class="no-bullets mt-4">
              <?php
              if(isset($appData['social'])){
                foreach($appData['social'] as $key => $value){
              ?>
              <li style="float: left;" class="mr-3">
                <a href="<?= e($value['url']) ?>" class="text-white d-block text-center rounded-circle bg-<?= e($value['key']) ?>" title="<?= e($value['name']) ?>" style="width: 35px; height: 35px; line-height: 35px;" target="_blank">
                  <span class="d-inline-block"><i class="fab fa-<?= e($value['key']) ?>"></i></span>
                </a>
              </li>
              <?php
                }
              }
              ?>
            </ul>
          </div>
          <div class="col-md-4 mb-5 mb-md-0 text-white">
            <ul class="no-bullets small-2">
              <li><a href="privacy-policy.php" class="text-white">นโยบายความเป็นส่วนตัว</a></li>
              <li><a href="terms-of-use.php" class="text-white">ข้อตกลงในการใช้งาน</a></li>
            </ul>
            <div class="w-100 mb-3"></div>
            <ul class="no-bullets small-2">
              <li class="mb-1"><a href="#" class="text-white">A Radio แอพพลิเคชัน</a></li>
              <li>
                <a href="<?= e($appData['mobile_app']['download']['android']) ?>" class="text-white" target="_blank">
                  <div class="getApp">
                    <figure class="m-0"><img src="assets/img/ui/google-play-badge-th.png?v=1001" alt="Android app"></figure>
                  </div>
                </a>
              </li>
              <li>
                <a href="<?= e($appData['mobile_app']['download']['ios']) ?>" class="text-white" target="_blank">
                  <div class="getApp">
                    <figure class="m-0"><img src="assets/img/ui/app-store-badge-th.png?v=1001" alt="iOS app"></figure>
                  </div>
                </a>
              </li>
            </ul>
          </div>
          <div class="col-md-4 mb-5 mb-md-0 text-white">
            <div class="fb-page" data-href="https://www.facebook.com/example/" data-tabs="timeline" data-width="500" data-height="200" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/example/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/example/">Official</a></blockquote></div>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-license text-center py-1 small">
      <span class="text-white-50">&copy; <?= e(date("Y")) ?> Copyright</span> <span class="text-white"><?= e($appData['about']['app_name']) ?></span>
    </div>
  </footer>
</body>
</html>
