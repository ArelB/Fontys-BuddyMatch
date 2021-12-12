<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Buddy Matching! Swipe to find yourself a new buddy!</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel='stylesheet' href="./html/matchPageStyle.css">
</head>
<body>
<?php

	// Initialize the session
	session_start();
	echo file_get_contents("html/navbar.html");	
//checking if user is logged in, if they are show matching page otherwise display message
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){

	require_once "config.php";
	//Create counter for array
	$y = 0;
	 //Create SQL and arrays for likes
	$sql = "SELECT DISTINCT userlikes FROM likes WHERE username = '".$_SESSION["username"]."'";
	$likedUsers = array();
	$dislikedUsers = array();
	
	//Create sql for disliked users then execute the search command
	$sqldisliked = "SELECT DISTINCT dislike FROM dislikes WHERE username = '".$_SESSION["username"]."'";
	if($stmt = $mysqli->prepare($sql)){
		$stmt->execute();
		$result = $stmt->get_result();
		  while($row = $result->fetch_assoc()) {
			array_push($likedUsers, $row["userlikes"]);
		  }
	}
	//Add yourself to the array to make sure you don't appear in your own search
	array_push($likedUsers,$_SESSION["username"]);
	
	if($stmt = $mysqli->prepare($sqldisliked)){
		$stmt->execute();
		$result = $stmt->get_result();
		 while($row = $result->fetch_assoc()) {
			array_push($dislikedUsers, $row["dislike"]);
		}
	}
	//Combine the two arrays into one large array
	$users = array_merge($likedUsers, $dislikedUsers);
	//Creates unique array removing all the duplicates that could be involved.
	$users = array_unique($users);
	
	//Creates string that holds all the already searched names.
	$numquestions = count($users);
	$paramvalues = "";
	
	//rearrange array in position
	$users = array_values($users);
	
	for ($x = 0; $x < $numquestions; $x++){
		if($x != 0){
			$paramvalues = $paramvalues." ,".$users[$x];
		}else{
			$paramvalues = $users[0];
		}
	}
	
	//available users array
	$avusers = array();
	$motivarray = array();
	$imagearray = array();
	//Search for all users that haven't appeared before
	$searchsql = "SELECT DISTINCT username, motivation, imagelocation FROM buddymatch";
	if($stmt = $mysqli->prepare($searchsql)){
		$stmt->execute();
		$result = $stmt->get_result();
		//Sorts array with new users only
		if(count($users) != 0){
			while($row = $result->fetch_assoc()) {
				//Users need to be checked against whole sqlreturn
				if(in_array($row["username"],$users)){
					continue;
				}
				//if not matched pushed into array
				array_push($avusers,$row["username"]);
				array_push($motivarray, $row["motivation"]);
				array_push($imagearray, $row["imagelocation"]);
			}
		//if no data found then just enter all data into array{
		}else{
			while($row = $result->fetch_assoc()) { 
					array_push($avusers,$row["username"]);
					array_push($motivarray, $row["motivation"]);
					array_push($imagearray, $row["imagelocation"]);
				}
		}
	}
?>

<?php

//Set up variable for loop counter
	if (count($avusers) > 0) { ?>
	<body>
	<div class="tinder">
	  <div class="tinder--status">
		<i class="fa fa-remove"></i>
		<i class="fa fa-heart"></i>
	  </div>
	 <div class="tinder--cards"> <?php
	  // output data of each row
	  $i = 1;
	  //while($row = $result->fetch_assoc()) {
		 for($z = 0; $z < count($avusers); $z++){
		?>
		<div class="tinder--card">
			  <img class="responsive" src=" <?php echo ($imagearray[$z]);?> ">
			  <h3 class="currentSwipe"><?php echo $avusers[$z]; ?></h3>
			  <p id="currentUser" hidden><?php echo $_SESSION["username"]; ?></p>
			  <p><?php echo "Motivation: ".$motivarray[$z] ?></p>
		</div>
		<?php 
	  } ?>
	  </div>

		  <div class="tinder--buttons">
			<button id="nope"><i class="fa fa-remove"></i></button>
			<button id="love"><i class="fa fa-heart"></i></button>
		  </div>
		</div>
		<?php
	} else {
	  echo ("<h2 class='titleHeader' style='width:60% !important; font-size: 2vh;'> Unfortunately, there are no more students left to match with. Please try again later! </h2>");
	}	
?>

<!-- partial -->
	  <script src='https://hammerjs.github.io/dist/hammer.min.js'></script>
	  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	  <script  src="./html/matchPageScript.js"></script>
	  <?php
}else{
	echo ("<h2 class='titleHeader' id='loginMessage'>You need to log in to find a buddy. Click <a href='login.php'> here </a> to login or register </h2>");
}
 ?>
</body>
