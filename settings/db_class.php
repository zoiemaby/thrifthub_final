<?php
/**
 * Database Connection Class
 * ThriftHub - Database Handler using MySQLi
 * 
 * This class provides methods to interact with the database
 * using MySQLi extension.
 */

require_once __DIR__ . '/db_cred.php';

class Database {
    private $conn;
    
    /**
     * Constructor - Establishes database connection using credentials from db_cred.php
     * 
     * @return void
     */
    public function __construct() {
        // Create connection using constants from db_cred.php
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check if connection failed
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        // Set charset to utf8mb4
        $this->conn->set_charset("utf8mb4");
    }
    
    /**
     * Execute a SQL query
     * 
     * @param string $sql SQL query to execute
     * @return mysqli_result|bool Returns result object on success, false on failure
     */
    public function query($sql) {
        $result = $this->conn->query($sql);
        
        if (!$result) {
            // Log error for debugging (but don't expose to user)
            error_log("Database query error: " . $this->conn->error . " | SQL: " . $sql);
            return false;
        }
        
        return $result;
    }
    
    /**
     * Fetch a single row from database
     * 
     * @param string $sql SQL query to execute
     * @return array|false Returns associative array of single row, or false on failure
     */
    public function fetchOne($sql) {
        $result = $this->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    /**
     * Fetch all rows from database
     * 
     * @param string $sql SQL query to execute
     * @return array Returns array of associative arrays, empty array if no results
     */
    public function fetchAll($sql) {
        $result = $this->query($sql);
        $data = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        return $data;
    }
    
    /**
     * Escape string data to prevent SQL injection
     * 
     * @param string $data Data to escape
     * @return string Escaped string
     */
    public function escape($data) {
        return $this->conn->real_escape_string($data);
    }
    
    /**
     * Get the last insert ID from the most recent INSERT query
     * 
     * @return int Last insert ID, or 0 if no previous INSERT
     */
    public function insert_id() {
        return $this->conn->insert_id;
    }
    
    /**
     * Get the database connection object (for advanced use)
     * 
     * @return mysqli Database connection object
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Close database connection
     * 
     * @return void
     */
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    
    /**
     * Destructor - Close connection when object is destroyed
     * 
     * @return void
     */
    public function __destruct() {
        $this->close();
    }
}
