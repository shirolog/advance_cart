<?php 

try{
    $dbname= 'mysql:host=localhost;dbname=advance_db';
    $username= 'root';
    $password= '';

    $conn = new PDO($dbname, $username, $password);
}catch(PDOException $e){
    echo 'Connection failed!'. $e->getMessage();
}


?>