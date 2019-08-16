<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: mhetauser
 * Date: 3/16/2019
 * Time: 2:00 PM
 */

namespace MhetaClient;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class GuzzlePrac
{
 
    private $defPath = 'firebase/example';
    
//    public function websocketTest() {
//        $defUrl = $this->defUrl;
//
//        $firebase = new \Firebase\FirebaseLib($defUrl, $this->defToken);
//
//        $test = [
//            'foo' => 'bar',
//            'i_love' => 'lamp',
//            'id' => 42
//        ];
//
//        $dateTime = date_create();
//        $firebase->set($defUrl . '/' . $dateTime->format('c'), $test);
//
//        // --- storing a string ---
//        $firebase->set($defUrl . '/name/contact001', 'John Doe');
//        // --- reading the stored string ---
//        $name = $firebase->get($defUrl . '/name/contact001');
//
//        return $name;
//    }
    
    public function guzzleStart() {
        $client = new Client();
        
        $requests = function ($total) {
            $uri = 'http://127.0.0.1:8126/guzzle-server/perf';
            for ($i = 0; $i < $total; $i++) {
                yield new Request('GET', $uri);
            }
        };
        
        $break = 'point';
        
        $pool = new Pool($client, $requests(2), [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) {
                echo "\n__>> There was a response";
            },
            'rejected' => function ($reason, $index) {
                echo "There was a request that was rejected";
                $this->logGuzzleRejection($reason, $index, 'prac pass');
                $break = 'point';
            }
        ]);
        
        $promise = $pool->promise();
        $break = 'Asynchronous';
        
        $promise->wait();
        $break = 'Synchronous';
    }
    
    public function logGuzzleRejection($reason, $index, string $passType): string {
        $errorMessage = "\n\r __>> ERROR in $passType: \n\r ";
        $space = "\n\r\n\r-------------------------------------------------------------------------\n\r\n\r";
        
        // Write error to local disk
        $randomNumber = rand(0, 90000000);
        $errorHandle = fopen(".\\errors\ERROR$randomNumber.txt", 'w');
        
        if ($reason instanceof \GuzzleHttp\Exception\ClientException) {
            $errorMessage = $reason->getResponse()->getBody();
            $errorMessage = $errorMessage . $space . $errorMessage;
            fwrite($errorHandle, $errorMessage);
        }
        else if ($reason instanceof \GuzzleHttp\Exception\RequestException) {
            $reqError = $reason->getRequest();
            $reqError = \GuzzleHttp\Psr7\str($reqError);
            $resError = '';
            
            if ($reason->hasResponse()) {
                $resError = $reason->getResponse();
                $resError = \GuzzleHttp\Psr7\str($resError);
            }
            
            $errorMessage = $errorMessage . $space . $reqError . $space . (string)$resError;
            fwrite($errorHandle, $errorMessage);
        }
        else {
            if (!isset($reason)) {
                $reason = ' [ Guzzle did not provide a reason why the request was rejected. ] ';
            }
            $errorMessage = $errorMessage . $space . $reason;
            fwrite($errorHandle, $errorMessage);
        }
        
        fclose($errorHandle);
        return $errorMessage;
    }
    
}