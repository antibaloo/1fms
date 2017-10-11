<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle(GetMessage('FMS_PAGE_TITLE_auth'));
?>

<?$APPLICATION->IncludeComponent('fms:auth', '', array())?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
