var page = require('webpage').create();
var system = require('system');
var params = [];
if (system.args.length === 1) {
    console.log('Try to pass some args when invoking this script!');
} else {
    system.args.forEach(function (arg, i) {	params[i] = arg;});
}

console.log(new Date());

console.log('start');

var url = params[1],
		img =  params[2];




console.log('start open ' + url);
page.settings.resourceTimeout = 5000;
page.open(url, function (status) {
	console.log('open success');
	if (status !== 'success') {
		console.log('Unable to access network: ' + status);
	} else {
		var left = 0,
			top = 0,
			width = 0,
			height = 0;

		console.log('start evaluate');
		var ua = page.evaluate(function() {
			if (!$('.fmsBlock_mDetail').length){
				return false;
			}
			var left = $('.fmsBlock_mDetail').offset().left;
			var top = $('.fmsBlock_mDetail').offset().top;
			var width = $('.fmsBlock_mDetail').width();
			var height = $('.fmsBlock_mDetail').height();
			return {left:left, top: top, width:width, height:height}
		});
		if (ua !== false){
			/*console.log('evaluate success');
			console.log("top: "+ua.top);
			console.log("left: "+ua.left);
			console.log("width: "+ua.width);
			console.log("height: "+ua.height);*/
			page.clipRect = {
				top:    ua.top,
				left:   ua.left,
				width:  ua.width+2+10,
				height: ua.height+2
			};
			
			console.log('start render');
			page.render(img);
			console.log('render success');
		}
	}

    phantom.exit();
});
