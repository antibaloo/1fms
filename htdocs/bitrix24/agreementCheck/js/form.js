function $_GET(key) {
  var s = window.location.search;
  s = s.match(new RegExp(key + '=([^&=]+)'));
  return s ? s[1] : false;
}
function loadForm(){
  $(".myBackGround").html('<div class="container myContainer"><div class="row myRow"><div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 align-self-center"><form id="agreementCheckForm"><div class="formBorder"><center><span class="formHeader">РЕГИСТРАЦИЯ СОГЛАСИЯ</span></center><br><span class="formDescription">Для бесплатной диагностики зрения вашего ребенка зарегистрируйте свое письменное согласие, указав код активации.</span></div><div class="formBorder"><div class="form-group"><label for="agreementId"><span class="inputLabel">Введите код активации согласия: </span><span class="red"><sup>*</sup> (латинскими буквами)</span></label><input type="text" class="form-control inputUppercase" id="agreementId" placeholder="на отрывном талоне над QR-кодом, например: SJKLMY" maxlength="6"><input type="hidden" id="type" value="email"><input type="hidden" id="userId"><input type="hidden" id="name"><input type="hidden" id="surname"><input type="hidden" id="link"><input type="hidden" id="picture"></div><center>Подтвердить через</center><div id="socialButtons" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bCenter socialBlock"><button link="https://1fms.com/bitrix24/socialAuth/fAuth.php" class="mySocial btn btn-social-icon btn-facebook"><span class="fa fa-facebook"></span></button><button link="https://1fms.com/bitrix24/socialAuth/vAuth.php" class="mySocial btn btn-social-icon btn-vk"><span class="fa fa-vk"></span></button><button link="https://1fms.com/bitrix24/socialAuth/oAuth.php" class="mySocial btn btn-social-icon btn-odnoklassniki"><span class="fa fa-odnoklassniki"></span></button><button link="https://1fms.com/bitrix24/socialAuth/gAuth.php" class="mySocial btn btn-social-icon btn-google"><span class="fa fa-google"></span></button>&nbsp;&nbsp;&nbsp;<a class="btn btm-social-icon btn-yahoo"><i title="Показать поле email" id="noSocial" class="fa fa-envelope" aria-hidden="true"></i></a></div><br><div id="emailBlock" class="form-group"><label for="userEmail"><span class="inputLabel">E-mail: </span><span class="red"><sup>*</sup></span></label><input type="text" class="form-control" id="userEmail" placeholder="Введите адрес электронной почты"></div><div class="form-group"><div id="resultCheck"я></div></div><div id="registrationButton" class="col-xs-12 col-sm-12 col-md-6 col-lg-6 bCenter"><a href="#block3893" id="buttonCheck" class="btn btn-block btn-lg btn-success">Активация</a></div> </div></form></div><div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 hidden-sm-down align-self-center g-color-white"><div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 bCenter"></div></div></div></div>');
}
function successForm(){
  $(".myBackGround").html('<div class="container myContainer"><div class="row myRow"><div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 align-self-center"><form id="agreementCheckForm"><div class="formBorder"><div id="reloadButton" class="col-xs-12 col-sm-12 col-md-6 col-lg-6 bCenter"><h2>Спасибо, Ваше согласие активировано!</h2><br><button class="btn btn-block btn-lg btn-info">Активировать новое</button></div><br><br></div></form></div><div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 hidden-sm-down align-self-center g-color-white"><div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 bCenter"></div></div></div></div>');
}
$(document).ready(function () {
  loadForm();
  $("#buttonCheck").click(function(){
    $("#resultCheck").html('<center><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></center>');
    $.ajax({
      url: "https://1fms.com/bitrix24/agreementCheck/ajax.php",
      type: "POST",
      data: {
        agreementId: $("#agreementId").val(),
        email: $("#userEmail").val(),
        type: $("#type").val(),
        userId: $("#userId").val(),
        name: $("#name").val(),
        surname: $("#surname").val(),
        link: $("#link").val(),
        picture: $("#picture").val(),
      },
      dataType: "text",
      success: function (html) {
        if (html == "ACTIVATED") successForm();
        else $("#resultCheck").html(html);
      },
      error: function (html) {
        $("#resultCheck").html("<span style='color:red;'>Ошибка!<br>Обратитесь к администратору!</span>");
      },
    });
  });
  $("#noSocial").click(function(event){
    event.preventDefault();
    if ($("#emailBlock").is(':visible')) {
      $(this).attr("title","Показать поле email");
      $("#emailBlock").hide();
      $("#registrationButton").hide();
    }else{
      $(this).attr("title","Убрать поле email");
      $("#emailBlock").show();
      $("#registrationButton").show();
      
    }
  });

  $(".mySocial").click(function(event){
    event.preventDefault();
    location.href = $(this).attr("link")+"?agreementId="+$("#agreementId").val();
  
  });
  if ($_GET('agreementId')) $("#agreementId").val($_GET('agreementId'));
  if ($_GET('userId')) $("#userId").val($_GET('userId'));
  if ($_GET('email')) $("#userEmail").val($_GET('email'));
  if ($_GET('name')) $("#name").val(decodeURI($_GET('name')));
  if ($_GET('surname')) $("#surname").val(decodeURI($_GET('surname')));
  if ($_GET('link')) $("#link").val(decodeURI($_GET('link')));
  if ($_GET('picture')) $("#picture").val($_GET('picture'));
  if ($_GET('type')) {
    $("#type").val($_GET('type'));
    $("#buttonCheck").trigger("click");
  }
  $( "#agreementId" ).keydown(function( event ) {
    var lat = /[a-zA-Z]/;
    var rus = /[а-яА-Я]/; 
    if (lat.test(event.key)) $("#resultCheck").html("");
    if (rus.test(event.key)) {
      $("#resultCheck").html("<span style='color:red'>Ошибка: попытка ввода русского символа!</span>");
      event.preventDefault();
    }
  });
});