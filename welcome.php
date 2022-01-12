<?php
// Initialize the session
session_start();
echo file_get_contents("html/navbar.html");	

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to your profile management page!</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
	<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel='stylesheet' href="./html/matchPageStyle.css">
</head>
<body>
    <h1 class="welcomeTitle">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>! Welcome to your profile management page</h1>
<?php

require_once "config.php";            

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
$uploadError = "";
$errorReason = "";
$uploadName = "";
$pcn_err = "";
$studentYear = "";
$motivationText = "";
$param_username = $param_password = $param_studentname = $pcn = $imagelocation = $year = $motivation = "";
$studentName = "";
$target_file = "";
$email = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	//Obtain student name
	$fileUploaded = false;
	$imagelocSQL = "";
	$motivationSQL = "";
	$emailSQL = "";
    $param_username = htmlspecialchars($_SESSION["username"]);
	$motivationText = $_POST["motivation"];
	
	if($_FILES["fileToUpload"]["name"] != ""){	
		//Target directory is where files are uploaded, creates file names and locations
		$target_dir = "uploads/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$fileUploaded = true;

		//Check if image is actually an image
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			$uploadOk = 1;
		}else{
			$uploadOk = 0;
			$errorReason = "image is not a file";
		}
			
		// Check if file already exists
		if (file_exists($target_file)) {
			$errorReason = " the filename is taken. please rename your file";
			$uploadOk = 0;
		}
		
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {	
			$uploadError = "Sorry, your file was not uploaded because ".$errorReason;
		// if everything is ok, try to upload file
		}else{
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			//echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
			}else{
				$uploadError = "Sorry, there was an error uploading your file because ".$errorReason;
			}
		}
		//Image location is stored
		$param_imagelocation = $target_file;

		if ($uploadOk != 0 && $param_imagelocation != ""){
		//Image location is here
		//create sql for picture location update
			$imagelocSQL = "UPDATE buddymatch SET imagelocation = '".$param_imagelocation."' WHERE username = '".$param_username."'";
		}
	}
		$param_motivation = $motivationText;
		//if motivation text is not empty then update motivation text
		if($motivationText != ""){
			//Create sql for motivation update
			$motivationSQL = "UPDATE buddymatch SET motivation = '".$motivationText."' WHERE username = '".$param_username."'";
		}
		$param_email = $_POST["email"];
		if($param_email != ""){
			//Create SQL for email update
			$emailSQL = "UPDATE buddymatch SET email = '".$param_email."' WHERE username = '".$param_username."'";
		}
	
	if($motivationSQL != ""){
	//new connection to database
		if ($mysqli->query($motivationSQL) === TRUE) {
		} else {
			echo "Error updating record: " . $mysqli->error;
		}
	}
	
	if($emailSQL != ""){
		if ($mysqli->query($emailSQL) === TRUE) {
		} else {
			echo "Error updating record: " . $mysqli->error;
		}
	}
	
	if($param_imagelocation != "" && $uploadOk != 0){
		if ($mysqli->query($imagelocSQL) === TRUE) {
		} else {
			echo "Error updating record: " . $mysqli->error;
		}
	}
}

function numbers_only($value)
{
    $result  = preg_match('/^([0-9]*)$/', $value);
	if($result != 1){
		return false; 
	}else{
		return true;
	}
}
?>
 
<body>
    <div class="wrapper">
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"  enctype="multipart/form-data">
			<table>
			<tr>
				<div class="form-group">
					<label for="email">Enter your email:</label>
					<input type="email" id="email" name="email">
				</div>
			</tr>
			<tr>
				<div class="form-group">
					<label>Select image to upload:</label>
					<input type="file" name="fileToUpload" id="fileToUpload" class="form-control <?php echo (!empty($uploadError)) ? 'is-invalid' : ''; ?> ">
					<span class="invalid-feedback"> <?php echo($uploadError); ?> </span>
				</div>
			</tr>
			<tr>
				<div class="form-group right">
					<label for="Motivation"> Motivation:</label>
					<textarea id="Motivation" name="motivation" rows="4" cols="50">
					</textarea>
				</div>
			</tr>
			<tr>
				<div class="test">
					<input type="submit" class="btn btn-primary" value="Submit">
					<input type="reset" class="btn btn-secondary ml-2" value="Reset">
					<a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
					<a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
				</div>
			</tr>
		</table>
        </form>
    </div>    
</body
</html>