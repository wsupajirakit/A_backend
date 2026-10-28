$(document).ready(function($){
  // new ClipboardJS('.copy-media-image');
  new ClipboardJS(".copy-media-image", {
    text: function(trigger){
      return $(trigger).data('filename'); 
    }
  });

  var imageUrl;
  var imageSupportExtensions = ['png', 'jpg', 'jpeg', 'gif', 'bmp'];
  var iOffset = 0;
  var iLimit = 50;
  var onloadMedia = false;  
  var isLoadAll = false; 
  var imageArray = [];
  var msnry = $('.ms-grid').masonry({
    itemSelector: '.ms-item',
    animated: false,
    transitionDuration: 0,
  });

  $(document).on('click', '#SelectMedia', function(event) {
    event.preventDefault();
    document.getElementById('media_files').click();
  });

  $(document).on('change', '#media_files', function(event) {
    event.preventDefault();
    var $self = this;
    var files = $self.files;
    if(files.length > 0){ 
      for(var i = 0; i < files.length; i++){
        if(checkFileExtension(imageSupportExtensions, files[i].name)){
          let _div = document.createElement('DIV');
          _div.className = "ms-item";
          let _divSquare = document.createElement('DIV');
          _divSquare.className = "square-parent";

          let _btnContainer = document.createElement('DIV');
          _btnContainer.className = "ms-btnContainer position-absolute";
          
          let _btnAbort = document.createElement('BUTTON');
          _btnAbort.className = "btn btn-sm btn-danger";
          _btnAbort.innerHTML = '<i class="fas fa-times"></i>';
          _btnAbort.title = txt_var.cancel;
          _btnContainer.appendChild(_btnAbort);

          let _divSquareChild = document.createElement('DIV');
          _divSquareChild.className = "square-child imgPreload";

          let _divSquareChildText = document.createElement('DIV');
          _divSquareChildText.className = "d-flex justify-content-center text-muted h-100 align-items-center text-center p-4";
          _divSquareChildText.innerHTML = "<span>Loading...</span>";

          _divSquareChild.appendChild(_divSquareChildText);
          _divSquare.appendChild(_divSquareChild);
          _divSquare.appendChild(_btnContainer);
          _div.appendChild(_divSquare);

          // add preload item to the top of container
          $(msnry).prepend(_div);
          $(msnry).masonry('reloadItems');
          $(msnry).masonry('layout');

          let fd = new FormData();
          fd.append('media_file', files[i]);
          fd.append('media_type', 'image');
          let xhr = $.ajax({
            url: 'webservice/ContentMediaSubmit.php',
            type: 'post',
            dataType: 'json',
            data: fd,
            contentType: false,
            processData: false,
          })
          .always(function(response, status, xhr){
            if(status=='success' && response.status==1){
              // remove preload item
              $(msnry).masonry('remove', _div);
              $(msnry).masonry('reloadItems');
              $(msnry).masonry('layout');

              // add new item to top
              let div = document.createElement('DIV');
              div.className = "ms-item";
              let divInner = document.createElement('DIV');
              divInner.className = "ms-inner bg-light position-relative";
              div.appendChild(divInner);
              
              let btnContainer = document.createElement('DIV');
              btnContainer.className = "ms-btnContainer position-absolute";
              
              // let btnCopy = document.createElement('BUTTON');
              // btnCopy.className = "btn btn-sm btn-primary mr-2 copy-media-image";
              // btnCopy.innerHTML = '<i class="fas fa-link"></i>';
              // $(btnCopy).data('filename', imageUrl + response.result.filename);
              // btnContainer.appendChild(btnCopy);

              let btnDelete = document.createElement('BUTTON');
              btnDelete.className = "btn btn-sm btn-danger delete-media-image";
              btnDelete.innerHTML = '<i class="fas fa-trash-alt"></i>';
              btnDelete.title = txt_var.delete;
              $(btnDelete).data('filename', response.result.filename);
              btnContainer.appendChild(btnDelete);

              divInner.appendChild(btnContainer);
              let img = new Image();
              img.className = "w-100 d-block";
              $(msnry).prepend(div);
              img.onload = function(){
                divInner.appendChild(img);
                $(msnry).masonry('reloadItems');
                $(msnry).masonry('layout');
              }
              img.src = imageUrl + response.result.filename;
              // add new item to top
            }else{
              // remove preload style 
              _divSquareChild.classList.remove('imgPreload');
              _divSquareChild.classList.add('bg-light');
              _divSquareChildText.innerHTML = '<span class="text-danger">' + txt_var.request_error + '</span>';
              // chage abort button to remove button
              _btnAbort.title = txt_var.close;
              $(_btnAbort).off('click').on('click', function(event){
                event.preventDefault();
                $(msnry).masonry('remove', _div)
                $(msnry).masonry('reloadItems');
                $(msnry).masonry('layout');
              });
            }
          });
          
          $(_btnAbort).on('click', function(event) {
            event.preventDefault();
            xhr.abort();
          });
          // $(_btnAbort).trigger('click')
        }
      }
    }
    $('#media_files').val('');
  });

  window.onscroll = function(event) {
    if((window.innerHeight + window.scrollY) >= document.body.offsetHeight){
      if(!onloadMedia && !isLoadAll){
        loadTrigger();
      }
    }
  };

  function loadMedia($offset, $limit){
    $defer = $.Deferred();
    $.ajax({
      url: 'webservice/ContentMediaList.php',
      type: 'get',
      dataType: 'json',
      data: {offset: $offset, limit: $limit},
    })
    .done(function(response){
      if(response.status==1){
        $defer.resolve(response.result);
      }else{
        $defer.reject(false);
      }
    })
    .fail(function(xhr, status, error){
      $defer.reject(error);
    });
    return $defer.promise();
  }

  function loadTrigger(){
    onloadMedia = true;
    loadMedia(iOffset, iLimit).then(function(response){
      var images = response.image;
      iOffset += images.length;
      if(images.length < iLimit){
        isLoadAll = true;
      }
      for(var i = 0; i < images.length; i++){
        imageArray.push(images[i])
        let div = document.createElement('DIV');
        div.className = "ms-item";
        let divInner = document.createElement('DIV');
        divInner.className = "ms-inner bg-light position-relative";
        div.appendChild(divInner);
        
        let btnContainer = document.createElement('DIV');
        btnContainer.className = "ms-btnContainer position-absolute";
        
        // let btnCopy = document.createElement('BUTTON');
        // btnCopy.className = "btn btn-sm btn-primary mr-2 copy-media-image";
        // btnCopy.innerHTML = '<i class="fas fa-link"></i>';
        // $(btnCopy).data('filename', imageUrl + images[i]);
        // btnContainer.appendChild(btnCopy);

        let btnDelete = document.createElement('BUTTON');
        btnDelete.className = "btn btn-sm btn-danger delete-media-image";
        btnDelete.innerHTML = '<i class="fas fa-trash-alt"></i>';
        btnDelete.title = txt_var.delete;
        $(btnDelete).data('filename', images[i]);
        btnContainer.appendChild(btnDelete);

        // $(btnContainer).append('<div class="toast"><div class="toast-header">Toast Header</div><div class="toast-body">Some text inside the toast body</div></div>')

        divInner.appendChild(btnContainer);
        let img = new Image();
        img.className = "w-100 d-block";
        $(msnry).append(div);
        img.onload = function(){
          divInner.appendChild(img);
          $(msnry).masonry('reloadItems');
          $(msnry).masonry('layout');
        }
        img.src = imageUrl + images[i];
      }
      onloadMedia = false;
    }, function(error){
      onloadMedia = false;
    });
  }
  $.ajax({
    url: 'webservice/ContentMediaIni.php',
    type: 'get',
    dataType: 'json',
    beforeSend: showLoader(true),
  })
  .done(function(response){
    if(response.status==1){
      imageUrl = response.result.config.img_url;
      imageSupportExtensions = response.result.config.img_ext.split(",");
      loadTrigger();
    }
  })
  .always(function(response, status, xhr){
    if(status!='success' || response.status!=1){

    }
    showLoader(false);
  });

  $(document).on('click', '.delete-media-image', function(event){
    event.preventDefault();
    var $self = this;
    var filename = $($self).data('filename');
    var div = $($self).parents('.ms-item');
    $.confirm({
      title: txt_var.delete_data,
      content: txt_var.delete_data_confirm,
      buttons: {
        confirm: {
          text: txt_var.confirm,
          btnClass: 'btn-primary',
          action: function(){
            $(msnry).masonry('remove', div)
            $(msnry).masonry('reloadItems');
            $(msnry).masonry('layout');
            $.ajax({
              url: 'webservice/ContentMediaDelete.php',
              type: 'post',
              dataType: 'json',
              data: {media_type: 'image', media_file: filename},
            });
          } 
        },
        calcel:  {
          text: txt_var.cancel,
          btnClass: 'btn-danger',
        },
      }
    });
  });

  $(document).on('change', '#sidebar-toggle', function(event){
    event.preventDefault();
    setTimeout(()=>{
      $(msnry).masonry('layout');
    }, 400);
  });
});