<?php
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
require_once __DIR__.'/db.class.php';
$numberType = array(
    0 => "GEO / Fixed / WireLine Support",
    1 => "NGN & Mobile Support",
    2 => "Toll Free Support",
);
$rows = array();
$db = new db();
$sql = "select * from gpfs_number_pattern";
$pattern = $db->rawQuery($sql);

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("OPS")->setLastModifiedBy("levin.lin")->setTitle("Number Patterns exported from GPFS")->setSubject("Number Patterns exported from GPFS")->setDescription("Number Patterns exported from GPFS")->setKeywords("GPFS Number Pattern")->setCategory("Number Pattern");
$objPHPExcel->getActiveSheet()->setTitle('pattern');
foreach($pattern as $k => $p) {
    $indexA = "A".($k+1);
    $indexB = "B".($k+1);
    $indexC = "C".($k+1);
    $number_type = $numberType[$p["number_type"]];
    $is_supported = ($p["supported"]==1)?"YES":"NO";
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($indexA,$p["pattern"])->setCellValue($indexB,$number_type)->setCellValue($indexC,$is_supported);
    var_dump($indexA, $indexB, $indexC, $number_type, $is_supported);
}
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("Number Patterns exported from GPFS.xlsx");
