<?php
if(eregi("^$SERVER_ROOT",$HTTP_REFERER)){

	//require_once($_SERVER['DOCUMENT_ROOT']."/agents/engine/setup.php");
	include_once ('./engine/setup.php');
	include_once ('./engine/db.php');

		if ($_POST['act']) {
			if(!empty($_POST["user"])) $user=clean($_POST["user"]);
			if(!empty($_POST["password"])) $password=clean($_POST["password"]);
			
			//из формы
			$password=md5($password);

			//id 	group_id 	login 	password 	name
			$sql = "SELECT * FROM $table_user WHERE login='".$user."' AND password='".$password."' AND `status`='1'";

			$result=mysql_query("$sql  limit 0, 1");
			if ($result and ($n=mysql_num_rows($result))>0) {
				$access='';
				while ($row=mysql_fetch_array($result)) {
					$user_id=$row['id'];
					$user_login=$row['login'];
					$user_name=$row['name'];
					
					$access['node_add']=$row['node_add'];
					$access['node_edit']=$row['node_edit'];
					$access['node_del']=$row['node_del'];
					
					$access['pq_add']=$row['pq_add'];
					$access['pq_edit']=$row['pq_edit'];
					$access['pq_del']=$row['pq_del'];

					$access['cable_add']=$row['cable_add'];
					$access['cable_edit']=$row['cable_edit'];
					$access['cable_move']=$row['cable_move'];
					$access['cable_del']=$row['cable_del'];
					$access['cable_del_all']=$row['cable_del_all'];
					
					$access['fiber_add']=$row['fiber_add'];
					$access['fiber_del']=$row['fiber_del'];
					$access['fiber_find']=$row['fiber_find'];
					
					$access['port_add']=$row['port_add'];
					$access['port_edit']=$row['port_edit'];
					$access['port_del']=$row['port_del'];
					$access['port_edit_desc']=$row['port_edit_desc'];
				}

				$_SESSION['where']="fibers";
				$_SESSION['user']=$user_name;
				$_SESSION['logged_user_fibers']=$user_login;
				$_SESSION['logged_user_fibers_id']=$user_id;

				$_SESSION['node_add']=$access['node_add'];
				$_SESSION['node_edit']=$access['node_edit'];
				$_SESSION['node_del']=$access['node_del'];
					
				$_SESSION['pq_add']=$access['pq_add'];
				$_SESSION['pq_edit']=$access['pq_edit'];
				$_SESSION['pq_del']=$access['pq_del'];
				
				$_SESSION['cable_add']=$access['cable_add'];
				$_SESSION['cable_edit']=$access['cable_edit'];
				$_SESSION['cable_move']=$access['cable_move'];
				$_SESSION['cable_del']=$access['cable_del'];
				$_SESSION['cable_del_all']=$access['cable_del_all'];
					
				$_SESSION['fiber_add']=$access['fiber_add'];
				$_SESSION['fiber_del']=$access['fiber_del'];
				$_SESSION['fiber_find']=$access['fiber_find'];
					
				$_SESSION['port_add']=$access['port_add'];
				$_SESSION['port_edit']=$access['port_edit'];
				$_SESSION['port_del']=$access['port_del'];
				$_SESSION['port_edit_desc']=$access['port_edit_desc'];

				define("LOGGED_USER_FIBERS_ID",$_SESSION['logged_user_fibers_id']);
				define("LOGGED_USER_FIBERS",$_SESSION['logged_user_fibers']);
				
				print_r($_SESSION);
//				die;
				
			}
			header("Location: index.php");
		}
}
?>
