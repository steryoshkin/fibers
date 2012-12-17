<?

// подключение к базе, пока под временным логинпассом
// логин пользователя БД
	$user_db="user";
// пароль пользователя БД
	$user_pass="pass";
// хост БД
	$host_db="localhost";
// БД
	$db_name="fib";

	$con_id=@mysql_connect($host_db ,$user_db , $user_pass); 
	@mysql_select_db($db_name);
	@mysql_query('set names "utf8"');
?>
