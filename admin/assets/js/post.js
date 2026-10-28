$(document).ready(function($){    
  var postImagePrefix;
  var postCoverPrefix;
  var readmoreImageUrl = "https://www.example.com/admin/img/ui/read-more-line.png";
  var action = getQueryVariable('action');
  var postImage;
  var PostImageConfig = {
    width: { min: 210, max: 2100 },
    height: { min: 90, max: 900 },
    supportExtension: ['png', 'jpg', 'jpeg', 'gif', 'bmp'],
    ratio: {x: 21, y: 9},
  };

  $('#publish_date').datepicker({
    format: 'dd/mm/yyyy',
    language: 'th',
    autoclose: true,
  });
  
  // Upload picture
  $(document).on('change', '#post-img', function(event){
    event.preventDefault();
    var $self = event.target;
    if(file = $self.files[0]){ 
    cropImageDialog(file, true, PostImageConfig).then(function(response){
      imageToDataUri(response, function(uri){
        var $croppedImg = new Image();
        $croppedImg.onload = function(){
          postImage = dataURItoBlob(uri);
          $('#post-img-tag').show();
          var fig = $('#post-img-tag').find('figure');
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

  $(document).on('submit', '#FormPostEditor', function(event){
    event.preventDefault();
    var $self = event.target;
    var action = getQueryVariable('action');
    var $url = $($self).attr('action');
    var $method = $($self).attr('method');
    var $fd = new FormData();
    var post_content = tinymce.get('post-content').getContent();
    // tinymce.get('post-content').getBody().setAttribute('contenteditable', 'false');
    $fd.append('post_title', document.getElementById('post-name').value);
    $fd.append('post_content', post_content);
    if(typeof postImage=='object'){
      $fd.append('post_image', postImage, 'cover-image.png');
    }else{
      $fd.append('post_image', postImage);
    }
    
    $fd.append('is_pin', document.getElementById('is-pin').checked===true ? 'on' : 'off');
    $fd.append('is_publish', document.getElementById('is-publish').checked===true ? 'on' : 'off');
    $fd.append('post_cover', typeof postImage);
    $fd.append('action', action);
    $fd.append('post_id', getQueryVariable('id'));
    $fd.append('publish_date', document.getElementById('publish_date').value);
    $fd.append('publish_time', document.getElementById('publish_hour').value + ":" + document.getElementById('publish_minute').value);
    $fd.append('post_slug', document.getElementById('post-slug').value);
  
    $.ajax({
      url: $url,
      type: $method,
      dataType: 'json',
      data: $fd,
      contentType: false,
      processData: false,
      beforeSend: showLoader(true),
    })
    .done(function(response){
      if(response.status==1){
        if(action=='new'){
          alertInfo('success', null, response.msg, 'dialog');
          setTimeout(()=>{
            // window.location.href = "post-editor.php?action=update&id=" + response.id;
            window.location.href = "post.php";
          }, 1000);
        }else{
          alertInfo('success', null, response.msg);
        }
      }
    })
    .always(function(response, status, xhr) {
      if(status!='success' || response.status!=1){
        var failMsg = void 0===response.msg ? txt_var.request_error : response.msg;
        alertInfo('fail', null, failMsg);
        // tinymce.get('post-content').getBody().setAttribute('contenteditable', 'true');
      }
      showLoader(false)
    });
  });
showLoader(true);
tinymce.init({
  selector: '#post-content',
  theme: 'silver',
  // content_css: '/assets/css/post.css',
  plugins: [
  'advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
  'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
  'save table directionality emoticons template paste'],
  toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons customReadMoreTagButton',
  init_instance_callback : "onLoadContentEditor",
  language: 'th_TH',
  image_title: true,
  automatic_uploads: false,
  image_dimensions: false,
   image_class_list: [
      {title: 'Responsive', value: 'img-fluid'}
  ],
  file_picker_types: 'image',
  file_picker_callback: function(cb, value, meta){
    browseImageFile(value, meta.filetype).then(function(res){
      cb(res.url, { title: res.filename });
    });  
  },
  relative_urls: false,
  remove_script_host: false,
  convert_urls: true,
  setup: function (editor){
    editor.ui.registry.addButton('customReadMoreTagButton',{
      text: 'Read More Tag',
      tooltip: 'Insert Read More Tag',
      onAction: function(_){
        editor.insertContent('<br/><!--more-->');
      },
    });
  },
  init_instance_callback: function(editor){
    // initialize data
    $.ajax({
      url: 'webservice/PostIni.php',
      type: 'get',
      dataType: 'json',
      data: {action: action, post_id: getQueryVariable('id')},
      beforeSend: showLoader(true),
    })
    .done(function(response){
      if(response.status==1){
        let coverSizeMin = response.config.cover_size_min.split(",");
        let coverSizeMax = response.config.cover_size_max.split(",");
        let coverSizeRatio = response.config.cover_ratio.split(",");
        PostImageConfig.width.min = coverSizeMin[0];
        PostImageConfig.height.min = coverSizeMin[1];
        PostImageConfig.width.max = coverSizeMax[0];
        PostImageConfig.height.max = coverSizeMax[1];
        PostImageConfig.ratio.x = coverSizeRatio[0];
        PostImageConfig.ratio.y = coverSizeRatio[1];
        PostImageConfig.supportExtension = response.config.img_ext.split(",");
        postImagePrefix = response.config.img_url;
        postCoverPrefix = response.config.cover_url;
        if(action=='update'){
          if(null !== response.post && void 0 !== response.post){
            document.getElementById('post-name').value = response.post.post_title;
            postImage = response.post.post_cover;
            (response.post.publish_status==1) ? document.getElementById('is-publish').checked = true : document.getElementById('is-publish').checked = false;
            (response.post.pin_status==1) ? document.getElementById('is-pin').checked = true : document.getElementById('is-pin').checked = false;
            var cv = new Image();
            cv.className = 'w-100';
            cv.src = postCoverPrefix+response.post.post_cover;
            $('#post-img-tag').show();
            var fig = $('#post-img-tag').find('figure');
            $(fig).append(cv);
            tinymce.get('post-content').setContent(response.post.post_content);

            var pDateTime = response.post.publish_date.split(" ");
            var pDate = pDateTime[0].split("-");
            var pTime = pDateTime[1].split(":");
            document.getElementById('publish_hour').value = pTime[0];
            document.getElementById('publish_minute').value = pTime[1];
            $("#publish_date").datepicker("update", pDate[2] + "/" + pDate[1] + "/" + pDate[0]);
            document.getElementById('post-slug').value = response.post.post_slug;
          }else{
            tinymce.remove('#post-content');
            $('#FormPostEditorContainer').addClass('w-100');
            $('#FormPostEditorContainer').html(parseTemplateFromDOM('#tmpl_404'));
          }
        }
      }
    })
    .always(function(){
      showLoader(false);
    });
    // read more tag image has to be with and without resize attr
    // because when view source editor remove withour resize attr
    var readMoreImage = '<img class="wp-my-custom-tag mce-wp-my-custom-tag" title="Read more" src="'+readmoreImageUrl+'" data-mce-placeholder="1" />';
    var readMoreImageWithNoResize = '<img class="wp-my-custom-tag mce-wp-my-custom-tag" title="Read more" src="'+readmoreImageUrl+'" data-mce-placeholder="1" data-mce-resize="false" />';
    var readMoreTag = '<!--more-->';
    editor.on('BeforeSetContent', function(e){
      if(e.content.indexOf(readMoreTag) !== -1){
        e.content = e.content.replace(readMoreTag, readMoreImageWithNoResize);
      }
    });
    editor.on('PostProcess', function(e){
      if(e.content){
        if(e.content.indexOf(readMoreImage) !== -1){
          e.content = e.content.replace(readMoreImage, readMoreTag);
        }
      }
    });
  }
});
function browseImageFile(editorCb, editorValue, editorMeta){
  var $def = $.Deferred();
  $.confirm({
    // title: 'Select Image  <button type="button" class="btn btn-primary btn-sm ml-3"><i class="fas fa-plus mr-1"></i>Add new</button>',
    title: txt_var.select_image,
    content: '<div class="container"><div class="row"></div></div><div class="media-loader" style="display: none;">' + getHTML('#loader_mini_tmpl') + '</div>',
    backgroundDismiss: true,
    closeIcon: true,
    columnClass: 'medium',
    onContentReady: function(){
      var $self = this;
      var iOffset = 0;
      var iLimit = 50;
      var onloadMedia = false;  
      var isLoadAll = false; 
      var row = $self.$content.find('.row').get(0);
      var loader = $self.$content.find('.media-loader').get(0);
      $self.buttons.select.disable();
      $($self.$content).on('change', 'input[name="rdo-media-selector"]', function(event) {
        event.preventDefault();
        $self.buttons.select.enable();
      });
      var loadTrigger = function(){
        $(loader).show();
        onloadMedia = true;
        loadMedia(iOffset, iLimit).then(function(response){
          var images = response.image;
          iOffset += images.length;
          if(images.length < iLimit){
            isLoadAll = true;
          }
          
          for(var i = 0; i < images.length; i++){
            var img_url = postImagePrefix + images[i];
            var ip_id = 'select-media-' + img_url;
            var div = document.createElement('DIV');
            div.className = 'col-4 col-md-3 mb-3';
            var sq = document.createElement('DIV');
            sq.className = 'square-parent bg-light';
            
            var lb = document.createElement('LABEL');
            lb.className = 'square-child media-img-selector';
            lb.style.backgroundImage = 'url(' + img_url + ')';
            lb.htmlFor = ip_id;
            var ip = document.createElement('INPUT');
            ip.type = 'radio';
            ip.name = 'rdo-media-selector';
            ip.id = ip_id;
            ip.value = images[i];
            var cm = document.createElement('DIV');
            cm.className = 'checkmark';
            lb.appendChild(ip);
            lb.appendChild(cm);
            sq.appendChild(lb);
            div.appendChild(sq);
            row.appendChild(div);
          }
          onloadMedia = false;
          $(loader).hide();
        }, function(error){
          onloadMedia = false;
          $(loader).hide();
        });
      }
      loadTrigger();
      $($self.$contentPane).scroll(function(event){
        if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 100){
          if(!onloadMedia && !isLoadAll){
            loadTrigger();
          }
        }
      });
    },
    buttons:{
      select: {
        text: txt_var.select,
        btnClass: "btn-primary",
        action: function(){
        var $self = this;
          var iSelected = $self.$content.find('input[name="rdo-media-selector"]:checked').get(0);
          if(void 0 !== iSelected){
            $def.resolve({url: postImagePrefix + iSelected.value, filename: iSelected.value});
          }else{
            $def.reject({msg: 'No file selected!'});
          }
        }
      },
      cancel: {
        text: txt_var.close,
        btnClass: "btn-danger",
        action: function(){
          $def.reject({msg: 'No file selected!'});
        }
      },
    },
  });
  return $def.promise();
}
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
function onLoadContentEditor(inst) {
  console.log('editor is loaded!')
}
});