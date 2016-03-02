<?php

include("config.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
// username and password sent from form 

	$myusername=$_POST['username'];//mysql_real_escape_string( $_POST['username']); 
	$mypassword=$_POST['password'];//mysql_real_escape_string( $_POST['password']); 
	$myfullname=$_POST['fullname'];//mysql_real_escape_string( $_POST['fullname']); 
	$myemail=$_POST['email'];//mysql_real_escape_string( $_POST['email']); 
	$myidentity=$_POST['identity'];//mysql_real_escape_string( $_POST['identity']); 
	$mymusic=$_POST['music'];//mysql_real_escape_string( $_POST['music']); 
	$mygender=$_POST['gender'];//mysql_real_escape_string( $_POST['gender']); 
	$myzipcode=$_POST['zipcode'];//mysql_real_escape_string( $_POST['zipcode']);
	$mylatitude=$_POST['latitude'];//mysql_real_escape_string( $_POST['latitude']);
	$mylongitude=$_POST['longitude'];//mysql_real_escape_string( $_POST['longitude']);

	if(isset($myusername) && isset($mypassword)){
		$sql="SELECT id FROM users WHERE username='$myusername'";
		$result=mysqli_query($db,$sql);
		$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
		$active=$row['active'];
		$count = mysqli_num_rows($result);
		if($count==1) {
			echo "registered";
		}else {
			$query = "INSERT INTO `users` (username, password, fullname, email, role, favorite_music, gender, zipcode) VALUES ('$myusername', '$mypassword', '$myfullname','$myemail', '$myidentity', '$mymusic', '$mygender', '$myzipcode')";
			$result = mysqli_query($db, $query);
			$query2="INSERT INTO `users_latest_location` (username, location) VALUES ('$myusername', POINT('$mylatitude', '$mylongitude'));";
			$result2 = mysqli_query($db, $query2);
			echo "success";
		}

	}else{
		echo "invalid";
	}
}
exit;
?>