<?php
  require_once __DIR__.'/assets/message_var/th.php';
  $active = "top-chart";
  $active_sm = "all-top-chart";
  $enableSearch = false;
  $breadcrumbList = array(
    array($txt_var['top_chart'], 'top-hart.php', 'top chart'),
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
  txt_var.request_error = "<?=$txt_var['request_error'];?>";
  txt_var.request_error = "<?=$txt_var['request_error'];?>";
  txt_var.number_order = "<?=$txt_var['number_order'];?>";
  txt_var.song = "<?=$txt_var['song'];?>";
  txt_var.close = "<?=$txt_var['close'];?>";
  txt_var.song_list = "<?=$txt_var['song_list'];?>";
  txt_var.top_chart_preview = "<?=$txt_var['top_chart_preview'];?>";
  function showTopChartSongs(id){
    $.alert({
      columnClass: 'medium',
      content: function(){
        var self = this;
        return $.ajax({
          url: 'webservice/TopChartSongList.php',
          ataType: 'json',
          ethod: 'get',
          data: {id: id},
        }).done(function (response){
          var _html = '<table class="table">';
          _html += ['<tr><th>',txt_var.number_order,'</th><th>',txt_var.song,'</th></tr>'].join("");
          $.each(response.data.song, function(index, val) {
            _html += ['<tr>',
            '<th scope="row">',val.number + 1,'</th>',
            '<td>',val.song,' - <small class="text-muted">',val.artist,'</small>','</td>',
            '</tr>'].join("");
          });
          _html += '</table>';
          self.setContent(_html);
          self.setTitle(response.data.title);
        }).fail(function(){
          self.setContent(txt_var.request_error);
        });
      },
      buttons: {
        close: {
          text: txt_var.close,
          btnClass: "btn-danger",
        }
      }
    });
  }
  $(document).ready(function(){
    $(document).on('submit', '.formDeleteTopChart', function(event) {
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
              ajaxFormSubmit(event).then(function(response){
                if(response.status==1){
                  table.row($($self).parents('tr')).remove().draw();
                }
              },);
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
      ajax: "webservice/TopChartList.php",
      columns: [
        { "data": "date" },
        { "data": "num_1_song" },
        { "data": "num_song" },
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
      "order": [[ 0, "desc" ]],
      "columnDefs": [{
        "targets": 0,
        "render": {
          _: 'display',
          sort: 'timestamp'
        }
      },
      {
        "targets": 1,
        "render": function (data, type, row, meta){
          return ['<div class="d-flex">',
          '<button type="submit" class="btn btn-sm btn-info mr-2" onclick="showTopChartSongs('+row.id+')" title="' + txt_var.song_list + '"><i class="fas fa-list-ol"></i></button>',
          data].join("")
        }
      },
      {
        "targets": 3,
        "orderable": false,
        "render": function (data, type, row, meta){
          return ['<div class="d-flex">',
          '<button type="submit" class="btn btn-sm btn-success mr-2" onclick="window.open(\'top-chart-view.php?id='+data+'\', \'_blank\')" title="' + txt_var.top_chart_preview + '"><i class="far fa-eye"></i></button>',
          '<button type="submit" class="btn btn-sm btn-primary mr-2" onclick="window.location.href=\'top-chart-form.php?action=update&id='+data+'\'" title="' + txt_var.update + '"><i class="fas fa-edit"></i></button>',
          '<form class="formDeleteTopChart" method="post" action="webservice/TopChartDelete.php" data-notice="none"><fieldset><input type="hidden" name="id" value="'+data+'"><button type="submit" class="btn btn-sm btn-danger" title="' + txt_var.delete + '"><i class="fas fa-trash-alt"></i></button></fieldset></form>',
          '</div>'].join("")
        }
      }],
    });
  });
</script>
<div class="row">
  <div class="col-12">
    <div class="bg-white p-3 my-2 border">
      <h3><?=$txt_var['top_chart'];?></h3>
      <div class="page-menu-container">
        <button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='top-chart-form.php?action=new'"><i class="fas fa-plus mr-1"></i><?=$txt_var['add_new'];?></button>
      </div>
      <hr>
      <div>
        <div class="table-responsive">
          <table class="table display dt-responsive nowrap w-100" id="song-table" cellpadding="0" cellspacing="0">
            <thead>
              <tr>
                <th><?=$txt_var['date'];?></th>
                <th><?=$txt_var['number_order_1'];?></th>
                <th><?=$txt_var['song_number'];?></th>
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