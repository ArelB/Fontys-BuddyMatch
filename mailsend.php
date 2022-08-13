<?php

 require_once "config.php";
 //Select last match from database
 $sql = "SELECT * FROM matches ORDER BY ID DESC LIMIT 1";
 $likedUsers = array();
 $emails = array();
 $firstemail = array();
 $secondemail = array();
 //Store into array
 if($stmt = $mysqli->prepare($sql)){
		$stmt->execute();
		$result = $stmt->get_result();
		  while($row = $result->fetch_assoc()) {
			array_push($likedUsers, $row["Matches"]);
		  }
	}
  //Get the name and explode them into seperate names
 $userNames = array_pop($likedUsers);
 $users = explode("-", $userNames);
 $userone = trim(array_pop($users));
 $usertwo = trim(array_pop($users));
 $sql = "SELECT email FROM buddymatch WHERE username = '".$userone."'";
 //Get user one email
 if($stmt = $mysqli->prepare($sql)){
		$stmt->execute();
		$result = $stmt->get_result();
		  while($row = $result->fetch_assoc()) {
			array_push($firstemail, $row["email"]);
		  }
	}
$userone = trim(array_pop($firstemail));
 $sql = "SELECT email FROM buddymatch WHERE username = '".$usertwo."'";
 //Get user one email
 if($stmt = $mysqli->prepare($sql)){
		$stmt->execute();
		$result = $stmt->get_result();
		  while($row = $result->fetch_assoc()) {
			array_push($secondemail, $row["email"]);
		  }
	}
$usertwo = trim(array_pop($secondemail));

 //recipient email string created
 $recipientString = $userone.",".$usertwo;
 $recipient= $recipientString;
 $subject="Match has been made";
 $mail_body="Congratulations! You made a new study buddy. The two of you can feel free to contact eachother through these mails";
 mail($recipient, $subject, $mail_body);
 ?>