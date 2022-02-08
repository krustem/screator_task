<?php 
class DB {
    private $host = 'localhost';
    private $user = 'dbadmin';
    private $pass = 'Dbadmin123.';
    private $dbname = 'screatordb';

    public function connect(){
        $conn_str = "mysql:host=$this->host;dbname=$this->dbname";
        $conn = new PDO($conn_str, $this->user, $this->pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }

}

?>