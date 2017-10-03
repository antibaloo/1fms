<?if ($_GET['album']):?>
	<script>
		$(function(){
			<?if ($_GET['photo']):?>
				open_page_photo(<?=$_GET['album']?>, <?=$_GET['photo']?>)
			<?else:?>
				open_page_album(<?=$_GET['album']?>);
			<?endif?>
		});
	</script>
<?endif?>
<script>
	console.log(<?=$arResult['COVER']?>);
</script>