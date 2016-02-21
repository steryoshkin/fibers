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
		//echo 'Обновите страницу';
		die;
	}
	
	if(isset($_GET['args'])) {
		//$result = file_get_contents('http://'.$host.':8080/geoserver/opengeo/'.$_GET['args']);
		$result = file_get_contents('http://localhost:8080/geoserver/opengeo/'.$_GET['args']);
		//print_r($result);
		$array = XML2Array::createArray($result);
		/*echo '<pre>';
		//print_r($result);
		print_r($array);
		echo '</pre>';
		die;*/
		
		$content="";
		$cable="";
		$node="";
		
		if(count($array['wfs:FeatureCollection']['gml:featureMember'])>1) {
			foreach ($array['wfs:FeatureCollection']['gml:featureMember'] as $value) {
				$temp=@get_node($value['opengeo:node']);
				if(!empty($temp)) {
					//$node.='<table class="egg_table">'.$temp.'</table>';
					$node.=$temp;
				}

				$temp=($group_access['map_type']==1?@get_cable($value['opengeo:cable']):'');
				if(!empty($temp)) {
					//$cable.='<table>'.$temp.'</table>';
					$cable.=$temp;
				}
			}
		} else {
			$temp=get_node($array['wfs:FeatureCollection']['gml:featureMember']['opengeo:node']);
			if(!empty($temp)) {
				//$node='<table class="egg_table">'.$temp.'</table>';
				$node.=$temp;
			}

			$temp=($group_access['map_type']==1?get_cable($array['wfs:FeatureCollection']['gml:featureMember']['opengeo:cable']):'');
			if(!empty($temp))
				//$cable='<table>'.$temp.'</table>';
				$cable.=$temp;
		}
		//$close_button='<td><input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');"></td>';
		//$close='<td><input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');"></td>';
		$close='<input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');">';

		if(!empty($node)) {
			$node=preg_replace('/{CLOSE}/', $close, $node);
			//echo $node;
			$content.=$node;
		}
		if(!empty($cable)) {
			//if(!empty($node))
				//$content.='<hr>';
				//echo '<hr>';
			if(empty($node))
				$cable=preg_replace('/{CLOSE}/', $close, $cable, 1);
			$cable=preg_replace('/{CLOSE}/', '', $cable);
			//echo $cable;
			$content.=$cable;
		}
		if(!empty($content))
			echo '<div class="egg_table_div">'.$content.'</div>';
			//echo '<table class="egg_table">'.$content.'</table>';
	}
	
	if(isset($_GET['geocode'])) {
		//if($_SERVER['REMOTE_ADDR']=='192.168.6.12' || $_SERVER['REMOTE_ADDR']=='192.168.6.14') {
		$addr=geocode_local($_GET['lat'],$_GET['lon'],$_GET['addressdetails']);
		//echo $addr;

			echo '<div style="float: left;">'.geocode_local($_GET['lat'],$_GET['lon'],$_GET['addressdetails']).'</div>
					<div style="float: right;">&nbsp;<input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');"></input></div><br>';
			//echo '<div><a href="http://maps.yandex.ru/?text='.$addr.'&l=map" target="_blank">Организации</a></div>';
			//echo '<a href="#" id="get_org" onClick="javascript: get_org(\''.$addr.'\'); return false;">Организации</a>';
		//} else geocode_local($_GET['lat'],$_GET['lon'],$_GET['addressdetails']);
		//echo geocode($_GET['geocode']);
	}

	if(isset($_GET['company'])) {
		$addr=$_GET['addr'];

		$url='http://maps.yandex.ru/?text='.urlencode($addr).'&source=form&output=json';
		/*$url='http://maps.yandex.ru/?where=87.1360435%2C53.7564775&sll=87.1360435%2C53.7564775&sspn=0.004097000000001572%2C0.002427000000004398&source=location&output=json';
		$url='http://maps.yandex.ru/?text='.urlencode($addr).'&sspn=0.004097000000001572%2C0.002427000000004398&source=location&output=json';*/

		$result = file_get_contents($url);

		$json=json_decode($result);

		$item = $json->{'vpage'}->{'data'}->{'locations'}->{'GeoObjectCollection'}->{'features'}[0]->{'properties'}->{'Businesses'}->{'items'};
		/*$item = $json->{'vpage'}->{'data'}->{'businesses'}->{'GeoObjectCollection'}->{'features'};

		echo '<pre>';
		print_r($json->{'vpage'}->{'data'}->{'businesses'}->{'GeoObjectCollection'}->{'features'});
		echo '</pre>';*/
		
/*		echo '<div style="float: left;">'.geocode_local($_GET['lat'],$_GET['lon'],$_GET['addressdetails']).'</div>
					<div style="float: right;">&nbsp;<input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');"></input></div>';
*/

		if($item) {
			echo '<div style="float: left;">Организации:</div>
					<div style="float: right;">&nbsp;<input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');"></input></div>
					<div style="float: left; width: 100%;">';
			foreach ($item AS $key => $value) {
				echo $key+1 .'.&nbsp;';
				//echo '<a href="#" oid="'.$value->{'id'}.'" onClick="$(\'#chicken_contentDiv\').html(\''.$value->{'id'}.'\');">'.$value->{'name'}.'</a><br>';
				//echo '<a href="#" onClick="javascript: get_company_detail(\''.$value->{'id'}.'\');">'.$value->{'name'}.'</a><br>';
				echo '<a href="http://maps.yandex.ru/?ol=biz&oid='.$value->{'id'}.'" target="_blank">'.$value->{'name'}.'</a><br>';
				//echo '<a href="http://maps.yandex.ru/?ol=biz&oid='.$value->{'properties'}->{'CompanyMetaData'}->{'id'}.'" target="_blank">'.$value->{'properties'}->{'CompanyMetaData'}->{'name'}.'</a><br>';
				//http://maps.yandex.ru/?ol=biz&oid=1266199711
				//echo '<hr>';
				//print_r($value);
				//echo '<br>';
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
	
		/*		echo '<div style="float: left;">'.geocode_local($_GET['lat'],$_GET['lon'],$_GET['addressdetails']).'</div>
		 <div style="float: right;">&nbsp;<input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');"></input></div>';
		*/
	
		if($item) {
			echo '<div style="float: left;">Организации:</div>
					<div style="float: right;">&nbsp;<input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');"></input></div>
					<div style="float: left; width: 100%;">';
			foreach ($item AS $key => $value) {
				echo $key+1 .'.&nbsp;';
				echo '<a href="#" oid="'.$value->{'id'}.'" onClick="$(\'#chicken_contentDiv\').html(\''.$value->{'id'}.'\');">'.$value->{'name'}.'</a><br>';
				//echo '<hr>';
				//print_r($value);
				//echo '<br>';
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
			
			//$array = json_decode($result);
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
		//echo geocode_addr($_GET['geocode_addr']);
		echo 'ssss';
		die;
	}
	
	die;
?>
