<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
?>
<a href="<?='http://'.$_SERVER['HTTP_HOST'].$uri_parts[0]."?album=".$arResult['RANDOM_ALBUM']?>"title="<?= GetMessage("MALL_ALBUMS")?>">
	<div class="fmsDetail_prizeOfMonth">
		<span style="font-weight:bold;font-size:16px;font-family: Arial;"><?= GetMessage("PRIZE_HEADER")?></span>
		<div class="prizeImageWrapper">
			<div class="prizeImage">
				<img src="<?=$arResult['prize']['DETAIL_PICTURE']['SRC']?>" width="auto" height="304px">
				<div class="logoText"></div>
				<div class="mallLogo"><img src="<?=$arResult['mallLogo']['SRC']?>" width="auto" height="74px"></div>
				<div class="prizeText">
					<div class="prizeSubText"><?=$arResult['prize']['DETAIL_TEXT']?></div>
				</div>
			</div>
			<img class="flashMobKing" src="/images/flashmobking.png" width="86px" height="86px">
		</div>
		<div class="prizeMessage"><?= GetMessage("PRIZE_MESSAGE")?></div>
		<div class="prizeLeader">
			<span style="font-weight:bold;font-size:14px;font-family: Arial;"><?= GetMessage("PRIZE_LEADER")?></span>
			<div>
				<div class="leaderPhoto"><img src="<?=$arResult['leader']['PHOTO']['SRC']?>" width="50px" height="50px"></div>
				<div class="leaderName">
					<?=$arResult['leader']['LAST_NAME']?> <?=$arResult['leader']['NAME']?><br>
					<?=GetMessage("LEADER_RESULT")?> <?=$arResult['leader']['COUNT']?>
				</div>
				<div class="clearFloat"></div>
			</div>
		</div>
		<?//echo "<pre>";print_r($arResult);echo "</pre>";?>
	</div>
</a>