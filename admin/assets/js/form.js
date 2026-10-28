(function(){
  'use strict';
  window.addEventListener('load', function(){
    // Disabling form submissions if there are invalid fields
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form){
      form.addEventListener('submit', function(event){
        if(form.checkValidity()===false){
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();

function CheckFormValid($event){
  var $form = $event.target;
  $form.classList.add('was-validated');
  if($form.checkValidity()===false){
    $event.preventDefault();
    $event.stopPropagation();
    return false;
  }
  return true;
}

function ajaxFormSubmit($event, $addFData, $callback){
  $event.preventDefault();
  var $self = $event.target;
  var $defer = $.Deferred();
  if($($self).hasClass('needs-validation')){
    if(!CheckFormValid($event)){
      return;
    }
  }
  
  var $clearAfterSuccess  = $($self).attr('data-clear-after-success') == 'yes' ? true : false;
  var $clearAfterFail     = $($self).attr('data-clear-after-fail') == 'yes' ? true : false;
  var $noticeMethod       = $($self).attr('data-notice') === void 0 ? ['popup','popup'] : $($self).attr('data-notice').split(",");
  var $noticeSuccessMethod    = $noticeMethod[0];
  var $noticeFailMethod       = $noticeMethod[1] === void 0 ? $noticeMethod[0] : $noticeMethod[1];
  var $alertBox = $($self).find('.alert').not('.no-action-on-submit');
  var $url = $($self).attr('action');
  var $method = $($self).attr('method');
  var $fd = new FormData($self);
  if(void 0 !== $addFData && null !== $addFData){
    for(var i=0; i < $addFData.length; i++){
      if(void 0 !== $addFData[i].filename && $addFData[i].filename != "")
        $fd.append($addFData[i].name, $addFData[i].data, $addFData[i].filename);
      else
        $fd.append($addFData[i].name, $addFData[i].data);
    }
  }
  $.ajax({
    url: $url,
    type: $method,
    dataType: 'json',
    data: $fd,
    contentType: false,
    processData: false,
    cache: false,
    beforeSend: function(){
      showLoader(true);
      $($self).find('fieldset').attr('disabled', 'disabled');
      if(void 0 !== $alertBox){
        $($alertBox).removeClass('alert-success');
        $($alertBox).removeClass('alert-danger');
        $($alertBox).addClass('d-none');
        $($alertBox).text("");
      }
    }
  })
  .always(function(response, status, xhr){
    if(status!='success' || response===null || response.status!=1){
      var failMsg = (response===null || undefined===response.msg) ? txt_var.request_error : response.msg;
     
      if($noticeFailMethod=='popup'){
        alertInfo('fail', null, failMsg);
      }else if($noticeFailMethod=='dialog'){
        alertInfo('fail', null, failMsg, 'dialog');
      }else if($noticeFailMethod=='message'){
        if(void 0 !== $alertBox){
          $($alertBox).removeClass('d-none');
          $($alertBox).addClass('alert-danger');
          $($alertBox).text(failMsg);
        }
      }
      if($clearAfterFail===true){
        $($self).trigger("reset");
      }
      $defer.reject({'response': response, 'status': status, 'xhr': xhr});
    }
    else if(response.status==1)
    {
      $($self).removeClass('was-validated');
      var successMsg = (void 0===response.msg) ? txt_var.request_success : response.msg;
      if($noticeSuccessMethod=='popup'){
        alertInfo('success', null, successMsg);
      }else if($noticeSuccessMethod=='dialog'){
        alertInfo('success', null, successMsg, 'dialog');
      }else if($noticeSuccessMethod=='message'){
        if(void 0 !== $alertBox){
          $($alertBox).removeClass('d-none');
          $($alertBox).addClass('alert-success');
          $($alertBox).text(successMsg);
        }
      }
      if($clearAfterSuccess===true){
        $($self).trigger("reset");
      }
      $defer.resolve(response);
    }
    $($self).find('fieldset').removeAttr('disabled');
    showLoader(false);
  });
  return $defer.promise();
}

$(document).on('input', 'input.ipNumeric', function(event) {
  event.preventDefault();
  var val = $(this).val();
  val = val.replace(/[^\d]/g, '');
  $(this).val(val);
});

$(document).on('input', 'input.ipUrl', function(event) {
  event.preventDefault();
  var val = $(this).val();
  val = decodeURIComponent(val);
  $(this).val(val);
});