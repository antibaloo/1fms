<?
require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');

if (isset($_REQUEST['id']) && intval($_REQUEST['id'] > 0)){
    $id = intval($_REQUEST['id']);

    Fms\ShareImages::create()->generateImages($id);

    $pics = Fms\ShareImages::create()->getImagesSrc($id);

    foreach ($pics as $key=>$val){
        echo "Картинка для $key: <br/><img src=\"$val\" /> <br /><br/>";
    }
}
