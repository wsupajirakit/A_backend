$(document).ready(function($){
  $('#displayArtistList.sortable').sortable({
    update: function(event, ui){
      var artistsInput = $('input[name="artists"]');
      var artistsListElement = document.getElementsByClassName('artist-lb');
      var artistsOrder = [];
      for(var i = 0; i < artistsListElement.length; i++){
        var _d = $(artistsListElement[i]).data('artist-id');
        artistsOrder.push(_d);
      }
      $(artistsInput).val(artistsOrder.join(","));
    }
  });
  $('#displayArtistList.sortable').disableSelection();

  var artistSelect = document.getElementById('artists_select');
  $(document).on('click', '#addArtist', function(event){
    event.preventDefault();
    var artistsInput = $('input[name="artists"]');
    var artistsArr = undefined===$(artistsInput).val() || $(artistsInput).val()=="" ? [] : $(artistsInput).val().split(",");
    var artistId = artistSelect.value;
    var artistName = artistSelect.options[artistSelect.selectedIndex].text;
    if(artistsArr.indexOf(artistId) == -1){
      artistsArr.push(artistId);
      $(artistsInput).val(artistsArr.join(","));
      var param = {'{{artist_name}}': artistName, '{{artist_id}}' : artistId};
      var lb = parseTemplateFromDOM('#tmpl-artist-label', param);
      $('#displayArtistList').append(lb);
    }
  });

  $(document).on('click', '#submitFormSong', function(event){
    var $self = event.target;
    $('#FormSong').find('input[type="submit"]').trigger('click');
  });
});

function removeArtist(id){
  var artistsInput = $('input[name="artists"]');
  var artistsArr = undefined===$(artistsInput).val() || $(artistsInput).val()=="" ? [] : $(artistsInput).val().split(",");
  var index = artistsArr.indexOf(id);
  if(index !== -1) 
    artistsArr.splice(index, 1);

  $(artistsInput).val(artistsArr.join(","));
  $("#artist-"+id).remove();
}