<?
$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__.'/../../../');
require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');

$GLOBALS['s5BitrixCodes']->generate();
echo "Done\n";