<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 10/24/2018
 * Time: 1:59 PM
 */


define("RSM_PRODUCTION_ENV", false);
define("RSM_DEBUG_MODE", false);

use Rsm\ElevateRecoveries\ElevateRecoveries;
use Rsm\ElevateRecoveries\ElrCsvParseModel;

// for now manually require interfaces rather than autoload them
require __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'interfaces' . DIRECTORY_SEPARATOR . 'ElevateRecoveriesInterface.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'interfaces' . DIRECTORY_SEPARATOR . 'ElrCsvParseModelInterface.php';
// for now manually require classes rather than autoload them
require __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'ElrCsvParseModel.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'ElevateRecoveries.php';

//-- primary script vars:
$csvFullPath = "";
$csvExportPath = "";
//-- helper variables:
$localPath2glob = "C:\\xampp\htdocs\\ninja\_elevate-recoveries\ci";
$productionPath2glob = "";
$localExportPath = "C:\\xampp\htdocs\\ninja\_elevate-recoveries\co";
$productionExportPath = "";

// ABSOLUTE PATH to csv file:
if(RSM_PRODUCTION_ENV) {
    //TODO: make sure this program deletes csv files in the ci folder after it completes
    $csvFullPath = $productionPath2glob;
    $csvExportPath = $productionExportPath;
}
else {
    $csvFullPath = $localPath2glob;
    $csvExportPath = $localExportPath;
}

function invokeElevateRecoveriesAlgorithm($csvFullPath, $csvExportPath): void {
    $rawCsvArr = ElrCsvParseModel::csv2array($csvFullPath);
    $elevateRecoveries = new ElevateRecoveries($rawCsvArr);
    $elevatedArr = $elevateRecoveries->elevate();
    ElrCsvParseModel::export2csv($elevatedArr, $csvExportPath);
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Elevate Recoveries Algorithm</title>
</head>
<body>

<h1>"Elevate Recoveries Data Algorithm version 0.1"</h1>

</body>
</html>




