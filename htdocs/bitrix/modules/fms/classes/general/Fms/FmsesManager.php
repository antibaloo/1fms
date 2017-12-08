<?
namespace Fms;

class FmsesManager {
	private $_params;

	/**
	 * Конструктор.
	 *
	 * $params:
	 * - iblock_id
	 * - fms_images_cache
	 * - event_handlers
	 *
	 * event_handlers:
	 * - on_after_fms_activeness_change
	 */
	public function __construct ($params) {
		$this->_params = $params;
	}



	public function delete ($id) {
		$this->_setState($id, 'deleted');
		return true;
	}

	public function complete ($id) {
		$this->_setState($id, 'completed');
		return true;
	}

	public function draft ($id) {
		$this->_setState($id, 'draft');
		return true;
	}



	public function completeTimedOuts () {
		global $CONFIG;
		$nowString = date('Y-m-d H:i:s', time()-3600);
		$r = \CIBlockElement::GetList(
			array('ID' => 'ASC'),
			array(
				'IBLOCK_ID'                     => $this->_params['iblock_id'],
				'<=PROPERTY_START_UTC_DATETIME' => $nowString,
				'PROPERTY_STATE'                => $CONFIG['fmses']['state_code_to_id']['active'],
			),
			false,
			false,
			array('IBLOCK_ID', 'ID', 'PROPERTY_START_UTC_DATETIME')
		);
		$idsList = array();
		$debug = array();
		while ($e = $r->GetNext()) {
			$debug[] = $e;
			$idsList[] = $e['ID'];
			$this->complete($e['ID']);
		}
		return $idsList;
	}


	/**
	 * завершать fms через 2 часа после начала
	 * @return array
	 */
	public function completeTimedOuts2 () {
		global $CONFIG;
		$nowString = date('Y-m-d H:i:s', time() - 3 * 3600);
		$r = \CIBlockElement::GetList(
			array('ID' => 'ASC'),
			array(
				'IBLOCK_ID'                     => $this->_params['iblock_id'],
				'<=PROPERTY_START_UTC_DATETIME' => $nowString,
				//'<=PROPERTY_START_DATETIME' => $nowString,
				'PROPERTY_STATE'                => $CONFIG['fmses']['state_code_to_id']['active'],
			),
			false,
			false,
			array('IBLOCK_ID', 'ID', 'PROPERTY_START_DATETIME')
		);
		$idsList = array();
		$debug = array();
		while ($e = $r->GetNext()) {
			$debug[] = $e;
			$idsList[] = $e['ID'];
			$this->complete($e['ID']);
		}
		return $idsList;
	}

	/**
	 * Создаёт новый ФМС на основе существующего.
	 *
	 * @param int $id
	 * @return int
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function copy ($id) {
		if (!ctype_digit((string)$id)) {
			throw new \InvalidArgumentException("\$id is not an ID");
		}
		global $CONFIG;
		static $fieldsToCopyList = array(
			'IBLOCK_ID',
			'IBLOCK_SECTION_ID',
			'NAME',
			'PREVIEW_TEXT',
			'PREVIEW_TEXT_TYPE',
			'DETAIL_TEXT',
			'DETAIL_TEXT_TYPE',
		);
		static $rawPropsToCopyList = array(
			'CURRENCY',
			'COUNTRY',
			'TOWN',
			'MALL',
			'OPERATOR',
			'REQUIRED_PEOPLE',
			'DISCOUNT',
			'BONUS_PEOPLE',
			'BONUS_DISCOUNT',
			'MAX_PEOPLE',
			'EXAMPLE_NAME',
			'EXAMPLE_ORIGINAL_PRICE',
			'EXAMPLE_DISCOUNT',
			'BRAND',
			'EMBEDDED_VIDEO',
			'RELATED_VIDEO',
			'PART_ACTION',
			'SPECIAL_MISSION',
		);
		static $imagePropsToCopyList = array(
			'ORIGINAL_IMAGE',
			'EXAMPLE_ORIGINAL_IMAGE',
			'EXAMPLE_IMAGE',
			'RELATED_VIDEO_ORIGINAL_IMAGE',
			'RELATED_VIDEO_IMAGE',
		);
		static $listPropsToCopyList = array(
			'TYPE',
			'PART_COND',
			'RELATED_VIDEO_STATE',
		);
		$o = \CIBlockElement::GetByID($id)->GetNextElement();
		if (empty($o)) {
			throw new \Exception("FMS not found");
		}
		$fieldsData = $o->GetFields();
		$propsData  = $o->GetProperties();
		//echo '<pre>'; print_r($propsData); echo '</pre>'; exit();
		$newFmsData = array();
		foreach ($fieldsToCopyList as $fieldName) {
			$newFmsData[$fieldName] = $fieldsData['~'.$fieldName];
		}
		foreach ($rawPropsToCopyList as $propName) {
			$newFmsData['PROPERTY_VALUES'][$propName] = $propsData[$propName]['~VALUE'];
		}
		foreach ($imagePropsToCopyList as $propName) {
			if ($propsData[$propName]['~VALUE']) {
				$newFmsData['PROPERTY_VALUES'][$propName] = $this->_getUploadFileArray($propsData[$propName]['~VALUE']);
			}
		}
		foreach ($listPropsToCopyList as $propName) {
			$newFmsData['PROPERTY_VALUES'][$propName] = $propsData[$propName]['VALUE_ENUM_ID'];
		}
		$newFmsData['ACTIVE_FROM'] = date('d.m.Y H:i:s');
		$newFmsData['PROPERTY_VALUES']['STATE'] = $CONFIG['fmses']['state_code_to_id']['draft'];
		$newFmsData['PREVIEW_PICTURE'] = $this->_getUploadFileArray($fieldsData['PREVIEW_PICTURE']);
		$newFmsData['DETAIL_PICTURE']  = $this->_getUploadFileArray($fieldsData['DETAIL_PICTURE']);
		//echo '<pre>'; print_r($newFmsData); echo '</pre>'; exit();
		$element = new \CIBlockElement();
		$r = $element->Add($newFmsData);
		//echo '<pre>'; print_r($r); echo '</pre>'; exit();
		if (empty($r)) {
			throw new \Exception("Cannot copy FMS");
		}
		$this->_params['fms_images_cache']->generateForFms($r);
		return $r;
	}

	private function _getUploadFileArray ($fileId) {
		$fileData = \CFile::MakeFileArray($fileId);
		$fileData['error'] = UPLOAD_ERR_OK;
		return $fileData;
	}



	/**
	 * Устанавливает значение свойства с кэшем идущих пользователей.
	 *
	 * @param int $fmsId
	 * @param array $userIdsList ID идущих пользователей, первые идущие должны быть в конце списка, последние - в начале
	 */
	public function setGoingPeopleCache ($fmsId, array $userIdsList) {
		\CIBlockElement::SetPropertyValuesEx(
			$fmsId,
			$this->_params['iblock_id'],
			array('GOING_PEOPLE_CACHE' => join(',', $userIdsList))
		);
	}



	private function _setState ($id, $stateCode) {
		global $CONFIG;
		\CIBlockElement::SetPropertyValuesEx(
			$id,
			$this->_params['iblock_id'],
			array(
				'STATE' => $CONFIG['fmses']['state_code_to_id'][$stateCode],
			)
		);
		//$this->_fireOnAfterFmsStateSetHandlers($id, $stateCode);
	}



	/*private function _fireOnAfterFmsActivenessChangeHandlers ($id, $stateCode) {
		if (is_array($this->_params['event_handlers']['on_after_fms_activeness_change'])) {
			$eventData = array(
				'id'         => $id,
				'state_code' => $stateCode,
			);
			foreach ($this->_params['event_handlers']['on_after_fms_activeness_change'] as $handler) {
				call_user_func($handler, $eventData);
			}
		}
	}*/
}
