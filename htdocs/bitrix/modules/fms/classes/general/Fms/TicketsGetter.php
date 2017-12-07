<?
namespace Fms;

class TicketsGetter {
	private $_params;

	/**
	 * Конструктор.
	 *
	 * $params:
	 * - fmses_getter
	 */
	public function __construct ($params) {
		$this->_params = $params;
	}



	/**
	 * Получение данных билета.
	 *
	 * @param int|array $fms Или ID ФМСа, или то, что возвращает FmsesGetter::getDataForDetailPageById()
	 * @param int $ticketNumber
	 * @return array
	 * @throws \Exception
	 */
	function getData ($fms, $ticketNumber) {
		global $USER;
		if (is_array($fms)) {
			$pageData = $fms;
		} else {
			$pageData = $this->_params['fmses_getter']->getDataForDetailPageById($fms);
		}
		if (empty($pageData)) {
			 throw new \Exception("FMS not found");
		}
		$fmsPlaceData = $pageData['location']['place'];
		$ticketData = array(
			'fms_id'        => $pageData['fms']['ID'],
			'fms_name'      => $pageData['fms']['NAME'],
			'ticket_number' => $ticketNumber,
			'user_name'     => $USER->GetFullName(),
			'fms_date'      => $pageData['fms']['start_date'],
			'fms_time'      => $pageData['fms']['start_time'],
			'fms_where'     => ($pageData['fms']['PROPERTY_ADDRESS_VALUE'] !="")?$pageData['fms']['PROPERTY_ADDRESS_VALUE']:$fmsPlaceData['mall_name'] . ', ' . $fmsPlaceData['operator_location'] . ', ' . $fmsPlaceData['operator_name'] . '.',
		);
		return $ticketData;
	}



	/**
	 * Получение данных билета с именованием полей, принятым в JS.
	 */
	function getJsData ($fms, $ticketNumber) {
		$ticketData = $this->getData($fms, $ticketNumber);
		$ticketJsData = array(
			'fmsId'        => $ticketData['fms_id'],
			'fmsName'      => $ticketData['fms_name'],
			'ticketNumber' => $ticketData['ticket_number'],
			'userName'     => $ticketData['user_name'],
			'fmsDate'      => $ticketData['fms_date'],
			'fmsTime'      => $ticketData['fms_time'],
			'fmsWhere'     => $ticketData['fms_where'],
		);
		return $ticketJsData;
	}
}
