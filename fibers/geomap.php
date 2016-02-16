<?php
	@header('Content-Type: text/html; charset=utf-8');

	include_once ('./engine/setup.php');
	include_once ('./engine/db.php');
	$user_id=$_SESSION['logged_user_fibers_id'];
	
if (empty($_SESSION['logged_user_fibers']) && $_SERVER['REQUEST_URI'] != $login_page && empty($_GET['ref'])) {
    header("Location: ".$login_page.'&ref='.base64_encode($_SERVER["REQUEST_URI"]) );
}	
	$lon=(@$_GET['lon']?$_GET['lon']:'87.13582992553711');
	$lat=(@$_GET['lat']?$_GET['lat']:'53.75581576607255');
	$osm_map_name="OpenStreetMap";
	$google_sat_map_name="Google Sat";
	$gis_map_name="2Гис";
	$zoom=(@$_GET['zoom']?$_GET['zoom']:'13');
	
	$layer=(@$_GET['baselayer']?
				$_GET['baselayer']:
				($group_access['map_layer']=='0'?
						$osm_map_name:
						($group_access['map_layer']=='1'?
							$google_sat_map_name:
							''
						)	
				)
			);
?>
<html>
  <head>
    <!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> -->
    <link rel="stylesheet" href="css/geomap.css" type="text/css">
    <link rel="stylesheet" href="js/themes/alertify.core.css" />
	<link rel="stylesheet" href="js/themes/alertify.default.css" id="toggleCSS" />
    
	<script type="text/javascript" src="/OpenLayers-2.12/OpenLayers.js"></script>
	<script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="js/geomap.js"></script>
	<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="js/alertify.min.js"></script>
	<script type="text/javascript" charset="utf-8" src="js/LoadingPanel.js"> </script>

    <script type="text/javascript">
    	var a=0;
    	var b=0;
    	OpenLayers.Lang.setCode("ru");
        var map, measureControls;
        var cacheWrite;
        var tb;
        var find = "<?php echo (isset($_GET['find'])?'&find':'');?>";

        // функция проверки истекла ли сессия
        function check_session(e) {
            $.ajax({
		        url: 'wms_proxy.php?check_session',
		        dataType: "html",
		        type: "GET",
		        success: function(data) {
		        	if(data=='reload') {
    	                window.location.reload();
	                }
		        }
		    });
		    // запуск таймера
            setTimeout(check_session, 60000);
            return false;
	    };

        check_session();

        function init(){
            var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
	        var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
	        var position       = new OpenLayers.LonLat(<? echo $lon.','.$lat; ?>).transform( fromProjection, toProjection);
	        var zoom           = <? echo $zoom; ?>;

            map = new OpenLayers.Map(
				'map',
				{
					controls: [],
					numZoomLevels: 19,
					projection: toProjection,
					displayProjection: fromProjection
				}
			);
			
			var loadingpanel = new OpenLayers.Control.LoadingPanel();

			var osm_map_name = "<?php echo $osm_map_name;?>";
			var google_sat_map_name = "<?php echo $google_sat_map_name; ?>";
			var gis_map_name = "<?php echo $gis_map_name; ?>";
            
            var google = new OpenLayers.Layer.OSM(
            	google_sat_map_name,
				"http://mt0.googleapis.com/vt?lyrs=t@130,r@210000000&src=apiv3&hl=ru-RU&x=${x}&y=${y}&z=${z}&s=G&style=api%7Csmartmaps",
				{numZoomLevels: 16},
				{tileOptions: {crossOriginKeyword: null}}
			);

    		var ver=184;
            var google_sat = new OpenLayers.Layer.OSM(
                	google_sat_map_name,
    				["https://khms0.google.com/kh/v=" + ver +"&src=app&x=${x}&y=${y}&z=${z}&s=Gal",
    				 "https://khms1.google.com/kh/v=" + ver +"&src=app&x=${x}&y=${y}&z=${z}&s=Gali",
    				 "https://khms2.google.com/kh/v=" + ver +"&src=app&x=${x}&y=${y}&z=${z}&s=Galil",
    				 "https://khms3.google.com/kh/v=" + ver +"&src=app&x=${x}&y=${y}&z=${z}&s=Galile"],
    				{tileOptions: {crossOriginKeyword: null}}
    		);

            //var cloudmade = new OpenLayers.Layer.OSM("Cloudmade", "http://tile.cloudmade.com/BC9A493B41014CAABB98F0471D759707/997/256/${z}/${x}/${y}.png", {tileOptions: {crossOriginKeyword: null}} );

			var OSM = new OpenLayers.Layer.OSM(
				osm_map_name,
				"http://tile.openstreetmap.org/${z}/${x}/${y}.png"//,
				//{ 'buffer': 3 }
			);

			/*var gis = new OpenLayers.Layer.OSM(
				'2Gis',
				"https://tile0.maps.2gis.com/tiles?x=${x}&y=${y}&z=${z}&v=32"
			);*/

			var gis = new OpenLayers.Layer.OSM(
				gis_map_name,
				["http://tile0.maps.2gis.com/tiles?x=${x}&y=${y}&z=${z}&v=37"],
				{
		            tileOptions: {
		                crossOriginKeyword: null
		            },
		            numZoomLevels: 19
	            }
			);
<?php
	if($group_access['map_type']==1) {
		echo
			"var maps = new OpenLayers.Layer.WMS(
				'Карта сети',
				'http://".$host.":8080/geoserver/opengeo/wms',
				{
					layers: 'map',
	                version: '1.3.0',
    	            transparent: 'true',
	                format: 'image/png'
				}
			);";

		if($layer==$osm_map_name)
			echo "map.addLayers([OSM,google_sat,gis]);";
		else if($layer==$google_sat_map_name)
			echo "map.addLayers([google_sat,OSM,gis]);";
		else if($layer==$gis_map_name)
			echo "map.addLayers([gis,google_sat,OSM]);";

		echo "map.addLayers([maps]);";
		
		if(is_numeric($_GET['find_fiber'])) {
				echo '
				var gardenStyle = new OpenLayers.Style({
					"strokeWidth":"${strokeWidth}",
					"strokeColor":"${strokeColor}",
					"fillColor":"${fillColor}",
					"pointRadius":"${pointRadius}"
				});
				var gardenStyle2 = new OpenLayers.Style({
					"strokeWidth":8,
					"fillColor":"#fff",
					"strokeColor":"#ffdd00"
				});
				var selectedGardenStyle = new OpenLayers.Style({
					"strokeWidth":4,
					"fillColor":"#00fffb",
					"strokeColor":"#0000ff"
				});
				var gardenStyleMap = new OpenLayers.StyleMap({"default": gardenStyle,"Feature": selectedGardenStyle});
			
				var geojson_layer = new OpenLayers.Layer.Vector("GeoJSON", {
					styleMap: gardenStyleMap,
					strategies: [new OpenLayers.Strategy.Fixed()],
					protocol: new OpenLayers.Protocol.HTTP({
						url: "engine/backend.php?act=find_fiber&fiber_id='.clean($_GET['find_fiber']).'",
						format: new OpenLayers.Format.GeoJSON({
							"internalProjection": toProjection,
							"externalProjection": fromProjection
						})
					})
				});
				map.addLayer(geojson_layer);
            	';
			}
	}
?>
            map.addControl(new OpenLayers.Control.Navigation());
            var ls = new OpenLayers.Control.LayerSwitcher({'ascending':false});
            map.addControl(ls);
            map.addControl(new OpenLayers.Control.Permalink());
            map.addControl(new OpenLayers.Control.OverviewMap());

			map.addControls([loadingpanel]);

            var sketchSymbolizers = {
            	    "Point": {
            	      pointRadius: 4,
            	      graphicName: "square",
            	      fillColor: "white",
            	      fillOpacity: 1,
            	      strokeWidth: 1,
            	      strokeOpacity: 1,
            	      strokeColor: "#333333"
            	    },
            	    "Line": {
            	      strokeWidth: 3,
            	      strokeOpacity: 1,
            	      strokeColor: "#FF1493",
            	      strokeDashstyle: "dash"
            	    }
            	};

            	var style = new OpenLayers.Style();
                style.addRules([
                    new OpenLayers.Rule({symbolizer: sketchSymbolizers})
                ]);
                var styleMap = new OpenLayers.StyleMap({"default": style});

            	var renderer = OpenLayers.Util.getParameters(window.location.href).renderer;
                renderer = (renderer) ? [renderer] : OpenLayers.Layer.Vector.prototype.renderers;

                tb = new OpenLayers.Control.Panel(
                        {
                            displayClass: 'olControlEditingToolbar'
						}
                );

                tb.addControls([
                    new OpenLayers.Control.Navigation({
						title: 'Навигация',
						'activate': function ()
                        	{
								find = '';
								$(".alertify-logs").html("");
                            	deactivateToggleControls();
                                this.dragPan.activate();this.zoomWheelEnabled&&this.handlers.wheel.activate();this.handlers.click.activate();this.zoomBoxEnabled&&this.zoomBox.activate();this.pinchZoom&&this.pinchZoom.activate();return OpenLayers.Control.prototype.activate.apply(this,arguments);
							}
					}),
                    new OpenLayers.Control.Measure(OpenLayers.Handler.Path, {
                    	title: 'Линейка',
                        displayUnits: 'm',
                        'activate': function () {
                        	find = '';
							deactivateToggleControls();
							$(".alertify-logs").html("");
							popupClear();
							//allert(this);
							if(this.active)return!1;
							this.handler&&this.handler.activate();
							this.active=!0;this.map&&OpenLayers.Element.addClass(this.map.viewPortDiv,this.displayClass.replace(/ /g,"")+"Active");this.events.triggerEvent("activate");return!0
							
						},
                        eventListeners:
                        {
    						'measure': handleMeasure_last,
    						'measurepartial': handleMeasure,
    						'deactivate': hideDistance
                        },
                        handlerOptions:
                        {
                          persist: true,
                          layerOptions: {
                        	  renderers: renderer,
                              styleMap: styleMap
    					  }
                        },
                        geodesic: true
                    }),
                    new OpenLayers.Control.Button({
						title: 'Поиск',
                        displayClass: "find",
	                    trigger: function () {
							deactivateToggleControls();
							$(".alertify-logs").html("");
							popupClear();
							this.activate();
							$("div.panel-sloc").show();
							$('input#sloc').focus();
						},
						eventListeners:{
							'deactivate': function () {
	    						$("input#sloc").val();
	    				    	$("div.panel-sloc").hide();
							}
    					}
					}),
					new OpenLayers.Control.Button({
						title: 'Легенда',
                        displayClass: "legend",
						type: OpenLayers.Control.TYPE_TOGGLE,
						eventListeners:{
							'activate': function () {
								this.activate();
	    				    	$("div#panel-legend").show();
							},
							'deactivate': function () {
								this.deactivate();
	    				    	$("div#panel-legend").hide();
							}
    					}
					}),
					new OpenLayers.Control.Button({
						title: 'Главная',
                        displayClass: "home",
	                    trigger: function () {
	                    	window.location.href="./";
						}
					}),
					new OpenLayers.Control.Button({
						title: 'Выход',
                        displayClass: "exit",
	                    trigger: function () {
	                    	window.location.href="?logout";
						}
					})
					
                ]);
                map.addControl(tb);

<?php
				if(isset($_GET['find'])) {
					echo 'tb.controls[2].activate();
						$("div.panel-sloc").show();
						$("input#sloc").focus();';
				} else {
					echo 'tb.controls[0].activate();';
				}
?>

            map.setCenter(position, zoom);
<?php if(isset($_GET['marker'])) {
	$addr=pg_result(pg_query("SELECT address FROM ".$table_node." WHERE the_geom = ST_PointFromText('POINT(".$_GET['lon']." ".$_GET['lat'].")', 4326);"),0);
			echo '
	            // маркер
	            var markers = new OpenLayers.Layer.Markers( "Markers" );
	            map.addLayer(markers);
	            var size = new OpenLayers.Size(42,42);
	            var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
	            var icon = new OpenLayers.Icon("./geomap/marker2.png",size,offset);
	            var marker = new OpenLayers.Marker(position,icon);
	            marker.events.register("click", { overlay: markers, marker: marker }, function(evt) {
	                this.overlay.removeMarker(this.marker);
	                this.marker.destroy();
	            	map.removeLayer(markers);
	            });

				marker.events.register("mouseover", markers, function(evt) {
					popup = new OpenLayers.Popup.FramedCloud(
	                        "chicken", 
	                        position,
	                        null,
	                        "'.$addr.'",
	                        null,
	                        true
	                    );
		        	popupClear();
		        	//popup.autoSize = true;
		        	map.addPopup(popup);
				});
				//here add mouseout event
				marker.events.register("mouseout", markers, function(evt) {popupClear();});

	            // маркер
	            // если есть в гете маркер, то поставить
	            markers.addMarker(marker);
	            ';
			}
?>

            // map functions
            function MapUrlLatlon(e) {
            	var center = map.getCenter().transform( toProjection, fromProjection);
            	var lat = center.lat;
            	var lon = center.lon;
            	var zoom = map.getZoom();
            	var layers = map.getLayersBy("visibility", true);
            	history.pushState(1, "", "?lat="+lat+"&lon="+lon+"&zoom="+zoom+"&baselayer="+layers[0].name+find+"<?php if(is_numeric($_GET['find_fiber'])) echo "&find_fiber=".clean($_GET['find_fiber']);?>");
            };

            function findLayerClick(event) {
            	var BBOX = map.getExtent().transform( toProjection, fromProjection).toBBOX();
            	var LonLat = map.getLonLatFromPixel(event.xy).transform( toProjection, fromProjection);
            	var HEIGHT = map.size.h;
            	var WIDTH = map.size.w;
            	var X = Math.round(event.xy.x);
            	var Y = Math.round(event.xy.y); 

            	var URL = 'wms?SERVICE=WMS&VERSION=1.1.1&REQUEST=GetFeatureInfo&LAYERS=map&QUERY_LAYERS=map&STYLES=&BBOX='+BBOX+'&FEATURE_COUNT=20&HEIGHT='+HEIGHT+'&WIDTH='+WIDTH+'&FORMAT=image%2Fpng&info_format=application%2Fvnd.ogc.gml&SRS=EPSG%3A4326&X='+X+'&Y='+Y;

            	URL = escape(URL);
                $.ajax({
                    url: "wms_proxy.php?args=" + URL,
                    dataType: "html",
                    type: "GET",
                    success: function(data) {
    	                if(data=='reload') {
        	                window.location.reload();
        	                return false;
    	                }
            	        if(data) {
            	        	$(".alertify-logs").html("");
							a++;
            	        	popup = new OpenLayers.Popup.FramedCloud(
                                    "chicken", 
                                    map.getLonLatFromPixel(event.xy),
                                    null,
                                    data,
                                    null,
                                    false
							);
            	        	popup.autoSize = true;
            	        	map.addPopup(popup,true);
            	        }
            	        else {
            	        	b++;
            	        	$.ajax({
            			        url: 'wms_proxy.php?geocode&lat='+LonLat.lat+'&lon='+LonLat.lon+'&addressdetails=1',
            			        dataType: "html",
            			        type: "GET",
            			        success: function(data) {
            				        if(data) {
            				        	$(".alertify-logs").html("");
            				        	var xy = map.getLonLatFromPixel(event.xy);
            				        	popup = new OpenLayers.Popup.FramedCloud(
            			                        "chicken", 
            			                        xy,
            			                        null,
            			                        data,
            			                        null,
            			                        false
            			                    );
            				        	popup.autoSize = true;
            				        	map.addPopup(popup,true);
            				        }
            			        }
            			    });
            	        }
            	        $(".panel-piu").show();
            	        $(".panel-piu").html("Попал: "+a+" Мимо: "+b);
                    }
                });
                Event.stop(event);
            }

            map.events.register("moveend", map, function(event) {
            	MapUrlLatlon(event);
            });

    		map.events.register('click', map, findLayerClick);
        }

        function allert(obj){
			var temp; for(key in obj) {temp += key + " = " + obj[key] + "\n";} alert(temp);
        	return false;
		}
        function deactivateToggleControls(){
			for (eachControl in tb.controls) {
        		if (tb.controls[eachControl].active) {
        			tb.controls[eachControl].deactivate();
				}
			}
		}  

        function handleMeasure(event)
        {
			$(".alertify-logs").show();
			if(event.units == 'km') event.units = 'км';
			if(event.units == 'm') event.units = 'м';
			if (event.order==1) // LINEAR
			{
				if(event.measure.toFixed(3)>0) alertify.success(event.measure.toFixed(3) + " " + event.units);
			}
			var obj = event;
			var points = event.geometry.getSortedSegments();
		}

        function handleMeasure_last(event) {
        	if(event.units == 'km') event.units = 'км';
			if(event.units == 'm') event.units = 'м';
			if (event.order==1) // LINEAR
			{
				alertify.log("Общее расстояние: " + event.measure.toFixed(3) + " " + event.units, "", 0);
			}
        }

        function hideDistance(event) {
			$(".alertify-logs").hide();
        }

        function get_company(event) {
        	$.ajax({
		        url: 'wms_proxy.php?company&addr='+event,
		        dataType: "html",
		        type: "GET",
		        success: function(data) {
			        if(data) {
			        	$(".alertify-logs").html("");
						map.popups[0].setContentHTML(data)
			        	//popupClear();
			        	popup.autoSize = true;
			        	map.addPopup(popup,true);
			        } else {
			        	alertify.error('Организаций не найдено.');
			        }
		        }
		    });
		}

        function get_company_detail(event) {
        	
		}
    </script>
  </head>
  <body onload="init()">
    <div class="container"">
		<div class="panel-container">
			<div class="panel-piu">
			</div>
			<div id="panel-legend">
				Занятость коммутаторов:
				<table>
					<tr><td style="width: 30px; background: red;">&nbsp;</td><td>&nbsp;0 - 19%</td></tr>
					<tr><td style="width: 30px; background: yellow;">&nbsp;</td><td>20 - 59%</td></tr>
					<tr><td style="width: 30px; background: green;">&nbsp;</td><td>60 - 79%</td></tr>
					<tr><td style="width: 30px; background: blue;">&nbsp;</td><td>80 - 100%</td></tr>
				</table>
			</div>
			<div class="panel-sloc">
				<input id="sloc" placeholder="Введите для поиска"><button id="sloc_button">Найти</button><button id="sloc_clear_button">Очистить</button><button id="find-close">X</button>
			</div>
		</div>
		<div id="map"></div>
		<div class="clear"></div>
	</div>
  </body>
</html>
