<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 9/7/2018
 * Time: 5:47 PM
 */

namespace TDD\Test;

use PHPUnit\Framework\TestCase;
use TDD\ItemsTable;
use \PDO;

require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$dbRsm = require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'settings.php';
define('DB_RSM', $dbRsm);

class ItemsTableTest extends TestCase
{
    private $PDO;
    private $ItemsTable;
    private $testStatus;
    
    public function setUp() {
        $this->testStatus = "Test Status was initialized in the fixture.";
    }
    
    public function testDatabaseConnectionCanBeMade(): bool {
        try {
            $this->PDO = $this->getConnection();
            $pdoSet = isset($this->PDO);
            
            if($pdoSet) {
                $this->createTable();
                $this->populateTable();
            }
            else {
                exit("__>> RSM_ERROR - database connection can't be made! ~ItemsTablesTest.php line 38");
            }
            
            $this->assertEquals(true, $pdoSet,
                'A connection to the db should have been made'
            );
            
            echo "\n\n__>> testDatabaseConnectionCanBeMade() testStatus = {$this->testStatus}\n\n";
            return true;
        }
        catch(\Exception $e) {
            return false;
        }
    }
    
    /**
     * @depends testDatabaseConnectionCanBeMade
     *
     * @param bool $connectionSucceeded - comes from "testDatabaseConnectionCanBeMade" dependency
     *
     * @return ItemsTable
     */
    public function testAnInstanceOfItemsTableWasCreated(bool $connectionSucceeded): ItemsTable {
        $itemsTable = null;
        if($connectionSucceeded) {
            // MAKING A SECOND CONNECTION TO THE database
            $this->PDO = $this->getConnection();
            $this->ItemsTable = new ItemsTable($this->PDO);
            $itemsTable = $this->ItemsTable;
            $this->assertEquals(true, isset($this->ItemsTable),
                'an ItemsTable instance should have been created'
            );
            
            return $itemsTable;
        }
        else {
            return $itemsTable;
        }
    }
    
    public function tearDown() {
        unset($this->ItemsTable);
        unset($this->PDO);
    }
    
    /**
     * @depends testDatabaseConnectionCanBeMade
     * @depends testAnInstanceOfItemsTableWasCreated
     *
     * @param bool $connectionSucceeded - from "testDatabaseConnectionCanBeMade", dependency
     * @param ItemsTable $itemsTable - from "testAnInstanceOfItemsTableWasCreated",
     */
    public function testFindForId(bool $connectionSucceeded, ItemsTable $itemsTable = null): void {
        $id = 1;
        
        if($connectionSucceeded && $itemsTable) {
            $result = $itemsTable->findForId($id);
            $this->assertInternalType('array', $result,
                'The result should always be an array.'
            );
            $this->assertEquals($id, $result['id'],
                'The id key/value of the result for id should be equal to the id.'
            );
            $this->assertEquals('Candy', $result['name'],
                'The id key/value of the result for name should be equal to `Candy`.'
            );
        }
        else {
            exit("__>> RSM_ERROR: an ItemsTable was not able to get created ~ItemsTableTest.php line 107");
        }
    }
    
    /**
     * @param bool $connectionSucceeded - simple boolean to make sure db connection was made from this functions dependency
     *
     * @depends testDatabaseConnectionCanBeMade
     */
    public function testFindForIdMock(bool $connectionSucceeded) {
        $id = 1;
        
        if($connectionSucceeded) {
            $PDOStatement = $this->getMockBuilder('\PDOStatement')
                                 ->setMethods(['execute', 'fetch'])
                                 ->getMock();
            
            $PDOStatement->expects($this->once())
                         ->method('execute')
                         ->with([$id])
                         ->will($this->returnSelf());
            $PDOStatement->expects($this->once())
                         ->method('fetch')
                         ->with($this->anything())
                         ->will($this->returnValue('canary'));
            
            $PDO = $this->getMockBuilder('\PDO')
                        ->setMethods(['prepare'])
                        ->disableOriginalConstructor()
                        ->getMock();
            
            $PDO->expects($this->once())
                ->method('prepare')
                ->with($this->stringContains('SELECT * FROM'))
                ->willReturn($PDOStatement);
            
            $ItemsTable = new ItemsTable($PDO);
            
            $output = $ItemsTable->findForId($id);
            
            $this->assertEquals('canary', $output,
                'The output for the mocked instance of the PDO and PDOStatment should produce the string `canary`.'
            );
        }
    }
    
    protected function getConnection(): \PDO {
        $server = DB_RSM['db1']['server'];
        $dbName = DB_RSM['db1']['dbName'];
        $username = DB_RSM['db1']['user'];
        $pass = DB_RSM['db1']['pass'];
        $connectionString = "sqlsrv:server={$server};Database={$dbName};";
        echo "\n\n__>> connection string = $connectionString\n\n";
        return new \PDO($connectionString,
            $username, $pass
        );
    }
    
    protected function createTable() {
        $query = /** @lang TSQL */
            "
                CREATE TABLE items (
                    id	INTEGER,
                    name	TEXT,
                    price	REAL,
                    PRIMARY KEY(id)
                );
		    ";
        $this->PDO->query($query);
    }
    
    protected function populateTable() {
        $query = /** @lang TSQL */
            "
                INSERT INTO items VALUES (1,'Candy',1.00);
                INSERT INTO items VALUES (2,'TShirt',5.34);
		    ";
        $this->PDO->query($query);
    }
}