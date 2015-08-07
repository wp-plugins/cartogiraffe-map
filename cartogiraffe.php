<?php
/*
Plugin Name: Cartogiraffe Map (Standard, Transport, Bicycle, Hiking)
Plugin URI: http://www.cartogiraffe.com/?page=wordpress
Description: Add a map to your blog. Map can be switched between standard view, bicycle view (with cycleways and routes) and transport view. Using Cartogiraffe.com-tiles, based on Openstreetmap-Data.
Version: 1.0
Author: Thomas Wendt
Author URI: http://www.code-wendt.de
License: GPLv2
Credits: Leaflet, FontAwesome
*/

$Cartogiraffe = new Cartogiraffe();
add_action( 'add_meta_boxes', 'cartogiraffe_create' );
add_action( 'admin_enqueue_scripts', 'colorpicker_create' );


function cartogiraffe_create() {
	global $Cartogiraffe;
	$screens = array( 'post', 'page' );
	foreach ($screens as $screen) {
		add_meta_box( 'IDcartogiraffe_generator', 'Map shortcode generator', array($Cartogiraffe,'cartogiraffe_generator'), $screen, 'normal', 'high' );
	}
}

function colorpicker_create( $hook ) {
	if( is_admin() ) {
        // Add the color picker css file
		wp_enqueue_style( 'wp-color-picker' );
 
        // Include our custom jQuery file with WordPress Color Picker dependency
        wp_enqueue_script( 'custom-script-handle', plugins_url( 'custom-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    }
}




class Cartogiraffe {
	
	var $id=0;
	
	function __construct() {
		
		$this->id=rand(0,1000000000);
		wp_enqueue_script(array ('jquery'));
		wp_enqueue_script('Leaflet', plugins_url('js/leaflet0.7.3/leaflet.js',__FILE__));
		if(is_admin()) wp_enqueue_script('Cartogiraffe'.$this->id, plugins_url('js/cartogiraffe_wordpress.php?id='.$this->id,__FILE__));
		wp_enqueue_style('LeafletCSS', plugins_url('js/leaflet0.7.3/leaflet.css',__FILE__));
		wp_enqueue_style('CartogiraffeCSS', plugins_url('css/cartogiraffe.css',__FILE__));
		add_shortcode('cartogiraffe_map',array(&$this, 'viewByShortCode'));
	}
	
	function viewByShortCode($data) {
		
		$this->id=$data["id"];
		wp_enqueue_script('Cartogiraffe'.$this->id, plugins_url('js/cartogiraffe_wordpress.php?id='.$this->id,__FILE__));
		
		if($data["scrollwheel"]=="false") $add="kartogiraffe_map".$this->id.".scrollWheelZoom.disable();";
		
		$return='
		<div id="kartogiraffe_frontend'.$this->id.'" class="kartogiraffe">			';
		
			if($data["search"]!="false") { $return.='
			<div id="kartogiraffe_search'.$this->id.'">
				
				<input class="kartogiraffe_search_input" type="text" name="addr" id="kartogiraffe_search_input'.$this->id.'" /><input type="submit" class="cartogiraffeAwesome kartogiraffe_search_submit" onclick="kartogiraffe_search'.$this->id.'();return false;" value="&#xf002;" />
				<div id="kartogiraffe_results'.$this->id.'"></div>
			</div>';
			}
			
			if($data["changetype"]!="false") {
				$return.='
				<div class="kartogiraffe_changelayer">
					<a href="#" class="cartogiraffeAwesome" onclick="cartogiraffeStandard'.$this->id.'();return false;">&#xf0ac;</a>&nbsp;<a href="#" class="cartogiraffeAwesome" onclick="cartogiraffeTransport'.$this->id.'();return false;">&#xf207;</a>&nbsp;<a href="#" class="cartogiraffeAwesome" onclick="cartogiraffeBike'.$this->id.'();return false;">&#xf206;</a>
				</div>';
			}
		$return.='
			<div id="kartogiraffe_map'.$this->id.'" style="width:'.$data["width"].';height:'.$data["height"].';min-height:'.$data["height"].';"></div>
			<script>
				kartogiraffe_map'.$this->id.' = L.map("kartogiraffe_map'.$this->id.'").setView(['.$data["center"].'], '.$data["zoom"].');
				kartogiraffe_id'.$this->id.' = "'.$data["id"].'";
				kartogiraffe_type'.$this->id.' = "'.$data["type"].'";
				kartogiraffe_relation'.$this->id.' = "'.$data["relation"].'";
				kartogiraffe_relation_color'.$this->id.' = "'.str_replace("#","",$data["relationcolor"]).'";
				kartogiraffe_relation_width'.$this->id.' = "'.$data["relationwidth"].'";
			</script>
			<div id="kartogiraffe_info'.$this->id.'">
				'.htmlspecialchars_decode($data["data"]).'
			</div>
		</div>';
		return $return;
	}
	
	function cartogiraffe_generator($a,$b) {
		echo "HOWTO: <strong>1. Select a position on map</strong> ... <strong>2. Copy & Paste the shortcode into the text</strong> ... <strong>3. That's it</strong>";
		echo "<br>";
		?>
		<div id="kartogiraffe<?=$this->id?>" class="kartogiraffe">
			<div id="kartogiraffe_shortlink_div<?=$this->id?>" style="display:none">
				<label for="kartogiraffe_shortlink<?=$this->id?>">
				<span class="cartogiraffeAwesome">&#xf121;</span>&nbsp;shortcode
				</label> <input type="text" style="width:100%;font-weight:bold;" id="kartogiraffe_shortlink<?=$this->id?>" />
				<br /><br />
			</div>
			
			<div id="kartogiraffe_search<?=$this->id?>">
				 
				<button class="cartogiraffeButton cartogiraffeAwesome" onclick="cartogiraffeStandard<?=$this->id?>();return false;">&#xf0ac;</button>
				<button class="cartogiraffeButton cartogiraffeAwesome" onclick="cartogiraffeTransport<?=$this->id?>();return false;">&#xf207;</button>
				<button class="cartogiraffeButton cartogiraffeAwesome" onclick="cartogiraffeBike<?=$this->id?>();return false;">&#xf206;</button>
				<button class="cartogiraffeButton cartogiraffeAwesome" onclick="cartogiraffeHiking<?=$this->id?>();return false;">&#xf183;</button>
				 
				<input type="text" name="addr" class="kartogiraffe_search_input" id="kartogiraffe_search_input<?=$this->id?>" style="width:50%" />
				<input type="submit" class="cartogiraffeButton cartogiraffeAwesome" onclick="kartogiraffe_search<?=$this->id?>();return false;" value="&#xf002;" />
				
				<div id="kartogiraffe_results<?=$this->id?>"></div>
			</div>
			<div id="kartogiraffe_map<?=$this->id?>" style="width:100%;height:50%;min-height:400px;"></div>
			<div id="kartogiraffe_settings<?=$this->id?>">
				<p><input type="checkbox" onclick="kartogiraffe_lastlatlon<?=$this->id?>='a';cartogiraffeLoad<?=$this->id?>();" id="kartogiraffe_searchfield<?=$this->id?>" value="1" checked=\"checked\" /> <label for="kartogiraffe_searchfield<?=$this->id?>">with searchbox</label> <span class="cartogiraffeAwesome">&#xf002;</span></p>
				<p><input type="checkbox" onclick="kartogiraffe_lastlatlon<?=$this->id?>='b';cartogiraffeLoad<?=$this->id?>();" id="kartogiraffe_change<?=$this->id?>" value="1" checked=\"checked\" /> <label for="kartogiraffe_change<?=$this->id?>">allow to change maptype</label> <span class="cartogiraffeAwesome">&#xf0ac; &#xf207; &#xf206;</span></p>
				<p><input type="checkbox" onclick="kartogiraffe_lastlatlon<?=$this->id?>='c';cartogiraffeLoad<?=$this->id?>();" id="kartogiraffe_scroll<?=$this->id?>" value="1" checked=\"checked\" /> <label for="kartogiraffe_scroll<?=$this->id?>">enable zoom by scrollwheel</label> <span class="cartogiraffeAwesome">&#xf00e;</span></p>
				<p></p>
				<h3>Relations</h3>
				<p><label for="kartogiraffe_relation<?=$this->id?>">Display relation (bike routes, bus lines, boundaries) - based on Openstreetmap-Relations:</p><i>Use % as placeholder, e.g. "Bus 256%" or "%Mauer%weg". Please note: At the moment, <a href="http://wiki.openstreetmap.org/wiki/Super-Relation" target="_blank">super-relations</a> are not supported.</i></label></p>
				<p id="kartogiraffe_relation_results<?=$this->id?>"></p>
				<p><label for="kartogiraffe_relation_input<?=$this->id?>">Search</label><br /><input id="kartogiraffe_relation_input<?=$this->id?>" placeholder="Search relations" /><input type="submit" class="cartogiraffeButton cartogiraffeAwesome" onclick="kartogiraffe_search_relation<?=$this->id?>();return false;" value="&#xf002;" /></p>
				<p><label for="kartogiraffe_relation_id<?=$this->id?>">OSM-Relation-ID</label><br /><input id="kartogiraffe_relation_id<?=$this->id?>" placeholder="Selected RelationID" /><input type="submit" class="cartogiraffeButton cartogiraffeAwesome" onclick="jQuery('#kartogiraffe_relation_id<?=$this->id?>').val('');kartogiraffe_relation<?=$this->id?>=false;cartogiraffeLoad<?=$this->id?>();return false;" value="&#xf014;" /></p>
				<p><label for="kartogiraffe_relation_colorx<?=$this->id?>">Color</label><br /><input id="kartogiraffe_relation_colorx<?=$this->id?>" value="#ff0000" /></p>
				<p><label for="kartogiraffe_relation_widthx<?=$this->id?>">Line Width (Pixel)</label><br /><input id="kartogiraffe_relation_widthx<?=$this->id?>" size="3" type="number" value="3" onchange="kartogiraffe_relation_width<?=$this->id?>=jQuery(this).val();cartogiraffeLoad<?=$this->id?>();window.setTimeout('cartogiraffeCreateShortCode<?=$this->id?>(true);',400);" /></p>
			</div>
			<script>
				kartogiraffe_map<?=$this->id?> = L.map('kartogiraffe_map<?=$this->id?>').setView([52.487349160352665, 13.408928317328959], 13);
				kartogiraffe_map<?=$this->id?>.scrollWheelZoom.disable();
				kartogiraffe_id<?=$this->id?> = '<?=$this->id?>';
				kartogiraffe_type<?=$this->id?> = 'standard';
				kartogiraffe_relation<?=$this->id?> = '';
				kartogiraffe_relation_color<?=$this->id?> = '';
				kartogiraffe_relation_width<?=$this->id?> = '';
			</script>
		</div>
		<?
		
	}

}


