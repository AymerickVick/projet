


<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'etudiants';
    private $username = 'root';
    private $password = 'Keyce-2024';
    public $conn;

    public function getConnection() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        return $this->conn;
    }
}
?>
