<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

global $CONFIG;

if (!$USER->IsAdmin()) {
	if (!$USER->IsAuthorized()) {
		if (isset($_REQUEST['back_url'])) {
			$_SESSION['fms_back_url'] = $_REQUEST['back_url'];
		}
	} else {
		if (isset($_SESSION['fms_back_url'])) {
			$backUrl = $_SESSION['fms_back_url'];
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
