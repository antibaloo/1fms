<?php
//UF_CRM_1531743520
define('KEY', '2SBZy3fsZXdfpAyZNsDx');
if ($_GET['key'] != KEY) die("<span style='color:red;'>Ошибка!<br>Не введен ключ запроса!</span>");
if (strlen($_GET['result']) == 0) die("<span style='color:red;'>Ошибка!<br>Не передан текст результата!</span>");
//echo $_GET['result'];
preg_match_all("/[RL](\d+|R)\sS:.{4,7}C:.{4,7}AX:.{3}/", $_GET['result'], $out, PREG_PATTERN_ORDER);
/*echo "<pre>";
print_r($out);
echo "</pre>";*/
$result = array();
foreach ($out[0] as $rString){
	$index = substr($rString,0,strpos($rString," "));
	$sString = substr($rString,strpos($rString,"S:")+2,strpos($rString,"C:")-strpos($rString,"S:")-2);
	$cString = substr($rString,strpos($rString,"C:")+2,strpos($rString,"AX:")-strpos($rString,"C:")-2);
	$axString = substr($rString,strpos($rString,"AX:")+3);
	
	$sString = str_replace('+','', $sString);
	$cString = str_replace('+','', $cString);
	$axString = str_replace('+','', $axString);
	$result[$index] = array("S" => str_replace(' ','', $sString), "C" => str_replace(' ','', $cString), "AX" => str_replace(' ','', $axString));
}
/*echo "<pre>";
print_r($result);
echo "</pre>";
*/
echo json_encode ($result);
?>