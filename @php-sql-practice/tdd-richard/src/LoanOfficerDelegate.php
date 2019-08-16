<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 8/28/2018
 * Time: 4:56 PM
 */


namespace Ninja;

use PHPUnit\Runner\Exception;

class LoanOfficerDelegate
{
    public $loanOfficerArr;
    public $dataArr;
    public $rawDataFile;
    public $loanOfficerFile;
    private $exportFolder;
    private $debugMode;
    
    //TODO: check for misspelled words and make better use of strpos() for core wording e.g. any field that contains a phone number should always have "number" or "phone" somewhere in the field title name
    // possible field titles
    private $loStateTitles = ['st', 'state', 'loan_officer_state'];
    // possible field titles
    private $loCountTitles = ['counts', 'loan_officer_counts', 'state_counts'];
    /**
     * These next class fields are column titles in the $loadOfficerArr
     * that are going to be used to uniquely identify loan officer info and or records
     * This is sort of kind of like the SQL "LIKE" reserved keyword
     */
    // possible field titles for the "Loan Officers' name"
    private $loColumnTitles = ['1st_drop_date', 'loan_officer', 'officer_name', 'name', 'officer'];
    // possible field titles for the "Loan Officers' numbers"
    private $loPhoneNumberTitles = ['phone_numbers', 'number', 'numbers', 'phone'];
    
    //TODO: try to use a generator instead
    
    /**
     * LoanOfficerDelegateTdd constructor transforms raw CSV data to PHP 7 arrays.
     *
     * @param string $loanOfficerPath - this is the Loan Officer Info data, This data file contains
     *      how many states each loan officer gets
     * @param string $rawDataPath - this is the raw data
     * @param string $exportFolderPath - folder to export to
     * @param bool $debugMode - output debug info for this class instance
     *
     */
    public function __construct(string $loanOfficerPath, string $rawDataPath, string $exportFolderPath, bool $debugMode) {
        $this->exportFolder = $exportFolderPath;
        $this->debugMode = $debugMode;
        
        // if glob is size 0 there was no file in the required loan officer info folder path
        $loanOfficerInfoCsvFile = isset(glob($loanOfficerPath . '\*.csv', GLOB_ERR)[0])
            ? glob($loanOfficerPath . '\*.csv', GLOB_ERR)[0] : null;
        
        if($loanOfficerInfoCsvFile) {
            $this->loanOfficerFile = $loanOfficerInfoCsvFile;
        }
        else {
            exit("\n\n__>> RSM_ERROR - There wasn't a loan officer info csv file, exiting program"
                . "\n\t - LoanOfficerDelegateTdd.php line 64 ish\n\n");
        }
        
        $rawDataCsvFile = isset(glob($rawDataPath . '\*.csv', GLOB_ERR)[0])
            ? glob($rawDataPath . '\*.csv', GLOB_ERR)[0] : null;
        
        if($rawDataCsvFile) {
            $this->rawDataFile = glob($rawDataPath . '\*.csv', GLOB_ERR)[0];
        }
        else {
            exit("\n\n__>> RSM_ERROR - There wasn't a raw data csv file, exiting program"
                . "\n\t - LoanOfficerDelegateTdd.php line 64 ish\n\n");
        }
    }
    
    // create the $loanOfficerInfoAr from CSV
    public function loanOfficerInfoCsvTransform(): bool {
        $count = 0;
        
        $loanOfficerHandle = fopen($this->loanOfficerFile, 'r');
        
        if($loanOfficerHandle !== false) {
            while(($loanOfficerData = fgetcsv($loanOfficerHandle, 8096, ",")) !== false) {
                $this->loanOfficerArr[$count] = $loanOfficerData;
                $count++;
            }
            
            //-- Close file stream:
            fclose($loanOfficerHandle);
            return true;
        }
        else {
            return false;
        }
    }
    
    // create $rawDataAr from CSV
    public function rawDataCsvTransform(): bool {
        $count = 0;
        
        if(($dataHandle = fopen($this->rawDataFile, 'r')) !== false) {
            while(($dataData = fgetcsv($dataHandle, 8096, ",")) !== false) {
                $this->dataArr[$count] = $dataData;
                $count++;
            }
            
            //-- Close file stream handle:
            fclose($dataHandle);
            return true;
        }
        else {
            return false;
        }
    }
    
    //-- Main Container Function:
    public function runLoanOfficerDelegate(): bool {
        $loanOfficerInfo = $this->createLoanOfficerInfoArr();
        try {
            if(!is_array($loanOfficerInfo))
                throw new \Exception("RSM_ERROR - var loanOfficerInfo is NOT an array,"
                    . "\n\t- Something broke. \n\t- LoanOfficerDelegateTdd.php line 82 ish\n\n");
            $this->rawDataIntegrate($loanOfficerInfo);
            
            // private function
            $this->export2csv();
            
            return true;
        } catch(\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
    
    //TODO: refactor this function into several smaller functions to write better unit tests
    private function createLoanOfficerInfoArr(): array {
        $loNameIndex = null; // index of loan officers' name
        $loNumberIndex = null; // index of loan officers' number
        $loStateIndex = null; // index of state
        $loCountIndex = null; // index of the count per state
        
        $loanOfficersNew = [];
        // create a way to uniquely identify loan officers
        $titles = $this->loanOfficerArr[0];
        // normalize titles
        $titles = array_map(function($e) {
            // Change: "  Some  titLe   like thIs " to "some_title_like_this"
            return preg_replace("/\s/", "_", trim(strtolower($e)));
        }, $titles);
        
        //TODO: try to refactor this to do a recursive search instead
        // Do an iterative search for the indexes of The Loan Officer
        // 1) Name,  2) Number, 3) State, 4) Counts
        // by checking to see if column title is in "$this->loColumnTitles"
        for($i = 0; $i < count($titles); $i++) {
            $item = $titles[$i];
            // is this field probably the loan officers name?
            if(in_array($item, $this->loColumnTitles)) {
                $loNameIndex = $i; // this Assumes an indexed array...
                if($loNumberIndex && $loStateIndex && $loCountIndex) break; // found everything, stop loop
            }
            // is this field probably the loan officers number?
            else if(in_array($item, $this->loPhoneNumberTitles)) {
                $loNumberIndex = $i;
                if($loNameIndex & $loStateIndex && $loCountIndex) break; // found everything, stop loop
            }
            else if(in_array($item, $this->loStateTitles)) {
                $loStateIndex = $i;
                if($loNumberIndex && $loNameIndex && $loCountIndex) break; // found everything, stop loop
            }
            else if(in_array($item, $this->loCountTitles)) {
                $loCountIndex = $i;
                if($loNameIndex && $loNumberIndex && $loStateIndex) break;
            }
        }
        
        // NOTE: this is NOT depending on Column Order, it dynamically finds the correct idx
        if(in_array($titles[$loNameIndex], $this->loColumnTitles)) {
            $tempArr = $this->loanOfficerArr;
            array_shift($tempArr);
            
            $loanOfficers = array_column($tempArr, $loNameIndex);
            $loanOfficersNumber = array_column($tempArr, $loNumberIndex);
            $loanOfficers = array_map(function($e) { return trim($e); }, $loanOfficers);
            $loanOfficersNumber = array_map(function($e) { return trim($e); }, $loanOfficersNumber);
            $loanOfficers = array_unique($loanOfficers);
            $loanOfficersNumber = array_unique($loanOfficersNumber);
            
            //TODO: thoroughly check this, THIS MAY BE A FLAW with weird edge cases
            // $loanOfficers and $loanOfficersNumber have same number of elements & the same indexes
            foreach($loanOfficersNumber as $i => $value) {
                $itemNumber = $loanOfficersNumber[$i];
                $itemName = $loanOfficers[$i];
                $loanOfficers[$i] = $itemName . "_" . $itemNumber;
            }
            
            if($this->debugMode) {
                echo "\n\rbreakpoint\n\r";
            }
            
            // At this point $loanOfficers = [2 => 'foo', 7 => 'bar', 26 => 'baz']
            // so transform arr keys to be loan officer name
            $loanOfficersNew = array_flip($loanOfficers);
            
            // now make key same as name
            foreach($loanOfficers as $key => $value) {
                $loanOfficersNew[$value] = ['id' => $value];
            }
            
            // now iterate over the 30+ recs (from orig "loan officer info" csv file)
            // start $i = 1 because 0 are column titles
            for($i = 1; $i < count($this->loanOfficerArr); $i++) {
                // $item will be a row
                $item = $this->loanOfficerArr[$i];
                
                //TODO: Figure out how to NOT do this inner loop >:\
                foreach($loanOfficersNew as $key => $value) {
                    $name = strstr($value['id'], "_", true);
                    $number = str_replace("_", "", strstr($value['id'], "_"));
                    if(
                        (strpos($item[$loNameIndex], $name) !== false) &&
                        (strpos($item[$loNumberIndex], $number) !== false)
                    ) {
                        $loanOfficersNew[$value['id']] [] = ['state' => $item[$loStateIndex],
                            'count' => (int)$item[$loCountIndex],
                            'currentCount' => 0, // prep it for integration with actual raw data file
                        ];
                    }
                    if($this->debugMode) {
                        echo "\n\rbreakpoint\n\r";
                    }
                }
            }
            
            if($this->debugMode) {
                echo "\n\rbreakpoint\n\r";
            }
        }
        
        return $loanOfficersNew;
    }
    
    // This is "HARDCODED" - it depends on order column to be correct
    private function rawDataIntegrate(array $loanOfficerInfo): void {
        // add a new field to header row
        $this->dataArr[0][count($this->dataArr[0])] = 'loan_officer';
        
        // ----------- OUTER loop -----------
        foreach($loanOfficerInfo as $key => $value) {
            // ----------- 1st inner loop -----------
            // loop over the 30,000+ records from raw data
            for($row = 1; $row < count($this->dataArr); $row++) {
                $record = $this->dataArr[$row];
                $headerRowSize = count($this->dataArr[0]);
                //===================================
                // << HARD_CODED >> 5 = state column
                //===================================
                $rdState = $record[5]; // rd = raw data
                $rdLoanOfficer = isset($this->dataArr[$row][$headerRowSize])
                    ? $this->dataArr[$row][$headerRowSize] : null;
                
                // ----------- 2nd inner loop -----------
                // loop over the loan officers' custom data structure
                // ... minus 1 because one of the keys isn't an index
                for($i = 0; $i < (count($loanOfficerInfo[$key]) - 1); $i++) {
                    $loState = $loanOfficerInfo[$key][$i]['state'];
                    $hitMaxCount = $loanOfficerInfo[$key][$i]['currentCount']
                        >= $loanOfficerInfo[$key][$i]['count'];
                    if(($loState === $rdState) && empty($rdLoanOfficer) && !$hitMaxCount) {
                        $this->dataArr[$row][$headerRowSize] = strstr($key, "_", true);
                        $loanOfficerInfo[$key][$i]['currentCount']++;
                        if($this->debugMode) {
                            echo "\nbreakpoint - LoanOfficerDelegate.php line 202, looping over loan officers' data structure\n";
                        }
                    }
                }
                
            } // END OF for() loop
            
            if($this->debugMode) {
                echo "\nbreakpoint - LoanOfficerDelegate.php line 210, transitioning to next loanOfficerInfo key\n";
            }
        } // END OF foreach() loop
    }
    
    // Delete files in folders AND export dataArr to CSV
    private function export2csv(): void {
        $path = $this->exportFolder . '\\completed_' . basename($this->rawDataFile);
        if(($handle = fopen($path, 'w')) !== false) {
            foreach($this->dataArr as $row) {
                fputcsv($handle, $row);
            }
            
            //-- Close file stream:
            fclose($handle);
        }
        
        echo "<h1>File located at </h1><p><code>{$path}</code></p>";
        
        unlink($this->rawDataFile);
        unlink($this->loanOfficerFile);
    }
    
} // END OF: class LoadOfficerDelegateTdd {}