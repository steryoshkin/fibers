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
