<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="pgPhotos_tabContent pgOverall_overallTabContent pgPhotos_activeTabContent">
	<div class="photo_container">
<?foreach (array_merge($arResult['PHOTOS'], $arResult['VIDEOS']) as $item):?>
		<div class="pgPhoto c-123">
			<img src="<?=$item['PICTURE']['THUMB']?>" alt="">
			<?if ($item['PROPERTIES']['MEDIA_TYPE']['VALUE_XML_ID'] == 'video'):?>
				<a class="pgPhoto_overlay " href="<?=$item['PROPERTIES']['LINK']['VALUE']?>" target="_blank"></a>
			<?else:?>
				<a class="pgPhoto_overlay pgOverall_grouped cboxElement" data-album="<?=$item['PROPERTIES']['FMS']['VALUE']?>" data-id="<?=$item['ID']?>" href="<?=$item['PICTURE']['SRC']?>" data-url="<?=$item['URL']?>"data-title="<?=$arResult['TITLE']?>"data-description="<?=$arResult['DESCRIPTION']?>"></a>
			<?endif?>
				<div class="pgPhoto_plus <?=Fms\Utils::isLike($item['ID']) ? 'pgPhoto_plus_gray':''?>" data-id="<?=$item['ID']?>"></div>
		</div>
		<?endforeach?>
	</div>
</div>
<div class="pgPhotos_Points">
	<?=GetMessage('POINTS')?>: <?=$arResult['COUNT_LIKES']?>
</div>
<script type="text/javascript">
$(function(){
	window.pluso.start($('.plusoAlbum'));
	});
</script>
<script>
$document.ready(function () {
	$('#pg_photos').ser5Tabs({
		tabButtonsSelector:    '.pgPhotos_counter',
		tabContentsSelector:   '.pgPhotos_tabContent',
		activeTabButtonClass:  'pgPhotos_activeCounter',
		activeTabContentClass: 'pgPhotos_activeTabContent',
		tabButtonRelAttr:      'data-tab-content'
	});
	$('.pgPhotos_photosTabContent .pgPhoto_grouped').colorbox({rel:'pgPhoto_grouped'});
	$('.pgOverall_overallTabContent .pgOverall_grouped').colorbox({rel:'pgOverall_grouped'});
});
</script>