<?php
// Initialize the session
session_start();
echo file_get_contents("html/navbar.html");	
require_once "config.php";

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
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();
                
                if($stmt->num_rows == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
	
	//Obtain student name
	$studentName = $_POST["studentname"];
	$uploadName = htmlspecialchars($studentName);
	$fileUploaded = false;
	
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
	}
	//Define PCN value and use preg_match() 
    if(numbers_only($_POST["pcn"]) != false){
		$pcn = $_POST["pcn"];
	}else{
		$pcn_err = "PCN must only contain letters";
	}
	
	$studentYear = $_POST["studentYear"];
	
	$motivationText = htmlspecialchars($_POST["motivation"]);
 }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($pcn_err) && empty($uploadError)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO buddymatch (username, password, studentname, pcn, imagelocation, year, motivation, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
		if($username !="" && $password != "" && $studentName != "" && $pcn != "" && $target_file != ""){
			if($stmt = $mysqli->prepare($sql)){
				
				// Set parameters
				$param_username = $username;
				$param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
				$param_studentname = $studentName;
				$param_pcn = $pcn;
				$param_imagelocation = $target_file;
				$param_year = $studentYear;
				$param_motivation = $motivationText;
				$param_email = $_POST["email"];
				// Bind variables to the prepared statement as parameters
			   $stmt->bind_param("ssssssss",$param_username, $param_password, $param_studentname, $param_pcn, $param_imagelocation, $param_year, $param_motivation, $param_email);
				// Attempt to execute the prepared statement
				if($stmt->execute()){
					// Redirect to login page
					header("location: login.php");
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				} 

				// Close statement
				$stmt->close();
			}
		} 
    }
    
    // Close connection
    $mysqli->close();

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
					<label>Username</label>
					<input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="">
					<span class="invalid-feedback"><?php echo $username_err; ?></span>
				</div>  
			</tr>
			<tr>
				<div class="form-group">
					<label>Password</label>
					<input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="">
					<span class="invalid-feedback"><?php echo $password_err; ?></span>
				</div>
			</tr>
			<tr>
				<div class="form-group">
					<label>Confirm Password</label>
					<input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="">
					<span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
				</div>
			</tr>
			<tr>
				<div class="form-group">
					<label for="studentName">Student Name</label>
					<input type="text" id="studentName" name="studentname" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="">
					<span class="invalid-feedback"><?php echo $username_err; ?></span>
				</div>
			</tr>
			<tr>
				<div class="form-group">
					<label for="email">Enter your email:</label>
					<input type="email" id="email" name="email">
				</div>
			</tr>
			<tr>
				<div class="form-group right">
					<label>PCN</label>
					<input type="text" name="pcn" class="form-control <?php echo (!empty($pcn_err)) ? 'is-invalid' : ''; ?>" value="">
					<span class="invalid-feedback"><?php echo $pcn_err; ?></span> 
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
					<label>Which year are you in?</label>
					<input type="radio" id="first" name="studentYear" value="first" checked>
					<label for="first">First Year</label>
					<input type="radio" id="second" name="studentYear" value="second">
					<label for="second">Second Year</label>
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