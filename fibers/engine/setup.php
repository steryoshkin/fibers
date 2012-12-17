<?

	@header('Content-Type: text/html; charset=utf-8');

	session_start();
	
	if (isset($_GET['logout'])){
		session_destroy();
		header("Location: index.php");
		die;
	}

	$login_page="/fibers/index.php?act=login";
	
	$table_user='users';
	$table_node='node';
	$table_pq='pq';
	$table_cable='cable';
	$table_fiber='fiber';
	$table_fiber_conn='fiber_conn';
	$table_cruz_conn='cruz_conn';
    $table_pq_type='pq_type';
    $table_cable_type='cable_type';
    
    $table_area='area';
    $table_street_name='street_name';
    $table_street_num='street_num';
    $table_location='location';
    $table_room='room';
    $table_keys='keys';
    $table_lift='lift';
    $table_lift_type='lift_type';
    $table_desc='desc';
    $table_switches='switches';
    $table_switch_type='switch_type';
    $table_mc='mc';
    $table_mc_type='mc_type';
    $table_box='box';
    $table_box_type='box_type';
    $table_ups='ups';
    $table_ups_type='ups_type';
    $table_other='other';
    $table_other_type='other_type';
    $table_sn='sn';
    
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

    $per_page=40;
/*    $access['node_add']=0;
    $access['node_edit']=0;
    $access['node_del']=0;
    
    $access['pq_add']=0;
    $access['pq_edit']=0;
    $access['pq_del']=0;
    
    $access['cable_add']=0;
    $access['cable_edit']=0;
    $access['cable_del']=0;
    
    $access['fiber_add']=0;
    $access['fiber_del']=0;
    $access['fiber_find']=1;
    
    $access['port_add']=0;
    $access['port_edit']=0;
    $access['port_del']=0;
    $access['port_edit_desc']=0;
*/
	function clean($value){
		$value=strip_tags($value);
		$value=trim($value);
		if(!get_magic_quotes_gpc()) $value=mysql_real_escape_string($value);
		return $value;
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
            s_num.num AS street_num,
            loc.location AS location,
            room.room AS room,
            n1.*,p1.id AS pq_id
            FROM `".$table_street_name."` AS s_name,
                `".$table_street_num."` AS s_num,
                `".$table_node."` AS n1
            LEFT JOIN `".$table_pq."` AS p1 ON n1.id = p1.node
            LEFT JOIN `".$table_location."` AS loc ON n1.location_id = loc.id
            LEFT JOIN `".$table_room."` AS room ON n1.room_id = room.id
            WHERE n1.street_id = s_name.id
            AND n1.street_num_id = s_num.id
            AND n1.id=".$id;
    $result=mysql_fetch_assoc(mysql_query($sql),0);
    	$name=($result['street_small_name']?$result['street_small_name']:$result['street_name']);
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
?>