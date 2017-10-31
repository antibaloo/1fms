<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//echo '<pre>'; print_r($arParams); echo '</pre>'; exit();
//echo '<pre>'; print_r($arResult); echo '</pre>'; exit();
//echo '<pre>'; print_r($arResult['form']); echo '</pre>'; exit();
//echo '<pre>'; print_r($_POST); echo '</pre>';
if ($arResult['error']) {
	echo $arResult['error'];
	return;
}

\Fms\HtmlParts\FmsDetail::show(array(
	'location' => 'fms_form',
	'data' => array(
		'id'               => $arResult['id'],
		'form'             => $arResult['form'],
		'sections'         => $arResult['sections'],
		'brand_names_list' => $arResult['brand_names_list'],
		'location'         => $arResult['location'],
		'subscription'     => true,
	),
	'fms_block_params' => array(
		'modifier_class' => 'fmsBlock_mDetail fmsBlock_mFmsForm',
		'location'       => 'fms_form',
		'data' => array(
			'form'         => $arResult['form'],
			'price_format' => $arResult['price_format'],
		),
	),
));