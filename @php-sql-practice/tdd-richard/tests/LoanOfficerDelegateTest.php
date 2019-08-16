<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 9/13/2018
 * Time: 11:17 AM
 */

require __DIR__ . DIRECTORY_SEPARATOR . '..\vendor\autoload.php';

use Ninja\LoanOfficerDelegate;
use PHPUnit\Framework\TestCase;


class LoanOfficerDelegateTest extends TestCase
{
    private $testPathForLoanOfficerInfo = 'C:\xampp\htdocs\php-sql\tdd-richard\loanOfficersInfo';
    private $testPathForRawData = 'C:\xampp\htdocs\php-sql\tdd-richard\loanOfficersRawData';
    private $testPathForExportFolder = 'C:\xampp\htdocs\php-sql\tdd-richard\loanOfficerComplete';
    private $LoanOfficerDelegate;
    
    public function setUp() {
        $this->LoanOfficerDelegate = new LoanOfficerDelegate(
            $this->testPathForLoanOfficerInfo,
            $this->testPathForRawData,
            $this->testPathForExportFolder,
            false
        );
    }
    
    public function tearDown() {
        unset($this->LoanOfficerDelegate);
    }
    
    /**
     * Call protected/private method of a class.
     * Got this function from:
     * https://jtreminio.com/blog/unit-testing-tutorial-part-iii-testing-protected-private-methods-coverage-reports-and-crap/
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     * @throws \ReflectionException
     * @return mixed Method return
     */
    public function invokeMethod(&$object, $methodName, array $parameters = []) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
    
    /**
     * @covers \Ninja\LoanOfficerDelegate::__construct
     */
    public function testCsvFilesExistInFolders() {
        $fileRawData = glob($this->testPathForRawData . '\*.csv', GLOB_ERR);
        if(isset($fileRawData) && (count($fileRawData) > 0) && ($fileRawData !== false)) {
            $this->assertFileIsReadable(
                $fileRawData[0],
                "RSM_TEST - The test wasn't able to open the file at {$this->testPathForRawData}"
                . "\n\t-LoanOfficerDelegateTest.php line 62 ish"
            );
            
            $this->assertStringMatchesFormat('%s.csv', $this->LoanOfficerDelegate->loanOfficerFile,
                "RSM_TEST - There is not a CSV file in {$this->testPathForLoanOfficerInfo}"
            );
            
            $this->assertStringMatchesFormat('%s.csv', $this->LoanOfficerDelegate->rawDataFile,
                "There is not a CSV file in {$this->testPathForRawData}"
                . "\n\t-LoanOfficerDelegateTddTest.php line 62 ish"
            );
        }
        else {
            exit ("\n\n__>> RSM_ERROR - There is no CSV file to test. Will stop Unit Testing...\n\n");
        }
    }
    
    /**
     * @covers \Ninja\LoanOfficerDelegateTdd::loanOfficerInfoCsvTransform
     *
     */
    public function testLoanOfficerInfoCsvGetsTransformedToAnArray() {
        $this->assertTrue($this->LoanOfficerDelegate->loanOfficerInfoCsvTransform(),
            "The loan officer info CSV was not transformed to an array"
        );
    }
    
    /**
     * @covers  \Ninja\LoanOfficerDelegateTdd::rawDataCsvTransform
     * @depends testLoanOfficerInfoCsvGetsTransformedToAnArray
     */
    public function testRawDataCsvGetsTransformedToAnArray() {
        $this->LoanOfficerDelegate->loanOfficerInfoCsvTransform();
        $this->assertTrue($this->LoanOfficerDelegate->rawDataCsvTransform(),
            "The raw data csv did not get transformed to an array"
        );
        $expected = 0;
        $actual = count($this->LoanOfficerDelegate->loanOfficerArr);
        $message = 'The loan officer arr is supposed to be greater than 0';
        $this->assertGreaterThan($expected, $actual, $message);
    }
    
    /**
     * Get the header row from the loan officers info CSV
     *
     * @covers \Ninja\LoanOfficerDelegateTdd::loanOfficerInfoCsvTransform
     */
    public function testLoanOfficerTitlesAreCorrect() {
        $this->LoanOfficerDelegate->loanOfficerInfoCsvTransform();
        //$this->markTestIncomplete('incomplete');
        $expected = ['1st Drop Date', 'state', 'Counts', 'Phone Numbers'];
        $firstDropDate = trim($this->LoanOfficerDelegate->loanOfficerArr[0][0]);
        $state = $this->LoanOfficerDelegate->loanOfficerArr[0][1];
        $counts = $this->LoanOfficerDelegate->loanOfficerArr[0][2];
        $phoneNumbers = $this->LoanOfficerDelegate->loanOfficerArr[0][3];
        $actual = [$firstDropDate, $state, $counts, $phoneNumbers];
//        echo "\n\n\n Header Row values =\n";
//        var_dump($actual);
//        echo "\n\n\n";
        
        $message = 'The header row does not have the correct fields titles and or the correct column order';
        $this->assertEquals($expected, $actual, $message);
    }
    
    /**
     *
     */
//    public function testExceptionIsRaisedIfExportFolderDoesNotExists() {
//        $this->markTestIncomplete('incomplete');
//    }

//    public function testProgramHasStartedWithNoErrors() {
//        $this->markTestIncomplete('incomplete');
//
//        $this->assertTrue($this->LoanOfficerDelegate->runLoanOfficerDelegate(),
//            "\n\n__>> The main container function for 'class LoanOfficerDelegate{}' failed.\n\n"
//        );
//    }

//    public function testFilesHaveBeenDeletedAfterProgramCompletion() {
//        $this->markTestIncomplete('incomplete');
//    }

//    public function testArrayHasBeenConvertedToCsv() {
//        $this->markTestIncomplete('incomplete');
//    }

//    public function testCsvHasBeenConvertedToAnArray() {
//        $this->markTestIncomplete('incomplete');
//    }
    
} // END OF: class LoanOfficerDelegateTddTest{}