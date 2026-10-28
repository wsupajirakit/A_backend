function iniPrData(prType){
  var $defer = $.Deferred();
  $.ajax({
    url: 'webservice/PRIni.php',
    type: 'get',
    dataType: 'json',
    data: {pr_type: prType},
  })
  .done(function(response){
    if(response.status==1 && response.result!==null){
      $defer.resolve(response);
    }else{
      $defer.reject(false);
    }
  })
  .fail(function(xhr, status, error){
    $defer.reject(error);
  });
  return $defer.promise();
}

function activatePrForm(val, formContainer){
  var form = $(formContainer).find('form');
  $(form).trigger('reset');
  if(val===true){
    $(form).find('fieldset').removeAttr('disabled');
    $(formContainer).show();
  }else{
    $(formContainer).hide();
    $(form).find('fieldset').attr('disabled', 'disabled');
  }
}

// --------------------------------------------------------------------
// announce message controller
app.controller('prAnnouceMessageController',  function($scope){
  $scope.prType;  
  $scope.annouceMessageList = [];
  $(document).ready(function($){
    iniPrData($scope.prType).then(function(data){
      $scope.annouceMessageList = data.result;
      $scope.$apply();
    }, ()=>{});
  });

  // insert
  $(document).on('submit', '#FormAnnounceMessage', function(event){
    var $self = event.target;
    ajaxFormSubmit(event).then((res)=>{
      activatePrForm(false, '#FormAnnounceMessage_Container');
      $('#openFormBtn').show();
      $scope.annouceMessageList.unshift(res.result);
      $scope.$apply();
    });
  });

  // delete
  $scope.deletePrAnnounceMessage = function(event, prId, arrIndex){
    $.confirm({
      title: txt_var.delete_data,
      content: txt_var.delete_data_confirm,
      buttons: {
        confirm: {
          text: txt_var.confirm,
          btnClass: "btn-primary",
          action: function(){
            $scope.annouceMessageList.splice(arrIndex, 1);
            $scope.$apply();
            $.ajax({
              url: 'webservice/PR_AnnouceMessageDelete.php',
              type: 'post',
              dataType: 'json',
              data: {id: prId},
            })
            .done(function(response){
            });
          } 
        },
        cancel: {
          text: txt_var.cancel,
          btnClass: "btn-danger",
        },
      }
    });
  }

  // activate update form
  $scope.activePrUpdateForm = function(event, value, prId){
    var form = $('.FormPrUpdate-' + prId).find('form');
    if(value===true){
      var fdata = $.grep($scope.annouceMessageList, function(obj){return obj.pr_id === prId;})[0];
      $(form).find('textarea[name="message"]').val(fdata.pr_message);
      $(form).find('input[name="pr_url"]').val(fdata.pr_url);
      $('.DisplayPr-' + prId).hide();
      $('.FormPrUpdate-' + prId).show();
    }else if(value===false){
      $('.DisplayPr-' + prId).show();
      $('.FormPrUpdate-' + prId).hide();
      $(form).trigger('reset');
    }
  }

  // update
  $(document).on('submit', '.FormPrAnnounceUpdate', function(event){
    var $self = event.target;
    ajaxFormSubmit(event).then((res)=>{
      var ref = $($self).data('arr-ref');
      $scope.annouceMessageList[ref] = res.result;
      $scope.$apply();
      $scope.activePrUpdateForm(null, false, res.result.pr_id);
    });
  });
});

// --------------------------------------------------------------------
// sponsor controller
app.controller('prSponsorController',  function($scope){
  $scope.prType;  
  $scope.sponsorList = [];
  $(document).ready(function($){
    iniPrData($scope.prType).then(function(data){
      let sponsorSizeMin = data.config.sponsor_size_min.split(",");
      let sponsorSizeMax = data.config.sponsor_size_max.split(",");
      let sponsorRatio = data.config.sponsor_ratio.split(",");
      SponsorImageConfig.supportExtension = data.config.sponsor_ext.split(",");
      SponsorImageConfig.width.min = sponsorSizeMin[0];
      SponsorImageConfig.height.min = sponsorSizeMin[1];
      SponsorImageConfig.width.max = sponsorSizeMax[0];
      SponsorImageConfig.height.max = sponsorSizeMax[1];
      SponsorImageConfig.ratio.x = sponsorRatio[0];
      SponsorImageConfig.ratio.y = sponsorRatio[1];
      $scope.sponsorList = data.result;
      $scope.$apply();
    }, ()=>{});
  });

  var SponsorImageConfig = {
    width: { min: 100, max: 1024 },
    height: { min: 100, max: 1024 },
    supportExtension: ['png', 'jpg', 'jpeg', 'gif', 'bmp'],
    ratio: { x: 1, y: 1 },
  };

  $(document).on('change', '#pr_image', function(event){
    event.preventDefault();
    var $self = event.target;
    if(file = $self.files[0]){ 
      cropImageDialog(file, true, SponsorImageConfig).then(function(response){
        $('#selectSponsorImage').hide();
        $('#displaySponsorImage').show();
        $('#previewSponsorImage').attr('src', response);
        $($self).val('');
      }, function(error){
        // console.log(error)
        $('#selectSponsorImage').show();
        $('#displaySponsorImage').hide();
        // $('#previewSponsorImage').attr('src', '');
        $($self).val('');
      });
    }
  });

  $scope.removePrImage = function(event){
    $('#previewSponsorImage').attr('src', '');
    $('#selectSponsorImage').show();
    $('#displaySponsorImage').hide();
    // document.getElementById('previewSponsorImage').src=''
  }

  // insert
  $(document).on('submit', '#FormSponsor', function(event){
    event.preventDefault()
    var $self = event.target;
    // var $image = new Image();
    imageToDataUri(document.getElementById('previewSponsorImage').src,
      function(dataUri){
        var $blob = dataURItoBlob(dataUri);
        var add = [{name: 'sponsor_image', data: $blob, filename: 'sponsor_image.png'}];
        // console.log(add)
        ajaxFormSubmit(event, add).then((res)=>{
          activatePrForm(false, '#FormSponsor_Container');
          $('#openFormBtn').show();
          $scope.sponsorList.unshift(res.result);
          $scope.$apply();
          $scope.removePrImage();
        });
        // console.log(dataUri);
    }, function(){
      alertInfo('fail', null, txt_var.upload_image_error);
    });
  });

  // delete
  $scope.deleteSponser = function(event, prId, arrIndex){
    $.confirm({
      title: txt_var.delete_data,
      content: txt_var.delete_data_confirm,
      buttons: {
        confirm: {
          text: txt_var.confirm,
          btnClass: "btn-primary",
          action: function(){
            $scope.sponsorList.splice(arrIndex, 1);
            $scope.$apply();
            $.ajax({
              url: 'webservice/PR_SponsorDelete.php',
              type: 'post',
              dataType: 'json',
              data: {id: prId},
            })
            .done(function(response){
            });
          } 
        },
        cancel: {
          text: txt_var.cancel,
          btnClass: "btn-danger",
        },
      }
    });
  }

  // activate update form
  $scope.activePrUpdateForm = function(event, value, prId){
    var form = $('.FormPrUpdate-' + prId).find('form');
    if(value===true){
      var fdata = $.grep($scope.sponsorList, function(obj){return obj.pr_id === prId;})[0];
      $(form).find('input[name="pr_title"]').val(fdata.pr_title);
      $(form).find('input[name="pr_url"]').val(fdata.pr_url);
      $('.DisplayPr-' + prId).hide();
      $('.FormPrUpdate-' + prId).show();
    }else if(value===false){
      $('.DisplayPr-' + prId).show();
      $('.FormPrUpdate-' + prId).hide();
      $(form).trigger('reset');
    }
  }

  // update
  $(document).on('submit', '.FormPrSponsorUpdate', function(event){
    var $self = event.target;
    ajaxFormSubmit(event).then((res)=>{
      var ref = $($self).data('arr-ref');
      $scope.sponsorList[ref] = res.result;
      $scope.$apply();
      $scope.activePrUpdateForm(null, false, res.result.pr_id);
    });
  });

  var tmpImgID = null;
  var tmpImgRef = null;
  $scope.updatePrImage = function(event, prId, ref){
    tmpImgID = prId;
    tmpImgRef = ref;
    document.getElementById('pr_image_update').click();
  }

  $(document).on('change', '#pr_image_update', function(event){
    event.preventDefault();
    var $self = event.target;
    // get and clear ref id
    var _imgId = tmpImgID,
    _imgRef = tmpImgRef;
    tmpImgID = null;
    tmpImgRef = null;

    if(file = $self.files[0]){ 
      cropImageDialog(file, true, SponsorImageConfig).then(function(response){
        var $blob = dataURItoBlob(response);
        // var add = [{name: 'sponsor_image', data: $blob, filename: 'sponsor_image.png'}];
        var $fd = new FormData();
        $fd.append('id', _imgId);
        $fd.append('update', 'image');
        $fd.append('sponsor_image', $blob, 'sponsor_image.png');
        $.ajax({
          url: 'webservice/PR_SponsorUpdate.php',
          type: 'post',
          dataType: 'json',
          data: $fd,
          contentType: false,
          processData: false,
          beforeSend: showLoader(true),
        })
        .done(function(res){
          if(res.status == 1){
            $scope.sponsorList[_imgRef] = res.result;
            $scope.$apply();
          }
        })
        .fail(function(){
        })
        .always(function(){
          showLoader(false);
        });
      }, function(error){
        // console.log(error)
      });
    }
    $($self).val('');
  });
});


// --------------------------------------------------------------------
// app popup controller
app.controller('prAppPopupController',  function($scope){
  $scope.prType;  
  $scope.appPopupList = [];
  $(document).ready(function($){
    iniPrData($scope.prType).then(function(data){
      let appPopupSizeMin = data.config.app_popup_size_min.split(",");
      let appPopupSizeMax = data.config.app_popup_size_max.split(",");
      let appPopupRatio = data.config.app_popup_ratio.split(",");
      AppPopupImageConfig.supportExtension = data.config.app_popup_ext.split(",");
      AppPopupImageConfig.width.min = appPopupSizeMin[0];
      AppPopupImageConfig.height.min = appPopupSizeMin[1];
      AppPopupImageConfig.width.max = appPopupSizeMax[0];
      AppPopupImageConfig.height.max = appPopupSizeMax[1];
      AppPopupImageConfig.ratio.x = appPopupRatio[0];
      AppPopupImageConfig.ratio.y = appPopupRatio[1];
      $scope.appPopupList = data.result;
      $scope.$apply();
    }, ()=>{});
  });

  var AppPopupImageConfig = {
    width: { min: 100, max: 1024 },
    height: { min: 100, max: 1024 },
    supportExtension: ['png', 'jpg', 'jpeg', 'gif', 'bmp'],
    ratio: { x: 1, y: 1 },
  };

  $(document).on('change', '#pr_image', function(event){
    event.preventDefault();
    var $self = event.target;
    if(file = $self.files[0]){ 
      cropImageDialog(file, true, AppPopupImageConfig).then(function(response){
        $('#selectAppPopupImage').hide();
        $('#displayAppPopupImage').show();
        $('#previewAppPopupImage').attr('src', response);
        $($self).val('');
      }, function(error){
        // console.log(error)
        $('#selectAppPopupImage').show();
        $('#displayAppPopupImage').hide();
        // $('#previewSponsorImage').attr('src', '');
        $($self).val('');
      });
    }
  });

  $scope.removePrImage = function(event){
    $('#previewAppPopupImage').attr('src', '');
    $('#selectAppPopupImage').show();
    $('#displayAppPopupImage').hide();
    // document.getElementById('previewSponsorImage').src=''
  }

  // insert
  $(document).on('submit', '#FormAppPopup', function(event){
    event.preventDefault()
    var $self = event.target;
    // var $image = new Image();
    imageToDataUri(document.getElementById('previewAppPopupImage').src,
      function(dataUri){
        var $blob = dataURItoBlob(dataUri);
        var add = [{name: 'apppopup_image', data: $blob, filename: 'apppopup_image.png'}];
        // console.log(add)
        ajaxFormSubmit(event, add).then((res)=>{
          activatePrForm(false, '#FormAppPopup_Container');
          $('#openFormBtn').show();
          $scope.appPopupList.unshift(res.result);
          $scope.$apply();
          $scope.removePrImage();
        });
        // console.log(dataUri);
    }, function(){
      alertInfo('fail', null, txt_var.upload_image_error);
    });
  });

  // delete
  $scope.deleteAppPopup = function(event, prId, arrIndex){
    $.confirm({
      title: txt_var.delete_data,
      content: txt_var.delete_data_confirm,
      buttons: {
        confirm: {
          text: txt_var.confirm,
          btnClass: "btn-primary",
          action: function(){
            $scope.appPopupList.splice(arrIndex, 1);
            $scope.$apply();
            $.ajax({
              url: 'webservice/PR_AppPopupDelete.php',
              type: 'post',
              dataType: 'json',
              data: {id: prId},
            })
            .done(function(response){
            });
          } 
        },
        cancel: {
          text: txt_var.cancel,
          btnClass: "btn-danger",
        },
      }
    });
  }

  // activate update form
  $scope.activePrUpdateForm = function(event, value, prId){
    var form = $('.FormPrUpdate-' + prId).find('form');
    if(value===true){
      var fdata = $.grep($scope.appPopupList, function(obj){return obj.pr_id === prId;})[0];
      $(form).find('input[name="pr_title"]').val(fdata.pr_title);
      $(form).find('input[name="pr_url"]').val(fdata.pr_url);
      $('.DisplayPr-' + prId).hide();
      $('.FormPrUpdate-' + prId).show();
    }else if(value===false){
      $('.DisplayPr-' + prId).show();
      $('.FormPrUpdate-' + prId).hide();
      $(form).trigger('reset');
    }
  }

  // update
  $(document).on('submit', '.FormPrAppPopupUpdate', function(event){
    var $self = event.target;
    ajaxFormSubmit(event).then((res)=>{
      var ref = $($self).data('arr-ref');
      $scope.appPopupList[ref] = res.result;
      $scope.$apply();
      $scope.activePrUpdateForm(null, false, res.result.pr_id);
    });
  });

  var tmpImgID = null;
  var tmpImgRef = null;
  $scope.updatePrImage = function(event, prId, ref){
    tmpImgID = prId;
    tmpImgRef = ref;
    document.getElementById('pr_image_update').click();
  }

  $(document).on('change', '#pr_image_update', function(event){
    event.preventDefault();
    var $self = event.target;
    // get and clear ref id
    var _imgId = tmpImgID,
    _imgRef = tmpImgRef;
    tmpImgID = null;
    tmpImgRef = null;

    if(file = $self.files[0]){ 
      cropImageDialog(file, true, AppPopupImageConfig).then(function(response){
        var $blob = dataURItoBlob(response);
        // var add = [{name: 'sponsor_image', data: $blob, filename: 'sponsor_image.png'}];
        var $fd = new FormData();
        $fd.append('id', _imgId);
        $fd.append('update', 'image');
        $fd.append('apppopup_image', $blob, 'apppopup_image.png');
        $.ajax({
          url: 'webservice/PR_AppPopupUpdate.php',
          type: 'post',
          dataType: 'json',
          data: $fd,
          contentType: false,
          processData: false,
          beforeSend: showLoader(true),
        })
        .done(function(res){
          if(res.status == 1){
            $scope.appPopupList[_imgRef] = res.result;
            $scope.$apply();
          }
        })
        .fail(function(){
        })
        .always(function(){
          showLoader(false);
        });
      }, function(error){
        // console.log(error)
      });
    }
    $($self).val('');
  });
});

// --------------------------------------------------------------------
// cover controller
app.controller('prCoverController',  function($scope){
  $scope.prType;  
  $scope.coverList = [];
  $(document).ready(function($){
    iniPrData($scope.prType).then(function(data){
      let coverSizeMin = data.config.cover_size_min.split(",");
      let coverSizeMax = data.config.cover_size_max.split(",");
      let coverSizeRatio = data.config.cover_ratio.split(",");
      CoverImageConfig.supportExtension = data.config.cover_ext.split(",");
      CoverImageConfig.width.min = coverSizeMin[0];
      CoverImageConfig.height.min = coverSizeMin[1];
      CoverImageConfig.width.max = coverSizeMax[0];
      CoverImageConfig.height.max = coverSizeMax[1];
      CoverImageConfig.ratio.x = coverSizeRatio[0];
      CoverImageConfig.ratio.y = coverSizeRatio[1];
      $scope.coverList = data.result;
      $scope.formCoverHelpMessage = imageSizeNoticeMessage(CoverImageConfig);
      $scope.$apply();
    }, ()=>{});
  });

  var CoverImageConfig = {
    width: { min: 1366, max: 1366 },
    height: { min: 455, max: 455 },
    supportExtension: ['png', 'jpg', 'jpeg', 'gif', 'bmp'],
    ratio: { x: 18, y: 6 },
  };
  $scope.formCoverHelpMessage = imageSizeNoticeMessage(CoverImageConfig);

  $(document).on('change', '#pr_image', function(event){
    event.preventDefault();
    var $self = event.target;
    if(file = $self.files[0]){ 
      var url = window.URL || window.webkitURL;
      var src = url.createObjectURL(file);
      var image = new Image();
      var noticeSizeMsg = imageSizeNoticeMessage(CoverImageConfig);
      image.onload = function(){
        // console.log(CoverImageConfig, {width: image.width, height: image.height})
        if(!checkImageRatio({width: image.width, height: image.height}, CoverImageConfig)){
          alertInfo('fail', null, noticeSizeMsg);
          $('#selectCoverImage').show();
          $('#displayCoverImage').hide();
          $('#previewCoverImage').attr('src', '');
        }else{
          if(!checkFileExtension(CoverImageConfig.supportExtension, file.name)){
            alertInfo('fail', null, txt_var.file_not_support);
            $('#selectCoverImage').show();
            $('#displayCoverImage').hide();
            $('#previewCoverImage').attr('src', '');
          }else{
            $('#selectCoverImage').hide();
            $('#displayCoverImage').show();
            $('#previewCoverImage').attr('src', image.src);
          }
        }
      }
      image.onerror = function(){
        alertInfo('fail', null, noticeSizeMsg);
        $('#selectCoverImage').show();
        $('#displayCoverImage').hide();
      }
      image.src = src;
      $($self).val('');
      // cropImageDialog(file, true, CoverImageConfig).then(function(response){
      //   $('#selectCoverImage').hide();
      //   $('#displayCoverImage').show();
      //   $('#previewCoverImage').attr('src', response);
      //   $($self).val('');
      // }, function(error){
      //   console.log(error)
      //   $('#selectCoverImage').show();
      //   $('#displayCoverImage').hide();
      //   // $('#previewCoverImage').attr('src', '');
      //   $($self).val('');
      // })
    }
  });

  $scope.removePrImage = function(event){
    $('#previewCoverImage').attr('src', '');
    $('#selectCoverImage').show();
    $('#displayCoverImage').hide();
    // document.getElementById('previewCoverImage').src=''
  }

  // insert
  $(document).on('submit', '#FormCover', function(event){
    event.preventDefault()
    var $self = event.target;
    // var $image = new Image();
    imageToDataUri(document.getElementById('previewCoverImage').src,
      function(dataUri){
        var $blob = dataURItoBlob(dataUri);
        var add = [{name: 'cover_image', data: $blob, filename: 'cover_image.png'}];
        // console.log(add)
        ajaxFormSubmit(event, add).then((res)=>{
          activatePrForm(false, '#FormCover_Container');
          $('#openFormBtn').show();
          $scope.coverList.unshift(res.result);
          $scope.$apply();
          $scope.removePrImage();
        });
        // console.log(dataUri);
    }, function(){
      alertInfo('fail', null, txt_var.upload_image_error);
    });
  });

  // delete
  $scope.deleteSponser = function(event, prId, arrIndex){
    $.confirm({
      title: txt_var.delete_data,
      content: txt_var.delete_data_confirm,
      buttons: {
        confirm: {
          text: txt_var.confirm,
          btnClass: "btn-primary",
          action: function(){
            $scope.coverList.splice(arrIndex, 1);
            $scope.$apply();
            $.ajax({
              url: 'webservice/PR_CoverDelete.php',
              type: 'post',
              dataType: 'json',
              data: {id: prId},
            })
            .done(function(response){
            });
          } 
        },
        cancel: {
          text: txt_var.cancel,
          btnClass: "btn-danger",
        },
      }
    });
  }

  // activate update form
  $scope.activePrUpdateForm = function(event, value, prId){
    var form = $('.FormPrUpdate-' + prId).find('form');
    if(value===true){
      var fdata = $.grep($scope.coverList, function(obj){return obj.pr_id === prId;})[0];
      $(form).find('input[name="pr_title"]').val(fdata.pr_title);
      $(form).find('input[name="pr_url"]').val(fdata.pr_url);
      $('.DisplayPr-' + prId).hide();
      $('.FormPrUpdate-' + prId).show();
    }else if(value===false){
      $('.DisplayPr-' + prId).show();
      $('.FormPrUpdate-' + prId).hide();
      $(form).trigger('reset');
    }
  }

  // update
  $(document).on('submit', '.FormPrCoverUpdate', function(event){
    var $self = event.target;
    ajaxFormSubmit(event).then((res)=>{
      var ref = $($self).data('arr-ref');
      $scope.coverList[ref] = res.result;
      $scope.$apply();
      $scope.activePrUpdateForm(null, false, res.result.pr_id);
    });
  });

  var tmpImgID = null;
  var tmpImgRef = null;
  $scope.updatePrImage = function(event, prId, ref){
    tmpImgID = prId;
    tmpImgRef = ref;
    document.getElementById('pr_image_update').click();
  }

  $(document).on('change', '#pr_image_update', function(event){
    event.preventDefault();
    var $self = event.target;
    // get and clear ref id
    var _imgId = tmpImgID,
    _imgRef = tmpImgRef;

    if(file = $self.files[0]){ 
      var url = window.URL || window.webkitURL;
      var src = url.createObjectURL(file);
      var image = new Image();
      var noticeSizeMsg = imageSizeNoticeMessage(CoverImageConfig);
      image.onload = function(){
        // console.log(CoverImageConfig, {width: image.width, height: image.height})
        if(!checkImageRatio({width: image.width, height: image.height}, CoverImageConfig)){
          alertInfo('fail', null, noticeSizeMsg);
         
        }else{
          if(!checkFileExtension(CoverImageConfig.supportExtension, file.name)){
            alertInfo('fail', null, txt_var.file_not_support);
          }else{
            imageToDataUri(image.src,
              function(dataUri){
                var $blob = dataURItoBlob(dataUri);
                var $fd = new FormData();
                $fd.append('id', _imgId);
                $fd.append('update', 'image');
                $fd.append('cover_image', $blob, 'cover_image.png');
                $.ajax({
                  url: 'webservice/PR_CoverUpdate.php',
                  type: 'post',
                  dataType: 'json',
                  data: $fd,
                  contentType: false,
                  processData: false,
                  beforeSend: showLoader(true),
                })
                .done(function(res){
                  if(res.status == 1){
                    $scope.coverList[_imgRef] = res.result;
                    $scope.$apply();
                  }
                })
                .fail(function(){
                })
                .always(function(){
                  showLoader(false);
                });
            }, function(){
              alertInfo('fail', null, txt_var.upload_image_error);
            });
          }
        }
      }
      image.onerror = function(){
        alertInfo('fail', null, noticeSizeMsg);
      }
      image.src = src;
    }
    $($self).val('');
    tmpImgID = null;
    tmpImgRef = null;
  });
});
// ----------------------------------