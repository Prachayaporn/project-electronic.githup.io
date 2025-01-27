<?php 
$dase_url = "http://localhost/project-electronic-return-system";

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'project-electronic return system';
$conn = mysqli_connect($servername,$username, $password, $dbname);

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
} 
?>
