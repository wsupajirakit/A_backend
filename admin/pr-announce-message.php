<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "public-relations";
  $active_sm = "announce-message";
  $enableSearch = false;
  $breadcrumbList = array(
    array($txt_var['public_relations'], 'pr-announce-message.php', 'public relations'),
    array($txt_var['announce_message'], 'pr-announce-message.php', 'announce message'),
  );
  $addtional_resources = array(
    array('js', 'assets/js/form.js'),
    array('js', 'assets/lib/angular/1.7.7/angular.min.js'),
    array('js', 'assets/js/app.js'),
    array('js', 'assets/js/ctrl/pr.js'),
  );
  require 'header.php'; 
?>
<script>
  txt_var.delete_data = "<?=$txt_var['delete_data'];?>";
  txt_var.delete_data_confirm = "<?=$txt_var['delete_data_confirm'];?>";
</script>
<div class="row" ng-controller="prAnnouceMessageController" ng-init="prType = 3">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <h3><?=$txt_var['announce_message'];?></h3>
      <div class="page-menu-container">
        <button type="button" class="btn btn-primary btn-sm" id="openFormBtn" onclick="activatePrForm(true, '#FormAnnounceMessage_Container'); $(this).hide();"><i class="fas fa-plus mr-1"></i><?=$txt_var['add_new'];?></button>
      </div>
      <hr>
      <div id="FormAnnounceMessage_Container" style="display: none;">
        <div class="row">
          <div class="col-md-6 mx-auto">
            <form class="needs-validation" novalidate method="post" action="webservice/PR_AnnouceMessageSubmit.php" id="FormAnnounceMessage">
              <fieldset>
                <div class="form-row">
                  <div class="col">
                    <div class="form-group">
                      <!-- <label for="message"><?=$txt_var['announce_message'];?></label> -->
                      <textarea class="form-control" id="message" name="message" rows="3" placeholder="<?=$txt_var['announce_message'];?>" required></textarea>
                    </div>

                    <div class="form-group">
                      <!-- <label for="pr_url"><?=$txt_var['connect_link'];?></label> -->
                      <input type="text" class="form-control ipUrl" name="pr_url" id="pr_url" placeholder="<?=$txt_var['connect_link'];?>" value="#" required>
                    </div>
                   
                    <div class="form-group">
                      <button class="btn btn-primary" type="submit"><?=$txt_var['save'];?></button>
                      <button class="btn btn-danger" type="button" onclick="activatePrForm(false, '#FormAnnounceMessage_Container'); $('#openFormBtn').show();"><?=$txt_var['cancel'];?></button>
                    </div>
                  </div>
                  </div>
              </fieldset>
            </form>
          <hr>
          </div>
        </div>
      </div>
      <div>
        <div id="no-pr" ng-show="!annouceMessageList.length" ng-cloak>
          <div class="text-center text-muted">
            <?=$txt_var['no_data_preview'];?>
          </div>
        </div>
        <div ng-show="annouceMessageList.length" ng-cloak>
          <div ng-repeat="(idMsg, valueMsg) in annouceMessageList track by idMsg">
            <div class="alert alert-light alert-dismissible fade show border DisplayPr-{{valueMsg.pr_id}}">
              <p class="text-dark">{{valueMsg.pr_message}}</p>
              <hr>
              <p class="mb-0">{{valueMsg.pr_url | trusted}}</p>
              <button type="button" class="close text-md" aria-label="Delete" ng-click="deletePrAnnounceMessage($event, valueMsg.pr_id, $index);" title="<?=$txt_var['delete'];?>">
                <span aria-hidden="true"><i class="fas fa-trash-alt"></i></span>
              </button> 
              <button type="button" class="close text-md" aria-label="Update" ng-click="activePrUpdateForm($event, true, valueMsg.pr_id);" style="top: 2rem;" title="<?=$txt_var['update'];?>">
                <span aria-hidden="true"><i class="fas fa-edit"></i></span>
              </button>
            </div>
            <div class="alert alert-light alert-dismissible border FormPrUpdate-{{valueMsg.pr_id}}" style="display: none;">
              <div class="row">
                <div class="col-md-8">
                  <form class="FormPrAnnounceUpdate" action="webservice/PR_AnnouceMessageUpdate.php" method="post" data-arr-ref="{{$index}}">
                    <fieldset>
                      <div class="form-row">
                        <div class="col">
                          <input type="hidden" name="id" value="{{valueMsg.pr_id}}" />
                          <div class="form-group">
                            <!-- <label for="message"><?=$txt_var['announce_message'];?></label> -->
                            <textarea class="form-control" name="message" rows="3" placeholder="<?=$txt_var['announce_message'];?>" required></textarea>
                          </div>

                          <div class="form-group">
                            <!-- <label for="pr_url"><?=$txt_var['connect_link'];?></label> -->
                            <input type="text" class="form-control ipUrl" name="pr_url" value="" placeholder="<?=$txt_var['connect_link'];?>" required>
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
              <button type="button" class="close text-md" aria-label="Cancel" ng-click="activePrUpdateForm($event, false, valueMsg.pr_id);" title="<?=$txt_var['cancel'];?>">
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