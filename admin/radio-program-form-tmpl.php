<?php
  // HTML fragment loaded by assets/js/rdoProgram.js; signed-in admins only.
  require_once __DIR__.'/bootstrap.php';
  require_once __DIR__.'/assets/message_var/th.php';
  require_once __DIR__.'/assets/php-function/pdo-database.php';
  require_once __DIR__.'/assets/php-function/function.php';

  if($adminAuth->user() === null){
    http_response_code(401);
    exit;
  }
  $db = new DatabaseConnection();

  $action = (string) ($_GET['action'] ?? '');
  $id = (int) ($_GET['id'] ?? 0);
	$radioProgramConfig = $siteConfig['radioprogram-config'];
  $radioList = json_decode($radioProgramConfig['radio_list'], true);

  if($action == 'new'){
    $radio = searchArrayByValue($radioList, 'id', $id);
    list($hrStart, $hrEnd) = array_map('intval', explode("-", $radio['onair']));
    $hrGap = intval($radio['hour_gap']);
  }else{
    $rpData = $db->dbRow("SELECT * FROM radio_program WHERE rdo_program_id = :id", array(':id' => $id));
    list($hrStart, $hrEnd) = array_map('intval', explode("-", $rpData['rdo_program_time_range']));
    $hrGap = $rpData['rdo_program_hour_gap'];
  }
?>
<div>
  <table class="table table-bordered radio-program-table">
    <thead>
      <tr>
        <th class="text-center"  style="width: 50px;"><?=$txt_var['time'];?></th>
      </tr>
    </thead>
    <tbody>
    <?php
    $jn = $hrGap;
    for($i = $hrStart; $i < $hrEnd; $i++){
      for($j = 0; $j < 60; $j+=$jn){?>
      <tr>
        <td class="p-1 small text-center no-select-here"><?= e(sprintf("%02d", $i) . ":" . sprintf("%02d", $j)) ?></td>
      </tr>
    <?php } }?>
    </tbody>
  </table>
</div>
<div class="w-100" style="overflow-x: auto;">
  <div class="position-relative" id="radioProgramTableContainer">
  <table class="table table-bordered radio-program-table table-fixed schedule-selector">
    <thead>
      <tr>
        <?php for ($k=0; $k < 7; $k++){?>
          <th class="px-1 text-center">
            <span class="d-none d-sm-inline">
              <?=$txt_var['days_name'][$k];?>
            </span>
            <span class="d-sm-none">
              <?=$txt_var['days_name_abb'][$k];?>
            </span>
            </th>
        <?php }?>
      </tr>
    </thead>
    <tbody>
    <?php for($i = $hrStart; $i < $hrEnd; $i++){
      for($j = 0; $j < 60; $j+=$jn){?>
      <tr>
        <?php for($k=0; $k < 7; $k++){
          $dt1 = $k . "_" . sprintf("%02d", $i) . "_" . sprintf("%02d", $j);
          $dt2 = $k . "_" . sprintf("%02d", $i) . "_" . sprintf("%02d", ($j+$jn-1));
          $dtF = $dt1 . "-" . $dt2;
        ?>
          <td data-datetime="<?= e($dtF) ?>" class="<?= e($dtF) ?>"></td>
        <?php }?>
      </tr>
    <?php }}?>
    </tbody>
  </table>
  </div>
</div>
