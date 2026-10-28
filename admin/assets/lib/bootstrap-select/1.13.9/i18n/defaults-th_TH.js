/*
 * Translated default messages for bootstrap-select.
 * Locale: TH (THAILAND)
 * Region: TH (THAILAND)
 */
(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'ไม่มีการเลือก',
    noneResultsText: 'ไม่พบข้อมูลที่ตรงกับ {0}',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected == 1) ? 'จำนวนที่เลือก {0}' : 'จำนวนที่เลือก {0}';
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        (numAll == 1) ? 'ข้อมูลเกินขีดจำกัด (ข้อมูลสูงสุด {n})' : 'ข้อมูลเกินขีดจำกัด (ข้อมูลสูงสุด {n})',
        (numGroup == 1) ? 'ข้อมูลกลุ่มเกินขีดจำกัด (ข้อมูลสูงสุด {n})' : 'ข้อมูลกลุ่มเกินขีดจำกัด (ข้อมูลสูงสุด {n})'
      ];
    },
    selectAllText: 'เลือกทั้งหมด',
    deselectAllText: 'ยกเลิกที่เลือกทั้งหมด',
    multipleSeparator: ', '
  };
})(jQuery);
