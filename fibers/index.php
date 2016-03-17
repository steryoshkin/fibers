<?php
$t=microtime(1);

include_once ('./engine/setup.php');
include_once ('./engine/db.php');
include_once ('./engine/parse_html.php');

$user_id=$_SESSION['logged_user_fibers_id'];

if (empty($_SESSION['logged_user_fibers']) && $_SERVER['REQUEST_URI'] != $login_page && empty($_GET['ref'])) {
    header("Location: ".$login_page.'&ref='.base64_encode($_SERVER["REQUEST_URI"]) );
}

$text = '';

// функция вывода меню
function show_menu() {
	global $t;
    global $title;
    global $action;
    global $content;
    global $menu;
    global $group_access;
    global $user_id;
    global $table_user;

    if($menu=='')
    $menu='
    <div class="horizontal-menu bg-color-blueLight">
        <ul>
            <li><a class="icon-home" href="/fibers"></a></li>
            <li><a href="?act=s_node">Узлы</a></li>
    		
    		'.($group_access['key']?'<li><a href="?act=dirs&dir=keys">Ключи</a></li>':'').
            ($group_access['dirs']?'<li class="sub-menu"><a href="#">Справочники</a>
                <ul class="text-left">
                    <li class="sub-menu"><a href="#">Адреса</a>
                        <ul class="text-left">
                        	<li><a href="?act=dirs&dir=region">Область	</a></li>
                        	<li><a href="?act=dirs&dir=city">Город/посёлок</a></li>
                            <li><a href="?act=dirs&dir=area">Район</a></li>
                            <li><a href="?act=dirs&dir=street">Улица</a></li>
                            <li><a href="?act=dirs&dir=location">Размещение</a></li>
                            <li><a href="?act=dirs&dir=room">Помещение</a></li>
                            <li><a href="?act=dirs&dir=keys">Ключи</a></li>
                            <li><a href="?act=dirs&dir=lift">Лифтёрки</a></li>
                        </ul>
                    </li>
                    <li class="sub-menu"><a href="#">Пассивное оборудование</a>
                        <ul class="text-left">
                            <li><a href="?act=dirs&dir=pq_type">Кроссы/Муфты</a></li>
                            <li><a href="?act=dirs&dir=cable_type">Кабели</a></li>
                        </ul>
                    </li>
                    <li class="sub-menu"><a href="#">Активное оборудование</a>
                        <ul class="text-left">
                            <li><a href="?act=dirs&dir=switch_type">Типы коммутаторов</a></li>
                            <li><a href="?act=dirs&dir=mc_type">Типы медиаконвертеров</a></li>
                        </ul>
                    </li>
            		<li><a href="?act=dirs&dir=node_type">Типы узлов</a></li>
                    <li class="sub-menu"><a href="#">Прочее оборудование</a>
                        <ul class="text-left">
                            <li><a href="?act=dirs&dir=box_type">Рамы/Ящики</a></li>
                            <li><a href="?act=dirs&dir=ups_type">ИБП</a></li>
                            <li><a href="?act=dirs&dir=other_type">Разное</a></li>
                        </ul>
                    </li>
    				<li class="sub-menu"><a href="#">Цветокодирование</a>
                        <ul class="text-left">
                            <li><a href="?act=dirs&dir=mod_color">Модули</a></li>
                            <li><a href="?act=dirs&dir=fib_color">Волокна</a></li>
                        </ul>
                    </li>
    				'.($group_access['dirs_users']?'<li><a href="?act=dirs&dir=users">Пользователи</a></li>':'').'
                </ul>
            </li>':'').
            ($group_access['u_const']?'<li><a href="?act=u_const">Узлы в строительстве</a></li>':'').'
			<li><a href="/fibers/geomap.php" target="_blank">Карта сети</a></li>
			<li><a class="icon-locked" href="?logout">['.$_SESSION['user'].']</a></li>
			<li><a href="?act=settings">Настройки</a></li>
		</ul>
    </div>
    <br>
    <br>
	'.(pg_result(pg_query("SELECT new_pass FROM ".$table_user." WHERE id=".$user_id),0)!='t' && $_GET['act']!='settings'?'<script>alertify.alert("Необходимо изменить пароль!<br>Зайдите в настройки с верху в меню");</script>':'').'
    ';
    $text='
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>'.$title.'</title>
    <link href="css/modern.css" rel="stylesheet">
    <link href="css/modern-responsive.css" rel="stylesheet">
    <link href="css/site.css" rel="stylesheet" type="text/css">
    <link href="css/style_2.css" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="js/themes/alertify.core.css" />
	<link rel="stylesheet" href="js/themes/alertify.default.css" id="toggleCSS" />

    <script type="text/javascript" src="js/lib/jquery-1.7.1-min.js"></script>
    <!--<script type="text/javascript" src="js/jquery-1.7.2-min.js"></script>-->
    <script type="text/javascript" src="js/action.js"></script>

	<link rel="stylesheet" media="screen" type="text/css" href="css/colorpicker.css" />
	<script type="text/javascript" src="js/colorpicker.js"></script>
	<script type="text/javascript" src="js/eye.js"></script>
    <script type="text/javascript" src="js/utils.js"></script>
    <script type="text/javascript" src="js/layout.js?ver=1.0.2"></script>
    <script type="text/javascript" src="js/alertify.min.js"></script>

    <script type="text/javascript" src="js/jquery.form.min.js"></script>
</head>
<body class="modern-ui">
    <div class="in_page">
	    <div class="page">
	        <div class="bg-color-blueLight fg-color-white text-center">
	            '.$menu.'
	        </div>
	        <div id="action2" class="bg-color-blueLight fg-color-white text-center">
	            <div class="grid">
	                <div class="row">
	                    <div id="action">'.$action.'</div>
	                </div>
	            </div>
	        </div>
		</div>
    </div>
    <div class="page">
        <div class="page-region" id="page-region">
            '.$content.'
			<div style="display2:none;">'.(microtime(1)-$t).'
        </div>
    </div>
</body>
</html>
    ';
    echo $text;
    //echo "<p>".(microtime(1)-$t)."</p>";
}

$link_node = '<a href="?act=s_node">Узлы</a>';

if (isset($_GET['act']) && $_GET['act'] == 'login') {
    $title='Логинься давай';
    $menu='<h1>Авторизация</h1>';
    $content='
        <form method="post" action="auth.php">
        <div class="grid">
            <div class="row">
                <div class="span3">Логин:</div>
                <div class="span1">Пароль:</div>
            </div>
            <div class="row">
                <div class="span3 input-control text"><input type="text" name="user" /></div>
                <div class="span3 input-control password"><input type="password" name="password"/></div>
                <div class="span1"><input type="submit" value="вход" name="act"></div>
            </div>
        </div>
		<input type="hidden" name="ref" value="'.$_GET['ref'].'">
        </form>
    ';
    show_menu();
    die;
}

// вывод списка узлов
if (isset($_GET['act']) && $_GET['act'] == 's_node' && $group_access['node']) {
    $sql = "SELECT DISTINCT(LEFT(name, 1)) AS name FROM " . $table_street_name." ORDER BY name";
    $result = pg_query($sql);
    $find_abc='';
    if (pg_num_rows($result)) {
        while ($row = pg_fetch_assoc($result)) {
        	$find_node_get=(@isset($_GET['find_node'])?$_GET['find_node']:"");
            $find_abc .= '<div class="b_m">'.($row['name'].'*'==$find_node_get?'<div class="b_m">'.$row['name'].'</div>':'<a class="b_m_a" href="?act=s_node&find_node='.$row['name'].'*'.(@is_numeric($_GET['area_id'])?'&area_id='.$_GET['area_id']:'').(isset($_GET['node_type_id'])?'&node_type_id='.clean($_GET['node_type_id']):'').'">'.$row['name'].'</a>').'</div>';
        	//$find_abc .= '<div class="b_m"><a class="b_m_a" href="?act=s_node&find_node='.$row['name'].'%'.(is_numeric($_GET['area_id'])?'&area_id='.$_GET['area_id']:'').'">'.$row['name'].'</a></div>';
        }
        $find_abc .= '<div class="b_m"><a class="b_m_a" href="?act=s_node'.(@is_numeric($_GET['area_id'])?'&area_id='.$_GET['area_id']:'').(isset($_GET['node_type_id'])?'&node_type_id='.clean($_GET['node_type_id']):'').'">Все</a></div>';
    }
    $i = 1;
    //$link = '<div class="title">Узлы</div>';
    $title='Узлы';
    $action='';
    if ($group_access['node_add'])
    	$action.='<div class="span2 m0 text-left"><button class="m0" id="in_div" rel="?act=n_node" />Добавить узел</button></div>';
        //$action.='<div class="span2 m0 text-left"><button class="m0" id="node_add_div" rel="?act=n_node" />Добавить узел</button></div>';
    $action.='<div class="span3 m0 text-left input-control text"><input class="" id="find_node" type="text" onchange="javascript: window.location=\'?act=s_node&find_node=*\'+$(\'input#find_node\').val()+\'*'.(@is_numeric($_GET['area_id'])?'&area_id='.$_GET['area_id']:'').(isset($_GET['node_type_id'])?'&node_type_id='.clean($_GET['node_type_id']):'').'\';" placeholder="Введите для поиска" /></div>';
    $action.='<div class="span1 m0 text-left toolbar"><button class="icon-search m0" onClick="javascript: window.location=\'?act=s_node&find_node=*\'+$(\'input#find_node\').val()+\'*'.(@is_numeric($_GET['area_id'])?'&area_id='.$_GET['area_id']:'').(isset($_GET['node_type_id'])?'&node_type_id='.clean($_GET['node_type_id']):'').'\';" /></button></div>';
    
    $sql="SELECT * FROM ".$table_area." ORDER BY id,name";
    $result = pg_query($sql);
    if(pg_num_rows($result)){
    	//$select_area='<select id="select_area" onchange="if($(\'select#select_area\').val()) var area_id=\'&area_id=\'+$(\'select#select_area\').val(); window.location=\'?act=s_node'.(isset($_GET['find_node'])?'&find_node='.clean(($_GET['find_node'])):'').(isnumeric($_GET['page'])?'&page='.clean(($_GET['page'])):'').'+area_id;">';
    	$select_area='<select id="select_area" onChange="var area_id=\'\'; if($(\'select#select_area\').val()) area_id=\'&area_id=\'+$(\'select#select_area\').val(); window.location=\'?act=s_node'.(isset($_GET['find_node'])?'&find_node='.clean($_GET['find_node']):'').(@is_numeric($_GET['page'])?'&page='.clean($_GET['page']):'').(isset($_GET['node_type_id'])?'&node_type_id='.clean($_GET['node_type_id']):'').'\'+area_id;">';
    	$select_area.='<option value="">Все районы</option>';
    	while($row=pg_fetch_assoc($result)){
    		$select_area.='<option value="'.$row['id'].'"';
    		if(@$_GET['area_id']==$row['id']) {
    			$select_area.=" SELECTED";
    		}
    		$select_area.='>'.$row['name'].'</option>';
    	}
    	$select_area.='</select>';
    }
    $action.='<div class="span3 m0 input-control text">'.$select_area.'</div>';
    
    // фильтр по типам узлов
    $sql="SELECT * FROM ".$table_node_type." ORDER BY id,name";
    $result = pg_query($sql);
    if(pg_num_rows($result)){
    	$select_node_type='<select id="select_node_type" onChange="var node_type_id=\'\'; if($(\'select#select_node_type\').val()) node_type_id=\'&node_type_id=\'+$(\'select#select_node_type\').val(); window.location=\'?act=s_node'.(isset($_GET['find_node'])?'&find_node='.clean($_GET['find_node']):'').(@is_numeric($_GET['page'])?'&page='.clean($_GET['page']):'').(isset($_GET['area_id'])?'&area_id='.clean($_GET['area_id']):'').'\'+node_type_id;">';
    	$select_node_type.='<option value="">Все типы узлов</option>';
    	while($row=pg_fetch_assoc($result)){
    		$select_node_type.='<option value="'.$row['id'].'"';
    		if(@$_GET['node_type_id']==$row['id']) {
    			$select_node_type.=" SELECTED";
    		}
    		$select_node_type.='>'.$row['name'].'</option>';
    	}
    	$select_node_type.='</select>';
    }
    $action.='<div class="span2 m0 input-control text">'.$select_node_type.'</div>';

    $find_node='';
    if (isset($_GET['find_node'])) {
    	//$find_node = 'AND s_name.name LIKE "' . clean($_GET['find_node']) . '"';
    	//$find_node = "AND lower(n1.address_full) LIKE lower('".clean($_GET['find_node'])."')";
    	$find_node = "AND lower(n1.address_full) LIKE lower('".str_replace(" ", "%", str_replace("*", "%", clean($_GET['find_node'])))."')";
    }
    
    $sql_count = "SELECT COUNT(*) FROM ".$table_node." AS n1, ".$table_street_name." AS s_name WHERE n1.street_id = s_name.id " . $find_node.(@is_numeric($_GET['area_id'])?" AND s_name.area_id = ".$_GET['area_id']:"").(@is_numeric($_GET['node_type_id'])?" AND n1.node_type_id = ".$_GET['node_type_id']:"");
    $total_rows=pg_fetch_row(pg_query($sql_count));

    //$sql_count_map = "SELECT COUNT(*) FROM ".$table_node." AS n1, ".$table_street_name." AS s_name WHERE n1.street_id = s_name.id AND n1.the_geom IS NULL " . $find_node;
    //$total_rows_map=pg_fetch_row(pg_query($sql_count_map));

    $num_pages=ceil($total_rows[0]/$per_page);

    if(isset($_GET['page'])) $page=($_GET['page']-1); else $page=0;
    $start=abs($page*$per_page);
    $i=$i+$start;
    
    $find='';
    $pages='';
    if(isset($_GET['find_node'])) $find='&find_node='.clean($_GET['find_node']);
    for($a=1;$a<=$num_pages;$a++) {
        if ($a-1 == $page) {
            $pages.='<div class="b_m">'.$a.'</div>';
        } else {
            $pages.='<div class="b_m"><a class="b_m_a" href="?act=s_node'.$find.'&page='.$a.(@is_numeric($_GET['area_id'])?'&area_id='.$_GET['area_id']:'').(isset($_GET['node_type_id'])?'&node_type_id='.clean($_GET['node_type_id']):'').'">'.$a.'</a></div>';
        }
    }
    $pages='<div class="text-center">
		    	<div class="b_m">Страницы:</div>
		    	'.$pages.//'<div class="b_m">всего: '.$total_rows[0].'</div>
		    '</div><br>';

    $action.='<div class="b_m m5">всего узлов: '.$total_rows[0].'</div>';
    $action.='<div class="span12 m5">'.$find_abc.'</div>';

	$sql = "
		SELECT
			n1.*,
			CASE WHEN COUNT(box.id) > 0 THEN true ELSE false END AS box,
			CASE WHEN COUNT(sw.id) > 0 THEN true ELSE false END AS sw,
			s_name.name AS street_name,
			".(@is_numeric($_GET['area_id'])?"s_name.area_id AS area_id,":"").
			"s_num.num AS street_num,
			n1.num_ent,
	        loc.location AS location,
	        room.room AS room,
	        COUNT(p1.id) AS pq_id,
	        /*COUNT(keys.num) AS keys,*/
			keys.num AS key_num,
			ST_X(ST_AsText(n1.the_geom)) AS x,
			ST_Y(ST_AsText(n1.the_geom)) AS y
		FROM
			".$table_street_name." AS s_name,
			".$table_street_num." AS s_num,
			".$table_node." AS n1
		LEFT JOIN ".$table_pq." AS p1 ON n1.id = p1.node
		LEFT JOIN ".$table_location." AS loc ON n1.location_id = loc.id
		LEFT JOIN ".$table_room." AS room ON n1.room_id = room.id
		LEFT JOIN ".$table_keys." AS keys ON keys.node_id = n1.id
		LEFT JOIN ".$table_box." AS box ON box.node_id = n1.id
		LEFT JOIN ".$table_switches." AS sw ON sw.node_id = n1.id
		WHERE
			n1.street_id = s_name.id
		AND
			n1.street_num_id = s_num.id
		".$find_node.
		(@is_numeric($_GET['area_id'])?" AND s_name.area_id = ".$_GET['area_id']:"").
		(@is_numeric($_GET['node_type_id'])?" AND n1.node_type_id = ".$_GET['node_type_id']:"")."
		GROUP BY
			".(@is_numeric($_GET['area_id'])?"s_name.area_id, ":"")."street_name, street_num, location, n1.id, room, key_num
		ORDER BY s_name.name, LENGTH(s_num.num), s_num.num, LENGTH(loc.location)
		LIMIT $per_page OFFSET $start";
/*	if($_SERVER['REMOTE_ADDR']=='192.168.6.12') {
		echo '<pre>';
		print_r($sql);
		echo '</pre>';
	}
*/
    $content='';
    if($num_pages>1) $content=$pages;

    $content.='<table class="striped">';
    $result = pg_query($sql);
    //print_r('<pre>'.$sql.'</pre>');
    if (pg_num_rows($result)) {
		while ($row = pg_fetch_assoc($result)) {
            $content.='<tr>';
            // подсветка для ПТО
            if($_SESSION['group']<=5)
            	$content.='<td class="span1'.($row['key_num']?' bg-color-green':'').'" '.($row['key_num']?'title="Ключ № '.$row['key_num'].'"':'').'><a href="engine/map.php?id='.$row['id'].'" target="_blank">'.$i.'.</a>';
            else
            	$content.='<td class="span1" '.($row['key_num']?'title="Ключ № '.$row['key_num'].'"':'').'>'.$i.'.';
            $content.='</td><td class="span5 ';
            // подсветка для ПТО
            if($_SESSION['group']<=5) $content.=($row['incorrect']==true?' bg-color-orangeDark':(!$row['pq_id']?' bg-color-orange':''));
            $content.='">';
            if($group_access['pq'])
            	$content.='<a id="addr" href="?act=s_pq&node_id='.$row['id'].'" '.($_SESSION['group']==0?'title="'.$row['user_id'].'"':'').'>'.addr($row['street_name'],$row['street_num'],$row['num_ent'],$row['location'],$row['room']).'</a>';
            else
            	$content.=addr($row['street_name'],$row['street_num'],$row['num_ent'],$row['location'],$row['room']);
            $content.='</td>';
            // подсветка статуса заполнения паспорта узла
            $content.='<td class="span1';
			if($row['box']=='f' && $row['sw']=='f' && $group_access['p_node'] && $row['type']==0)
				$content.=' bg-color-orangeDark" title="Паспорт узла не заполнен"';
			else if($row['box']=='t' && $row['sw']=='f' && $group_access['p_node'] && $row['type']==0)
				$content.=' bg-color-blueLight" title="Коммутатор не задан"';
			else if($row['box']=='f' && $row['sw']=='t' && $group_access['p_node'] && $row['type']==0)
				$content.=' bg-color-blue" title="Ящик/рама не заданы"';
            //.$row['box'].'/'.$row['sw'].
            else
            	$content.='" title="Информация занесена"';
            //$content.='>&nbsp;'.$row['box'].'/'.$row['sw'].'</td>';
            //$content.=($group_access['p_node']?' onClick="window.location=\'?act=s_pq&p_node&node_id='.$row['id'].'\';"':'').'>П/У&nbsp;</td>';
            $content.=($group_access['p_node']?' onClick="window.open(\'?act=s_pq&p_node&node_id='.$row['id'].'\');"':'').'>&nbsp;</td>';
            // подсветка статуса заполнения паспорта узла
            $content.='<td class="span5">'.$row['descrip'].'</td>';
			$content.='<td class="span2">';
            if ($group_access['node_edit'])
                $content.='<button class="icon-pencil mini m0" id="pq_e_add_div" rel="?act=e_node&node_id=' . $row['id'] . '" title="Редактировать" /></button>&nbsp;';
            if ($group_access['node_del'])
            	$content.='<button class="icon-cancel-2 mini m0" id="pq_d_add_div" rel="?act=d_node&node_id=' . $row['id'] . '&addr=' . $row['address'] . '" title="Удалить"/></button>&nbsp;';
            if($row['x'] && $row['y'])
            	$content.='<button class="icon-compass mini m0" id="map" rel="lat='.$row['y'].'&lon='.$row['x'].'&marker" title="Показать на карте"/></button>';
                $content.='</td>';
            $content.='</tr>';
            $i++;
        }
    }
    $content.='</table>';
    // Миха просил :)
    if($num_pages>1) $content.=$pages;
    //$content.=$pages.'<br>';
    show_menu();
    die;
}

// вывод списка пассивного оборудования Кроссы/Муфты
//if (isset($_GET['act']) && $_GET['act'] == 's_pq' && ( isset($_GET['o_node']) || isset($_GET['p_node']) ) && is_numeric($_GET['node_id'])) {
if (isset($_GET['act']) && $_GET['act'] == 's_pq' && ( is_numeric($_GET['node_id']) || is_numeric($_GET['pq_id']) ) && $group_access['pq']) {

	if(!isset($_GET['p_node']) && !isset($_GET['o_node'])) {
		$sql="SELECT s1.name, s1.street_id, sn1.num, ST_X(ST_AsText(n1.the_geom)) AS x, ST_Y(ST_AsText(n1.the_geom)) AS y, n1.num_ent FROM " . $table_node . " AS n1, " . $table_street_name . " AS s1, " . $table_street_num . " AS sn1 WHERE n1.street_id = s1.id AND n1.street_num_id = sn1.id AND n1.id=" . clean($_GET['node_id']) . ";";
		$full_addr=pg_fetch_assoc(pg_query($sql));
		$ent=$full_addr['num_ent'];
		//echo '<pre>';
		//print_r($full_addr);
		//print_r($sql);
		//echo '</pre>';
		// определение адреса и прочей фигни для запроса на сайт
		//$street = array(1 => '11-й Гвардейской Армии', 2 => '13-й Микрорайон', 3 => '1-го Мая', 4 => '25 лет Октября', 5 => '375 км', 6 => '40 лет ВЛКСМ', 7 => '40 лет Октября', 8 => '40 лет Победы', 9 => 'Абажурный', 10 => 'Аварийная', 11 => 'Авиаторов', 12 => 'Азотная', 13 => 'Алюминиевая', 14 => 'Андреевский', 15 => 'Анодная', 16 => 'Антоновская', 17 => 'Архитекторов', 18 => 'Бабушкина', 19 => 'Багратиона', 20 => 'Байдаевская', 21 => 'Бакинская', 22 => 'Бардина', 23 => 'Батюшкова', 24 => 'Белана', 25 => 'Белградская', 26 => 'Белорецкая', 27 => 'Библиотечный', 28 => 'Благовещенская', 29 => 'Братьев Гаденовых', 30 => 'Бугарева', 31 => 'Бульварный', 32 => 'Буркацкого', 33 => 'Васильковый', 34 => 'Ватутина', 35 => 'Верхне-Восточная', 36 => 'Верхнее Редаково', 37 => 'Веры Соломиной', 38 => 'Вечерняя', 39 => 'Внутренняя', 40 => 'Водоемная', 41 => 'Вокзальная', 42 => 'Вологодского', 43 => 'Володарского', 44 => 'Воробьева', 45 => 'Воровского', 46 => 'Воронежская', 47 => 'Восточная', 48 => 'Всесторонняя', 49 => 'Выборгская', 50 => 'Герасименко', 51 => 'Герцена', 52 => 'Гидротехническая', 53 => 'Глинки', 54 => 'Гончарова', 55 => 'Горноспасательная', 56 => 'Горьковская', 57 => 'Граневая', 58 => 'Граневой', 59 => 'Грдины', 60 => 'Грибоедова', 61 => 'Дагестанская', 62 => 'Дагестанский', 63 => 'Дачная', 64 => 'Демьяна Бедного', 65 => 'День Шахтера', 66 => 'Дзержинского', 67 => 'Дизельная', 68 => 'ДОЗ', 69 => 'Дозорная', 70 => 'Донецкая', 71 => 'Дорстроевская', 72 => 'Достоевского', 73 => 'Дружбы', 74 => 'Дружинина', 75 => 'Дузенко', 76 => 'Екимова', 77 => 'Ермака', 78 => 'Ермакова', 79 => 'Есенина', 80 => 'Жасминная', 81 => 'Железноводская', 82 => 'Железнодорожная', 83 => 'Запорожская', 84 => 'Запсибовцев', 85 => 'Звездова', 86 => 'Зеленый', 87 => 'Земнухова', 88 => 'Зыряновская', 89 => 'Ижевский', 90 => 'Интернатная', 91 => 'Казарновского', 92 => 'Каирская', 93 => 'Калужская', 94 => 'Камчатская', 95 => 'Кандалепская', 96 => 'Капитальная', 97 => 'Карбышева', 98 => 'Карла Маркса', 99 => 'Керамический', 100 => 'Кирова', 101 => 'Кирпичная', 102 => 'Кисловодская', 103 => 'Климасенко', 104 => 'Клименко', 105 => 'Колыванская', 106 => 'Кондомское шоссе', 107 => 'Конева', 108 => 'Космонавтов', 109 => 'Косыгина', 110 => 'Красилова', 111 => 'Кременчугская', 112 => 'Крупской', 113 => 'Кубинская', 114 => 'Кузбасская', 115 => 'Кузнецкстроевский', 116 => 'Кузнецова', 117 => 'Куйбышева', 118 => 'Кулакова', 119 => 'Курако', 120 => 'Курбатова', 121 => 'Кутузова', 122 => 'Лазо', 123 => 'Латугина', 124 => 'Лебединская', 125 => 'Левашова', 126 => 'Левитана', 127 => 'Ленина', 128 => 'Ленинградская', 129 => 'Лермонтова', 130 => 'Лесная', 131 => 'Лесогорная', 132 => 'Ливинская', 133 => 'Линейная', 134 => 'Линейный', 135 => 'Литейная', 136 => 'Локомотивная', 137 => 'Ломоносова', 138 => 'Луговая', 139 => 'Луначарского', 140 => 'Магаданская', 141 => 'Магнитогорский', 142 => 'Макеевская', 143 => 'Малоэтажная', 144 => 'Малышей', 145 => 'Маркшейдерская', 146 => 'Матросова', 147 => 'Машиностроительная', 148 => 'Металлургов', 149 => 'Метелкина', 150 => 'Мечникова', 151 => 'Мира', 152 => 'Мирная', 153 => 'Мичурина', 154 => 'Молдавская', 155 => 'Молодежная', 156 => 'Мориса Тореза', 157 => 'Мостовая', 158 => 'Мраморная', 159 => 'Мурманская', 160 => 'Народная', 161 => 'Невского', 162 => 'Никитинский', 163 => 'Николая Островского', 164 => 'Николая Руднева', 165 => 'Новаторов', 166 => 'Новобайдаевская', 167 => 'Новогодняя', 168 => 'Новоселов', 169 => 'Ноградская', 170 => 'Норильская', 171 => 'Обнорского', 172 => 'Октябрьский', 173 => 'Олеко Дундича', 174 => 'Олимпийская', 175 => 'Орджоникидзе', 176 => 'Орлова', 177 => 'Осинники', 178 => 'Осьмухина', 179 => 'Отдельная', 180 => 'п/я 100', 181 => 'Павлова', 182 => 'Павловского', 183 => 'Пархоменко', 184 => 'Пасека №1', 185 => 'Первостроителей', 186 => 'Песцовый', 187 => 'Петракова', 188 => 'Пинская', 189 => 'Пионерский', 190 => 'Пирогова', 191 => 'Подъемная', 192 => 'Покрышкина', 193 => 'Полосухина', 194 => 'Попова', 195 => 'Поссоветская', 196 => 'Пржевальского', 197 => 'Прибрежная', 198 => 'Привольная', 199 => 'Промышленная', 200 => 'Пушкина', 201 => 'Радищева', 202 => 'Разведчиков', 203 => 'Резервный', 204 => 'Рихарда Зорге', 205 => 'Рожковой', 206 => 'Рокоссовского', 207 => 'Ростовская', 208 => 'Рубцовская', 209 => 'Рыночный', 210 => 'Садгородская', 211 => 'Садопарковая', 212 => 'Салтыкова-Щедрина', 213 => 'Свердлова', 214 => 'Севастопольская', 215 => 'Серпуховская', 216 => 'Сеченова', 217 => 'Сибиряков Гвардейцев', 218 => 'Симферопольская', 219 => 'Скоростная', 220 => 'Слесарная', 221 => 'Смирнова', 222 => 'Советской Армии', 223 => 'Спартака', 224 => 'Спасская', 225 => 'Спортивная', 226 => 'Строителей', 227 => 'Суворова', 228 => 'Суданская', 229 => 'Сумского', 230 => 'Сурикова', 231 => 'Сусанина', 232 => 'Сызранская', 233 => 'Теш Лог', 234 => 'Тихоокеанская', 235 => 'Толбухина', 236 => 'Толмачева', 237 => 'Тольятти', 238 => 'Топографический', 239 => 'Транспортная', 240 => 'Трестовский', 241 => 'Тузовского', 242 => 'Тульская', 243 => 'Тульский', 244 => 'Тушинская', 245 => 'Угольная', 246 => 'Ударников', 247 => 'Ульяны Громовой', 248 => 'Урюпинский', 249 => 'Успенская', 250 => 'Учительская', 251 => 'Ушинского', 252 => 'Уютная', 253 => 'Фестивальная', 254 => 'Филиппова', 255 => 'Франкфурта', 256 => 'Фурманова', 257 => 'Хитарова', 258 => 'Цветочный', 259 => 'Циолковского', 260 => 'Чекалина', 261 => 'Чекистов', 262 => 'Челюскина', 263 => 'Черемнова', 264 => 'Черноморская', 265 => 'Чернышова', 266 => 'Черняховского', 267 => 'Читинский', 268 => 'Шахтеров', 269 => 'Шахтостроительный', 270 => 'Шестакова', 271 => 'Школьный', 272 => 'Шолохова', 273 => 'Шорский', 274 => 'Шункова', 275 => 'Шушталепская', 276 => 'Щедрухинский', 277 => 'Экскаваторная', 278 => 'Электролизная', 279 => 'Энтузиастов', 280 => 'Юбилейная', 281 => 'Южная', 282 => 'Ярославская', 283 => 'Ясная', 284 => 'Вокзальная ст.Ерунаково');
		$street = array(1 => '11-й Гвардейской Армии', 2 => '13-й Микрорайон', 3 => '1-го Мая', 4 => '25 лет Октября', 5 => '375 км', 6 => '40 лет ВЛКСМ', 7 => '40 лет Октября', 8 => '40 лет Победы', 9 => 'Абажурный', 10 => 'Аварийная', 11 => 'Авиаторов', 12 => 'Азотная', 13 => 'Алюминиевая', 14 => 'Андреевский', 15 => 'Анодная', 16 => 'Антоновская', 17 => 'Архитекторов', 18 => 'Бабушкина', 19 => 'Багратиона', 20 => 'Байдаевская', 21 => 'Бакинская', 22 => 'Бардина', 23 => 'Батюшкова', 24 => 'Белана', 25 => 'Белградская', 26 => 'Белорецкая', 27 => 'Библиотечный', 28 => 'Благовещенская', 29 => 'Братьев Гаденовых', 30 => 'Бугарева', 31 => 'Бульварный', 32 => 'Буркацкого', 33 => 'Васильковый', 34 => 'Ватутина', 35 => 'Верхне-Восточная', 36 => 'Верхнее Редаково', 37 => 'Веры Соломиной', 38 => 'Вечерняя', 39 => 'Внутренняя', 40 => 'Водоемная', 41 => 'Вокзальная', 42 => 'Вологодского', 43 => 'Володарского', 44 => 'Воробьева', 45 => 'Воровского', 46 => 'Воронежская', 47 => 'Восточная', 48 => 'Всесторонняя', 49 => 'Выборгская', 50 => 'Герасименко', 51 => 'Герцена', 52 => 'Гидротехническая', 53 => 'Глинки', 54 => 'Гончарова', 55 => 'Горноспасательная', 56 => 'Горьковская', 57 => 'Граневая', 58 => 'Граневой', 59 => 'Грдины', 60 => 'Грибоедова', 61 => 'Дагестанская', 62 => 'Дагестанский', 63 => 'Дачная', 64 => 'Демьяна Бедного', 65 => 'День Шахтера', 66 => 'Дзержинского', 67 => 'Дизельная', 68 => 'ДОЗ', 69 => 'Дозорная', 70 => 'Донецкая', 71 => 'Дорстроевская', 72 => 'Достоевского', 73 => 'Дружбы', 74 => 'Дружинина', 75 => 'Дузенко', 76 => 'Екимова', 77 => 'Ермака', 78 => 'Ермакова', 79 => 'Есенина', 80 => 'Жасминная', 81 => 'Железноводская', 82 => 'Железнодорожная', 83 => 'Запорожская', 84 => 'Запсибовцев', 85 => 'Звездова', 86 => 'Зеленый', 87 => 'Земнухова', 88 => 'Зыряновская', 89 => 'Ижевский', 90 => 'Интернатная', 91 => 'Казарновского', 92 => 'Каирская', 93 => 'Калужская', 94 => 'Камчатская', 95 => 'Кандалепская', 96 => 'Капитальная', 97 => 'Карбышева', 98 => 'Карла Маркса', 99 => 'Керамический', 100 => 'Кирова', 101 => 'Кирпичная', 102 => 'Кисловодская', 103 => 'Климасенко', 104 => 'Клименко', 105 => 'Колыванская', 106 => 'Кондомское шоссе', 107 => 'Конева', 108 => 'Космонавтов', 109 => 'Косыгина', 110 => 'Красилова', 111 => 'Кременчугская', 112 => 'Крупской', 113 => 'Кубинская', 114 => 'Кузбасская', 115 => 'Кузнецкстроевский', 116 => 'Кузнецова', 117 => 'Куйбышева', 118 => 'Кулакова', 119 => 'Курако', 120 => 'Курбатова', 121 => 'Кутузова', 122 => 'Лазо', 123 => 'Латугина', 124 => 'Лебединская', 125 => 'Левашова', 126 => 'Левитана', 127 => 'Ленина', 128 => 'Ленинградская', 129 => 'Лермонтова', 130 => 'Лесная', 131 => 'Лесогорная', 132 => 'Ливинская', 133 => 'Линейная', 134 => 'Линейный', 135 => 'Литейная', 136 => 'Локомотивная', 137 => 'Ломоносова', 138 => 'Луговая', 139 => 'Луначарского', 140 => 'Магаданская', 141 => 'Магнитогорский', 142 => 'Макеевская', 143 => 'Малоэтажная', 144 => 'Малышей', 145 => 'Маркшейдерская', 146 => 'Матросова', 147 => 'Машиностроительная', 148 => 'Металлургов', 149 => 'Метелкина', 150 => 'Мечникова', 151 => 'Мира', 152 => 'Мирная', 153 => 'Мичурина', 154 => 'Молдавская', 155 => 'Молодежная', 156 => 'Мориса Тореза', 157 => 'Мостовая', 158 => 'Мраморная', 159 => 'Мурманская', 160 => 'Народная', 161 => 'Невского', 162 => 'Никитинский', 163 => 'Николая Островского', 164 => 'Николая Руднева', 165 => 'Новаторов', 166 => 'Новобайдаевская', 167 => 'Новогодняя', 168 => 'Новоселов', 169 => 'Ноградская', 170 => 'Норильская', 171 => 'Обнорского', 172 => 'Октябрьский', 173 => 'Олеко Дундича', 174 => 'Олимпийская', 175 => 'Орджоникидзе', 176 => 'Орлова', 177 => 'Осинники', 178 => 'Осьмухина', 179 => 'Отдельная', 180 => 'п/я 100', 181 => 'Павлова', 182 => 'Павловского', 183 => 'Пархоменко', 184 => 'Пасека №1', 185 => 'Первостроителей', 186 => 'Песцовый', 187 => 'Петракова', 188 => 'Пинская', 189 => 'Пионерский', 190 => 'Пирогова', 191 => 'Подъемная', 192 => 'Покрышкина', 193 => 'Полосухина', 194 => 'Попова', 195 => 'Поссоветская', 196 => 'Пржевальского', 197 => 'Прибрежная', 198 => 'Привольная', 199 => 'Промышленная', 200 => 'Пушкина', 201 => 'Радищева', 202 => 'Разведчиков', 203 => 'Резервный', 204 => 'Рихарда Зорге', 205 => 'Рожковой', 206 => 'Рокоссовского', 207 => 'Ростовская', 208 => 'Рубцовская', 209 => 'Рыночный', 210 => 'Садгородская', 211 => 'Садопарковая', 212 => 'Салтыкова-Щедрина', 213 => 'Свердлова', 214 => 'Севастопольская', 215 => 'Серпуховская', 216 => 'Сеченова', 217 => 'Сибиряков Гвардейцев', 218 => 'Симферопольская', 219 => 'Скоростная', 220 => 'Слесарная', 221 => 'Смирнова', 222 => 'Советской Армии', 223 => 'Спартака', 224 => 'Спасская', 225 => 'Спортивная', 226 => 'Строителей', 227 => 'Суворова', 228 => 'Суданская', 229 => 'Сумского', 230 => 'Сурикова', 231 => 'Сусанина', 232 => 'Сызранская', 233 => 'Теш Лог', 234 => 'Тихоокеанская', 235 => 'Толбухина', 236 => 'Толмачева', 237 => 'Тольятти', 238 => 'Топографический', 239 => 'Транспортная', 240 => 'Трестовский', 241 => 'Тузовского', 242 => 'Тульская', 243 => 'Тульский', 244 => 'Тушинская', 245 => 'Угольная', 246 => 'Ударников', 247 => 'Ульяны Громовой', 248 => 'Урюпинский', 249 => 'Успенская', 250 => 'Учительская', 251 => 'Ушинского', 252 => 'Уютная', 253 => 'Фестивальная', 254 => 'Филиппова', 255 => 'Франкфурта', 256 => 'Фурманова', 257 => 'Хитарова', 258 => 'Цветочный', 259 => 'Циолковского', 260 => 'Чекалина', 261 => 'Чекистов', 262 => 'Челюскина', 263 => 'Черемнова', 264 => 'Черноморская', 265 => 'Чернышова', 266 => 'Черняховского', 267 => 'Читинский', 268 => 'Шахтеров', 269 => 'Шахтостроительный', 270 => 'Шестакова', 271 => 'Школьный', 272 => 'Шолохова', 273 => 'Шорский', 274 => 'Шункова', 275 => 'Шушталепская', 276 => 'Щедрухинский', 277 => 'Экскаваторная', 278 => 'Электролизная', 279 => 'Энтузиастов', 280 => 'Юбилейная', 281 => 'Южная', 282 => 'Ярославская', 283 => 'Ясная', 284 => 'Вокзальная ст.Ерунаково', 285 => 'п.Чистогорский');
		mb_internal_encoding("UTF-8");
		$inp_addr=strtolower($full_addr['name']);
		foreach ( $street as $key => $value ) {
			$inp_addr=str_replace(' ', '.*', $inp_addr);
			if(preg_match("/.*$inp_addr.*/i", strtolower($value))) {
				break;
			}
		}

	$content='
		<div class="horizontal-menu">
			<input id="full_addr_name" type="hidden" value="'.$key.'">
			<input id="street_id" type="hidden" value="'.$full_addr['street_id'].'">
			<input id="full_addr_num" type="hidden" value="'.$full_addr['num'].'">
            <ul class="m0">'.
				($group_access['o_node']?'<li><a href="?act=s_pq&o_node&node_id='.clean($_GET['node_id']).'">Узел</a></li>':'').
				($group_access['p_node']?'<li><a href="?act=s_pq&p_node&node_id='.clean($_GET['node_id']).'">Паспорт узла</a></li>':'').
				'<li><a id="map" rel="lat='.$full_addr['y'].'&lon='.$full_addr['x'].'&marker" href="#" target="_blank"/>Показать на карте</a></li>
			</ul>
        </div>
        <div id="content"></div>';
	}

	//$address = pg_result(pg_query("SELECT address_full FROM " . $table_node . " WHERE id=" . clean($_GET['node_id']) . ";"), 0);
	$address=(isset($_GET['node_id'])?addr_id_full(clean($_GET['node_id'])):"");
	
    //$address=addr_id(clean($_GET['node_id']));
    $title = 'Узел: '.$address;
    $action='<li>
                <a href="?act=s_pq&node_id='.clean($_GET['node_id']).'">'.$address.'</a>
                    <ul>'.
                        ($group_access['o_node']?'<li><a href="?act=s_pq&o_node&node_id='.clean($_GET['node_id']).'">Узел</a></li>':'').
                        ($group_access['p_node']?'<li><a href="?act=s_pq&p_node&node_id='.clean($_GET['node_id']).'">Паспорт узла</a></li>':'')
                    .'</ul>
            </li>
            <!-- <li'.(pg_result(pg_query("SELECT incorrect FROM ".$table_node." WHERE id =".clean($_GET['node_id'])),0)==true?' class="bg-color-orangeDark"':'').'><a href="#">Проблема</a></li>-->';
    
    if(isset($_GET['o_node']) && $group_access['o_node']) {
        $i=1;
        $o=1;
        /*if($_SERVER['REMOTE_ADDR']=='192.168.6.12') {
	        echo '<pre>';
	        print_r($group_access);
	        echo '</pre>';
        }*/
        $action.=($group_access['o_node_add']?'<li><a id="pq_into_div" href="/" rel="?act=n_pq&node_id=' . clean($_GET['node_id']) .($group_access['prompt']?'&prompt=1':'').'"/>Добавить кросс/муфту</a></li>':'');
	    $content='<table class="striped">';
	    
	    $sql="SELECT pq.id AS id, pq_t.type , pq.num, pq.descrip AS pq_descrip, pq_t.descrip AS pq_type_descrip, pq_t.name
			    FROM ".$table_node." AS node, ".$table_pq." AS pq
			    LEFT JOIN ".$table_pq_type." AS pq_t ON pq.pq_type_id = pq_t.id
			    WHERE pq.node=node.id AND pq.node=".clean($_GET['node_id'])."
			    ORDER BY pq.node, pq.num";
	    //print_r($sql);
	    $result = pg_query($sql);
	    if (pg_num_rows($result)) {
	        while ($row = pg_fetch_assoc($result)) {
	            //if ($row['type'] == 0) $type = 'Кросс'; else $type = 'Муфта';
	            if ($row['type'] == 0) $type = 'Кросс'; else if ($row['type'] == 1) $type = 'Муфта'; else $type = 'Медный';
	            if (isset($row['num'])) $num = ' №' . $row['num']; else $num = '';

	            $content.='<tr>';
	            $content.='
	            	<td class="span1">'.$i.'</td>
	            	<td class="span4"><a href="?act=s_cable&pq_id='.$row['id'].'">'.$type.$num.' ('.$row['name'].')</a></td>
	            	<td class="span8">'.$row['pq_descrip'].'</td>
	            ';
	            if ($group_access['o_node_edit'] || $group_access['o_node_del'])
	                $content.='<td class="span2">';
	            else
	                $content.='<td class="span2">&nbsp;</td>';
	            if ($group_access['o_node_edit'])
	                $content.='<button class="icon-pencil mini m0" id="pq_e_add_div" rel="?act=e_pq&pq_id='.$row['id'].'" title="Редактировать" /></button>&nbsp;';            
	            if ($group_access['o_node_del'])
	                //$content.='<button class="icon-cancel-2 mini m0" id="pq_d_add_div" rel="?act=d_pq&pq_id='.$row['id'].'&addr='.$row['address'].' - '.$type.$num.'" title="Удалить"/></button>';
	            	$content.='<button class="icon-cancel-2 mini m0" id="pq_d_add_div" rel="?act=d_pq&pq_id='.$row['id'].'&addr='.$row['name'].'" title="Удалить"/></button>&nbsp;';
	            //if ($group_access['o_node_edit'] || $group_access['o_node_del'])
	            	//$content.='<button class="icon-attachment mini m0" id="pq_d_add_div" rel="?act=pq_file&pq_id='.$row['id'].'" title="Файлы"/></button>';
	            	$content.='<button class="icon-attachment mini m0" onClick="location.href=\'?act=pq_file&pq_id='.$row['id'].'\'" title="Файлы"/></button>';
	            	//$content.='<a class="icon-attachment mini m0 button" href="?act=pq_file&pq_id='.$row['id'].'" title="Файлы"/></a>';
	            	//?act=s_cable&pq_id=2841
	                $content.='</td>';
	            $content.='</tr>';
	            $i++;
	        }
	    }
	    $content.='</table>';

/*
	    $content.='Адресс для вставки в 2gis:<br>';
	    $content.='<input id="2g_addr" type="text" style="width: 300px;" value=\''.$address.'\'</input><br>';
	    $content.='HTML код для вставки в 2gis:<br>';
	    $content.='<input id="2g_html" type="text" style="width: 100%;" value=\'<a href="http://'.$host.'/fibers/index.php?act=s_pq&node_id='.clean($_GET['node_id']).'">'.$address.'</a>\'</input>';
*/
	    $content.='<script>$("#2g_addr").focus(function() { $(this).select() });$("#2g_html").focus(function() { $(this).select() });</script>';
    }

    if(isset($_GET['p_node']) && $group_access['p_node']) {
/*    		$action2='
    		<div class="span m0 text-left">
    			<button class="m0" id="switch_add_div" rel="?act=n_switches&node_id=' . clean($_GET['node_id']) . '"/>Добавить коммутатор</button>
    			<button class="m0" id="mc_add_div" rel="?act=n_mc&node_id=' . clean($_GET['node_id']) . '"/>Добавить медиаконвертер</button>
    		</div>'.$action;*/
//        s_node_edit
		if($group_access['p_node_edit'])
        $action.=($group_access['p_node_add']?'
                <li class="sub-menu"><a href="">Добавить</a>
                    <ul class="text-left">
                        <li><a id="pq_into_div" href="/" rel="?act=n_box&node_id=' . clean($_GET['node_id']) . '"/>Раму/Ящик</a></li>
                        <li><a id="pq_into_div" href="/" rel="?act=n_switches&node_id=' . clean($_GET['node_id']) . '"/>Коммутатор</a></li>
                        <li><a id="pq_into_div" href="/" rel="?act=n_mc&node_id=' . clean($_GET['node_id']) . '"/>Медиаконвертер</a></li>
                        <li><a id="pq_into_div" href="/" rel="?act=n_ups&node_id=' . clean($_GET['node_id']) . '"/>ИБП</a></li>
                        <li><a id="pq_into_div" href="/" rel="?act=n_other&node_id=' . clean($_GET['node_id']) . '"/>Прочее</a></li>
                        '.(!@pg_result(pg_query("SELECT * FROM ".$table_keys." WHERE node_id = ".clean($_GET['node_id'])),0)?'<li><a id="pq_into_div" href="/" rel="?act=e_key_node&node_id='.clean($_GET['node_id']).'"/>Ключ</a></li>':'').'
                        '.(!@pg_result(pg_query("SELECT * FROM ".$table_lift." WHERE node_id = ".clean($_GET['node_id'])),0)?'<li><a id="pq_into_div" href="/" rel="?act=e_lift_node&node_id=' . clean($_GET['node_id']) . '"/>Лифтовую</a></li>':'').'
                    </ul>
                </li>':'');

        // общая таблица
        $i=1;
        $content='<table class="striped">';
        $content.='<tr>';
        
        // таблица ключей, лифтовых и описания
        $content.='<td>Общее</td>';
        $content.='</tr><tr>';
        $content.='<td>';
        $result_descrip=@pg_fetch_assoc(pg_query("SELECT * FROM ".$table_descrip." WHERE node_id =".clean($_GET['node_id'])),0);
        $result_key=@pg_fetch_assoc(pg_query("SELECT * FROM ".$table_keys." WHERE node_id =".clean($_GET['node_id'])),0);
        $result_lift=@pg_fetch_assoc(pg_query("SELECT *,lt1.descrip AS lt_descrip FROM ".$table_lift_type." AS lt1, ".$table_lift." AS l1 WHERE l1.lift_id = lt1.id AND l1.node_id =".clean($_GET['node_id'])),0);
            $content.='
            <table class="striped">
                <tr>';
            if($result_key)
            $content.='
                    <td class="span4">Ключ</td>
                    <td class="span2">'.
                        ($group_access['p_node_edit']?'<button class="icon-pencil m0 mini" id="key_node_e_add_div" rel="?act=e_key_node&node_id='.clean($_GET['node_id']).'" title="Редактировать"></button>&nbsp;':'&nbsp;').
                        ($group_access['p_node_del']?'<button class="icon-cancel-2 mini m0" id="key_node_d_add_div" rel="?act=d_key_node&node_id='.clean($_GET['node_id']).'" title="Удалить"/></button>':'')
                    .'</td>';
            else
            $content.='<td class="span4">&nbsp;</td><td class="span2">&nbsp;</td>';
            $content.='
                    <td class="span10">Описание</td>
                </tr>
                <tr>
                    <td colspan=2>'.$result_key['num'].' '.($result_key['descrip']?'('.$result_key['descrip'].')':'').'&nbsp;</td>
                    <td class="span10 text-right" rowspan=5><div class="input-control textarea"><textarea id="descrip_text">'.$result_descrip['text'].'</textarea></div>
                        <input type="hidden" id="id_descrip_text" value="'.clean($_GET['node_id']).'">'.
                        ($group_access['p_node_edit']?'<button class="icon-pencil m0 mini" id="e_descrip_text" title="Редактировать"></button>&nbsp;':'&nbsp').
                        ($group_access['p_node_del']?'<button class="icon-cancel-2 mini m0" id="d_descrip_text" title="Удалить"/></button>':'')
                    .'</td>
                </tr>';
            if($result_lift)
            $content.='
                <tr>
                    <td>Лифтовая</td>
                    <td>'.
                        ($group_access['p_node_edit']?'<button class="icon-pencil m0 mini" id="lift_node_e_add_div" rel="?act=e_lift_node&node_id='.clean($_GET['node_id']).'" title="Ok"></button>&nbsp;':'&nbsp;').
                        ($group_access['p_node_del']?'<button class="icon-cancel-2 mini m0" id="lift_node_d_add_div" rel="?act=d_lift_node&node_id='.clean($_GET['node_id']).'" title="Удалить"/></button>':'')
                    .'</td>
                </tr>
                <tr><td colspan=2>Адрес: '.$result_lift['name'].'&nbsp;</td></tr>
                <tr><td colspan=2>Телефоны: '.$result_lift['tel'].'&nbsp;</td></tr>
                <tr><td colspan=2>'.$result_lift['lt_descrip'].'&nbsp;</td></tr>';
            /*else
            $content.='
                <tr rowspan=4>
                    <td colspan=2>&nbsp;</td>
                </tr>';*/

            $content.='
            </table>';
        $content.='</td></tr>';

        // таблица типа узла
        $content.='<td>Тип узла</td>';
        $content.='</tr><tr>';
        $content.='<td>';
        
            // таблица рам/ящиков
            $o=1;
            $content.='<table class="striped">';
            $sql="SELECT b1.*, bt1.name, bt1.unit
                FROM ".$table_box_type." AS bt1, ".$table_box." AS b1
                WHERE bt1.id = b1.box_type_id
                AND b1.node_id=" . clean($_GET['node_id'])."
                ORDER BY bt1.name";
            $result = pg_query($sql);
            if (pg_num_rows($result)) {
                //$content.='<tr>';
                //$content.='<td colspan=4>Ящики/Рамы</td>';
                //$content.='</tr>';
                $content.='<tr>';
                $content.='<td class="span1">№</td>';
                $content.='<td class="span7">Название</td>';
                $content.='<td class="span1">Юнитов</td>';
                $content.='<td class="span9">Описание</td>';
                $content.='<td class="span2">&nbsp;</td>';
                $content.='</tr>';
                $content.='<tr>';
                while ($row = pg_fetch_assoc($result)) {
                    $content.='<tr>';
                    $content.='
	                    <td>'.$i.'.'.$o++.'</td>
	                    <td>'.$row['name'].'</td>
	                    <td>'.$row['unit'].'&nbsp;</td>
	                    <td>'.$row['descrip'].'&nbsp;</td>
                    ';
					$content.='<td class="span2">'.
							($group_access['p_node_edit']?'<button class="icon-pencil mini m0" id="box_e_add_div" rel="?act=e_box&id='.$row['id'].'" title="Редактировать" /></button>&nbsp;':'&nbsp;').
                        	($group_access['p_node_del']?'<button class="icon-cancel-2 mini m0" id="box_d_add_div" rel="?act=d_box&id='.$row['id'].'" title="Удалить"/></button>':'')
						.'</td>';
                    $content.='</tr>';
                }
            }
            $content.='</table>';
            $i++;
        
        $content.='</td>';
        $content.='</tr>';
        // таблица оборудования
        $content.='<tr>';
        $content.='<td>Оборудование</td>';
        $content.='</tr><tr>';
        $content.='<td>';
            // таблица коммутаторов
            $o=1;
        	$content.='<table class="striped">';
        	$sql="SELECT sw1 . * , st1.name, st1.ports_num, st1.unit, st1.power, sn1.sn
    	    	FROM ".$table_switch_type." AS st1, ".$table_switches." AS sw1
    	    	LEFT JOIN ".$table_sn." AS sn1 ON sn1.eq = sw1.id AND eq_type='".$switch_id."'
    	    	WHERE st1.id = sw1.switch_type_id
    	    	AND sw1.node_id=" . clean($_GET['node_id'])."
    	    	ORDER BY st1.name";
        	$result = pg_query($sql);
        	
        	$total_used_watt=0;
        	
        	if (pg_num_rows($result)) {
        		$content.='<tr>';
        		$content.='<td colspan=9>Коммутаторы</td>';
        		$content.='</tr>';
        		$content.='<tr>';
        		$content.='<td class="span1">№</td>';
        		$content.='<td class="span4">Название</td>';
        		$content.='<td class="span1">Портов</td>';
        		$content.='<td class="span1">Занято</td>';
                $content.='<td class="span1">Юнитов</td>';
                $content.='<td class="span1">ip</td>';
        		$content.='<td class="span3">S/N</td>';
        		$content.='<td class="span3">Описание</td>';
        		$content.='<td class="span2">&nbsp;</td>';
        		$content.='</tr>';
        		$content.='<tr>';
        		while ($row = pg_fetch_assoc($result)) {
        			$total_used_watt=$total_used_watt+$row['power'];
        		    // подсветка если портов мало или не осталось
        			$content.='<tr class="'.($row['used_ports']>=$row['ports_num']?'bg-color-red':($row['used_ports']+1>=$row['ports_num']?'bg-color-yellow':'')).'">';
        			$content.='
	        			<td>'.$i.'.'.$o++.'</td>
	        			<td>'.$row['name'].'</td>
	        			<td>'.$row['ports_num'].'&nbsp;</td>
	        			<td>'.$row['used_ports'].'&nbsp;</td>
	        			<td>'.$row['unit'].'&nbsp;</td>
	        			<td>'.(!empty($row['ip'])?'<a href="http://syslog.sd.rdtc.ru/cgi-bin/rrd_switch.cgi?mode=list&ip='.$row['ip'].'" target="_blank">ГР</a>&nbsp;<a href="http://syslog.sd.rdtc.ru/cgi-bin/port-status.cgi?host='.$row['ip'].'&notest=false" target="_blank">СТ</a>':'').'&nbsp;</td>
	        			<td>'.$row['sn'].'&nbsp;</td>
	        			<td>'.$row['descrip'].'&nbsp;</td>
        			';
					$content.='<td class="span2">'.
        					($group_access['p_node_edit']?'<button class="icon-pencil mini m0" id="switches_e_add_div" rel="?act=e_switches&id='.$row['id'].'" title="Редактировать" /></button>&nbsp;':'&nbsp;').
							($group_access['p_node_del']?'<button class="icon-cancel-2 mini m0" id="switches_d_add_div" rel="?act=d_switches&id='.$row['id'].'" title="Удалить"/></button>':'')
						.'</td>';
        			$content.='</tr>';
        		}
        	}
        	$content.='</table>';
            $i++;
            
            // таблица медиаконвертеров
            $o=1;
            $content.='<table class="striped">';
            $sql="SELECT mc1 . * , mt1.name, mt1.power, sn1.sn
                FROM ".$table_mc_type." AS mt1, ".$table_mc." AS mc1
                LEFT JOIN ".$table_sn." AS sn1 ON sn1.eq = mc1.id AND eq_type='".$mc_id."'
                WHERE mt1.id = mc1.mc_type_id
                AND mc1.node_id=" . clean($_GET['node_id'])."
                ORDER BY mt1.name";
            $result = pg_query($sql);
            if (pg_num_rows($result)) {
                $content.='<tr>';
                $content.='<td colspan=5>Медиаконвертеры</td>';
                $content.='</tr>';
                $content.='<tr>';
                $content.='<td class="span1">№</td>';
                $content.='<td class="span4">Название</td>';
                $content.='<td class="span5">S/N</td>';
                $content.='<td class="span9">Описание</td>';
                $content.='<td class="span2">&nbsp;</td>';
                $content.='</tr>';
                $content.='<tr>';
                while ($row = pg_fetch_assoc($result)) {
                	$total_used_watt=$total_used_watt+$row['power'];
                    $content.='<tr>';
                    $content.='
	                    <td>'.$i.'.'.$o++.'</td>
	                    <td>'.$row['name'].'</td>
	                    <td>'.$row['sn'].'&nbsp;</td>
	                    <td>'.$row['descrip'].'&nbsp;</td>
                    ';
					$content.='<td class="span2">'.
							($group_access['p_node_edit']?'<button class="icon-pencil mini m0" id="mc_e_add_div" rel="?act=e_mc&id='.$row['id'].'" title="Редактировать" /></button>&nbsp;':'&nbsp;').
							($group_access['p_node_del']?'<button class="icon-cancel-2 mini m0" id="mc_d_add_div" rel="?act=d_mc&id='.$row['id'].'" title="Удалить"/></button>':'')
                        .'</td>';
                    $content.='</tr>';
                }
            }
            $content.='</table>';
            $i++;
            
            // таблица ИБП
            $o=1;
            $content.='<table class="striped">';
            $sql="SELECT u1 . * , ut1.name, ut1.unit, ut1.power, sn1.sn
                FROM ".$table_ups_type." AS ut1, ".$table_ups." AS u1
                LEFT JOIN ".$table_sn." AS sn1 ON sn1.eq = u1.id AND eq_type='".$ups_id."'
                WHERE ut1.id = u1.ups_type_id
                AND u1.node_id=" . clean($_GET['node_id'])."
                ORDER BY ut1.name";
            $result = pg_query($sql);
            
            $total_watt=0;
            
            if (pg_num_rows($result)) {
                $content.='<tr>';
                $content.='<td colspan=7>ИБП</td>';
                $content.='</tr>';
                $content.='<tr>';
                $content.='<td class="span1">№</td>';
                $content.='<td class="span4">Название</td>';
                $content.='<td class="span1">Мощность</td>';
                $content.='<td class="span1">Юнитов</td>';
                $content.='<td class="span4">S/N</td>';
                $content.='<td class="span4">Описание</td>';
                $content.='<td class="span2">&nbsp;</td>';
                $content.='</tr>';
                $content.='<tr>';
                while ($row = pg_fetch_assoc($result)) {
                	$total_watt=$total_watt+$row['power'];
                    $content.='<tr>';
                    $content.='
	                    <td>'.$i.'.'.$o++.'</td>
	                    <td>'.$row['name'].'</td>
	                    <td>'.$row['power'].'&nbsp;</td>
	                    <td>'.$row['unit'].'&nbsp;</td>
	                    <td>'.$row['sn'].'&nbsp;</td>
	                    <td>'.$row['descrip'].'&nbsp;</td>
                    ';
					 $content.='<td class="span2">'.
							($group_access['p_node_edit']?'<button class="icon-pencil mini m0" id="ups_e_add_div" rel="?act=e_ups&id='.$row['id'].'" title="Редактировать" /></button>&nbsp;':'&nbsp;').
							($group_access['p_node_del']?'<button class="icon-cancel-2 mini m0" id="ups_d_add_div" rel="?act=d_ups&id='.$row['id'].'" title="Удалить"/></button>':'')
						.'</td>';
                    $content.='</tr>';
                }
            }
            $content.='</table>';
            $i++;
            
            // таблица кроссов
            $o=1;
            $content.='<table class="striped">';
            $sql="SELECT pq.id AS id, pq_t.type , pq_t.ports_num ,pq_t.unit , pq.num, pq.descrip AS pq_descrip, pq_t.descrip AS pq_type_descrip, pq_t.name
                FROM ".$table_node." AS node, ".$table_pq." AS pq
                LEFT JOIN ".$table_pq_type." AS pq_t ON pq.pq_type_id = pq_t.id
                WHERE pq.node=node.id AND pq.node=" . clean($_GET['node_id'])."
                ORDER BY pq.node";
            $result = pg_query($sql);
            if (pg_num_rows($result)) {

                $content.='<tr>';
                $content.='<td colspan=6>Кроссы</td>';
                $content.='</tr>';
                $content.='<tr>';
                $content.='<td class="span1">№</td>';
                $content.='<td class="span4">Название</td>';
                $content.='<td class="span2">Портов</td>';
                $content.='<td class="span2">Юнитов</td>';
                $content.='<td class="span6">Описание</td>';
                $content.='<td class="span2">&nbsp;</td>';
                $content.='</tr>';
                $content.='<tr>';
                while ($row = pg_fetch_assoc($result)) {
                	//if ($row['type'] == 0) $type = 'Кросс'; else $type = 'Муфта';
                	if ($row['type'] == 0) $type = 'Кросс'; else if ($row['type'] == 1) $type = 'Муфта'; else $type = 'Медный';
                	if (isset($row['num'])) $num = ' №' . $row['num']; else $num = ' №1';

                    $content.='<tr>';
                    $content.='
	                    <td>'.$i.'.'.$o++.'</td>
	                    <td>'.($group_access['o_node']?'<a href="index.php?act=s_cable&pq_id='.$row['id'].'" target="_blank">'.$row['name'].($row['type']<2?' '.$type.$num:'').'</a>':$row['name'].' '.$type.$num).'</td>
	                    <td>'.$row['ports_num'].'&nbsp;</td>
	                    <td>'.$row['unit'].'&nbsp;</td>
	                    <td>'.@$row['descrip'].'&nbsp;</td>
	                    <td>&nbsp;</td>
                    ';
                    $content.='</tr>';
                }
            }
            $content.='</table>';
            $i++;
            
            // таблица прочего оборудования
            $o=1;
            $content.='<table class="striped">';
            $sql="SELECT o1 . * , ot1.name, ot1.unit
                FROM ".$table_other_type." AS ot1, ".$table_other." AS o1
                WHERE ot1.id = o1.other_type_id
                AND o1.node_id=" . clean($_GET['node_id'])."
                ORDER BY ot1.name";
            $result = pg_query($sql);
            if (pg_num_rows($result)) {
                $content.='<tr>';
                $content.='<td colspan=5>Разное</td>';
                $content.='</tr>';
                $content.='<tr>';
                $content.='<td class="span1">№</td>';
                $content.='<td class="span6">Название</td>';
                $content.='<td class="span1">Юнитов</td>';
                $content.='<td class="span10">Описание</td>';
                $content.='<td class="span2">&nbsp;</td>';
                $content.='</tr>';
                $content.='<tr>';
                while ($row = pg_fetch_assoc($result)) {
                    $content.='<tr>';
                    $content.='
	                    <td>'.$i.'.'.$o++.'</td>
	                    <td>'.$row['name'].'</td>
	                    <td>'.$row['unit'].'&nbsp;</td>
	                    <td>'.$row['descrip'].'&nbsp;</td>
                    ';
					$content.='<td class="span2">'.
                        	($group_access['p_node_edit']?'<button class="icon-pencil mini m0" id="other_e_add_div" rel="?act=e_other&id='.$row['id'].'" title="Редактировать" /></button>&nbsp;':'&nbsp;').
							($group_access['p_node_del']?'<button class="icon-cancel-2 mini m0" id="other_d_add_div" rel="?act=d_other&id='.$row['id'].'" title="Удалить"/></button>':'')
						.'</td>';
                    $content.='</tr>';
                }
            }
            $content.='</table>';
            $i++;

        $content.='</td>';
        $content.='</tr>';
        $content.='</table>';
        $content='<table class="striped"><tr><td>Суммарная мощность: '.$total_used_watt.($total_watt?' из '.$total_watt:'').' Ватт.</td></tr></table>'.$content;
        
    }

    $result=pg_fetch_assoc(pg_query("SELECT u1.*, n1.type FROM ".$table_node." AS n1 LEFT JOIN ".$table_user." AS u1 ON u1.id = n1.user_id WHERE n1.id=".clean($_GET['node_id'])),0);
    $user=$result['name'];
    //print_r($result);
    $action='
    <div class="m0 text-left">
        <div class="horizontal-menu">
            <ul class="m0">
                '.$action.'
            </ul>
		'.($group_access['incorrect']?'<div class="m5 span_text"><label class="checkbox"><input type="checkbox" id="incorrect_'.clean($_GET['node_id']).'" '.(pg_result(pg_query("SELECT incorrect FROM ".$table_node." WHERE id =".clean($_GET['node_id'])),0)==true?'checked':'').'><span>Проблема</span></label></div>':'').
		($group_access['u_const'] && $result['type']!=1?'<div class="m5 span_text"><label class="checkbox"><input type="checkbox" id="u_const_'.clean($_GET['node_id']).'" '.(pg_result(pg_query("SELECT u_const FROM ".$table_node." WHERE id =".clean($_GET['node_id'])),0)==true?'checked':'').'><span>В стадии строительства</span></label></div>':'').
		($_SESSION['group']==0 || $user_id == 2 || $user_id == 4?'<div class="m5 span_text" style="color: #f1f1f1;" >узел создал: '.$user.'</div>':'').'</div>
    </div>';
    show_menu();
    //echo "SELECT u_const FROM ".$table_node." WHERE id =".clean($_GET['node_id']);
    die;
}

// Справочники
if (isset($_GET['act']) && $_GET['act'] == 'dirs' && ($group_access['dirs'] || $group_access['key'] )) {
	$i=1;
	$title = 'Справочники';

// редактирование Области
	if($_GET['dir'] == 'region' && $group_access['dirs']) {
		$i=1;
		$title.= ' > Область';
		$action='
		<div class="span2 m5 input-control text">Область</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_region" type="button" />Добавить область</button>
		</div>';
		$sql = "SELECT * FROM " . $table_region . " AS ar1 ORDER BY name;";
		$result = pg_query($con_id, $sql);
		if (pg_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span5">Наименование</td>';
			$content.='<td class="span8">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = pg_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['name'].'&nbsp;</td>';
				$content.='<td>'.$row['descrip'].'&nbsp;</td>';
				$content.='
				<td class="toolbar m0">
					<button class="icon-pencil m0 mini" id="region_edit_in_div" rel="?act=e_region&id='.$row['id'].'" title="Редактировать"></button>
					<button class="icon-cancel-2 m0 mini" id="region_del_in_div" rel="?act=d_region&id='.$row['id'].'" title="Удалить"></button>
				</td>';
				$content.='</tr>';
				$i++;
			}
			$content.='</table>';
		}
	}

// редактирование Области
	if($_GET['dir'] == 'region' && $group_access['dirs']) {
		$i=1;
		$title.= ' > Область';
		$action='
		<div class="span2 m5 input-control text">Область</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_region" type="button" />Добавить область</button>
		</div>';
		$sql = "SELECT * FROM " . $table_region . " AS ar1 ORDER BY name;";
		$result = pg_query($con_id, $sql);
		if (pg_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span5">Наименование</td>';
			$content.='<td class="span8">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = pg_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['name'].'&nbsp;</td>';
				$content.='<td>'.$row['descrip'].'&nbsp;</td>';
				$content.='
				<td class="toolbar m0">
					<button class="icon-pencil m0 mini" id="region_edit_in_div" rel="?act=e_region&id='.$row['id'].'" title="Редактировать"></button>
					<button class="icon-cancel-2 m0 mini" id="region_del_in_div" rel="?act=d_region&id='.$row['id'].'" title="Удалить"></button>
				</td>';
				$content.='</tr>';
				$i++;
			}
			$content.='</table>';
		}
	}

// редактирование Города/посёлка
	if($_GET['dir'] == 'city' && $group_access['dirs']) {
		$i=1;
		$title.= ' > Город/посёлок';
		$action='
		<div class="span2 m5 input-control text">Город/посёлок</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_city" type="button" />Добавить город/посёлок</button>
		</div>';
		$sql = "SELECT * FROM " . $table_city . " AS ar1 ORDER BY name;";
		$result = pg_query($con_id, $sql);
		if (pg_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span5">Наименование</td>';
			$content.='<td class="span8">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = pg_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['name'].'&nbsp;</td>';
				$content.='<td>'.$row['descrip'].'&nbsp;</td>';
				$content.='
				<td class="toolbar m0">
					<button class="icon-pencil m0 mini" id="city_edit_in_div" rel="?act=e_city&id='.$row['id'].'" title="Редактировать"></button>
					<button class="icon-cancel-2 m0 mini" id="city_del_in_div" rel="?act=d_city&id='.$row['id'].'" title="Удалить"></button>
				</td>';
				$content.='</tr>';
				$i++;
			}
			$content.='</table>';
		}
	}


// редактирование Районов
	if($_GET['dir'] == 'area2' && $group_access['dirs']) {
		$i=1;
		$title.= ' > Район';
		$action='
		<div class="span2 m5 input-control text">Район</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_area" type="button" />Добавить район</button>
		</div>';
		$sql = "SELECT * FROM " . $table_area . " AS ar1 ORDER BY name;";
		$result = pg_query($con_id, $sql);
		if (pg_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span5">Наименование</td>';
			$content.='<td class="span8">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = pg_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['name'].'&nbsp;</td>';
				$content.='<td>'.$row['descrip'].'&nbsp;</td>';
				$content.='
				<td class="toolbar m0">
					<button class="icon-pencil m0 mini" id="area_edit_in_div" rel="?act=e_area&id='.$row['id'].'" title="Редактировать"></button>
					<button class="icon-cancel-2 m0 mini" id="area_del_in_div" rel="?act=d_area&id='.$row['id'].'" title="Удалить"></button>
				</td>';
				$content.='</tr>';
				$i++;
			}
			$content.='</table>';
		}
	}

// редактирование Района
	if($_GET['dir'] == 'area' && $group_access['dirs']) {
		$i=1;
		$title.= ' > Районы';
		$action='
		<div class="span2 m5 input-control text">Районы</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_area" type="button" />Добавить район</button>
		</div>';
		$sql = "SELECT a1.*,c1.name AS city_name FROM ".$table_area." AS a1 LEFT JOIN ".$table_city." AS c1 ON a1.city_id = c1.id ORDER BY a1.name";
		$result = pg_query($sql);
		if (pg_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span4">Название</td>';
			$content.='<td class="span4">Город/посёлок</td>';
			$content.='<td class="span5">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = pg_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['name'].'</td>';
				$content.='<td>'.$row['city_name'].'&nbsp;</td>';
				$content.='<td>'.($row['descrip'] ? $row['descrip'] : "&nbsp;").'</td>';
				$content.='
				<td class="toolbar m0">
				<button class="icon-pencil m0 mini" id="area_edit_in_div" rel="?act=e_area&id='.$row['id'].'" title="Редактировать"></button>
				<button class="icon-cancel-2 m0 mini" id="area_del_in_div" rel="?act=d_area&id='.$row['id'].'" title="Удалить"></button>
				</td>';
				$content.='</tr>';
				$i++;
			}
			$content.='</table>';
		}
	}

// редактирование Улиц
	if($_GET['dir'] == 'street' && $group_access['dirs']) {
		$i=1;
		$title.= ' > Улицы';
		$action='
		<div class="span2 m5 input-control text">Улицы</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_street_name" type="button" />Добавить улицу</button>
		</div>';
		$sql = "SELECT sn1.*, ar1.name AS area_name FROM ".$table_street_name." AS sn1 LEFT JOIN ".$table_area." AS ar1 ON sn1.area_id = ar1.id ORDER BY sn1.name;";
		$result = pg_query($sql);
		if (pg_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span4">Название</td>';
			$content.='<td class="span3">Кратк. название</td>';
			$content.='<td class="span4">Район</td>';
			$content.='<td class="span5">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = pg_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['name'].'</td>';
				$content.='<td>'.$row['small_name'].'&nbsp;</td>';
				$content.='<td>'.$row['area_name'].'&nbsp;</td>';
				$content.='<td>'.($row['descrip'] ? $row['descrip'] : "&nbsp;").'</td>';
				$content.='
				<td class="toolbar m0">
				<button class="icon-pencil m0 mini" id="street_name_edit_in_div" rel="?act=e_street_name&id='.$row['id'].'" title="Редактировать"></button>
				<button class="icon-cancel-2 m0 mini" id="street_name_del_in_div" rel="?act=d_street_name&id='.$row['id'].'" title="Удалить"></button>
				</td>';
				$content.='</tr>';
				$i++;
			}
			$content.='</table>';
		}
	}

// редактирование Размешения
	if($_GET['dir'] == 'location' && $group_access['dirs']) {
		$i=1;
		$title.= ' > Размещение';
		$action.='
		<div class="span2 m5 input-control text">Размещение</div>
		<div class="span m0 text-left">
		<button class="m0" id="in_div" rel="?act=n_location" type="button" />Добавить размещение</button>
		</div>';
		$sql = "SELECT * FROM ".$table_location." ORDER BY location";
		$result = pg_query($sql);
		if (pg_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span6">Размещение</td>';
			$content.='<td class="span7">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = pg_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['location'].'</td>';
				$content.='<td>'.$row['descrip'].'&nbsp;</td>';
				$content.='
				<td class="toolbar m0">
				<button class="icon-pencil m0 mini" id="location_edit_in_div" rel="?act=e_location&id='.$row['id'].'" title="Редактировать"></button>
				<button class="icon-cancel-2 m0 mini" id="location_del_in_div" rel="?act=d_location&id='.$row['id'].'" title="Удалить"></button>
				</td>';
				$content.='</tr>';
				$i++;
			}
			$content.='</table>';
		}
	}

// редактирование Помещения
	if($_GET['dir'] == 'room' && $group_access['dirs']) {
		$i=1;
		$title.= ' > Помещение';
		$action.='
		<div class="span2 m5 input-control text">Помещение</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_room" type="button" />Добавить Помещение</button>
		</div>';
		$sql = "SELECT * FROM ".$table_room." ORDER BY room";
		$result = pg_query($sql);
		if (pg_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span6">Помещение</td>';
			$content.='<td class="span7">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = pg_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				//$content.='<td>'.$row['room'].'</td>';
				$content.='<td><a tagret="_blank" href="?act=razm&id='.$row['id'].'">'.$row['room'].'</a></td>';
				$content.='<td>'.$row['descrip'].'&nbsp;</td>';
				$content.='
				<td class="toolbar m0">
    				<button class="icon-pencil m0 mini" id="room_edit_in_div" rel="?act=e_room&id='.$row['id'].'" title="Редактировать"></button>
    				<button class="icon-cancel-2 m0 mini" id="room_del_in_div" rel="?act=d_room&id='.$row['id'].'" title="Удалить"></button>
				</td>';
				$content.='</tr>';
				$i++;
			}
			$content.='</table>';
		}
	}

// редактирование ключей
    if($_GET['dir'] == 'keys' && ($group_access['dirs'] || $group_access['key'])) {
        $i=1;
        $title.= ' > Ключи';
        $action.='
		<div class="span2 m0 text-left">
    		'.($group_access['key_edit']?'<button class="m0" id="in_div" rel="?act=n_key" type="button" />Добавить ключ</button>':'').'
    	</div>
    				
		<div class="horizontal-menu">
		    <ul>
				<li><a class="m0" href="?act=dirs&dir=keys'.(isset($_GET['free'])?'':'&free').'" />'.(isset($_GET['free'])?'Все':'Свободные').' ключи</a></li>
				<li><a href="?act=dirs&dir=keys_print" target="_blank">Печать</a></li>
		    </ul>
	    </div>';
        $sql = "SELECT k1.*, CASE WHEN left(k1.descrip, 1) = '.' THEN right(k1.descrip, length(k1.descrip)-1) ELSE n1.address_full END AS address_full FROM ".$table_keys." AS k1 LEFT JOIN ".$table_node." AS n1 ON n1.id = k1.node_id ".(isset($_GET['free'])?'WHERE k1.node_id IS NULL AND (left(k1.descrip, 1) != \'.\' OR k1.descrip IS NULL)':'')." ORDER BY LENGTH(k1.num), k1.num";
        $result = pg_query($sql);
        if (pg_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span2">Номер</td>';
            $content.='<td class="span6">Адрес</td>';
            $content.='<td class="span8">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = pg_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['num'].'</td>';
                $content.='<td><a href="?act=s_pq&o_node&node_id='.$row['node_id'].'" target="_blank">'.$row['address_full'].'</a>&nbsp;</td>';
                $content.='<td>'.$row['descrip'].'&nbsp;</td>';
                $content.='
                <td class="toolbar m0">
                    '.($group_access['key_edit']?'<button class="icon-pencil m0 mini" id="key_edit_in_div" rel="?act=e_key&id='.$row['id'].'" title="Редактировать"></button>
                    <button class="icon-cancel-2 m0 mini" id="key_del_in_div" rel="?act=d_key&id='.$row['id'].'" title="Удалить"></button>':'').'
                </td>';
                $content.='</tr>';
                $i++;
            }
            $content.='</table>';
        }
    }

    // редактирование ключей / печать
    if($_GET['dir'] == 'keys_print' && ($group_access['dirs'] || $group_access['key'])) {
    	$sql = "SELECT k1.*, CASE WHEN left(k1.descrip, 1) = '.' THEN right(k1.descrip, length(k1.descrip)-1) ELSE n1.address_full END FROM ".$table_keys." AS k1 LEFT JOIN ".$table_node." AS n1 ON n1.id = k1.node_id WHERE k1.node_id IS NOT NULL OR left(k1.descrip, 1) = '.' ORDER BY n1.address_full";
    	//$sql = "SELECT k1.*, CASE WHEN left(k1.descrip, 1) = '.' THEN right(k1.descrip, length(k1.descrip)-1) ELSE n1.address_full END AS address_full FROM ".$table_keys." AS k1 LEFT JOIN ".$table_node." AS n1 ON n1.id = k1.node_id ".(isset($_GET['free'])?'WHERE k1.node_id IS NULL AND (left(k1.descrip, 1) != \'.\' OR k1.descrip IS NULL)':'')." ORDER BY LENGTH(k1.num), k1.num";
    	$title='Ключи [Версия для печати]';

    	$result = pg_query($sql);
    	if (pg_num_rows($result)) {
/*    		$i=0;
    		$max_i=3;

    		$content_head.='<tr class="text-center" style="background-color: silver; font-weight: bold;">';
    		for($o=0;$max_i>=$o;$o++) {
				$content_head.='<td class="span1 left bt">№</td>
						<td class="span7 br bt">Адрес</td>';
    		}
			$content_head.='</tr>';

			$oo=39;
			$o=0;
    		while ($row = pg_fetch_assoc($result)) {
    			if($o==0 && $i==0) $content.='<table>'.$content_head;
    			
    			$content.=($i==0?'<tr>':'');
    			$content.='	<td class="text-center">'.$row['num'].'</td>';
    			$content.=' <td class="br">'.$row['address_full'].'</td>';
    			$content.=($i==$max_i?'</tr>':'');

    			if($i>=$max_i) {
    				$i=0;
    				$o++;
    			} else $i++;
    			
    			if($o>=$oo) {
    				$content.='</table><br>';
    				$o=0;
    			}
    		}*/
    		$c=4;
    		$r=62;
    		//$first=true;
    		while ($row = pg_fetch_assoc($result)) {
    			if($c_==0 && $r_==0) $content.='<div class="page'.($first?' page_next':'').'">';
    			if(!$page_first) $page_first = true;

    			if($r_==0) {
    				$content.='<div class="col">';
    			}

    			if($r_==0) {
    				$content.='		<div class="num">№</div>';
    				$content.='		<div class="addr">Адрес</div>';
    				$content.='		<div class="clear"></div>';
    			}
    			$num = explode("_",$row['num']);
    			$content.='<div class="num'.($r_>=$r?' bb_n':'').'">'.$num[0].'</div>';
    			$content.='<div class="addr'.($r_>=$r?' bb_n':'').'">'.$row['address_full'].'</div>';
    			$content.='<div class="clear"></div>';

				if($r_>=$r) {
					$r_=0;
					$c_++;
					$content.='</div>';
				} else $r_++;
				
				if($c_>=$c) {
					$c_=0;
					$content.='<div class="clear"></div></div>';
				}
    		}
    		//$content.='</div><div class="clear"></div>';
    		$content.='</div>';
    	}
    	//$content.='</table>';
    	$text='
			<html lang="ru">
			<head>
			    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			    <title>'.$title.'</title>
			    <style type="text/css">
			    	.page {
			    		//border: 1px solid silver;
			    		page-break-after: always;
			    		font-size: 7pt;
			    		font-weight: bold;
			    	}
			    	.page_next {
			    		page-break-before: always;
			    	}
			    	.pb {
			    		//display: block;
			    		page-break-after: always;
			    	}
			    	.col {
			    		border: 1px solid silver;
			    		float: left;
			    		page-break-inside: avoid;
		    		}
			    	.num {
			    		width: 40px;
			    		height: 16px;
			    		text-align: center;
			    		border-bottom: 1px solid silver;
			    		border-right: 1px solid silver;
			    		float: left;
			    		white-space: nowrap;
						overflow: hidden;
			    	}
			    	.addr {
			    		width: 140px;
			    		height: 16px;
			    		//text-align: center;
			    		border-bottom: 1px solid silver;
			    		float: left;
			    		white-space: nowrap;
						overflow: hidden;
			    	}
			    	.bb_n {
			    		border-bottom: none;
			    	}
			    	.clear { clear: both; margin: 0px; padding: 0px; }

			    	body, table {
					    font-family: "Segoe UI Semilight","Open Sans",Verdana,Arial,Helvetica,sans-serif;
					    //font-weight: 300;
					    font-size: 7pt;
					    letter-spacing: 0.02em;
					    line-height: 20px;
					}
			    	.title {
			    		text-align: center;
			    		font-size: 14pt;
			    		padding: 0 0 10px 0;
			    	}
			        table {
			    		width: 100%;
			    		//margin: 0px 0px 20px;
			    		//border-collapse: separate;
			    		border-spacing: 0px;
			            border: 1px solid black;
			        }
			    	td {
			    		//width: 100%;
			    		//margin: 0px 0px 20px;
			    		//border-collapse: separate;
			    		border-spacing: 0px;
			            border-left: 1px solid black;
			    		//border-top: 1px solid black;
			    		border-bottom: 1px solid black;
			    		//border-right: none;
			        }
					.br {
			            border-right: 1px solid black;
			        }
					.bt {
			            border-top: 1px solid black;
			        }
			    	.bgs {
			    		background-color: #DCDCDC;
			    	}
			    	.bgw {
			    		background-color: none;
			    	}
			    	.text-center { text-align: center; }
			    	.span1 { width: 50px; }
			    	.span2 { width: 100px; }
			    	.span3 { width: 150px; }
			    	.span4 { width: 200px; }
			    	.span5 { width: 250px; }
			    	.span6 { width: 300px; }
			    </style>
			</head>
			<body>
			    <div>
			        '.$content.'
			        <div class="clear"></div>
				</div>
			    <!--    <p>'.(microtime(1)-$t).'</p>
			    
				<a href="#print-this-document" onclick="print(); return false;">Распечатать</a>-->
			</body>
			</html>';
    	echo $text;
    	//echo "<p>".(microtime(1)-$t)."</p>";
    	die;
    }

// редактирование лифтёрок
    if($_GET['dir'] == 'lift' && $group_access['dirs']) {
        $i=1;
        $title.= ' > Лифтёрки';
        $action.='
        <div class="span2 m5 input-control text">Лифтёрки</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_lift_type" type="button" />Добавить лифтёрку</button>
        </div>';
        $sql = "SELECT * FROM ".$table_lift_type." ORDER BY name";
        $result = pg_query($sql);
        if (pg_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span6">Лифтёрка</td>';
            $content.='<td class="span5">Телефоны</td>';
            $content.='<td class="span7">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = pg_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['tel'].'</td>';
                $content.='<td>'.$row['descrip'].'&nbsp;</td>';
                $content.='
                <td class="toolbar m0">
                    <button class="icon-pencil m0 mini" id="lift_type_edit_in_div" rel="?act=e_lift_type&id='.$row['id'].'" title="Редактировать"></button>
                    <button class="icon-cancel-2 m0 mini" id="lift_type_del_in_div" rel="?act=d_lift_type&id='.$row['id'].'" title="Удалить"></button>
                </td>';
                $content.='</tr>';
                $i++;
            }
            $content.='</table>';
        }
    }

// редактирование типов пассивного оборудования (Кроссы/Муфты)
	if($_GET['dir'] == 'pq_type' && $group_access['dirs']) {
		$i=1;
		$title.=' > Кроссы/Муфты';
		$action.='
		<div class="span2 m5 input-control text">Кроссы/Муфты</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_pq_type" type="button" />Добавить тип</button>
		</div>';
		$sql = "SELECT * FROM ".$table_pq_type." AS pq_type ORDER BY type, name ;";
		$result = pg_query($sql);
		if (pg_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span6">Тип</td>';
			$content.='<td class="span9">Наименование</td>';
			$content.='<td class="span1">Портов</td>';
			$content.='<td class="span1">Юнитов</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = pg_fetch_assoc($result)) {
				//if ($row['type'] == 0) $type = 'Кросс'; else $type = 'Муфта';
				if ($row['type'] == 0) $type = 'Кросс'; else if ($row['type'] == 1) $type = 'Муфта'; else $type = 'Медный';
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$type.'</td>';
				$content.='<td>'.$row['name'].'</td>';
				$content.='<td>'.$row['ports_num'].'&nbsp;</td>';
				$content.='<td>'.$row['unit'].'&nbsp;</td>';
				$content.='
				<td class="toolbar m0">
					<button class="icon-pencil m0 mini" id="pq_edit_in_div" rel="?act=e_pq_type&id='.$row['id'].'" title="Редактировать"></button>
					<button class="icon-cancel-2 m0 mini" id="pq_del_in_div" rel="?act=d_pq_type&id='.$row['id'].'" title="Удалить"></button>
				</td>';
				$content.='</tr>';
				$i++;
			}
			$content.='</table>';
		}
	}

// редактирование типов кабелей
	if($_GET['dir'] == 'cable_type' && $group_access['dirs']) {
		$i=1;
		$title.= ' > Кабели';
		$action.='
		<div class="span2 m5 input-control text">Кабели</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_cable_type" type="button" />Добавить тип</button>
			<button class="m0" onClick="window.open(\'?act=cable\',\'_blank\');" type="button" />Список кабелей</button>
		</div>';
		
		$sql="SELECT ct.fib FROM ".$table_cable_type." AS ct WHERE ct.fib IS NOT NULL GROUP BY ct.fib ORDER BY ct.fib";
		$result = pg_query($sql);
		if(pg_num_rows($result)){
			$select_fib_count='<select id="select_fib_count" onChange="var fib=\'\'; if($(\'select#select_fib_count\').val()) fib=\'&fib=\'+$(\'select#select_fib_count\').val(); window.location=\'?act=dirs&dir=cable_type\'+fib;">';
			$select_fib_count.='<option value="">Все</option>';
			while($row=pg_fetch_assoc($result)){
				$select_fib_count.='<option value="'.$row['fib'].'"';
				if($_GET['fib']==$row['fib']) {
					$select_fib_count.=" SELECTED";
				}
				$select_fib_count.='>'.$row['fib'].'ОВ</option>';
			}
			$select_fib_count.='</select>';
		}
		$action.='<div class="span m0">&nbsp;</div><div class="span m0 input-control text">'.$select_fib_count.'</div>';
		
		$sql = "SELECT * FROM ".$table_cable_type." AS cable_type ".(is_numeric($_GET['fib'])?" WHERE fib=".$_GET['fib']:"")." ORDER BY name, fib ;";
		$result = pg_query($sql);
		if (pg_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span5">Наименование</td>';
			$content.='<td class="span1">Волокон</td>';
			$content.='<td class="span6">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = pg_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['name'].'</td>';
				$content.='<td '.(pg_result(pg_query("SELECT COUNT(*) AS total FROM " . $table_fiber_type . " WHERE cable_id=".$row['id']." AND mod_color IS NOT NULL AND fib_color IS NOT NULL;"),0)!=$row['fib']?'class="bg-color-orangeDark"':'').'>'.$row['fib'].'&nbsp;</td>';
				$content.='<td>'.$row['descrip'].'&nbsp;</td>';
				$content.='
				<td class="toolbar m0">
    				<button class="icon-pencil m0 mini" id="cable_edit_in_div" rel="?act=e_cable_type&id='.$row['id'].'" title="Редактировать"></button>
    				<button class="icon-yelp m0 mini" onClick="window.location.href=\'?act=dirs&dir=cable_color&id='.$row['id'].'\';" title="Редактировать цвета"></button>
    				<button class="icon-cancel-2 m0 mini" id="d_cable" rel="?act=d_cable_type&id='.$row['id'].'" title="Удалить"></button>
				</td>';
				$content.='</tr>';
				$i++;
			}
			$content.='</table>';
		}
	}

// редактирование цветов волокон кабеля
	if($_GET['dir'] == 'cable_color' && is_numeric($_GET['id']) && $group_access['dirs']) {
		$i=1;
		$title.= ' > Цвета кабеля';
		$action.='
		<div class="span2 m5 input-control text">Цвета кабеля</div>';

		$cable=pg_fetch_assoc(pg_query("SELECT * FROM " . $table_cable_type . " WHERE id=".$_GET['id'].";"));
		
		// добавлять волокна в тип кабеля если их нет
		if(pg_result(pg_query("SELECT COUNT(*) AS total FROM " . $table_fiber_type . " WHERE cable_id=".$_GET['id'].";"),0)==0) {
			$o = 1;
			while ($o <= $cable['fib']) {
				//if(!pg_result(pg_query("SELECT * AS total FROM " . $table_fiber_type . " WHERE cable_id=".$_GET['id']." AND num = ".$o.";"),0))
				if(!pg_result(pg_query("SELECT * FROM " . $table_fiber_type . " WHERE cable_id=".$_GET['id']." AND num = ".$o.";"),0))
					pg_query("INSERT INTO ".$table_fiber_type." (cable_id,num,user_id) VALUES (".$_GET['id'].", ".$o.", ".$user_id.");");
				$o++;
			}
		}

		$sql = "SELECT ft1.*,col1.name AS mod_name, col1.color AS mod_color, col1.stroke AS mod_stroke, col2.name AS fib_name, col2.stroke AS fib_stroke, col2.color AS fib_color
				FROM ".$table_fiber_type." AS ft1
						LEFT JOIN ".$table_color." AS col1 ON ft1.mod_color = col1.id AND col1.type = 0
						LEFT JOIN ".$table_color." AS col2 ON ft1.fib_color = col2.id AND col2.type = 1
				WHERE ft1.cable_id = ".$_GET['id']."
				ORDER BY ft1.num;";
		$content='<input type="hidden" id="color_select" value="">';
		$content.='<input type="hidden" id="fiber_type" value="1">';
		$result = pg_query($sql);
			$content.='<table class="striped">
						<tr>
							<td class="span4">Кабеля: '.$cable['name'].'</td>
							<td class="span1">ОВ: '.$cable['fib'].'</td>
							<td class="span9">Описание: '.$cable['descrip'].'&nbsp;</td>
							<td class="span1"><button class="m0" onClick="window.location.href=\'?act=dirs&dir=cable_type\'" title="Вернуться назад">Назад</button></td>
						</tr>
						<tr>
							<td colspan=4>';
			$content.='<table class="striped">';
			$content.='
				<tr>
					'.($pq_type!=1?'<td class="span1">Порт</td>':'').'
					<td class="span1 span1_5" colspan=2>ОВ[м/в]</td>
				</tr>';
			//$content.='<tr>';
			while ($row = pg_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';

				$fiber_id = $row['id'];
				$fiber_mod_color = $row['mod_color'];
				$fiber_fib_color = $row['fib_color'];

				$content.='<td class="m5 color span5" '.($group_access['fiber_edit']?'id="color_mod_'.$fiber_id.'" rel_id="'.$fiber_id.'" rel_type="mod" ':'').'style="background-color: #'.$fiber_mod_color.';" title="Цвет модуля: '.($row['mod_name']?$row['mod_name']:'не задан').'">'.($row['mod_name']?$row['mod_name'].'&nbsp;':'&nbsp;').($row['mod_stroke']?'/':'&nbsp;').'</td>';
				$content.='<td class="m5 color span5" '.($group_access['fiber_edit']?'id="color_fib_'.$fiber_id.'" rel_id="'.$fiber_id.'" rel_type="fib" ':'').'style="background-color: #'.$fiber_fib_color.';" title="Цвет волокна: '.($row['fib_name']?$row['fib_name']:'не задан').'">'.($row['mod_name']?$row['fib_name'].'&nbsp;':'&nbsp;').($row['fib_stroke']?'/':'&nbsp;').'</td>';

				$content.='</tr>';
				$i++;
			}
			$content.='</table>
					</td>
				</tr>
			</table>';
//		}	
	}

// редактирование типов коммутаторов
    if($_GET['dir'] == 'switch_type' && $group_access['dirs']) {
        $i=1;
        $title.= ' > Коммутаторы';
        $action.='
        <div class="span2 m5 input-control text">Коммутаторы</div>
        <div class="span m0 text-left">
        <button class="m0" id="in_div" rel="?act=n_switch_type" type="button" />Добавить Коммутатор</button>
        </div>';
        $sql = "SELECT * FROM ".$table_switch_type." ORDER BY name";
        $result = pg_query($sql);
        if (pg_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span4">Название</td>';
            $content.='<td class="span1 span1_5">Портов</td>';
            $content.='<td class="span1 span1_5">Юнитов</td>';
            $content.='<td class="span1 span1_5">Мощность</td>';
            $content.='<td class="span7">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = pg_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['ports_num'].'</td>';
                $content.='<td>'.$row['unit'].'&nbsp;</td>';
                $content.='<td>'.$row['power'].'&nbsp;</td>';
                $content.='<td>'.$row['descrip'].'&nbsp;</td>';
                $content.='
                <td class="toolbar m0">
                    <button class="icon-pencil m0 mini" id="switch_type_edit_in_div" rel="?act=e_switch_type&id='.$row['id'].'" title="Редактировать"></button>
                    <button class="icon-cancel-2 m0 mini" id="switch_type_del_in_div" rel="?act=d_switch_type&id='.$row['id'].'" title="Удалить"></button>
                </td>';
                $content.='</tr>';
                $i++;
            }
            $content.='</table>';
        }
    }

// редактирование типов медиаконвертеров
    if($_GET['dir'] == 'mc_type' && $group_access['dirs']) {
        $i=1;
        $title.= ' > Медиаконвертеры';
        $action.='
        <div class="span2 m5 input-control text">Медиаконвертеры</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_mc_type" type="button" />Добавить Медиаконвертер</button>
        </div>';
        $sql = "SELECT * FROM ".$table_mc_type." ORDER BY name";
        $result = pg_query($sql);
        if (pg_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span4">Название</td>';
            $content.='<td class="span1 span1_5">Мощность</td>';
            $content.='<td class="span7">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = pg_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['power'].'&nbsp;</td>';
                $content.='<td>'.$row['descrip'].'&nbsp;</td>';
                $content.='
                <td class="toolbar m0">
                    <button class="icon-pencil m0 mini" id="mc_type_edit_in_div" rel="?act=e_mc_type&id='.$row['id'].'" title="Редактировать"></button>
                    <button class="icon-cancel-2 m0 mini" id="mc_type_del_in_div" rel="?act=d_mc_type&id='.$row['id'].'" title="Удалить"></button>
                </td>';
                $content.='</tr>';
                $i++;
            }
            $content.='</table>';
        }
    }

// редактирование типов узлов
    if($_GET['dir'] == 'node_type' && $group_access['dirs']) {
    	$i=1;
    	$title.= ' > Типы узлов';
    	$action.='
        <div class="span2 m5 input-control text">Типы узлов</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_node_type" type="button" />Добавить тип узла</button>
        </div>';
    	$sql = "SELECT * FROM ".$table_node_type." ORDER BY name";
    	$result = pg_query($sql);
    	if (pg_num_rows($result)) {
    		$content='<table class="striped">';
    		$content.='<tr>';
    		$content.='<td class="span1">№</td>';
    		$content.='<td class="span4">Название</td>';
    		$content.='<td class="span7">Описание</td>';
    		$content.='<td class="span2">&nbsp;</td>';
    		$content.='</tr>';
    		$content.='<tr>';
    		while ($row = pg_fetch_assoc($result)) {
    			$content.='<td>'.$i.'</td>';
    			$content.='<td>'.$row['name'].'</td>';
    			$content.='<td>'.$row['descrip'].'&nbsp;</td>';
    			$content.='
                <td class="toolbar m0">
                    <button class="icon-pencil m0 mini" id="node_type_edit_in_div" rel="?act=e_node_type&id='.$row['id'].'" title="Редактировать"></button>
                    <button class="icon-cancel-2 m0 mini" id="node_type_del_in_div" rel="?act=d_node_type&id='.$row['id'].'" title="Удалить"></button>
                </td>';
    			$content.='</tr>';
    			$i++;
    		}
    		$content.='</table>';
    	}
    }

// редактирование типов рам/ящиков
    if($_GET['dir'] == 'box_type' && $group_access['dirs']) {
        $i=1;
        $title.= ' > Ящики/Рамы';
        $action.='
        <div class="span2 m5 input-control text">Ящики/Рамы</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_box_type" type="button" />Добавить Ящик/Раму</button>
        </div>';
        $sql = "SELECT * FROM ".$table_box_type." ORDER BY name";
        $result = pg_query($sql);
        if (pg_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span4">Название</td>';
            $content.='<td class="span2">Юнитов</td>';
            $content.='<td class="span7">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = pg_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['unit'].'&nbsp;</td>';
                $content.='<td>'.$row['descrip'].'&nbsp;</td>';
                $content.='
                <td class="toolbar m0">
                    <button class="icon-pencil m0 mini" id="box_type_edit_in_div" rel="?act=e_box_type&id='.$row['id'].'" title="Редактировать"></button>
                    <button class="icon-cancel-2 m0 mini" id="box_type_del_in_div" rel="?act=d_box_type&id='.$row['id'].'" title="Удалить"></button>
                </td>';
                $content.='</tr>';
                $i++;
            }
            $content.='</table>';
        }
    }
// редактирование типов ИБП
    if($_GET['dir'] == 'ups_type' && $group_access['dirs']) {
        $i=1;
        $title.= ' > ИБП';
        $action.='
        <div class="span2 m5 input-control text">ИБП</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_ups_type" type="button" />Добавить ИБП</button>
        </div>';
        $sql = "SELECT * FROM ".$table_ups_type." ORDER BY name";
        $result = pg_query($sql);
        if (pg_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span6">Название</td>';
            $content.='<td class="span2">Юнитов</td>';
            $content.='<td class="span2">Мощьность</td>';
            $content.='<td class="span7">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = pg_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['unit'].'&nbsp;</td>';
                $content.='<td>'.$row['power'].'&nbsp;</td>';
                $content.='<td>'.$row['descrip'].'&nbsp;</td>';
                $content.='
                <td class="toolbar m0">
                    <button class="icon-pencil m0 mini" id="ups_type_edit_in_div" rel="?act=e_ups_type&id='.$row['id'].'" title="Редактировать"></button>
                    <button class="icon-cancel-2 m0 mini" id="ups_type_del_in_div" rel="?act=d_ups_type&id='.$row['id'].'" title="Удалить"></button>
                </td>';
                $content.='</tr>';
                $i++;
            }
            $content.='</table>';
        }
    }
// редактирование типов прочего оборудования
    if($_GET['dir'] == 'other_type' && $group_access['dirs']) {
        $i=1;
        $title.= ' > Разное';
        $action.='
        <div class="span2 m5 input-control text">Разное</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_other_type" type="button" />Добавить разное</button>
        </div>';
        $sql = "SELECT * FROM ".$table_other_type." ORDER BY name";
        $result = pg_query($sql);
        if (pg_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span4">Название</td>';
            $content.='<td class="span2">Юнитов</td>';
            $content.='<td class="span7">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = pg_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['unit'].'&nbsp;</td>';
                $content.='<td>'.$row['descrip'].'&nbsp;</td>';
                $content.='
                <td class="toolbar m0">
                    <button class="icon-pencil m0 mini" id="other_type_edit_in_div" rel="?act=e_other_type&id='.$row['id'].'" title="Редактировать"></button>
                    <button class="icon-cancel-2 m0 mini" id="other_type_del_in_div" rel="?act=d_other_type&id='.$row['id'].'" title="Удалить"></button>
                </td>';
                $content.='</tr>';
                $i++;
            }
            $content.='</table>';
        }
    }

// редактирование типов цвета модулей
        if($_GET['dir'] == 'mod_color' && $group_access['dirs']) {
        	$i=1;
        	$title.= ' > Цвета модулей';
        	$action.='
	        <div class="span2 m5 input-control text">Цвета модулей</div>
	        <div class="span m0 text-left">
	            <button class="m0" id="in_div" rel="?act=n_color&type=0" type="button" />Добавить цвет модуля</button>
	        </div>';
        	$sql = "SELECT * FROM ".$table_color." WHERE type=0 ORDER BY name";
        	$result = pg_query($sql);
        	$content.='<a href="http://vvz.nw.ru/Lessons/HTML_Colors/HTMLcolors_HSB.htm" target="_blank">Цвета</a><br>';
        	if (pg_num_rows($result)) {
        		$content.='<table class="striped">';
        		$content.='<tr>';
        		$content.='<td class="span1">№</td>';
        		$content.='<td class="span4">Название</td>';
        		$content.='<td class="span2">Цвет</td>';
        		$content.='<td class="span1">&nbsp;</td>';
        		$content.='<td class="span7">Описание</td>';
        		$content.='<td class="span2">&nbsp;</td>';
        		$content.='</tr>';
        		$content.='<tr>';
        		while ($row = pg_fetch_assoc($result)) {
        			$content.='<td>'.$i.'</td>';
        			$content.='<td>'.$row['name'].'</td>';
        			$content.='<td>'.$row['color'].'</td>';
        			$content.='<td class="color" style="background: #'.$row['color'].'">'.($row['stroke']?'/':'&nbsp;').'</td>';
        			$content.='<td>'.$row['descrip'].'&nbsp;</td>';
        			$content.='
                <td class="toolbar m0">
                    <button class="icon-pencil m0 mini" id="color_edit_in_div" rel="?act=e_color&id='.$row['id'].'&type=0" title="Редактировать"></button>
                    <button class="icon-cancel-2 m0 mini" id="color_del_in_div" rel="?act=d_color&id='.$row['id'].'&type=0" title="Удалить"></button>
                </td>';
        			$content.='</tr>';
        			$i++;
        		}
        		$content.='</table>';
        		$content='<div id="colorpickerHolder" style="display: none;"></div>'.$content;
        	}
    }

// редактирование типов цвета волокон
    if($_GET['dir'] == 'fib_color' && $group_access['dirs']) {
    	$i=1;
    	$title.= ' > Цвета волокон';
    	$action.='
        <div class="span2 m5 input-control text">Цвета волокон</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_color&type=1" type="button" />Добавить цвет волокна</button>
        </div>';
    	$sql = "SELECT * FROM ".$table_color." WHERE type=1 ORDER BY name";
    	$result = pg_query($sql);
    	$content.='<a href="http://vvz.nw.ru/Lessons/HTML_Colors/HTMLcolors_HSB.htm" target="_blank">Цвета</a><br>';
    	if (pg_num_rows($result)) {
    		$content.='<table class="striped">';
    		$content.='<tr>';
    		$content.='<td class="span1">№</td>';
    		$content.='<td class="span4">Название</td>';
    		$content.='<td class="span2">Цвет</td>';
    		$content.='<td class="span1">&nbsp;</td>';
    		$content.='<td class="span7">Описание</td>';
    		$content.='<td class="span2">&nbsp;</td>';
    		$content.='</tr>';
    		$content.='<tr>';
    		while ($row = pg_fetch_assoc($result)) {
    			$content.='<td>'.$i.'</td>';
    			$content.='<td>'.$row['name'].'</td>';
    			$content.='<td>'.$row['color'].'</td>';
    			$content.='<td class="color" style="background: #'.$row['color'].'">'.($row['stroke']?'/':'&nbsp;').'</td>';
    			$content.='<td>'.$row['descrip'].'&nbsp;</td>';
    			$content.='
                <td class="toolbar m0">
                    <button class="icon-pencil m0 mini" id="color_edit_in_div" rel="?act=e_color&id='.$row['id'].'&type=1" title="Редактировать"></button>
                    <button class="icon-cancel-2 m0 mini" id="color_del_in_div" rel="?act=d_color&id='.$row['id'].'&type=1" title="Удалить"></button>
                </td>';
    			$content.='</tr>';
    			$i++;
    		}
    		$content.='</table>';
    	}
    }

// редактирование пользователей
    if($_GET['dir'] == 'users' && $_SESSION['user_type']==0 && $group_access['dirs']) {
    	$i=1;
    	$title.= ' > Пользователи';
    	$action='
		<div class="span2 m5 input-control text">Пользователи</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_user" type="button" />Добавить пользователя</button>
		</div>';
    	$sql = "SELECT * FROM " . $table_user . " AS u1 ORDER BY id,name;";
    	$result = pg_query($con_id, $sql);
    	if (pg_num_rows($result)) {
    		$content='<table class="striped">';
    		$content.='<tr>';
    		$content.='<td class="span1">№</td>';
    		$content.='<td class="span4">Логин</td>';
    		$content.='<td class="span4">Имя</td>';
    		$content.='<td class="span4">Группа</td>';
    		$content.='<td class="span2">&nbsp;</td>';
    		$content.='</tr>';
    		$content.='<tr>';
    		while ($row = pg_fetch_assoc($result)) {
    			$content.='<td>'.$i.'</td>';
    			$content.='<td'.($row['status']?'':' style="background: silver;"').'>'.$row['login'].'&nbsp;</td>';
    			$content.='<td>'.$row['name'].'&nbsp;</td>';
    			$content.='<td>'.($group[$row['group']]['name']?$group[$row['group']]['name']:$row['group']).'&nbsp;</td>';
    			$content.='
				<td class="toolbar m0">
					<button class="icon-pencil m0 mini" id="area_edit_in_div" rel="?act=e_user&id='.$row['id'].'" title="Редактировать"></button>
					<button class="icon-cancel-2 m0 mini" id="area_del_in_div" rel="?act=d_user&id='.$row['id'].'" title="Удалить"></button>
				</td>';
    			$content.='</tr>';
    			$i++;
    		}
    		$content.='</table>';
    	}
    }

// вывод лога
    if($_GET['dir'] == 'log' && $group_access['dirs']) {
    	$i=1;
    	$title.= ' > логи';
    	$action='
		<div class="span2 m5 input-control text">логи</div>
		<div class="span m0 text-left">
			<!--<button class="m0" id="in_div" rel="?act=n_user" type="button" />Добавить пользователя</button>-->
		</div>';
    	
    	$sql_count = "SELECT COUNT(*) FROM ".$table_log." AS n1";
    	$total_rows=pg_fetch_row(pg_query($sql_count));
    	
    	//$sql_count_map = "SELECT COUNT(*) FROM ".$table_node." AS n1, ".$table_street_name." AS s_name WHERE n1.street_id = s_name.id AND n1.the_geom IS NULL " . $find_node;
    	//$total_rows_map=pg_fetch_row(pg_query($sql_count_map));
    	$num_pages=ceil($total_rows[0]/$per_page);
    	
    	if(isset($_GET['page'])) $page=($_GET['page']-1); else $page=0;
    	$start=abs($page*$per_page);
    	$i=$i+$start;
    	
    	$find='';
    	$pages='';
    	if(isset($_GET['find_node'])) $find='&find_node='.clean($_GET['find_node']);
    	for($a=1;$a<=$num_pages;$a++) {
    		if ($a-1 == $page) {
    			$pages.='<div class="b_m">'.$a.'</div>';
    		} else {
    			$pages.='<div class="b_m"><a class="b_m_a" href="?act=dirs&dir=log'.$find.'&page='.$a.'">'.$a.'</a></div>';
    		}
    	}
    	$pages='<div class="text-center">
		    	<div class="b_m">Страницы:</div>
		    		'.$pages.//'<div class="b_m">всего: '.$total_rows[0].'</div>
				'</div><br>';
    	
    	$content='';
    	if($num_pages>1) $content=$pages;
    	
    	$sql = "SELECT l1.*,u1.name FROM " . $table_log . " AS l1 LEFT JOIN ".$table_user." AS u1 ON l1.user_id = u1.id LIMIT $per_page OFFSET $start;";
    	$result = pg_query($con_id, $sql);
    	if (pg_num_rows($result)) {
    		$content.='<table class="striped">';
    		$content.='<tr>';
    		$content.='<td class="span1">№</td>';
    		$content.='<td class="span2">Таблица</td>';
    		$content.='<td class="span1">id</td>';
    		$content.='<td class="span7">Данные</td>';
    		$content.='<td class="span2">Пользователь</td>';
    		$content.='</tr>';
    		$content.='<tr>';
    		while ($row = pg_fetch_assoc($result)) {
    			$content.='<td>'.$i.'</td>';
    			$content.='<td>'.$row['table_name'].'&nbsp;</td>';
    			$content.='<td>'.$row['table_id'].'&nbsp;</td>';
    			$content.='<td>';
	    			$t=false;
	    			foreach ( unserialize($row['data_old']) AS $key => $value) {
	    				$content.=($t?'<br>':'').$key.'="'.$value.'"';
	    				$t=true;
	    			}
    			$content.='&nbsp;</td>';
    			$content.='<td>'.$row['name'].'&nbsp;</td>';
    			$content2.='
				<td class="toolbar m0">
					<button class="icon-pencil m0 mini" id="area_edit_in_div" rel="?act=e_user&id='.$row['id'].'" title="Редактировать"></button>
					<button class="icon-cancel-2 m0 mini" id="area_del_in_div" rel="?act=d_user&id='.$row['id'].'" title="Удалить"></button>
				</td>';
    			$content.='</tr>';
    			$i++;
    		}
    		$content.='</table>';
    	}
    }
    show_menu();
    die;
}

// вывод всех объектов в стадии строительства
if(@$_GET['act'] == 'u_const' && $group_access['u_const']) {
	$i=1;
	$title.= ' > Список объектов в стадии строительства';

	// запрос
	$sql = "SELECT *, to_char(date + '7 hour'::interval, 'DD.MM.YYYY') AS date
		FROM ".$table_node." AS n1
		WHERE n1.u_const=true
		ORDER BY n1.date, n1.address_full ASC";
	/*echo '<pre>';
	 print_r($sql);
	echo '<pre>';*/
	$result = pg_query($sql);

	if (pg_num_rows($result)) {
		$content.='<table class="striped">';
		$content.='<td class="span1">№</td>';
		$content.='<td class="span8">Адрес</td>';
		$content.='<td class="span3">Дата</td>';
		$content.='<td class="span2">&nbsp;</td>';
		$content.='</tr>';
		while ($row = pg_fetch_assoc($result)) {

			$content.='<td class="span1">'.$i.'.</td>';
			$content.='<td class="span8">'.$row['address_full'].'</td>';
			$content.='<td class="span3">'.$row['date'].'</td>';
			//($_SESSION['group']==0 || $user_id == 2 || $user_id == 4?'<div class="m5 span_text" style="color: #f1f1f1;" >узел создал: '.$user.'</div>':'').'</div>
			$content.='<td class="span2"><button class="icon-pencil mini m0" id="u_const_'.$row['id'].'" title="Изменить состояние"></button></td>';
			$content.='</tr>';

			$i++;
		}
		$content.='</table>';
		//$content.='<script type="text/javascript">'.$content_js.'</script>';
	}
	show_menu();
}

// редактирование настроек пользователя
if(@$_GET['act'] == 'settings') {
	$i=1;
	$title.= ' > Настройки';
	$action='
		<div class="span2 m5 input-control text">Настройки</div>';
	
	$user_data=pg_fetch_assoc(pg_query("SELECT * FROM ".$table_user." WHERE id = ".$user_id ));

	$content.='<div class="grid">
					<div class="row">
						<div class="grid">
							Настройка пароля для входа в систему:
						</div>
					</div>
					<div class="row">
						<input type="hidden" id="user_id" value="'.$user_id.'">
						<input type="hidden" id="old_pass_md5" value="'.pg_result(pg_query("SELECT password FROM ".$table_user." WHERE id=".$user_id),0).'">
						<div class="span3 input-control left"><input type="password" id="old_pass" value="" placeholder="Старый пароль" /></div>
						<div class="span3 input-control left"><input type="password" id="new_pass" value="" placeholder="Новый пароль" /></div>
						<div class="span3 input-control left"><input type="password" id="new_pass2" value="" placeholder="Повторить новый пароль" /></div>
						<div class="offset12"><button class="icon-checkmark" id="change_pass" title="Изменить"></button></div>
					</div>
				</div>';
	show_menu();
	die;
}

// вывод списка кабелей в кроссе
if (isset($_GET['act']) && ( $_GET['act'] == 's_cable' || $_GET['act'] == 's_ports' || $_GET['act'] == 's_ports_print' || $_GET['act'] == 's_ports_test' ) && is_numeric($_GET['pq_id']) && $group_access['cable']) {
    $i=1;
    // id кросса/муфты
    $pq_id = clean($_GET['pq_id']);
    // навигация
    $sql="SELECT n1.id AS id, pt.type AS type, p1.num AS num, LEFT(p1.descrip, 15) AS descrip, ST_X(ST_AsText(n1.the_geom)) AS x, ST_Y(ST_AsText(n1.the_geom)) AS y FROM ".$table_pq." AS p1 , ".$table_node." AS n1, ".$table_pq_type." AS pt WHERE p1.node = n1.id AND p1.id=" . $_GET['pq_id'] . " AND p1.pq_type_id = pt.id;";
    $result = pg_fetch_assoc(pg_query($sql));
    //print_r($result);
    // id узла
    $node_id = $result['id'];
    // координаты узла
    $x = $result['x'];
    $y = $result['y'];
    // тип и номер кросса/муфты
    $pq_type = $result['type'];
    $pq_num = $result['num'];
	$address=(isset($node_id)?addr_id_full($node_id):"");
	$descrip=(!empty($result['descrip'])?' "'.$result['descrip'].'"':'');
    
	if ($result['type'] == 0) $type = 'Кросс'; else if ($result['type'] == 1) $type = 'Муфта'; else $type = 'Медный';
    if (isset($result['num'])) $num = ' №' . $result['num']; else $num = '';

    $action='<div class="m0 text-left">
     <div class="horizontal-menu">
	    <ul>
		    <li><a href="?act=s_pq&o_node&node_id='.$node_id.'">'.$address.' ('.$type.$num.') '.$descrip.'</a></li>
			    '.($result['type']==0?'<li'.($_GET['act']=='s_cable'?' class="border-color-blueLight"':'').'><a href="?act=s_cable&pq_id='.$pq_id.'">Кабеля</a></li><li'.($_GET['act']=='s_ports'?' class="border-color-blueLight"':'').'><a href="?act=s_ports&pq_id='.$pq_id.'">Порты</a></li>':'').'
			    <li><a href="?act=s_ports_print&pq_id='.$pq_id.'" target="_blank">Печать</a></li>
			    <li><a href="?act=s_cable&pq_id='.$pq_id.(isset($_GET['used'])?'':'&used').'">'.(isset($_GET['used'])?'Убрать занятые волокна':'Занятые волокна').'</a></li>
			    <li><a href="engine/backend.php?act=get_pq_img&pq_id='.$pq_id.'" target="_blank">Карта волокон</a></li>
		    </li>
	    </ul>
    </div>
    </div>';
    if($_GET['act'] == 's_cable')
    {
    	$tt=microtime(1);
    	$title='Узлы > '.$address.' > '.$type.$num.' > '.'Кабеля';
    	if ($group_access['cable_add'])
    		$action=($group_access['cable_add']?'
    		<div class="span2 m0 text-left">
    			<button class="m0" id="cable_add_div" rel="?act=n_cable&pq_id=' . clean($_GET['pq_id']) .($group_access['prompt']?'&prompt=1':''). '" />Добавить кабель</button>
    		</div>':'').$action;
	    // скрытые поля
		$content='<input type="hidden" id="node_id" value="' . $node_id . '">';
	    $content.='<input type="hidden" id="pq_id" value="' . clean($_GET['pq_id']) . '">';
	    $content.='<input type="hidden" id="pq_type" value="' . $pq_type . '">';
	    $content.='<input type="hidden" type="text" id="pq_num" value="' . $pq_num . '">';
	    // запрос
/*	    $sql = "SELECT a.id,
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
    				AND b2.pq_type_id = pt2.id";
*/
// сортировка кабелей по портам
	    //$sql = "SELECT aa.*, ff.* FROM (
	    $sql = "SELECT aa.* FROM (
	    			SELECT a.id,
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
	        	) AS aa
	    			LEFT JOIN (
	    				SELECT f.cable_id, MIN(fcc.port) AS fmin FROM
	    					fibers.fiber AS f, fibers.cruz_conn AS fcc
						WHERE
							f.id = fcc.fiber_id AND pq_id = ".clean($_GET['pq_id'])."
	    				GROUP BY f.cable_id
	    			) AS ff
	    		ON aa.id = ff.cable_id
	    		ORDER BY ff.fmin";

	    //print_r('<pre>'.$sql.'</pre>');	    
	    
	    $content.='<input type="hidden" id="color_select" value="">';

	    $result = pg_query($sql);

	    if (pg_num_rows($result)) {
	    	$content.='<table class="striped">';
	        while ($row = pg_fetch_assoc($result)) {

				//$fib='';
	            if ($row['type_1'] == 0) $type_1 = 'Кросс'; else if ($row['type_1'] == 1) $type_1 = 'Муфта'; else $type_1 = 'Медный';
	            if (isset($row['num_1'])) $num_1 = ' №' . $row['num_1']; else $num_1 = '';
				if ($row['type_2'] == 0) $type_2 = 'Кросс'; else if ($row['type_2'] == 1) $type_2 = 'Муфта'; else $type_2 = 'Медный';
	            if (isset($row['num_2'])) $num_2 = ' №' . $row['num_2']; else $num_2 = '';
	
	            // меняем местами адрема узлов для удобства, вначале всегда выбранного узла
	            if (isset($_GET['pq_id']) && $_GET['pq_id'] == $row['pq_2']) {
	                $to_id = $row['pq_1'];
	                $from_id = $row['pq_2'];
	                $to_addr = $row['addr_1'] . ' (' . $type_1 . $num_1 . ')';
	            } else {
	                $to_id = $row['pq_2'];
	                $from_id = $row['pq_1'];
	                $to_addr = $row['addr_2'] . ' (' . $type_2 . $num_2 . ')';
	            }
	
	            $sql_node = "SELECT * FROM " . $table_pq . " AS p1 WHERE p1.id = " . $to_id;
	            $result_node = pg_fetch_assoc(pg_query($sql_node));
	            $to_node = $result_node['node'];

	            $sql_node2 = "SELECT * FROM " . $table_pq . " AS p1 WHERE p1.id = " . $to_id;
	            $result_node2 = pg_fetch_assoc(pg_query($sql_node2));
	            
	            $sql_descrip = "SELECT LEFT(descrip, 15) AS descrip FROM " . $table_pq . " AS p1 WHERE p1.id = " . $to_id;
	            $result_descrip = pg_fetch_assoc(pg_query($sql_descrip));
	            $descrip=(!empty($result_descrip['descrip'])?' "'.$result_descrip['descrip'].'"':'');
	            
	            //echo '<pre>';
	            //print_r($result_node);
	            //echo '</pre>';
	            //echo $sql_node.'<br>'.$to_id.' '.$from_id.'<br>';
	            $content.='<tr>';

	            if(isset($_GET['cable_id']) && $_GET['cable_id']==$row['id']) {
	            	$bb='<u>';
	            	$be='</u>';
	            } else {
	            	$bb='';
	            	$be='';
	            }
	            
	            $content.='<td class="span1">'.$bb.$i.$be.'.</td>';
	            $content.='<td class="span6">'.$bb.' до <a href="?act=s_cable&pq_id='.$to_id.'&cable_id='.$row['id'].'">'.$to_addr.$descrip.'</a>'.$be.'</td>';
	            $content.='<td class="span4">'.$bb.'тип: '.$row['cable_name'].$be.'</td>';
				$content.='<td class="span2">';
	            if ($group_access['cable_edit'])
	            	$content.='<button class="icon-move mini m0" id="move_cable_div" rel="?act=e_cable&pq_id='.clean($_GET['pq_id']).'&cable_id='.$row['id'].'" title="Переместить" /></button>&nbsp;';
	            if ($group_access['cable_del'])
	            	$content.='<button class="icon-cancel-2 mini m0" id="del_cable_div" rel="?act=d_cable&pq_id='.clean($_GET['pq_id']).'&cable_id='.$row['id'].'" title="Удалить"/></button>&nbsp;';
	            if ($group_access['cable_geom'])
	            	$content.='<button class="icon-joomla mini m0" id="del_cable_div" rel="?act=d_cable_geom&cable_id='.$row['id'].'&to_addr='.$to_addr.'" title="Сбросить координаты"/></button>&nbsp;';
	            if ($group_access['fiber_edit'])
	            	$content.='<button class="icon-yelp mini m0" id="cable_fiber_color" rel_cable_id='.$row['id'].' title="Применить цвета кабеля"></button>';
				$content.='</td>';
	            $content.='</tr>';
	            // если задан кросс/муфта, то выводим список волокон в кабелях
	            $sql="SELECT a.id AS id, a.num AS num, g.id AS to_pq_id, e.cable_id AS to_cable_id, e.id AS to_id, e.num AS to_num,
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
						";#GROUP BY a.num";
	            //print_r('<pre>'.$sql.'</pre>');
	            
	            $result_fib = pg_query($sql);
	            $content.='</tr>';
	                if (pg_num_rows($result_fib)) {
	                    $content.='<tr><td colspan="4"><table>';
						$content.='
	    					<tr>
	    					   '.($pq_type!=1?'<td class="span1">Порт</td>':'').'
	    					   <td class="span1 span1_5" colspan=3>ОВ[м/в]</td>
	    					   '.($pq_type!=1?'<td class="span2">Кросс/муфта</td><td class="span5">Кабель</td>':'<td class="span7">Кабель</td>').'
	    					   <td class="span1 span1_5">ОВ'.($pq_type!=1?' [порт]':'').'</td>
	    					   <td class="span2" colspam="2">&nbsp;</td>
	                    	</tr>';
						$content.='<tr>';

						//echo pg_num_rows($result_fib);
						
						$cable_array = array();
	                    while ($row_fib = pg_fetch_assoc($result_fib)) {
	                    	//print_r($row_fib);
	                        $from_fib = $row_fib['num'];
	                        ////////////$fiber_id = $row_fib['id'];
	                        $fiber_id = $row_fib['id'];
	                        //echo $row_fib['id'].'<br>';

	                        $fiber_mod_color = $row_fib['mod_color'];

	                        $fiber_fib_color = $row_fib['fib_color'];
	                        $cable_id = $row_fib['to_cable_id'];
	                        if($row_fib['port_id']) $port_id = $row_fib['port_id']; else $port_id=0;
	                        //echo 'cable: '.$cable_id.' port: '.$port_id."<br>";
                            //echo '<pre>';
                            //print_r($row_fib);
                            //echo '</pre>';
	                        $to_fiber_id = $row_fib['to_id'];
	                        // если pq_id в базе есть, берём из базы, иначе с GET
	                        if ($row_fib['to_pq_id'] > 0) {
	                            $pq_id_ = $row_fib['to_pq_id'];
	                        } else {
	                            $pq_id_ = clean($_GET['pq_id']);
	                        }
	                        // выбор порта для соединения с волокном
	                        if($pq_type!=1)
							$content.='
	                            <td class="text-left input-control text m0">'.
	                            	get_port_select($fiber_id,clean($_GET['pq_id']),(!$group_access['port_edit'] || ($port_id!=0 && $to_fiber_id ) || ($port_id==0 && $to_fiber_id )?true:false)).'
								</td>';
	                        //////////////
	                        $used=false;
	                        if(isset($_GET['used'])) {
	                        	//$to_port = find_end_port($node_id,0,$fiber_id,true);
	                        	$to_port = find_end_port($node_id,0,$fiber_id,true);
	                        	//function fib_find_used($id,$last_id,$to_node_id,$pq_id) {
	                        	//echo '<pre>';
	                        	//print_r($to_port);
	                        	//echo '<pre>';
	                        	//$content.='<td>';
	                        	//$content.=$to_port['port_used'];
	                        	//$content.='</td>';
	                        	$used=$to_port['port_used']; 
	                        }

	                        $content.='<td class="m5'.($used?' border-color-red':'').'">'.$from_fib.'</td>';
	                        $content.='<td class="m5 color" '.($group_access['fiber_edit']?'id="color_mod_'.$fiber_id.'" rel_id="'.$fiber_id.'" rel_type="mod" ':'').'style="background-color: #'.$fiber_mod_color.';" title="Цвет модуля: '.($row_fib['mod_name']?$row_fib['mod_name']:'не задан').'">'.($row_fib['mod_stroke']?'/':'&nbsp;').'</td>';
	                        $content.='<td class="m5 color" '.($group_access['fiber_edit']?'id="color_fib_'.$fiber_id.'" rel_id="'.$fiber_id.'" rel_type="fib" ':'').'style="background-color: #'.$fiber_fib_color.';" title="Цвет волокна: '.($row_fib['fib_name']?$row_fib['fib_name']:'не задан').'">'.($row_fib['fib_stroke']?'/':'&nbsp;').'</td>';

							$content.='<input type="hidden" id="pq_id_' . $fiber_id . '" value="' . $pq_id_ . '">';
							$content.='<input type="hidden" id="to_node_' . $fiber_id . '" value="' . $to_node . '">';
	                        // селект (выбор кросса/муфты)
							//$group_access['cable_edit'] = 1;
							//$group_access['port_conn'] = 0;
							if($pq_type!=1) {
		                        $content.='<td class="input-control text m0">'.
		                        	get_pq_select($fiber_id,$node_id,$pq_id_,$pq_type,$pq_num,(($group_access['cable_edit'] || $group_access['port_conn']) && empty($to_fiber_id)?false:true)).'
		                        </td>';
							}
	                        // селект (выбор кабеля)
	                        $content.='<input type="hidden" value="' . $cable_id . '">';
	                        
	                        if(array_key_exists($pq_id.'_'.$cable_id, $cable_array)) {
								$select=$cable_array[$pq_id.'_'.$cable_id];
	                        } else {
		                        $select=get_cable_select($fiber_id,$pq_id_,$cable_id);
	                        	$cable_array[$pq_id.'_'.$cable_id]=$select;
	                        }

                            $content.='
	                            <td class="text-left input-control text m0">
	                            	<select class="cable" id="cable_id_' . $fiber_id . '"'.(($group_access['cable_edit'] || ($group_access['port_conn'] && !empty($port_id)) ) && empty($to_fiber_id)?'':' disabled').'>'.
	                            		$select.
	                            	'</select>
	                            </td>';
	                        $content.='<input type="hidden" value="' . $to_fiber_id . '">';
	                        $content.='
	                            <td class="text-left input-control text m0">'.
	                                get_fiber_select($fiber_id,$node_id,$pq_id_,$cable_id,$to_fiber_id,$port_id,(($group_access['cable_edit'] || $group_access['port_conn']) && empty($to_fiber_id)?false:true)).
	                        //$cable_id.'_'.$to_fiber_id.'_'.$port_id.'_'.(($group_access['cable_edit'] || $group_access['port_conn']) && empty($to_fiber_id)?0:1).
	                            '</td>';
	                        $content.='<td class="toolbar m0">';
		                        if ($group_access['cable_edit'] || ($group_access['port_conn'] && !empty($port_id))) {
		                            if ($to_fiber_id) {
		                                    $content.='<button class="icon-cancel-2 m0" id="del_fib_conn_'.$fiber_id.'" title="Удалить"></button>';
		                            } else {
		                                    $content.='<button class="icon-checkmark m0" id="new_fib_conn_'.$fiber_id.'" title="Ok"></button>';
		                            }
		                        } else {
		                        	$content.='<button class="m0"></button>';
		                        }
		                        // будет всегда выводить кнопки
		                            $content.='&nbsp;<button class="icon-share-2 m0" id="find_fib_conn_'.$fiber_id.'" title="Отследить ОВ"></button>';
		                            $content.='<button class="icon-cancel m0" id="f_fiber_clean_'.$fiber_id.'" title="Очистить" style="display:none" ></button>';
		                            $content.='&nbsp;<button class="icon-earth m0" id="show_fib_map_'.$fiber_id.'" rel="lat='.$y.'&lon='.$x.'" title="Показать на карте"></button>';
	                        $content.='</td>';


	                        $content.='
	                            <tr class="f_fiber" style="display: none;" id="f_fiber_tr_'.$fiber_id.'"><td colspan="8">
	                                <div class="f_fiber" id="f_fiber_'.$fiber_id.'"></div>
	                                <input type="hidden" type="text" id="f_fiber_pq_'.$fiber_id.'" value="'.$to_id.'">
	                            </tr>';
	                        $content.='</tr>';
	                    }
	                    $content.='</table></td></tr>';
						/*echo '<pre>';
						print_r($cable_array);
						echo '</pre>';*/
	                }
	            $i++;
	        }
	        $content.='</table>';
	        //$content.='<script type="text/javascript">'.$content_js.'</script>';
	    }
	} else if($_GET['act'] == 's_ports') {
		$content="";

		$title='Узлы > '.$address.' > '.$type.$num.' > '.'Порты';
		
		//$sql="SELECT p1.id AS id, pq_t.ports_num AS pq_type_ports FROM ".$table_pq." as p1, ".$table_pq_type." AS pq_t WHERE p1.pq_type_id = pq_t.id AND p1.type = pq_t.type AND p1.id = ".clean($_GET['pq_id']).";";
		$sql="SELECT p1.id AS id, pq_t.ports_num AS pq_type_ports
				FROM ".$table_pq." as p1, ".$table_pq_type." AS pq_t
				WHERE p1.pq_type_id = pq_t.id
				AND p1.id = ".clean($_GET['pq_id']).";";
		$result = pg_fetch_assoc(pg_query($sql));

		if ($group_access['port_edit'] && pg_result(pg_query("SELECT COUNT(*) FROM ".$table_cruz_conn." WHERE pq_id=".clean($_GET['pq_id']).";"), 0)!=$result['pq_type_ports']) {
			$action='<div class="span2 m0 text-left">
				<button class="m0" id="port_add_div" rel="?act=n_port&pq_id='.clean($_GET['pq_id']).'&all" />Добавить все порты</button>
			</div>'.$action;
		}

		$content.='<input type="hidden" id="pq_id" value="' . clean($_GET['pq_id']) . '">';

		//$sql = "SELECT *,pq.id AS id FROM ".$table_node." AS node, ".$table_pq." AS pq LEFT JOIN pq_type AS pq_t ON pq.pq_type_id = pq_t.id AND pq.type = pq_t.type WHERE pq.node=node.id AND pq.id = ".$pq_id." ORDER BY pq.node";
		$sql = "SELECT *,pq.id AS id
					FROM ".$table_node." AS node, ".$table_pq." AS pq
					LEFT JOIN ".$table_pq_type." AS pq_t ON pq.pq_type_id = pq_t.id
					WHERE pq.node=node.id AND pq.id = ".$pq_id;#." ORDER BY pq.node";
		$result = pg_query($sql);
		if (pg_num_rows($result)) {
			$content.='<table class="striped">';
			
			while ($row = pg_fetch_assoc($result)) {
				//if ($row['type'] == 0) $type = 'Кросс'; else $type = 'Муфта';
				//if (isset($row['num'])) $num = ' №' . $row['num']; else $num = '';
				$sql = "SELECT * FROM ".$table_cruz_conn." WHERE pq_id = ".$row['id']." ORDER BY port;";
				//print_r('<pre>'.$sql.'</pre>');
				$result_port = pg_query($sql);
				if (pg_num_rows($result_port)) {
					$content.='<tr>
						<td class="span1">Порт</td>
						<td class="span1">Занят</td>
						<td class="span1">ОВ</td>
						<td class="span8">Кабель</td>
						<td class="span5">Описание</td>
						<td class="span1 span1_5">&nbsp;</td>
					</tr>';
					while ($row_port = pg_fetch_assoc($result_port)) {
						$content.='<tr>';
						$content.='<td>'.$row_port['port'].'</td>';
						if ($row_port['fiber_id']) {
							/*$sql = "SELECT f1.id, f1.num, p1.type AS pq_type, p1.num AS pq_num, n1.address_full AS addr
								FROM " . $table_fiber . " AS f1, " . $table_cable . " AS c1, " . $table_pq . " AS p1, " . $table_node . " AS n1
								WHERE f1.id = " . $row_port['fiber_id'] . "
								AND f1.cable_id = c1.id
								AND p1.id = IF( c1.pq_2 = " . $pq_id . ", c1.pq_1, c1.pq_2 )
								AND n1.id = p1.node";*/
							$sql = "SELECT f1.id, f1.num, pt.type AS pq_type, p1.num AS pq_num, n1.address_full AS addr
								FROM ".$table_fiber." AS f1, ".$table_cable." AS c1, ".$table_pq." AS p1, ".$table_node." AS n1, ".$table_pq_type." AS pt
								WHERE f1.id = ".$row_port['fiber_id']."
								AND f1.cable_id = c1.id
								AND p1.id = CASE WHEN c1.pq_2 = ".$pq_id." THEN c1.pq_1 ELSE c1.pq_2 END
								AND p1.pq_type_id = pt.id
								AND n1.id = p1.node";
							// 0.032
							$row_cable_fib = pg_fetch_assoc(pg_query($sql));
							/*echo '<pre>';
							print_r($sql);
							print_r($row_cable_fib);
							echo '</pre>';*/
							if ($row_cable_fib['pq_type'] == 0) $type = 'Кросс'; else $type = 'Муфта';
							if (isset($row_cable_fib['pq_num'])) $num = ' №' . $row_cable_fib['pq_num']; else $num = '';
							$content.='<td><label class="checkbox"><input type="checkbox" '.($group_access['port_desc']?'id="port_used_'.$row_port['id'].'" ':'disabled ').($row_port['used']==true?'checked':'').'><span>&nbsp;</span></label></td>';
							$content.='<td>'.$row_cable_fib['num'].'</td>';
							$content.='<td>'.$row_cable_fib['addr'].' ('.$type.$num.')'.'</td>';
							
							if ($group_access['port_desc'])
								$content.='
									<td class="m0 input-control text"><input type="text" id="p_descrip_'.$row_port['id'].'" onchange="document.getElementById(\'p_descrip_b_'.$row_port['id'].'\').click();" value="'.$row_port['descrip'].'" placeholder="Описание" /></td>
									<td class="toolbar m0">
										<button class="icon-checkmark m0" id="p_descrip_b_'.$row_port['id'].'" title="Ok"></button>
									</td>';
							else
								$content.='<td>'.$row_port['descrip'].'</td>';
						} else {
							$content.='<td>&nbsp;</td>';
							$content.='<td>&nbsp;</td>';
							$content.='<td>&nbsp;</td>';

							if ($group_access['port_desc'])
							{
								$content.='
									<td class="m0 input-control text"><input type="text" id="p_descrip_'.$row_port['id'].'" onchange="document.getElementById(\'p_descrip_b_'.$row_port['id'].'\').click();" value="'.$row_port['descrip'].'" placeholder="Описание" /></td>
									<td class="toolbar m0">
										<button class="icon-checkmark m0" id="p_descrip_b_' . $row_port['id'] . '" title="Ok"></button>&nbsp;';
							if ($group_access['port_edit'])
								$content.='<button class="icon-cancel m0" id="p_descrip_d_'.$row_port['id'].'" title="Удалить"></button>';
								$content.='</td>';
							}
							else
								$content.='<td>'.$row_port['descrip'].'</td>';
						}
						//$text .= '</div><div class="clear"></div>';
						$content.='</tr>';
					}
					
					//$text .= '<div class="last"></div>';
				}/* else {
					$text .= '<div class="show_ports_table">';
					$text .= 'Нет портов';
					$text .= '</div><div class="clear"></div>';
				}*/
				$i++;
				//$fib.='<div class="last_free"> </div>';
				$text .= '</div>';
				$text .= '<div class="clear"></div>';
				
				$content.='</table>';		
			}
			//$text .= '</div>';
		}
	} else if($_GET['act'] == 's_ports_print') {
		$title=$address.' ('.$type.$num.') [Версия для печати]';
		$sql = "SELECT * FROM ".$table_cruz_conn." WHERE pq_id = ".$pq_id." ORDER BY port";
		
		$sql='SELECT
				cc1.*,
				cc2.port AS to_port,
				cc2.port AS to_port,
				pt1.name AS to_pq_name,
				p1.num AS to_pq_num
			FROM
				'.$table_cruz_conn.' AS cc1
			LEFT JOIN '.$table_fiber_conn.' AS fc1 ON (fc1.fiber_id_1 = cc1.fiber_id OR fc1.fiber_id_2 = cc1.fiber_id) AND fc1.node_id = 81
				LEFT JOIN '.$table_fiber.' AS f1
					JOIN '.$table_cruz_conn.' AS cc2
						JOIN '.$table_pq.' AS p1
								JOIN '.$table_pq_type.' AS pt1 ON p1.pq_type_id = pt1.id
						ON cc2.pq_id = p1.id AND p1.node = '.$node_id.'
					ON cc2.fiber_id = f1.id
				ON f1.id = CASE WHEN fc1.fiber_id_1 = cc1.fiber_id THEN fc1.fiber_id_2 ELSE CASE WHEN fc1.fiber_id_2 = cc1.fiber_id THEN fc1.fiber_id_1 END END
			WHERE cc1.pq_id = '.$pq_id.'
			ORDER BY cc1.port';
		
		//echo $sql.' '.$node_id;
		$last_addr='';
		$addr_array = array();
		$result = pg_query($sql);
		if (pg_num_rows($result)) {
			$content.='<table>';
			$content.='<tr class="text-center" style="background-color: silver; font-weight: bold;">
				<td class="span1 left bt">Порт</td>
				<td class="span4 bt">Описание</td>
				<td class="span4 bt">Подключение</td>
				<td class="span3 bt">Адрес</td>
				<td class="span2 bt">Кросс</td>
				<td class="span1 bt">Порт</td>
				<td class="span4 br bt">Описание</td>
			</tr>';
			while ($row = pg_fetch_assoc($result)) {
				$to_port = find_end_port($node_id,0,$row['fiber_id'],true);

				$bb=($last_addr != $to_port['curr_node_addr']?'bt':'');
				
				$bg_key = array_search($to_port['curr_node_addr'], $addr_array);
				if(!is_numeric($bg_key)) {
					array_push($addr_array,$to_port['curr_node_addr']);
				}
				
				$bg=(isset($to_port['curr_node_addr']) ? (array_search($to_port['curr_node_addr'], $addr_array)%2==0?' bgw':' bgs'):' bgw');
				
				$content.='<tr>';
				$content.='	<td class="'.$bb.$bg.'">'.$row['port'].'</td>';
				$content.='	<td class="'.$bb.$bg.'">'.($row['descrip']?$row['descrip']:'&nbsp;').'</td>';
				$content.='	<td class="'.$bb.$bg.'">'.($row['to_port']?$row['to_pq_name'].' №'.($row['to_pq_num']?$row['to_pq_num']:'1').' порт '.$row['to_port']:'&nbsp;').'</td>';
				$content.=' <td class="'.$bb.$bg.'">'.($to_port['curr_node_addr']?$to_port['curr_node_addr']:'&nbsp;').'</td>';
				$content.=' <td class="'.$bb.$bg.'">'.($to_port['pq_name']?$to_port['pq_name'].($to_port['pq_type']==0?' №'.($to_port['pq_num']?$to_port['pq_num']:'1'):''):'&nbsp;').'</td>';
				$content.=' <td class="'.$bb.$bg.'">'.($to_port['to_port']&&$to_port['pq_type']==0?$to_port['to_port']:'&nbsp;').'</td>';
				$content.=' <td class="br '.$bb.$bg.'">'.($to_port['port_desc']?$to_port['port_desc']:'&nbsp;').'</td>';
				$content.='</tr>';
				
				$last_addr = $to_port['curr_node_addr'];
			}
		}
		/*echo '<pre>';
		print_r($addr_array);
		echo '</pre>';*/
		$content.='</table>';
		$text='
			<html lang="ru">
			<head>
			    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			    <title>'.$title.'</title>
			    <style type="text/css">
			    	body, table {
					    font-family: "Segoe UI Semilight","Open Sans",Verdana,Arial,Helvetica,sans-serif;
					    //font-weight: 300;
					    font-size: 10pt;
					    letter-spacing: 0.02em;
					    line-height: 20px;
					}
			    	.title {
			    		text-align: center;
			    		font-size: 14pt;
			    		padding: 0 0 10px 0;
			    	}
			        table {
			    		width: 100%;
			    		//margin: 0px 0px 20px;
			    		//border-collapse: separate;
			    		border-spacing: 0px;
			            border: 1px solid black;
			        }
			    	td {
			    		//width: 100%;
			    		//margin: 0px 0px 20px;
			    		//border-collapse: separate;
			    		border-spacing: 0px;
			            border-left: 1px solid black;
			    		//border-top: 1px solid black;
			    		border-bottom: 1px solid black;
			    		//border-right: none;
			        }
					.br {
			            border-right: 1px solid black;
			        }
					.bt {
			            border-top: 1px solid black;
			        }
			    	.bgs {
			    		background-color: #DCDCDC;
			    	}
			    	.bgw {
			    		background-color: none;
			    	}
			    	.text-center { text-align: center; }
			    	.span1 { width: 50px; }
			    	.span2 { width: 100px; }
			    	.span3 { width: 150px; }
			    	.span4 { width: 200px; }
			    	.span5 { width: 250px; }
			    	.span6 { width: 300px; }
			    </style>
			</head>
			<body>
			    <div>
			        <div class="title">'.$address.' ('.$type.$num.')</div>
			        '.$content.'
			    </div>
				<a href="#print-this-document" onclick="print(); return false;">Распечатать</a>
			</body>
			</html>';
		echo $text;
		echo "<p>".(microtime(1)-$t)."</p>";
		die;
	} else if($_GET['act'] == 's_ports_test') {
		$title=$address.' ('.$type.$num.') [Версия для печати]';
		$sql='SELECT
				cc1.*,
				cc2.port AS to_port,
				cc2.port AS to_port,
				pt1.name AS to_pq_name,
				p1.num AS to_pq_num,
				f2.num AS fib_num,
				col1.name AS mod_name,
				col1.color AS mod_color,
				col1.stroke AS mod_stroke,
				col2.name AS fib_name,
				col2.color AS fib_color,
				col2.stroke AS fib_stroke
			FROM
				'.$table_cruz_conn.' AS cc1
			LEFT JOIN '.$table_fiber_conn.' AS fc1 ON (fc1.fiber_id_1 = cc1.fiber_id OR fc1.fiber_id_2 = cc1.fiber_id) AND fc1.node_id = 81
				LEFT JOIN '.$table_fiber.' AS f1
					JOIN '.$table_cruz_conn.' AS cc2
						JOIN '.$table_pq.' AS p1
								JOIN '.$table_pq_type.' AS pt1 ON p1.pq_type_id = pt1.id
						ON cc2.pq_id = p1.id AND p1.node = '.$node_id.'
					ON cc2.fiber_id = f1.id
				ON f1.id = CASE WHEN fc1.fiber_id_1 = cc1.fiber_id THEN fc1.fiber_id_2 ELSE CASE WHEN fc1.fiber_id_2 = cc1.fiber_id THEN fc1.fiber_id_1 END END
			LEFT JOIN '.$table_fiber.' AS f2 ON f2.id = cc1.fiber_id 
			LEFT JOIN '.$table_color.' AS col1 ON col1.id = f2.mod_color
			LEFT JOIN '.$table_color.' AS col2 ON col2.id = f2.fib_color
			WHERE cc1.pq_id = '.$pq_id.'
			ORDER BY cc1.port';
		echo '<pre>'; print_r($sql); echo '</pre>';
		$last_addr='';
		$addr_array = array();
		$result = pg_query($sql);
		$fiber_array = array();
		if (pg_num_rows($result)) {
			$content.='<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="1000" height="800">';
			while ($row = pg_fetch_assoc($result)) {
				//echo '<pre>'; print_r($row); echo '</pre>';
				array_push($fiber_array,$row['port']);
				$content.='
						<rect x="10" y="'.($row['port']*15).'" width="60" height="15" style="fill:#'.($row['mod_color']?$row['mod_color']:'FFFFFF').';stroke-width:0.5;stroke:black" />
								<text x="12" y="'.($row['port']*15+11).'" font-family="Verdana" font-size="8" fill="black" text-anchor="">'.substr($row['mod_name'], 0, 20).'</text>
						</rect>';
				$content.='
						<rect x="70" y="'.($row['port']*15).'" width="15" height="15" style="fill:white;stroke-width:0.5;stroke:black" />
								<text x="77" y="'.($row['port']*15+11).'" font-family="Verdana" font-size="8" fill="black" text-anchor="middle">'.$row['port'].'</text>
						</rect>';
			}
			$content.='</svg>';
		}
			/*$content.='<table>';
			$content.='<tr class="text-center" style="background-color: silver; font-weight: bold;">
				<td class="span1 left bt">Порт</td>
				<td class="span4 bt">Описание</td>
				<td class="span4 bt">Подключение</td>
				<td class="span3 bt">Адрес</td>
				<td class="span2 bt">Кросс</td>
				<td class="span1 bt">Порт</td>
				<td class="span4 br bt">Описание</td>
			</tr>';
			while ($row = pg_fetch_assoc($result)) {
				$to_port = find_end_port($node_id,0,$row['fiber_id'],true);

				$bb=($last_addr != $to_port['curr_node_addr']?'bt':'');
				
				$bg_key = array_search($to_port['curr_node_addr'], $addr_array);
				if(!is_numeric($bg_key)) {
					array_push($addr_array,$to_port['curr_node_addr']);
				}
				
				$bg=(isset($to_port['curr_node_addr']) ? (array_search($to_port['curr_node_addr'], $addr_array)%2==0?' bgw':' bgs'):' bgw');
				
				$content.='<tr>';
				$content.='	<td class="'.$bb.$bg.'">'.$row['port'].'</td>';
				$content.='	<td class="'.$bb.$bg.'">'.($row['descrip']?$row['descrip']:'&nbsp;').'</td>';
				$content.='	<td class="'.$bb.$bg.'">'.($row['to_port']?$row['to_pq_name'].' №'.($row['to_pq_num']?$row['to_pq_num']:'1').' порт '.$row['to_port']:'&nbsp;').'</td>';
				$content.=' <td class="'.$bb.$bg.'">'.($to_port['curr_node_addr']?$to_port['curr_node_addr']:'&nbsp;').'</td>';
				$content.=' <td class="'.$bb.$bg.'">'.($to_port['pq_name']?$to_port['pq_name'].($to_port['pq_type']==0?' №'.($to_port['pq_num']?$to_port['pq_num']:'1'):''):'&nbsp;').'</td>';
				$content.=' <td class="'.$bb.$bg.'">'.($to_port['to_port']&&$to_port['pq_type']==0?$to_port['to_port']:'&nbsp;').'</td>';
				$content.=' <td class="br '.$bb.$bg.'">'.($to_port['port_desc']?$to_port['port_desc']:'&nbsp;').'</td>';
				$content.='</tr>';
				
				$last_addr = $to_port['curr_node_addr'];
			}
		}
		$content.='</table>';*/
		$text='
			<html lang="ru">
			<head>
			    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			    <title>'.$title.'</title>
			    <style type="text/css">
			    	body, table {
					    font-family: "Segoe UI Semilight","Open Sans",Verdana,Arial,Helvetica,sans-serif;
					    //font-weight: 300;
					    font-size: 10pt;
					    letter-spacing: 0.02em;
					    line-height: 20px;
					}
			    	.title {
			    		text-align: center;
			    		font-size: 14pt;
			    		padding: 0 0 10px 0;
			    	}
			        table {
			    		width: 100%;
			    		//margin: 0px 0px 20px;
			    		//border-collapse: separate;
			    		border-spacing: 0px;
			            border: 1px solid black;
			        }
			    	td {
			    		//width: 100%;
			    		//margin: 0px 0px 20px;
			    		//border-collapse: separate;
			    		border-spacing: 0px;
			            border-left: 1px solid black;
			    		//border-top: 1px solid black;
			    		border-bottom: 1px solid black;
			    		//border-right: none;
			        }
					.br {
			            border-right: 1px solid black;
			        }
					.bt {
			            border-top: 1px solid black;
			        }
			    	.bgs {
			    		background-color: #DCDCDC;
			    	}
			    	.bgw {
			    		background-color: none;
			    	}
			    	.text-center { text-align: center; }
			    	.span1 { width: 50px; }
			    	.span2 { width: 100px; }
			    	.span3 { width: 150px; }
			    	.span4 { width: 200px; }
			    	.span5 { width: 250px; }
			    	.span6 { width: 300px; }
			    </style>
			</head>
			<body>
			    <div>
			        <div class="title">'.$address.' ('.$type.$num.')</div>
			        '.$content.'
			    </div>
				<a href="#print-this-document" onclick="print(); return false;">Распечатать</a>
			</body>
			</html>';
		echo $text;
		echo "<p>".(microtime(1)-$t)."</p>";
		die;
	}

    show_menu();
    die;
}

// списов файлов в кроссе/муфту
//if (isset($_GET['act']) && $_GET['act'] == 'pq_file' && is_numeric($_GET['pq_id']) && $group_access['cable']) {
//if (isset($_GET['act']) && $_GET['act'] == 'pq_file' && ( is_numeric($_GET['pq_id']) || isset($_GET['id']))) {
if (isset($_GET['act']) && $_GET['act'] == 'pq_file' && ( @is_numeric($_GET['pq_id']) || isset($_GET['id']))) {

	if(@is_numeric($_GET['pq_id']))
	{
		
		$i=1;
		// id кросса/муфты
		$pq_id = clean($_GET['pq_id']);
		// навигация
		$sql="SELECT n1.id AS id, pt.type AS type, p1.num AS num, LEFT(p1.descrip, 15) AS descrip FROM ".$table_pq." AS p1 , ".$table_node." AS n1, ".$table_pq_type." AS pt WHERE p1.node = n1.id AND p1.id=" . $pq_id . " AND p1.pq_type_id = pt.id;";
		$result = pg_fetch_assoc(pg_query($sql));
		//print_r($result);
		// id узла
		$node_id = $result['id'];
		// тип и номер кросса/муфты
		$pq_type = $result['type'];
		$pq_num = $result['num'];
		$address=(isset($node_id)?addr_id_full($node_id):"");
		$descrip=(!empty($result['descrip'])?' "'.$result['descrip'].'"':'');
		
		if ($result['type'] == 0) $type = 'Кросс'; else if ($result['type'] == 1) $type = 'Муфта'; else $type = 'Медный';
		if (isset($result['num'])) $num = ' №' . $result['num']; else $num = '';
		
		$action='<div class="m0 text-left">
	     <div class="horizontal-menu">
		    <ul>
			    <li><a href="?act=s_pq&o_node&node_id='.$node_id.'">'.$address.' ('.$type.$num.') '.$descrip.'</a></li>
			    </li>
		    </ul>
	    </div>
	    </div>';

		$title='Узлы > '.$address.' > '.$type.$num.' > '.'Кабеля';
		//if ($group_access['cable_add'])
			//$action=($group_access['cable_add']?'
		$action='
    		<div class="span2 m0 text-left">
    			<button class="m0" id="_add_div" rel="?act=pq_file_add&pq_id=' . clean($_GET['pq_id']) .($group_access['prompt']?'&prompt=1':''). '" />Добавить файл</button>
    		</div>'.$action;
//    		</div>':'').$action;
		// скрытые поля
		$content='<input type="hidden" id="node_id" value="' . $node_id . '">';
		$content.='<input type="hidden" id="pq_id" value="' . clean($_GET['pq_id']) . '">';
		$content.='<input type="hidden" id="pq_type" value="' . $pq_type . '">';
		$content.='<input type="hidden" type="text" id="pq_num" value="' . $pq_num . '">';

		// запрос
		$sql = "SELECT pqs.*, to_char(pqs.date + '7 hour'::interval, 'HH24:MI:SS DD.MM.YY') AS date, u.name AS user
				FROM ".$table_pq_schem." AS pqs
				LEFT JOIN ".$table_user." AS u ON u.id = pqs.user_id 
				WHERE pqs.pq_id=".clean($_GET['pq_id'])."
				ORDER BY pqs.date DESC";
		/*echo '<pre>';
		print_r($sql);
		echo '<pre>';*/
		$result = pg_query($sql);

		if (pg_num_rows($result)) {
			$content.='<table class="striped">';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span8">Имя файла</td>';
			$content.='<td class="span3">Автор</td>';
			$content.='<td class="span3">Дата</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			while ($row = pg_fetch_assoc($result)) {

				$content.='<td class="span1">'.$i.'.</td>';
				//$content.='<td class="span8"><a href="?act=pq_file&id='.base64_encode($row['id']).'" target="_blank">'.$row['name'].'</a></td>';
				$content.='<td class="span8">'.$row['name'].'</td>';
				$content.='<td class="span3">'.$row['user'].'</td>';
				$content.='<td class="span3">'.$row['date'].'</td>';
				//($_SESSION['group']==0 || $user_id == 2 || $user_id == 4?'<div class="m5 span_text" style="color: #f1f1f1;" >узел создал: '.$user.'</div>':'').'</div>
				$content.='<td class="span2">';
				$content.='<button class="icon-file-pdf mini m0" onClick="window.open(\'?act=pq_file&id='.base64_encode($row['id']).'\');" title="Просмотреть"></button>&nbsp;';
				$content.='<button class="icon-download-2 mini m0" onClick="window.open(\'?act=pq_file&id='.base64_encode($row['id']).'&download\');" title="Скачать"></button>&nbsp;';
				$content.=($user_id == 1?'<button class="icon-cancel-2 mini m0" id="pq_file_add_div" rel="?act=pq_file_del&id='.$row['id'].'" title="Удалить"></button>':'');
				
				$content.='</td></tr>';

					$i++;
					}
					$content.='</table>';
					//$content.='<script type="text/javascript">'.$content_js.'</script>';
	    }
	    show_menu();
	}
	
	if(isset($_GET['id'])) {
		$sql="SELECT * FROM ".$table_pq_schem." WHERE id = ".base64_decode(clean($_GET['id']));
		$row=pg_fetch_assoc(pg_query($sql));
		if(isset($row['id'])) {
			$res = pg_unescape_bytea($row['data']) ;
			header('Content-Description: File Transfer');
			header('Content-type: application/pdf');
			header('Content-Disposition: '.(isset($_GET['download'])?'attachment':'inline').'; filename="'.$row['name'].'"');
			//header('Content-Disposition: attachment; filename="'.$row['name'].'"');
			header('Content-Transfer-Encoding: binary');
			
			/*header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);*/
			
			echo $res;
		} else {
			echo 'Ошипка..';
		}
	}
	die;
}
// конец списов файлов в кроссе/муфту


if (empty($_GET)) {
    $text='Главная страница';
	$content='
			<a class="button" href="/fibers/geomap.php" target="_blank">Карта сети</a>';
	if($_SESSION['group']<=5)
		$content.='<a class="button" target="_blank" href="http://'.$host.':8080/geoexplorer/composer/?layers=opengeo%3Acable,opengeo%3Acable_reserve,opengeo%3Anode" />Редактор карты</a>';
	if($_SESSION['group']==0)
	 	$content.='<a class="button" href="http://'.$host.':8080/dashboard/" target="_blank">OpenGeo</a>';
    
	// ссылки на главной
//	if($_SERVER['REMOTE_ADDR']=='192.168.6.12' || $_SERVER['REMOTE_ADDR']=='192.168.6.6') {
		$content.='<div>
				<h3>Полезные ссылки:</h3>
				<a href="http://incab.ru/choice_pod_ok/" target="_blank">Инкаб Проектирование</a><br>
				<a href="http://incab.ru/useful-information/documents/" target="_blank">Инкаб Библиотека</a><br>
				<a href="http://altayok.ru/techinfo/cvetovaia_identifikaciia_opticheskih_volokon_i_modylei/" target="_blank">Цветовая идентификация оптических волокон и модулей (Алтайкабель)</a><br>
			</div>';
//	}

    show_menu($text);
    die;
}

?>
