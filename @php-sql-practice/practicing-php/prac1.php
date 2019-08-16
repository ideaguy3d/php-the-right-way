<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 4/23/2018
 * Time: 8:52 PM
 */


echo "<h1>Lab916</h1><hr>";

// print_r(labConversion1());
function labConversion1() {
    $columns = [
        'id serial PRIMARY KEY ',
        'title VARCHAR(255)',
        'author VARCHAR(255)',
        'published_date VARCHAR(255)',
        'image_url VARCHAR(255)',
        'description VARCHAR(255)',
        'created_by VARCHAR(255)',
        'created_by_id VARCHAR(255)',
        'currently_selling VARCHAR(255)'
    ];

    $labMapF = function ($colDef) {
        return explode(' ', $colDef)[0];
    };

    $colNames = array_map($labMapF, $columns);

    $colText = implode(", ", $columns);

    echo "<br><br> column text = <br> $colText <br><br>";

    return $colNames;
}


scrapeOne();
function scrapeOne() {
    $ad1 = file_get_contents("http://lab916.wpengine.com/mws/src/MarketplaceWebService/api/report1.php");
    $explode1 = explode('<h2>Report Contents</h2>', $ad1);
    $explode2b = explode('  ', $explode1[1]);

    // ---------- $rows does work ----------
    $rows = explode("\n", $explode2b[0]);

    $amazonRowsFba = [];
    for ($i=0; $i<count($rows); $i++) {
        $row = preg_replace('/\t/', '|', $rows[$i]);
        $rowArray = explode('|', $row);
        $amazonRowsFba[$i] = $rowArray;
    }

    $amazonRowsFbaClean = []; $idx = 0;
    for ($i = 0; $i<count($amazonRowsFba); $i++) {
        $tempA = array();
        foreach ($amazonRowsFba[$i] as $record) {
            if(str_word_count($record) > 0) {
                $tempA[$idx] = $record;
            }
            $idx++;
        }
        $amazonRowsFbaClean[$i] = $tempA;
        $idx = 0;
    }

    echo "\n\n Cleaned up Amazon rows\n";
    print_r($amazonRowsFbaClean);


    $columnCellsRow1 = preg_replace('/\t/', '|', $rows[1]);
    $columnCellsRowVals1 = explode('|', $columnCellsRow1);
    $count = 0;
    $columnCellsRowClean1 = [];
    foreach ($columnCellsRowVals1 as $item) {
        if (str_word_count($item) > 0) {
            $columnCellsRowClean1[$count] = $item;
        }
        $count++;
    }
}