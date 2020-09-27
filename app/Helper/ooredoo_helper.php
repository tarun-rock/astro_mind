<?php

use Illuminate\Support\Facades\Cache;

/**
 * Created by PhpStorm.
 * User: kinshuk
 * Date: 8/3/19
 * Time: 11:02 AM
 */



function postOoredooCurl($data)
{



}

function getOoredooKey()
{



}


function validateOoredooMobile($mobile)
{

}

function ooredooResponseMap($code)
{

    $response = ['status' => 309];

    switch ($code){

        case 0:

            $response['status'] = 200;

            $message = "success";

            break;

        case 100:

            $message = "Invalid number";

            break;

        default:

            $message = "Invalid response";

            break;

    }

    $response['message'] = $message;

    return $response;

}

function deductOoredooBalance()
{



}

function sendOoredooSms($otp,$number){


}

function ooredooSubscription($data)
{


    $key = getOoredooKey();

    $result = postOoredooCurl([
        "action" => "purchase",
        "extra" => "&clientkey=$key&packageid=".$data["packageID"]."&msisdn=".$data["mobile"]
    ]);

    return ooredooResponseMap($result->error_code);
//    return ooredooResponseMap(0);

}