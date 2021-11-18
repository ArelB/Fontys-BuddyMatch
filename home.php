  <meta charset="UTF-8">
  <title>Welcome to FontysBuddy Matching! </title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel='stylesheet' href="./html/matchPageStyle.css">

<?php 
	// Initialize the session
	session_start();
	 
	echo file_get_contents("html/header.html");
	echo file_get_contents("html/navbar.html");
	echo file_get_contents("html/home-body.html");
	
	// Check if the user is already logged in, if yes then redirect him to welcome page
	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
		echo ("</br> Hi ");
		echo htmlspecialchars($_SESSION["username"]."! Want to start looking for a buddy?"); 
		echo (" Click <a href='match.php'>here</a>");
	} else{
		echo ("Click <a href='login.php'> here </a> to login or register");
	}
?>