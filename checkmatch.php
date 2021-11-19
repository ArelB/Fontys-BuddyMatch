<?php
	header("Content-Type: application/json; charset=UTF-8");
	//require DB connection config
    require_once "config.php";
	$match = "no";
	//Start PHP file to obtain JSON data 
	//receive JSON object and decode it to php object
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$obj = json_decode($_POST["x"], false);
		//Define objects for SQL query
		$param_username = $obj->userSwiper;
		$param_matches = $obj->userSwiper." - ".$obj->userLiked;
		$param_userliked = $obj->userLiked;
		//Contact Database to get results
		 $sql = "SELECT username FROM likes WHERE userlikes = '".$obj->userSwiper."'";
		 
		 if($stmt = $mysqli->prepare($sql)){
			$stmt->execute();
			$result = $stmt->get_result(); 
			if ($result->num_rows > 0) {
			// output data of each row
				while($row = $result->fetch_assoc()) {
					if($row["username"] == $obj->userLiked){
						$match = "yes";
					}
				}
			}
			if($match == "yes"){
				//Update matches table with new match
				$sql = "INSERT INTO `matches` (`id`, `created_at`, `username`, `Matches`) VALUES ('', current_timestamp(),'".$param_username."', '".$param_matches."')";
				if($stmt = $mysqli->prepare($sql)){
					$stmt->execute();
				}
				$sql = "INSERT INTO `likes` (`id`, `created_at`, `username`, `userlikes`) VALUES ('', current_timestamp(),'".$param_username."', '".$param_userliked."')";
				if($stmt = $mysqli->prepare($sql)){
					$stmt->execute();
				}
				
				
			}else{
				//update liked table with new like
				$sql = "INSERT INTO `likes` (`id`, `created_at`, `username`, `userlikes`) VALUES ('', current_timestamp(),'".$param_username."', '".$param_userliked."')";
				if($stmt = $mysqli->prepare($sql)){
					$stmt->execute();
				}
			}
			echo ($match);
		 }
	}
