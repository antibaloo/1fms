<?
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

$zfPath = realpath(__DIR__."/lib/vendor/ZendFramework-2.2.0/library/");
//var_dump($zfPath); exit();
set_include_path($zfPath.PATH_SEPARATOR.get_include_path());

require_once 'Zend/Loader/StandardAutoloader.php';
$loader = new \Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));
$loader->registerPrefix('S5', __DIR__.'/lib/vendor/S5');
$loader->registerNamespace('S5', __DIR__.'/lib/vendor/S5');
$loader->registerNamespace('Fms', __DIR__.'/classes/general/Fms');
$loader->registerNamespace('Instagram', __DIR__.'/lib/vendor/PHP-Instagram-API-master/Instagram/');
$loader->registerNamespace('\Instagram', __DIR__.'/lib/vendor/PHP-Instagram-API-master/Instagram/');
$loader->register();

CModule::IncludeModule('iblock');

require_once __DIR__.'/lib/vendor/PhpThumb/ThumbLib.inc.php';
require_once __DIR__.'/lib/vendor/Net_URL2-2.0.0/Net/URL2.php';



global
$CONFIG,
$s5BitrixCodes,
$fmsActiveTownId,
$fmsActiveMallId,
$currentLanguageCode,
$fmsLocalization,
$fmsBrandsManager,
$fmsUserToBrandManager,
$fmsTagsManager,
$fmsUserToTagManager,
$fmsCachedTowns,
$fmsCachedMalls,
$fmsCachedOperators,
$fmsCachedBrands,
$fmsCachedUsers,
$fmsesManager,
$fmsesGetter,
$fmsToUserManager,
$fmsUserToOperatorSubscriptionsManager,
$fmsUserToFmsSubscriptionsManager,
$fmsImGoingManager,
$fmsTicketsGetter,
$fmsActiveFmsesCounter,
$fmsCurrencyCodes,
$fmsImagesProcessor,
$fmsImagesCache,
$fmsCurrentUserTypeCode,
$fmsEmailsSender,
$fmsDataForEmailTransformer,
$fmsesSubscriptionSender,
$fmsAuthorization;

if (isset($_REQUEST['show_lang'])) {
	\Fms\LanguageSelector::setCurrentLanguageCode($_REQUEST['show_lang']);
	Fms\LanguageSelector::setBitrixLanguage(true);
	#$asd=\Fms\LanguageSelector::getCurrentLanguageCode();
	#echo $_REQUEST['show_lang'],'<br>';
	#die($asd);
}

$CONFIG = array(
	'users' => array(
		'type_id_to_code' => array(
			3 => 'user',
			4 => 'operator',
			5 => 'mall',
		),
		'generated_passwords' => array(
			'length' => 12,
		),
		'profiles' => array(
			'images' => array(
				'photo' => array(
					'profile' => array(
						'max_size' => 200,
					),
					'fms_detail' => array(
						'max_size' => 130,
					),
					'small' => array(
						'max_size' => 39,
					),
				),
				'logo' => array(
					'profile' => array(
						'max_size' => 200,
					),
					'small' => array(
						'max_size' => 131,
					),
				),
				'stubs' => array(
					'size' => 200,
				),
			),
			'users' => array(
				'images' => array(
					'photo' => array(
						'stub_url' => '/upload/fms/user_profile_photo_stub.png',
					),
				),
			),
			'operators' => array(
				'images' => array(
					'photo' => array(
						'stub_url' => '/upload/fms/operator_profile_photo_stub.png',
					),
					'logo' => array(
						'stub_url' => '/upload/fms/operator_profile_logo_stub.png',
					),
				),
			),
			'malls' => array(
				'images' => array(
					'photo' => array(
						'stub_url' => '/upload/fms/operator_profile_photo_stub.png',
					),
					'logo' => array(
						'stub_url' => '/upload/fms/operator_profile_logo_stub.png',
					),
				),
			),
		),
	),
	'fmses' => array(
		'type_id_to_code' => array(
			1 => 'sale',
			2 => 'event',
		),
		'state_id_to_code' => array(
			5 => 'draft',
			6 => 'active',
			7 => 'completed',
			8 => 'deleted',
		),
		'part_cond_id_to_code' => array(
			3 => 'ticket',
			4 => 'action',
		),
		'related_video_state_id_to_code' => array(
			0 => 'waiting',
			9 => 'approved',
		),
		'images' => array(
			'preview' => array(
				'width'  => 386,
				'height' => 261,
				'jpeg_quality' => 85,
			),
			'detail' => array(
				'width'  => 365,
				'height' => 420,
				'jpeg_quality' => 85,
			),
		),
		'example_product' => array(
			'image' => array(
				'width'  => 62,
				'height' => 62,
				'jpeg_quality' => 85,
			),
		),
		'related_video' => array(
			'image' => array(
				'width'  => 823,
				'height' => 479,
				'jpeg_quality' => 85,
			),
		),
		'participants' => array(
			'limit_for_events_list' => 5,
		),
		'temp_images_dir_path' => __DIR__.'/temp/fms_element_images/',
	),
	'user_to_fms_subscriptions' => array(
		'is_viewed_id_to_code' => array(
			0  => 'no',
			10 => 'yes',
		),
		'is_sent_id_to_code' => array(
			0  => 'no',
			18 => 'yes',
		),
	),
	'top_unauth_images_dir_url' => '/upload/fms/top_unauth_images',
	'html_parts' => array(
		'lang_dir_path' => __DIR__.'/html_parts_lang/',
	),
	'auth' => array(
		'error_id_to_code' => array(
			1 => 'FMS_USER_EMAIL_ALREADY_EXISTS',
		),
	),
	'languages' => array(
		'russian_countries' => array(
			7 => true,
		),
	),
	'components' => array(
		'fms.list' => array(
			'rpp' => 10,
		),
	),
);

$CONFIG['users']['type_code_to_id']                = array_flip($CONFIG['users']['type_id_to_code']);
$CONFIG['users']['profiles']['others']             = $CONFIG['users']['profiles']['users'];
$CONFIG['fmses']['type_code_to_id']                = array_flip($CONFIG['fmses']['type_id_to_code']);
$CONFIG['fmses']['state_code_to_id']               = array_flip($CONFIG['fmses']['state_id_to_code']);
$CONFIG['fmses']['part_cond_code_to_id']           = array_flip($CONFIG['fmses']['part_cond_id_to_code']);
$CONFIG['fmses']['related_video_state_code_to_id'] = array_flip($CONFIG['fmses']['related_video_state_id_to_code']);
$CONFIG['user_to_fms_subscriptions']['is_viewed_code_to_id'] = array_flip($CONFIG['user_to_fms_subscriptions']['is_viewed_id_to_code']);
$CONFIG['user_to_fms_subscriptions']['is_sent_code_to_id']   = array_flip($CONFIG['user_to_fms_subscriptions']['is_sent_id_to_code']);

$CONFIG['instagram'] = array(
    'client_id'         => 'c2509441c7db48e2b6d72ba0def8bd0d',
    'client_secret'     => '07713cd85fce4471b565cf78e9ec73a2',
    'redirect_uri'      => 'http://1fms.com/instagram/redirect.php',
    //'scope'             => array( 'likes', 'comments', 'relationships' )
);
$CONFIG['youtube'] = array(
    'client_id'         => '1001863874449.apps.googleusercontent.com',
    'client_secret'     => '4NZSlKZu7wuhbNiXC8e5AuFO',
    'redirect_uri'      => 'http://1fms.com/youtube/auth.php',
);

$s5BitrixCodes = new \S5_Bitrix_Codes(__DIR__.'/codes_data.php');
$fmsActiveTownId     = \Fms\TownAndMallSelector::getActiveTownId();
$fmsActiveMallId     = \Fms\TownAndMallSelector::getActiveMallId();
$cookieLanguageCode  = \Fms\LanguageSelector::getCookieLanguageCode();
if (!$fmsActiveTownId or !$cookieLanguageCode) {
	$geoSelector = new \Fms\TownAndLanguageGeoSelector(array(
		'towns_iblock_id'        => $s5BitrixCodes->getIblockId('towns'),
		'ru_countries_iblock_id' => $s5BitrixCodes->getIblockId('ru_countries'),
		'town_id'                => $fmsActiveTownId,
		'language_code'          => $cookieLanguageCode,
	));
	if (!$fmsActiveTownId) {
		\Fms\TownAndMallSelector::setActiveTownId($geoSelector->getTownId());
		$fmsActiveTownId = \Fms\TownAndMallSelector::getActiveTownId();
		$fmsActiveMallId = \Fms\TownAndMallSelector::getActiveMallId();
	}
	if (!$cookieLanguageCode) {
		\Fms\LanguageSelector::setCurrentLanguageCode($geoSelector->getLanguageCode());
		\Fms\LanguageSelector::setBitrixLanguage(true);
	}
}
$currentLanguageCode = \Fms\LanguageSelector::getCurrentLanguageCode();



$fmsLocalization = new \Fms\Localization($currentLanguageCode);
$fmsBrandsManager = new \Fms\SimpleItemsManager(array(
	'items_iblock_id' => $s5BitrixCodes->getIblockId('brands'),
));
$fmsUserToBrandManager = new \Fms\UserToSimpleItemManager(array(
	'items_iblock_id'        => $s5BitrixCodes->getIblockId('brands'),
	'user_to_item_iblock_id' => $s5BitrixCodes->getIblockId('user_to_brand'),
	'item_property_code'     => 'BRAND',
));
$fmsTagsManager = new \Fms\SimpleItemsManager(array(
	'items_iblock_id' => $s5BitrixCodes->getIblockId('tags'),
));
$fmsUserToTagManager = new \Fms\UserToSimpleItemManager(array(
	'items_iblock_id'        => $s5BitrixCodes->getIblockId('tags'),
	'user_to_item_iblock_id' => $s5BitrixCodes->getIblockId('user_to_tag'),
	'item_property_code'     => 'TAG',
));

$cacheTime = 3600;
$fmsCachedTowns = new \Fms\CachedLists\Towns(array(
	'iblock_id'     => $s5BitrixCodes->getIblockId('towns'),
	'cache_time'    => $cacheTime,
	'language_code' => $currentLanguageCode,
));
$fmsCachedMalls = new \Fms\CachedLists\Malls(array(
	'cache_time'    => $cacheTime,
	'language_code' => $currentLanguageCode,
	'cached_towns'  => $fmsCachedTowns,
));
$fmsCachedOperators = new \Fms\CachedLists\Operators(array(
	'cache_time'    => $cacheTime,
	'language_code' => $currentLanguageCode,
	'cached_malls'  => $fmsCachedMalls,
));
$fmsCachedBrands = new \Fms\CachedLists\Brands(array(
	'iblock_id'     => $s5BitrixCodes->getIblockId('brands'),
	'cache_time'    => $cacheTime,
));
$fmsCachedUsers = new \Fms\CachedLists\Users(array(
	'cache_time'    => $cacheTime,
	'cached_towns'  => $fmsCachedTowns,
));

$fmsImagesProcessor = new \Fms\FmsImagesProcessor(array(
	'temp_images_dir_path' => $CONFIG['fmses']['temp_images_dir_path'],
));
$fmsImagesCache = new \Fms\FmsImagesCache(array(
	'iblock_id' => $s5BitrixCodes->getIblockId('fmses'),
));

$fmsesManager = new \Fms\FmsesManager(array(
	'iblock_id'        => $s5BitrixCodes->getIblockId('fmses'),
	'fms_images_cache' => $fmsImagesCache,
));

$fmsActiveFmsesCounter = new \Fms\ActiveFmsesCounter(array(
	'fmses_iblock_id' => $s5BitrixCodes->getIblockId('fmses'),
	'towns_iblock_id' => $s5BitrixCodes->getIblockId('towns'),
));

$fmsToUserManager = new \Fms\FmsToUserManager(array(
	'iblock_id' => $s5BitrixCodes->getIblockId('fmses_to_users'),
));

$fmsUserToOperatorSubscriptionsManager = new \Fms\UserToOperatorSubscriptionsManager(array(
	'iblock_id' => $s5BitrixCodes->getIblockId('user_to_operator_subscriptions'),
));
$fmsUserToFmsSubscriptionsManager = new \Fms\UserToFmsSubscriptionsManager(array(
	'iblock_id' => $s5BitrixCodes->getIblockId('user_to_fms_subscriptions'),
));

$fmsesGetter = new \Fms\FmsesGetter(array(
	'iblock_id'           => $s5BitrixCodes->getIblockId('fmses'),
	'cached_brands'       => $fmsCachedBrands,
	'cached_malls'        => $fmsCachedMalls,
	'cached_operators'    => $fmsCachedOperators,
	'cached_users'        => $fmsCachedUsers,
	'fms_to_user_manager' => $fmsToUserManager,
	'user_to_operator_subscriptions_manager' => $fmsUserToOperatorSubscriptionsManager,
	'localization'        => $fmsLocalization,
));

$fmsImGoingManager = new \Fms\ImGoingManager(array(
	'fmses_iblock_id'      => $s5BitrixCodes->getIblockId('fmses'),
	'fmses_manager'        => $fmsesManager,
	'fms_to_user_manager'  => $fmsToUserManager,
	'active_fmses_counter' => $fmsActiveFmsesCounter,
	'user_to_operator_subscriptions_manager' => $fmsUserToOperatorSubscriptionsManager,
	'user_to_fms_subscriptions_manager'      => $fmsUserToFmsSubscriptionsManager,
));
$fmsTicketsGetter = new \Fms\TicketsGetter(array(
	'fmses_getter' => $fmsesGetter,
));

$fmsCurrencyCodes = new \Fms\CurrencyCodes(array(
	'iblock_id'      => $s5BitrixCodes->getIblockId('currencies'),
	'data_file_path' => __DIR__.'/currencies_data.php',
));

$fmsEmailsSender = new \Fms\Email\Sender(array(
	'from_email'           => 'info@1fms.com',
	'from_name'            => 'Flash Mob Shopping',
	'messages_initializer' => new \Fms\Email\Localization\MessagesInitializer(array('lang_dir_path'=>__DIR__.'/email_templates/lang/')),
	'templates_dir_path'   => __DIR__.'/email_templates/',
	'base_url'             => 'http://1fms.com',
));

$fmsDataForEmailTransformer = new \Fms\Email\FmsDataTransformer();

$fmsesSubscriptionSender = new \Fms\FmsesSubscriptionSender(array(
	'user_to_fms_subscriptions_manager' => $fmsUserToFmsSubscriptionsManager,
	'fmses_getter'                      => $fmsesGetter,
	'emails_sender'                     => $fmsEmailsSender,
	'localizations' => array(
		'ru' => new \Fms\Localization('ru'),
		'en' => new \Fms\Localization('en'),
	),
	'fms_data_transformer' => $fmsDataForEmailTransformer,
));

$fmsAuthorization = new \Fms\Authorization(array(
	'iblock_id' => $s5BitrixCodes->getIblockId('additional_authorization'),
	'duration'  => 86400*7,
));

$fmsCurrentUserTypeCode = false;



//Обработчики
AddEventHandler('main',   'OnBeforeProlog',              'onBeforePrologHandler');
AddEventHandler('main',   'OnAfterUserLogin',            'onAfterUserLoginHandler');
AddEventHandler('main',   'OnAfterUserLogout',           'onAfterUserLogoutHandler');
AddEventHandler('main',   'OnAfterUserAuthorize',        'onAfterUserAuthorizeHandler');
AddEventHandler('main',   'OnBeforeUserAdd',             'onBeforeUserAddHandler');
//AddEventHandler('iblock', 'OnBeforeIBlockElementAdd',    'onBeforeIBlockElementAddHandler');
//AddEventHandler('iblock', 'OnBeforeIBlockElementUpdate', 'onBeforeIBlockElementUpdateHandler');
AddEventHandler('search', 'BeforeIndex',                 'onBeforeIndexHandler');


AddEventHandler('main',   'OnAfterUserAdd',        'onAfterUserAddHandler');
function onAfterUserAddHandler(&$arFields){
    $_SESSION['fms_after_register_id'] = $arFields['ID'];
}


function onBeforePrologHandler () {
	//$USER инициализируется только здесь - нам он нужен
	global $CONFIG, $USER, $fmsCurrentUserTypeCode, $fmsCachedOperators, $fmsAuthorization;
	$fmsAuthorization->tryAuthorizeUser();
	$fmsCurrentUserTypeCode = \Fms\UserType::getCurrentUserTypeCode();
	//Обработка всяких action
	if (isset($_REQUEST['action'])) {
		if ($fmsCurrentUserTypeCode != 'operator') {
			if ($_REQUEST['action'] == 'set_town' and isset($_REQUEST['town_id']) and ctype_digit($_REQUEST['town_id'])) {
				\Fms\TownAndMallSelector::setActiveTownId($_REQUEST['town_id']);
				\Fms\TownAndMallSelector::unsetActiveMall();
				\Fms\Redirecter::redirect(false, array('action','town_id'));
			}
			elseif ($_REQUEST['action'] == 'set_mall' and isset($_REQUEST['mall_id']) and ctype_digit($_REQUEST['mall_id'])) {
				\Fms\TownAndMallSelector::setActiveMallId($_REQUEST['mall_id']);
				\Fms\Redirecter::redirect(false, array('action','mall_id'));
			}
		}
		if ($_REQUEST['action'] == 'set_lang' and isset($_REQUEST['lang'])) {
			\Fms\LanguageSelector::setCurrentLanguageCode($_REQUEST['lang']);
			\Fms\Redirecter::redirect(false, array('action','lang'));
		}
		
	}
	
	//Оператор не может выбирать город и молл. У него всегда свои выбраны.
	if (isset($GLOBALS['is_onAfterUserLoginHandler_fired'])) {
		if ($fmsCurrentUserTypeCode == 'operator') {
			$data = $fmsCachedOperators->getDataById($USER->GetID());
			\Fms\TownAndMallSelector::setActiveTownId($data['~UF_TOWN']);
			\Fms\TownAndMallSelector::setActiveMallId($data['~UF_OP_MALL']);
		}
	}
	//Защита профилей
	if (strpos($_SERVER['REQUEST_URI'], '/profile/') === 0) {
		\Fms\ProfileUtils::turnInvalidTypeUsersOut('user');
	} elseif (strpos($_SERVER['REQUEST_URI'], '/operator_profile/') === 0) {
		\Fms\ProfileUtils::turnInvalidTypeUsersOut('operator');
	} elseif (strpos($_SERVER['REQUEST_URI'], '/mall_profile/') === 0) {
		\Fms\ProfileUtils::turnInvalidTypeUsersOut('mall');
	}
}

function onAfterUserLoginHandler (&$fields) {
	$GLOBALS['is_onAfterUserLoginHandler_fired'] = true;
}

function onAfterUserLogoutHandler (&$params) {
	global $fmsAuthorization;
	if ($params['SUCCESS']) {
		\Fms\UserType::clearUserTypeCache();
		$fmsAuthorization->unsetCurrentAuthorization();
	}
}

function onAfterUserAuthorizeHandler (&$fields) {
	global $fmsAuthorization;
	//echo '<pre>'; var_dump($fields); echo '</pre>'; exit();
	global $USER;
	if ($fields['user_fields']['EXTERNAL_AUTH_ID'] == 'socservices') {
		$fmsAuthorization->setAuthorization($fields['user_fields']['ID']);
	}
}

function onBeforeUserAddHandler (&$fields) {
	//echo '<pre>'; debug_print_backtrace(); echo '</pre>';
	//echo '<pre>'; var_dump($fields); echo '</pre>'; exit();
	global $CONFIG, $APPLICATION;
	if (isset($fields['EMAIL'])) {
		$userData = \CUser::GetList(
			$by = 'ID',
			$order = 'ASC',
			array(
				'EMAIL' => $fields['EMAIL'],
			),
			array(
				'SELECT' => array('UF_TYPE'),
			)
		)->NavNext();
	} else {
		$userData = false;
	}
	//echo '<pre>'; print_r($userData); echo '</pre>'; exit();
	$isFromAuthPage       = (isset($fields['EXTERNAL_AUTH_ID']) and $fields['EXTERNAL_AUTH_ID'] == 'socservices');
	$isEmailAlreadyExists = false;
	$userTypeCode         = false;
	if (!empty($userData)) {
		$isEmailAlreadyExists = ($userData['EMAIL'] == $fields['EMAIL']);
		if (isset($CONFIG['users']['type_id_to_code'][$userData['UF_TYPE']])) {
			$userTypeCode = $CONFIG['users']['type_id_to_code'][$userData['UF_TYPE']];
		}
	}
	if (!$isFromAuthPage) {
		//Или в админке пользователь создаётся, или это молл оператора создаёт
		//В буге в админке включена проверка дублирования емейлов. Тогда пусть код будет закомментирован.
		/*if ($isEmailAlreadyExists) {
			$APPLICATION->ThrowException(GetMessage('FMS_USER_EMAIL_ALREADY_EXISTS'));
			return false;
		}*/
	} else {
		//Через соцсеть авторизуется
		if ($isEmailAlreadyExists) {
			if ($userTypeCode == 'user') {
				$user = new \CUser();
				$user->Authorize($userData['ID']);
				return false;
			} else {?>
<script type="text/javascript">
if (window.opener) window.opener.location = '/auth/?error=1';
window.close();
</script>
<?
				return false;
			}
		} else {
			$fields['UF_TYPE'] = $CONFIG['users']['type_code_to_id']['user'];
		}
	}
}

function onBeforeIndexHandler ($params) {
	if ($params['MODULE_ID'] == 'iblock' and $params['PARAM1'] == 'fmses') {
		return onBeforeFmsIndexHandler($params);
	}
}

function onBeforeFmsIndexHandler ($params) {
	global $CONFIG, $s5BitrixCodes;
	$fmsData = \CIBlockElement::GetList(
		array(),
		array(
			'IBLOCK_ID' => $s5BitrixCodes->getIblockId($params['PARAM2']),
			'ID'        => $params['ITEM_ID'],
		),
		false,
		false,
		array('IBLOCK_ID', 'ID', 'PROPERTY_MALL', 'PROPERTY_OPERATOR', )
	)->NavNext();
	$usersData = \Fms\Search\UsersData::getInstance();
	$mallSearchContent     = $usersData->getById($fmsData['PROPERTY_MALL_VALUE']);
	$operatorSearchContent = $usersData->getById($fmsData['PROPERTY_OPERATOR_VALUE']);
	if ($mallSearchContent) {
		$params['BODY'] .= (' '.$mallSearchContent);
	}
	if ($operatorSearchContent) {
		$params['BODY'] .= (' '.$operatorSearchContent);
	}
	$params['PARAMS'] = array(
		'mall_id'     => $fmsData['PROPERTY_MALL_VALUE'],
		'operator_id' => $fmsData['PROPERTY_OPERATOR_VALUE'],
	);
	//echo '<pre>'; print_r($params); echo '</pre>'; exit();
	return $params;
}

/*if ($_GET['user'] and $_GET['ELEMENT_ID']) 
	\Bitrix\Main\Diag\Debug::writeToFile(array('SERVER' => $_SERVER, 'user'=>$_GET['user'], 'fms' => $_GET['ELEMENT_ID'] ),"","/debug.txt");*/
# реферальная ссылка
if ($_GET['user'] and $_GET['ELEMENT_ID'] /*and $_SERVER['HTTP_REFERER']*/){
	
	$user_id = $_GET['user'];
	#global $USER;
	#if (($USER->IsAuthorized() and $USER->GetID() != $user_id) or !$USER->IsAuthorized()){
		$arFilter = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('referal'), "PROPERTY_USER"=>$user_id, 'PROPERTY_FMS'=>$_GET['ELEMENT_ID']);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), array('PROPERTY_COUNT', 'ID', 'IBLOCK_ID'));
		if($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			
			$arFilterIP = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('referal_ip'), "NAME"=>$_SERVER['REMOTE_ADDR'] /*$_SERVER['HTTP_X_FORWARDED_FOR']*/, 'PROPERTY_REFERAL'=>$arFields['ID']);
			$resIP = CIBlockElement::GetList(Array(), $arFilterIP, false, Array("nPageSize"=>1), array('ID', 'IBLOCK_ID','NAME'));
			if($obIP = $resIP->GetNextElement()) {
				$arFieldsIP = $obIP->GetFields();
			}else{
				CIBlockElement::SetPropertyValues($arFields['ID'], $s5BitrixCodes->getIblockId('referal'), intval($arFields['PROPERTY_COUNT_VALUE']) + 1, 'COUNT'); 
				$el = new CIBlockElement;

				$PROP = array();
				$PROP['REFERAL'] = $arFields['ID'];

				$arLoadProductArray = Array(
					"IBLOCK_ID"      => $s5BitrixCodes->getIblockId('referal_ip'),
					"PROPERTY_VALUES"=> $PROP,
					"NAME"           => $_SERVER['REMOTE_ADDR'],//$_SERVER['HTTP_X_FORWARDED_FOR']
					"ACTIVE"         => "Y", 
				);

				$id = $el->Add($arLoadProductArray);
			}
		}else{
			$el = new CIBlockElement;

			$PROP = array();
			$PROP['COUNT'] = 1;
			$PROP['USER'] = $user_id;
			$PROP['FMS'] = $_GET['ELEMENT_ID'];

			$arLoadProductArray = Array(
				"IBLOCK_ID"      => $s5BitrixCodes->getIblockId('referal'),
				"PROPERTY_VALUES"=> $PROP,
				"NAME"           => "item",
				"ACTIVE"         => "Y", 
			);

			$id = $el->Add($arLoadProductArray);
			
			$el = new CIBlockElement;

			$PROP = array();
			$PROP['REFERAL'] = $id;

			$arLoadProductArray = Array(
				"IBLOCK_ID"      => $s5BitrixCodes->getIblockId('referal_ip'),
				"PROPERTY_VALUES"=> $PROP,
				"NAME"           => $_SERVER['HTTP_X_FORWARDED_FOR'],
				"ACTIVE"         => "Y", 
			);

			$id = $el->Add($arLoadProductArray);
		}
	#}

}elseif($_GET['album'] /*and $_SERVER['HTTP_REFERER']*/ and $_GET['user']){
	$user_id = intval($_GET['user']);
	$arFilter = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('fmses'), "ID"=>$_GET['album']);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), array('PROPERTY_MALL', 'ID', 'IBLOCK_ID'));
	if($ob = $res->GetNext()) {
		$arFilterIP = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('referal2'), "PROPERTY_MALL"=>$ob['PROPERTY_MALL_VALUE'],'PROPERTY_USER'=>$user_id,
			'PROPERTY_MONTH'=>date('n'),'PROPERTY_YEAR'=>date('Y'),'PROPERTY_ALBUM'=>$_GET['album'],'PROPERTY_IP'=>$_SERVER['HTTP_X_FORWARDED_FOR']);
		$resIP = CIBlockElement::GetList(Array(), $arFilterIP, false, Array("nPageSize"=>1), array('ID', 'IBLOCK_ID'));
		if($obIP = $resIP->GetNext()) {

		}else{
			
			$el = new CIBlockElement;
			
			$PROP = array();
			$PROP['MALL'] = $ob['PROPERTY_MALL_VALUE'];
			$PROP['USER'] = $user_id;
			$PROP['MONTH'] = date('n');
			$PROP['YEAR'] = date('Y');
			$PROP['ALBUM'] = $_GET['album'];
			$PROP['IP'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
			
			$code = "album{$_GET['album']}";
			if ($_GET['photo']){
				$code .= "photo{$_GET['photo']}";
			}

			$arLoadProductArray = Array(
				"IBLOCK_ID"      => $s5BitrixCodes->getIblockId('referal2'),
				"PROPERTY_VALUES"=> $PROP,
				"NAME"           => "item",
				"CODE" => $code,
				"ACTIVE"         => "Y", 
				'ACTIVE_FROM'    => date('d.m.Y H:i:s')
			);

			$el->Add($arLoadProductArray);
		}
	} 
}