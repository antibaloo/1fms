<?php
if (!substr_count($_REQUEST['DOMAIN'],'.bitrix24.ru')) die("ПНХ!");
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
//define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].'/include/fpdf/font/'); 
require($_SERVER['DOCUMENT_ROOT'].'/include/fpdf/fpdf.php');
//Функция перевода десятичного числа в 26ричный символьный код
function to26LetterCode($id){
  $digits26 = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
  $decimal = $id;
  $ostatok = 0;
  $stroka26 = "";
  while($decimal>0){
		$ostatok = $decimal % 26;
		$stroka26 = $digits26[$ostatok].$stroka26;
		$decimal = intval($decimal/26);
	}
	while (strlen($stroka26) < 6){
		$stroka26 = "A".$stroka26;
	}
	return $stroka26;
}
// подключаем модули
CModule::IncludeModule('iblock');
CModule::IncludeModule('highloadblock');
// необходимые классы
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
// $hlblock - это массив, 1 - hl блок AgreementRanges(Диапазоны согласий)
$hlblock   = HL\HighloadBlockTable::getById(1)->fetch();
$AgreementRanges   = HL\HighloadBlockTable::compileEntity($hlblock);
$AgreementRangesClass = $AgreementRanges->getDataClass();

// $hlblock - это массив, 2 - hl блок Agreements(Согласия)
$hlblock   = HL\HighloadBlockTable::getById(2)->fetch();
$Agreements   = HL\HighloadBlockTable::compileEntity( $hlblock );
$AgreementsClass = $Agreements->getDataClass();

//Формируем список диапазонов согласий для вычисления общего количества
$rsData = $AgreementRangesClass::getList(
  array(
    "select" => array('UF_COUNT'), //выбираем количества согласий в диапазонах
    "filter" => array(),
    "order" => array("ID"=>"DESC"), // сортировка по полю ID, будет работать только, если вы завели такое поле в hl'блоке
  )
);
$globalCount = 0;
while($arAgreement = $rsData->Fetch()){  $globalCount+=$arAgreement['UF_COUNT'];}
?>
<!doctype html>
<html lang="ru">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Генерация согласий на осмотр</title>
    <!-- Bootstrap -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="css/application.css" rel="stylesheet">
  </head>
  <body>
    <pre>
    <?php print_r($_REQUEST);?>
    </pre>
    <?php
    // use $_REQUEST data as OAuth 2.0 params to execute REST API calls
    
    $queryUrl = 'https://'.$_REQUEST['DOMAIN'].'/rest/user.current.json';
    
    // as user.current does not have any specific parameters we just set an access_token ("auth")
    $queryData = http_build_query(array(
      "auth" => $_REQUEST['AUTH_ID']
    ));
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_POST => 1,
      CURLOPT_HEADER => 0,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $queryUrl,
      CURLOPT_POSTFIELDS => $queryData,
    ));
    
    $result = json_decode(curl_exec($curl), true);
    curl_close($curl);
    switch ($_REQUEST['action']){
      case "generation":
        $AgreementRangesClass::add(
          array(
            'UF_DESCRIPTION' => $_REQUEST['argeementDesc'],
            'UF_GENERATION_DATE' => date("d.m.Y H:i:s"),
            'UF_COUNT' => $_REQUEST['agreementCount'],
            'UF_FIRST' => $globalCount+1,
            'UF_LAST' => $globalCount+$_REQUEST['agreementCount'],
            'UF_LEADS_CREATED' => 0
          )
        );
        break;
      case "leadCreate":
        $activeRange = $AgreementRangesClass::getRowById($_REQUEST['activeRange']);
        for($id = $activeRange['UF_FIRST']; $id <= $activeRange['UF_LAST'];$id ++){
          $queryUrl = 'https://ibalakov.bitrix24.ru/rest/1/ex50021nagym0e11/crm.lead.add.json/';
          //$queryUrl = 'https://zdz-online.bitrix24.ru/rest/16/opxrg0lm0un683us/crm.lead.add.json/';
          $queryData = http_build_query(
            array( 
              'fields' => array( 
                "TITLE" => "Согласие на профилактический осмотр",
                "NAME" => to26LetterCode($id),
                //"UF_CRM_1522138105" => to26LetterCode($id),
                "STATUS_ID" => "NEW",
                "OPENED" => "Y", 
                "ASSIGNED_BY_ID" => 1, //16 или $result['result']['ID']
              ), 
              'params' => array("REGISTER_SONET_EVENT" => "Y") )
          );
          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
          ));
          
          $result = json_decode(curl_exec($curl), true);
          curl_close($curl);
          //echo "<pre>"; print_r($result); echo "</pre>";
        }
        $AgreementRangesClass::update($_REQUEST['activeRange'],array('UF_LEADS_CREATED' => 1));
        break;
      case "printDoc":
        $pdf=new FPDF();
        $activeRange = $AgreementRangesClass::getRowById($_REQUEST['activeRange']);
        $pdf->SetAuthor('zdz-online.ru');
        $pdf->SetTitle('Range of agreements from '.$activeRange['UF_FIRST'].' to '.$activeRange['UF_LAST']);
        $pdf->SetFont('Times','B',12);
        for($id = $activeRange['UF_FIRST']; $id <= $activeRange['UF_LAST'];$id ++){
          $pdf->AddPage('P');
          $pdf->SetFontSize(12);
          $pdf->Image('http://chart.apis.google.com/chart?cht=qr&chs=300x300&chl=zdz-online.ru?agreementId='.to26LetterCode($id),174,13,25,25,'PNG');
          $pdf->Text(177,14,to26LetterCode($id));
          $pdf->SetFontSize(14);
          $pdf->Image('http://chart.apis.google.com/chart?cht=qr&chs=300x300&chl=zdz-online.ru?agreementId='.to26LetterCode($id),17,259,27,27,'PNG');
          $pdf->Text(20,260,to26LetterCode($id));
        }
        $pdf->Output('Диапазон_'.$activeRange['UF_FIRST'].'_'.$activeRange['UF_LAST'].'.pdf','F');
        $AgreementRangesClass::update($_REQUEST['activeRange'],array('UF_PDF_FILE' => 'Диапазон_'.$activeRange['UF_FIRST'].'_'.$activeRange['UF_LAST'].'.pdf'));
        break;
    }
    
    ?>
    <div id="app" class="container-fluid">
      <div class="bs-callout bs-callout-info">
        <h4>Список созданных диапазонов согласий</h4>
        <p>Текущий пользователь: <span id="user-name"><?=$result['result']['NAME'].' '.$result['result']['LAST_NAME'];?></span></p>
        <form id="generationForm" action="" method="POST">
          <input type="hidden" name="AUTH_ID" value="<?=$_REQUEST['AUTH_ID']?>">
          <input type="hidden" id="action" name="action">
          <input type="hidden" id="globalCount" name="globalCount" value="<?=$globalCount?>">
          <input type="hidden" id="activeRange" name="activeRange">
          <div class="form-row">
            <div class="form-group col-md-8">
              <input id="argeementDesc" name="argeementDesc" type="text" class="form-control" placeholder="Описание диапазона">
            </div>
            <div class="form-group col-md-2">
              <input id="agreementCount" name="agreementCount" type="number" class="form-control" placeholder="Количество" min="10" step="10">
            </div>
            <div class="form-group col-md-2">
              <button id="generateAgreement" type="button" class="btn btn-primary">Генерация</button>
            </div>
          </div>
          <div id="errors" class="form-row errors"></div>
        </form>
      </div>
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="panel panel-default">
            <table class="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Описание</th>
                  <th>Дата генерации</th>
                  <th>Кол-во</th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr>
<?if ($globalCount > 0){
  $rsData = $AgreementRangesClass::getList(
    array(
      "select" => array('*'), //выбираем все поля
      "filter" => array(),
      "order" => array("ID"=>"DESC"), // сортировка по полю ID, будет работать только, если вы завели такое поле в hl'блоке
    )
  );
  while($arAgreementRange = $rsData->Fetch()){
?>
                <tr>
                  <th scope="row"><?=$arAgreementRange['ID']?></th>
                  <td><?=$arAgreementRange['UF_DESCRIPTION']?></td>
                  <td><?=$arAgreementRange['UF_GENERATION_DATE']?></td>
                  <td title="с <?=to26LetterCode($arAgreementRange['UF_FIRST'])?> по <?=to26LetterCode($arAgreementRange['UF_LAST'])?>"><?=$arAgreementRange['UF_COUNT']?></td>
                  <td><button range-id="<?=$arAgreementRange['ID']?>" type="button" class="btn btn-warning leadCreate" <?=($arAgreementRange['UF_LEADS_CREATED'])?"disabled":""?>><?=($arAgreementRange['UF_LEADS_CREATED'])?"Лиды созданы":"Создать лиды"?></button></td>
<?
    if (strlen($arAgreementRange['UF_PDF_FILE'])){
      echo '<td><a href="'.$arAgreementRange['UF_PDF_FILE'].'" target="_blank">Скачать бланк</a></td>';
    }else{
      echo '<td><button range-id="'.$arAgreementRange['ID'].'" type="button" class="btn btn-info printDoc">Печать</button></td>';
    }
?>                  
                </tr>
<?
  }
}else{
  
}?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//api.bitrix24.com/api/v1/"></script>
    <script>
      $(document).ready(function () {
        var currentSize = BX24.getScrollSize();
        minHeight = currentSize.scrollHeight;
        if (minHeight < 400) minHeight = 400;
        BX24.resizeWindow(document.getElementById("app").offsetWidth, minHeight);
      });
      $("#generateAgreement").click(function(){
        if ($("#agreementCount").val() == "" && $("#argeementDesc").val() == ""){
          $("#errors").html("Все поля обязательны для заполнения!");
        }else{
          $("#action").val("generation");
          $("#generationForm").submit();
        }
      });
      $(".leadCreate").click(function(){
        $("#action").val("leadCreate");
        $("#activeRange").val($(this).attr("range-id"));
        $("#generationForm").submit();
      });
      $(".printDoc").click(function(){
        $("#action").val("printDoc");
        $("#activeRange").val($(this).attr("range-id"));
        $("#generationForm").submit();
      });
    </script>
  </body>
</html>