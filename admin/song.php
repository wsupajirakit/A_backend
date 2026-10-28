<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "song";
  $active_sm = "all-song";
  $enableSearch = false;
  $breadcrumbList = array(
    array($txt_var['song'], 'song.php', 'song'),
  );
  $addtional_resources = array(
    array('js', 'assets/js/form.js'),
    array('css', 'assets/lib/datatables/1.10.18/datatables.min.css'),
    array('js',  'assets/lib/datatables/1.10.18/datatables.min.js'),
  );
  require 'header.php';
?>
<script>
  txt_var.delete_data = "<?=$txt_var['delete_data'];?>";
  txt_var.delete_data_confirm = "<?=$txt_var['delete_data_confirm'];?>";

  $(document).ready(function(){
    $(document).on('submit', '.formDeleteSong', function(event) {
      event.preventDefault();
      $self = event.target;
      $.confirm({
        title: txt_var.delete_data,
        content: txt_var.delete_data_confirm,
        buttons: {
          confirm: {
            text: txt_var.confirm,
            btnClass: "btn-primary",
            action: function(){
              // table.row($($self).parents('tr')).remove().draw();
              // ajaxFormSubmit(event);
              ajaxFormSubmit(event).then(function(response){
                if(response.status==1){
                  table.row($($self).parents('tr')).remove().draw();
                }
              },);
              // var $fd = new FormData($self);
              // $.ajax({
              //   url: 'webservice/SongDelete.php',
              //   type: 'post',
              //   dataType: 'json',
              //   data: $fd,
              //   contentType: false,
              //   processData: false,
              // .done(function(response){
            } 
          },
          cancel: {
            text: txt_var.cancel,
            btnClass: "btn-danger",
          },
        }
      });
    });

    var table =  $('#song-table').DataTable({
      ajax: "webservice/SongList.php",
      columns: [
        { "data": "name" },
        { "data": "artists" },
        { "data": "id" },
      ],
      deferRender: true,
      responsive: true,
      fixedHeader: {
        headerOffset: 65
      },
      language: {
        url: "assets/lib/datatables/1.10.18/lang/th.json"
      },
      dom:  
        "<'row py-1'<'col-sm-6'l><'col-sm-6'f>>" +
        "<'row'<'col-sm-12'r>>" +
        "<'row'<'col-sm-12't>>" +
        "<'row'<'col-sm-4'i><'col-sm-8'p>>",
      "columnDefs": [{
        "targets": 2,
        // "data": "id",
        "orderable": false,
        "render": function (data, type, row, meta){
          return ['<div class="d-flex">',
          '<button type="submit" class="btn btn-sm btn-primary mr-2" onclick="window.location.href=\'song-form.php?action=update&id='+data+'\'" title="' + txt_var.update + '"><i class="fas fa-edit"></i></button>',
          '<form class="formDeleteSong" method="post" action="webservice/SongDelete.php"><fieldset><input type="hidden" name="id" value="'+data+'"><button type="submit" class="btn btn-sm btn-danger" title="' + txt_var.delete + '"><i class="fas fa-trash-alt"></i></button></fieldset></form>',
          '</div>'].join("")
        }
      }],
    });
  });
</script>
<div class="row">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <h3><?=$txt_var['song'];?></h3>
      <div class="page-menu-container">
        <button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='song-form.php?action=new'"><i class="fas fa-plus mr-1"></i><?=$txt_var['add_new'];?></button>
      </div>
      <hr>
      <div>
        <div class="table-responsive">
          <table class="table display dt-responsive nowrap w-100" id="song-table" cellpadding="0" cellspacing="0">
            <thead>
              <tr>
                <th><?=$txt_var['song_name'];?></th>
                <th><?=$txt_var['artist'];?></th>
                <th></th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
  require 'footer.php';
?>