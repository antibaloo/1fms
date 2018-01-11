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
	if ($leaders){
		arsort($leaders);
		reset($leaders);
		$arResult['leader']['ID'] = key($leaders);
		$rsUser = CUser::GetByID(key($leaders));
		$arUser = $rsUser->Fetch();
		$arResult['leader']['NAME'] = $arUser['NAME'];
		$arResult['leader']['LAST_NAME'] = $arUser['LAST_NAME'];
		$arResult['leader']['PHOTO'] = CFile::GetFileArray($arUser['PERSONAL_PHOTO']);
		$arResult['leader']['COUNT'] = current($leaders);
		$arResult['leader']['show'] = true;
	}else{
		$arResult['leader']['show'] = false;
	}
	/*Выбор всех мероприятий молла*/
	$arFilter=array(
		"IBLOCK_ID"=>$s5BitrixCodes->getIblockId('fmses'),
		"PROPERTY_MALL" => $arParams ['MALL_ID'],
	);
	$arSelect = array("ID");
	$rsFms = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while ($arFms = $rsFms->Fetch()){$Fmses[] = $arFms['ID'];}
	$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('instagram_albums'), "CODE"=>"STATUS"));
	$status_enums = array();
	while($enum_fields = $property_enums->GetNext()) {
		$status_enums[$enum_fields['XML_ID']] = $enum_fields;
	}
	/*Выбор опубликованных фото по мероприятиям молла*/
	$arFilter=array(
		"IBLOCK_ID"=>$s5BitrixCodes->getIblockId('instagram_albums'),
		"PROPERTY_FMS" => $Fmses,
		'PROPERTY_STATUS' => $status_enums['PUBLISHED']['ID']
	);
	$arSelect = array("PROPERTY_FMS");
	$rsAlbums = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while ($arAlbums = $rsAlbums->Fetch()){$fmsAlbums[] = $arAlbums['PROPERTY_FMS_VALUE'];}
	/*Уникальные ID мероприятий молла с опубликованными альбомами*/
	$fmsAlbums = array_unique($fmsAlbums);
	$rand_key = array_rand ($fmsAlbums);
	$arResult['RANDOM_ALBUM'] = $fmsAlbums[$rand_key];
	$this->IncludeComponentTemplate();
}
?>