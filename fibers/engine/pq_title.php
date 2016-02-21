<?php
	header("Content-type: image/png");

	if(isset($_GET['text']))
	{
		$text=$_GET['text'];
		$w=200;
		if(is_numeric($_GET['h'])) {
			$h=$_GET['h'];
		} else {
			$h=100;
		}


		$font_size=12;
		$font_name='isocpeur';
		
		$coord = imagettfbbox(
				$font_size,  // размер шрифта
				0,          // угол наклона шрифта (0 = не наклоняем)
				$font_name,  // имя шрифта, а если точнее, ttf-файла
				$text       // собственно, текст
		);
		//header("Content-type: image/png");
		/*
		echo '<pre>';
		print_r($coord);
		echo '</pre>';*/
		//die;
		/* Функция imagettfbbox возвращает нам массив из восьми элементов,
		 содержащий всевозможные координаты минимального прямоугольника,
		в который можно вписать данный текст. Индексы массива
		удобно обозначить на схеме в виде координат (x,y):
		
		(6,7)           (4,5)
		+---------------+
		|Всем привет! :)|
		+---------------+
		(0,1)           (2,3)
		
		Число элементов массива может на первый взгляд показаться избыточным,
		но не следует забывать о возможности вывода текста под произвольным
		углом.
		
		По этой схеме легко вычислить ширину и высоту текста:
		*/
		$width = $coord[2] - $coord[0];
		$height = $coord[1] - $coord[7];
		
		$w=50;
		$h=10;
		
		// Зная ширину и высоту изображения, располагаем текст по центру:
		$X = ($w - $width) / 2;
		$Y = ($h + $height) / 2;
		//
		//$img = imagecreate($w,$h);
		$img = imagecreate($height,$width);
		$black = ImageColorAllocate($img, 0, 0, 0);
		$green = ImageColorAllocate($img, 0, 255, 0);
		$white = ImageColorAllocate($img, 255, 255, 255);
		$trans = ImageColorTransparent($img, $white);
		ImageFill($img, 0, 0, $trans);
		//ImageString($img , 2, 10, 10, "Laa is so happy to see you!", $black);
		//$mf = imageloadfont ('myfont.phpfont');
		//ImageString($img , $mf, 10, 25, "привет!", $black);

		//ImageTTFText($img, 10, 0, 75, 74, $black, 'isocpeur',toUnicodeEntities($before) );
		//ImageTTFText($img, $font_size, 90, 10, $h, $black, $font_name, $text );
		//ImageTTFText($img, $font_size, 90, $Y+3, $h-$X, $black, $font_name, 'x:'.$X.' y:'.$Y );
		//ImageTTFText($img, $font_size, 90, $Y+3, $h+$X, $black, $font_name, $text );
		ImageTTFText($img, $font_size, 90, -$coord[5]-1, $coord[2]+2, $black, $font_name, $text );
		//ImageTTFText($img, 18, 0, 45, 45, $green, "arial8.ttf", "$text");
		ImagePng($img);
		ImageDestroy($img);
	} else {
		$img = imagecreate(100,10);
		$black = ImageColorAllocate($img, 0, 0, 0);
		$white = ImageColorAllocate($img, 255, 255, 255);
		$trans = ImageColorTransparent($img, $white);
		ImageFill($img, 0, 0, $trans);
		//ImageTTFText($img, 12, 0, 0, 10, $black, 'isocpeur','Ашипка' );
		ImageTTFText($img, 10, 0, 75, 74, $black, 'isocpeur',"sfsdf" );
		ImagePng($img);
		ImageDestroy($img);
	}
?>
