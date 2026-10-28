$(document).ready(function($){
  $(document).on('change', 'input[name="app_img"]', function(event){
    event.preventDefault();
    var $self = this;
    var $page = $($self).data('page');
    var file;
    var _URL = window.URL || window.webkitURL;
    if(file = $self.files[0]){
      var img = new Image();
      var src = _URL.createObjectURL(file);
      img.onload = confirmImage(src, $page);
      img.src = src;
    }
    // $($self).val('');
  });

  function confirmImage($img, $page){
    $.confirm({
      title: txt_var.change_background_image,
      content: '<p>' + txt_var.page + " - " + txt_var.page_name[$page] + '</p>' + '<img src="'  +$img +'" />',
      buttons: {
        confirm: {
          text: txt_var.confirm,
          btnClass: "btn-primary",
          action: function(){
            $('#FormAppearance_' + $page).submit();
          }
        },
        cancel: {
          text: txt_var.cancel,
          btnClass: "btn-danger",
        },
      },
      onClose: function(){
        $('#app_img_' + $page).val('');
      }
    });
  }

  $(document).on('submit', 'form.FormAppearance', function(event){
    event.preventDefault();
    ajaxFormSubmit(event).then(function(response){
      console.log(response)
      if(response.status == 1){
        $('#bgPage_' + response.result.page).attr('src', response.result.bg_image);
      }
    },);
  });
});