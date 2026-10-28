$(document).ready(function($){
  displayTopChartSongsOrder();

  $('#top_chart_date').datepicker({
    format: 'dd/mm/yyyy',
    language: 'th',
    autoclose: true,
  });

  $('#displaySongtList.sortable').sortable({
    update: function(event, ui){
      var songsInput = $('input[name="songs"]');
      var songsListElement = document.getElementsByClassName('song-lb');
      var songsOrder = [];
      for(var i = 0; i < songsListElement.length; i++){
        var _d = $(songsListElement[i]).data('song-id');
        var _s = getSongStats(_d)
        // songsOrder.push(_d);
        songsOrder.push(_s.join("_"));
      }
      $(songsInput).val(songsOrder.join(","));
      displayTopChartSongsOrder();
    }
  });
  $('#displaySongtList.sortable').disableSelection();

  var songsSelect = document.getElementById('songs_select');
  var topChartNumSelect = document.getElementById('top_chart_num');
  $(document).on('click', '#addSong', function(event){
    event.preventDefault();
    var maxSongs = topChartNumSelect.value;
    var songsInput = $('input[name="songs"]');
    var songsArr = undefined===$(songsInput).val() || $(songsInput).val()=="" ? [] : $(songsInput).val().split(",");
    if(songsArr.length >= maxSongs)
      return;
    var songId = songsSelect.value;
    var songValue = songsSelect.value + "_0_0";
    var songName = songsSelect.options[songsSelect.selectedIndex].text;
    // if(songsArr.indexOf(songId) == -1){
    if(validateSongsRepeat(songId)){
      songsArr.push(songValue);
      $(songsInput).val(songsArr.join(","));
      var param = {'{{song_name}}': songName, '{{song_id}}' : songId, "{{_lwo_}}": "", "{{_ocwn_}}": ""};
      var lb = parseTemplateFromDOM('#tmpl-song-label', param);
      $('#displaySongtList').append(lb);
    }
    displayTopChartSongsOrder();
  });

  $(document).on('click', '#submitFormTopChart', function(event){
    var $self = event.target;
    $('#FormTopChart').find('input[type="submit"]').trigger('click');
  });

  $(document).on('input', 'input[name="last_week_order"], input[name="on_chart_week_number"]', function(event) {
    event.preventDefault();
    var $self = this;
    var val = $.trim($($self).val());
    val = val == "" ? 0 : val;
    if($($self).is('.ipNumeric'))
      val = parseInt($.trim($($self).val()), 10);

    var sid = $($self).data('song-id');
    var songsInput = $('input[name="songs"]');
    var songsArr = undefined===$(songsInput).val() || $(songsInput).val()=="" ? [] : $(songsInput).val().split(",");
    for(var i = 0; i < songsArr.length; i++){
      let v = songsArr[i].split("_");
      if(v[0] == sid){

        if($($self).is('input[name="last_week_order"]'))
          v[1] = val;
        else if($($self).is('input[name="on_chart_week_number"]'))
          v[2] = val;
        songsArr[i] = v.join("_");
        break;
      }
    }
    $(songsInput).val(songsArr.join(","));
  });
});

function removeSong(id){
  var songsInput = $('input[name="songs"]');
  var songsArr = undefined===$(songsInput).val() || $(songsInput).val()=="" ? [] : $(songsInput).val().split(",");
  // var index = songsArr.indexOf(id);
  // if(index !== -1) 
  for(var i = 0; i < songsArr.length; i++){
    let v = songsArr[i].split("_");
    if(v[0] == id)
      songsArr.splice(i, 1);
  }
  // if(!validateSongsRepeat(id)) 
  //   songsArr.splice(index, 1);

  $(songsInput).val(songsArr.join(","));
  $("#song-"+id).remove();
  displayTopChartSongsOrder();
}

function displayTopChartSongsOrder(){
  var songsInput = $('input[name="songs"]');
  var songsArr = undefined===$(songsInput).val() || $(songsInput).val()=="" ? [] : $(songsInput).val().split(",");
  for(var i=0; i < songsArr.length; i++){
    let v = songsArr[i].split("_");
    $('.song-lb.song-' + v[0]).find('.song-order').text(i + 1);
    // console.log($('.song-lb.song-' + i).find('.song-order'))
  }
}

function getSongStats(id){
  var e = $('.song-lb.song-' + id);
  if(e === void 0)
    return false;
  var l = $(e).find('input[name="last_week_order"]').get(0);
  var o = $(e).find('input[name="on_chart_week_number"]').get(0);
  var lv = $.trim($(l).val()) == "" ? 0 : parseInt($.trim($(l).val()), 10);
  var ov = $.trim($(o).val()) == "" ? 0 : $.trim($(o).val());
  // var ov = $.trim($(o).val()) == "" ? 0 : parseInt($.trim($(o).val()), 10);
  return [id, lv, ov];
}

function validateSongsRepeat(id){
  var songsInput = $('input[name="songs"]');
  var songsArr = undefined===$(songsInput).val() || $(songsInput).val()=="" ? [] : $(songsInput).val().split(",");
  if(songsArr.length == 0)
    return true;

  for(var i = 0; i < songsArr.length; i++){
    let v = songsArr[i].split("_");
    if(v[0] == id)
      return false;
  }
  return true;
}