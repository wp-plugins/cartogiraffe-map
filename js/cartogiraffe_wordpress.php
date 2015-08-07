jQuery(function() {
	cartogiraffeLoad<?=intval($_GET["id"])?>();
	L.control.scale().addTo(kartogiraffe_map<?=intval($_GET["id"])?>);
	

	jQuery('#kartogiraffe_shortlink<?=intval($_GET["id"])?>').on("click", function () {
	   jQuery(this).select();
	});	
	kartogiraffe_lastlatlon<?=intval($_GET["id"])?>='';

	kartogiraffe_map<?=intval($_GET["id"])?>.on("moveend dragend layeradd",function (e) {
		cartogiraffeCreateShortCode<?=intval($_GET["id"])?>();
	});
	
	jQuery('.kartogiraffe_search_input').on('keyup keypress', function(e) {
	  var code = e.keyCode || e.which;
	  if (code == 13) { 
		 kartogiraffe_search<?=intval($_GET["id"])?>();
		e.preventDefault();
		return false;
	  }
	});
	
	jQuery("#kartogiraffe_relation_input<?=$_GET["id"]?>, #kartogiraffe_relation_id<?=$_GET["id"]?>").on('keyup keypress', function(e) {
	  var code = e.keyCode || e.which;
	  if (code == 13) { 
		 kartogiraffe_search_relation<?=intval($_GET["id"])?>();
		e.preventDefault();
		return false;
	  }
	});
	
    jQuery('#kartogiraffe_relation_colorx<?=intval($_GET["id"])?>').wpColorPicker({
		change: function(event,ui) {
			kartogiraffe_relation_color<?=intval($_GET["id"])?>=ui.color.toString().replace('#','');
			cartogiraffeLoad<?=intval($_GET["id"])?>();
			
			window.setTimeout('cartogiraffeCreateShortCode<?=intval($_GET["id"])?>(true);',400);
		}
    });
	
});

function cartogiraffeCreateShortCode<?=intval($_GET["id"])?>(force) {
		var latlon=kartogiraffe_map<?=intval($_GET["id"])?>.getCenter();
		
		var ll=latlon.lat+latlon.lng+kartogiraffe_map<?=intval($_GET["id"])?>.getZoom()+kartogiraffe_type<?=intval($_GET["id"])?>;
		
		if(force||(ll!=kartogiraffe_lastlatlon<?=intval($_GET["id"])?>)) {
			kartogiraffe_lastlatlon<?=intval($_GET["id"])?>=ll;
			
			kartogiraffe_data<?=intval($_GET["id"])?>='';
			jQuery.ajax({
					url: 'http://www.kartogiraffe.de/ajax.whereami.php?lat='+latlon.lat+'&lon='+latlon.lng
				}).done(function(data) {
				 kartogiraffe_data<?=intval($_GET["id"])?>=data;
				 
				 var code="[cartogiraffe_map center='"+latlon.lat+","+latlon.lng+"' relation='"+jQuery('#kartogiraffe_relation_id<?=$_GET["id"]?>').val()+"' relationcolor='"+jQuery('#kartogiraffe_relation_colorx<?=$_GET["id"]?>').val()+"' relationwidth='"+jQuery('#kartogiraffe_relation_widthx<?=$_GET["id"]?>').val()+"' zoom='"+kartogiraffe_map<?=intval($_GET["id"])?>.getZoom()+"' type='"+kartogiraffe_type<?=intval($_GET["id"])?>+"' width='100%' height='450px' id='"+Math.round(Math.random()*1000000000)+"' data='"+kartogiraffe_data<?=intval($_GET["id"])?>+"' scrollwheel='"+jQuery('#kartogiraffe_scroll<?=intval($_GET["id"])?>').prop('checked')+"' search='"+jQuery('#kartogiraffe_searchfield<?=intval($_GET["id"])?>').prop('checked')+"' changetype='"+jQuery('#kartogiraffe_change<?=intval($_GET["id"])?>').prop('checked')+"']";
			
				jQuery('#kartogiraffe_shortlink<?=intval($_GET["id"])?>').val(code).click();
				jQuery('#kartogiraffe_shortlink_div<?=intval($_GET["id"])?>').show("slow");
				
			});
			

		}
}

function cartogiraffeStandard<?=intval($_GET["id"])?>() {
	kartogiraffe_type<?=intval($_GET["id"])?>='';
	cartogiraffeLoad<?=intval($_GET["id"])?>();
}

function cartogiraffeBike<?=intval($_GET["id"])?>() {
	kartogiraffe_type<?=intval($_GET["id"])?>='bicycle';
	cartogiraffeLoad<?=intval($_GET["id"])?>();
}

function cartogiraffeTransport<?=intval($_GET["id"])?>() {
	kartogiraffe_type<?=intval($_GET["id"])?>='transport';
	cartogiraffeLoad<?=intval($_GET["id"])?>();
}

function cartogiraffeHiking<?=intval($_GET["id"])?>() {
	kartogiraffe_type<?=intval($_GET["id"])?>='hiking';
	cartogiraffeLoad<?=intval($_GET["id"])?>();
}

function kartogiraffe_search<?=intval($_GET["id"])?>() {
  jQuery.getJSON('http://nominatim.openstreetmap.org/search?format=json&q=' + jQuery('#kartogiraffe_search_input<?=intval($_GET["id"])?>').val(), function(data) {
	var items = [];
	jQuery.each(data, function(key, val) {
		items.push("<li><a href='#' onclick='kartogiraffe_panto<?=intval($_GET["id"])?>(" + val.lat + ", " + val.lon + ");return false;'>" + val.display_name + '</a></li>');
	});
	
	jQuery('#kartogiraffe_results<?=intval($_GET["id"])?>').html("");
    if (items.length != 0) {
		jQuery('<p>', { html: " " }).appendTo('#kartogiraffe_results<?=intval($_GET["id"])?>');
		jQuery('<ul/>', {
			'class': 'my-new-list',
			html: items.join('')
			}).appendTo('#kartogiraffe_results<?=intval($_GET["id"])?>');
	} else {
		jQuery('<p>', { html: "0 results" }).appendTo('#kartogiraffe_results<?=intval($_GET["id"])?>');
	}
  });
}

function kartogiraffe_search_relation<?=intval($_GET["id"])?>() {
  jQuery.getJSON('http://www.kartogiraffe.de/ajax.searchrelations.php?search=' + jQuery('#kartogiraffe_relation_input<?=intval($_GET["id"])?>').val().replace(/%/g,"*"), function(data) {
	var items = [];
	jQuery.each(data, function(key, val) {
		var info=new Array;
		jQuery.each(val.info,function(ikey,ival) { info[info.length]=ival.k+':'+ival.v;});
		
		items.push("<li><a href='#' onclick='jQuery(\"#kartogiraffe_relation_id<?=intval($_GET["id"])?>\").val("+val.id+");kartogiraffe_relation<?=intval($_GET["id"])?>="+val.id+";kartogiraffe_relation_color<?=intval($_GET["id"])?>=\""+jQuery("#kartogiraffe_relation_colorx<?=intval($_GET["id"])?>").val().replace("#","")+"\";kartogiraffe_relation_width<?=intval($_GET["id"])?>=\""+jQuery("#kartogiraffe_relation_widthx<?=intval($_GET["id"])?>").val()+"\";cartogiraffeLoad<?=intval($_GET["id"])?>(); kartogiraffe_map<?=intval($_GET["id"])?>.panTo([parseFloat("+val.point[1]+"),parseFloat("+val.point[0]+")]).setZoom(14);jQuery(\"#kartogiraffe_relation_results<?=intval($_GET["id"])?>\").html(\"\");return false;'>" + val.name + '</a>('+info.join('; ').substring(0,200)+')</li>');
	});
	
	jQuery('#kartogiraffe_relation_results<?=intval($_GET["id"])?>').html("");
    if (items.length != 0) {
		jQuery('<p>', { html: " " }).appendTo('#kartogiraffe_relation_results<?=intval($_GET["id"])?>');
		jQuery('<ul/>', {
			'class': 'my-new-list',
			html: items.join('')
			}).appendTo('#kartogiraffe_relation_results<?=intval($_GET["id"])?>');
	} else {
		jQuery('<p>', { html: "0 results" }).appendTo('#kartogiraffe_relation_results<?=intval($_GET["id"])?>');
	}
  }).fail(function (error) {console.log(error); });
}


function kartogiraffe_panto<?=intval($_GET["id"])?>(lat, lng) {
  var location = new L.LatLng(lat, lng);
  kartogiraffe_map<?=intval($_GET["id"])?>.panTo(location).setZoom(14);
  jQuery('#kartogiraffe_results<?=intval($_GET["id"])?>').html('');
}

function cartogiraffeLoad<?=intval($_GET["id"])?>() {
	
	kartogiraffe_map<?=intval($_GET["id"])?>.eachLayer(function (layer) {
		try {
			kartogiraffe_map<?=intval($_GET["id"])?>.removeLayer(layer);
		} catch(e) {
			alert(e);
		}
	});
			
	L.tileLayer('http://www.kartogiraffe.de/tiles/tile.php?zoom={z}&x={x}&y={y}&id='+kartogiraffe_id<?=intval($_GET["id"])?>+'&type='+kartogiraffe_type<?=intval($_GET["id"])?>+'&wp=1', {
		attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://opendatacommons.org/licenses/odbl/">OdBL</a>, <a href="http://www.kartogiraffe.de">Kartogiraffe.de</a>',
	}).addTo(kartogiraffe_map<?=intval($_GET["id"])?>);
	
	
	if (kartogiraffe_relation<?=intval($_GET["id"])?>) {
		L.tileLayer('http://www.kartogiraffe.de/tiles/relationtile.php?zoom={z}&x={x}&y={y}&id='+kartogiraffe_id<?=intval($_GET["id"])?>+'&id='+kartogiraffe_relation<?=intval($_GET["id"])?>+'&wp=1&color='+kartogiraffe_relation_color<?=intval($_GET["id"])?>+'&width='+kartogiraffe_relation_width<?=intval($_GET["id"])?>, {
			attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a>contributors, <a href="http://opendatacommons.org/licenses/odbl/">OdBL</a>, <a href="http://www.kartogiraffe.de">Kartogiraffe.de</a>',
		}).addTo(kartogiraffe_map<?=intval($_GET["id"])?>);
	}
}



