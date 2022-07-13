<?php
require_once dirname(__FILE__).'/../config.php';

/**
* This BaseDao class will contact to database.
*
* Other dao classes will be child of this BaseDao class.
*
* @author Suleyman Oner
*/
class BaseDao {
    protected $connection; 

    public function __construct() {
      try {
        $this->connection = new PDO("mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_SCHEME,DB_USERNAME,DB_PASSWORD);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        throw new Exception("Internal server error. Please try again in a few minutes!");
      } 
    }

    public function query($query, $params) {
      $stmt = $this->connection->prepare($query);
      $stmt->execute($params);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($table, $entity){
      $query = "INSERT INTO ${table} (";
      foreach ($entity as $column => $value) {
        $query .= $column.", ";
      }
      $query = substr($query,0,-2);
      $query .= ") VALUES (";
      foreach ($entity as $column => $value) {
        $query .= ":".$column.", ";
      }
      $query = substr($query,0,-2);
      $query .= ")";
  
      $stmt= $this->connection->prepare($query);
      $stmt->execute($entity);
      $entity['id'] = $this->connection->lastInsertId();
      return $entity;
    }

}
