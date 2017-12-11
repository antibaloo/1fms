<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//echo '<pre>'; print_r($arParams); echo '</pre>'; exit();
//echo '<pre>'; print_r($arResult); echo '</pre>'; exit();
$ticketData      = $arResult['ticket'];
$fmsData         = $arResult['fms_page']['fms'];
$locationData    = $arResult['fms_page']['location'];
?>

<?if ($arResult['error']) {?>
	<div class="ticketPage">
		<?=$arResult['error']?>
	</div>
	<?
	return;
}?>



<div class="ticketPage">
	<div class="ticketPage_ticketBlock">
		<?\Fms\HtmlParts\TicketTitleAndBody::show(array(
			'modifier_class' => 'ticket_mPrintPage',
			'data'           => $arResult['ticket'],
		))?>
	</div>
	<?if ($arResult['fms_page']['fms']['PROPERTY_ADDRESS_VALUE'] == ""){?>
	<div class="ticketPage_mapBlock">
		<div>
			<div class="ticketPage_paramName ticketPage_mapBlock_title"><?=GetMessage('MALL_MAP')?></div>
		</div>
		<img src="<?=$locationData['mall_map']['SRC']?>" alt="" width="830px">
	</div>
	<?}else{?>
	<div class="ticketPage_mapBlock">
		<!--PROPERTY_LATITUDE_VALUE PROPERTY_LONGITUDE_VALUE-->
		<div class="ticketPage_paramName ticketPage_mapBlock_title"><?=GetMessage('PLACE')?>: <?=$arResult['fms_page']['fms']['PROPERTY_ADDRESS_VALUE']?></div>
		<div id="mymap" style="height:319px;width:830px;"></div>
	</div>
	<?}?>
	<div class="ticketPage_partCondBlock">
		<div class="ticketPage_paramName"><?=GetMessage('PART_COND')?>:</div>
		<div class="ticketPage_paramValue">
			<?=strtolower(GetMessage('PART_COND_'.$fmsData['PROPERTY_PART_COND_code']))?>
		</div>
	</div>
	<?if ($fmsData['PROPERTY_SPECIAL_MISSION_VALUE']) {?>
		<div class="ticketPage_specMissionBlock">
			<div class="ticketPage_paramName"><?=GetMessage('SPECIAL_MISSION')?>:</div>
			<div class="ticketPage_paramValue ticketPage_specMissionBlock_paramValue">
				<?=$fmsData['PROPERTY_SPECIAL_MISSION_VALUE']?>
			</div>
		</div>
	<?}?>
</div>

<script>
	$document.ready(function () {
		fixTicketHeight();
	});
	$(window).load(function(){
	
	var map = new google.maps.Map(document.getElementById('mymap'), {
		zoom: 16,
		center: {lat: <?=$arResult['fms_page']['fms']['PROPERTY_LATITUDE_VALUE']?>, lng: <?=$arResult['fms_page']['fms']['PROPERTY_LONGITUDE_VALUE']?>},
		scaleControl: false
	});
	
	marker = new google.maps.Marker({
		map: map,
		draggable: false,
		animation: google.maps.Animation.DROP,
		position: {lat: <?=$arResult['fms_page']['fms']['PROPERTY_LATITUDE_VALUE']?>, lng: <?=$arResult['fms_page']['fms']['PROPERTY_LONGITUDE_VALUE']?>},
	});
	
	var contentString = "<?=$arResult['fms_page']['fms']['PROPERTY_ADDRESS_VALUE']?>";
	var infowindow = new google.maps.InfoWindow({
		content: contentString
	});
	});
	
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js"></script>