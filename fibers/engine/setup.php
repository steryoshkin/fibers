<?php
	@header('Content-Type: text/html; charset=utf-8');
	include_once ('class/xml2array.php');
	
	$host='62.231.168.109';
	$addr_obl='Сургутский район';

	session_start();
	
	if (isset($_GET['logout'])){
		session_destroy();
		header("Location: /");
		die;
	}

	$login_page="/fibers/index.php?act=login";
	
	$table_user='fibers.users';
	$table_node='fibers.node';
	$table_node_new='fibers.node_new';
	$table_pq='fibers.pq';
	$table_pq_schem='fibers.pq_schem';
	$table_cable='fibers.cable';
	$table_cable_reserve='fibers.cable_reserve';
	$table_fiber='fibers.fiber';
	$table_fiber_type='fibers.fiber_type';
	$table_fiber_conn='fibers.fiber_conn';
	$table_cruz_conn='fibers.cruz_conn';
    $table_pq_type='fibers.pq_type';
    $table_cable_type='fibers.cable_type';
    
    $table_city='fibers.city';
    $table_area='fibers.area';
    $table_street_name='fibers.street_name';
    $table_street_num='fibers.street_num';
    $table_location='fibers.location';
    $table_room='fibers.room';
    $table_keys='fibers.keys';
    $table_lift='fibers.lift';
    $table_lift_type='fibers.lift_type';
    $table_descrip='fibers.descrip';
    $table_switches='fibers.switches';
    $table_mag_switch='fibers.mag_switch';
    $table_mag_switch_tmp='fibers.mag_switch_tmp';
    $table_mag_sw_gr='fibers.mag_sw_gr';
    $table_switch_type='fibers.switch_type';
    $table_mc='fibers.mc';
    $table_mc_type='fibers.mc_type';
    $table_node_type='fibers.node_type';
    $table_box='fibers.box';
    $table_box_type='fibers.box_type';
    $table_ups='fibers.ups';
    $table_ups_type='fibers.ups_type';
    $table_other='fibers.other';
    $table_other_type='fibers.other_type';
    $table_sn='fibers.sn';
    $table_color='fibers.color';
    $table_log='fibers.log';

    $switch_id='switch';
    $mc_id='mc';
    $ups_id='ups';

    $cable_color=array(
		"cable_2"=>"#8A2BE2",
		"cable_4"=>"#DC143C",
    	"cable_6"=>"#008080",
		"cable_8"=>"#0000FF",
		"cable_16"=>"#FFD700",
    	"cable_24"=>"#008000",
    	"cable_32"=>"#00BFFF",
    	"cable_48"=>"#FF7F50",
    	"cable_64"=>"#A9A9A9",
    	"cable_96"=>"#000000"
    );
    
    $mag_percent=array(
    	1=>array(0, 19, "FF0000"),
    	2=>array(20, 59, "FFFF00"),
    	3=>array(60, 79, "008000"),
    	4=>array(80, 100, "0000FF")
    );

    function convert_to_csv($input_array, $output_file_name, $delimiter)
    {
    	/** open raw memory as file, no need for temp files */
    	$temp_memory = fopen('php://memory', 'w');
    	/** loop through array  */
    	foreach ($input_array as $line) {
    		/** default php csv handler **/
    		fputcsv($temp_memory, $line, $delimiter);
    	}
    	/** rewrind the "file" with the csv lines **/
    	fseek($temp_memory, 0);
    	/** modify header to be downloadable csv file **/
    	header('Content-Type: application/csv');
    	header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
    	/** Send file to browser for download */
    	fpassthru($temp_memory);
    }
    
    //$group_access['node']
    $group=array(
    		0=>array('name'=>'Админ',
				'dirs'=>1,			// доступ в справочники
				'plan'=>1,			// доступ в план
				'dirs_users'=>1,	// доступ в справочник пользователей
				'key'=>0,			// показывать кнопку ключей
				'key_edit'=>1,		// добавлять, редактировать, удалять ключи
				'node'=>1,			// доступ в список узлов
				'node_add'=>1,		// добавление узла
    			'prompt'=>0,		// подсказка для тупых
				'node_edit'=>1,		// редактирование узла
				'node_del'=>1,		// удаление узла
				'pq'=>1,			// доступ в узел
				'incorrect'=>1,		// доступ к галке проблемма
    			'u_const'=>1,		// доступ к галке объекты в стадии строительства
				'o_node'=>1,		// доступ в список оптического оборудования узла
				'o_node_add'=>1,	// добавление кросса/муфты
				'o_node_edit'=>1,	// редактирование кросса/муфты
				'o_node_del'=>1,	// удаление кросса/муфты
				'p_node'=>1,		// доступ в паспорт узла
    			'p_node_add'=>1,	// добавление в паспорт узла
    			'p_node_edit'=>1,	// редактирования в паспорте узла
    			'p_node_del'=>1,	// удаление в паспорте узла
    			'cable'=>1,			// доступ к списку кабелей в кроссе/муфте
    			'cable_add'=>1,		// добавление кабеля
    			'cable_edit'=>1,	// редактирование кабеля
    			'cable_del'=>1,		// удаление кабеля
    			'cable_geom'=>1,	// доступ на сброс координат кабеля
    			'port_edit'=>1,		// редактирование портов
    			'port_conn'=>1,		// соединение портов
    			'fiber_edit'=>1,	// редактирование волокон
    			'cable_edit'=>1,	// редактирование кабеля
    			'port_desc'=>1,		// редактирование занятости портов
    			'map_type'=>1,		// тип карты 0 - без слоев, 1 - все слои, 2 - слой для абонентского
    			'map_layer'=>0		// слой: 0 - опенстритмап, 1 - локальный
    		),
    		5=>array('name'=>'Проектный отдел',
    				'dirs'=>1,			// доступ в справочники
    				'plan'=>1,			// доступ в план
    				'dirs_users'=>0,	// доступ в справочник пользователей
    				'key'=>0,			// показывать кнопку ключей
    				'key_edit'=>1,		// добавлять, редактировать, удалять ключи
    				'node'=>1,			// доступ в список узлов
    				'node_add'=>1,		// добавление узла
    				'prompt'=>1,		// подсказка для тупых
    				'node_edit'=>1,		// редактирование узла
    				'node_del'=>1,		// удаление узла
    				'pq'=>1,			// доступ в узел
    				'incorrect'=>1,		// доступ к галке проблемма
    				'u_const'=>1,		// доступ к галке объекты в стадии строительства
    				'o_node'=>1,		// доступ в список оптического оборудования узла
    				'o_node_add'=>1,	// добавление кросса/муфты
    				'o_node_edit'=>1,	// редактирование кросса/муфты
    				'o_node_del'=>1,	// удаление кросса/муфты
    				'p_node'=>1,		// доступ в паспорт узла
    				'p_node_add'=>1,	// добавление в паспорт узла
    				'p_node_edit'=>1,	// редактирования в паспорте узла
    				'p_node_del'=>1,	// удаление в паспорте узла
    				'cable'=>1,			// доступ к списку кабелей в кроссе/муфте
    				'cable_add'=>1,		// добавление кабеля
    				'cable_edit'=>1,	// редактирование кабеля
    				'cable_del'=>1,		// удаление кабеля
    				'cable_geom'=>1,	// доступ на сброс координат кабеля
    				'port_edit'=>1,		// редактирование портов
    				'port_conn'=>1,		// соединение портов
    				'fiber_edit'=>1,	// редактирование волокон
    				'cable_edit'=>1,	// редактирование кабеля
    				'port_desc'=>1,		// редактирование занятости портов
    				'map_type'=>1,		// тип карты 0 - без слоев, 1 - все слои, 2 - слой для абонентского
    				'map_layer'=>0		// слой: 0 - опенстритмап, 1 - локальный
    		),
    		10=>array('name'=>'Просмотр',
				'dirs'=>0,			// доступ в справочники
				'plan'=>1,			// доступ в план
				'dirs_users'=>0,	// доступ в справочник пользователей
				'key'=>0,			// показывать кнопку ключей
				'key_edit'=>0,		// добавлять, редактировать, удалять ключи
				'node'=>1,			// доступ в список узлов
				'node_add'=>0,		// добавление узла
				'prompt'=>0,		// подсказка для тупых
				'node_edit'=>0,		// редактирование узла
				'node_del'=>0,		// удаление узла
				'pq'=>1,			// доступ в узел
				'incorrect'=>0,		// доступ к галке проблемма
				'u_const'=>1,		// доступ к галке объекты в стадии строительства
				'o_node'=>1,		// доступ в список оптического оборудования узла
				'o_node_add'=>0,	// добавление кросса/муфты
				'o_node_edit'=>0,	// редактирование кросса/муфты
				'o_node_del'=>0,	// удаление кросса/муфты
				'p_node'=>1,		// доступ в паспорт узла
    			'p_node_add'=>0,	// добавление в паспорт узла
    			'p_node_edit'=>0,	// редактирования в паспорте узла
    			'p_node_del'=>0,	// удаление в паспорте узла
    			'cable'=>1,			// доступ к списку кабелей в кроссе/муфте
    			'cable_add'=>0,		// добавление кабеля
    			'cable_edit'=>0,	// редактирование кабеля
    			'cable_del'=>0,		// удаление кабеля
    			'cable_geom'=>0,	// доступ на сброс координат кабеля
    			'port_edit'=>0,		// редактирование портов
    			'port_conn'=>0,		// соединение портов
    			'fiber_edit'=>0,	// редактирование волокон
    			'cable_edit'=>0,	// редактирование кабеля
    			'port_desc'=>1,		// редактирование занятости портов
    			'map_type'=>1,		// тип карты 0 - без слоев, 1 - все слои, 2 - слой для абонентского
    			'map_layer'=>1		// слой: 0 - опенстритмап, 1 - локальный
    		),
    		15=>array('name'=>'Эксплуатация',
				'dirs'=>0,			// доступ в справочники
				'plan'=>0,			// доступ в план
				'dirs_users'=>0,	// доступ в справочник пользователей
				'key'=>1,			// показывать кнопку ключей
				'key_edit'=>0,		// добавлять, редактировать, удалять ключи
				'node'=>1,			// доступ в список узлов
				'node_add'=>0,		// добавление узла
				'prompt'=>0,		// подсказка для тупых
				'node_edit'=>0,		// редактирование узла
				'node_del'=>0,		// удаление узла
				'pq'=>1,			// доступ в узел
				'incorrect'=>1,		// доступ к галке проблемма
				'u_const'=>0,		// доступ к галке объекты в стадии строительства
				'o_node'=>1,		// доступ в список оптического оборудования узла
				'o_node_add'=>0,	// добавление кросса/муфты
				'o_node_edit'=>0,	// редактирование кросса/муфты
				'o_node_del'=>0,	// удаление кросса/муфты
				'p_node'=>1,		// доступ в паспорт узла
    			'p_node_add'=>0,	// добавление в паспорт узла
    			'p_node_edit'=>0,	// редактирования в паспорте узла
    			'p_node_del'=>0,	// удаление в паспорте узла
    			'cable'=>1,			// доступ к списку кабелей в кроссе/муфте
    			'cable_add'=>0,		// добавление кабеля
    			'cable_edit'=>0,	// редактирование кабеля
    			'cable_del'=>0,		// удаление кабеля
    			'cable_geom'=>0,	// доступ на сброс координат кабеля
    			'port_edit'=>0,		// редактирование портов
    			'port_conn'=>1,		// соединение портов
    			'fiber_edit'=>0,	// редактирование волокон
    			'cable_edit'=>0,	// редактирование кабеля
    			'port_desc'=>1,		// редактирование занятости портов
    			'map_type'=>1,		// тип карты 0 - без слоев, 1 - все слои, 2 - слой для абонентского
    			'map_layer'=>1		// слой: 0 - опенстритмап, 1 - локальный
    		),
    		20=>array('name'=>'Мониторинг',
				'dirs'=>0,			// доступ в справочники
				'plan'=>0,			// доступ в план
				'dirs_users'=>0,	// доступ в справочник пользователей
				'key'=>0,			// показывать кнопку ключей
				'key_edit'=>0,		// добавлять, редактировать, удалять ключи
				'node'=>1,			// доступ в список узлов
				'node_add'=>0,		// добавление узла
				'prompt'=>0,		// подсказка для тупых
				'node_edit'=>0,		// редактирование узла
				'node_del'=>0,		// удаление узла
				'pq'=>1,			// доступ в узел
				'incorrect'=>1,		// доступ к галке проблемма
				'u_const'=>0,		// доступ к галке объекты в стадии строительства
				'o_node'=>1,		// доступ в список оптического оборудования узла
				'o_node_add'=>0,	// добавление кросса/муфты
				'o_node_edit'=>0,	// редактирование кросса/муфты
				'o_node_del'=>0,	// удаление кросса/муфты
				'p_node'=>1,		// доступ в паспорт узла
    			'p_node_add'=>0,	// добавление в паспорт узла
    			'p_node_edit'=>0,	// редактирования в паспорте узла
    			'p_node_del'=>0,	// удаление в паспорте узла
    			'cable'=>1,			// доступ к списку кабелей в кроссе/муфте
    			'cable_add'=>0,		// добавление кабеля
    			'cable_edit'=>0,	// редактирование кабеля
    			'cable_del'=>0,		// удаление кабеля
    			'cable_geom'=>0,	// доступ на сброс координат кабеля
    			'port_edit'=>0,		// редактирование портов
    			'port_conn'=>1,		// соединение портов
    			'fiber_edit'=>0,	// редактирование волокон
    			'cable_edit'=>0,	// редактирование кабеля
    			'port_desc'=>1,		// редактирование занятости портов
    			'map_type'=>1,		// тип карты 0 - без слоев, 1 - все слои, 2 - слой для абонентского
    			'map_layer'=>1		// слой: 0 - опенстритмап, 1 - локальный
    		),
    		21=>array('name'=>'Саппорт',
    				'dirs'=>0,			// доступ в справочники
    				'plan'=>0,			// доступ в план
    				'dirs_users'=>0,	// доступ в справочник пользователей
    				'key'=>0,			// показывать кнопку ключей
    				'key_edit'=>0,		// добавлять, редактировать, удалять ключи
    				'node'=>1,			// доступ в список узлов
    				'node_add'=>0,		// добавление узла
    				'prompt'=>0,		// подсказка для тупых
    				'node_edit'=>0,		// редактирование узла
    				'node_del'=>0,		// удаление узла
    				'pq'=>1,			// доступ в узел
    				'incorrect'=>1,		// доступ к галке проблемма
    				'u_const'=>0,		// доступ к галке объекты в стадии строительства
    				'o_node'=>1,		// доступ в список оптического оборудования узла
    				'o_node_add'=>0,	// добавление кросса/муфты
    				'o_node_edit'=>0,	// редактирование кросса/муфты
    				'o_node_del'=>0,	// удаление кросса/муфты
    				'p_node'=>1,		// доступ в паспорт узла
    				'p_node_add'=>0,	// добавление в паспорт узла
    				'p_node_edit'=>0,	// редактирования в паспорте узла
    				'p_node_del'=>0,	// удаление в паспорте узла
    				'cable'=>1,			// доступ к списку кабелей в кроссе/муфте
    				'cable_add'=>0,		// добавление кабеля
    				'cable_edit'=>0,	// редактирование кабеля
    				'cable_del'=>0,		// удаление кабеля
    				'cable_geom'=>0,	// доступ на сброс координат кабеля
    				'port_edit'=>0,		// редактирование портов
    				'port_conn'=>0,		// соединение портов
    				'fiber_edit'=>0,	// редактирование волокон
    				'cable_edit'=>0,	// редактирование кабеля
    				'port_desc'=>0,		// редактирование занятости портов
    				'map_type'=>1,		// тип карты 0 - без слоев, 1 - все слои, 2 - слой для абонентского
    				'map_layer'=>1		// слой: 0 - опенстритмап, 1 - локальный
    		),
    		25=>array('name'=>'Абонентский',
				'dirs'=>0,			// доступ в справочники
				'plan'=>0,			// доступ в план
				'dirs_users'=>0,	// доступ в справочник пользователей
				'key'=>0,			// показывать кнопку ключей
				'key_edit'=>0,		// добавлять, редактировать, удалять ключи
				'node'=>1,			// доступ в список узлов
				'node_add'=>0,		// добавление узла
				'prompt'=>0,		// подсказка для тупых
				'node_edit'=>0,		// редактирование узла
				'node_del'=>0,		// удаление узла
				'pq'=>0,			// доступ в узел
				'incorrect'=>0,		// доступ к галке проблемма
				'u_const'=>0,		// доступ к галке объекты в стадии строительства
				'o_node'=>0,		// доступ в список оптического оборудования узла
				'o_node_add'=>0,	// добавление кросса/муфты
				'o_node_edit'=>0,	// редактирование кросса/муфты
				'o_node_del'=>0,	// удаление кросса/муфты
				'p_node'=>0,		// доступ в паспорт узла
    			'p_node_add'=>0,	// добавление в паспорт узла
    			'p_node_edit'=>0,	// редактирования в паспорте узла
    			'p_node_del'=>0,	// удаление в паспорте узла
    			'cable'=>0,			// доступ к списку кабелей в кроссе/муфте
    			'cable_add'=>0,		// добавление кабеля
    			'cable_edit'=>0,	// редактирование кабеля
    			'cable_del'=>0,		// удаление кабеля
    			'cable_geom'=>0,	// доступ на сброс координат кабеля
    			'port_edit'=>0,		// редактирование портов
    			'port_conn'=>0,		// соединение портов
    			'fiber_edit'=>0,	// редактирование волокон
    			'cable_edit'=>0,	// редактирование кабеля
    			'port_desc'=>0,		// редактирование занятости портов
    			'map_type'=>2,		// тип карты 0 - без слоев, 1 - все слои, 2 - слой для абонентского
    			'map_layer'=>1		// слой: 0 - опенстритмап, 1 - локальный
    		),
			30=>array('name'=>'Группа подключений',
    			'dirs'=>0,			// доступ в справочники
    			'plan'=>0,			// доступ в план
    			'dirs_users'=>0,	// доступ в справочник пользователей
    			'key'=>1,			// показывать кнопку ключей
    			'key_edit'=>1,		// добавлять, редактировать, удалять ключи
    			'node'=>1,			// доступ в список узлов
    			'node_add'=>0,		// добавление узла
    			'prompt'=>0,		// подсказка для тупых
    			'node_edit'=>0,		// редактирование узла
    			'node_del'=>0,		// удаление узла
    			'pq'=>1,			// доступ в узел
    			'incorrect'=>0,		// доступ к галке проблемма
    			'u_const'=>0,		// доступ к галке объекты в стадии строительства
    			'o_node'=>0,		// доступ в список оптического оборудования узла
    			'o_node_add'=>0,	// добавление кросса/муфты
    			'o_node_edit'=>0,	// редактирование кросса/муфты
    			'o_node_del'=>0,	// удаление кросса/муфты
    			'p_node'=>1,		// доступ в паспорт узла
    			'p_node_add'=>1,	// добавление в паспорт узла
    			'p_node_edit'=>1,	// редактирования в паспорте узла
    			'p_node_del'=>1,	// удаление в паспорте узла
    			'cable'=>0,			// доступ к списку кабелей в кроссе/муфте
    			'cable_add'=>0,		// добавление кабеля
    			'cable_edit'=>0,	// редактирование кабеля
    			'cable_del'=>0,		// удаление кабеля
    			'cable_geom'=>0,	// доступ на сброс координат кабеля
    			'port_edit'=>0,		// редактирование портов
    			'port_conn'=>0,		// соединение портов
    			'fiber_edit'=>0,	// редактирование волокон
    			'cable_edit'=>0,	// редактирование кабеля
    			'port_desc'=>0,		// редактирование занятости портов
    			'map_type'=>1,		// тип карты 0 - без слоев, 1 - все слои, 2 - слой для абонентского
    			'map_layer'=>1		// слой: 0 - опенстритмап, 1 - локальный
    		)
    );
    
    $group_access=@$group[$_SESSION['group']];

    $per_page=50;

	function clean($value){
		$value=strip_tags($value);
		$value=trim($value);
		//if(!get_magic_quotes_gpc()) $value=mysql_real_escape_string($value);
		return $value;
	}

	function add_log($table,$id,$data_old,$user_id){
		global $table_log;
		pg_query("INSERT INTO ".$table_log." (table_name,table_id,data_old,user_id) VALUES ('".$table."', ".$id.", '".$data_old."', ".$user_id.")");
	}
	
    function addr_id($id)
    {
        global $table_street_name;
        global $table_street_num;
        global $table_node;
        global $table_pq;
        global $table_location;
        global $table_room;
        $sql="SELECT s_name.name AS street_name,
        	s_name.small_name AS street_small_name,
            s_num.num AS street_num
            FROM ".$table_street_name." AS s_name,
                ".$table_street_num." AS s_num,
                ".$table_node." AS n1
            WHERE n1.street_id = s_name.id
            AND n1.street_num_id = s_num.id
            AND n1.id=".$id;
    $result=pg_fetch_assoc(pg_query($sql),0);
    	return addr($result['street_name'],$result['street_num'], NULL, NULL, NULL);
    }
    
    function addr_id_full($id)
    {
    	global $table_street_name;
    	global $table_street_num;
    	global $table_node;
    	global $table_pq;
    	global $table_location;
    	global $table_room;
    	$sql="SELECT s_name.name AS street_name,
	    	s_name.small_name AS street_small_name,
	    	s_num.num AS street_num,
	    	loc.location AS location,
	    	room.room AS room,
	    	n1.*,p1.id AS pq_id
	    	FROM ".$table_street_name." AS s_name,
	    	".$table_street_num." AS s_num,
	    	".$table_node." AS n1
	    	LEFT JOIN ".$table_pq." AS p1 ON n1.id = p1.node
	    	LEFT JOIN ".$table_location." AS loc ON n1.location_id = loc.id
	    	LEFT JOIN ".$table_room." AS room ON n1.room_id = room.id
	    	WHERE n1.street_id = s_name.id
	    	AND n1.street_num_id = s_num.id
	    	AND n1.id=".$id;
    	$result=pg_fetch_assoc(pg_query($sql),0);
    		$name=$result['street_name'];
    	return addr($name,$result['street_num'],$result['num_ent'],$result['location'],$result['room']);
    }

    function addr($street_name,$street_num,$num_ent,$location,$room)
    {
        return $street_name.' '.$street_num.
            ($num_ent||$location||$room?" (".
                ($num_ent?$num_ent."п".($location||$room?"/":""):"").
                ($location?$location.
                    (preg_match("/\d+/", $location)?"э":"").
                ($room?"/":""):"").($room?$room:"")
            .")":"");
    }
    
    function addr_id_loc($id)
    {
    	global $table_node;
    	global $table_pq;
    	global $table_location;
    	global $table_room;
    	$sql="SELECT
	    	loc.location AS location,
	    	room.room AS room,
	    	n1.*,p1.id AS pq_id
	    	FROM
	    	".$table_node." AS n1
	    	LEFT JOIN ".$table_pq." AS p1 ON n1.id = p1.node
	    	LEFT JOIN ".$table_location." AS loc ON n1.location_id = loc.id
	    	LEFT JOIN ".$table_room." AS room ON n1.room_id = room.id
	    	WHERE n1.id=".$id;
    	$result=pg_fetch_assoc(pg_query($sql),0);
    	$text=($result['num_ent']?$result['num_ent']."п".($result['location']||$result['room']?"/":""):"").($result['location']?$result['location'].(preg_match("/\d+/", $result['location'])?"э":"").($result['room']?"/":""):"").($result['room']?$result['room']:"");
    	return $text;
    }

    function button_ok_cancel($button,$ok_id='')
    {
        global $_GET;
        if($button=='div_new')
            return '<div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        if($button=='div_cancel')
        return '<div class="span1 toolbar m0">
                  <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        if($button=='div_del')
            return '<div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="'.$ok_id.'" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
    }
    function toupper($content)
    {
    	$content = strtr($content, 'абвгдеёжзийклмнорпстуфхцчшщъьыэюя',
    			'АБВГДЕЁЖЗИЙКЛМНОРПСТУФХЦЧШЩЪЬЫЭЮЯ');
    	return strtoupper($content);
    }
    
    function tolower($content)
    {
    	$content = strtr($content, 'АБВГДЕЁЖЗИЙКЛМНОРПСТУФХЦЧШЩЪЬЫЭЮЯ',
    			'абвгдеёжзийклмнорпстуфхцчшщъьыэюя');
    	return strtolower($content);
    }

    // вывод инфы о узле
    function get_node($value) {
    	global $group_access;
    	
    	if(count($value)>0) {
    		$id=explode('.', $value['@attributes']['fid']);
    			
    		global $table_box_type;
    		global $table_box;
    		global $table_node;
    		global $table_keys;
    		global $table_lift;
    		global $table_lift_type;
    		global $table_street_name;
    		global $table_street_num;
    		global $table_pq;
    		global $table_pq_type;
    		global $table_pq_schem;
    
    		$sql="SELECT b1.*, bt1.name, bt1.unit
                FROM ".$table_box_type." AS bt1, ".$table_box." AS b1
                WHERE bt1.id = b1.box_type_id
                AND b1.node_id=".$id[1]."
                ORDER BY bt1.name";
    		$result = pg_query($sql);
    		if (pg_num_rows($result)) {
    			while ($row = pg_fetch_assoc($result)) {
    				/*$content2.='
						<tr>
	                    	<td><a href="index.php?act=s_pq&p_node&node_id='.$id[1].'" target="_blank">'.$row['name'].($row['unit']?' '.$row['unit'].'U':'').'</a></td>
	                    </tr>
                    ';*/
    				$content.='
						<div><a href="index.php?act=s_pq&p_node&node_id='.$id[1].'" target="_blank">'.$row['name'].($row['unit']?' '.$row['unit'].'U':'').'</a></div><div class="clear"></div>
                    ';
    			}
    			//$content.='</tr>';
    		}
    		$sql_info="SELECT
    			k1.num AS key_num,
    			k1.descrip AS key_desc,
    			lt1.name AS lift_name,
    			lt1.tel AS lift_tel,
    			lt1.descrip AS lift_desc
    		FROM ".$table_node." AS n1
    			LEFT JOIN ".$table_keys." AS k1 ON k1.node_id = n1.id
    			LEFT JOIN ".$table_lift." AS l1 ON l1.node_id = n1.id
    			LEFT JOIN ".$table_lift_type." AS lt1 ON lt1.id = l1.lift_id
    		WHERE n1.id=".$id[1];
    		
    		$result_info=pg_fetch_assoc(pg_query($sql_info));
    		
    		//вывод списка кроссов в всплывающем окне с переходом на разварку в пдф
    		$sql_pq="SELECT pq.id, pq.num, pq_t.name, pq_t.type, pq_t.ports_num, pqs.id AS pqs_id
			FROM ".$table_pq." AS pq
			LEFT JOIN ".$table_pq_type." AS pq_t ON pq.pq_type_id = pq_t.id
			LEFT JOIN (
				SELECT pqs.id, pqs.pq_id
				FROM ".$table_pq_schem." AS pqs
				JOIN (SELECT MAX(pqs.date) AS date, pqs.pq_id FROM ".$table_pq_schem." AS pqs GROUP BY pqs.pq_id) AS pqs1 ON pqs.date = pqs1.date AND pqs.pq_id = pqs1.pq_id
			) AS pqs ON pqs.pq_id = pq.id
			WHERE pq.node = ".$id[1]."
			ORDER BY pq.num";
    		
    		//echo $sql_pq;
    		$result_pq = pg_query($sql_pq);
    		if (pg_num_rows($result_pq)) {
    			while ($row_pq = pg_fetch_assoc($result_pq)) {
    				if($row_pq['type']!=2 && is_numeric($row_pq['pqs_id'])) {
	    				/*$content2.='
							<tr>
		                    	<td><a href="index.php?act=pq_file&id='.base64_encode($row_pq['pqs_id']).'" target="_blank">'.($row_pq['type']==0?'Кросс':'Муфта').(isset($row_pq['num'])?' №'.$row_pq['num']:'').' '.$row_pq['name'].'</a></td>
		                    </tr>
	                    ';*/
	    				$content.='<div><a href="index.php?act=pq_file&id='.base64_encode($row_pq['pqs_id']).'" target="_blank">'.($row_pq['type']==0?'Кросс':'Муфта').(isset($row_pq['num'])?' №'.$row_pq['num']:'').' '.$row_pq['name'].'</a></div><div class="clear"></div>';
    				}
    			}
    			$content.='</tr>';
    		}
    		
    		//конец вывода списка кроссов

    		$uk_info=pg_fetch_assoc(pg_query("SELECT s1.street_id, sn1.num FROM ".$table_node." AS n1 LEFT JOIN ".$table_street_name." AS s1 ON n1.street_id = s1.id LEFT JOIN ".$table_street_num." AS sn1 ON n1.street_num_id = sn1.id WHERE n1.id=".$id[1].";"));
    		
    		$node_info=($result_info['key_num']?'Ключ: '.$result_info['key_num']:'').($result_info['key_desc']?'<br>Описание:<br>'.$result_info['key_desc']:'');
    		$node_info.=($node_info!='' && $result_info['lift_name']?'<hr>':'').($result_info['lift_name']?'Адрес лифтовой:<br>'.$result_info['lift_name']:'').($result_info['lift_tel']?' Телефоны:<br>'.$result_info['lift_tel']:'').($result_info['lift_desc']?'<br>'.$result_info['lift_desc']:'');

    		$node='<div style="border-bottom: 1px solid silver;"><div style="float: left;"><b>Узел:&nbsp;</b></div><div style="float: left; background: silver;">'.($group_access['map_type']==1?'<a href="index.php?act=s_pq&node_id='.$id[1].'" target="_blank">':'').$value['opengeo:address'].($value['opengeo:loc_text']?' ('.$value['opengeo:loc_text'].')':'').($group_access['map_type']==1?'</a>&nbsp;':'').''.($node_info!=''?'<a href="#" id="info" onClick="javascript: $(\'#info\').attr(\'onclick\',\'return false;\'); alertify.log(\''.$node_info.'\', \'\', 0); return false;">[Инфо]</a>&nbsp;':'').'</div><div style="float: right;">{CLOSE}</div><div class="clear"></div>'.$node.=($value['opengeo:descrip']?'<div style="border-top: 1px solid silver;">'.$value['opengeo:descrip'].'</div>':'').$content.'</div>';

    		return $node;
    	}
    }

    // вывод инфы по кабелю
    function get_cable($value) {
    	global $table_cable;
    	global $table_cable_type;
    	global $table_pq;
    	global $table_node;

    	if(count($value)>0) {
    		$mysql_id=explode('.', $value['@attributes']['fid']);
    
    		$cable_sql="SELECT
					ct1.name AS cable_name,
					ct1.fib AS cable_fib,
					n1.address AS addr_1_name,
					n1.id AS addr_1_id,
					n2.address AS addr_2_name,
					n2.id AS addr_2_id,
					c1.the_geom AS geom,
					ceiling(ST_length(c1.the_geom, true)) AS geom2
				FROM
					".$table_cable." AS c1,
					".$table_pq." AS p1,
					".$table_pq." AS p2,
					".$table_node." AS n1,
					".$table_node." AS n2,
					".$table_cable_type." AS ct1
				WHERE
					c1.id = ".$mysql_id[1]."
				AND
					c1.pq_1 = p1.id
				AND
					c1.pq_2 = p2.id
				AND
					p1.node = n1.id
				AND
					p2.node = n2.id
				AND
					c1.cable_type = ct1.id;";
    		$query=pg_fetch_assoc(pg_query($cable_sql),0);
    		if(count($query)>0) {
    			$cable='<div style="float: left;"><b>Кабель: </b>'.$query['cable_name'].'</div><div style="float: fight;"></div><div style="float: right;">{CLOSE}</div><div class="clear"></div>';
    			$cable.='<div style="float: left;"><a href="index.php?act=s_pq&node_id='.$query['addr_1_id'].'" target="_blank">'.$query['addr_1_name'].'</a> - <a href="index.php?act=s_pq&node_id='.$query['addr_2_id'].'" target="_blank">'.$query['addr_2_name'].'</a></div><div class="clear"></div>';
			$cable.='<div style="float: left;">Расстояние: <b>'.$query['geom2'].'</b> м.</div><div class="clear"></div>';
    		}
    		return $cable;
    	}
    }

    function geocode_local($lat,$lon,$addressdetails) {
    	//$geocode_host='http://pto.rdtc.ru/nominatim/reverse.php?format=jsonv2&lat='.$lat.'&lon='.$lon.'&addressdetails='.$addressdetails;
    	$geocode_host='http://nominatim.openstreetmap.org/reverse.php?format=jsonv2&lat='.$lat.'&lon='.$lon.'&addressdetails='.$addressdetails;
    	
    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$geocode_host);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows; U; Windows NT 5.0; En; rv:1.8.0.2) Gecko/20070306 Firefox/1.0.0.4");
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
	
    	$address=$result->{'address'};
    	/*echo '<pre>';
    	print_r($result);
    	echo '-----------------------';
    	//print_r($address);
    	echo '</pre>';*/
    	//$result = ($address->{'state'}?$address->{'state'}.', ':'');
    	//echo ($address->{'county'} && $address->{'county'}!='Новокузнецк'?$address->{'county'}.'':'');
    	$result = (@$address->{'county'}?$address->{'county'}.', ':'');
    	$result .= (@$address->{'village'}?'п. '.$address->{'village'}.'<br>':'');
    	$result .= (@$address->{'road'}?$address->{'road'}.', ':'');
    	$result .= (@$address->{'house_number'}?$address->{'house_number'}.'':'');
    	
    	return $result;
    }

    function geocode($geocode) {
    	$result = file_get_contents('http://geocode-maps.yandex.ru/1.x/?geocode='.$geocode);

    	$array = XML2Array::createArray($result);
    	//echo '<pre>';
    	//print_r($result);
    	//print_r($array['ymaps']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name']);
    	//return $array['ymaps']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'].'&nbsp;<input id="close_button" type="button" title="Закрыть" value="X" onClick="javascript: popupClear(); $(\'.alertify-logs\').html(\'\');">';
    	return $array['ymaps']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'];
    	die;
    }

    function geocode_addr($addr) {
/*    	if($_SERVER['REMOTE_ADDR']=='192.168.6.12') {
    		echo $addr;
    		die;
    	}*/
    	$result = file_get_contents('http://geocode-maps.yandex.ru/1.x/?geocode='.$addr_obl.','.$addr.'&results=1');
    	//echo 'http://geocode-maps.yandex.ru/1.x/?geocode=Кемеровская область,'.$addr.'&results=1';
    
    	$array = XML2Array::createArray($result);
    	//echo '<pre>';
    	//print_r($result);
    	//print_r($array['ymaps']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name']);
    	//return $array['ymaps']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'];
    	//die;
    	if($array['ymaps']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found']) {
    		$LonLat = explode(" ", $array['ymaps']['GeoObjectCollection']['featureMember']['GeoObject']['Point']['pos']);
    		echo 'var lon='.$LonLat['0'].'; var lat='.$LonLat['1'].';';
    		//echo '</pre>';
    	}
    	die;
    }
    
    function geocode_addr2($addr) {
    	preg_match("/(\W+) \W+ .*$/s", $addr, $city);
    	if(empty($city[1])) {
    		$addr = 'новокузнецк '.$addr;
    	}
    		
    	$url = 'http://catalog.api.2gis.ru/geo/search?version=1.3&key=ruoaxn8012&q='.$addr.'&limit=1';
    	
    	//echo $url;

    	$result = file_get_contents($url);

    	$array = json_decode($result);
    	/*echo '<pre>';
    	print_r($array);
    	echo '</pre>';*/
    	if($array->{'total'} > 0) {
    		$result_lon_lat = $array->{'result'}[0]->{'centroid'};
    		preg_match("/\((\S+) (\S+)\)$/s", $result_lon_lat, $LonLat);
    		echo 'var lon='.$LonLat[1].'; var lat='.$LonLat[2].';';
    	}
    	die;
    }
    
    // геометрию кабеля begin -------------------------------------------------------------------------------------------------------
    //if(isset($_GET['act']) && $_GET['act']=='cable_geom') {
	function cable_geom($cable_id,$ins) {
		global $table_cable;
		global $table_pq;
		global $table_node;
		//$cable_id = '940';
		//ST_AsText(ST_MakeLine(n1.the_geom,n2.the_geom)) AS line_text,
		//c1.the_geom AS geom,
    	$sql = "SELECT c1.id,
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
			".(!$ins?"AND c1.the_geom IS NULL":"")."
			AND
				c1.id = ".$cable_id;
    	$row = pg_fetch_assoc(pg_query($sql));
    	//print_r($row);
    	if(@$row ) {
    		pg_query("UPDATE ".$table_cable." SET the_geom='".$row['line']."' WHERE id=".$cable_id);
    		//echo "UPDATE ".$table_cable." SET the_geom='".$row['line']."' WHERE id=".$cable_id;
    	}
    	return;
    }
    // геометрию кабеля end -------------------------------------------------------------------------------------------------------

    // отслеживание волокна до первого разваренного порта
    function find_end_port($to_node_id,$last_id,$id,$first=false) {
    	global $table_cruz_conn;
    	global $table_fiber_conn;
    	global $table_fiber;
    	global $table_cable;
    	global $table_pq;
    	global $table_pq_type;
    	global $table_node;
    	global $table_color;
    	
    	//$result_array = array();
    	
    	$sql='
			SELECT
				f1.id AS id, f1.num AS num, n1.id AS from_node_id,
				CASE WHEN p1.node = n1.id THEN p1.id ELSE p2.id END AS from_pq_id,
				c1.id AS from_cable_id,
			    	
				f2.id AS to_id, f2.num AS to_num, n2.id AS to_node_id,
				CASE WHEN p3.node = n2.id THEN p3.id ELSE p4.id END AS to_pq_id,
				c2.id AS to_cable_id,
			    	
				c_n.id AS curr_node_id, c_n.address AS curr_node_addr, c_n.descrip AS curr_node_descrip,
				c_n.incorrect,
				CASE WHEN p1.node = c_n.id THEN p1.id ELSE CASE WHEN p2.node = c_n.id THEN p2.id ELSE NULL END END AS curr_pq_id,
			    	
				f1.mod_color AS mod_color_1, f1.fib_color AS fib_color_1,
				f2.mod_color AS mod_color_2, f2.fib_color AS fib_color_2,
		    	cc1.port AS to_port
			FROM
    			'.$table_fiber_conn.' AS fc1,  '.$table_cable.' AS c1, '.$table_pq.' AS p1, '.$table_pq.' AS p2, '.$table_fiber.' AS f2, '.$table_cable.' AS c2, '.$table_pq.' AS p3, '.$table_pq.' AS p4,
    			'.$table_node.' AS n1, '.$table_node.' AS c_n, '.$table_node.' AS n2, '.$table_fiber.' AS f1
			LEFT JOIN '.$table_cruz_conn.' AS cc1
				JOIN '.$table_pq.' AS pp1 ON pp1.node !='.$to_node_id.'
    		ON cc1.fiber_id = f1.id AND cc1.pq_id = pp1.id
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
			AND
				c_n.id '.($first?'!= ':'= ').$to_node_id.' LIMIT 1';
    	//$text.=$sql;
    	//echo '<br>';
    	$result=@pg_fetch_assoc(pg_query($sql));
    	//print_r($result);
    	/*echo '<pre>';
    	print_r($sql);
    	echo '</pre>';*/
    	if($result && empty($result['to_port'])) {
    		//$text.=' to_fiber: '.$result['to_port'];
    		//echo 'node: '.$to_node_id.' fiber_id: '.$last_id.' to_fiber_id: '.$id;
    		/*echo '<pre>';
    		print_r($sql);
    		echo '</pre>';*/
    		return find_end_port($result['to_node_id'],$result['id'],$result['to_id'],false);
    		$first=true;
    	} else {
    		//$text.='<hr>';
    		//$text.=$result['to_node'].' '.$result['fiber_id'].' '.$result['to_fiber_id'];
    		//echo '<br>node: '.$to_node_id.' fiber: '.$last_id.' to_fiber: '.$id.'<hr>';
    		//echo '<hr>';
    		//return $text;
    		$sql2='
		        SELECT
		        	f1.id AS id,
    				f1.num AS num,
    				/*f1.descrip AS port_desc,*/
    				n1.id AS curr_node_id,
    				n1.address AS curr_node_addr,
    				n1.incorrect,
    				n1.descrip AS curr_node_descrip,
    				p1.id AS curr_pq_id,
    				pt1.name AS pq_name,
    				pt1.type AS pq_type,
    				p1.num AS pq_num,
		        	f1.mod_color AS mod_color,
    				f1.fib_color AS fib_color,
    				cc1.port AS to_port,
    				cc1.used AS port_used,
    				cc1.descrip AS port_desc
		        FROM
    				'.$table_cable.' AS c1, '.$table_pq.' AS p1, '.$table_pq_type.' AS pt1, '.$table_node.' AS n1, '.$table_fiber.' AS f1
    			LEFT JOIN '.$table_cruz_conn.' AS cc1
					JOIN '.$table_pq.' AS pp1 ON pp1.node '.($first?'!':'').'='.$to_node_id.'
	    		ON cc1.fiber_id = f1.id AND cc1.pq_id = pp1.id 
		        WHERE
		        	f1.id = '.$id.'
		        AND
		        	c1.id = f1.cable_id
		        AND
		        	( c1.pq_1 = p1.id OR c1.pq_2 = p1.id)
		        AND
		        	p1.node '.($first?'!= ':'= ').$to_node_id.'
		        AND
		        	p1.pq_type_id = pt1.id
		        AND
		        	n1.id = p1.node';
    		/*echo '<pre>';
    		print_r($sql2);
    		echo '</pre>';*/
    		return @pg_fetch_assoc(pg_query($sql2));
    		//$result2=@pg_fetch_assoc(pg_query($sql2));
    		//return $result2;
    	}
    	//echo '<pre>'.$text.'</pre>';
    }
///////////////////////////////////////////////////////////////
// вывод номера порта в кроссе присоедененного к волокну
function get_port_select($fiber_id,$pq_id,$enable=false) {
	global $table_cruz_conn;

	$result=pg_query("SELECT id,fiber_id,port FROM ".$table_cruz_conn." WHERE pq_id = ".$pq_id." AND ( fiber_id IS NULL OR fiber_id = ".$fiber_id." ) ORDER BY port;");
	$text='<option value="0">---</option>';
	if(pg_num_rows($result)){
		$curr_port_id=0;
		while($row=pg_fetch_assoc($result)){
			$text.='<option value="'.$row['id'].'"';
			if($fiber_id==$row['fiber_id']) {
				$text.=" SELECTED";
				$curr_port_id=$row['id'];
			}
			$text.='>'.$row['port'].'</option>';
		}
	} else $enable=true;
	$text='<select class="ports" id="ports_'.$fiber_id.'"'.($enable?' disabled':'').'>'.$text.'</select><input type="hidden" id="curr_port_id_'.$fiber_id.'" value="'.@$curr_port_id.'">';
	return $text;
}

// функция вывода свободных волокон после выбора кабеля
function get_pq_select($fiber_id,$node_id,$pq_id,$pq_type,$pq_num,$enable=false) {
	global $table_pq;
	global $table_pq_type;

	$text="";
	$num="";
	
    if($pq_type == 1) $num = ' AND p1.num = '.$pq_num;
    $result=pg_query("SELECT p1.*, pt.type FROM ".$table_pq." AS p1, ".$table_pq_type." AS pt WHERE  p1.node=".$node_id." AND p1.pq_type_id = pt.id AND pt.type = ".$pq_type.$num.";");
    if(pg_num_rows($result)){
        while($row=pg_fetch_assoc($result)){
            if($row['type']==0) {
                if(isset($row['num'])) $num='Кросс №'.$row['num']; else $num='Кросс';
            } else {
                if(isset($row['num'])) $num='Муфта №'.$row['num']; else $num='Муфта';
            }
            $text.='<option value="'.$row['id'].'"';
            if($row['id']==$pq_id || pg_num_rows($result)==1)
            	$text.=' SELECTED';
            $text.='>'.$num.'</option>';
        }
    }
	$text='<select id="pq_id_' . $fiber_id . '"'.($enable || pg_num_rows($result)<2?' disabled':'').'>'.$text.'</select>';
	return $text;
}
//функция вывода кабелей в кроссе/муфте
//function get_cable_select($fiber_id,$pq_id,$cable_id,$enable=false) {
function get_cable_select($fiber_id,$pq_id,$cable_id) {
	global $table_cable;
	global $table_pq;
	global $table_node;
	global $table_pq_type;

    $result=pg_query("SELECT a.*, c1.address_full as addr_1, pt1.type as type_1, b1.num as num_1, c2.address_full as addr_2, pt2.type as type_2, b2.num as num_2,
			LEFT(b1.descrip, 15) AS descrip_1, LEFT(b2.descrip, 15) AS descrip_2
			FROM ".$table_cable." AS a, ".$table_pq." AS b1, ".$table_pq." AS b2, ".$table_node." AS c1, ".$table_node." AS c2, ".$table_pq_type." AS pt1, ".$table_pq_type." AS pt2
			WHERE (
			a.pq_1 = b1.id
			AND b1.node = c1.id
	)
			AND (
			a.pq_2 = b2.id
			AND b2.node = c2.id
	) AND (a.pq_1=".$pq_id." OR a.pq_2=".$pq_id.")
	AND b1.pq_type_id = pt1.id AND b2.pq_type_id = pt2.id");

	if(pg_num_rows($result)){
		$text='<option value="0">---</option>';
		while($row=pg_fetch_assoc($result)){
			// кросс или муфта для первого pq
			if($row['type_1']==0) $type_1='Кросс'; else $type_1='Муфта';
			// номер кросса/муфты для первого pq
			if(isset($row['num_1'])) $num_1=' №'.$row['num_1']; else $num_1='';
			// кросс или муфта для второго pq
			if($row['type_2']==0) $type_2='Кросс'; else $type_2='Муфта';
			// номер кросса/муфты для второго pq
			if(isset($row['num_2'])) $num_2=' №'.$row['num_2']; else $num_2='';

			// меняем местами адреса узлов для удобства, вначале всегда выбранного узла
			if(isset($pq_id) && $pq_id==$row['pq_2']) {
				$a=2; $b=1;
				$descrip=(!empty($row['descrip_1'])?' "'.$row['descrip_1'].'"':'');
			} else {
				$a=1; $b=2;
				$descrip=(!empty($row['descrip_2'])?' "'.$row['descrip_2'].'"':'');
			}
			@eval("\$pq_addr_1 = \$row[addr_$a].' (' .\$type_$a.\$num_$a. ')';");
			@eval("\$pq_addr_2 = \$row[addr_$b].' (' .\$type_$b.\$num_$b. ')';");

			$text.='<option value="'.$row['id'].'"';
			if($cable_id==$row['id'])
				$text.=' SELECTED';
			$text.='>'.$pq_addr_2.$descrip.'</option>';
		}
	}
	//$text='<select class="cable" id="cable_id_' . $fiber_id . '"'.($enable?' disabled':'').'>'.$text.'</select>';
	return $text;
}
//функция вывода волокон в кабеле
function get_fiber_select($fiber_id,$node_id,$pq_id,$cable_id,$to_fiber_id,$port_id,$enable=false) {
	global $table_fiber;
	global $table_fiber_conn;
	global $table_cable;
	global $table_pq;
	global $table_cruz_conn;

	$not_enable='';
	$text='';
	
    $sql="SELECT a.*, c.id AS to_id, c.cable_id AS to_cable_id, e.id AS to_pq_id, cc.port
	    	FROM ".$table_fiber." AS a
	    	LEFT JOIN ".$table_fiber_conn." AS b ON ( a.id = b.fiber_id_1 OR a.id = b.fiber_id_2 ) AND b.node_id= ".$node_id."
	    	LEFT JOIN ".$table_fiber." AS c ON c.id = CASE WHEN a.id = b.fiber_id_1 THEN b.fiber_id_2 ELSE CASE WHEN a.id = b.fiber_id_2 THEN b.fiber_id_1 ELSE NULL END END AND c.id != ".$fiber_id."
	    	LEFT JOIN ".$table_cable." AS d ON c.cable_id = d.id
	    	LEFT JOIN ".$table_pq." AS e ON ( e.id = d.pq_1 OR e.id = d.pq_2 ) AND e.node = ".$node_id."
	    	 
	    	LEFT JOIN ".$table_cruz_conn." AS cc ON cc.pq_id = ".$pq_id." AND cc.fiber_id = a.id
	    	 
	    	WHERE a.cable_id = ".$cable_id." AND c.id IS NULL AND a.id != ".$fiber_id."
	    	".($port_id!=-1?'AND cc.port IS '.($port_id!=0?'NOT':'').' NULL':'')."
	    	ORDER BY a.num";
    $res_bool=false;
    //$aa='';
    if(is_numeric($to_fiber_id)) {
    	//return ('<pre>'.$to_fiber_id.'</pre>');
    	//$aa=$to_fiber_id;
		$result=pg_query($sql);
		if(pg_num_rows($result)){
			while($row=pg_fetch_assoc($result)){
				$text.='<option value="'.$row['id'].'"';
				if(is_numeric($to_fiber_id))
					if($to_fiber_id==$row['id'])
						$text.=' SELECTED';
				$text.='>'.$row['num'].($row['port']?' ['.$row['port'].']':'').'</option>';
			}
		} else $not_enable='<script>$("select#fiber_id_"+'.$fiber_id.'").attr("disabled",true);</script>';
		$res_bool=(pg_num_rows($result)<2);
    //}
    }
    $text='<select id="fiber_id_' . $fiber_id . '"'.($enable || $res_bool?' disabled':'').'>'.$text.'</select>'.$not_enable;
	return $text;
}

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
function password_quality_check($p, $is_check_digits = true, $is_check_letters = true)
{
#проверка минимальной длины и допустимых символов
	if (! preg_match('/^[\x20-\x7e]{6,20}$/s', $p)) return false;

	#проверка на цифры
	if ($is_check_digits and ! preg_match('/\d/s', $p)) return false;

	#проверка на латинские буквы
	if ($is_check_letters and ! preg_match('/[a-zA-Z]/s', $p)) return false;

	#последовательность символов как на клавиатуре (123456, qwerty, qazwsx, abcdef)
	$chars = '`1234567890-=\\'.  #второй ряд клавиш, [Shift] off
		'~!@#$%^&*()_ |'.   #второй ряд клавиш, [Shift] on
		'qwertyuiop[]asdfghjkl;\'zxcvbnm,./'.  #по горизонтали (расшир. диапазон)
		'QWERTYUIOP{}ASDFGHJKL:"ZXCVBNM<>?'.   #по горизонтали (расшир. диапазон)
		'qwertyuiopasdfghjklzxcvbnm'.  #по горизонтали
		'QWERTYUIOPASDFGHJKLZXCVBNM'.  #по горизонтали
		'qazwsxedcrfvtgbyhnujmikolp'.  #по диагонали
		'QAZWSXEDCRFVTGBYHNUJMIKOLP'.  #по диагонали
		'abcdefghijklmnopqrstuvwxyz'.  #по алфавиту
		'ABCDEFGHIJKLMNOPQRSTUVWXYZ';  #по алфавиту

	if (strpos($chars, $p)         !== false) return false;
    if (strpos($chars, strrev($p)) !== false) return false;

	$length = strlen($p);

	#половинки, как на клавиатуре (повторные и "отражённые" последовательности сюда включаются)
	if ($length > 5 and $length % 2 == 0) {
        $c = $length / 2;
        $left  = substr($p, 0, $c);  #первая половина пароля
        $right = substr($p, $c);     #вторая половина пароля

        $is_left  = (strpos($chars, $left)  !== false or strpos($chars, strrev($left))  !== false);
        $is_right = (strpos($chars, $right) !== false or strpos($chars, strrev($right)) !== false);

        if ($is_left and $is_right) return false;
	}

	#процент уникальности символов
	$k = strlen(count_chars($p, 3)) / $length;
	if ($k < 0.46) return false;

	return true;
}
?>