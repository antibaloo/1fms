<?
namespace Fms\HtmlParts;

class FmsButtonPluso {


    public static function getUserService(){
        global $USER;
        $login = (string) $USER->GetLogin();
        if (0 === strpos($login, 'OKuser')) {
            $service = 'odnoklassniki';
        }elseif (0 === strpos($login, 'FB_')){
            $service = 'facebook';
        }elseif (0 === strpos($login, 'G_')){
            $service = 'google';
        } else {
            $service = 'vkontakte';
        }
        return $service;
    }

    public static function showIamGoing($fmsData){
        global $USER;

        if (!$USER->IsAuthorized()){
            return '';
        }

        $fmsId = $fmsData['ID'];
        $fmsName= $fmsData['NAME'];
        $service = self::getUserService();

        $title = 'Я иду на флэшмоб ' . $fmsName;
        $image = \Fms\ShareImages::create()->getImageSrc($fmsId, $service);
        $description = '';
        $url = "http://{$_SERVER['HTTP_HOST']}/fms/$fmsId/?go=1&show_lang=".\Fms\LanguageSelector::getCookieLanguageCode() . '&user='.$USER->GetID();

        ?><div class="pluso" data-services="<?=$service?>" data-url="<?=$url?>" data-title="<?=$title?>" data-image="<?=$image?>" data-description="<?=$description?>"></div><?
    }
}
