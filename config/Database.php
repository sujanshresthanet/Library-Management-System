<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
class Database{
	
	private $host  = 'localhost';
    private $user  = 'root';
    private $password   = "anchorPoints@1";
    private $database  = "libraryhub"; 
    
    public function getConnection(){		
		$conn = new mysqli($this->host, $this->user, $this->password, $this->database);
		if($conn->connect_error){
			die("Error failed to connect to MySQL: " . $conn->connect_error);
		} else {
			return $conn;
		}
    }
}
?>