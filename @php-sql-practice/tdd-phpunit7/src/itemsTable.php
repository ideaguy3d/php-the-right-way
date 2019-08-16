<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 9/7/2018
 * Time: 5:37 PM
 */

namespace TDD;

use PDO;

class ItemsTable {
    protected $table = 'items';
    
    protected $PDO;
    
    public function __construct(PDO $pdo) {
        $this->PDO = $pdo;
    }
    
    public function __destruct() {
        unset($this->PDO);
    }
    
    public function findForId($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $statement = $this->PDO->prepare($query);
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}