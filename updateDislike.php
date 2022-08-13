<?php
	header("Content-Type: application/json; charset=UTF-8");
	//require DB connection config
    require_once "config.php";
	//Start PHP file to obtain JSON data 
	//receive JSON object and decode it to php object
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$obj = json_decode($_POST["x"], false);
		//Define objects for SQL query
		$param_username = $obj->userSwiper;
		$param_userdisliked = $obj->userDisliked;
		//update dislike table with newly disliked user
		$sql = "INSERT INTO `dislikes` (`id`, `created_at`, `username`, `dislike`) VALUES ('', current_timestamp(),'".$param_username."', '".$param_userdisliked."')";
		if($stmt = $mysqli->prepare($sql)){
			$stmt->execute();
			echo("no failure to launch");
		}
	}

