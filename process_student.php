<?php
// define variables and set to empty values
$firstName = $middleName = $lastName = $email = $PCN = $studentID = $motivation = $courseoptions = $year = $pass = $repass = $website = "";
$sql = "";
function test_input($data)
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $firstName = test_input($_POST["firstname"]);
  $middleName = test_input($_POST["middlename"]);
  $lastName = test_input($_POST["lastname"]);
  $PCN = test_input($_POST["PCN"]);
  $studentID = test_input($_POST["studentID"]);
  $motivation = test_input($_POST["motivation"]);
  $courseoptions = test_input($_POST["courseOptions"]);
  $year = test_input($_POST["year"]);
  $email = test_input($_POST["email"]);
  $pass = test_input($_POST["pass"]);
  $repass = test_input($_POST["repass"]);
}

$servername = "localhost";
$username = "root";
$password = "";
$dbName = "test";

$fullName = $firstName." ".$middleName." ".$lastName;
$photoloc = "bla";
$likes = "maartje";
$dislikes = "Tchina";
$matches = "none";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbName);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

//Create the fullname from the parts of the name;

$fullName = $firstName." ".$middleName." ".$lastName;
//Create the SQLCommand with the user obtained data. 
$sqlCommand = "INSERT INTO `buddymatch`(`Student Name`, `PCN`, `Student ID`, `Photo Location`, `Year`, `Motivation`, `Likes`, `Dislikes`, `Matches`) 
VALUES ('".$fullName."','".$PCN."','".$studentID."','".$photoloc."','".$year."','".$motivation."','".$likes."','".$dislikes."','".$matches."')";

echo $sqlCommand;

if ($conn->query($sqlCommand) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();


?>
