$(document).ready(function($){
  $('#rp_bdate').datepicker({
    format: 'dd/mm/yyyy',
    language: 'th',
    autoclose: true,
    endDate: new Date(),
  });

  var _action = getQueryVariable('action');
  var rpImage;
  if(_action == 'update'){
    rpImage = $('#tmpRpImage').val();
    $('#tmpRpImage').remove();
  }
  
  var RpImageConfig = {
    width: { min: 128, max: 512 },
    height: { min: 128, max: 512 },
    supportExtension: ['png', 'jpg', 'jpeg', 'gif', 'bmp'],
    ratio: {x: 1, y: 1},
  };

  $.ajax({
    url: 'webservice/RadioPresenterIni.php',
    type: 'get',
    dataType: 'json',
  })
  .done(function(response){
    if(response.status == 1){
      let rpSizeMin = response.config.rp_size_min.split(",");
      let rpSizeMax = response.config.rp_size_max.split(",");
      let rpRatio = response.config.rp_ratio.split(",");
      RpImageConfig.supportExtension = response.config.rp_ext.split(",");
      RpImageConfig.width.min = rpSizeMin[0];
      RpImageConfig.height.min = rpSizeMin[1];
      RpImageConfig.width.max = rpSizeMax[0];
      RpImageConfig.height.max = rpSizeMax[1];
      RpImageConfig.ratio.x = rpRatio[0];
      RpImageConfig.ratio.y = rpRatio[1];
    }
  });
  
  // Upload picture
  $(document).on('change', '#rp-img', function(event){
    event.preventDefault();
    var $self = event.target;
    if(file = $self.files[0]){ 
    cropImageDialog(file, true, RpImageConfig).then(function(response){
      imageToDataUri(response, function(uri){
        var $croppedImg = new Image();
        $croppedImg.onload = function(){
          rpImage = dataURItoBlob(uri);
          $('#rp-img-tag').show();
          var fig = $('#rp-img-tag').find('figure');
          $($(fig).find('img')).remove();
          $croppedImg.className = 'w-100';
          $(fig).append($croppedImg);
        }
        $croppedImg.onerror = function(){
          alertInfo('fail', null, txt_var.upload_image_error);
        }
        $croppedImg.src = response;
        
        }, function(){
          alertInfo('fail', null, txt_var.upload_image_error);
        })
      }, function(error){
      });
    }
    $($self).val('');
  });

  $(document).on('submit', '#FormRadioPresenter', function(event){
    event.preventDefault();
    var _add = [];
    if(_action == 'new'){
      _add.push({'name': 'rp_image', 'data': rpImage, 'filename': 'rp-image.png'});
    }else{
      if(typeof rpImage=='object'){
        _add.push({'name': 'rp_image', 'data': rpImage, 'filename': 'rp-image.png'});
      }
      else{
        _add.push({'name': 'rp_image', 'data': rpImage});
      }
      _add.push({'name': 'rp_image_type', 'data': typeof rpImage});
    }
    ajaxFormSubmit(event, _add).then(function(response){
      if(response.status == 1 && _action == 'new'){
        setTimeout(()=>{
          window.location.href = "radio-presenter.php";
        }, 1000);
      }
    },);
  });

});