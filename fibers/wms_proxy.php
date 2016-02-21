<?php
	include_once ('./engine/class/xml2array.php');
	include_once ('./engine/setup.php');
	include_once ('./engine/db.php');

	@header('Content-Type: text/html; charset=utf-8');
	
	if(isset($_GET['check_session'])) {
		if(empty($_SESSION['logged_user_fibers'])) {
			echo 'reload';
		}
		die;
	}
	
	if(empty($_SESSION['logged_user_fibers'])) {
		echo 'reload';
		die;
	}
	
	if(isset($_GET['args'])) {
		$result = file_get_contents('http://localhost:8080/geoserver/opengeo/'.$_GET['args']);
		$array = XML2Array::createArray($result);
		$content="";
		$cable="";
		$node="";
		
		if(count($array['wfs:FeatureCollection']['gml:featureMember'])>1) {
			foreach ($array['wfs:FeatureCollection']['gml:featureMember'] as $value) {
				$temp=@get_node($value['opengeo:node']);
				if(!empty($temp)) {
					$node.=$temp;
				}

				$temp=($group_access['map_type']==1?@get_cable($value['opengeo:cable']):'');
				if(!empty($temp)) {
					$cable.=$temp;
				}
			}
		} else {
			$temp=get_node($array['wfs:FeatureCollection']['gml:featureMember']['opengeo:node']);
			if(!empty($temp)) {
				$node.=$temp;
			}

			$temp=($group_access['map_type']==1?get_cable($array['wfs:FeatureCollection']['gml:featureMember']['opengeo:cable']):'');
			if(!empty($temp))
				$cable.=$temp;
		}
		$close='<input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');">';

		if(!empty($node)) {
			$node=preg_replace('/{CLOSE}/', $close, $node);
			$content.=$node;
		}
		if(!empty($cable)) {
			if(empty($node))
				$cable=preg_replace('/{CLOSE}/', $close, $cable, 1);
			$cable=preg_replace('/{CLOSE}/', '', $cable);
			$content.=$cable;
		}
		if(!empty($content))
			echo '<div class="egg_table_div">'.$content.'</div>';
	}
	
	if(isset($_GET['geocode'])) {
		$addr=geocode_local($_GET['lat'],$_GET['lon'],$_GET['addressdetails']);
			echo '<div style="float: left;">'.geocode_local($_GET['lat'],$_GET['lon'],$_GET['addressdetails']).'</div>
					<div style="float: right;">&nbsp;<input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');"></input></div><br>';
	}

	if(isset($_GET['company'])) {
		$addr=$_GET['addr'];

		$url='http://maps.yandex.ru/?text='.urlencode($addr).'&source=form&output=json';
		$result = file_get_contents($url);

		$json=json_decode($result);

		$item = $json->{'vpage'}->{'data'}->{'locations'}->{'GeoObjectCollection'}->{'features'}[0]->{'properties'}->{'Businesses'}->{'items'};

		if($item) {
			echo '<div style="float: left;">Организации:</div>
					<div style="float: right;">&nbsp;<input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');"></input></div>
					<div style="float: left; width: 100%;">';
			foreach ($item AS $key => $value) {
				echo $key+1 .'.&nbsp;';
				echo '<a href="http://maps.yandex.ru/?ol=biz&oid='.$value->{'id'}.'" target="_blank">'.$value->{'name'}.'</a><br>';
			}
			echo '</div>';
		}
	}

	if(isset($_GET['company_detail'])) {
		$addr=$_GET['addr'];
	
		$url='http://maps.yandex.ru/?text='.urlencode($addr).'&source=form&output=json';
	
		$result = file_get_contents($url);
	
		$json=json_decode($result);
	
		echo '<pre>';
		$item = $json->{'vpage'}->{'data'}->{'locations'}->{'GeoObjectCollection'}->{'features'}[0]->{'properties'}->{'Businesses'}->{'items'};
		print_r($json);
		echo '</pre>';

		if($item) {
			echo '<div style="float: left;">Организации:</div>
					<div style="float: right;">&nbsp;<input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');"></input></div>
					<div style="float: left; width: 100%;">';
			foreach ($item AS $key => $value) {
				echo $key+1 .'.&nbsp;';
				echo '<a href="#" oid="'.$value->{'id'}.'" onClick="$(\'#chicken_contentDiv\').html(\''.$value->{'id'}.'\');">'.$value->{'name'}.'</a><br>';
			}
			echo '</div>';
		}
	}
	
	if(isset($_GET['geocode_addr'])) {
		echo geocode_addr($_GET['geocode_addr']);
	}
	
	if(isset($_GET['get_org'])) {
		if($_SERVER['REMOTE_ADDR']=='192.168.6.122' || $_SERVER['REMOTE_ADDR']=='192.168.3.192') {
			$url = 'http://catalog.api.2gis.ru/geo/search?version=1.3&key=ruoaxn8012&q='.$_GET['get_org'].'&limit=1';
			$url='http://catalog.api.2gis.ru/advanced?criteria={%22what%22%3A{%22id%22%3A%22845060585292558%22%2C%22type%22%3A%22house%22}%2C%22types%22%3A[%22firm%22]%2C%22page%22%3A1%2C%22filters%22%3A{%22project_id%22%3A6}}&output=jsonp&key=ruoaxn8012&version=1.3&lang=ru&callback=DG.Online.Utils.Ajax.callback.dga_11';
			 
			echo $url;
			die;
			$result = file_get_contents($url);

			echo '<pre>';
			print_r($result);
			echo '</pre>';
			die;
			if($array->{'total'} > 0) {
				$id = $array->{'result'}[0]->{'id'};
				//echo $id;
				$url = 'http://catalog.api.2gis.ru/geo/get?version=1.3&key=ruoaxn8012&id='.$id;
				$result = file_get_contents($url);
				$array = json_decode($result);
				echo '<pre>';
				print_r($array);
				echo '</pre>';
			}
			die;
		}
		echo 'ssss';
		die;
	}
	
	die;
?>
