<?php
	ini_set("display_errors", 0);
	error_reporting(0);
 
	$db_host		= "192.168.1.111:3306";
	$db_user		= "root";
	$db_pass		= "root";
	$db_name		= "easylink";
 
	$conn = mysqli_connect($db_host, $db_user, $db_pass,$db_name);
	// if (!$conn) die("Connection for user $db_user refused!");
	// mysqli_select_db($conn, $db_name) or die("Can not connect to database!");

	if(!$conn){
		echo "Koneksi database gagal : " . mysqli_connect_error();
	}
 
?>