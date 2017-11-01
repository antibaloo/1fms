<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
global $CONFIG, $s5BitrixCodes, $fmsesManager, $fmsesGetter, $fmsToUserManager, $fmsCachedMalls, $fmsBrandsManager, $fmsCachedBrands, $fmsLocalization, $fmsActiveFmsesCounter, $fmsUserToFmsSubscriptionsManager, $fmsCurrencyCodes, $fmsImagesProcessor, $fmsImagesCache;
$fmsesIblockId = $s5BitrixCodes->getIblockId('fmses');

$languageCode = \Fms\LanguageSelector::getCurrentLanguageCode();

$action = 'show_form';
if ($_SERVER['REQUEST_METHOD'] == 'POST' and check_bitrix_sessid()) {
	$validPostActionsHash = array(
		'preview_fms' => true,
		'save_draft'  => true,
		'delete_fms'  => true,
	);
	if (isset($validPostActionsHash[$_POST['action']])) {
		$action = $_POST['action'];
	}
}

$accessChecker     = new \Fms\FmsMasterAccessChecker();
$accessCheckerData = $accessChecker->checkMaster();

if (!$accessCheckerData) {
	return;
}
$arResult['error'] = $accessCheckerData['error'];
$fmsId             = $accessCheckerData['fms_id'];
$fmsData           = $accessCheckerData['fms'];

if ($arResult['error']) {
	goto INCLUDE_TEMPLATE;
}

$fmsStateCode = $CONFIG['fmses']['state_id_to_code'][$fmsData['PROPERTY_STATE_ENUM_ID']];


if ($fmsStateCode == 'active') {
	if (!$fmsToUserManager->isAnyLinkExistsByFmsId($fmsId)) {
		$fmsesManager->draft($fmsId);
		$fmsActiveFmsesCounter->updateByFms($fmsId);
		$fmsUserToFmsSubscriptionsManager->deleteListByFmsId($fmsId);
	} else {
		$arResult['error'] = GetMessage('FMS_INVALID_STATE');
		goto INCLUDE_TEMPLATE;
	}
}

$arResult['form'] = array(
	'values' => array(),
	'errors' => array(),
);

//echo '<pre>'; var_dump($action); echo '</pre>'; exit();



//Всё, что с role = field, сохраним в поля.
//Что с role = property, сохраним в свойства.
//Остальные преобразуем и назначим вручную.
//role = visual просто для отображения на форме
$fieldsDataHash = array(
	'NAME' => array(
		'role' => 'field',
		'is_required' => true,
	),
	'IBLOCK_SECTION_ID' => array(
		'role' => 'field',
		'is_required' => true,
	),
	'TYPE' => array(
		'role' => 'property',
		'is_required' => true,
		'transformer' => function ($value) {
			return $GLOBALS['CONFIG']['fmses']['type_code_to_id'][$value];
		},
	),
	'COVER_TYPE' => array(
		'role' => 'visual',
	),
	'ORIGINAL_IMAGE' => array(
		'role' => 'property',
		'is_required_file' => true,
		'is_file' => true,
	),
	'EMBEDDED_VIDEO' => array(
		'role' => 'property',
		'validator' => function ($value) {
			if ($_POST['COVER_TYPE'] == 'image') {
				return true;
			}
			return ($value !== '');
		},
		'transformer' => function ($value) {
			if ($_POST['COVER_TYPE'] == 'image') {
				return '';
			} else {
				//return \Fms\YoutubeUtils::getEmbedUrl($value);
				return $value;
			}
		},
	),
	'BRAND' => array(
		//'for_fms_type' => 'sale',
		'role' => 'property',
		'transformer' => function ($value) {
			global $fmsBrandsManager;
			$fmsBrandsManager->addUniqueList(array($value));
			$brandId = $fmsBrandsManager->getIdByName($value);
			if ($brandId) {
				return $brandId;
			} else {
				return '';
			}
		},
	),
	'DISCOUNT' => array(
		'role' => 'property',
		'for_fms_type' => 'sale',
		'is_required' => true,
		'validator' => function ($value) {
			if (ctype_digit($value) and $value <= 100) {
				return true;
			}
			$matches = array();
			if (preg_match('/(\d+)-(\d+)/', $value, $matches)) {
				if ($matches[1] < $matches[2] and $matches2 <= 100) {
					return true;
				}
			}
			return false;
		},
	),
	'START_DATE' => array(
		'role' => 'visual',
		'is_required' => true,
		'validator' => function ($value) {
			return (bool)strtotime($value);
		},
	),
	'START_TIME' => array(
		'role' => 'visual',
		'is_required' => true,
		'validator' => function ($value) {
			return preg_match('/\d\d:\d\d/', $value);
		},
	),
	'EXAMPLE_ORIGINAL_IMAGE' => array(
		'role' => 'property',
		'for_fms_type' => 'sale',
		'is_file' => true,
		'is_required_file' => true,
	),
	'EXAMPLE_NAME' => array(
		'role' => 'property',
		'for_fms_type' => 'sale',
		'is_required' => true,
	),
	'EXAMPLE_DISCOUNT' => array(
		'role' => 'property',
		'for_fms_type' => 'sale',
		'is_required' => true,
		'validator' => function ($value) {
			return (ctype_digit($value) and $value <= 100);
		},
	),
	'EXAMPLE_ORIGINAL_PRICE' => array(
		'role' => 'property',
		'for_fms_type' => 'sale',
		'is_required' => true,
		'validator' => function ($value) {
			return preg_match('/\d+([,.]\d{1,2})?/', $value);
		},
	),
	'REQUIRED_PEOPLE' => array(
		'role' => 'property',
		'is_required' => true,
		'validator' => function ($value) {
			return ctype_digit($value);
		},
	),
	'BONUS_PEOPLE' => array(
		'role' => 'property',
		'for_fms_type' => 'sale',
	),
	'BONUS_DISCOUNT' => array(
		'role' => 'property',
		'for_fms_type' => 'sale',
	),
	'MAX_PEOPLE' => array(
		'role' => 'property',
		'validator' => function ($value) {
			if ($value === '') {
				return true;
			}
			if (!ctype_digit($value)) {
				return false;
			}
			if ($_POST['BONUS_PEOPLE'] > 0 and $value < $_POST['BONUS_PEOPLE']) {
				return false;
			}
			if ($value < $_POST['REQUIRED_PEOPLE']) {
				return false;
			}
			return true;
		},
	),
	'DETAIL_TEXT' => array(
		'role' => 'field',
		'is_required' => true,
	),
	'PART_COND' => array(
		'role' => 'property',
		'is_required' => true,
		'transformer' => function ($value) {
			return $GLOBALS['CONFIG']['fmses']['part_cond_code_to_id'][$value];
		},
	),
	'PART_ACTION' => array(
		'role' => 'property',
		'validator' => function ($value) {
			if ($_POST['PART_COND'] == 'ticket') {
				return true;
			} else {
				return $value !== '';
			}
		},
		'transformer' => function ($value) {
			return ($_POST['PART_COND'] == 'ticket') ? '' : $value;
		},
	),
	'SPECIAL_MISSION' => array(
		'role' => 'property',
	),
	'LATITUDE' => array(
		'role' => 'property',
	),
	'LONGITUDE' => array(
		'role' => 'property',
	),
		'ADDRESS' => array(
		'role' => 'property',
	),
);

$checkBonusPeople = function ($value) {
	return (ctype_digit($value) and $value > $_POST['REQUIRED_PEOPLE']);
};
$checkBonusDiscount = function ($value) {
	return (ctype_digit($value));
};



$operatorData = \CUser::GetList(
	$by='id',
	$order='asc',
	array('ID'=>$USER->GetID()),
	array('SELECT' => array('UF_COUNTRY', 'UF_TOWN', 'UF_OP_MALL', 'UF_OP_IN_MALL_LOCIMG'))
)->NavNext();

$countryData = \CIBlockElement::GetList(
	array(),
	array(
		'IBLOCK_ID' => $s5BitrixCodes->getIblockId('countries'),
		'ID' => $operatorData['UF_COUNTRY'],
	),
	false,
	false,
	array('PROPERTY_CURRENCY')
)->NavNext();

$currencyId   = $countryData['PROPERTY_CURRENCY_VALUE'];
$currencyCode = $fmsCurrencyCodes->getCode($currencyId);



if ($action == 'delete_fms') {
	//\CIBlockElement::Delete($fmsId);
	$fmsesManager->delete($fmsId);
	header('Location: /operator_profile/my_fms/');
	exit();
}
elseif ($action == 'preview_fms' or $action == 'save_draft') {
	//Чтобы дальше постоянно не долбиться с isset-ом и trim-ом
	foreach (array_keys($fieldsDataHash) as $fieldName) {
		if (isset($_POST[$fieldName])) {
			$_POST[$fieldName] = trim($_POST[$fieldName]);
		} else {
			$_POST[$fieldName] = '';
		}
	}
	//Проверка заполненности и валидности полей
	//Ручные проверки
	//Тип FMS
	if (isset($GLOBALS['CONFIG']['fmses']['type_code_to_id'][$_POST['TYPE']])) {
		$fmsTypeCode = $_POST['TYPE'];
	} else {
		goto SHOW;
	}
	if (!isset($GLOBALS['CONFIG']['fmses']['part_cond_code_to_id'][$_POST['PART_COND']])) {
		goto SHOW;
	}
	//Бонусная скидка
	if ($fmsTypeCode == 'sale') {
		if ($_POST['BONUS_PEOPLE'] !== '' and $_POST['BONUS_DISCOUNT'] === '') {
			$arResult['form']['errors']['BONUS_DISCOUNT'] = true;
			if (!$checkBonusPeople($_POST['BONUS_PEOPLE'])) {
				$arResult['form']['errors']['BONUS_PEOPLE'] = true;
			}
		}
		elseif ($_POST['BONUS_DISCOUNT'] !== '' and $_POST['BONUS_PEOPLE'] === '') {
			$arResult['form']['errors']['BONUS_PEOPLE'] = true;
			if (!$checkBonusDiscount($_POST['BONUS_DISCOUNT'])) {
				$arResult['form']['errors']['BONUS_DISCOUNT'] = true;
			}
		}
		elseif ($_POST['BONUS_PEOPLE'] !== '' and $_POST['BONUS_DISCOUNT'] !== '') {
			if (!$checkBonusPeople($_POST['BONUS_PEOPLE'])) {
				$arResult['form']['errors']['BONUS_PEOPLE'] = true;
			}
			if (!$checkBonusDiscount($_POST['BONUS_DISCOUNT'])) {
				$arResult['form']['errors']['BONUS_DISCOUNT'] = true;
			}
		}
	}
	//Картинка продукта-примера.
	if ($fmsTypeCode == 'sale' and !$fmsData['PROPERTY_EXAMPLE_ORIGINAL_IMAGE_VALUE'] and @$_FILES['EXAMPLE_ORIGINAL_IMAGE']['error'] !== UPLOAD_ERR_OK) {
		$arResult['form']['errors']['EXAMPLE_ORIGINAL_IMAGE'] = true;
	}
	//Автоматические проверки
	foreach ($fieldsDataHash as $fieldName => $fieldData) {
		if (isset($fieldData['for_fms_type']) and $fieldData['for_fms_type'] != $fmsTypeCode) {
			continue;
		}
		if (isset($fieldData['is_required']) and (!isset($_POST[$fieldName]) or $_POST[$fieldName] === '')) {
			$arResult['form']['errors'][$fieldName] = true;
			continue;
		}
		if (
			(!$fmsId and isset($fieldData['is_required_file'])) and
			(@$_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK)
		) {
			$arResult['form']['errors'][$fieldName] = true;
			continue;
		}
		if (isset($fieldData['validator'])) {
			$validator = $fieldData['validator'];
			$validationResult = $validator($_POST[$fieldName]);
			if (!$validationResult) {
				$arResult['form']['errors'][$fieldName] = true;
				continue;
			}
		}
	}
		
	//echo '<pre>'; print_r($_POST); echo '</pre>';
	//echo '<pre>'; print_r($arResult['form']['errors']); echo '</pre>'; exit();
	if (count($arResult['form']['errors']) > 0) {
		goto SHOW;
	}
	//Создаём FMS
	//Собираем данные для создания ФМС
	$data = array(
		'IBLOCK_ID'        => $fmsesIblockId,
		'DETAIL_TEXT_TYPE' => 'html',
		'ACTIVE_FROM'      => date('d.m.Y H:i:s'),
		'PROPERTY_VALUES' => array(
			'STATE'     => $CONFIG['fmses']['state_code_to_id']['draft'],
			'COUNTRY'   => $operatorData['UF_COUNTRY'],
			'TOWN'      => $operatorData['UF_TOWN'],
			'MALL'      => $operatorData['UF_OP_MALL'],
			'OPERATOR'  => $USER->GetID(),
		),
	);
	//Я в какой стране? - какая у меня валюта?
	if ($fmsTypeCode == 'sale') {
		$data['PROPERTY_VALUES']['CURRENCY'] = $currencyId;
	} else {
		$data['PROPERTY_VALUES']['CURRENCY'] = null;
	}
	//Автоматический сбор данных из введённых полей
	foreach ($fieldsDataHash as $fieldName => $fieldData) {
		$fieldValue = '';
		if (isset($fieldData['for_fms_type']) and $fieldData['for_fms_type'] != $fmsTypeCode) {
			//Если поле не нужно для данного типа FMS, то оно должно быть очищено, имеющееся в БД значение должно быть удалено
			if (!isset($fieldData['is_file'])) {
				$fieldValue = '';
			} else {
				//Да-да, чтобы удалить файл через CIBlockElement::Update(), почему-то надо формировать именно такой говномассив.
				//CIBlockElement::SetPropertyValuesEx() подобной фигнёй не страдает.
				//Как видно, этот код удаляет картинки только из свойств. Из полей не удаляет - пока этого не требуется.
				if ($fmsData['PROPERTY_'.$fieldName.'_VALUE']) {
					$fieldValue = array(
						$fmsData['PROPERTY_'.$fieldName.'_VALUE_ID'] => array('del'=>'Y'),
					);
				}
			}
		} else {
			//Если поле нужно для данного типа FMS, то значение должно быть назначено
			if (!isset($fieldData['transformer'])) {
				if (isset($fieldData['is_file'])) {
					if (@$_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
						$fieldValue = $_FILES[$fieldName];
					} else {
						continue;
					}
				} else {
					$fieldValue = $_POST[$fieldName];
				}
			} else {
				$transformer = $fieldData['transformer'];
				$fieldValue = $transformer($_POST[$fieldName]);
			}
		}
		if ($fieldData['role'] == 'field') {
			$data[$fieldName] = $fieldValue;
		} elseif ($fieldData['role'] == 'property') {
			$data['PROPERTY_VALUES'][$fieldName] = $fieldValue;
		}
	}
	//Дата и время начала
	$startDatetimeString = $_POST['START_DATE'].' '.$_POST['START_TIME'];
	$startDatetimeString = date('d.m.Y H:i:s', strtotime($startDatetimeString));
	$data['PROPERTY_VALUES']['START_DATETIME'] = $startDatetimeString;
	//Я в каком городе? - какое у меня смещение времени?
	//Найдём дату-время в стандарте UTC.
	$townData = \CIBlockElement::GetList(
		array(),
		array(
			'IBLOCK_ID' => $s5BitrixCodes->getIblockId('towns'),
			'ID'        => $operatorData['UF_TOWN'],
		),
		false,
		false,
		array('IBLOCK_ID', 'ID', 'PROPERTY_UTC_OFFSET', )
	)->NavNext();
	if (isset($townData['PROPERTY_UTC_OFFSET_VALUE']) and preg_match('/^[+\-]\d\d\d\d$/', $townData['PROPERTY_UTC_OFFSET_VALUE'])) {
		$utcOffset = $townData['PROPERTY_UTC_OFFSET_VALUE'];
	} else {
		$utcOffset = '+0000';
	}
	$data['PROPERTY_VALUES']['START_UTC_DATETIME'] = \Fms\DateTimeUtils::addUtcOffsetStringToDateTimeString(
		$data['PROPERTY_VALUES']['START_DATETIME'],
		$utcOffset,
		'd.m.Y H:i:s'
	);
	//Обработка обложки
	if (isset($data['PROPERTY_VALUES']['ORIGINAL_IMAGE'])) {
		$data['PREVIEW_PICTURE'] = $fmsImagesProcessor->resize($data['PROPERTY_VALUES']['ORIGINAL_IMAGE'], $CONFIG['fmses']['images']['preview']);
		$data['DETAIL_PICTURE']  = $fmsImagesProcessor->resize($data['PROPERTY_VALUES']['ORIGINAL_IMAGE'], $CONFIG['fmses']['images']['detail']);
	}
	//Обработка картинки для продукта-примера
	if (isset($data['PROPERTY_VALUES']['EXAMPLE_ORIGINAL_IMAGE'])) {
		//Если длина массива - 1, то, вестимо, там тот дурацкий массив для удаления, состоящий из одного элемента, внутри которого del => Y
		//Если больше 1, то там набор полей файла для добавления картинки
		if (count($data['PROPERTY_VALUES']['EXAMPLE_ORIGINAL_IMAGE']) > 1) {
			$data['PROPERTY_VALUES']['EXAMPLE_IMAGE'] = $fmsImagesProcessor->resize($data['PROPERTY_VALUES']['EXAMPLE_ORIGINAL_IMAGE'], $CONFIG['fmses']['example_product']['image']);
		} else {
			$data['PROPERTY_VALUES']['EXAMPLE_IMAGE'] = array('del'=>'Y');
		}
	}
	//Данные собрали, можно создавать/редактировать FMS
	//echo '<pre>'; print_r($data); echo '</pre>'; exit();
	$element = new \CIBlockElement();
	if (!$fmsId) {
		$fmsId = $element->Add($data);
	} else {
		//Отдельный SetPropertyValuesEx() вместо передавания свойств в Update() тут затем,
		//что в редактировании используются не все свойства - и вызов Update() их обнулит.
		$propertiesData = $data['PROPERTY_VALUES'];
		unset($data['PROPERTY_VALUES']);
		$r = $element->Update($fmsId, $data);
		$element->SetPropertyValuesEx(
			$fmsId,
			$fmsesIblockId,
			$propertiesData
		);
		//echo '<pre>'; var_dump($r, $element->LAST_ERROR); echo '</pre>'; exit();
	}
	$fmsImagesCache->generateForFms($fmsId);
	//echo '<pre>'; var_dump($r, $element->LAST_ERROR); echo '</pre>'; exit();
	if ($action == 'preview_fms') {
		LocalRedirect('/operator_profile/fms_master/preview/?id='.$fmsId);
	}
	elseif ($action == 'save_draft') {
		LocalRedirect('?id='.$fmsId.'&saved=1');
	}
}



SHOW:

if (!$fmsId) {
	$pageData = $fmsesGetter->getDataForMasterCreatePageByOperatorId($USER->GetID());
} else {
	$pageData = $fmsesGetter->getDataForMasterEditPageById($fmsId);
}


$arResult['location'] = $pageData['location'];
$fmsData = $pageData['fms'];
//echo '<pre>'; print_r($fmsData); echo '</pre>'; exit();

if ($action == 'show_form') {
	if (!$fmsId) {
		foreach (array_keys($fieldsDataHash) as $fieldName) {
			$arResult['form']['values'][$fieldName] = '';
		}
	} else {
		//Автоназначение
		foreach ($fieldsDataHash as $fieldName => $fieldData) {
			if ($fieldData['role'] == 'field') {
				$arResult['form']['values'][$fieldName] = $fmsData[$fieldName];
			} elseif ($fieldData['role'] == 'property') {
				$arResult['form']['values'][$fieldName] = $fmsData['PROPERTY_'.$fieldName.'_VALUE'];
			}
		}
		//Вычисляемые назначения
		$arResult['form']['values']['BRAND'] = '';
		if ($fmsData['PROPERTY_BRAND_VALUE']) {
			$brandData = $fmsCachedBrands->getDataById($fmsData['PROPERTY_BRAND_VALUE']);
			if ($brandData) {
				$arResult['form']['values']['BRAND'] = $brandData['NAME'];
			}
		}
		$arResult['form']['values']['TYPE']       = $CONFIG['fmses']['type_id_to_code'][$fmsData['PROPERTY_TYPE_ENUM_ID']];
		$arResult['form']['values']['START_DATE'] = $fmsLocalization->formatDateForDatepicker($fmsData['PROPERTY_START_DATETIME_VALUE']);
		$arResult['form']['values']['START_TIME'] = date('H:i', strtotime($fmsData['PROPERTY_START_DATETIME_VALUE']));
		$arResult['form']['values']['PART_COND']  = $CONFIG['fmses']['part_cond_id_to_code'][$fmsData['PROPERTY_PART_COND_ENUM_ID']];
		$arResult['form']['values']['COVER_TYPE'] = ($fmsData['PROPERTY_EMBEDDED_VIDEO_VALUE'] == '' ? 'image' : 'video');
		//echo '<pre>'; print_r($arResult['form']['values']); echo '</pre>'; exit();
	}
}
elseif ($action == 'preview_fms' or $action == 'save_draft') {
	//Если с таким action мы оказались здесь - значит сохранение формы не прошло, были ошибки
	foreach ($fieldsDataHash as $fieldName => $fieldData) {
		if (isset($_POST[$fieldName])) {
			$arResult['form']['values'][$fieldName] = htmlspecialchars($_POST[$fieldName]);
		} else {
			$arResult['form']['values'][$fieldName] = '';
		}
	}
	//Сортировка хэша ошибок. Ключи ошибок должны быть в том же порядке, что и поля на форме.
	if (count($arResult['form']['errors']) > 0) {
		$formErrorsHash = $arResult['form']['errors'];
		$arResult['form']['errors'] = array();
		foreach (array_keys($fieldsDataHash) as $fieldName) {
			if (isset($formErrorsHash[$fieldName])) {
				$arResult['form']['errors'][$fieldName] = true;
			}
		}
	}
}



//Данные для мастера
//Секции
$arResult['id'] = ($fmsId ? $fmsId : '');
$r = \CIBlockSection::GetList(
	array(),
	array('IBLOCK_ID' => $fmsesIblockId),
	false,
	array('ID', 'NAME', 'UF_EN_NAME')
);
$arResult['sections'] = array();
while ($e = $r->GetNext()) {
	if ($languageCode == 'en') {
		$e['NAME'] = $e['UF_EN_NAME'];
	}
	$arResult['sections'][] = $e;
}

//Брэнды
$arResult['brand_names_list'] = array();
foreach ($fmsBrandsManager->getDataList() as $brandData) {
	$arResult['brand_names_list'][] = $brandData['NAME'];
}

//Форматирование цены
$arResult['price_format'] = $fmsLocalization->getPriceFormatData($currencyCode);

$arResult['form']['saved'] = (isset($_REQUEST['saved']) and $_REQUEST['saved']);
INCLUDE_TEMPLATE:
$this->IncludeComponentTemplate();
?>