<?php
	if($_GET['map']=='y') {
		$url = "http://sat0".rand(1, 4).".maps.yandex.net/tiles?l=sat&x=".($_GET['x'])."&y=".($_GET['y'])."&z=".$_GET['z']."&g=".substr("Gagarin", 0, rand(1, 8));
		
		$url = "https://khms0.google.com/kh/v=184&src=app&x=".($_GET['x'])."&y=".($_GET['y'])."&z=".($_GET['z'])."&s=".substr("Galileo", 0, rand(1, 8));
		//echo "<a href='".$url."' target='_blank'>".$url."</a><br>";
		
		//$exec='echo '.$_GET['x'].' '.$_GET['y'].' |cs2cs +proj=merc +ellps=WGS84 +to +proj=merc +lon_0=0 +k=1 +x_0=0 +y_0=0 +a=6378137 +b=6356752 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs';
		//echo '<br>';
		//$lonlat = explode("	", exec($exec));
		//print_r($lonlat);
		//echo exec($exec);
		/*echo '<br>';
		echo $exec;*/
		//$url = "http://sat0".rand(1, 4).".maps.yandex.net/tiles?l=sat&x=".round($lonlat[0])."&y=".round($lonlat[1])."&z=".$_GET['z']."&g=".substr("Gagarin", 0, rand(1, 8));
		
		$xtile = ($_GET['x']) / 111319.49079327358;
		
		$ytile = rad2deg(
					asin(tanh($_GET['y'] / 20037508.342789244 * pi))
				);

		echo $xtile.'<br>';
		echo $ytile.'<br>';
		
		$url = "http://sat0".rand(1, 4).".maps.yandex.net/tiles?l=sat&x=".round($lonlat[0])."&y=".round($lonlat[1])."&z=".$_GET['z']."&g=".substr("Gagarin", 0, rand(1, 8));
		//echo "<a href='".$url."' target='_blank'>".$url."</a><br>";
	} else {
		$url = "http://khms".rand(1, 3).".google.com/kh/v=184&src=app&x=".$_GET['x']."&y=".$_GET['y']."&z=".$_GET['z']."&s=".substr("Galileo", 0, rand(1, 8));;
	}
	
	if($_GET['map']!='y')
	{
	header('Content-Type: image/jpeg');
    //header('Content-Length: ' . filesize($file));
    //echo $url;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    //curl_setopt($ch, CURLOPT_POST, 1);
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    //curl_setopt($ch, CURLOPT_POSTFIELDS,"user=".$login."&password=".$pass.'&act=1');
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows; U; Windows NT 5.0; En; rv:1.8.0.2) Gecko/20070306 Firefox/1.0.0.4");
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    
    echo $result;
	}
?>