<?php

use App\Model\Answers;
use App\Model\AnswerTranslation;
use App\Model\AppVersion;
use App\Model\Contest;
use App\Model\ContestParticipation;
use App\Model\ContestParticipant;
use App\Model\ContestPrizeDistribution;
use App\Model\ContestQuiz;
use App\Model\ContestQuizQuestions;
use App\Model\ContestTickets;
use App\Model\ContestWinners;
use App\Model\ContestAnswers;
use App\Model\MasterReward;
use App\Model\MediaLink;
use App\Model\PartnerModule;
use App\Model\PredictorCategory;
use App\Model\Questions;
use App\Model\QuestionTranslation;
use App\Model\MetaSetting;
use App\Model\Sport;
use App\Model\Teams;
use App\Model\UserOtp;
use App\Model\UserPartnerOtp;
use App\Model\Recipient;
use App\Model\Payment;
use App\Model\Currency;

use App\Model\ContestParticipants;

use App\Model\UserRewards;
use App\Notifications\Otp;
use App\User;
use Illuminate\Notifications\Notifications;
/*use Notification;*/

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

//use Cache;

/**
 * Created by PhpStorm.
 * User: kinshuk
 * Date: 28/1/19
 * Time: 10:04 AM
 */

function returnConfig($name)
{

    return config("constant.$name");

}

function isActive()
{

    return returnConfig("active");

}


function globalSeparator()
{

    return returnConfig("column_separator");

}

function currentTime()
{

    return \Carbon\Carbon::now();

}

# Function to create secure random chars for token

function crypto_rand_secure($min, $max)
{

    $range = $max - $min;

    if ($range < 1) return $min; // not so random...

    $log = ceil(log($range, 2));

    $bytes = (int)($log / 8) + 1; // length in bytes

    $bits = (int)$log + 1; // length in bits

    $filter = (int)(1 << $bits) - 1; // set all lower bits to 1

    do {

        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));

        $rnd = $rnd & $filter; // discard irrelevant bits

    } while ($rnd > $range);

    return $min + $rnd;

}


# Function to generate token by length

function getToken($length)
{
    $token = "";

    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";

    $codeAlphabet .= "0123456789";

    $max = strlen($codeAlphabet); // edited

    for ($i = 0; $i < $length; $i++) {

        $token .= $codeAlphabet[crypto_rand_secure(0, $max - 1)];

    }

    return $token;
}

function checkUniqueBeneIdExists($beneId)
{

    $extra = [
        "select" => ["recipient_id"],
        "where" => [
            "recipient_id" => $beneId
        ],
        "na" => 1,
        "single" => 1
    ];

    $check = getTableData(Recipient::class, $extra);

    if (!empty($check->recipient_id)) {

        return 1;
    }
    else {

        return 0;
    }
}

function getRecipientDetail($input)
{

    $extra = [
        "select" => ["id", "recipient_id", "payment_mode"],
        "where" => [
            "id" => $input['bene_id'],
            "user_id" => $input['user_id'],
        ],
//        "na" => 1,
        "single" => 1
    ];

    $recip_data = getTableData(Recipient::class, $extra);

    if (!empty($recip_data->recipient_id)) {

        return $recip_data;
    }
    else {

        return false;
    }
}

function playerUniqueAppId()
{

    $found = 1;

    while ($found != 0) {


        $token = getToken(8);

        $extra = [
            "select" => ["app_id"],
            "where" => [
                "app_id" => $token
            ],
            "na" => 1,
            "single" => 1
        ];

        $check = getTableData(User::class, $extra);

        if (empty($check->app_id)) {

            $found = 0;

        }
    }

    return $token;

}

function uploadFile($file, $path, $extra = [])
{

    if (!empty($file)) {

        $destination = base_path("public/" . $path);

        $ext = strtolower($file->getClientOriginalExtension());

        $name = Str::random(2) . "_" . time() . "." . $ext;

        $file->move($destination, $name);

        $links = url($path, [$name]);

        $response = ["name" => $name, "path" => $links, "file" => $destination . $name];

        if (empty($extra["na"])) {
            $insert = [
                "media_url" => $links,
                "type" => 1, //For PIC
                "active" => isActive(),
                "created_at" => currentTime()
            ];

            $extra = [
                "data" => $insert,
                "id" => 1
            ];

            $mediaID = insertData(MediaLink::class, $extra);

            $response["media_id"] = $mediaID;

        }

        return $response;

    }
}

function signInSource($provider)
{
    $response = returnConfig('email_sign_in');

    $custom = returnConfig('custom_sign_in');

    if (strpos($provider, 'google') !== false) {

        $response = returnConfig('google_sign_in');

    } else if (strpos($provider, 'facebook') !== false) {

        $response = returnConfig('fb_sign_in');

    } else if (strpos($provider, 'twitter') !== false) {

        $response = returnConfig('twitter_sign_in');

    } else if ($provider == $custom) {

        $response = $custom;

    }

    return $response;

}

function deviceTypeID($device)
{

    $response = 0;

    if ($device == "A") {
        $response = returnConfig("android");
    } else if ($device == "I") {

        $response = returnConfig("ios");

    }

    return $response;

}

function userProfile($uid)
{

    $extra = [
        "select" => [
            "name",
            "email",
            "id",
            "password",
            "app_id",
        ],
        "where" => [
            "uid" => $uid
        ],
        "single" => 1
    ];

    return getTableData(User::class, $extra);

}

function getUserProfileSpecific($userID, $column = [])
{

    return getTableData(User::class, [
        "select" => $column,
        "where" => [
            "id" => $userID
        ],
        "single" => 1
    ]);

}

function getUserIDFromApp($appID)
{

    return getTableData(User::class, [
        "select" => [
            "id"
        ],
        "where" => [
            "app_id" => $appID
        ],
        "single" => 1
    ]);

}

function isProd()
{

    return env("PROD", 0);

}

function userInfo($data)
{


    $appID = $data["app_id"] ?? 0;

    $userID = $data["user_id"] ?? 0;

    $where = [
        "app_id" => $appID
    ];

    if (empty($appID)) {

        $where = [
            "users.id" => $userID
        ];

    }

    $extra = [
        "select" => [
            "users.name",
            "users.email",
            "users.id",
            "users.steps",
            "users.device_type_id",
            "users.app_id",
            "users.lang_id",
            "api_token",
            DB::raw("ifnull(user_partner_relations.partner_id,0) as partner,
                           ifnull(countries.calling_code,0) as cc,
                           ifnull(MDLINK.media_url,'" . asset('img/logo/ooredoo_logo_new.png') . "') as brand_logo,
                           ifnull(media_links.media_url,'" . getDefaultProfilePic() . "') as pic")
        ],
        "joins" => [
            [
                "type" => returnConfig("left_join"),
                "table" => "media_links",
                "left_condition" => "media_links.id",
                "right_condition" => "users.media_id"
            ],
            [
                "type" => returnConfig("left_join"),
                "table" => "user_partner_relations",
                "left_condition" => "user_partner_relations.user_id",
                "right_condition" => "users.id"
            ],
            [
                "type" => returnConfig("left_join"),
                "table" => "biz_partners",
                "left_condition" => "biz_partners.id",
                "right_condition" => "user_partner_relations.partner_id"
            ],
            [
                "type" => returnConfig("left_join"),
                "alias" => "media_links as MDLINK",
                "table" => "MDLINK",
                "left_condition" => "MDLINK.id",
                "right_condition" => "biz_partners.media_id"
            ],
            [
                "type" => returnConfig("left_join"),
                "table" => "countries",
                "left_condition" => "biz_partners.country_id",
                "right_condition" => "countries.id"
            ],
        ],
        "where" => $where,
        "single" => 1
    ];

    $userInfo = getTableData(User::class, $extra);

    $keepB = [1, 3];

    $userInfo->logo = asset('img/logo/ooredoo_logo_new.png');

    //TODO Remove hardcoded value for keepB for others, Switch to constants and add check in API to prevent false access

    if (!empty($userInfo->partner)) {

        $keepB = getTableData(PartnerModule::class, [
            "select" => [
                "module_id"
            ],
            "where" => [
                "partner_id" => $userInfo->partner
            ]
        ])->toArray();

        $keepB = array_values(array_column($keepB, "module_id"));

        if (!empty($userInfo->brand_logo)) {
            $userInfo->logo = $userInfo->brand_logo;

            unset($userInfo->brand_logo);
        }

    }

    $contestCount = 1;

    if (!empty($userInfo->partner)) {

        $contestDetails = getTableData(Contest::class, [
            "select" => [
                DB::raw("count(contests.id) as count")
            ], "joins" => [
                [
                    "type" => returnConfig("inner_join"),
                    "table" => "partner_contests",
                    "left_condition" => "partner_contests.contest_id",
                    "right_condition" => "contests.id",
                    "conditions" => [
                        "partner_contests.partner_id" => $userInfo->partner
                    ]
                ],
            ], "single" => 1
        ]);

        $contestCount = $contestDetails->count;

    }

    $userInfo->keepB = $keepB;
    $userInfo->contest_count = $contestCount > 1 ? 1 : 0;

    $userInfo->ques_rem = userPartnerRechargeValue($userInfo->id);

    unset($userInfo->id);

    $minVersion = getTableData(AppVersion::class, [
        "select" => [
            'android',
            'ios'
        ],
        "single" => 1]);

    $userInfo->upgrade = ($userInfo->device_type_id == returnConfig("android")) ? $minVersion->android : $minVersion->ios;


    return $userInfo;

}

function userPartnerRechargeValue($userID): int
{


    $sum = getTableData(\App\Model\PartnerRecharge::class, [
        "select" => [
            DB::raw("SUM(value) as sum"),
            DB::raw("SUM(used) as used")
        ],
        "where" => [
            "user_id" => $userID,
            "paid" => 1
        ],
        "whereOperand" => [[
            "column" => "end_date",
            "operand" => ">=",
            "value" => currentTime()
        ]],
        "single" => 1
    ]);

    $val = $sum->sum - $sum->used;

    if ($val < 0) {
        $val = 0;

    }

    return $val ?? 0;

}

function getPlayerRankContestNew($playerId, $contestID, $extra = [])
{

    $contestCondition = "";

    if (!empty($contestID)) {

        $contestCondition = 'AND contest_quiz.contest_id = ' . $contestID;

    }


    $level_condition = $dateCondition = $userCondition = "";


    if (!empty($extra['level'])) {

        $level_condition = " AND t.quiz_id =" . $extra['level'];

    }

    if (!empty($extra['date'])) {

        $dateCondition = $extra['date'];
    }

    if (!empty($playerId)) {

        $userCondition = 'AND m.user_id = ' . $playerId;
    }

    /*SELECT m.profile_pic as pic,m.name,m.scorer AS points, m.timing, m.user_id, CAST(m.rank AS UNSIGNED) as rank FROM (
                                              SELECT d.user_id,d.scorer,  @rownum := @rownum + 1 AS rank,  d.name, d.profile_pic, d.timing FROM (
                                                SELECT t.user_id,SUM(t.score) as scorer,SUM(t.time) as timing,users.name,
                                                IFNULL(NULLIF(media_links.media_url,""),"' . getDefaultProfilePic() . '") as profile_pic
                                                FROM contest_sessions t JOIN users ON users.id = t.user_id
                                                LEFT JOIN media_links ON users.media_id = media_links.id AND media_links.active = ' . isActive() . '*/


    $query = 'SELECT m.profile_pic as pic, m.name, m.rank,CAST(m.scorer AS UNSIGNED) AS points,m.user_id, m.timing FROM (
                                              SELECT d.user_id,d.scorer,  @rownum := @rownum + 1 AS rank, d.name, d.profile_pic, d.timing FROM (
                                                SELECT t.user_id,SUM(t.score) as scorer,SUM(t.time) as timing, users.name,
                                                IFNULL(NULLIF(media_links.media_url,""),"' . getDefaultProfilePic() . '") as profile_pic
                                                FROM contest_sessions t JOIN users ON users.id = t.user_id
                                                LEFT JOIN media_links ON users.media_id = media_links.id AND media_links.active = ' . isActive() . '
                                                JOIN contest_quiz ON t.quiz_id = contest_quiz.id
                                                WHERE t.active = ' . isActive() . ' ' . $contestCondition . '  AND users.active = ' . isActive() . ' ' . $level_condition . $dateCondition . '
                                                GROUP BY t.user_id
                                                ORDER BY scorer DESC, timing ASC, t.created_at ASC) d,
                                              (SELECT @rownum := 0) r) m
                                              WHERE m.scorer > 0 '. $userCondition;

    $results = DB::select($query);

//    dd($query);

    $finalPlayerStat = ["rank" => 0, "points" => 0];

//    dd($results);

    if (!empty($results[0])) {

        $finalPlayerStat = (array)$results[0];

    }

    //$finalPlayerStat["prize"] = contestPrize($playerId);

    return $finalPlayerStat;
}

function contestPrize($playerID, $type = 0, $formatDate = ''): string
{

    $prize = "0.00";

    $where = [
        "user_id" => $playerID,

    ];

    if (!empty($type)) {

        $where["type"] = $type;

    }
    if (!empty($formatDate)) {

        $where['created_at >='] = $formatDate;

    }

    $result = getTableData(ContestWinners::class, [
        "select" => [
            DB::raw("SUM(prize) as sum")
        ],
        'where' => $where,
        'single' => 1
    ]);

    if (!empty($result->sum)) {

        $prize = (string)$result->sum;

    }

    return $prize;

}


function rankBasedPrize($contestID, $awardedRank)
{

    $prizes = getTableData(ContestPrizeDistribution::class, [
        "select" => [
            "count", "amount"
        ],
        'where' => [
            "contest_id" => $contestID
        ]
    ]);

    $winnings = '-';

    if (!empty($awardedRank)) {

        foreach ($prizes as $prize) {

            $awardedRank -= $prize->count;

            if ($awardedRank <= 0) {

                $winnings = $prize->amount;

                break;

            }

        }

    }

    return $winnings;

}

function getWinnersList()
{

    $winners_list = getTableData(UserRewards::class, [

        "select" => [
            DB::raw("SUM('user_rewards.points') as total_win"),
            'users.name',
            'user_rewards.user_id',
            'media_links.media_url as user_pic',

        ],
        "where" => [
            'user_rewards.master_id' => 4
        ],
        "group" => [
            'user_rewards.user_id'
        ],
        'joins' => [
            [
                "table" => "users",

                'type' => returnConfig("inner_join"),

                "left_condition" => "user_rewards.user_id",

                "right_condition" => "users.id",
            ],
            [
                'table' => 'media_links',

                'type' => returnConfig("inner_join"),

                'left_condition' => 'users.media_id',

                'right_condition' => 'media_links.id'
            ]
        ],
        "having" => "total_win > 0"
    ]);

    $sorted_list = $winners_list->sortByDesc('total_win');

    return $sorted_list->values()->all();

}

function getRecentWinners()
{

    $winners_list = getTableData(UserRewards::class, [

        "select" => [
            'user_rewards.points',
            'users.name',
            'user_rewards.user_id',
            'user_rewards.created_at',
            'media_links.media_url as user_pic',

        ],
        "where" => [
            'user_rewards.master_id' => 4
        ],
        /*"group" => [
            'user_rewards.user_id'
        ],*/
        'joins' => [
            [
                "table" => "users",

                'type' => returnConfig("inner_join"),

                "left_condition" => "user_rewards.user_id",

                "right_condition" => "users.id",
            ],
            [
                'table' => 'media_links',

                'type' => returnConfig("inner_join"),

                'left_condition' => 'users.media_id',

                'right_condition' => 'media_links.id'
            ]
        ],
        "limit" => 3,
        "order" => [
            'user_rewards.created_at' => 'DESC',
            'user_rewards.points' => 'DESC',
        ]
        // "having" => "total_win > 0"
    ]);

    $sorted_list = $winners_list->sortByDesc('total_win');

    return $sorted_list->values()->all();

}

function checkUserAuthenticated($api_token)
{

    return getTableData(User::class, [
        "select" => ["users.id"],
        "where" => [
            "api_token" => $api_token,
            "type" => returnConfig("user"),
        ],

        "single" => isActive()
    ]);

}

function getUserID($request): int
{

    //$data = $request->attributes->get('userID');

    //$userID = trim($data);

    return Auth::id();

}

function getPartnerID($request): int
{

    $data = $request->attributes->get('partnerID');

    $partnerID = (int)trim($data);

    return $partnerID;

}

function getLangID($request): int
{

    $data = $request->attributes->get('langID');

    $langID = (int)trim($data);

    if ($langID == 2) {

        App::setLocale("fr");

    }

    return $langID;

}

function sortByTime($a, $b)
{

    $a = strtotime($a['end_utc']);

    $b = strtotime($b['end_utc']);

    return $b - $a; // DESC

}

function participantCheck($userID, $contestID)
{

    $check = getTableData(ContestParticipant::class, [
        "select" => ['contest_participants.id', "contests.points", "contests.time",
            DB::raw("CONCAT(hint_curr.sign,' ', hint.amount) as hint_package,
                           CONCAT(fifty_curr.sign,' ', fifty.amount) as fifty_package"
            )
        ],
        "where" => ['contest_participants.user_id' => $userID, 'contest_participants.contest_id' => $contestID],
        "joins" => [
            [
                "table" => "contests",
                "type" => returnConfig("inner_join"),
                "right_condition" => "contests.id",
                "left_condition" => "contest_participants.contest_id"
            ],
            [
                "table" => "hint",
                "alias" => "partner_packages as hint",
                "type" => returnConfig("left_join"),
                "right_condition" => "contests.hint_package_id",
                "left_condition" => "hint.id"
            ],
            [
                "table" => "hint_curr",
                "alias" => "currencies as hint_curr",
                "type" => returnConfig("left_join"),
                "right_condition" => "hint_curr.id",
                "left_condition" => "hint.currency_id"
            ],
            [
                "table" => "fifty",
                "alias" => "partner_packages as fifty",
                "type" => returnConfig("left_join"),
                "right_condition" => "contests.hint_package_id",
                "left_condition" => "fifty.id"
            ],
            [
                "table" => "fifty_curr",
                "alias" => "currencies as fifty_curr",
                "type" => returnConfig("left_join"),
                "right_condition" => "fifty_curr.id",
                "left_condition" => "fifty.currency_id"
            ]
        ],
        "single" => 1
    ]);


    return $check;

    /* $status = false;

     if (empty($check->id)) {

         $status = true;
     }


     return $status;*/

}

function ticketCount($playerID): int
{

    $sum = getTableData(ContestTickets::class, [
        "single" => 1,
        "where" => ['user_id' => $playerID, 'redeem' => 0],
        "select" => [
            DB::raw("SUM(ticket) as ticket"),
        ]
    ]);

    $spent = getTableData(ContestParticipation::class, [
        "select" => [
            DB::raw("SUM(ticket) as ticket"),
        ],
        "where" => ['user_id' => $playerID],
        "single" => 1
    ]);


    $redeem = getTableData(ContestTickets::class, [
        "select" => [DB::raw("SUM(ticket) as ticket")],
        "where" => ['user_id' => $playerID, 'redeem' => 1],
        "single" => 1
    ]);


    $left = $sum['ticket'] - ($spent['ticket'] + $redeem['ticket']);

    return $left;
}


function contestLevelReached($contestID, $playerID, $langID = 1)
{

    $overall = getTableData(ContestQuiz::class, [
        "select" => [
            DB::raw("DISTINCT contest_answers.quiz_id as quiz_id,
            IF(contest_quiz_translations.name IS NULL,contest_quiz.name,contest_quiz_translations.name) as name"),
            "contest_quiz.id",
            "start_utc",
            "end_utc"
        ],
        "joins" => [
            [
                'table' => 'contest_answers',
                'type' => returnConfig("left_join"),
                'left_condition' => 'contest_quiz.id',
                'right_condition' => 'contest_answers.quiz_id',
                'conditions' => [
                    'contest_answers.user_id' => $playerID,
                ]
            ],
            [
                "table" => "contest_quiz_translations",
                "type" => returnConfig("left_join"),
                "left_condition" => "contest_quiz_translations.quiz_id",
                "right_condition" => "contest_quiz.id",
                "conditions" => [
                    "contest_quiz_translations.lang_id" => $langID
                ]
            ]
        ],
        "where" => [
            "contest_id" => $contestID
        ],
        "order" => [
//            "start_utc" => "ASC"
        ]
    ])->toArray();

    $playedFound = array_filter($overall, function ($value) {
        return !empty($value["quiz_id"]);
    });

    $quiz = array_unique(array_column($playedFound, "quiz_id"));

    $next = 0;

    $name = "";

    $played = array_slice($overall, 0, count($quiz));

    if (count($quiz) != count($overall)) {

        if ($overall[count($played)]['start_utc'] > currentTime()) {

            $name = $overall[count($played)]['name'] . " Coming Soon";

            $next = -1;

        } else {


            if ($overall[count($played)]['end_utc'] < currentTime()) {

                $name = "Coming Soon";

                $next = -1;
            } else {

                $name = $overall[count($played)]['name'];

                $next = $overall[count($played)]['id'];

            }


        }


    }

    $next = [
        "status" => $next,
        "name" => $name
    ];

    $currentLevel = 0;

    /* if(!empty($quiz))
     {

     /*
             $currentLevel = getTableData("plr_contest_answers",[
                 "points" => "SUM(score)"
             ],[
                 "quiz_id" => $quiz[count($quiz) - 1],
                 "player_id" => $playerID
             ])->first();/

             $currentLevel =  $quiz[count($quiz) - 1];

     } */


    /*    if(!empty($currentLevel))
        {
            $currentLevelScore = $currentLevel->points;

        }*/

    $data = [
        "levels" => $overall,
        "next" => $next,
        "played" => $played,
        //  "current_level" => $currentLevel
    ];

    return $data;

}

function getPlayerContestRankLevelWise($playerId, $contestID = 1, $level_condition = "")
{

    $query = 'SELECT m.rank,m.scorer AS points,m.user_id, m.quiz_id  FROM (
                                              SELECT d.user_id,d.scorer, @rownum:= CASE WHEN @quiz_id <> d.quiz_id THEN 1 ELSE @rownum+1 END as rank, @quiz_id:= d.quiz_id as quiz_id FROM (SELECT @rownum := 1) r,(SELECT @quiz_id := 0) c,(
                                                SELECT t.user_id,SUM(t.score) as scorer, t.quiz_id
                                                FROM contest_sessions t JOIN users ON users.id = t.user_id
                                                JOIN contest_quiz ON t.quiz_id = contest_quiz.id
                                                WHERE t.active = ' . isActive() . ' AND contest_quiz.contest_id = ? AND users.active = ' . isActive() . ' ' . $level_condition . '
                                                GROUP BY t.user_id,t.quiz_id
                                                ORDER BY quiz_id DESC,scorer DESC, t.created_at ASC) d
                                              ) m
                                              WHERE m.scorer > 0 AND m.user_id = ? ORDER BY quiz_id';


    $results = DB::select($query, [$contestID, $playerId]);

    return $results;

}

function getCurrentQuizRankNew($quizID, $playerID)
{

    $query = 'SELECT m.rank,m.scorer AS points FROM (
                                          SELECT d.user_id,d.scorer,  @rownum := @rownum + 1 AS rank FROM (
                                            SELECT t.user_id,SUM(t.score) as scorer,SUM(t.time) as timing
                                            FROM contest_sessions t JOIN users ON users.id = t.user_id
                                            WHERE t.quiz_id = ? AND t.active = ' . isActive() . 'AND users.active = ' . isActive() . '
                                            GROUP BY t.user_id
                                            ORDER BY scorer DESC, timing ASC, t.created_at ASC) d,
                                          (SELECT @rownum := 0) r) m
                                          WHERE m.scorer > 0 AND m.user_id = ?';

    $results = DB::select($query, [$quizID, $playerID]);

    $finalPlayerStat = ["rank" => 0, "points" => 0];

    if (!empty($results[0])) {

        $finalPlayerStat = (array)$results[0];

    }

    return $finalPlayerStat;
}

function winnerList($contest_id)
{

    $cpd = contestPrizeDistributionsDetail($contest_id);

    if($cpd)
    {

        $lb_limit = $cpd->sum('count');
        
    }

    $total = getOverallPlayerRankContestNew($contest_id,$lb_limit);


    $prizes = getTableData(ContestPrizeDistribution::class ,[
            "select" => [
                "rank",
                "amount",
                "start_index",
                "count"
            ],
            "where" => [

                "contest_id" => $contest_id
            ]
    ]);

        foreach ($prizes as  $ky => $val) {

            $rank = $val['rank'];
            $amount = $val['amount'];
            $index = $val['start_index']; 
            $count = $val['count']; 


            for( $i = $index ; $i < $index + $count ; $i++)
            {      

                if(empty($total[$index]))
                {
                    break;
                }    
                $user_id= $total[$index]->user_id;

                userReward($user_id , 1 , $amount , $contest_id , 4);

            }

        }

}

function getContestNewLeaderboard($playerId, $contestID, $limit, $extra = [])
{

    $data = getOverallPlayerRankContestNew($contestID, $limit, $extra); // 

    $finalPlayerStat = getPlayerRankContestNew($playerId, $contestID, $extra);

    $response = ["stats" => $data, "player_stat" => $finalPlayerStat];

    return $response;

}

function getOverallPlayerRankContestNew($contestID, $limit = 100, $extra = [])
{

    $level_condition = $dateCondition = "";


    if (!empty($extra['level'])) {

        $level_condition = " AND t.quiz_id =" . $extra['level'];
    }

    if (!empty($extra['date'])) {

        $dateCondition = $extra['date'];
    }

    $query = 'SELECT m.profile_pic as pic,m.name,m.scorer AS points, m.timing, m.user_id, CAST(m.rank AS UNSIGNED) as rank FROM (
                          SELECT d.user_id,d.scorer,  @rownum := @rownum + 1 AS rank,  d.name, d.profile_pic, d.timing FROM (
                            SELECT t.user_id,SUM(t.score) as scorer,SUM(t.time) as timing,users.name,
                            IFNULL(NULLIF(media_links.media_url,""),"' . getDefaultProfilePic() . '") as profile_pic
                            FROM contest_sessions t JOIN users ON users.id = t.user_id
                            LEFT JOIN media_links ON users.media_id = media_links.id AND media_links.active = ' . isActive() . '
                            JOIN contest_quiz ON t.quiz_id = contest_quiz.id
                            WHERE contest_quiz.contest_id = ? AND users.active = ' . isActive() . ' AND t.active = ' . isActive() . $level_condition . $dateCondition . '
                            GROUP BY t.user_id
                            ORDER BY scorer DESC, timing ASC, t.created_at ASC) d,
                          (SELECT @rownum := 0) r) m
                           WHERE m.scorer > 0 LIMIT ?';


    $results = DB::select($query, [$contestID, $limit]);
//    dd($results);

    return $results;
}

function getDefaultProfilePic()
{

    $target_path = asset('app/player/pic/profile-icon.png');

    return $target_path;

}

function scheduleMatches()
{

    return Cache::remember("matchSchedule", 480, function () {


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.sportradar.us/nba/trial/v5/en/games/2018/REG/schedule.json?api_key=' . env("SPORTRADAR_KEY"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($result, true);

    });

}

function getTeams()
{

    return getTableData(Teams::class, [
        "select" => [
            "team_id as game_id",
            "media_links.media_url as pic"
        ],
        "joins" => [
            [
                "table" => "media_links",

                "type" => returnConfig("inner_join"),

                "left_condition" => "media_links.id",

                "right_condition" => "teams.media_id",
            ]
        ]
    ]);

}

function signUpSteps($step, $userID)
{
    $data = [
        "steps" => $step,
    ];

    $where = [
        "id" => $userID,
    ];

    return updateData(User::class, [
        "update" => $data,
        "where" => $where
    ]);

}

function partnerMatch($match)
{

    $data = returnConfig("partnerData");

    $response = 0;

    if (!empty($data[$match])) {

        $response = $data[$match];

    }

    return $response;

}

function getUserPredictorRank($userID, $date_condition = "", $extra = [])
{

    $category = !empty($extra["category"]) ? " AND predictions.category_id = " . $extra["category"] : "";

    $week = !empty($extra["week"]) ? " AND predictions.week = " . $extra["week"] : "";

    $query = 'SELECT m.rank,m.scorer AS points,m.user_id FROM (
                                              SELECT d.user_id,d.scorer,  @rownum := @rownum + 1 AS rank FROM (
                                                SELECT t.user_id,SUM(t.score) as scorer
                                                FROM predictor_poll_answers t
                                                JOIN users ON users.id = t.user_id
                                                JOIN predictor_poll_options ON predictor_poll_options.id = t.answer_id
                                                JOIN predictor_polls ON predictor_poll_options.poll_id = predictor_polls.id
                                                JOIN predictions ON predictor_polls.predictor_id = predictions.id AND predictions.category_id != 0 ' . $category . $week . '
                                                WHERE users.active = ' . isActive() . ' AND t.active = ' . isActive() . $date_condition . '
                                                GROUP BY t.user_id
                                                ORDER BY scorer DESC, t.created_at ASC) d,
                                              (SELECT @rownum := 0) r) m
                                              WHERE m.scorer > 0 AND m.user_id = ?';


    $results = DB::select($query, [$userID]);
    $finalPlayerStat = (object)["rank" => "0", "points" => "0"];

    if (!empty($results[0])) {


        $finalPlayerStat = $results[0];

        $finalPlayerStat->rank = (string)$results[0]->rank;

        $finalPlayerStat->prize = "0.00"; //contestPrize($results[0]["player_id"]);


    } else {

        $finalPlayerStat->prize = "0.00";

    }

    return $finalPlayerStat;

}

function getPredictorCategory()
{

    $table = "PlrPredictor";

    $category = getTableData(PredictorCategory::class, [
        "select" => [
            "predictor_categories.id",
            "predictor_categories.name",
            "media_links.media_url as pic",
            DB::raw("MAX($table.end_utc) as end_utc"),
        ],
        "order" => [
            "rank" => "ASC",
            "end_utc" => "DESC"
        ],
        "group" => ["predictor_categories.id"],
        "joins" => [
            [

                'table' => $table,

                'alias' => "predictions as $table",

                'type' => returnConfig("inner_join"),

                "left_condition" => "predictor_categories.id",

                "right_condition" => "$table.category_id",

                'conditions' => [

                    "$table.start_utc" => [
                        "operand" => ">=",
                        "value" => currentTime()
                    ],

                    "$table.end_utc" => [
                        "operand" => ">=",
                        "value" => currentTime()
                    ],

                ]
            ],
            [
                "type" => returnConfig("inner_join"),
                "table" => "media_links",
                "left_condition" => "media_links.id",
                "right_condition" => "predictor_categories.media_id"
            ]
        ]])->toArray();


    return array_values($category);

}

function getPredictorLeaderboardData($playerId, $days = 0, $limit = 100, $more = 0, $extra = [])
{

    /*$where = [
        "plr_player_profile.is_active" => 1,
        "PlrPollAnswers.is_active" => 1,
    ];*/

    $date_condition = "";

    /*$query = TableRegistry::get("PlrPollAnswers")->find()
        ->join([
            'table' => 'plr_player_profile',
            'type' => 'INNER',
            'conditions' => 'plr_player_profile.player_id = PlrPollAnswers.player_id'
        ])
        ->where($where)
        ->where(function ($exp) {
            return $exp->notEq("plr_player_profile.signin_source_id", guestSignInSourceId());
        });*/

    if (!empty($days)) {


        $start_date = date('Y-m-d', strtotime("-$days days", strtotime(date("Y-m-d")))) . " 00:00:00";

        $end_date = date('Y-m-d') . " 23:59:59";


        /*$query->where(function ($exp) use($start_date, $end_date) {

            return $exp->between("PlrPollAnswers.created_utc", $start_date, $end_date);

        });*/

        $date_condition = " AND t.created_at BETWEEN '" . $start_date . "' AND '" . $end_date . "'";

    }

    /*$query->select([
        "points" => $query->func()->sum('score'),
        "player_id",
        "name" => "plr_player_profile.name"
    ]);


    $query->group("PlrPollAnswers.player_id");

    $query->orderDesc("points");

    $query->limit($limit);

    $data = $query->toArray();

    $data = array_values($data);*/

    $offset = $limit * $more;

    $data = getOverallPlayerRankPredictor($date_condition, $limit, $offset, $extra);

    foreach ($data as &$d) {

        $d->points = (int)$d->points;

        if (empty($d->pic)) {

            $d->pic = getDefaultProfilePic();

        }

        $d->prize = "0.00"; //contestPrize($d["player_id"]);

    }

    unset($d);

    $finalPlayerStat = getUserPredictorRank($playerId, $date_condition, $extra);

    $response = ["stats" => $data, "player_stat" => $finalPlayerStat];

    return $response;

}

function getOverallPlayerRankPredictor($date_condition = "", $limit = 100, $offset = 0, $extra = [])
{

    $category = !empty($extra["category"]) ? " AND predictions.category_id = " . $extra["category"] : "";

    $week = !empty($extra["week"]) ? " AND predictions.week = " . $extra["week"] : "";

    $active = isActive();

    $query = 'SELECT m.pic,m.name,m.scorer AS points,m.user_id FROM (
                                              SELECT d.user_id,d.scorer,  @rownum := @rownum + 1 AS rank,  d.name, d.pic FROM (
                                                SELECT t.user_id,SUM(t.score) as scorer,users.name, media_links.media_url as pic
                                                FROM predictor_poll_answers t
                                                JOIN users ON users.id = t.user_id
                                                LEFT JOIN media_links ON users.media_id = media_links.id AND media_links.active = ' . $active . '
                                                JOIN predictor_poll_options ON predictor_poll_options.id = t.answer_id
                                                JOIN predictor_polls ON predictor_poll_options.poll_id = predictor_polls.id
                                                JOIN predictions ON predictor_polls.predictor_id = predictions.id AND predictions.category_id != 0 ' . $category . $week . '
                                                WHERE users.active = ' . $active . ' AND t.active = ' . $active . $date_condition . '
                                                GROUP BY t.user_id
                                                ORDER BY scorer DESC, t.created_at ASC) d,
                                              (SELECT @rownum := 0) r) m
                                               WHERE m.scorer > 0 LIMIT ? OFFSET ?';

    $results = DB::select($query, [$limit, $offset]);

    return $results;

}

function datediffInWeeks($date1, $date2, $week = 1)
{

    $week = 7 * $week;

    if ($date1 > $date2) return datediffInWeeks($date2, $date1);
    $first = DateTime::createFromFormat('m/d/Y', $date1);
    $second = DateTime::createFromFormat('m/d/Y', $date2);
    return floor($first->diff($second)->days / $week);
}

function base64toImage($base64Data, $imgName)
{

    $imageDecoded = base64_decode($base64Data);

    $first_path = base_path();

    $path = '/app/player/pic/';

    $target_path = $first_path . '/public' . $path . $imgName;

    file_put_contents($target_path, $imageDecoded);

    $first_path = url('/');

    $target_path = $first_path . $path . $imgName;

    return $target_path;

}


function sendOTP($userID, $email) // change to laravel
{

    $otp = (string)rand(1000, 9999);
//    $otp = '0000';

    $insert = insertData(UserOtp::class, [
        "data" =>
            [
                "user_id" => $userID,
                "otp" => $otp,
                "expiry" => date("Y-m-d H:i:s", strtotime("+30 minutes")),
                "used" => 0
            ]
    ]);

    try {


        Notification::route('mail', $email)->notify(new Otp($otp));


    } catch (\Exception $e) {

        echo 'Exception : ', $e->getMessage(), "\n";

    }

    return $insert;

}

/*function sendOoredooOtp($playerID){

    $otp = (string)rand(1000, 9999);

    $insert = insertData(UserPartnerOtp::class, [
        "data" =>
            ["user_id" => $playerID,
                "otp" => $otp,
                "expiry" => date("Y-m-d H:i:s", strtotime("+30 minutes")),
                "used" => 0,
                "active" => 1,
                "created_at" => currentTime()
            ]
    ]);
}*/


//function sortByLocked($a, $b)
//{
//    $c = $a->locked;
//    $d = $b->locked;
//
//    $e = $a->status;
//    $f = $b->status;
//
//
//    print_r($c);
//    print_r($d);
//    print_r($e);
//    print_r($d);die;
///*
//    if($c < $d){
//        if($e > $f){
//            return 1;
//        }else{
//            return -1;
//        }*/
//
//
//        if($c < $e){
//            if($d >= $f){
//                return 1;
//            }else{
//                return -1;
//            }
//
//    }
//
//}


function multiSort()
{
    //get args of the function
    $args = func_get_args();


    $c = count($args);
    if ($c < 2) {
        return false;
    }

    $array = $args[0];
    //get the array to sort
    array_splice($args, 0, 1);


    $array = $array->toArray();
    usort($array, function ($a, $b) use ($args) {


        $i = 0;
        $c = count($args);
        $cmp = 0;

        while ($cmp == 0 && $i < $c) {
            $cmp = strcmp($a[$args[$i]], $b[$args[$i]]);
            $i++;
        }

        return $cmp;

    });

    return $array;

}


function questionsMigrate($path, $images = [], $level_id = 0)
{


    $handle = fopen($path, "r");

    $i = 0;

    $copyImage = $uploaded = [];

    if (!empty($images)) {

        foreach ($images as $key => $img) {

            list($name, $ext) = explode(".", $img->getClientOriginalName());

            $ext = strtolower($ext);

            if (!in_array($ext, ["jpg", "png", "jpeg", "gif"])) {

                continue;

            }

            $copyImage[$key] = $name;

        }

    }

    $path = '/img/question';

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        if ($i > 0) {


            $sports = trim($data[1]);
            $category = trim($data[2]);
            $topic = trim($data[3]);
            $question_title = trim($data[5]);
            $image = trim($data[6]);

            if (empty($question_title)) {

                continue;

            }

            $level = trim($data[7]);

            $a1 = trim($data[8]);
            $a2 = trim($data[9]);
            $a3 = trim($data[10]);
            $a4 = trim($data[11]);

            $correct_a = trim($data[12]);

            switch ($correct_a) {
                case 'A':
                    $final_answer = 1;
                    break;
                case 'B':
                    $final_answer = 2;
                    break;
                case 'C':
                    $final_answer = 3;
                    break;
                case 'D':
                    $final_answer = 4;
                    break;
                default:
                    break;
            }


            $right_answer = trim($data[13]);

            $after_taste = trim($data[14]);

            $hint = trim($data[15]);

            /*$check = TableRegistry::get('CntSports')
                ->find("all", array('conditions' => array('title LIKE' => "%" . $sports . "%")))
                ->select("sports_id")
                ->first();*/


            $check = getTableData(Sport::class, [
                "select" => ["id"],
                "whereOperand" => [
                    [
                        "column" => "title",
                        "operand" => "LIKE",
                        "value" => "%" . $sports . "%",
                    ]
                ],
                "single" => 1
            ]);

            if (!empty($check->id)) {

                $insert_sports_id = $check->id;

            } else {


                $insert_sports_id = insertData(Sport::class, [
                    "data" => [
                        "title" => $sports,
                        "media_id" => 0,
                        "display_rank" => 0,
                    ],
                    'id' => 1
                ]);

            }


            /* $check = TableRegistry::get('CntCategory')
                 ->find("all", [
                     'conditions' => [
                         'title LIKE' => "%" . $category . "%",
                         'sports_id' => $insert_sports_id
                     ]
                 ])
                 ->select("category_id")
                 ->first();




             if (!empty($check->category_id)) {

                 $insert_category_id = $check->category_id;

             } else {

                 $alias = TableRegistry::get('CntCategory');

                 $ref = $alias->newEntity();

                 $ref->sports_id = $insert_sports_id;

                 $ref->region_id = 1;

                 $ref->title = $category;

                 $ref->after_taste = "";

                 $ref->image_link_id = 0;

                 $ref->display_rank = 0;

                 $ref->is_sponsored = 0;

                 $ref->lang_id = 1;

                 $ref->default_lang_row_id = 0;

                 $ref->admin_id = 1;

                 $ref->published_utc = Time::now();

                 $ref->created_utc = Time::now();

                 $ref->is_active = 1;

                 $cat_id = $alias->save($ref);

                 $insert_category_id = $cat_id->category_id;

             }*/
            /*
                        $check = TableRegistry::get('CntTopics')
                            ->find("all", ['conditions' =>
                                [
                                    'title LIKE' => "%" . $topic . "%",
                                    'category_id' => $insert_category_id
                                ]
                            ])
                            ->select("topic_id")
                            ->first();


                        if (!empty($check->topic_id)) {

                            $insert_topic_id = $check->topic_id;

                        } else {

                            $query = TableRegistry::get('CntTopics');

                            $ref = $query->newEntity();

                            $ref->category_id = $insert_category_id;

                            $ref->title = $topic;

                            $ref->admin_id = 1;

                            $ref->created_utc = Time::now();

                            $ref->is_active = 1;

                            $topic_id = $query->save($ref);

                            $insert_topic_id = $topic_id->topic_id;

                        }*/


            /*  $difficulty_id = TableRegistry::get('cfg_difficulty')
                  ->find("all", array('conditions' => array('title LIKE' => "%" . $level . "%")))
                  ->select("difficulty_id")
                  ->first()->difficulty_id;

              $query = TableRegistry::get("CntQuestions");

              $ref = $query->newEntity();

              if (($level_id != 0)) {

                  $ref->category_id = 0;

                  $ref->category_id_old = $insert_category_id ?? 0;

              } else {

                  $ref->category_id = $insert_category_id ?? 0;
              }*/


            $formatID = 1;

            $mediaID = 0;

            if (in_array($image, $copyImage)) {


                $keyIndex = array_search($image, $copyImage);

                if (!empty($uploaded[$keyIndex])) {

                    $formatID = 4;

                    $mediaID = $uploaded[$keyIndex];


                } else if (!empty($images[$keyIndex])) {

                    $resp = uploadFile($images[$keyIndex], $path);

                    if (!empty($resp["media_id"])) {

                        $formatID = 4;

                        $mediaID = $resp["media_id"];

                        if (empty($uploaded[$keyIndex])) {

                            $uploaded[$keyIndex] = $resp['media_id'];
                        }

                    }

                }

            }


            $question_id = insertData(Questions::class, [
                "data" => [
                    "region_id" => $sports,
                    "difficulty_id" => 0,
                    "question_title" => $question_title,
                    "format_type_id" => $formatID,
                    "media_link_id" => $mediaID,
                    "hint" => $hint,
                    "after_taste" => $after_taste,
                    "trivia_source" => '',
                    "lang_id" => 1,
                    "default_lang_row_id" => 0,
                    "admin_id" => 1,
                    "is_published" => 1,
                ],
                'id' => 1
            ]);


            if (!empty($level_id)) {

                $insert = [

                    "question_id" => $question_id,
                    "quiz_id" => $level_id,

                ];

                insertData(ContestQuizQuestions::class, [
                    "data" => $insert
                ]);

            }


            for ($j = 1; $j <= 4; $j++) {

                if (!empty(${'a' . $j}) || ${'a' . $j} == 0) {

                    $is_correct_answer = ($final_answer == $j) ? 1 : 0;
                    insertData(Answers::class, [
                        'data' => [
                            'question_id' => $question_id,
                            'answer_title' => ${'a' . $j},
                            'media_link_id' => 0,
                            'is_correct_answer' => $is_correct_answer,
                            'lang_id' => 1,
                            'default_lang_row_id' => 0,
                            'admin_id' => 1,
                            'is_published' => 1
                        ]
                    ]);

                }

            }

        }

        $i = 1;

    }
}

function questionsLangMigrate($path, $langID, $level_id = 0)
{


    $handle = fopen($path, "r");

    $i = 0;

    $j = 0;

    $path = '/img/question';

    $allQuestions = getTableData(ContestQuizQuestions::class, [
        "select" => [
            "question_id"
        ],
        "where" => [
            "quiz_id" => $level_id
        ]
    ])->pluck('question_id')->toArray();

    $langQuestion = getTableData(QuestionTranslation::class, [
        "select" => [
            "question_id"
        ],
        "whereIn" => ["question_id" => $allQuestions],
        "where" => [
            "lang_id" => $langID
        ]
    ])->pluck('question_id')->toArray();

    $finalList = array_values(array_diff($allQuestions, $langQuestion));


    $answers = getTableData(Answers::class, [
        "select" => [
            "id",
            "question_id",
            "answer_title"
        ],
        "whereIn" => [
            "question_id" => $finalList
        ]
    ]);

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        if ($i > 0) {


            $sports = trim($data[1]);
            $category = trim($data[2]);
            $topic = trim($data[3]);
            $question_title = trim($data[5]);
            $image = trim($data[6]);

            if (empty($question_title)) {

                continue;

            }

            $level = trim($data[7]);

            $a1 = trim($data[8]);
            $a2 = trim($data[9]);
            $a3 = trim($data[10]);
            $a4 = trim($data[11]);

            $correct_a = trim($data[12]);

            switch ($correct_a) {
                case 'A':
                    $final_answer = 1;
                    break;
                case 'B':
                    $final_answer = 2;
                    break;
                case 'C':
                    $final_answer = 3;
                    break;
                case 'D':
                    $final_answer = 4;
                    break;
                default:
                    break;
            }


            $right_answer = trim($data[13]);

            $after_taste = trim($data[14]);

            $hint = trim($data[15]);

            $formatID = 1;

            $mediaID = 0;

            $question_id = insertData(QuestionTranslation::class, [
                "data" => [
                    "question_id" => $finalList[$j],
                    "lang_id" => $langID,
                    "title" => $question_title,
                    "hint" => $hint,
                ],
                'id' => 1
            ]);

            $orgAnswer = $answers->where("question_id", $finalList[$j])->pluck('id')->toArray();
            $orgAnswerT = $answers->where("question_id", $finalList[$j])->pluck('answer_title')->toArray();

            $m = 0;

            for ($k = 1; $k <= 4; $k++) {

                $answerText = ${'a' . $k};

                //TODO array_diff to reduce loop

                if (!empty($answerText) || $answerText == 0) {

                    if (!in_array($answerText, $orgAnswerT)) {
                        insertData(AnswerTranslation::class, [
                            'data' => [
                                'answer_id' => $orgAnswer[$m],
                                'title' => $answerText,
                                'lang_id' => $langID,
                            ]
                        ]);

                    }
                    $m++;

                }


            }

            $j++;

        }

        $i = 1;

    }
}

function numberUsed($mobile): bool
{

    $status = false;

    $check = getTableData(UserPartnerOtp::class, [
        "select" => ['id'],
        "where" => [
            "number" => $mobile,
            "used" => isActive()
        ],
        "single" => isActive()
    ]);

    if (!empty($check->id)) {

        $status = true;

    }

    return $status;

}

function receivedInvite($playedID, $referrerID, $point = 100)
{

    $existing = getTableData(UserRewards::class, [
        "select" => [
            "id"
        ],
        "where" => [
            "user_id" => $referrerID,
            "link_id" => $playedID,
            "type" => 1
        ],
        "single" => 1]);

    if (!empty($existing->id)) {

        return;

    }

    $data = [];

    $data['user_id'] = $referrerID;
    $data['link_id'] = $playedID;
    $data['type'] = 1;
    $data['points'] = $point;

    rewardUser($data);

    $name = getUserProfileSpecific($playedID, ["name"]);

    $friendName = $name->name ?? "";

    $extra = [
        "title" => "Your friend " . $friendName . " has joined SportsQwizz Bizconnect!",
        "body" => "Congratulations! You have got $point points.",
        "image" => "",
        "cta" => "",
        "extra" => []
    ];

    $receiverId = [];

    $referredByDetails = getTableData(
        User::class,
        ["select" => [
            "id",
            "device_type_id",
            "push_token",
        ],
            "where" => [
                "id" => $referrerID
            ], "single" => 1]);


    if ($referredByDetails->device_type_id == returnConfig("android")) {

        $receiverId["android"] = [$referredByDetails->push_token];

        $deviceTypeID = 1;

    } else {

        $receiverId["ios"] = [$referredByDetails->push_token];

        $deviceTypeID = 2;

    }

    if (!empty($receiverId)) {

        firebaseNotification($extra, $receiverId, ["deviceTypeId" => $deviceTypeID, "senderId" => $referrerID], [$referrerID]);

    }

}

function rewardUser($data = [])
{

    if (!empty($data)) {

        insertData(UserRewards::class, [
            "data" => [
                "user_id" => $data['user_id'],
                "link_id" => $data['link_id'] ?? 0,
                "type" => $data['type'],
                "points" => $data['points']
            ]]);

    }


}


function send_to_slack_channel($msg, $channel)
{

    if (isProd()) {

        $cmd = 'curl -X POST --data-urlencode "payload={\"channel\": \"#' . $channel . '\", \"username\": \"Reporter\", \"text\": \"' . $msg . '\"}" https://hooks.slack.com/services/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';

        return shell_exec($cmd);

    }

}

function userFirebaseUID()
{

    $found = 1;

    while ($found != 0) {


        $token = getToken(28);

        $extra = [
            "select" => ["app_id"],
            "where" => [
                "uid" => $token
            ],
            "na" => 1,
            "single" => 1
        ];

        $check = getTableData(User::class, $extra);

        if (empty($check->app_id)) {

            $found = 0;

        }

    }

    return $token;

}

function userReward($userID, $type, $points = 100, $link_id = 0, $master_id = 0)
{
    if($type == 1)
    {
        updateData(User::class, [
            "update" => [
                "balance" => DB::raw("balance+$points"),
            ],
            "where" => [
                "id" => $userID,
            ],
            "na" => 1
        ]);
    }
    elseif ($type == 2)
    {
        updateData(User::class, [
            "update" => [
                "balance" => DB::raw("balance-$points"),
            ],
            "where" => [
                "id" => $userID,
            ],
            "na" => 1
        ]);
    }

    return insertData(UserRewards::class, [
        'data' => [
            'user_id'   => $userID,
            'type'      => $type,
            'points'    => $points,
            'link_id'   => $link_id,
            'master_id' => $master_id,
        ],
        "id" => isActive()
    ]);
}


function isPaymentExists($data)
{
    $is_pymt_exists = getTableData(Payment::class, [

        "select" => [
            "id"
        ],
        "where" => [
            "txn_id" => $data['referenceId'],
            "order_id" => $data['orderId'],
        ],
        "single" => 1
    ]);

    if(isset($is_pymt_exists->id) && $is_pymt_exists->id != "")
    {
        return true;
    }
    else
    {
        return false;
    }
}


function updateRecipientStatus($data)
{

    $upd_data = [
        "active" => $data['status'],
    ];

    $where = [
        "user_id" => Auth::id(),
        "recipient_id" => $data['recipient_id'],
    ];

    return updateData(Recipient::class, [
        "update" => $upd_data,
        "where" => $where
    ]);
}

function contestParticipant($userID, $contestID)
{

    return insertData(ContestParticipants::class, [
        "data" => [
            "user_id" => $userID,
            "contest_id" => $contestID,
        ],
        "id" => isActive()
    ]);

}


function getUserCoins()
{

    return auth()->user()->coins;

}

function getUserBalance()
{
    $bal = getTableData(User::class, [

        "select" => [
            'balance'
        ],
        "where" => [
            'id' => auth()->user()->id,
        ],
        "single" => 1
    ]);

    return $bal->balance;

}

function mask($str)
{


    $len = strlen($str);

    return substr($str, 0, 2) . str_repeat('*', $len - 5) . substr($str, $len - 3, 3);


}

/*function checkContestParticipants($request, $contest_id)
{
    $user_id = auth()->user()->id;

    $is_contest_started = \DB::table('contest_participants')->where('contest_id', $contest_id)->where('user_id', $user_id)->count();

    if ($is_contest_started > 0)
    {
        if ($quizzes = \Cache::get('quiz')) {
            $ques_count = $quizzes->where('id', $quiz_id)->first()->count;
        } else {
            $ques_count = \DB::table('contest_quiz')->where('id', $quiz_id)->first()->count;
        }

        $ansed_ques = \DB::table('contest_answers')->where('quiz_id', $quiz_id)->where('user_id', $user_id)->count();

        $is_ques_left = $ques_count - $ansed_ques;

        if ($is_ques_left > 0) {
            // started but not finished
            $request->request->add(['quiz_status' => '1']);

            return '1';
        } else {
            // finished
            $request->request->add(['quiz_status' => '2']);
            return '2';
        }
    } else {
        // not yet started
        $request->request->add(['quiz_status' => '0']);
        return '0';
    }
//    return $message_arr[$num];
}*/


function answeredQuestionCheck_old($request, $quiz_id)
{
    $user_id = auth()->user()->id;
    $is_started = \DB::table('contest_participants')->where('contest_id', $quiz_id)->where('user_id', $user_id)->count();

    if ($is_started > 0) {
        if ($quizzes = \Cache::get('quiz')) {
            $ques_count = $quizzes->where('id', $quiz_id)->first()->count;
        } else {
            $ques_count = \DB::table('contest_quiz')->where('id', $quiz_id)->first()->count;
        }

        $ansed_ques = \DB::table('contest_answers')->where('quiz_id', $quiz_id)->where('user_id', $user_id)->count();

        $is_ques_left = $ques_count - $ansed_ques;

        if ($is_ques_left > 0) {
            // started but not finished
            $request->request->add(['quiz_status' => '1']);

            return '1';
        } else {
            // finished
            $request->request->add(['quiz_status' => '2']);
            return '2';
        }
    } else {
        // not yet started
        $request->request->add(['quiz_status' => '0']);
        return '0';
    }
//    return $message_arr[$num];

}


function createContestCache($forget = 0)
{

    if (!empty($forget)) {

        Cache::forget("contests");

    }

    $contests = Cache::remember("contests", 86000, function () {

        $joins = [


            [
                "table" => "contest_quiz",

                'type' => returnConfig("inner_join"),

                "left_condition" => "contests.id",

                "right_condition" => "contest_quiz.contest_id",

            ],

            [
                "table" => "media_links",

                'type' => returnConfig("left_join"),

                "left_condition" => "contests.media_id",

                "right_condition" => "media_links.id",

            ],

            [
                "table" => "sports",

                'type' => returnConfig("left_join"),

                "left_condition" => "contests.sports_id",

                "right_condition" => "sports.id",

            ],

            [
                "table" => "contest_participants",

                'type' => returnConfig("left_join"),

                "left_condition" => "contests.id",

                "right_condition" => "contest_participants.contest_id",

            ],
        ];

        return getTableData(Contest::class, [  // ContestQuiz::class
            "select" => [
//                "contest_quiz.id as id",
//                "contest_quiz.name as quiz_name",
//                            "count",
//                "contest_quiz.start_utc as start_utc",
//                "contest_quiz.end_utc as end_utc",
//                "sports.title as sport",
//                            "sports.id as sports_id",
//                "media_links.media_url as pic",
//                "contest_quiz.rules",
//                'contest_quiz.count',
//                'contest_quiz.price',
//                        'contest_participants.status',
//                'contest_quiz.max_player_allowed',

                "contests.id as id",
                "contests.name as contest_name",
//                "count",
                "contests.start_utc as start_utc",
                "contests.end_utc as end_utc",
                "contests.wait_utc as wait_utc",
                "sports.title as sport",
//                "sports.id as sports_id",
                "media_links.media_url as pic",
//                "contests.rules",
                'contests.total',
                'contests.price',
                'contests.ticket',
                'contests.is_featured',
                'contests.description',
                'contests.rules',
                'contests.total',
                DB::raw("COUNT(DISTINCT contest_participants.user_id) as total_participants"),
                DB::raw("CASE WHEN contests.wait_utc <= CURRENT_TIMESTAMP THEN 1 ELSE 2 END as timer_format") // timer_format : 1=Ends At; 2=Starts At
                /*CASE
                    WHEN COUNT(contest_quiz.count) - COUNT(DISTINCT contest_answers.id) <= 0
                    THEN '1'
                    ELSE '0'
                    END as locked"),*/
            ],
            "whereOperand" => [
                // For live and upcoming Contest Only
                /*[
                    "column" => "contests.start_utc",
                    "operand" => "<=",
                    "value" => currentTime(),
                ],*/
                [
                    "column" => "contests.end_utc",
                    "operand" => ">=",
                    "value" => currentTime(),
                ],
            ],
            "group" => [
                'contests.id'
            ],
            'joins' => $joins,
        ]);
    });


    return $contests;
}

function createContestQuizCache($contest_id, $forget = 0)
{

    if (!empty($forget)) {

        Cache::forget("quizzes.$contest_id");

    }

    $quizzess = Cache::remember("quizzes.$contest_id", 86000, function () use ($contest_id) {

        $joins = [

            [
                "table" => "contest_quiz",

                'type' => returnConfig("inner_join"),

                "left_condition" => "contests.id",

                "right_condition" => "contest_quiz.contest_id",

            ],

            [
                "table" => "sports",

                'type' => returnConfig("left_join"),

                "left_condition" => "contest_quiz.sports_id",

                "right_condition" => "sports.id",

            ],

            [
                "table" => "media_links",

                'type' => returnConfig("left_join"),

                "left_condition" => "contest_quiz.media_id",

                "right_condition" => "media_links.id",

            ],

            /*[
                "table" => "contest_participants",

                'type' => returnConfig("left_join"),

                "left_condition" => "contest_quiz.id",

                "right_condition" => "contest_participants.quiz_id",

            ],*/
        ];

        $contest = getTableData(Contest::class, [  // ContestQuiz::class
            "select" => [

                "contests.id as contest_id",
                "contests.name as contest_name",
//                "count",
//                "contests.start_utc as start_utc",
//                "contests.end_utc as end_utc",
//                "contests.wait_utc as wait_utc",
//                "sc.title as sport",
//                "sports.id as sports_id",
//                "mlc.media_url as cpic",
//                "contests.rules",
//                'contests.count',
//                'contests.price',
//                'contests.ticket',
//                'contests.description',
//                'contest_participants.status',
//                'contests.total',
//                "contests.",
                DB::raw("CONCAT('[', GROUP_CONCAT(JSON_OBJECT('quiz_id', contest_quiz.id, 'quiz_name', contest_quiz.name, 'start_utc', contest_quiz.start_utc, 'end_utc', contest_quiz.end_utc, 'sport', sports.title, 'pic', media_links.media_url, 'count', contest_quiz.count, 'price', contest_quiz.price)), ']') as quizzes"),
                /*DB::raw("COUNT(DISTINCT contest_participants.user_id) as total_played,
                CASE
                    WHEN COUNT(contest_quiz.count) - COUNT(DISTINCT contest_answers.id) <= 0
                    THEN '1'
                    ELSE '0'
                    END as locked"),*/

            ],
            "whereOperand" => [
                [
                    "column" => "contests.start_utc",
                    "operand" => "<=",
                    "value" => currentTime(),
                ],
                [
                    "column" => "contests.end_utc",
                    "operand" => ">=",
                    "value" => currentTime(),
                ],
                /*[
                    "column" => "contest_quiz.start_utc",
                    "operand" => "<=",
                    "value" => currentTime(),
                ],
                [
                    "column" => "contest_quiz.end_utc",
                    "operand" => ">=",
                    "value" => currentTime(),
                ],*/
                [
                    "column" => "contest_quiz.contest_id",
                    "operand" => "=",
                    "value" => $contest_id,
                ],
            ],
            "group" => [
                'contests.id'
            ],
            "single" => 1,
            'joins' => $joins,
        ]);

//        dd($contest);

        if(isset($contest) && !is_null($contest))
            $contest->quizzes = (!isset($contest->quizzes) && is_null($contest->quizzes) && empty($contest->quizzes)) ? "" : collect(json_decode($contest->quizzes));

        return $contest;
    });


    return $quizzess;
}

function createQuizzesHTML($quizzes)
{
    $quiz_html = '';

//    dd($quizzes);

//    dd(auth()->user()->question_count($value->quiz_id));

    foreach ($quizzes->quizzes as $key => $value) // $value->count <= $usr_qcnt
    {
        $usr_qcnt = auth()->user()->question_count($value->quiz_id, $quizzes);

//        dd(auth()->user()->question_count($value->quiz_id));

        $quiz_html .= '<div class="col-lg-4 col-md-6">
            <div class="single-bonus">
                <a href="'. (($value->count <= $usr_qcnt) ? "javascript:void(0);" : route('quiz.question', ["contest_id" => $quizzes->contest_id, "quiz_id" =>
                $value->quiz_id])) .'">
                <div class="content">
                    <img src="'. $value->pic .'" alt="">
                    <h4 class="title">
                        '.$value->quiz_name.'
                    </h4>
                    <small class="text-uppercase">'. $value->sport .'</small>
                </div>
                </a>
                <a href="'. (($value->count <= $usr_qcnt) ? "javascript:void(0);" : route('quiz.question', ["contest_id" => $quizzes->contest_id, "quiz_id" =>
                $value->quiz_id])) .'" class="mybtn2">'. (($usr_qcnt == 0) ? "<i class='fa fa-play-circle'></i> Play Now" : ($value->count > $usr_qcnt ? "<i class='fa fa-pause-circle'></i> Continue Play" : "<i class='fa fa-check-circle'></i> Completed")) . '</a>
            </div>
        </div>';

        /*$quiz_html .= '<div class="quiz-sec col-sm-6"> <a href="'. (($value->count <= $usr_qcnt) ? "javascript:void(0);" : route('front.quiz.question', ["contest_id" => $quizzes->contest_id, "quiz_id" => $value->quiz_id])) .'"> '. (($usr_qcnt == 0) ? "<i class='fa fa-4x fa-play-circle text-white'></i>" : ($value->count > $usr_qcnt ? "<i class='fa fa-4x fa-pause-circle text-white'></i>" : "<i class='fa fa-4x fa-check-circle text-white'></i>")) . '<img class="img-fluid" src="'.
            $value->pic.'"/><div class="info"><div class="infoinner"><small class="text-uppercase">'. $value->sport .'</small><h5>'.$value->quiz_name.'</h5></div></div></a> </div>';*/

    }

    return $quiz_html;
}

function createQuizCache($forget = 0)
{

    if (!empty($forget)) {

        Cache::forget("quiz");

    }

    $contest = Cache::remember("quiz", 86000, function () {

        $joins = [

            [
                "table" => "sports",

                'type' => returnConfig("left_join"),

                "left_condition" => "contest_quiz.sports_id",

                "right_condition" => "sports.id",

            ],

            [
                "table" => "media_links",

                'type' => returnConfig("left_join"),

                "left_condition" => "contest_quiz.media_id",

                "right_condition" => "media_links.id",

            ],

            /*[
                "table" => "contest_participants",

                'type' => returnConfig("left_join"),

                "left_condition" => "contest_quiz.id",

                "right_condition" => "contest_participants.quiz_id",

            ],*/
        ];

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
            'joins' => $joins,
        ]);
    });


    return $contest;
}

function createQuizQuestionCache($quiz_id, $forget = 0)
{

    if (!empty($forget)) {

        Cache::forget("questions.$quiz_id");

    }

    return Cache::remember("questions.$quiz_id", 86000, function () use ($quiz_id) {

        $joins = [

            [
                "table" => "questions",

                'type' => returnConfig("inner_join"),

                "left_condition" => "contest_quiz_questions.question_id",

                "right_condition" => "questions.id",

            ],

            [
                "table" => "media_links",

                'type' => returnConfig("left_join"),

                "left_condition" => "questions.media_link_id",

                "right_condition" => "media_links.id",
            ],

            [
                "table" => "answers",

                'type' => returnConfig("left_join"),

                "left_condition" => "questions.id",

                "right_condition" => "answers.question_id",
            ],

        ];

        $questions = getTableData(ContestQuizQuestions::class, [
            "select" => [
                "questions.id as question_id",
                DB::raw("CONCAT('[', GROUP_CONCAT(JSON_OBJECT('is_correct_answer', answers.is_correct_answer, 'answer_id', answers.id, 'answer_title', answers.answer_title)), ']') as options"),
                "questions.question_title",
                "questions.hint",
                "questions.after_taste",
                "questions.format_type_id",
                "media_links.media_url as question_pic",
            ],
            "where" => [
                "contest_quiz_questions.quiz_id" => $quiz_id,
                "contest_quiz_questions.active" => 1
            ],
            "joins" => $joins,
            "group" => [
                "questions.id"
            ],
        ]);

        $questions->map(function ($value) {

            $value->options = collect(json_decode($value->options));

            return $value;
        });

        return $questions;

    });
}

function createContestParticipantCache($contest_id = "", $user_id = "", $forget = 0) // $forget [1=forget, 2=update userid in contest Cache, 3=create the contest and update it]
{ //  $user_id,

    if (!empty($forget) && $forget == 1) {
        Cache::forget("participants");
    }

    if($contest_id != "" && $user_id != "")
    {
        $cont_partci = createContestParticipantCache();

        if($forget == 2)
        {

            $list_participated = $cont_partci->where('contest_id', $contest_id)->first();

            $usr_ids = $list_participated['user_ids'];

            $usr_ids->push($user_id);

            $list_participated['user_ids'] = $usr_ids;

            $cp_key = $cont_partci->search(function ($item, $key) use ($contest_id) {

                return $item['contest_id'] == $contest_id;
            });

            $cont_partci[$cp_key] = $list_participated;
        }
        elseif($forget == 3)
        {
            $participants = collect([
                "contest_id" => $contest_id,
                "user_ids" => collect(array($user_id)),
            ]);

            $cont_partci->push($participants);
        }

        Cache::put('participants', $cont_partci);

        return $cont_partci;
    }
    else {

        return Cache::remember("participants", 86000, function () {

            $participants = getTableData(ContestParticipants::class, [
                "select" => [
                    "contest_id",
                    DB::raw("GROUP_CONCAT(user_id) as user_ids"),
                ],
                "group" => [
                    'contest_id'
                ],
                /*"whereOperand" => [
                    [
                        "column" => "contest_id",
                        "operand" => "=",
                        "value" => $contest_id,
                    ]
                ]*/
            ]);

            $participants->map(function ($value) {

//            $value->user_ids  = collect(explode(',', $value->user_ids));
                $value->user_ids = collect(array_map('trim', explode(',', $value->user_ids)));

                return $value;
            });

            return $participants;
        });
    }
}

function getOrCreateUserdataCache($userID, $quizzes, $quiz_id = "", $forget = 0) // $forget [1=forget, 2=update answer, score,  in contest Cache, 3=create the contest and update
// it]
{

    if (!empty($forget) && $forget == 1) {

        Cache::forget("userdata.$userID");

    }

//    $quizzes = createQuizCache();

    if($quiz_id != "")
    {
        $userdata = getOrCreateUserdataCache($userID, $quizzes); // Cache::get("userdata.$userID");

        /*if($forget == 2)
        {

            $usr_data = $userdata->where('quiz_id', $quiz_id)->first();

            $list_participated->user_ids->push($user_id);

            $cp_key = $cont_partci->search(function ($item, $key) use ($contest_id) {

                return $item['contest_id'] == $contest_id;
            });

            $cont_partci[$cp_key] = $list_participated;
        }
        elseif($forget == 3)
        {
            $participants = collect([
                "contest_id" => $contest_id,
                "user_ids" => collect(array($user_id)),
            ]);

            $cont_partci->push($participants);
        }*/

        Cache::put("userdata.$userID", $userdata);

//        return $cont_partci;

//        $cur_quiz_count = $quizzes->quizzes->where('id', $quiz_id)->first()->count;

        /*$cr_q_data = getTableData(ContestAnswers::class, [
            "select" => [
                "contest_answers.quiz_id",
                DB::raw("GROUP_CONCAT(question_id) as answered_question_id"),
                DB::raw("GROUP_CONCAT(updated_at) as updated_at_date"),
                DB::raw("SUM(score) as score"),
                DB::raw("SUM(time_secs) as time"),
            ],
            "where" => [
                "contest_answers.user_id" => $userID,
                "contest_answers.quiz_id" => $quiz_id,
            ],
            "group" => [
                "contest_answers.quiz_id"
            ],
            "single" => 1,
        ]);*/

//        $cr_q_data->answered_question_id = array_map('trim', explode(',', $cr_q_data->answered_question_id));
//        $cr_q_data->answered_question_id = array_combine(array_map('trim', explode(', ', $cr_q_data->answered_question_id)), explode(', ', $cr_q_data->updated_at_date));
//        $cr_q_data->question_count = $cur_quiz_count;
//
//        unset($cr_q_data->updated_at_date);
//
///        // ------------------------------------ //
//
//        $userdata[$userdata->count()] = $cr_q_data;
//
//        $userdata = collect($userdata);

        return $userdata;
    }
    else {

//        dd($quizzes);

        $userdata_cache = Cache::remember("userdata.$userID", 86000, function () use ($userID, $quizzes) { // , $quiz_id

            $userdatas = getTableData(ContestAnswers::class, [
                "select" => [
                    "contest_answers.quiz_id",
                    DB::raw("GROUP_CONCAT(question_id) as answered_question_id"),
                    DB::raw("GROUP_CONCAT(CASE WHEN updated_at IS NULL OR updated_at = '' THEN 1 ELSE updated_at END) as updated_at_date"),
                    DB::raw("SUM(score) as score"),
                    DB::raw("SUM(time_secs) as time"),
                ],
                "where" => [
                    "contest_answers.user_id" => $userID,
                ],
                "group" => [
                    "contest_answers.quiz_id"
                ],
            ]);

//            dd($userdatas);

            $userdatas->map(function ($value) use ($quizzes) {

                $value->answered_question_id  = array_combine(array_map('trim', explode(',', $value->answered_question_id)), explode(',', $value->updated_at_date));
//                $value->answered_question_id = array_map('trim', explode(',', $value->answered_question_id));
                $value->question_count = $quizzes->quizzes->where('quiz_id', $value->quiz_id)->first()->count;

                unset($value->updated_at_date);

                return $value;
            });

            return $userdatas;
        });
    }

    return $userdata_cache;
}

function updateContestStatus($data)
{
    // dd($data);
    $upd_data = [
        "active" => (($data['is_active'] == "1") ? "0" : ""),
    ];

    $where = [
        "id" => $data['contest_id'],
    ];

    // dd($where);

    return updateData(Contest::class, [
        "update" => $upd_data,
        "where" => $where,
        "na" => 1
    ]);
}


function currencySign()
{
    $cur_id = 2; // for INR

    $currency_sign = getTableData(Currency::class, [

        "select" => [
            "id",
            "sign"
        ],
        "where" => [
            "id" => $cur_id
        ],
        "single" => 1
    ]);

    return $currency_sign->sign;

}

function getMetaSettingValue($meta_id)
{

    $meta_setting = getTableData(MetaSetting::class, [

        "select" => [

            'value'
        ],
        'where' =>
            [
                'id' => $meta_id
            ],
        "single" => 1
    ]);

    return $meta_setting->value;

}

function referral($user)
{

    $metaValue = getMetaSettingValue(1);

    userReward($user->referred_by, 1 , $metaValue, $user->id , 5);

}

function getMasterRewards()
{
    return Cache::remember("master-rewards", 86000, function () {

        $master_rewards = getTableData(MasterReward::class, [
            "select" => [
                "*",
            ]
        ]);

        return $master_rewards;
    });
}


function getUserRewards($user_id, $master_id = "", $indi_sum = "")
{
//    return Cache::remember("user-reward.$user_id", 60, function () use ($user_id, $master_id, $indi_sum) {

        if($master_id != '')
        {
            $where = [
                "user_rewards.user_id" => $user_id,
                "user_rewards.master_id" => $master_id,
            ];
        }
        else {
            $where = [
                "user_rewards.user_id" => $user_id,
            ];
        }

        if($indi_sum != "")
        {
            $group = [
                "user_rewards.master_id"
            ];

            $select = [
                "user_rewards.master_id",
                DB::raw("SUM(user_rewards.points) as amount"),
            ];
        }
        else {
            $group = "";

            $select = [
                "master_rewards.name as mr_name",
                "user_rewards.points",
                "user_rewards.type",
                "user_rewards.created_at",
                "user_rewards.id",
            ];
        }

        $user_rewards = getTableData(UserRewards::class, [

            "select" => $select,
            "joins" => [
                [
                    "table" => "master_rewards",

                    'type' => returnConfig("inner_join"),

                    "left_condition" => "user_rewards.master_id",

                    "right_condition" => "master_rewards.id",
                ],
            ],
            "where" => $where,
            "group" => $group,
            "order" => [
                "user_rewards.created_at" => "DESC",
            ]
        ]);

        return $user_rewards;
//    });
}

function contestPrizeDistributionsDetail($contest_id, $forget = 0)
{

    if (!empty($forget) && $forget == 1) {
        Cache::forget("prize_distribution.$contest_id");
    }

    if($contest_id != "")
    {
        return Cache::remember("prize_distribution.$contest_id", 86000, function () use ($contest_id) {

            $prize_distri = getTableData(ContestPrizeDistribution::class, [

                "select" => [

                    "*",
                ],
                "whereOperand" => [
                    [
                        "column" => "contest_id",
                        "operand" => "=",
                        "value" => $contest_id,
                    ]
                ]
            ]);

            return collect($prize_distri);
        });
    }
    else
    {
        return false;
    }
}

function viewCount($postID)
{
    
    updateData(PostStatus::class, [
                "update" => [
                    "views" => 'views' + 1
                ],
                "where" => [
                    "id" => $postID,
                ],

            ]);

}
