(function(){
  $(document).on('submit', '#FormProfileInfo', function(event){
    ajaxFormSubmit(event)
  });


  $(document).on('submit', '#FormChangePassword', function(event){
    $('#confirm_password, #new_password').removeClass('border-danger');
    ajaxFormSubmit(event).then(function(){}, 
      function(error){
        if(error.status == 'success' && error.response.status == 3){
          $('#confirm_password, #new_password').addClass('border-danger');
        }
      });
    });
})();