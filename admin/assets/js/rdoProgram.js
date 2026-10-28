$(document).ready(function($){
  var $action = getQueryVariable('action');
  var $rpId = getQueryVariable('id');
  $('#sidebar-toggle').on('change', function(event){
    setTimeout(repaintRdoProgramPane, 300)
  });

  $('.rp_datepicker').datepicker({
    format: 'dd/mm/yyyy',
    language: 'th',
    autoclose: true,
  });

  var rdoRadio = $('input[name="rdo_select"]');
  var currentRadio = $(rdoRadio[0]).val();
  var rpData = [];
  var rpConfig;
  var rpSelectorHtml = "";

  var iniTableParam = $action == 'new' ? {id: currentRadio, action: $action} : {id: $rpId, action: $action};
  showLoader(true);
  $.when(getRadioProgramTable(iniTableParam), getRadioProgramIni()).done(function(r1, r2){
    showLoader(false);
    rpTableCallback(r1);
    rpConfigCallback(r2);
  }).fail(showLoader(false));

  function rpTableCallback(data){
    $(".radio-program-table.schedule-selector tbody").selectable("destroy");
    $('#rpTableContainer').html(data);
    rpTableToSelectable();
  }

  function rpConfigCallback(data){
    rpData = data.result.radio_presenter;
    rpConfig = data.config;
    if(void 0 !== rpData && rpData !== null && rpData.length > 0){
      rpSelectorHtml = ['<div><div>', '<form>', '<fieldset>',  '<div>'].join("");
      for(var i = 0; i < rpData.length; i++){
        rpSelectorHtml += ['<div class="custom-control custom-checkbox mb-1 small-2">',
        '<input type="checkbox" class="custom-control-input rpCheck" id="rp',rpData[i].id,'" value="',rpData[i].id,'" />',
        '<label class="custom-control-label" for="rp',rpData[i].id,'">',
        '<div>',
        '<img src="',rpConfig.rp_url,rpData[i].image,'" class="rounded mr-2" style="width: 1.8rem;" />',
        '<span style="vertical-align: top;">',rpData[i].name,'</span>',
        '</div>',
        '</label>',
        '</div>'].join("");
      }
      rpSelectorHtml += ['</div>', '</fieldset>', '</form>', '</div></div>'].join("");
    }

    if($action == 'update'){
      if(data.result.schedule.length > 0){
        rpSchedule = data.result.schedule;
        for(var i = 0; i < rpSchedule.length; i++){
          createRdoProgramPane(rpSchedule[i].time, rpSchedule[i].rp, true);
          // createRdoProgramInput(rpSchedule[i].time, rpSchedule[i].rp);
          // let inpStr = rpSchedule[i].time.join(",") + ":" +rpSchedule[i].rp.join(",");
          // console.log(inpStr)
        }
        repaintRdoProgramPane();
      }else{
        $('#FormRadioProgramContainer').addClass('w-100');
        $('#FormRadioProgramContainer').html(parseTemplateFromDOM('#tmpl_404'));
      }
    }
  }

  function rpTableToSelectable(){
    $(".radio-program-table.schedule-selector tbody").selectable({
      filter: 'td, th',
      stop: function(){
        var selectedItems = $(".ui-selected");
        var checkDuplicate = true;
        var dt = [];
        $(selectedItems, this).each(function(){
          if($(this).hasClass('marked') || !$(this).is('td')){
            checkDuplicate = false;
            return false;
          }else{
            dt.push($(this).data('datetime'));
          }
        });
        if(checkDuplicate){
          selectRadioPresenter(rpSelectorHtml, dt).then(
            function(response){
              $.each(selectedItems, function(index, val){
                $(this).addClass('marked');
              });

              // split diff date
              var tmpDtList = {};
              for(var i = 0; i < dt.length; i++){
                let _ = dt[i].split("_");
                if(!(_[0] in tmpDtList))
                  tmpDtList[_[0]] = [];
                tmpDtList[_[0]].push(dt[i]);
              }

              $.each(tmpDtList, function(index, val){
                createRdoProgramPane(val, response.rp, false);
                createRdoProgramInput(val, response.rp);
              });
              repaintRdoProgramPane();
            },
            function(){
              $.each(selectedItems, function(index, val){
                $(val).removeClass('ui-selected');
              });
            }
          );
        }else{
          $(selectedItems, this).each(function(){
            $(this).removeClass('ui-selected');
          });
        }
      }
    });
  }

  function getRadioProgramIni(){
    var $defer = $.Deferred();
    $.ajax({
      url: 'webservice/RadioProgramIni.php',
      type: 'get',
      dataType: 'json',
      data: {action: $action, id: $rpId}
    })
    .done(function(response){
      if(response.status == 1){
        $defer.resolve(response);
      }else{
        $defer.reject(false);
      }
    })
    .fail(function(){
      $defer.reject(false);
    });
    return $defer.promise();
  }

  function getRadioProgramTable(param){
    var $defer = $.Deferred();
    $.ajax({
      url: 'radio-program-form-tmpl.php',
      type: 'get',
      dataType: 'html',
      data: param,
      cache: false,
    })
    .done(function(response){
      $defer.resolve(response);
    })
    .fail(function(){
      $defer.reject('');
    });
    return $defer.promise();
  }

  $('input[type=radio][name=rdo_select]').change(function(event){
    var $self = this;
    var rdo = $self.value;
    checkBeforeChangeRadio().then(function(){
      showLoader(true);
      getRadioProgramTable({id: rdo, action: $action}).then(function(response){
        currentRadio = rdo;
        showLoader(false);
        rpTableCallback(response);
      }, );
    }, function(){
      $($self).prop('checked', false);
      $('input[type=radio][name=rdo_select]').filter('[value="'+currentRadio+'"]').prop('checked', true);
    });
  });

  function checkBeforeChangeRadio(){
    var $defer = $.Deferred();
    var scheduled = $('input[name="rp_schedule[]"]');
    console.log(scheduled)
    if(scheduled.length == 0){
      $defer.resolve(true);
    }else{
      $.confirm({
        title: 'เปลี่ยนคลื่นวิทยุ',
        content: 'หากทำการเปลี่ยนคลื่นวิทยุ รายการจัดรายการที่ถูกเพิ่มไปก่อนหน้าจะถูกลบออก ต้องการยืนยันการเปลี่ยนแปลงหรือไม่?',
        buttons: {
          confirm: {
            text: txt_var.confirm,
            btnClass: "btn-primary",
            action: function(){
              $defer.resolve(true);
            }
          },
          cancel: {
            text: txt_var.cancel,
            btnClass: "btn-danger",
            action: function(){
              $defer.reject(false);
            }
          },
        },
        onClose: function(){
          if($defer.state() == 'pending'){
            $defer.reject(false);
          }
        }
      });
    }
    return $defer.promise();
  }

  $(window).resize(function(event){
    setTimeout(repaintRdoProgramPane, 250);
  });

  function selectRadioPresenter(_content, _datetime){
    var $defer = $.Deferred();
    $.confirm({
      title: 'เลือกผู้จัดรายการ',
      content: _content,
      onContentReady: function(){
        var $self = this;
        $self.buttons.confirm.disable();
        $($self.$content).on('change', '.rpCheck', function(event){
          event.preventDefault();
          var rp_selected = $self.$content.find('input.rpCheck:checked');
          if(rp_selected !== null && rp_selected.length > 0)
            $self.buttons.confirm.enable();
          else
            $self.buttons.confirm.disable();
        });
      },
      buttons: {
        confirm: {
          text: txt_var.confirm,
          btnClass: "btn-primary",
          action: function(){
            var rp_selected = $(this.$content.find('input.rpCheck:checked'));
            var selected_index = [];
            if(rp_selected !== null && rp_selected.length > 0){
              for(var i = 0; i < rp_selected.length; i++){
                selected_index.push($(rp_selected[i]).val())
              }
              $defer.resolve({rp: selected_index, dt: _datetime});
            }else{
              $defer.reject('no data selected');
            }
          }
        },
        cancel: {
          text: txt_var.cancel,
          btnClass: "btn-danger",
          action: function(){
            $defer.reject('cancel');
          }
        },
      },
      onClose: function(){
        if($defer.state() == 'pending'){
          $defer.reject('cancel');
        }
      }
    });
    return $defer.promise();
  }

  var _rpTmpl = document.getElementById('tmpl_rpDisplay').innerHTML;
  var _rpTmplMore = document.getElementById('tmpl_rpDisplayMore').innerHTML;
  var _rpTmplControl = document.getElementById('tmpl_rpControlMenu').innerHTML;
  var _rpTmplDj = document.getElementById('tmpl_rpdj').innerHTML;
  // _datetime = array
  // _radioPresenter = array
  function createRdoProgramPane(_datetime, _radioPresenter, _fromUpdate){
    let _datetimeFormatted = transformRpDateText(_datetime.join(","));
    var div = document.createElement('DIV');
    // div.id = "pane_" + _datetime.join(",").replace(/,/g, ".");
    div.id = "pane_" + _datetime.join(",");
    div.dataset.selectedItems = _datetime;
    div.dataset.rp = _radioPresenter;
    div.className = "position-absolute rdoProgramPane p-1 border-primary"; 
  
    var divInner = document.createElement('DIV');
    divInner.className = "position-relative d-flex align-items-center h-100";
  
    var divWrapper = document.createElement('DIV');
    divWrapper.className = "d-flex justify-content-center w-100";
  
    for(var i = 0; i < _radioPresenter.length; i++){
      if(i<=1){
        let _rp = $.grep(rpData, function(obj){return obj.id === _radioPresenter[i] ;})[0];
        let pm = {
          "{_img_}": rpConfig.rp_url + _rp.image, 
          "{_name_}": _rp.name, 
          "{_zIndex_}": (_radioPresenter.length-i), 
          "{_marginRight_}": function(){
            if(_radioPresenter.length==1)
              return "0";
            else
              if(_radioPresenter.length==2 && i==1)
                return "0";
              else
                return "-7px";
          }, 
          "d-none _": (_radioPresenter.length==1) ? "" : "d-none"
        };
        let _rpHtml = parseTemplate(_rpTmpl, pm);
        $(divWrapper).append(_rpHtml);
  
        if((_radioPresenter.length==1 || _radioPresenter.length==2) && i==0){
          let pm = {
            "{_num_after_2_}": _radioPresenter.length, 
            "{_num_total_}": _radioPresenter.length, 
            "d-md-inline _": "", 
            "{_rpDisplayClass_}": _radioPresenter.length==1 ? "d-none" : "d-block d-md-none",
          };
          let _rpHtml = parseTemplate(_rpTmplMore, pm);
          $(divWrapper).append(_rpHtml);
        }
      }
      else if(i==2){
        let pm = {
          "{_num_after_2_}": ((_radioPresenter.length - i) > 9 ? "9+" : (_radioPresenter.length - i)), 
          "{_num_total_}": (_radioPresenter.length - i > 9 ? "9+" : _radioPresenter.length)
        };
        let _rpHtml = parseTemplate(_rpTmplMore, pm);
        $(divWrapper).append(_rpHtml);
      }
    }  
    divInner.appendChild(divWrapper); 
    div.appendChild(divInner); 
    let _ctrlPm = {"{_dt_}": _datetime, "{_dj_}": _radioPresenter, "{_dt_fmt_}": [_datetimeFormatted[0][1] , ' ' , _datetimeFormatted[1][0], ' - ' , _datetimeFormatted[1][1] , ' น.'].join(""), "{_up_id_}": ($rpId !== false && _fromUpdate === true && $action == 'update') ? 'data-update="'+$rpId+'"' : ""};
    let _ctrl = parseTemplate(_rpTmplControl, _ctrlPm);
    div.appendChild(_ctrl); 
    document.getElementById("radioProgramTableContainer").appendChild(div);
    setTimeout(repaintRdoProgramPane, 100);
    return div;
  }

  function createRdoProgramInput(_datetime, _radioPresenter){
    var input = document.createElement("INPUT");
    input.type = "hidden";
    // input.id = "input_" + _datetime.join(",").replace(/,/g, ".");
    input.id = "input_" + _datetime.join(",");
    input.name = "rp_schedule[]";
    input.value = _datetime.join(",") + ":" + _radioPresenter.join(",");
    document.getElementById('datetimeInput').appendChild(input);
  }

  function createRdoProgramRemoveInput(_datetime){
    var input = document.createElement("INPUT");
    input.type = "hidden";
    input.id = "inputRemove_" + _datetime.join(",");
    input.name = "rpRemove_schedule[]";
    input.value = _datetime.join(",") + ":" + $rpId;
    document.getElementById('datetimeInputRemove').appendChild(input);
  }

  function repaintRdoProgramPane(){
    var rpPanes = $('.rdoProgramPane');
    $.each(rpPanes, function(index, val){
      var selector = $(val).data('selected-items').split(",");
      var selectedItems = [];
      for(var i = 0; i < selector.length; i++){
        selectedItems.push($('td.'+selector[i]).get(0));
      }
      val.style.left  = selectedItems[0].offsetLeft + "px";
      val.style.top   =  selectedItems[0].offsetTop + "px";
      val.style.width =  selectedItems[0].offsetWidth  + "px";
      val.style.height =  (selectedItems[0].offsetHeight  * selectedItems.length) + "px";
    });
  }

  $(document).on('click', '.btnRpSetting', function(event){
    event.preventDefault();
    var $self = this;
    var dt = $self.dataset.datetime;
    var dj = $self.dataset.radiopresenter;
    var du = $self.dataset.update;
    var datetime = transformRpDateText(dt);
    var _alert = $.alert({
      title: "รายละเอียดการจัดรายการ",
      content: function(){
        var _dj = dj.split(",");
        var _djHTML = "";
        for(var i = 0; i < _dj.length; i++){
          let _rp = $.grep(rpData, function(obj){return obj.id === _dj[i] ;})[0];
          let _pm = {"{_img_}": rpConfig.rp_url + _rp.image, "{_name_}": _rp.name};
          _djHTML += parseTemplate(_rpTmplDj, _pm, false);
        }
        return [
        '<h6 class="font-weight-bold">', "วัน/เวลา ", '</h6>',
        '<p>', 'วัน: ', datetime[0][0] , ' เวลา: ' , datetime[1][0], ' - ' , datetime[1][1] , ' น.', '</p>',
        '<h6 class="font-weight-bold">' , txt_var.radio_presenter , '</h6>', 
        _djHTML,
        '<div class="mt-4">', 
        '<h6 class="font-weight-bold">', "ดำเนินการ", '</h6>',
        // '<button class="btn btn-sm btn-info   mb-1 rpcSchedule">', "เพิ่มการจัดรายการแทน",'</button><br />',
        '<button class="btn btn-sm btn-danger mb-1 delSchedule">', "ลบรายการนี้",'</button><br />',
        '</div>',
        ].join("");
      },
      onContentReady: function(){
        var $self = this;
        this.$content.find('.delSchedule').click(function(){
          var _dt = dt.split(",");
          let x = dt.replace(/,/g, "\\,");
          $('#pane_' + x).remove();
          $('#input_' + x).remove();
          for(var i = 0; i < _dt.length; i++){
            $('td.' + _dt[i]).removeClass('marked');
            $('td.' + _dt[i]).removeClass('ui-selected');
          }

          if($action == 'update' && du == $rpId)
            createRdoProgramRemoveInput(_dt);
          
          _alert.close();
        });
    },
      buttons: {
        close: {
          text: txt_var.close,
          btnClass: 'btn-danger',
        },
      }
    });
  });

  function transformRpDateText(_datetime){
    var _splitTime = _datetime.split(",");
    var _timeArr = [];
    for(var i = 0; i < _splitTime.length; i++){
      let dtF = _splitTime[i].split("-");
      let dtFex = [dtF[0].split("_"), dtF[1].split("_")];
      _timeArr.push(dtFex);
    }
    var _day = txt_var.days_name[_timeArr[0][0][0]];
    var _dayShort = txt_var.days_name_abb[_timeArr[0][0][0]];
    var _datetimeFormatted;
    _datetimeFormatted = [[_day, _dayShort] , [_timeArr[0][0][1] + ":" + _timeArr[0][0][2] , _timeArr[_splitTime.length-1][1][1] + ":" + _timeArr[_splitTime.length-1][1][2]]];
    return _datetimeFormatted;
  }
});