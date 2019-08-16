<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 10/24/2018
 * Time: 4:00 PM
 */

namespace Rsm\ElevateRecoveries;

class ElevateRecoveries implements ElevateRecoveriesInterface
{
    private $rawDataArr;
    private $csv = [];
    private $rawDataNoHeader;
    private $rawDataHeaderRow;
    private $headerRowKey = '';
    // start at 1 because the appends formula will always start at 1
    private $rightShiftUpGroupAppends = 1;
    // 14 because the first 14 fields are how many fields there are originally
    // right shift up group appends additional fields to these first 14 fields
    private $baseFieldsCount = 14;
    private $numberOfFieldsToAppend = 7;
    
    public function __construct(array $rawDataArr) {
        $this->rawDataArr = $rawDataArr;
        $this->rawDataHeaderRow = array_shift($rawDataArr);
        $this->rawDataNoHeader = $rawDataArr;
    }
    
    //TODO: sort the array based on something useful
    
    public function elevate(): array {
        foreach($this->rawDataArr as $data) {
            // TODO: implement 'LIKE' algorithm for field titles !!!!!!!!!!!!!!!!!!
            // HARD_CODED [1], [2] for [Name], [Address Street 1]
            $key = $data[1] . '_' . $data[2];
            
            if(!isset($this->csv[$key])) {
                // dynamically hash map key and initialize it
                $this->csv[$key] = $data;
                if(!isset($this->csv[$this->headerRowKey])) {
                    $this->headerRowKey = $key;
                }
            }
            // else rather than overwrite hash map key value, merge more data to it
            else {
                // TODO: implement 'LIKE' algorithm for field titles !!!!!!!!!!!!!!!!!!
                $originalClientAccountNumber = $data[7]; // [Original Client Account Number] field
                $accountBalance = $data[8];              // [Account Balance] field
                $settlementRate = $data[9];              // [Settlement Rate] field
                $remarksCurrentCreditor = $data[10];     // [Remarks Current Creditor] field
                $originalCreditorName = $data[11];       // [Original Creditor Name] field
                $groupNumber = $data[12];                // [Group Number] field
                $serviceDate = $data[13];                // [Service Date] field
                
                $mergeData = [
                    $originalClientAccountNumber,
                    $accountBalance,
                    $settlementRate,
                    $remarksCurrentCreditor,
                    $originalCreditorName,
                    $groupNumber,
                    $serviceDate,
                ];
                
                $this->csv[$key] = array_merge($this->csv[$key], $mergeData);
                
                // for better unit testing, will make append tracker its' own function
                $this->trackAppends(count($this->csv[$key]));
            }
        }
        
        echo "\nPHP 7 has finished processing data.\n";
        
        // find the last
        for($i = 0; $i < $this->rightShiftUpGroupAppends; $i++) {
            // HARD_CODED [7]-[13] are the fields being appended
            $headerMergeData = [
                $this->rawDataHeaderRow[7] . ' ' . ($i + 1),
                $this->rawDataHeaderRow[8] . ' ' . ($i + 1),
                $this->rawDataHeaderRow[9] . ' ' . ($i + 1),
                $this->rawDataHeaderRow[10] . ' ' . ($i + 1),
                $this->rawDataHeaderRow[11] . ' ' . ($i + 1),
                $this->rawDataHeaderRow[12] . ' ' . ($i + 1),
                $this->rawDataHeaderRow[13] . ' ' . ($i + 1),
            ];
            
            $this->csv[$this->headerRowKey] = array_merge($this->csv[$this->headerRowKey], $headerMergeData);
            
            $break = "point";
        }
        
        return $this->csv;
    }
    
    private function trackAppends(int $arrSize): void {
        $appends = ($arrSize - $this->baseFieldsCount) / $this->numberOfFieldsToAppend;
        if($appends > $this->rightShiftUpGroupAppends) {
            $this->rightShiftUpGroupAppends++;
        }
    }
    
    public function elevate_copy() {
        $countZeroGroups = 0;
        foreach($this->rawDataArr as $data) {
            // TODO: implement 'LIKE' algorithm for field titles !!!!!!!!!!!!!!!!!!
            // HARD_CODED [1], [2] for [Name], [Address Street 1]
            $key = $data[1] . '_' . $data[2];
            
            if(!isset($this->csv[$key])) {
                $count = 0;
                $this->csv[$key] = $data;
                // the following if block just sets the count to 0 so that php
                // knows how many records were "right shift up grouped"
                if(!isset($this->csv[$this->headerRowKey])) {
                    $this->headerRowKey = $key;
                    // put a tracker at start of array to count number of appends
                    array_unshift($this->csv[$key], $this->rightShiftUpGroupAppends);
                }
            }
            else if(isset($this->csv[$key])) {
                // TODO: implement 'LIKE' algorithm for field titles !!!!!!!!!!!!!!!!!!
                $originalClientAccountNumber = $data[7]; // [Original Client Account Number] field
                $accountBalance = $data[8]; // [Account Balance] field
                $settlementRate = $data[9]; // [Settlement Rate] field
                $remarksCurrentCreditor = $data[10]; // [Remarks Current Creditor] field
                $originalCreditorName = $data[11]; // [Original Creditor Name] field
                $groupNumber = $data[12]; // [Group Number] field
                $serviceDate = $data[13]; // [Service Date] field
                $wantedMergeData = [
                    $originalClientAccountNumber,
                    $accountBalance,
                    $settlementRate,
                    $remarksCurrentCreditor,
                    $originalCreditorName,
                    $groupNumber,
                    $serviceDate,
                ];
                
                $this->csv[$key] = array_merge($this->csv[$key], $wantedMergeData);
                $this->rightShiftUpGroupAppends++;
                var_dump($this->csv[$key]);
            }
            else {
                // TODO: implement 'LIKE' algorithm for field titles !!!!!!!!!!!!!!!!!!
                $originalClientAccountNumber = $data[7]; // [Original Client Account Number] field
                $accountBalance = $data[8]; // [Account Balance] field
                $settlementRate = $data[9]; // [Settlement Rate] field
                $remarksCurrentCreditor = $data[10]; // [Remarks Current Creditor] field
                $originalCreditorName = $data[11]; // [Original Creditor Name] field
                $groupNumber = $data[12]; // [Group Number] field
                $serviceDate = $data[13]; // [Service Date] field
                $wantedMergeData = [
                    $originalClientAccountNumber,
                    $accountBalance,
                    $settlementRate,
                    $remarksCurrentCreditor,
                    $originalCreditorName,
                    $groupNumber,
                    $serviceDate,
                ];
                
                $this->csv[$key] = array_merge($this->csv[$key], $wantedMergeData);
                $this->rightShiftUpGroupAppends++;
                var_dump($this->csv[$key]);
            }
        }
        echo "\nPHP 7 has finished processing data.\n";
    }
}