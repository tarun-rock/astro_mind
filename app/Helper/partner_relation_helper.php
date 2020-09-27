<?php

use App\Model\UserPartnerOtp;

function partnerSubscription($extra = [])
{

    switch ($extra['partnerID']){

        case returnConfig("partnerData.ooredoo"):




            $extra["packageID"] = $extra["package"];
            $extra["mobile"] = $number;


            $response = ooredooSubscription($extra);

            break;

        case 2:

            $response = ooredooSubscription($extra);

            break;

        case 3:

            $response = ooredooSubscription($extra);

            break;

        default:

            $response = ["status" => 200];

            break;
    }




    return $response;

}