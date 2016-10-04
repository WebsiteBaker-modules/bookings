function updateColor ( id )
{
    var elstyle = document.getElementById('sample'+id);
    var color   = document.getElementById('color'+id).value;
    elstyle.backgroundColor = color;
}
function checkInput() {
    if (!e) var e = window.event
	  if (e.keyCode) code = e.keyCode;
	  else if (e.which) code = e.which;
	  var character = String.fromCharCode(code);
	  alert(character);
}
if ( typeof jQuery != 'undefined' ) {
    jQuery(document).ready(function($) {
        var ch = '0123456789abcdefABCDEF';
        jQuery('#color').keypress(function (e) {
				    if (!e.charCode) k = String.fromCharCode(e.which);
            else k = String.fromCharCode(e.charCode);
						if (ch.indexOf(k) == -1) e.preventDefault();
						if (e.ctrlKey&&k=='v') e.preventDefault();
				});
    });
}
else {
    if ( typeof(document.body.addEventListener) ) {
        document.getElementById('color').addEventListener('onkeyup', checkInput, false);
    }
    else if ( typeof(document.body.attachEvent) ) {
        document.getElementById('color').attachEvent('onkeyup', checkInput);
    }
    else {
        document.getElementById('color').onkeyup=checkInput;
    }
}

