<?
$MESS['FMS_NOT_FOUND'] = 'FMS not found';
$MESS['FMS_INVALID_STATE'] = 'FMS has invalid state';

//Для отображения ошибок на форме
$MESS['INVALID_FIELDS'] = 'Fields with errors';
\Fms\HtmlParts\LangFilesIncluder::getInstance()->includeLangFile('FmsBlock');
\Fms\HtmlParts\LangFilesIncluder::getInstance()->includeLangFile('FmsDetail');

$MESS['NAME']                   = GetMessage('FMS_NAME');
$MESS['IBLOCK_SECTION_ID']      = GetMessage('CATEGORY');
$MESS['ORIGINAL_IMAGE']         = 'Cover photo';
$MESS['EMBEDDED_VIDEO']         = 'Cover video';
//$MESS['DISCOUNT']             = GetMessage('DISCOUNT');
$MESS['START_DATE']             = GetMessage('DATE');
$MESS['START_TIME']             = GetMessage('TIME');
$MESS['EXAMPLE_ORIGINAL_IMAGE'] = 'Example product image';
$MESS['EXAMPLE_NAME']           = GetMessage('EXAMPLE_PRODUCT_NAME');
$MESS['EXAMPLE_DISCOUNT']       = 'Example product discount';
$MESS['EXAMPLE_ORIGINAL_PRICE'] = GetMessage('Example product regular price');
$MESS['REQUIRED_PEOPLE']        = 'Number of people required to attend to get a discount';
$MESS['BONUS_PEOPLE']           = 'People '.GetMessage('PEOPLE_TO_BONUS');
$MESS['BONUS_DISCOUNT']         = 'Additional discount';
$MESS['MAX_PEOPLE']             = 'Total eligible';
$MESS['DETAIL_TEXT']            = GetMessage('DESCRIPTION');
$MESS['PART_ACTION']            = GetMessage('PART_COND').' - '.strtolower(GetMessage('PART_COND_action'));
