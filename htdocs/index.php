<?php
require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php';
if (\Fms\AjaxUtils::isAjaxBlockRequested('fms:fms.list')) {
	$APPLICATION->IncludeComponent('fms:fms.list', '', array());
	return;
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle(GetMessage('FMS_PAGE_TITLE_index'));

if ($USER->isAuthorized()) {
	$APPLICATION->SetPageProperty('main_css_class', 'main_mIndexPage');
} else {
	\Fms\HtmlParts\TopUnauthImages::show();
	$APPLICATION->SetPageProperty('main_css_class', 'main_mUnauth');
}
?>
<div class="indexRelPos">
	<?#$APPLICATION->IncludeComponent('fms:timeline', '', array('CACHE_TIME' => 3600))?>

	<?#$APPLICATION->IncludeComponent('fms:categories_menu', '', array('CACHE_TIME'=>3600))?>
	<?#\Fms\HtmlParts\TopScrollCovers::show()?>
	<?$APPLICATION->IncludeComponent('fms:photostream.overlay', '', array('CACHE_TIME'=>3600))?>

	<div class="indexContent" style="<?=$USER->isAuthorized() ? '':'padding-top:20px;'?>">
		<?\Fms\HtmlParts\Window::show(
			function () {\Fms\HtmlParts\LeftMenu::show();},
			function () use ($APPLICATION) {?>
				<div id="fmses_list_container">
					<?$APPLICATION->IncludeComponent('fms:fms.list', '', array(
						'IS_SHOW_ENDLESS_SCROLL_JS' => true,
					))?>
				</div>
				<div id="index_fmses_loading_block" class="loadingBlock">
					<div class="loadingBlock_loading">
						<div class="loadingBlock_image"></div>
						<div class="loadingBlock_text">Loading&hellip;</div>
					</div>
				</div>
			<?},
			false,
			array('modifier_class' => 'pubWindow_mIndexGroupPage indexPageCl')
		)?>
		<?/*
		<div class="indexContent_aboveBlock">
			<?\Fms\HtmlParts\FmsesTypeFilter::show();?>
		</div>
		*/?>
	</div>
</div>

<script>$document.ready(function () {
var photosScroll = $('#index_photostream_scroll');
OnScrollFixer.do({
	divsToFix:           $('#categories_menu, #left_menu, #photostream, #top_scroll_cover, #top_scroll_shade'),
	scrollTopFixValue:   <?=($USER->IsAuthorized() ? 152 : 448)?>/*,
	onScrollInFixedMode: function (scrollDiff) {
		if (scrollDiff > 0) {
			photosScroll.scrollTop(photosScroll.scrollTop() + scrollDiff);
		} else {
			if (photosScroll.scrollTop() > 0 && photosScroll.scrollTop() < scrollDiff) {
				photosScroll.scrollTop(0);
			}
			else if (photosScroll.scrollTop() > scrollDiff) {
				photosScroll.scrollTop(photosScroll.scrollTop() + scrollDiff);
			}
		}
	}*/
});
});</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>