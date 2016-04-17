<?php
	//header('Access-Control-Allow-Origin: "*"');
	include_once ('./setup.php');
	include_once ('./db.php');
	include_once ('./parse_html.php');

    $user_id=@$_SESSION['logged_user_fibers_id'];

    if(isset($_GET['act']) && $_GET['act']=='get_pq_img' && is_numeric($_GET['pq_id'])) {
    	$sql = "SELECT a.id,
				    CASE WHEN a.pq_1 = ".clean($_GET['pq_id'])." THEN pq_1 ELSE pq_2 END AS pq_1,
				    CASE WHEN a.pq_1 = ".clean($_GET['pq_id'])." THEN pq_2 ELSE pq_1 END AS pq_2,				    		
		    		ct.fib, ct.name AS cable_name,
		    		CASE WHEN a.pq_1 = ".clean($_GET['pq_id'])." THEN c1.address_full ELSE c2.address_full END AS addr_1,
		    		CASE WHEN a.pq_1 = ".clean($_GET['pq_id'])." THEN pt1.type ELSE pt2.type END AS type_1,
		    		CASE WHEN a.pq_1 = ".clean($_GET['pq_id'])." THEN b1.num ELSE b2.num END AS num_1,
		    		CASE WHEN a.pq_1 = ".clean($_GET['pq_id'])." THEN c2.address_full ELSE c1.address_full END AS addr_2,
		    		
		    		CASE WHEN a.pq_1 = ".clean($_GET['pq_id'])." THEN pt2.type ELSE pt1.type END AS type_2,
		    		CASE WHEN a.pq_1 = ".clean($_GET['pq_id'])." THEN b2.num ELSE b1.num END AS num_2
				FROM ".$table_cable." AS a, ".$table_pq." AS b1, ".$table_pq." AS b2, ".$table_node." AS c1, ".$table_node." AS c2, ".$table_cable_type." AS ct, ".$table_pq_type." AS pt1, ".$table_pq_type." AS pt2
					WHERE (
						a.pq_1 = b1.id
    				AND b1.node = c1.id
					) AND (
						a.pq_2 = b2.id
    				AND b2.node = c2.id
					) AND (a.pq_1=".clean($_GET['pq_id'])." OR a.pq_2=".clean($_GET['pq_id']).") AND a.cable_type = ct.id
    				AND b1.pq_type_id = pt1.id
    				AND b2.pq_type_id = pt2.id
					ORDER BY fib DESC";
		//echo $sql;

    	$result = pg_query($sql);
    	$row_num = pg_num_rows($result);
    	$col = 0;
    	$last_y_1 = 15;
    	$last_y_2 = 15;
    	$step_x = 1280;
    	$w = 20;
    	$ww=$w/2;
    	$hh = 20;
    	$o=0;
    	
    	$fiber_pos = array();
    	$fiber_conn = array();
    	
    	$pq_id = $_GET['pq_id'];
    	
    	if ($row_num) {
    		//$svg='<text x="10" y="10" >'.$row['addr_1'].'</text>';
    		while ($row = pg_fetch_assoc($result)) {
    			@$addr='<a xmlns="http://www.w3.org/2000/svg" xlink:href="/fibers/index.php?act=s_cable&amp;pq_id='.$pq_id.'" xmlns:xlink="http://www.w3.org/1999/xlink"><text x="'.($step_x/2).'" y="15" >'.$row['addr_1'].'</text></a>';
    			// меняем местами адрема узлов для удобства, вначале всегда выбранного узла
    			if (isset($_GET['pq_id']) && $_GET['pq_id'] == $row['pq_2']) {
    				$to_id = $row['pq_1'];
    				/*$from_id = $row['pq_2'];
    				$to_addr = $row['addr_1'] . ' (' . $type_1 . $num_1 . ')';*/
    			} else {
    				$to_id = $row['pq_2'];
    				/*$from_id = $row['pq_1'];
    				$to_addr = $row['addr_2'] . ' (' . $type_2 . $num_2 . ')';*/
    			}

    			$sql_node = "SELECT * FROM " . $table_pq . " AS p1 WHERE p1.id = " . $pq_id;
    			$result_node = pg_fetch_assoc(pg_query($sql_node));
    			$node_id = $result_node['node'];
    			
    			$sql_fib="SELECT a.id AS id, a.num AS num, g.id AS to_pq_id, e.cable_id AS to_cable_id, e.id AS to_id, e.num AS to_num,
            			col1.name AS mod_name, col1.color AS mod_color, col1.stroke AS mod_stroke, col2.name AS fib_name, col2.stroke AS fib_stroke, col2.color AS fib_color,
                        pt.type AS from_type, c.num AS from_num, g.type AS to_type, g.num AS to_num, cc.port, cc.id AS port_id
                    FROM ".$table_pq." AS c, ".$table_pq_type." AS pt, ".$table_cable." AS b, ".$table_fiber." AS a
                        LEFT JOIN ".$table_fiber_conn." AS d ON ( a.id = d.fiber_id_1 OR a.id = d.fiber_id_2 ) AND d.node_id = ".$node_id."
                        LEFT JOIN ".$table_fiber." AS e ON e.id =
                        		CASE WHEN a.id = d.fiber_id_1 THEN d.fiber_id_2 ELSE
                        			CASE WHEN a.id = d.fiber_id_2 THEN d.fiber_id_1 ELSE NULL END
                        		END
                        LEFT JOIN ".$table_cable." AS f ON f.id = e.cable_id
                        LEFT JOIN (SELECT pq.*, pt.type FROM ".$table_pq." AS pq, ".$table_pq_type." AS pt WHERE pq.node = ".$node_id." AND pq.pq_type_id = pt.id) AS g ON g.id = f.pq_1 OR g.id = f.pq_2
            
                        LEFT JOIN ".$table_cruz_conn." AS cc ON cc.pq_id = ".$pq_id." AND cc.fiber_id = a.id
						LEFT JOIN ".$table_color." AS col1 ON a.mod_color = col1.id AND col1.type = 0
						LEFT JOIN ".$table_color." AS col2 ON a.fib_color = col2.id AND col2.type = 1
            
                    WHERE a.cable_id = ".$row['id']."
                        AND a.cable_id = b.id
                        AND c.id =  ".$pq_id."
                        AND c.node = ".$node_id."
						AND c.pq_type_id=pt.id
                    ORDER BY a.num
					";
    			/*echo '<pre>';
    			print_r($sql_fib);
    			echo '</pre><br>';*/

    			$result_fib = pg_query($sql_fib);

    			$h = $row['fib'] * $hh;
    			$step = 20;
    			if($col%2==0) {
    				$x=1;
    				$y=$last_y_1;
    				$last_y_1 = $y + $h + 50;
    				//$svg.='	<rect x="'.($x+$step).'" y="'.$y.'" width="'.$w.'" height="'.$h.'" style="stroke:black;fill:white;stroke-width:1" />';

    				// подсвечивать предыдущий узел
    				if($_GET['lpq']==$to_id) $svg.='	<rect x="'.($x).'" y="'.$y.'" width="'.($w-1).'" height="'.$h.'" style="stroke:pink;fill:pink;stroke-width:2" />';

    				// ссылка на узел
    				$svg_font.='		<a xmlns="http://www.w3.org/2000/svg" xlink:href="/fibers/engine/backend.php?act=get_pq_img&amp;pq_id='.$to_id.'&amp;lpq='.$pq_id.'{GET_FIB}" xmlns:xlink="http://www.w3.org/1999/xlink">
    						<text x="'.$y.'" y="'.$x.'" dx = "-'.($y*2+$h/2).'" dy = "'.($w/3).'" transform="rotate(-90)">'.$row['addr_2'].'</text>
    						<text x="'.$y.'" y="'.($x+$step/2).'" dx = "-'.($y*2+$h/2).'" dy = "'.($w/3).'" transform="rotate(-90)">'.$row['cable_name'].'</text></a>';
    				if (pg_num_rows($result_fib)) {
    					$i=0;
    					while ($row_fib = pg_fetch_assoc($result_fib)) {
    						// номер волокна
    						$svg.='<a xmlns="http://www.w3.org/2000/svg" xlink:href="?act=get_pq_img&amp;pq_id='.$pq_id.'&amp;lpq='.$to_id.'&amp;fib1='.$row_fib['id'].'" xmlns:xlink="http://www.w3.org/1999/xlink">';
    							$svg.='	<rect x="'.($x+$step).'" y="'.($y+$i).'" width="'.$w.'" height="'.$hh.'" style="stroke:black;fill:#'.($row_fib['fib_color']?$row_fib['fib_color']:'FFFFFF').';stroke-width:1" />';
    						$svg.='</a>';
    						$svg_font.='<a xmlns="http://www.w3.org/2000/svg" xlink:href="?act=get_pq_img&amp;pq_id='.$pq_id.'&amp;lpq='.$to_id.'&amp;fib1='.$row_fib['id'].'" xmlns:xlink="http://www.w3.org/1999/xlink">';
    							$svg_font.='		<text x="'.($x+$step).'" y="'.($y+$i+1).'" dx = "'.($w/2).'" dy = "'.($w/1.5).'">'.$row_fib['num'].'</text>';
    						$svg_font.='</a>';
    						// номер порта
    						$svg.='	<rect x="'.($x+$step+$w).'" y="'.($y+$i).'" width="'.$w.'" height="'.$hh.'" style="stroke:black;fill:white;stroke-width:'.($row_fib['id']==$_GET['fib1'] || $row_fib['id']==$_GET['fib2']?'{GET_PORT}':'1').'" />';
    						$svg_font.='		<text x="'.($x+$step+$w).'" y="'.($y+$i+1).'" dx = "'.($w/2).'" dy = "'.($w/1.5).'">'.$row_fib['port'].'</text>';
    						// точка соединения волокна
    						//$svg.='<circle cx="'.($x+$step+$w*2).'" cy="'.($y+$i+$w/2).'" r="2" style="stroke-width:1"/>';
    								//<text x="'.($x+$step+$w*2).'" y="'.($y+$i+1).'" dx = "'.($w/2).'" dy = "'.$step.'">'.$row_fib['id'].'</text>';
    						// точка соединения волокна в массив
    						$fiber_pos[$row_fib['id']] =  array('x'=>$x+$step+$w*2, 'y'=>$y+$i+$w/2);
    							// заносит соединения в массив исключая дубликаты
   							if(isset($row_fib['to_id']) && $fiber_conn[$row_fib['to_id']]!=$row_fib['id']) {
   								$fiber_conn[$row_fib['id']] = $row_fib['to_id'];
   							}

   							$i=$i+$hh;
    					}
    				}
    			} else {
	    			$x=$step_x;
	    			$y=$last_y_2;
	    			$last_y_2 = $y + $h + 40;
	    			//$svg.='	<rect x="'.$x.'" y="'.$y.'" width="'.$w.'" height="'.$h.'" style="stroke:black;fill:white;stroke-width:1" />';
	    			
	    			// подсвечивать предыдущий узел
	    			if($_GET['lpq']==$to_id) $svg.='	<rect x="'.($x+$w).'" y="'.$y.'" width="'.($w-1).'" height="'.$h.'" style="stroke:pink;fill:pink;stroke-width:2" />';
	    			
	    			// ссылка на узел
	    			$svg_font.='		<a xmlns="http://www.w3.org/2000/svg" xlink:href="/fibers/engine/backend.php?act=get_pq_img&amp;pq_id='.$to_id.'&amp;lpq='.$pq_id.'{GET_FIB}" xmlns:xlink="http://www.w3.org/1999/xlink">
	    					<text x="'.$y.'" y="'.($x+$step/2).'" dx = "-'.($y*2+$h/2).'" dy = "'.($w).'" transform="rotate(-90)">'.$row['cable_name'].'</text>
	    					<text x="'.$y.'" y="'.($x+$step).'" dx = "-'.($y*2+$h/2).'" dy = "'.($w).'" transform="rotate(-90)">'.$row['addr_2'].'</text></a>';
	    			if (pg_num_rows($result_fib)) {
	    				$i=0;
	    				while ($row_fib = pg_fetch_assoc($result_fib)) {
	    					// номер волокна
	    					$svg.='<a xmlns="http://www.w3.org/2000/svg" xlink:href="?act=get_pq_img&amp;pq_id='.$pq_id.'&amp;lpq='.$to_id.'&amp;fib1='.$row_fib['id'].'" xmlns:xlink="http://www.w3.org/1999/xlink">';
	    						$svg.='	<rect x="'.($x).'" y="'.($y+$i).'" width="'.$w.'" height="'.$hh.'" style="stroke:black;fill:#'.($row_fib['fib_color']?$row_fib['fib_color']:'FFFFFF').';stroke-width:1" />';
	    					$svg.='</a>';
	    					$svg_font.='<a xmlns="http://www.w3.org/2000/svg" xlink:href="?act=get_pq_img&amp;pq_id='.$pq_id.'&amp;lpq='.$to_id.'&amp;fib1='.$row_fib['id'].'" xmlns:xlink="http://www.w3.org/1999/xlink">';
	    						$svg_font.='		<text x="'.($x).'" y="'.($y+$i+1).'" dx = "'.($w/2).'" dy = "'.($w/1.5).'">'.$row_fib['num'].'</text>';
	    					$svg_font.='</a>';
	    					// номер порта
	    					$svg.='	<rect x="'.($x-$w).'" y="'.($y+$i).'" width="'.$w.'" height="'.$hh.'" style="stroke:black;fill:white;stroke-width:'.($row_fib['id']==$_GET['fib1'] || $row_fib['id']==$_GET['fib2']?'{GET_PORT}':'1').'" />';
	    					$svg_font.='		<text x="'.($x-$w).'" y="'.($y+$i+1).'" dx = "'.($w/2).'" dy = "'.($w/1.5).'">'.$row_fib['port'].'</text>';
	    					// точка соединения волокна
	    					//$svg.='<circle cx="'.($x-$w).'" cy="'.($y+$i+$w/2).'" r="2" style="stroke-width:1"/>';
	    							//<text x="'.($x-$w*4).'" y="'.($y+$i+1).'" dx = "'.($w/2).'" dy = "'.$step.'">'.$row_fib['id'].'</text>';
	    					// точка соединения волокна в массив
	    					$fiber_pos[$row_fib['id']] =  array('x'=>$x-$w, 'y'=>$y+$i+$w/2);

	    					// заносит соединения в массив исключая дубликаты
	    					if(isset($row_fib['to_id']) && $fiber_conn[$row_fib['to_id']]!=$row_fib['id']) {
    							$fiber_conn[$row_fib['id']] = $row_fib['to_id'];
    						}

	    					$i=$i+$hh;
	    				}
	    			}
    			}
    			$col++;
    		}

    		$fib_conn_ = 0;
    		foreach ($fiber_conn AS $fib_1 => $fib_2) {
    			//echo $fiber_pos[$fib_1][x].' '.$fiber_pos[$fib_2][x].'<br>';
    			//$svg_line.='<line x1="'.$fiber_pos[$fib_1][x].'" y1="'.$fiber_pos[$fib_1][y].'" x2="'.$fiber_pos[$fib_2][x].'" y2="'.$fiber_pos[$fib_2][y].'" style="stroke-width:1"/>';
    			if($fiber_pos[$fib_1][x]>$fiber_pos[$fib_2][x]) {
    				$x1=$fiber_pos[$fib_2][x];
    				$y1=$fiber_pos[$fib_2][y];
    				$x2=$fiber_pos[$fib_1][x];
    				$y2=$fiber_pos[$fib_1][y];
    			} else {
    				$x1=$fiber_pos[$fib_1][x];
    				$y1=$fiber_pos[$fib_1][y];
    				$x2=$fiber_pos[$fib_2][x];
    				$y2=$fiber_pos[$fib_2][y];
    			}
  				$svg_line.='<path d="M'.$x1.','.$y1.' ';
  					if($y1!=$y2) {
  						if($x1!=$x2)
  							$svg_line.='h'.($v++*$ww+$w).' V'.$y2.' ';
  						else
  							$svg_line.='H'.($v++*$ww+$w*4).' V'.$y2.' ';
  					}
  				if($_GET['fib1']==$fib_1 || $_GET['fib1']==$fib_2 || $_GET['fib2']==$fib_1 || $_GET['fib2']==$fib_2) {
  					$sw='3';
  					$get_fib='&amp;fib1='.$fib_1.'&amp;fib2='.$fib_2;
  					//echo '2';
  					$fib_conn_++;
  				} else {
  					$sw='1';
  					//$get_fib='';
  				}
  				//{GET_FIB}
				$svg_line.='L'.$x2.','.$y2.'" style="stroke-width:'.$sw.'" />
';
    		}

    		if($fib_conn_==0) {
    			$get_fib=(isset($_GET['fib1'])?'&amp;fib1='.$_GET['fib1']:'').(isset($_GET['fib2'])?'&amp;fib2='.$_GET['fib2']:'');
    			$svg = str_replace('{GET_PORT}', '3', $svg);
    		}

    		$svg_font='	<g font-size = "10" font = "serif" fill = "black" stroke = "none" text-anchor = "middle">'.$svg_font.'	</g>';
    		$svg='<g style="stroke:black;fill:none">
'.$svg_line.'
</g>'.$svg;

    		$svg='<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="'.($step_x+$step*2).'" height="'.($last_y_1).'">
    			'.$addr.'
    			'.$svg.'
    			'.$svg_font.'
</svg>';
    		//header("Content-type: image/svg+xml");
    		echo '<?xml version="1.0" encoding="UTF-8"?>
   <!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN"
     "http://www.w3.org/TR/2001/
      REC-SVG-20010904/DTD/svg10.dtd">';

    		$svg = str_replace('{GET_FIB}', $get_fib, $svg);

    		//print_r($svg);
    		echo $svg;
    	}
    	die;
    }
    
// get_pg_img
    
// удаление улицы div
    /**
     * @param   string  $p                 пароль
     * @param   bool    $is_check_digits   проверять существование цифр?
     * @param   bool    $is_check_letters  проверять существование латинских букв?
     * @return  bool                       TRUE, если пароль хороший и FALSE, если пароль слабый
     *
     * @license  http://creativecommons.org/licenses/by-nc-sa/3.0/
     * @author   Nasibullin Rinat <n a s i b u l l i n  at starlink ru>
     * @charset  ANSI
     * @version  1.0.1
     */
    if(isset($_POST['act']) && $_POST['act']=='change_pass' && isset($_POST['old_pass']) && isset($_POST['new_pass'])) {
    	if(password_quality_check(clean($_POST['new_pass']),true,true)) {
    		pg_query("UPDATE ".$table_user." SET password='".md5(clean($_POST['new_pass']))."', new_pass = true WHERE id=".clean($_POST['user_id']));
    		//echo "UPDATE ".$table_user." SET password='".md5(clean($_POST['new_pass']))."' WHERE id=".clean($_POST['user_id']);
    	} else {
    		echo 'bad_pass';
    	}
    	die;
    }

// загрузка файлов
	if(isset($_GET['act']) && $_GET['act']=='pq_file_add' && is_numeric($_GET['pq_id'])) {
		$text='<form action="./engine/upload_file.php" method="post" enctype="multipart/form-data" id="form_submit">';

		$text.='<input type="hidden" name="pq_id" value="'.clean($_GET['pq_id']).'">';
		
		$text.='<input name="file_input" type="file" />';

		$text.='<div class="span2 m0 text-left"><button class="m0" id="fake_file_input" title="Обзор">Обзор</button></div>';
		$text.='<div class="span6 m0 input-control text"><input type="text" disabled2 id="file_input_filename" placeholder="Имя файла" /></div>';
		$text.='<div class="span2 m0 input-control text-left"><input class="m0" name="file_submit" type="submit" value="Загрузить"/></div>';
		//$text.='<div class="span2 m0 text-left"><button class="m0" id="file_input_submit" title="Загрузить">Загрузить</button></div>';
		$text.'</form>';
		
		//$text.='<div class="span2 m0 text-left"><button class="m0" id="'.clean($_GET['act']).'" type="file" title="Обзор">Обзор</button></div>';
		//$text.='<div class="span2 m0 text-left"><button class="m0" id="'.clean($_GET['act']).'" title="Обзор">Обзор</button></div>';
		//$text.='<div class="span5 m0 input-control text"><input type="text" disabled id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
		//$text.=button_ok_cancel('div_new');
		echo $text;
		die;
	}
	
	// удаление файла div
	if(isset($_GET['act']) && $_GET['act']=='pq_file_del' && @is_numeric($_GET['id'])) {
		$name=pg_result(pg_query("SELECT name FROM ".$table_pq_schem." WHERE id =".clean($_GET['id'])),0);
		$text='<div class="span10 m5">&nbsp;Удалить файл "'.$name.'"?</div>'.button_ok_cancel('div_del','pq_file_del');
		echo $text;
		die;
	}
	
	// удаление файла sql
	if(isset($_POST['act']) && $_POST['act']=='pq_file_del' && @is_numeric($_POST['id']) ) {
		if(@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_pq_schem." WHERE id = ".clean($_POST['id']).""),0)) {
/*
			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_pq_schem." WHERE id = ".clean($_POST['id']) ));
			$result = serialize($data_old);
			add_log($table_pq_schem,clean($_POST['id']),$result,$user_id);
*/
			pg_query("DELETE FROM ".$table_pq_schem." WHERE id = ".clean($_POST['id']));
			//echo "DELETE FROM ".$table_pq_schem." WHERE id = ".clean($_POST['id']);
		} else echo "Ашипка, файла с id=".$_POST['id']." не существует...";
		die;
	}
// конец загрузка файлов

// область begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование области в div
    if(isset($_GET['act']) && ($_GET['act']=='n_region' || $_GET['act']=='e_region') ) {
    	if($_GET['act']=='e_region')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM ".$table_region." AS r1 WHERE r1.id=".$id;
    		$result=pg_fetch_assoc(pg_query($sql));
    		$name=$result['name'];
    		$descrip=$result['descrip'];
    	}

    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span4 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Область" /></div>';
    	$text.='<div class="span6 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
    	$text.=button_ok_cancel('div_new');
    	echo $text;
    	die;
    }

// ввод нового/редактирование области в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_region' || $_POST['act']=='e_region') ) {
    	$sql="SELECT * FROM ".$table_region." WHERE name='".clean($_POST['name'])."'";
    	$text='';
    	if($_POST['act']=='n_region') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такоя область существует!!!";
    		} else {
    			//echo "INSERT INTO ".$table_region." (name,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", ".$user_id.")";
    			pg_query("INSERT INTO ".$table_region." (name,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", ".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_region') {
		    if(@pg_result(pg_query($sql." AND descrip ".($_POST['descrip']?"='".$_POST['descrip']."'":"IS NULL")),0)) {
    			$text="Изменить невозможно, такоя область существует!!!";
    		} else {
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_region." WHERE id = ".clean($_POST['id']) ));

    			pg_query("UPDATE ".$table_region." SET name='".clean($_POST['name'])."', descrip=".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", user_id=".$user_id." WHERE id=".clean($_POST['id']).";")
    				or die("Изменить невозможно, такоя область существует!!!");

    			$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_region." WHERE id = ".clean($_POST['id']) ));
    			
    			$result = serialize(array_diff($data_old, $data_new));
    			add_log($table_area,clean($_POST['id']),$result,$user_id);
    		}
    	}
    	echo $text;
    	die;
    }

// удаление области div
    if(isset($_GET['act']) && $_GET['act']=='d_region' && @is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM ".$table_city." AS s1 WHERE s1.region_id =".clean($_GET['id']);
    	$region_name=pg_result(pg_query("SELECT name FROM ".$table_region." AS r1 WHERE r1.id =".clean($_GET['id'])),0);
    	if(pg_result(pg_query($sql),0)) {
    		$text='<div class="span11 m5">&nbsp;Область "'.$region_name.'" используется. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
    	} else {
    		$text='<div class="span10 m5">&nbsp;Удалить область "'.$region_name.'"?</div>'.button_ok_cancel('div_del','d_region');
    	}
    	echo $text;
    	die;
    }

    // удаление области sql
    if(isset($_POST['act']) && $_POST['act']=='d_region' && @is_numeric($_POST['id']) ) {
    	if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_city." AS r1 WHERE r1.region_id = ".clean($_POST['id']).""),0)) {

    		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_region." WHERE id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
    		add_log($table_region,clean($_POST['id']),$result,$user_id);

    		pg_query("DELETE FROM ".$table_region." WHERE id = ".clean($_POST['id']));
    	} else echo "not exist";
    	die;
    }
// область end -------------------------------------------------------------------------------------------------------

// город/посёлок begin -------------------------------------------------------------------------------------------------------
// ввод новой/редактирование города/посёлка в div
    if(isset($_GET['act']) && ($_GET['act']=='n_city' || $_GET['act']=='e_city') ) {
    	if($_GET['act']=='e_city')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT *, ST_X(ST_astext(the_geom)) AS lat, ST_Y(ST_astext(the_geom)) AS lon FROM ".$table_city." WHERE id = ".$id;
    		$result=pg_fetch_assoc(pg_query($sql));
    		$name=$result['name'];
    		$region_id=$result['region_id'];
    		$lat=$result['lat'];
    		$lon=$result['lon'];
    		$descrip=$result['descrip'];
    	}
    	$sql="SELECT * FROM ".$table_region." ORDER BY name";
    	$result = pg_query($sql);
    	if(pg_num_rows($result)){
    		$select_region='<select id="region">';
    		$select_region.='<option value="0">-Область-</option>';
    		while($row=pg_fetch_assoc($result)){
    			$select_region.='<option value="'.$row['id'].'"';
    			if($region_id==$row['id']) {
    				$select_region.=" SELECTED";
    				$region_id=$row['region_id'];
    			}
    			$select_region.='>'.$row['name'].'</option>';
    		}
    		$select_region.='</select>';
    	}

    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Город/посёлок" /></div>';
    	$text.='<div class="span3 m0 input-control text">'.$select_region.'</div>';
    	$text.='<div class="span1_5 m0 input-control text"><input type="text" id="lat" value="'.$lat.'" placeholder="широта" /></div>';
    	$text.='<div class="span1_5 m0 input-control text"><input type="text" id="lon" value="'.$lon.'" placeholder="долгота" /></div>';
    	$text.='<div class="span4 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.=button_ok_cancel('div_new');
    	echo $text;
    	die;
    }

// ввод новой/редактирование города/посёлка в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_city' || $_POST['act']=='e_city') && @is_numeric($_POST['region_id']) ) {
        $sql="SELECT COUNT(*) FROM ".$table_city." WHERE name='".clean($_POST['name'])."' AND region_id=".clean($_POST['region_id'])." AND the_geom=ST_GeomFromText('POINT(".clean($_POST['lat'])." ".clean($_POST['lon']).")', 4326)";
    	//$sql="SELECT COUNT(*) FROM ".$table_city." WHERE name='".clean($_POST['name'])."' AND region_id=".clean($_POST['region_id']);
    	if($_POST['act']=='n_city') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такаой город/посёлок существует!!!";
    		} else {
    			pg_query("INSERT INTO ".$table_city." (name,region_id,descrip,user_id,the_geom) VALUES ('".clean($_POST['name'])."', ".clean($_POST['region_id']).", ".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", ".$user_id.", ST_GeomFromText('POINT(".clean($_POST['lat'])." ".clean($_POST['lon']).")', 4326))");
    		}
    	} elseif($_POST['act']=='e_city') {
    		//if(pg_result(pg_query($sql." AND region_id ".($_POST['region_id']?"=".$_POST['region_id']:"IS NULL")." AND descrip ".($_POST['descrip']?"='".$_POST['descrip']."'":"IS NULL")),0)) {
    		if(@pg_result(pg_query($sql." AND descrip ".($_POST['descrip']?"='".$_POST['descrip']."'":"IS NULL")),0)) {
    			$text="Изменить невозможно, такай город/посёлок существует!!!";
    		} else {
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_city." WHERE id = ".clean($_POST['id']) ));

    			pg_query("UPDATE ".$table_city." SET name='".clean($_POST['name'])."', region_id=".clean($_POST['region_id']).", descrip=".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", user_id=".$user_id.", the_geom=ST_GeomFromText('POINT(".clean($_POST['lat'])." ".clean($_POST['lon']).")', 4326) WHERE id=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такай город/посёлок существует!!!");

				$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_city." WHERE id = ".clean($_POST['id']) ));

				$result = serialize(array_diff($data_old, $data_new));
				add_log($table_city,clean($_POST['id']),$result,$user_id);
    		}
    	}
    	echo @$text;
    	die;
    }

// удаление города/посёлка div
    if(isset($_GET['act']) && $_GET['act']=='d_city' && @is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM ".$table_area." AS a1 WHERE a1.city_id=".clean($_GET['id']);
    	$city_name=pg_result(pg_query("SELECT name FROM ".$table_city." AS s1 WHERE s1.id =".clean($_GET['id'])),0);
    	if(pg_result(pg_query($sql),0)) {
    		$text='<div class="span11 m5">&nbsp;Город/посёлок "'.$city_name.'" используется. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
    	} else {
    		$text='<div class="span10 m5">&nbsp;Удалить город/посёлок "'.$city_name.'"?</div>'.button_ok_cancel('div_del','d_city');
    	}
    	echo $text;
    	die;
    }

// удаление города/посёлка sql
    if(isset($_POST['act']) && $_POST['act']=='d_city' && @is_numeric($_POST['id']) ) {
    	if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_area." AS a1 WHERE a1.city_id = ".clean($_POST['id']).""),0)) {

    		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_city." WHERE id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
    		add_log($table_city,clean($_POST['id']),$result,$user_id);
    		pg_query("DELETE FROM ".$table_city." WHERE id = ".clean($_POST['id']));
    	} else echo "not exist";
    	die;
    }
// город/посёлок end -------------------------------------------------------------------------------------------------------

// район begin -------------------------------------------------------------------------------------------------------
// ввод новой/редактирование района в div
    if(isset($_GET['act']) && ($_GET['act']=='n_area' || $_GET['act']=='e_area') ) {
    	if($_GET['act']=='e_area')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM ".$table_area." WHERE id = ".$id;
    		$result=pg_fetch_assoc(pg_query($sql));
    		$name=$result['name'];
    		$city_id=$result['city_id'];
    		$descrip=$result['descrip'];
    	}
    	$sql="SELECT c1.*,r1.name AS region_name FROM ".$table_city." AS c1, ".$table_region." AS r1 WHERE c1.region_id = r1.id ORDER BY c1.name";
    	$result = pg_query($sql);
    	if(pg_num_rows($result)){
    		$select_city='<select id="city">';
    		$select_city.='<option value="0">-Город/посёлок-</option>';
    		while($row=pg_fetch_assoc($result)){
    			$select_city.='<option value="'.$row['id'].'"';
    			if($city_id==$row['id']) {
    				$select_city.=" SELECTED";
    				$city_id=$row['city_id'];
    			}
    			$select_city.='>'.$row['name'].' ('.$row['region_name'].')</option>';
    		}
    		$select_city.='</select>';
    	}

    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Район" /></div>';
    	$text.='<div class="span4 m0 input-control text">'.$select_city.'</div>';
    	$text.='<div class="span4 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.=button_ok_cancel('div_new');
    	echo $text;
    	die;
    }

// ввод новой/редактирование района в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_area' || $_POST['act']=='e_area') && @is_numeric($_POST['city_id']) ) {
        $sql="SELECT COUNT(*) FROM ".$table_area." WHERE name='".clean($_POST['name'])."' AND city_id=".clean($_POST['city_id']);
    	if($_POST['act']=='n_area') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такаой район существует!!!";
    		} else {
    			pg_query("INSERT INTO ".$table_area." (name,city_id,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".clean($_POST['city_id']).", ".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", ".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_area') {
    		if(@pg_result(pg_query($sql." AND descrip ".($_POST['descrip']?"='".$_POST['descrip']."'":"IS NULL")),0)) {
    		//if(@pg_result(pg_query($sql." AND city_id ".($_POST['city_id']?"=".$_POST['city_id']:"IS NULL")." AND descrip ".($_POST['descrip']?"='".$_POST['descrip']."'":"IS NULL")),0)) {
    			$text="Изменить невозможно, такай район существует!!!";
    			////
    		} else {
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_area." WHERE id = ".clean($_POST['id']) ));

    			pg_query("UPDATE ".$table_area." SET name='".clean($_POST['name'])."', city_id=".clean($_POST['city_id']).", descrip=".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", user_id=".$user_id." WHERE id=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такай район существует!!!");

				$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_area." WHERE id = ".clean($_POST['id']) ));

				$result = serialize(array_diff($data_old, $data_new));
				add_log($table_area,clean($_POST['id']),$result,$user_id);
    		}
    	}
    	echo @$text;
    	die;
    }

// удаление района div
    if(isset($_GET['act']) && $_GET['act']=='d_area' && @is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM ".$table_street_name." AS sn1 WHERE sn1.area_id=".clean($_GET['id']);
    	$area_name=pg_result(pg_query("SELECT name FROM ".$table_area." AS a1 WHERE a1.id =".clean($_GET['id'])),0);
    	if(pg_result(pg_query($sql),0)) {
    		$text='<div class="span11 m5">&nbsp;Район "'.$area_name.'" используется. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
    	} else {
    		$text='<div class="span10 m5">&nbsp;Удалить район "'.$area_name.'"?</div>'.button_ok_cancel('div_del','d_area');
    	}
    	echo $text;
    	die;
    }

// удаление района sql
    if(isset($_POST['act']) && $_POST['act']=='d_area' && @is_numeric($_POST['id']) ) {
    	if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_street_name." AS sn1 WHERE sn1.area_id = ".clean($_POST['id']).""),0)) {

    		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_area." WHERE id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
    		add_log($table_area,clean($_POST['id']),$result,$user_id);
    		pg_query("DELETE FROM ".$table_area." WHERE id = ".clean($_POST['id']));
    	} else echo "not exist";
    	die;
    }
// район end -------------------------------------------------------------------------------------------------------

// улица begin -------------------------------------------------------------------------------------------------------
// ввод новой/редактирование улицы в div
    if(isset($_GET['act']) && ($_GET['act']=='n_street_name' || $_GET['act']=='e_street_name') ) {
    	if($_GET['act']=='e_street_name')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM ".$table_street_name." WHERE id = ".$id;
    		$result=pg_fetch_assoc(pg_query($sql));
    		$name=$result['name'];
    		$small_name=$result['small_name'];
    		$area_id=$result['area_id'];
    		$descrip=$result['descrip'];
    		$street_id=$result['street_id'];
    	}
    	$sql="SELECT a1.*,c1.name AS city_name FROM ".$table_area." AS a1, ".$table_city." AS c1 WHERE a1.city_id = c1.id ORDER BY a1.name";
    	$result = pg_query($sql);
    	if(pg_num_rows($result)){
    		$select_area='<select id="area">';
    		$select_area.='<option value="0">-Район-</option>';
    		while($row=pg_fetch_assoc($result)){
    			$select_area.='<option value="'.$row['id'].'"';
    			if($area_id==$row['id']) {
    				$select_area.=" SELECTED";
    				$region_id=$row['region_id'];
    			}
    			$select_area.='>'.$row['name'].' ('.$row['city_name'].')</option>';
    		}
    		$select_area.='</select>';
    	}

    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Улица" /></div>';
    	$text.='<div class="span2 m0 input-control text"><input type="text" id="small_name" value="'.$small_name.'" placeholder="Улица (кр. название)" /></div>';
    	$text.='<div class="span4 m0 input-control text">'.$select_area.'</div>';
    	$text.='<div class="span4 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.=button_ok_cancel('div_new');
    	echo $text;
    	die;
    }

// ввод новой/редактирование улицы в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_street_name' || $_POST['act']=='e_street_name') && @is_numeric($_POST['area_id']) ) {
        $sql="SELECT COUNT(*) FROM ".$table_street_name." WHERE name='".clean($_POST['name'])."' AND small_name ".($_POST['small_name']?"='".clean($_POST['small_name'])."'":'IS NULL')." AND area_id=".clean($_POST['area_id']);
    	if($_POST['act']=='n_street_name') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такая улица существует!!!";
    		} else {
    			pg_query("INSERT INTO ".$table_street_name." (name,small_name,area_id,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".($_POST['small_name']?"'".clean($_POST['small_name'])."'":"NULL").", ".clean($_POST['area_id']).", ".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", ".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_street_name') {
    		if(@pg_result(pg_query($sql." AND descrip ".($_POST['descrip']?"='".$_POST['descrip']."'":"IS NULL")),0)) {
    		//if(@pg_result(pg_query($sql." AND street_id ".($_POST['street_id']?"=".$_POST['street_id']:"IS NULL")." AND descrip ".($_POST['descrip']?"='".$_POST['descrip']."'":"IS NULL")),0)) {
    			$text="Изменить невозможно, такая улица существует!!!";
    			////
    		} else {
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_street_name." WHERE id = ".clean($_POST['id']) ));

    			pg_query("UPDATE ".$table_street_name." SET name='".clean($_POST['name'])."', small_name=".($_POST['small_name']?"'".clean($_POST['small_name'])."'":"NULL").", area_id=".clean($_POST['area_id']).", descrip=".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", user_id=".$user_id." WHERE id=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такая улица существует!!!");

				$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_street_name." WHERE id = ".clean($_POST['id']) ));

				$result = serialize(array_diff($data_old, $data_new));
				add_log($table_street_name,clean($_POST['id']),$result,$user_id);
    		}
    	}
    	echo @$text;
    	die;
    }

// удаление улицы div
    if(isset($_GET['act']) && $_GET['act']=='d_street_name' && @is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM ".$table_node." AS n1 WHERE n1.street_id=".clean($_GET['id']);
    	$street_name=pg_result(pg_query("SELECT name FROM ".$table_street_name." AS sn1 WHERE sn1.id =".clean($_GET['id'])),0);
    	if(pg_result(pg_query($sql),0)) {
    		$text='<div class="span11 m5">&nbsp;Улица "'.$street_name.'" используется. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
    	} else {
    		$text='<div class="span10 m5">&nbsp;Удалить улицу "'.$street_name.'"?</div>'.button_ok_cancel('div_del','d_street_name');
    	}
    	echo $text;
    	die;
    }

// удаление улицы sql
    if(isset($_POST['act']) && $_POST['act']=='d_street_name' && @is_numeric($_POST['id']) ) {
    	if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_node." AS n1 WHERE n1.street_id = ".clean($_POST['id']).""),0)) {

    		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_street_name." WHERE id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
    		add_log($table_street_name,clean($_POST['id']),$result,$user_id);

    		// удаляем улицу
    		pg_query("DELETE FROM ".$table_street_name." WHERE id = ".clean($_POST['id']));
    		// удаляем все дома этой улицы
    		pg_query("DELETE FROM ".$table_street_num." WHERE street_name_id = ".clean($_POST['id']));
    	} else echo "not exist";
    	die;
    }
// улица end -------------------------------------------------------------------------------------------------------

// номер дома begin -------------------------------------------------------------------------------------------------------
// номер дома sql
    if(isset($_POST['act']) && $_POST['act']=='check_street_num' && @is_numeric($_POST['street_name_id']) && isset($_POST['street_num']) ) {
        $street_num_id=@pg_result(pg_query("SELECT id FROM ".$table_street_num." WHERE street_name_id = ".clean($_POST['street_name_id'])." AND num='".clean($_POST['street_num'])."'"),0);
        echo $street_num_id;
        die;
    }
// номер дома end -------------------------------------------------------------------------------------------------------

// размещение begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование размещения в div
    if(isset($_GET['act']) && ($_GET['act']=='n_location' || $_GET['act']=='e_location') ) {
    	if($_GET['act']=='e_location')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM ".$table_location." WHERE id = ".$id;
    		$result=pg_fetch_assoc(pg_query($sql));
    		$location=$result['location'];
    		$descrip=$result['descrip'];
    	}
    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="location" value="'.$location.'" placeholder="Размещение" /></div>';
    	$text.='<div class="span6 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
    	$text.=button_ok_cancel('div_new');
    	echo $text;
    	die;
    }

// ввод нового/редактирование размещения в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['location']) && ($_POST['act']=='n_location' || $_POST['act']=='e_location') ) {
    	$sql="SELECT * FROM ".$table_location." WHERE location='".clean($_POST['location'])."'";
    	if($_POST['act']=='n_location') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такое размещение существует!!!";
    		} else {
    			pg_query("INSERT INTO ".$table_location." (location,descrip,user_id) VALUES ('".clean($_POST['location'])."', ".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", ".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_location') {
    		if(@pg_result(pg_query($sql." AND descrip ".($_POST['descrip']?"='".$_POST['descrip']."'":"IS NULL")),0)) {
    			$text="Изменить невозможно, такое размещение существует!!!";
    		} else {
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_location." WHERE id = ".clean($_POST['id']) ));

    			pg_query("UPDATE ".$table_location." SET location='".clean($_POST['location'])."', descrip=".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", user_id=".$user_id." WHERE id=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такое размещение существует!!!");

				$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_location." WHERE id = ".clean($_POST['id']) ));

				$result = serialize(array_diff($data_old, $data_new));
				add_log($table_location,clean($_POST['id']),$result,$user_id);
    		}
    	}
    	echo $text;
    	die;
    }

// удаление размещения div
    if(isset($_GET['act']) && $_GET['act']=='d_location' && @is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM ".$table_node." AS n1 WHERE n1.location_id=".clean($_GET['id']);
    	$location=pg_result(pg_query("SELECT location FROM ".$table_location." AS l1 WHERE l1.id =".clean($_GET['id'])),0);
    	if(pg_result(pg_query($sql),0)) {
    		$text='<div class="span11 m5">&nbsp;Размещение "'.$location.'" используется. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
    	} else {
    		$text='<div class="span10 m5">&nbsp;Удалить размещение "'.$location.'"?</div>'.button_ok_cancel('div_del','d_location');
    	}
    	echo $text;
    	die;
    }

// удаление размещения sql
    if(isset($_POST['act']) && $_POST['act']=='d_location' && @is_numeric($_POST['id']) ) {
    	if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_node." AS n1 WHERE n1.location_id = ".clean($_POST['id']).""),0)) {

    		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_location." WHERE id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
    		add_log($table_location,clean($_POST['id']),$result,$user_id);

    		// удаляем размещение
    		pg_query("DELETE FROM ".$table_location." WHERE id = ".clean($_POST['id']));
    	} else echo "not exist";
    	die;
    }
// размещение end -------------------------------------------------------------------------------------------------------

// помещение begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование помещения в div
    if(isset($_GET['act']) && ($_GET['act']=='n_room' || $_GET['act']=='e_room') ) {
    	if($_GET['act']=='e_room')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM ".$table_room." WHERE id = ".$id;
    		$result=pg_fetch_assoc(pg_query($sql));
    		$room=$result['room'];
    		$descrip=$result['descrip'];
    	}
    
    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="room" value="'.$room.'" placeholder="Помещение" /></div>';
    	$text.='<div class="span6 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
    	$text.=button_ok_cancel('div_new');
    	echo $text;
    	die;
    }

// ввод нового/редактирование помещения в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['room']) && ($_POST['act']=='n_room' || $_POST['act']=='e_room') ) {
    	$sql="SELECT * FROM ".$table_room." WHERE room='".clean($_POST['room'])."'";
    	if($_POST['act']=='n_room') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такое помещение существует!!!";
    		} else {
    			pg_query("INSERT INTO ".$table_room." (room,descrip,user_id) VALUES ('".clean($_POST['room'])."', ".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", ".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_room') {
    		if(@pg_result(pg_query($sql." AND descrip ".($_POST['descrip']?"='".$_POST['descrip']."'":"IS NULL")),0)) {
    			$text="Изменить невозможно, такое помещение существует!!!";
    		} else {
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_room." WHERE id = ".clean($_POST['id']) ));

    			pg_query("UPDATE ".$table_room." SET room='".clean($_POST['room'])."', descrip=".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", user_id=".$user_id." WHERE id=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такое помещение существует!!!");

				$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_room." WHERE id = ".clean($_POST['id']) ));

				$result = serialize(array_diff($data_old, $data_new));
				add_log($table_room,clean($_POST['id']),$result,$user_id);
    		}
    	}
    	echo $text;
    	die;
    }

// удаление помещения div
    if(isset($_GET['act']) && $_GET['act']=='d_room' && @is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM ".$table_node." AS n1 WHERE n1.room_id=".clean($_GET['id']);
    	echo "SELECT COUNT(*) FROM ".$table_node." AS n1 WHERE n1.room_id=".clean($_GET['id']);
    	$room=pg_result(pg_query("SELECT room FROM ".$table_room." AS l1 WHERE l1.id =".clean($_GET['id'])),0);
    	if(pg_result(pg_query($sql),0)) {
    		$text='<div class="span11 m5">&nbsp;Помещение "'.$room.'" используется. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
    	} else {
    		$text='<div class="span10 m5">&nbsp;Удалить помещение "'.$room.'"?</div>'.button_ok_cancel('div_del','d_room');
    	}
    	echo $text;
    	die;
    }

// удаление помещения sql
    if(isset($_POST['act']) && $_POST['act']=='d_room' && @is_numeric($_POST['id']) ) {
    	if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_node." AS n1 WHERE n1.room_id = ".clean($_POST['id']).""),0)) {

    		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_room." WHERE id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
    		add_log($table_room,clean($_POST['id']),$result,$user_id);

    		// удаляем помещение
    		pg_query("DELETE FROM ".$table_room." WHERE id = ".clean($_POST['id']));
    	} else echo "not exist";
    	die;
    }

// помещение end -------------------------------------------------------------------------------------------------------

// ключи begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование ключа в div
    if(isset($_GET['act']) && ($_GET['act']=='n_key' || $_GET['act']=='e_key') ) {
        if($_GET['act']=='e_key')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM ".$table_keys." WHERE id = ".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $num=$result['num'];
            $descrip=$result['descrip'];
        }
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="num" value="'.$num.'" placeholder="Ключ" /></div>';
        $text.='<div class="span6 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.=button_ok_cancel('div_new');
        echo $text;
        die;
    }

// ввод нового/редактирование ключа в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['num']) && ($_POST['act']=='n_key' || $_POST['act']=='e_key') ) {
        $sql="SELECT * FROM ".$table_keys." WHERE num='".clean($_POST['num'])."'";
        if($_POST['act']=='n_key') {
            if(@pg_result(pg_query($sql),0)) {
                $text="Создать невозможно, такой ключ существует!!!";
            } else {
                pg_query("INSERT INTO ".$table_keys." (num,descrip,user_id) VALUES ('".clean($_POST['num'])."', ".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", ".$user_id.")");
            }
        } elseif($_POST['act']=='e_key') {
            if(@pg_result(pg_query($sql." AND descrip ".($_POST['descrip']?"='".$_POST['descrip']."'":"IS NULL")),0)) {
                $text="Изменить невозможно, такой ключ существует!!!";
            } else {
            	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_keys." WHERE id = ".clean($_POST['id']) ));

                pg_query("UPDATE ".$table_keys." SET num='".clean($_POST['num'])."', descrip=".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", user_id=".$user_id." WHERE id=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такой ключ существует!!!");

				$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_keys." WHERE id = ".clean($_POST['id']) ));

				$result = serialize(array_diff($data_old, $data_new));
				add_log($table_keys,clean($_POST['id']),$result,$user_id);
            }
        }
        echo $text;
        die;
    }

// удаление ключа div
    if(isset($_GET['act']) && $_GET['act']=='d_key' && @is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM ".$table_keys." AS k1 WHERE k1.node_id IS NOT NULL AND k1.id=".clean($_GET['id']);
        $result=pg_fetch_assoc(pg_query("SELECT k1.num, n1.id, n1.address FROM ".$table_keys." AS k1 LEFT JOIN ".$table_node." AS n1 ON n1.id = k1.node_id WHERE k1.id =".clean($_GET['id'])),0);
        if(pg_result(pg_query($sql),0)) {
            $text='<div class="span11 m5">&nbsp;Ключ "'.$result['num'].'" используется для узла <a href="?act=s_pq&o_node&node_id='.$result['id'].'" target="_blank">'.$result['address'].'</a>. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
        } else {
            $text='<div class="span10 m5">&nbsp;Удалить ключ "'.$result['num'].'"?</div>'.button_ok_cancel('div_del','d_key');
        }
        echo $text;
        die;
    }
    
// удаление ключа sql
    if(isset($_POST['act']) && $_POST['act']=='d_key' && @is_numeric($_POST['id']) ) {
        if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_keys." AS k1 WHERE k1.node_id IS NOT NULL AND k1.id = ".clean($_POST['id']).""),0)) {

        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_keys." WHERE id = ".clean($_POST['id']) ));
        	$result = serialize($data_old);
        	add_log($table_keys,clean($_POST['id']),$result,$user_id);

            // удаляем ключ
            pg_query("DELETE FROM ".$table_keys." WHERE id = ".clean($_POST['id']));
        } else echo "not exist";
        die;
    }
// ключи end -------------------------------------------------------------------------------------------------------

// ключи к узлам begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование ключи к узлам в div
    if(isset($_GET['act']) && $_GET['act']=='e_key_node' && isset($_GET['node_id']) ) {
        $id=clean($_GET['node_id']);
        $sql="SELECT * FROM ".$table_keys." WHERE node_id IS NULL OR node_id = ".$id." ORDER BY LENGTH(num), num";
        $result = pg_query($sql);
        if(pg_num_rows($result)){
            $select_key='<select id="key_node">';
            //$select_key.='<option value="0">---</option>';
            while($row=pg_fetch_assoc($result)){
                $select_key.='<option value="'.$row['id'].'" '.($row['node_id']==$id?'SELECTED':'').'>'.$row['num'].' '.($row['descrip']?'('.$row['descrip'].')':'').'</option>';
            }
            $select_key.='</select>';
        }
        $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
        $text.='<div class="span3 m0 input-control text">'.$select_key.'</div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        $text.='<script>document.getElementById("key_node").focus();</script>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование/удаление ключи к узлам в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && ( ( $_POST['act']=='e_key_node' && @is_numeric($_POST['num']) ) || $_POST['act']=='d_key_node' ) ) {

    	$data_old=pg_fetch_assoc(pg_query("SELECT num FROM ".$table_keys." WHERE node_id = ".clean($_POST['id']) ));

        // удаление старого
        pg_query("UPDATE ".$table_keys." SET node_id=NULL WHERE node_id=".clean($_POST['id']).";");
        // если изменение, то ввод нового
        if($_POST['act']=='e_key_node') pg_query("UPDATE ".$table_keys." SET node_id=".clean($_POST['id'])." WHERE id=".clean($_POST['num']).";");

        $data_new=pg_fetch_assoc(pg_query("SELECT num FROM ".$table_keys." WHERE node_id = ".clean($_POST['id']) ));
         
        if($_POST['act']=='e_key_node') $result = serialize(array_diff($data_old, $data_new)); else $result = serialize($data_old); 
        if(!empty($data_old)) add_log($table_keys,clean($_POST['id']),$result,$user_id);
        die;
    }
    
// удаление ключи к узлам div
    if(isset($_GET['act']) && $_GET['act']=='d_key_node' && @is_numeric($_GET['node_id'])) {
        $text='
        <div class="span10 m5">&nbsp;Удалить ключ?</div>
        <div class="span2 toolbar m0">
            <input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// ключи к узлам end -------------------------------------------------------------------------------------------------------

// лифтёрки begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование лифтёрки в div
    if(isset($_GET['act']) && ($_GET['act']=='n_lift_type' || $_GET['act']=='e_lift_type') ) {
        if($_GET['act']=='e_lift_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM ".$table_lift_type." WHERE id = ".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $name=$result['name'];
            $tel=$result['tel'];
            $descrip=$result['descrip'];
        }
    
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span4 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Лифтёрка" /></div>';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="tel" value="'.$tel.'" placeholder="Телефоны" /></div>';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.=button_ok_cancel('div_new');
        echo $text;
        die;
    }

// ввод нового/редактирование лифтёрки в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && isset($_POST['tel']) && ($_POST['act']=='n_lift_type' || $_POST['act']=='e_lift_type') ) {
        $sql="SELECT * FROM ".$table_lift_type." WHERE name='".clean($_POST['name'])."'";
        if($_POST['act']=='n_lift_type') {
            if(@pg_result(pg_query($sql),0)) {
                $text="Создать невозможно, такая лифтёрка существует!!!";
            } else {
                pg_query("INSERT INTO ".$table_lift_type." (name,tel,descrip,user_id) VALUES ('".clean($_POST['name'])."', '".clean($_POST['tel'])."', ".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", ".$user_id.")");
            }
        } elseif($_POST['act']=='e_lift_type') {
            if(@pg_result(pg_query($sql." AND descrip ".($_POST['descrip']?"='".$_POST['descrip']."'":"IS NULL")." AND tel ".($_POST['tel']?"='".$_POST['tel']."'":"IS NULL")),0)) {
                $text="Изменить невозможно, такая лифтёрка существует!!!";
            } else {
            	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_lift_type." WHERE id = ".clean($_POST['id']) ));

                pg_query("UPDATE ".$table_lift_type." SET name='".clean($_POST['name'])."', tel='".clean($_POST['tel'])."', descrip=".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", user_id=".$user_id." WHERE id=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такая лифтёрка существует!!!");

				$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_lift_type." WHERE id = ".clean($_POST['id']) ));

				$result = serialize(array_diff($data_old, $data_new));
				add_log($table_lift_type,clean($_POST['id']),$result,$user_id);
            }
        }
        echo $text;
        die;
    }

// удаление лифтёрки div
    if(isset($_GET['act']) && $_GET['act']=='d_lift_type' && @is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM ".$table_lift." AS l1 WHERE l1.lift_id=".clean($_GET['id']);
        $name=pg_result(pg_query("SELECT name FROM ".$table_lift_type." AS lt1 WHERE lt1.id =".clean($_GET['id'])),0);
        if(pg_result(pg_query($sql),0)) {
        	$node_id=pg_result(pg_query("SELECT node_id FROM ".$table_lift." AS l1 WHERE l1.lift_id =".clean($_GET['id'])),0);
            $text='<div class="span11 m5">&nbsp;Лифрётка "'.$name.'" используется <a href="?act=s_pq&p_node&node_id='.$node_id.'" target="_blank">'.addr_id_full($node_id).'</a>. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
        } else {
            $text='<div class="span10 m5">&nbsp;Удалить лифтёрку "'.$name.'"?</div>'.button_ok_cancel('div_del','d_lift_type');
        }
        echo $text;
        die;
    }

// удаление лифтёрки sql
    if(isset($_POST['act']) && $_POST['act']=='d_lift_type' && @is_numeric($_POST['id']) ) {
        if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_lift_type." AS lt1 WHERE lt1.node_id IS NOT NULL AND lt1.id=".clean($_GET['id'])),0)) {

        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_lift_type." WHERE id = ".clean($_POST['id']) ));
        	$result = serialize($data_old);
        	add_log($table_lift_type,clean($_POST['id']),$result,$user_id);

            // удаляем лифтёрку
            pg_query("DELETE FROM ".$table_lift_type." WHERE id = ".clean($_POST['id']));
        } else echo "not exist";
        die;
    }

// лифтёрки end -------------------------------------------------------------------------------------------------------

// лифтёрки к узлам begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование лифтёрки к узлам в div
    if(isset($_GET['act']) && $_GET['act']=='e_lift_node' && isset($_GET['node_id']) ) {
        $id=clean($_GET['node_id']);
        //$sql="SELECT lt1.* FROM ".$table_lift_type." AS lt1 LEFT JOIN ".$table_lift." AS l1 ON lt1.id = l1.lift_id WHERE l1.node_id IS NULL OR l1.node_id = ".$id." ORDER BY lt1.name";
        //$sql="SELECT lt1.*, l1.node_id AS node_id FROM ".$table_lift_type." AS lt1 LEFT JOIN ".$table_lift." AS l1 ON lt1.id = l1.lift_id AND l1.node_id = ".$id." GROUP BY lt1.name ORDER BY lt1.name";
        $sql="SELECT lt1.*, l1.node_id AS node_id FROM ".$table_lift_type." AS lt1 LEFT JOIN ".$table_lift." AS l1 ON lt1.id = l1.lift_id AND l1.node_id = ".$id." ORDER BY lt1.name";
        $result = pg_query($sql);
        if(pg_num_rows($result)){
            $select_lift='<select id="lift_node">';
            while($row=pg_fetch_assoc($result)){
                $select_lift.='<option value="'.$row['id'].'" '.($row['node_id']==$id?'SELECTED':'').'>'.$row['name'].'</option>';
            }
            $select_lift.='</select>';
        }
        $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
        $text.='<div class="span4 m0 input-control text">'.$select_lift.'</div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }

// ввод нового/редактирование/удаление лифтёрки к узлам в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && ( ( $_POST['act']=='e_lift_node' && @is_numeric($_POST['lift']) ) || $_POST['act']=='d_lift_node' ) ) {
        if($_POST['act']=='e_lift_node') {
            if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_lift." AS l1 WHERE l1.node_id=".clean($_POST['id'])),0)) {
                pg_query("INSERT INTO ".$table_lift." (node_id,lift_id,descrip,user_id) VALUES (".clean($_POST['id']).", ".clean($_POST['lift']).", ".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", ".$user_id.")");
            } else {

            	$data_old=pg_fetch_assoc(pg_query("SELECT lift_id FROM ".$table_lift." WHERE node_id = ".clean($_POST['id']) ));

                pg_query("UPDATE ".$table_lift." SET lift_id=".clean($_POST['lift'])." WHERE node_id=".clean($_POST['id']).";");

                $result = serialize($data_old);
                add_log($table_lift,clean($_POST['id']),$result,$user_id);
            }
        } else if($_POST['act']=='d_lift_node') {

        	$data_old=pg_fetch_assoc(pg_query("SELECT lift_id FROM ".$table_lift." WHERE node_id = ".clean($_POST['id']) ));
        	$result = serialize($data_old);
        	add_log($table_lift,clean($_POST['id']),$result,$user_id);

            pg_query("DELETE FROM ".$table_lift." WHERE node_id = ".clean($_POST['id']));
        } else echo "not exist";
        die;
    }
// удаление лифтёрки к узлам div
    if(isset($_GET['act']) && $_GET['act']=='d_lift_node' && @is_numeric($_GET['node_id'])) {
        $text='
        <div class="span10 m5">&nbsp;Удалить лифтёрку?</div>
        <div class="span2 toolbar m0">
            <input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// лифтёрки к узлам end -------------------------------------------------------------------------------------------------------

// описание begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование описания post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && $_POST['act']=='e_descrip_text' ) {
        if(@pg_result(pg_query("SELECT * FROM ".$table_descrip." WHERE node_id=".clean($_POST['id'])),0)) {

        	$data_old=pg_fetch_assoc(pg_query("SELECT text FROM ".$table_descrip." WHERE node_id = ".clean($_POST['id']) ));

            pg_query("UPDATE ".$table_descrip." SET text='".clean($_POST['text'])."', descrip=".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", user_id=".$user_id." WHERE node_id=".clean($_POST['id']).";");

            $result = serialize($data_old);
            if(!empty($data_old)) add_log($table_descrip,clean($_POST['id']),$result,$user_id);
        } else {
            pg_query("INSERT INTO ".$table_descrip." (text, node_id, descrip,user_id) VALUES ('".clean($_POST['text'])."', ".clean($_POST['id']).", ".($_POST['descrip']?"'".$_POST['descrip']."'":"NULL").", ".$user_id.")");
        }
        die;
    }
    
// удаление описание sql
    if(isset($_POST['act']) && $_POST['act']=='d_descrip_text' ) {

    	$data_old=pg_fetch_assoc(pg_query("SELECT text FROM ".$table_descrip." WHERE node_id = ".clean($_POST['id']) ));
    	$result = serialize($data_old);
    	add_log($table_descrip,clean($_POST['id']),$result,$user_id);

        pg_query("DELETE FROM ".$table_descrip." WHERE node_id = ".clean($_POST['id']));
        die;
    }
// описание end -------------------------------------------------------------------------------------------------------

// цвета begin -------------------------------------------------------------------------------------------------------
    // вывод списка выбора цвета модуля/волокна
    if(isset($_POST['act']) && $_POST['act']=='color_select' && is_numeric($_POST['id']) && isset($_POST['type']) ) {
    	$type=($_POST['type']=='mod'?'0':($_POST['type']=='fib'?'1':''));
    	$sql_color="SELECT * FROM ".$table_color." WHERE type=".$type." ORDER BY name ASC";
    	$result_color = pg_query($sql_color);
    	if (pg_num_rows($result_color)) {
    		//$mod_color_select='<select id="mod_color">';
    		$mod_color_select='<div class="color_block">';
    		$mod_color_select.='<ul class="cities_list">';
    		while ($row_color_select = pg_fetch_assoc($result_color)) {
    			//$mod_color_select.='<option id='.$row_color_select['id'].' style="background: #'.$row_color_select['color'].'">'.$row_color_select['name'].'</option>';
    			//$mod_color_select.='<li id='.$row_color_select['id'].' style="background: #'.$row_color_select['color'].'">'.$row_color_select['name'].'</li>';
    			//$mod_color_select.='<li style="background: #'.$row_color_select['color'].'" alt="'.$row_color_select['color'].'" rel_id="'.clean($_POST['id']).'" rel_type="'.$type.'">'.$row_color_select['name'].'</li>';
    			$mod_color_select.='<li style="background: #'.$row_color_select['color'].'" color_id="'.$row_color_select['id'].'" rel_id="'.clean($_POST['id']).'" rel_type="'.clean($_POST['type']).'">'.$row_color_select['name'].'</li>';
    		}
    		$mod_color_select.='<li><button class="icon-blocked mini mini_button" id="exit" title="Отмена">&nbsp;Отмена</button></li>';
    		$mod_color_select.='</ul>';
    		$mod_color_select.='</div>';
    	}
    	echo $mod_color_select;
    	die;
    }

function set_color($table,$id,$type,$color_id) {
	global $user_id;

    $data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table." WHERE id = ".$id ));
    
    pg_query("UPDATE ".$table." SET ".$type."_color=".$color_id.", user_id=".$user_id." WHERE id=".$id.";")
    or die("Изменить невозможно!!!");
    
    $data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table." WHERE id = ".$id ));
     
    $result = serialize(array_diff($data_old, $data_new));
    add_log($table,$id,$result,$user_id);
    return;
}
    // редактирование цвета волокна
    if(isset($_POST['act']) && $_POST['act']=='set_color' && is_numeric($_POST['id']) && isset($_POST['type']) && isset($_POST['color_id']) ) {
    	if(@$_POST['fiber_type']) $table = $table_fiber_type; else $table = $table_fiber;

    	set_color($table,clean($_POST['id']),clean($_POST['type']),clean($_POST['color_id']));
    	//echo "UPDATE ".$table_fiber." SET ".clean($_POST['type'])."_color=".clean($_POST['color_id']).", user_id=".$user_id." WHERE id=".clean($_POST['id']).";";
    	//echo $text;
    	die;
    }

    //ввод нового типа цвета модуля/волокна в div
    if(isset($_GET['act']) && ($_GET['act']=='n_color' || $_GET['act']=='e_color') ) {
    	if($_GET['act']=='e_color')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM ".$table_color." WHERE id=".$id." AND type=".clean($_GET['type']);
    		$result=pg_fetch_assoc(pg_query($sql));
    		$type=$result['type'];
    		$name=$result['name'];
    		$color=$result['color'];
    		$stroke=$result['stroke'];
    		$descrip=$result['descrip'];
    	} else {
    		$type = clean($_GET['type']);
    	}
    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<input type="hidden" id="type" value="'.$type.'">';
    
    	$text.='<div class="span4 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Наименование" /></div>';
    	$text.='<div class="span2 m0 input-control text"><input type="text" id="color" maxlength="6" value="'.$color.'" placeholder="Цвет" /></div>';
    	$text.='<div class="span1 span1_5 m5"><label class="checkbox"><input type="checkbox" id="stroke" '.($stroke==true?'checked':'').'><span>Штрих</span></label></div>';
    	//$text.='<input id="colorpickerField" type="text" value="00ff00" />';
    	//$text.='<div id="colorpickerHolder" style="display: none;"></div>';
    	$text.='<div class="span4 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
    	$text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
    	echo $text.'<script type="text/javascript">color_init();</script>';
    	die;
    }

    // ввод нового типа цвета модуля/волокна в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && @is_numeric($_POST['type']) && ($_POST['act']=='n_color' || $_POST['act']=='e_color') ) {
    	if(!empty($_POST['descrip'])) {
    		$descrip="='".clean($_POST['descrip'])."'";
    		$descrip_sql="'".clean($_POST['descrip'])."'";
    	} else {
    		$descrip="IS NULL";
    		$descrip_sql="NULL";
    	}
    	$sql="SELECT * FROM ".$table_color." WHERE type=".clean($_POST['type'])." AND name='".clean($_POST['name'])."' AND color='".clean($_POST['color'])."' AND stroke ".(empty($_POST['stroke'])?"IS NULL":"=true")."AND descrip ".$descrip;
    	//echo $sql;
    	if($_POST['act']=='n_color') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такой цвет существует!!!";
    		} else {
    			pg_query("INSERT INTO ".$table_color." (type,name,color,stroke,descrip,user_id) VALUES (".clean($_POST['type']).", '".clean($_POST['name'])."', '".clean($_POST['color'])."', ".(empty($_POST['stroke'])?"NULL":"true").", ".$descrip_sql.", ".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_color') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Изменить невозможно, аналогичный цвет существует!!!";
    		} else {
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_color." WHERE id = ".clean($_POST['id']) ));

    			pg_query("UPDATE ".$table_color." SET name='".clean($_POST['name'])."', color='".clean($_POST['color'])."', stroke ".(empty($_POST['stroke'])?"=NULL":"=true").", descrip=".$descrip_sql.", user_id=".$user_id." WHERE id=".clean($_POST['id'])." AND type=".clean($_POST['type']).";");

    			$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_color." WHERE id = ".clean($_POST['id']) ));

    			$result = serialize(array_diff($data_old, $data_new));
    			add_log($table_color,clean($_POST['id']),$result,$user_id);
    		}
    	}
    	echo $text;
    	die;
    }

    // удаление типа цвета модуля/волокна в div
    if(isset($_GET['act']) && $_GET['act']=='d_color' && @is_numeric($_GET['id']) ) {
    	//if(isset($_POST['act']) && $_POST['act']=='d_pq_type' && @is_numeric($_POST['id']) ) {
    	//echo clean($_GET['id']);
    	//$data_old=serialize(pg_fetch_assoc(pg_query("SELECT * FROM ".$table_color." WHERE id = ".clean($_GET['id'])." AND type=".clean($_GET['type']))));
    	//add_log($table_color,$data_old,'',$user_id);
    	 
    	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_color." WHERE id = ".clean($_GET['id']) ));
    	$result = serialize($data_old);
    	add_log($table_color,clean($_GET['id']),$result,$user_id);
    
    	pg_query("DELETE FROM ".$table_color." WHERE id = ".clean($_GET['id'])." AND type=".clean($_GET['type']));
    	echo "reload";
    	//echo $id;
    	die;
    }
    
    // применения цвета кабеля
    //if(isset($_POST['act']) && $_POST['act']=='cable_fiber_color' && @is_numeric($_POST['id']) ) {
    if(isset($_REQUEST['act']) && $_REQUEST['act']=='cable_fiber_color' && @is_numeric($_REQUEST['id']) ) {
		//$cable_type_id=pg_result(pg_query("SELECT cable_type AS total FROM ".$table_cable." WHERE id=".$_POST['id']),0);
    	$cable_type_id=pg_result(pg_query("SELECT cable_type AS total FROM ".$table_cable." WHERE id=".$_REQUEST['id']),0);

		//$sql="SELECT * FROM ".$table_fiber." WHERE cable_id=".clean($_POST['id']);
    	$sql="SELECT * FROM ".$table_fiber." WHERE cable_id=".clean($_REQUEST['id']);
		$result = pg_query($sql);
		if (pg_num_rows($result)) {
			while ($row = pg_fetch_assoc($result)) {
				$fiber_type=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_fiber_type." WHERE cable_id=".$cable_type_id." AND num=".$row['num']." AND mod_color IS NOT NULL AND fib_color IS NOT NULL"));
				if($fiber_type) {
					//echo $cable_type_id.' '.$row['num'].' '.$fiber_type['mod_color'];
					set_color($table_fiber,clean($row['id']),'mod',$fiber_type['mod_color']);
					set_color($table_fiber,clean($row['id']),'fib',$fiber_type['fib_color']);
				}
			}
		}
		//set_color($table,$id,$type,$color_id)
		
//		echo $cable_type;
		//if(isset($_GET['pq_id'])) echo "<html><script>close();</script></html>";
		if(isset($_GET['pq_id'])) echo "<html><script>history.back();</script></html>";
		//header('location: http://pto.rdtc.ru/fibers/index.php?act=s_cable&pq_id='.$_GET['pq_id']);
		die;
//    	if(pg_result(pg_query("SELECT COUNT(*) AS total FROM ".$table_fiber_type." WHERE cable_id=".$_GET['id']." AND mod_color IS NOT NULL AND fib_color IS NOT NULL;"),0)!=$row['fib']

    	//if(isset($_POST['act']) && $_POST['act']=='d_pq_type' && @is_numeric($_POST['id']) ) {
    	//echo clean($_GET['id']);
    	//$data_old=serialize(pg_fetch_assoc(pg_query("SELECT * FROM ".$table_color." WHERE id = ".clean($_GET['id'])." AND type=".clean($_GET['type']))));
    	//add_log($table_color,$data_old,'',$user_id);

    	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_color." WHERE id = ".clean($_GET['id']) ));
    	$result = serialize($data_old);
    	add_log($table_color,clean($_GET['id']),$result,$user_id);
    
    	pg_query("DELETE FROM ".$table_color." WHERE id = ".clean($_GET['id'])." AND type=".clean($_GET['type']));
    	echo "reload";
    	//echo $id;
    	die;
    }
// цвета end -------------------------------------------------------------------------------------------------------

// пользователи start -------------------------------------------------------------------------------------------------------

    //ввод нового пользователя а в div
    if(isset($_GET['act']) && ($_GET['act']=='n_user' || $_GET['act']=='e_user') ) {
    	if($_GET['act']=='e_user')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM ".$table_user." WHERE id=".$id;
    		$result=pg_fetch_assoc(pg_query($sql));
    		$login=$result['login'];
    		$name=$result['name'];
    		$password=$result['password'];
    		$group_id=$result['group'];
    		$status=$result['status'];
    	} else {
    		$group_id=-1;
    	}
    	$select_group='<select id="group_id">';
    	if($_GET['act']=='n_user') $select_group.='<option value="-1">Выберите группу</option>';
    	foreach ($group AS $key=>$group_array) {
    		$select_group.='<option value="'.$key.'"';
    		if($group_id==$key) {
    			$select_group.=" SELECTED";
    		}
    		$select_group.='>'.$group_array['name'].'</option>';
    	}
    	$select_group.='</select>';
    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<input type="hidden" id="pass" value="'.$password.'">';
    
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="login" value="'.$login.'" placeholder="Логин" /></div>';
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Имя" /></div>';
    	$text.='<div class="span2 m0 input-control text"><input type="password" id="password" value="" placeholder="Пароль" /></div>';
    	$text.='<div class="span2 m0 input-control text"><input type="password" id="password2" value="" placeholder="Повторить" /></div>';
    	$text.='<div class="span2 m0 input-control text">'.$select_group.'</div>';
    	$text.='<div class="span1 span1_5 m5"><label class="checkbox"><input type="checkbox" id="status" '.($status==true?'checked':'').'><span>Активен</span></label></div>';
    	$text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
    	echo $text;
    	die;
    }

    // ввод нового пользователя в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['login']) && isset($_POST['name']) && isset($_POST['password']) && @is_numeric($_POST['group']) && ($_POST['act']=='n_user' || $_POST['act']=='e_user') ) {
		if($_POST['password']) $password=md5(clean($_POST['password']));
    	$sql="SELECT * FROM ".$table_user." WHERE login='".clean($_POST['login'])."'";
    	//echo $password;
    	if($_POST['act']=='n_user') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такой пользователь существует!!!";
    		} else {
    			pg_query("INSERT INTO ".$table_user." (login,password,name,status,\"group\") VALUES ('".clean($_POST['login'])."', '".$password."', '".clean($_POST['name'])."', ".(empty($_POST['status'])?"NULL":"true").", ".clean($_POST['group']).")");
    			//echo "INSERT INTO ".$table_user." (login,password,name,status,\"group\") VALUES ('".clean($_POST['login'])."', '".$password."', '".clean($_POST['name'])."', ".(empty($_POST['status'])?"NULL":"true").", ".clean($_POST['group']).")";
    		}
    	} elseif($_POST['act']=='e_user') {
    		if(@pg_result(pg_query($sql." AND name='".clean($_POST['name'])."'".($_POST['password']?" AND password='".$password."'":"")." AND \"group\"=".clean($_POST['group'])." AND status ".($_POST['status']?"=true":" IS NULL")),0)) {
    			$text="Изменить невозможно, аналогичный пользователь существует!!!";
    		} else {
    			//echo "UPDATE ".$table_user." SET login='".clean($_POST['login'])."', name='".clean($_POST['name'])."', ".($_POST['password']?" password='".$password."',":"")." \"group\"=".$_POST['group'].", status=".($_POST['status']?"true":"NULL")." WHERE id=".clean($_POST['id']);
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_user." WHERE id = ".clean($_POST['id']) ));

    			pg_query("UPDATE ".$table_user." SET login='".clean($_POST['login'])."', name='".clean($_POST['name'])."', ".($_POST['password']?" password='".$password."',":"")." \"group\"=".$_POST['group'].", status=".($_POST['status']?"true":"NULL")." WHERE id=".clean($_POST['id']));

    			$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_user." WHERE id = ".clean($_POST['id']) ));
    			
    			$result = serialize(array_diff($data_old, $data_new));
    			add_log($table_user,clean($_POST['id']),$result,$user_id);
    		}
    	}
    	echo $text;
    	die;
    }
// пользователи end -------------------------------------------------------------------------------------------------------

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// узел start -------------------------------------------------------------------------------------------------------
// ввод нового узла div
    //if(isset($_GET['act']) && $_GET['act']=='n_node' ) {   
// редактирование узла div
    if(isset($_GET['act']) && ( $_GET['act']=='n_node' || $_GET['act']=='e_node') ) {
    	if($_GET['act']=='e_node')
    	{
    		$node_id=clean($_GET['node_id']);
    		$sql="SELECT * FROM ".$table_node." WHERE id='".$node_id."';";
    		$sql="SELECT n.*, a.city_id AS city_id
    				FROM ".$table_area." AS a,
    				".$table_node." AS n
    				LEFT JOIN ".$table_street_name." AS s ON s.id = n.street_id  
    				LEFT JOIN ".$table_street_num." AS sn ON sn.id = n.street_num_id
    				WHERE s.area_id = a.id AND n.id='".$node_id."';";
    		//$address=@pg_result(pg_query("SELECT address FROM ".$table_node." WHERE id='".$node_id."';"),0);
    		$result=pg_fetch_assoc(pg_query($sql));
    		$city_id=$result['city_id'];
    		$street_id=$result['street_id'];
    		$street_num_id=$result['street_num_id'];
    		$num_ent=$result['num_ent'];
    		$location_id=$result['location_id'];
    		$room_id=$result['room_id'];
    		$descrip=$result['descrip'];
    		$incorrect=$result['incorrect'];
    		$node_type_id=$result['node_type_id'];
    		$select_node='<select id="id">';
			$select_node.='<option value="'.clean($_GET['node_id']).'" SELECTED">'.$node_id.'</option>';
    		$select_node.='</select>';
    	} else if($_GET['act']=='n_node') {
    		$city_id=clean($_GET['city_id']);
    		pg_query("UPDATE ".$table_node." SET is_new = 'f'::boolean WHERE address IS NOT NULL");
    		// забить в полный адрес с геокодирования
    		$sql="SELECT id, ST_X(ST_astext(the_geom)) AS lon, ST_Y(ST_astext(the_geom)) AS lat FROM ".$table_node." WHERE is_new = true AND address_full IS NULL";
    		$result = pg_query($sql);
    		if(pg_num_rows($result)){
    			while($row=pg_fetch_assoc($result)){
    				pg_query("UPDATE ".$table_node." SET address_full='".geocode($row['lon'].','.$row['lat'])."' WHERE id=".$row['id']);
    				//echo "UPDATE ".$table_node." SET address_full='".geocode($row['lon'].','.$row['lat'])."' WHERE id=".$row['id'];
    			}
    		}
    		 
    		$sql="SELECT * FROM ".$table_node." WHERE is_new = true AND address_full IS NOT NULL AND address IS NULL ORDER BY address_full";
    		$result = pg_query($sql);
    		if(pg_num_rows($result)){
    			$select_node='<select id="id">';
    			$select_node.='<option value="0">Выберите новый узел</option>';
    			while($row=pg_fetch_assoc($result)){
    				$select_node.='<option value="'.$row['id'].'"';
    				if(@$location_id==$row['id']) {
    					$select_node.=" SELECTED";
    				}
    				$select_node.='>'.$row['address_full'].' id: '.$row['id'].'</option>';
    			}
    			$select_node.='</select>';
    		}
    	}
    	// Город
    	$sql="SELECT c1.id, r1.name AS region, c1.name AS city FROM ".$table_city." AS c1, ".$table_region." AS r1 WHERE c1.region_id = r1.id ORDER BY r1.name, c1.name";
    	$result = pg_query($sql);
    	$select_city='<select id="city">';
    	//$select_city.='<option value="0">-Город-</option>';
    	if(pg_num_rows($result)){
    		
    		while($row=pg_fetch_assoc($result)){
    			$select_city.='<option value="'.$row['id'].'"';
    			if(@$city_id==$row['id']) {
    				$select_city.=" SELECTED";
    			}
    			$select_city.='>'.$row['city'].' ('.$row['region'].')</option>';
    		}
    	}
    	$select_city.='</select>';
    	// улица
/*    	$sql="SELECT s1.*, a1.name AS area FROM ".$table_street_name." AS s1, ".$table_area." AS a1 WHERE s1.area_id = a1.id AND a1.id = ".$city_id." ORDER BY s1.name";
    	$result = pg_query($sql);
    	$select_street_name='<select id="street_name">';
    	$select_street_name.='<option value="0">-Улица-</option>';
    	if(pg_num_rows($result)){
    		
    		while($row=pg_fetch_assoc($result)){
    			$select_street_name.='<option value="'.$row['id'].'"';
    			if(@$street_id==$row['id']) {
    				$select_street_name.=" SELECTED";
    			}
    			$select_street_name.='>'.$row['name'].' ('.$row['area'].')</option>';
    		}
    	}
    	$select_street_name.='</select>';
*/
    	$select_street_name=street_list_select($city_id, $street_id);
    	// номер дома
    	if(@$street_num_id) {
    		$sql="SELECT num FROM ".$table_street_num." WHERE id=".$street_num_id." AND street_name_id=".$street_id;
    		$street_num=pg_result(pg_query($sql),0);
    	}
    	// размещение
    	$sql="SELECT * FROM ".$table_location." ORDER BY location";
    	$result = pg_query($sql);
    	if(pg_num_rows($result)){
    		$select_location='<select id="location">';
    		$select_location.='<option value="0">-Размещение-</option>';
    		while($row=pg_fetch_assoc($result)){
    			$select_location.='<option value="'.$row['id'].'"';
    			if(@$location_id==$row['id']) {
    				$select_location.=" SELECTED";
    			}
    			$select_location.='>'.$row['location'].'</option>';
    		}
    		$select_location.='</select>';
    	}
    	// помещение
    	$sql="SELECT * FROM ".$table_room." ORDER BY room";
    	$result = pg_query($sql);
    	if(pg_num_rows($result)){
    		$select_room='<select id="room">';
    		$select_room.='<option value="0">-Помещение-</option>';
    		while($row=pg_fetch_assoc($result)){
    			$select_room.='<option value="'.$row['id'].'"';
    			if(@$room_id==$row['id']) {
    				$select_room.=" SELECTED";
    			}
    			$select_room.='>'.$row['room'].'</option>';
    		}
    		$select_room.='</select>';
    	}
    	// тип узла
    	$sql="SELECT * FROM ".$table_node_type." ORDER BY name";
    	$result = pg_query($sql);
    	if(pg_num_rows($result)){
    		$select_node_type='<select id="node_type">';
    		$select_node_type.='<option value="0">-Тип узла-</option>';
    		while($row=pg_fetch_assoc($result)){
    			$select_node_type.='<option value="'.$row['id'].'"';
    			if(@$node_type_id==$row['id']) {
    				$select_node_type.=" SELECTED";
    			}
    			$select_node_type.='>'.$row['name'].'</option>';
    		}
    		$select_node_type.='</select>';
    	}

    	$text='<input type="hidden" id="act" value="'.clean($_GET['act']).'" />';
    	$text.='<input type="hidden" id="id" value="'.$node_id.'" />';
    	if(!empty($select_node)) {
	    	//$text.='<div class="span3 m0 input-control text" '.($_GET['act']=='e_node'?'style="display: none;"':'').'>'.$select_node.'</div>';
    		$text.='<div class="span3 m0 input-control text" >'.($_GET['act']=='e_node'?'&nbsp;':$select_node).'</div>';
	    	$text.='<div class="span3 m0 input-control text">'.$select_city.'</div>';
	    	$text.='<div class="span3 m0 input-control text">'.$select_street_name.'</div>';
	    	$text.='<div class="span1 span1_5 m0 input-control text"><input class="mini" type="text" id="street_num" value="'.@$street_num.'" placeholder="№ дома" /></div>';
	    	$text.='<div class="span1 span1_5 m0 input-control text"><input class="mini" type="text" id="num_ent" value="'.@$num_ent.'" placeholder="№ подъезда" /></div>';
	    	$text.='<div class="span1 span1_5 m0 input-control text">'.$select_location.'</div>';
	    	$text.='<div class="span1 span1_5 m0 input-control text">'.$select_room.'</div>';
	    	$text.='<div class="span1 span2 m0 input-control text">'.$select_node_type.'</div>';

	    	$text.='<div class="span8 m0 input-control text"><input class="mini" type="text" id="descrip" value="'.@$descrip.'" placeholder="Введите описание" /></div>';
	    	$text.='<div class="span2 m5"><label class="checkbox"><input type="checkbox" id="incorrect" '.(@$incorrect==true?'checked':'').'><span>Проблемма</span></label></div>';
    	} else $text.='<div class="span10 m5">Сначала необходимо занести узел на карте!!!</div>';
    	$text.='<div class="span2 toolbar m0">';
    	if(!empty($select_node)) $text.='<button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>';
		$text.='	<button class="icon-blocked m0" id="exit" title="Отмена"></button>';
        $text.='</div>';
    	echo $text;
    	die;
    }

// ввод нового/редактирование узла в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['street_name_id']) && isset($_POST['street_num']) && ($_POST['act']=='n_node' || $_POST['act']=='e_node') ) {
        // если нету номера улицы, то вносим
        if(!$_POST['street_num_id']) {
            pg_query("INSERT INTO ".$table_street_num." (street_name_id,num,descrip,user_id) VALUES (".clean($_POST['street_name_id']).", '".clean($_POST['street_num'])."', NULL,".$user_id.")");
            $sql="SELECT id FROM ".$table_street_num." WHERE street_name_id=".clean($_POST['street_name_id'])." AND num='".clean($_POST['street_num'])."'";
            $street_num_id=@pg_result(pg_query($sql),0);
        } else $street_num_id=clean($_POST['street_num_id']);

        $sql='SELECT * FROM '.$table_node.' AS n
        	LEFT JOIN '.$table_street_num.' AS sn ON sn.id = n.street_num_id
            WHERE n.street_id='.clean($_POST['street_name_id']).'
            AND n.street_num_id='.$street_num_id.'
            AND n.num_ent '.(empty($_POST['num_ent'])?"IS NULL":"=".$_POST['num_ent']).'
            AND n.location_id '.($_POST['location_id']!=0?"=".$_POST['location_id']:"IS NULL").'
            AND n.room_id '.($_POST['room_id']!=0?"=".$_POST['room_id']:"IS NULL").'
            AND n.incorrect '.(empty($_POST['incorrect'])?"IS NULL":"=true").'
            AND n.node_type_id '.($_POST['node_type_id']!=0?"=".$_POST['node_type_id']:"IS NULL");

            if(@pg_result(pg_query($sql.' AND n.descrip '.(empty($_POST['descrip'])?"IS NULL":"='".$_POST['descrip']."'")),0)) {
                $text="Изменить невозможно, аналогичное помещение существует!!!";
            } else {
            	$id = clean($_POST['id']);
                $sql_u = "UPDATE ".$table_node." SET
                	street_id = ".clean($_POST['street_name_id']).", 
                    street_num_id = ".$street_num_id.",
                    num_ent = ".(empty($_POST['num_ent'])?"NULL":$_POST['num_ent']).",
                    location_id = ".($_POST['location_id']!=0?$_POST['location_id']:"NULL").",
                    room_id = ".($_POST['room_id']!=0?$_POST['room_id']:"NULL").",
                    incorrect=".(empty($_POST['incorrect'])?"NULL":"true").",
                    node_type_id = ".($_POST['node_type_id']!=0?$_POST['node_type_id']:"NULL").",
                    descrip= ".(empty($_POST['descrip'])?"NULL":"'".$_POST['descrip']."'").",
					user_id = ".$user_id.",
					".($_POST['act']=='n_node'?'type = 0,':'')."
					is_new = 'f'::boolean
                    WHERE id = ".$id.";";
                echo $sql_u;
                $data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node." WHERE id = ".$id ));

                pg_query($sql_u);

				pg_query("UPDATE ".$table_node." SET address = '".addr_id($id)."', address_full = '".addr_id_full($id)."' WHERE id = ".$id);
            }
        //}
        //pg_query("UPDATE ".$table_node." SET address='".addr_id($id)."', address_full='".addr_id_full($id)."' WHERE id=".$id);
        $loc_text=addr_id_loc($id);
        if($loc_text)
        	$loc_text="'".$loc_text."'";
        else
        	$loc_text="NULL";
        //pg_query("UPDATE ".$table_node." SET loc_text = ".$loc_text." WHERE id=".$id);
        pg_query("UPDATE ".$table_node." SET address='".addr_id($id)."', address_full='".addr_id_full($id)."', loc_text = ".$loc_text." WHERE id=".$id);

        $data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node." WHERE id = ".$id ));

        $result = serialize(array_diff($data_old, $data_new));
        add_log($table_node,$id,$result,$user_id);

        echo @$text;
        die;
    }

// удаление узла div
    if(isset($_GET['act']) && $_GET['act']=='d_node' && @is_numeric($_GET['node_id'])) {
        $sql="SELECT COUNT(*) FROM ".$table_pq." AS p1 WHERE p1.node =".clean($_GET['node_id']);
        if(pg_result(pg_query($sql),0)) {
            $text='
            <div class="span11 m5">&nbsp;Узел "'.clean($_GET['addr']).'" не пустой. Перед удалением узла необходимо удалить пассивное оборудование!!!</div>
            <div class="span1 toolbar m0">
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
            $text='
            <div class="span10 m5">&nbsp;Удалить узел "'.clean($_GET['addr']).'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_node" rel="'.clean($_GET['node_id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        }
        echo $text;
        die;
    }

// удаление узла
    if(isset($_POST['act']) && $_POST['act']=='d_node' && @is_numeric($_POST['id']) ) {
        if(!@pg_result(pg_query("SELECT * FROM ".$table_node." AS n1, ".$table_pq." AS p1 WHERE n1.id=".clean($_POST['id']))." AND p1.node !=".clean($_POST['id']),0)) {
            $street_num_id=pg_result(pg_query("SELECT street_num_id FROM ".$table_node." WHERE id=".clean($_POST['id'])),0);

            $data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node." WHERE id = ".clean($_POST['id']) ));
            $result = serialize($data_old);
            add_log($table_node,clean($_POST['id']),$result,$user_id);

            pg_query("DELETE FROM ".$table_node." WHERE id = ".clean($_POST['id']));
            if(!pg_result(pg_query("SELECT COUNT(*) FROM ".$table_node." WHERE street_num_id =".$street_num_id),0))
                pg_query("DELETE FROM ".$table_street_num." WHERE id = ".$street_num_id);
            die;
        }
        echo "not exist";
        die;
    }
// узел end -------------------------------------------------------------------------------------------------------

// вывод списка улиц в городе в div
    if(isset($_POST['act']) && $_POST['act']=='street_list' && @is_numeric($_POST['city_id'])) {
    	echo $select_street_name=street_list_select(clean($_POST['city_id']), 0);
    	die;
    }

// типы коммутаторов begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование типа коммутатора в div
    if(isset($_GET['act']) && ($_GET['act']=='n_switch_type' || $_GET['act']=='e_switch_type') ) {
    	if($_GET['act']=='e_switch_type')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM " . $table_switch_type . " WHERE id = ".$id;
    		$result=pg_fetch_assoc(pg_query($sql));
    		$name=$result['name'];
    		$ports_num=$result['ports_num'];
    		$unit=$result['unit'];
    		$power=$result['power'];
    		$descrip=$result['descrip'];
    	}
    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span2 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Название" /></div>';
    	$text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="ports_num" value="'.$ports_num.'" placeholder="Портов" /></div>';
    	$text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" /></div>';
    	$text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="power" value="'.$power.'" placeholder="Мощность" /></div>';
    	$text.='<div class="span4 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
    	$text.='<div class="span2 toolbar m0">
	    	<button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
	    	<button class="icon-blocked m0" id="exit" title="Отмена"></button>
    	</div>';
    	echo $text;
    	die;
    }
    
// ввод нового/редактирование типа коммутатора в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && isset($_POST['ports_num']) && ($_POST['act']=='n_switch_type' || $_POST['act']=='e_switch_type') ) {
    	/*if(!empty($_POST['descrip'])) {
    		$descrip_sql='"'.clean($_POST['descrip']).'"';
    	} else {
    		$descrip_sql="NULL";
    	}*/
    	$descrip_sql=(empty($_POST['descrip'])?'NULL':"'".clean($_POST['descrip'])."'");
    	$sql='SELECT * FROM '.$table_switch_type.' WHERE name="'.clean($_POST['name']).'" AND ports_num='.clean($_POST['ports_num']);
    	if($_POST['act']=='n_switch_type') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такой тип коммутатор существует!!!";
    		} else {
    			pg_query("INSERT INTO ".$table_switch_type." (name,ports_num,unit,power,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".clean($_POST['ports_num']).", ".($_POST['unit']?clean($_POST['unit']):'NULL').", ".($_POST['power']?clean($_POST['power']):'NULL').", ".$descrip_sql.",".$user_id.")");
                die;
    		}
    	} elseif($_POST['act']=='e_switch_type') {
    		if(@pg_result(pg_query($sql.' AND descrip = '.$descrip_sql),0)) {
    			$text="Изменить невозможно, аналогичный тип коммутатора существует!!!";
    		} else {
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_switch_type." WHERE id = ".clean($_POST['id']) ));

    			pg_query("UPDATE ".$table_switch_type." SET name='".clean($_POST['name'])."', ports_num=".clean($_POST['ports_num']).", unit=".($_POST['unit']?clean($_POST['unit']):'NULL').", power=".($_POST['power']?clean($_POST['power']):'NULL').", descrip=".$descrip_sql.", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

    			$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_switch_type." WHERE id = ".clean($_POST['id']) ));
    			
    			$result = serialize(array_diff($data_old, $data_new));
    			add_log($table_switch_type,clean($_POST['id']),$result,$user_id);
                die;
    		}
    	}
    	echo $text;
    	die;
    }
    
// удаление типа коммутатора div
    if(isset($_GET['act']) && $_GET['act']=='d_switch_type' && @is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM ".$table_switches." AS s1 WHERE s1.switch_type_id=".clean($_GET['id']);
    	$name=pg_result(pg_query("SELECT name FROM ".$table_switch_type." AS st1 WHERE st1.id =".clean($_GET['id'])),0);
    	if(pg_result(pg_query($sql),0)) {
    		$text='
    		<div class="span11 m5">&nbsp;Тип коммутатора "'.$name.'" используется. Удалить нельзя!!!</div>
    		<div class="span1 toolbar m0">
    		<button class="icon-blocked m0" id="exit" title="Отмена"></button>
    		</div>';
    	} else {
    		$text='
    		<div class="span10 m5">&nbsp;Удалить тип коммутатора "'.$name.'"?</div>
    		<div class="span2 toolbar m0">
	    		<button class="icon-checkmark m0" id="d_switch_type" rel="'.clean($_GET['id']).'" title="Ok"></button>
	    		<button class="icon-blocked m0" id="exit" title="Отмена"></button>
    		</div>';
    	}
    	echo $text;
    	die;
    }
    
// удаление типа коммутатора sql
    if(isset($_POST['act']) && $_POST['act']=='d_switch_type' && @is_numeric($_POST['id']) ) {
    	if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_switches." AS s1 WHERE s1.switch_type_id = ".clean($_POST['id']).""),0)) {

    		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_switch_type." WHERE id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
    		add_log($table_switch_type,clean($_POST['id']),$result,$user_id);

    		// удаляем коммутатор
    		pg_query("DELETE FROM ".$table_switch_type." WHERE id = ".clean($_POST['id']));
    		die;
    	}
    	echo "not exist";
    	die;
    }
    
// типы коммутаторов end -------------------------------------------------------------------------------------------------------

// коммутаторы begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование коммутаторов в div
    if(isset($_GET['act']) && ($_GET['act']=='n_switches' || $_GET['act']=='e_switches') ) {
        if($_GET['act']=='e_switches')
        {
            $id=clean($_GET['id']);
            //$sql="SELECT *, s1.descrip AS sw_descrip FROM ".$table_switches." s1 LEFT JOIN ".$table_switch_type." AS st1 ON s1.switch_type_id = st1.id WHERE s1.id = ".$id;
            $sql="SELECT sw1 . * , st1.name, st1.ports_num, st1.unit, st1.power, sn1.sn
                FROM ".$table_switch_type." AS st1, ".$table_switches." AS sw1
                LEFT JOIN ".$table_sn." AS sn1 ON sn1.eq = sw1.id AND eq_type='".$switch_id."'
                WHERE st1.id = sw1.switch_type_id
                AND sw1.id = ".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $name=$result['name'];
            $ports_num=$result['ports_num'];
            $used_ports=$result['used_ports'];
            $sn=$result['sn'];
            $descrip=$result['descrip'];
            $text='<input type="hidden" id="id" value="'.$id.'">';
            $text.='<div class="span3 m0 input-control text"><input type="text" value="'.$name.'" placeholder="Название" disabled /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="ports_num" value="'.$ports_num.'" disabled placeholder="Портов" /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="used_ports" value="'.$used_ports.'" placeholder="Занято" /></div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" value="'.$sn.'" disabled placeholder="S/N" /></div>';
        } elseif($_GET['act']=='n_switches') {
            // тип коммутатора
            $sql="SELECT * FROM ".$table_switch_type." ORDER BY name";
            $result = pg_query($sql);
            $select_switch_type_js="var arr = [];";
            if(pg_num_rows($result)){
                $select_switch_type='<select id="switch_type_id" onchange="$(\'input#ports_num\').val(arr[$(\'select#switch_type_id\').val()]);">';
                $select_switch_type.='<option value="0">---</option>';
                while($row=pg_fetch_assoc($result)){
                    $select_switch_type.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
                    $select_switch_type_js.="arr[".$row['id']."] = '".$row['ports_num']."';";
                }
                $select_switch_type.='</select>';
                $select_switch_type='<script type="text/javascript">'.$select_switch_type_js.'</script>'.$select_switch_type;
            }
            $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
            $text.='<div class="span3 m0 input-control text">'.$select_switch_type.'</div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="ports_num" value="" disabled placeholder="Портов" /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="used_ports" value="" placeholder="Занято" /></div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" id="sn" value="" placeholder="S/N" /></div>';
        }
        $text.='<div class="span3 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование коммутатора в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && ($_POST['act']=='n_switches' || $_POST['act']=='e_switches') ) {
        $descrip_sql=(empty($_POST['descrip'])?'NULL':"'".clean($_POST['descrip'])."'");
        if($_POST['act']=='n_switches') {
            $sql_ret = pg_fetch_row(pg_query("INSERT INTO ".$table_switches." (node_id,switch_type_id,used_ports,descrip,user_id) VALUES (".clean($_POST['node_id']).", ".clean($_POST['switch_type_id']).", ".($_POST['used_ports']?clean($_POST['used_ports']):'NULL').", ".$descrip_sql.",".$user_id.") RETURNING Currval('".$table_switches."_id_seq')"));
            if(!empty($_POST['sn'])) {
            	$eq = $sql_ret[0];
                pg_query("INSERT INTO ".$table_sn." (sn,eq,eq_type,descrip,user_id) VALUES ('".clean($_POST['sn'])."', ".$eq.", '".$switch_id."', NULL,".$user_id.")");
                die;                
            }
        } else if($_POST['act']=='e_switches') {
        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_switches." WHERE id = ".clean($_POST['id']) ));

            pg_query("UPDATE ".$table_switches." SET used_ports=".($_POST['used_ports']?clean($_POST['used_ports']):'NULL').", descrip=".$descrip_sql.", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

            $data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_switches." WHERE id = ".clean($_POST['id']) ));

            $result = serialize(array_diff($data_old, $data_new));
            add_log($table_switches,clean($_POST['id']),$result,$user_id);
            die;
        }
        echo $text;
        die;
    }
    
// удаление коммутатора div
    if(isset($_GET['act']) && $_GET['act']=='d_switches' && @is_numeric($_GET['id'])) {
        //$sql="SELECT COUNT(*) FROM ".$table_switches." AS s1 WHERE s1.switch_type_id=".clean($_GET['id']);
        $name=pg_result(pg_query("SELECT name FROM ".$table_switches." AS sw1, ".$table_switch_type." AS st1 WHERE st1.id = sw1.switch_type_id AND sw1.id =".clean($_GET['id'])),0);
            $text='
            <div class="span10 m5">&nbsp;Удалить коммутатор "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_switches" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        //}
        echo $text;
        die;
    }
    
// удаление коммутатора sql
    if(isset($_POST['act']) && $_POST['act']=='d_switches' && @is_numeric($_POST['id']) ) {
        //if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_switches." AS s1 WHERE s1.switch_type_id = ".clean($_POST['id']).""),0)) {

    		$data_old=pg_fetch_assoc(pg_query("SELECT sw1.*,sn1.sn FROM ".$table_switches." AS sw1 LEFT JOIN ".$table_sn." AS sn1 ON sn1.eq = sw1.id WHERE sw1.id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
    		add_log($table_node,clean($_POST['id']),$result,$user_id);

            // удаляем помещение
            pg_query("DELETE FROM ".$table_switches." WHERE id = ".clean($_POST['id']));
            pg_query("DELETE FROM ".$table_sn." WHERE eq = ".clean($_POST['id']));
            //pg_query("DELETE FROM ".$table_switches." WHERE id = ".clean($_POST['id']));
            die;
        //}
        //echo "not exist";
        //die;
    }
    
// коммутаторы end -------------------------------------------------------------------------------------------------------

// типы медиаконвертеров begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование типа коммутатора в div
    if(isset($_GET['act']) && ($_GET['act']=='n_mc_type' || $_GET['act']=='e_mc_type') ) {
        if($_GET['act']=='e_mc_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM " . $table_mc_type . " WHERE id = ".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $name=$result['name'];
            $power=$result['power'];
            $descrip=$result['descrip'];
        }
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Название" /></div>';
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="power" value="'.$power.'" placeholder="Мощность" /></div>';
        $text.='<div class="span6 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// ввод нового/редактирование типа медиаконвертера в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_mc_type' || $_POST['act']=='e_mc_type') ) {
        $descrip_sql=(empty($_POST['descrip'])?'NULL':"'".clean($_POST['descrip'])."'");
        $sql="SELECT * FROM ".$table_mc_type." WHERE name='".clean($_POST['name'])."'";
        if($_POST['act']=='n_mc_type') {
            if(@pg_result(pg_query($sql),0)) {
                $text="Создать невозможно, такой тип медиаконвертера существует!!!";
            } else {
                pg_query("INSERT INTO ".$table_mc_type." (name,power,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".($_POST['power']?clean($_POST['power']):'NULL').", ".$descrip_sql.",".$user_id.")");
                die;
            }
        } elseif($_POST['act']=='e_mc_type') {
            if(@pg_result(pg_query($sql.' AND descrip = '.$descrip_sql),0)) {
                $text="Изменить невозможно, аналогичный тип медиаконвертера существует!!!";
            } else {
            	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_mc_type." WHERE id = ".clean($_POST['id']) ));

                pg_query("UPDATE ".$table_mc_type." SET name='".clean($_POST['name'])."', power=".($_POST['power']?clean($_POST['power']):'NULL').", descrip=".$descrip_sql.", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

                $data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_mc_type." WHERE id = ".clean($_POST['id']) ));

                $result = serialize(array_diff($data_old, $data_new));
                add_log($table_mc_type,clean($_POST['id']),$result,$user_id);
                die;
            }
        }
        echo $text;
        die;
    }
    
// удаление типа медиаконвертера div
    if(isset($_GET['act']) && $_GET['act']=='d_mc_type' && @is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM ".$table_mc." AS mc1 WHERE mc1.mc_type_id=".clean($_GET['id']);
        $name=pg_result(pg_query("SELECT name FROM ".$table_mc_type." AS mc1 WHERE mc1.id =".clean($_GET['id'])),0);
        if(pg_result(pg_query($sql),0)) {
            $text='
            <div class="span11 m5">&nbsp;Тип медиаконвертера "'.$name.'" используется. Удалить нельзя!!!</div>
            <div class="span1 toolbar m0">
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
            $text='
            <div class="span10 m5">&nbsp;Удалить тип медиаконвертера "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_mc_type" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        }
        echo $text;
        die;
    }
    
// удаление типа медиаконвертера sql
    if(isset($_POST['act']) && $_POST['act']=='d_mc_type' && @is_numeric($_POST['id']) ) {
        if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_mc." AS mc1 WHERE mc1.mc_type_id = ".clean($_POST['id']).""),0)) {
        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_mc_type." WHERE id = ".clean($_POST['id']) ));
        	$result = serialize($data_old);
        	add_log($table_mc_type,clean($_POST['id']),$result,$user_id);

            // удаляем помещение
            pg_query("DELETE FROM ".$table_mc_type." WHERE id = ".clean($_POST['id']));
            die;
        }
        echo "not exist";
        die;
    }
    
// типы медиаконвертеров end -------------------------------------------------------------------------------------------------------

// типы узлов begin -------------------------------------------------------------------------------------------------------
    // ввод нового/редактирование типа узла в div
    if(isset($_GET['act']) && ($_GET['act']=='n_node_type' || $_GET['act']=='e_node_type') ) {
    	if($_GET['act']=='e_node_type')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM " . $table_node_type . " WHERE id = ".$id;
    		$result=pg_fetch_assoc(pg_query($sql));
    		$name=$result['name'];
    		$descrip=$result['descrip'];
    	}
    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Название" /></div>';
    	$text.='<div class="span6 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
    	$text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
    	echo $text;
    	die;
    }

    // ввод нового/редактирование типа узла в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_node_type' || $_POST['act']=='e_node_type') ) {
    	$descrip_sql=(empty($_POST['descrip'])?'NULL':"'".clean($_POST['descrip'])."'");
    	$sql="SELECT * FROM ".$table_node_type." WHERE name='".clean($_POST['name'])."'";
    	if($_POST['act']=='n_node_type') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такой тип узла существует!!!";
    		} else {
    			pg_query("INSERT INTO ".$table_node_type." (name,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".$descrip_sql.",".$user_id.")");
    			die;
    		}
    	} elseif($_POST['act']=='e_node_type') {
    		if(@pg_result(pg_query($sql.' AND descrip = '.$descrip_sql),0)) {
    			$text="Изменить невозможно, аналогичный тип узла существует!!!";
    		} else {
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node_type." WHERE id = ".clean($_POST['id']) ));
    
    			pg_query("UPDATE ".$table_node_type." SET name='".clean($_POST['name'])."', descrip=".$descrip_sql.", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");
    
    			$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node_type." WHERE id = ".clean($_POST['id']) ));
    
    			$result = serialize(array_diff($data_old, $data_new));
    			add_log($table_node_type,clean($_POST['id']),$result,$user_id);
    			die;
    		}
    	}
    	echo $text;
    	die;
    }
    
    // удаление типа узла div
    if(isset($_GET['act']) && $_GET['act']=='d_node_type' && @is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM ".$table_node." AS n1 WHERE n1.node_type_id=".clean($_GET['id']);
    	$name=pg_result(pg_query("SELECT name FROM ".$table_node_type." AS n1 WHERE n1.id =".clean($_GET['id'])),0);
    	if(pg_result(pg_query($sql),0)) {
    		$text='
            <div class="span11 m5">&nbsp;Тип узла "'.$name.'" используется. Удалить нельзя!!!</div>
            <div class="span1 toolbar m0">
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
    	} else {
    		$text='
            <div class="span10 m5">&nbsp;Удалить тип узла "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_node_type" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
    	}
    	echo $text;
    	die;
    }
    
    // удаление типа узла sql
    if(isset($_POST['act']) && $_POST['act']=='d_node_type' && @is_numeric($_POST['id']) ) {
    	if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_node." AS n1 WHERE n1.type2 = ".clean($_POST['id']).""),0)) {
    		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node_type." WHERE id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
    		add_log($table_node_type,clean($_POST['id']),$result,$user_id);
    
    		// удаляем тип узла
    		pg_query("DELETE FROM ".$table_node_type." WHERE id = ".clean($_POST['id']));
    		die;
    	}
    	echo "not exist";
    	die;
    }
    
// типы узлов end -------------------------------------------------------------------------------------------------------

// медиаконвертеры begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование медиакорветрета в div
    if(isset($_GET['act']) && ($_GET['act']=='n_mc' || $_GET['act']=='e_mc') ) {
        if($_GET['act']=='e_mc')
        {
            $id=clean($_GET['id']);
            $sql="SELECT mc1 . * , mt1.name, mt1.power, sn1.sn
                FROM ".$table_mc_type." AS mt1, ".$table_mc." AS mc1
                LEFT JOIN ".$table_sn." AS sn1 ON sn1.eq = mc1.id AND eq_type='".$mc_id."'
                WHERE mt1.id = mc1.mc_type_id
                AND mc1.id = ".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $name=$result['name'];
            $sn=$result['sn'];
            $descrip=$result['descrip'];
            $text='<input type="hidden" id="id" value="'.$id.'">';
            $text.='<div class="span3 m0 input-control text"><input type="text" value="'.$name.'" placeholder="Название" disabled /></div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" value="'.$sn.'" disabled placeholder="S/N" /></div>';
        } elseif($_GET['act']=='n_mc') {
            // тип медиаконвертера
            $sql="SELECT * FROM ".$table_mc_type." ORDER BY name";
            $result = pg_query($sql);
            if(pg_num_rows($result)){
                $select_mc_type='<select id="mc_type_id">';
                $select_mc_type.='<option value="0">---</option>';
                while($row=pg_fetch_assoc($result)){
                    $select_mc_type.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
                }
                $select_mc_type.='</select>';
            }
            $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
            $text.='<div class="span3 m0 input-control text">'.$select_mc_type.'</div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" id="sn" value="" placeholder="S/N" /></div>';
        }
        $text.='<div class="span5 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование медиаконвертера в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && ($_POST['act']=='n_mc' || $_POST['act']=='e_mc') ) {
        $descrip_sql=(empty($_POST['descrip'])?'NULL':"'".clean($_POST['descrip'])."'");
        if($_POST['act']=='n_mc') {
            $sql_ret = pg_fetch_row(pg_query("INSERT INTO ".$table_mc." (node_id,mc_type_id,descrip,user_id) VALUES (".clean($_POST['node_id']).", ".clean($_POST['mc_type_id']).", ".$descrip_sql.", ".$user_id.") RETURNING Currval('".$table_mc."_id_seq')"));
            //echo "INSERT INTO ".$table_mc." (node_id,mc_type_id,descrip,user_id) VALUES (".clean($_POST['node_id']).", ".clean($_POST['mc_type_id']).", ".$descrip_sql.", ".$user_id.")";
            if(!empty($_POST['sn'])) {
                $eq=$sql_ret[0];
                //echo $eq." INSERT INTO ".$table_sn." (sn,eq,eq_type,descrip,user_id) VALUES ('".clean($_POST['sn'])."', ".$eq.", '".$mc_id."', NULL,".$user_id.")";
                pg_query("INSERT INTO ".$table_sn." (sn,eq,eq_type,descrip,user_id) VALUES ('".clean($_POST['sn'])."', ".$eq.", '".$mc_id."', NULL,".$user_id.")");
                die;                
            }
        } else if($_POST['act']=='e_mc') {
        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_mc." WHERE id = ".clean($_POST['id']) ));

            pg_query("UPDATE ".$table_mc." SET descrip=".$descrip_sql.", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

            $data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_mc." WHERE id = ".clean($_POST['id']) ));
            
            $result = serialize(array_diff($data_old, $data_new));
            add_log($table_mc,clean($_POST['id']),$result,$user_id);
            die;
        }
        echo $text;
        die;
    }
    
// удаление медиаконвертера div
    if(isset($_GET['act']) && $_GET['act']=='d_mc' && @is_numeric($_GET['id'])) {
        $name=pg_result(pg_query("SELECT name FROM ".$table_mc." AS mc1, ".$table_mc_type." AS mt1 WHERE mt1.id = mc1.mc_type_id AND mc1.id =".clean($_GET['id'])),0);
            $text='
            <div class="span10 m5">&nbsp;Удалить медиаконвертер "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_mc" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        echo $text;
        die;
    }
    
// удаление медиаконвертера sql
    if(isset($_POST['act']) && $_POST['act']=='d_mc' && @is_numeric($_POST['id']) ) {
    		$data_old=pg_fetch_assoc(pg_query("SELECT mc1.*,sn1.sn FROM ".$table_mc." AS mc1 LEFT JOIN ".$table_sn." AS sn1 ON sn1.eq = mc1.id WHERE mc1.id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
	    	add_log($table_mc,clean($_POST['id']),$result,$user_id);

            pg_query("DELETE FROM ".$table_mc." WHERE id = ".clean($_POST['id']));
            pg_query("DELETE FROM ".$table_sn." WHERE eq = ".clean($_POST['id']));
            die;
    }
    
// медиаконвертеры end -------------------------------------------------------------------------------------------------------

// типы рам/ящиков begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование типа рамы/ящика в div
    if(isset($_GET['act']) && ($_GET['act']=='n_box_type' || $_GET['act']=='e_box_type') ) {
        if($_GET['act']=='e_box_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM ".$table_box_type." WHERE id = ".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $name=$result['name'];
            $unit=$result['unit'];
            $descrip=$result['descrip'];
        }
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Название" /></div>';
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" /></div>';
        $text.='<div class="span6 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// ввод нового/редактирование типа рамы/ящика в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_box_type' || $_POST['act']=='e_box_type') ) {
        $descrip_sql=(empty($_POST['descrip'])?'NULL':"'".clean($_POST['descrip'])."'");
        $sql="SELECT * FROM ".$table_box_type." WHERE name='".clean($_POST['name'])."'";
        if($_POST['act']=='n_box_type') {
            if(@pg_result(pg_query($sql),0)) {
                $text="Создать невозможно, такой тип рамы/ящика существует!!!";
            } else {
                pg_query("INSERT INTO ".$table_box_type." (name,unit,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".($_POST['unit']?clean($_POST['unit']):'NULL').", ".$descrip_sql.",".$user_id.")");
                //die;
            }
        } elseif($_POST['act']=='e_box_type') {
            if(@pg_result(pg_query($sql." AND descrip = ".$descrip_sql),0)) {
                $text="Изменить невозможно, аналогичный тип рамы/ящика существует!!!";
            } else {
            	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_box_type." WHERE id = ".clean($_POST['id']) ));

                pg_query("UPDATE ".$table_box_type." SET name='".clean($_POST['name'])."', unit=".($_POST['unit']?clean($_POST['unit']):'NULL').", descrip=".$descrip_sql.", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

                $data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_box_type." WHERE id = ".clean($_POST['id']) ));

                $result = serialize(array_diff($data_old, $data_new));
                add_log($table_box_type,clean($_POST['id']),$result,$user_id);
                //die;
            }
        }
        echo $text;
        die;
    }
    
// удаление типа рамы/ящика div
    if(isset($_GET['act']) && $_GET['act']=='d_box_type' && @is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM ".$table_box." AS b1 WHERE b1.box_type_id=".clean($_GET['id']);
        $name=pg_result(pg_query("SELECT name FROM ".$table_box_type." AS b1 WHERE b1.id =".clean($_GET['id'])),0);
        if(pg_result(pg_query($sql),0)) {
            $text='
            <div class="span11 m5">&nbsp;Тип рамы/ящика "'.$name.'" используется. Удалить нельзя!!!</div>
            <div class="span1 toolbar m0">
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
            $text='
            <div class="span10 m5">&nbsp;Удалить тип рамы/ящика "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_box_type" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        }
        echo $text;
        die;
    }
    
// удаление типа рамы/ящика sql
    if(isset($_POST['act']) && $_POST['act']=='d_box_type' && @is_numeric($_POST['id']) ) {
        if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_box." AS b1 WHERE b1.box_type_id = ".clean($_POST['id']).""),0)) {
        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_box_type." WHERE id = ".clean($_POST['id']) ));
        	$result = serialize($data_old);
        	add_log($table_box_type,clean($_POST['id']),$result,$user_id);

            // удаляем тип раму/ящика
            pg_query("DELETE FROM ".$table_box_type." WHERE id = ".clean($_POST['id']));
            die;
        }
        echo "not exist";
        die;
    }
    
// типы рам/ящиков end -------------------------------------------------------------------------------------------------------

// рамы/ящики begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование рам/ящиков в div
    if(isset($_GET['act']) && ($_GET['act']=='n_box' || $_GET['act']=='e_box') ) {
        if($_GET['act']=='e_box')
        {
            $id=clean($_GET['id']);
            $sql="SELECT b1 . * , bt1.name, bt1.unit
                FROM ".$table_box_type." AS bt1, ".$table_box." AS b1
                WHERE bt1.id = b1.box_type_id
                AND b1.id = ".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $name=$result['name'];
            $descrip=$result['descrip'];
            $text='<input type="hidden" id="id" value="'.$id.'">';
            $text.='<div class="span3 m0 input-control text"><input type="text" value="'.$name.'" placeholder="Название" disabled /></div>';
        } elseif($_GET['act']=='n_box') {
            // тип рам/ящиков
            $sql="SELECT * FROM ".$table_box_type." ORDER BY name";
            $result = pg_query($sql);
            if(pg_num_rows($result)){
                $select_box_type='<select id="box_type_id">';
                $select_box_type.='<option value="0">---</option>';
                while($row=pg_fetch_assoc($result)){
                    $select_box_type.='<option value="'.$row['id'].'">'.$row['name'].($row['unit']?' ('.$row['unit'].'U)':'').'</option>';
                }
                $select_box_type.='</select>';
            }
            $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
            $text.='<div class="span3 m0 input-control text">'.$select_box_type.'</div>';
        }
        $text.='<div class="span7 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование рам/ящиков в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && ($_POST['act']=='n_box' || $_POST['act']=='e_box') ) {
        $descrip_sql=(empty($_POST['descrip'])?'NULL':"'".clean($_POST['descrip'])."'");
        if($_POST['act']=='n_box') {
            pg_query("INSERT INTO ".$table_box." (node_id,box_type_id,descrip,user_id) VALUES (".clean($_POST['node_id']).", ".clean($_POST['box_type_id']).", ".$descrip_sql.", ".$user_id.")");
            die;
        } else if($_POST['act']=='e_box') {
        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_box." WHERE id = ".clean($_POST['id']) ));

            pg_query("UPDATE ".$table_box." SET descrip=".$descrip_sql.", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

            $data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_box." WHERE id = ".clean($_POST['id']) ));

            $result = serialize(array_diff($data_old, $data_new));
            add_log($table_box,clean($_POST['id']),$result,$user_id);
            die;
        }
        echo $text;
        die;
    }
    
// удаление рам/ящиков div
    if(isset($_GET['act']) && $_GET['act']=='d_box' && @is_numeric($_GET['id'])) {
        $name=pg_result(pg_query("SELECT name FROM ".$table_box." AS b1, ".$table_box_type." AS bt1 WHERE bt1.id = b1.box_type_id AND b1.id =".clean($_GET['id'])),0);
            $text='
            <div class="span10 m5">&nbsp;Удалить раму/ящик "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_box" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        echo $text;
        die;
    }
    
// удаление рам/ящиков sql
    if(isset($_POST['act']) && $_POST['act']=='d_box' && @is_numeric($_POST['id']) ) {
    		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_box." WHERE id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
    		add_log($table_box,clean($_POST['id']),$result,$user_id);

            pg_query("DELETE FROM ".$table_box." WHERE id = ".clean($_POST['id']));
            die;
    }
    
// рамы/ящики end -------------------------------------------------------------------------------------------------------

// типы ИБП begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование типа ИБП в div
    if(isset($_GET['act']) && ($_GET['act']=='n_ups_type' || $_GET['act']=='e_ups_type') ) {
        if($_GET['act']=='e_ups_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM " . $table_ups_type . " WHERE id = ".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $name=$result['name'];
            $unit=$result['unit'];
            $power=$result['power'];
            $descrip=$result['descrip'];
        }
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Название" /></div>';
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" /></div>';
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="power" value="'.$power.'" placeholder="Мощность" /></div>';
        $text.='<div class="span4 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// ввод нового/редактирование типа ИБП в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_ups_type' || $_POST['act']=='e_ups_type') ) {
        $sql='SELECT * FROM '.$table_ups_type.' WHERE name="'.clean($_POST['name']).'"';
        if($_POST['act']=='n_ups_type') {
            if(@pg_result(pg_query($sql),0)) {
                $text="Создать невозможно, такой тип ИБП существует!!!";
            } else {
                pg_query("INSERT INTO ".$table_ups_type." (name,unit,power,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".($_POST['unit']?clean($_POST['unit']):'NULL').", ".($_POST['power']?clean($_POST['power']):'NULL').", ".($_POST['descrip']?"'".clean($_POST['descrip'])."'":'NULL').",".$user_id.")");
                die;
            }
        } elseif($_POST['act']=='e_ups_type') {
            if(@pg_result(pg_query($sql.' AND descrip = '.($_POST['descrip']?"'".clean($_POST['descrip'])."'":'NULL')),0)) {
                $text="Изменить невозможно, аналогичный тип ИБП существует!!!";
            } else {
            	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_ups_type." WHERE id = ".clean($_POST['id']) ));

                pg_query("UPDATE ".$table_ups_type." SET name='".clean($_POST['name'])."', unit=".($_POST['unit']?clean($_POST['unit']):'NULL').", power=".($_POST['power']?clean($_POST['power']):'NULL').", descrip=".($_POST['descrip']?"'".clean($_POST['descrip'])."'":'NULL').", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

                $data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_ups_type." WHERE id = ".clean($_POST['id']) ));

                $result = serialize(array_diff($data_old, $data_new));
                add_log($table_ups_type,clean($_POST['id']),$result,$user_id);
                die;
            }
        }
        echo $text;
        die;
    }
    
// удаление типа ИБП div
    if(isset($_GET['act']) && $_GET['act']=='d_ups_type' && @is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM ".$table_ups." AS u1 WHERE u1.ups_type_id=".clean($_GET['id']);
        $name=pg_result(pg_query("SELECT name FROM ".$table_ups_type." AS u1 WHERE u1.id =".clean($_GET['id'])),0);
        if(pg_result(pg_query($sql),0)) {
            $text='
            <div class="span11 m5">&nbsp;Тип ИБП "'.$name.'" используется. Удалить нельзя!!!</div>
            <div class="span1 toolbar m0">
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
            $text='
            <div class="span10 m5">&nbsp;Удалить тип ИБП "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_ups_type" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        }
        echo $text;
        die;
    }
    
// удаление типа ИБП sql
    if(isset($_POST['act']) && $_POST['act']=='d_ups_type' && @is_numeric($_POST['id']) ) {
        if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_ups." AS u1 WHERE u1.ups_type_id = ".clean($_POST['id']).""),0)) {
        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_ups_type." WHERE id = ".clean($_POST['id']) ));
        	$result = serialize($data_old);
        	add_log($table_ups_type,clean($_POST['id']),$result,$user_id);

            pg_query("DELETE FROM ".$table_ups_type." WHERE id = ".clean($_POST['id']));
            die;
        }
        echo "not exist";
        die;
    }
// типы ИБП end -------------------------------------------------------------------------------------------------------

// ИБП begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование ИБП в div
    if(isset($_GET['act']) && ($_GET['act']=='n_ups' || $_GET['act']=='e_ups') ) {
        if($_GET['act']=='e_ups')
        {
            $id=clean($_GET['id']);
            $sql="SELECT u1 . * , ut1.name, ut1.unit, ut1.power, sn1.sn
                FROM ".$table_ups_type." AS ut1, ".$table_ups." AS u1
                LEFT JOIN ".$table_sn." AS sn1 ON sn1.eq = u1.id AND eq_type='".$ups_id."'
                WHERE ut1.id = u1.ups_type_id
                AND u1.id = ".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $name=$result['name'];
            $unit=($result['unit']?$result['unit'].'U':'');
            $power=($result['power']?$result['power'].'W':'');
            $sn=$result['sn'];
            $descrip=$result['descrip'];
            $text='<input type="hidden" id="id" value="'.$id.'">';
            $text.='<div class="span3 m0 input-control text"><input type="text" value="'.$name.'" placeholder="Название" disabled /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" disabled /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="power" value="'.$power.'" placeholder="Мощность" disabled /></div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" value="'.$sn.'" disabled placeholder="S/N" /></div>';
        } elseif($_GET['act']=='n_ups') {
            // тип ИБП
            $sql="SELECT * FROM ".$table_ups_type." ORDER BY name";
            $result = pg_query($sql);
            $select_ups_type_js="var arr = [],arr2 = [];";
            if(pg_num_rows($result)){
                //$select_ups_type='<select id="ups_type_id" onchange="$(\'input#unit\').val(arr[$(\'select#ups_type_id\').val()]);">';
                $select_ups_type='<select id="ups_type_id" onchange="$(\'input#unit\').val(arr[$(\'select#ups_type_id\').val()]);$(\'input#power\').val(arr2[$(\'select#ups_type_id\').val()]);">';
                $select_ups_type.='<option value="0">---</option>';
                while($row=pg_fetch_assoc($result)){
                    $select_ups_type.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
                    $select_ups_type_js.="arr[".$row['id']."] = '".($row['unit']?$row['unit'].'U':'')."';arr2[".$row['id']."] = '".($row['power']?$row['power'].'W':'')."';";
                }
                $select_ups_type.='</select>';
                $select_ups_type='<script type="text/javascript">'.$select_ups_type_js.'</script>'.$select_ups_type;
            }
            $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
            $text.='<div class="span3 m0 input-control text">'.$select_ups_type.'</div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" disabled /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="power" value="'.$power.'" placeholder="Мощность" disabled /></div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" id="sn" value="" placeholder="S/N" /></div>';
        }
        $text.='<div class="span3 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование ИБП в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && ($_POST['act']=='n_ups' || $_POST['act']=='e_ups') ) {
        if($_POST['act']=='n_ups') {
        	//echo "INSERT INTO ".$table_ups." (node_id,ups_type_id,descrip,user_id) VALUES (".clean($_POST['node_id']).", ".clean($_POST['ups_type_id']).", ".($_POST['descrip']?"'".clean($_POST['descrip'])."'":'NULL').", ".$user_id.") RETURNING Currval('".$table_ups."_id_seq')";
            $sql_ret = pg_fetch_row(pg_query("INSERT INTO ".$table_ups." (node_id,ups_type_id,descrip,user_id) VALUES (".clean($_POST['node_id']).", ".clean($_POST['ups_type_id']).", ".($_POST['descrip']?"'".clean($_POST['descrip'])."'":'NULL').", ".$user_id.") RETURNING Currval('".$table_ups."_id_seq')"));
            if(!empty($_POST['sn'])) {
                $eq=$sql_ret[0];
                pg_query("INSERT INTO ".$table_sn." (sn,eq,eq_type,descrip,user_id) VALUES ('".clean($_POST['sn'])."', ".$eq.", '".$ups_id."', NULL,".$user_id.")");
                die;                
            }
        } else if($_POST['act']=='e_ups') {
        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_ups." WHERE id = ".clean($_POST['id']) ));

            pg_query("UPDATE ".$table_ups." SET descrip=".($_POST['descrip']?"'".clean($_POST['descrip'])."'":'NULL').", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

            $data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_ups." WHERE id = ".clean($_POST['id']) ));

            $result = serialize(array_diff($data_old, $data_new));
            add_log($table_ups,clean($_POST['id']),$result,$user_id);
            die;
        }
        echo $text;
        die;
    }
    
// удаление ИБП div
    if(isset($_GET['act']) && $_GET['act']=='d_ups' && @is_numeric($_GET['id'])) {
        $name=pg_result(pg_query("SELECT name FROM ".$table_ups." AS u1, ".$table_ups_type." AS ut1 WHERE ut1.id = u1.ups_type_id AND u1.id =".clean($_GET['id'])),0);
            $text='
            <div class="span10 m5">&nbsp;Удалить ИБП "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_ups" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        echo $text;
        die;
    }
    
// удаление ИБП sql
    if(isset($_POST['act']) && $_POST['act']=='d_ups' && @is_numeric($_POST['id']) ) {
    		$data_old=pg_fetch_assoc(pg_query("SELECT ups1.*,sn1.sn FROM ".$table_ups." AS ups1 LEFT JOIN ".$table_sn." AS sn1 ON sn1.eq = ups1.id WHERE ups1.id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
	    	add_log($table_ups,clean($_POST['id']),$result,$user_id);

            pg_query("DELETE FROM ".$table_ups." WHERE id = ".clean($_POST['id']));
            pg_query("DELETE FROM ".$table_sn." WHERE eq = ".clean($_POST['id']));
            die;
    }
// ИБП end -------------------------------------------------------------------------------------------------------

// типы прочего оборудования begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование типа прочего оборудования в div
    if(isset($_GET['act']) && ($_GET['act']=='n_other_type' || $_GET['act']=='e_other_type') ) {
        if($_GET['act']=='e_other_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM " . $table_other_type . " WHERE id = ".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $name=$result['name'];
            $unit=$result['unit'];
            $descrip=$result['descrip'];
        }
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Название" /></div>';
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" /></div>';
        $text.='<div class="span4 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// ввод нового/редактирование типа прочего оборудования в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_other_type' || $_POST['act']=='e_other_type') ) {
        $sql='SELECT * FROM '.$table_other_type.' WHERE name="'.clean($_POST['name']).'"';
        if($_POST['act']=='n_other_type') {
            if(@pg_result(pg_query($sql),0)) {
                $text="Создать невозможно, такой тип прочего оборудования существует!!!";
            } else {
                pg_query("INSERT INTO ".$table_other_type." (name,unit,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".($_POST['unit']?clean($_POST['unit']):'NULL').", ".($_POST['descrip']?"'".clean($_POST['descrip'])."'":'NULL').",".$user_id.")");
                die;
            }
        } elseif($_POST['act']=='e_other_type') {
            if(@pg_result(pg_query($sql.' AND descrip = '.($_POST['descrip']?"'".clean($_POST['descrip'])."'":'NULL')),0)) {
                $text="Изменить невозможно, аналогичный тип прочего оборудования существует!!!";
            } else {
            	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_other_type." WHERE id = ".clean($_POST['id']) ));

                pg_query("UPDATE ".$table_other_type." SET name='".clean($_POST['name'])."', unit=".($_POST['unit']?clean($_POST['unit']):'NULL').", descrip=".($_POST['descrip']?"'".clean($_POST['descrip'])."'":'NULL').", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

                $data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_other_type." WHERE id = ".clean($_POST['id']) ));

                $result = serialize(array_diff($data_old, $data_new));
                add_log($table_other_type,clean($_POST['id']),$result,$user_id);
                die;
            }
        }
        echo $text;
        die;
    }
    
// удаление типа прочего оборудования div
    if(isset($_GET['act']) && $_GET['act']=='d_other_type' && @is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM ".$table_other." AS o1 WHERE o1.other_type_id=".clean($_GET['id']);
        $name=pg_result(pg_query("SELECT name FROM ".$table_other_type." AS o1 WHERE o1.id =".clean($_GET['id'])),0);
        if(pg_result(pg_query($sql),0)) {
            $text='
            <div class="span11 m5">&nbsp;Тип прочего оборудования "'.$name.'" используется. Удалить нельзя!!!</div>
            <div class="span1 toolbar m0">
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
            $text='
            <div class="span10 m5">&nbsp;Удалить тип прочего оборудования "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_other_type" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        }
        echo $text;
        die;
    }
    
// удаление типа прочего оборудования sql
    if(isset($_POST['act']) && $_POST['act']=='d_other_type' && @is_numeric($_POST['id']) ) {
        if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_other." AS o1 WHERE o1.other_type_id = ".clean($_POST['id']).""),0)) {
        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_other_type." WHERE id = ".clean($_POST['id']) ));
        	$result = serialize($data_old);
        	add_log($table_other_type,clean($_POST['id']),$result,$user_id);

            pg_query("DELETE FROM ".$table_other_type." WHERE id = ".clean($_POST['id']));
            die;
        }
        echo "not exist";
        die;
    }
// типы прочего оборудования end -------------------------------------------------------------------------------------------------------

// Прочее оборудование begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование прочего оборудования в div
    if(isset($_GET['act']) && ($_GET['act']=='n_other' || $_GET['act']=='e_other') ) {
        if($_GET['act']=='e_other')
        {
            $id=clean($_GET['id']);
            $sql="SELECT o1 . * , ot1.name, ot1.unit
                FROM ".$table_other_type." AS ot1, ".$table_other." AS o1
                WHERE ot1.id = o1.other_type_id
                AND o1.id = ".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $name=$result['name'];
            $unit=($result['unit']?$result['unit'].'U':'');
            $descrip=$result['descrip'];
            $text='<input type="hidden" id="id" value="'.$id.'">';
            $text.='<div class="span3 m0 input-control text"><input type="text" value="'.$name.'" placeholder="Название" disabled /></div>';
        } elseif($_GET['act']=='n_other') {
            // тип прочего оборудования
            $sql="SELECT * FROM ".$table_other_type." ORDER BY name";
            $result = pg_query($sql);
            $select_other_type_js="var arr = [];";
            if(pg_num_rows($result)){
                $select_other_type='<select id="other_type_id" onchange="$(\'input#unit\').val(arr[$(\'select#other_type_id\').val()]);">';
                $select_other_type.='<option value="0">---</option>';
                while($row=pg_fetch_assoc($result)){
                    $select_other_type.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
                    $select_other_type_js.="arr[".$row['id']."] = '".($row['unit']?$row['unit'].'U':'')."';";
                }
                $select_other_type.='</select>';
                $select_other_type='<script type="text/javascript">'.$select_other_type_js.'</script>'.$select_other_type;
            }
            $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
            $text.='<div class="span3 m0 input-control text">'.$select_other_type.'</div>';
        }
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" disabled /></div>';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование прочего оборудования в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && ($_POST['act']=='n_other' || $_POST['act']=='e_other') ) {
        if($_POST['act']=='n_other') {
            pg_query("INSERT INTO ".$table_other." (node_id,other_type_id,descrip,user_id) VALUES (".clean($_POST['node_id']).", ".clean($_POST['other_type_id']).", ".($_POST['descrip']?"'".clean($_POST['descrip'])."'":'NULL').", ".$user_id.")");
        } else if($_POST['act']=='e_other') {
        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_other." WHERE id = ".clean($_POST['id']) ));

            pg_query("UPDATE ".$table_other." SET descrip=".($_POST['descrip']?"'".clean($_POST['descrip'])."'":'NULL').", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

            $data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_other." WHERE id = ".clean($_POST['id']) ));
            
            $result = serialize(array_diff($data_old, $data_new));
            add_log($table_other,clean($_POST['id']),$result,$user_id);
            die;
        }
        echo $text;
        die;
    }
    
// удаление прочего оборудования div
    if(isset($_GET['act']) && $_GET['act']=='d_other' && @is_numeric($_GET['id'])) {
        $name=pg_result(pg_query("SELECT name FROM ".$table_other." AS o1, ".$table_other_type." AS ot1 WHERE ot1.id = o1.other_type_id AND o1.id =".clean($_GET['id'])),0);
            $text='
            <div class="span10 m5">&nbsp;Удалить прочее оборудование "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_other" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        echo $text;
        die;
    }
    
// удаление прочего оборудования sql
    if(isset($_POST['act']) && $_POST['act']=='d_other' && @is_numeric($_POST['id']) ) {
    		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_other." WHERE id = ".clean($_POST['id']) ));
    		$result = serialize($data_old);
	    	add_log($table_other,clean($_POST['id']),$result,$user_id);

            pg_query("DELETE FROM ".$table_other." WHERE id = ".clean($_POST['id']));
            die;
    }
// Прочее оборудование end -------------------------------------------------------------------------------------------------------

//показать все кабеля
    if(isset($_POST['act']) && $_POST['act']=='pq_all_cable' && @is_numeric($_POST['node_id']) ) {
/*        if(!@pg_result(pg_query("SELECT * FROM ".$table_node." AS n1, ".$table_pq." AS p1 WHERE n1.id=".clean($_POST['id']))." AND p1.node !=".clean($_POST['id']),0)) {
            pg_query("DELETE FROM ".$table_node." WHERE id = ".clean($_POST['id']));
            die;
        }*/
       // список всех кабелей на данном узле
        /*$sql="SELECT *, p1.num AS pq_num, p1.id AS pq_id
                FROM pq AS p1, cable AS c1, node AS n1, pq_type AS pq_t
                WHERE p1.node = ".clean($_POST['node_id'])."
                AND ( c1.pq_1 = p1.id OR c1.pq_2 = p1.id )
                AND p1.node = n1.id
                AND p1.pq_type_id = pq_t.id";*/
        $sql="SELECT *, p1.num AS pq_num, p1.id AS pq_id
                FROM pq AS p1, ".$table_node." AS n1, ".$table_pq_type." AS pq_t
                WHERE p1.node = ".clean($_POST['node_id'])."
                AND p1.node = n1.id
                AND p1.pq_type_id = pq_t.id";
                echo $sql;
        $result = pg_query($sql);
        if (pg_num_rows($result)) {
            while ($row = pg_fetch_assoc($result)) {
                if($row['type']==0) $type='Кросс'; else $type='Муфта';
                if(isset($row['pq_num'])) $num=' №'.$row['pq_num']; else $num='';

                $text.='<table class="node">';
                $text.='<tr><td colspan=2>'.$row['address'].' ('.$type.$num.')</td></tr>';
                /*echo '<pre>';
                print_r($row);
                echo '</pre>';*/
                $pq_id = $row['pq_id'];
                $sql = "SELECT a.id, IF( a.pq_1 =".$pq_id.", pq_1, pq_2 ) AS pq_1, IF( a.pq_1 =".$pq_id.", pq_2, pq_1 ) AS pq_2, ct.fib, ct.name AS cable_name, IF( a.pq_1 =".$pq_id.", c1.address , c2.address ) AS addr_1, IF( a.pq_1 =".$pq_id.", b1.type , b2.type ) AS type_1, IF( a.pq_1 =".$pq_id.", b1.num , b2.num ) AS num_1, IF( a.pq_1 =".$pq_id.", c2.address , c1.address ) AS addr_2, IF( a.pq_1 =".$pq_id.", b2.type , b1.type ) AS type_2, IF( a.pq_1 =".$pq_id.", b2.num , b1.num ) AS num_2
                            FROM " . $table_cable . " AS a, " . $table_pq . " AS b1, " . $table_pq . " AS b2, " . $table_node . " AS c1, " . $table_node . " AS c2, " . $table_cable_type . " AS ct
                            WHERE (
                                a.pq_1 = b1.id
                                AND b1.node = c1.id
                            )
                            AND (
                                a.pq_2 = b2.id
                                AND b2.node = c2.id
                            ) AND (a.pq_1=".$pq_id." OR a.pq_2=".$pq_id.") AND a.cable_type = ct.id";
                $result_cable = pg_query($sql);
                
                if (pg_num_rows($result_cable)) {
                    while ($row_cable = pg_fetch_assoc($result_cable)) {
                    	$text.='<tr>';                        
                    	$text.='<td><div class="rotateText">'.$row_cable['addr_2'].'</div></td>';
                    	
                    	$sql="SELECT * FROM ".$table_fiber." WHERE cable_id = ".$row_cable['id']." ORDER BY num";
                    	//echo $sql;
                    	$result_fiber = pg_query($sql);
                    	if (pg_num_rows($result_fiber)) {
                    		$text.='<td>';
                    		while ($row_fiber = pg_fetch_assoc($result_fiber)) {
                    			$text.='<div class="fiber">'.$row_fiber['num'].'</div>';
                    		}
                    		$text.='</td>';
                    	}
                        /*echo '<pre>';
                        print_r($row_cable);
                        echo '</pre>';*/
                    	$text.='</tr>';
                    }
                }
                $text.='</table>';
            }
        }
        echo $text;
        die;
    }

// ввод нового типа кросса/муфты в div
    if(isset($_GET['act']) && ($_GET['act']=='n_pq_type' || $_GET['act']=='e_pq_type') ) {
        if($_GET['act']=='e_pq_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM " . $table_pq_type . " AS pq_type WHERE id=".$id;
            $result=pg_fetch_assoc(pg_query($sql));
            $name=$result['name'];
            $type=$result['type'];
            $ports_num=$result['ports_num'];
            $unit=$result['unit'];
            $descrip=$result['descrip'];
        }
		//$type='<select id="type" ><option value="0" '.($type==0 ? "SELECTED" : "").'>Кросс</option><option value="1" '.($type==1 ? "SELECTED" : "").'>Муфта</option></select>';
        $type='<select id="type" ><option value="0" '.($type==0 ? "SELECTED" : "").'>Кросс</option><option value="1" '.($type==1 ? "SELECTED" : "").'>Муфта</option><option value="2" '.($type==2 ? "SELECTED" : "").'>Медный</option></select>';

		$text='<input type="hidden" id="id" value="'.$id.'">';
		$text.='<div class="span2 m0 input-control text">'.$type.'</div>';
		$text.='<div class="span4 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Наименование" /></div>';
		$text.='<div class="span2 m0 input-control text" id="ports_num_div"><input type="text" id="ports_num" value="'.$ports_num.'" placeholder="Портов" /></div>';
		$text.='<div class="span2 m0 input-control text" id="unit_div"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" /></div>';

		$text.='<div class="span2 toolbar m0">
			<button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
			<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		</div>';
		
		$text.=($result['type']==1 ? '<script>$("div#ports_num_div").hide(); $("div#unit_div").hide();</script>' : "");
		echo $text;
		die;
    }

// ввод нового типа кросса/муфты в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && @is_numeric($_POST['type']) && ($_POST['act']=='n_pq_type' || $_POST['act']=='e_pq_type') ) {
    	if(!empty($_POST['ports_num']) && $_POST['type']==0) {
    		$ports_num="=".clean($_POST['ports_num']);
    		$ports_num_sql=clean($_POST['ports_num']);
    	} else {
    		$ports_num="IS NULL";
    		$ports_num_sql="NULL";
    	}
    	if(!empty($_POST['unit']) && $_POST['type']==0) {
    		$unit="=".clean($_POST['unit']);
    		$unit_sql=clean($_POST['unit']);
    	} else {
    		$unit="IS NULL";
    		$unit_sql="NULL";
    	}
    	$sql='SELECT * FROM '.$table_pq_type.' WHERE name="'.clean($_POST['name']).'" AND ports_num '.$ports_num.' AND unit '.$unit;
    
    	if($_POST['act']=='n_pq_type') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такой тип существует!!!";
    		} else {
    			pg_query("INSERT INTO ".$table_pq_type." (name,type,ports_num,unit,user_id) VALUES ('".clean($_POST['name'])."', ".clean($_POST['type']).", ".$ports_num_sql.", ".$unit_sql.",".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_pq_type') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Изменить невозможно, аналогичный тип существует!!!";
    		} else {
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_pq_type." WHERE id = ".clean($_POST['id']) ));

    			pg_query("UPDATE ".$table_pq_type." SET name='".clean($_POST['name'])."', type=".clean($_POST['type']).", ports_num=".$ports_num_sql.", unit=".$unit_sql.", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

    			$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_pq_type." WHERE id = ".clean($_POST['id']) ));
    			
    			$result = serialize(array_diff($data_old, $data_new));
    			add_log($table_pq_type,clean($_POST['id']),$result,$user_id);
    		}
    	}
    	echo $text;
    	die;
    }
    
// удаление типа кросса/муфты в div
    if(isset($_GET['act']) && $_GET['act']=='d_pq_type' && @is_numeric($_GET['id']) ) {
    	//if(isset($_POST['act']) && $_POST['act']=='d_pq_type' && @is_numeric($_POST['id']) ) {
    	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_pq_type." WHERE id = ".clean($_GET['id']) ));
    	$result = serialize($data_old);
    	add_log($table_pq_type,clean($_GET['id']),$result,$user_id);

    	pg_query("DELETE FROM ".$table_pq_type." WHERE id = ".clean($_GET['id']));
    	die;
    }

// ввод нового типа кабеля в div
    if(isset($_GET['act']) && ($_GET['act']=='n_cable_type' || $_GET['act']=='e_cable_type') ) {
    	if($_GET['act']=='e_cable_type')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM ".$table_cable_type." AS pq_type WHERE id=".$id;
    		$result=pg_fetch_assoc(pg_query($sql));
    		$name=$result['name'];
    		$fib=$result['fib'];
    		$descrip=$result['descrip'];
    	}
    	$text='<input type="hidden" id="id" value="'.$id.'">';

        $text.='<div class="span4 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Наименование" /></div>';
        $text.='<div class="span2 m0 input-control text"><input type="text" id="fib" value="'.$fib.'" placeholder="Волокон" /></div>';
        $text.='<div class="span4 m0 input-control text"><input type="text" id="descrip" value="'.$descrip.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
    	echo $text;
    	die;
    }

// ввод нового типа кабеля в div post
    if(isset($_POST['act']) && @is_numeric($_POST['id']) && isset($_POST['name']) && @is_numeric($_POST['fib']) && ($_POST['act']=='n_cable_type' || $_POST['act']=='e_cable_type') ) {
    	if(!empty($_POST['descrip'])) {
    		$descrip="='".clean($_POST['descrip'])."'";
    		$descrip_sql="'".clean($_POST['descrip'])."'";
    	} else {
    		$descrip="IS NULL";
    		$descrip_sql="NULL";
    	}
    	$sql="SELECT * FROM '.$table_cable_type.' WHERE name='".clean($_POST['name'])."' AND fib=".clean($_POST['fib'])." AND descrip ".$descrip;
    	//echo $sql;
    	if($_POST['act']=='n_cable_type') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Создать невозможно, такой тип существует!!!";
    		} else {
    			pg_query("INSERT INTO ".$table_cable_type." (name,fib,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".clean($_POST['fib']).", ".$descrip_sql.",".$user_id.")");
    			//echo "INSERT INTO ".$table_cable_type." (name,fib,descrip,user_id) VALUES ('".clean($_POST['name'])."', ".clean($_POST['fib']).", ".$descrip_sql.",".$user_id.")";
    		}
    	} elseif($_POST['act']=='e_cable_type') {
    		if(@pg_result(pg_query($sql),0)) {
    			$text="Изменить невозможно, аналогичный тип существует!!!";
    		} else {
    			$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cable_type." WHERE id = ".clean($_POST['id']) ));

    			pg_query("UPDATE ".$table_cable_type." SET name='".clean($_POST['name'])."', fib=".clean($_POST['fib']).", descrip=".$descrip_sql.", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

    			$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cable_type." WHERE id = ".clean($_POST['id']) ));

    			//print_r($data_old.' '.$data_new);
    			$result = serialize(array_diff($data_old, $data_new));
    			add_log($table_cable_type,clean($_POST['id']),$result,$user_id);
    			//echo "UPDATE ".$table_cable_type." SET name='".clean($_POST['name'])."', fib=".clean($_POST['fib']).", descrip=".$descrip_sql." WHERE id=".clean($_POST['id']).";";
    			//die;
    		}
    	}
    	echo $text;
    	die;
    }

// удаление типа кабеля в div
    if(isset($_GET['act']) && $_GET['act']=='d_cable_type' && @is_numeric($_GET['id']) ) {
    	$sql="SELECT * FROM ".$table_cable." WHERE cable_type = ".clean($_GET['id']);
    	if(@pg_result(pg_query($sql),0)) {
    		echo 'error';
    	} else {
    		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_fiber_type." WHERE cable_id = ".clean($_GET['id']) ));
    		$result = serialize($data_old);
    		pg_query("DELETE FROM ".$table_fiber_type." WHERE cable_id = ".clean($_GET['id']));
    		add_log($table_fiber_type,clean($_GET['id']),$result,$user_id);
    		
	    	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cable_type." WHERE id = ".clean($_GET['id']) ));
	    	$result = serialize($data_old);
	    	pg_query("DELETE FROM ".$table_cable_type." WHERE id = ".clean($_GET['id']));
	    	add_log($table_cable_type,clean($_GET['id']),$result,$user_id);

	    	echo 'reload';
    	}
    	die;
    }

// ввод нового пассивного оборудования Кроссы/Муфты div
    if(isset($_GET['act']) && ($_GET['act']=='n_pq' || $_GET['act']=='e_pq') ) {
// выбор узла для размещения кросса
    	if($_GET['act']=='e_pq')
    	{
    		$pq_id=clean($_GET['pq_id']);
    		$sql_1 = "SELECT pq.id AS pq_id, pq.node, pq.num, pq.descrip AS pq_descrip, pq_t.* FROM ".$table_pq." AS pq LEFT JOIN ".$table_pq_type." AS pq_t ON pq.pq_type_id = pq_t.id WHERE pq.id='".$pq_id."' LIMIT 1;";
    		$result=pg_fetch_assoc(pg_query($sql_1));
    		//print_r('<pre>'.$sql_1.'</pre>');
    		/*echo '1<pre>';
    		print_r($result);
    		echo '</pre>';*/
    		$node=$result['node'];
    		$type=$result['type'];
    		$type_id=$result['id'];
    		$num=$result['num'];
    		$pq_descrip=$result['pq_descrip'];
    		//$sql="SELECT n1.*, p1.type, p1.num, p1.descrip AS pq_descrip, pq_t.id AS pq_type_id, pq_t.name AS pq_type_name, pq_t.ports_num AS pq_ports FROM node AS n1 JOIN pq AS p1 ON p1.node = n1.id AND p1.id = ".clean($_GET['pq_id'])." LEFT JOIN pq_type AS pq_t ON p1.pq_type_id = pq_t.id WHERE p1.type != 1 OR p1.type IS NULL OR n1.id =".$node." ORDER BY n1.address";
    		$sql="SELECT n1.*, pq_t.type AS pq_type, p1.num, p1.descrip AS pq_descrip, pq_t.id AS pq_type_id, pq_t.name AS pq_type_name, pq_t.ports_num AS pq_ports
    				FROM ".$table_node." AS n1
    			JOIN ".$table_pq." AS p1 ON p1.node = n1.id AND p1.id = ".clean($_GET['pq_id'])."
				LEFT JOIN ".$table_pq_type." AS pq_t ON p1.pq_type_id = pq_t.id
				WHERE pq_t.type != 1 OR pq_t.type = 2 OR pq_t.type IS NULL OR n1.id =".$node."
				ORDER BY n1.address";
    	} else {
    		$node=clean($_GET['node_id']);
    		//$sql="SELECT n1.*, p1.type, p1.num, p1.descrip AS pq_descrip, pq_t.id AS pq_type_id, pq_t.name AS pq_type_name, pq_t.ports_num AS pq_ports FROM node AS n1 LEFT JOIN pq AS p1 ON p1.node = n1.id LEFT JOIN pq_type AS pq_t ON p1.pq_type_id = pq_t.id WHERE p1.type != 1 OR p1.type IS NULL ORDER BY n1.address";
    	}
    	//print_r('<pre>'.$sql.'</pre>');
    	$result=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node." AS n1 WHERE n1.id=".$node.";"));
    	//print_r($result);

// выбор узла для размещения кросса end
    	$text='<input type="hidden" id="act" value="'.clean($_GET['act']).'" />';
		@$text.='<input type="hidden" id="id" value="'.$pq_id.'" />';
		$text.='<input type="hidden" id="node" value="'.$node.'" />';
		@$text.=($_GET['prompt']?'<input type="hidden" id="prompt" value="'.$_GET['prompt'].'" />':'');
		//$text.='<input type="hidden" id="type_id" value="'.$type_id.'" />';
    	//$text.='<div class="span3 m0 input-control text">'.$select_node.'</div>';
		$text.='<div class="span3 m5 input-control text">'.$result['address'].'</div>';
		$text.='<div class="span2 m0 input-control text"><select id="type"></select></div>';
		$text.='<div class="span2 m0 input-control text"><select id="pq_type"></select></div>';
		@$text.='<div class="span1 m0 input-control text"><input class="" type="text" id="num" value="'.$num.'" placeholder="№" /></div>';
		@$text.='<div class="span3 m0 input-control text"><input type="text" id="pq_descrip" value="'.$pq_descrip.'" placeholder="Описание" /></div>';
		$text.='<div class="span2 toolbar m0">
					<button class="icon-checkmark m0" id="new_pq" title="Ok"></button>
					<button class="icon-blocked m0" id="exit" title="Отмена"></button>
				</div>';
		@$text.='<script type="text/javascript">s_pq_type_ports("'.$node.'","'.$type.'","'.$type_id.'");</script>';
		//$text.='s_pq_type_ports("'.$node.'","'.$type.'","'.$type_id.'");';
		@$text.='<script type="text/javascript">s_pq_type_sel("'.$type.'","'.$type_id.'");</script>';
    	echo $text;
    	die;
    }

// вывод селекта типов пассивного оборудования после выбора
	if(isset($_POST['act']) && $_POST['act']=='s_pq_type_sel' && @is_numeric($_POST['type']) ) {
    	$sql="SELECT * FROM ".$table_pq_type." WHERE type=".clean($_POST['type']." ORDER BY name");
		$result=pg_query($sql);
    	if(pg_num_rows($result)){
    		$select_pq_type='<select id="pq_type">';
    		$select_pq_type.='<option value="0">---</option>';
    		while($row=pg_fetch_assoc($result)){
    			$select_pq_type.='<option value="'.$row['id'].'"';
    			if(@$_POST['type_id']==$row['id']) {
    				$select_pq_type.=" SELECTED";
    			}
    			$select_pq_type.='>'.$row['name'].($row['type']==0 ? ' ('.$row['ports_num'].')' : '').'</option>';
    		}
    		$select_pq_type.='</select>';
    	}
    	echo $select_pq_type;
		die;
	}

// вывод списка волокон div
    if(isset($_GET['act']) && $_GET['act']=='s_fiber') {
    	$i=1;
    	if(isset($_GET['cable_id']) && @is_numeric($_GET['cable_id'])) {
    		$cable_id='AND aa.cable_id='.clean($_GET['cable_id']);
    		// сравнение количества волокон с количеством в кабеле
    		$result=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cable." WHERE id='".clean($_GET['cable_id'])."' LIMIT 1;"));
    		$fib_busy=pg_result(pg_query("SELECT COUNT(*) FROM ".$table_fiber." WHERE cable_id='".clean($_GET['cable_id'])."';"),0);
    		// если количество волокон меньше чем в количество в кабеле, то выводим ссылку на добавление волокон
    		//$text='<div><a href="?act=n_fiber&cable_id='.clean($_GET['cable_id']).'">Добавить волокно</a></div>';
    		/*			if($result['type']==0) $type='Кросс'; else $type='Муфта';
    			if(isset($result['num'])) $num=' №'.$result['num']; else $num='';*/
    
    		$link='<div class="title">'.$link_node.' > ';
    		$link.='<a href="?act=s_cable&pq_id='.$result['pq_1'];
    			
    		$link2='</div>';
    		$link2.='<div id="action">';
    		if($_SESSION['fiber_add'])
    			if($result['fib']>$fib_busy)
    			$link2.='<input class="mini" id="fiber_add_div" rel="?act=n_fiber&cable_id='.clean($_GET['cable_id']).'" type="button" value="Добавить волокно" />';
    		$link2.='</div>';
    		$text.='<br>';
    	} else {
    		$text.='<div id="action">';
    		if($_SESSION['fiber_add'])
    			$text.='<input class="mini" id="fiber_add_div" rel="?act=n_fiber&cable_id='.clean($_GET['cable_id']).'" type="button" value="Добавить волокно" />';
    		$text.='</div>';
    		$text.='<br>';
    	}
    
    	$result=pg_query("SELECT aa.*, a.pq_1, a.pq_2, a.fib, c1.address as addr_1, b1.type as type_1, b1.num as num_1, c2.address as addr_2, b2.type as type_2, b2.num as num_2
    			FROM ".$table_fiber." AS aa, ".$table_cable." AS a, ".$table_pq." AS b1, ".$table_pq." AS b2, ".$table_node." AS c1, ".$table_node." AS c2
    			WHERE (
    			a.pq_1 = b1.id
    			AND b1.node = c1.id
    	)
    			AND (
    			a.pq_2 = b2.id
    			AND b2.node = c2.id
    	) ".$cable_id." AND aa.cable_id = a.id ORDER BY addr_1, type_1, addr_2, type_2, aa.num");
    	if(pg_num_rows($result)){
    		while($row=pg_fetch_assoc($result)){
    			if($row['type_1']==0) $type_1='Кросс'; else $type_1='Муфта';
    			if(isset($row['num_1'])) $num_1=' №'.$row['num_1']; else $num_1='';
    
    			if($row['type_2']==0) $type_2='Кросс'; else $type_2='Муфта';
    			if(isset($row['num_2'])) $num_2=' №'.$row['num_2']; else $num_2='';
    
    			$pq_addr_1=$row['addr_1'].' (' .$type_1.$num_1. ')';
    			$pq_addr_2=$row['addr_2'].' (' .$type_2.$num_2. ')';
    
    			$text.='
    			<div style="show_fiber">
    			<div class="left_fiber">
    			'.$i.'. '.$pq_addr_1.' - '.$pq_addr_2.' [ОВ №'.$row['num'].']
    			</div>';
    			if($_SESSION['fiber_del']) $text.='<div><input type="button" id="d_fiber_'.$row['id'].'" value="Удалить" /></div>';
    			$text.='</div>';
    			$text.='<div class="clear"></div>';
    			$i++;
    		}
    	}
    	//show_menu();
    	//echo $link.$text;
    	//if($link) echo $link.$pq_addr_1.' - '.$pq_addr_2.$link2;
    	//if($link) echo $link.'">'.$pq_addr_1.'</a> > Кабель: '.$pq_addr_2.$link2;
    	echo $link2;
    	$text.='<br>';
    	$text.='<div class="left_fiber">&nbsp;</div><div><input id="exit" type="button" value="Отмена" /></div>';
    	echo $text;
    	die;
    }


// удаление пассивного оборудования Узла/Муфты div
    if(isset($_GET['act']) && $_GET['act']=='d_pq' && @is_numeric($_GET['pq_id'])) {
        $sql="SELECT COUNT(*) FROM ".$table_cable." AS c1 WHERE c1.pq_1 =".clean($_GET['pq_id'])." OR c1.pq_2 =".clean($_GET['pq_id']);
        if(pg_result(pg_query($sql),0)) {
        	//$addr=pg_result(pg_query("SELECT n1.address_full FROM ".$table_node." AS n1, ".$table_pq." AS p1 WHERE p1.node = n1.id AND p1.id='".clean($_POST['pq_id'])."';"),0);
            $text='
            <div class="span11 m5">&nbsp;Пассивное оборудование "'.clean($_GET['addr']).'" не пустое. Перед удалением пассивного оборудования необходимо удалить кабеля!!!</div>
            <div class="span1 toolbar m0">
            	<button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
	        $text='
	        <div class="span10 m5">&nbsp;Удалить пассивное оборудование "'.clean($_GET['addr']).'"?</div>
	        <div class="span2 toolbar m0">
		        <button class="icon-checkmark m0" id="d_pq" rel="'.clean($_GET['pq_id']).'" title="Ok"></button>
		        <button class="icon-blocked m0" id="exit" title="Отмена"></button>
	        </div>';
        }
        echo $text;
        die;
    }

// удаление пассивного оборудования Узла/Муфты
    if(isset($_POST['act']) && $_POST['act']=='d_pq' && @is_numeric($_POST['id']) ) {
        // запрос кривоватый.... но работает
        if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_cable." AS c1 LEFT JOIN ".$table_pq." AS p1 ON c1.pq_1 = p1.id OR c1.pq_2 = p1.id WHERE c1.pq_1 = ".clean($_POST['id'])." OR c1.pq_2 = ".clean($_POST['id'])),0)) {
        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_pq." WHERE id = ".clean($_POST['id']) ));
        	$result = serialize($data_old);
        	add_log($table_pq,clean($_POST['id']),$result,$user_id);

            pg_query("DELETE FROM ".$table_pq." WHERE id = ".clean($_POST['id']));
            die;
        }
        echo "not exist";
        die;
    }

// ввод нового узла
/*	if(isset($_POST['act']) && $_POST['act']=='n_node_sql' && isset($_POST['address'])) {
		if(! @pg_result(pg_query("SELECT id FROM ".$table_node." WHERE address='".clean($_POST['address'])."';"),0)) {
			if(empty($_POST['descrip']))
				$descrip="NULL";
			else
				$descrip="'".clean($_POST['descrip'])."'";
			pg_query("INSERT INTO ".$table_node." (address,incorrect,descrip,user_id) VALUES ('".clean($_POST['address'])."', ".(isset($_POST['address_incorrect'])?'1':'NULL').", ".$descrip.",".$user_id.")");
			die;
		}
		echo "exist";
		die;
	}
*/
// редактирование существующего узла
/*	if(isset($_POST['act']) && $_POST['act']=='e_node_sql' && @is_numeric($_POST['id']) && isset($_POST['address'])) {
		if($_POST['descrip']=='') $descrip='NULL'; else $descrip="'".clean($_POST['descrip'])."'";
		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node." WHERE id = ".clean($_POST['id']) ));
		
		pg_query("UPDATE ".$table_node." SET address='".clean($_POST['address'])."', incorrect=".(isset($_POST['address_incorrect'])?'1':'NULL').",descrip=".$descrip.", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

		$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node." WHERE id = ".clean($_POST['id']) ));

		$result = serialize(array_diff($data_old, $data_new));
		//add_log($table_node,clean($_POST['id']),$result,$user_id);
		die;
	}
*/
// после выбора узла выводит список добавления возможного пассивного оборудования 
	if(isset($_POST['act']) && $_POST['act']=='s_pq_type' && @is_numeric($_POST['node_id']) && isset($_POST['type'])) {
		$select_type='<select id="type">';
		//if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_pq." AS p1, ".$table_pq_type." AS pt WHERE p1.pq_type_id = pt.id AND pt.type != 0 AND p1.node =".clean($_POST['node_id'])),0)) {
		if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_pq." AS p1, ".$table_pq_type." AS pt WHERE p1.pq_type_id = pt.id AND pt.type != 0 AND pt.type != 2 AND p1.node =".clean($_POST['node_id'])),0)) {
			$select_type.='<option value="0"';
			if(clean($_POST['type'])==0) $select_type.=" SELECTED";
			$select_type.='>кросс</option>';
		}
		/*$select_type='<select id="type"><option value="0"';
		if(clean($_POST['node_id'])==0) $select_type.=" SELECTED";
		$select_type.='>кросс</option>';*/
		//if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_pq." WHERE type != 1 AND node =".clean($_POST['node_id'])),0)) {
		if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_pq." AS p1, ".$table_pq_type." AS pt WHERE p1.pq_type_id = pt.id AND pt.type != 1 AND p1.node =".clean($_POST['node_id'])),0)) {
			$select_type.='<option value="1"';
			if(clean($_POST['type'])==1) $select_type.=" SELECTED";
			$select_type.='>муфта</option>';
		}
		//if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_pq." AS p1, ".$table_pq_type." AS pt WHERE p1.pq_type_id = pt.id AND pt.type != 2 AND p1.node =".clean($_POST['node_id'])),0)) {
		if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_pq." AS p1, ".$table_pq_type." AS pt WHERE p1.pq_type_id = pt.id AND pt.type != 0 AND pt.type != 2 AND p1.node =".clean($_POST['node_id'])),0)) {
			$select_type.='<option value="2"';
			if(clean($_POST['type'])==2) $select_type.=" SELECTED";
			$select_type.='>медный</option>';
		}
		$select_type.='</select>';
		echo "var select_type='".$select_type."'; show=true;";
		die;
	}

// вывод свободных порта в кроссе
	if(isset($_POST['act']) && $_POST['act']=='s_port_free' && @is_numeric($_POST['pq_id']) && @is_numeric($_POST['pq_type_id'])) {
		$select_port='<select id="ports">';
		// если id не равен нулю, заполняем массив
		if($_POST['pq_type_id']!=0) {
			// общее количество волокон в кабеле
			//echo "SELECT ports FROM ".$table_pq." WHERE id=".clean($_POST['pq_id']).";";
			//$num=pg_result(pg_query("SELECT ports FROM ".$table_pq." WHERE id=".clean($_POST['pq_id']).";"),0);
			$num=pg_result(pg_query("SELECT ports_num FROM ".$table_pq_type." WHERE id=".clean($_POST['pq_type_id']).";"),0);
			$i=0;
			$port[]='';
			$result=pg_query("SELECT * FROM ".$table_cruz_conn." WHERE pq_id=".clean($_POST['pq_id'])." ORDER BY port;");
			if(pg_num_rows($result)){
				while($row=pg_fetch_assoc($result)){
					$port[$i]=$row['port'];
					$i++;
				}
			}
			$i=1;
			while($i<=$num) {
				// если в базе нету такого порта, то заносим его в выбор портов
				if(!is_numeric(array_search($i, $port))) {
					$select_port.='<option value="'.$i.'"';

					$select_port.='>'.$i.'</options>';
				}
				$i++;
			}
		}
		$select_port.='</select>';
		echo $select_port;
		die;
	}

// изменение номера порта в кроссе присоедененного к волокну
	if(isset($_POST['act']) && $_POST['act']=='fiber_port_conn' && @is_numeric($_POST['pq_id']) && @is_numeric($_POST['port_id']) && @is_numeric($_POST['fiber_id']) && @is_numeric($_POST['curr_port_id'])) {
		//echo "pq_id: ".clean($_POST['pq_id'])." port_id: ".clean($_POST['pq_id'])." fiber_id: ".clean($_POST['fiber_id']);
		//die;
		if($_POST['port_id']==0) {
			pg_query("UPDATE ".$table_cruz_conn." SET fiber_id=NULL, used=NULL, user_id=".$user_id." WHERE pq_id=".clean($_POST['pq_id'])." AND id=".clean($_POST['curr_port_id']).";");
			//pg_query("UPDATE ".$table_cruz_conn." SET used=".($_POST['port_used']==0?'NULL':($_POST['port_used']==1?'true':'')).", user_id=".$user_id." WHERE id=".clean($_POST['port_id']).";");
			//echo "UPDATE ".$table_cruz_conn." SET fiber_id=NULL WHERE pq_id=".clean($_POST['pq_id'])." AND id=".clean($_POST['curr_port_id']).";";
		} else {
			if($_POST['curr_port_id']!=0)
				pg_query("UPDATE ".$table_cruz_conn." SET fiber_id=NULL, user_id=".$user_id." WHERE pq_id=".clean($_POST['pq_id'])." AND id=".clean($_POST['curr_port_id']).";");
			//if(clean($_POST['curr_port_id'])!=0) echo "UPDATE ".$table_cruz_conn." SET fiber_id=NULL WHERE pq_id=".clean($_POST['pq_id'])." AND port=".clean($_POST['curr_port_id']).";";
			pg_query("UPDATE ".$table_cruz_conn." SET fiber_id=".clean($_POST['fiber_id']).", user_id=".$user_id." WHERE pq_id=".clean($_POST['pq_id'])." AND id=".clean($_POST['port_id']).";");
			//echo "UPDATE ".$table_cruz_conn." SET fiber_id=".clean($_POST['fiber_id'])." WHERE pq_id=".clean($_POST['pq_id'])." AND id=".clean($_POST['port_id']).";";
		}
//		if(!@pg_result(pg_query("SELECT * FROM ".$table_cruz_conn." WHERE pq_id = ".clean($_POST['pq_id'])." AND port = ".clean($_POST['port_id'])." AND fiber_id = ".clean($_POST['fiber_id'])),0)) {
//			echo "нету";
		//pg_query("UPDATE ".$table_cruz_conn." SET fiber_id=".clean($_POST['fiber_id'])." WHERE pq_id=".clean($_POST['pq_id'])." AND port_id=".clean($_POST['port_id']).";");
		//echo "UPDATE ".$table_cruz_conn." SET fiber_id=".$fiber_id." WHERE pq_id=".clean($_POST['pq_id'])." AND port=".clean($_POST['curr_port_id']).";";
		//pg_query("UPDATE ".$table_cruz_conn." SET fiber_id=".$fiber_id." WHERE pq_id=".clean($_POST['pq_id'])." AND port=".clean($_POST['curr_port_id']).";");
			//pg_query("INSERT INTO ".$table_cruz_conn." (node,type,num,ports) VALUES ('".clean($_POST['node'])."', ".clean($_POST['type']).", ".$numm.", ".$portss.")");
//		}		
		die;

		$sql="SELECT * FROM ".$table_cruz_conn." WHERE pq_id = ".clean($_POST['pq_id'])." AND ( fiber_id IS NULL OR fiber_id = ".clean($_POST['fiber_id'])." );";
		
		$result=pg_query($sql);
		$text.='<option value="0">---</option>';
		if(pg_num_rows($result)){
			while($row=pg_fetch_assoc($result)){
				$text.='<option value="'.$row['id'].'"';
				if(clean($_POST['fiber_id'])==$row['fiber_id']) $text.=" SELECTED";
				$text.='>'.$row['port'].'</option>';
			}
		}
		$text.='</select>';
		echo $text;
		die;
	}

// ввод нового пассивного оборудования
	if(isset($_POST['act']) && $_POST['act']=='n_pq_sql' && isset($_POST['node'])) {
		if(empty($_POST['type'])) $_POST['type']=0;
		if(empty($_POST['num'])) {
			$num="num IS NULL";	// для SELECT
			$numm='NULL';			// для INSERT
		} else {
			$num='num='.clean($_POST['num']);
			$numm=clean($_POST['num']);
		}
		if(empty($_POST['pq_descrip'])) {
			$pq_descrip="NULL";
		} else {
			$pq_descrip="'".$_POST['pq_descrip']."'";
		}
		//print_r($_POST);
		//if(! @pg_result(pg_query("SELECT id FROM ".$table_pq." WHERE node='".clean($_POST['node'])."' AND type=".$_POST['type']." AND ".$num." AND pq_type_id=".clean($_POST['pq_type']).";"),0)) {
		if(! @pg_result(pg_query("SELECT id FROM ".$table_pq." WHERE node='".clean($_POST['node'])."' AND ".$num." AND pq_type_id=".clean($_POST['pq_type']).";"),0)) {
			//pg_query("INSERT INTO ".$table_pq." (node,type,num,pq_type_id,descrip) VALUES ('".clean($_POST['node'])."', ".clean($_POST['type']).", ".$numm.", ".clean($_POST['pq_type']).", ".$pq_descrip.")");
			// вставить новое пассивное оборудование
			pg_query("INSERT INTO ".$table_pq." (node,num,pq_type_id,descrip,user_id) VALUES ('".clean($_POST['node'])."', ".$numm.", ".clean($_POST['pq_type']).", ".$pq_descrip.",".$user_id.")");
			// изменить тип узла
			$node_type = clean($_POST['type']);
			pg_query("UPDATE ".$table_node." SET type = ".$node_type." WHERE id=".clean($_POST['node']));
			//print_r($_POST);
			//echo "UPDATE ".$table_node." SET type = ".$node_type." WHERE id=".clean($_POST['node']);
			
			$sql="SELECT p1.id AS id, pq_t.ports_num AS pq_type_ports
					FROM ".$table_pq." as p1, ".$table_pq_type." AS pq_t
					WHERE p1.pq_type_id = pq_t.id
					AND p1.node = ".clean($_POST['node'])."
					AND p1.".$num."
					AND p1.pq_type_id = ".clean($_POST['pq_type']).";";
			//echo $sql;
			$result = pg_fetch_assoc(pg_query($sql));
			$pq_id=$total_num=$result['id'];
			$total_num=$result['pq_type_ports'];
			while($total_num) {
				if(! @pg_result(pg_query("SELECT id FROM ".$table_cruz_conn." WHERE pq_id=".$pq_id." AND port=".$total_num.";"),0))
					pg_query("INSERT INTO ".$table_cruz_conn." (pq_id,port,user_id) VALUES (".$pq_id.", ".$total_num.",".$user_id.")");
					echo "INSERT INTO ".$table_cruz_conn." (pq_id,port) VALUES (".$pq_id.", ".$total_num.")\n";
				$total_num--;
			}
			die;
		}
		echo "exist";
		die;
	}

	if(isset($_GET['act']) && $_GET['act']=='n_port' && isset($_GET['all']) && @is_numeric($_GET['pq_id']) ) {
		$i=1;
		$sql="SELECT *,pq.id AS id, pq_t.id AS pq_type_id FROM ".$table_node." AS node, ".$table_pq." AS pq LEFT JOIN ".$table_pq_type." AS pq_t ON pq.pq_type_id = pq_t.id WHERE pq.node=node.id AND pq.id = ".clean($_GET['pq_id'])." ORDER BY pq.node";
		$result=pg_fetch_assoc(pg_query($sql));
		$total_num=$result['ports_num'];
		while($total_num) {
			if(! @pg_result(pg_query("SELECT id FROM ".$table_cruz_conn." WHERE pq_id=".clean($_GET['pq_id'])." AND port=".$total_num.";"),0)) {
				pg_query("INSERT INTO ".$table_cruz_conn." (pq_id,port,user_id) VALUES (".clean($_GET['pq_id']).", ".$total_num.",".$user_id.")");
			}
			$total_num--;
		}
		echo 'reload';
		die;
	}

// редактирование описания порта
	if(isset($_POST['act']) && $_POST['act']=='port_descrip_edit' && @is_numeric($_POST['port_id'])) {
		if($_POST['port_descrip']=='') $descrip='NULL'; else $descrip="'".clean($_POST['port_descrip'])."'";
		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cruz_conn." WHERE id = ".clean($_POST['port_id']) ));

		pg_query("UPDATE ".$table_cruz_conn." SET descrip=".$descrip.", user_id=".$user_id." WHERE id=".clean($_POST['port_id']).";");

		$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cruz_conn." WHERE id = ".clean($_POST['port_id']) ));

		$result = serialize(array_diff($data_old, $data_new));
		add_log($table_cruz_conn,clean($_POST['port_id']),$result,$user_id);
		die;
	}

// чекбокс занятости порта
	if(isset($_POST['act']) && $_POST['act']=='port_used_edit' && @is_numeric($_POST['port_id'])) {
		$sql="UPDATE ".$table_cruz_conn." SET used=".($_POST['port_used']==0?'NULL':($_POST['port_used']==1?'true':'')).", user_id=".$user_id." WHERE id=".clean($_POST['port_id']).";";

		$data_old=pg_fetch_assoc(pg_query("SELECT used FROM ".$table_cruz_conn." WHERE id = ".clean($_POST['port_id']) ));

		pg_query($sql);

		$data_new=pg_fetch_assoc(pg_query("SELECT used FROM ".$table_cruz_conn." WHERE id = ".clean($_POST['port_id']) ));

		$result = serialize(array_diff($data_old, $data_new));
		if(empty($data_old)) $result=serialize(array('used')); // для нормального лога :о)
		add_log($table_cruz_conn,clean($_POST['port_id']),$result,$user_id);

		echo (pg_result(pg_query("SELECT used FROM ".$table_cruz_conn." WHERE id=".clean($_POST['port_id'])),0)==true?'1':'0');
		//echo $sql;
		die;
	}

// чекбокс проблеммы на узле
	if(isset($_POST['act']) && $_POST['act']=='incorrect_edit' && @is_numeric($_POST['node_id'])) {
		$sql="UPDATE ".$table_node." SET incorrect=".($_POST['incorrect']==false?'NULL':($_POST['incorrect']==true?'true':'')).", user_id=".$user_id." WHERE id=".clean($_POST['node_id']).";";

		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node." WHERE id = ".clean($_POST['node_id']) ));

		pg_query($sql);

		$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node." WHERE id = ".clean($_POST['node_id']) ));

		$result = serialize(array_diff($data_old, $data_new));
		add_log($table_node,clean($_POST['node_id']),$result,$user_id);

		echo (pg_result(pg_query("SELECT incorrect FROM ".$table_node." WHERE id=".clean($_POST['node_id'])),0)==true?'1':'0');
		//echo $sql;
		die;
	}

// чекбокс в стадии строительства узла
	if(isset($_POST['act']) && $_POST['act']=='u_const_edit' && @is_numeric($_POST['node_id'])) {
		//print_r($_POST);
		$sql="UPDATE ".$table_node." SET u_const=".($_POST['u_const']==false?'NULL':($_POST['u_const']==true?'true':'')).", user_id=".$user_id." WHERE id=".clean($_POST['node_id']).";";
	
//		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node." WHERE id = ".clean($_POST['node_id']) ));
	
		pg_query($sql);
	
/*		$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_node." WHERE id = ".clean($_POST['node_id']) ));
	
		$result = serialize(array_diff($data_old, $data_new));
		add_log($table_node,clean($_POST['node_id']),$result,$user_id);
*/	
		echo (pg_result(pg_query("SELECT u_const FROM ".$table_node." WHERE id=".clean($_POST['node_id'])),0)==true?'1':'0');
		//echo $sql;
		die;
	}

// редактирование существующего пассивного оборудования
	//if(isset($_POST['act']) && $_POST['act']=='e_pq_sql' && isset($_POST['id']) && isset($_POST['node']) && isset($_POST['type']) && isset($_POST['num']) && isset($_POST['pq_type'])) {
	if(isset($_POST['act']) && $_POST['act']=='e_pq_sql' && isset($_POST['id']) && isset($_POST['node']) && isset($_POST['pq_type'])) {
	//if(isset($_POST['act']) && $_POST['act']=='e_pq_sql' && isset($_POST['id']) && isset($_POST['node']) ) {
		//if(empty($_POST['type'])) $_POST['type']=0;
		if(empty($_POST['num'])) $_POST['num']='NULL';
		if(empty($_POST['pq_descrip'])) $pq_descrip='NULL'; else $pq_descrip="'".clean($_POST['pq_descrip'])."'";
		$sql="UPDATE ".$table_pq." SET node=".clean($_POST['node']).", num = ".clean($_POST['num']).", pq_type_id=".clean($_POST['pq_type']).", descrip = ".$pq_descrip.", user_id=".$user_id." WHERE id=".clean($_POST['id']).";";
		//echo $sql;

		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_pq." WHERE id = ".clean($_POST['id']) ));

		pg_query($sql);

		$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_pq." WHERE id = ".clean($_POST['id']) ));
		
		$result = serialize(array_diff($data_old, $data_new));
		add_log($table_pq,clean($_POST['id']),$result,$user_id);

		$node_type = pg_result(pg_query("SELECT pt1.type FROM fibers.pq AS p1, fibers.pq_type AS pt1 WHERE p1.node =".clean($_POST['node'])." AND p1.pq_type_id = pt1.id LIMIT 1"),0);
		pg_query("UPDATE ".$table_node." SET type = ".$node_type." WHERE id=".clean($_POST['node']));
		//echo "UPDATE ".$table_pq." SET node=".clean($_POST['node']).", type=".clean($_POST['type']).", num=".clean($_POST['num'])." WHERE id=".clean($_POST['id']).";";
		die;
	}

// ввод нового кабеля
	if(isset($_POST['act']) && $_POST['act']=='n_cable_sql' && isset($_POST['pq_1']) && isset($_POST['pq_2']) && isset($_POST['cable_type'])) {
		//echo "SELECT id FROM ".$table_pq." WHERE node='".clean($_POST['node'])."' AND type=".$_POST['type']." AND ".$num.";";
		if(empty($_POST['descrip'])) {
			$descrip='NULL';
		} else { 
			$descrip="'".clean($_POST['descrip'])."'";
		}
		//echo $descrip;
		if(! @pg_result(pg_query("SELECT id FROM ".$table_cable." WHERE pq_1='".clean($_POST['pq_1'])."' AND pq_2=".$_POST['pq_2']." AND cable_type=".$_POST['cable_type'].";"),0)) {
			$result_cable=pg_query("INSERT INTO ".$table_cable." (pq_1,pq_2,cable_type,descrip,user_id) VALUES ('".clean($_POST['pq_1'])."', ".clean($_POST['pq_2']).", ".clean($_POST['cable_type']).",".$descrip.",".$user_id.") RETURNING id");
			//$row_cable = pg_fetch_assoc($result_cable);
			$result = pg_fetch_assoc(pg_query("SELECT c1.id AS id, ct.fib AS fib FROM ".$table_cable." AS c1, ".$table_cable_type." AS ct WHERE c1.pq_1='".clean($_POST['pq_1'])."' AND c1.pq_2=".$_POST['pq_2']." AND c1.cable_type=".$_POST['cable_type']." AND ct.id = c1.cable_type;"));
			$cable_id=$result['id'];
			//$cable_id=$row_cable['id'];
			cable_geom($cable_id,true);
			//print_r($row_cable['id'].' '.$cable_id);
			$total_num=$result['fib'];
			while($total_num) {
				if(! @pg_result(pg_query("SELECT id FROM ".$table_fiber." WHERE cable_id=".$cable_id." AND num=".$total_num.";"),0))
					pg_query("INSERT INTO ".$table_fiber." (cable_id,num,user_id) VALUES (".$cable_id.", ".$total_num.",".$user_id.")");
				$total_num--;
			}
			die;
		}
		echo "exist";
		die;
	}

// редактирование существующего кабеля
	if(isset($_POST['act']) && $_POST['act']=='e_cable_sql' && isset($_POST['pq_1']) && isset($_POST['pq_2']) && isset($_POST['cable_type'])) {
		//echo "UPDATE ".$table_cable." SET pq_1=".clean($_POST['pq_1']).", pq_2=".clean($_POST['pq_2']).", fib=".clean($_POST['fib'])." WHERE id=".clean($_POST['id']).";";
		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cable." WHERE id = ".clean($_POST['id']) ));

		pg_query("UPDATE ".$table_cable." SET pq_1=".clean($_POST['pq_1']).", pq_2=".clean($_POST['pq_2']).", cable_type=".clean($_POST['cable_type']).", user_id=".$user_id." WHERE id=".clean($_POST['id']).";");

		$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cable." WHERE id = ".clean($_POST['id']) ));

		$result = serialize(array_diff($data_old, $data_new));
		add_log($table_cable,clean($_POST['id']),$result,$user_id);

		cable_geom(clean($_POST['id']),false);
		//echo "UPDATE ".$table_cable." SET pq_1=".clean($_POST['pq_1']).", pq_2=".clean($_POST['pq_2']).", fib=".clean($_POST['fib'])." WHERE id=".clean($_POST['id']).";";
		die;
	}

// вывод свободных волокон в кабеле
	if(isset($_POST['act']) && $_POST['act']=='s_fiber_free' && @is_numeric($_POST['id'])) {
		$select_fiber='<select id="fiber">';
// если id не равен нулю, заполняем массив
		if($_POST['id']!=0) {
// общее количество волокон в кабеле
			$num=pg_result(pg_query("SELECT ct.fib FROM ".$table_cable." AS c1, ".$table_cable_type." AS ct WHERE c1.id=".clean($_POST['id'])." AND ct.id = c1.cable_type;"),0);
			$i=0;
			$fiber[]='';
			$result=pg_query("SELECT * FROM ".$table_fiber." WHERE cable_id=".clean($_POST['id']).";");
			if(pg_num_rows($result)){
				while($row=pg_fetch_assoc($result)){
					$fiber[$i]=$row['num'];
					$i++;
				}
			}
			$i=1;
/*			$a=0;
			$select_fiber.='<option value="0">---</option>';*/
			while($i<=$num) {
// если в базе нету такого волокна, то заносим его в выбор волокон
				if(!is_numeric(array_search($i, $fiber))) {
					$select_fiber.='<option value="'.$i.'"';
/*					if($a==0) {
						$select_fiber.=" SELECTED";
						$a++;
					}*/
					$select_fiber.='>'.$i.'</options>';
				}
				$i++;
			}	
		}
		$select_fiber.='</select>';
		echo $select_fiber;
		die;
	}

// ввод нового волокна div
    //if(isset($_GET['act']) && ($_GET['act']=='n_fiber' || $_GET['act']=='e_fiber') ) {
	if(isset($_GET['act']) && ($_GET['act']=='n_fiber' || $_GET['act']=='e_fiber') && @is_numeric($_GET['cable_id'])) {
    	//if(is_numeric($_GET['cable_id'])) $cable_id_sql=' AND a.id='.clean($_GET['cable_id']);
    	$sql="SELECT a.*, ct.fib AS fib, c1.address as addr_1, b1.type as type_1, b1.num as num_1, c2.address as addr_2, b2.type as type_2, b2.num as num_2
                                    FROM ".$table_cable." AS a, ".$table_pq." AS b1, ".$table_pq." AS b2, ".$table_node." AS c1, ".$table_node." AS c2, ".$table_cable_type." AS ct 
                                    WHERE (
                                    a.pq_1 = b1.id
                                    AND b1.node = c1.id
                            )
                                    AND (
                                    a.pq_2 = b2.id
                                    AND b2.node = c2.id
                            ) AND a.id=".clean($_GET['cable_id'])." AND ct.id = a.cable_type";
        	$result=pg_fetch_assoc(pg_query($sql));
        	if(isset($_GET['all'])) {
        		$total_num=$result['fib'];
        		while($total_num) {
        			if(! @pg_result(pg_query("SELECT id FROM ".$table_fiber." WHERE cable_id=".clean($_GET['cable_id'])." AND num=".$total_num.";"),0)) {
        				pg_query("INSERT INTO ".$table_fiber." (cable_id,num,user_id) VALUES (".clean($_GET['cable_id']).", ".$total_num.",".$user_id.")");
        				$text.='Волокно №'.$total_num.' добавлено.<br>';
        			} else {
        				$text.='Волокно №'.$total_num.' уже существует.<br>';
        			}
        			$total_num--;
        		}
        		$text.='<input id="exit" type="button" value="ok" />';
        	} else {
	        	$result=pg_fetch_assoc(pg_query($sql));
	        	/*echo '<pre>';
	        	print_r($result);
	        	echo '</pre>';*/
	        	$result_2=pg_query($sql);
	
	        	if($result['type_1']==0) $type_1='Кросс'; else $type_1='Муфта';
	        	if(isset($result['num_1'])) $num_1=' №'.$result['num_1']; else $num_1='';
	        	 
	        	if($row['type_2']==0) $type_2='Кросс'; else $type_2='Муфта';
	        	if(isset($result['num_2'])) $num_2=' №'.$result['num_2']; else $num_2='';
	        	 
	        	$pq_addr_1=$result['addr_1'].' (' .$type_1.$num_1. ')';
	        	$pq_addr_2=$result['addr_2'].' (' .$type_2.$num_2. ')';
	        	
	        	$fib_busy=pg_result(pg_query("SELECT COUNT(*) FROM ".$table_fiber." WHERE cable_id='".$result['id']."';"),0);
	        	if($result['fib']>$fib_busy) {
	        		$select_cable='['.$result['fib'].' ОВ / '.($result['fib']-$fib_busy).'св.]';
	        	}
	
	        	$text.='
	        	<div id="new_fiber" style="new_fiber">
	        	<input type="hidden" id="act" value="'.clean($_GET['act']).'" />
	        	<input type="hidden" id="cable_id" value="'.$result['id'].'" />
	        	Кабель:&nbsp;'.$pq_addr_1.' - '.$pq_addr_2.'&nbsp;'.$select_cable.'&nbsp;Волокно:
	        	<select id="fiber"></select>&nbsp;
	        	<input type="button" id="new_fiber" value="ok" autofocus="autofocus" />
	        	<input id="exit" type="button" value="Отмена" />
	        	<script type="text/javascript">fiber_list_free('.$result['id'].');</script>
	        	</div>';
	        	$text.='<div class="clear"></div>';
        }
        //$text.='$("input#new_fiber").focus();';
        echo $text;
        die;
    }

// ввод нового волокна
	if(isset($_POST['act']) && $_POST['act']=='n_fiber_sql' && @is_numeric($_POST['cable_id']) && @is_numeric($_POST['num'])) {
// проверка, не пустое ли значение прислали
		if($_POST['cable_id']==0 || $_POST['num']==0) die;
		//echo "SELECT id FROM ".$table_pq." WHERE node='".clean($_POST['node'])."' AND type=".$_POST['type']." AND ".$num.";";
		if(! @pg_result(pg_query("SELECT id FROM ".$table_fiber." WHERE cable_id=".clean($_POST['cable_id'])." AND num=".clean($_POST['num']).";"),0)) {
			pg_query("INSERT INTO ".$table_fiber." (cable_id,num,user_id) VALUES (".clean($_POST['cable_id']).", ".clean($_POST['num']).",".$user_id.")");
			//echo "INSERT INTO ".$table_fiber." (cable_id,num) VALUES (".clean($_POST['cable_id']).", ".clean($_POST['num']).")";
			die;
		}
		echo "exist";
		die;
	}

// ввод нового кабеля div
    if(isset($_GET['act']) && ($_GET['act']=='n_cable' || $_GET['act']=='e_cable') && isset($_GET['pq_id']) ) {
    	$lock=clean(!isset($_GET['unlocked'])?'unlocked':'locked');
    	if($_GET['act']=='e_cable')
    	{
    		$cable_id=clean($_GET['cable_id']);
    		//$sql="SELECT id, IF(pq_2=".clean($_GET['pq_id']).",pq_2,IF(pq_1=".clean($_GET['pq_id']).",pq_1,NULL)) AS pq_1, IF(pq_1=".clean($_GET['pq_id']).",pq_2,IF(pq_2=".clean($_GET['pq_id']).",pq_1,NULL)) AS pq_2, fib, descrip FROM ".$table_cable." WHERE id='".$cable_id."' LIMIT 1;";
    		$sql="SELECT c1.id, CASE WHEN c1.pq_2=".clean($_GET['pq_id'])." THEN c1.pq_2 ELSE CASE WHEN c1.pq_1=".clean($_GET['pq_id'])." THEN c1.pq_1 ELSE NULL END END AS pq_1, CASE WHEN c1.pq_1=".clean($_GET['pq_id'])." THEN c1.pq_2 ELSE CASE WHEN c1.pq_2=".clean($_GET['pq_id'])." THEN c1.pq_1 ELSE NULL END END AS pq_2, c1.descrip, ct.id AS cable_type_id, ct.fib
    				FROM ".$table_cable." AS c1, ".$table_cable_type." AS ct
    				WHERE c1.id=".$cable_id." AND c1.cable_type = ct.id LIMIT 1;";
    		//echo $sql;
    		$result=pg_fetch_assoc(pg_query($sql));
    		//$pq_1=$result['pq_1'];
    		$pq_2=$result['pq_2'];
    		$cable_id=$result['id'];
    		$cable_type_id=$result['cable_type_id'];
    		$fib=$result['fib'];
    		//print_r($result);
    	}
		$pq_1=clean($_GET['pq_id']);

    	$sql="SELECT p1.id, pt.type, p1.num, n1.address, n1.loc_text, sn.area_id
			    	FROM ".$table_pq." AS p1, ".$table_node." AS n1, ".$table_pq_type." AS pt, ".$table_street_name." AS sn
			    	WHERE p1.node = n1.id
			    	AND p1.id = ".$pq_1."
			    	AND p1.pq_type_id = pt.id
			    	AND n1.street_id = sn.id
			    	ORDER BY sn.name";
    	$result_pq_1=pg_fetch_assoc(pg_query($sql));
    	
    	//if($result_pq_1['type']==0) $type='Кросс'; else $type='Муфта';
    	if($result_pq_1['type']==0) $type='Кросс'; else if ($result_pq_1['type']==1) $type='Муфта'; else $type='Медный';
    	if(isset($result_pq_1['num'])) $num=' №'.$result_pq_1['num']; else $num='';
    	
    	$pq_1_text=$result_pq_1['address'].(!empty($result_pq_1['loc_text'])?' ('.$result_pq_1['loc_text'].') ':' ').$type.$num;

    	$sql3="SELECT p1.id, pt.type, p1.num, n1.address, n1.loc_text, LEFT(p1.descrip, 15) AS descrip
					FROM ".$table_pq." AS p1, ".$table_node." AS n1, ".$table_pq_type." AS pt, ".$table_street_name." AS sn
					WHERE p1.node = n1.id
					AND p1.id != ".$pq_1."
					AND p1.pq_type_id = pt.id
					AND n1.street_id = sn.id
					AND pt.type".($result_pq_1['type']==2?"":"!")."=2
					".($lock=='unlocked'?"AND sn.area_id = ".$result_pq_1['area_id']:"")."
					ORDER BY n1.address"; //LENGTH(p1.num),
    	$result=pg_query($sql3);
    	//echo $sql3;
    	
    	$node_2_text="";
    	
    	if(pg_num_rows($result)){
    		$select_node_2='<select id="pq_2">';
    		$select_node_2.='<option value="0">Выберите кросс/муфту</option>';
    		while($row=pg_fetch_assoc($result)){
    			//if($row['type']==0) $type='Кросс'; else $type='Муфта';
    			if ($row['type'] == 0) $type = 'Кросс'; else if ($row['type'] == 1) $type = 'Муфта'; else $type = 'Медный';
    			if(isset($row['num'])) $num=' №'.$row['num']; else $num=' №1';
    
    			$select_node_2.='<option value="'.$row['id'].'"';
    			if(@$pq_2==$row['id']) {
    				$select_node_2.=" SELECTED";
    				$node_2_text=$row['address'].(!empty($row['loc_text'])?' ('.$row['loc_text'].') ':' ').$type.$num.(!empty($row['descrip'])?' '.$row['descrip']:'');
    			}
    			$select_node_2.='>'.$row['address'].(!empty($row['loc_text'])?' ('.$row['loc_text'].') ':' ').$type.$num.(!empty($row['descrip'])?' '.$row['descrip']:'').'</option>';
    			//$select_node_2.='>'.$row['address'].(!empty($row['loc_text'])?' ('.$row['loc_text'].') ':' ').$type.$num.' '.$row['descrip'].'</option>';
    			/////////////////////////////////////////
    		}
    		$select_node_2.='</select>';
    	}

    	if(@$pq_2) {
    		// проверка на соединение кабеля с другим кабелем перед перемещением
	    	$sql_cable_connect_busy="SELECT COUNT(*) FROM ".$table_cable." AS c1 JOIN ".$table_pq." AS p1 ON p1.id = ".$pq_2." JOIN ".$table_fiber." AS f1 ON f1.cable_id = c1.id JOIN ".$table_fiber_conn." AS fc1 ON ( fc1.fiber_id_1 = f1.id OR fc1.fiber_id_2 = f1.id ) AND fc1.node_id = p1.node WHERE c1.id = ".$cable_id;
	    	$cable_connect_busy=pg_result(pg_query($sql_cable_connect_busy),0);
	    	if($cable_connect_busy>0) {
	    		$text='
		    		<div class="span11 m5">&nbsp;Волокна сварены с кабелем на узле <a href="?act=s_cable&pq_id='.$pq_2.'">'.$node_2_text.'</a>.</div>
		    		<div class="span2 toolbar m0">
		    			<button class="icon-move m0" id="change_ct" rel_cable_id="'.$cable_id.'" title="Изменить тип"></button>
		    			<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		    		</div>';
	    		echo $text;
	    		die;
	    	}
	    	// проверка на соединение кабеля с портами перед перемещением
	    	$sql_port_connect_busy="SELECT COUNT(*) FROM ".$table_cruz_conn." AS cc1 JOIN ".$table_fiber." AS f1 ON f1.id = cc1.fiber_id WHERE cc1.pq_id = ".$pq_2." AND f1.cable_id = ".$cable_id;
	    	$cable_port_busy=pg_result(pg_query($sql_port_connect_busy),0);
	    	if($cable_port_busy>0) {
	    		$text='
		    		<div class="span11 m5">&nbsp;Волокна соединены с портами на <a href="?act=s_cable&pq_id='.$pq_2.'">'.$node_2_text.'</a>.</div>
		    		<div class="span2 toolbar m0">
		    			<button class="icon-move m0" id="change_ct" rel_cable_id="'.$cable_id.'" title="Изменить тип"></button>
			    		<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		    		</div>';
	    		echo $text;
	    		die;
	    	}
    	}

    	//$sql="SELECT * FROM ".$table_cable_type." ".($fib?"WHERE fib LIKE ".$fib:"")." ORDER BY name, fib";
    	//$sql="SELECT * FROM ".$table_cable_type." ".($fib?"WHERE fib=".$fib:"")." ORDER BY name, fib";
    	$sql="SELECT * FROM ".$table_cable_type." ".(@$fib?"WHERE fib=".$fib:"WHERE type = ".($result_pq_1['type']==2?"1":"0"))."  ORDER BY name, fib";
    	//echo $sql;
    	$result=pg_query($sql);
    	if(pg_num_rows($result)){
    		$select_cable_type='<select id="cable_type">';
    		while($row=pg_fetch_assoc($result)){
    			$select_cable_type.='<option value="'.$row['id'].'" '.($row['id']==@$cable_type_id?"SELECTED":"").'>'.$row['name'].($result_pq_1['type']!=2?' ('.$row['fib'].' ОВ)':'').'</option>';
    		}
    		$select_cable_type.='</select>';
    	}

    	$text='
	    	<input type="hidden" id="act" value="'.@clean($_GET['act']).'" />
	    	<input type="hidden" id="id" value="'.@$cable_id.'" />
	    	<input type="hidden" id="pq_1" value="'.@$pq_1.'" />';
    	//$text.='<div class="span3 input-control text m5">От:&nbsp;'.$result_pq_1['address'].' ('.$type.$num.')</div>';
    	$text.=(@$_GET['prompt']?'<input type="hidden" id="prompt" value="'.$_GET['prompt'].'" />':'');
    	$text.='<div class="span4 text-left input-control text m5">&nbsp;От:&nbsp;'.$pq_1_text.'</div>';
    	$text.='<div class="span text-left input-control text m5">&nbsp;до:&nbsp;</div>';
    	$text.='<div class="span3 text-left input-control text m0">'.$select_node_2.'</div>';
    	$text.='<div class="span text-left input-control text m5">&nbsp;тип:&nbsp;</div>';
    	$text.='<div class="span2 text-left input-control text m0">'.$select_cable_type.'</div>';
    	//$text.='<div class="span4 text-left input-control text"></div>';
    	//if($_GET['act']=='n_cable') $text.='<div class="span3 input-control text m0"><input class="" type="text" id="descrip" value="'.$descrip.'" placeholder="Описание"/></div>';
    	
    	//$text.='&nbsp;<input class="mini" type="button" id="new_cable" value="ok" autofocus="autofocus" />&nbsp;<input class="mini" id="exit" type="button" value="Отмена" /></div>';
    	////////////////////
    	$text.='
    		<div class="span2 toolbar m0">
		    	<button class="icon-checkmark m0" id="new_cable" rel="'.clean($_GET['pq_id']).'" title="ok"></button>
		    	<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		    	<button class="icon-'.$lock.' m0" id="cable_add_div" rel="?act='.clean($_GET['act']).'&pq_id='.clean($_GET['pq_id']).(isset($cable_id)?'&cable_id='.$cable_id:'').'&'.$lock.'" title="'.($lock=='unlocked'?"Все узлы/муфты":"Узлы/муфты района").'"></button>
    		</div>';
    	//show_menu();
    	echo $text;
    	die;
    }

// изменения типа кабеля div
    if(isset($_POST['act']) && ($_POST['act']=='change_cable_type' && isset($_POST['cable_id']) )) {

    	$cable_id=clean($_POST['cable_id']);

    	$sql="SELECT ct1.id, ct1.fib
    			FROM ".$table_cable." AS c1, ".$table_cable_type." AS ct1
    			WHERE c1.id=".$cable_id." AND c1.cable_type = ct1.id LIMIT 1;";
    	$result=pg_fetch_assoc(pg_query($sql));
    	$cable_type_id=$result['id'];
    	$fib=$result['fib'];
    
    	//echo $fib;
    	$sql="SELECT * FROM ".$table_cable_type." WHERE fib=".$fib." ORDER BY name, fib";
    	$result=pg_query($sql);
    	if(pg_num_rows($result)){
    		$select_cable_type='<select id="cable_type">';
    		while($row=pg_fetch_assoc($result)){
    			$select_cable_type.='<option value="'.$row['id'].'" '.($row['id']==$cable_type_id?"SELECTED":"").'>'.$row['name'].($result_pq_1['type']!=2?' ('.$row['fib'].' ОВ)':'').'</option>';
    		}
    		$select_cable_type.='</select>';
    	}
    
    	$text='
	    	<input type="hidden" id="id" value="'.$cable_id.'" />';
    	$text.='<div class="span text-left input-control text m5">&nbsp;Изменить тип кабеля на:&nbsp;</div>';
    	$text.='<div class="span9 text-left input-control text m0">'.$select_cable_type.'</div>';

    	$text.='
    		<div class="span2 toolbar m0">
		    	<button class="icon-checkmark m0" id="change_ct_sql" rel_cable_id="'.$cable_id.'" title="ok"></button>
		    	<button class="icon-blocked m0" id="exit" title="Отмена"></button>
	    	</div>';
    	//show_menu();
    	echo $text;
    	die;
    }

// изменения типа кабеля sql
    if(isset($_POST['act']) && ($_POST['act']=='change_cable_type_sql' && isset($_POST['cable_id']) && isset($_POST['cable_type']) )) {
    	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cable." WHERE id = ".clean($_POST['cable_id']) ));

    	pg_query("UPDATE ".$table_cable." SET cable_type=".clean($_POST['cable_type']).", user_id=".$user_id." WHERE id=".clean($_POST['cable_id']).";")
    		or die("Изменить тип кабеля невозможно, хз почему!!!");

    	$data_new=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cable." WHERE id = ".clean($_POST['cable_id']) ));

    	$result = serialize(array_diff($data_old, $data_new));
    	add_log($table_cable,clean($_POST['cable_id']),$result,$user_id);
    	die;
    }

// удаление кабеля div
    if(isset($_GET['act']) && $_GET['act']=='d_cable' && isset($_GET['pq_id']) && @is_numeric($_GET['cable_id']) ) {

    	$cable_id=clean($_GET['cable_id']);
    	$sql="SELECT id, CASE WHEN pq_2=".clean($_GET['pq_id'])." THEN pq_2 ELSE CASE WHEN pq_1=".clean($_GET['pq_id'])." THEN pq_1 ELSE NULL END END AS pq_1, CASE WHEN pq_1=".clean($_GET['pq_id'])." THEN pq_2 ELSE CASE WHEN pq_2=".clean($_GET['pq_id'])." THEN pq_1 ELSE NULL END END AS pq_2, descrip FROM ".$table_cable." WHERE id='".$cable_id."' LIMIT 1;";
    	$result=pg_fetch_assoc(pg_query($sql));
    	$pq_2=$result['pq_2'];

    	$pq_1=clean($_GET['pq_id']);
    
    	if($pq_2) {
    		$sql_cable_connect_busy_pq_1="SELECT COUNT(*) FROM ".$table_cable." AS c1 JOIN ".$table_pq." AS p1 ON p1.id = ".$pq_1." JOIN ".$table_fiber." AS f1 ON f1.cable_id = c1.id JOIN ".$table_fiber_conn." AS fc1 ON ( fc1.fiber_id_1 = f1.id OR fc1.fiber_id_2 = f1.id ) AND fc1.node_id = p1.node WHERE c1.id = ".$cable_id;
    		$sql_cable_connect_busy_pq_2="SELECT COUNT(*) FROM ".$table_cable." AS c1 JOIN ".$table_pq." AS p1 ON p1.id = ".$pq_2." JOIN ".$table_fiber." AS f1 ON f1.cable_id = c1.id JOIN ".$table_fiber_conn." AS fc1 ON ( fc1.fiber_id_1 = f1.id OR fc1.fiber_id_2 = f1.id ) AND fc1.node_id = p1.node WHERE c1.id = ".$cable_id;
    		$sql_port_connect_busy_pq_1="SELECT COUNT(*) FROM ".$table_cruz_conn." AS cc1 JOIN ".$table_fiber." AS f1 ON f1.id = cc1.fiber_id WHERE cc1.pq_id = ".$pq_1." AND f1.cable_id = ".$cable_id;
    		$sql_port_connect_busy_pq_2="SELECT COUNT(*) FROM ".$table_cruz_conn." AS cc1 JOIN ".$table_fiber." AS f1 ON f1.id = cc1.fiber_id WHERE cc1.pq_id = ".$pq_2." AND f1.cable_id = ".$cable_id;
    		// проверка на соединение кабеля с другим кабелем перед перемещением
    		$cable_connect_busy_pq_1=pg_result(pg_query($sql_cable_connect_busy_pq_1),0);
    		$cable_connect_busy_pq_2=pg_result(pg_query($sql_cable_connect_busy_pq_2),0);
    		// проверка на соединение кабеля с портами перед перемещением
    		$cable_port_busy_pq_1=pg_result(pg_query($sql_port_connect_busy_pq_1),0);
    		$cable_port_busy_pq_2=pg_result(pg_query($sql_port_connect_busy_pq_2),0);
    		//echo 'cable: '.$cable_id.' fib_bus: '.$cable_connect_busy.' port_bus: '.$cable_port_busy;
    		if($cable_connect_busy_pq_1==0 && $cable_connect_busy_pq_2==0 && $cable_port_busy_pq_1==0 && $cable_port_busy_pq_2==0) {
    			$text='<div class="warning">Удалить кабель??? <a href="?act=s_cable&pq_id='.$pq_2.'">'.$node_2_text.'</a>&nbsp;';
    			$text.='<input type="hidden" id="cable_id" value="'.$cable_id.'" /><input id="d_cable_all_button" type="button" value="Удалить" /><input id="exit" type="button" value="отмена" />';
    			$text.='</div><div class="clear"></div>';
    			$text='
    				<input type="hidden" id="cable_id" value="'.$cable_id.'" />
	    			<div class="span10 m5">&nbsp;Удалить кабель??? <a href="?act=s_cable&pq_id='.$pq_2.'">'.$node_2_text.'</a>.</div>
	    			<div class="span2 toolbar m0">
	    				<button class="icon-checkmark m0" id="d_cable_all_button" rel="'.clean($_GET['pq_id']).'" title="ok"></button>
	    				<button class="icon-blocked m0" id="exit" title="Отмена"></button>
	    			</div>';
    		} else {
    			if($cable_connect_busy_pq_1!=0 || $cable_port_busy_pq_1!=0) {
	    			$text='<div class="warning">Кабель занят...&nbsp;';
	    			$text.='<input id="exit" type="button" value="отмена" />';
	    			$text.='</div><div class="clear"></div>';
	    			$text='
		    			<div class="span11 m5">&nbsp;Кабель занят на текущем узле.</a>.</div>
		    			<div class="span1 toolbar m0">
		    				<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		    			</div>';
    			} else if($cable_connect_busy_pq_2!=0 || $cable_port_busy_pq_2!=0) {
	    			$text='<div class="warning">Кабель занят...&nbsp;';
	    			$text.='<input id="exit" type="button" value="отмена" />';
	    			$text.='</div><div class="clear"></div>';
	    			$text='
		    			<div class="span11 m5">&nbsp;Кабель занят удалённом узле.</a>.</div>
		    			<div class="span1 toolbar m0">
		    				<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		    			</div>';
    			}
    		}
    		echo $text;
    	}    	
    	die;
    }

// удаление кабеля с волокнами
    if(isset($_POST['act']) && $_POST['act']=='d_cable_all' && @is_numeric($_POST['cable_id'])) {
    	if($_POST['cable_id']==0) die;
    	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_fiber." WHERE cable_id = ".clean($_POST['cable_id']) ));
    	$result = serialize($data_old);
    	add_log($table_fiber,clean($_POST['cable_id']),$result,$user_id);

		pg_query("DELETE FROM ".$table_fiber." WHERE cable_id = ".clean($_POST['cable_id']));
		
		$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cable." WHERE id = ".clean($_POST['cable_id']) ));
		$result = serialize($data_old);
		add_log($table_cable,clean($_POST['cable_id']),$result,$user_id);

		pg_query("DELETE FROM ".$table_cable." WHERE id = ".clean($_POST['cable_id']));
    	die;
    }

// удаление порта
    if(isset($_POST['act']) && $_POST['act']=='d_port' && @is_numeric($_POST['port_id'])) {
        if(@pg_result(pg_query("SELECT id FROM ".$table_cruz_conn." WHERE id=".clean($_POST['port_id'])." AND fiber_id IS NULL"),0)) {
        	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cruz_conn." WHERE id = ".clean($_POST['port_id']) ));
        	$result = serialize($data_old);
        	add_log($table_cruz_conn,clean($_POST['port_id']),$result,$user_id);

            pg_query("DELETE FROM ".$table_cruz_conn." WHERE id = ".clean($_POST['port_id']));
            die;
        }
        echo "exist";
        die;
    }

// вывод списка кабелей на узле
    if(isset($_POST['act']) && $_POST['act']=='s_cable_list' && @is_numeric($_POST['pq_id']) && @is_numeric($_POST['cable_id']) && @is_numeric($_POST['fiber_id']) ) {
    	//echo get_cable_select(clean($_POST['fiber_id']),clean($_POST['pq_id']),clean($_POST['cable_id']),clean($_POST['enable']));
    	echo get_cable_select(clean($_POST['fiber_id']),clean($_POST['pq_id']),clean($_POST['cable_id']));
    	die;
    }

// вывод не подключенных волокон в кабеле
    if(isset($_POST['act']) && $_POST['act']=='s_fiber_list' && @is_numeric($_POST['node_id']) && @is_numeric($_POST['pq_id']) && @is_numeric($_POST['cable_id']) && @is_numeric($_POST['to_fiber_id']) && @is_numeric($_POST['fiber_id'])) {
    	echo get_fiber_select(clean($_POST['fiber_id']),clean($_POST['node_id']),clean($_POST['pq_id']),@clean($_POST['cable_id']),@clean($_POST['to_fiber_id']),@clean($_POST['port_id']),clean($_POST['enable']));
    	die;
    }
    
// ввод нового соединения волокон
    if(isset($_POST['act']) && $_POST['act']=='n_fiber_conn' && @is_numeric($_POST['fiber_id']) && @is_numeric($_POST['to_fiber_id']) && @is_numeric($_POST['node_id'])) {
    	if(! @pg_result(pg_query("SELECT id FROM ".$table_fiber_conn." WHERE fiber_id_1=".clean($_POST['fiber_id'])." AND fiber_id_2=".clean($_POST['to_fiber_id'])." AND node_id=".clean($_POST['node_id']).";"),0)) {
    		pg_query("INSERT INTO ".$table_fiber_conn." (fiber_id_1,fiber_id_2,node_id,user_id) VALUES (".clean($_POST['fiber_id']).", ".clean($_POST['to_fiber_id']).", ".clean($_POST['node_id']).",".$user_id.")");
    		// установить галки занятоски портов
    		pg_query("UPDATE ".$table_cruz_conn." SET used = true WHERE id = (SELECT cc1.id FROM ".$table_pq." AS pq1, ".$table_cruz_conn." AS cc1 WHERE pq1.node = ".clean($_POST['node_id'])." AND pq1.id = cc1.pq_id AND cc1.fiber_id = ".clean($_POST['fiber_id']).");");
    		pg_query("UPDATE ".$table_cruz_conn." SET used = true WHERE id = (SELECT cc1.id FROM ".$table_pq." AS pq1, ".$table_cruz_conn." AS cc1 WHERE pq1.node = ".clean($_POST['node_id'])." AND pq1.id = cc1.pq_id AND cc1.fiber_id = ".clean($_POST['to_fiber_id']).");");
    		
    		/*$result = pg_query("SELECT cc1.id FROM fibers.cruz_conn AS cc1, fibers.pq AS pq1, fibers.fiber_conn AS fc1 WHERE ((cc1.fiber_id = fc1.fiber_id_1 OR cc1.fiber_id = fc1.fiber_id_2) AND fc1.node_id = pq1.node ) AND cc1.pq_id = pq1.id AND cc1.fiber_id IS NOT NULL AND cc1.used IS NULL");
    		if (pg_num_rows($result)) {
    			while ($row = pg_fetch_assoc($result)) {
    				echo $row['id'].'
';
    				pg_query("UPDATE ".$table_cruz_conn." SET used = true WHERE id = ".$row['id'].";");
    			}
    		}*/
    		
    		die;
    	}
    	echo "exist";
    	die;
    }

// удаление соединения волокон
    if(isset($_POST['act']) && $_POST['act']=='d_fiber_conn' && @is_numeric($_POST['node_id']) && @is_numeric($_POST['to_fiber_id']) && @is_numeric($_POST['fiber_id'])) {
    	$id=@pg_result(pg_query("SELECT id FROM ".$table_fiber_conn." WHERE ( fiber_id_1=".clean($_POST['fiber_id'])." OR fiber_id_1=".clean($_POST['to_fiber_id'])." ) AND ( fiber_id_1=".clean($_POST['fiber_id'])." OR fiber_id_1=".clean($_POST['to_fiber_id'])." ) AND node_id=".clean($_POST['node_id']).";"),0);
    	if($id) {
    		//echo "SELECT id FROM ".$table_fiber_conn." WHERE fiber_id_1=".clean($_POST['fiber_id'])." AND fiber_id_2=".clean($_POST['to_fiber_id'])." AND node_id=".clean($_POST['node_id']).";";
    		if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_fiber_conn." WHERE ( fiber_id_1=".clean($_POST['fiber_id'])." OR fiber_id_1=".clean($_POST['to_fiber_id'])." ) AND ( fiber_id_2=".clean($_POST['fiber_id'])." OR fiber_id_2=".clean($_POST['to_fiber_id'])." ) AND node_id=".clean($_POST['node_id']).";"),0)>1) {
				echo "Волокно занято!!! Не работает... Ищите ашипку";
				die;
			} else {
				$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_fiber_conn." WHERE id = ".$id ));
				$result = serialize($data_old);
				add_log($table_fiber_conn,$id,$result,$user_id);
				// снять галки занятоски портов
				pg_query("UPDATE ".$table_cruz_conn." SET used = NULL WHERE id = (SELECT cc1.id FROM ".$table_pq." AS pq1, ".$table_cruz_conn." AS cc1 WHERE pq1.node = ".clean($_POST['node_id'])." AND pq1.id = cc1.pq_id AND cc1.fiber_id = ".clean($_POST['fiber_id']).");");
				pg_query("UPDATE ".$table_cruz_conn." SET used = NULL WHERE id = (SELECT cc1.id FROM ".$table_pq." AS pq1, ".$table_cruz_conn." AS cc1 WHERE pq1.node = ".clean($_POST['node_id'])." AND pq1.id = cc1.pq_id AND cc1.fiber_id = ".clean($_POST['to_fiber_id']).");");

				pg_query("DELETE FROM ".$table_fiber_conn." WHERE id = ".$id);
			}
    		die;
    	}
    	echo "exist";
    	die;
    }

// отслеживание соединения волокон
    if(isset($_POST['act']) && $_POST['act']=='f_fiber_conn' && @is_numeric($_POST['node_id']) && @is_numeric($_POST['fiber_id']) && @is_numeric($_POST['to_fiber_id'])) {
    	//$id=@pg_result(pg_query("SELECT id FROM ".$table_fiber_conn." WHERE ( fiber_id_1=".clean($_POST['fiber_id'])." OR fiber_id_1=".clean($_POST['to_fiber_id'])." ) AND ( fiber_id_1=".clean($_POST['fiber_id'])." OR fiber_id_1=".clean($_POST['to_fiber_id'])." ) AND node_id=".clean($_POST['node_id']).";"),0);
    	/*if($id) {
    		//echo "SELECT id FROM ".$table_fiber_conn." WHERE fiber_id_1=".clean($_POST['fiber_id'])." AND fiber_id_2=".clean($_POST['to_fiber_id'])." AND node_id=".clean($_POST['node_id']).";";
    		if(!@pg_result(pg_query("SELECT COUNT(*) FROM ".$table_fiber_conn." WHERE ( fiber_id_1=".clean($_POST['fiber_id'])." OR fiber_id_1=".clean($_POST['to_fiber_id'])." ) AND ( fiber_id_2=".clean($_POST['fiber_id'])." OR fiber_id_2=".clean($_POST['to_fiber_id'])." ) AND node_id=".clean($_POST['node_id']).";"),0)>1) {
    			echo "Волокно занято!!! Не работает... Ищите ашипку";
    			die;
    		} else pg_query("DELETE FROM ".$table_fiber_conn." WHERE id = ".$id);
    		die;
    	}*/
    	$i=0;
    	//echo find_fiber(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),'last',clean($_POST['f_fiber_pq_iq']));
    	//echo fib_find(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),0,clean($_POST['f_fiber_pq_iq']));
    	
    	//echo fib_find(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),clean($_POST['node_id']),clean($_POST['to_node_id']),true);
        echo fib_find(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),clean($_POST['to_node_id']));

    	//echo fib_find(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),0,clean($_POST['to_node']));
    	//function fib_find($id,$last_id,$to_pq,$to_node_id) {
    	//echo fib_find($id,$last_id,0,81);
    	echo '<div class="clear"></div>';
        //echo '<input type="button" id="f_fiber_clean_'.clean($_POST['fiber_id']).'" value="Очистить" />';
    	die;
    }

// отслеживание занятости портов
    if(isset($_POST['act']) && $_POST['act']=='f_fiber_used' && @is_numeric($_POST['node_id']) && @is_numeric($_POST['fiber_id']) && @is_numeric($_POST['to_fiber_id'])) {
    	$i=0;
    	//echo ' pq: '.clean($_POST['pq_id']);
    	//echo fib_find_used(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),clean($_POST['to_node_id']),clean($_POST['pq_id']));
    	
    	echo fib_find_used(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),clean($_POST['to_node_id']),clean($_POST['pq_id']));
    	
    	//echo fib_find_used(clean($_POST['to_fiber_id']),clean($_POST['fiber_id']),clean($_POST['node_id']));
    	//echo clean($_POST['fiber_id']).' '.clean($_POST['to_fiber_id']).' '.clean($_POST['node_id']);
    	//echo fib_find_used(clean($_POST['to_fiber_id']),clean($_POST['fiber_id']),clean($_POST['node_id']));
    	//echo '<div class="clear"></div>';
    	die;
    }

// вывод отслеживания соединения волокон на карту
    if(isset($_GET['act']) && $_GET['act']=='find_fiber' && @is_numeric($_GET['fiber_id'])) {
    	// массив для линий
    	global $array_line;
    	$array_line = array();
    	// массив для точек
    	global $array_point;
    	$array_point = array();

    	header('Content-Type: application/json');

    	$node_id = (is_numeric($_GET['node'])?clean($_GET['node']):0);
    	$fiber_id = clean($_GET['fiber_id']);
    	$sql="SELECT CASE WHEN fc.fiber_id_1 = ".$fiber_id." THEN fc.fiber_id_2 ELSE fc.fiber_id_1 END AS to_fiber FROM ".$table_fiber_conn." AS fc WHERE (fiber_id_1 = ".$fiber_id." OR fiber_id_2 = ".$fiber_id.")";

    	$result = pg_query($sql);
    	if (pg_num_rows($result)) {
    		while ($row = pg_fetch_assoc($result)) {
    			//echo $fiber_id.' '.$row['to_fiber'].'<br>';
    			fib_find($fiber_id,$row['to_fiber'],$node_id,true);
    			fib_find($row['to_fiber'],$fiber_id,$node_id,true);
    		}
    	}

    	$sql="SELECT ST_AsGeoJSON(cc.the_geom) AS the_geom, ST_AsGeoJSON(n1.the_geom) AS the_geom1, ST_AsGeoJSON(n2.the_geom) AS the_geom2
				FROM (SELECT
						f1.id, c1.pq_1, c1.pq_2, c1.the_geom AS the_geom
					FROM ".$table_fiber." AS f1, ".$table_cable." AS c1
					WHERE
						f1.id = ".$fiber_id."
					AND
						f1.cable_id = c1.id) AS cc
				LEFT JOIN ".$table_cruz_conn." AS cc1 ON cc1.fiber_id = cc.id AND cc1.pq_id = cc.pq_1
					LEFT JOIN ".$table_pq." AS p1 ON p1.id = cc1.pq_id
						LEFT JOIN ".$table_node." AS n1 ON n1.id = p1.node
				
				LEFT JOIN ".$table_cruz_conn." AS cc2 ON cc2.fiber_id = cc.id AND cc2.pq_id = cc.pq_2
					LEFT JOIN ".$table_pq." AS p2 ON p2.id = cc2.pq_id
						LEFT JOIN ".$table_node." AS n2 ON n2.id = p2.node";

    	$result=pg_fetch_assoc(pg_query($sql));
    	$array_line[] = $result['the_geom'];

    	if(isset($result['the_geom1'])) $array_point[] = $result['the_geom1'];
    	if(isset($result['the_geom2'])) $array_point[] = $result['the_geom2'];

    	$array_line = array_unique($array_line);
    	$array_point = array_unique($array_point);

    	$geom = '{ "type": "FeatureCollection",
	"features":[
		';
    	$total = count($array_line);
    	$counter = 0;
    	$pre = '{"type":"Feature", "properties":{"strokeColor":"#ff4500", "strokeWidth":5}, "geometry":';
    	foreach ($array_line as $line) {
    		$counter++;
    		if ($counter == $total) {
    			$geom .= $pre.$line.'}
	';
    		}
    		else {
    			$geom .= $pre.$line.'},
		';
    		}
    	}
    	$total = count($array_point);
    	$counter = 0;
    	if($total>0) $geom .=",";
    	$pre = '{"type":"Feature", "properties":{"strokeColor":"black", "fillColor":"yellow","pointRadius": 8, "strokeWidth":1.5}, "geometry":';
    	foreach ($array_point as $line) {
    		$counter++;
    		if ($counter == $total) {
    			$geom .= $pre.$line.'}
	';
    		}
    		else {
    			$geom .= $pre.$line.'},
		';
    		}
    	}

    	$geom .= ']
}';
    	echo $geom;
    	//echo fib_find(clean($_GET['fiber_id']),0,$geom);
    	//echo fib_find(clean($_GET['fiber_id']),0,0,$geom);
/*    	print_r($array_line);
    	$array_line = array_unique($array_line);
    	print_r($array_line);*/
    	//print_r($array_point);
    	die;
    }

function fib_find($id,$last_id,$to_node_id,$geom=false) {
	//echo ' 1: '.$id.' 2: '.$last_id.' 3: '.$to_node_id.'<br>';
	//die;
	global $table_cruz_conn;
	global $table_fiber_conn;
	global $table_fiber;
	global $table_cable;
	global $table_pq;
	global $table_pq_type;
	global $table_node;
	global $table_color;
	global $array_line;
	global $array_point;

	$sql='
	SELECT
	f1.id AS id, f1.num AS num, n1.id AS from_node_id,
	CASE WHEN p1.node = n1.id THEN p1.id ELSE p2.id END AS from_pq_id,
	c1.id AS from_cable_id,
	ST_AsGeoJSON(c1.the_geom) AS the_geom1,
	ST_AsGeoJSON(c2.the_geom) AS the_geom2,

	f2.id AS to_id, f2.num AS to_num, n2.id AS to_node_id,
	CASE WHEN p3.node = n2.id THEN p3.id ELSE p4.id END AS to_pq_id,
	c2.id AS to_cable_id,
		
	c_n.id AS curr_node_id, c_n.address AS curr_node_addr, c_n.descrip AS curr_node_descrip,
	c_n.incorrect,
	CASE WHEN p1.node = c_n.id THEN p1.id ELSE CASE WHEN p2.node = c_n.id THEN p2.id ELSE NULL END END AS curr_pq_id,

	f1.mod_color AS mod_color_1, f1.fib_color AS fib_color_1,
	f2.mod_color AS mod_color_2, f2.fib_color AS fib_color_2

	FROM '.$table_fiber_conn.' AS fc1, '.$table_fiber.' AS f1, '.$table_cable.' AS c1, '.$table_pq.' AS p1, '.$table_pq.' AS p2, '.$table_fiber.' AS f2, '.$table_cable.' AS c2, '.$table_pq.' AS p3, '.$table_pq.' AS p4,
	'.$table_node.' AS n1, '.$table_node.' AS c_n, '.$table_node.' AS n2
	WHERE
	( ( fc1.fiber_id_1 = '.$id.' AND fc1.fiber_id_2 != '.$last_id.' ) OR ( fc1.fiber_id_2 = '.$id.' AND fc1.fiber_id_1 != '.$last_id.' ) )
	AND
	f1.id = '.$id.'
	AND
	c1.id = f1.cable_id
	AND
	p1.id = c1.pq_1 AND p2.id = c1.pq_2
	AND
	n1.id = CASE WHEN ( p1.node = p3.node OR p1.node = p4.node ) THEN p2.node ELSE p2.node END
	AND
	p3.id = c2.pq_1 AND p4.id = c2.pq_2
	AND
	n2.id = CASE WHEN ( p1.node = p3.node OR p2.node = p3.node ) THEN p4.node ELSE p3.node END
	AND
	( (c_n.id = CASE WHEN ( p1.node = p3.node OR p1.node = p4.node ) THEN p1.node ELSE NULL END ) OR ( c_n.id = CASE WHEN ( p2.node = p3.node OR p2.node = p4.node ) THEN p2.node ELSE NULL END ) )
	AND
	f2.id = CASE WHEN fc1.fiber_id_1 = '.$id.' THEN fc1.fiber_id_2 ELSE fc1.fiber_id_1 END
	AND
	c2.id = f2.cable_id
	'.($to_node_id>0?'AND c_n.id = '.$to_node_id:"");

	//echo "|".($to_node_id>0?'AND c_n.id = '.$to_node_id:"")."|";
	
//	print_r('<pre>'.$sql.'</pre>');
	//echo 'last_id: '.$last_id.' id: '.$id.'<br>';
	$result=@pg_fetch_assoc(pg_query($sql));
/*
    echo '<pre>';
    print_r($sql);
    echo '</pre>';
*/
	if($result) {
		//echo 'curr_pq: '.$result['curr_pq_id'].'<br>';
		//print_r('<pre>'.$sql.'</pre><hr>');

		if(!$geom) {
			$sql_color='SELECT
						col1.name AS mod_name_1, col1.color AS mod_color_1, col1.stroke AS mod_stroke_1,
						col2.name AS fib_name_1, col2.color AS fib_color_1, col2.stroke AS fib_stroke_1,
						col3.name AS mod_name_2, col3.color AS mod_color_2, col3.stroke AS mod_stroke_2,
						col4.name AS fib_name_2, col4.color AS fib_color_2, col4.stroke AS fib_stroke_2
					FROM '.$table_color.'
					LEFT JOIN '.$table_color.' AS col1 ON '.$result['mod_color_1'].' = col1.id AND col1.type = 0
					LEFT JOIN '.$table_color.' AS col2 ON '.$result['fib_color_1'].' = col2.id AND col2.type = 1
			
					LEFT JOIN '.$table_color.' AS col3 ON '.$result['mod_color_2'].' = col3.id AND col3.type = 0
					LEFT JOIN '.$table_color.' AS col4 ON '.$result['fib_color_2'].' = col4.id AND col4.type = 1
					LIMIT 1';
			//print_r('<pre>'.$sql_color.'</pre>');
			$result_color=pg_fetch_assoc(pg_query($sql_color));
			/////
			// вывод номеров портов
			// волокно 1
			$sql_c1="SELECT * FROM ".$table_pq." AS p1, ".$table_cruz_conn." AS cc1 WHERE p1.node = ".$result['curr_node_id']." AND cc1.fiber_id = ".$result['id']." AND p1.id = cc1.pq_id";
			$result_c1=pg_fetch_assoc(pg_query($sql_c1));
	
			if(isset($result_c1['port'])) $port1=$result_c1['port'];
			// волокно 2
			$sql_c2="SELECT * FROM ".$table_pq." AS p1, ".$table_cruz_conn." AS cc1 WHERE p1.node = ".$result['curr_node_id']." AND cc1.fiber_id = ".$result['to_id']." AND p1.id = cc1.pq_id";
			$result_c2=pg_fetch_assoc(pg_query($sql_c2));
			if(isset($result_c2['port'])) $port2=$result_c2['port'];
	
	        //echo 'port_1: '.$result_c1['port'].' port_2: '.$result_c2['port'];
			// если кроссы не совподают, то выводить номер кросса
			if($result_c1['pq_id'] != $result_c2['pq_id']) {
	            if($result_c1['type']==0) $type1='Кросс'; else $type1='Муфта';
	            if(isset($result_c1['num'])) $num1.='№'.$result_c1['num']; else $num1.='';
	                
	            if($result_c2['type']==0) $type2='Кросс'; else $type2='Муфта';
	            if(isset($result_c2['num'])) $num2.='№'.$result_c2['num']; else $num2.='';
	
	            if($type1==$type2) $cruz='<div class="show_find_pq_legend">'.$type1.':</div><div class="show_find_pq_lfib"><a class="isoc" href="?act=s_cable&pq_id='.$result_c1['pq_id'].'" target="_blank">'.$num1.'</a></div><div class="show_find_pq_rfib"><a class="isoc" href="?act=s_cable&pq_id='.$result_c2['pq_id'].'" target="_blank">'.$num2.'</a></div>';
			} else {
				$sql_c="SELECT * FROM ".$table_pq." AS p1, ".$table_pq_type." AS pt WHERE p1.id = ".$result['curr_pq_id']." AND p1.pq_type_id = pt.id";
				$result_c=pg_fetch_assoc(pg_query($sql_c));
	
				$num='';
				if($result_c['type']==0) $type='Кросс'; else $type='Муфта';
				if(isset($result_c['num'])) $num.='№'.$result_c['num']; else $num.='';
	
				if(!$num) $num = '№1';
				$cruz='<div class="show_find_pq_legend">'.$type.':</div><div class="show_find_pq_fib"><a class="isoc" href="?act=s_cable&pq_id='.$result['curr_pq_id'].'" target="_blank">&nbsp;'.$num.'&nbsp;</a></div>';
			}
	
	        // если заданы порты, то выводим
	        if($result_c1 && $result_c2) {
	            $port='<div class="show_find_pq_legend border_top">Порт:</div><div class="show_find_pq_lfib border_top">'.$port1.'</div><div class="show_find_pq_rfib border_top">'.$port2.'</div>';
	
	        } else {
	            $port='<div class="show_find_pq_legend border_top">&nbsp;</div><div class="show_find_pq_lfib border_top">&nbsp;</div><div class="show_find_pq_rfib border_top">&nbsp;</div>';
	
	        }
	
			$text='
			<div class="show_find_pq">
			<div class="show_find_pq_title'.($result['incorrect']==true?' bg-color-orangeDark':'').'">
			<a class="isoc'.($result['incorrect']==true?' bg-color-orangeDark':'').'" href="?act=s_pq&node_id='.$result['curr_node_id'].'" target="_blank">'.$result['curr_node_addr'].'</a>
			</div>';
			$text.=$cruz;
			$text.='
			<div class="show_find_pq_legend border_top">ОВ:</div><div class="show_find_pq_lfib border_top">'.$result['num'].'</div><div class="show_find_pq_rfib border_top">'.$result['to_num'].'</div>';
			$text.='
			<div class="show_find_pq_legend border_top">цвет:</div>
			<div class="show_find_pq_lfib border_top">
					<div class="show_find_pq_mod_color" title="Цвет модуля: '.($result_color['mod_name_1']?$result_color['mod_name_1']:'не задан').'" style="background-color: '.$result_color['mod_color_1'].'">'.($result_color['mod_stroke_1']?'/':'&nbsp;').'</div>
					<div class="show_find_pq_fib_color" title="Цвет волокна: '.($result_color['fib_name_1']?$result_color['fib_name_1']:'не задан').'" style="background-color: '.$result_color['fib_color_1'].'">'.($result_color['fib_stroke_1']?'/':'&nbsp;').'</div>
					<div class="clear"></div>
			</div>
			<div class="show_find_pq_rfib border_top">
					<div class="show_find_pq_mod_color" title="Цвет модуля: '.($result_color['mod_name_2']?$result_color['mod_name_2']:'не задан').'" style="background-color: '.$result_color['mod_color_2'].'">'.($result_color['mod_stroke_2']?'/':'&nbsp;').'</div>
					<div class="show_find_pq_fib_color" title="Цвет волокна: '.($result_color['fib_name_2']?$result_color['fib_name_2']:'не задан').'" style="background-color: '.$result_color['fib_color_2'].'">'.($result_color['fib_stroke_2']?'/':'&nbsp;').'</div>
					<div class="clear"></div>
			</div>';
			$text.=$port;
			$text.='
			</div>';
			$text.='<div class="show_find_pq_arrow">></div>';
			echo $text;
		} else {
			// занесение координат кабелей в массив
			if($last_cable_id != $result['from_cable_id']) {
				$array_line[] = $result['the_geom1'];
			} else {
				$array_line[] = $result['the_geom2'];
			}

			// занесение координат узла в массив, если шос
			// волокно 1
			$sql_c1="SELECT *, ST_AsGeoJSON(n1.the_geom) AS the_geom FROM ".$table_pq." AS p1, ".$table_cruz_conn." AS cc1, ".$table_node." AS n1 WHERE p1.node = ".$result['curr_node_id']." AND cc1.fiber_id = ".$result['id']." AND p1.id = cc1.pq_id AND n1.id = p1.node";
			$result_c1=pg_fetch_assoc(pg_query($sql_c1));
			// волокно 2
			$sql_c2="SELECT *, ST_AsGeoJSON(n1.the_geom) AS the_geom FROM ".$table_pq." AS p1, ".$table_cruz_conn." AS cc1 ".$table_node." AS n1 WHERE p1.node = ".$result['curr_node_id']." AND cc1.fiber_id = ".$result['to_id']." AND p1.id = cc1.pq_id AND n1.id = p1.node";
			$result_c2=pg_fetch_assoc(pg_query($sql_c2));

			if(isset($result_c1['port']) || isset($result_c2['port'])) $array_point[] = $result_c1['the_geom'];
		}
		echo fib_find($result['to_id'],$result['id'],$result['to_node_id'],$geom);
	} else {
        $sql2='
        SELECT
        	f1.id AS id, f1.num AS num, n1.id AS curr_node_id, n1.address AS curr_node_addr, n1.incorrect, n1.descrip AS curr_node_descrip, p1.id AS curr_pq_id,
        	f1.mod_color AS mod_color, f1.fib_color AS fib_color,
        	ST_AsGeoJSON(c1.the_geom) AS the_geom
        FROM '.$table_fiber.' AS f1, '.$table_cable.' AS c1, '.$table_pq.' AS p1, '.$table_node.' AS n1
        WHERE
        f1.id = '.$id.'
        AND
        c1.id = f1.cable_id
        AND
        ( c1.pq_1 = p1.id OR c1.pq_2 = p1.id)
		'.($to_node_id>0?'AND p1.node = '.$to_node_id:"").'
        AND
        n1.id = p1.node
        ';

        //print_r('<pre>'.$sql2.'</pre>');

		if(!$geom) {
			$result2=pg_fetch_assoc(pg_query($sql2));
			$sql_color='SELECT
						col1.name AS mod_name, col1.color AS mod_color, col1.stroke AS mod_stroke,
						col2.name AS fib_name, col2.color AS fib_color, col2.stroke AS fib_stroke
					FROM '.$table_color.'
					LEFT JOIN '.$table_color.' AS col1 ON '.$result2['mod_color'].' = col1.id AND col1.type = 0
					LEFT JOIN '.$table_color.' AS col2 ON '.$result2['fib_color'].' = col2.id AND col2.type = 1
					LIMIT 1';
			//print_r('<pre>'.$sql_color.'</pre>');
			$result_color=pg_fetch_assoc(pg_query($sql_color));
	
			//$sql_c="SELECT * FROM ".$table_pq." AS p1 WHERE p1.id = ".$result2['curr_pq_id'];
			$sql_c="SELECT * FROM ".$table_pq." AS p1, ".$table_pq_type." AS pt WHERE p1.id = ".$result2['curr_pq_id']." AND p1.pq_type_id = pt.id";
	
			$result_c=pg_fetch_assoc(pg_query($sql_c));
			$sql_cc="SELECT * FROM ".$table_cruz_conn." AS cc1 WHERE cc1.fiber_id = ".$result2['id']." AND cc1.pq_id = ".$result2['curr_pq_id']."";
			$result_cc=pg_fetch_assoc(pg_query($sql_cc));
	
			$num='';
			if($result_c['type']==0) $type='Кросс'; else $type='Муфта';
			if(isset($result_c['num'])) $num.='№'.$result_c['num']; else $num.='';
			if(!$num) $num = '№1';
	
			$text='
			<div class="show_find_pq">
			<div class="show_find_pq_title'.($result2['incorrect']==true?' bg-color-orangeDark':'').'">
			<a class="isoc '.($result2['incorrect']==true?' bg-color-orangeDark':'').'" href="?act=s_pq&node_id='.$result2['curr_node_id'].'" target="_blank">'.$result2['curr_node_addr'].'</a>
			</div>
			<div class="show_find_pq_legend">'.$type.':</div><div class="show_find_pq_fib"><a class="isoc" href="?act=s_cable&pq_id='.$result2['curr_pq_id'].'" target="_blank">&nbsp;'.$num.'&nbsp;</a></div>
			<div class="show_find_pq_legend border_top">ОВ:</div><div class="show_find_pq_fib border_top">'.$result2['num'].'</div>
			<div class="show_find_pq_legend border_top">цвет:</div>
			<div class="show_find_pq_fib border_top">
					<div class="show_find_pq_mod_color w30" title="Цвет модуля: '.($result_color['mod_name']?$result_color['mod_name']:'не задан').'" style="background-color: '.$result_color['mod_color'].'">'.($result_color['mod_stroke']?'/':'&nbsp;').'</div>
					<div class="show_find_pq_fib_color w30" title="Цвет волокна: '.($result_color['fib_name']?$result_color['fib_name']:'не задан').'" style="background-color: '.$result_color['fib_color'].'">'.($result_color['fib_stroke']?'/':'&nbsp;').'</div>
					<div class="clear"></div>
			</div>
			<div class="show_find_pq_legend border_top">Порт:</div><div class="show_find_pq_fib border_top">'.$result_cc['port'].'</div>';
			if($result_cc['descrip']) $text.='<div class="show_find_pq_descrip border_top">'.$result_cc['descrip'].'</div>';
			$text.='</div>';
			echo $text;
		} else {
			$result2 = pg_query($sql2);
			if (pg_num_rows($result2)) {
				while ($row2 = pg_fetch_assoc($result2)) {
					$array_line[] = $row2['the_geom'];
					// занесение координат узла в массив, если шос
					$sql_cc="SELECT *, ST_AsGeoJSON(n1.the_geom) AS the_geom FROM ".$table_cruz_conn." AS cc1, ".$table_node." AS n1, ".$table_pq." AS p1 WHERE cc1.fiber_id = ".$row2['id']." AND cc1.pq_id = ".$row2['curr_pq_id']." AND p1.node = n1.id AND cc1.pq_id = p1.id";
					$result_cc=pg_fetch_assoc(pg_query($sql_cc));
					if(isset($result_cc['port'])) $array_point[] = $result_cc['the_geom'];
				}
			}
		}
	}
	//die;
}

function fib_find_used($id,$last_id,$to_node_id,$pq_id) {
	//echo ' 1: '.$id.' 2: '.$last_id.' 3: '.$to_node_id.'<br>';
	echo ' id: '.$id.' last_id: '.$last_id.' to_node: '.$to_node_id.' pq: '.$pq_id.'<br>';
	//die;
	global $table_cruz_conn;
	global $table_fiber_conn;
	global $table_fiber;
	global $table_cable;
	global $table_pq;
	global $table_pq_type;
	global $table_node;

	$sql='
	SELECT
	CASE WHEN fc1.fiber_id_1='.$id.' THEN fc1.fiber_id_1 ELSE CASE WHEN fc1.fiber_id_2='.$id.' THEN fc1.fiber_id_1 ELSE NULL END END AS id,
	CASE WHEN fc1.fiber_id_1='.$id.' THEN fc1.fiber_id_2 ELSE CASE WHEN fc1.fiber_id_2='.$id.' THEN fc1.fiber_id_2 ELSE NULL END END AS to_id,
	fc1.node_id AS to_node_id

	FROM '.$table_fiber_conn.' AS fc1
	WHERE
	( ( fc1.fiber_id_1 = '.$id.' AND fc1.fiber_id_2 != '.$last_id.' ) OR ( fc1.fiber_id_2 = '.$id.' AND fc1.fiber_id_1 != '.$last_id.' ) )
	';

	//echo 'last_id: '.$last_id.' id: '.$id.'<br>';
	echo '<pre>';
	print_r($sql);
	echo '</pre>';
	$result=@pg_fetch_assoc(pg_query($sql));
	if($result) {
		echo '<pre>';
		print_r($result);
		echo '</pre>';
		echo fib_find_used($result['to_id'],$result['id'],$result['to_node_id'],$result['curr_pq_id']);
	} else {
		//$sql_c="SELECT * FROM ".$table_pq." AS p1, ".$table_pq_type." AS pt WHERE p1.id = ".$result2['curr_pq_id']." AND p1.pq_type_id = pt.id";

		//=pg_fetch_assoc(pg_query($sql_c));
		$sql="SELECT * FROM
		".$table_cruz_conn." AS cc1,
		".$table_pq." AS p1,
		".$table_node." AS n1
		WHERE
		cc1.fiber_id = ".$id.'
		AND p1.id = cc1.pq_id
		AND n1.id = p1.node';
		
		$result=pg_fetch_assoc(pg_query($sql));
		echo '<pre>';
		print_r($result);
		//print_r($sql);
		echo '</pre>';

		if($result['type']==0) $type='Кросс'; else $type='Муфта';
		if(isset($result['num'])) $num.='№'.$result['num']; else $num.='';
		if(!$num) $num = '№1';

		$text='
		<div class="left'.($result['incorrect']==true?' bg-color-orangeDark':'').'"><a class="isoc '.($result['incorrect']==true?' bg-color-orangeDark':'').'" href="?act=s_pq&node_id='.$result['node'].'" target="_blank">&nbsp;'.$result['address'].'</a>&nbsp;</div>
		<div class="left">&nbsp;'.$type.':</div><div class="left">&nbsp;<a class="isoc" href="?act=s_cable&pq_id='.$result['pq_id'].'" target="_blank">&nbsp;'.$num.'&nbsp;</a></div>
		<div class="left">&nbsp;ОВ:</div><div class="left">&nbsp;'.$result['num'].'&nbsp;</div>
		<div class="left '.($result['used']?'bg-color-green':'').'">&nbsp;Порт:&nbsp;</div><div class="left">&nbsp;'.$result['port'].'&nbsp;</div>';
		if($result['descrip']) $text.='<div class="left">'.$result['descrip'].'</div>';
		echo $text;
	}
	//die;
}

// Перенос узлов из node_new в node begin -------------------------------------------------------------------------------------------------------
if(isset($_GET['act']) && $_GET['act']=='move') {
	$sql = "SELECT nn1.*
			FROM
				".$table_node_new." AS nn1,
				".$table_node." AS n1
			WHERE
				nn1.node_id = n1.id
			AND
				n1.the_geom IS NULL;";
	//echo $sql;
	$result = pg_query($sql);
	if (pg_num_rows($result)) {
		while ($row = pg_fetch_assoc($result)) {
			pg_query("UPDATE ".$table_node." SET the_geom='".$row['the_geom']."' WHERE id=".$row['node_id']);
			if(pg_result(pg_query("SELECT COUNT(*) FROM ".$table_node." WHERE the_geom='".$row['the_geom']."'"),0)) {
				pg_query("DELETE FROM ".$table_node_new." WHERE node_id=".$row['node_id']);
				echo $row['the_geom'].' move ok<br>';
			}
		}
	}

	$sql = "SELECT c1.id,
				ST_AsText(ST_MakeLine(n1.the_geom,n2.the_geom)) AS line_text,
				ST_MakeLine(n1.the_geom,n2.the_geom) AS line
			FROM
				".$table_cable." AS c1,
				".$table_pq." AS p1,
				".$table_pq." AS p2,
				".$table_node." AS n1,
				".$table_node." AS n2
			WHERE
				c1.pq_1 = p1.id
			AND
				c1.pq_2 = p2.id
			AND
				p1.node = n1.id
			AND
				p2.node = n2.id
			AND
				n1.the_geom IS NOT NULL
			AND
				n2.the_geom IS NOT NULL
			AND
				c1.the_geom IS NULL
			;";
	//print_r('<pre>'.$sql.'</pre>');
	//die;
	$result = pg_query($sql);
	if (pg_num_rows($result)) {
		while ($row = pg_fetch_assoc($result)) {
			echo $row['id'].' '.$row['line'].'<br>';
			pg_query("UPDATE ".$table_cable." SET the_geom='".$row['line']."' WHERE id=".$row['id']);
		}
	}
	//echo str_replace('tbody','table',$text);
	die;
}
// Перенос узлов из node_new в node end -------------------------------------------------------------------------------------------------------

// удаление геометрии кабеля div
if(isset($_GET['act']) && $_GET['act']=='d_cable_geom' && @is_numeric($_GET['cable_id'])) {
	$text='
		<div class="span10 m5">&nbsp;Сбросить координаты кабеля до "'.clean($_GET['to_addr']).'"?</div>
		<div class="span2 toolbar m0">
			<button class="icon-checkmark m0" id="in_div" rel="?act=d_cable_geom_sql&id='.clean($_GET['cable_id']).'" title="Ok"></button>
		<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		</div>';
	echo $text;
	die;
}

// удаление геометрии кабеля
if(isset($_GET['act']) && $_GET['act']=='d_cable_geom_sql' && @is_numeric($_GET['id']) ) {

	$data_old=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_cable." WHERE id = ".$_GET['id'] ));
	$result = serialize($data_old);
	add_log($table_cable,$_GET['id'],$result,$user_id);

	cable_geom(clean($_GET['id']),true);
	echo "reload";
	die;
}

if(isset($_GET['act']) && $_GET['act']=='get_json' ) {
	if(isset($_GET['street'])) {
		$sql = "SELECT
					s1.id,
					c1.name AS city_name,
					a1.name AS area_name,
					s1.name AS street_name
				FROM
					".$table_city." AS c1,
					".$table_area." AS a1,
					".$table_street_name." AS s1
				WHERE
					a1.city_id = c1.id
				AND
					s1.area_id = a1.id
				;";
		$streetArray = array();
		$result = pg_query($sql);
		$i=0;
		if (pg_num_rows($result)) {
			while ($row = pg_fetch_assoc($result)) {
				@array_push($streetArray[$row['city_name'].', '.$row['area_name'].', '.$row['street_name']]=$row['street_name']);
				//@array_push($streetArray[$row['city_name'].', '.$row['street_name']]=$row['street_name']);
				//echo $row['id'].':'.$row['city_name'].':'.$row['area_name'].':'.$row['street_name'].'<br>';
			}
		}
		header('Content-Type: application/json');
		echo json_encode($streetArray);
	}
	
	if(isset($_GET['street2'])) {
		$sql = "SELECT
					s1.id,
					c1.name AS city_name,
					a1.name AS area_name,
					s1.name AS street_name
				FROM
					".$table_city." AS c1,
					".$table_area." AS a1,
					".$table_street_name." AS s1
				WHERE
					a1.city_id = c1.id
				AND
					s1.area_id = a1.id
				;";
		$streetArray = array();
		$result = pg_query($sql);
		$i=0;
		if (pg_num_rows($result)) {
			while ($row = pg_fetch_assoc($result)) {
				//@array_push($streetArray[$row['city_name'].', '.$row['area_name'].', '.$row['street_name']]=$row['street_name']);
				@array_push($streetArray[$row['city_name'].', '.$row['street_name']]=$row['street_name']);
				//echo $row['id'].':'.$row['city_name'].':'.$row['area_name'].':'.$row['street_name'].'<br>';
			}
		}
		header('Content-Type: application/json');
		echo json_encode($streetArray);
	}
	die;
}

// инфа для asp
if(@$_GET['Gaoj7Quo']=='te4aiK2s') {
	//if($_GET['Gaoj7Quo'] == 'te4aiK2s' && isset($_GET['street_id']) && isset($_GET['num'])) {
	$sql = "SELECT
					n.loc_text,
					k.num,
					lt.name,
					lt.tel,
					lt.descrip
				FROM
					".$table_street_name." AS sn,
					".$table_street_num." AS s_num,
					".$table_node." AS n
				LEFT JOIN ".$table_keys." AS k ON n.id = k.node_id
				LEFT JOIN ".$table_lift." AS l ON n.id = l.node_id
				LEFT JOIN ".$table_lift_type." AS lt ON l.lift_id = lt.id
				WHERE
					n.type !=1
				AND
					sn.id = n.street_id
				AND
					n.street_num_id = s_num.id
				AND
					sn.street_id = ".clean($_GET['street_id'])."
				AND
					lower(s_num.num) = lower('".clean($_GET['num'])."')";
	if(isset($_GET['debug'])) {
		echo '<pre>';
		print_r($sql);
		echo '</pre>';
	}
	$infoArray = array();
	$result = pg_query($sql);
	$i=0;
	if (pg_num_rows($result)) {
		while ($row = pg_fetch_assoc($result)) {
			@array_push($infoArray[$i]['loc_text'] = $row['loc_text']);
			@array_push($infoArray[$i]['num'] = $row['num']);
			@array_push($infoArray[$i]['name'] = $row['name']);
			@array_push($infoArray[$i]['tel'] = $row['tel']);
			@array_push($infoArray[$i]['descrip'] = $row['descrip']);
			$i++;
		}
	}
	
	if(isset($_GET['debug'])) {
		echo '<pre>';
		 print_r($infoArray);
		echo '</pre>';
	} else {
		header('Content-Type: application/json');
		echo json_encode($infoArray);
		/*echo '<pre>';
		print_r($infoArray);
		echo '</pre>';*/
	}
	die;
}

	echo "Нифига не работает\n";
	echo '<pre>';
	print_r($_REQUEST);
	echo '</pre>';
?>
