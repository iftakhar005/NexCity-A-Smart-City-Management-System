<?php

$username='root';
$hostname='localhost';
$password='';
$database='nextcity';
 $conn=mysqli_connect($hostname,$username,$password,$database,3306);

 if($conn==False){
    die("ERROR in connection establishment".mysqli_connect_error());
 }

?>