<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
global $s5BitrixCodes, $USER;
$rsUser = CUser::GetByID($arParams ['MALL_ID']);
$arUser = $rsUser->Fetch();
$arResult['mallLogo'] =  CFile::GetFileArray($arUser['WORK_LOGO']);

$arFilter=array(
	"IBLOCK_ID"=>$s5BitrixCodes->getIblockId('mall_awards'),
	"PROPERTY_MALL" => $arParams ['MALL_ID'],
	"PROPERTY_YEAR" => date("Y"),
	"PROPERTY_MONTH" => date("n")
);
$arSelect = array("DETAIL_PICTURE", "DETAIL_TEXT");
$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
if ($arResult['prize'] = $res->Fetch()) {//Если есть приз месяца
	$arResult['prize']['DETAIL_PICTURE'] = CFile::GetFileArray($arResult['prize']['DETAIL_PICTURE']);
	//Ищем лидера
	$arFilter=array(
		"IBLOCK_ID"=>$s5BitrixCodes->getIblockId('referal2'),
		"PROPERTY_MALL" => $arParams ['MALL_ID'],
		"PROPERTY_YEAR" => date("Y"),
		"PROPERTY_MONTH" => date("n")
	);
	$arSelect = array("PROPERTY_USER");
	$rsLeaders = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while($arLeader = $rsLeaders->Fetch()){
		$leaders[] = $arLeader["PROPERTY_USER_VALUE"];
	}
	$leaders = array_count_values ($leaders);
	arsort($leaders);
	reset($leaders);
	$arResult['leader']['ID'] = key($leaders);
	$rsUser = CUser::GetByID(key($leaders));
	$arUser = $rsUser->Fetch();
	$arResult['leader']['NAME'] = $arUser['NAME'];
	$arResult['leader']['LAST_NAME'] = $arUser['LAST_NAME'];
	$arResult['leader']['PHOTO'] = CFile::GetFileArray($arUser['PERSONAL_PHOTO']);
	$arResult['leader']['COUNT'] = current($leaders);
	$this->IncludeComponentTemplate();
}
?>