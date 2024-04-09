<?php 
// Database connection
$DBservername = "localhost";
$DBusername = "root";
$DBpassword = "";
$DBdatabase = "shopping";

$conn = new mysqli($DBservername, $DBusername, $DBpassword, $DBdatabase);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>