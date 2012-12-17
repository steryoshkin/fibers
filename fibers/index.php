<?

include_once ('./engine/setup.php');
include_once ('./engine/db.php');
$user_id=$_SESSION['logged_user_fibers_id'];

if (empty($_SESSION['logged_user_fibers']) && $_SERVER['REQUEST_URI'] != $login_page)
    header("Location: " . $login_page);

$debug = isset($_GET['debug']);
//	include_once ($_SERVER["DOCUMENT_ROOT"].'./engine/function.php');

$js_jsplumb2='
 <script type="text/javascript" src="js/1.3.8/jsPlumb-1.3.8-RC1.js"></script>
 <script type="text/javascript" src="js/1.3.8/jsPlumb-defaults-1.3.8-RC1.js"></script>
 <script type="text/javascript" src="js/1.3.8/jsPlumb-renderers-canvas-1.3.8-RC1.js"></script>
 <script type="text/javascript" src="js/1.3.8/jquery.jsPlumb-1.3.8-RC1.js"></script>';
// <script type="text/javascript" src="js/action2.js"></script>
// ';
//echo $text.$js_jsplumb;
$text = '';

// функция вывода меню
function show_menu() {
    global $title;
    global $action;
    global $content;
    global $menu;
    if($menu=='')
    $menu='
    <div class="horizontal-menu bg-color-blueLight">
        <ul>
            <li><a class="icon-home" href="./"></a></li>
            <li><a href="?act=s_node">Узлы</a></li>
            <li class="sub-menu"><a href="#">Справочники</a>
                <ul class="text-left">
                    <li class="sub-menu"><a href="#">Адреса</a>
                        <ul class="text-left">
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
                            <li><a href="?act=dirs&dir=switch_type">Коммутаторы</a></li>
                            <li><a href="?act=dirs&dir=mc_type">Медиаконвертеры</a></li>
                        </ul>
                    </li>
                    <li class="sub-menu"><a href="#">Прочее оборудование</a>
                        <ul class="text-left">
                            <li><a href="?act=dirs&dir=box_type">Рамы/Ящики</a></li>
                            <li><a href="?act=dirs&dir=ups_type">ИБП</a></li>
                            <li><a href="?act=dirs&dir=other_type">Разное</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li><a class="icon-locked" href="?logout">['.$_SESSION['user'].']</a></li>
        </ul>
    </div>
    <br>
    <br>
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
    <script type="text/javascript" src="js/lib/jquery-1.7.1-min.js"></script>
    <script type="text/javascript" src="js/action.js"></script>
</head>
<body class="modern-ui">
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
        <div class="page-region">
            '.$content.'
        </div>
    </div>
</body>
</html>
    ';
    echo $text;
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
        </form>
    ';
    show_menu();
    die;
}

// вывод списка узлов
if (isset($_GET['act']) && $_GET['act'] == 's_node') {
    $sql = "SELECT DISTINCT(LEFT(name, 1)) AS name FROM `" . $table_street_name . "`";
    $result = mysql_query($sql);
    if (mysql_num_rows($result)) {
        while ($row = mysql_fetch_assoc($result)) {
            $find_abc .= '<div class="b_m"><a class="b_m_a" href="?act=s_node&find_node=' . $row['name'] . '%">' . $row['name'] . '</a></div>';
        }
        $find_abc .= '<div class="b_m"><a class="b_m_a" href="?act=s_node">Все</a></div>';
    }
    $i = 1;
    //$link = '<div class="title">Узлы</div>';
    $title='Узлы';
    $action='';
    if ($_SESSION['node_add'])
    	$action.='<div class="span2 m0 text-left"><button class="m0" id="in_div" rel="?act=n_node" />Добавить узел</button></div>';
        //$action.='<div class="span2 m0 text-left"><button class="m0" id="node_add_div" rel="?act=n_node" />Добавить узел</button></div>';
    $action.='<div class="span3 m0 text-left input-control text"><input class="" id="find_node" type="text" onchange="javascript: window.location=\'?act=s_node&find_node=%\'+$(\'input#find_node\').val()+\'%\';" placeholder="Введите для поиска" /></div>';
    $action.='<div class="span1 m0 text-left toolbar"><button class="icon-search m0" onClick="javascript: window.location=\'?act=s_node&find_node=%\'+$(\'input#find_node\').val()+\'%\';" /></button></div>';
    $action.='<div class="span6 m5">'.$find_abc.'</div>';

    if (isset($_GET['find_node'])) {
    	//$find_node = 'AND s_name.name LIKE "' . clean($_GET['find_node']) . '"';
    	$find_node = 'AND n1.address LIKE "' . clean($_GET['find_node']) . '"';
    }
    
    $sql_count = "SELECT COUNT(*) FROM `".$table_node."` AS n1, `".$table_street_name."` AS s_name WHERE n1.street_id = s_name.id " . $find_node;
    $total_rows=mysql_fetch_row(mysql_query($sql_count));

    $num_pages=ceil($total_rows[0]/$per_page);

    if(isset($_GET['page'])) $page=($_GET['page']-1); else $page=0;
    $start=abs($page*$per_page);
    $i=$i+$start;
    
    if(isset($_GET['find_node'])) $find='&find_node='.clean($_GET['find_node']);
    for($a=1;$a<=$num_pages;$a++) {
        if ($a-1 == $page) {
            $pages.='<div class="b_m">'.$a.'</div>';
        } else {
            $pages.='<div class="b_m"><a class="b_m_a" href="?act=s_node'.$find.'&page='.$a.'">'.$a.'</a></div>';
        }
    }
    $pages='<div class="text-center">
		    	<div class="b_m">Страницы:</div>
		    	'.$pages.'
		    	<div class="b_m">всего: '.$total_rows[0].'</div>
		    </div>';
    //$sql = "SELECT n1.*,p1.id AS pq_id FROM `" . $table_node . "` AS n1 LEFT JOIN `".$table_pq."` AS p1 ON n1.id = p1.node " . $find_node . " GROUP BY `n1`.`address` ORDER BY `n1`.`address` LIMIT $start,$per_page";
    $sql = "SELECT s_name.name AS street_name,
            s_num.num AS street_num,
            loc.location AS location,
            room.room AS room,
            n1.*,p1.id AS pq_id,
            keys.num AS key_num
        FROM `".$table_street_name."` AS s_name,
            `".$table_street_num."` AS s_num,
            `".$table_node."` AS n1
        LEFT JOIN `".$table_pq."` AS p1 ON n1.id = p1.node
        LEFT JOIN `".$table_location."` AS loc ON n1.location_id = loc.id
        LEFT JOIN `".$table_room."` AS room ON n1.room_id = room.id
        LEFT JOIN `".$table_keys."` AS `keys` ON `keys`.`node_id` = `n1`.`id`
        WHERE n1.street_id = s_name.id
        AND n1.street_num_id = s_num.id
        " . $find_node . "
        GROUP BY n1.address
        ORDER BY s_name.name, s_num.num LIMIT $start,$per_page";
    if($num_pages>1) $content=$pages;

    $content.='<table class="striped">';
    $result = mysql_query($sql);
    if (mysql_num_rows($result)) {
        while ($row = mysql_fetch_assoc($result)) {
            //$addr=$row['address'];
            // закончил тут, бошка не варит
            /*$addr=$row['street_name'].' '.$row['street_num'].
            ($row['num_ent']||$row['location']||$row['room']?" (".
            	($row['num_ent']?$row['num_ent']."п".(!$row['location']||!$row['room']?"/":""):"").
            	($row['location']?$row['location'].
            		(preg_match("/\d+/", $row['location'])?"э":"").
            	($row['room']?"/":""):"").($row['room']?$row['room']:"")
			.")":"");*/
            $content.='<tr>';
            //$content.='<td class="span1">'.$i.'.</td><td class="span4'.($row['incorrect']==1?' bg-color-orangeDark':'').'"><a id="addr" href="?act=s_pq&node_id=' . $row['id'] . '">' . $row['address'] . '</a></td>';
            $content.='<td class="span1'.($row['key_num']?' bg-color-green':'').'"><a href="engine/map.php?id='.$row['id'].'" target="_blank">'.$i.'.</a></td><td class="span5'.($row['incorrect']==1?' bg-color-orangeDark':(!$row['pq_id']?' bg-color-orange':'')).'"><a id="addr" href="?act=s_pq&o_node&node_id=' . $row['id'] . '" '.($user_id==1?'title="'.$row['user_id'].'"':'').'>' .addr($row['street_name'],$row['street_num'],$row['num_ent'],$row['location'],$row['room']). '</a></td>';
            $content.='<td class="span7">'.$row['desc'].'</td>';
            if ($_SESSION['node_edit'] || $_SESSION['node_del'])
                $content.='<td class="span2">';
            else
            	$content.='<td class="span2">&nbsp;</td>';

            if ($_SESSION['node_edit'] )
                $content.='<button class="icon-pencil mini m0" id="pq_e_add_div" rel="?act=e_node&node_id=' . $row['id'] . '" title="Редактировать" /></button>&nbsp;';
            if ($_SESSION['node_del'])
            	$content.='<button class="icon-cancel-2 mini m0" id="pq_d_add_div" rel="?act=d_node&node_id=' . $row['id'] . '&addr=' . $row['address'] . '" title="Удалить"/></button>';
            if ($_SESSION['node_edit'] || $_SESSION['node_del'])
                $content.='</td>';
            $content.='</tr>';
            $i++;
        }
    }
    $content.='</table>';
    // Миха просил :)
    if($num_pages>1 && $user_id==2) $content.=$pages.'<br>';
    show_menu();
    die;
}

// вывод списка пассивного оборудования Кроссы/Муфты
//if (isset($_GET['act']) && $_GET['act'] == 's_pq' && ( isset($_GET['o_node']) || isset($_GET['p_node']) ) && is_numeric($_GET['node_id'])) {
if (isset($_GET['act']) && $_GET['act'] == 's_pq' && is_numeric($_GET['node_id'])) {

	if(!isset($_GET['p_node']) && !isset($_GET['o_node'])) {
		$content.='
		<div class="horizontal-menu">
            <ul class="m0">
				<li><a href="?act=s_pq&o_node&node_id='.clean($_GET['node_id']).'">Оптика</a></li>
				<li><a href="?act=s_pq&p_node&node_id='.clean($_GET['node_id']).'">Паспорт узла</a></li>
				<li><a href="engine/map.php?id='.clean($_GET['node_id']).'" target="_blank">Карта соединений</a></li>
			</ul>
        </div>';
	}

    $address = @mysql_result(mysql_query("SELECT `address` FROM `" . $table_node . "` WHERE `id`='" . clean($_GET['node_id']) . "';"), 0);
    //$address=addr_id(clean($_GET['node_id']));
    $title = 'Узел: '.$address;
    //'.(isset($_GET['p_node'])?'p_node&':(isset($_GET['o_node'])?'o_node&':'')).'
    $action='<li>
                <a href="?act=s_pq&node_id='.clean($_GET['node_id']).'">'.$address.'</a>
                    <ul>
                        <li><a href="?act=s_pq&o_node&node_id='.clean($_GET['node_id']).'">Оптика</a></li>
                        <li><a href="?act=s_pq&p_node&node_id='.clean($_GET['node_id']).'">Паспорт узла</a></li>
                    </ul>
            </li>';    
    if(isset($_GET['o_node'])) {
        $i=1;
        $o=1;
        $action.='
                <li class="sub-menu"><a href="">Добавить</a>
                    <ul class="text-left">
                        <li><a id="pq_into_div" href="/" rel="?act=n_pq&node_id=' . clean($_GET['node_id']) . '"/>Кросс/Муфту</a></li>
                    </ul>
                </li>';
	    $content='<table class="striped">';
	    
	    //$sql="SELECT *,`pq`.`id` AS id, `pq`.`desc` AS pq_desc, pq_t.desc AS pq_type_desc
	    /*$sql="SELECT `pq`.`id` AS id, `node`.`address` , `pq_t`.`type` , `pq`.`num`, `pq`.`desc` AS pq_desc, pq_t.desc AS pq_type_desc, pq_t.name
	    		FROM `".$table_node."` AS node, `".$table_pq."` AS pq
	    		LEFT JOIN `".$table_pq_type."` AS pq_t ON pq.pq_type_id = pq_t.id AND pq.type = pq_t.type
	    		WHERE `pq`.`node`=`node`.`id` AND `pq`.`node`=" . clean($_GET['node_id'])."
	    		ORDER BY `pq`.`node`";*/
		//$sql="SELECT `pq`.`id` AS id, `node`.`address2` , `pq_t`.`type` , `pq`.`num`, `pq`.`desc` AS pq_desc, pq_t.desc AS pq_type_desc, pq_t.name
	    $sql="SELECT `pq`.`id` AS id, `pq_t`.`type` , `pq`.`num`, `pq`.`desc` AS pq_desc, pq_t.desc AS pq_type_desc, pq_t.name
			    FROM `".$table_node."` AS node, `".$table_pq."` AS pq
			    LEFT JOIN `".$table_pq_type."` AS pq_t ON pq.pq_type_id = pq_t.id
			    WHERE `pq`.`node`=`node`.`id` AND `pq`.`node`=" . clean($_GET['node_id'])."
			    ORDER BY `pq`.`node`";
	    $result = mysql_query($sql);
	    if (mysql_num_rows($result)) {
	        while ($row = mysql_fetch_assoc($result)) {
	            if ($row['type'] == 0) $type = 'Кросс'; else $type = 'Муфта';
	            if (isset($row['num'])) $num = ' №' . $row['num']; else $num = '';
	            
	/*            if (isset($row['type'])) {
	            	$type='Введите ';
	            	$num='значение';
	            }*/
	            //$content.=$row['type'];
	            $content.='<tr>';
	            //$content.='<td class="span1">'.$i.'</td><td class="span11"><a href="?act=s_cable&pq_id='.$row['id'].'">'.$row['address'].' - '.$type.$num.' '.$row['name'].'</a></td>';
	            $content.='
	            	<td class="span1">'.$i.'</td>
	            	<td class="span4"><a href="?act=s_cable&pq_id='.$row['id'].'">'.$type.$num.' ('.$row['name'].')</a></td>
	            	<td class="span8">'.$row['pq_desc'].'</td>
	            ';
	            if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
	                $content.='<td class="span2">';
	            else
	                $content.='<td class="span2">&nbsp;</td>';
	            if ($_SESSION['pq_edit'])
	                $content.='<button class="icon-pencil mini m0" id="pq_e_add_div" rel="?act=e_pq&pq_id='.$row['id'].'" title="Редактировать" /></button>&nbsp;';            
	            if ($_SESSION['pq_del'])
	                $content.='<button class="icon-cancel-2 mini m0" id="pq_d_add_div" rel="?act=d_pq&pq_id='.$row['id'].'&addr='.$row['address'].' - '.$type.$num.'" title="Удалить"/></button>';
	            if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
	                $content.='</td>';
	            $content.='</tr>';
	            $i++;
	        }
	    }
	    $content.='</table>';

	    $content.='Адресс для вставки в 2gis:<br>';
	    $content.='<input id="2g_addr" type="text" style="width: 300px;" value=\''.$address.'\'</input><br>';
	    $content.='HTML код для вставки в 2gis:<br>';
	    $content.='<input id="2g_html" type="text" style="width: 100%;" value=\'<a href="http://62.231.168.109/fibers/index.php?act=s_pq&node_id='.clean($_GET['node_id']).'">'.$address.'</a>\'</input>';
	    $content.='<script>$("#2g_addr").focus(function() { $(this).select() });$("#2g_html").focus(function() { $(this).select() });</script>';
    }
    
    if(isset($_GET['p_node'])) {
    	//if ($_SESSION['pq_add'] && mysql_result(mysql_query("SELECT COUNT(*) FROM `" . $table_pq . "` AS p1, `" . $table_pq_type. "` AS pt WHERE p1.pq_type_id = pt.id AND pt.type = 1 AND p1.node = " . clean($_GET['node_id']) . ";"), 0)==0 )
    		$action2='
    		<div class="span m0 text-left">
    			<button class="m0" id="switch_add_div" rel="?act=n_switches&node_id=' . clean($_GET['node_id']) . '"/>Добавить коммутатор</button>
    			<button class="m0" id="mc_add_div" rel="?act=n_mc&node_id=' . clean($_GET['node_id']) . '"/>Добавить медиаконвертер</button>
    		</div>'.$action;
        
        $action.='
                <li class="sub-menu"><a href="">Добавить</a>
                    <ul class="text-left">
                        <li><a id="pq_into_div" href="/" rel="?act=n_box&node_id=' . clean($_GET['node_id']) . '"/>Раму/Ящик</a></li>
                        <li><a id="pq_into_div" href="/" rel="?act=n_switches&node_id=' . clean($_GET['node_id']) . '"/>Коммутатор</a></li>
                        <li><a id="pq_into_div" href="/" rel="?act=n_mc&node_id=' . clean($_GET['node_id']) . '"/>Медиаконвертер</a></li>
                        <li><a id="pq_into_div" href="/" rel="?act=n_ups&node_id=' . clean($_GET['node_id']) . '"/>ИБП</a></li>
                        <li><a id="pq_into_div" href="/" rel="?act=n_other&node_id=' . clean($_GET['node_id']) . '"/>Прочее</a></li>
                        '.(!@mysql_result(mysql_query("SELECT * FROM `".$table_keys."` WHERE node_id = ".clean($_GET['node_id'])),0)?'<li><a id="pq_into_div" href="/" rel="?act=e_key_node&node_id='.clean($_GET['node_id']).'"/>Ключ</a></li>':'').'
                        '.(!@mysql_result(mysql_query("SELECT * FROM `".$table_lift."` WHERE node_id = ".clean($_GET['node_id'])),0)?'<li><a id="pq_into_div" href="/" rel="?act=e_lift_node&node_id=' . clean($_GET['node_id']) . '"/>Лифтовую</a></li>':'').'
                    </ul>
                </li>';
        /*<ul>
            <li class="sub-menu"><a href="?act=s_pq&o_node&node_id='.clean($_GET['node_id']).'">'.$address.'</a></li>
        </ul>*/

        // общая таблица
        $i=1;
        $content.='<table class="striped">';
        $content.='<tr>';
        
        // таблица ключей, лифтовых и описания
        $content.='<td>Общее</td>';
        $content.='</tr><tr>';
        $content.='<td>';
        $result_desc=mysql_fetch_assoc(mysql_query("SELECT * FROM `".$table_desc."` WHERE `node_id` =".clean($_GET['node_id'])),0);
        $result_key=mysql_fetch_assoc(mysql_query("SELECT * FROM `".$table_keys."` WHERE `node_id` =".clean($_GET['node_id'])),0);
        $result_lift=mysql_fetch_assoc(mysql_query("SELECT *,lt1.desc AS lt_desc FROM `".$table_lift_type."` AS lt1, `".$table_lift."` AS l1 WHERE l1.lift_id = lt1.id AND l1.node_id =".clean($_GET['node_id'])),0);
            $content.='
            <table class="striped">
                <tr>';
            if($result_key)
            $content.='
                    <td class="span4">Ключ</td>
                    <td class="span2">
                        <button class="icon-checkmark m0 mini" id="key_node_e_add_div" rel="?act=e_key_node&node_id='.clean($_GET['node_id']).'" title="Ok"></button>
                        <button class="icon-cancel-2 mini m0" id="key_node_d_add_div" rel="?act=d_key_node&node_id='.clean($_GET['node_id']).'" title="Удалить"/></button>
                    </td>';
            else
            $content.='<td class="span4">&nbsp;</td><td class="span2">&nbsp;</td>';
            $content.='
                    <td class="span10">Описание</td>
                </tr>
                <tr>
                    <td colspan=2>'.$result_key['num'].' '.($result_key['desc']?'('.$result_key['desc'].')':'').'&nbsp;</td>
                    <td class="span10 text-right" rowspan=5><div class="input-control textarea"><textarea id="desc_text">'.$result_desc['text'].'</textarea></div>
                        <input type="hidden" id="id_desc_text" value="'.clean($_GET['node_id']).'">
                        <button class="icon-checkmark m0 mini" id="e_desc_text" title="Ok"></button>
                        <button class="icon-cancel-2 mini m0" id="d_desc_text" title="Удалить"/></button>
                    </td>
                </tr>';
            if($result_lift)
            $content.='
                <tr>
                    <td>Лифтовая</td>
                    <td>
                        <button class="icon-checkmark m0 mini" id="lift_node_e_add_div" rel="?act=e_lift_node&node_id='.clean($_GET['node_id']).'" title="Ok"></button>
                        <button class="icon-cancel-2 mini m0" id="lift_node_d_add_div" rel="?act=d_lift_node&node_id='.clean($_GET['node_id']).'" title="Удалить"/></button>
                    </td>
                </tr>
                <tr><td colspan=2>Адрес: '.$result_lift['name'].'&nbsp;</td></tr>
                <tr><td colspan=2>Телефоны: '.$result_lift['tel'].'&nbsp;</td></tr>
                <tr><td colspan=2>'.$result_lift['lt_desc'].'&nbsp;</td></tr>';
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
                FROM `".$table_box_type."` AS bt1, `".$table_box."` AS b1
                WHERE bt1.id = b1.box_type_id
                AND b1.node_id=" . clean($_GET['node_id'])."
                ORDER BY bt1.name";
            $result = mysql_query($sql);
            if (mysql_num_rows($result)) {
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
                while ($row = mysql_fetch_assoc($result)) {
                    $content.='<tr>';
                    $content.='
                    <td>'.$i.'.'.$o++.'</td>
                    <td>'.$row['name'].'</td>
                    <td>'.$row['unit'].'&nbsp;</td>
                    <td>'.$row['desc'].'&nbsp;</td>
                    ';
    //              if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
                        $content.='<td class="span2">';
    //              else
    //                  $content.='<td class="span2">&nbsp;</td>';
    //              if ($_SESSION['pq_edit'])
                        $content.='<button class="icon-pencil mini m0" id="box_e_add_div" rel="?act=e_box&id='.$row['id'].'" title="Редактировать" /></button>&nbsp;';
    //              if ($_SESSION['pq_del'])
                        $content.='<button class="icon-cancel-2 mini m0" id="box_d_add_div" rel="?act=d_box&id='.$row['id'].'" title="Удалить"/></button>';
    //              if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
                        $content.='</td>';
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
    	    	FROM `".$table_switch_type."` AS st1, `".$table_switches."` AS sw1
    	    	LEFT JOIN `".$table_sn."` AS sn1 ON sn1.eq = sw1.id AND eq_type='".$switch_id."'
    	    	WHERE st1.id = sw1.switch_type_id
    	    	AND `sw1`.`node_id`=" . clean($_GET['node_id'])."
    	    	ORDER BY `st1`.`name`";
        	$result = mysql_query($sql);
        	if (mysql_num_rows($result)) {
        		$content.='<tr>';
        		$content.='<td colspan=8>Коммутаторы</td>';
        		$content.='</tr>';
        		$content.='<tr>';
        		$content.='<td class="span1">№</td>';
        		$content.='<td class="span4">Название</td>';
        		$content.='<td class="span1">Портов</td>';
        		$content.='<td class="span1">Занято</td>';
                $content.='<td class="span1">Юнитов</td>';
        		$content.='<td class="span3">S/N</td>';
        		$content.='<td class="span3">Описание</td>';
        		$content.='<td class="span2">&nbsp;</td>';
        		$content.='</tr>';
        		$content.='<tr>';
        		while ($row = mysql_fetch_assoc($result)) {
        			$total_used_watt=$total_used_watt+$row['power'];
        		    // подсветка если портов мало или не осталось
        			$content.='<tr class="'.($row['used_ports']>=$row['ports_num']?'bg-color-red':($row['used_ports']+1>=$row['ports_num']?'bg-color-yellow':'')).'">';
        			$content.='
        			<td>'.$i.'.'.$o++.'</td>
        			<td>'.$row['name'].'</td>
        			<td>'.$row['ports_num'].'&nbsp;</td>
        			<td>'.$row['used_ports'].'&nbsp;</td>
        			<td>'.$row['unit'].'&nbsp;</td>
        			<td>'.$row['sn'].'&nbsp;</td>
        			<td>'.$row['desc'].'&nbsp;</td>
        			';
    //    			if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
        				$content.='<td class="span2">';
    //    			else
    //    				$content.='<td class="span2">&nbsp;</td>';
    //    			if ($_SESSION['pq_edit'])
        				$content.='<button class="icon-pencil mini m0" id="switches_e_add_div" rel="?act=e_switches&id='.$row['id'].'" title="Редактировать" /></button>&nbsp;';
    //    			if ($_SESSION['pq_del'])
        				$content.='<button class="icon-cancel-2 mini m0" id="switches_d_add_div" rel="?act=d_switches&id='.$row['id'].'" title="Удалить"/></button>';
    //    			if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
        				$content.='</td>';
        			$content.='</tr>';
        		}
        	}
        	$content.='</table>';
            $i++;
            
            // таблица медиаконвертеров
            $o=1;
            $content.='<table class="striped">';
            $sql="SELECT mc1 . * , mt1.name, mt1.power, sn1.sn
                FROM `".$table_mc_type."` AS mt1, `".$table_mc."` AS mc1
                LEFT JOIN `".$table_sn."` AS sn1 ON sn1.eq = mc1.id AND eq_type='".$mc_id."'
                WHERE mt1.id = mc1.mc_type_id
                AND mc1.node_id=" . clean($_GET['node_id'])."
                ORDER BY mt1.name";
            $result = mysql_query($sql);
            if (mysql_num_rows($result)) {
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
                while ($row = mysql_fetch_assoc($result)) {
                	$total_used_watt=$total_used_watt+$row['power'];
                    $content.='<tr>';
                    $content.='
                    <td>'.$i.'.'.$o++.'</td>
                    <td>'.$row['name'].'</td>
                    <td>'.$row['sn'].'&nbsp;</td>
                    <td>'.$row['desc'].'&nbsp;</td>
                    ';
    //              if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
                        $content.='<td class="span2">';
    //              else
    //                  $content.='<td class="span2">&nbsp;</td>';
    //              if ($_SESSION['pq_edit'])
                        $content.='<button class="icon-pencil mini m0" id="mc_e_add_div" rel="?act=e_mc&id='.$row['id'].'" title="Редактировать" /></button>&nbsp;';
    //              if ($_SESSION['pq_del'])
                        $content.='<button class="icon-cancel-2 mini m0" id="mc_d_add_div" rel="?act=d_mc&id='.$row['id'].'" title="Удалить"/></button>';
    //              if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
                        $content.='</td>';
                    $content.='</tr>';
                }
            }
            $content.='</table>';
            $i++;
            
            // таблица ИБП
            $o=1;
            $content.='<table class="striped">';
            $sql="SELECT u1 . * , ut1.name, ut1.unit, ut1.power, sn1.sn
                FROM `".$table_ups_type."` AS ut1, `".$table_ups."` AS u1
                LEFT JOIN `".$table_sn."` AS sn1 ON sn1.eq = u1.id AND eq_type='".$ups_id."'
                WHERE ut1.id = u1.ups_type_id
                AND u1.node_id=" . clean($_GET['node_id'])."
                ORDER BY ut1.name";
            $result = mysql_query($sql);
            if (mysql_num_rows($result)) {
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
                while ($row = mysql_fetch_assoc($result)) {
                	$total_watt=$total_watt+$row['power'];
                    $content.='<tr>';
                    $content.='
                    <td>'.$i.'.'.$o++.'</td>
                    <td>'.$row['name'].'</td>
                    <td>'.$row['power'].'&nbsp;</td>
                    <td>'.$row['unit'].'&nbsp;</td>
                    <td>'.$row['sn'].'&nbsp;</td>
                    <td>'.$row['desc'].'&nbsp;</td>
                    ';
    //              if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
                        $content.='<td class="span2">';
    //              else
    //                  $content.='<td class="span2">&nbsp;</td>';
    //              if ($_SESSION['pq_edit'])
                        $content.='<button class="icon-pencil mini m0" id="ups_e_add_div" rel="?act=e_ups&id='.$row['id'].'" title="Редактировать" /></button>&nbsp;';
    //              if ($_SESSION['pq_del'])
                        $content.='<button class="icon-cancel-2 mini m0" id="ups_d_add_div" rel="?act=d_ups&id='.$row['id'].'" title="Удалить"/></button>';
    //              if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
                        $content.='</td>';
                    $content.='</tr>';
                }
            }
            $content.='</table>';
            $i++;
            
            // таблица кроссов
            $o=1;
            $content.='<table class="striped">';
            $sql="SELECT `pq`.`id` AS id, `pq_t`.`type` , `pq_t`.`ports_num` ,`pq_t`.`unit` , `pq`.`num`, `pq`.`desc` AS pq_desc, pq_t.desc AS pq_type_desc, pq_t.name
                FROM `".$table_node."` AS node, `".$table_pq."` AS pq
                LEFT JOIN `".$table_pq_type."` AS pq_t ON pq.pq_type_id = pq_t.id
                WHERE `pq`.`node`=`node`.`id` AND `pq`.`node`=" . clean($_GET['node_id'])."
                ORDER BY `pq`.`node`";
            $result = mysql_query($sql);
            if (mysql_num_rows($result)) {

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
                while ($row = mysql_fetch_assoc($result)) {
                	if ($row['type'] == 0) $type = 'Кросс'; else $type = 'Муфта';
                	if (isset($row['num'])) $num = ' №' . $row['num']; else $num = ' №1';

                    $content.='<tr>';
                    $content.='
                    <td>'.$i.'.'.$o++.'</td>
                    <td>'.$row['name'].' '.$type.$num.'</td>
                    <td>'.$row['ports_num'].'</td>
                    <td>'.$row['unit'].'</td>
                    <td>'.$row['desc'].'&nbsp;</td>
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
                FROM `".$table_other_type."` AS ot1, `".$table_other."` AS o1
                WHERE ot1.id = o1.other_type_id
                AND o1.node_id=" . clean($_GET['node_id'])."
                ORDER BY ot1.name";
            $result = mysql_query($sql);
            if (mysql_num_rows($result)) {
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
                while ($row = mysql_fetch_assoc($result)) {
                    $content.='<tr>';
                    $content.='
                    <td>'.$i.'.'.$o++.'</td>
                    <td>'.$row['name'].'</td>
                    <td>'.$row['unit'].'&nbsp;</td>
                    <td>'.$row['desc'].'&nbsp;</td>
                    ';
    //              if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
                        $content.='<td class="span2">';
    //              else
    //                  $content.='<td class="span2">&nbsp;</td>';
    //              if ($_SESSION['pq_edit'])
                        $content.='<button class="icon-pencil mini m0" id="other_e_add_div" rel="?act=e_other&id='.$row['id'].'" title="Редактировать" /></button>&nbsp;';
    //              if ($_SESSION['pq_del'])
                        $content.='<button class="icon-cancel-2 mini m0" id="other_d_add_div" rel="?act=d_other&id='.$row['id'].'" title="Удалить"/></button>';
    //              if ($_SESSION['pq_edit'] || $_SESSION['pq_edit'])
                        $content.='</td>';
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
    $action='
    <div class="m0 text-left">
        <div class="horizontal-menu">
            <ul class="m0">
                '.$action.'
            </ul>
        </div>
    </div>';
    show_menu();
    die;
}

// Справочники
if (isset($_GET['act']) && $_GET['act'] == 'dirs') {
	$i=1;
	$title = 'Справочники';

	//if ($_SESSION['pq_add'] && mysql_result(mysql_query("SELECT COUNT(*) FROM `" . $table_pq . "` WHERE type = 1 AND node = " . clean($_GET['node_id']) . ";"), 0)==0 )

// редактирование Районов
	if($_GET['dir'] == 'area') {
		$i=1;
		$title.= ' > Район';
		$action.='
		<div class="span2 m5 input-control text">Район</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_area" type="button" />Добавить район</button>
		</div>';
		$sql = "SELECT * FROM `" . $table_area . "` AS ar1 ORDER BY name;";
		$result = mysql_query($sql);
		if (mysql_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span5">Наименование</td>';
			$content.='<td class="span8">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = mysql_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['name'].'&nbsp;</td>';
				$content.='<td>'.$row['desc'].'&nbsp;</td>';
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
	if($_GET['dir'] == 'street') {
		$i=1;
		$title.= ' > Улицы';
		$action.='
		<div class="span2 m5 input-control text">Улицы</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_street_name" type="button" />Добавить улицу</button>
		</div>';
		$sql = "SELECT sn1.*, ar1.name AS area_name FROM `" . $table_street_name . "` AS sn1 LEFT JOIN `" . $table_area . "` AS ar1 ON `sn1`.`area_id` = `ar1`.`id` ORDER BY `sn1`.`name`;";
		$result = mysql_query($sql);
		if (mysql_num_rows($result)) {
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
			while ($row = mysql_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['name'].'</td>';
				$content.='<td>'.$row['small_name'].'&nbsp;</td>';
				$content.='<td>'.$row['area_name'].'&nbsp;</td>';
				$content.='<td>'.($row['desc'] ? $row['desc'] : "&nbsp;").'</td>';
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
	if($_GET['dir'] == 'location') {
		$i=1;
		$title.= ' > Размещение';
		$action.='
		<div class="span2 m5 input-control text">Размещение</div>
		<div class="span m0 text-left">
		<button class="m0" id="in_div" rel="?act=n_location" type="button" />Добавить размещение</button>
		</div>';
		$sql = "SELECT * FROM `" . $table_location . "` ORDER BY location";
		$result = mysql_query($sql);
		if (mysql_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span6">Размещение</td>';
			$content.='<td class="span7">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = mysql_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['location'].'</td>';
				$content.='<td>'.$row['desc'].'&nbsp;</td>';
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
	if($_GET['dir'] == 'room') {
		$i=1;
		$title.= ' > Помещение';
		$action.='
		<div class="span2 m5 input-control text">Помещение</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_room" type="button" />Добавить Помещение</button>
		</div>';
		$sql = "SELECT * FROM `" . $table_room . "` ORDER BY room";
		$result = mysql_query($sql);
		if (mysql_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span6">Помещение</td>';
			$content.='<td class="span7">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = mysql_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['room'].'</td>';
				$content.='<td>'.$row['desc'].'&nbsp;</td>';
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
    if($_GET['dir'] == 'keys') {
        $i=1;
        $title.= ' > Ключи';
        $action.='
        <div class="span2 m5 input-control text">Ключи</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_key" type="button" />Добавить ключ</button>
        </div>';
        $sql = "SELECT k1.*, n1.address FROM `" . $table_keys . "` AS k1 LEFT JOIN `" . $table_node . "` AS n1 ON n1.id = k1.node_id ORDER BY LENGTH(k1.num), k1.num";
        $result = mysql_query($sql);
        if (mysql_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span2">Номер</td>';
            $content.='<td class="span6">Адрес</td>';
            $content.='<td class="span8">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = mysql_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['num'].'</td>';
                $content.='<td><a href="?act=s_pq&o_node&node_id='.$row['node_id'].'" target="_blank">'.$row['address'].'</a>&nbsp;</td>';
                $content.='<td>'.$row['desc'].'&nbsp;</td>';
                $content.='
                <td class="toolbar m0">
                    <button class="icon-pencil m0 mini" id="key_edit_in_div" rel="?act=e_key&id='.$row['id'].'" title="Редактировать"></button>
                    <button class="icon-cancel-2 m0 mini" id="key_del_in_div" rel="?act=d_key&id='.$row['id'].'" title="Удалить"></button>
                </td>';
                $content.='</tr>';
                $i++;
            }
            $content.='</table>';
        }
    }

// редактирование лифтёрок
    if($_GET['dir'] == 'lift') {
        $i=1;
        $title.= ' > Лифтёрки';
        $action.='
        <div class="span2 m5 input-control text">Лифтёрки</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_lift_type" type="button" />Добавить лифтёрку</button>
        </div>';
        $sql = "SELECT * FROM `" . $table_lift_type . "` ORDER BY name";
        $result = mysql_query($sql);
        if (mysql_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span6">Лифтёрка</td>';
            $content.='<td class="span5">Телефоны</td>';
            $content.='<td class="span7">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = mysql_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['tel'].'</td>';
                $content.='<td>'.$row['desc'].'&nbsp;</td>';
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
	if($_GET['dir'] == 'pq_type') {
		$i=1;
		$title.=' > Кроссы/Муфты';
		$action.='
		<div class="span2 m5 input-control text">Кроссы/Муфты</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_pq_type" type="button" />Добавить тип</button>
		</div>';
		$sql = "SELECT * FROM `" . $table_pq_type . "` AS pq_type ORDER BY type, name ;";
		$result = mysql_query($sql);
		if (mysql_num_rows($result)) {
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
			while ($row = mysql_fetch_assoc($result)) {
				if ($row['type'] == 0) $type = 'Кросс'; else $type = 'Муфта';
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
	if($_GET['dir'] == 'cable_type') {
		$i=1;
		$title.= ' > Кабели';
		$action.='
		<div class="span2 m5 input-control text">Кабели</div>
		<div class="span m0 text-left">
			<button class="m0" id="in_div" rel="?act=n_cable_type" type="button" />Добавить тип</button>
		</div>';
		$sql = "SELECT * FROM `" . $table_cable_type . "` AS cable_type ORDER BY name, fib ;";
		$result = mysql_query($sql);
		if (mysql_num_rows($result)) {
			$content='<table class="striped">';
			$content.='<tr>';
			$content.='<td class="span1">№</td>';
			$content.='<td class="span5">Наименование</td>';
			$content.='<td class="span1">Волокон</td>';
			$content.='<td class="span6">Описание</td>';
			$content.='<td class="span2">&nbsp;</td>';
			$content.='</tr>';
			$content.='<tr>';
			while ($row = mysql_fetch_assoc($result)) {
				$content.='<td>'.$i.'</td>';
				$content.='<td>'.$row['name'].'</td>';
				$content.='<td>'.$row['fib'].'</td>';
				$content.='<td>'.$row['desc'].'&nbsp;</td>';
				$content.='
				<td class="toolbar m0">
    				<button class="icon-pencil m0 mini" id="cable_edit_in_div" rel="?act=e_cable_type&id='.$row['id'].'" title="Редактировать"></button>
    				<button class="icon-cancel-2 m0 mini" id="cable_del_in_div" rel="?act=d_cable_type&id='.$row['id'].'" title="Удалить"></button>
				</td>';
				$content.='</tr>';
				$i++;
			}
			$content.='</table>';
		}
	}

// редактирование типов коммутаторов
    if($_GET['dir'] == 'switch_type') {
        $i=1;
        $title.= ' > Коммутаторы';
        $action.='
        <div class="span2 m5 input-control text">Коммутаторы</div>
        <div class="span m0 text-left">
        <button class="m0" id="in_div" rel="?act=n_switch_type" type="button" />Добавить Коммутатор</button>
        </div>';
        $sql = "SELECT * FROM `" . $table_switch_type . "` ORDER BY name";
        $result = mysql_query($sql);
        if (mysql_num_rows($result)) {
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
            while ($row = mysql_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['ports_num'].'</td>';
                $content.='<td>'.$row['unit'].'&nbsp;</td>';
                $content.='<td>'.$row['power'].'&nbsp;</td>';
                $content.='<td>'.$row['desc'].'&nbsp;</td>';
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
    if($_GET['dir'] == 'mc_type') {
        $i=1;
        $title.= ' > Медиаконвертеры';
        $action.='
        <div class="span2 m5 input-control text">Медиаконвертеры</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_mc_type" type="button" />Добавить Медиаконвертер</button>
        </div>';
        $sql = "SELECT * FROM `" . $table_mc_type . "` ORDER BY name";
        $result = mysql_query($sql);
        if (mysql_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span4">Название</td>';
            $content.='<td class="span1 span1_5">Мощность</td>';
            $content.='<td class="span7">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = mysql_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['power'].'&nbsp;</td>';
                $content.='<td>'.$row['desc'].'&nbsp;</td>';
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

// редактирование типов рам/ящиков
    if($_GET['dir'] == 'box_type') {
        $i=1;
        $title.= ' > Ящики/Рамы';
        $action.='
        <div class="span2 m5 input-control text">Ящики/Рамы</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_box_type" type="button" />Добавить Ящик/Раму</button>
        </div>';
        $sql = "SELECT * FROM `" . $table_box_type . "` ORDER BY name";
        $result = mysql_query($sql);
        if (mysql_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span4">Название</td>';
            $content.='<td class="span2">Юнитов</td>';
            $content.='<td class="span7">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = mysql_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['unit'].'&nbsp;</td>';
                $content.='<td>'.$row['desc'].'&nbsp;</td>';
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
    if($_GET['dir'] == 'ups_type') {
        $i=1;
        $title.= ' > ИБП';
        $action.='
        <div class="span2 m5 input-control text">ИБП</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_ups_type" type="button" />Добавить ИБП</button>
        </div>';
        $sql = "SELECT * FROM `" . $table_ups_type . "` ORDER BY name";
        $result = mysql_query($sql);
        if (mysql_num_rows($result)) {
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
            while ($row = mysql_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['unit'].'&nbsp;</td>';
                $content.='<td>'.$row['power'].'&nbsp;</td>';
                $content.='<td>'.$row['desc'].'&nbsp;</td>';
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
    if($_GET['dir'] == 'other_type') {
        $i=1;
        $title.= ' > Разное';
        $action.='
        <div class="span2 m5 input-control text">Разное</div>
        <div class="span m0 text-left">
            <button class="m0" id="in_div" rel="?act=n_other_type" type="button" />Добавить разное</button>
        </div>';
        $sql = "SELECT * FROM `" . $table_other_type . "` ORDER BY name";
        $result = mysql_query($sql);
        if (mysql_num_rows($result)) {
            $content='<table class="striped">';
            $content.='<tr>';
            $content.='<td class="span1">№</td>';
            $content.='<td class="span4">Название</td>';
            $content.='<td class="span2">Юнитов</td>';
            $content.='<td class="span7">Описание</td>';
            $content.='<td class="span2">&nbsp;</td>';
            $content.='</tr>';
            $content.='<tr>';
            while ($row = mysql_fetch_assoc($result)) {
                $content.='<td>'.$i.'</td>';
                $content.='<td>'.$row['name'].'</td>';
                $content.='<td>'.$row['unit'].'&nbsp;</td>';
                $content.='<td>'.$row['desc'].'&nbsp;</td>';
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
	show_menu();
}

// вывод списка кабелей в кроссе
if (isset($_GET['act']) && ( $_GET['act'] == 's_cable' || $_GET['act'] == 's_ports' ) && is_numeric($_GET['pq_id'])) {
    $i=1;
    // id кросса/муфты
    $pq_id = clean($_GET['pq_id']);
    // навигация
    $sql="SELECT n1.id AS id, pt.type AS type, p1.num AS num FROM `" . $table_pq . "` AS p1 , `" . $table_node . "` AS n1, `" . $table_pq_type . "` AS pt WHERE p1.node = n1.id AND p1.id=" . $_GET['pq_id'] . " AND p1.pq_type_id = pt.id;";
    $result = mysql_fetch_assoc(mysql_query($sql));
    //print_r($sql);
    // id узла
    $node_id = $result['id'];
    // тип и номер кросса/муфты
    $pq_type = $result['type'];
    $pq_num = $result['num'];
	$address=addr_id($node_id);
    
    if ($result['type'] == 0) $type = 'Кросс'; else $type = 'Муфта';
    if (isset($result['num'])) $num = ' №' . $result['num']; else $num = '';

    /*$action='<div class="m0 text-left">
    <div class="horizontal-menu">
	    <ul>
	    	<li><a href="?act=s_pq&o_node&node_id='.$node_id.'">'.$address.'</a></li>
		    <li'.($result['type']==0?' class="sub-menu"':'').'><a>'.$type.$num.'</a>
			'.($result['type']==0?'<ul><li><a href="?act=s_ports&pq_id='.$pq_id.'">Порты</a></li><li><a href="?act=s_cable&pq_id='.$pq_id.'">Кабеля</a></li></ul>':'').'
		    </li>
	    </ul>
    </div>
    </div>';*/
    $action='<div class="m0 text-left">
     <div class="horizontal-menu">
	    <ul>
		    <li><a href="?act=s_pq&o_node&node_id='.$node_id.'">'.$address.' ('.$type.$num.')</a></li>
			    '.($result['type']==0?'<li'.($_GET['act']=='s_cable'?' class="border-color-blueLight"':'').'><a href="?act=s_cable&pq_id='.$pq_id.'">Кабеля</a></li><li'.($_GET['act']=='s_ports'?' class="border-color-blueLight"':'').'><a href="?act=s_ports&pq_id='.$pq_id.'">Порты</a></li>':'').'
		    </li>
	    </ul>
    </div>
    </div>';
    if($_GET['act'] == 's_cable')
    {
    	$title='Узлы > '.$address.' > '.$type.$num.' > '.'Кабеля';
    	
    	if ($_SESSION['cable_add'])
    		$action='
    		<div class="span2 m0 text-left">
    			<button class="m0" id="cable_add_div" rel="?act=n_cable&pq_id=' . clean($_GET['pq_id']) . '" />Добавить кабель</button>
    		</div>'.$action;
	    // скрытые поля
		$content='<input type="hidden" id="node_id" value="' . $node_id . '">';
	    $content.='<input type="hidden" id="pq_id" value="' . clean($_GET['pq_id']) . '">';
	    $content.='<input type="hidden" id="pq_type" value="' . $pq_type . '">';
	    $content.='<input type="hidden" type="text" id="pq_num" value="' . $pq_num . '">';
	    // запрос
	    //$sql = "SELECT a.id, IF( a.pq_1 =".clean($_GET['pq_id']).", pq_1, pq_2 ) AS pq_1, IF( a.pq_1 =".clean($_GET['pq_id']).", pq_2, pq_1 ) AS pq_2, ct.fib, ct.name AS cable_name, IF( a.pq_1 =".clean($_GET['pq_id']).", `c1`.`address` , `c2`.`address` ) AS addr_1, IF( a.pq_1 =".clean($_GET['pq_id']).", `b1`.`type` , `b2`.`type` ) AS type_1, IF( a.pq_1 =".clean($_GET['pq_id']).", `b1`.`num` , `b2`.`num` ) AS num_1, IF( a.pq_1 =".clean($_GET['pq_id']).", `c2`.`address` , `c1`.`address` ) AS addr_2, IF( a.pq_1 =".clean($_GET['pq_id']).", `b2`.`type` , `b1`.`type` ) AS type_2, IF( a.pq_1 =".clean($_GET['pq_id']).", `b2`.`num` , `b1`.`num` ) AS num_2
	    $sql = "SELECT a.id, IF( a.pq_1 =".clean($_GET['pq_id']).", pq_1, pq_2 ) AS pq_1, IF( a.pq_1 =".clean($_GET['pq_id']).", pq_2, pq_1 ) AS pq_2, ct.fib, ct.name AS cable_name, IF( a.pq_1 =".clean($_GET['pq_id']).", `c1`.`address` , `c2`.`address` ) AS addr_1, IF( a.pq_1 =".clean($_GET['pq_id']).", `pt1`.`type` , `pt2`.`type` ) AS type_1, IF( a.pq_1 =".clean($_GET['pq_id']).", `b1`.`num` , `b2`.`num` ) AS num_1, IF( a.pq_1 =".clean($_GET['pq_id']).", `c2`.`address` , `c1`.`address` ) AS addr_2, IF( a.pq_1 =".clean($_GET['pq_id']).", `pt2`.`type` , `pt1`.`type` ) AS type_2, IF( a.pq_1 =".clean($_GET['pq_id']).", `b2`.`num` , `b1`.`num` ) AS num_2
									FROM `" . $table_cable . "` AS a, `" . $table_pq . "` AS b1, `" . $table_pq . "` AS b2, `" . $table_node . "` AS c1, `" . $table_node . "` AS c2, `" . $table_cable_type . "` AS ct, `" . $table_pq_type . "` AS pt1, `" . $table_pq_type . "` AS pt2
									WHERE (
	    								`a`.`pq_1` = `b1`.`id`
	    								AND `b1`.`node` = `c1`.`id`
									)
									AND (
	    								`a`.`pq_2` = `b2`.`id`
	    								AND `b2`.`node` = `c2`.`id`
									) AND (`a`.`pq_1`=".clean($_GET['pq_id'])." OR `a`.`pq_2`=".clean($_GET['pq_id']).") AND `a`.`cable_type` = `ct`.`id`
	    							AND b1.pq_type_id = pt1.id
	    							AND b2.pq_type_id = pt2.id";
	    //print_r($sql);
	    //die;
	    $result = mysql_query($sql);
	    if (mysql_num_rows($result)) {
	    	$content.='<table class="striped">';
	        while ($row = mysql_fetch_assoc($result)) {
				$fib='';
	            if ($row['type_1'] == 0) $type_1 = 'Кросс'; else $type_1 = 'Муфта';
	            if (isset($row['num_1'])) $num_1 = ' №' . $row['num_1']; else $num_1 = '';
				if ($row['type_2'] == 0) $type_2 = 'Кросс'; else $type_2 = 'Муфта';
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
	
	            $sql_node = "SELECT * FROM `" . $table_pq . "` AS p1 WHERE p1.id = " . $to_id;
	            $result_node = mysql_fetch_assoc(mysql_query($sql_node));
	            $to_node = $result_node['node'];
	            
	            $sql_node2 = "SELECT * FROM `" . $table_pq . "` AS p1 WHERE p1.id = " . $to_id;
	            $result_node2 = mysql_fetch_assoc(mysql_query($sql_node2));
	            
	            /*echo '<pre>';
	            print_r($result_node);
	            echo '</pre>';*/
	            //echo $sql_node.'<br>'.$to_id.' '.$from_id.'<br>';
	            $content.='<tr>';
	
	            $content.='<td class="span1">'.$i.'.</td>';
	            $content.='<td class="span6"> до <a href="?act=s_cable&pq_id='.$to_id.'">'.$to_addr.'</a></td>';
	            $content.='<td class="span4">тип: '.$row['cable_name'].'</td>';
				$content.='<td class="span2">';
	            //$content.='<button class="icon-pencil mini m0" id="edit_cable_div" onClick="javascript: window.location=\'?act=s_fiber&cable_id='.$row['id'].'\';" title="Изменить" /></button>&nbsp;';
	            if ($_SESSION['cable_move'] )
	            	$content.='<button class="icon-move mini m0" id="move_cable_div" rel="?act=e_cable&pq_id='.clean($_GET['pq_id']).'&cable_id='.$row['id'].'" title="Переместить" /></button>&nbsp;';
	            if ($_SESSION['cable_del_all'])
	            	$content.='<button class="icon-cancel-2 mini m0" id="del_cable_div" rel="?act=d_cable&pq_id='.clean($_GET['pq_id']).'&cable_id='.$row['id'].'" title="Удалить"/></button>';
				$content.='</td>';
	            $content.='</tr>';
	
	            // если задан кросс/муфта, то выводим список волокон в кабелях
	            $sql="SELECT a.id AS id, a.num AS num, g.id AS to_pq_id, e.cable_id AS to_cable_id, e.id AS to_id, e.num AS to_num,
	                        pt.type AS from_type, c.num AS from_num, g.type AS to_type, g.num AS to_num, cc.port, cc.id AS port_id
	                    FROM ".$table_pq." AS c, ".$table_pq_type." AS pt, ".$table_cable." AS b, ".$table_fiber." AS a
	                        LEFT JOIN ".$table_fiber_conn." AS d ON ( a.id = d.fiber_id_1 OR a.id = d.fiber_id_2 ) AND d.node_id = ".$node_id."
	                        LEFT JOIN ".$table_fiber." AS e ON e.id = IF(a.id = d.fiber_id_1, d.fiber_id_2, IF(a.id = d.fiber_id_2,d.fiber_id_1,NULL))
	                        LEFT JOIN ".$table_cable." AS f ON f.id = e.cable_id
	                        LEFT JOIN (SELECT pq.*, pt.type FROM ".$table_pq." AS pq, ".$table_pq_type." AS pt WHERE pq.node = ".$node_id." AND pq.pq_type_id = pt.id) AS g ON g.id = f.pq_1 OR g.id = f.pq_2
	                        
	                        LEFT JOIN `".$table_cruz_conn."` AS cc ON cc.pq_id = ".$pq_id." AND cc.fiber_id = a.id
	                        
	                    WHERE a.cable_id = ".$row['id']."
	                        AND a.cable_id = b.id
	                        AND c.id =  ".$pq_id."
	                        AND c.node = ".$node_id."
							AND c.pq_type_id=pt.id
						GROUP BY a.num";
	            $result_fib = mysql_query($sql);

	            $content.='</tr>';
	                if (mysql_num_rows($result_fib)) {
	                    $content.='<tr><td colspan="4"><table>';
						$content.='
	    					<tr>
	    					   '.($pq_type!=1?'<td class="span1 span1_5">Порт</td>':'').'
	    					   <td class="span1">ОВ</td>
	    					   '.($pq_type!=1?'<td class="span2">Кросс/муфта</td><td class="span5">Кабель</td>':'<td class="span7">Кабель</td>').'
	    					   <td class="span1 span1_5">ОВ'.($pq_type!=1?' [порт]':'').'</td>
	    					   <td class="span2" colspam="2">&nbsp;</td>
	                    	</tr>';
						$content.='<tr>';
	
	                    while ($row_fib = mysql_fetch_assoc($result_fib)) {
	                        $from_fib = $row_fib['num'];

	                        $fiber_id = $row_fib['id'];
	                        $cable_id = $row_fib['to_cable_id'];
	                        if($row_fib['port_id']) $port_id = $row_fib['port_id']; else $port_id=0;
	                        //echo 'cable: '.$cable_id.' port: '.$port_id."<br>";
                            /*echo '<pre>';
                            print_r($row_fib);
                            echo '</pre>';*/
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
	                            <td class="text-left input-control text m0">
	                            	<select class="ports" id="ports_' . $fiber_id . '" disabled>
	                            	</select><input type="hidden" id="curr_port_id_' . $fiber_id . '" value="">
	                            </td>';
	                        $content_js.='fiber_ports("' . clean($_GET['pq_id']) . '","' . $fiber_id . '",';
	                        //if ($_SESSION['port_edit'])
	                        if (!$_SESSION['port_edit'] || ($port_id!=0 && $to_fiber_id ) || ($port_id==0 && $to_fiber_id ))
	                        	$content_js.= 'true';
	                        else
	                        	$content_js.= 'false';
	                        $content_js.= ');';
	                        
	                        $content.='<td class="m5">'.$from_fib.'</td>';
							$content.='<input type="hidden" id="pq_id_' . $fiber_id . '" value="' . $pq_id_ . '">';
							$content.='<input type="hidden" id="to_node_' . $fiber_id . '" value="' . $to_node . '">';
	                        // селект (выбор кросса/муфты)
							if($pq_type!=1) {
		                        $content.='<td class="input-control text m0"><select id="pq_id_' . $fiber_id . '" disabled></select></td></td>';
		                        $content_js.='pq_list("' . $node_id . '","' . $pq_id_ . '","' . $pq_type . '","' . $pq_num . '","' . $fiber_id . '",';
		                        //если нельзя редактировать кабель, дизеблим селект
		                        if ($_SESSION['cable_edit'])
		                        	$content_js.= '"true"';
		                        else
		                        	$content_js.= '""';
		                        $content_js.= ');';
							}
	                        // селект (выбор кабеля)
	                        $content.='<input type="hidden" value="' . $cable_id . '">';
	                        // если нельзя редактировать кабель, дизеблим селект
	                        if ($_SESSION['cable_edit']) {
	                            $content.='
	                            <td class="text-left input-control text m0">
	                                <select id="cable_id_' . $fiber_id . '"></select>
	                            </td>';
	                            $content_js.='cable_list("' . $pq_id_ . '","' . $cable_id . '","' . $fiber_id . '");';
	                        } else {
	                            $content.='
	                            <td class="text-left input-control text m0">
	                                <select id="cable_id_' . $fiber_id . '" disabled></select>
	                            </td>';
	                            $content_js.='cable_list("' . $pq_id_ . '","' . $cable_id . '","' . $fiber_id . '");';
	                        }
	                        $content.='<input type="hidden" value="' . $to_fiber_id . '">';
	                        $content.='
	                            <td class="text-left input-control text m0">
	                                <select class="span1" id="fiber_id_' . $fiber_id . '" disabled></select>
	                            </td>';
	                        $content_js.='fiber_list("' . $node_id . '","' . $pq_id_ . '","' . $cable_id . '","' . $to_fiber_id . '","' . $fiber_id . '",';
	                        //если нельзя редактировать кабель, дизеблим селект
	                        if ($_SESSION['cable_edit'])
	                        	$content_js.= '"true"';
	                        else
	                        	$content_js.= '""';
	                        $content_js.= ','.$port_id.');';
	                        
	                        
	                        /*$content.='fiber_list("' . $node_id . '","' . $pq_id_ . '","' . $cable_id . '","' . $to_fiber_id . '","' . $fiber_id . '","",'.$port_id.')<br>';
	                        $content.='port_id: '.$port_id.'<br>';*/
	                        
	                        $content.='<td class="toolbar m0">';
		                        if ($_SESSION['cable_edit'] || $_SESSION['cable_del']) {
		                            if ($to_fiber_id) {
		                                if ($_SESSION['cable_del'])
		                                    $content.='<button class="icon-cancel-2 m0" id="del_fib_conn_'.$fiber_id.'" title="Удалить"></button>';
		                                else
		                                    $content.='<button class="m0" disabled ></button>';
		                            } else {
		                                if ($_SESSION['cable_edit'])
		                                    $content.='<button class="icon-checkmark m0" id="new_fib_conn_'.$fiber_id.'" title="Ok"></button>';
		                                else
		                                    $content.='<button class="m0" disabled ></button>';
		                            }
		                        }
		                        // будет всегда выводить кнопки
		                        if ($_SESSION['fiber_find']) {
		                            $content.='&nbsp;<button class="icon-share-2 m0" id="find_fib_conn_'.$fiber_id.'" title="Отследить ОВ"></button>';
		                            $content.='<button class="icon-cancel m0" id="f_fiber_clean_'.$fiber_id.'" title="Очистить" style="display:none" ></button>';
		                            $content.='&nbsp;<button class="icon-link m0" id="find_fib_used_'.$fiber_id.'" title="Занято?"></button>';
		                        }
	                        $content.='</td>';
                            //<div class="f_fiber" id="f_fiber_'.$fiber_id.'"></div>
	                        $content.='
	                            <tr class="f_fiber" style="display: none;" id="f_fiber_tr_'.$fiber_id.'"><td colspan="6">
	                                <div class="f_fiber" id="f_fiber_'.$fiber_id.'"></div>
	                                <input type="hidden" type="text" id="f_fiber_pq_'.$fiber_id.'" value="'.$to_id.'">
	                            </td></tr>';
	                        $content.='</tr>';
	                    }
	                    $content.='</table></td></tr>';
	                }
	            $i++;
	        }
	        $content.='</table>';
	        $content.='<script type="text/javascript">'.$content_js.'</script>';
	    }
	} else {
		$title='Узлы > '.$address.' > '.$type.$num.' > '.'Порты';
		
		//$sql="SELECT p1.id AS id, pq_t.ports_num AS pq_type_ports FROM `".$table_pq."` as p1, `".$table_pq_type."` AS pq_t WHERE p1.pq_type_id = pq_t.id AND p1.type = pq_t.type AND `p1`.`id` = ".clean($_GET['pq_id']).";";
		$sql="SELECT p1.id AS id, pq_t.ports_num AS pq_type_ports
				FROM `".$table_pq."` as p1, `".$table_pq_type."` AS pq_t
				WHERE p1.pq_type_id = pq_t.id
				AND `p1`.`id` = ".clean($_GET['pq_id']).";";
		$result = mysql_fetch_assoc(mysql_query($sql));
		
		if ($_SESSION['port_add'] && mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_cruz_conn."` WHERE pq_id=".clean($_GET['pq_id']).";"), 0)!=$result['pq_type_ports']) {
			$action='<div class="span2 m0 text-left">
				<button class="m0" id="port_add_div" rel="?act=n_port&pq_id='.clean($_GET['pq_id']).'&all" />Добавить все порты</button>
			</div>'.$action;
		}

		$content.='<input type="hidden" id="pq_id" value="' . clean($_GET['pq_id']) . '">';

		//$sql = "SELECT *,`pq`.`id` AS id FROM `".$table_node."` AS node, `".$table_pq."` AS pq LEFT JOIN `pq_type` AS pq_t ON pq.pq_type_id = pq_t.id AND pq.type = pq_t.type WHERE `pq`.`node`=`node`.`id` AND `pq`.`id` = ".$pq_id." ORDER BY `pq`.`node`";
		$sql = "SELECT *,`pq`.`id` AS id
					FROM `".$table_node."` AS node, `".$table_pq."` AS pq
					LEFT JOIN `pq_type` AS pq_t ON pq.pq_type_id = pq_t.id
					WHERE `pq`.`node`=`node`.`id` AND `pq`.`id` = ".$pq_id." ORDER BY `pq`.`node`";
		$result = mysql_query($sql);
		if (mysql_num_rows($result)) {
			$content.='<table class="striped">';
			
			while ($row = mysql_fetch_assoc($result)) {
				//if ($row['type'] == 0) $type = 'Кросс'; else $type = 'Муфта';
				//if (isset($row['num'])) $num = ' №' . $row['num']; else $num = '';
				$sql = "SELECT * FROM `" . $table_cruz_conn . "` WHERE pq_id = " . $row['id'] . " ORDER BY port;";
				$result_port = mysql_query($sql);
				if (mysql_num_rows($result_port)) {
					$content.='<tr>
						<td class="span1">Порт</td>
						<td class="span1">Занят</td>
						<td class="span1">ОВ</td>
						<td class="span6">Кабель</td>
						<td class="span5">Описание</td>
						<td class="span1 span1_5">&nbsp;</td>
					</tr>';

					while ($row_port = mysql_fetch_assoc($result_port)) {
						$content.='<tr>';
						$content.='<td>'.$row_port['port'].'</td>';
						if ($row_port['fiber_id']) {
							/*$sql = "SELECT f1.id, f1.num, p1.type AS pq_type, p1.num AS pq_num, n1.address AS addr
								FROM `" . $table_fiber . "` AS f1, `" . $table_cable . "` AS c1, `" . $table_pq . "` AS p1, `" . $table_node . "` AS n1
								WHERE f1.id = " . $row_port['fiber_id'] . "
								AND f1.cable_id = c1.id
								AND p1.id = IF( c1.pq_2 = " . $pq_id . ", c1.pq_1, c1.pq_2 )
								AND n1.id = p1.node";*/
							$sql = "SELECT f1.id, f1.num, pt.type AS pq_type, p1.num AS pq_num, n1.address AS addr
								FROM `" . $table_fiber . "` AS f1, `" . $table_cable . "` AS c1, `" . $table_pq . "` AS p1, `" . $table_node . "` AS n1, `" . $table_pq_type . "` AS pt
								WHERE f1.id = " . $row_port['fiber_id'] . "
								AND f1.cable_id = c1.id
								AND p1.id = IF( c1.pq_2 = " . $pq_id . ", c1.pq_1, c1.pq_2 )
								AND p1.pq_type_id = pt.id
								AND n1.id = p1.node";
							$row_cable_fib = mysql_fetch_assoc(mysql_query($sql));
							if ($row_cable_fib['pq_type'] == 0) $type = 'Кросс'; else $type = 'Муфта';
							if (isset($row_cable_fib['pq_num'])) $num = ' №' . $row_cable_fib['pq_num']; else $num = '';
							$content.='<td><label class="checkbox"><input type="checkbox" id="port_used_'.$row_port['id'].'" '.($row_port['used']==1?'checked':'').'><span>&nbsp;</span></label></td>';
							$content.='<td>'.$row_cable_fib['num'].'</td>';
							$content.='<td>'.$row_cable_fib['addr'].' ('.$type.$num.')'.'</td>';
							
							if ($_SESSION['port_edit_desc'])
								$content.='
									<td class="m0 input-control text"><input type="text" id="p_desc_'.$row_port['id'].'" onchange="document.getElementById(\'p_desc_b_'.$row_port['id'].'\').click();" value="'.$row_port['desc'].'" placeholder="Описание" /></td>
									<td class="toolbar m0">
										<button class="icon-checkmark m0" id="p_desc_b_'.$row_port['id'].'" title="Ok"></button>
									</td>';
							else
								$content.='<td>'.$row_port['desc'].'</td>';
						} else {
							$content.='<td>&nbsp;</td>';
							$content.='<td>&nbsp;</td>';
							$content.='<td>&nbsp;</td>';

							if ($_SESSION['port_edit_desc'])
							{
								$content.='
									<td class="m0 input-control text"><input type="text" id="p_desc_'.$row_port['id'].'" onchange="document.getElementById(\'p_desc_b_'.$row_port['id'].'\').click();" value="'.$row_port['desc'].'" placeholder="Описание" /></td>
									<td class="toolbar m0">
										<button class="icon-checkmark m0" id="p_desc_b_' . $row_port['id'] . '" title="Ok"></button>&nbsp;';
							if ($_SESSION['port_del'])
								$content.='<button class="icon-cancel m0" id="p_desc_d_'.$row_port['id'].'" title="Удалить"></button>';
								$content.='</td>';
							}
							else
								$content.='<td>'.$row_port['desc'].'</td>';
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
	}
    show_menu();
    die;
}

// ввод нового волокна
if (isset($_GET['act']) && ($_GET['act'] == 'n_fiber_' || $_GET['act'] == 'e_fiber_')) {
    $sql = "SELECT `a`.*, `c1`.`address` as addr_1, `b1`.`type` as type_1, `b1`.`num` as num_1, `c2`.`address` as addr_2, `b2`.`type` as type_2, `b2`.`num` as num_2
				FROM `" . $table_cable . "` AS a, `" . $table_pq . "` AS b1, `" . $table_pq . "` AS b2, `" . $table_node . "` AS c1, `" . $table_node . "` AS c2
				WHERE (
				`a`.`pq_1` = `b1`.`id`
				AND `b1`.`node` = `c1`.`id`
		)
				AND (
				`a`.`pq_2` = `b2`.`id`
				AND `b2`.`node` = `c2`.`id`
		) ";
    $result = mysql_query($sql);
    $select_fiber = '<select id="fiber"></select>';
    if (mysql_num_rows($result)) {
        $select_cable = '<select id="cable">';
        $select_cable .= '<option value="0">Выберите кабель</option>';
        while ($row = mysql_fetch_assoc($result)) {
            // выводим кабель в список, если количество занятых волокон меньше общего количества волокон в кабеле
            $fib_busy = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . $table_fiber . "` WHERE `cable_id`='" . $row['id'] . "';"), 0);
            if ($fib_busy < $row['fib']) {
                if ($row['type_1'] == 0)
                    $type_1 = 'Кросс';
                else
                    $type_1 = 'Муфта';
                if (isset($row['num_1']))
                    $num_1 = ' №' . $row['num_1'];
                else
                    $num_1 = '';

                if ($row['type_2'] == 0)
                    $type_2 = 'Кросс';
                else
                    $type_2 = 'Муфта';
                if (isset($row['num_2']))
                    $num_2 = ' №' . $row['num_2'];
                else
                    $num_2 = '';

                $pq_addr_1 = $row['addr_1'] . ' (' . $type_1 . $num_1 . ')';
                $pq_addr_2 = $row['addr_2'] . ' (' . $type_2 . $num_2 . ')';

                $select_cable .= '<option value="' . $row['id'] . '"';
                if (clean($_GET['cable_id']) == $row['id']) {
                    $select_cable .= " SELECTED";
                    // вызывает заполнение списка свободных волокон
                    $select_fiber .= '<script type="text/javascript">fiber_list_free(' . $row['id'] . ');</script>';
                }
                if ($fib_busy == 0)
                    $select_cable .= '>' . $pq_addr_1 . ' - ' . $pq_addr_2 . ' [' . $row['fib'] . ' ОВ]</options>';
                else
                    $select_cable .= '>' . $pq_addr_1 . ' - ' . $pq_addr_2 . ' [' . $row['fib'] . ' ОВ / ' . ($row['fib'] - $fib_busy) . ']</options>';
            }
        }
        $select_cable .= '</select>';
    }
    $text = '
		<div id="new_fiber" style="new_fiber">
		<input type="hidden" id="act" value="' . clean($_GET['act']) . '" />
		<input type="hidden" id="id" value="' . $cable_id . '" />
		' . $select_cable . '&nbsp;
		' . $select_fiber . '&nbsp;
		<input type="button" id="new_fiber" value="ok" />
		</div>';
    $text .= '<div class="clear"></div>';
    show_menu();
    echo $text;
}
/////////////////

if (empty($_GET)) {
    $text='Главная страница';
    show_menu($text);
}
?>
