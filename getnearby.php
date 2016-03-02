<?php
include("config.php");
session_start();
/*if(isset($_SESSION['login_user'])){
	$user_check = $_SESSION['login_user'];

	$ses_sql=mysqli_query($db,"select username from users where username='$user_check'; ");

	$row=mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);

	$login_session=$row['username'];
}*/
if($_SERVER["REQUEST_METHOD"] == "POST"){

	$mylatitude=$_POST['latitude'];//mysql_real_escape_string( $_POST['latitude']);
	$mylongitude= $_POST['longitude']; //mysql_real_escape_string( $_POST['longitude']);
	//$myTab=mysql_real_escape_string($_POST['currentTab']);

	if(isset($_SESSION['login_user'])){
		$user_check = $_SESSION['login_user'];
		$ses_sql=mysqli_query($db,"select username from users where username='$user_check'; ");
		$row=mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
		$login_session=$row['username'];
		
		$sql_location="SELECT location FROM users_latest_location WHERE username='$login_session';";
		$result_location=mysqli_query($db,$sql_location);	
		$count_location = mysqli_num_rows($result_location);
		if($count_location == 1){
			$sql_update="UPDATE `users_latest_location` SET `location`=POINT('$mylatitude', '$mylongitude') WHERE username='$login_session';";
			$result_update=mysqli_query($db,$sql_update);
		}else{
			$query_insert="INSERT INTO `users_latest_location` (username, location) VALUES ('$login_session', POINT('$mylatitude', '$mylongitude'));";
			$result_insert = mysqli_query($db, $query_insert);
		}
	}	
	$sql = "SELECT 
  				users.username, 
  				users.role,
  				users.favorite_music,
   				( 3959 * acos( cos( radians('$mylatitude') ) * cos( radians( X(location) ) ) 
   					* cos( radians(Y(location)) - radians('$mylongitude')) + sin(radians('$mylatitude')) 
   					* sin( radians(X(location))))) AS distance 
			FROM users_latest_location 
			INNER JOIN users
			ON users_latest_location.username=users.username
			WHERE users.username <> '$login_session'
			ORDER BY distance ASC;";
	$result=mysqli_query($db,$sql);
    $rows = array();
	while($r =mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		$rows[] = $r;
	}
    echo json_encode($rows);

}


if($_SERVER["REQUEST_METHOD"] == "GET"){

	$mylatitude=$_GET['latitude'];//mysql_real_escape_string( $_POST['latitude']);
	$mylongitude= $_GET['longitude']; //mysql_real_escape_string( $_POST['longitude']);
	//$myTab=mysql_real_escape_string($_POST['currentTab']);

	if(isset($_SESSION['login_user'])){
		$user_check = $_SESSION['login_user'];
		$ses_sql=mysqli_query($db,"select username from users where username='$user_check'; ");
		$row=mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
		$login_session=$row['username'];
		
		$sql_location="SELECT location FROM users_latest_location WHERE username='$login_session';";
		$result_location=mysqli_query($db,$sql_location);	
		$count_location = mysqli_num_rows($result_location);
		if($count_location == 1){
			$sql_update="UPDATE `users_latest_location` SET `location`=POINT('$mylatitude', '$mylongitude') WHERE username='$login_session';";
			$result_update=mysqli_query($db,$sql_update);
		}else{
			$query_insert="INSERT INTO `users_latest_location` (username, location) VALUES ('$login_session', POINT('$mylatitude', '$mylongitude'));";
			$result_insert = mysqli_query($db, $query_insert);
		}
		$sql = "SELECT 
  				users.username, 
  				users.role,
  				users.favorite_music,
   				( 3959 * acos( cos( radians('$mylatitude') ) * cos( radians( X(location) ) ) 
   					* cos( radians(Y(location)) - radians('$mylongitude')) + sin(radians('$mylatitude')) 
   					* sin( radians(X(location))))) AS distance 
			FROM users_latest_location 
			INNER JOIN users
			ON users_latest_location.username=users.username
			WHERE users.username <> '$login_session'
			ORDER BY distance ASC;";
	}else{
		$sql = "SELECT 
  				users.username, 
  				users.role,
  				users.favorite_music,
   				( 3959 * acos( cos( radians('$mylatitude') ) * cos( radians( X(location) ) ) 
   					* cos( radians(Y(location)) - radians('$mylongitude')) + sin(radians('$mylatitude')) 
   					* sin( radians(X(location))))) AS distance 
			FROM users_latest_location 
			INNER JOIN users
			ON users_latest_location.username=users.username
			ORDER BY distance ASC;";
	}
	$result=mysqli_query($db,$sql);
    $rows = array();
	while($r =mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		$rows[] = $r;
	}
    echo json_encode($rows);

}

?>