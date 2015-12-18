<?php
	@header('Content-Type: text/html; charset=utf-8');
	include_once ('./setup.php');
	include_once ('./db.php');
	$user_id=$_SESSION['logged_user_fibers_id'];

/*	echo '<pre>';
	print_r($_POST);
	print_r($_FILES);
	echo '</pre>';
	die;*/

if(isset($_FILES["file_input"]) && $_FILES["file_input"]["error"]== UPLOAD_ERR_OK)
{
	############ Edit settings ##############
	$UploadDirectory	= '../uploads/'; //specify upload directory ends with / (slash)
	##########################################
	
	/*
	Note : You will run into errors or blank page if "memory_limit" or "upload_max_filesize" is set to low in "php.ini". 
	Open "php.ini" file, and search for "memory_limit" or "upload_max_filesize" limit 
	and set them adequately, also check "post_max_size".
	*/
	
	//check if this is an ajax request
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
		die();
	}
	
	
	//Is file size is less than allowed size.
	if ($_FILES["file_input"]["size"] > 5242880) {
		die("File size is too big!");
	}
	
	//allowed file type Server side check
	switch(strtolower($_FILES['file_input']['type']))
		{
			//allowed file types
            /*case 'image/png': 
			case 'image/gif': 
			case 'image/jpeg': 
			case 'image/pjpeg':
			case 'text/plain':
			case 'text/html': //html file
			case 'application/x-zip-compressed':*/
			case 'application/pdf':
			/*case 'application/msword':
			case 'application/vnd.ms-excel':
			case 'video/mp4':*/
				break;
			default:
				die('Unsupported File!'); //output error
	}
	
	$File_Name          = strtolower($_FILES['file_input']['name']);
	$File_Ext           = substr($File_Name, strrpos($File_Name, '.')); //get file extention
	$Random_Number      = rand(0, 9999999999); //Random number to be added to name.
	$NewFileName 		= $Random_Number.$File_Ext; //new file name
	
	if(move_uploaded_file($_FILES['file_input']['tmp_name'], $UploadDirectory.$NewFileName ))
	{
		$file=file_get_contents($UploadDirectory.$NewFileName);
		$sql="INSERT INTO ".$table_pq_schem." (pq_id, name, data, user_id) VALUES (".clean($_POST['pq_id']).", '".$File_Name."', '".pg_escape_bytea(file_get_contents($UploadDirectory.$NewFileName))."', ".$user_id.")";
		//$sql="INSERT INTO ".$table_pq_schem." (data) VALUES (pg_read_binary_file('".$UploadDirectory.$NewFileName."')::bytea)";
		//$sql="INSERT INTO ".$table_pq_schem." (data) VALUES ('".pg_escape_bytea(file_get_contents($UploadDirectory.$NewFileName))."')";
		pg_query($sql);
		unlink($UploadDirectory.$NewFileName);
		//echo $sql;
		die('Success! File Uploaded.');
	}else{
		die('error uploading File!');
	}
	
}
else
{
	die('Something wrong with upload! Is "upload_max_filesize" set correctly?');
}