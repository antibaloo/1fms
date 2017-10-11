<?
require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php';

if (\Fms\AjaxUtils::isAjaxBlockRequested('fms:client_request')) {
	$APPLICATION->IncludeComponent("fms:client_request", "", Array());
	return;
}



require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle(GetMessage('FMS_PAGE_TITLE_about'));
global $currentLanguageCode;
?>

<div class="popup onlySinglePopup aboutPage_requestPopup">
	<?/*$APPLICATION->IncludeComponent(
		"fms:client_request",
		"",
		Array('AJAX_MODE' => 'Y'),
	false
	);*/?>
</div>

<?\Fms\HtmlParts\Window::show(
	function () {\Fms\HtmlParts\LeftMenu::show(array('modifier_class' => 'leftMenuFixed'));},
	function () use ($APPLICATION) {
		global $currentLanguageCode;
		?>
		<div class="aboutPage">
			<div class="aboutPage_left">
				<div class="aboutPage_title"><?=GetMessage('FMS_ABOUT_TITLE')?></div>
				<br><br><br>
				<div class="aboutPage_left_text userContent">
					<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						Array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "include/text/$currentLanguageCode.php",
							"EDIT_TEMPLATE" => ""
						),
					false
					);?>
				</div>
				<div class="aboutPage_left_socNets">
					<?$list = array(
						
						
					)?>
					<?foreach ($list as $data) {?>
						<div class="aboutPage_left_socNet <?=$data[0]?>">
							<a class="aboutPage_left_socNet_link" href=""><?=$data[1]?></a>
						</div>
					<?}?>
				</div>
				<div class="aboutPage_left_pdf">
					<div class="aboutPage_title">Flash Mob Shopping</div>
					<a href="files/overview/<?=$currentLanguageCode?>/fms_overview.pdf" class="aboutPage_strong"><?=GetMessage('FMS_ABOUT_DOWNLOAD_OVERVIEW')?></a>
				</div>
			</div>
			<div class="aboutPage_right">
				<?$APPLICATION->IncludeComponent(
					"fms:op_mall_entrance",
					"",
					Array(),
				false
				);?>
				<div class="aboutPage_request">
					<div class="aboutPage_request_text">
						<div class="aboutPage_strong"><?=GetMessage('FMS_ABOUT_FOR_BUSINESS')?></div>
						<?=GetMessage('FMS_ABOUT_TO_START_USING')?>
					</div>
					<div class="aboutPage_request_button"><?=GetMessage('FMS_ABOUT_SEND_REQUEST')?></div>
				</div>
			</div>
		</div>
	<?},
	false,
	array(
		'modifier_class' => 'pubWindow_mInnerNoRcPage',
	)
);?>

<script>$(document).ready(function () {
var popup = $('.aboutPage_requestPopup');
window.clientRequestPopup = popup;
$('.aboutPage_request_button').on('vclick', function (e) {
	e.stopPropagation();
	OnlySinglePopup.do(
		popup,
		function () {
			popup.html('');
			$.get(
				'',
				{ajax_block_id:'fms:client_request'},
				function (data, status) {
					popup.html(data);
					popup.css('margin-left', '-' + Math.round(popup.width() / 2) + 'px');
				}
			);
		}
	);
});
});</script>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
