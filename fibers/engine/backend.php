<?
	
	include_once ('./setup.php');
	include_once ('./db.php');
    $user_id=$_SESSION['logged_user_fibers_id'];

// район begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование района в div
    if(isset($_GET['act']) && ($_GET['act']=='n_area' || $_GET['act']=='e_area') ) {
    	if($_GET['act']=='e_area')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM `" . $table_area . "` AS ar1 WHERE ar1.id=".$id;
    		$result=mysql_fetch_assoc(mysql_query($sql));
    		$name=$result['name'];
    		$desc=$result['desc'];
    	}
    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span5 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Район" /></div>';
    	$text.='<div class="span5 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
    	$text.=button_ok_cancel('div_new');
    	echo $text;
    	die;
    }

// ввод нового/редактирование района в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_area' || $_POST['act']=='e_area') ) {
    	$sql='SELECT * FROM `'.$table_area.'` WHERE name="'.clean($_POST['name']).'"';
    	if($_POST['act']=='n_area') {
    		if(@mysql_result(mysql_query($sql),0)) {
    			$text="Создать невозможно, такой район существует!!!";
    		} else {
    			mysql_query("INSERT INTO `".$table_area."` (`name`,`desc`,`user_id`) VALUES ('".clean($_POST['name'])."', ".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", ".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_area') {
		    if(@mysql_result(mysql_query($sql." AND `desc` ".($_POST['desc']?"='".$_POST['desc']."'":"IS NULL")),0)) {
    			$text="Изменить невозможно, такой район существует!!!";
    		} else {
    			mysql_query("UPDATE `".$table_area."` SET `name`='".clean($_POST['name'])."', `desc`=".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такой район существует!!!");
    		}
    	}
    	echo $text;
    	die;
    }

// удаление района div
    if(isset($_GET['act']) && $_GET['act']=='d_area' && is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM `".$table_street_name."` AS sn1 WHERE sn1.area_id =".clean($_GET['id']);
    	$area_name=mysql_result(mysql_query("SELECT name FROM `".$table_area."` AS a1 WHERE a1.id =".clean($_GET['id'])),0);
    	if(mysql_result(mysql_query($sql),0)) {
    		$text='<div class="span11 m5">&nbsp;Район "'.$area_name.'" используется. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
    	} else {
    		$text='<div class="span10 m5">&nbsp;Удалить район "'.$area_name.'"?</div>'.button_ok_cancel('div_del','d_area');
    	}
    	echo $text;
    	die;
    }

    // удаление района sql
    if(isset($_POST['act']) && $_POST['act']=='d_area' && is_numeric($_POST['id']) ) {
    	if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_street_name."` AS sn1 WHERE sn1.area_id = ".clean($_POST['id']).""),0)) {
    		mysql_query("DELETE FROM `".$table_area."` WHERE `id` = ".clean($_POST['id']));
    	} else echo "not exist";
    	die;
    }
// район end -------------------------------------------------------------------------------------------------------

// улица begin -------------------------------------------------------------------------------------------------------
// ввод новой/редактирование улицы в div
    if(isset($_GET['act']) && ($_GET['act']=='n_street_name' || $_GET['act']=='e_street_name') ) {
    	if($_GET['act']=='e_street_name')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM `" . $table_street_name . "` WHERE id = ".$id;
    		$result=mysql_fetch_assoc(mysql_query($sql));
    		$name=$result['name'];
    		$small_name=$result['small_name'];
    		$area_id=$result['area_id'];
    		$desc=$result['desc'];
    	}
    	$sql="SELECT * FROM `" . $table_area . "` ORDER BY name";
    	$result = mysql_query($sql);
    	if(mysql_num_rows($result)){
    		$select_area='<select id="area">';
    		$select_area.='<option value="0">---</option>';
    		while($row=mysql_fetch_assoc($result)){
    			$select_area.='<option value="'.$row['id'].'"';
    			if($area_id==$row['id']) {
    				$select_area.=" SELECTED";
    			}
    			$select_area.='>'.$row['name'].'</option>';
    		}
    		$select_area.='</select>';
    	}
    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Улица" /></div>';
    	$text.='<div class="span2 m0 input-control text"><input type="text" id="small_name" value="'.$small_name.'" placeholder="Улица (кр. название)" /></div>';
    	$text.='<div class="span3 m0 input-control text">'.$select_area.'</div>';
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.=button_ok_cancel('div_new');
    	echo $text;
    	die;
    }

// ввод новой/редактирование улицы в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_street_name' || $_POST['act']=='e_street_name') && is_numeric($_POST['area_id']) ) {
        $sql='SELECT COUNT(*) FROM `'.$table_street_name.'` WHERE name="'.clean($_POST['name']).'" AND area_id='.clean($_POST['area_id']);
    	if($_POST['act']=='n_street_name') {
    		if(@mysql_result(mysql_query($sql),0)) {
    			$text="Создать невозможно, такая улица существует!!!";
    		} else {
    			mysql_query("INSERT INTO `".$table_street_name."` (`name`,`small_name`,`area_id`,`desc`,`user_id`) VALUES ('".clean($_POST['name'])."', ".($_POST['small_name']?"'".clean($_POST['small_name'])."'":"NULL").",".clean($_POST['area_id']).", ".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", ".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_street_name') {
		    if(@mysql_result(mysql_query($sql." AND `desc` ".($_POST['desc']?"='".$_POST['desc']."'":"IS NULL")),0)) {
    			$text="Изменить невозможно, такая улица существует!!!";
    		} else {
    			mysql_query("UPDATE `".$table_street_name."` SET `name`='".clean($_POST['name'])."', `small_name`=".($_POST['small_name']?"'".clean($_POST['small_name'])."'":"NULL").", `area_id`=".clean($_POST['area_id']).", `desc`=".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такая улица существует!!!");
    		}
    	}
    	echo $text;
    	die;
    }

// удаление улицы div
    if(isset($_GET['act']) && $_GET['act']=='d_street_name' && is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM `".$table_node."` AS n1 WHERE n1.street_id=".clean($_GET['id']);
    	$street_name=mysql_result(mysql_query("SELECT name FROM `".$table_street_name."` AS sn1 WHERE sn1.id =".clean($_GET['id'])),0);
    	if(mysql_result(mysql_query($sql),0)) {
    		$text='<div class="span11 m5">&nbsp;Улица "'.$street_name.'" используется. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
    	} else {
    		$text='<div class="span10 m5">&nbsp;Удалить улицу "'.$street_name.'"?</div>'.button_ok_cancel('div_del','d_street_name');
    	}
    	echo $text;
    	die;
    }

// удаление улицы sql
    if(isset($_POST['act']) && $_POST['act']=='d_street_name' && is_numeric($_POST['id']) ) {
    	if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_node."` AS n1 WHERE n1.street_id = ".clean($_POST['id']).""),0)) {
    		// удаляем улицу
    		mysql_query("DELETE FROM `".$table_street_name."` WHERE `id` = ".clean($_POST['id']));
    		// удаляем все дома этой улицы
    		mysql_query("DELETE FROM `".$table_street_num."` WHERE `street_name_id` = ".clean($_POST['id']));
    	} else echo "not exist";
    	die;
    }
// улица end -------------------------------------------------------------------------------------------------------

// номер дома begin -------------------------------------------------------------------------------------------------------
// номер дома sql
    if(isset($_POST['act']) && $_POST['act']=='check_street_num' && is_numeric($_POST['street_name_id']) && isset($_POST['street_num']) ) {
        $street_num_id=@mysql_result(mysql_query("SELECT id FROM `".$table_street_num."` WHERE street_name_id = ".clean($_POST['street_name_id'])." AND num='".clean($_POST['street_num'])."'"),0);
        echo $street_num_id;
        die;
    }
// номер дома end -------------------------------------------------------------------------------------------------------

// размещение begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование размещения в div
    if(isset($_GET['act']) && ($_GET['act']=='n_location' || $_GET['act']=='e_location') ) {
    	if($_GET['act']=='e_location')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM `" . $table_location . "` WHERE id = ".$id;
    		$result=mysql_fetch_assoc(mysql_query($sql));
    		$location=$result['location'];
    		$desc=$result['desc'];
    	}
    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="location" value="'.$location.'" placeholder="Размещение" /></div>';
    	$text.='<div class="span6 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
    	$text.=button_ok_cancel('div_new');
    	echo $text;
    	die;
    }

// ввод нового/редактирование размещения в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['location']) && ($_POST['act']=='n_location' || $_POST['act']=='e_location') ) {
    	$sql='SELECT * FROM `'.$table_location.'` WHERE location="'.clean($_POST['location']).'"';
    	if($_POST['act']=='n_location') {
    		if(@mysql_result(mysql_query($sql),0)) {
    			$text="Создать невозможно, такое размещение существует!!!";
    		} else {
    			mysql_query("INSERT INTO `".$table_location."` (`location`,`desc`,`user_id`) VALUES ('".clean($_POST['location'])."', ".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", ".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_location') {
    		if(@mysql_result(mysql_query($sql." AND `desc` ".($_POST['desc']?"='".$_POST['desc']."'":"IS NULL")),0)) {
    			$text="Изменить невозможно, такое размещение существует!!!";
    		} else {
    			mysql_query("UPDATE `".$table_location."` SET `location`='".clean($_POST['location'])."', `desc`=".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такое размещение существует!!!");
    		}
    	}
    	echo $text;
    	die;
    }

// удаление размещения div
    if(isset($_GET['act']) && $_GET['act']=='d_location' && is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM `".$table_node."` AS n1 WHERE n1.location_id=".clean($_GET['id']);
    	$location=mysql_result(mysql_query("SELECT location FROM `".$table_location."` AS l1 WHERE l1.id =".clean($_GET['id'])),0);
    	if(mysql_result(mysql_query($sql),0)) {
    		$text='<div class="span11 m5">&nbsp;Размещение "'.$location.'" используется. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
    	} else {
    		$text='<div class="span10 m5">&nbsp;Удалить размещение "'.$location.'"?</div>'.button_ok_cancel('div_del','d_location');
    	}
    	echo $text;
    	die;
    }

// удаление размещения sql
    if(isset($_POST['act']) && $_POST['act']=='d_location' && is_numeric($_POST['id']) ) {
    	if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_node."` AS n1 WHERE n1.location_id = ".clean($_POST['id']).""),0)) {
    		// удаляем размещение
    		mysql_query("DELETE FROM `".$table_location."` WHERE `id` = ".clean($_POST['id']));
    	} else echo "not exist";
    	die;
    }
// размещение end -------------------------------------------------------------------------------------------------------

// помещение begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование помещения в div
    if(isset($_GET['act']) && ($_GET['act']=='n_room' || $_GET['act']=='e_room') ) {
    	if($_GET['act']=='e_room')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM `" . $table_room . "` WHERE id = ".$id;
    		$result=mysql_fetch_assoc(mysql_query($sql));
    		$room=$result['room'];
    		$desc=$result['desc'];
    	}
    
    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span3 m0 input-control text"><input type="text" id="room" value="'.$room.'" placeholder="Помещение" /></div>';
    	$text.='<div class="span6 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
    	$text.=button_ok_cancel('div_new');
    	echo $text;
    	die;
    }

// ввод нового/редактирование помещения в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['room']) && ($_POST['act']=='n_room' || $_POST['act']=='e_room') ) {
    	$sql='SELECT * FROM `'.$table_room.'` WHERE room="'.clean($_POST['room']).'"';
    	if($_POST['act']=='n_room') {
    		if(@mysql_result(mysql_query($sql),0)) {
    			$text="Создать невозможно, такое помещение существует!!!";
    		} else {
    			mysql_query("INSERT INTO `".$table_room."` (`room`,`desc`,`user_id`) VALUES ('".clean($_POST['room'])."', ".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", ".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_room') {
    		if(@mysql_result(mysql_query($sql." AND `desc` ".($_POST['desc']?"='".$_POST['desc']."'":"IS NULL")),0)) {
    			$text="Изменить невозможно, такое помещение существует!!!";
    		} else {
    			mysql_query("UPDATE `".$table_room."` SET `room`='".clean($_POST['room'])."', `desc`=".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такое помещение существует!!!");
    		}
    	}
    	echo $text;
    	die;
    }

// удаление помещения div
    if(isset($_GET['act']) && $_GET['act']=='d_room' && is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM `".$table_node."` AS n1 WHERE n1.room_id=".clean($_GET['id']);
    	$room=mysql_result(mysql_query("SELECT room FROM `".$table_room."` AS l1 WHERE l1.id =".clean($_GET['id'])),0);
    	if(mysql_result(mysql_query($sql),0)) {
    		$text='<div class="span11 m5">&nbsp;Помещение "'.$room.'" используется. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
    	} else {
    		$text='<div class="span10 m5">&nbsp;Удалить помещение "'.$room.'"?</div>'.button_ok_cancel('div_del','d_room');
    	}
    	echo $text;
    	die;
    }

// удаление помещения sql
    if(isset($_POST['act']) && $_POST['act']=='d_room' && is_numeric($_POST['id']) ) {
    	if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_node."` AS n1 WHERE n1.room_id = ".clean($_POST['id']).""),0)) {
    		// удаляем помещение
    		mysql_query("DELETE FROM `".$table_room."` WHERE `id` = ".clean($_POST['id']));
    	} else echo "not exist";
    	die;
    }

// помещение end -------------------------------------------------------------------------------------------------------

// ключи begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование ключа в div
    if(isset($_GET['act']) && ($_GET['act']=='n_key' || $_GET['act']=='e_key') ) {
        if($_GET['act']=='e_key')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM `" . $table_keys . "` WHERE id = ".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $num=$result['num'];
            $desc=$result['desc'];
        }
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="num" value="'.$num.'" placeholder="Ключ" /></div>';
        $text.='<div class="span6 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.=button_ok_cancel('div_new');
        echo $text;
        die;
    }

// ввод нового/редактирование ключа в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['num']) && ($_POST['act']=='n_key' || $_POST['act']=='e_key') ) {
        $sql='SELECT * FROM `'.$table_keys.'` WHERE num="'.clean($_POST['num']).'"';
        if($_POST['act']=='n_key') {
            if(@mysql_result(mysql_query($sql),0)) {
                $text="Создать невозможно, такой ключ существует!!!";
            } else {
                mysql_query("INSERT INTO `".$table_keys."` (`num`,`desc`,`user_id`) VALUES ('".clean($_POST['num'])."', ".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", ".$user_id.")");
            }
        } elseif($_POST['act']=='e_key') {
            if(@mysql_result(mysql_query($sql." AND `desc` ".($_POST['desc']?"='".$_POST['desc']."'":"IS NULL")),0)) {
                $text="Изменить невозможно, такой ключ существует!!!";
            } else {
                mysql_query("UPDATE `".$table_keys."` SET `num`='".clean($_POST['num'])."', `desc`=".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такой ключ существует!!!");
            }
        }
        echo $text;
        die;
    }

// удаление ключа div
    if(isset($_GET['act']) && $_GET['act']=='d_key' && is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM `".$table_keys."` AS k1 WHERE k1.node_id IS NOT NULL AND k1.id=".clean($_GET['id']);
        $result=mysql_fetch_assoc(mysql_query("SELECT k1.num, n1.id, n1.address FROM `".$table_keys."` AS k1 LEFT JOIN `".$table_node."` AS n1 ON n1.id = k1.node_id WHERE k1.id =".clean($_GET['id'])),0);
        if(mysql_result(mysql_query($sql),0)) {
            $text='<div class="span11 m5">&nbsp;Ключ "'.$result['num'].'" используется для узла <a href="?act=s_pq&o_node&node_id='.$result['id'].'" target="_blank">'.$result['address'].'</a>. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
        } else {
            $text='<div class="span10 m5">&nbsp;Удалить ключ "'.$num.'"?</div>'.button_ok_cancel('div_del','d_key');
        }
        echo $text;
        die;
    }
    
// удаление ключа sql
    if(isset($_POST['act']) && $_POST['act']=='d_key' && is_numeric($_POST['id']) ) {
        if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_keys."` AS k1 WHERE k1.node_id IS NOT NULL AND k1.id = ".clean($_POST['id']).""),0)) {
            // удаляем ключ
            mysql_query("DELETE FROM `".$table_keys."` WHERE `id` = ".clean($_POST['id']));
        } else echo "not exist";
        die;
    }
// ключи end -------------------------------------------------------------------------------------------------------

// ключи к узлам begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование ключи к узлам в div
    if(isset($_GET['act']) && $_GET['act']=='e_key_node' && isset($_GET['node_id']) ) {
        $id=clean($_GET['node_id']);
        $sql="SELECT * FROM `".$table_keys."` WHERE node_id IS NULL OR node_id = ".$id." ORDER BY LENGTH(num), num";
        $result = mysql_query($sql);
        if(mysql_num_rows($result)){
            $select_key='<select id="key_node">';
            //$select_key.='<option value="0">---</option>';
            while($row=mysql_fetch_assoc($result)){
                $select_key.='<option value="'.$row['id'].'" '.($row['node_id']==$id?'SELECTED':'').'>'.$row['num'].' '.($row['desc']?'('.$row['desc'].')':'').'</option>';
            }
            $select_key.='</select>';
        }
        $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
        $text.='<div class="span3 m0 input-control text">'.$select_key.'</div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование/удаление ключи к узлам в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && ( ( $_POST['act']=='e_key_node' && is_numeric($_POST['num']) ) || $_POST['act']=='d_key_node' ) ) {
        // удаление старого
        mysql_query("UPDATE `".$table_keys."` SET `node_id`=NULL WHERE `node_id`=".clean($_POST['id']).";");
        // если изменение, то ввод нового
        if($_POST['act']=='e_key_node') mysql_query("UPDATE `".$table_keys."` SET `node_id`=".clean($_POST['id'])." WHERE `id`=".clean($_POST['num']).";");
        die;
    }
    
// удаление ключи к узлам div
    if(isset($_GET['act']) && $_GET['act']=='d_key_node' && is_numeric($_GET['node_id'])) {
        $text='
        <div class="span10 m5">&nbsp;Удалить ключ?</div>
        <div class="span2 toolbar m0">
            <input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// ключи к узлам end -------------------------------------------------------------------------------------------------------

// лифтёрки begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование лифтёрки в div
    if(isset($_GET['act']) && ($_GET['act']=='n_lift_type' || $_GET['act']=='e_lift_type') ) {
        if($_GET['act']=='e_lift_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM `" . $table_lift_type . "` WHERE id = ".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $name=$result['name'];
            $tel=$result['tel'];
            $desc=$result['desc'];
        }
    
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span4 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Лифтёрка" /></div>';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="tel" value="'.$tel.'" placeholder="Телефоны" /></div>';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.=button_ok_cancel('div_new');
        echo $text;
        die;
    }

// ввод нового/редактирование лифтёрки в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['name']) && isset($_POST['tel']) && ($_POST['act']=='n_lift_type' || $_POST['act']=='e_lift_type') ) {
        $sql='SELECT * FROM `'.$table_lift_type.'` WHERE name="'.clean($_POST['name']).'"';
        if($_POST['act']=='n_lift_type') {
            if(@mysql_result(mysql_query($sql),0)) {
                $text="Создать невозможно, такая лифтёрка существует!!!";
            } else {
                mysql_query("INSERT INTO `".$table_lift_type."` (`name`,`tel`,`desc`,`user_id`) VALUES ('".clean($_POST['name'])."', '".clean($_POST['tel'])."', ".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", ".$user_id.")");
            }
        } elseif($_POST['act']=='e_lift_type') {
            if(@mysql_result(mysql_query($sql." AND `desc` ".($_POST['desc']?"='".$_POST['desc']."'":"IS NULL")),0)) {
                $text="Изменить невозможно, такая лифтёрка существует!!!";
            } else {
                mysql_query("UPDATE `".$table_lift_type."` SET `name`='".clean($_POST['name'])."', `tel`='".clean($_POST['tel'])."', `desc`=".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";")
                    or die("Изменить невозможно, такая лифтёрка существует!!!");
            }
        }
        echo $text;
        die;
    }

// удаление лифтёрки div
    if(isset($_GET['act']) && $_GET['act']=='d_lift_type' && is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM `".$table_lift."` AS l1 WHERE l1.lift_id=".clean($_GET['id']);
        $name=mysql_result(mysql_query("SELECT name FROM `".$table_lift_type."` AS lt1 WHERE lt1.id =".clean($_GET['id'])),0);
        if(mysql_result(mysql_query($sql),0)) {
        	$node_id=mysql_result(mysql_query("SELECT node_id FROM `".$table_lift."` AS l1 WHERE l1.lift_id =".clean($_GET['id'])),0);
            $text='<div class="span11 m5">&nbsp;Лифрётка "'.$name.'" используется <a href="?act=s_pq&p_node&node_id='.$node_id.'" target="_blank">'.addr_id($node_id).'</a>. Удалить нельзя!!!</div>'.button_ok_cancel('div_cancel');
        } else {
            $text='<div class="span10 m5">&nbsp;Удалить лифтёрку "'.$name.'"?</div>'.button_ok_cancel('div_del','d_lift_type');
        }
        echo $text;
        die;
    }

// удаление лифтёрки sql
    if(isset($_POST['act']) && $_POST['act']=='d_lift_type' && is_numeric($_POST['id']) ) {
        if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_lift_type."` AS lt1 WHERE lt1.node_id IS NOT NULL AND lt1.id=".clean($_GET['id'])),0)) {
            // удаляем лифтёрку
            mysql_query("DELETE FROM `".$table_lift_type."` WHERE `id` = ".clean($_POST['id']));
        } else echo "not exist";
        die;
    }

// лифтёрки end -------------------------------------------------------------------------------------------------------

// лифтёрки к узлам begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование лифтёрки к узлам в div
    if(isset($_GET['act']) && $_GET['act']=='e_lift_node' && isset($_GET['node_id']) ) {
        $id=clean($_GET['node_id']);
        //$sql="SELECT lt1.* FROM `".$table_lift_type."` AS lt1 LEFT JOIN `".$table_lift."` AS l1 ON lt1.id = l1.lift_id WHERE l1.node_id IS NULL OR l1.node_id = ".$id." ORDER BY lt1.name";
        $sql="SELECT lt1.*, l1.node_id AS node_id FROM `".$table_lift_type."` AS lt1 LEFT JOIN `".$table_lift."` AS l1 ON lt1.id = l1.lift_id AND l1.node_id = ".$id." GROUP BY lt1.name ORDER BY lt1.name";
        $result = mysql_query($sql);
        if(mysql_num_rows($result)){
            $select_lift='<select id="lift_node">';
            while($row=mysql_fetch_assoc($result)){
                $select_lift.='<option value="'.$row['id'].'" '.($row['node_id']==$id?'SELECTED':'').'>'.$row['name'].'</option>';
            }
            $select_lift.='</select>';
        }
        $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
        $text.='<div class="span4 m0 input-control text">'.$select_lift.'</div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }

// ввод нового/редактирование/удаление лифтёрки к узлам в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && ( ( $_POST['act']=='e_lift_node' && is_numeric($_POST['lift']) ) || $_POST['act']=='d_lift_node' ) ) {
        if($_POST['act']=='e_lift_node') {
            if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_lift."` AS l1 WHERE l1.node_id=".clean($_POST['id'])),0)) {
                mysql_query("INSERT INTO `".$table_lift."` (`node_id`,`lift_id`,`desc`,`user_id`) VALUES (".clean($_POST['id']).", ".clean($_POST['lift']).", ".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", ".$user_id.")");
            } else {
                mysql_query("UPDATE `".$table_lift."` SET `lift_id`=".clean($_POST['lift'])." WHERE `node_id`=".clean($_POST['id']).";");  
            }
        } else if($_POST['act']=='d_lift_node') {
            mysql_query("DELETE FROM `".$table_lift."` WHERE `node_id` = ".clean($_POST['id']));
        } else echo "not exist";
        die;
    }
// удаление лифтёрки к узлам div
    if(isset($_GET['act']) && $_GET['act']=='d_lift_node' && is_numeric($_GET['node_id'])) {
        $text='
        <div class="span10 m5">&nbsp;Удалить лифтёрку?</div>
        <div class="span2 toolbar m0">
            <input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// лифтёрки к узлам end -------------------------------------------------------------------------------------------------------

// описание begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование описания post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && $_POST['act']=='e_desc_text' ) {
        if(@mysql_result(mysql_query('SELECT * FROM `'.$table_desc.'` WHERE node_id='.clean($_POST['id'])),0)) {
            mysql_query("UPDATE `".$table_desc."` SET `text`='".clean($_POST['text'])."', `desc`=".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", user_id=".$user_id." WHERE `node_id`=".clean($_POST['id']).";");
        } else {
            mysql_query("INSERT INTO `".$table_desc."` (`text`, `node_id`, `desc`,`user_id`) VALUES ('".clean($_POST['text'])."', ".clean($_POST['id']).", ".($_POST['desc']?"'".$_POST['desc']."'":"NULL").", ".$user_id.")");
        }
        die;
    }
    
// удаление описание sql
    if(isset($_POST['act']) && $_POST['act']=='d_desc_text' ) {
        mysql_query("DELETE FROM `".$table_desc."` WHERE `node_id` = ".clean($_POST['id']));
        die;
    }
// описание end -------------------------------------------------------------------------------------------------------

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// узел start -------------------------------------------------------------------------------------------------------
// ввод нового узла div
    if(isset($_GET['act']) && ($_GET['act']=='n_node' || $_GET['act']=='e_node') ) {
        if($_GET['act']=='e_node')
        {
            $node_id=clean($_GET['node_id']);
            $sql="SELECT * FROM `".$table_node."` WHERE `id`='".$node_id."';";
            //$address=@mysql_result(mysql_query("SELECT `address` FROM `".$table_node."` WHERE `id`='".$node_id."';"),0);
            $result=mysql_fetch_assoc(mysql_query($sql));
            $street_id=$result['street_id'];
            $street_num_id=$result['street_num_id'];
            $num_ent=$result['num_ent'];
            $location_id=$result['location_id'];
            $room_id=$result['room_id'];
            $desc=$result['desc'];
            $incorrect=$result['incorrect'];
        }
        // улица
        $sql="SELECT * FROM `".$table_street_name."` ORDER BY name";
        $result = mysql_query($sql);
        if(mysql_num_rows($result)){
            $select_street_name='<select id="street_name">';
            $select_street_name.='<option value="0">Улица</option>';
            while($row=mysql_fetch_assoc($result)){
                $select_street_name.='<option value="'.$row['id'].'"';
                if($street_id==$row['id']) {
                    $select_street_name.=" SELECTED";
                }
                $select_street_name.='>'.$row['name'].'</option>';
            }
            $select_street_name.='</select>';
        }
        // номер дома
        if($street_num_id) {
            $sql="SELECT num FROM `".$table_street_num."` WHERE `id`=".$street_num_id." AND street_name_id=".$street_id;
            $street_num=mysql_result(mysql_query($sql),0);
        }
        // размещение
        $sql="SELECT * FROM `".$table_location."` ORDER BY location";
        $result = mysql_query($sql);
        if(mysql_num_rows($result)){
            $select_location='<select id="location">';
            $select_location.='<option value="0">Размещение</option>';
            while($row=mysql_fetch_assoc($result)){
                $select_location.='<option value="'.$row['id'].'"';
                if($location_id==$row['id']) {
                    $select_location.=" SELECTED";
                }
                $select_location.='>'.$row['location'].'</option>';
            }
            $select_location.='</select>';
        }
        // помещение
        $sql="SELECT * FROM `".$table_room."` ORDER BY room";
        $result = mysql_query($sql);
        if(mysql_num_rows($result)){
            $select_room='<select id="room">';
            $select_room.='<option value="0">Помещение</option>';
            while($row=mysql_fetch_assoc($result)){
                $select_room.='<option value="'.$row['id'].'"';
                if($room_id==$row['id']) {
                    $select_room.=" SELECTED";
                }
                $select_room.='>'.$row['room'].'</option>';
            }
            $select_room.='</select>';
        }
        $text='<input type="hidden" id="act" value="'.clean($_GET['act']).'" />';
        $text.='<input type="hidden" id="id" value="'.$node_id.'" />';
        $text.='<div class="span3 m0 input-control text">'.$select_street_name.'</div>';
        $text.='<div class="span1 span1_5 m0 input-control text"><input class="mini" type="text" id="street_num" value="'.$street_num.'" placeholder="№ дома" /></div>';
        $text.='<div class="span1 m0 input-control text"><input class="mini" type="text" id="num_ent" value="'.$num_ent.'" placeholder="№ подъезда" /></div>';
        $text.='<div class="span1 span1_5 m0 input-control text">'.$select_location.'</div>';
        $text.='<div class="span1 span1_5 m0 input-control text">'.$select_room.'</div>';

        $text.='<div class="span2 m5"><label class="checkbox"><input type="checkbox" id="incorrect" '.($incorrect==1?'checked':'').'><span>Проблемма</span></label></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        $text.='<div class="span12 m0 input-control text"><input class="mini" type="text" id="desc" value="'.$desc.'" placeholder="Введите описание" /></div>';
        echo $text;
        die;
    }

// ввод нового/редактирование узла в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['street_name_id']) && isset($_POST['street_num']) && ($_POST['act']=='n_node' || $_POST['act']=='e_node') ) {
        // если нету номера улицы, то вносим
        if(!$_POST['street_num_id']) {
            mysql_query("INSERT INTO `".$table_street_num."` (`street_name_id`,`num`,`desc`,`user_id`) VALUES (".clean($_POST['street_name_id']).", '".clean($_POST['street_num'])."', NULL,".$user_id.")");
            $sql="SELECT id FROM `".$table_street_num."` WHERE street_name_id=".clean($_POST['street_name_id'])." AND num='".clean($_POST['street_num'])."'";
            $street_num_id=@mysql_result(mysql_query($sql),0);
        } else $street_num_id=clean($_POST['street_num_id']);
        $sql='SELECT * FROM `'.$table_node.'`
            WHERE street_id='.clean($_POST['street_name_id']).'
            AND street_num_id='.$street_num_id.'
            AND num_ent '.(empty($_POST['num_ent'])?"IS NULL":"=".$_POST['num_ent']).'
            AND location_id '.($_POST['location_id']!=0?"=".$_POST['location_id']:"IS NULL").'
            AND room_id '.($_POST['room_id']!=0?"=".$_POST['room_id']:"IS NULL").'
            AND incorrect '.(empty($_POST['incorrect'])?"IS NULL":"=1").'';
        if($_POST['act']=='n_node') {
            if(@mysql_result(mysql_query($sql),0)) {
                $text="Создать невозможно, такое помещение существует!!!";
            } else {
                mysql_query("INSERT INTO `".$table_node."` (`street_id`,`street_num_id`, `num_ent`, `location_id`, `room_id`, `incorrect`, `desc`,`user_id`) VALUES (".clean($_POST['street_name_id']).", ".$street_num_id.", ".(empty($_POST['num_ent'])?'NULL':clean($_POST['num_ent'])).", ".($_POST['location_id']!=0?clean($_POST['location_id']):"NULL").", ".($_POST['room_id']!=0?clean($_POST['room_id']):"NULL").", ".(empty($_POST['incorrect'])?"NULL":"1").", ".(empty($_POST['desc'])?"NULL":clean($_POST['desc'])).", ".$user_id.")");
                $result=mysql_fetch_assoc(mysql_query($sql));
                mysql_query("UPDATE `".$table_node."` SET `address`='".addr_id($result['id'])."' WHERE id=".$result['id']);
                die;
            }
        } elseif($_POST['act']=='e_node') {
            if(@mysql_result(mysql_query($sql.' AND desc '.(empty($_POST['desc'])?"IS NULL":"='".$_POST['desc']."'")),0)) {
                $text="Изменить невозможно, аналогичное помещение существует!!!";
            } else {
                mysql_query("UPDATE `".$table_node."` SET `street_id`=".clean($_POST['street_name_id']).", 
                    `street_num_id`=".$street_num_id.",
                    `num_ent`=".(empty($_POST['num_ent'])?"NULL":$_POST['num_ent']).",
                    `location_id`=".($_POST['location_id']!=0?$_POST['location_id']:"NULL").",
                    `room_id`=".($_POST['room_id']!=0?$_POST['room_id']:"NULL").",
                    `incorrect`=".(empty($_POST['incorrect'])?"NULL":"1").",
                    `desc`= ".(empty($_POST['desc'])?"NULL":"'".$_POST['desc']."'").",
                    `user_id`=".$user_id." WHERE `id`=".clean($_POST['id']).";");
                mysql_query("UPDATE `".$table_node."` SET `address`='".addr_id(clean($_POST['id']))."' WHERE id=".clean($_POST['id']));
                die;
            }
        }
        echo $text;
        die;
    }

// удаление узла div
    if(isset($_GET['act']) && $_GET['act']=='d_node' && is_numeric($_GET['node_id'])) {
        $sql="SELECT COUNT(*) FROM `".$table_pq."` AS p1 WHERE p1.node =".clean($_GET['node_id']);
        if(mysql_result(mysql_query($sql),0)) {
            $text='
            <div class="span11 m5">&nbsp;Узел "'.clean($_GET['addr']).'" не пустой. Перед удалением узла необходимо удалить пассивное оборудование!!!</div>
            <div class="span1 toolbar m0">
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
            $text='
            <div class="span10 m5">&nbsp;Удалить узел "'.clean($_GET['addr']).'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_node" rel="'.clean($_GET['node_id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        }
        echo $text;
        die;
    }

// удаление узла
    if(isset($_POST['act']) && $_POST['act']=='d_node' && is_numeric($_POST['id']) ) {
        if(!@mysql_result(mysql_query("SELECT * FROM `".$table_node."` AS n1, `".$table_pq."` AS p1 WHERE n1.id=".clean($_POST['id']))." AND p1.node !=".clean($_POST['id']),0)) {
            $street_num_id=mysql_result(mysql_query("SELECT street_num_id FROM `".$table_node."` WHERE `id`=".clean($_POST['id'])),0);
            mysql_query("DELETE FROM `".$table_node."` WHERE `id` = ".clean($_POST['id']));
            if(!mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_node."` WHERE street_num_id =".$street_num_id),0))
                mysql_query("DELETE FROM `".$table_street_num."` WHERE `id` = ".$street_num_id);
            die;
        }
        echo "not exist";
        die;
    }
// узел end -------------------------------------------------------------------------------------------------------

// типы коммутаторов begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование типа коммутатора в div
    if(isset($_GET['act']) && ($_GET['act']=='n_switch_type' || $_GET['act']=='e_switch_type') ) {
    	if($_GET['act']=='e_switch_type')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM `" . $table_switch_type . "` WHERE id = ".$id;
    		$result=mysql_fetch_assoc(mysql_query($sql));
    		$name=$result['name'];
    		$ports_num=$result['ports_num'];
    		$unit=$result['unit'];
    		$power=$result['power'];
    		$desc=$result['desc'];
    	}
    	$text='<input type="hidden" id="id" value="'.$id.'">';
    	$text.='<div class="span2 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Название" /></div>';
    	$text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="ports_num" value="'.$ports_num.'" placeholder="Портов" /></div>';
    	$text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" /></div>';
    	$text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="power" value="'.$power.'" placeholder="Мощность" /></div>';
    	$text.='<div class="span4 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
    	$text.='<div class="span2 toolbar m0">
	    	<button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
	    	<button class="icon-blocked m0" id="exit" title="Отмена"></button>
    	</div>';
    	echo $text;
    	die;
    }
    
// ввод нового/редактирование типа коммутатора в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['name']) && isset($_POST['ports_num']) && ($_POST['act']=='n_switch_type' || $_POST['act']=='e_switch_type') ) {
    	/*if(!empty($_POST['desc'])) {
    		$desc_sql='"'.clean($_POST['desc']).'"';
    	} else {
    		$desc_sql="NULL";
    	}*/
    	$desc_sql=(empty($_POST['desc'])?'NULL':"'".clean($_POST['desc'])."'");
    	$sql='SELECT * FROM `'.$table_switch_type.'` WHERE name="'.clean($_POST['name']).'" AND ports_num='.clean($_POST['ports_num']);
    	if($_POST['act']=='n_switch_type') {
    		if(@mysql_result(mysql_query($sql),0)) {
    			$text="Создать невозможно, такой тип коммутатор существует!!!";
    		} else {
    			mysql_query("INSERT INTO `".$table_switch_type."` (`name`,`ports_num`,`unit`,`power`,`desc`,`user_id`) VALUES ('".clean($_POST['name'])."', ".clean($_POST['ports_num']).", ".($_POST['unit']?clean($_POST['unit']):'NULL').", ".($_POST['power']?clean($_POST['power']):'NULL').", ".$desc_sql.",".$user_id.")");
                die;
    		}
    	} elseif($_POST['act']=='e_switch_type') {
    		if(@mysql_result(mysql_query($sql.' AND desc = '.$desc_sql),0)) {
    			$text="Изменить невозможно, аналогичный тип коммутатора существует!!!";
    		} else {
    			mysql_query("UPDATE `".$table_switch_type."` SET `name`='".clean($_POST['name'])."', `ports_num`=".clean($_POST['ports_num']).", `unit`=".($_POST['unit']?clean($_POST['unit']):'NULL').", `power`=".($_POST['power']?clean($_POST['power']):'NULL').", `desc`=".$desc_sql.", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
                die;
    		}
    	}
    	echo $text;
    	die;
    }
    
// удаление типа коммутатора div
    if(isset($_GET['act']) && $_GET['act']=='d_switch_type' && is_numeric($_GET['id'])) {
    	$sql="SELECT COUNT(*) FROM `".$table_switches."` AS s1 WHERE s1.switch_type_id=".clean($_GET['id']);
    	$name=mysql_result(mysql_query("SELECT name FROM `".$table_switch_type."` AS st1 WHERE st1.id =".clean($_GET['id'])),0);
    	if(mysql_result(mysql_query($sql),0)) {
    		$text='
    		<div class="span11 m5">&nbsp;Тип коммутатора "'.$name.'" используется. Удалить нельзя!!!</div>
    		<div class="span1 toolbar m0">
    		<button class="icon-blocked m0" id="exit" title="Отмена"></button>
    		</div>';
    	} else {
    		$text='
    		<div class="span10 m5">&nbsp;Удалить тип коммутатора "'.$name.'"?</div>
    		<div class="span2 toolbar m0">
	    		<button class="icon-checkmark m0" id="d_switch_type" rel="'.clean($_GET['id']).'" title="Ok"></button>
	    		<button class="icon-blocked m0" id="exit" title="Отмена"></button>
    		</div>';
    	}
    	echo $text;
    	die;
    }
    
// удаление типа коммутатора sql
    if(isset($_POST['act']) && $_POST['act']=='d_switch_type' && is_numeric($_POST['id']) ) {
    	if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_switches."` AS s1 WHERE s1.switch_type_id = ".clean($_POST['id']).""),0)) {
    		// удаляем помещение
    		mysql_query("DELETE FROM `".$table_switch_type."` WHERE `id` = ".clean($_POST['id']));
    		die;
    	}
    	echo "not exist";
    	die;
    }
    
// типы коммутаторов end -------------------------------------------------------------------------------------------------------

// коммутаторы begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование коммутаторов в div
    if(isset($_GET['act']) && ($_GET['act']=='n_switches' || $_GET['act']=='e_switches') ) {
        if($_GET['act']=='e_switches')
        {
            $id=clean($_GET['id']);
            //$sql="SELECT *, s1.desc AS sw_desc FROM `".$table_switches."` s1 LEFT JOIN `".$table_switch_type."` AS st1 ON s1.switch_type_id = st1.id WHERE s1.id = ".$id;
            $sql="SELECT sw1 . * , st1.name, st1.ports_num, st1.unit, st1.power, sn1.sn
                FROM `".$table_switch_type."` AS st1, `".$table_switches."` AS sw1
                LEFT JOIN `".$table_sn."` AS sn1 ON sn1.eq = sw1.id AND eq_type='".$switch_id."'
                WHERE st1.id = sw1.switch_type_id
                AND sw1.id = ".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $name=$result['name'];
            $ports_num=$result['ports_num'];
            $used_ports=$result['used_ports'];
            $sn=$result['sn'];
            $desc=$result['desc'];
            $text='<input type="hidden" id="id" value="'.$id.'">';
            $text.='<div class="span3 m0 input-control text"><input type="text" value="'.$name.'" placeholder="Название" disabled /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="ports_num" value="'.$ports_num.'" disabled placeholder="Портов" /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="used_ports" value="'.$used_ports.'" placeholder="Занято" /></div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" value="'.$sn.'" disabled placeholder="S/N" /></div>';
        } elseif($_GET['act']=='n_switches') {
            // тип коммутатора
            $sql="SELECT * FROM `".$table_switch_type."` ORDER BY name";
            $result = mysql_query($sql);
            $select_switch_type_js="var arr = [];";
            if(mysql_num_rows($result)){
                $select_switch_type='<select id="switch_type_id" onchange="$(\'input#ports_num\').val(arr[$(\'select#switch_type_id\').val()]);">';
                $select_switch_type.='<option value="0">---</option>';
                while($row=mysql_fetch_assoc($result)){
                    $select_switch_type.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
                    $select_switch_type_js.="arr[".$row['id']."] = '".$row['ports_num']."';";
                }
                $select_switch_type.='</select>';
                $select_switch_type='<script type="text/javascript">'.$select_switch_type_js.'</script>'.$select_switch_type;
            }
            $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
            $text.='<div class="span3 m0 input-control text">'.$select_switch_type.'</div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="ports_num" value="" disabled placeholder="Портов" /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="used_ports" value="" placeholder="Занято" /></div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" id="sn" value="" placeholder="S/N" /></div>';
        }
        $text.='<div class="span3 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование коммутатора в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && ($_POST['act']=='n_switches' || $_POST['act']=='e_switches') ) {
        $desc_sql=(empty($_POST['desc'])?'NULL':"'".clean($_POST['desc'])."'");
        if($_POST['act']=='n_switches') {
            mysql_query("INSERT INTO `".$table_switches."` (`node_id`,`switch_type_id`,`used_ports`,`desc`,`user_id`) VALUES (".clean($_POST['node_id']).", ".clean($_POST['switch_type_id']).", ".($_POST['used_ports']?clean($_POST['used_ports']):'NULL').", ".$desc_sql.",".$user_id.")");
            if($_POST['sn']) {
                $eq=mysql_insert_id();
                mysql_query("INSERT INTO `".$table_sn."` (`sn`,`eq`,`eq_type`,`desc`,`user_id`) VALUES ('".clean($_POST['sn'])."', ".$eq.", '".$switch_id."', NULL,".$user_id.")");
                die;                
            }
        } else if($_POST['act']=='e_switches') {
            mysql_query("UPDATE `".$table_switches."` SET `used_ports`=".($_POST['used_ports']?clean($_POST['used_ports']):'NULL').", `desc`=".$desc_sql.", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
            die;
        }
        echo $text;
        die;
    }
    
// удаление коммутатора div
    if(isset($_GET['act']) && $_GET['act']=='d_switches' && is_numeric($_GET['id'])) {
        //$sql="SELECT COUNT(*) FROM `".$table_switches."` AS s1 WHERE s1.switch_type_id=".clean($_GET['id']);
        $name=mysql_result(mysql_query("SELECT name FROM `".$table_switches."` AS sw1, `".$table_switch_type."` AS st1 WHERE st1.id = sw1.switch_type_id AND sw1.id =".clean($_GET['id'])),0);
            $text='
            <div class="span10 m5">&nbsp;Удалить коммутатор "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_switches" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        //}
        echo $text;
        die;
    }
    
// удаление коммутатора sql
    if(isset($_POST['act']) && $_POST['act']=='d_switches' && is_numeric($_POST['id']) ) {
        //if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_switches."` AS s1 WHERE s1.switch_type_id = ".clean($_POST['id']).""),0)) {
            // удаляем помещение
            mysql_query("DELETE FROM `".$table_switches."` WHERE `id` = ".clean($_POST['id']));
            mysql_query("DELETE FROM `".$table_sn."` WHERE `eq` = ".clean($_POST['id']));
            //mysql_query("DELETE FROM `".$table_switches."` WHERE `id` = ".clean($_POST['id']));
            die;
        //}
        //echo "not exist";
        //die;
    }
    
// коммутаторы end -------------------------------------------------------------------------------------------------------

// типы медиаконвертеров begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование типа коммутатора в div
    if(isset($_GET['act']) && ($_GET['act']=='n_mc_type' || $_GET['act']=='e_mc_type') ) {
        if($_GET['act']=='e_mc_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM `" . $table_mc_type . "` WHERE id = ".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $name=$result['name'];
            $power=$result['power'];
            $desc=$result['desc'];
        }
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Название" /></div>';
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="power" value="'.$power.'" placeholder="Мощность" /></div>';
        $text.='<div class="span6 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// ввод нового/редактирование типа медиаконвертера в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_mc_type' || $_POST['act']=='e_mc_type') ) {
        $desc_sql=(empty($_POST['desc'])?'NULL':"'".clean($_POST['desc'])."'");
        $sql='SELECT * FROM `'.$table_mc_type.'` WHERE name="'.clean($_POST['name']).'"';
        if($_POST['act']=='n_mc_type') {
            if(@mysql_result(mysql_query($sql),0)) {
                $text="Создать невозможно, такой тип медиаконвертера существует!!!";
            } else {
                mysql_query("INSERT INTO `".$table_mc_type."` (`name`,`power`,`desc`,`user_id`) VALUES ('".clean($_POST['name'])."', ".($_POST['power']?clean($_POST['power']):'NULL').", ".$desc_sql.",".$user_id.")");
                die;
            }
        } elseif($_POST['act']=='e_mc_type') {
            if(@mysql_result(mysql_query($sql.' AND desc = '.$desc_sql),0)) {
                $text="Изменить невозможно, аналогичный тип медиаконвертера существует!!!";
            } else {
                mysql_query("UPDATE `".$table_mc_type."` SET `name`='".clean($_POST['name'])."', `power`=".($_POST['power']?clean($_POST['power']):'NULL').", `desc`=".$desc_sql.", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
                die;
            }
        }
        echo $text;
        die;
    }
    
// удаление типа медиаконвертера div
    if(isset($_GET['act']) && $_GET['act']=='d_mc_type' && is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM `".$table_mc."` AS mc1 WHERE mc1.mc_type_id=".clean($_GET['id']);
        $name=mysql_result(mysql_query("SELECT name FROM `".$table_mc_type."` AS mc1 WHERE mc1.id =".clean($_GET['id'])),0);
        if(mysql_result(mysql_query($sql),0)) {
            $text='
            <div class="span11 m5">&nbsp;Тип медиаконвертера "'.$name.'" используется. Удалить нельзя!!!</div>
            <div class="span1 toolbar m0">
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
            $text='
            <div class="span10 m5">&nbsp;Удалить тип медиаконвертера "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_mc_type" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        }
        echo $text;
        die;
    }
    
// удаление типа медиаконвертера sql
    if(isset($_POST['act']) && $_POST['act']=='d_mc_type' && is_numeric($_POST['id']) ) {
        if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_mc."` AS mc1 WHERE mc1.mc_type_id = ".clean($_POST['id']).""),0)) {
            // удаляем помещение
            mysql_query("DELETE FROM `".$table_mc_type."` WHERE `id` = ".clean($_POST['id']));
            die;
        }
        echo "not exist";
        die;
    }
    
// типы медиаконвертеров end -------------------------------------------------------------------------------------------------------

// медиаконвертеры begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование медиакорветрета в div
    if(isset($_GET['act']) && ($_GET['act']=='n_mc' || $_GET['act']=='e_mc') ) {
        if($_GET['act']=='e_mc')
        {
            $id=clean($_GET['id']);
            $sql="SELECT mc1 . * , mt1.name, mt1.power, sn1.sn
                FROM `".$table_mc_type."` AS mt1, `".$table_mc."` AS mc1
                LEFT JOIN `".$table_sn."` AS sn1 ON sn1.eq = mc1.id AND eq_type='".$mc_id."'
                WHERE mt1.id = mc1.mc_type_id
                AND mc1.id = ".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $name=$result['name'];
            $sn=$result['sn'];
            $desc=$result['desc'];
            $text='<input type="hidden" id="id" value="'.$id.'">';
            $text.='<div class="span3 m0 input-control text"><input type="text" value="'.$name.'" placeholder="Название" disabled /></div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" value="'.$sn.'" disabled placeholder="S/N" /></div>';
        } elseif($_GET['act']=='n_mc') {
            // тип медиаконвертера
            $sql="SELECT * FROM `".$table_mc_type."` ORDER BY name";
            $result = mysql_query($sql);
            if(mysql_num_rows($result)){
                $select_mc_type='<select id="mc_type_id">';
                $select_mc_type.='<option value="0">---</option>';
                while($row=mysql_fetch_assoc($result)){
                    $select_mc_type.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
                }
                $select_mc_type.='</select>';
            }
            $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
            $text.='<div class="span3 m0 input-control text">'.$select_mc_type.'</div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" id="sn" value="" placeholder="S/N" /></div>';
        }
        $text.='<div class="span5 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование медиаконвертера в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && ($_POST['act']=='n_mc' || $_POST['act']=='e_mc') ) {
        $desc_sql=(empty($_POST['desc'])?'NULL':"'".clean($_POST['desc'])."'");
        if($_POST['act']=='n_mc') {
            mysql_query("INSERT INTO `".$table_mc."` (`node_id`,`mc_type_id`,`desc`,`user_id`) VALUES (".clean($_POST['node_id']).", ".clean($_POST['mc_type_id']).", ".$desc_sql.", ".$user_id.")");
            if($_POST['sn']) {
                $eq=mysql_insert_id();
                mysql_query("INSERT INTO `".$table_sn."` (`sn`,`eq`,`eq_type`,`desc`,`user_id`) VALUES ('".clean($_POST['sn'])."', ".$eq.", '".$switch_id."', NULL,".$user_id.")");
                die;                
            }
        } else if($_POST['act']=='e_mc') {
            mysql_query("UPDATE `".$table_mc."` SET `desc`=".$desc_sql.", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
            die;
        }
        echo $text;
        die;
    }
    
// удаление медиаконвертера div
    if(isset($_GET['act']) && $_GET['act']=='d_mc' && is_numeric($_GET['id'])) {
        $name=mysql_result(mysql_query("SELECT name FROM `".$table_mc."` AS mc1, `".$table_mc_type."` AS mt1 WHERE mt1.id = mc1.mc_type_id AND mc1.id =".clean($_GET['id'])),0);
            $text='
            <div class="span10 m5">&nbsp;Удалить медиаконвертер "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_mc" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        echo $text;
        die;
    }
    
// удаление медиаконвертера sql
    if(isset($_POST['act']) && $_POST['act']=='d_mc' && is_numeric($_POST['id']) ) {
            mysql_query("DELETE FROM `".$table_mc."` WHERE `id` = ".clean($_POST['id']));
            mysql_query("DELETE FROM `".$table_sn."` WHERE `eq` = ".clean($_POST['id']));
            die;
    }
    
// медиаконвертеры end -------------------------------------------------------------------------------------------------------

// типы рам/ящиков begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование типа рамы/ящика в div
    if(isset($_GET['act']) && ($_GET['act']=='n_box_type' || $_GET['act']=='e_box_type') ) {
        if($_GET['act']=='e_box_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM `" . $table_box_type . "` WHERE id = ".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $name=$result['name'];
            $unit=$result['unit'];
            $desc=$result['desc'];
        }
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Название" /></div>';
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" /></div>';
        $text.='<div class="span6 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// ввод нового/редактирование типа рамы/ящика в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_box_type' || $_POST['act']=='e_box_type') ) {
        $desc_sql=(empty($_POST['desc'])?'NULL':"'".clean($_POST['desc'])."'");
        $sql='SELECT * FROM `'.$table_box_type.'` WHERE name="'.clean($_POST['name']).'"';
        if($_POST['act']=='n_box_type') {
            if(@mysql_result(mysql_query($sql),0)) {
                $text="Создать невозможно, такой тип рамы/ящика существует!!!";
            } else {
                mysql_query("INSERT INTO `".$table_box_type."` (`name`,`unit`,`desc`,`user_id`) VALUES (\"".clean($_POST['name'])."\", ".($_POST['unit']?clean($_POST['unit']):'NULL').", ".$desc_sql.",".$user_id.")");
                die;
            }
        } elseif($_POST['act']=='e_box_type') {
            if(@mysql_result(mysql_query($sql.' AND desc = '.$desc_sql),0)) {
                $text="Изменить невозможно, аналогичный тип рамы/ящика существует!!!";
            } else {
                mysql_query("UPDATE `".$table_box_type."` SET `name`=\"".clean($_POST['name'])."\", `unit`=".($_POST['unit']?clean($_POST['unit']):'NULL').", `desc`=".$desc_sql.", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
                die;
            }
        }
        echo $text;
        die;
    }
    
// удаление типа рамы/ящика div
    if(isset($_GET['act']) && $_GET['act']=='d_box_type' && is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM `".$table_box."` AS b1 WHERE b1.box_type_id=".clean($_GET['id']);
        $name=mysql_result(mysql_query("SELECT name FROM `".$table_box_type."` AS b1 WHERE b1.id =".clean($_GET['id'])),0);
        if(mysql_result(mysql_query($sql),0)) {
            $text='
            <div class="span11 m5">&nbsp;Тип рамы/ящика "'.$name.'" используется. Удалить нельзя!!!</div>
            <div class="span1 toolbar m0">
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
            $text='
            <div class="span10 m5">&nbsp;Удалить тип рамы/ящика "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_box_type" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        }
        echo $text;
        die;
    }
    
// удаление типа рамы/ящика sql
    if(isset($_POST['act']) && $_POST['act']=='d_box_type' && is_numeric($_POST['id']) ) {
        if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_box."` AS b1 WHERE b1.box_type_id = ".clean($_POST['id']).""),0)) {
            // удаляем тип раму/ящика
            mysql_query("DELETE FROM `".$table_box_type."` WHERE `id` = ".clean($_POST['id']));
            die;
        }
        echo "not exist";
        die;
    }
    
// типы рам/ящиков end -------------------------------------------------------------------------------------------------------

// рамы/ящики begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование рам/ящиков в div
    if(isset($_GET['act']) && ($_GET['act']=='n_box' || $_GET['act']=='e_box') ) {
        if($_GET['act']=='e_box')
        {
            $id=clean($_GET['id']);
            $sql="SELECT b1 . * , bt1.name, bt1.unit
                FROM `".$table_box_type."` AS bt1, `".$table_box."` AS b1
                WHERE bt1.id = b1.box_type_id
                AND b1.id = ".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $name=$result['name'];
            $desc=$result['desc'];
            $text='<input type="hidden" id="id" value="'.$id.'">';
            $text.='<div class="span3 m0 input-control text"><input type="text" value="'.$name.'" placeholder="Название" disabled /></div>';
        } elseif($_GET['act']=='n_box') {
            // тип рам/ящиков
            $sql="SELECT * FROM `".$table_box_type."` ORDER BY name";
            $result = mysql_query($sql);
            if(mysql_num_rows($result)){
                $select_box_type='<select id="box_type_id">';
                $select_box_type.='<option value="0">---</option>';
                while($row=mysql_fetch_assoc($result)){
                    $select_box_type.='<option value="'.$row['id'].'">'.$row['name'].($row['unit']?' ('.$row['unit'].'U)':'').'</option>';
                }
                $select_box_type.='</select>';
            }
            $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
            $text.='<div class="span3 m0 input-control text">'.$select_box_type.'</div>';
        }
        $text.='<div class="span7 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование рам/ящиков в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && ($_POST['act']=='n_box' || $_POST['act']=='e_box') ) {
        $desc_sql=(empty($_POST['desc'])?'NULL':"'".clean($_POST['desc'])."'");
        if($_POST['act']=='n_box') {
            mysql_query("INSERT INTO `".$table_box."` (`node_id`,`box_type_id`,`desc`,`user_id`) VALUES (".clean($_POST['node_id']).", ".clean($_POST['box_type_id']).", ".$desc_sql.", ".$user_id.")");
            die;
        } else if($_POST['act']=='e_box') {
            mysql_query("UPDATE `".$table_box."` SET `desc`=".$desc_sql.", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
            die;
        }
        echo $text;
        die;
    }
    
// удаление рам/ящиков div
    if(isset($_GET['act']) && $_GET['act']=='d_box' && is_numeric($_GET['id'])) {
        $name=mysql_result(mysql_query("SELECT name FROM `".$table_box."` AS b1, `".$table_box_type."` AS bt1 WHERE bt1.id = b1.box_type_id AND b1.id =".clean($_GET['id'])),0);
            $text='
            <div class="span10 m5">&nbsp;Удалить раму/ящик "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_box" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        echo $text;
        die;
    }
    
// удаление рам/ящиков sql
    if(isset($_POST['act']) && $_POST['act']=='d_box' && is_numeric($_POST['id']) ) {
            mysql_query("DELETE FROM `".$table_box."` WHERE `id` = ".clean($_POST['id']));
            die;
    }
    
// рамы/ящики end -------------------------------------------------------------------------------------------------------

// типы ИБП begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование типа ИБП в div
    if(isset($_GET['act']) && ($_GET['act']=='n_ups_type' || $_GET['act']=='e_ups_type') ) {
        if($_GET['act']=='e_ups_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM `" . $table_ups_type . "` WHERE id = ".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $name=$result['name'];
            $unit=$result['unit'];
            $power=$result['power'];
            $desc=$result['desc'];
        }
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Название" /></div>';
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" /></div>';
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="power" value="'.$power.'" placeholder="Мощность" /></div>';
        $text.='<div class="span4 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// ввод нового/редактирование типа ИБП в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_ups_type' || $_POST['act']=='e_ups_type') ) {
        $sql='SELECT * FROM `'.$table_ups_type.'` WHERE name="'.clean($_POST['name']).'"';
        if($_POST['act']=='n_ups_type') {
            if(@mysql_result(mysql_query($sql),0)) {
                $text="Создать невозможно, такой тип ИБП существует!!!";
            } else {
                mysql_query("INSERT INTO `".$table_ups_type."` (`name`,`unit`,`power`,`desc`,`user_id`) VALUES ('".clean($_POST['name'])."', ".($_POST['unit']?clean($_POST['unit']):'NULL').", ".($_POST['power']?clean($_POST['power']):'NULL').", ".($_POST['desc']?"'".clean($_POST['desc'])."'":'NULL').",".$user_id.")");
                die;
            }
        } elseif($_POST['act']=='e_ups_type') {
            if(@mysql_result(mysql_query($sql.' AND desc = '.($_POST['desc']?"'".clean($_POST['desc'])."'":'NULL')),0)) {
                $text="Изменить невозможно, аналогичный тип ИБП существует!!!";
            } else {
                mysql_query("UPDATE `".$table_ups_type."` SET `name`='".clean($_POST['name'])."', `unit`=".($_POST['unit']?clean($_POST['unit']):'NULL').", `power`=".($_POST['power']?clean($_POST['power']):'NULL').", `desc`=".($_POST['desc']?"'".clean($_POST['desc'])."'":'NULL').", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
                die;
            }
        }
        echo $text;
        die;
    }
    
// удаление типа ИБП div
    if(isset($_GET['act']) && $_GET['act']=='d_ups_type' && is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM `".$table_ups."` AS u1 WHERE u1.ups_type_id=".clean($_GET['id']);
        $name=mysql_result(mysql_query("SELECT name FROM `".$table_ups_type."` AS u1 WHERE u1.id =".clean($_GET['id'])),0);
        if(mysql_result(mysql_query($sql),0)) {
            $text='
            <div class="span11 m5">&nbsp;Тип ИБП "'.$name.'" используется. Удалить нельзя!!!</div>
            <div class="span1 toolbar m0">
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
            $text='
            <div class="span10 m5">&nbsp;Удалить тип ИБП "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_ups_type" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        }
        echo $text;
        die;
    }
    
// удаление типа ИБП sql
    if(isset($_POST['act']) && $_POST['act']=='d_ups_type' && is_numeric($_POST['id']) ) {
        if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_ups."` AS u1 WHERE u1.ups_type_id = ".clean($_POST['id']).""),0)) {
            mysql_query("DELETE FROM `".$table_ups_type."` WHERE `id` = ".clean($_POST['id']));
            die;
        }
        echo "not exist";
        die;
    }
// типы ИБП end -------------------------------------------------------------------------------------------------------

// ИБП begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование ИБП в div
    if(isset($_GET['act']) && ($_GET['act']=='n_ups' || $_GET['act']=='e_ups') ) {
        if($_GET['act']=='e_ups')
        {
            $id=clean($_GET['id']);
            $sql="SELECT u1 . * , ut1.name, ut1.unit, ut1.power, sn1.sn
                FROM `".$table_ups_type."` AS ut1, `".$table_ups."` AS u1
                LEFT JOIN `".$table_sn."` AS sn1 ON sn1.eq = u1.id AND eq_type='".$ups_id."'
                WHERE ut1.id = u1.ups_type_id
                AND u1.id = ".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $name=$result['name'];
            $unit=($result['unit']?$result['unit'].'U':'');
            $power=($result['power']?$result['power'].'W':'');
            $sn=$result['sn'];
            $desc=$result['desc'];
            $text='<input type="hidden" id="id" value="'.$id.'">';
            $text.='<div class="span3 m0 input-control text"><input type="text" value="'.$name.'" placeholder="Название" disabled /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" disabled /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="power" value="'.$power.'" placeholder="Мощность" disabled /></div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" value="'.$sn.'" disabled placeholder="S/N" /></div>';
        } elseif($_GET['act']=='n_ups') {
            // тип ИБП
            $sql="SELECT * FROM `".$table_ups_type."` ORDER BY name";
            $result = mysql_query($sql);
            $select_ups_type_js="var arr = [],arr2 = [];";
            if(mysql_num_rows($result)){
                //$select_ups_type='<select id="ups_type_id" onchange="$(\'input#unit\').val(arr[$(\'select#ups_type_id\').val()]);">';
                $select_ups_type='<select id="ups_type_id" onchange="$(\'input#unit\').val(arr[$(\'select#ups_type_id\').val()]);$(\'input#power\').val(arr2[$(\'select#ups_type_id\').val()]);">';
                $select_ups_type.='<option value="0">---</option>';
                while($row=mysql_fetch_assoc($result)){
                    $select_ups_type.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
                    $select_ups_type_js.="arr[".$row['id']."] = '".($row['unit']?$row['unit'].'U':'')."';arr2[".$row['id']."] = '".($row['power']?$row['power'].'W':'')."';";
                }
                $select_ups_type.='</select>';
                $select_ups_type='<script type="text/javascript">'.$select_ups_type_js.'</script>'.$select_ups_type;
            }
            $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
            $text.='<div class="span3 m0 input-control text">'.$select_ups_type.'</div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" disabled /></div>';
            $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="power" value="'.$power.'" placeholder="Мощность" disabled /></div>';
            $text.='<div class="span2 m0 input-control text"><input type="text" id="sn" value="" placeholder="S/N" /></div>';
        }
        $text.='<div class="span3 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование ИБП в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && ($_POST['act']=='n_ups' || $_POST['act']=='e_ups') ) {
        if($_POST['act']=='n_ups') {
            mysql_query("INSERT INTO `".$table_ups."` (`node_id`,`ups_type_id`,`desc`,`user_id`) VALUES (".clean($_POST['node_id']).", ".clean($_POST['ups_type_id']).", ".($_POST['desc']?"'".clean($_POST['desc'])."'":'NULL').", ".$user_id.")");
            if($_POST['sn']) {
                $eq=mysql_insert_id();
                mysql_query("INSERT INTO `".$table_sn."` (`sn`,`eq`,`eq_type`,`desc`,`user_id`) VALUES ('".clean($_POST['sn'])."', ".$eq.", '".$switch_id."', NULL,".$user_id.")");
                die;                
            }
        } else if($_POST['act']=='e_ups') {
            mysql_query("UPDATE `".$table_ups."` SET `desc`=".($_POST['desc']?"'".clean($_POST['desc'])."'":'NULL').", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
            die;
        }
        echo $text;
        die;
    }
    
// удаление ИБП div
    if(isset($_GET['act']) && $_GET['act']=='d_ups' && is_numeric($_GET['id'])) {
        $name=mysql_result(mysql_query("SELECT name FROM `".$table_ups."` AS u1, `".$table_ups_type."` AS ut1 WHERE ut1.id = u1.ups_type_id AND u1.id =".clean($_GET['id'])),0);
            $text='
            <div class="span10 m5">&nbsp;Удалить ИБП "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_ups" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        echo $text;
        die;
    }
    
// удаление ИБП sql
    if(isset($_POST['act']) && $_POST['act']=='d_ups' && is_numeric($_POST['id']) ) {
            mysql_query("DELETE FROM `".$table_ups."` WHERE `id` = ".clean($_POST['id']));
            mysql_query("DELETE FROM `".$table_sn."` WHERE `eq` = ".clean($_POST['id']));
            die;
    }
// ИБП end -------------------------------------------------------------------------------------------------------

// типы прочего оборудования begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование типа прочего оборудования в div
    if(isset($_GET['act']) && ($_GET['act']=='n_other_type' || $_GET['act']=='e_other_type') ) {
        if($_GET['act']=='e_other_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM `" . $table_other_type . "` WHERE id = ".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $name=$result['name'];
            $unit=$result['unit'];
            $desc=$result['desc'];
        }
        $text='<input type="hidden" id="id" value="'.$id.'">';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Название" /></div>';
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" /></div>';
        $text.='<div class="span4 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
// ввод нового/редактирование типа прочего оборудования в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['name']) && ($_POST['act']=='n_other_type' || $_POST['act']=='e_other_type') ) {
        $sql='SELECT * FROM `'.$table_other_type.'` WHERE name="'.clean($_POST['name']).'"';
        if($_POST['act']=='n_other_type') {
            if(@mysql_result(mysql_query($sql),0)) {
                $text="Создать невозможно, такой тип прочего оборудования существует!!!";
            } else {
                mysql_query("INSERT INTO `".$table_other_type."` (`name`,`unit`,`desc`,`user_id`) VALUES ('".clean($_POST['name'])."', ".($_POST['unit']?clean($_POST['unit']):'NULL').", ".($_POST['desc']?"'".clean($_POST['desc'])."'":'NULL').",".$user_id.")");
                die;
            }
        } elseif($_POST['act']=='e_other_type') {
            if(@mysql_result(mysql_query($sql.' AND desc = '.($_POST['desc']?"'".clean($_POST['desc'])."'":'NULL')),0)) {
                $text="Изменить невозможно, аналогичный тип прочего оборудования существует!!!";
            } else {
                mysql_query("UPDATE `".$table_other_type."` SET `name`='".clean($_POST['name'])."', `unit`=".($_POST['unit']?clean($_POST['unit']):'NULL').", `desc`=".($_POST['desc']?"'".clean($_POST['desc'])."'":'NULL').", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
                die;
            }
        }
        echo $text;
        die;
    }
    
// удаление типа прочего оборудования div
    if(isset($_GET['act']) && $_GET['act']=='d_other_type' && is_numeric($_GET['id'])) {
        $sql="SELECT COUNT(*) FROM `".$table_other."` AS o1 WHERE o1.other_type_id=".clean($_GET['id']);
        $name=mysql_result(mysql_query("SELECT name FROM `".$table_other_type."` AS o1 WHERE o1.id =".clean($_GET['id'])),0);
        if(mysql_result(mysql_query($sql),0)) {
            $text='
            <div class="span11 m5">&nbsp;Тип прочего оборудования "'.$name.'" используется. Удалить нельзя!!!</div>
            <div class="span1 toolbar m0">
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
            $text='
            <div class="span10 m5">&nbsp;Удалить тип прочего оборудования "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_other_type" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        }
        echo $text;
        die;
    }
    
// удаление типа прочего оборудования sql
    if(isset($_POST['act']) && $_POST['act']=='d_other_type' && is_numeric($_POST['id']) ) {
        if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_other."` AS o1 WHERE o1.other_type_id = ".clean($_POST['id']).""),0)) {
            mysql_query("DELETE FROM `".$table_other_type."` WHERE `id` = ".clean($_POST['id']));
            die;
        }
        echo "not exist";
        die;
    }
// типы прочего оборудования end -------------------------------------------------------------------------------------------------------

// Прочее оборудование begin -------------------------------------------------------------------------------------------------------
// ввод нового/редактирование прочего оборудования в div
    if(isset($_GET['act']) && ($_GET['act']=='n_other' || $_GET['act']=='e_other') ) {
        if($_GET['act']=='e_other')
        {
            $id=clean($_GET['id']);
            $sql="SELECT o1 . * , ot1.name, ot1.unit
                FROM `".$table_other_type."` AS ot1, `".$table_other."` AS o1
                WHERE ot1.id = o1.other_type_id
                AND o1.id = ".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $name=$result['name'];
            $unit=($result['unit']?$result['unit'].'U':'');
            $desc=$result['desc'];
            $text='<input type="hidden" id="id" value="'.$id.'">';
            $text.='<div class="span3 m0 input-control text"><input type="text" value="'.$name.'" placeholder="Название" disabled /></div>';
        } elseif($_GET['act']=='n_other') {
            // тип прочего оборудования
            $sql="SELECT * FROM `".$table_other_type."` ORDER BY name";
            $result = mysql_query($sql);
            $select_other_type_js="var arr = [];";
            if(mysql_num_rows($result)){
                $select_other_type='<select id="other_type_id" onchange="$(\'input#unit\').val(arr[$(\'select#other_type_id\').val()]);">';
                $select_other_type.='<option value="0">---</option>';
                while($row=mysql_fetch_assoc($result)){
                    $select_other_type.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
                    $select_other_type_js.="arr[".$row['id']."] = '".($row['unit']?$row['unit'].'U':'')."';";
                }
                $select_other_type.='</select>';
                $select_other_type='<script type="text/javascript">'.$select_other_type_js.'</script>'.$select_other_type;
            }
            $text.='<input type="hidden" id="node_id" value="'.clean($_GET['node_id']).'">';
            $text.='<div class="span3 m0 input-control text">'.$select_other_type.'</div>';
        }
        $text.='<div class="span1 span1_5 m0 input-control text"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" disabled /></div>';
        $text.='<div class="span3 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
        echo $text;
        die;
    }
    
// ввод нового/редактирование прочего оборудования в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && ($_POST['act']=='n_other' || $_POST['act']=='e_other') ) {
        if($_POST['act']=='n_other') {
            mysql_query("INSERT INTO `".$table_other."` (`node_id`,`other_type_id`,`desc`,`user_id`) VALUES (".clean($_POST['node_id']).", ".clean($_POST['other_type_id']).", ".($_POST['desc']?"'".clean($_POST['desc'])."'":'NULL').", ".$user_id.")");
        } else if($_POST['act']=='e_other') {
            mysql_query("UPDATE `".$table_other."` SET `desc`=".($_POST['desc']?"'".clean($_POST['desc'])."'":'NULL').", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
            die;
        }
        echo $text;
        die;
    }
    
// удаление прочего оборудования div
    if(isset($_GET['act']) && $_GET['act']=='d_other' && is_numeric($_GET['id'])) {
        $name=mysql_result(mysql_query("SELECT name FROM `".$table_other."` AS o1, `".$table_other_type."` AS ot1 WHERE ot1.id = o1.other_type_id AND o1.id =".clean($_GET['id'])),0);
            $text='
            <div class="span10 m5">&nbsp;Удалить прочее оборудование "'.$name.'"?</div>
            <div class="span2 toolbar m0">
                <button class="icon-checkmark m0" id="d_other" rel="'.clean($_GET['id']).'" title="Ok"></button>
                <button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        echo $text;
        die;
    }
    
// удаление прочего оборудования sql
    if(isset($_POST['act']) && $_POST['act']=='d_other' && is_numeric($_POST['id']) ) {
            mysql_query("DELETE FROM `".$table_other."` WHERE `id` = ".clean($_POST['id']));
            die;
    }
// Прочее оборудование end -------------------------------------------------------------------------------------------------------

//показать все кабеля
    if(isset($_POST['act']) && $_POST['act']=='pq_all_cable' && is_numeric($_POST['node_id']) ) {
/*        if(!@mysql_result(mysql_query("SELECT * FROM `".$table_node."` AS n1, `".$table_pq."` AS p1 WHERE n1.id=".clean($_POST['id']))." AND p1.node !=".clean($_POST['id']),0)) {
            mysql_query("DELETE FROM `".$table_node."` WHERE `id` = ".clean($_POST['id']));
            die;
        }*/
       // список всех кабелей на данном узле
        /*$sql="SELECT *, p1.num AS pq_num, p1.id AS pq_id
                FROM pq AS p1, cable AS c1, node AS n1, pq_type AS pq_t
                WHERE p1.node = ".clean($_POST['node_id'])."
                AND ( c1.pq_1 = p1.id OR c1.pq_2 = p1.id )
                AND p1.node = n1.id
                AND p1.pq_type_id = pq_t.id";*/
        $sql="SELECT *, p1.num AS pq_num, p1.id AS pq_id
                FROM pq AS p1, ".$table_node." AS n1, ".$table_pq_type." AS pq_t
                WHERE p1.node = ".clean($_POST['node_id'])."
                AND p1.node = n1.id
                AND p1.pq_type_id = pq_t.id";
                echo $sql;
        $result = mysql_query($sql);
        if (mysql_num_rows($result)) {
            while ($row = mysql_fetch_assoc($result)) {
                if($row['type']==0) $type='Кросс'; else $type='Муфта';
                if(isset($row['pq_num'])) $num=' №'.$row['pq_num']; else $num='';

                $text.='<table class="node">';
                $text.='<tr><td colspan=2>'.$row['address'].' ('.$type.$num.')</td></tr>';
                /*echo '<pre>';
                print_r($row);
                echo '</pre>';*/
                $pq_id = $row['pq_id'];
                $sql = "SELECT a.id, IF( a.pq_1 =".$pq_id.", pq_1, pq_2 ) AS pq_1, IF( a.pq_1 =".$pq_id.", pq_2, pq_1 ) AS pq_2, ct.fib, ct.name AS cable_name, IF( a.pq_1 =".$pq_id.", `c1`.`address` , `c2`.`address` ) AS addr_1, IF( a.pq_1 =".$pq_id.", `b1`.`type` , `b2`.`type` ) AS type_1, IF( a.pq_1 =".$pq_id.", `b1`.`num` , `b2`.`num` ) AS num_1, IF( a.pq_1 =".$pq_id.", `c2`.`address` , `c1`.`address` ) AS addr_2, IF( a.pq_1 =".$pq_id.", `b2`.`type` , `b1`.`type` ) AS type_2, IF( a.pq_1 =".$pq_id.", `b2`.`num` , `b1`.`num` ) AS num_2
                            FROM `" . $table_cable . "` AS a, `" . $table_pq . "` AS b1, `" . $table_pq . "` AS b2, `" . $table_node . "` AS c1, `" . $table_node . "` AS c2, `" . $table_cable_type . "` AS ct
                            WHERE (
                                `a`.`pq_1` = `b1`.`id`
                                AND `b1`.`node` = `c1`.`id`
                            )
                            AND (
                                `a`.`pq_2` = `b2`.`id`
                                AND `b2`.`node` = `c2`.`id`
                            ) AND (`a`.`pq_1`=".$pq_id." OR `a`.`pq_2`=".$pq_id.") AND `a`.`cable_type` = `ct`.`id`";
                $result_cable = mysql_query($sql);
                
                if (mysql_num_rows($result_cable)) {
                    while ($row_cable = mysql_fetch_assoc($result_cable)) {
                    	$text.='<tr>';                        
                    	$text.='<td><div class="rotateText">'.$row_cable['addr_2'].'</div></td>';
                    	
                    	$sql="SELECT * FROM ".$table_fiber." WHERE `cable_id` = ".$row_cable['id']." ORDER BY num";
                    	//echo $sql;
                    	$result_fiber = mysql_query($sql);
                    	if (mysql_num_rows($result_fiber)) {
                    		$text.='<td>';
                    		while ($row_fiber = mysql_fetch_assoc($result_fiber)) {
                    			$text.='<div class="fiber">'.$row_fiber['num'].'</div>';
                    		}
                    		$text.='</td>';
                    	}
                        /*echo '<pre>';
                        print_r($row_cable);
                        echo '</pre>';*/
                    	$text.='</tr>';
                    }
                }
                $text.='</table>';
            }
        }
        echo $text;
        die;
    }

// ввод нового типа пассивного оборудования в div
    if(isset($_GET['act']) && ($_GET['act']=='n_pq_type' || $_GET['act']=='e_pq_type') ) {
        if($_GET['act']=='e_pq_type')
        {
            $id=clean($_GET['id']);
            $sql="SELECT * FROM `" . $table_pq_type . "` AS pq_type WHERE id=".$id;
            $result=mysql_fetch_assoc(mysql_query($sql));
            $name=$result['name'];
            $type=$result['type'];
            $ports_num=$result['ports_num'];
            $unit=$result['unit'];
            $desc=$result['desc'];
        }
		$type='<select id="type" ><option value="0" '.($type==0 ? "SELECTED" : "").'>Кросс</option><option value="1" '.($type==1 ? "SELECTED" : "").'>Муфта</option></select>';

		$text='<input type="hidden" id="id" value="'.$id.'">';
		$text.='<div class="span2 m0 input-control text">'.$type.'</div>';
		$text.='<div class="span4 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Наименование" /></div>';
		$text.='<div class="span2 m0 input-control text" id="ports_num_div"><input type="text" id="ports_num" value="'.$ports_num.'" placeholder="Портов" /></div>';
		$text.='<div class="span2 m0 input-control text" id="unit_div"><input type="text" id="unit" value="'.$unit.'" placeholder="Юнитов" /></div>';

		$text.='<div class="span2 toolbar m0">
			<button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
			<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		</div>';
		
		$text.=($result['type']==1 ? '<script>$("div#ports_num_div").hide(); $("div#unit_div").hide();</script>' : "");
		echo $text;
		die;
    }

// ввод нового типа пассивного оборудования в div
    if(isset($_GET['act']) && ($_GET['act']=='n_cable_type' || $_GET['act']=='e_cable_type') ) {
    	if($_GET['act']=='e_cable_type')
    	{
    		$id=clean($_GET['id']);
    		$sql="SELECT * FROM `" . $table_cable_type . "` AS pq_type WHERE id=".$id;
    		$result=mysql_fetch_assoc(mysql_query($sql));
    		$name=$result['name'];
    		$fib=$result['fib'];
    		$desc=$result['desc'];
    	}
    	$text='<input type="hidden" id="id" value="'.$id.'">';

        $text.='<div class="span4 m0 input-control text"><input type="text" id="name" value="'.$name.'" placeholder="Наименование" /></div>';
        $text.='<div class="span2 m0 input-control text"><input type="text" id="fib" value="'.$fib.'" placeholder="Волокон" /></div>';
        $text.='<div class="span4 m0 input-control text"><input type="text" id="desc" value="'.$desc.'" placeholder="Описание" /></div>';
        $text.='<div class="span2 toolbar m0">
            <button class="icon-checkmark m0" id="'.clean($_GET['act']).'" title="Ok"></button>
            <button class="icon-blocked m0" id="exit" title="Отмена"></button>
        </div>';
    
    	echo $text;
    	die;
    }

// удаление типа пассивного оборудования в div
    if(isset($_GET['act']) && $_GET['act']=='d_pq_type' && is_numeric($_GET['id']) ) {
        mysql_query("DELETE FROM `".$table_pq_type."` WHERE `id` = ".clean($_GET['id']));
        die;
    }

//удаление типа пассивного оборудования в div
    if(isset($_GET['act']) && $_GET['act']=='d_cable_type' && is_numeric($_GET['id']) ) {
    	mysql_query("DELETE FROM `".$table_cable_type."` WHERE `id` = ".clean($_GET['id']));
    	die;
    }

// ввод нового типа пассивного оборудования в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['name']) && is_numeric($_POST['type']) && ($_POST['act']=='n_pq_type' || $_POST['act']=='e_pq_type') ) {
        if(!empty($_POST['ports_num']) && $_POST['type']==0) {
            $ports_num="=".clean($_POST['ports_num']);
            $ports_num_sql=clean($_POST['ports_num']);
        } else {
            $ports_num="IS NULL";
            $ports_num_sql="NULL";
        }
        if(!empty($_POST['unit']) && $_POST['type']==0) {
            $unit="=".clean($_POST['unit']);
            $unit_sql=clean($_POST['unit']);
        } else {
            $unit="IS NULL";
            $unit_sql="NULL";
        }
        $sql='SELECT * FROM `'.$table_pq_type.'` WHERE name="'.clean($_POST['name']).'" AND ports_num '.$ports_num.' AND unit '.$unit;

        if($_POST['act']=='n_pq_type') {
            if(@mysql_result(mysql_query($sql),0)) {
                $text="Создать невозможно, такой тип существует!!!";
            } else {
                mysql_query("INSERT INTO `".$table_pq_type."` (`name`,`type`,`ports_num`,`unit`,`user_id`) VALUES ('".clean($_POST['name'])."', ".clean($_POST['type']).", ".$ports_num_sql.", ".$unit_sql.",".$user_id.")");
            }
        } elseif($_POST['act']=='e_pq_type') {
            if(@mysql_result(mysql_query($sql),0)) {
                $text="Изменить невозможно, аналогичный тип существует!!!";
            } else {
                mysql_query("UPDATE `".$table_pq_type."` SET `name`='".clean($_POST['name'])."', type=".clean($_POST['type']).", ports_num=".$ports_num_sql.", unit=".$unit_sql.", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
            }
        }
        echo $text;
        die;
    }

// ввод нового типа кабеля в div post
    if(isset($_POST['act']) && is_numeric($_POST['id']) && isset($_POST['name']) && is_numeric($_POST['fib']) && ($_POST['act']=='n_cable_type' || $_POST['act']=='e_cable_type') ) {
    	if(!empty($_POST['desc'])) {
    		$desc='="'.clean($_POST['desc']).'"';
    		$desc_sql='"'.clean($_POST['desc']).'"';
    	} else {
    		$desc="IS NULL";
    		$desc_sql="NULL";
    	}
    	$sql='SELECT * FROM `'.$table_cable_type.'` WHERE name="'.clean($_POST['name']).'" AND fib='.clean($_POST['fib']).' AND desc '.$desc;
    	if($_POST['act']=='n_cable_type') {
    		if(@mysql_result(mysql_query($sql),0)) {
    			$text="Создать невозможно, такой тип существует!!!";
    		} else {
    			mysql_query("INSERT INTO `".$table_cable_type."` (`name`,`fib`,`desc`,`user_id`) VALUES ('".clean($_POST['name'])."', ".clean($_POST['fib']).", ".$desc_sql.",".$user_id.")");
    		}
    	} elseif($_POST['act']=='e_cable_type') {
    		if(@mysql_result(mysql_query($sql),0)) {
    			$text="Изменить невозможно, аналогичный тип существует!!!";
    		} else {
    			mysql_query("UPDATE `".$table_cable_type."` SET `name`='".clean($_POST['name'])."', `fib`=".clean($_POST['fib']).", `desc`=".$desc_sql.", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
    			//echo "UPDATE `".$table_cable_type."` SET `name`='".clean($_POST['name'])."', fib=".clean($_POST['fib']).", desc=".$desc_sql." WHERE `id`=".clean($_POST['id']).";";
    			//die;
    		}
    	}
    	echo $text;
    	die;
    }

// ввод нового пассивного оборудования Кроссы/Муфты div
    if(isset($_GET['act']) && ($_GET['act']=='n_pq' || $_GET['act']=='e_pq') ) {
// выбор узла для размещения кросса
    	if($_GET['act']=='e_pq')
    	{
    		$pq_id=clean($_GET['pq_id']);
    		$result=mysql_fetch_assoc(mysql_query("SELECT `pq`.`id` AS pq_id, `pq`.`node`, `pq`.`num`, `pq`.`desc` AS pq_desc, `pq_t`.* FROM `".$table_pq."` AS pq LEFT JOIN `".$table_pq_type."` AS pq_t ON `pq`.`pq_type_id` = `pq_t`.`id` WHERE `pq`.`id`='".$pq_id."' LIMIT 1;"));
    		/*echo '1<pre>';
    		print_r($result);
    		echo '</pre>';*/
    		$node=$result['node'];
    		$type=$result['type'];
    		$type_id=$result['id'];
    		$num=$result['num'];
    		$pq_desc=$result['pq_desc'];
    		$sql="SELECT n1.*, p1.type, p1.num, p1.desc AS pq_desc, pq_t.id AS pq_type_id, pq_t.name AS pq_type_name, pq_t.ports_num AS pq_ports FROM `node` AS n1 JOIN `pq` AS p1 ON p1.node = n1.id AND p1.id = ".clean($_GET['pq_id'])." LEFT JOIN `pq_type` AS pq_t ON p1.pq_type_id = pq_t.id WHERE p1.type != 1 OR p1.type IS NULL OR n1.id =".$node." ORDER BY n1.`address`";
    	} else {
    		$node=clean($_GET['node_id']);
    		//$sql="SELECT n1.*, p1.type, p1.num, p1.desc AS pq_desc, pq_t.id AS pq_type_id, pq_t.name AS pq_type_name, pq_t.ports_num AS pq_ports FROM `node` AS n1 LEFT JOIN `pq` AS p1 ON p1.node = n1.id LEFT JOIN `pq_type` AS pq_t ON p1.pq_type_id = pq_t.id WHERE p1.type != 1 OR p1.type IS NULL ORDER BY n1.`address`";
    	}
    	$result=mysql_fetch_assoc(mysql_query("SELECT * FROM `".$table_node."` AS n1 WHERE `n1`.`id`=".$node.";"));

// выбор узла для размещения кросса end
    	$text='<input type="hidden" id="act" value="'.clean($_GET['act']).'" />';
		$text.='<input type="hidden" id="id" value="'.$pq_id.'" />';
		$text.='<input type="hidden" id="node" value="'.$node.'" />';
		//$text.='<input type="hidden" id="type_id" value="'.$type_id.'" />';
    	//$text.='<div class="span3 m0 input-control text">'.$select_node.'</div>';
		$text.='<div class="span3 m5 input-control text">'.$result['address'].'</div>';
		$text.='<div class="span2 m0 input-control text"><select id="type"></select></div>';
		$text.='<div class="span2 m0 input-control text"><select id="pq_type"></select></div>';
		$text.='<div class="span1 m0 input-control text"><input class="" type="text" id="num" value="'.$num.'" placeholder="№" /></div>';
		$text.='<div class="span3 m0 input-control text"><input type="text" id="pq_desc" value="'.$pq_desc.'" placeholder="Описание" /></div>';
		$text.='<div class="span2 toolbar m0">
					<button class="icon-checkmark m0" id="new_pq" title="Ok"></button>
					<button class="icon-blocked m0" id="exit" title="Отмена"></button>
				</div>';
		$text.='<script type="text/javascript">s_pq_type_ports("'.$node.'","'.$type.'","'.$type_id.'");</script>';
		//$text.='s_pq_type_ports("'.$node.'","'.$type.'","'.$type_id.'");';
		$text.='<script type="text/javascript">s_pq_type_sel("'.$type.'","'.$type_id.'");</script>';
    	echo $text;
    	die;
    }

// вывод селекта типов пассивного оборудования после выбора
	if(isset($_POST['act']) && $_POST['act']=='s_pq_type_sel' && is_numeric($_POST['type']) ) {
    	$sql="SELECT * FROM `".$table_pq_type."` WHERE `type`=".clean($_POST['type']." ORDER BY name");
		$result=mysql_query($sql);
    	if(mysql_num_rows($result)){
    		$select_pq_type='<select id="pq_type">';
    		$select_pq_type.='<option value="0">---</option>';
    		while($row=mysql_fetch_assoc($result)){
    			$select_pq_type.='<option value="'.$row['id'].'"';
    			if($_POST['type_id']==$row['id']) {
    				$select_pq_type.=" SELECTED";
    			}
    			$select_pq_type.='>'.$row['name'].($row['type']==0 ? ' ('.$row['ports_num'].')' : '').'</option>';
    		}
    		$select_pq_type.='</select>';
    	}
    	echo $select_pq_type;
		die;
	}

// вывод списка волокон div
    if(isset($_GET['act']) && $_GET['act']=='s_fiber') {
    	$i=1;
    	if(isset($_GET['cable_id']) && is_numeric($_GET['cable_id'])) {
    		$cable_id='AND `aa`.`cable_id`='.clean($_GET['cable_id']);
    		// сравнение количества волокон с количеством в кабеле
    		$result=mysql_fetch_assoc(mysql_query("SELECT * FROM `".$table_cable."` WHERE `id`='".clean($_GET['cable_id'])."' LIMIT 1;"));
    		$fib_busy=mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_fiber."` WHERE `cable_id`='".clean($_GET['cable_id'])."';"),0);
    		// если количество волокон меньше чем в количество в кабеле, то выводим ссылку на добавление волокон
    		//$text='<div><a href="?act=n_fiber&cable_id='.clean($_GET['cable_id']).'">Добавить волокно</a></div>';
    		/*			if($result['type']==0) $type='Кросс'; else $type='Муфта';
    			if(isset($result['num'])) $num=' №'.$result['num']; else $num='';*/
    
    		$link='<div class="title">'.$link_node.' > ';
    		$link.='<a href="?act=s_cable&pq_id='.$result['pq_1'];
    			
    		$link2='</div>';
    		$link2.='<div id="action">';
    		if($_SESSION['fiber_add'])
    			if($result['fib']>$fib_busy)
    			$link2.='<input class="mini" id="fiber_add_div" rel="?act=n_fiber&cable_id='.clean($_GET['cable_id']).'" type="button" value="Добавить волокно" />';
    		$link2.='</div>';
    		$text.='<br>';
    	} else {
    		$text.='<div id="action">';
    		if($_SESSION['fiber_add'])
    			$text.='<input class="mini" id="fiber_add_div" rel="?act=n_fiber&cable_id='.clean($_GET['cable_id']).'" type="button" value="Добавить волокно" />';
    		$text.='</div>';
    		$text.='<br>';
    	}
    
    	$result=mysql_query("SELECT `aa`.*, `a`.`pq_1`, `a`.`pq_2`, `a`.`fib`, `c1`.`address` as addr_1, `b1`.`type` as type_1, `b1`.`num` as num_1, `c2`.`address` as addr_2, `b2`.`type` as type_2, `b2`.`num` as num_2
    			FROM `".$table_fiber."` AS aa, `".$table_cable."` AS a, `".$table_pq."` AS b1, `".$table_pq."` AS b2, `".$table_node."` AS c1, `".$table_node."` AS c2
    			WHERE (
    			`a`.`pq_1` = `b1`.`id`
    			AND `b1`.`node` = `c1`.`id`
    	)
    			AND (
    			`a`.`pq_2` = `b2`.`id`
    			AND `b2`.`node` = `c2`.`id`
    	) ".$cable_id." AND `aa`.`cable_id` = `a`.`id` ORDER BY `addr_1`, `type_1`, `addr_2`, `type_2`, `aa`.`num`");
    	if(mysql_num_rows($result)){
    		while($row=mysql_fetch_assoc($result)){
    			if($row['type_1']==0) $type_1='Кросс'; else $type_1='Муфта';
    			if(isset($row['num_1'])) $num_1=' №'.$row['num_1']; else $num_1='';
    
    			if($row['type_2']==0) $type_2='Кросс'; else $type_2='Муфта';
    			if(isset($row['num_2'])) $num_2=' №'.$row['num_2']; else $num_2='';
    
    			$pq_addr_1=$row['addr_1'].' (' .$type_1.$num_1. ')';
    			$pq_addr_2=$row['addr_2'].' (' .$type_2.$num_2. ')';
    
    			$text.='
    			<div style="show_fiber">
    			<div class="left_fiber">
    			'.$i.'. '.$pq_addr_1.' - '.$pq_addr_2.' [ОВ №'.$row['num'].']
    			</div>';
    			if($_SESSION['fiber_del']) $text.='<div><input type="button" id="d_fiber_'.$row['id'].'" value="Удалить" /></div>';
    			$text.='</div>';
    			$text.='<div class="clear"></div>';
    			$i++;
    		}
    	}
    	//show_menu();
    	//echo $link.$text;
    	//if($link) echo $link.$pq_addr_1.' - '.$pq_addr_2.$link2;
    	//if($link) echo $link.'">'.$pq_addr_1.'</a> > Кабель: '.$pq_addr_2.$link2;
    	echo $link2;
    	$text.='<br>';
    	$text.='<div class="left_fiber">&nbsp;</div><div><input id="exit" type="button" value="Отмена" /></div>';
    	echo $text;
    	die;
    }


// удаление пассивного оборудования Узла/Муфты div
    if(isset($_GET['act']) && $_GET['act']=='d_pq' && is_numeric($_GET['pq_id'])) {
        $sql="SELECT COUNT(*) FROM `".$table_cable."` AS c1 WHERE c1.pq_1 =".clean($_GET['pq_id'])." OR c1.pq_2 =".clean($_GET['pq_id']);
        if(mysql_result(mysql_query($sql),0)) {
            $text='
            <div class="span11 m5">&nbsp;Пассивное оборудование "'.clean($_GET['addr']).'" не пустое. Перед удалением пассивного оборудования необходимо удалить кабеля!!!</div>
            <div class="span1 toolbar m0">
            	<button class="icon-blocked m0" id="exit" title="Отмена"></button>
            </div>';
        } else {
	        $text='
	        <div class="span10 m5">&nbsp;Удалить пассивное оборудование "'.clean($_GET['addr']).'"?</div>
	        <div class="span2 toolbar m0">
		        <button class="icon-checkmark m0" id="d_pq" rel="'.clean($_GET['pq_id']).'" title="Ok"></button>
		        <button class="icon-blocked m0" id="exit" title="Отмена"></button>
	        </div>';
        }
        echo $text;
        die;
    }

// удаление пассивного оборудования Узла/Муфты
    if(isset($_POST['act']) && $_POST['act']=='d_pq' && is_numeric($_POST['id']) ) {
        // запрос кривоватый.... но работает
        if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_cable."` AS c1 LEFT JOIN `".$table_pq."` AS p1 ON c1.pq_1 = p1.id OR c1.pq_2 = p1.id WHERE c1.pq_1 = ".clean($_POST['id'])." OR c1.pq_2 = ".clean($_POST['id'])),0)) {
            mysql_query("DELETE FROM `".$table_pq."` WHERE `id` = ".clean($_POST['id']));
            die;
        }
        echo "not exist";
        die;
    }

// ввод нового узла
	if(isset($_POST['act']) && $_POST['act']=='n_node_sql' && isset($_POST['address'])) {
		if(! @mysql_result(mysql_query("SELECT id FROM `".$table_node."` WHERE `address`='".clean($_POST['address'])."';"),0)) {
			if(empty($_POST['desc']))
				$desc="NULL";
			else
				$desc="'".clean($_POST['desc'])."'";
			mysql_query("INSERT INTO `".$table_node."` (`address`,`incorrect`,`desc`,`user_id`) VALUES ('".clean($_POST['address'])."', ".(isset($_POST['address_incorrect'])?'1':'NULL').", ".$desc.",".$user_id.")");
			die;
		}
		echo "exist";
		die;
	}

// редактирование существующего узла
	if(isset($_POST['act']) && $_POST['act']=='e_node_sql' && is_numeric($_POST['id']) && isset($_POST['address'])) {
		if($_POST['desc']=='') $desc='NULL'; else $desc="'".clean($_POST['desc'])."'"; 
		mysql_query("UPDATE `".$table_node."` SET `address`='".clean($_POST['address'])."', `incorrect`=".(isset($_POST['address_incorrect'])?'1':'NULL').",`desc`=".$desc.", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
		die;
	}

// после выбора узла выводит список добавления возможного пассивного оборудования 
	if(isset($_POST['act']) && $_POST['act']=='s_pq_type' && is_numeric($_POST['node_id']) && isset($_POST['type'])) {
		$select_type.='<select id="type">';
		if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_pq."` AS p1, `".$table_pq_type."` AS pt WHERE `p1`.`pq_type_id` = `pt`.`id` AND `pt`.`type` != 0 AND `p1`.`node` =".clean($_POST['node_id'])),0)) {
			$select_type.='<option value="0"';
			if(clean($_POST['node_id'])==0) $select_type.=" SELECTED";
			$select_type.='>кросс</option>';
		}
		/*$select_type='<select id="type"><option value="0"';
		if(clean($_POST['node_id'])==0) $select_type.=" SELECTED";
		$select_type.='>кросс</option>';*/
		//if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_pq."` WHERE type != 1 AND node =".clean($_POST['node_id'])),0)) {
		if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_pq."` AS p1, `".$table_pq_type."` AS pt WHERE `p1`.`pq_type_id` = `pt`.`id` AND `pt`.`type` != 1 AND `p1`.`node` =".clean($_POST['node_id'])),0)) {
			$select_type.='<option value="1"';
			if(clean($_POST['node_id'])==1) $select_type.=" SELECTED";
			$select_type.='>муфта</option>';
		}
		$select_type.='</select>';
		echo "var select_type='".$select_type."'; show=true;";
		die;
	}

// вывод свободных порта в кроссе
	if(isset($_POST['act']) && $_POST['act']=='s_port_free' && is_numeric($_POST['pq_id']) && is_numeric($_POST['pq_type_id'])) {
		$select_port='<select id="ports">';
		// если id не равен нулю, заполняем массив
		if($_POST['pq_type_id']!=0) {
			// общее количество волокон в кабеле
			//echo "SELECT ports FROM `".$table_pq."` WHERE `id`=".clean($_POST['pq_id']).";";
			//$num=mysql_result(mysql_query("SELECT ports FROM `".$table_pq."` WHERE `id`=".clean($_POST['pq_id']).";"),0);
			$num=mysql_result(mysql_query("SELECT ports_num FROM `".$table_pq_type."` WHERE `id`=".clean($_POST['pq_type_id']).";"),0);
			$i=0;
			$port[]='';
			$result=mysql_query("SELECT * FROM `".$table_cruz_conn."` WHERE `pq_id`=".clean($_POST['pq_id'])." ORDER BY port;");
			if(mysql_num_rows($result)){
				while($row=mysql_fetch_assoc($result)){
					$port[$i]=$row['port'];
					$i++;
				}
			}
			$i=1;
			while($i<=$num) {
				// если в базе нету такого порта, то заносим его в выбор портов
				if(!is_numeric(array_search($i, $port))) {
					$select_port.='<option value="'.$i.'"';

					$select_port.='>'.$i.'</options>';
				}
				$i++;
			}
		}
		$select_port.='</select>';
		echo $select_port;
		die;
	}

// вывод номера порта в кроссе присоедененного к волокну
	if(isset($_POST['act']) && $_POST['act']=='s_fiber_ports' && is_numeric($_POST['pq_id']) && is_numeric($_POST['fiber_id'])) {
		$text='<select class="ports" id="ports_'.clean($_POST['fiber_id']).'">';

		$result=mysql_query("SELECT * FROM `".$table_cruz_conn."` WHERE pq_id = ".clean($_POST['pq_id'])." AND ( fiber_id IS NULL OR fiber_id = ".clean($_POST['fiber_id'])." ) ORDER BY port;");
		// clean($_POST['id'])
		$text.='<option value="0">---</option>';
		if(mysql_num_rows($result)){
			$curr_port_id=0;
			while($row=mysql_fetch_assoc($result)){
				$text.='<option value="'.$row['id'].'"';
				if(clean($_POST['fiber_id'])==$row['fiber_id']) {
					$text.=" SELECTED";
					$curr_port_id=$row['id'];
				}
				$text.='>'.$row['port'].'</option>';
			}
		}
		$text.='</select>';
		//echo $text;
		echo "var select_ports='".$text."'; curr_port_".clean($_POST['fiber_id'])."=".$curr_port_id.";";
		die;
	}

// изменение номера порта в кроссе присоедененного к волокну
	if(isset($_POST['act']) && $_POST['act']=='fiber_port_conn' && is_numeric($_POST['pq_id']) && is_numeric($_POST['port_id']) && is_numeric($_POST['fiber_id']) && is_numeric($_POST['curr_port_id'])) {
		//echo "pq_id: ".clean($_POST['pq_id'])." port_id: ".clean($_POST['pq_id'])." fiber_id: ".clean($_POST['fiber_id']);
		//die;
		if($_POST['port_id']==0) {			
			mysql_query("UPDATE `".$table_cruz_conn."` SET `fiber_id`=NULL, user_id=".$user_id." WHERE `pq_id`=".clean($_POST['pq_id'])." AND `id`=".clean($_POST['curr_port_id']).";");
			//echo "UPDATE `".$table_cruz_conn."` SET `fiber_id`=NULL WHERE `pq_id`=".clean($_POST['pq_id'])." AND `id`=".clean($_POST['curr_port_id']).";";
		} else {
			if($_POST['curr_port_id']!=0)
				mysql_query("UPDATE `".$table_cruz_conn."` SET `fiber_id`=NULL, user_id=".$user_id." WHERE `pq_id`=".clean($_POST['pq_id'])." AND `id`=".clean($_POST['curr_port_id']).";");
			//if(clean($_POST['curr_port_id'])!=0) echo "UPDATE `".$table_cruz_conn."` SET `fiber_id`=NULL WHERE `pq_id`=".clean($_POST['pq_id'])." AND `port`=".clean($_POST['curr_port_id']).";";
			mysql_query("UPDATE `".$table_cruz_conn."` SET `fiber_id`=".clean($_POST['fiber_id']).", user_id=".$user_id." WHERE `pq_id`=".clean($_POST['pq_id'])." AND `id`=".clean($_POST['port_id']).";");
			//echo "UPDATE `".$table_cruz_conn."` SET `fiber_id`=".clean($_POST['fiber_id'])." WHERE `pq_id`=".clean($_POST['pq_id'])." AND `id`=".clean($_POST['port_id']).";";
		}
//		if(!@mysql_result(mysql_query("SELECT * FROM `".$table_cruz_conn."` WHERE pq_id = ".clean($_POST['pq_id'])." AND port = ".clean($_POST['port_id'])." AND fiber_id = ".clean($_POST['fiber_id'])),0)) {
//			echo "нету";
		//mysql_query("UPDATE `".$table_cruz_conn."` SET `fiber_id`=".clean($_POST['fiber_id'])." WHERE `pq_id`=".clean($_POST['pq_id'])." AND `port_id`=".clean($_POST['port_id']).";");
		//echo "UPDATE `".$table_cruz_conn."` SET `fiber_id`=".$fiber_id." WHERE `pq_id`=".clean($_POST['pq_id'])." AND `port`=".clean($_POST['curr_port_id']).";";
		//mysql_query("UPDATE `".$table_cruz_conn."` SET `fiber_id`=".$fiber_id." WHERE `pq_id`=".clean($_POST['pq_id'])." AND `port`=".clean($_POST['curr_port_id']).";");
			//mysql_query("INSERT INTO `".$table_cruz_conn."` (`node`,`type`,`num`,`ports`) VALUES ('".clean($_POST['node'])."', ".clean($_POST['type']).", ".$numm.", ".$portss.")");
//		}		
		die;

		$sql="SELECT * FROM `".$table_cruz_conn."` WHERE pq_id = ".clean($_POST['pq_id'])." AND ( fiber_id IS NULL OR fiber_id = ".clean($_POST['fiber_id'])." );";
		
		$result=mysql_query($sql);
		$text.='<option value="0">---</option>';
		if(mysql_num_rows($result)){
			while($row=mysql_fetch_assoc($result)){
				$text.='<option value="'.$row['id'].'"';
				if(clean($_POST['fiber_id'])==$row['fiber_id']) $text.=" SELECTED";
				$text.='>'.$row['port'].'</option>';
			}
		}
		$text.='</select>';
		echo $text;
		die;
	}

// ввод нового пассивного оборудования
	if(isset($_POST['act']) && $_POST['act']=='n_pq_sql' && isset($_POST['node'])) {
		if(empty($_POST['type'])) $_POST['type']=0;
		if(empty($_POST['num'])) {
			$num="`num` IS NULL";	// для SELECT
			$numm='NULL';			// для INSERT
		} else {
			$num='`num`='.clean($_POST['num']);
			$numm=clean($_POST['num']);
		}
		if(empty($_POST['pq_desc'])) {
			$pq_desc="NULL";
		} else {
			$pq_desc="'".$_POST['pq_desc']."'";
		}
		//print_r($_POST);
		//if(! @mysql_result(mysql_query("SELECT id FROM `".$table_pq."` WHERE `node`='".clean($_POST['node'])."' AND `type`=".$_POST['type']." AND ".$num." AND pq_type_id=".clean($_POST['pq_type']).";"),0)) {
		if(! @mysql_result(mysql_query("SELECT id FROM `".$table_pq."` WHERE `node`='".clean($_POST['node'])."' AND ".$num." AND pq_type_id=".clean($_POST['pq_type']).";"),0)) {
			//mysql_query("INSERT INTO `".$table_pq."` (`node`,`type`,`num`,`pq_type_id`,`desc`) VALUES ('".clean($_POST['node'])."', ".clean($_POST['type']).", ".$numm.", ".clean($_POST['pq_type']).", ".$pq_desc.")");
			mysql_query("INSERT INTO `".$table_pq."` (`node`,`num`,`pq_type_id`,`desc`,`user_id`) VALUES ('".clean($_POST['node'])."', ".$numm.", ".clean($_POST['pq_type']).", ".$pq_desc.",".$user_id.")");
			$sql="SELECT p1.id AS id, pq_t.ports_num AS pq_type_ports
					FROM `".$table_pq."` as p1, `".$table_pq_type."` AS pq_t
					WHERE p1.pq_type_id = pq_t.id
					AND `p1`.`node` = ".clean($_POST['node'])."
					AND `p1`.".$num."
					AND `p1`.`pq_type_id` = ".clean($_POST['pq_type']).";";
			//echo $sql;
			$result = mysql_fetch_assoc(mysql_query($sql));
			$pq_id=$total_num=$result['id'];
			$total_num=$result['pq_type_ports'];
			while($total_num) {
				if(! @mysql_result(mysql_query("SELECT id FROM `".$table_cruz_conn."` WHERE `pq_id`=".$pq_id." AND `port`=".$total_num.";"),0))
					mysql_query("INSERT INTO `".$table_cruz_conn."` (`pq_id`,`port`,`user_id`) VALUES (".$pq_id.", ".$total_num.",".$user_id.")");
					echo "INSERT INTO `".$table_cruz_conn."` (`pq_id`,`port`) VALUES (".$pq_id.", ".$total_num.")\n";
				$total_num--;
			}
			die;
		}
		echo "exist";
		die;
	}

	if(isset($_GET['act']) && $_GET['act']=='n_port' && isset($_GET['all']) && is_numeric($_GET['pq_id']) ) {
		$i=1;
		$sql="SELECT *,`pq`.`id` AS id, pq_t.id AS pq_type_id FROM `".$table_node."` AS node, `".$table_pq."` AS pq LEFT JOIN `pq_type` AS pq_t ON pq.pq_type_id = pq_t.id WHERE `pq`.`node`=`node`.`id` AND pq.id = ".clean($_GET['pq_id'])." ORDER BY `pq`.`node`";
		$result=mysql_fetch_assoc(mysql_query($sql));
		$total_num=$result['ports_num'];
		while($total_num) {
			if(! @mysql_result(mysql_query("SELECT id FROM `".$table_cruz_conn."` WHERE `pq_id`=".clean($_GET['pq_id'])." AND `port`=".$total_num.";"),0)) {
				mysql_query("INSERT INTO `".$table_cruz_conn."` (`pq_id`,`port`,`user_id`) VALUES (".clean($_GET['pq_id']).", ".$total_num.",".$user_id.")");
			}
			$total_num--;
		}
		die;
	}

// редактирование описания порта
	if(isset($_POST['act']) && $_POST['act']=='port_desc_edit' && is_numeric($_POST['port_id'])) {
		if($_POST['port_desc']=='') $desc='NULL'; else $desc="'".clean($_POST['port_desc'])."'";
		mysql_query("UPDATE `".$table_cruz_conn."` SET `desc`=".$desc.", user_id=".$user_id." WHERE `id`=".clean($_POST['port_id']).";");
		die;
	}

// чекбокс занятости порта
	if(isset($_POST['act']) && $_POST['act']=='port_used_edit' && is_numeric($_POST['port_id'])) {
		mysql_query("UPDATE `".$table_cruz_conn."` SET `used`=".($_POST['port_used']==0?'NULL':($_POST['port_used']==1?'1':'')).", user_id=".$user_id." WHERE `id`=".clean($_POST['port_id']).";");
		echo (mysql_result(mysql_query("SELECT used FROM `".$table_cruz_conn."` WHERE `id`=".clean($_POST['port_id'])),0)==1?'1':'0');
		die;
	}
// редактирование существующего пассивного оборудования
	//if(isset($_POST['act']) && $_POST['act']=='e_pq_sql' && isset($_POST['id']) && isset($_POST['node']) && isset($_POST['type']) && isset($_POST['num']) && isset($_POST['pq_type'])) {
	if(isset($_POST['act']) && $_POST['act']=='e_pq_sql' && isset($_POST['id']) && isset($_POST['node']) && isset($_POST['pq_type'])) {
	//if(isset($_POST['act']) && $_POST['act']=='e_pq_sql' && isset($_POST['id']) && isset($_POST['node']) ) {
		//if(empty($_POST['type'])) $_POST['type']=0;
		if(empty($_POST['num'])) $_POST['num']='NULL';
		if(empty($_POST['pq_desc'])) $pq_desc='NULL'; else $pq_desc="'".clean($_POST['pq_desc'])."'";
		$sql="UPDATE `".$table_pq."` SET `node`=".clean($_POST['node']).", `num` = ".clean($_POST['num']).", `pq_type_id`=".clean($_POST['pq_type']).", `desc` = ".$pq_desc.", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";";
		echo $sql;
		mysql_query($sql);
		//echo "UPDATE `".$table_pq."` SET `node`=".clean($_POST['node']).", `type`=".clean($_POST['type']).", `num`=".clean($_POST['num'])." WHERE `id`=".clean($_POST['id']).";";
		die;
	}

// ввод нового кабеля
	if(isset($_POST['act']) && $_POST['act']=='n_cable_sql' && isset($_POST['pq_1']) && isset($_POST['pq_2']) && isset($_POST['cable_type'])) {
		//echo "SELECT id FROM `".$table_pq."` WHERE `node`='".clean($_POST['node'])."' AND `type`=".$_POST['type']." AND ".$num.";";
		if(empty($_POST['desc'])) {
			$desc='NULL';
		} else { 
			$desc="'".clean($_POST['desc'])."'";
		}
		//echo $desc;
		if(! @mysql_result(mysql_query("SELECT id FROM `".$table_cable."` WHERE `pq_1`='".clean($_POST['pq_1'])."' AND `pq_2`=".$_POST['pq_2']." AND `cable_type`=".$_POST['cable_type'].";"),0)) {
			mysql_query("INSERT INTO `".$table_cable."` (`pq_1`,`pq_2`,`cable_type`,`desc`,`user_id`) VALUES ('".clean($_POST['pq_1'])."', ".clean($_POST['pq_2']).", ".clean($_POST['cable_type']).",".$desc.",".$user_id.")");
			$result = mysql_fetch_assoc(mysql_query("SELECT c1.id AS id, ct.fib AS fib FROM `".$table_cable."` AS c1, `".$table_cable_type."` AS ct WHERE `c1`.`pq_1`='".clean($_POST['pq_1'])."' AND `c1`.`pq_2`=".$_POST['pq_2']." AND `c1`.`cable_type`=".$_POST['cable_type']." AND ct.id = c1.cable_type;"));
			$cable_id=$result['id'];
			$total_num=$result['fib'];
			while($total_num) {
				if(! @mysql_result(mysql_query("SELECT id FROM `".$table_fiber."` WHERE `cable_id`=".$cable_id." AND `num`=".$total_num.";"),0))
					mysql_query("INSERT INTO `".$table_fiber."` (`cable_id`,`num`,`user_id`) VALUES (".$cable_id.", ".$total_num.",".$user_id.")");
				$total_num--;
			}
			die;
		}
		echo "exist";
		die;
	}

// редактирование существующего кабеля
	if(isset($_POST['act']) && $_POST['act']=='e_cable_sql' && isset($_POST['pq_1']) && isset($_POST['pq_2']) && isset($_POST['cable_type'])) {
		//echo "UPDATE `".$table_cable."` SET `pq_1`=".clean($_POST['pq_1']).", `pq_2`=".clean($_POST['pq_2']).", `fib`=".clean($_POST['fib'])." WHERE `id`=".clean($_POST['id']).";";
		mysql_query("UPDATE `".$table_cable."` SET `pq_1`=".clean($_POST['pq_1']).", `pq_2`=".clean($_POST['pq_2']).", `cable_type`=".clean($_POST['cable_type']).", user_id=".$user_id." WHERE `id`=".clean($_POST['id']).";");
		//echo "UPDATE `".$table_cable."` SET `pq_1`=".clean($_POST['pq_1']).", `pq_2`=".clean($_POST['pq_2']).", `fib`=".clean($_POST['fib'])." WHERE `id`=".clean($_POST['id']).";";
		die;
	}

// вывод свободных волокон в кабеле
	if(isset($_POST['act']) && $_POST['act']=='s_fiber_free' && is_numeric($_POST['id'])) {
		$select_fiber='<select id="fiber">';
// если id не равен нулю, заполняем массив
		if($_POST['id']!=0) {
// общее количество волокон в кабеле
			$num=mysql_result(mysql_query("SELECT `ct`.`fib` FROM `".$table_cable."` AS c1, `".$table_cable_type."` AS ct WHERE `c1`.`id`=".clean($_POST['id'])." AND `ct`.`id` = `c1`.`cable_type`;"),0);
			$i=0;
			$fiber[]='';
			$result=mysql_query("SELECT * FROM `".$table_fiber."` WHERE `cable_id`=".clean($_POST['id']).";");
			if(mysql_num_rows($result)){
				while($row=mysql_fetch_assoc($result)){
					$fiber[$i]=$row['num'];
					$i++;
				}
			}
			$i=1;
/*			$a=0;
			$select_fiber.='<option value="0">---</option>';*/
			while($i<=$num) {
// если в базе нету такого волокна, то заносим его в выбор волокон
				if(!is_numeric(array_search($i, $fiber))) {
					$select_fiber.='<option value="'.$i.'"';
/*					if($a==0) {
						$select_fiber.=" SELECTED";
						$a++;
					}*/
					$select_fiber.='>'.$i.'</options>';
				}
				$i++;
			}	
		}
		$select_fiber.='</select>';
		echo $select_fiber;
		die;
	}

// ввод нового волокна div
    //if(isset($_GET['act']) && ($_GET['act']=='n_fiber' || $_GET['act']=='e_fiber') ) {
	if(isset($_GET['act']) && ($_GET['act']=='n_fiber' || $_GET['act']=='e_fiber') && is_numeric($_GET['cable_id'])) {
    	//if(is_numeric($_GET['cable_id'])) $cable_id_sql=' AND `a`.`id`='.clean($_GET['cable_id']);
    	$sql="SELECT `a`.*, ct.fib AS fib, `c1`.`address` as addr_1, `b1`.`type` as type_1, `b1`.`num` as num_1, `c2`.`address` as addr_2, `b2`.`type` as type_2, `b2`.`num` as num_2
                                    FROM `".$table_cable."` AS a, `".$table_pq."` AS b1, `".$table_pq."` AS b2, `".$table_node."` AS c1, `".$table_node."` AS c2, `".$table_cable_type."` AS ct 
                                    WHERE (
                                    `a`.`pq_1` = `b1`.`id`
                                    AND `b1`.`node` = `c1`.`id`
                            )
                                    AND (
                                    `a`.`pq_2` = `b2`.`id`
                                    AND `b2`.`node` = `c2`.`id`
                            ) AND `a`.`id`=".clean($_GET['cable_id'])." AND `ct`.`id` = `a`.`cable_type`";
        	$result=mysql_fetch_assoc(mysql_query($sql));
        	if(isset($_GET['all'])) {
        		$total_num=$result['fib'];
        		while($total_num) {
        			if(! @mysql_result(mysql_query("SELECT id FROM `".$table_fiber."` WHERE `cable_id`=".clean($_GET['cable_id'])." AND `num`=".$total_num.";"),0)) {
        				mysql_query("INSERT INTO `".$table_fiber."` (`cable_id`,`num`,`user_id`) VALUES (".clean($_GET['cable_id']).", ".$total_num.",".$user_id.")");
        				$text.='Волокно №'.$total_num.' добавлено.<br>';
        			} else {
        				$text.='Волокно №'.$total_num.' уже существует.<br>';
        			}
        			$total_num--;
        		}
        		$text.='<input id="exit" type="button" value="ok" />';
        	} else {
	        	$result=mysql_fetch_assoc(mysql_query($sql));
	        	/*echo '<pre>';
	        	print_r($result);
	        	echo '</pre>';*/
	        	$result_2=mysql_query($sql);
	
	        	if($result['type_1']==0) $type_1='Кросс'; else $type_1='Муфта';
	        	if(isset($result['num_1'])) $num_1=' №'.$result['num_1']; else $num_1='';
	        	 
	        	if($row['type_2']==0) $type_2='Кросс'; else $type_2='Муфта';
	        	if(isset($result['num_2'])) $num_2=' №'.$result['num_2']; else $num_2='';
	        	 
	        	$pq_addr_1=$result['addr_1'].' (' .$type_1.$num_1. ')';
	        	$pq_addr_2=$result['addr_2'].' (' .$type_2.$num_2. ')';
	        	
	        	$fib_busy=mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_fiber."` WHERE `cable_id`='".$result['id']."';"),0);
	        	if($result['fib']>$fib_busy) {
	        		$select_cable='['.$result['fib'].' ОВ / '.($result['fib']-$fib_busy).'св.]';
	        	}
	
	        	$text.='
	        	<div id="new_fiber" style="new_fiber">
	        	<input type="hidden" id="act" value="'.clean($_GET['act']).'" />
	        	<input type="hidden" id="cable_id" value="'.$result['id'].'" />
	        	Кабель:&nbsp;'.$pq_addr_1.' - '.$pq_addr_2.'&nbsp;'.$select_cable.'&nbsp;Волокно:
	        	<select id="fiber"></select>&nbsp;
	        	<input type="button" id="new_fiber" value="ok" autofocus="autofocus" />
	        	<input id="exit" type="button" value="Отмена" />
	        	<script type="text/javascript">fiber_list_free('.$result['id'].');</script>
	        	</div>';
	        	$text.='<div class="clear"></div>';
        }
        //$text.='$("input#new_fiber").focus();';
        echo $text;
        die;
    }

// ввод нового волокна
	if(isset($_POST['act']) && $_POST['act']=='n_fiber_sql' && is_numeric($_POST['cable_id']) && is_numeric($_POST['num'])) {
// проверка, не пустое ли значение прислали
		if($_POST['cable_id']==0 || $_POST['num']==0) die;
		//echo "SELECT id FROM `".$table_pq."` WHERE `node`='".clean($_POST['node'])."' AND `type`=".$_POST['type']." AND ".$num.";";
		if(! @mysql_result(mysql_query("SELECT id FROM `".$table_fiber."` WHERE `cable_id`=".clean($_POST['cable_id'])." AND `num`=".clean($_POST['num']).";"),0)) {
			mysql_query("INSERT INTO `".$table_fiber."` (`cable_id`,`num`,`user_id`) VALUES (".clean($_POST['cable_id']).", ".clean($_POST['num']).",".$user_id.")");
			//echo "INSERT INTO `".$table_fiber."` (`cable_id`,`num`) VALUES (".clean($_POST['cable_id']).", ".clean($_POST['num']).")";
			die;
		}
		echo "exist";
		die;
	}

// ввод нового кабеля div
    if(isset($_GET['act']) && ($_GET['act']=='n_cable' || $_GET['act']=='e_cable') && isset($_GET['pq_id']) ) {
    	if($_GET['act']=='e_cable')
    	{
    		$cable_id=clean($_GET['cable_id']);
    		//$sql="SELECT `id`, IF(pq_2=".clean($_GET['pq_id']).",pq_2,IF(pq_1=".clean($_GET['pq_id']).",pq_1,NULL)) AS pq_1, IF(pq_1=".clean($_GET['pq_id']).",pq_2,IF(pq_2=".clean($_GET['pq_id']).",pq_1,NULL)) AS pq_2, `fib`, `desc` FROM `".$table_cable."` WHERE `id`='".$cable_id."' LIMIT 1;";
    		$sql="SELECT c1.id, IF(c1.pq_2=".clean($_GET['pq_id']).",c1.pq_2,IF(c1.pq_1=".clean($_GET['pq_id']).",c1.pq_1,NULL)) AS pq_1, IF(c1.pq_1=".clean($_GET['pq_id']).",c1.pq_2,IF(c1.pq_2=".clean($_GET['pq_id']).",c1.pq_1,NULL)) AS pq_2, c1.desc, ct.id AS cable_type_id, ct.fib
    				FROM `".$table_cable."` AS c1, `".$table_cable_type."` AS ct
    				WHERE c1.id=".$cable_id." AND c1.cable_type = ct.id LIMIT 1;";
    		//echo $sql;
    		$result=mysql_fetch_assoc(mysql_query($sql));
    		//$pq_1=$result['pq_1'];
    		$pq_2=$result['pq_2'];
    		$cable_id=$result['id'];
    		$cable_type_id=$result['cable_type_id'];
    		$fib=$result['fib'];
    		//print_r($result);
    	}
		$pq_1=clean($_GET['pq_id']);

    	$sql="SELECT p1.id, pt.type, p1.num, n1.address
			    	FROM ".$table_pq." AS p1, ".$table_node." AS n1, ".$table_pq_type." AS pt, ".$table_street_name." AS sn
			    	WHERE p1.node = n1.id
			    	AND p1.id = ".$pq_1."
			    	AND p1.pq_type_id = pt.id
			    	AND n1.street_id = sn.id
			    	ORDER BY sn.name";
    	$sql3="SELECT p1.id, pt.type, p1.num, n1.address
					FROM ".$table_pq." AS p1, ".$table_node." AS n1, ".$table_pq_type." AS pt, ".$table_street_name." AS sn
					WHERE p1.node = n1.id
					AND p1.id != ".$pq_1."
					AND p1.pq_type_id = pt.id
					AND n1.street_id = sn.id
					ORDER BY n1.address"; //LENGTH(p1.num), 
    	$result_pq_1=mysql_fetch_assoc(mysql_query($sql));
    	
    	if($result_pq_1['type']==0) $type='Кросс'; else $type='Муфта';
    	if(isset($result_pq_1['num'])) $num=' №'.$result_pq_1['num']; else $num='';
    	
    	$pq_1_text=$result_pq_1['address'].' (' .$type.$num. ')';

    	$result=mysql_query($sql3);
    	if(mysql_num_rows($result)){
    		$select_node_2='<select id="pq_2">';
    		$select_node_2.='<option value="0">Выберите кросс/муфту</option>';
    		while($row=mysql_fetch_assoc($result)){
    			if($row['type']==0) $type='Кросс'; else $type='Муфта';
    			if(isset($row['num'])) $num=' №'.$row['num']; else $num='';
    
    			$select_node_2.='<option value="'.$row['id'].'"';
    			if($pq_2==$row['id']) {
    				$select_node_2.=" SELECTED";
    				$node_2_text=$row['address'].' (' .$type.$num. ')';
    			}
    			$select_node_2.='>'.$row['address'].' (' .$type.$num. ')</option>';
    			/////////////////////////////////////////
    		}
    		$select_node_2.='</select>';
    	}

    	if($pq_2) {
    		// проверка на соединение кабеля с другим кабелем перед перемещением
	    	$sql_cable_connect_busy="SELECT COUNT(*) FROM `".$table_cable."` AS c1 JOIN `".$table_pq."` AS p1 ON p1.id = ".$pq_2." JOIN `".$table_fiber."` AS f1 ON f1.cable_id = c1.id JOIN `".$table_fiber_conn."` AS fc1 ON ( fc1.fiber_id_1 = f1.id OR fc1.fiber_id_2 = f1.id ) AND fc1.node_id = p1.node WHERE c1.id = ".$cable_id;
	    	$cable_connect_busy=mysql_result(mysql_query($sql_cable_connect_busy),0);
	    	if($cable_connect_busy>0) {
	    		$text='
		    		<div class="span11 m5">&nbsp;Волокна сварены с кабелем на узле <a href="?act=s_cable&pq_id='.$pq_2.'">'.$node_2_text.'</a>.</div>
		    		<div class="span1 toolbar m0">
		    			<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		    		</div>';
	    		echo $text;
	    		die;
	    	}
	    	// проверка на соединение кабеля с портами перед перемещением
	    	$sql_port_connect_busy="SELECT COUNT(*) FROM `".$table_cruz_conn."` AS cc1 JOIN `".$table_fiber."` AS f1 ON f1.id = cc1.fiber_id WHERE cc1.pq_id = ".$pq_2." AND f1.cable_id = ".$cable_id;
	    	$cable_port_busy=mysql_result(mysql_query($sql_port_connect_busy),0);
	    	if($cable_port_busy>0) {
	    		$text='
		    		<div class="span11 m5">&nbsp;Волокна соединены с портами на <a href="?act=s_cable&pq_id='.$pq_2.'">'.$node_2_text.'</a>.</div>
		    		<div class="span1 toolbar m0">
			    		<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		    		</div>';
	    		echo $text;
	    		die;
	    	}
    	}

    	$sql="SELECT * FROM `".$table_cable_type."` ".($fib?"WHERE fib LIKE ".$fib:"")." ORDER BY name, fib";
    	$result=mysql_query($sql);
    	if(mysql_num_rows($result)){
    		$select_cable_type='<select id="cable_type">';
    		while($row=mysql_fetch_assoc($result)){
    			$select_cable_type.='<option value="'.$row['id'].'" '.($row['id']==$cable_type_id?"SELECTED":"").'>'.$row['name'].' ('.$row['fib'].' ОВ)</option>';
    		}
    		$select_cable_type.='</select>';
    	}
    	
    	
/*    	$text='
    	<div id="new_cable" style="new_cable">
    	От:&nbsp;'.$select_pq_1.'&nbsp;до:&nbsp;'.$select_node_2.'
    	&nbsp;тип:&nbsp;'.$select_cable_type;
*/
    	$text='
	    	<input type="hidden" id="act" value="'.clean($_GET['act']).'" />
	    	<input type="hidden" id="id" value="'.$cable_id.'" />
	    	<input type="hidden" id="pq_1" value="'.$pq_1.'" />';
    	//$text.='<div class="span3 input-control text m5">От:&nbsp;'.$result_pq_1['address'].' ('.$type.$num.')</div>';
    	$text.='<div class="span4 text-left input-control text m5">&nbsp;От:&nbsp;'.$pq_1_text.'</div>';
    	$text.='<div class="span text-left input-control text m5">&nbsp;до:&nbsp;</div>';
    	$text.='<div class="span3 text-left input-control text m0">'.$select_node_2.'</div>';
    	$text.='<div class="span text-left input-control text m5">&nbsp;тип:&nbsp;</div>';
    	$text.='<div class="span2 text-left input-control text m0">'.$select_cable_type.'</div>';
    	//$text.='<div class="span4 text-left input-control text"></div>';
    	//if($_GET['act']=='n_cable') $text.='<div class="span3 input-control text m0"><input class="" type="text" id="desc" value="'.$desc.'" placeholder="Описание"/></div>';
    	
    	//$text.='&nbsp;<input class="mini" type="button" id="new_cable" value="ok" autofocus="autofocus" />&nbsp;<input class="mini" id="exit" type="button" value="Отмена" /></div>';
    	$text.='
    		<div class="span2 toolbar m0">
		    	<button class="icon-checkmark m0" id="new_cable" rel="'.clean($_GET['pq_id']).'" title="Удалить"></button>
		    	<button class="icon-blocked m0" id="exit" title="Отмена"></button>
	    	</div>';
    	//show_menu();
    	echo $text;
    	die;
    }

// удаление кабеля div
    if(isset($_GET['act']) && $_GET['act']=='d_cable' && isset($_GET['pq_id']) && is_numeric($_GET['cable_id']) ) {

    	$cable_id=clean($_GET['cable_id']);
    	$sql="SELECT `id`, IF(pq_2=".clean($_GET['pq_id']).",pq_2,IF(pq_1=".clean($_GET['pq_id']).",pq_1,NULL)) AS pq_1, IF(pq_1=".clean($_GET['pq_id']).",pq_2,IF(pq_2=".clean($_GET['pq_id']).",pq_1,NULL)) AS pq_2, `desc` FROM `".$table_cable."` WHERE `id`='".$cable_id."' LIMIT 1;";
    	$result=mysql_fetch_assoc(mysql_query($sql));
    	$pq_2=$result['pq_2'];
    
    	$pq_1=clean($_GET['pq_id']);
    
    	if($pq_2) {
    		$sql_cable_connect_busy_pq_1="SELECT COUNT(*) FROM `".$table_cable."` AS c1 JOIN `".$table_pq."` AS p1 ON p1.id = ".$pq_1." JOIN `".$table_fiber."` AS f1 ON f1.cable_id = c1.id JOIN `".$table_fiber_conn."` AS fc1 ON ( fc1.fiber_id_1 = f1.id OR fc1.fiber_id_2 = f1.id ) AND fc1.node_id = p1.node WHERE c1.id = ".$cable_id;
    		$sql_cable_connect_busy_pq_2="SELECT COUNT(*) FROM `".$table_cable."` AS c1 JOIN `".$table_pq."` AS p1 ON p1.id = ".$pq_2." JOIN `".$table_fiber."` AS f1 ON f1.cable_id = c1.id JOIN `".$table_fiber_conn."` AS fc1 ON ( fc1.fiber_id_1 = f1.id OR fc1.fiber_id_2 = f1.id ) AND fc1.node_id = p1.node WHERE c1.id = ".$cable_id;
    		$sql_port_connect_busy_pq_1="SELECT COUNT(*) FROM `".$table_cruz_conn."` AS cc1 JOIN `".$table_fiber."` AS f1 ON f1.id = cc1.fiber_id WHERE cc1.pq_id = ".$pq_1." AND f1.cable_id = ".$cable_id;
    		$sql_port_connect_busy_pq_2="SELECT COUNT(*) FROM `".$table_cruz_conn."` AS cc1 JOIN `".$table_fiber."` AS f1 ON f1.id = cc1.fiber_id WHERE cc1.pq_id = ".$pq_2." AND f1.cable_id = ".$cable_id;
    		// проверка на соединение кабеля с другим кабелем перед перемещением
    		$cable_connect_busy_pq_1=mysql_result(mysql_query($sql_cable_connect_busy_pq_1),0);
    		$cable_connect_busy_pq_2=mysql_result(mysql_query($sql_cable_connect_busy_pq_2),0);
    		// проверка на соединение кабеля с портами перед перемещением
    		$cable_port_busy_pq_1=mysql_result(mysql_query($sql_port_connect_busy_pq_1),0);
    		$cable_port_busy_pq_2=mysql_result(mysql_query($sql_port_connect_busy_pq_2),0);
    		//echo 'cable: '.$cable_id.' fib_bus: '.$cable_connect_busy.' port_bus: '.$cable_port_busy;
    		if($cable_connect_busy_pq_1==0 && $cable_connect_busy_pq_2==0 && $cable_port_busy_pq_1==0 && $cable_port_busy_pq_2==0) {
    			$text='<div class="warning">Удалить кабель??? <a href="?act=s_cable&pq_id='.$pq_2.'">'.$node_2_text.'</a>&nbsp;';
    			$text.='<input type="hidden" id="cable_id" value="'.$cable_id.'" /><input id="d_cable_all_button" type="button" value="Удалить" /><input id="exit" type="button" value="отмена" />';
    			$text.='</div><div class="clear"></div>';
    			$text='
    				<input type="hidden" id="cable_id" value="'.$cable_id.'" />
	    			<div class="span10 m5">&nbsp;Удалить кабель??? <a href="?act=s_cable&pq_id='.$pq_2.'">'.$node_2_text.'</a>.</div>
	    			<div class="span2 toolbar m0">
	    				<button class="icon-checkmark m0" id="d_cable_all_button" rel="'.clean($_GET['pq_id']).'" title="Удалить"></button>
	    				<button class="icon-blocked m0" id="exit" title="Отмена"></button>
	    			</div>';
    		} else {
    			if($cable_connect_busy_pq_1!=0 || $cable_port_busy_pq_1!=0) {
	    			$text='<div class="warning">Кабель занят...&nbsp;';
	    			$text.='<input id="exit" type="button" value="отмена" />';
	    			$text.='</div><div class="clear"></div>';
	    			$text='
		    			<div class="span11 m5">&nbsp;Кабель занят на текущем узле.</a>.</div>
		    			<div class="span1 toolbar m0">
		    				<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		    			</div>';
    			} else if($cable_connect_busy_pq_2!=0 || $cable_port_busy_pq_2!=0) {
	    			$text='<div class="warning">Кабель занят...&nbsp;';
	    			$text.='<input id="exit" type="button" value="отмена" />';
	    			$text.='</div><div class="clear"></div>';
	    			$text='
		    			<div class="span11 m5">&nbsp;Кабель занят удалённом узле.</a>.</div>
		    			<div class="span1 toolbar m0">
		    				<button class="icon-blocked m0" id="exit" title="Отмена"></button>
		    			</div>';
    			}
    		}
    		echo $text;
    	}    	
    	die;
    }

// удаление кабеля с волокнами
    if(isset($_POST['act']) && $_POST['act']=='d_cable_all' && is_numeric($_POST['cable_id'])) {
    	if($_POST['cable_id']==0) die;
		mysql_query("DELETE FROM `".$table_fiber."` WHERE `cable_id` = ".clean($_POST['cable_id']));
		mysql_query("DELETE FROM `".$table_cable."` WHERE `id` = ".clean($_POST['cable_id']));
    	die;
    }

/*    if(isset($_GET['act']) && ($_GET['act']=='n_cable' || $_GET['act']=='e_cable') ) {
        if($_GET['act']=='e_cable')
        {
            $cable_id=clean($_GET['cable_id']);
            $result=mysql_fetch_assoc(mysql_query("SELECT * FROM `".$table_cable."` WHERE `id`='".$cable_id."' LIMIT 1;"));
            echo "SELECT *, IF(pq_1=".clean($_GET['pq_id']).",pq_2,IF(pq_2=".clean($_GET['pq_id']).",pq_1,NULL)) AS pq FROM `".$table_cable."` WHERE `id`='".$cable_id."' LIMIT 1;<br>";
            //$pq_1=$result['pq_1'];
            //$pq_2=$result['pq_2'];
            $pq=$result['pq'];
            $fib=$result['fib'];
        } else $pq_1=clean($_GET['pq_id']);
        //$result=mysql_query("SELECT * FROM `".$table_node."` ORDER BY `address`;");
        $sql="SELECT *,`pq`.`id` AS id FROM `".$table_pq."` AS pq, `".$table_node."` AS node WHERE `pq`.`node`=`node`.`id` ".$node." ORDER BY `node`.`address`;";
        echo $sql;
        $result=mysql_query($sql);

        if(mysql_num_rows($result)){
            $select_node_1='<select id="pq_1">';
            $select_node_1.='<option value="0">Выберите кросс/муфту</option>';
            while($row=mysql_fetch_assoc($result)){
                if($row['type']==0) $type='Кросс'; else $type='Муфта';
                if(isset($row['num'])) $num=' №'.$row['num']; else $num='';
                
                $select_node_1.='<option value="'.$row['id'].'"';
                if($pq_1==$row['id']) $select_node_1.=" SELECTED";
                $select_node_1.='>'.$row['address'].' (' .$type.$num. ')</option>';
            }
            $select_node_1.='</select>';
        }

        $result=mysql_query($sql);
        if(mysql_num_rows($result)){
            $select_node_2='<select id="pq_2">';
            $select_node_2.='<option value="0">Выберите кросс/муфту</option>';
            while($row=mysql_fetch_assoc($result)){
                if($row['type']==0) $type='Кросс'; else $type='Муфта';
                if(isset($row['num'])) $num=' №'.$row['num']; else $num='';
        
                $select_node_2.='<option value="'.$row['id'].'"';
                if($pq_2==$row['id']) $select_node_2.=" SELECTED";
                $select_node_2.='>'.$row['address'].' (' .$type.$num. ')</option>';
            }
            $select_node_2.='</select>';
        }

        $select_type='
        <select id="type">
        <option value="0"';
        if($type==0) $select_type.=" SELECTED";
        $select_type.='>кросс</option>
        <option value="1"';
        if($type==1) $select_type.=" SELECTED";
        $select_type.='>муфта</option>
        </select>
        ';
        $text='
        <div id="new_cable" style="new_cable">
            <input type="hidden" id="act" value="'.clean($_GET['act']).'" />
            <input type="hidden" id="id" value="'.$cable_id.'" />
            &nbsp;От:&nbsp;'.$select_node_1.'&nbsp;
            &nbsp;до:&nbsp;'.$select_node_2.'&nbsp;
            кол-во ОВ:&nbsp;<input class="num" type="text" id="fib" value="'.$fib.'" />
            <input type="button" id="new_cable" value="ok" />
            <input id="exit" type="button" value="Отмена" />
        </div>';
        //show_menu();
        echo $text;
        die;
    }*/


// удаление порта
    if(isset($_POST['act']) && $_POST['act']=='d_port' && is_numeric($_POST['port_id'])) {
        if(@mysql_result(mysql_query("SELECT id FROM `".$table_cruz_conn."` WHERE `id`=".clean($_POST['port_id'])." AND fiber_id IS NULL"),0)) {
            mysql_query("DELETE FROM `".$table_cruz_conn."` WHERE `id` = ".clean($_POST['port_id']));
            die;
        }
        echo "exist";
        die;
    }

// вывод списка кабелей на узле
/*	if(isset($_POST['act']) && $_POST['act']=='s_cable_list++' && is_numeric($_POST['pq_id']) && is_numeric($_POST['to_cable_id']) ) {
		$result=mysql_query("SELECT `a`.*, `c1`.`address` as addr_1, `b1`.`type` as type_1, `b1`.`num` as num_1, `c2`.`address` as addr_2, `b2`.`type` as type_2, `b2`.`num` as num_2
				FROM `".$table_cable."` AS a, `".$table_pq."` AS b1, `".$table_pq."` AS b2, `".$table_node."` AS c1, `".$table_node."` AS c2
				WHERE (
				`a`.`pq_1` = `b1`.`id`
				AND `b1`.`node` = `c1`.`id`
		)
				AND (
				`a`.`pq_2` = `b2`.`id`
				AND `b2`.`node` = `c2`.`id`
		) AND (`a`.`pq_1`=".clean($_POST['pq_id'])." OR `a`.`pq_2`=".clean($_POST['pq_id']).")");

		if(mysql_num_rows($result)){
			$select_cable_list='<select class="cable" id="to_cable_'.clean($_REQUEST['pq_id']).'">';
			$select_cable_list.='<option value="0">---</option>';
			while($row=mysql_fetch_assoc($result)){
				if($row['type_1']==0) $type_1='Кросс'; else $type_1='Муфта';
				if(isset($row['num_1'])) $num_1=' №'.$row['num_1']; else $num_1='';
		
				if($row['type_2']==0) $type_2='Кросс'; else $type_2='Муфта';
				if(isset($row['num_2'])) $num_2=' №'.$row['num_2']; else $num_2='';
		
				// меняем местами адреса узлов для удобства, вначале всегда выбранного узла
				if(isset($_POST['pq_id']) && $_POST['pq_id']==$row['pq_2']) {
					$a=2; $b=1;
				} else {
					$a=1; $b=2;
				}
				eval("\$pq_addr_1 = \$row[addr_$a].' (' .\$type_$a.\$num_$a. ')';");
				eval("\$pq_addr_2 = \$row[addr_$b].' (' .\$type_$b.\$num_$b. ')';");

				//$select_cable_list.='<option value="'.$row['id'].'">'.$pq_addr_1.' - '.$pq_addr_2.' ['.$row['fib'].' ОВ]</option>';
				//$select_cable_list.='<option value="'.$row['id'].'">'.$pq_addr_2.' '.$row['id'].' '.$_POST['pq_id'].'</option>';
				$select_cable_list.='<option value="'.$row['id'].'"';
				if($_POST['to_cable_id']==$row['id']) $select_cable_list.=' SELECTED'; 
				$select_cable_list.='>'.$pq_addr_2.'</option>';
				$select_cable_list.='>'.$pq_addr_2.'</option>';
			}
			$select_cable_list.='</select>';
			echo $select_cable_list;
			die;
		}
		echo "exist";
		die;
	}
*/
// вывод списка кроссов/муфт
    if(isset($_POST['act']) &&
                $_POST['act']=='s_pq_list' &&
                is_numeric($_POST['node_id']) &&
                is_numeric($_POST['pq_id']) &&
                is_numeric($_POST['pq_type']) &&
                is_numeric($_POST['pq_num']) &&
                is_numeric($_POST['fiber_id'])
        ) {
    	/*echo '<pre>';
    	print_r($_POST);
    	echo '</pre>';*/
        $fiber_id=clean($_POST['fiber_id']);
        if($_POST['pq_type'] == 1) $num = ' AND p1.`num` = '.clean($_POST['pq_num']);
        $sql="SELECT p1.*, pt.type FROM `".$table_pq."` AS p1, `".$table_pq_type."` AS pt WHERE  p1.`node`=".clean($_POST['node_id'])." AND p1.pq_type_id = pt.id AND pt.`type` = ".clean($_POST['pq_type']).$num.";";
        //echo $sql.'<br>';
        $result_pq=mysql_query($sql);
        if(mysql_num_rows($result_pq)){
            $select_pq_list='<select id="pq_id_'.$fiber_id.'">';
            while($row_pq=mysql_fetch_assoc($result_pq)){
                if($row_pq['type']==0) {
                    if(isset($row_pq['num'])) $num='Кросс №'.$row_pq['num']; else $num='Кросс';
                } else {
                    if(isset($row_pq['num'])) $num='Муфта №'.$row_pq['num']; else $num='Муфта';
                }
                /*echo '<pre>';
                print_r($row_pq);
                echo '</pre>';*/
                $select_pq_list.='<option value="'.$row_pq['id'].'"';
                //if($row_pq['num']==clean($_POST['pq_num']) || mysql_num_rows($result_pq)==1) $select_pq_list.=' SELECTED';
                if($row_pq['id']==clean($_POST['pq_id']) || mysql_num_rows($result_pq)==1) $select_pq_list.=' SELECTED';
                $select_pq_list.='>'.$num.'</option>';
            }
            $select_pq_list.='</select>';
        }
        echo $select_pq_list;
        die;
    }

// вывод списка кабелей на узле
    if(isset($_POST['act']) && $_POST['act']=='s_cable_list' && is_numeric($_POST['pq_id']) && is_numeric($_POST['cable_id']) && is_numeric($_POST['fiber_id']) ) {
    	//$result=mysql_query("SELECT `a`.*, `c1`.`id` as addr_id_1, `pt1`.`type` as type_1, `b1`.`num` as num_1, `c2`.`id` as addr_id_2, `pt2`.`type` as type_2, `b2`.`num` as num_2
    	$result=mysql_query("SELECT `a`.*, `c1`.`address` as addr_1, `pt1`.`type` as type_1, `b1`.`num` as num_1, `c2`.`address` as addr_2, `pt2`.`type` as type_2, `b2`.`num` as num_2
    			FROM `".$table_cable."` AS a, `".$table_pq."` AS b1, `".$table_pq."` AS b2, `".$table_node."` AS c1, `".$table_node."` AS c2, `".$table_pq_type."` AS pt1, `".$table_pq_type."` AS pt2
    			WHERE (
    			`a`.`pq_1` = `b1`.`id`
    			AND `b1`.`node` = `c1`.`id`
    	)
    			AND (
    			`a`.`pq_2` = `b2`.`id`
    			AND `b2`.`node` = `c2`.`id`
    	) AND (`a`.`pq_1`=".clean($_POST['pq_id'])." OR `a`.`pq_2`=".clean($_POST['pq_id']).")
    	AND b1.pq_type_id = pt1.id AND b2.pq_type_id = pt2.id");
    
    	if(mysql_num_rows($result)){
    		$select_cable_list='<select class="cable" id="cable_id_'.clean($_REQUEST['fiber_id']).'">';
    		$select_cable_list.='<option value="0">---</option>';
    		while($row=mysql_fetch_assoc($result)){
				// кросс или муфта для первого pq
    			if($row['type_1']==0) $type_1='Кросс'; else $type_1='Муфта';
    			// номер кросса/муфты для первого pq
    			if(isset($row['num_1'])) $num_1=' №'.$row['num_1']; else $num_1='';
    			// кросс или муфта для второго pq
    			if($row['type_2']==0) $type_2='Кросс'; else $type_2='Муфта';
    			// номер кросса/муфты для второго pq
    			if(isset($row['num_2'])) $num_2=' №'.$row['num_2']; else $num_2='';
    
    			// меняем местами адреса узлов для удобства, вначале всегда выбранного узла
    			if(isset($_POST['pq_id']) && $_POST['pq_id']==$row['pq_2']) {
    				$a=2; $b=1;
    			} else {
    				$a=1; $b=2;
    			}
    			eval("\$pq_addr_1 = \$row[addr_$a].' (' .\$type_$a.\$num_$a. ')';");
    			eval("\$pq_addr_2 = \$row[addr_$b].' (' .\$type_$b.\$num_$b. ')';");
    
    			//$select_cable_list.='<option value="'.$row['id'].'">'.$pq_addr_1.' - '.$pq_addr_2.' ['.$row['fib'].' ОВ]</option>';
    			//$select_cable_list.='<option value="'.$row['id'].'">'.$pq_addr_2.' '.$row['id'].' '.$_POST['pq_id'].'</option>';
    			$select_cable_list.='<option value="'.$row['id'].'"';
    			if($_POST['cable_id']==$row['id']) $select_cable_list.=' SELECTED';
    			$select_cable_list.='>'.$pq_addr_2.'</option>';
    			//$select_cable_list.='>'.$pq_addr_2.'</option>';
    		}
    		$select_cable_list.='</select>';
    		echo $select_cable_list;
    		die;
    	}
    	echo "exist";
    	die;
    }

// вывод не подключенных волокон в кабеле
    if(isset($_POST['act']) && $_POST['act']=='s_fiber_list' && is_numeric($_POST['node_id']) && is_numeric($_POST['pq_id']) && is_numeric($_POST['cable_id']) && is_numeric($_POST['to_fiber_id']) && is_numeric($_POST['fiber_id'])) {
    	/*$sql="SELECT a.*, `c`.`id` AS to_id, `c`.`cable_id` AS to_cable_id, `e`.`id` AS to_pq_id
    			FROM `".$table_fiber."` AS a
    			LEFT JOIN `".$table_fiber_conn."` AS b ON ( `a`.`id` = `b`.`fiber_id_1` OR `a`.`id` = `b`.`fiber_id_2` ) AND `b`.`node_id`= ".clean($_POST['node_id'])."
    			LEFT JOIN `".$table_fiber."` AS c ON `c`.`id` = IF(`a`.`id` = `b`.`fiber_id_1`, `b`.`fiber_id_2`, IF(`a`.`id` = `b`.`fiber_id_2`, `b`.`fiber_id_1`, NULL) ) AND `c`.`id` != ".clean($_POST['fiber_id'])."
    			LEFT JOIN `".$table_cable."` AS d ON `c`.`cable_id` = `d`.`id`
    			LEFT JOIN `".$table_pq."` AS e ON ( `e`.`id` = `d`.`pq_1` OR `e`.`id` = `d`.`pq_2` ) AND `e`.`node` = ".clean($_POST['node_id'])."
    			WHERE `a`.`cable_id` = ".clean($_POST['cable_id'])." AND `c`.`id` IS NULL AND `a`.`id` != ".clean($_POST['fiber_id'])."
    			ORDER BY `a`.`num`";*/
    	$sql="SELECT a.*, `c`.`id` AS to_id, `c`.`cable_id` AS to_cable_id, `e`.`id` AS to_pq_id, cc.port
		    	FROM `".$table_fiber."` AS a
		    	LEFT JOIN `".$table_fiber_conn."` AS b ON ( `a`.`id` = `b`.`fiber_id_1` OR `a`.`id` = `b`.`fiber_id_2` ) AND `b`.`node_id`= ".clean($_POST['node_id'])."
		    	LEFT JOIN `".$table_fiber."` AS c ON `c`.`id` = IF(`a`.`id` = `b`.`fiber_id_1`, `b`.`fiber_id_2`, IF(`a`.`id` = `b`.`fiber_id_2`, `b`.`fiber_id_1`, NULL) ) AND `c`.`id` != ".clean($_POST['fiber_id'])."
		    	LEFT JOIN `".$table_cable."` AS d ON `c`.`cable_id` = `d`.`id`
		    	LEFT JOIN `".$table_pq."` AS e ON ( `e`.`id` = `d`.`pq_1` OR `e`.`id` = `d`.`pq_2` ) AND `e`.`node` = ".clean($_POST['node_id'])."
		    	
		    	LEFT JOIN `".$table_cruz_conn."` AS cc ON cc.pq_id = ".clean($_POST['pq_id'])." AND cc.fiber_id = a.id
		    	
		    	WHERE `a`.`cable_id` = ".clean($_POST['cable_id'])." AND `c`.`id` IS NULL AND `a`.`id` != ".clean($_POST['fiber_id'])."
		    	".($_POST['port_id']!=-1?'AND cc.port IS '.($_POST['port_id']!=0?'NOT':'').' NULL':'')." 
		    	ORDER BY `a`.`num`";
    	$sql="SELECT a.*, `c`.`id` AS to_id, `c`.`cable_id` AS to_cable_id, `e`.`id` AS to_pq_id, cc.port
		    	FROM `".$table_fiber."` AS a
		    	LEFT JOIN `".$table_fiber_conn."` AS b ON ( `a`.`id` = `b`.`fiber_id_1` OR `a`.`id` = `b`.`fiber_id_2` ) AND `b`.`node_id`= ".clean($_POST['node_id'])."
		    	LEFT JOIN `".$table_fiber."` AS c ON `c`.`id` = IF(`a`.`id` = `b`.`fiber_id_1`, `b`.`fiber_id_2`, IF(`a`.`id` = `b`.`fiber_id_2`, `b`.`fiber_id_1`, NULL) ) AND `c`.`id` != ".clean($_POST['fiber_id'])."
		    	LEFT JOIN `".$table_cable."` AS d ON `c`.`cable_id` = `d`.`id`
		    	LEFT JOIN `".$table_pq."` AS e ON ( `e`.`id` = `d`.`pq_1` OR `e`.`id` = `d`.`pq_2` ) AND `e`.`node` = ".clean($_POST['node_id'])."
		    	 
		    	LEFT JOIN `".$table_cruz_conn."` AS cc ON cc.pq_id = ".clean($_POST['pq_id'])." AND cc.fiber_id = a.id
		    	 
		    	WHERE `a`.`cable_id` = ".clean($_POST['cable_id'])." AND `c`.`id` IS NULL AND `a`.`id` != ".clean($_POST['fiber_id'])."
		    	".($_POST['port_id']!=-1?'AND cc.port IS '.($_POST['port_id']!=0?'NOT':'').' NULL':'')."
		    	ORDER BY `a`.`num`";
    	echo $sql;
    	$result=mysql_query($sql);
    	if(mysql_num_rows($result)){
    		$select_fiber='<select id="fiber_id_'.clean($_POST['fiber_id']).'">';
    		while($row=mysql_fetch_assoc($result)){
    			$select_fiber.='<option value="'.$row['id'].'"';
    			if($_POST['to_fiber_id']==$row['id']) $select_fiber.=' SELECTED';
    			$select_fiber.='>'.$row['num'].($row['port']?' ['.$row['port'].']':'').'</option>';
    		}
    		$select_fiber.='</select>';
    		echo $select_fiber;
    		die;
    	}
    	echo "error...";
    	die;
    }
    
// ввод нового соединения волокон
    if(isset($_POST['act']) && $_POST['act']=='n_fiber_conn' && is_numeric($_POST['fiber_id']) && is_numeric($_POST['to_fiber_id']) && is_numeric($_POST['node_id'])) {
    	//echo "SELECT id FROM `".$table_pq."` WHERE `node`='".clean($_POST['node'])."' AND `type`=".$_POST['type']." AND ".$num.";";
    	if(! @mysql_result(mysql_query("SELECT id FROM `".$table_fiber_conn."` WHERE `fiber_id_1`=".clean($_POST['fiber_id'])." AND `fiber_id_2`=".clean($_POST['to_fiber_id'])." AND `node_id`=".clean($_POST['node_id']).";"),0)) {
    		mysql_query("INSERT INTO `".$table_fiber_conn."` (`fiber_id_1`,`fiber_id_2`,`node_id`,`user_id`) VALUES (".clean($_POST['fiber_id']).", ".clean($_POST['to_fiber_id']).", ".clean($_POST['node_id']).",".$user_id.")");
    		//echo "INSERT INTO `".$table_fiber_conn."` (`fiber_id_1`,`fiber_id_2`,`pq_id`) VALUES (".clean($_POST['from']).", ".clean($_POST['to']).", ".clean($_POST['pq']).")";
    		//echo "INSERT INTO `".$table_fiber."` (`cable_id`,`num`) VALUES (".clean($_POST['cable_id']).", ".clean($_POST['num']).")";
    		echo "ok";
    		die;
    	}
    	echo "exist";
    	die;
    }

// удаление соединения волокон
    if(isset($_POST['act']) && $_POST['act']=='d_fiber_conn' && is_numeric($_POST['node_id']) && is_numeric($_POST['to_fiber_id']) && is_numeric($_POST['fiber_id'])) {
    	$id=@mysql_result(mysql_query("SELECT id FROM `".$table_fiber_conn."` WHERE ( `fiber_id_1`=".clean($_POST['fiber_id'])." OR `fiber_id_1`=".clean($_POST['to_fiber_id'])." ) AND ( `fiber_id_1`=".clean($_POST['fiber_id'])." OR `fiber_id_1`=".clean($_POST['to_fiber_id'])." ) AND `node_id`=".clean($_POST['node_id']).";"),0);
    	if($id) {
    		//echo "SELECT id FROM `".$table_fiber_conn."` WHERE `fiber_id_1`=".clean($_POST['fiber_id'])." AND `fiber_id_2`=".clean($_POST['to_fiber_id'])." AND `node_id`=".clean($_POST['node_id']).";";
    		if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_fiber_conn."` WHERE ( `fiber_id_1`=".clean($_POST['fiber_id'])." OR `fiber_id_1`=".clean($_POST['to_fiber_id'])." ) AND ( `fiber_id_2`=".clean($_POST['fiber_id'])." OR `fiber_id_2`=".clean($_POST['to_fiber_id'])." ) AND `node_id`=".clean($_POST['node_id']).";"),0)>1) {
				echo "Волокно занято!!! Не работает... Ищите ашипку";
				die;
			} else mysql_query("DELETE FROM `".$table_fiber_conn."` WHERE `id` = ".$id);
    		die;
    	}
    	echo "exist";
    	die;
    }

// отслеживание соединения волокон
    if(isset($_POST['act']) && $_POST['act']=='f_fiber_conn' && is_numeric($_POST['node_id']) && is_numeric($_POST['fiber_id']) && is_numeric($_POST['to_fiber_id'])) {
    	//$id=@mysql_result(mysql_query("SELECT id FROM `".$table_fiber_conn."` WHERE ( `fiber_id_1`=".clean($_POST['fiber_id'])." OR `fiber_id_1`=".clean($_POST['to_fiber_id'])." ) AND ( `fiber_id_1`=".clean($_POST['fiber_id'])." OR `fiber_id_1`=".clean($_POST['to_fiber_id'])." ) AND `node_id`=".clean($_POST['node_id']).";"),0);
    	/*if($id) {
    		//echo "SELECT id FROM `".$table_fiber_conn."` WHERE `fiber_id_1`=".clean($_POST['fiber_id'])." AND `fiber_id_2`=".clean($_POST['to_fiber_id'])." AND `node_id`=".clean($_POST['node_id']).";";
    		if(!@mysql_result(mysql_query("SELECT COUNT(*) FROM `".$table_fiber_conn."` WHERE ( `fiber_id_1`=".clean($_POST['fiber_id'])." OR `fiber_id_1`=".clean($_POST['to_fiber_id'])." ) AND ( `fiber_id_2`=".clean($_POST['fiber_id'])." OR `fiber_id_2`=".clean($_POST['to_fiber_id'])." ) AND `node_id`=".clean($_POST['node_id']).";"),0)>1) {
    			echo "Волокно занято!!! Не работает... Ищите ашипку";
    			die;
    		} else mysql_query("DELETE FROM `".$table_fiber_conn."` WHERE `id` = ".$id);
    		die;
    	}*/
    	$i=0;
    	//echo find_fiber(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),'last',clean($_POST['f_fiber_pq_iq']));
    	//echo fib_find(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),0,clean($_POST['f_fiber_pq_iq']));
    	
    	//echo fib_find(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),clean($_POST['node_id']),clean($_POST['to_node_id']),true);
        echo fib_find(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),clean($_POST['to_node_id']));

    	//echo fib_find(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),0,clean($_POST['to_node']));
    	//function fib_find($id,$last_id,$to_pq,$to_node_id) {
    	//echo fib_find($id,$last_id,0,81);
    	echo '<div class="clear"></div>';
        //echo '<input type="button" id="f_fiber_clean_'.clean($_POST['fiber_id']).'" value="Очистить" />';
    	die;
    }

// отслеживание занятости портов
    if(isset($_POST['act']) && $_POST['act']=='f_fiber_used' && is_numeric($_POST['node_id']) && is_numeric($_POST['fiber_id']) && is_numeric($_POST['to_fiber_id'])) {
    	$i=0;
    	//echo ' pq: '.clean($_POST['pq_id']);
    	//echo fib_find_used(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),clean($_POST['to_node_id']),clean($_POST['pq_id']));
    	
    	echo fib_find_used(clean($_POST['fiber_id']),clean($_POST['to_fiber_id']),clean($_POST['to_node_id']),clean($_POST['pq_id']));
    	
    	//echo fib_find_used(clean($_POST['to_fiber_id']),clean($_POST['fiber_id']),clean($_POST['node_id']));
    	//echo clean($_POST['fiber_id']).' '.clean($_POST['to_fiber_id']).' '.clean($_POST['node_id']);
    	//echo fib_find_used(clean($_POST['to_fiber_id']),clean($_POST['fiber_id']),clean($_POST['node_id']));
    	//echo '<div class="clear"></div>';
    	die;
    }

//function fib_find($id,$last_id,$to_pq,$to_node_id,$first) {
function fib_find($id,$last_id,$to_node_id) {
	//echo ' 1: '.$id.' 2: '.$last_id.' 3: '.$to_node_id;
	//die;
	global $table_cruz_conn;
	global $table_fiber_conn;
	global $table_fiber;
	global $table_cable;
	global $table_pq;
	global $table_pq_type;
	global $table_node;

	$sql='
	SELECT
	f1.id AS id, f1.num AS num, n1.id AS from_node_id,
	IF(p1.node = n1.id, p1.id, p2.id) AS from_pq_id,
	c1.id AS from_cable_id,

	f2.id AS to_id, f2.num AS to_num, n2.id AS to_node_id,
	IF(p3.node = n2.id, p3.id, p4.id) AS to_pq_id,
	c2.id AS to_cable_id,
		
	c_n.id AS curr_node_id, c_n.address AS curr_node_addr, c_n.desc AS curr_node_desc,
	c_n.incorrect,
	#IF(p1.node = p3.node OR p1.node = p4.node, p1.id,IF(p2.node = p3.node OR p2.node = p4.node,p2.id,NULL)) AS curr_pq_id
	IF(p1.node = c_n.id, p1.id,IF(p2.node = c_n.id,p2.id,NULL)) AS curr_pq_id

	FROM '.$table_fiber_conn.' AS fc1, '.$table_fiber.' AS f1, '.$table_cable.' AS c1, '.$table_pq.' AS p1, '.$table_pq.' AS p2, '.$table_fiber.' AS f2, '.$table_cable.' AS c2, '.$table_pq.' AS p3, '.$table_pq.' AS p4,
	'.$table_node.' AS n1, '.$table_node.' AS c_n, '.$table_node.' AS n2
	WHERE
	( ( fc1.fiber_id_1 = '.$id.' AND fc1.fiber_id_2 != '.$last_id.' ) OR ( fc1.fiber_id_2 = '.$id.' AND fc1.fiber_id_1 != '.$last_id.' ) )
	AND
	f1.id = '.$id.'
	AND
	c1.id = f1.cable_id
	AND
	p1.id = c1.pq_1 AND p2.id = c1.pq_2
	AND
	n1.id = IF(p1.node = p3.node OR p1.node = p4.node, p2.node,p2.node)
	AND
	p3.id = c2.pq_1 AND p4.id = c2.pq_2
	AND
	n2.id = IF(p1.node = p3.node OR p2.node = p3.node, p4.node,p3.node)
	AND
	#c_n.id = IF(p1.node = p3.node OR p1.node = p4.node, p1.node,IF(p2.node = p3.node OR p2.node = p4.node,p2.node,NULL))
	( c_n.id = IF(p1.node = p3.node OR p1.node = p4.node, p1.node,NULL) OR c_n.id = IF(p2.node = p3.node OR p2.node = p4.node,p2.node,NULL) )
	AND
	f2.id = IF(fc1.fiber_id_1 = '.$id.', fc1.fiber_id_2, fc1.fiber_id_1)
	AND
	c2.id = f2.cable_id
	AND c_n.id = '.$to_node_id.'
	';

	//echo 'last_id: '.$last_id.' id: '.$id.'<br>';
	$result=@mysql_fetch_assoc(mysql_query($sql));
/*
    echo '<pre>';
    print_r($sql);
    echo '</pre>';
*/
	if($result) {
		//echo 'curr_pq: '.$result['curr_pq_id'].'<br>';

		/////
		// вывод номеров портов
		// волокно 1
		$sql_c1="SELECT * FROM `".$table_pq."` AS p1, `".$table_cruz_conn."` AS cc1 WHERE p1.node = ".$result['curr_node_id']." AND cc1.fiber_id = ".$result['id']." AND p1.id = cc1.pq_id";
		$result_c1=mysql_fetch_assoc(mysql_query($sql_c1));

		if(isset($result_c1['port'])) $port1=$result_c1['port'];
		// волокно 2
		$sql_c2="SELECT * FROM `".$table_pq."` AS p1, `".$table_cruz_conn."` AS cc1 WHERE p1.node = ".$result['curr_node_id']." AND cc1.fiber_id = ".$result['to_id']." AND p1.id = cc1.pq_id";
		$result_c2=mysql_fetch_assoc(mysql_query($sql_c2));
		if(isset($result_c2['port'])) $port2=$result_c2['port'];

        //echo 'port_1: '.$result_c1['port'].' port_2: '.$result_c2['port'];
		// если кроссы не совподают, то выводить номер кросса
		if($result_c1['pq_id'] != $result_c2['pq_id']) {
            if($result_c1['type']==0) $type1='Кросс'; else $type1='Муфта';
            if(isset($result_c1['num'])) $num1.='№'.$result_c1['num']; else $num1.='';
                
            if($result_c2['type']==0) $type2='Кросс'; else $type2='Муфта';
            if(isset($result_c2['num'])) $num2.='№'.$result_c2['num']; else $num2.='';

            if($type1==$type2) $cruz='<div class="show_find_pq_legend">'.$type1.':</div><div class="show_find_pq_lfib"><a class="isoc" href="?act=s_cable&pq_id='.$result_c1['pq_id'].'" target="_blank">'.$num1.'</a></div><div class="show_find_pq_rfib"><a class="isoc" href="?act=s_cable&pq_id='.$result_c2['pq_id'].'" target="_blank">'.$num2.'</a></div>';
		} else {
			$sql_c="SELECT * FROM `".$table_pq."` AS p1, `".$table_pq_type."` AS pt WHERE p1.id = ".$result['curr_pq_id']." AND p1.pq_type_id = pt.id";
			$result_c=mysql_fetch_assoc(mysql_query($sql_c));

			if($result_c['type']==0) $type='Кросс'; else $type='Муфта';
			if(isset($result_c['num'])) $num.='№'.$result_c['num']; else $num.='';

			if(!$num) $num = '№1';
			$cruz='<div class="show_find_pq_legend">'.$type.':</div><div class="show_find_pq_fib"><a class="isoc" href="?act=s_cable&pq_id='.$result['curr_pq_id'].'" target="_blank">&nbsp;'.$num.'&nbsp;</a></div>';
		}

        // если заданы порты, то выводим
        if($result_c1 && $result_c2) {
            $port='<div class="show_find_pq_legend border_top">Порт:</div><div class="show_find_pq_lfib border_top">'.$port1.'</div><div class="show_find_pq_rfib border_top">'.$port2.'</div>';

        } else {
            $port='<div class="show_find_pq_legend border_top">&nbsp;</div><div class="show_find_pq_lfib border_top">&nbsp;</div><div class="show_find_pq_rfib border_top">&nbsp;</div>';

        }

		$text='
		<div class="show_find_pq">
		<div class="show_find_pq_title'.($result['incorrect']==1?' bg-color-orangeDark':'').'">
		<a class="isoc'.($result['incorrect']==1?' bg-color-orangeDark':'').'" href="?act=s_pq&node_id='.$result['curr_node_id'].'" target="_blank">'.$result['curr_node_addr'].'</a>
		</div>';
		$text.=$cruz;
		$text.='
		<div class="show_find_pq_legend border_top">ОВ:</div><div class="show_find_pq_lfib border_top">'.$result['num'].'</div><div class="show_find_pq_rfib border_top">'.$result['to_num'].'</div>';
		$text.=$port;
		$text.='
		</div>';
		$text.='<div class="show_find_pq_arrow">></div>';
		echo $text;
		echo fib_find($result['to_id'],$result['id'],$result['to_node_id']);
	} else {
        $sql2='
        SELECT
        f1.id AS id, f1.num AS num, n1.id AS curr_node_id, n1.address AS curr_node_addr, n1.incorrect, n1.desc AS curr_node_desc, p1.id AS curr_pq_id
        FROM '.$table_fiber.' AS f1, '.$table_cable.' AS c1, '.$table_pq.' AS p1, '.$table_node.' AS n1
        WHERE
        f1.id = '.$id.'
        AND
        c1.id = f1.cable_id
        AND
        ( c1.pq_1 = p1.id OR c1.pq_2 = p1.id)
        AND
        p1.node = '.$to_node_id.'
        AND
        n1.id = p1.node
        ';
		$result2=mysql_fetch_assoc(mysql_query($sql2));

		//$sql_c="SELECT * FROM `".$table_pq."` AS p1 WHERE p1.id = ".$result2['curr_pq_id'];
		$sql_c="SELECT * FROM `".$table_pq."` AS p1, `".$table_pq_type."` AS pt WHERE p1.id = ".$result2['curr_pq_id']." AND p1.pq_type_id = pt.id";

		$result_c=mysql_fetch_assoc(mysql_query($sql_c));
		$sql_cc="SELECT * FROM `".$table_cruz_conn."` AS cc1 WHERE cc1.fiber_id = ".$result2['id']." AND cc1.pq_id = ".$result2['curr_pq_id']."";
	
		$result_cc=mysql_fetch_assoc(mysql_query($sql_cc));

		if($result_c['type']==0) $type='Кросс'; else $type='Муфта';
		if(isset($result_c['num'])) $num.='№'.$result_c['num']; else $num.='';
		if(!$num) $num = '№1';

		$text='
		<div class="show_find_pq">
		<div class="show_find_pq_title'.($result2['incorrect']==1?' bg-color-orangeDark':'').'">
		<a class="isoc '.($result2['incorrect']==1?' bg-color-orangeDark':'').'" href="?act=s_pq&node_id='.$result2['curr_node_id'].'" target="_blank">'.$result2['curr_node_addr'].'</a>
		</div>
		<div class="show_find_pq_legend">'.$type.':</div><div class="show_find_pq_fib"><a class="isoc" href="?act=s_cable&pq_id='.$result2['curr_pq_id'].'" target="_blank">&nbsp;'.$num.'&nbsp;</a></div>
		<div class="show_find_pq_legend border_top">ОВ:</div><div class="show_find_pq_fib border_top">'.$result2['num'].'</div>
		<div class="show_find_pq_legend border_top">Порт:</div><div class="show_find_pq_fib border_top">'.$result_cc['port'].'</div>';
		if($result_cc['desc']) $text.='<div class="show_find_pq_desc border_top">'.$result_cc['desc'].'</div>';
		$text.='</div>';
		echo $text;
	}
	//die;
}

function fib_find_used($id,$last_id,$to_node_id,$pq_id) {
	//echo ' 1: '.$id.' 2: '.$last_id.' 3: '.$to_node_id.'<br>';
	echo ' id: '.$id.' last_id: '.$last_id.' to_node: '.$to_node_id.' pq: '.$pq_id.'<br>';
	//die;
	global $table_cruz_conn;
	global $table_fiber_conn;
	global $table_fiber;
	global $table_cable;
	global $table_pq;
	global $table_pq_type;
	global $table_node;

	$sql='
	SELECT
	if(fc1.fiber_id_1='.$id.',fc1.fiber_id_1,if(fc1.fiber_id_2='.$id.',fc1.fiber_id_1,NULL)) AS id,
	if(fc1.fiber_id_1='.$id.',fc1.fiber_id_2,if(fc1.fiber_id_2='.$id.',fc1.fiber_id_2,NULL)) AS to_id,
	fc1.node_id AS to_node_id

	FROM '.$table_fiber_conn.' AS fc1
	WHERE
	( ( fc1.fiber_id_1 = '.$id.' AND fc1.fiber_id_2 != '.$last_id.' ) OR ( fc1.fiber_id_2 = '.$id.' AND fc1.fiber_id_1 != '.$last_id.' ) )
	';

	//echo 'last_id: '.$last_id.' id: '.$id.'<br>';
	echo '<pre>';
	print_r($sql);
	echo '</pre>';
	$result=@mysql_fetch_assoc(mysql_query($sql));
	if($result) {
		echo '<pre>';
		print_r($result);
		echo '</pre>';
		echo fib_find_used($result['to_id'],$result['id'],$result['to_node_id'],$result['curr_pq_id']);
	} else {
		//$sql_c="SELECT * FROM `".$table_pq."` AS p1, `".$table_pq_type."` AS pt WHERE p1.id = ".$result2['curr_pq_id']." AND p1.pq_type_id = pt.id";

		//=mysql_fetch_assoc(mysql_query($sql_c));
		$sql="SELECT * FROM
		`".$table_cruz_conn."` AS cc1,
		`".$table_pq."` AS p1,
		`".$table_node."` AS n1
		WHERE
		cc1.fiber_id = ".$id.'
		AND p1.id = cc1.pq_id
		AND n1.id = p1.node';
		
		$result=mysql_fetch_assoc(mysql_query($sql));
		echo '<pre>';
		print_r($result);
		//print_r($sql);
		echo '</pre>';

		if($result['type']==0) $type='Кросс'; else $type='Муфта';
		if(isset($result['num'])) $num.='№'.$result['num']; else $num.='';
		if(!$num) $num = '№1';

		/*$text='
		<div class="show_find_pq">
			<div class="show_find_pq_title'.($result2['incorrect']==1?' bg-color-orangeDark':'').'">
				<a class="isoc '.($result2['incorrect']==1?' bg-color-orangeDark':'').'" href="?act=s_pq&node_id='.$result2['curr_node_id'].'" target="_blank">'.$result2['curr_node_addr'].'</a>
			</div>
			<div class="show_find_pq_legend">'.$type.':</div><div class="show_find_pq_fib"><a class="isoc" href="?act=s_cable&pq_id='.$result2['curr_pq_id'].'" target="_blank">&nbsp;'.$num.'&nbsp;</a></div>
			<div class="show_find_pq_legend border_top">ОВ:</div><div class="show_find_pq_fib border_top">'.$result2['num'].'</div>
			<div class="show_find_pq_legend border_top '.($result_cc['used']?'bg-color-green':'').'">Порт:</div><div class="show_find_pq_fib border_top">'.$result_cc['port'].'</div>';
			if($result_cc['desc']) $text.='<div class="show_find_pq_desc border_top">'.$result_cc['desc'].'</div>';
		$text.='</div>';*/
		$text='
		<div class="left'.($result['incorrect']==1?' bg-color-orangeDark':'').'"><a class="isoc '.($result['incorrect']==1?' bg-color-orangeDark':'').'" href="?act=s_pq&node_id='.$result['node'].'" target="_blank">&nbsp;'.$result['address'].'</a>&nbsp;</div>
		<div class="left">&nbsp;'.$type.':</div><div class="left">&nbsp;<a class="isoc" href="?act=s_cable&pq_id='.$result['pq_id'].'" target="_blank">&nbsp;'.$num.'&nbsp;</a></div>
		<div class="left">&nbsp;ОВ:</div><div class="left">&nbsp;'.$result['num'].'&nbsp;</div>
		<div class="left '.($result['used']?'bg-color-green':'').'">&nbsp;Порт:&nbsp;</div><div class="left">&nbsp;'.$result['port'].'&nbsp;</div>';
		if($result['desc']) $text.='<div class="left">'.$result['desc'].'</div>';
		echo $text;
	}
	//die;
}

/*
function fib_find($id,$last_id,$to_node_id) {
    //echo ' 1: '.$id.' 2: '.$last_id.' 3: '.$to_node_id;
    //die;
    global $table_cruz_conn;
    global $table_fiber_conn;
    global $table_fiber;
    global $table_cable;
    global $table_pq;
    global $table_node;

    $sql='
    SELECT
    f1.id AS id, f1.num AS num, n1.id AS from_node_id,
    IF(p1.node = n1.id, p1.id, p2.id) AS from_pq_id,
    c1.id AS from_cable_id,

    f2.id AS to_id, f2.num AS to_num, n2.id AS to_node_id,
    IF(p3.node = n2.id, p3.id, p4.id) AS to_pq_id,
    c2.id AS to_cable_id,
        
    c_n.id AS curr_node_id, c_n.address AS curr_node_addr, c_n.desc AS curr_node_desc,
    IF(p1.node = p3.node OR p1.node = p4.node, p1.id,IF(p2.node = p3.node OR p2.node = p4.node,p2.id,NULL)) AS curr_pq_id

    FROM '.$table_fiber_conn.' AS fc1, '.$table_fiber.' AS f1, '.$table_cable.' AS c1, '.$table_pq.' AS p1, '.$table_pq.' AS p2, '.$table_fiber.' AS f2, '.$table_cable.' AS c2, '.$table_pq.' AS p3, '.$table_pq.' AS p4,
    '.$table_node.' AS n1, '.$table_node.' AS c_n, '.$table_node.' AS n2
    WHERE
    ( ( fc1.fiber_id_1 = '.$id.' AND fc1.fiber_id_2 != '.$last_id.' ) OR ( fc1.fiber_id_2 = '.$id.' AND fc1.fiber_id_1 != '.$last_id.' ) )
    AND
    f1.id = '.$id.'
    AND
    c1.id = f1.cable_id
    AND
    p1.id = c1.pq_1 AND p2.id = c1.pq_2
    AND
    n1.id = IF(p1.node = p3.node OR p1.node = p4.node, p2.node,p2.node)
    AND
    p3.id = c2.pq_1 AND p4.id = c2.pq_2
    AND
    n2.id = IF(p1.node = p3.node OR p2.node = p3.node, p4.node,p3.node)
    AND
    c_n.id = IF(p1.node = p3.node OR p1.node = p4.node, p1.node,IF(p2.node = p3.node OR p2.node = p4.node,p2.node,NULL))
    AND
    f2.id = IF(fc1.fiber_id_1 = '.$id.', fc1.fiber_id_2, fc1.fiber_id_1)
    AND
    c2.id = f2.cable_id
    AND c_n.id = '.$to_node_id.'
    ';

    //echo 'last_id: '.$last_id.' id: '.$id.'<br>';
    $result=@mysql_fetch_assoc(mysql_query($sql));

//    echo '<pre>';
//    print_r($sql);
//    echo '</pre>';

    if($result) {
        //echo 'curr_pq: '.$result['curr_pq_id'].'<br>';

        /////
        // вывод номеров портов
        // волокно 1
        $sql_c1="SELECT * FROM `".$table_pq."` AS p1, `".$table_cruz_conn."` AS cc1 WHERE p1.node = ".$result['curr_node_id']." AND cc1.fiber_id = ".$result['id']." AND p1.id = cc1.pq_id";
        $result_c1=mysql_fetch_assoc(mysql_query($sql_c1));

        if(isset($result_c1['port'])) $port1=$result_c1['port'];
        // волокно 2
        $sql_c2="SELECT * FROM `".$table_pq."` AS p1, `".$table_cruz_conn."` AS cc1 WHERE p1.node = ".$result['curr_node_id']." AND cc1.fiber_id = ".$result['to_id']." AND p1.id = cc1.pq_id";
        $result_c2=mysql_fetch_assoc(mysql_query($sql_c2));
        if(isset($result_c2['port'])) $port2=$result_c2['port'];

        //echo 'port_1: '.$result_c1['port'].' port_2: '.$result_c2['port'];
        // если кроссы не совподают, то выводить номер кросса
        if($result_c1['pq_id'] != $result_c2['pq_id']) {
                                    if($result_c1['type']==0) $type1='Кросс'; else $type1='Муфта';
            if(isset($result_c1['num'])) $num1.='№'.$result_c1['num']; else $num1.='';
                
            if($result_c2['type']==0) $type2='Кросс'; else $type2='Муфта';
            if(isset($result_c2['num'])) $num2.='№'.$result_c2['num']; else $num2.='';

            if($type1==$type2) $cruz='<div class="show_find_pq_legend">'.$type1.':</div><div class="show_find_pq_lfib"><a class="isoc" href="?act=s_cable&pq_id='.$result_c1['pq_id'].'" target="_blank">'.$num1.'</a></div><div class="show_find_pq_rfib"><a class="isoc" href="?act=s_cable&pq_id='.$result_c2['pq_id'].'" target="_blank">'.$num2.'</a></div>';
        } else {
            $sql_c="SELECT * FROM `".$table_pq."` AS p1 WHERE p1.id = ".$result['curr_pq_id'];
            $result_c=mysql_fetch_assoc(mysql_query($sql_c));

            if($result_c['type']==0) $type='Кросс'; else $type='Муфта';
            if(isset($result_c['num'])) $num.='№'.$result_c['num']; else $num.='';

            if(!$num) $num = '№1';
            $cruz='<div class="show_find_pq_legend">'.$type.':</div><div class="show_find_pq_fib"><a class="isoc" href="?act=s_cable&pq_id='.$result['curr_pq_id'].'" target="_blank">&nbsp;'.$num.'&nbsp;</a></div>';
        }

        // если заданы порты, то выводим
        if($result_c1 && $result_c2) {
            $port='<div class="show_find_pq_legend border_top">Порт:</div><div class="show_find_pq_lfib border_top">'.$port1.'</div><div class="show_find_pq_rfib border_top">'.$port2.'</div>';

        } else {
            $port='<div class="show_find_pq_legend border_top">&nbsp;</div><div class="show_find_pq_lfib border_top">&nbsp;</div><div class="show_find_pq_rfib border_top">&nbsp;</div>';

        }

        $text='
        <div class="show_find_pq">
        <div class="show_find_pq_title">
        <a class="isoc" href="?act=s_pq&node_id='.$result['curr_node_id'].'" target="_blank">'.$result['curr_node_addr'].'</a>
        </div>';
        $text.=$cruz;
        $text.='
        <div class="show_find_pq_legend border_top">ОВ:</div><div class="show_find_pq_lfib border_top">'.$result['num'].'</div><div class="show_find_pq_rfib border_top">'.$result['to_num'].'</div>';
        $text.=$port;
        $text.='
        </div>';
        $text.='<div class="show_find_pq_arrow">></div>';
        echo $text;
        echo fib_find($result['to_id'],$result['id'],$result['to_node_id']);
    } else {
        $sql2='
        SELECT
        f1.id AS id, f1.num AS num, n1.id AS curr_node_id, n1.address AS curr_node_addr, n1.desc AS curr_node_desc, p1.id AS curr_pq_id
        FROM '.$table_fiber.' AS f1, '.$table_cable.' AS c1, '.$table_pq.' AS p1, '.$table_node.' AS n1
        WHERE
        f1.id = '.$id.'
        AND
        c1.id = f1.cable_id
        AND
        ( c1.pq_1 = p1.id OR c1.pq_2 = p1.id)
        AND
        p1.node = '.$to_node_id.'
        AND
        n1.id = p1.node
        ';
        $result2=mysql_fetch_assoc(mysql_query($sql2));

        $sql_c="SELECT * FROM `".$table_pq."` AS p1 WHERE p1.id = ".$result2['curr_pq_id'];

        $result_c=mysql_fetch_assoc(mysql_query($sql_c));
        $sql_cc="SELECT * FROM `".$table_cruz_conn."` AS cc1 WHERE cc1.fiber_id = ".$result2['id']." AND cc1.pq_id = ".$result2['curr_pq_id']."";
    
        $result_cc=mysql_fetch_assoc(mysql_query($sql_cc));

        if($result_c['type']==0) $type='Кросс'; else $type='Муфта';
        if(isset($result_c['num'])) $num.='№'.$result_c['num']; else $num.='';
        if(!$num) $num = '№1';

        $text='
        <div class="show_find_pq">
        <div class="show_find_pq_title">
        <a class="isoc" href="?act=s_pq&node_id='.$result2['curr_node_id'].'" target="_blank">'.$result2['curr_node_addr'].'</a>
        </div>
        <div class="show_find_pq_legend">'.$type.':</div><div class="show_find_pq_fib"><a class="isoc" href="?act=s_cable&pq_id='.$result2['curr_pq_id'].'" target="_blank">&nbsp;'.$num.'&nbsp;</a></div>
        <div class="show_find_pq_legend border_top">ОВ:</div><div class="show_find_pq_fib border_top">'.$result2['num'].'</div>
        <div class="show_find_pq_legend border_top">Порт:</div><div class="show_find_pq_fib border_top">'.$result_cc['port'].'</div>';
        if($result_cc['desc']) $text.='<div class="show_find_pq_desc border_top">'.$result_cc['desc'].'</div>';
        $text.='</div>';
        echo $text;
    }
    //die;
}
*/


	echo "Нифига не работает\n";
	echo '<pre>';
	print_r($_REQUEST);
	echo '</pre>';
?>