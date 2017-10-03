<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$isIndexPage = S5_Bitrix_IndexPageDetector::detect();
/* @global $APPLICATION CMain */
?><!DOCTYPE html>
<html>
	<head>
		<title><?$APPLICATION->ShowTitle()?></title>
		<?$APPLICATION->ShowViewContent("plusoru_meta_title");?>
		<?$APPLICATION->ShowViewContent("plusoru_meta_description");?>
		<?$APPLICATION->ShowViewContent("plusoru_meta_images");?>
		<?$APPLICATION->ShowViewContent("plusoru_meta_image_width");?>
		<?$APPLICATION->ShowViewContent("plusoru_meta_image_height");?>
    <?$APPLICATION->ShowViewContent("plusoru_meta_url");?>
		<?$APPLICATION->ShowViewContent("plusoru_meta_type");?>

		
		<meta name="viewport" content="width=1200">
		<?/* --- styles --- */?>
		<link href="/styles/jquery-ui/smoothness/jquery-ui.min.css" type="text/css" rel="stylesheet" />
		<link href="/styles/jquery-datepick/jquery.datepick.css" type="text/css" rel="stylesheet" />
		<link rel="stylesheet" href="/styles/colorbox/colorbox.css">
		<link rel="stylesheet" href="/styles/_to_top.css">
		<link rel="stylesheet" href="/js/jquery-custom-scrollbar/jquery.custom-scrollbar.css">
		<?/* --- javascript --- */?>
		<script src="/js/jquery-1.9.1.min.js"></script>
		<script src="/js/langs.js"></script>
		<script src="/js/jquery.mobile-1.3.1.custom.min.js"></script>
		<script src="/js/jquery-ui-1.10.3.custom.min.js"></script>
		<script src="/js/jquery.datepick.min.js"></script>
		<script src="/js/tiny_mce/tiny_mce.js"></script>
		<script src="/js/jquery.colorbox.js"></script>
		<script src="/js/jquery.inputmask.bundle.min.js"></script>
		<?/*<script src="/js/jquery.jcarousel.min.js"></script>*/?>
		<?/*<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU"></script>*/?>
		<script type="text/javascript" src="/js/jquery.placeholder.min.js"></script>
		<?/*<script src="/js/jquery.scrollTo-min.js"></script>
		<script src="/js/to_top.js"></script>*/?>

        <script src="/js/upload_media.js"></script>
        <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
        <script src="/js/fileupload/js/vendor/jquery.ui.widget.js"></script>
        <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
        <script src="/js/fileupload/js/jquery.iframe-transport.js"></script>
        <!-- The basic File Upload plugin -->
        <script src="/js/fileupload/js/jquery.fileupload.js"></script>


		<script src="/js/likes.js"></script>
		<script src="/js/swfobject/swfobject.js"></script>
		<script src="/js/media.archive.js"></script>
		<script src="/js/modernizr.custom.js"></script>
		<script type="text/javascript" src="/js/js.js"></script>
		
		<script type="text/javascript">
			var isLteIe7 = false;
			var $document = $(document);
			var $window = $(window);
		</script>
		<!--[if lte IE 7]>
			<script type="text/javascript">isLteIe7 = true;</script>
			<link href="/styles/ie.css" rel="stylesheet" type="text/css">
		<![endif]-->
		<!--[if IE 6]>
			<script src="/js/DD_belatedPNG_0.0.8a-min.js" type="text/javascript"></script>
			<script type="text/javascript">
				DD_belatedPNG.fix('.png_fix, .Dcorner_l, .Dcorner_r');
			</script>
		<![endif]-->
		<!--[if lt IE 9]>
			<script src="/js/html5shiv.js"></script>
		<![endif]-->
		<?$APPLICATION->ShowHead();?>


	</head>
	<body>
		<?
		$APPLICATION->SetAdditionalCSS('/styles/styles.css');
		$APPLICATION->SetAdditionalCSS('/styles/upload_media.css');
		if (isset($_GET['tahoma'])) {
			$APPLICATION->SetAdditionalCSS('/styles/tahoma.css');
		}
		$APPLICATION->AddHeadScript('/js/js.js');
		$APPLICATION->AddHeadScript('/js/flowplayer.js');
		$APPLICATION->AddHeadScript('/js/jquery-custom-scrollbar/jquery.custom-scrollbar.min.js');
		$APPLICATION->ShowPanel();
		?>