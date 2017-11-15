<?
namespace Fms;

/**
 * Получение данных по FMS для разных страниц.
 *
 * На разных страницах - списки ФМС, детальный просмотр, страницы мастера и т. д. - отображаются почти одни и те же данные,
 * но с некоторыми вариациями. Также, на некоторых страницах кроме данных по самому ФМС нужны сопутствующие данные - об идущих,
 * о подписке.
 *
 * Этот класс является центром раздачи всех этих данных.
 */
class FmsesGetter {
	private $_params;
	private $_brandsDataHash;
	private $_mallsDataHash;
	private $_operatorsDataHash;

	/**
	 * Конструктор.
	 *
	 * $params:
	 * - iblock_id
	 * - cached_brands
	 * - cached_malls
	 * - cached_operators
	 * - cached_users
	 * - fms_to_user_manager
	 * - user_to_operator_subscriptions_manager
	 * - localization
	 */
	public function __construct ($params) {
		$this->_params = $params;
		$this->_brandsDataHash    = $params['cached_brands']->getDataHash();
		$this->_mallsDataHash     = $params['cached_malls']->getDataHash();
		$this->_operatorsDataHash = $params['cached_operators']->getDataHash();
	}



	/**
	 * Возвращает список с данными, без преобразований + низкоуровневые данные битрикса
	 *
	 * $params:
	 * - sort
	 * - filter
	 * - nav
	 *
	 * Возвращает:
	 * - list
	 * - CIBlockResult
	 *
	 * @param array $params
	 * @return array
	 */
	public function getDataWithRawList ($params) {
		$filterDefault = array(
			'IBLOCK_ID' => $this->_params['iblock_id'],
		);
		$filter = array_merge($filterDefault, $params['filter']);
		$navStartParams = isset($params['nav']) ? $params['nav'] : false;
		$r = \CIBlockElement::GetList(
			$params['sort'],
			$filter,
			false,
			$navStartParams,
			array(
				'IBLOCK_ID', 'ID', 'NAME', 'DETAIL_TEXT', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'IBLOCK_SECTION_ID',
				'PROPERTY_COUNTRY',
				'PROPERTY_CURRENCY',
				'PROPERTY_STATE',
				'PROPERTY_OPERATOR',
				'PROPERTY_START_DATETIME',
				'PROPERTY_TYPE',
				'PROPERTY_REQUIRED_PEOPLE',
				'PROPERTY_GOING_PEOPLE',
				'PROPERTY_DISCOUNT',
				'PROPERTY_BONUS_PEOPLE',
				'PROPERTY_BONUS_DISCOUNT',
				'PROPERTY_EXAMPLE_NAME',
				'PROPERTY_EXAMPLE_ORIGINAL_IMAGE',
				'PROPERTY_EXAMPLE_IMAGE',
				'PROPERTY_EXAMPLE_ORIGINAL_PRICE',
				'PROPERTY_EXAMPLE_DISCOUNT',
				'PROPERTY_MAX_PEOPLE',
				'PROPERTY_BRAND',
				'PROPERTY_EMBEDDED_VIDEO',
				'PROPERTY_RELATED_VIDEO',
				'PROPERTY_RELATED_VIDEO_ORIGINAL_IMAGE',
				'PROPERTY_RELATED_VIDEO_IMAGE',
				'PROPERTY_RELATED_VIDEO_STATE',
				'PROPERTY_PART_COND',
				'PROPERTY_PART_ACTION',
				'PROPERTY_SPECIAL_MISSION',
				'PROPERTY_IMAGES_CACHE',
				'PROPERTY_GOING_PEOPLE_CACHE',
				'PROPERTY_LATITUDE',
				'PROPERTY_LONGITUDE',
				'PROPERTY_ADDRESS'
			)
		);
		$list = array();
		while ($e = $r->GetNext()) {
			$e['PROPERTY_START_DATETIME_VALUE'] = date('d.m.Y H:i:s', strtotime($e['PROPERTY_START_DATETIME_VALUE']));
			
			$list[] = $e;
		}
		return array(
			'list'          => $list,
			'CIBlockResult' => $r,
		);
	}



	/**
	 * Возвращает список с данными, без преобразований
	 *
	 * $params:
	 * - sort
	 * - filter
	 * - nav
	 *
	 * @param array $params
	 * @return array
	 */
	public function getRawDataList ($params) {
		$data = $this->getDataWithRawList($params);
		return $data['list'];
	}



	/**
	 * Преобразует сырые данные из БД в данные, удобные для использования
	 *
	 *
	 *
	 *
	 * @param array $data
	 * @return array
	 */
	private function _transformData ($data) {
		global $USER, $CONFIG;
		$typeCode = $CONFIG['fmses']['type_id_to_code'][$data['PROPERTY_TYPE_ENUM_ID']];
		//Язык
		$data['language_code'] = LanguageByCountry::getCode($data['PROPERTY_COUNTRY_VALUE']);
		//Кэш картинок
		$data['PROPERTY_IMAGES_CACHE_VALUE'] = unserialize($data['~PROPERTY_IMAGES_CACHE_VALUE']);
		//Коды для списков
		$data['PROPERTY_STATE_code']      = $CONFIG['fmses']['state_id_to_code'][$data['PROPERTY_STATE_ENUM_ID']];
		$data['PROPERTY_TYPE_code']       = $CONFIG['fmses']['type_id_to_code'][$data['PROPERTY_TYPE_ENUM_ID']];
		$data['PROPERTY_PART_COND_code']  = $CONFIG['fmses']['part_cond_id_to_code'][$data['PROPERTY_PART_COND_ENUM_ID']];
		//Дата, время
		$data['start_date'] = $this->_params['localization']->formatDate($data['PROPERTY_START_DATETIME_VALUE']);
		$data['start_time'] = substr($data['PROPERTY_START_DATETIME_VALUE'], -8, 5);
		//
		$data['PROPERTY_GOING_PEOPLE_VALUE'] = (int)$data['PROPERTY_GOING_PEOPLE_VALUE'];
		//Описание, картинки
		if ($data['DETAIL_TEXT_TYPE'] == 'text') {
			$data['DETAIL_TEXT_TYPE'] = nl2br($data['DETAIL_TEXT_TYPE']);
		}
		$data['PREVIEW_PICTURE'] = $data['PROPERTY_IMAGES_CACHE_VALUE']['PREVIEW_PICTURE'];
		//Брэнд, оператор
		if (isset($this->_brandsDataHash[$data['PROPERTY_BRAND_VALUE']])) {
			$data['PROPERTY_BRAND_VALUE'] = $this->_brandsDataHash[$data['PROPERTY_BRAND_VALUE']];
		}
		$data['PROPERTY_OPERATOR_VALUE'] = $this->_operatorsDataHash[$data['PROPERTY_OPERATOR_VALUE']];
		//Продукт-пример
		$data['PROPERTY_EXAMPLE_ORIGINAL_IMAGE_VALUE'] = $data['PROPERTY_IMAGES_CACHE_VALUE']['PROPERTY_EXAMPLE_ORIGINAL_IMAGE'];
		$data['PROPERTY_EXAMPLE_IMAGE_VALUE']          = $data['PROPERTY_IMAGES_CACHE_VALUE']['PROPERTY_EXAMPLE_IMAGE'];
		if ($typeCode == 'sale') {
			$data['discounted_price'] = $data['PROPERTY_EXAMPLE_ORIGINAL_PRICE_VALUE'] - ($data['PROPERTY_EXAMPLE_ORIGINAL_PRICE_VALUE'] / 100 * $data['PROPERTY_EXAMPLE_DISCOUNT_VALUE']);
			$data['discounted_price'] = round($data['discounted_price'], 2);
		}
		//Премиум-видео
		$data['PROPERTY_RELATED_VIDEO_ORIGINAL_IMAGE_VALUE'] = $data['PROPERTY_IMAGES_CACHE_VALUE']['PROPERTY_RELATED_VIDEO_ORIGINAL_IMAGE'];
		$data['PROPERTY_RELATED_VIDEO_IMAGE_VALUE']          = $data['PROPERTY_IMAGES_CACHE_VALUE']['PROPERTY_RELATED_VIDEO_IMAGE'];
		$stateId = $data['PROPERTY_RELATED_VIDEO_STATE_ENUM_ID'];
		if (isset($CONFIG['fmses']['related_video_state_id_to_code'][$stateId])) {
			$data['PROPERTY_RELATED_VIDEO_STATE_code'] = $CONFIG['fmses']['related_video_state_id_to_code'][$stateId];
		} else {
			$data['PROPERTY_RELATED_VIDEO_STATE_code'] = $CONFIG['fmses']['related_video_state_id_to_code'][0];
		}
		//URLы для просмотра и встраивания обоих видео
		if (!empty($data['PROPERTY_EMBEDDED_VIDEO_VALUE'])) {
			$data['PROPERTY_EMBEDDED_VIDEO_watch'] = \Fms\YoutubeUtils::getWatchUrl($data['PROPERTY_EMBEDDED_VIDEO_VALUE']);
			$data['PROPERTY_EMBEDDED_VIDEO_embed'] = \Fms\YoutubeUtils::getEmbedUrl($data['PROPERTY_EMBEDDED_VIDEO_VALUE']);
		}
		if (!empty($data['PROPERTY_RELATED_VIDEO_VALUE'])) {
			$data['PROPERTY_RELATED_VIDEO_watch']  = \Fms\YoutubeUtils::getWatchUrl($data['PROPERTY_RELATED_VIDEO_VALUE']);
			$data['PROPERTY_RELATED_VIDEO_embed']  = \Fms\YoutubeUtils::getEmbedUrl($data['PROPERTY_RELATED_VIDEO_VALUE']);
		}
		//Возможная скидка
		if (ctype_digit($data['PROPERTY_DISCOUNT_VALUE'])) {
			$data['possible_discount'] = $data['PROPERTY_DISCOUNT_VALUE'] + $data['PROPERTY_BONUS_DISCOUNT_VALUE'];
		}
		elseif (preg_match('/\d+\s?-\s?\d+/', $data['PROPERTY_DISCOUNT_VALUE'])) {
			$rangeDiscountNumbersList = preg_split('/\s?-\s?/', $data['PROPERTY_DISCOUNT_VALUE']);
			$data['possible_discount'] =
				($rangeDiscountNumbersList[0] + $data['PROPERTY_BONUS_DISCOUNT_VALUE']).
				'-'.
				($rangeDiscountNumbersList[1] + $data['PROPERTY_BONUS_DISCOUNT_VALUE']);
		}
		//Является ли текущий пользователь создателем ФМСа? Оператором, вестимо?
		$data['is_current_user_creator'] = ($data['~PROPERTY_OPERATOR_VALUE'] == $USER->GetID());
		return $data;
	}

	private function _detectWhichFmsesCurrentUserGoingTo ($list) {
		global $USER;
		if (!$USER->IsAuthorized()) {
			return $list;
		}
		$fmsIdsList = array();
		foreach ($list as &$data) {
			$fmsIdsList[] = $data['ID'];
		}
		$linksDataList = $this->_params['fms_to_user_manager']->getDataList(array(
			'filter' => array(
				'PROPERTY_USER' => $USER->GetID(),
				'PROPERTY_FMS'  => $fmsIdsList,
			)
		));
		$fmsIdsCurrentUserGoingToHash = array();
		foreach ($linksDataList as $linkData) {
			$fmsIdsCurrentUserGoingToHash[$linkData['PROPERTY_FMS_VALUE']] = true;
		}
		foreach ($list as &$data) {
			$data['is_current_user_going'] = isset($fmsIdsCurrentUserGoingToHash[$data['ID']]);
		}
		unset($data);
		return $list;
	}

	private function _transformDataList ($list) {
		foreach ($list as &$data) {
			$data = $this->_transformData($data);
		}
		unset($data);
		$list = $this->_detectWhichFmsesCurrentUserGoingTo($list);
		return $list;
	}

	private function _appendParticipantsDataToEventTypeFmsesList ($list) {
		$eventTypeId       = $GLOBALS['CONFIG']['fmses']['type_code_to_id']['event'];
		$participantsLimit = $GLOBALS['CONFIG']['fmses']['participants']['limit_for_events_list'];
		foreach ($list as &$data) {
			if ($data['PROPERTY_TYPE_ENUM_ID'] == $eventTypeId) {
				$data['participants'] = $this->_getParticipantsData($data, 8);
			}
		}
		unset($data);
		return $list;
	}



	/**
	 * Возвращает список с данными, преобразованными в удобоваримый вид
	 *
	 * $params:
	 * - sort
	 * - filter
	 * - nav
	 *
	 * @param array $params
	 * return array
	 */
	public function getDataList ($params) {
		$list = $this->getRawDataList($params);
		$list = $this->_transformDataList($list);
		$list = $this->_appendParticipantsDataToEventTypeFmsesList($list);
		return $list;
	}



	public function getDataWithList ($params) {
		$data = $this->getDataWithRawList($params);
		$data['list'] = $this->_transformDataList($data['list']);
		$data['list'] = $this->_appendParticipantsDataToEventTypeFmsesList($data['list']);
		//echo '<pre>'; print_r($data['list']); echo '</pre>'; exit();
		return $data;
	}



	/**
	 * Данные для детальной страницы, с преобразованиями
	 *
	 * Почти те же данные, что и для предпросмотра мастера, только с участниками и подпиской.
	 *
	 * Возвращает:
	 * - fms
	 * - location
	 * - participants
	 * - subscription
	 *
	 * @param int $id
	 * @return array
	 */
	public function getDataForDetailPageById ($id) {
		$data = $this->getDataForMasterPreviewPageById($id);
		if ($data) {
			list($data['fms']) = $this->_detectWhichFmsesCurrentUserGoingTo(array($data['fms']));
			$data['participants'] = $this->_getParticipantsData($data['fms'], false);
			$data['link_count_users'] = $this->_getLinkCountUser($data['fms']);
			if ($data['fms']['PROPERTY_TYPE_code'] == 'event') {
				$data['fms']['participants'] = $data['participants'];
				$participantsLimit = $GLOBALS['CONFIG']['fmses']['participants']['limit_for_events_list'];
				if (count($data['fms']['participants']) > $participantsLimit) {
					$data['fms']['participants'] = array_slice($data['fms']['participants'], 0, 8/*$participantsLimit*/);
				}
			}
			$data['subscription'] = $this->_getSubscriptionData($data['fms']['~PROPERTY_OPERATOR_VALUE']);
		}
		return $data;
	}
	
	/**
	 * Данные для детальной страницы
	 *
	 * Список пользователей упорядоченный по кол-ву переходов по реферальным ссылкам
	 *
	 * Возвращает:
	 * - link_count_users
	 *
	 * @param int $id
	 * @return array
	 */
	public function _getLinkCountUser ($fms, $limit = 20) {
		$link_count_users = array();
		global $s5BitrixCodes;
		
		$arFilter = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('fmses_to_users'), "PROPERTY_FMS"=>$fms['ID']);
		$res = \CIBlockElement::GetList(Array('ID'=>'DESC'), $arFilter, false, false, array('ID','IBLOCK_ID', 'PROPERTY_USER'));
		while($arFields = $res->GetNext()) {
			$rsUser = \CUser::GetByID($arFields['PROPERTY_USER_VALUE']);

            if ($arUser = $rsUser->Fetch()){
                $userImages = new \Fms\UserImages($arUser);
                $arUser['images'] = array(
                    'small' => $userImages->getPhotoFileArray('small'),
                );
                $arUser['IMAGE'] = $userImages->getPhotoFileArray('fms_detail');
                $arUser['COUNT'] = 0;

                $link_count_users[$arUser['ID']] = $arUser;
            }
		}
		
		$arFilter = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('referal'), "PROPERTY_FMS"=>$fms['ID']);
		$res = \CIBlockElement::GetList(Array('PROPERTY_COUNT'=>'DESC'), $arFilter, false, false, array('ID','IBLOCK_ID', 'PROPERTY_USER', 'PROPERTY_FMS', 'PROPERTY_COUNT'));

		while($arFields = $res->GetNext()) {
			if (isset($link_count_users[$arFields['PROPERTY_USER_VALUE']])){
				$link_count_users[$arFields['PROPERTY_USER_VALUE']]['COUNT'] = $arFields['PROPERTY_COUNT_VALUE'];
			}
		} 
		
		usort($link_count_users, "userSort");

		
		return $link_count_users;
	}



	/**
	 * Данные для мастера в режиме создания - собственно, по самому FMS тут данных нет, только сопутствующие данные.
	 *
	 * Возвращает:
	 * - location
	 *
	 * @param int $operatorId
	 * @return array
	 */
	public function getDataForMasterCreatePageByOperatorId ($operatorId) {
		if (!isset($this->_operatorsDataHash[$operatorId])) {
			return false;
		}
		$data = array(
			'location' => $this->_getLocationData($operatorId),
		);
		return $data;
	}



	/**
	 * Данные для мастера в режиме редактирования, без преобразований.
	 *
	 * Возвращает:
	 * - fms
	 * - location
	 *
	 * @param int $id
	 * @return array
	 */
	public function getDataForMasterEditPageById ($id) {
		$dataList = $this->getRawDataList(array(
			'filter' => array('ID' => $id)
		));
		if (count($dataList) == 0) {
			return false;
		}
		$data = array(
			'fms' => $dataList[0],
		);
		$imagesCache = unserialize($data['fms']['~PROPERTY_IMAGES_CACHE_VALUE']);
		$data['fms']['DETAIL_PICTURE'] = $imagesCache['DETAIL_PICTURE'];
		$data['fms']['time_left']      = $this->_getTimeLeftString($data['fms']['PROPERTY_START_DATETIME_VALUE']);

		$data['location'] = $this->_getLocationData($data['fms']['PROPERTY_OPERATOR_VALUE']);

		return $data;
	}



	/**
	 * Данные для предпросмотра мастера, с преобразованиями
	 *
	 * Почти те же данные, что и для мастера в режиме редактирования, только с преобразованиями.
	 *
	 * Возвращает:
	 * - fms
	 * - location
	 * - пустой список участников для fms
	 * - subscription установленный в true
	 *
	 * @param int $id
	 * @return array
	 */
	public function getDataForMasterPreviewPageById ($id) {
		$data = $this->getDataForMasterEditPageById($id);
		if ($data) {
			$data['fms'] = $this->_transformData($data['fms']);
			$data['fms']['participants'] = array();
			$data['subscription'] = true;
		}
		return $data;
	}



	private function _getLocationData ($operatorId) {
		$operatorData = $this->_operatorsDataHash[$operatorId];
		$mallData     = $operatorData['UF_OP_MALL'];
		$userImages   = new \Fms\UserImages($operatorData['ID']);
		$data = array(
			'place' => array(
				'operator_original_image' => $userImages->getPhotoFileArray('original'),
				'operator_image'          => $userImages->getPhotoFileArray('fms_detail'),
				'mall_name'               => $mallData['NAME'],
				'mall_address'            => $mallData['WORK_STREET'],
				'operator_name'           => $operatorData['NAME'],
				'operator_location'       => $operatorData['UF_OP_IN_MALL_LOCSTR'],
			),
			'mall_map' => \CFile::GetFileArray($operatorData['UF_OP_IN_MALL_LOCIMG']),
			'geo_map' => array(
				'text' => $mallData['NAME'],
			),
		);
		list($data['geo_map']['lat'], $data['geo_map']['lon']) = explode(',', $mallData['UF_MALL_GEO_COORDS']);;
		return $data;
	}



	/*private function _getParticipantsData ($fmsId, $limit) {
		$linksDataList = $this->_params['fms_to_user_manager']->getDataListByFmsId($fmsId);
		$userIdsList = array();
		foreach ($linksDataList as $linkData) {
			$userIdsList[] = $linkData['PROPERTY_USER_VALUE'];
		}
		$return = $this->_params['cached_users']->getDataHash(array(
			'id' => $userIdsList,
		));
		return $return;
	}*/

	private function _getParticipantsData ($fmsData, $limit) {
		if (empty($fmsData['PROPERTY_GOING_PEOPLE_CACHE_VALUE'])) {
			return array();
		}
		$goingUserIdsList = explode(',', $fmsData['PROPERTY_GOING_PEOPLE_CACHE_VALUE']);
		if ($limit and count($goingUserIdsList) > $limit) {
			$goingUserIdsList = array_slice($goingUserIdsList, 0, $limit);
		}
		//echo '<pre>'; var_dump($fmsData); echo '</pre>'; exit();
		//echo '<pre>'; var_dump($fmsData['PROPERTY_GOING_PEOPLE_CACHE_VALUE'], $goingUserIdsList); echo '</pre>'; exit();
		$return = $this->_params['cached_users']->getDataHash(array(
			'id' => $goingUserIdsList,
		));
		
		global $fmsActiveMallId, $fmsActiveTownId;
		
		global $s5BitrixCodes;
		foreach ($return as &$user){
			$userImages = new \Fms\UserImages($user['ID']);
			$user['IMAGE'] = $userImages->getPhotoFileArray('fms_detail');
			$user['POINTS'] = $arResult['RATING'] = \Fms\Awards::getRating($user['ID'], date('n'), date('Y'), $fmsData['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['ID']);
			
		}
		
		return $return;
	}



	private function _getSubscriptionData ($operatorId) {
		global $USER;
		if ($USER->IsAuthorized()) {
			return $this->_params['user_to_operator_subscriptions_manager']->isExists($USER->GetID(), $operatorId);
		} else {
			return false;
		}
	}



	private function _getTimeLeftString ($startDatetime) {
		$nowTs = time();
		$startTs = strtotime($startDatetime);
		if ($startTs > $nowTs) {
			$diff = $startTs - $nowTs;
			$hoursLeft = floor($diff / 3600);
			$diff -= $hoursLeft*3600;
			$minutesLeft = floor($diff / 60);
			$secondsLeft = $diff - $minutesLeft*60;
			$return = sprintf('%02d',$hoursLeft).':'.sprintf('%02d',$minutesLeft).':'.sprintf('%02d',$secondsLeft);
		} else {
			$return = false;
		}
		return $return;
	}
}
