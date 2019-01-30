<?php
//UF_CRM_1531743520
define('KEY', '2SBZy3fsZXdfpAyZNsDx');
if ($_GET['key'] != KEY) die("<span style='color:red;'>Ошибка!<br>Не введен ключ запроса!</span>");
if (strlen($_GET['agreementId']) == 0) die("<span style='color:red;'>Ошибка!<br>Не введен код активации согласия!</span>");
$queryUrl = 'https://zdz-online.bitrix24.ru/rest/16/opxrg0lm0un683us/crm.lead.list.json/';
$queryData = http_build_query(
	array( 
		'order' => array("ID"=>"DESC"),
		'filter' => array("UF_CRM_1522138105"=>$_GET['agreementId']),
		'select' => array("UF_CRM_1531743520")
	)
);

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_SSL_VERIFYPEER => 0,
	CURLOPT_POST => 1,
	CURLOPT_HEADER => 0,
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => $queryUrl,
	CURLOPT_POSTFIELDS => $queryData,
));
$replay = json_decode(curl_exec($curl), true);
curl_close($curl);
if ($replay['total'] >1) die("<span style='color:red;'>Ошибка!<br> Несколько согласий в системе. Обратитесь к администратору.</span>");
if ($replay['total'] == 0) die("<span style='color:red;'>Ошибка!<br> Проверьте правильность ввода кода активации согласия.</span>");
echo $replay['result'][0]['UF_CRM_1531743520'];
?>