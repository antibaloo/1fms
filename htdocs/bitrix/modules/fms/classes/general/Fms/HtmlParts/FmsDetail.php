<?
namespace Fms\HtmlParts;

class FmsDetail {
public static function show ($params = array()) {
	\Fms\HtmlParts\LangFilesIncluder::getInstance()->includeLangFile('FmsDetail');
	global $APPLICATION, $USER, $CONFIG, $fmsLocalization, $s5BitrixCodes;
	$default = array(
		'fms_block_params' => array(),
		'location'         => 'detail', //detail/fms_form/fms_preview
	);
	$p = array_merge($default, $params);
	$location = $p['location'];
	$data = $p['data'];
	//echo "<pre>";print_r($data);echo "</pre>";
	if ($location == 'detail') {
		$disabledClickClass = '';
		$isAliveLocation    = true;
	} else {
		$disabledClickClass = 'disabledClick';
		$isAliveLocation    = false;
	}
	if ($location == 'fms_form') {
		$languageCode = \Fms\LanguageSelector::getCurrentLanguageCode();
		$fmsFormLanguageModifierClass = ($languageCode == 'en' ? '' : 'fmsForm_mRu');
		$formValues = $data['form']['values'];
		//echo '<pre>'; print_r($formValues); echo '</pre>';
	} else {
		$fmsData  = $data['fms'];
		$partData = $data['link_count_users'];
		$fmsButtonRole = \Fms\FmsButtonsRoleDetector::detect($fmsData, $isAliveLocation);
		
		#echo $fmsButtonRole;
		#$location == 'list_preview' ? $my_role = 'album' :  $my_role = $fmsButtonRole;
		/*if ($_GET['a']=='b'){
			echo '<pre>ROLE:';
			print_r($fmsButtonRole);
			echo '</pre>';
		}*/
		$showFmsButton = function ($appearance, $class = '') use ($fmsData, $fmsButtonRole, $disabledClickClass,$location) {
			global $USER;
			$my_role = ($location == 'fms_preview' ? 'im_going' :  $fmsButtonRole);
			if ($USER->IsAuthorized()) {
				FmsButton::show($fmsData['ID'], $my_role, $appearance, "$disabledClickClass $class", '', $fmsData);
			} else {
				FmsButton::showLink('/auth/?back_url='.urlencode($_SERVER['REQUEST_URI'].'?go=1&fms_id='.$fmsData['ID'].'&user=none'), $my_role, $appearance, "$disabledClickClass $class", '', $fmsData);
			}
		};
		//echo '<pre>'; print_r($fmsData); echo '</pre>'; exit();
	}
	$markError = function ($fieldCode) use ($data) {
		if (isset($data['form']['errors'][$fieldCode])) {
			echo 'fmsForm_error';
		}
	};

	if ($data['fms']['PROPERTY_TYPE_ENUM_ID'] == 1){
		$plusoruTitle = 'Flash Mob Shopping - ' . $p['fms_block_params']['data']['NAME'];
	}else{
		$plusoruTitle = $p['fms_block_params']['data']['NAME'];
	}

	if ($data['fms']['PROPERTY_TYPE_ENUM_ID'] == 1){ # распродажа
		//$discount = ($p['fms_block_params']['data']['PROPERTY_GOING_PEOPLE_VALUE'] < $p['fms_block_params']['data']['PROPERTY_BONUS_PEOPLE_VALUE']) ? $p['fms_block_params']['data']['PROPERTY_DISCOUNT_VALUE'] : $p['fms_block_params']['data']['possible_discount'];
        $discount = $p['fms_block_params']['data']['PROPERTY_DISCOUNT_VALUE'];

		$people = \Fms\Awards::getWordByNumber($p['fms_block_params']['data']['PROPERTY_REQUIRED_PEOPLE_VALUE'], GetMessage('SHARE_PEOPLE1'), GetMessage('SHARE_PEOPLE2'), GetMessage('SHARE_PEOPLE3'));
		$plusoruDescription = "{$p['fms_block_params']['data']['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['UF_TOWN']['NAME']}. {$p['fms_block_params']['data']['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['NAME']}. {$p['fms_block_params']['data']['PROPERTY_OPERATOR_VALUE']['NAME']}. ".GetMessage('SHARE_FOR_ACTIVATION_DISCOUNT')." {$discount}% ".GetMessage('SHARE_WE_COLLECT')." {$p['fms_block_params']['data']['PROPERTY_REQUIRED_PEOPLE_VALUE']} {$people}. ".GetMessage('SHARE_ALREADY_PEOPLE')." {$p['fms_block_params']['data']['PROPERTY_GOING_PEOPLE_VALUE']}." . GetMessage('SHARE_RETURN_RECEIPT');
	}elseif($data['fms']['PROPERTY_TYPE_ENUM_ID'] == 2){ # событие
		//$discount = ($p['fms_block_params']['data']['PROPERTY_GOING_PEOPLE_VALUE'] < $p['fms_block_params']['data']['PROPERTY_BONUS_PEOPLE_VALUE']) ? $p['fms_block_params']['data']['PROPERTY_DISCOUNT_VALUE'] : $p['fms_block_params']['data']['possible_discount'];
        $discount = $p['fms_block_params']['data']['PROPERTY_DISCOUNT_VALUE'];
		$people = \Fms\Awards::getWordByNumber($p['fms_block_params']['data']['PROPERTY_REQUIRED_PEOPLE_VALUE'], GetMessage('SHARE_PEOPLE1'), GetMessage('SHARE_PEOPLE2'), GetMessage('SHARE_PEOPLE3'));
		$plusoruDescription = "{$p['fms_block_params']['data']['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['UF_TOWN']['NAME']}. {$p['fms_block_params']['data']['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['NAME']}. {$p['fms_block_params']['data']['PROPERTY_OPERATOR_VALUE']['NAME']}. ".GetMessage('SHARE_EVENT_WE_COLLECT')." {$p['fms_block_params']['data']['PROPERTY_REQUIRED_PEOPLE_VALUE']} {$people}. ".GetMessage('SHARE_ALREADY_PEOPLE')." {$p['fms_block_params']['data']['PROPERTY_GOING_PEOPLE_VALUE']}.";
	}


    $fmsId = $p['fms_block_params']['data']['ID'];

	$APPLICATION->AddViewContent("plusoru_meta_title", "<meta property=\"og:title\" content=\"{$plusoruTitle}\">");
	$APPLICATION->AddViewContent("plusoru_meta_description", "<meta property=\"og:description\" content=\"{$plusoruDescription}\">");

    $plusoUrl = "http://{$_SERVER['HTTP_HOST']}/fms/$fmsId/?show_lang=".GetMessage('PLUSO_LANG');
    global $USER;
    if ($USER->IsAuthorized()){
        $plusoUrl .= '&user='.$USER->GetID();
    }

	$APPLICATION->AddViewContent("plusoru_meta_url", "<meta property=\"og:url\" content=\"{$plusoUrl}\">");

    //$plusoImage = \Fms\ShareImages::create()->getImageSrcByUserAgent($fmsId);
    //$plusoImageMeta = '<meta property="og:image" content="'.$plusoImage.'" /><link rel="image_src" href="'.$plusoImage.'" />';
    //$APPLICATION->AddViewContent("plusoru_meta_images", $plusoImageMeta);
		$plusoImage = "http://{$_SERVER['HTTP_HOST']}" .$p['fms_block_params']['data']["DETAIL_PICTURE"]["SRC"];
		$APPLICATION->AddViewContent("plusoru_meta_images", "<meta property=\"og:image\" content=\"{$plusoImage}\" /><link rel=\"image_src\" href=\"{$plusoImage}\" />");
		$APPLICATION->AddViewContent("plusoru_meta_image_width", '<meta property="og:image:width" content="'.$p['fms_block_params']['data']["DETAIL_PICTURE"]['WIDTH'].'"/>');
		$APPLICATION->AddViewContent("plusoru_meta_image_height", '<meta property="og:image:height" content="'.$p['fms_block_params']['data']["DETAIL_PICTURE"]['HEIGHT'].'"/>');

	$arFilter = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('rating_awards'), 'PROPERTY_MALL' => $p['data']['fms']['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['ID'], 'PROPERTY_MONTH'=>date('n'), 'PROPERTY_YEAR'=>date('Y'));
	$res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), array('ID', 'IBLOCK_ID', 'PROPERTY_FLASHMOBBER','PROPERTY_BEST_FRIEND', 'PROPERTY_PHOTOGRAPHER', 'PROPERTY_FAVORITE', 'PROPERTY_TOTAL'));

	$AWARD_FLASHMOBBER = '';
	$AWARD_PHOTOGRAPHER = '';
	$AWARD_FAVORITE = '';
	$AWARD_TOTAL = '';

	if($ob = $res->GetNextElement()) {
		$arFields = $ob->GetFields();

		$AWARD_FLASHMOBBER = $arFields['PROPERTY_FLASHMOBBER_VALUE'];
		$AWARD_BEST_FRIEND = $arFields['PROPERTY_BEST_FRIEND_VALUE'];
		$AWARD_PHOTOGRAPHER = $arFields['PROPERTY_PHOTOGRAPHER_VALUE'];
		$AWARD_FAVORITE = $arFields['PROPERTY_FAVORITE_VALUE'];
		$AWARD_TOTAL = $arFields['PROPERTY_TOTAL_VALUE'];
	}
	
	$current_user_link_count = 0;
	if ($USER->IsAuthorized()){
		$arFilter = Array("IBLOCK_ID"=>$s5BitrixCodes->getIblockId('referal'), "PROPERTY_USER"=>$USER->GetId(), "PROPERTY_FMS"=>$data['fms']['ID']);
		$res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), array('ID','IBLOCK_ID', 'PROPERTY_COUNT'));
		if($arFields = $res->GetNext()) {
			$current_user_link_count = $arFields['PROPERTY_COUNT_VALUE'];
		} 
	}

	?>
<?if ($location == 'fms_form') {?>
	<form id="fms_form" class="fmsForm <?=$fmsFormLanguageModifierClass?>" action="?" method="post" enctype="multipart/form-data">
	<input type="hidden" name="action" value="">
	<input type="hidden" name="id" value="<?=$data['id']?>">
	<?=bitrix_sessid_post()?>
<?}?>

<article class="fmsDetail">
	<?//echo '<pre>'; print_r($data['form']['errors']); echo '</pre>'; ?>
	<?/*if ($location == 'fms_form' and count($data['form']['errors']) > 0) {?>
			<div class="errors">
			<div><?=GetMessage('INVALID_FIELDS')?>:</div>
			<?foreach (array_keys($data['form']['errors']) as $errorFieldCode) {?>
				<div><?=GetMessage($errorFieldCode)?></div>
			<?}?>
		</div>
	<?}*/?>
	<?if ($location == 'detail') {?>
		<div class="pubWindow_rightColumn">
			<div class="fms_detail_link_count_block fmsDetail_going">
				<div class="blueUcTitle"><?=GetMessage('FMS_LEADER_TITLE')?></div>
				<ul>
					<?$i=0;foreach ($partData as $userData) {
						\Fms\HtmlParts\UserLinkCount::show(array(
							'modifier_class' => 'revealer_mFullWidth',
							'image'          => $userData['IMAGE'],
							'name'           => $userData['NAME'] .' '. $userData['LAST_NAME'],
							'first_name'           => $userData['NAME'],
							'last_name'           => $userData['LAST_NAME'],
							'location'       => $userData['PERSONAL_CITY'],
							'count'        	=> $userData['COUNT'],
							"i" => $i
						));
						$i++;
					}?>
				</ul>
			</div>
			<form class="fmsDetail_invite Std_form" method="post">
				<div class="fmsDetail_invite_text"><?=GetMessage('INVITE_FRIEND')?>:</div>
				<input type="text" name="email" class="fmsDetail_invite_field" placeholder="friend@gmail.com">
				<input type="submit" class="fmsDetail_invite_button" value="<?=GetMessage('INVITE')?>">
				<div class="fmsDetail_invite_message"></div>
			</form>
			
			<div class="ya-share2" data-services="viber,whatsapp,telegram" data-direction="vertical" data-description="<?=$plusoruDescription?>" data-image="<?=$plusoImage?>" data-title="<?=$plusoruTitle?>" data-url="<?=$plusoUrl?>"></div>
			
		</div>
	<?}?>
	<div class="pubWindow_content">
		<div class="pubWindow_content_padded">
			<div class="fmsDetail_info">
					<?if ($location != 'fms_form') {?>
						<div class="fmsDetail_titleBlock">
							<div class="fmsDetail_titleBlock_imGoing">
								<?$showFmsButton('203_73')?>
							</div>
							<h1 class="fmsDetail_titleBlock_title"><?=$fmsData['NAME']?></h1>
						</div>
					<?} else {?>
						<div>
							<div class="blueUcTitle"><?=GetMessage('NEW_FMS')?></div>
							<div class="fmsForm_titleBlock">
								<div class="fmsForm_titleBlock_block">
									<div class="fmsForm_titleBlock_left fmsForm_fmsTypeRadios">
										<input id="fms_form_fms_type_sale" class="stdForm_inputradio" type="radio" name="TYPE" value="sale"> <label for="fms_form_fms_type_sale"><?=GetMessage('SALE')?></label>
										&nbsp;&nbsp;&nbsp;
										<input id="fms_form_fms_type_event" class="stdForm_inputradio" type="radio" name="TYPE" value="event"> <label for="fms_form_fms_type_event"><?=GetMessage('EVENT')?></label>
									</div>
									<div class="fmsForm_titleBlock_right">
										<textarea name="NAME" cols="30" rows="10" class="stdForm_textarea fmsForm_fmsNameI fmsForm_required <?$markError('NAME')?>" placeholder="<?=GetMessage('FMS_NAME')?>"><?=$formValues['NAME']?></textarea>
									</div>
								</div>
								<br>
								<div class="fmsForm_titleBlock_block">
									<div class="fmsForm_titleBlock_left">
										<div class="stdForm_label"><?=GetMessage('CATEGORY')?>:</div>
									</div>
									<div class="fmsForm_titleBlock_right">
										<div class="stdForm_input">
											<select name="IBLOCK_SECTION_ID" class="stdForm_select">
												<?foreach ($data['sections'] as $sectionData) {?>
													<option value="<?=$sectionData['ID']?>" <?if($sectionData['ID']==$formValues['IBLOCK_SECTION_ID'])echo'selected'?>><?=$sectionData['NAME']?></option>
												<?}?>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?}?>
				<?\Fms\HtmlParts\FmsBlock::show($p['fms_block_params'])?>
				<div class="fmsDetail_share2">
					<div class="fmsDetail_share2_image"></div>
					<div class="fmsDetail_share2_rightBlock">
						<div class="fmsDetail_share2_rightBlock_topText">
							<span class="fmsDetail_share2_rightBlock_topText_left"><?=($p['fms_block_params']['data']['PROPERTY_TYPE_code']!='event')?"Пригласи друзей и верни 50% чека:":""?></span>
							<span class="fmsDetail_share2_rightBlock_topText_right"><?=($p['fms_block_params']['data']['PROPERTY_TYPE_code']!='event')?"Как это сделать":""?></span>
							<div id="fms_leader_how_to" class="topMenu_langPopup popup topMenu_popup onlySinglePopup">
								<div class="topMenu_popup_arrow topMenu_langPopup_arrow"></div>
								<div class="topMenu_langPopup_padded">
									Зарегистрируйся на флэшмоб. Набери наибольшее число переходов по своей ссылке на анонс этого флешмоба, 
									стань “Флэшмоб лидером” и тебе вернут 50% чека прямо в магазине.
								</div>
							</div>
						</div>
						
						<div class="fmsDetail_share2_pluso">
							

							<div class="pluso" data-options="big,square,line,horizontal,counter,theme=04" data-services="facebook,google,twitter,odnoklassniki,vkontakte" data-background="transparent"
								data-url="<?=$plusoUrl?>"
								data-title="<?=$plusoruTitle?>"
								data-description="<?=$plusoruDescription?>">
							</div>
							
							<?if ($location == 'fms_form' or $location == 'fms_preview') {?>
								<div class="fmsForm_plusoProtector"></div>
							<?}?>
						</div>
						<div class="fmsDetail_share2_rightBlock_bottomText">
							У тебя 
							<div class="fms_block_flash_mob_leader_link_count">
								<?=$current_user_link_count?>
							</div> 
							<?=\Fms\Awards::getWordByNumber($current_user_link_count, 'переходов', 'переход', 'перехода');?>
						</div>
					</div>
				</div>


                <?/*
                <div class="fmsDetail_delimiterLine2"></div>
                    <?
				\Fms\HtmlParts\FmsKingBlock::show(array(
					'mall_id' => $data['fms']['PROPERTY_OPERATOR_VALUE']['UF_OP_MALL']['ID'],
					'month'=>date('n'),
					'year'=>date('Y'),
				));
				?>

				<div class="fmsDetail_delimiterLine2"></div>*/?>

				<div class="fmsDetail_delimiterLine"></div>
				<div class="fmsDetail_descriptionBlock">
					<div class="fmsDetail_description">
						<div class="fmsDetail_description_padded">
							<div class="fmsDetail_description_title fmsDetail_strong"><?=GetMessage('DESCRIPTION')?></div>
							<?if ($location != 'fms_form') {?>
								<?=$fmsData['DETAIL_TEXT']?>
							<?} else {?>
								<textarea name="DETAIL_TEXT" class="stdForm_textarea fmsForm_descriptionI fmsForm_required <?$markError('DETAIL_TEXT')?>" id="fms_form_description_textarea" cols="30" rows="10"><?=$formValues['DETAIL_TEXT']?></textarea>
							<?}?>
						</div>
					</div>
					
				</div>
				<div class="fmsDetail_participation">
					<div class="fmsDetail_participation_brief">
						<span class="fmsDetail_strong"><?=GetMessage('PART_COND')?>:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?if ($location != 'fms_form') {?>
							<span class="fmsDetail_strong">
								<?=strtolower(GetMessage('PART_COND_'.$fmsData['PROPERTY_PART_COND_code']))?>
							</span>
						<?} else {?>
							<div class="fmsForm_partCondRadios">
								<input id="fms_form_part_cond_ticket" class="stdForm_inputradio" type="radio" name="PART_COND" value="ticket"> <label for="fms_form_part_cond_ticket"><?=GetMessage('PART_COND_ticket')?></label>
								&nbsp;&nbsp;&nbsp;
								<input id="fms_form_part_cond_action" class="stdForm_inputradio" type="radio" name="PART_COND" value="action"> <label for="fms_form_part_cond_action"><?=GetMessage('PART_COND_action')?></label>
							</div>
						<?}?>
					</div>
					<?if ($location != 'fms_form') {?>
						<?if ($fmsData['PROPERTY_PART_COND_code'] == 'action') {?>
							<div class="fmsDetail_participation_detailed">
								<?=$fmsData['PROPERTY_PART_ACTION_VALUE']?>
							</div>
						<?}?>
					<?} else {?>
						<div class="fmsDetail_participation_detailed">
							<input name="PART_ACTION" id="fms_form_part_cond_action_description_i" type="text" class="stdForm_inputtext fmsForm_w734 fmsForm_required <?$markError('EMBEDDED_VIDEO')?>" value="<?=$formValues['PART_ACTION']?>">
						</div>
					<?}?>
				</div>
				<?if (($location != 'fms_form' and $fmsData['PROPERTY_SPECIAL_MISSION_VALUE']) or $location == 'fms_form') {?>
					<div class="fmsDetail_special_mission">
						<span class="fmsDetail_strong"><?=GetMessage('SPECIAL_MISSION')?>:</span>
						&nbsp;&nbsp;
						<?if ($location != 'fms_form') {?>
							<?=$fmsData['PROPERTY_SPECIAL_MISSION_VALUE']?>
						<?} else {?>
							<input name="SPECIAL_MISSION" class="stdForm_inputtext fmsForm_specialMissionI fmsForm_w598" type="text" value="<?=$formValues['SPECIAL_MISSION']?>">
						<?}?>
					</div>
				<?}?>
				<div class="fmsDetail_place">
					<div class="fmsDetail_place_description">
						<div class="fmsDetail_place_title fmsDetail_strong"><?=GetMessage('PLACE')?></div>
						<div class="fmsDetail_place_description_image">
							<a class="colorbox" href="<?=$data['location']['place']['operator_original_image']['SRC']?>">
								<img alt="" src="<?=$data['location']['place']['operator_image']['SRC']?>">
							</a>
						</div>
						<div class="fmsDetail_place_description_text">
							<div>
								<?=GetMessage('MALL_NAME')?>:
								<?=$data['location']['place']['mall_name']?>
							</div>
							<div>
								<?=GetMessage('MALL_ADDRESS')?>:
								<?=$data['location']['place']['mall_address']?>
							</div>
							<div>
								<?=GetMessage('STORE_NAME')?>:
								<?=$data['location']['place']['operator_name']?>
							</div>
							<div>
								<?=GetMessage('STORE_LOCATION')?>:
								<?=$data['location']['place']['operator_location']?>
							</div>
						</div>
					</div>
					<div class="fmsDetail_map fmsDetail_mallMap">
						<div class="fmsDetail_map_name"><?=GetMessage('MALL_MAP')?></div>
						<div class="fmsDetail_map_content fmsDetail_mallMap_content">
							<img src="<?=$data['location']['mall_map']['SRC']?>" alt="">
						</div>
					</div>
					<div class="fmsDetail_map fmsDetail_cityMap">
						<div class="fmsDetail_map_name"><?=GetMessage('TOWN_MAP')?></div>
						<div class="fmsDetail_map_content fmsDetail_cityMap_content">
							<?
							$geoMapData = $data['location']['geo_map'];
							$coordsHash = array();
							$googleMapData = array(
								'google_lat' => $geoMapData['lat'],
								'google_lon' => $geoMapData['lon'],
								'google_scale' => 13,
								'PLACEMARKS' => array(
									array(
										'LAT' => $geoMapData['lat'],
										'LON' => $geoMapData['lon'],
										'TEXT' => $geoMapData['text'],
									),
								),
							);
							$APPLICATION->IncludeComponent(
								"bitrix:map.google.view",
								"",
								Array(
									"INIT_MAP_TYPE" => "ROADMAP",
									"MAP_DATA" => serialize($googleMapData),
									"MAP_WIDTH" => "807",
									"MAP_HEIGHT" => "319",
									"CONTROLS" => array("SMALL_ZOOM_CONTROL", "TYPECONTROL", "SCALELINE"),
									"OPTIONS" => array("ENABLE_SCROLL_ZOOM", "ENABLE_DBLCLICK_ZOOM", "ENABLE_DRAGGING", "ENABLE_KEYBOARD"),
									"MAP_ID" => ""
								),
							false
							);
							?>
						</div>
					</div>
				</div>
				<div class="fmsDetail_bottomControls">
					<div class="fmsDetail_shopSubscr">
						<div class="fmsDetail_shopSubscr_text"><?=GetMessage('SHOP_SUBSCRIPTION')?></div>
						<div id="shop_subscr_switch" class="fmsDetail_shopSubscr_switch fmsDetail_shopSubscr_switch<?=($data['subscription'] ? 'On' : 'Off')?> <?=$disabledClickClass?>" data-operator-id="<?=$fmsData['~PROPERTY_OPERATOR_VALUE']?>"></div>
					</div>
					<?if ($location == 'detail') {
						//FmsButton::show($fmsData['ID'], $fmsButtonRole, '288_73', "fmsDetail_bottomControls_imGoing $disabledClickClass");
						$showFmsButton('288_73', 'fmsDetail_bottomControls_imGoing');
					} elseif ($location == 'fms_preview') {?>
						<?//Мастер-предпросмотр: нижние кнопки?>
						<div class="fmsForm_bottomControls">
							<div class="fmsForm_bottomControls_note"><?=GetMessage('PUBLISH_NOTE')?></div>
							<div class="stdForm_blueButton publishButton">
								<img src="/images/small_white_megaphone.png" alt="" style="position:relative; top:-3px;">
								<?=GetMessage('PUBLISH')?>
							</div>
							<div class="stdForm_grayButton editButton"><?=GetMessage('EDIT')?></div>
						</div>
					<?}?>
				</div>
				
				<?if ($location == 'fms_form') {?>
					<?//Мастер-форма: кнопки справа?>
					<div class="fmsForm_rightColumn">
						<div class="fmsForm_rightColumn_buttons">
							<div class="fmsForm_rightColumn_button">
								<div class="stdForm_blueButton previewButton"><?=GetMessage('PREVIEW')?></div>
							</div>
							<div class="fmsForm_rightColumn_button">
								<div class="stdForm_grayButton saveDraftButton"><?=GetMessage('SAVE_DRAFT')?></div>
							</div>
							<div class="fmsForm_rightColumn_button">
								<div class="stdForm_grayButton deleteButton">
									<img src="/images/delete_button_cross.png" alt="">
									<?=GetMessage('DELETE')?>
								</div>
							</div>
							<?if ($data['form']['saved']) {?>
								<div class="fmsForm_rightColumn_button fmsForm_saved"><?=GetMessage('FMS_SAVED')?></div>
							<?}?>
						</div>
						<div class="fmsForm_rightColumn_fieldsNote">
							<div class="stdForm_field">
								<div class="stdForm_input"><div class="stdForm_inputtext fmsForm_w49 fmsForm_required"></div></div>
								&nbsp;
								<div class="stdForm_label"><?=GetMessage('REQUIRED_FIELD')?></div>
							</div>
							<div class="stdForm_field">
								<div class="stdForm_input"><div class="stdForm_inputtext fmsForm_w49"></div></div>
								&nbsp;
								<div class="stdForm_label"><?=GetMessage('OPTIONAL_FIELDS')?></div>
							</div>
							<div class="stdForm_field">
								<div class="stdForm_input"><div class="stdForm_inputtext fmsForm_w49 fmsForm_error"></div></div>
								&nbsp;
								<div class="stdForm_label"><?=GetMessage('ERROR_FIELDS')?></div>
							</div>
						</div>
					</div>
					<?//Мастер-форма: кнопки снизу?>
					<div class="fmsForm_bottomButtons">
						<div class="stdForm_blueButton previewButton"><?=GetMessage('PREVIEW')?></div>
						<div class="stdForm_grayButton saveDraftButton"><?=GetMessage('SAVE_DRAFT')?></div>
						<div class="stdForm_grayButton deleteButton">
							<img src="/images/delete_button_cross.png" alt="">
							<?=GetMessage('DELETE')?>
						</div>
						<?if ($data['form']['saved']) {?>
							<div style="height:11px;"></div>
							<div class="fmsForm_rightColumn_button fmsForm_saved"><?=GetMessage('FMS_SAVED')?></div>
						<?}?>
					</div>
				<?} elseif ($location == 'fms_preview') {?>
					<div class="fmsForm_rightColumn">
						<div class="fmsForm_rightColumn_buttons">
							<div class="fmsForm_rightColumn_button">
								<div class="stdForm_blueButton publishButton">
									<img src="/images/small_white_megaphone.png" alt="" style="position:relative; top:-3px;">
									<?=GetMessage('PUBLISH')?>
								</div>
							</div>
							<div class="fmsForm_rightColumn_button">
								<div class="stdForm_grayButton editButton"><?=GetMessage('EDIT')?></div>
							</div>
						</div>
					</div>
				<?}?>
			</div>
		</div>
	</div>
</article>

<?if ($location == 'fms_form') {?>
	</form>
<?}?>

<?//echo '<pre>'; var_dump($formValues); echo '</pre>'; exit();?>

<script>$(document).ready(function () {
<?/* ----- Detail or preview page ----- */?>
<?if ($location == 'detail' or $location == 'fms_preview') {?>
var awardsSelector     = $('#awards_selector_awards');
var mallPrizesSelector = $('#awards_selector_prizes');
var descriptions       = $('.fmsDetail_award_description');
var mallPrizes         = $('.fmsDetail_award_mallPrize');
var activeClasses      = 'fmsDetail_awards_selector_itemActive fmsDetail_strong';
var inactiveClasses    = 'fmsDetail_awards_selector_itemInactive';
$(awardsSelector).on('vclick', function () {
	if (!awardsSelector.hasClass('fmsDetail_awards_selector_itemActive')) {
		awardsSelector.addClass(activeClasses).removeClass(inactiveClasses);
		mallPrizesSelector.addClass(inactiveClasses).removeClass(activeClasses);
		descriptions.show();
		mallPrizes.hide();
	}
});
$(mallPrizesSelector).on('vclick', function () {
	if (!mallPrizesSelector.hasClass('fmsDetail_awards_selector_itemActive')) {
		awardsSelector.addClass(inactiveClasses).removeClass(activeClasses);
		mallPrizesSelector.addClass(activeClasses).removeClass(inactiveClasses);
		descriptions.hide();
		mallPrizes.show();
	}
});
<?}?>
<?/* ----- Detail page ----- */?>
<?if ($location == 'detail') {?>
var inviteField   = $('.fmsDetail_invite_field');
var inviteMessage = $('.fmsDetail_invite_message');
$('.fmsDetail_invite').submit(function (e) {
	e.preventDefault();
	$.post(
		'/ajax/friend_inviter.php',
		{
			fms_id: <?=$fmsData['ID']?>,
			email: inviteField.val()
		},
		function (data, status) {
			data = $.parseJSON(data);
			var messageHtml;
			if (typeof(data['infoMessage']) != 'undefined') {
				messageHtml = '<div class="fmsDetail_invite_message_info">' + data.infoMessage + '</div>';
			}
			else if (typeof(data['errorMessage']) != 'undefined') {
				messageHtml = '<div class="fmsDetail_invite_message_error">' + data.errorMessage + '</div>';
			}
			inviteMessage.html(messageHtml);
		}
	);
	inviteField.val('');
	return false;
});
<?if ($USER->IsAuthorized()) {?>
	$('#shop_subscr_switch').on('vclick', function () {
		var me = $(this);
		var operatorId = me.attr('data-operator-id');
		var dataToSend = {
			operator_id: operatorId,
			sessid:      '<?=bitrix_sessid()?>'
		};
		if (me.hasClass('fmsDetail_shopSubscr_switchOn')) {
			me.removeClass('fmsDetail_shopSubscr_switchOn').addClass('fmsDetail_shopSubscr_switchOff');
			dataToSend.is_subscribe = 0;
		} else {
			me.removeClass('fmsDetail_shopSubscr_switchOff').addClass('fmsDetail_shopSubscr_switchOn');
			dataToSend.is_subscribe = 1;
		}
		$.post(
			'/ajax/user_to_operator_subscription.php',
			dataToSend,
			function (data, status) {}
		);
	});
<?}?>
<?}?>
<?/* ----- Master page ----- */?>
<?if ($location == 'fms_form') {?>
var form = $('#fms_form');
var formActionI = $('#fms_form input[name=action]');
var fmsTypeRadios = $('#fms_form input[name=TYPE]');
var discountTypeRadios  = $('#fms_form input[name=discount_type]');

var line1Discount    = $('.fmsBlock_line1_discount');
var line1Arrow       = $('.fmsBlock_line1_arrow');
var line2Content     = $('#fms_form_line2_content');
var bonusLineContent = $('#fms_form_bonus_line_content');

//div с надписью «Пригласи друзей и верни 50% чека: Как это сделать»
var fmsDetail_share2_rightBlock_topText = $(".fmsDetail_share2_rightBlock_topText");

var brandBlock   = $('#fms_form_brand_block');
var brandButton  = $('#fms_form_brand_button');
var brandSelect  = $('#fms_form_brand_select');
var brandSelectI = $('#fms_form_brand_select input');

fmsTypeRadios.on('vclick', function () {
	var me = $(this);
	if (me.val() == 'sale') {
		//brandBlock.css('visibility', 'visible');
		line1Discount.css('visibility', 'visible');
		line1Arrow.css('visibility', 'visible');
		line2Content.show();
		bonusLineContent.show();
		fmsDetail_share2_rightBlock_topText.css('visibility', 'visible');//Показываем div с надписью «Пригласи друзей и верни 50% чека: Как это сделать»
		$("div.fmsBlock_paramName:contains('Необходимо людей')").html("Для активации скидки");//Меняем подпись
		
	} else {
		//brandBlock.css('visibility', 'hidden');
		line2Content.hide();
		line1Discount.css('visibility', 'hidden');
		line1Arrow.css('visibility', 'hidden');
		bonusLineContent.hide();
		fmsDetail_share2_rightBlock_topText.css('visibility', 'hidden');//Скрываем div с надписью «Пригласи друзей и верни 50% чека: Как это сделать»
		$("div.fmsBlock_paramName:contains('Для активации скидки')").html("Необходимо людей");//Меняем подпись
	}
});

<?if(isset($formValues['TYPE']) and $formValues['TYPE'] !== '') {?>
	$('#fms_form input[name=TYPE][value=<?=$formValues['TYPE']?>]').click();
<?} else {?>
	fmsTypeRadios.eq(0).click();
<?}?>

var partCondActionDescriptionI = $('#fms_form_part_cond_action_description_i');
var partCondRadios = $('#fms_form input[name=PART_COND]');
partCondRadios.on('vclick', function () {
	var me = $(this);
	if (me.val() == 'ticket') {
		partCondActionDescriptionI.hide();
	}
	else if (me.val() == 'action') {
		partCondActionDescriptionI.show();
	}
});

<?if(isset($formValues['PART_COND']) and $formValues['PART_COND'] !== '') {?>
	$('#fms_form input[name=PART_COND][value=<?=$formValues['PART_COND']?>]').click();
<?} else {?>
	partCondRadios.eq(0).click();
<?}?>

brandButton.on('vclick', function () {
	brandButton.hide();
	brandSelect.show();
	brandSelectI.focus();
});

var onBrandAcceptedHandler = function () {
	brandSelect.hide();
	if (brandSelectI.val() != '') {
		brandButton.html(brandSelectI.val());
	} else {
		brandButton.html(brandButton.attr('data-default-text'));
	}
	brandButton.show();
};

var brandsList = <?=json_encode($data['brand_names_list'])?>;
brandSelectI
.autocomplete({
	source: brandsList,
	select: function (event, ui) {
		brandSelect.hide();
		brandButton.html(ui.item.value);
		brandButton.show();
	}
})
.keypress(function (e) {
	if (e.which == 13) {
		brandSelect.hide();
		if (brandSelectI.val() != '') {
			brandButton.html(brandSelectI.val());
		} else {
			brandButton.html(brandButton.attr('data-default-text'));
		}
		brandButton.show();
	}
})
.blur(function () {
	brandSelect.hide();
	brandButton.show();
})
/*.keypress(function (e) {
	if (e.which == 13) {
		brandSelect.hide();
		if (brandSelectI.val() != '') {
			brandButton.html(brandSelectI.val());
		} else {
			brandButton.html(brandButton.attr('data-default-text'));
		}
		brandButton.show();
	}
})*/
.keydown(function (e) {
	if (e.which == 27) {
		brandSelect.hide();
		brandButton.show();
	}
})
;

var coverTypeRadios = $('#fms_form input[name=COVER_TYPE]');
var videoInput = $('#fms_form_cover_video_url_input');
coverTypeRadios.on('vclick', function () {
	var me = $(this);
	if (me.val() == 'image') {
		videoInput.hide();
	} else {
		videoInput.show();
	}
});

<?if(isset($formValues['COVER_TYPE']) and $formValues['COVER_TYPE'] !== '') {?>
	$('#fms_form input[name=COVER_TYPE][value=<?=$formValues['COVER_TYPE']?>]').click();
<?} else {?>
	coverTypeRadios.eq(0).click();
<?}?>

$('#fms_form_example_image_i').ser5InstantPreview({
	previewDiv: $('#fms_form_example_image_preview_div'),
	fakeButton: $('#fms_form_example_image_button')
});

$('#fms_form_date').datepick({
	dateFormat: '<?=$fmsLocalization->getDatepickerFormatString()?>'
});

$('#fms_form_time').inputmask('h:s');

var exampleOriginalPriceI            = $('#fms_form_example_original_price_i');
var exampleDiscountPercentI          = $('#fms_form_example_discount_percent_i');
var exampleDiscountedPriceParamValue = $('#fms_form_example_discounted_price');
var calcExampleDiscount = function () {
	var originalPrice   = parseFloat(exampleOriginalPriceI.val());
	var discountPercent = parseFloat(exampleDiscountPercentI.val());
	if (!isNaN(originalPrice) && !isNaN(discountPercent)) {
		var discount = originalPrice / 100 * discountPercent;
		discount *= 100;
		discount = Math.round(discount);
		discount /= 100;
		var discountedPrice = originalPrice - discount;
		discountedPrice = Math.round(discountedPrice*10) / 10;
		//console.log(originalPrice, discount, discountedPrice);
		exampleDiscountedPriceParamValue.html(discountedPrice);
	}
	else if (!isNaN(originalPrice) && isNaN(discountPercent)) {
		exampleDiscountedPriceParamValue.html(originalPrice);
	}
	else {
		exampleDiscountedPriceParamValue.html('0');
	}
};
exampleOriginalPriceI.change(calcExampleDiscount).keyup(calcExampleDiscount);
exampleDiscountPercentI.change(calcExampleDiscount).keyup(calcExampleDiscount);

var discountI                 = $('#fms_form input[name=discount]');
var bonusDiscountI            = $('#fms_form input[name=bonus_discount]');
var bonusedDiscountParamValue = $('#fms_form_example_bonused_discount');
var calcBonusedDiscount = function () {
	var discount      = parseFloat(discountI.val());
	var bonusDiscount = parseFloat(bonusDiscountI.val());
	if (!isNaN(discount) && !isNaN(bonusDiscount)) {
		var bonusedDiscount = discount + bonusDiscount;
		bonusedDiscountParamValue.html(bonusedDiscount);
	}
	else if (!isNaN(discount) && isNaN(bonusDiscount)) {
		bonusedDiscountParamValue.html(discount);
	}
	else {
		bonusedDiscountParamValue.html(0);
	}
};
discountI.change(calcBonusedDiscount).keyup(calcBonusedDiscount).change();
bonusDiscountI.change(calcBonusedDiscount).keyup(calcBonusedDiscount).change();

<?$errorBodyClass = isset($data['form']['errors']['DETAIL_TEXT']) ? 'error' : ''?>
tinyMCE.init({
	// General options
	mode : "exact",
	elements: "fms_form_description_textarea",
	theme : "advanced",
	//plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	plugins : "autolink,lists,pagebreak,style,layer,table,advhr,advimage,advlink,visualchars,nonbreaking",

	// Theme options
	/*
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	*/
	theme_advanced_buttons1 : "fontsizeselect,|,forecolor,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,removeformat",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,

	// Skin options
	skin : "o2k7",
	skin_variant : "silver",

	// Example content CSS (should be your site CSS)
	content_css : "/styles/tiny_mce.css",
	body_class : "userContent <?=$errorBodyClass?>",

	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "js/template_list.js",
	external_link_list_url : "js/link_list.js",
	external_image_list_url : "js/image_list.js",
	media_external_list_url : "js/media_list.js"
});

$('.previewButton').on('vclick', function () {
	formActionI.val('preview_fms');
	form.submit();
});
$('.saveDraftButton').on('vclick', function () {
	formActionI.val('save_draft');
	form.submit();
});
$('.deleteButton').on('vclick', function () {
	if (confirm('<?=GetMessage('CONFIRM_DELETE')?>')) {
		formActionI.val('delete_fms');
		form.submit();
	}
});
<?} /* <<< Edit form */?>
});</script>

<?}}?>
<script>
	$(window).load(function(){
		$("span.ya-share2__title").each(function(){
			var temp = $(this).text();
			$(this).text("Пригласи в "+temp);
		});
		$("li.ya-share2__item").each(function(){
			$(this).css('background', '#f3f3f3');
		});
		$("li.ya-share2__item").mouseout(function (){
			$(this).css('background', '#f3f3f3');
		});
		$("li.ya-share2__item").mouseover(function(){
			$(this).css('background', '#dbecff');
		});
	});	
</script>