<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
global $USER;
$arSelect = array(
	"ID",
	"NAME",
	"CODE",
	"ACTIVE_FROM",
	"DATE_CREATE",
	"CREATED_BY",
	"IBLOCK_ID",
	"IBLOCK_SECTION_ID",
	"DETAIL_PAGE_URL",
	"DETAIL_TEXT",
	"DETAIL_TEXT_TYPE",
	"DETAIL_PICTURE",
	"PREVIEW_TEXT",
	"PREVIEW_TEXT_TYPE",
	"PREVIEW_PICTURE",
	
);

global $s5BitrixCodes, $fmsesGetter;

$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('instagram_albums'), "CODE"=>"STATUS"));
$status_enums = array();
while($enum_fields = $property_enums->GetNext()) {
	$status_enums[$enum_fields['XML_ID']] = $enum_fields;
}

$arResult['TITLE'] = Fms\MallAwardsShareMessages::getMessageByFmsId($arParams['FMS_ID'], 'SHARED_PHOTO');


$arResult['URL'] = "/?album={$arResult['FMS']['ID']}&show_lang=".GetMessage('PLUSO_LANG');
if ($USER->IsAuthorized()){
	$arResult['URL'] .= '&user='.$USER->GetID();
}
$arResult['URL'] = "http://{$_SERVER['HTTP_HOST']}".\Fms\Utils::GetOrAddShortUri($arResult['URL']);

$arFilter = array(
	"IBLOCK_ID"=>$s5BitrixCodes->getIblockId('instagram_albums'), 
	'PROPERTY_FMS'=>$arParams['FMS_ID'],
	'PROPERTY_STATUS' => $status_enums['PUBLISHED']['ID']
);
$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
$arResult['PHOTOS'] = array();
$arResult['VIDEOS'] = array();
$arResult['COUNT_LIKES'] = 0;
while($ob = $res->GetNextElement()) {
	$arFields = $ob->GetFields();
	$arFields['PROPERTIES'] = $ob->GetProperties();
	
	$arFields['URL'] = "/?album={$arParams['FMS_ID']}&photo={$arFields['ID']}&show_lang=".GetMessage('PLUSO_LANG');
	if ($USER->IsAuthorized()){
		$arFields['URL'] .= '&user='.$USER->GetID();
	}
	$arFields['URL'] = "http://{$_SERVER['HTTP_HOST']}".\Fms\Utils::GetOrAddShortUri($arFields['URL']);
	
	if ($arFields['PROPERTIES']['THUMB']['VALUE']){
		$thumb_id = $arFields['PROPERTIES']['THUMB']['VALUE'];
	}else{
		$thumb_id = $arFields['PROPERTIES']['FILE']['VALUE'];
	}
	
	$arFields["PICTURE"] = CFile::GetFileArray($arFields['PROPERTIES']['FILE']['VALUE']);

	$tmp = CFile::ResizeImageGet($thumb_id,array('width'=>150, 'height'=>150), BX_RESIZE_IMAGE_EXACT, true);
    $arFields["PICTURE"]['THUMB'] =  Fms\ImageUrl::getUploadSrc($tmp['src']);
    $arFields["PICTURE"]['SRC']  =  Fms\ImageUrl::getUploadSrc($arFields["PICTURE"]['SRC']);

	$arResult['COUNT_LIKES'] += intval($arFields['PROPERTIES']['COUNT_LIKES']['VALUE']);
	
	if ($arFields['PROPERTIES']['MEDIA_TYPE']['VALUE_XML_ID'] == 'image'){
		$arResult['PHOTOS'][] = $arFields;
	}elseif($arFields['PROPERTIES']['MEDIA_TYPE']['VALUE_XML_ID'] == 'video'){
		$arFields['USER'] = CUser::GetByID($arFields['PROPERTIES']['USER']['VALUE'])->Fetch();
		$arResult['VIDEOS'][] = $arFields;
	}
} 






//echo '<pre>'; print_r($arResult); echo '</pre>';
$this->IncludeComponentTemplate();
?>