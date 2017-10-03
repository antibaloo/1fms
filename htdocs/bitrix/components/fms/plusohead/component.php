<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

global $fmsesGetter,$s5BitrixCodes;
if ($_GET['album']){
	$arResult = $fmsesGetter->getDataForDetailPageById($_GET['album']);
	
	

	$arFilterCover = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('instagram_albums'), 'PROPERTY_FMS'=>$arResult['fms']['ID'],'PROPERTY_STATUS' => $status_enums['PUBLISHED']['ID']);
	if ($_GET['photo']){
		$arFilterCover['ID'] = $_GET['photo'];
	}
	$resCover = CIBlockElement::GetList(Array('ID'=>'ASC'), $arFilterCover, false, Array("nPageSize"=>1), $arSelect);
	if($obCover = $resCover->GetNextElement()) {
		$arFieldsCover = $obCover->GetFields();
		$arFieldsCover['PROPERTIES'] = $obCover->GetProperties();

		$thumb_id = $arFieldsCover['PROPERTIES']['FILE']['VALUE'];

		$arFieldsCover["PICTURE"] = CFile::GetFileArray($arFieldsCover['PROPERTIES']['FILE']['VALUE']);
		$tmp = CFile::ResizeImageGet($thumb_id,array('width'=>208, 'height'=>208),BX_RESIZE_IMAGE_EXACT, true);
		$arFieldsCover["PICTURE"]['THUMB'] = $tmp['src'];
		
		$arFields['COVER'] = $arFieldsCover;
		
		$plusoImage = "http://{$_SERVER['HTTP_HOST']}" .$arFieldsCover["PICTURE"]["SRC"];//  $tmp['src'];
		$APPLICATION->AddViewContent("plusoru_meta_images", "<meta property=\"og:image\" content=\"{$plusoImage}\" /><link rel=\"image_src\" href=\"{$plusoImage}\" />");
		$APPLICATION->AddViewContent("plusoru_meta_image_width", '<meta property="og:image:width" content="'.$arFieldsCover["PICTURE"]['WIDTH'].'"/>');
		$APPLICATION->AddViewContent("plusoru_meta_image_height", '<meta property="og:image:height" content="'.$arFieldsCover["PICTURE"]['HEIGHT'].'"/>');
	}
	
//	$arResult['TITLE'] = GetMessage('DISCOUNT_FOR_FRIENDS')."{$arResult['fms']['NAME']}";
    $arResult['TITLE'] = \Fms\MallAwardsShareMessages::getMessageByFmsId($arResult['fms']['ID'], 'SHARED_PHOTO');
	$people = \Fms\Awards::getWordByNumber($arResult['fms']['PROPERTY_REQUIRED_PEOPLE_VALUE'], GetMessage('SHARE_PEOPLE1'), GetMessage('SHARE_PEOPLE2'), GetMessage('SHARE_PEOPLE3'));
	//$discount = ($arResult['fms']['PROPERTY_GOING_PEOPLE_VALUE'] < $arResult['fms']['PROPERTY_BONUS_PEOPLE_VALUE']) ? $arResult['fms']['PROPERTY_DISCOUNT_VALUE'] : $arResult['fms']['possible_discount'];
    $discount = $arResult['fms']['PROPERTY_DISCOUNT_VALUE'];
	$arResult['DESCRIPTION'] = "{$arResult['fms']['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['UF_TOWN']['NAME']}. {$arResult['fms']['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['NAME']}. {$arResult['fms']['PROPERTY_OPERATOR_VALUE']['NAME']}. ".GetMessage('SHARE_EVENT_WE_COLLECT')." {$arResult['fms']['PROPERTY_REQUIRED_PEOPLE_VALUE']} {$people} {$discount}%".GetMessage('SHARE_DISCOUNT_AFTER').'.';

	
	$APPLICATION->AddViewContent("plusoru_meta_title", "<meta property=\"og:title\" content=\"{$arResult['TITLE']}\" />");  
	$APPLICATION->AddViewContent("plusoru_meta_description", "<meta property=\"og:description\" content=\"{$arResult['DESCRIPTION']}\" />");
	$APPLICATION->AddViewContent("plusoru_meta_url", "<meta property=\"og:url\" content=\""."http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."\" />");
	$APPLICATION->AddViewContent("plusoru_meta_type", '<meta property="og:type" content="website"/>');
}

$this->IncludeComponentTemplate();
