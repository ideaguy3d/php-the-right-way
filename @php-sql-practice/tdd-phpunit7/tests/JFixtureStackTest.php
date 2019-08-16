<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 9/12/2018
 * Time: 5:57 PM
 */

use PHPUnit\Framework\TestCase;
use TDD\JCsvFileIterator;

class JFixtureStackTest extends TestCase
{
    protected $stack;
    protected static $pdo;
    
//    public static function setUpBeforeClass() {
//        $config = require __DIR__ . '\..\config\settings.php';
//        $db = $config['dbNinja'];
//
//        self::$pdo = new PDO(
//            "sqlsrv:Database={$db['dbname']};server={$db['host']}",
//            $db['user'], $db['pass']
//        );
//    }
    
    public function setUp() {
        $this->stack = [];
    }
    
    public function testPush() {
        array_push($this->stack, 'ninja');
        $this->assertSame('ninja',
            $this->stack[count($this->stack) - 1]
        );
        $this->assertFalse(empty($this->stack));
    }
    
    public function testPop() {
        array_push($this->stack, 'ninja');
        $this->assertSame('ninja',
            array_pop($this->stack)
        );
        $this->assertTrue(empty($this->stack));
    }
    
    /**
     * @dataProvider additionProvider
     * @param int $a - an operand
     * @param int $b - an operand
     * @param int $expected - the expected result
     */
    public function testAdd(int $a, int $b, int $expected) {
        $this->assertSame($expected, $a + $b);
    }
    
    public function additionProvider() {
        return new JCsvFileIterator('jData.csv');
    }
    
    public function provider() {
        return [
            ['provider1'],
        ];
    }
    
    public function testProducerOne() {
        $this->assertTrue(true);
        return 'first';
    }
    
    public function testProducerTwo() {
        $this->assertTrue(true);
        return 'second';
    }
    
    /**
     * @dataProvider provider
     * @depends      testProducerOne
     * @depends      testProducerTwo
     */
    public function testConsumer() {
        $this->assertSame(
            ['provider1', 'first', 'second'],
            func_get_args()
        );
    }
    
    /**
     * @dataProvider additionWithNegativeNumbers
     * @dataProvider additionWithNonNegativeNumbers
     * @param int $a
     * @param int $b
     * @param int $expected
     */
    public function testAddMultiple(int $a, int $b, int $expected) {
        $this->assertSame($expected, $a + $b);
    }
    
    public function additionWithNonNegativeNumbers() {
        return [
            [1, 41, 42],
            [3, 9, 12],
            [156, 50, 206],
        ];
    }
    
    public function additionWithNegativeNumbers() {
        return [
            [-89, -5, -94],
            [-20, -9, -29],
            [-1, 1, 0],
        ];
    }
    
//    public static function tearDownAfterClass() {
//        self::$pdo = null;
//    }
}