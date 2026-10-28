$(document).ready(function($){
  var cover = document.getElementById('cover');
  if(void 0 !== cover && null !== cover){
    $(cover).slick({
      autoplay: true,
      autoplaySpeed: 5000,
      dots: true,
      prevArrow: '<button type="button" class="cover-arrow prev slick-prev"><i class="fas fa-chevron-left"></i></button>',
      nextArrow: '<button type="button" class="cover-arrow next slick-next"><i class="fas fa-chevron-right"></i></button>',
      dotsClass: 'cover-dots',
      mobileFirst: true,
    });
  }

  var announceMsg = document.getElementById('announce-msg');
  if(void 0 !== announceMsg && null !== announceMsg){
    $(announceMsg).marquee({
      pauseOnHover: true,
      duration: $(announceMsg).width() * 30,
      gap: 50,
      duplicated: true,
    });
  }
  var homePostPreview = document.getElementsByClassName('post-preview-p');
  if(!CSS.supports("(-webkit-line-clamp: 2)")){
    if(void 0 !== homePostPreview && homePostPreview.length > 0){
      var n = void 0 !== homePostPreview[0].dataset.ellipLine ? parseInt(homePostPreview[0].dataset.ellipLine, 10) : 3;
      $('.post-preview-p').ellipsis({
        lines: n,             
        ellipClass: 'ellip', 
        responsive: true      
      });
    }
  }

  if($('#hamburger-check').is(':checked'))
    $('#main-nav').addClass('on-open');
  
  $(document).on('change', '#hamburger-check', function(event){
    event.preventDefault();
    $self = event.target;
    var isCheck = $($self).is(':checked');
    if(isCheck)
      $('#main-nav').addClass('on-open');
    else
      $('#main-nav').removeClass('on-open');
  });
});