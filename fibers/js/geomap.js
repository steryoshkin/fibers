$(document).ready(function() {
	$("button#find-close").live('click', function(event) {
		if(!$("div.panel-sloc").is(':hidden')) {
			$("input#sloc").val();
			$("div.panel-sloc").hide();
			deactivateToggleControls();
			tb.controls[0].activate();
		}
		return false;
    });
	
	$("input#sloc").keydown(function(e) {
       //console.log('keyup called');
       var code = e.keyCode || e.which;
       if (code == '9' || code == '13') {
    	   sloc($("input#sloc").val());
       }
       /*if (code == '32') {
    	   $("input#sloc").val(Auto($("input#sloc").val()));
       }*/
    });

	$("button#sloc_button").live('click', function(event) {
		sloc($("input#sloc").val());
		//alert($("input#sloc").val());
		return false;
    });

	$("button#sloc_clear_button").live('click', function(event) {
		$("input#sloc").val('');
		//map.setCenter(new OpenLayers.LonLat('87.109965,53.757281'),16);
		return false;
    });

    // Load countries then initialize plugin:
    $.ajax({
        url: './engine/backend.php?act=get_json&street',
        dataType: 'json'
    }).done(function (source) {

    	//allert(source);
        //var streetArray = $.map(source, function (value, key) { return { value: value, data: key }; });
    	var streetArray = $.map(source, function (value, key) { return { value: key, data: key }; });

        // Инициализируем autocomplete:
        $('#sloc').autocomplete({
            lookup: streetArray,
			onSelect: function (suggestion) {
                $('#sloc').val(suggestion.data+' ');
                $('input#sloc').focus();
            }
        });
    });

});

function Auto(str) {
	var search = new Array(
			"й","ц","у","к","е","н","г","ш","щ","з","х","ъ",
			"ф","ы","в","а","п","р","о","л","д","ж","э",
			"я","ч","с","м","и","т","ь","б","ю"
	);
	var replace = new Array(
			"q","w","e","r","t","y","u","i","o","p","\\[","\\]",
			"a","s","d","f","g","h","j","k","l",";","'",
			"z","x","c","v","b","n","m",",","\\."
	);

	for (var i = 0; i < replace.length; i++) {
		var reg = new RegExp(replace[i], 'mig');
		str = str.replace(reg, function (a) {
			return a == a.toLowerCase() ? search[i] : search[i].toUpperCase();
		})
	}
	return str
}

function popupClear() {
    while( map.popups.length ) {
         map.removePopup(map.popups[0]);
    }
}

function sloc(value) {
    if(!value) {
        alert('Введите адрес для поиска');
        return false;
    }
    $.ajax({
        //url: 'wms_proxy.php?geocode_addr='+$("select#city").val()+','+value,
    	url: 'wms_proxy.php?geocode_addr='+value+', ',
        dataType: "html",
        type: "GET",
        success: function(data) {
	        if(data) {
		        //alert(data);
		        eval(data);
				//history.pushState(1, "", "?lat="+lat+"&lon="+lon+"&zoom=18");
		        var layers = map.getLayersBy("visibility", true);
		        window.location.href="?lat="+lat+"&lon="+lon+"&zoom=18&marker"+"&baselayer="+layers[0].name+"&find";
	        }
        }
    });
    //return;
};

function get_org(addr) {
	$.ajax({
		url: 'wms_proxy.php?get_org='+addr,
		dataType: "html",
		type: "GET",
		success: function(data) {
			if(data) {
				alert(data);
				//alertify.log(data, '', 0);
				//eval(data);
				//var layers = map.getLayersBy("visibility", true);
				//window.location.href="?lat="+lat+"&lon="+lon+"&zoom=18&marker"+"&baselayer="+layers[0].name+"&find";
			} else {
				alertify.log('информация отсутствует', '', 0);
			}
		}
	});
};
