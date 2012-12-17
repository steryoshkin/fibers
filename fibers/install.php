<?

	include_once ('./engine/setup.php');
	include_once ('./engine/db.php');
	
	if (empty($_POST)) {
    $content='
        <form method="post" action="install.php">
        	Введите существующие логин/пасс и хост админа БД MySQL<br>
			user: <input type="text" name="user_db" value="'.($_POST['user_db']?$_POST['user_db']:'user').'"/><br>
			pass: <input type="password" name="password_db" value="'.($_POST['password_db']?$_POST['password_db']:'pass').'"/><br>
			host: <input type="text" name="host_db" value="'.($_POST['host_db']?$_POST['host_db']:'localhost').'"/><br>
			<input type="submit" value="тест" name="db_test">
        </form>
    ';
    echo $content;
    //die;
}

	if (isset($_POST['db_test'])) {
		if(!@mysql_connect($_POST['host_db'] ,$_POST['user_db'] , $_POST['password_db'])) die('Не удается соединиться с БД');
		mysql_query('set names "utf8"');

		$dbs = mysql_query("SHOW DATABASES");
		if(!$dbs) exit(mysql_error());
		$flag = false;
		while($data_base = mysql_fetch_array($dbs,MYSQL_NUM))
		{
			if($data_base[0] == $db_name)
			{
				$flag = true;
				break;
			}
		}
		if($flag) {
			echo "База данных $db cуществует<br>Необходимо удалить вручную или использовать другое имя";
			//die;
		}
		//echo "База данных $db не cуществует";
		mysql_query("CREATE DATABASE ".$db_name.";");
		mysql_select_db($db_name);
		mysql_query("CREATE USER '".$user_db."'@'".$host_db."' IDENTIFIED BY '***';");
		
		mysql_query("GRANT USAGE ON * . * TO '".$user_db."'@'".$host_db."' IDENTIFIED BY '***' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;");
		mysql_query("GRANT ALL PRIVILEGES ON `".$db_name."` . * TO '".$user_db."'@'".$host_db."' WITH GRANT OPTION ;");
		mysql_query("SET PASSWORD FOR '".$user_db."'@'".$host_db."' = PASSWORD( '".$user_pass."' )");
		SplitSQL('./fib1.sql');
		mysql_query("INSERT INTO `".$db_name."`.`users` ( `id`, `login`, `password`, `node_add`, `node_edit`, `node_del`, `pq_add`, `pq_edit`, `pq_del`, `cable_add`, `cable_edit`, `cable_move`, `cable_del`, `cable_del_all`, `fiber_add`, `fiber_del`, `fiber_find`, `port_add`, `port_edit`, `port_del`, `port_edit_desc`, `name`, `status`) VALUES ( NULL, 'test', MD5('test'), '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'test user',  '1');");
		//mysql_close($con_id);
		die('Всё вроде хорошо.');
	}

function SplitSQL($file, $delimiter = ';')
{
	set_time_limit(0);
	if (is_file($file) === true)
	{
		$file = fopen($file, 'r');
		if (is_resource($file) === true)
		{
			$query = array();
			while (feof($file) === false)
			{
				$query[] = fgets($file);
				if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1)
				{
					$query = trim(implode('', $query));
					if (mysql_query($query) === false)
					{
						echo '<h3>ERROR: ' . $query . '</h3>' . "\n";
					}
					/*else
					{
						echo '<h3>SUCCESS: ' . $query . '</h3>' . "\n";
					}*/
					while (ob_get_level() > 0)
					{
						ob_end_flush();
					}
					flush();
				}
				if (is_string($query) === true)
				{
					$query = array();
				}
			}
			return fclose($file);
		}
	}
	return false;
}
?>