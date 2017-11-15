<?
namespace Fms\HtmlParts;
class FmsBlock {
public static function show ($params = array()) {
	\Fms\HtmlParts\LangFilesIncluder::getInstance()->includeLangFile('FmsBlock');
	global $APPLICATION, $USER, $CONFIG, $fmsLocalization, $fmsCurrencyCodes, $s5BitrixCodes;
	$default = array(
		'modifier_class'               => '',
		'related_video_modifier_class' => '',
		'location'                     => 'index', //index/detail/edit_form/list_preview/detail_preview
		'is_show_operator_controls'    => false,
	);
	$p = array_merge($default, $params);
	$data = $p['data'];
	$location = $p['location'];
	$tagName = ($location == 'index') ? 'article' : 'div';
	if ($location != 'fms_form') {
		$typeCode           = $CONFIG['fmses']['type_id_to_code'][$data['PROPERTY_TYPE_ENUM_ID']];
		$stateCode          = $CONFIG['fmses']['state_id_to_code'][$data['PROPERTY_STATE_ENUM_ID']];
		$isWithRelatedVideo = (
			($location == 'index' or $location == 'list_preview') and
			($data['PROPERTY_RELATED_VIDEO_STATE_code'] == 'approved' and !empty($data['PROPERTY_RELATED_VIDEO_VALUE']) and !empty($data['PROPERTY_RELATED_VIDEO_ORIGINAL_IMAGE_VALUE']))
		);
		$currencyCode = ($typeCode == 'sale') ? $fmsCurrencyCodes->getCode($data['PROPERTY_CURRENCY_VALUE']) : false;
	} else {
		$formValues = $data['form']['values'];
		//echo '<pre>'; print_r($formValues); echo '</pre>'; exit();
	}
	$APPLICATION->SetPageProperty('is_need_ticket', true);
	if ($location == 'index' or $location == 'list_preview') {
		$APPLICATION->SetPageProperty('is_need_youtube', true);
	}
	$markError = function ($fieldCode) use ($data) {
		if (isset($data['form']['errors'][$fieldCode])) {
			echo 'fmsForm_error';
		}
	};
	$detailUrl = '/fms/'.$data['ID'].'/';
	
	
	$is_user_going = false;
	if ($USER->IsAuthorized()){ 

		$going_ids = array();
		$arFilter = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('fmses_to_users'), "PROPERTY_FMS"=>$data['ID']);
		$res = \CIBlockElement::GetList(Array(), $arFilter, false, false, array('ID','IBLOCK_ID', 'PROPERTY_USER'));
		while($arFields = $res->GetNext()) {
			$going_ids[] = $arFields['PROPERTY_USER_VALUE'];
		} 

		$arFilter = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('fmses_to_users'), "PROPERTY_USER"=>$USER->GetId(), "PROPERTY_FMS"=>$data['ID']);
		$res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), array('ID','IBLOCK_ID'));
		if($arFields = $res->GetNext()) {
			$is_user_going  = true;
		} 
		
		$current_user_link_count = 0;
		$leader_user_link_count = 0;
		
		$arFilter = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('referal'), "PROPERTY_USER"=>$USER->GetId(), "PROPERTY_FMS"=>$data['ID']);
		$res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), array('ID','IBLOCK_ID', 'PROPERTY_COUNT'));
		if($arFields = $res->GetNext()) {
			$current_user_link_count = $arFields['PROPERTY_COUNT_VALUE'];
		} 
		
		$is_user_leader = false;
		
		if (count($going_ids) > 0){
			$arFilter = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('referal'), "PROPERTY_FMS"=>$data['ID'], 'PROPERTY_USER' => $going_ids);
			$res = \CIBlockElement::GetList(Array('PROPERTY_COUNT'=>'DESC'), $arFilter, false, Array("nPageSize"=>1), array('ID','IBLOCK_ID', 'PROPERTY_COUNT','PROPERTY_USER'));
			if($arFields = $res->GetNext()) {
				$leader_user_link_count = $arFields['PROPERTY_COUNT_VALUE'];
				if ($arFields['PROPERTY_USER_VALUE'] == $USER->GetId()){
					$is_user_leader = true;
				}
			} 
		}

	}
	$data['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['WORK_LOGO_ORIGINAL'] = \CFile::ResizeImageGet($data['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['~WORK_LOGO'],array('width'=>250, 'height'=>220),BX_RESIZE_IMAGE_PROPORTIONAL, true);
	?>

<<?=$tagName?> class="fmsBlock <?=$p['modifier_class']?> <?if ($isWithRelatedVideo) echo 'fmsBlock_mWithRelatedVideo'?>">
	<?if ($location == 'index') {?>
		<a href="<?=$detailUrl?>" class="fmsBlock_link" target="_blank"></a>
	<?} elseif ($location == 'list_preview') {?>
		<a href="" class="fmsBlock_link disabledClick"></a>
	<?}?>
	<div class="fmsBlock_image">
		<?if ($location != 'fms_form' and ($typeCode == 'sale' || $typeCode == 'event')) {?>
			<?if (isset($data['PROPERTY_BRAND_VALUE']['PREVIEW_PICTURE']) and is_array($data['PROPERTY_BRAND_VALUE']['PREVIEW_PICTURE'])) {
				$imageData = $data['PROPERTY_BRAND_VALUE']['PREVIEW_PICTURE'];
				?>
				<img src="<?=$imageData['SRC']?>" width="<?=$imageData['WIDTH']?>" height="<?=$imageData['HEIGHT']?>" alt="<?=$data['PROPERTY_BRAND_VALUE']['NAME']?>" class="fmsBlock_image_brandLogo">
			<?}?>
		<?}?>
		<?if ($location != 'fms_form') {
			$imageData = ($location == 'index' or $location == 'list_preview' or $location == 'album_preview') ? $data['PREVIEW_PICTURE'] : $data['DETAIL_PICTURE'];
			if (is_array($imageData)) {
			?>
				<img src="<?=$imageData['SRC']?>" alt="">
			<?}?>
		<?} else {?>
			<div id="fms_form_brand_block" class="fmsForm_brandBlock">
				<div class="fmsForm_brand">
					<div id="fms_form_brand_button" class="fmsForm_brand_button" data-default-text="<?=GetMessage('ADD_BRAND')?>"><?=($formValues['BRAND'] == '' ? GetMessage('ADD_BRAND') : $formValues['BRAND'])?></div>
					<div id="fms_form_brand_select" class="fmsForm_brand_select">
						<input name="BRAND" type="text" class="stdForm_inputtext fmsForm_brand_select_i" value="<?=$formValues['BRAND']?>">
					</div>
				</div>
			</div>
			<div class="fmsForm_cover">
				<div class="fmsForm_cover_text"><?=GetMessage('COVER')?></div>
				<div class="fmsForm_cover_radios">
					<input id="fms_form_cover_type_image" class="stdForm_inputradio" type="radio" name="COVER_TYPE" value="image">
					<label for="fms_form_cover_type_image"><?=GetMessage('PHOTO')?></label>
					&nbsp;&nbsp;&nbsp;
					<input id="fms_form_cover_type_video" class="stdForm_inputradio" type="radio" name="COVER_TYPE" value="video">
					<label for="fms_form_cover_type_video"><?=GetMessage('VIDEO')?></label>
				</div>
				<div class="fmsForm_cover_inputs">
					<div class="fmsForm_cover_input">
						<div class="fmsForm_cover_requiredWrapper fmsForm_required <?$markError('ORIGINAL_IMAGE')?>">
							<input name="ORIGINAL_IMAGE" type="file" class="stdForm_inputfile">
						</div>
					</div>
					<div id="fms_form_cover_video_url_input" class="fmsForm_cover_input fmsForm_cover_videoInput">
						<input name="EMBEDDED_VIDEO" class="stdForm_inputtext fmsForm_cover_videoI fmsForm_w250 fmsForm_required <?$markError('EMBEDDED_VIDEO')?>" type="text" placeholder="<?=GetMessage('YOUTUBE_URL')?>" value="<?=$formValues['EMBEDDED_VIDEO']?>">
					</div>
				</div>
			</div>
		<?}?>
		<div class="fmsBlock_image_grayLine">
			<div class="fmsBlock_image_grayLine_shadow"></div>
			<?if ($location != 'fms_form') {?>
				<div class="fmsBlock_image_grayLine_padded">
					<div class="fmsBlock_operator">
						<div class="fmsBlock_operator_image">
							<?if (is_array($data['PROPERTY_OPERATOR_VALUE']['PERSONAL_PHOTO'])) {
								$photoData = $data['PROPERTY_OPERATOR_VALUE']['PERSONAL_PHOTO'];
								?>
								<img src="<?=$photoData['SRC']?>" width="<?=$photoData['WIDTH']?>" height="<?=$photoData['HEIGHT']?>" alt="">
							<?}?>
						</div>
						<div class="fmsBlock_operator_text">
							<div class="fmsBlock_operator_text_padded"><?=$data['NAME']?></div>
						</div>
					</div>
					<div class="where Slashed_chain">
						<?//echo '<pre>'; var_dump($data['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']); echo '</pre>';?>
						<?=$data['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['UF_TOWN']['NAME']?>
						<span class="delimiter">/</span>
						<?=$data['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['NAME']?>
						<span class="delimiter">/</span>
						<?=$data['PROPERTY_OPERATOR_VALUE']['NAME']?>
					</div>
				</div>
			<?}?>
		</div>
	</div>
	<?if ($_GET['render']=='Y'):?>
		<div class="render_fms_datetime_container">
			<div class="render_fms_datetime_date">
				<div class="render_fms_datetime_date_icon"></div>
				<div class="render_fms_datetime_date_text">
					<?=$fmsLocalization->formatDate($data['PROPERTY_START_DATETIME_VALUE'])?>
				</div>
			</div>
			<div class="render_fms_datetime_time">
				<div class="render_fms_datetime_time_icon"></div>
				<div class="render_fms_datetime_time_text">
					<?=date('H:i', strtotime($data['PROPERTY_START_DATETIME_VALUE']))?>
				</div>
			</div>
		</div>
		<div class="render_fms_logo">
			<img src="<?=$data['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['WORK_LOGO_ORIGINAL']['src']?>">
		</div>

	<?else:?>
		<div class="fmsBlock_data">
			<div class="fmsBlock_line fmsBlock_line1">
				<?/* Discount */?>
				<?if ($typeCode == 'sale' or $location == 'fms_form') {?>
					<?if ($location != 'fms_form') {?>
						<div class="fmsBlock_line1_discount"><?
							//echo ($data['PROPERTY_GOING_PEOPLE_VALUE'] < $data['PROPERTY_BONUS_PEOPLE_VALUE']) ? $data['PROPERTY_DISCOUNT_VALUE'] : $data['possible_discount'];
                            echo $data['PROPERTY_DISCOUNT_VALUE'];
						?>%</div>
					<?} else {?>
						<div class="fmsBlock_line1_discount">
							<input name="DISCOUNT" class="stdForm_inputtext fmsForm_line1DiscountI fmsForm_w90 fmsForm_required <?$markError('DISCOUNT')?>" type="text" placeholder="<?=GetMessage('DISCOUNT')?>" value="<?=$formValues['DISCOUNT']?>">
							%
						</div>
					<?}?>
					<div class="fmsBlock_line1_arrow"></div>
				<?} elseif ($typeCode == 'event') {?>
					<div class="fmsBlock_line1_discount">
						FREE
					</div>
				<div class="fmsBlock_line1_arrow"></div>
				<?}?>
				<time class="fmsBlock_hiddenTime" datetime="2013-01-01 00:00:00"></time>
				<?/* Datetime */?>
				<div class="fmsBlock_line1_datetime">
					<div class="fmsBlock_line1_date">
						<div class="fmsBlock_line1_icon fmsBlock_line1_date_icon"></div>
						<?if ($location != 'fms_form') {?>
							<div class="fmsBlock_line1_text"><?=$fmsLocalization->formatDate($data['PROPERTY_START_DATETIME_VALUE'])?></div>
						<?} else {?>
							<input name="START_DATE" id="fms_form_date" class="stdForm_inputtext fmsForm_line1DatetimeI fmsForm_w90 fmsForm_required <?$markError('START_DATE')?>" type="text" placeholder="<?=GetMessage('DATE')?>" value="<?=$formValues['START_DATE']?>">
						<?}?>
					</div>
					<div class="fmsBlock_line1_time">
						<div class="fmsBlock_line1_icon fmsBlock_line1_time_icon"></div>
						<?if ($location != 'fms_form') {?>
							<div class="fmsBlock_line1_text"><?=date('H:i', strtotime($data['PROPERTY_START_DATETIME_VALUE']))?></div>
						<?} else {?>
							<input name="START_TIME" id="fms_form_time" class="stdForm_inputtext fmsForm_line1DatetimeI fmsForm_w71 fmsForm_required <?$markError('START_TIME')?>" type="text" placeholder="<?=GetMessage('TIME')?>" value="<?=$formValues['START_TIME']?>">
						<?}?>
					</div>
				</div>
				<div class="fmsBlock_line_bottomLine fmsBlock_line1_bottomLine"></div>
			</div>
			<?if ($typeCode == 'sale' or $location == 'fms_form') {?>
				<div class="fmsBlock_line fmsBlock_line2">
					<?if ($location == 'fms_form') echo '<div id="fms_form_line2_content">'?>
					<div class="fmsBlock_line2_padded">
						<?if ($location != 'fms_form' and $is_user_going):?>
							<div class="fms_block_flash_mob_leader_<?=$is_user_leader ? 'active':'gray'?>"></div>
							<div class="fms_block_flash_mob_leader_info_container">
								<table>
									<tr>
										<td>
											Верни 50% чека
										</td>
										<td>
											Переходы
											
										</td>
									</tr>
									<tr>
										<td colspan="2" class="fms_block_flash_mob_leader_info_container_sep"></td>
									</tr>
									<tr>
										<td>
											У тебя
											<div class="fms_block_flash_mob_leader_link_count"><?=intval($current_user_link_count)?></div>
										</td>
										<td>
											У лидера
											<div class="fms_block_flash_mob_leader_link_count"><?=intval($leader_user_link_count)?></div>
										</td>
									</tr>
								</table>
							</div>
						<?else:?>
						<?/* Example image */?>
							<div class="fmsBlock_line2_imageBlock">
								<div class="fmsBlock_line2_imageBlock_vtext"></div>
								<?if ($location != 'fms_form') {?>
									<?// echo '<pre>'; print_r($data['PROPERTY_EXAMPLE_IMAGE_VALUE']); echo '</pre>'; ?>
									<?if (is_array($data['PROPERTY_EXAMPLE_IMAGE_VALUE'])) {
										$imageData = $data['PROPERTY_EXAMPLE_IMAGE_VALUE'];
										?>
										<img class="fmsBlock_line2_imageBlock_image" src="<?=$imageData['SRC']?>" width="<?=$imageData['WIDTH']?>" height="<?=$imageData['HEIGHT']?>" alt="">
									<?}?>
								<?} else {?>
									<div id="fms_form_example_image_preview_div" class="fmsBlock_line2_imageBlock_image" style="background:#999999;">
										<div id="fms_form_example_image_button" class="fmsForm_line2ExampleImageButton"><?=GetMessage('ADD_PHOTO')?></div>
										<input name="EXAMPLE_ORIGINAL_IMAGE" id="fms_form_example_image_i" class="fmsForm_line2ExampleImageI" type="file">
									</div>
								<?}?>
							</div>
							<div class="fmsBlock_line2_text">
								<?/* Example name */?>
								<div class="fmsBlock_line2_text_name <?if ($location == 'fms_form') echo 'fmsBlock_line2_text_name_mEditMode'?>">
									<?if ($location != 'fms_form') {?>
										<?=$data['PROPERTY_EXAMPLE_NAME_VALUE']?>
									<?} else {?>
										<input name="EXAMPLE_NAME" class="stdForm_inputtext fmsForm_line2NameI fmsForm_w305 fmsForm_required <?$markError('EXAMPLE_NAME')?>" type="text" placeholder="<?=GetMessage('EXAMPLE_PRODUCT_NAME')?>" value="<?=$formValues['EXAMPLE_NAME']?>">
									<?}?>
								</div>
								<div class="fmsBlock_line2_text_params">
									<?/* Example discount - percents */?>
									<div class="fmsBlock_param fmsBlock_line2_text_param">
										<div class="fmsBlock_paramValue">
											<?if ($location != 'fms_form') {?>
												<?=$data['PROPERTY_EXAMPLE_DISCOUNT_VALUE']?>%
											<?} else {?>
												<input name="EXAMPLE_DISCOUNT" id="fms_form_example_discount_percent_i" class="stdForm_inputtext fmsForm_line2DiscountI fmsForm_w49 fmsForm_required <?$markError('EXAMPLE_DISCOUNT')?>" type="text" value="<?=$formValues['EXAMPLE_DISCOUNT']?>">
												%
											<?}?>
										</div>
										<div class="fmsBlock_paramName"><?=GetMessage('DISCOUNT')?></div>
									</div>
									<?/* Example discount - discounted price */?>
									<div class="fmsBlock_param fmsBlock_line2_text_param">
										<div class="fmsBlock_paramValue">
											<?if ($location != 'fms_form') {?>
												<?=$fmsLocalization->formatPriceInt($data['discounted_price'], $currencyCode)?>
											<?} else {
												foreach ($data['price_format']['parts_order'] as $partCode) {
													switch ($partCode) {
														case 'value':
															?><span id="fms_form_example_discounted_price">0</span><?
														break;
														case 'currency': echo $data['price_format']['currency_name']; break;
													}
												}?>
												<?/*$<span id="fms_form_example_discounted_price">0</span>*/?>
											<?}?>
										</div>
										<div class="fmsBlock_paramName"><?=GetMessage('SALE_PRICE')?></div>
									</div>
									<?/* Example discount - original price */?>
									<div class="fmsBlock_param fmsBlock_line2_text_param">
										<div class="fmsBlock_paramValue fmsBlock_line2_text_originalPrice">
											<?if ($location != 'fms_form') {?>
												<?=$fmsLocalization->formatPriceInt($data['PROPERTY_EXAMPLE_ORIGINAL_PRICE_VALUE'], $currencyCode)?>
											<?} else {
												foreach ($data['price_format']['parts_order'] as $partCode) {
													switch ($partCode) {
														case 'value':?>
															<input name="EXAMPLE_ORIGINAL_PRICE" id="fms_form_example_original_price_i" class="stdForm_inputtext fmsForm_line2DiscountI fmsForm_w49 fmsForm_required <?$markError('EXAMPLE_ORIGINAL_PRICE')?>" type="text" value="<?=$formValues['EXAMPLE_ORIGINAL_PRICE']?>">
														<?break;
														case 'currency': echo $data['price_format']['currency_name']; break;
													}
												}
												?>
											<?}?>
										</div>
										<div class="fmsBlock_paramName"><?=GetMessage('ORIGINAL_PRICE')?></div>
									</div>
								</div>
							</div>
						<?endif?>
					</div>
					<?if ($location == 'fms_form') echo '</div>'?>
					<div class="fmsBlock_line_bottomLine fmsBlock_line2_bottomLine"></div>
				</div>
			<?} elseif ($typeCode == 'event' ) { //echo '<pre>'; print_r($data); echo '</pre>'; exit(); ?>
				<div class="fmsBlock_line fmsBlock_line2">
					<!--<div class="fmsBlock_line2_weather">
						<div class="fmsBlock_line2_weather_icon fmsBlock_line2_weather_sunIcon"></div>
						<div class="fmsBlock_line2_weather_temp">4°C</div>
					</div>-->
					<?//if($location != 'detail'){*/?>
					<ul class="fmsBlock_line2_participants">
						<?
						$limiter = 8;
						foreach ($data['participants'] as $partData) {?>
							<li><img src="<?=$partData['images']['small']['SRC']?>" alt="" class="fmsBlock_line2_participants_image"></li>
							<?
							if (!--$limiter) break;
						}?>
					</ul>
					<?//}?>
					<div class="fmsBlock_line_bottomLine fmsBlock_line2_bottomLine"></div>
				</div>
			<?}?>
			<?/* Line 3 */?>
			<div class="fmsBlock_line fmsBlock_generalLine fmsBlock_line3">
				<?/* People required */?>
				<div class="fmsBlock_param fmsBlock_line3_param fmsBlock_line3_peopleRequired">
					<div class="fmsBlock_paramValue fmsBlock_generalLine_paramValue">
						<?if ($location != 'fms_form') {?>
							<?=$data['PROPERTY_REQUIRED_PEOPLE_VALUE']?> <?=GetMessage('PEOPLE')?>
						<?} else {?>
							<input name="REQUIRED_PEOPLE" class="stdForm_inputtext fmsForm_raisedI fmsForm_w42 fmsForm_required <?$markError('REQUIRED_PEOPLE')?>" type="text" value="<?=$formValues['REQUIRED_PEOPLE']?>">
							<?=GetMessage('PEOPLE')?>
						<?}?>
					</div>
					<div class="fmsBlock_paramName"><?=($typeCode == 'event')?GetMessage('PEOPLE_NEED'):GetMessage('PEOPLE_TO_ACTIVATE')?></div>
					<?
					$meterMin = -200;
					$meterMax = -121;
					$meterLength = $meterMax - $meterMin;
					if ($data['PROPERTY_GOING_PEOPLE_VALUE'] < $data['PROPERTY_REQUIRED_PEOPLE_VALUE']) {
						$koeff = $data['PROPERTY_GOING_PEOPLE_VALUE'] / $data['PROPERTY_REQUIRED_PEOPLE_VALUE'];
						$meterPos = floor($meterLength * $koeff + $meterMin);
						$fullMeterClass = '';
					} else {
						$meterPos = $meterMax;
						$fullMeterClass = 'peopleMeter_mFull';
					}
					?>
					<div class="peopleMeter fmsBlock_peopleMeter <?=$fullMeterClass?>">
						<div class="peopleMeter_bg"></div>
						<div style="left:<?=$meterPos?>px" class="peopleMeter_fill"></div>
						<div class="peopleMeter_window"></div>
					</div>
				</div>
				<div class="fmsBlock_generalLine_arrow"></div>
				<?/* People ready */?>
				<div class="fmsBlock_param fmsBlock_line3_param">
					<div class="fmsBlock_paramValue fmsBlock_generalLine_paramValue">
						<?if ($location != 'fms_form') {?>
							<?=$data['PROPERTY_GOING_PEOPLE_VALUE']?>
						<?} else {?>
							0
						<?}?>
						<?=GetMessage('PEOPLE')?>
					</div>
					<div class="fmsBlock_paramName"><?=GetMessage('READY_TO_GO')?></div>
				</div>
				<?if ($location == 'detail' or $location == 'fms_form' or $location == 'detail_preview') {?>
					<div class="fmsBlock_line_bottomLine"></div>
				<?}?>
			</div>
			<?if ($location == 'detail' or $location == 'fms_form' or $location == 'detail_preview') {?>
				<div class="fmsBlock_line fmsBlock_generalLine fmsBlock_bonusLine">
					<?if ($location == 'fms_form') echo '<div id="fms_form_bonus_line_content">'?>
					<?if (
						($typeCode == 'sale' and $data['PROPERTY_BONUS_PEOPLE_VALUE'] > 0 and $data['PROPERTY_BONUS_DISCOUNT_VALUE'] > 0) or
						$location == 'fms_form'
					) {?>
						<?/* Bonus people */?>
						<div class="fmsBlock_param fmsBlock_bonusLine_param fmsBlock_bonusLine_people">
							<div class="fmsBlock_paramValue fmsBlock_generalLine_paramValue">
							<?if ($location != 'fms_form') {?>
								<?=$data['PROPERTY_BONUS_PEOPLE_VALUE']?>
							<?} else {?>
								<input name="BONUS_PEOPLE" class="stdForm_inputtext fmsForm_raisedI fmsForm_w42 <?$markError('BONUS_PEOPLE')?>" type="text" value="<?=$formValues['BONUS_PEOPLE']?>">
							<?}?>
							<?=GetMessage('PEOPLE')?>
							</div>
							<div class="fmsBlock_paramName"><?=GetMessage('PEOPLE_TO_BONUS')?></div>
						</div>
						<div class="fmsBlock_generalLine_arrow fmsBlock_bonusLine_arrow fmsBlock_bonusLine_arrow1"></div>
						<?/* Bonus discount */?>
						<div class="fmsBlock_param fmsBlock_bonusLine_param fmsBlock_bonusLine_bonus">
							<div class="fmsBlock_paramValue">
								<?if ($location != 'fms_form') {?>
									+ <?=$data['PROPERTY_BONUS_DISCOUNT_VALUE']?>%
								<?} else {?>
									+ <input name="BONUS_DISCOUNT" class="stdForm_inputtext fmsForm_raisedI fmsForm_w42 <?$markError('BONUS_DISCOUNT')?>" type="text" value="<?=$formValues['BONUS_DISCOUNT']?>">%
								<?}?>
							</div>
							<div class="fmsBlock_paramName"><?=GetMessage('BONUS_FROM_1FMS')?></div>
						</div>
                        <?/* <div class="fmsBlock_generalLine_arrow fmsBlock_bonusLine_arrow fmsBlock_bonusLine_arrow2"></div>
						Bonus possible
						<div class="fmsBlock_param fmsBlock_bonusLine_param fmsBlock_bonusLine_current">
							<div class="fmsBlock_paramValue fmsBlock_generalLine_paramValue">
								<?if ($location != 'fms_form') {?>
									<?=$data['possible_discount']?>%
								<?} else {?>
									<span id="fms_form_example_bonused_discount"></span>%
								<?}?>
							</div>
							<div class="fmsBlock_paramName"><?=GetMessage('POSSIBLE_DISCOUNT')?></div>
						</div>
                        */?>
					<?}?>
					<?if ($location == 'fms_form') echo '</div>'?>
					<div class="fmsBlock_line_bottomLine"></div>
				</div>
				<div class="fmsBlock_line fmsBlock_generalLine fmsBlock_timeLine">
					<div class="fmsBlock_param fmsBlock_timeLine_timeLeft">
						<div id="time_left_value" class="fmsBlock_timeLine_timeLeft_paramValue">
							<?
							if ($location != 'fms_form') {
								/*Учет часового пояса из данных оператора*/
								if ($data['time_left']){
									$timeArray=explode(":",$data['time_left']);
									$offset = $data['PROPERTY_OPERATOR_VALUE']['UTC_OFFSET'];
									$timeVecktor = $timeArray[0]*3600 + $timeArray[1]*60 + $timeArray[2];
									$hours = (int)((int)$offset/100);
									$mins = ($offset/100 - $hours)*100;
									if (substr_count ($offset,'+')){
										$timeVecktor -= $hours*3600;
										$timeVecktor -= $mins*60;
									}else if (substr_count ($offset,'-')){
										$timeVecktor += $hours*3600;
										$timeVecktor += $mins*60;
									}
									$timeArray = array((int)($timeVecktor/3600),(int)(($timeVecktor % 3600)/60),$timeVecktor % 60);
									$data['time_left'] = implode(":",$timeArray);
								}
								/*----------------------------------------*/
								$timeLeft = ($data['time_left'] ? $data['time_left'] : '00:00:00');
							} else {
								$timeLeft = '00:00:00';
							}
							echo $timeLeft;
							?>
						</div>
						<div class="fmsBlock_paramName fmsBlock_timeLine_timeLeft_paramName"><?=GetMessage('TIME_LEFT')?></div>
					</div>
					<div class="fmsBlock_param fmsBlock_timeLine_peopleCounter">
						<div class="fmsBlock_paramValue fmsBlock_generalLine_paramValue">
							<?if ($location != 'fms_form') {?>
								<?=$data['PROPERTY_GOING_PEOPLE_VALUE']?> <?=GetMessage('OF')?> <?=$data['PROPERTY_MAX_PEOPLE_VALUE']?>
							<?} else {?>
								<?=GetMessage('OF')?> &nbsp;<input name="MAX_PEOPLE" class="stdForm_inputtext fmsForm_raisedI fmsForm_w42 <?$markError('MAX_PEOPLE')?>" type="text" value="<?=$formValues['MAX_PEOPLE']?>">
							<?}?>
						</div>
						<div class="fmsBlock_paramName"><?=GetMessage('UNTIL_PEOPLE_END')?></div>
					</div>
				</div>
			<?}?>
			<?
			if ($location == 'index' or $location == 'list_preview') {
				if ($location != 'list_preview') {
					$disabledClickClass = '';
					$isAliveLocation    = true;
				} else {
					$disabledClickClass = 'disabledClick';
					$isAliveLocation    = false;
				}
				$fmsButtonRole = \Fms\FmsButtonsRoleDetector::detect($data, $isAliveLocation);
//echo "<pre>";
//echo $data['PROPERTY_GOING_PEOPLE_VALUE']." ".$data['PROPERTY_REQUIRED_PEOPLE_VALUE'];
//echo "</pre>";
//echo $fmsButtonRole;
				?>
				<div class="fmsBlock_viewAndDo">
					<?if ($fmsButtonRole == 'album') {?>
						<a class="fmsBlock_viewAndDo_link" href="/profile/my_media/album/<?=$data['ID']?>/" target="_blank"><?=GetMessage('VIEW_AND_UPLOAD_PHOTOS')?></a>
					<?} elseif ($fmsButtonRole == 'im_going') {?>
						<a class="fmsBlock_viewAndDo_link" href="<?=$detailUrl?>" target="_blank"><?=GetMessage('VIEW_AND_GO')?></a>
					<?} elseif ($fmsButtonRole == 'print_ticket') {?>
						<a class="fmsBlock_viewAndDo_link" href="<?=$detailUrl?>" target="_blank"><?=GetMessage('VIEW_AND_PRINT_TICKET')?></a>
					<?} elseif ($fmsButtonRole == 'upload') {?>
						<a class="fmsBlock_viewAndDo_link" href="/profile/my_media/album/<?=$data['ID']?>/" target="_blank"><?=GetMessage('VIEW_AND_UPLOAD_PHOTOS')?></a>
					<?} elseif ($fmsButtonRole == 'was_fun') {?>
						<a class="fmsBlock_viewAndDo_link" href="<?=$detailUrl?>" target="_blank"><?=GetMessage('VIEW')?></a>
					<?}?>
				</div>
				<?$location == 'list_preview' ? $my_role = 'im_going' :  $my_role = $fmsButtonRole;?>
				<?if ($USER->IsAuthorized()) {
					FmsButton::show($data['ID'], $my_role, '89_89', "fmsBlock_rbButton $disabledClickClass", '', $data);
				}else{
					FmsButton::showLink('/auth/?back_url='.urlencode("/fms/".$data['ID']."/".'?go=1&fms_id='.$data['ID'].'&user=none'), $my_role, '89_89', "fmsBlock_rbButton $disabledClickClass", '', $data);
				}?>
				<?if (!empty($data['PROPERTY_EMBEDDED_VIDEO_VALUE'])) {?>
					<div id="fms_embedded_video_play_<?=$data['ID']?>" class="fmsBlock_play"></div>
					<div class="fmsBlock_video">
						<iframe id="fms_embedded_video_<?=$data['ID']?>" src="" data-src="<?=$data['PROPERTY_EMBEDDED_VIDEO_embed']?>?enablejsapi=1&wmode=opaque" frameborder="0" class="fmsBlock_video_iframe"></iframe>
						<script>
						if (isMobileBrowser()) {
							$('#fms_embedded_video_play_<?=$data['ID']?>').replaceWith('<a class="fmsBlock_playLink" href="<?=$data['PROPERTY_EMBEDDED_VIDEO_watch']?>" target="_blank">&nbsp;</a>');
						}
						</script>
					</div>
				<?}?>
			<?}?>
		</div>
	<?endif?>
	<?if ($location != 'fms_form' and $typeCode == 'sale' and isset($data['PROPERTY_EXAMPLE_ORIGINAL_IMAGE_VALUE']['SRC'])) {?>
		<a href="<?=$data['PROPERTY_EXAMPLE_ORIGINAL_IMAGE_VALUE']['SRC']?>" target="_blank" class="fmsBlock_exampleImageLink dynamicColorbox"></a>
	<?}?>
</<?=$tagName?>>
<? //echo "<pre>";print_r($data);echo "</pre>";?>
<?if ($isWithRelatedVideo) {?>
<div class="fmsVideoblock <?=$p['related_video_modifier_class']?>">
	<div class="fmsVideoblock_video">
		<iframe id="fms_related_video_<?=$data['ID']?>" class="fmsVideoblock_iframe" src="" data-src="<?=$data['PROPERTY_RELATED_VIDEO_embed']?>?enablejsapi=1&wmode=opaque" frameborder="0" allowfullscreen></iframe>
	</div>
	<div id="fms_related_video_play_<?=$data['ID']?>" class="fmsVideoblock_cover">
		<?$imageData = $data['PROPERTY_RELATED_VIDEO_IMAGE_VALUE']?>
		<img src="<?=$imageData['SRC']?>" width="<?=$imageData['WIDTH']?>" height="<?=$imageData['HEIGHT']?>" alt="">
		<div class="fmsVideoblock_play"></div>
		<?/*<div class="fmsVideoblock_text">
			<div class="fmsVideoblock_text_padded">
				<?=$data['NAME']?>
			</div>
		</div>*/?>
	</div>
	<script>
	if (isMobileBrowser()) {
		$('#fms_related_video_play_<?=$data['ID']?>').replaceWith(function () {
			var me = $(this);
			return $('<a class="fmsVideoblock_coverLink" href="<?=$data['PROPERTY_RELATED_VIDEO_watch']?>" target="_blank">' + me.html() + '</a>');
		});
	}
	</script>
</div>
<?}?>

<?if ($p['is_show_operator_controls']) {?>
	<div class="operatorFmsesPage_fms_controls">
		<?if ($stateCode == 'draft' and $data['is_editable']) {?>
			<a class="operatorFmsesPage_fms_control operatorFmsesPage_fms_editDraftControl" href="/operator_profile/fms_master/?id=<?=$data['ID']?>"><?=GetMessage('EDIT_DRAFT')?></a>
		<?} elseif ($stateCode == 'active' and $data['is_editable']) {?>
			<a class="operatorFmsesPage_fms_control operatorFmsesPage_fms_editOpenControl" href="/operator_profile/fms_master/?id=<?=$data['ID']?>"><?=GetMessage('EDIT')?></a>
		<?}?>
		<?if (($stateCode == 'draft' or $stateCode == 'active') and $data['is_editable']) {?>
			<form id="delete_fms_form_<?=$data['ID']?>" action="" method="post" style="display:none;">
				<?=bitrix_sessid_post()?>
				<input type="hidden" name="action" value="delete_fms">
				<input type="hidden" name="id" value="<?=$data['ID']?>">
			</form>
			<a class="operatorFmsesPage_fms_control operatorFmsesPage_fms_deleteControl" href="" data-rel="<?=$data['ID']?>"><?=GetMessage('REMOVE')?></a>
		<?}?>
		<form id="copy_fms_form_<?=$data['ID']?>" action="" method="post" style="display:none;">
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="action" value="copy_fms">
			<input type="hidden" name="id" value="<?=$data['ID']?>">
		</form>
		<a class="operatorFmsesPage_fms_control operatorFmsesPage_fms_repeatControl" href="" data-rel="<?=$data['ID']?>"><?=GetMessage('REPEAT')?></a>
		<?if ($stateCode == 'completed') {?>
			<a class="operatorFmsesPage_fms_control operatorFmsesPage_fms_albumControl" href=""><?=GetMessage('ALBUM')?></a>
		<?}?>
		<div class="operatorFmsesPage_fms_info"><?=GetMessage('STATE')?>: <?=GetMessage('STATE_'.$data['PROPERTY_STATE_code'])?></div>
	</div>
<?}?>



<?if ($location == 'detail' or $location == 'detail_preview') {?>
<script>$(document).ready(function () {
	<?if ($data['time_left']) {?>
		(function () {
			if ($('*').is('#time_left_value')){
				var timeLeft = $('#time_left_value');
				if ($('#time_left_value').html().trim() == "00:00:00") return false;
				var timeParts = timeLeft.html().split(/:/);
				var timeLeftInterval = setInterval(
					function () {
						timeParts[2]--;
						if (timeParts[0] == 0 && timeParts[1] == 0 & timeParts[2] == 0) {
							clearInterval(timeLeftInterval);
						}
						if (timeParts[2] == -1) {
							timeParts[2] = 59;
							timeParts[1]--;
							if (timeParts[1] == -1) {
								timeParts[1] = 59;
								timeParts[0]--;
							}
						}
						for (var a in timeParts) {
							if (timeParts[a].toString().length == 1) {
								timeParts[a] = '0'+timeParts[a];
							}
						}
						timeLeft.html(timeParts[0]+':'+timeParts[1]+':'+timeParts[2]);
					},
					1000
				);
			}
		})();
		<?}?>
	});</script>
<?}?>


<?}}?>
