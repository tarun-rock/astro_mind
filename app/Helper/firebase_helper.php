<?php


/**
 * Firebase Helper created to get data from Firebase Database Realtime cloud
 * @author: Kinshuk Lahiri
 * @date: 17-08-2017
 */


use Firebase\JWT\JWT;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\Facades\Log;

# Function to get access_token from Google JWT

function generateAccessToken()
{
    $cachedToken = Cache::read("firebaseAccessToken");

    if (empty($cachedToken)) {


        $token = [
            "iss" => Configure::read("firebaseIss"),
            "scope" => "https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/userinfo.email",
            "aud" => "https://accounts.google.com/o/oauth2/token",
            "exp" => time() + 3600,
            "iat" => time(),
        ];

        $privateKey = Configure::read("firebaseRSA");
        $jwt = JWT::encode($token, $privateKey, 'RS256');

        $res = shell_exec("curl -d 'grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Ajwt-bearer&assertion=" . $jwt . "
' https://accounts.google.com/o/oauth2/token");

        $access_token = json_decode($res)->access_token;
        Cache::write('firebaseAccessToken', $access_token, 'midterm_cache');
    } else {
        $access_token = $cachedToken;
    }
    return $access_token;


}

# Function to get data from Firebase

function getFirebaseData($access_token, $path = "")
{
    return shell_exec("curl 'https://" . Configure::read("firebaseProjectId") . ".firebaseio.com/" . $path . ".json?access_token=" . $access_token . "'");
}

# Function to save data to Firebase

function saveFirebaseData($access_token, $path = "", $data)
{
    shell_exec("curl -X PUT -d '" . $data . "' 'https://" . Configure::read("firebaseProjectId") . ".firebaseio.com/" . $path . ".json?access_token=" . $access_token . "'");
}

# Function to update data to Firebase with respective key

function updateFirebaseData($access_token, $path = "", $data)
{
    $str = "curl -X PATCH -d '" . $data . "' 'https://" . Configure::read("firebaseProjectId") . ".firebaseio.com/" . $path . ".json?access_token=" . $access_token . "'";

    shell_exec($str);
}

# Function to delete data from Firebase

function deleteFirebaseData($access_token, $path = "")
{
    $str = "curl -X DELETE 'https://" . Configure::read("firebaseProjectId") . ".firebaseio.com/" . $path . ".json?access_token=" . $access_token . "'";

    shell_exec($str);
}

# Function to push notification for all device

function firebaseNotification($add, $receiverId, $api = [], $players = [], $notification_id = 0, $all = 0)
{

    $extra = [
        "title" => $add['title'],
        "body" => $add['body'],
        "image" => $add['image'],
        "click_action" => $add['cta'],
        "extra" => $add['extra']
    ];

    $str = $istr = "";

    $stop = true;

    if (!empty($receiverId["android"])) //Android
    {

        $pushAndroid = firebaseAndroidPush($extra, $receiverId["android"]);

        $str = $pushAndroid['data'];

        $json = json_encode($pushAndroid['json']);

        $stop = false;

    }


    if (!empty($receiverId["ios"])) {

        $pushiOS = firebaseiOSPush($extra, $receiverId["ios"]);

        $istr = $pushiOS['data'];

        $json = json_encode($pushiOS['json']);

        $stop = false;

    }

    if ($stop) {

        return 0;

    }


    if (isProd()) {


        if (!empty($istr)) {

            $s = shell_exec($istr);

                Log::debug("Notification iOS Response: " . $s);
                Log::debug("Notification iOS Text: " . $istr);

        }

        if (!empty($str)) {

            $s = shell_exec($str);

            Log::debug("Notification Android Response: " . $s);
            Log::debug("Notification Android Text: " . $str);

        }

        if (!empty($api)) {

            if (empty($notification_id)) {

                $insert = [
                    "title" => $add['title'],
                    "body" => $add['body'],
                    "group_id" => $add['group_id'] ?? 0,
                    "data" => $json,
                    "created_by" => $api['senderId'],
                    "device_type_id" => $api['deviceTypeId'],
                    "created_utc" => currentTime()
                ];

//                $data = insertData("SysNotifications", $insert);

//                $notification_id = $data->notification_id;

            }

            /*if (!empty($players) && empty($all)) {

                foreach ($players as $player) {

                    $insert = [
                        "notification_id" => $notification_id,
                        "player_id" => $player,
                        "is_active" => 1,
                        "created_utc" => currentTime()
                    ];

                    insertData("SysNotificationUser", $insert);

                }

            }*/

            /*if (!empty($all) && $all == 1) {

                $insert = [
                    "notification_id" => $notification_id,
                    "player_id" => 0,
                    "is_active" => 1,
                    "created_utc" => currentTime()
                ];

                insertData("SysNotificationUser", $insert);

            }*/

        }

    }

//    return $notification_id;

}

function firebaseAndroidPush($extra, $receiverId)
{

    $data = [
        "data" => (object)[
            "title" => $extra["title"],
            "body" => $extra["body"],
            "image" => $extra["image"],
            "click_action" => $extra["click_action"],
            "data" => (object)$extra['extra']
        ],
        "registration_ids" => $receiverId
    ];

    $json = json_encode($data, JSON_HEX_APOS);

    $str = "curl --header \"Authorization: key=" . returnConfig("firebaseSereverKey") . "\" \
       --header Content-Type:\"application/json\" \
       https://fcm.googleapis.com/fcm/send \
       -d '" . $json . "'";

    $json = $data["data"];

    return ["json" => $json, "data" => $str];

}

function firebaseiOSPush($extra, $receiverId)
{


    $data = [
        "notification" => (object)[
            "title" => $extra["title"],
            "body" => $extra["body"],
            "image" => $extra["image"],
            "click_action" => $extra["click_action"],
            "data" => (object)$extra['extra']
        ],
        "registration_ids" => $receiverId
    ];

    $json = json_encode($data, JSON_HEX_APOS);

    $str = "curl --header \"Authorization: key=" . returnConfig("firebaseSereverKey") . "\" \
       --header Content-Type:\"application/json\" \
       https://fcm.googleapis.com/fcm/send \
       -d '" . $json . "'";

    $json = $data["notification"];

    return ["json" => $json, "data" => $str];

}

function create_custom_token($uid)
{
    $service_account_email = config("services.firebase.firebaseAdmin_client_email");

    $private_key = str_replace("\\n", "\n", config("services.firebase.firebaseAdminRSA"));

    $now_seconds = time();
    $payload = array(
        "iss" => $service_account_email,
        "sub" => $service_account_email,
        "aud" => "https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit",
        "iat" => $now_seconds,
        "exp" => $now_seconds + (60 * 60),  // Maximum expiration time is one hour
        "uid" => $uid,
        /*"claims" => array(
            "premium_account" => $is_premium_account
        )*/
    );
    return JWT::encode($payload, $private_key, "RS256");

}




function idToken($uid)
{

    $token = create_custom_token($uid);

    $ch = curl_init();

    $authToken = config("services.firebase.firebaseAuthToken");

    curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/verifyCustomToken?key='.$authToken);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"token\":\"$token\",\"returnSecureToken\":true}");
    curl_setopt($ch, CURLOPT_POST, 1);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close ($ch);

    return json_decode($result);

}

function getFirebaseUserProfile($firebaseIDToken)
{

    $authToken = config("services.firebase.firebaseAuthToken");

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/getAccountInfo?key='.$authToken);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"idToken\":\"$firebaseIDToken\"}");
    curl_setopt($ch, CURLOPT_POST, 1);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);

    return json_decode($result);

}


function changeFirebasePassword($firebaseIDToken, $password){


    $authToken = config("services.firebase.firebaseAuthToken");

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/setAccountInfo?key='.$authToken);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"idToken\":\"$firebaseIDToken\",\"password\":\"$password\",\"returnSecureToken\":true}");
    curl_setopt($ch, CURLOPT_POST, 1);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);

    return json_decode($result);


}

function firestoreConnection()
{
    $db = new FirestoreClient(['projectId' => env('FIRESTORE_PROJECT_ID'),
        'keyFilePath' => storage_path('app/public/' . env("FIRESTORE_KEY_FILE_PATH"))]);

    return $db;

}