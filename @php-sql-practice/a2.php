<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: julius
 * Date: 1/31/2019
 * Time: 3:27 PM
 */

namespace Ninja\Auto;

use Ninja\Auto\Interfaces\ICommissionAutoModel;


DEFINE("rsm_uncomputable", ['status' => 'uncomputable', 'incomplete_info' => 'Incomplete Info']);

class CommissionAutoModel implements ICommissionAutoModel
{
    private $jobBoardData;
    private $dbJobBoard;
    private $dbComAuto;
    private $hashGroup;
    private $commissionClientSums;
    private $commissionSalesRepSums;
    private $mainLoopIdxTracker;
    private $jobTypeTrack;
    private $jobId;
    private $reqInkjet;
    private $indexFor;
    private $headerRow;
    private $precision;
    private $queryParams;
    private $_curRecord;
    
    private $emptyMandatoryFields = [];
    private $pricePerClickSummary = [];
    private $uncomputableJobs = [];
    private $tableJobBoardDataMash = '[_JobBoardDataMash]';
    private $tableClientsPricing = '[ClientsPricing]';
    private $tableComAutoOutput = '[_CommAuto_Output]';
    
    // HARD CODED - this field should eventually be variable
    private $comPct = 0.1;
    
    public function __construct(
        array $jobBoardData, $dbJobBoard, $dbComAuto, $queryParams
    ) {
        // CSV data
        $this->jobBoardData = $jobBoardData;
        $this->headerRow = $this->jobBoardData[0];
        
        // SQL server db's
        $this->dbJobBoard = $dbJobBoard;
        $this->dbComAuto = $dbComAuto;
        
        $this->queryParams = $queryParams;
    }
    
    public function findColumnOrder(): array {
        // variable initialization
        $indexesFound /** assoc.arr **/ = [];
        $justFindParticularIndexes = false;
        
        // the else statement will probably never happen
        if(!$justFindParticularIndexes) {
            // Dynamically find the column order
            //TODO: make an exception handler in case any field indexes can't be found !! SERIOUSLY !!!!!!!!
            for($i = 0; $i < count($this->headerRow); $i++) {
                $field = $this->headerRow[$i];
                $indexesFound[$field] = array_search($field, $this->headerRow);
            }
        }
        else {
            // These are the literal field titles from header row from the downloaded Job Board CSV
            $indexes2find = [
                'job_id', 'export_coordinator', 'qty', 'postage_amt', 'total', 'jobtype', 'papertype', 'paper_color',
                'envelopetype', 'envelopepaper', 'color', 'num_inserts', 'req_inkjet', 'snap_seal', 'snap_paper',
                'dist_type', 'purls', 'variable_streetview', 'export_sales', 'export_company_name', 'invoice', 'export_duedate',
            ];
            
            // Dynamically find the column order
            //TODO: make an exception handler in case any field indexes can't be found !! SERIOUSLY !!!!!!!!
            for($i = 0; $i < count($indexes2find); $i++) {
                $field = $indexes2find[$i];
                $indexesFound[$field] = array_search($field, $this->headerRow);
            }
        }
        
        $this->indexFor = $indexesFound;
        
        return $indexesFound;
    }
    
    //-- THEEE CONTAINER FUNCTION --\\
    public function computeCommissions(): array {
        // variable declaration
        $csvName = null;
        // variable initialization
        $comData = ['individual_sales' => []];
        $path2folder = AppGlobals::PathToCommissionCsvFolder();
        
        //-- Add 2 more fields to header row:
        $this->jobBoardData[0][count($this->jobBoardData[0])] = 'cost';
        $this->jobBoardData[0][count($this->jobBoardData[0])] = 'commission';
        
        // SUPER SUPER IMPORTANT !!
        //-- This variable is the result of the whole comauto program:
        $computedCommission /* assoc.array */ = $this->reconstructAndCompute();
        
        // Export each sales rep csv
        foreach($computedCommission['sales_rep'] as $key => $value) {
            $comData['individual_sales'][$key] = $value;
            // $key = "sales rep", $value = "multiple arrays"
            CsvParseModel::export2csv($value, $path2folder, $key);
        }
        
        $groupByClient = $this->commissionClientSums = $this->groupCommissions($computedCommission['client'], 'client');
        $groupBySalesRep = $this->commissionSalesRepSums = $this->groupCommissions($computedCommission['sales_rep'], 'sales_rep');
        
        $comData['clients'] = $groupByClient;
        $comData['sales_reps'] = $groupBySalesRep;
        $comData['uncomputables'] = $this->uncomputableJobs;
        
        // Add the header row to the Uncomputables array
        array_unshift($comData['uncomputables'], [
            'job_id' => 'job_id',
            'client_name' => 'client_name',
            'job_name' => 'job_name',
            'reason' => 'reason',
            'info' => 'info',
            'additional_info' => 'additional_info',
            'sales_rep' => 'sales_rep',
        ]);
        
        CsvParseModel::export2csv($comData['uncomputables'], $path2folder, 'Uncomputable');
        
        // This data gets sent to AngularJS.
        return $comData;
        
    } // END OF: computeCommissions()
    
    // Track which coordinators are not inputting mandatory fields
    public function emptyFieldTracker(
        string $fieldTitle, string $issue, string $fieldValue, int $jobId, string $coordName
    ): void {
        $this->emptyMandatoryFields [] = [
            'field_title' => $fieldTitle, 'issue' => $issue, 'field_value' => $fieldValue,
            'job_id' => $jobId, 'coordinator_name' => $coordName,
        ];
    }
    
    // The sort of "engine" to this program, may refactor to several smaller functions
    public function determinePricePerClick(
        int $jobId, string $jobType, string $paperType, string $paperColor, int $jobSize, string $envelopePaper,
        string $colorAndSides, int $numInserts, string $coordName, int $inkjet, string $snapSeal, string $snapPaper,
        string $distType, int $purls, int $streetView, string $mailSize, string $postageType, array $complexReport
    ): array {
        
        // sanitize/normalize raw value for job type
        $jobType = strtolower(trim($jobType));
        $rawColor = $colorAndSides;
        
        // _DEBUG jobId
        if($this->jobId === 65590) {
            $break = 'point';
        }
        
        // extract color and sides from [color]
        if(!empty($colorAndSides)) {
            $colorAndSides /* as.ar */ = $this->determineColorAndSides($colorAndSides);
        }
        else {
            $fieldTitle = 'color';
            $issue = 'coordinator did not enter [color]';
            $this->emptyFieldTracker($fieldTitle, $issue, $colorAndSides, $jobId, $coordName);
        }
        
        #region - Variable declarations and initializations -
        // variable declarations
        $pricePerClick = null;
        $mockPrice = null;
        $baseCost = null;
        
        // variable initializations
        $setupFeePostalPrep = 25;
        $setupFeeOversizedEnvelope = 0.0;
        $mockPricePerClick = false;
        $threshold = 20000; // 20,000 standard letter
        $thresholdAddrStampStation = 5000; // 5,000
        
        // base costs
        $baseAddrStampStation = 0.015;
        $baseAddrStampStationUnder5k = $baseAddrStampStation + 0.005;
        $baseLetterCost = 0.06;
        $baseLetterCostUnder20k = $baseLetterCost + 0.005;
        $baseSnapPackCost = 0.05;
        $baseSnapPackCostUnder20k = $baseSnapPackCost + 0.01;
        
        // additional costs
        $expensiveEnvelopeCost = 0.005;
        $expensivePaperCost = 0.005;
        $inkjetAddressCost = 0.02;
        $overSizedEnvelopeCost = 0.01;
        $digitalColorPrintingCost = 0.03;
        $digitalColor2ndSideBlackCost = 0.01;
        $digitalColor2ndSideColorCost = 0.03;
        $additionalInsertCost = 0.005;
        $pressureSealCost = 0.01;
        $windowCheckSnapCost = 0.02;
        $purlCost = 0.01;
        $streetViewCost = 0.035;
        $inkjetAddrUpperCost = 0.02;
        $inkjetAddrLowerCost = 0.015;
        $precanceledStampInlineCost = 0.01;
        $precanceledStampOfflineCost = 0.005;
        
        // shipping
        $pmodCost = 0.07;
        $dropShipCost = 0.015;
        
        // set up fees
        $overSizedEnvelopeSetupFee = 25.0;
        $inkjetAddrSetupFee = 50.0;
        #endregion
        
        // _DEBUG - set jobId in main loop
        if($this->jobId === 63854) {
            $break = "point";
        }
        
        /*******************************************************
         ************ Base Cost "EXPRESSION TREE" *************
         * job types = letter, post card, print / other, self mailer, snap pack *
         *******************************************************/
        if($jobType === 'letter' && $jobSize > $threshold) {
            $baseCost = $pricePerClick = $baseLetterCost;
        }
        else if($jobType === 'letter' && $jobSize < $threshold) {
            $baseCost = $pricePerClick = $baseLetterCostUnder20k;
        }
        else if($jobType === 'snap pack' && $jobSize > $threshold) {
            $baseCost = $pricePerClick = $baseSnapPackCost;
        }
        else if($jobType === 'snap pack' && $jobSize < $threshold) {
            $baseCost = $pricePerClick = $baseSnapPackCostUnder20k;
        }
        else if($jobType === 'post card') {
            $baseCost = $pricePerClick = 0.0;
        }
        else {
            exit("__>> RSM ERROR - A base cost could not be determined ~ CommissionAuto.php line 133 ish");
        }
        
        /*****************************************************
         ******** Additional Cost "EXPRESSION TREE" ********
         ****************************************************/
        // Standard Letter functions
        if($jobType === 'letter') {
            //++ 2 _ADDITIONAL_COST "additional insert":
            $this->calcAdditionalInsert($pricePerClick, $numInserts, $additionalInsertCost);
            
            //++ 3 _ADDITIONAL_COST "expensive envelope":
            $this->calcExpenseEnvelope($pricePerClick, $envelopePaper, $expensiveEnvelopeCost, $jobId, $coordName);
            
            //++ 4 _ADDITIONAL_COST "expensive paper"
            $this->calcExpensePaper($pricePerClick, $paperType, $expensivePaperCost, $paperColor);
            
            //++ 5 _ADDITIONAL_COST "inkjet variable address"
            $this->calcInkjetVariable($pricePerClick, $inkjet, $inkjetAddressCost);
            
            //++ 6 _ADDITIONAL_COST "inserting over sized envelope"
            $this->calcOverSizedEnvelope(
                $pricePerClick, $setupFeeOversizedEnvelope, $overSizedEnvelopeCost,
                $overSizedEnvelopeSetupFee, $envelopePaper, $jobId, $coordName
            );
            
            if(gettype($colorAndSides) === 'string') {
                $break = 'point';
            }
            //++ 7 _ADDITIONAL_COST "digital color printing"
            $this->calcDigitalColor(
                $pricePerClick, $digitalColorPrintingCost, $digitalColor2ndSideBlackCost,
                $colorAndSides, $digitalColor2ndSideColorCost
            );
        }
        // Snap Pack functions
        else if($jobType === 'snap pack') {
            //++ 8 _ADDITIONAL_COST "pressure seal form"
            $this->calcPressureSeal($pricePerClick, $pressureSealCost, $snapSeal, $snapPaper);
            
            //++ 9 _ADDITIONAL_COST "window check snap pressure seal form"
            $this->calcWindowCheck($pricePerClick, $windowCheckSnapCost, $snapSeal, $snapPaper);
        }
        // Post Card
        else if($jobType === 'post card') {
            $this->calcPostCardPriceMatrix(
                $pricePerClick, $mockPricePerClick, $mailSize, $paperType, $colorAndSides,
                $rawColor
            );
        }
        
        // Compute "Addressing and Stamping Station" pricing
        /*
            INKJET
            [inkjet] Boolean = true and [qty] <=5000 then .020 each
            [inkjet] Boolean = true and [qty] >5000 then .015 each
            
            STAMPS
            [inkjet] Boolean = false and [Postage Type] = 'Stamps' then add.010 each
            [inkjet] Boolean = true and [Postage Type] = 'Stamps' then add.005 each
            
            POSTCARDS
            4/4
            4/1
            1/1
            -.01 if customer provided shell
        */
        if($inkjet === 1) {
            $this->calcInkjetAddr(
                $pricePerClick, $inkjetAddrUpperCost, $inkjetAddrLowerCost, $jobSize,
                $thresholdAddrStampStation, $jobType, $inkjetAddrSetupFee
            );
        }
        else if($postageType === 'stamp') {
            $this->calcPrecanceledStamps(
                $pricePerClick, $precanceledStampInlineCost, $precanceledStampOfflineCost, $inkjet
            );
        }
        
        //TODO: Write an exception handler to deal with $pricePerClick being null.
        if($pricePerClick === null) {
            $break = 'point';
            return ['error' => 'cost per click is null ~ CommissionAutoModel.php line 524 ish'];
        }
        
        //-- Invoke General Purpose functions:
        
        //++ _CORE_COST "drop ship and pmod" ~ ComAuto for now is not going to calc pmod & drop ship
        //$this->calcShipping($pricePerClick, $pmodCost, $dropShipCost, $distType); // <- IMPORTANT!
        //++ 11 _ADDITIONAL_COST "purl and variable image"
        $this->calcPurlVarImg($pricePerClick, $purlCost, $streetViewCost, $purls, $streetView, $jobSize);
        
        $this->pricePerClickSummary['$BASE_COST'] = $baseCost;
        $this->pricePerClickSummary['$PRICE_PER_CLICK'] = $pricePerClick;
        $this->pricePerClickSummary['SetupFee_postalPrep'] = $setupFeePostalPrep;
        $this->pricePerClickSummary['SetupFee_oversizedEnvelope'] = $setupFeeOversizedEnvelope;
        $this->pricePerClickSummary['mockPricePerClick'] = $mockPricePerClick;
        
        $summaryArray = $this->pricePerClickSummary;
        $addedCosts = '';
        foreach($summaryArray as $key => $value) {
            if($key !== '$PRICE_PER_CLICK' && $key !== 'mockPricePerClick') {
                $addedCosts .= (' | ' . $key . ' = ' . $value . ' | ');
            }
        }
        $summaryArray['added_costs'] = $addedCosts;
        
        return $summaryArray;
        
    } // END OF: determinePricePerClick()
    
    // Will extract values from [color] = 1/0, 4/1
    public function determineColorAndSides(string $color): array {
        // convert 1-Apr, 4-Jan, etc
        if(strpos($color, '/') === false) {
            $color = strtolower($color);
            $color = str_replace('-', '/', $color);
            $color = str_replace('apr', '4', $color);
            $color = str_replace('jan', '1', $color);
            $color = str_replace('00', '0', $color);
            $break = 'point';
        }
        
        // possible values = 1/0, 1/1, 4/0, 4/1, 4/4
        $colorAndSides = ['sides' => 1, 'front_color' => null, 'back_color' => null];
        
        // cs = color and sides
        $csExplode = explode('/', $color);
        try {
            if(!isset($csExplode[1])) {
                $break = 'point';
            }
            if($csExplode[0] !== null && $csExplode[1] !== null) {
                $numerator = (int)trim($csExplode[0]);
                $denominator = (int)trim($csExplode[1]);
            }
            else {
                //TODO: let user know something went wrong and the cost had to increase
                $numerator = 4;
                $denominator = 4;
            }
            
            if($denominator === 0) {
                $colorAndSides['sides'] = 1;
            }
            else {
                $colorAndSides['sides'] = 2;
            }
            
            if($numerator === 4) {
                $colorAndSides['front_color'] = 'color';
            }
            else {
                $colorAndSides['front_color'] = 'black-white';
            }
            
            if($denominator === 4) {
                $colorAndSides['back_color'] = 'color';
            }
            
            if($denominator === 1) {
                $colorAndSides['back_color'] = 'black-white';
            }
            
            return $colorAndSides;
        }
        catch(\Exception $e) {
            $err = 'color and sides explode() did not work due to: ' . $e->getMessage();
            return [$err];
        }
    }
    
    public function uncomputableJobCheck(
        int $numInserts, string $color, string $jobType, string $paperType, string $mailSize,
        int $jobId, string $salesRep, string $clientName, string $jobName
    ): bool {
        $rsMatrix = 'The Redstone Master Price Matrix';
        $jobType = strtolower(trim($jobType));
        $computable = true;
        $reasonMatrix = "Not in price matrix";
        $reasonInfo = "Incomplete info";
        $precision = isset($this->queryParams['precision']) ? $this->queryParams['precision'] : null;
        
        $info = null;
        $additionalInfo = null;
        // this is likely something we'll want to keep track of internally.
        $mockPricesUsed = null;
        
        // <CLOSURE/>
        $uncompute = function($reason, $info, $additionalInfo) use (
            $jobId, $clientName, $jobName, $salesRep
        ) {
            if(isset($this->uncomputableJobs[$jobId])) { // it exists, so don't overwrite it.
                $this->uncomputableJobs[$jobId]['info'] .= (" | " . $info);
                $this->uncomputableJobs[$jobId]['additional_info'] .= (" | " . $additionalInfo);
            }
            else { // it doesn't exist so create it.
                $this->uncomputableJobs[$jobId] = [
                    'job_id' => $jobId,
                    'client_name' => $clientName,
                    'job_name' => $jobName,
                    'reason' => $reason, // variable
                    'info' => $info, // variable
                    'additional_info' => $additionalInfo, // variable
                    'sales_rep' => $salesRep,
                ];
            }
        };
        
        // this "precision" value may change
        if($precision === 'exact') {
            if($numInserts > 1) {
                $info = "There were $numInserts inserts, There isn't any data for the additional letters inserted.";
                $additionalInfo = "4 letter inserts may contain 4/0, 1/1, 1/0, 4/4 and we only have known data for 1 of those letters.";
                $uncompute($reasonInfo, $info, $additionalInfo);
                
                $computable = false;
            }
            
            if(empty($color) && ($jobType === 'letter' || $jobType === 'post card')) {
                $info = 'The [color] field was blank';
                $additionalInfo = 'Color per side costs cannot be computed.';
                $uncompute($reasonInfo, $info, $additionalInfo);
                
                $computable = false;
            }
            
            if($jobType === 'self mailer') {
                $info = "$rsMatrix does not contain pricing for [self mailers]";
                $additionalInfo = 'N/A';
                $uncompute($reasonMatrix, $info, $additionalInfo);
                
                $computable = false;
            }
            
            if($jobType === 'print / other') {
                $info = "$rsMatrix does not contain pricing for [print / other]";
                $additionalInfo = 'N/A/';
                $uncompute($reasonMatrix, $info, $additionalInfo);
                
                $computable = false;
            }
            
            //TODO: if one day the paper type of post cards matter set $postCardPaperTypeMatters = true
            $postCardPaperTypeMatters = false;
            if($postCardPaperTypeMatters) {
                if($jobType === 'post card') { //TODO: improve this condition
                    $contains100 = strpos(trim($paperType), '100');
                    $contains67 = strpos(trim($paperType), '67');
                    if($contains100 === false && $contains67 === false) {
                        $info = "$rsMatrix doesn't contain pricing for [Post Card] jobs that use paper type [$paperType]";
                        $additionalInfo = 'N/A';
                        $uncompute($reasonMatrix, $info, $additionalInfo);
                        
                        if($paperType !== '20#') {
                            $break = 'point';
                        }
                        $computable = false;
                    }
                }
            }
        }
        // just resort to ballpark costs if no precision value
        else {
            $mockPricesUsed = true;
        }
        
        return $computable;
        
    } // END OF: uncomputableJobCheck()
    
    // Will Reconstruct the array and Compute the commission
    public function reconstructAndCompute(): array {
        
        // variable declarations
        $uncomputableInfo /* as.ar */ = null;
        $hashClient /* str */ = null;
        $hashSalesRep /* str */ = null;
        $field /* as.ar */ = null;
        $computable /* bool */ = null;
        $zHashRec /* as.ar */ = null;
        $zHashGroup /* as.ar */ = null;
        // _HEADER ROWS, Create the header row (these are literally the same name as Job Board CSV)
        $salesRepHeaderRowSimple = [
            // simple field
            'job_id' => 'job_id',
            // simple field
            'export_duedate' => 'export_duedate',
            // simple field
            'invoice' => 'invoice',
            // simple field
            'gross_billed' => 'gross_billed',
            // PHP computed field
            'bill_minus_postage' => 'bill_minus_postage',
            // PHP computed field
            'price_per_click' => 'price_per_click',
            // simple field
            'quantity' => 'quantity',
            // PHP computed field
            'gross_cost' => 'gross_cost',
            // PHP computed field
            'profit_loss_amount' => 'profit_loss_amount',
            // PHP computed field
            'added_costs' => 'added_costs',
        ];
        $salesRepHeaderRowComplex = [
            'job_due_date' => 'job_due_date', // done,
            'client' => 'client', // done,
            'job_num' => 'job_num', // done,
            'job_name' => 'job_name', // done,
            'invoice_num' => 'invoice_num', // done,
            'pmod_or_freight' => 'pmod_or_freight', // done,
            'gross_billed' => 'gross_billed', // done
            'quantity' => 'quantity', // done,
            'pricing' => 'pricing', // done, PHP CALC
            'postal_prep' => 'postal_prep', // done
            'production_cost' => 'production_cost', // done, PHP CALC
            'per_piece' => 'per_piece', // done, PHP CALC
            'data_cost' => 'data_cost', // done,
            'postage_psi' => 'postage_psi', // done,
            'data_po_num' => 'data_po_num', // done
            'threePointFivePercent_ccFee' => 'threePointFivePercent_ccFee', // done
            'specialProduct_envelopes_envPrinting' => 'specialProduct_envelopes_envPrinting', // PHP CALC
            'paper_colorPrintCost' => 'paper_colorPrintCost', // PHP CALC
            'shipping_pmodCost' => 'shipping_pmodCost', // SQL CALC
            'shipping_perPiece' => 'shipping_perPiece', // SQL CALC
            'miscCost_pmodLaborPurlVarImg' => 'miscCost_pmodLaborPurlVarImg', // done, PHP CALC
            'gross_cost' => 'gross_cost', // done,
            'net_gross_margin_for_labor' => 'net_gross_margin_for_labor', // done
            'sales_rep' => 'sales_rep', // done
            'comm_paid' => 'comm_paid', // done
            'new_client' => 'new_client', // done
            'grossMargin_perPiece' => 'grossMargin_perPiece', // PHP CALC
            'description' => 'description', // done
            'production_type' => 'production_type', // done
        ];
        
        // This is where the code finds column order index and fills field values.
        $indexesFound = $this->findColumnOrder();
        
        // <CLOSURE/> 's
        $exportDuedate = function(array $field, string $key, array &$record): void {
            $dueDateStr = $field[$key];
            // take the 0's out of the export duedate field
            $record[$key] = substr(
                $dueDateStr, 0, strpos($dueDateStr, ' ')
            );
        };
        
        //-- MAIN-LOOP:
        // Reconstruct the array for 1) easier sorting, 2) GROUP BY [client], 3) also SKIP HEADER ROW $i = 1
        for($i = 1; $i < count($this->jobBoardData); $i++) {
            // each record is a job
            $record = $this->jobBoardData[$i];
            // attach to class for debugging
            $this->_curRecord = $record;
            
            // track the main loop index for better debugging
            $this->mainLoopIdxTracker = $i;
            
            /*
                     IMPORTANT FIELDS TO EXPORT:
                1  - job due date          / 'export_duedate'
                2  - job #                 / 'job_id'
                3  - invoice #             / 'invoice'
                4a - gross billed          / 'total'
                4b - ($/pc)                / custom_calculation
                5  - (gross cost)          / custom_calculation
                6  - (net gross margin)    / custom_calculation
                7 - data cost              / data_cost
            */
            
            // INNER-LOOP, WORST CASE < ~50
            // fill $field[] w/values and type cast pertinent fields
            foreach($indexesFound as $key => $value) {
                $fieldValue = $record[$indexesFound[$key]];
                
                // 1st type cast
                switch($key) {
                    case 'job_id':
                    case 'qty':
                    case 'num_inserts':
                    case 'req_inkjet':
                    case 'purls':
                    case 'variable_streetview':
                        $fieldValue = (int)$fieldValue;
                        break;
                    case 'total':
                    case 'postage_amt':
                        $fieldValue = (float)$fieldValue;
                        break;
                }
                
                // store all field values for determinePricePerClick()
                $field[$key] = $fieldValue;
                
                // 2nd - get pertinent export fields for the "simple report"
                switch($key) {
                    // special app logic
                    case 'export_duedate':
                        $exportDuedate($field, $key, $zHashRec['simple']);
                        break;
                    // 1 to 1 mapping
                    case 'job_id':
                    case 'invoice':
                        $zHashRec['simple'][$key] = $field[$key];
                        break;
                }
                
                // 3rd - get export fields for "complex report"
                switch($key) {
                    // special app logic
                    case 'export_duedate':
                        $exportDuedate($field, $key, $zHashRec['complex']);
                        break;
                    // 1 to 1 mapping
                    case 'job_id': // job num
                    case 'invoice': // invoice num
                    case 'data_cost': // data cost
                        $zHashRec['complex'][$key] = $field[$key];
                        break;
                }
                
            } // END OF: foreach()
            
            // store these fields as class globals for debugging purposes
            $this->jobTypeTrack = $field['jobtype'];
            $this->jobId = (int)$field['job_id'];
            $this->reqInkjet = (int)$field['req_inkjet'];
            
            /*************************************************
             ************ "Uncomputable check" *************
             ************************************************/
            $computable = $this->uncomputableJobCheck(
                $field['num_inserts'], $field['color'], $field['jobtype'], $field['papertype'], $field['mailsize'],
                $field['job_id'], $field['export_sales'], $field['export_company_name'], $field['name']
            );
            
            if($computable) /* The job can be computed. */ {
                
                /***********************************************************************************
                 ***************************** SUPER IMPORTANT "$/pc" *****************************
                 **********************************************************************************/
                $pricePerClick /* as.ar */ = $this->determinePricePerClick(
                    $field['job_id'], $field['jobtype'], $field['papertype'], $field['paper_color'],
                    $field['qty'], $field['envelopepaper'], $field['color'], $field['num_inserts'],
                    $field['export_coordinator'], $field['req_inkjet'], $field['snap_seal'],
                    $field['snap_paper'], $field['dist_type'], $field['purls'], $field['variable_streetview'],
                    $field['mailsize'], $field['postage_type'], $zHashRec['complex']
                );
                
                // _DEBUG break point
                if($this->jobId === 64004 || $this->reqInkjet === 1) {
                    $break = 'point';
                }
                
                ksort($pricePerClick);
                ksort($this->pricePerClickSummary);
                
                #region  ----------------- THE CORE COMPUTATIONS -----------------
                //###########################################################################################################
                // These are the formulas Hemphill gave me
                $rsGrossCost = ($field['qty'] * $pricePerClick['$PRICE_PER_CLICK'])
                    + $pricePerClick['SetupFee_postalPrep'] + $pricePerClick['SetupFee_oversizedEnvelope'];
                $rsGrossBilled = $field['total'] - $field['postage_amt'];
                $rsNetGrossMargin = $rsGrossBilled - $rsGrossCost;
                //$rsCommission = $rsNetGrossMargin * $this->comPct;
                //###########################################################################################################
                #endregion  --------------- END OF: Core Computations --------------
                
                //-- complex report computations:
                $svCost = isset($this->pricePerClickSummary['streetViewCost']) ? $this->pricePerClickSummary['streetViewCost'] : null;
                $svCost = is_null($svCost) ? 0 : ($svCost * $field['qty']);
                $purlCost = isset($this->pricePerClickSummary['purlsCost']) ? $this->pricePerClickSummary['purlsCost'] : null;
                $purlCost = is_null($purlCost) ? 0 : ($purlCost * $field['qty']);
                
                // for [Special product/Envelopes/Env Printing]
                $expensiveEnvelope = isset($pricePerClick['expensiveEnvelopeCost']) ? $pricePerClick['expensiveEnvelopeCost'] : 0;
                $expensiveSnapPack = isset($pricePerClick['pressureSealCost']) ? $pricePerClick['pressureSealCost'] : 0;
                
                /*
                    Append dynamically calculated and additional simple fields,
                    these are the fields that get exported to CSV
                */
                //-- simple report, convert field title names:
                $zHashRec['simple']['quantity'] = $field['qty'];
                $zHashRec['simple']['gross_billed'] = $field['total'];
                $zHashRec['simple']['price_per_piece'] = $pricePerClick['$PRICE_PER_CLICK'];
                $zHashRec['simple']['added_costs'] = $pricePerClick['added_costs'];
                $zHashRec['simple']['gross_cost'] = $rsGrossCost;
                $zHashRec['simple']['bill_minus_postage'] = $rsGrossBilled;
                $zHashRec['simple']['profit_loss_amount'] = $rsNetGrossMargin;
                
                //-- complex report, convert field title names:
                $zHashRec['complex']['quantity'] = $field['qty'];
                $zHashRec['complex']['pricing'] = round($field['total'] / $field['qty'], 3);
                $zHashRec['complex']['gross_billed'] = $field['total'];
                $zHashRec['complex']['client'] = $field['export_company_name'];
                $zHashRec['complex']['job_name'] = $field['name'];
                $zHashRec['complex']['postage_psi'] = $field['postage_amt'];
                $zHashRec['complex']['pmod_or_freight'] = $field['dist_type'];
                $zHashRec['complex']['sales_rep'] = $field['export_sales'];
                $zHashRec['complex']['data_po_num'] = 'unknown';
                $zHashRec['complex']['threePointFivePercent_ccFee'] = 'unknown';
                $zHashRec['complex']['comm_paid'] = 'unknown';
                $zHashRec['complex']['new_client'] = 'unknown';
                $zHashRec['complex']['postal_prep'] = $this->pricePerClickSummary['SetupFee_postalPrep'];
                $zHashRec['complex']['production_cost'] = ($pricePerClick['$BASE_COST'] * $field['qty']);
                $zHashRec['complex']['per_piece'] = ($zHashRec['complex']['production_cost'] / $field['qty']);
                $zHashRec['complex']['miscCost_pmodLaborPurlVarImg'] = round($svCost, 2) + round($purlCost, 2);
                $zHashRec['complex']['description'] = (
                    $field['paper_color'] . ' ' . $field['papertype'] . ' ' . $field['paper_color'] . ', ' .
                    $field['envelopepaper'] . ' ' . $field['envelopetype'] . ', ' .
                    $field['snap_paper'] . ' ' . $field['snap_seal']
                );
                
                $zHashRec['complex']['gross_cost'] = $rsGrossCost;
                $zHashRec['complex']['net_gross_margin_for_labor'] = $rsNetGrossMargin;
                $zHashRec['complex']['production_type'] = $field['jobtype'];
                $zHashRec['complex']['added_costs'] = $pricePerClick['added_costs'];
                
                // Hash by client [export_company_name] and sales rep [export_sales]
                $hashClient = $record[$indexesFound['export_company_name']];
                $hashSalesRep = $record[$indexesFound['export_sales']];
                
                if(gettype($zHashRec) !== "array" || gettype($zHashGroup) === "string") {
                    // something went wrong
                    $broken = 'point';
                }
                
                // HASH TABLE - Client (this data is used for the totals ngView and the rs_commission-client.csv)
                $zHashGroup['client'][$hashClient] [] = $zHashRec;
                // HASH TABLE - Sales Rep
                $zHashGroup['sales_rep'][$hashSalesRep] [] = $zHashRec;
                
                // clear values from prior iteration
                $this->pricePerClickSummary = null;
                
            } // END OF: if uncomputable
            
        } // END OF: the "main loop" I.E. Job Board data for-loop
        
        // Sort hash tables by key
        ksort($zHashGroup['client']);
        ksort($zHashGroup['sales_rep']);
        
        $break = 'point';
        
        // unshift each sales rep array with a header row for CSV export
        foreach($zHashGroup['sales_rep'] as $key => $value) {
            array_unshift($zHashGroup['sales_rep'][$key], $salesRepHeaderRowSimple);
        }
        
        //TODO: export a csv as thorough as the 'Job Tracker' spreadsheet.
        $salesRepHeaderRowThorough = [];
        
        // make the result of the entire comauto program available to the class for debugging purposes
        $this->hashGroup = $zHashGroup;
        
        return $zHashGroup;
        
    } // END OF: reconstructAndCompute()
    
    // Will group by $groupBy, this will summarize total [gross_billed] AND [gross_cost]
    public function groupCommissions(array $computedCommission, string $groupBy): array {
        // field initializations
        $entityTotalSums = [];
        $uncomputableMash = [];
        // Do some commission computations and build the array that will be exported as CSV
        foreach($computedCommission as $key => $value) {
            // field declarations
            $sumArr = null;
            // field initializations
            $isComputable = true;
            $commissionTotal = 0;
            $grossBilled = 0;
            $grossCost = 0;
            
            // Inner-Loop over each client or sales rep group, this will sum totals
            // WORST CASE < ~50 ...probably
            for($i = 0; $i < count($value); $i++) {
                $record = $value[$i];
                
                /*  isset() logic:
                $fieldGrossBilled = isset($record['gross_billed']) ? $record['gross_billed'] : null;
                $fieldGrossCost = isset($record['gross_cost']) ? $record['gross_cost'] : null;
                $fieldProfitLoss = isset($record['profit_loss_amount']) ? $record['profit_loss_amount'] : null;
                */
                
                /*  is_null() logic
                $fieldGrossBilled = is_null($record['gross_billed']) ? null : $record['gross_billed'];
                $fieldGrossCost = is_null($record['gross_cost']) ? null : $record['gross_cost'];
                $fieldProfitLoss = is_null($record['profit_loss_amount']) ? null : $record['profit_loss_amount'];
                */
                
                $fieldGrossBilled = array_key_exists('gross_billed', $record) ? $record['gross_billed'] : null;
                $fieldGrossCost = array_key_exists('gross_cost', $record) ? $record['gross_cost'] : null;
                $fieldProfitLoss = array_key_exists('profit_loss_amount', $record) ? $record['profit_loss_amount'] : null;
                
                if($fieldGrossBilled === null || $fieldGrossCost === null || $fieldProfitLoss === null) {
                    // we have an uncomputable job, record will look like [uncomputable => 'reason why uncomputable']
                    //$isComputable = false;
                    // will probably just need to build a unique uncomputable by sales_rep and client hashtable
                    $uncomputableMash [] = $record;
                    continue;
                }
                else {
                    // get the relevant fields
                    $_tTotal = (float)$fieldGrossBilled;
                    $_tCost = (float)$fieldGrossCost;
                    $_tCommission = (float)$fieldProfitLoss;
                    
                    // do some addition
                    $commissionTotal += $_tCommission;
                    $grossBilled += $_tTotal;
                    $grossCost += $_tCost;
                }
            } // END OF: inner-loop to sum total
            
            if($isComputable) {
                // make fields human readable
                $commissionTotal = number_format($commissionTotal);
                $grossBilled = number_format($grossBilled);
                $grossCost = number_format($grossCost);
                
                // make a summed field array
                $sumArr = [
                    $groupBy => $key,
                    'gross_billed' => $grossBilled,
                    'gross_cost' => $grossCost,
                    'net_gross_margin' => $commissionTotal,
                ];
                
                $entityTotalSums[$key] = $sumArr;
            }
            
        } // END OF: outer-loop to group by
        
        // add field titles to the array that'll be exported to CSV
        array_unshift($entityTotalSums, [
            $groupBy, 'gross_billed', 'gross_cost', 'commission',
        ]);
        
        return $entityTotalSums;
        
    } // END OF: groupCommissions()
    
    /** Standard Letter
     * 1) For every additional insert into an envelope an additional cost of 0.005 gets added
     * This function only returns a value for Unit Testing
     *
     * @param float $pricePerClick
     * @param int $numInserts
     * @param float $additionalInsertCost
     *
     * @return float - return the additional cost sum from the additional inserts, not the $/pc
     */
    public function calcAdditionalInsert(
        float &$pricePerClick, int $numInserts, float $additionalInsertCost
    ): float {
        $additionalInsertSum = 0;
        
        //-- -- "Additional insert" cost: -- --\\
        if($numInserts > 1) {
            for($i = 1; $i < $numInserts; $i++) {
                $pricePerClick += $additionalInsertCost;
                $additionalInsertSum += $additionalInsertCost;
            }
            $this->pricePerClickSummary['numInsertsCost'] = $additionalInsertSum;
        }
        
        return empty($this->pricePerClickSummary['numInsertsCost'])
            ? 0.0 : $this->pricePerClickSummary['numInsertsCost'];
    }
    
    //todo: DON'T CHECK FOR paper thickness
    
    /**  Standard Letter
     * 2) Figure out the cost of 4x6 and #100 or 67# postcards, only [color] is being taken into account
     * This essentially covers the "Digital Color Printing - Postcards" table from the Price Matrix.
     *
     * @param float $pricePerClick
     * @param bool $mockPricePerClick
     * @param string $mailSize
     * @param string $paperType
     * @param array $colorAndSides
     */
    public function calcPostCardPriceMatrix(
        float &$pricePerClick, bool &$mockPricePerClick, string $mailSize, string $paperType,
        array $colorAndSides, string $color
    ): void {
        // NOT CHECKING PAPER TYPE ANYMORE, just force true
        $number100 = strpos(trim($paperType), '100');
        $number100 = true;
        $_67pound = strpos(trim($paperType), '67');
        $_67pound = true;
        
        // _DEBUG
        if($paperType === '67#') {
            $break = 'point';
        }
        
        if(
            $number100 !== false && $colorAndSides['front_color'] === 'color' &&
            $colorAndSides['back_color'] === 'color'
        ) {
            $pricePerClick = 0.03;
            $this->pricePerClickSummary['postCardCost'] = $pricePerClick;
        }
        else if(
            $_67pound !== false && $colorAndSides['front_color'] === 'color' &&
            $colorAndSides['back_color'] === 'black-white'
        ) {
            $pricePerClick = 0.025;
            $this->pricePerClickSummary['postCardCost'] = $pricePerClick;
        }
        else if(
            $_67pound !== false && $colorAndSides['front_color'] === 'black-white' &&
            $colorAndSides['back_color'] === 'black-white'
        ) {
            $pricePerClick = 0.02;
            $this->pricePerClickSummary['postCardCost'] = $pricePerClick;
        }
        else {
            $pricePerClick = 0.03;
            $this->pricePerClickSummary['mockPriceReason'] = "Post Card $paperType $color job";
            $this->pricePerClickSummary['postCardCost'] = $pricePerClick;
            $mockPricePerClick = true;
        }
    }
    
    /** Standard Letter
     * 3) Figure out if this job is using an expensive envelope
     *
     * @param float $pricePerClick
     * @param string $envelopePaper
     * @param float $expensiveEnvelopeCost
     * @param int $jobId
     * @param string $coordName
     */
    public function calcExpenseEnvelope(
        float &$pricePerClick, string $envelopePaper, float $expensiveEnvelopeCost,
        int $jobId, string $coordName
    ): void {
        // _DEBUG - break when job id is $intJobId
        $intJobId = (int)$this->jobId;
        if($intJobId === 64004) {
            $break = 'point';
        }
        
        if(!empty($envelopePaper)) {
            // if PHP finds "#10" it's not expensive envelope
            $isNum10 = strpos(strtolower($envelopePaper), '#10');
            // if PHP finds "white" it's not expensive paper
            $isWhite = strpos(strtolower($envelopePaper), 'white');
            
            // #10 was not found OR white was not found, therefore it's expensive
            if($isNum10 === false || $isWhite === false) {
                $pricePerClick += $expensiveEnvelopeCost; // cpc
                $this->pricePerClickSummary['expensiveEnvelopeCost'] = $expensiveEnvelopeCost;
            }
        }
        // Track which coordinators are not entering mandatory fields
        else {
            $fieldTitle = '[envelopepaper]';
            $issue = 'coordinator did not input envelope paper field';
            $this->emptyFieldTracker($fieldTitle, $issue, $envelopePaper, $jobId, $coordName);
        }
    }
    
    /** Standard Letter
     * 4) Figure out if this job is using expensive paper.
     *
     * @param float $pricePerClick
     * @param string $paperType
     * @param float $expensivePaperCost
     * @param string $paperColor
     */
    public function calcExpensePaper(
        float &$pricePerClick, string $paperType, float $expensivePaperCost, string $paperColor
    ): void {
        $paperType = trim($paperType);
        $paperColor = trim($paperColor);
        if( // paper type isn't "20#" or "60#", or paper color isn't white
            ($paperType !== '20#' && $paperType !== '60#') ||
            $paperColor !== 'White'
        ) {
            $pricePerClick += $expensivePaperCost;
            $this->pricePerClickSummary['expensivePaperCost'] = $expensivePaperCost;
        }
    }
    
    /** Standard Letter
     * 5) - Figure out if the "Inkjet Variable Address" cost should be added to this job
     *
     * @param float $pricePerClick
     * @param int $inkjet
     * @param float $inkjetAddressCost
     */
    public function calcInkjetVariable(
        float &$pricePerClick, int $inkjet, float $inkjetAddressCost
    ): void {
        // should make sure 'window' not in [envelopetype]
        if($inkjet === 1) {
            $pricePerClick += $inkjetAddressCost;
            $this->pricePerClickSummary['inkjetCost'] = $inkjetAddressCost;
        }
    }
    
    /** Standard Letter
     * 6) Figure out if the "Inserting Oversized Envelope" cost should be added
     *
     * @param float $pricePerClick
     * @param float $setupFees
     * @param float $overSizedEnvelopeCost
     * @param float $overSizedEnvelopeSetupFee
     * @param string $envelopePaper
     * @param int $jobId
     * @param string $coordName
     */
    public function calcOverSizedEnvelope(
        float &$pricePerClick, float &$setupFees, float $overSizedEnvelopeCost,
        float $overSizedEnvelopeSetupFee, string $envelopePaper, int $jobId, string $coordName
    ): void {
        if(!empty($envelopePaper)) {
            // if PHP finds "#10" it is a regular sized envelope
            $isNum10 = strpos(strtolower($envelopePaper), '#10');
            if($isNum10 === false) {
                $pricePerClick += $overSizedEnvelopeCost;
                $setupFees += $overSizedEnvelopeSetupFee;
                $this->pricePerClickSummary['oversizedEnvelopePerPiece'] = $overSizedEnvelopeCost;
                $this->pricePerClickSummary['SetupFee_oversizedEnvelope'] = $overSizedEnvelopeSetupFee;
            }
        }
        // add to empty field tracker
        else {
            $fieldTitle = '[envelopepaper]';
            $issue = 'coordinator did not enter envelope paper';
            $this->emptyFieldTracker($fieldTitle, $issue, $envelopePaper, $jobId, $coordName);
        }
    }
    
    /** Standard Letter
     * 7) Figure out if the cost of "Digital Color Printing" should be added
     *
     * @param float $pricePerClick
     * @param $digitalColorPrintingCost
     * @param $digitalColor2ndSideCost
     * @param $colorAndSides
     * @param $digitalColor2ndSideColorCost
     *
     * @return float - the calculated cost per piece / price per click
     */
    public function calcDigitalColor(
        float &$pricePerClick, float $digitalColorPrintingCost, float $blackWhite2ndSideCost,
        array $colorAndSides, float $digitalColor2ndSideCost
    ): float {
        if(!empty($colorAndSides)) {
            if($colorAndSides['front_color'] === 'color') {
                $pricePerClick += $digitalColorPrintingCost; // cost per click
                $this->pricePerClickSummary['frontColorCost'] = $digitalColorPrintingCost;
            }
            
            if($colorAndSides['back_color'] === 'black-white') {
                $pricePerClick += $blackWhite2ndSideCost;  // cost per click
                $this->pricePerClickSummary['backColorCost'] = $blackWhite2ndSideCost;
            }
            else if($colorAndSides['back_color'] === 'color') {
                $pricePerClick += $digitalColor2ndSideCost;
                $this->pricePerClickSummary['backColorCost'] = $digitalColor2ndSideCost;
            }
        }
        // make the cost of the job a 4/4
        else {
            $pricePerClick += ($digitalColorPrintingCost + $digitalColor2ndSideCost);
            $this->pricePerClickSummary['no_color_field_cost'] = ($digitalColorPrintingCost + $digitalColor2ndSideCost);
        }
        
        return $pricePerClick;
    }
    
    /** Pressure Seal
     * 8) Figure out if the cost of "Pressure Seal Form" should be added
     *
     * @param float $pricePerClick
     * @param $pressureSealCost
     * @param $snapSeal
     * @param $snapPaper
     */
    public function calcPressureSeal(
        float &$pricePerClick, float $pressureSealCost, string $snapSeal, string $snapPaper
    ): void {
        // normalize and sanitize
        $snapSeal = strtolower(trim($snapSeal));
        $snapPaper = strtolower(trim($snapPaper));
        
        // see if [snap_paper] contains word 'window'
        $snapPaperContainsWindow = strpos($snapPaper, 'window');
        
        if($snapPaperContainsWindow === false) {
            if($snapSeal === 'pressure seal') {
                $pricePerClick += $pressureSealCost;
                $this->pricePerClickSummary['pressureSealCost'] = $pressureSealCost;
            }
        }
    }
    
    /** Pressure Seal
     *  9) Figure out if cost of "Window Check Snap Pressure Seal Form" should be added
     *
     * @param float $pricePerClick
     * @param $windowCheckSnapCost
     * @param $snapSeal
     * @param $snapPaper
     *
     */
    public function calcWindowCheck(
        float &$pricePerClick, float $windowCheckSnapCost, string $snapSeal, string $snapPaper
    ): void {
        $snapPaper = strtolower(trim($snapPaper));
        $snapPaperContainsWindow = !!strpos($snapPaper, 'window');
        if($snapPaperContainsWindow) {
            $pricePerClick += $windowCheckSnapCost;
            $this->pricePerClickSummary['windowCheckSnapCost'] = $windowCheckSnapCost;
        }
    }
    
    /**General Purpose
     *  10) Calculate the shipping cost:
     *  SUPER SUPER IMPORTANT - This function will use the magic numbers Heather gave me.
     *  PMOD = 0.07
     *  Drop ship = 0.015
     *
     * @param float $pricePerClick
     * @param $pmodCost
     * @param $dropShipCost
     * @param $distType
     */
    public function calcShipping(
        float &$pricePerClick, float $pmodCost, float $dropShipCost, string $distType
    ): void {
        $distType = strtolower(trim($distType));
        
        if($distType === 'pmod') {
            $pricePerClick += $pmodCost;
            $this->pricePerClickSummary['pmodCost'] = $pmodCost;
        }
        else if($distType === 'drop ship') {
            $pricePerClick += $dropShipCost;
            $this->pricePerClickSummary['dropShipCost'] = $dropShipCost;
        }
    }
    
    /** General Purpose
     * 11) Add the cost of PURLs or Variable Images if needed
     *
     * @param float $pricePerClick
     * @param $purlCost
     * @param $streetViewCost
     * @param $purls
     * @param $streetView
     */
    public function calcPurlVarImg(
        float &$pricePerClick, float $purlCost, float $streetViewCost, int $purls, int $streetView, int $qty
    ): void {
        
        if($streetView === 1) {
            $pricePerClick += $streetViewCost;
            $this->pricePerClickSummary['streetViewCost'] = $streetViewCost;
        }
        
        if($purls === 1) {
            $pricePerClick += $purlCost;
            $this->pricePerClickSummary['purlsCost'] = $purlCost;
        }
        
    }
    
    /** Addressing and Stamping Station
     * 12) figure out "Ink Jet Address & Delivery to USPS ($50 Set up < 5K)" cost
     *
     * @param float $pricePerClick
     * @param float $inkjetAddrUpperCost
     * @param float $inkjetAddrLowerCost
     * @param int $jobSize
     * @param int $thresholdAddrStampStation
     * @param string $jobType
     * @param float $inkjetAddrSetupFee
     */
    public function calcInkjetAddr(
        float &$pricePerClick, float $inkjetAddrUpperCost, float $inkjetAddrLowerCost,
        int $jobSize, int $thresholdAddrStampStation, string $jobType, float $inkjetAddrSetupFee
    ): void {
        // make sure this additional cost is only added to standard letter and post card jobs
        if($jobType === 'letter' || $jobType === 'post card') {
            if($jobSize < $thresholdAddrStampStation) {
                $pricePerClick += $inkjetAddrUpperCost;
                $this->pricePerClickSummary['inkjetAddressCost'] = $inkjetAddrUpperCost;
                $this->pricePerClickSummary['SetupFee_inkjetAddrLessThan5k'] = $inkjetAddrSetupFee;
            }
            else {
                $pricePerClick += $inkjetAddrLowerCost;
                $this->pricePerClickSummary['inkjetAddressCost'] = $inkjetAddrLowerCost;
            }
        }
    } // END OF: calInkjetAddr()
    
    /**Addressing and Stamping Station
     * 13) figure out "Apply Pre-Canceled Stamps" cost
     *
     * @param float $pricePerClick
     * @param float $precanceledStampInlineCost
     * @param float $precanceledStampOfflineCost
     * @param string $postageType
     * @param int $inkjet
     *
     */
    public function calcPrecanceledStamps(
        float &$pricePerClick, float $precanceledStampInlineCost, float $precanceledStampOfflineCost,
        int $inkjet
    ): void {
        if($inkjet === 1) {
            $pricePerClick += $precanceledStampInlineCost;
            $this->pricePerClickSummary['precanceledStampCostInline'] = $precanceledStampInlineCost;
        }
        else {
            $pricePerClick += $precanceledStampOfflineCost;
            $this->pricePerClickSummary['precanceledStampCostOffline'] = $precanceledStampOfflineCost;
        }
    }
    
    //TODO: implement this _ADDITIONAL_COST
    
    /**Data Lists Provided by Redstone
     * 14) figure out the "Data Lists Provided by Redstone" costs
     *
     * @param float $pricePerClick
     * @param float $dataCost
     */
    public function calcData(float &$pricePerClick, float $dataCost): void {
    
    }
    
    // This function is purely to make sure PHPUnit is wired up with SlimPHP and this comauto program
    public function simpleTest(): array {
        return [$this->jobBoardData[0][0], $this->jobBoardData[1][0]];
    }
    
} // END OF: "class CommissionAuto {}"