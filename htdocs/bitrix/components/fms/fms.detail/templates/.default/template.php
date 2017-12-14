<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//echo '<pre>'; print_r($arParams); echo '</pre>'; exit();
//echo '<pre>'; print_r($arResult['fms']['DETAIL_PICTURE']); echo '</pre>'; exit();
//echo '<pre>'; print_r($arResult['fms']['time_left']); echo '</pre>'; exit();
if (isset($arResult['error'])) {
	echo $arResult['error'];
	return;
}

\Fms\HtmlParts\FmsDetail::show(array(
	'location' => 'detail',
	'data' => $arResult,
	'fms_block_params' => array(
		'location' => 'detail',
		'modifier_class' => 'fmsBlock_mDetail',
		'data' => $arResult['fms']
	),
));
$APPLICATION->IncludeComponent(
		'fms:fms.album',
		'',
		array(
		),
		false
	);
?>