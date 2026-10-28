function getQueryVariable(variable, decode){
  decode = (decode===undefined||typeof decode!=='boolean') ? false : decode;
  var query = window.location.search.substring(1);
  query = (decode===true) ? decodeURIComponent((query+'').replace(/\+/g, '%20')) : query;
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    if(pair[0]==variable){ return pair[1]; }
  }
  return(false);
}

function parseTemplate(templ, replaceParam, isParse){
  replaceParam = (replaceParam===undefined||typeof replaceParam!=='object') ? null : replaceParam;
  isParse = (isParse===undefined||typeof replaceParam!=='boolean') ? true : isParse;
  let  tmplHTML = $.trim(templ);
  if(replaceParam!==null){
    let _keys = [];
    let _value = [];
    for (var key in replaceParam){
      _keys.push(key);
      _value.push(replaceParam[key]);
    }
    tmplHTML = tmplHTML.replaceArray(_keys, _value)
  }
  if(isParse===true){
    tmplHTML = $.parseHTML(tmplHTML)
    tmplHTML = $(tmplHTML);
  }
  return (isParse) ? tmplHTML[0] : tmplHTML;
}

function parseTemplateFromDOM(elem, replaceParam, isParse){
  tmplHTML = $(elem).html();
  tmplHTML = $.trim(tmplHTML);
  tmplHTML = parseTemplate(tmplHTML, replaceParam, isParse);
  return tmplHTML;
}

function getHTML(s){
  var e = $(s).get(0);
  var _html = $(e).html();
  _html = $.trim(_html);
  return _html;
}

// extend string object
String.prototype.replaceArray = function(find, replace){
  var replaceString = this;
  var regex; 
  for (var i = 0; i < find.length; i++){
    regex = new RegExp(find[i], "g");
    replaceString = replaceString.replace(regex, replace[i]);
  }
  return replaceString;
};

function addCommaToNumber(val){
  while (/(\d+)(\d{3})/.test(val.toString())){
    val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
  }
  return val;
}

function imageToDataUri(url, callback, errCallback){
  var image = new Image();
  image.onload = function(){
    var canvas = document.createElement('canvas');
    canvas.width = image.width; // or 'width' if you want a special/scaled size
    canvas.height = image.height; // or 'height' if you want a special/scaled size
    canvas.getContext('2d').drawImage(image, 0, 0, image.width, image.height);

    // Get raw image data
    // callback(canvas.toDataURL('image/png').replace(/^data:image\/(png|jpg);base64,/, ''));
    // ... or get as Data URI
    callback(canvas.toDataURL('image/png'));
  };
  image.onerror = function(){
    errCallback();
  }
  image.src = url;
}

function dataURItoBlob(dataURI){
  // convert base64 to raw binary data held in a string
  // doesn't handle URLEncoded DataURIs - see SO answer #6850276 for code that does this
  var byteString = atob(dataURI.split(',')[1]);
  // separate out the mime component
  var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
  // write the bytes of the string to an ArrayBuffer
  var ab = new ArrayBuffer(byteString.length);
  var dw = new DataView(ab);
  for(var i = 0; i < byteString.length; i++) {
      dw.setUint8(i, byteString.charCodeAt(i));
  }
  // write the ArrayBuffer to a blob, and you're done
  return new Blob([ab], {type: mimeString});
}

function resizeBase64Img(base64, width, height){
  var canvas = document.createElement("canvas");
  canvas.width = width;
  canvas.height = height;
  var context = canvas.getContext("2d");
  var deferred = $.Deferred();
  $("<img/>").attr("src", base64).on('load', function(event){
    context.scale(width/this.width,  height/this.height);
    context.drawImage(this, 0, 0); 
    deferred.resolve($("<img/>").attr("src", canvas.toDataURL()));               
  });
  return deferred.promise();    
}

function checkAttributeSupport($tag, $attr){
  var i = document.createElement($tag);
  i.setAttribute($attr, true);
  return !!i[$attr];
}

function alertInfo(status, title, message, type){
  type = void 0 === type ? 'alert' : type;
  if(status=='success'){
    title = (void 0 === title || null === title) ? txt_var.success : title;
    message = (void 0 === message || null === message) ? txt_var.request_success : message;
    if(type=='alert'){
      $.alert({
        title: title,
        content: message,
        type: 'green',
        buttons: {
          close: {
            text: txt_var.close,
            btnClass: 'btn-success',
          }
        }
      });
    }else if(type=='dialog'){
      $.dialog({
        title: title,
        content: message,
        type: 'green',
        backgroundDismiss: false,
        closeIcon: false,
      });
    }
  }else if(status=='fail'){
    title = (void 0 === title || null === title) ? txt_var.error : title;
    message = (void 0 === message || null === message) ? txt_var.request_error : message;
    if(type=='alert'){
      $.alert({
        title: title,
        content: message,
        type: 'red',
        buttons: {
          close: {
            text: txt_var.close,
            btnClass: 'btn-danger',
          }
        }
      });
    }else if(type=='dialog'){
      $.dialog({
        title: title,
        content: message,
        type: 'red',
        backgroundDismiss: false,
        closeIcon: false,
      });
    }
  }
}

function showLoader(val){
  if(val===true)
    $('#main_loader').show();
  else
    $('#main_loader').hide();
}

function getFileExtension(filename, letterCase){
  var ext = (/[.]/.exec(filename)) ? /[^.]+$/.exec(filename)[0] : undefined;
  if(ext !== undefined){
    if(letterCase == 'lowercase'){
      return ext.toLowerCase();
    }else if(letterCase == 'uppercase'){
      return ext.toUpperCase();
    }else{
      return ext.toLowerCase();
    }
  }else{
    return '';
  }
}