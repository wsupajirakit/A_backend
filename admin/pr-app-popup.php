<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "public-relations";
  $active_sm = "app-popup";
  $enableSearch = false;
  $breadcrumbList = array(
    array($txt_var['public_relations'], 'app-popup.php', 'public relations'),
    array($txt_var['app_popup'], 'app-popup.php', 'app popup'),
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
</script>
<div class="row" ng-controller="prAppPopupController" ng-init="prType = 4">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <h3><?=$txt_var['app_popup'];?></h3>
      <div class="page-menu-container">
        <button type="button" class="btn btn-primary btn-sm" id="openFormBtn" onclick="activatePrForm(true, '#FormAppPopup_Container'); $(this).hide();" ng-click="removePrImage($event);" ng-hide="appPopupList.length" ng-disabled="appPopupList.length"><i class="fas fa-plus mr-1"></i><?=$txt_var['add_new'];?></button>
      </div>
      <hr>
      <div style="display: none;" id="FormAppPopup_Container">
        <div class="row">
          <div class="col-md-6 mx-auto">
            <div class="row">
              <div class="col-md-3" id="selectAppPopupImage">
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
              <div class="col-md-3" id="displayAppPopupImage" style="display: none;">
                <div class="pr-form-img-preview mb-2">
                  <div class="square-parent">
                    <div class="square-child">
                      <img src="" class="w-100" id="previewAppPopupImage" />
                    </div>
                    <button class="d-block btn-sm btn btn-warning text-white select-new-img" onclick="document.getElementById('pr_image').click();" title="<?=$txt_var['change_image'];?>">
                      <i class="fas fa-image"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-9">
                <form class="needs-validation" novalidate method="post" action="webservice/PR_AppPopupSubmit.php" id="FormAppPopup">
                  <fieldset>
                    <div class="form-row">
                      <div class="col-12">
                        <input type="file" name="pr_image" id="pr_image" class="d-none" accept="<?= e(createInputFileAcceptAttr($prConfig['app_popup_ext'])) ?>" />

                        <div class="form-group">
                          <!-- <label for="pr_title"><?=$txt_var['title'];?></label> -->
                          <input type="text" class="form-control" name="pr_title" id="pr_title" required maxlength="255" placeholder="<?=$txt_var['title'];?>"> 
                        </div>

                        <div class="form-group">
                          <!-- <label for="pr_url"><?=$txt_var['connect_link'];?></label> -->
                          <input type="text" class="form-control ipUrl" name="pr_url" id="pr_url" value="#" required  placeholder="<?=$txt_var['connect_link'];?>">
                        </div>
                       
                        <div class="form-group">
                          <button class="btn btn-primary" type="submit"><?=$txt_var['save'];?></button>
                          <button class="btn btn-danger" type="button" onclick="activatePrForm(false, '#FormAppPopup_Container'); $('#openFormBtn').show();" ng-click="removePrImage($event);"><?=$txt_var['cancel'];?></button>
                        </div>
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
        <div id="no-pr" ng-show="!appPopupList.length" ng-cloak>
          <div class="text-center text-muted">
            <?=$txt_var['no_data_preview'];?>
          </div>
        </div>
        <div ng-show="appPopupList.length" ng-cloak>
          <input type="file" name="pr_image_update" id="pr_image_update" class="d-none" accept="<?= e(createInputFileAcceptAttr($prConfig['app_popup_ext'])) ?>" />
          <!-- <form novalidate method="post" action="webservice/PR_AppPopupUpdate.php" id="FormAppPopupUpdate">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="update" value="image" />
          </form> -->
          <div ng-repeat="(idPop, valuePop) in appPopupList track by idPop">
            <div class="alert alert-light alert-dismissible fade show border DisplayPr-{{valuePop.pr_id}}">
              <div class="row">
                
              <div class="col-md-2 col-lg-1">
                <div class="pr-form-img-preview mb-2">
                  <div class="square-parent">
                    <div class="square-child">
                      <img ng-src="{{valuePop.pr_image | trusted}}" class="w-100" />
                    </div>
                    <button class="d-block btn-sm btn btn-warning text-white select-new-img" ng-click="updatePrImage($event, valuePop.pr_id, $index);" title="<?=$txt_var['change_image'];?>">
                      <i class="fas fa-image"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-10 col-lg-11">
                
              <p class="text-dark">{{valuePop.pr_title}}</p>
              <hr>
              <p class="mb-0">{{valuePop.pr_url | trusted}}</p>
              </div>
              </div>

              <button type="button" class="close text-md" aria-label="Delete" ng-click="deleteAppPopup($event, valuePop.pr_id, $index);" title="<?=$txt_var['delete'];?>">
                <span aria-hidden="true"><i class="fas fa-trash-alt"></i></span>
              </button> 
              <button type="button" class="close text-md" aria-label="Update" ng-click="activePrUpdateForm($event, true, valuePop.pr_id);" style="top: 2rem;" title="<?=$txt_var['update'];?>">
                <span aria-hidden="true"><i class="fas fa-edit"></i></span>
              </button>
            </div>
            <div class="alert alert-light alert-dismissible border FormPrUpdate-{{valuePop.pr_id}}" style="display: none;">
              <div class="row">
                <div class="col-md-2 col-lg-1">
                  <div class="pr-form-img-preview mb-2">
                    <div class="square-parent">
                      <div class="square-child">
                        <img ng-src="{{valuePop.pr_image | trusted}}" class="w-100" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-10 col-lg-11">
                  <form class="FormPrAppPopupUpdate" action="webservice/PR_AppPopupUpdate.php" method="post" data-arr-ref="{{$index}}">
                    <fieldset>
                      <div class="form-row">
                        <div class="col-md-8">
                          <input type="hidden" name="id" value="{{valuePop.pr_id}}" />
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
              
              <button type="button" class="close text-md" aria-label="Cancel" ng-click="activePrUpdateForm($event, false, valuePop.pr_id);" title="<?=$txt_var['cancel'];?>">
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