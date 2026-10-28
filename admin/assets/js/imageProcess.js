function checkImageRatio($imgSize, $configSize){
  if(
    ($configSize.width.min != 0 && $imgSize.width < $configSize.width.min) ||
    ($configSize.width.max != 0 && $imgSize.width > $configSize.width.max) ||
    ($configSize.height.min != 0 && $imgSize.height < $configSize.height.min) ||
    ($configSize.height.max != 0 && $imgSize.height > $configSize.height.max)
    )
    return false;
  return true;
  // if($imgSize.width < $configSize.width.min || $imgSize.width > $configSize.width.max || $imgSize.height < $configSize.height.min || $imgSize.height > $configSize.height.max)
  //   return false;
  // return true;
}

function checkFileExtension($extensions, $filename){
  var ext = getFileExtension($filename);
  if($extensions.indexOf(ext) == -1){
    return false;
  }
  return true;
}

function imageSizeNoticeMessage($imageConfig){
  if($imageConfig.width.min == $imageConfig.width.max && $imageConfig.height.min == $imageConfig.height.max){
    let k = ["{n1}", "{n2}"];
    let v = [$imageConfig.width.min, $imageConfig.height.min];
    return txt_var.image_size_notice_exact.replaceArray(k, v);
  }
  else if($imageConfig.width.min > 0 && $imageConfig.width.max <= 0){
    let k = ["{n1}", "{n2}"];
    let v = [$imageConfig.width.min, $imageConfig.height.min];
    return txt_var.image_size_notice_min.replaceArray(k, v);
  }
  else if($imageConfig.width.min <= 0 && $imageConfig.width.max > 0){
    let k = ["{n1}", "{n2}"];
    let v = [$imageConfig.width.max, $imageConfig.height.max];
    return txt_var.image_size_notice_max.replaceArray(k, v);
  }
  else if($imageConfig.width.min > 0 && $imageConfig.width.max > 0){
    let k = ["{n1}", "{n2}", "{n3}", "{n4}"];
    let v = [$imageConfig.width.min, $imageConfig.height.min, $imageConfig.width.max, $imageConfig.height.max];
    return txt_var.image_size_notice_between.replaceArray(k, v);
  }
}

function cropImageDialog($src, $convertObjURL, $imageConfig){
  var $defer = $.Deferred();
  var url = window.URL || window.webkitURL;

  if($convertObjURL)
    $src = url.createObjectURL($src);
  
  if(checkFileExtension($imageConfig.supportExtension, $src.name)){
    $defer.reject({"status": 0, "msg": "Image extension not supported!"});
  }
  else{
    var noticeSizeMsg = imageSizeNoticeMessage($imageConfig);
    var $image;
    $.confirm({
      title: txt_var.crop_img,
      draggable: false,
      backgroundDismiss: false,
      columnClass: 'medium',
      content: 
        '<div class="p_cropper">'+
          '<div>'+
            '<small class="text-muted mb-2 d-block">'+noticeSizeMsg+'</small>'+
            '<small class="text-muted mb-2 d-block crop-size"></small>'+
            '<div class="pr_img_container bg-dark" style="overflow: hidden;">'+
            '<img src="'+$src+'" id="imgToCrop" class="mx-auto d-block" />'+
            '</div>' + 
          '</div>'+
        '</div>',
      onContentReady: function(){
        var $self = this;
        var $cropSizeInfo = $self.$content.find('.crop-size');
        $image = $self.$content.find('#imgToCrop');
        $image.cropper({
          viewMode: 1,
          dragMode: 'move',
          // zoomable: false,
          movable: false,
          scalable: false,
          aspectRatio: $imageConfig.ratio.x / $imageConfig.ratio.y,
          crop: function(event){ 
            $($cropSizeInfo).text(event.detail.width.toFixed(0) + 'px - ' + event.detail.height.toFixed(0) + 'px');
            // console.log({width: event.detail.width, height: event.detail.height}, $imageConfig)
            if(!checkImageRatio({width: event.detail.width, height: event.detail.height}, $imageConfig)){
              $self.buttons.crop.disable();
            }else{
              $self.buttons.crop.enable();
            }
          },
        });
      },
      buttons: {
        crop: {
          text: txt_var.crop_img,
          btnClass: "btn-primary",
          action: function(){
            var $croppedURL = $image.cropper('getCroppedCanvas').toDataURL("image/png"); 
            var $croppedImg = new Image();
            $croppedImg.src = $croppedURL;
            $croppedImg.onload = function(){
              // check image ratio
              if(!checkImageRatio({width: $croppedImg.width, height: $croppedImg.height}, $imageConfig)){
                alertInfo('fail', null, noticeSizeMsg);
                $defer.reject({"status": 0, "msg": "Image size is too big or too small!"});
              }else{
                $defer.resolve($croppedURL);
              }
            }
            $croppedImg.onerror = function(){
              $defer.reject({"status": 0, "msg": "Unable to load image!"});
            }
            $image.cropper('destroy');
          }
        },
        cancel: {
          text: txt_var.cancel,
          btnClass: "btn-danger",
          action: function(){
            $defer.reject({"status": 0, "msg": "Crop process has been cancel by user!"});
          }
        }
      },
      onClose: function(){
        setTimeout(() => {
          if($defer.state() == "pending")
            $defer.reject({"status": 0, "msg": "Crop process has been cancel by user!"});
        }, 100);
      },
    });
  }
  return $defer.promise();
}