<?php

use App\Model\Payment;
use App\Model\Packages;

function getCashFreeAuthToken() {

//    \Cache::forget("cf_authtoken");

    $authToken = \Cache::remember("cf_authtoken", 9.5, function () {

        $clientId = env("CASHFREE_CLIENT_ID");

        $clientSecret = env("CASHFREE_CLIENT_SECRET");



        $headers = [
            "X-Client-Id: $clientId",
            "X-Client-Secret: $clientSecret"
        ];

        // $baseUrl = url('/'); // need to check it first

        $endpoint = "/authorize";

//        dd($endpoint);

        $curlResponse = cashFreePostCurl($endpoint, $headers);

//        dd($curlResponse);

//        $response = ["token" => "", "baseUrl" => $baseUrl];

        if ($curlResponse["status"] == "SUCCESS" && $curlResponse["subCode"]) {

            $response["token"] = $curlResponse["data"]["token"];

            return $response["token"];
        }

//        return $response["token"];
    });



    return $authToken;
}

function existingRecipient($myRecipient)
{
    $auth_token = getCashFreeAuthToken();

    $headers = [
        "Authorization: Bearer $auth_token",
    ];

    $resp_arr = [];

//    dd($myRecipient);

    foreach($myRecipient as $recip)
    {
//        dd($recip);
        $endpoint = "/getBeneficiary/". $recip['recipient_id'];

        $curlResponse = cashFreeGetCurl($endpoint, $headers);

//        dd($curlResponse);
        if(isset($curlResponse['data']['status']) && $curlResponse['data']['status'] == 'VERIFIED')
        {
            $resp_arr[$recip['id']] = $curlResponse['data']['name'].' - '. ($recip->payment_mode == 2 ? $curlResponse['data']['vpa'] : $curlResponse['data']['phone']) . ' (' .
                ($recip->payment_mode == 2
                    ? "UPI" : "PAYTM")
                . ')';
        }
    }

    return $resp_arr;
}

function validateUpiId($upi_id, $name)
{
    $auth_token = getCashFreeAuthToken();

//    dd($auth_token);

    $headers = [
        "Authorization: Bearer $auth_token",
    ];

    // For test account
    if(env('APP_ENV') == 'local') {

        $upi_id = "success@upi"; // for success
        //$upi_id = "failure@upi"; // for failure
    }

    $getParams = "vpa=$upi_id&name=$name";

    $endpoint = "/validation/upiDetails?". $getParams;

    $curlResponse = cashFreeGetCurl($endpoint, $headers);

//    dd($curlResponse);
    return $curlResponse;
    //return $resp_arr;
}

function getCFpayoutBeneId()
{
    $beneId = getToken(8);

    $beneExists = checkUniqueBeneIdExists($beneId);

    if($beneExists)
    {
        $beneId = getCFpayoutBeneId();
    }

    return $beneId;
}

function addBeneficiary ($beneficiary) {

    $response =["status" => "FAILED", "message" => "Authorization failed"];

//    $authorize = getCashFreeAuthToken();

    $token = getCashFreeAuthToken(); // $authorize["token"];

//    dd($token);

    if ($token) {

//        dd($token);
        $endpoint = "/addBeneficiary"; // $authorize["baseUrl"]."/addBeneficiary";

        $headers = [
            "Authorization: Bearer $token"
        ];

        $curlResponse = cashFreePostCurl($endpoint, $headers, $beneficiary);

//        dd($curlResponse);

        return $curlResponse;
    }

    return $response;
}

function removeBenificiaryAccnt ($bene_id) {

    $response =["status" => "FAILED", "message" => "Authorization failed"];

//    $authorize = getCashFreeAuthToken();

    $token = getCashFreeAuthToken(); // $authorize["token"];

//    dd($token);

    if ($token) {

//        dd($token);
        $endpoint = "/removeBeneficiary"; // $authorize["baseUrl"]."/addBeneficiary";

        $headers = [
            "Authorization: Bearer $token"
        ];

        $curlResponse = cashFreePostCurl($endpoint, $headers, $bene_id);

        return $curlResponse;
    }

    return $response;
}

function requestTransferAmt ($transferData) {

    $response =["status" => "FAILED", "message" => "Authorization failed"];

//    $authorize = getCashFreeAuthToken();

    $token = getCashFreeAuthToken(); // $authorize["token"];

//    dd($token);

    if ($token) {

//        dd($token);
        $endpoint = "/requestTransfer"; // $authorize["baseUrl"]."/addBeneficiary";

        $headers = [
            "Authorization: Bearer $token"
        ];

        $curlResponse = cashFreePostCurl($endpoint, $headers, $transferData);

//        dd($curlResponse);

        return $curlResponse;
    }

    return $response;
}

function removeBeneficiary ($beneId)
{
    $response =["status" => "FAILED", "message" => "Authorization failed"];

    $token = getCashFreeAuthToken();

    if ($token && !is_null($token) && !empty($token)) { // $this->token

        $params = [];

        $params["beneId"] = $beneId;

        $endpoint = "/removeBeneficiary"; // $this->baseUrl."/removeBeneficiary";

//        $authToken = $this->token;

        $headers = [
            "Authorization: Bearer $token" // $authToken"
        ];

        $curlResponse = cashFreePostCurl($endpoint, $headers, $params);

        return $curlResponse;
    }

    return $response;
}

function requestTransfer ($transfer) {

    $response =["status" => "FAILED", "message" => "Authorization failed"];

    // $authorize = getCashFreeAuthToken();

    $token = getCashFreeAuthToken(); // $authorize["token"];

    if ($token && !is_null($token) && !empty($token)) {

        $endpoint = "/requestTransfer"; // $authorize["baseUrl"]."/requestTransfer";

        $headers = [
            "Authorization: Bearer $token"
        ];

        $curlResponse = cashFreePostCurl($endpoint, $headers, $transfer);

        return $curlResponse;

    }

    return $response;
}

function getBalance ()
{

    $balance =["ledger" => -1, "available" => -1];

    $token = getCashFreeAuthToken(); // $authorize["token"];

    if ($token && !is_null($token) && !empty($token)) { // $this->token

        $endpoint = "/getBalance"; // $this->baseUrl."/getBalance";

//        $authToken = $this->token;

        $headers = [
            "Authorization: Bearer $token" // $authToken
        ];

        $curlResponse = cashFreePostCurl(); // $this->getCurl($endpoint, $headers);

        if ($curlResponse["status"] == "SUCCESS")
        {
            $balance["ledger"] = $curlResponse["data"]["balance"];
            $balance["available"] = $curlResponse["data"]["availableBalance"];
        }
    }

    return $balance;
}

function getTransferStatus($params)
{
    $response =["status" => "FAILED", "message" => "Authorization failed"];

//    $authorize = getCashFreeAuthToken();

    $token = getCashFreeAuthToken(); // $authorize["token"];

    if ($token && !is_null($token) && !empty($token)) {

        $endpoint = "/getTransferStatus?referenceId=".$params["referenceId"]."&transferId=".$params["transferId"]; // $authorize["baseUrl"]."/getTransferStatus?referenceId="
        //.$params["referenceId"]."&transferId=".$params["transferId"];

        $headers = [
            "Authorization: Bearer $token"
        ];

        $curlResponse = cashFreeGetCurl($endpoint, $headers);

        return $curlResponse;

    }

    return $response;

}

function cashFreePostCurl ($endpoint, $headers, $params = []) {

    if (env('APP_ENV') == "production") {

        $apiEndpoint = env("CASHFREE_PAYOUT_PROD_URL");

    } else {

        $apiEndpoint = env("CASHFREE_PAYOUT_TEST_URL");
    }

    $postFields = json_encode($params);

    array_push($headers,
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postFields));

    $endpoint = $endpoint."?";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $apiEndpoint.$endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $returnData = curl_exec($ch);

//    dd($returnData);

    curl_close($ch);

    if ($returnData != "") {

        return json_decode($returnData, true);
    }

    return NULL;
}

function cashFreeGetCurl ($endpoint, $headers) {

    if (env('APP_ENV') == "production") {

        $apiEndpoint = env("CASHFREE_PAYOUT_PROD_URL");

    } else {

        $apiEndpoint = env("CASHFREE_PAYOUT_TEST_URL");
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $apiEndpoint.$endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $returnData = curl_exec($ch);

    curl_close($ch);

    if ($returnData != "") {

        return json_decode($returnData, true);
    }

    return NULL;
}

/*-----------ENDS (Payouts) -----------*/

/*------------ For Cashfree Payment Gateway (STARTS) --------------*/

function addPaymentData ($ret_arr) {

    $insert = [
        "order_id" => $ret_arr['orderId'],
        "user_id" => auth()->user()->id,
        "amount" => $ret_arr['orderAmount'],
//        "type" => 1, //For PIC
        "active" => isActive(),
        "created_at" => currentTime()
    ];

    $extra = [
        "data" => $insert,
        "id" => 1
    ];

    return insertData(Payment::class, $extra);
}

function updatePaymentData ($res_data) {

    $upd_pymt = updateData(Payment::class, [
        "update" => [
            "txn_id"    => $res_data['referenceId'],
            "response"  => json_encode($res_data),
            "amount"    => $res_data['orderAmount'],
        ],
        "where" => [
            "order_id" => $res_data['orderId'],
        ],
//        "na" => 1
    ]);

    return $upd_pymt;
//    return insertData(Payment::class, $extra);
}

function verifyPaymentResopnse($data)
{
    $secretkey      = env('CASHFREE_SECRET_KEY');

    $orderId        = $data["orderId"];
    $orderAmount    = $data["orderAmount"];
    $referenceId    = $data["referenceId"];
    $txStatus       = $data["txStatus"];
    $paymentMode    = $data["paymentMode"];
    $txMsg          = $data["txMsg"];
    $txTime         = $data["txTime"];
    $signature      = $data["signature"];

    $data = $orderId.$orderAmount.$referenceId.$txStatus.$paymentMode.$txMsg.$txTime;

    $hash_hmac = hash_hmac('sha256', $data, $secretkey, true);

    $computedSignature = base64_encode($hash_hmac);

    if ($signature == $computedSignature) {
        return true;
    } else {
        return false;
    }
}

function getPayoutAuthToken($forget = 0)
{

    if (!empty($forget)) {

        Cache::forget("cf_authtoken");

    }

    $contest = Cache::remember("cf_authtoken", 86000, function () {


        /*$cf_request = array();
        $cf_request["appId"] = "${APPLICATION_ID}";
        $cf_request["secretKey"] = "${SECRET_KEY}";
        $cf_request["orderId"] = "ORDER-104";
        $cf_request["orderAmount"] = 100;
        $cf_request["orderNote"] = "Subscription";
        $cf_request["customerPhone"] = "9000012345";
        $cf_request["customerName"] = "Test Name";
        $cf_request["customerEmail"] = "test@cashfree.com";
        $cf_request["returnUrl"] = "RETURNURL";
        $cf_request["notifyUrl"] = "NOTIFYURL";

        $timeout = 10;

        $request_string = "";
        foreach($cf_request as $key=>$value) {
            $request_string .= $key.'='.rawurlencode($value).'&';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"$opUrl?");
        curl_setopt($ch,CURLOPT_POST, count($cf_request));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $request_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $curl_result=curl_exec ($ch);
        curl_close ($ch);

        $jsonResponse = json_decode($curl_result);
        if ($jsonResponse->{'status'} == "OK") {
            $paymentLink = $jsonResponse->{"paymentLink"};
            //Send this payment link to customer over email/SMS OR redirect to this link on browser
        } else {
            //Log request, $jsonResponse["reason"]
        }*/

        return getTableData(ContestQuiz::class, [
            "select" => [
                "contest_quiz.id as id",
                "contest_quiz.name as quiz_name",
//                            "count",
                "contest_quiz.start_utc as start_utc",
                "contest_quiz.end_utc as end_utc",
                "sports.title as sport",
//                            "sports.id as sports_id",
                "media_links.media_url as pic",
                "contest_quiz.rules",
                'contest_quiz.count',
                'contest_quiz.price',
//                        'contest_participants.status',
                'contest_quiz.max_player_allowed',
                /*DB::raw("COUNT(DISTINCT contest_participants.user_id) as total_played,
                CASE
                    WHEN COUNT(contest_quiz.count) - COUNT(DISTINCT contest_answers.id) <= 0
                    THEN '1'
                    ELSE '0'
                    END as locked"),*/

            ],
            "whereOperand" => [
                [
                    "column" => "contest_quiz.start_utc",
                    "operand" => "<=",
                    "value" => currentTime(),
                ],
                [
                    "column" => "contest_quiz.end_utc",
                    "operand" => ">=",
                    "value" => currentTime(),
                ]
            ],
            "group" => [
                'contest_quiz.id'
            ],
//            'joins' => $joins,
        ]);
    });


    return $contest;
}

/*-------------ENDS (Payment Gateway) ---------------*/
