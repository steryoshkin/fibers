<?php

if (isset($_GET['noactive'])) {
	@header('Content-Type: text/html; charset=utf-8');
	echo 'Учетная запись не активна...';
	die;
}

header("Location: /fibers");

////////////////////
$t=microtime(1);
include_once ('fibers/engine/setup.php');
include_once ('fibers/engine/db.php');

if (isset($_GET['act']) && $_GET['act'] == 'login') {
	$title='Логинься давай';
	$menu='<h1>Авторизация</h1>';
	$content='
        <form method="post" action="/fibers/auth.php">
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

// функция вывода меню
function show_menu() {
	global $t;
	global $title;
	global $action;
	global $content;
	global $menu;
	$text='
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>'.$title.'</title>
    <link href="/fibers/css/modern.css" rel="stylesheet">
    <link href="/fibers/css/modern-responsive.css" rel="stylesheet">
    <link href="/fibers/css/site.css" rel="stylesheet" type="text/css">
    <!--<link href="/fibers/css/style_2.css" rel="stylesheet" type="text/css">-->
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
	echo "<p>".(microtime(1)-$t)."</p>";
}

if (empty($_SESSION['logged_user_fibers']) && $_SERVER['REQUEST_URI'] != $login_page) {
	header("Location: " . $login_page);
}
?>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ПТО - РЦТК джегерме-берме</title>
    <link href="fibers/css/modern.css" rel="stylesheet">
    <link href="fibers/css/modern-responsive.css" rel="stylesheet">
    <link href="fibers/css/site.css" rel="stylesheet" type="text/css">
    <link href="fibers/css/style_2.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="fibers/js/lib/jquery-1.7.1-min.js"></script>
    <script type="text/javascript" src="fibers/js/action.js"></script>
</head>
<body class="modern-ui">
	<div class="page">
        <div class="page-header">
            <div class="page-header-content bg-color-blue">
				<h1>ПТО</h1>
				<h3><?php echo $_SESSION['user']; ?></h3>
            </div>
        </div>
 
        <div class="page-region">
            <div class="page-region-content">
			        <a class="button" href="/fibers">Сеть РЦТК</a>
					<a class="button" href="#" onclick="$('#submit_doc').click();" >Документооборот</a>
					<a class="button" href="#" onclick="$('#submit_agents').click();" >Агенты</a>
					<a class="button" href="http://syslog.sd.rdtc.ru/switches/" target="_blank">Коммутаторы</a>
					<a class="button" href="http://pto.rdtc.ru:8080/dashboard/" target="_blank">OpenGeo</a>
					<a class="button" href="http://kuzkom.ru/page2/" target="_blank">ЖКХ Новокузнецка</a>
					<a class="button" href="/fibers/geomap.php" target="_blank">Карто!!!</a>
					<a class="button" href="/?logout">Выйти</a>
<?php
	//if($_SERVER[REMOTE_ADDR]=='192.168.6.12') echo '<a class="button" href="http://kuzkom.ru/page2/" target="_blank">ЖКХ Новокузнецка</a>';
	include_once ('fibers/engine/setup.php');
	include_once ('fibers/engine/db.php');
	if(is_numeric($_SESSION['logged_user_fibers_id'])) $doc_user_pass=pg_fetch_assoc(pg_query("SELECT doc_user, doc_pass, agents_user, agents_pass FROM " . $table_user . " WHERE id=" . $_SESSION['logged_user_fibers_id'] . ";"));
?>
            </div>
        </div>
    </div>
    <div style="display:none;">
		<form method="post" action="http://abon.rdtc.ru/doc/index.php?module=auth" target="_blank">
			<input type="text" name="user" value="<?php echo $doc_user_pass['doc_user']; ?>">
			<input type="text" name="password" value="<?php echo $doc_user_pass['doc_pass'];?>">
			<input id="submit_doc" type='submit' value='вход' name='act'>
		</form>
		<form method="post" action="http://abon.rdtc.ru/agents/auth.php" target="_blank">
			<input type="text" name="user" value="<?php echo $doc_user_pass['agents_user']; ?>">
			<input type="text" name="password" value="<?php echo $doc_user_pass['agents_pass'];?>">
			<input id="submit_agents" type='submit' value='ок' name='act'>
		</form>
	</div>
</body>
</html>