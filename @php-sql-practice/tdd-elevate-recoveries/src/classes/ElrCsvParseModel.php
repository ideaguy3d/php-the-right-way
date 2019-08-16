<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 10/24/2018
 * Time: 3:56 PM
 */

namespace Rsm\ElevateRecoveries;

class ElrCsvParseModel implements ElrCsvParseModelInterface
{
    public function __construct() { }
    
    public static function csv2array(string $path): array {
        $csvFileFolder = glob("$path/*.csv");
        $csvFile = $csvFileFolder[0];
        $csv = [];
        $count = 0;
        
        if(($handle = fopen($csvFile, 'r')) !== false) {
            while(($data = fgetcsv($handle, 8096, ",")) !== false) {
                $csv[$count] = $data;
                ++$count;
            }
            fclose($handle);
        }
        return $csv;
    }
    
    public static function export2csv(array $dataSet, string $exportPath): void {
        $exportPath = $exportPath . DIRECTORY_SEPARATOR . 'elevateResource.csv';
        $outputFile = fopen($exportPath, 'w') or exit("mheta - unable to open $exportPath");
        foreach($dataSet as $value) {
            fputcsv($outputFile, $value);
        }
        fclose($outputFile);
    }
}