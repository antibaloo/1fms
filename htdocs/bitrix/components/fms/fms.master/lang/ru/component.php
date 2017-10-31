<?
$MESS['FMS_NOT_FOUND'] = 'FMS не найден';
$MESS['FMS_INVALID_STATE'] = 'FMS имеет недопустимое состояние';

//Для отображения ошибок на форме
$MESS['INVALID_FIELDS'] = 'Неверно заполненные поля';
\Fms\HtmlParts\LangFilesIncluder::getInstance()->includeLangFile('FmsBlock');
\Fms\HtmlParts\LangFilesIncluder::getInstance()->includeLangFile('FmsDetail');

$MESS['NAME']                   = GetMessage('FMS_NAME');
$MESS['IBLOCK_SECTION_ID']      = GetMessage('CATEGORY');
$MESS['ORIGINAL_IMAGE']         = 'Обложка - фото';
$MESS['EMBEDDED_VIDEO']         = 'Обложка - видео';
//$MESS['DISCOUNT']             = GetMessage('DISCOUNT');
$MESS['START_DATE']             = GetMessage('DATE');
$MESS['START_TIME']             = GetMessage('TIME');
$MESS['EXAMPLE_ORIGINAL_IMAGE'] = 'Картинка продукта-примера';
$MESS['EXAMPLE_NAME']           = GetMessage('EXAMPLE_PRODUCT_NAME');
$MESS['EXAMPLE_DISCOUNT']       = 'Скидка продукта-примера';
$MESS['EXAMPLE_ORIGINAL_PRICE'] = GetMessage('Цена продукта-примера без скидки');
$MESS['REQUIRED_PEOPLE']        = GetMessage('PEOPLE_TO_ACTIVATE');
$MESS['BONUS_PEOPLE']           = GetMessage('PEOPLE_TO_BONUS');
$MESS['BONUS_DISCOUNT']         = GetMessage('BONUS');
$MESS['MAX_PEOPLE']             = GetMessage('UNTIL_PEOPLE_END');
$MESS['DETAIL_TEXT']            = GetMessage('DESCRIPTION');
$MESS['PART_ACTION']            = GetMessage('PART_COND').' - '.strtolower(GetMessage('PART_COND_action'));
