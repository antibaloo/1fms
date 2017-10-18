<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

function unparse_url($parsed_url) { 
  $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
  $host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
  $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
  $user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
  $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
  $pass     = ($user || $pass) ? "$pass@" : ''; 
  $path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
  $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
  $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 
  return "$scheme$user$pass$host$port$path$query$fragment"; 
} 

global $CONFIG, $USER, $fmsesGetter, $fmsImGoingManager, $fmsDataForEmailTransformer, $fmsEmailsSender;

if (!$USER->IsAdmin()) {
	if (!$USER->IsAuthorized()) {
		if (isset($_REQUEST['back_url'])) {
 			$_SESSION['fms_back_url'] = $_REQUEST['back_url'];
 		}
	} else {
		if (isset($_SESSION['fms_back_url'])) {
			$params = array();
			$url_array = parse_url ( $_SESSION['fms_back_url']);
			parse_str ($url_array['query'], $params);
			
			if ($params['go']= 1 && $params['fms_id'] >0 && $params['user'] == 'none'){//Вызов авторизации по нажатию кнопки "Я иду"
				$goingData = $fmsImGoingManager->go($params['fms_id'], $USER->GetID());
				$pageData = $fmsesGetter->getDataForDetailPageById($_POST['fms_id']);
				$fmsData = $pageData['fms'];
				if ($goingData['is_new'] and $USER->GetEmail()) {
					$fmsEmailsSender->sendTemplate(array(
						'to'            => $USER->GetEmail(),
						'template_name' => 'im_going',
						'language_code' => $fmsData['language_code'],
						'is_add_ics'    => true,
						'data'          => array(
							'user_name'  => $USER->GetFullName(),
							'fms'        => $fmsDataForEmailTransformer->transform($pageData['fms'], new \Fms\Localization($fmsData['language_code'])),
						),
					));
				}
				unset($url_array['query']);
				$backUrl = unparse_url($url_array);
			}else{
				$backUrl = $_SESSION['fms_back_url'];
			}
			Bitrix\Main\Diag\Debug::writeToFile(array('session_back_url' => $_SESSION['fms_back_url'], 'modified_back_url'=>$backUrl ),"","/debug.txt");
			unset($_SESSION['fms_back_url']);
			LocalRedirect($backUrl);
		} else {
			LocalRedirect('/');
		}
	}
}



$arResult['error'] = false;
if (isset($_REQUEST['error']) and ctype_digit($_REQUEST['error'])) {
	if (isset($CONFIG['auth']['error_id_to_code'][$_REQUEST['error']])) {
		$arResult['error'] = GetMessage($CONFIG['auth']['error_id_to_code'][$_REQUEST['error']]);
	} else {
		$arResult['error'] = GetMessage('UNKNOWN_ERROR');
	}
} elseif (isset($_REQUEST['auth_service_error'])) {
	$arResult['error'] = GetMessage('AUTH_SERVICE_ERROR');
}
/*if (isset($_SESSION['fms_soc_net_auth_error'])) {
		$arResult['error'] = GetMessage($CONFIG['auth']['error_id_to_code'][$_SESSION['fms_soc_net_auth_error']]);
}
unset($_SESSION['fms_soc_net_auth_error']);*/



$this->IncludeComponentTemplate();