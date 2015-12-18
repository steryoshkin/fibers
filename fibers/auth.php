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

			//$sql = "INSERT INTO $table_user (login, password, name, status) VALUES ('ttest', '".md5('ttest')."', 'ttest name', true)";
			//$result = pg_query($con_id, $sql);
			
			//id 	group_id 	login 	password 	name
			//$sql = "SELECT * FROM $table_user WHERE login='".$user."' AND password='".$password."' AND status='1'";
			$sql = "SELECT * FROM $table_user WHERE login='".$user."' AND password='".$password."'";
			$result = pg_query($con_id, "$sql  limit 1");
			if ($result and ($n=pg_num_rows($result))>0) {
				$access='';
				while ($row2=pg_fetch_array($result)) {
					if(!$row2['status']) {
						header("Location: /index.php?noactive");
						die;
					}
					$user_id=$row2['id'];
					$user_login=$row2['login'];
					$user_name=$row2['name'];
					$group=$row2['group'];
					
					$access['user_type']=$row2['user_type'];
					
					$access['node_add']=$row2['node_add'];
					$access['node_edit']=$row2['node_edit'];
					$access['node_del']=$row2['node_del'];
					
					$access['pq_add']=$row2['pq_add'];
					$access['pq_edit']=$row2['pq_edit'];
					$access['pq_del']=$row2['pq_del'];

					$access['cable_add']=$row2['cable_add'];
					$access['cable_edit']=$row2['cable_edit'];
					$access['cable_move']=$row2['cable_move'];
					$access['cable_del']=$row2['cable_del'];
					$access['cable_del_all']=$row2['cable_del_all'];
					
					$access['fiber_add']=$row2['fiber_add'];
					$access['fiber_del']=$row2['fiber_del'];
					$access['fiber_find']=$row2['fiber_find'];
					
					$access['port_add']=$row2['port_add'];
					$access['port_edit']=$row2['port_edit'];
					$access['port_del']=$row2['port_del'];
					$access['port_edit_desc']=$row2['port_edit_desc'];

					$access['p_node_edit']=$row2['p_node_edit'];

					echo '<pre>';
					print_r($row2);
					echo '</pre>';
					//die;
					
					if(is_numeric($user_id)) $doc_user_pass=pg_fetch_assoc(pg_query("SELECT doc_user, doc_pass, agents_user, agents_pass FROM " . $table_user . " WHERE id=" . $user_id . ";"));
				}

				$_SESSION['where']="fibers";
				$_SESSION['user']=$user_name;
				$_SESSION['group']=$group;
				$_SESSION['logged_user_fibers']=$user_login;
				$_SESSION['logged_user_fibers_id']=$user_id;

				$_SESSION['user_type']=$access['user_type'];
				
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

				$_SESSION['p_node_edit']=$access['p_node_edit'];
				
				$_SESSION['doc_user_pass']=$doc_user_pass;

				define("LOGGED_USER_FIBERS_ID",$_SESSION['logged_user_fibers_id']);
				define("LOGGED_USER_FIBERS",$_SESSION['logged_user_fibers']);				
			}
			if(empty($_POST['ref']))
				header("Location: /index.php");
			else
				header("Location: ".base64_decode($_POST['ref']));
		}
}
?>
