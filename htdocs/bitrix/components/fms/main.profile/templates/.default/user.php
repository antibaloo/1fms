<div class="profile userProfileForm">
	<form class="stdForm stdForm_arial" action="" method="post" enctype="multipart/form-data">
		<?=$arResult["BX_SESSION_CHECK"]?>
		<input type="hidden" name="lang" value="<?=LANG?>" />
		<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
		<div class="profile_fullName"><?=$USER->GetFullName()?></div>
		<div class="profile_editable">
			<div class="profile_image">
				<?$showImageBlock('PERSONAL_PHOTO', 'photo')?>
			</div>
			<div class="profile_fields">
				<div class="bitrixErrors"><?ShowError($arResult["strProfileError"]);?></div>
				<div class="stdForm_fieldsBlock">
					<?$showField('NAME')?>
					<?$showField('LAST_NAME')?>
					<?$showField('EMAIL')?>
					<?$showField('PERSONAL_GENDER')?>
					<?$showField('PERSONAL_BIRTHDAY')?>
					<?$showField('PERSONAL_CITY')?>
					<?$showField('PERSONAL_MOBILE')?>
				</div>
				<!--Зачем-то упоминается Андрей
				<div class="stdForm_fieldsBlock">
					<?=GetMessage('SOC_NET')?>: <a class="blue" href="">Andrey Lapin</a> (facebook)
				</div>
				-->
				<div class="stdForm_fieldsBlock">
					<input class="stdForm_blueButton" type="submit" name="save" value="<?=GetMessage('SAVE')?>">
				</div>
			</div>
		</div>
	</form>
</div>
