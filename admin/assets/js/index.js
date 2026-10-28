$(document).ready(function($){
  $('.custom-scbar').each(function(i){
    new SimpleBar(this);
  });

  // $('.masonry-grid').masonry({
  //   itemSelector: '.masonry-grid-item',
  //   columnWidth: '.masonry-grid-sizer',
  // });

  $(document).on('click', function(event){
    $('.nav-dropdown').hide();
  });
  
  $('#sidebar-toggle').on('change', function(event){
    event.preventDefault();
    if($(this).is(':checked')){
      $('body').addClass('is-collapsed');
      openClosedDropdownMenu();
    }else{
      $('body').removeClass('is-collapsed');
      closeOpenedDropdownMenu();
    }
  });

  $('#search-toggle').on('change', function(event){
    event.preventDefault();
    var $self = this;
    if($($self).is(':checked')){
      $('#search-form').show();
      $('#search').focus();
    }else{
      $('#search-form').hide();
    }
  });

  $('#close-sidebar').on('click', function(event){
    event.preventDefault();
    $('#sidebar-toggle').prop('checked', false);
    $('#sidebar-toggle').trigger('change');
  });

  // open sidebar dropdown menu
  $('.menu-item.dropdown').on('change', 'input[type="checkbox"][name="sidebar-menu-dropdown"]', function(event){
    event.preventDefault();
    var self = this;
    var ul = $(self).parent('label.item-link').siblings('.sidebar-menu').get(0);
    if($(self).is(':checked')){
      var dd = $(sidebar_dd_menu).filter(':checked').not(self);
      $(dd).prop('checked', false);
      $(dd).trigger('change');
      $(ul).slideDown();
    }else{
      $(ul).slideUp();
    }
  });

  $('.sidebar').hover(function(){
    $(this).addClass('is-hovered');
    openClosedDropdownMenu();
  }, function() {
    $(this).removeClass('is-hovered');
    if(!$('body').is('.is-collapsed')){
      closeOpenedDropdownMenu();
    }
  });

  var sidebar_dd_menu = $('input[type="checkbox"][name="sidebar-menu-dropdown"]');
  var tmp_dd = null;
  function closeOpenedDropdownMenu(){
    var d = $(sidebar_dd_menu).filter(':checked');
    $(d).prop('checked', false);
    $(d).trigger('change');
    tmp_dd = d;
  }
  
  function openClosedDropdownMenu(){
    $(tmp_dd).prop('checked', true);
    $(tmp_dd).trigger('change');
  }

  $('.user-nav-toggle').on('click', function(event){
    // event.preventDefault();
    var $self = this;
    var targetDD = $($self).find('.nav-dropdown').get(0);
    var otherDD = $('.user-nav-toggle').not($self).find('.nav-dropdown');
    $(otherDD).hide();
    if($(targetDD).is(':visible')){
      $(targetDD).hide();
    }else{
      $(targetDD).show();
    }
    event.stopImmediatePropagation();
  });

  $('a.j-confirm-link').confirm({
    buttons:{
      confirm:{
        text: txt_var.confirm,
        btnClass: "btn btn-primary",
        action: function(){
          location.href = this.$target.attr('href');
        } 
      },
      cancel:{
        text: txt_var.cancel,
        btnClass: "btn btn-danger",
      }
    }
  });
});
// $(window).on('load', function(event){
//   event.preventDefault();
//   setTimeout(function(){
//     $('#preloader').fadeOut('400', function(){});
//   },500);
// });