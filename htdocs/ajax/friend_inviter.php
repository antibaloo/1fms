<?
require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');

global $fmsesGetter, $fmsEmailsSender;

$jsData = array();

do {
	if (!$USER->IsAuthorized()) {
		$jsData['errorMessage'] = GetMessage('FMS_UNAUTHORIZED_USER');
		break;
	}
	if (!isset($_REQUEST['fms_id'])) {
		$jsData['errorMessage'] = GetMessage('FMS_UNKNOWN_ERROR');
		break;
	}
	if (!isset($_REQUEST['email']) or trim($_REQUEST['email']) == '') {
		$jsData['errorMessage'] = GetMessage('FMS_INVITE_FRIEND_EMAIL_NOT_SPECIFIED');
		break;
	}
	$pageData = $fmsesGetter->getDataForDetailPageById($_REQUEST['fms_id']);
	if (!$pageData) {
		$jsData['errorMessage'] = GetMessage('FMS_UNKNOWN_ERROR');
		break;
	}
	$fmsData = $pageData['fms'];
	if ($fmsData['PROPERTY_STATE_code'] != 'active') {
		$jsData['errorMessage'] = GetMessage('FMS_UNKNOWN_ERROR');
		break;
	}
	//echo '<pre>'; print_r($fmsData); echo '</pre>'; exit();
	$fmsEmailsSender->sendTemplate(array(
		'to'            => $_REQUEST['email'],
		'template_name' => 'invite_friend',
		'language_code' => $fmsData['language_code'],
		'data'          => array(
			'sender_name'     => $USER->GetFullName(),
			'datetime'        => $fmsData['PROPERTY_START_DATETIME_VALUE'],
			'fms_name'        => $fmsData['NAME'],
			'discount'        => $fmsData['PROPERTY_DISCOUNT_VALUE'],
			'people_required' => $fmsData['PROPERTY_REQUIRED_PEOPLE_VALUE'],
			'mall_name'       => $pageData['location']['place']['mall_name'],
			'operator_name'   => $pageData['location']['place']['operator_name'],
			'fms_url'         => '/fms/'.$fmsData['ID'].'/?show_lang='.$fmsData['language_code'].'&user='.$USER->GetID(),
		),
	));
	$jsData['infoMessage'] = GetMessage(
		'FMS_INVITE_FRIEND_EMAIL_SENT',
		array(
			'#EMAIL#' => $_REQUEST['email'],
		)
	);
} while (false);

echo json_encode($jsData);
