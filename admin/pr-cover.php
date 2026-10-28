<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "public-relations";
  $active_sm = "page-cover";
  $enableSearch = false;
  $breadcrumbList = array(
    array($txt_var['public_relations'], 'pr-cover.php', 'public relations'),
    array($txt_var['page_cover_image'], 'pr-cover.php', 'page cover'),
  );
  $addtional_resources = array(
    array('js', 'assets/js/form.js'),
    array('js', 'assets/lib/angular/1.7.7/angular.min.js'),
    array('js', 'assets/js/app.js'),
    array('js', 'assets/js/ctrl/pr.js'),
    array('js', 'assets/js/imageProcess.js'),
    array('js', 'assets/lib/cropper/4.0.0/cropper.min.js'),
    array('css', 'assets/lib/cropper/4.0.0/cropper.min.css'),
  );
  require 'header.php'; 
  $prConfig = $siteConfig['pr-config'];

?>
<script>
  txt_var.upload_image_error = "<?=$txt_var['upload_image_error'];?>";
  txt_var.crop_img = "<?=$txt_var['crop_img'];?>";
  txt_var.delete_data = "<?=$txt_var['delete_data'];?>";
  txt_var.delete_data_confirm = "<?=$txt_var['delete_data_confirm'];?>";
  txt_var.image_size_notice_min = "<?=$txt_var['image_size_notice_min'];?>";
  txt_var.image_size_notice_max = "<?=$txt_var['image_size_notice_max'];?>";
  txt_var.image_size_notice_between = "<?=$txt_var['image_size_notice_between'];?>";
  txt_var.image_size_notice_exact = "<?=$txt_var['image_size_notice_exact'];?>";
  txt_var.file_not_support = "<?=$txt_var['file_not_support'];?>";
</script>
<div class="row" ng-controller="prCoverController" ng-init="prType = 1">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <h3><?=$txt_var['page_cover_image'];?></h3>
      <div class="page-menu-container">
        <button type="button" class="btn btn-primary btn-sm" id="openFormBtn" onclick="activatePrForm(true, '#FormCover_Container'); $(this).hide();" ng-click="removePrImage($event);"><i class="fas fa-plus mr-1"></i><?=$txt_var['add_new'];?></button>
      </div>
      <hr>
      <div style="display: none;" id="FormCover_Container">
        <div class="row">
          <div class="col-md-6 mx-auto">
            <div class="row">
              <div class="col-md-3" id="selectCoverImage">
                <div class="pr-form-img-preview mb-2">
                  <div class="square-parent">
                    <div class="square-child">
                      <button class="d-block w-100 h-100 bg-light btn text-black-50" style="font-size: 2.5rem;" onclick="document.getElementById('pr_image').click();" title="<?=$txt_var['select_image'];?>">
                        <i class="fas fa-image"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-9" id="displayCoverImage" style="display: none;">
                <div class="position-relative mb-2">
                  <div class="position-relative">
                    <div class="">
                      <img src="" class="w-100" id="previewCoverImage" />
                    </div>
                    <button class="d-block btn-sm btn btn-warning text-white pr-select-new-img" onclick="document.getElementById('pr_image').click();" title="<?=$txt_var['change_image'];?>">
                      <i class="fas fa-image"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-9">
                <form class="needs-validation" novalidate method="post" action="webservice/PR_CoverSubmit.php" id="FormCover">
                  <fieldset>
                    <div class="form-row">
                      <div class="col-12">
                        <input type="file" name="pr_image" id="pr_image" class="d-none" accept="<?= e(createInputFileAcceptAttr($prConfig['sponsor_ext'])) ?>" />

                        <div class="form-group">
                          <!-- <label for="pr_title"><?=$txt_var['title'];?></label> -->
                          <input type="text" class="form-control" name="pr_title" id="pr_title" required maxlength="255" placeholder="<?=$txt_var['title'];?>"> 
                        </div>

                        <div class="form-group">
                          <!-- <label for="pr_url"><?=$txt_var['connect_link'];?></label> -->
                          <input type="text" class="form-control ipUrl" name="pr_url" id="pr_url" value="#" required placeholder="<?=$txt_var['connect_link'];?>">
                        </div>
                       
                        <div class="form-group">
                          <button class="btn btn-primary" type="submit"><?=$txt_var['save'];?></button>
                          <button class="btn btn-danger" type="button" onclick="activatePrForm(false, '#FormCover_Container'); $('#openFormBtn').show();" ng-click="removePrImage($event);"><?=$txt_var['cancel'];?></button>
                        </div>
                        <small class="text-muted">{{formCoverHelpMessage}}</small>
                      </div>
                    </div>
                  </fieldset>
                </form>
              </div>
            </div>
          </div>
        </div>
        
        <hr>
      </div>
      <div>
        <div id="no-pr" ng-show="!coverList.length" ng-cloak>
          <div class="text-center text-muted">
            <?=$txt_var['no_data_preview'];?>
          </div>
        </div>
        <div ng-show="coverList.length" ng-cloak>
          <input type="file" name="pr_image_update" id="pr_image_update" class="d-none" accept="<?= e(createInputFileAcceptAttr($prConfig['sponsor_ext'])) ?>" />
          <div ng-repeat="(idCov, valueCov) in coverList track by idCov">
            <div class="alert alert-light alert-dismissible fade show border DisplayPr-{{valueCov.pr_id}}">
              <div class="row">
                
              <div class="col-md-3 col-lg-4">
                <div class="position-relative mb-2">
                  <div class="position-relative">
                    <div class="">
                      <img ng-src="{{valueCov.pr_image | trusted}}" class="w-100" />
                    </div>
                    <button class="d-block btn-sm btn btn-warning text-white pr-select-new-img" ng-click="updatePrImage($event, valueCov.pr_id, $index);"  title="<?=$txt_var['change_image'];?>">
                      <i class="fas fa-image"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-9 col-lg-8">
                
              <p class="text-dark">{{valueCov.pr_title}}</p>
              <hr>
              <p class="mb-0">{{valueCov.pr_url | trusted}}</p>
              </div>
              </div>

              <button type="button" class="close text-md" aria-label="Delete" ng-click="deleteSponser($event, valueCov.pr_id, $index);" title="<?=$txt_var['delete'];?>">
                <span aria-hidden="true"><i class="fas fa-trash-alt"></i></span>
              </button> 
              <button type="button" class="close text-md" aria-label="Update" ng-click="activePrUpdateForm($event, true, valueCov.pr_id);" style="top: 2rem;" title="<?=$txt_var['update'];?>">
                <span aria-hidden="true"><i class="fas fa-edit"></i></span>
              </button>
            </div>
            <div class="alert alert-light alert-dismissible border FormPrUpdate-{{valueCov.pr_id}}" style="display: none;">
              <div class="row">
                <div class="col-md-3 col-lg-4">
                  <div class="position-relative mb-2">
                    <div class="position-relative">
                      <div class="">
                        <img ng-src="{{valueCov.pr_image | trusted}}" class="w-100" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-9 col-lg-8">
                  <form class="FormPrCoverUpdate" action="webservice/PR_CoverUpdate.php" method="post" data-arr-ref="{{$index}}">
                    <fieldset>
                      <div class="form-row">
                        <div class="col-md-8">
                          <input type="hidden" name="id" value="{{valueCov.pr_id}}" />
                          <input type="hidden" name="update" value="info" />
                          <div class="form-group">
                            <!-- <label for="pr_title"><?=$txt_var['title'];?></label> -->
                            <input type="text" class="form-control" name="pr_title" value="" required maxlength="255" placeholder="<?=$txt_var['title'];?>">
                          </div>

                          <div class="form-group">
                            <!-- <label for="pr_url"><?=$txt_var['connect_link'];?></label> -->
                            <input type="text" class="form-control ipUrl" name="pr_url" value="" required placeholder="<?=$txt_var['connect_link'];?>">
                          </div>
                         
                          <div class="form-group">
                            <button class="btn btn-primary" type="submit"><?=$txt_var['save'];?></button>
                          </div>
                          
                        </div>
                      </div>
                    </fieldset>
                  </form>
                </div>
              </div>
              
              <button type="button" class="close text-md" aria-label="Cancel" ng-click="activePrUpdateForm($event, false, valueCov.pr_id);" title="<?=$txt_var['cancel'];?>">
                <span aria-hidden="true"><i class="fas fa-times"></i></span>
              </button> 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
  require 'footer.php';
?>