<?php

/*
 * @author: Kinshuk Lahiri | kinshuk@sportswizzleague.com
 * @date : 06/12/18
 * @description: Query Helper to handle the DB queries through the system
 */

function getTableData($table, $extra)
{

    $where = $extra["where"] ?? [];

    $orgTable = with(new $table)->getTable();

    if(empty($extra["na"]))
    {

        $where[$orgTable.".active"] = returnConfig("active");
    }

    $select = $extra["select"] ?? [];

    $single = $extra["single"] ?? 0;

    $count = $extra["count"] ?? 0;

    $sum = $extra["sum"] ?? "";

    $query = $table::where($where);

    $joins = $extra["joins"] ?? [];

    $limit = $extra["limit"] ?? 0;

    $random = $extra["random"] ?? 0;

    $orderBy = $extra["order"] ?? [];

    $groupBy = $extra["group"] ?? [];

    $having = $extra["having"] ?? "";

    $whereNotIn  = $extra["whereNotIn"] ?? [];

    $whereIn  = $extra["whereIn"] ?? [];

    $whereNull  = $extra["whereNull"] ?? [];

    $whereRaw  = $extra["whereRaw"] ?? "";

    $whereOperand = $extra["whereOperand"] ?? [];

    $paginate = $extra["paginate"] ?? 0;

    if(!empty($joins))
    {

        foreach ($joins as $join)
        {

            $type = strtoupper($join['type']);

            $joiningTable = $join["alias"] ?? $join["table"];

            switch ($type){

                case "INNER":


                    $query->join($joiningTable, function ($internal) use ($join){

                        $internal->on($join["left_condition"],"=",$join["right_condition"]);
                        $internal->on($join['table'].".active", DB::raw(returnConfig("active")));

                        if(!empty($join["conditions"]))
                        {

                            foreach ($join["conditions"] as $key => $value)
                            {

                                if(!empty($value["operand"]))
                                {

                                    $val = $value["value"];

                                    $internal->on($key, $value["operand"], DB::raw("'.$val.'"));
                                }
                                else
                                {

                                    $internal->on($key, DB::raw($value));
                                }
                            }
                        }


                    });

                    break;

                case "LEFT":

                    $query->leftJoin($joiningTable, function ($internal) use ($join){

                        $internal->on($join["left_condition"],"=",$join["right_condition"]);
                        $internal->on($join['table'].".active", DB::raw(returnConfig("active")));

                        if(!empty($join["conditions"]))
                        {

                            foreach ($join["conditions"] as $key => $value)
                            {

                                if(!empty($value["operand"]))
                                {

                                    $val = $value["value"];

                                    $internal->on($key, $value["operand"], DB::raw("'.$val.'"));
                                }
                                else
                                {

                                    $internal->on($key, DB::raw($value));
                                }


                            }

                        }
                    });

                    break;

                case "RIGHT":

                    $query->rightJoin($joiningTable, function ($internal) use ($join){

                        $internal->on($join["left_condition"],"=",$join["right_condition"]);
                        $internal->on($join['table'].".active", DB::raw(returnConfig("active")));

                        if(!empty($join["conditions"]))
                        {

                            foreach ($join["conditions"] as $key => $value)
                            {

                                if(!empty($value["operand"]))
                                {

                                    $val = $value["value"];

                                    $internal->on($key, $value["operand"], DB::raw("'.$val.'"));
                                }
                                else
                                {

                                    $internal->on($key, DB::raw($value));
                                }


                            }

                        }

                    });

                    break;

                default:

                    $query->join($joiningTable, function ($internal) use ($join){

                        $internal->on($join["left_condition"],"=",$join["right_condition"]);
                        $internal->on($join['active'].".active", DB::raw(returnConfig("active")));

                        if(!empty($join["conditions"]))
                        {

                            foreach ($join["conditions"] as $key => $value)
                            {

                                if(!empty($value["operand"]))
                                {

                                    $val = $value["value"];

                                    $internal->on($key, $value["operand"], DB::raw("'.$val.'"));
                                }
                                else
                                {

                                    $internal->on($key, DB::raw($value));
                                }


                            }

                        }

                    });

                    break;
            }


        }


    }

    if(!empty($whereNotIn))
    {

        foreach ($whereNotIn as $key => $not)
        {

            $query->whereNotIn($key, $not);

        }

    }

    if(!empty($whereIn))
    {

        foreach ($whereIn as $key => $in)
        {

            $query->whereIn($key, $in);

        }

    }

    if(!empty($whereOperand))
    {

        foreach ($whereOperand as $where)
        {

            $query->where($where["column"], $where["operand"], $where["value"]);

        }


    }

    if(!empty($whereNull))
    {

        foreach ($whereNull as $null)
        {

            $query->whereNull($null);

        }

    }

    if(!empty($customWhere))
    {

        $query->where($customWhere);

    }

    if(!empty($whereRaw))
    {

        $query->whereRaw($whereRaw);

    }

    if(!empty($limit))
    {

        $query->limit($limit);

    }

    if(!empty($orderBy)) {

        foreach ($orderBy as $column => $order)
        {

            $query->orderBy($column, $order);

        }

    }

    if(!empty($random))
    {

        $query->inRandomOrder();

    }

    if(!empty($groupBy)) {

        foreach ($groupBy as $group)
        {

            $query->groupBy($group);

        }

    }

    if(!empty($having))
    {

        $query->havingRaw($having);

    }

    if(!empty($single))
    {

        $response = $query->first($select);

    }
    elseif(!empty($count))
    {

        $response = $query->count();

    }
    elseif(!empty($sum))
    {

        $response = $query->sum($sum);

    }
    elseif(!empty($paginate))
    {

        $response = $query->select($select)->paginate($paginate);

    }
    else
    {

        $response = $query->get($select);

    }

    return $response;

}

function insertData($table, $extra)
{


    if(empty($extra["na"]))
    {

        if((empty($extra["data"][0])))
        {

            $extra["data"]['active'] = isActive();
            $extra["data"]['created_at'] = currentTime();

        }

        if((!empty($extra["data"][0]) && !is_array($extra["data"][0])))
        {

            $extra["data"] = [$extra["data"]];

            array_walk($extra["data"], $f = function (&$value, $key){

                $value['active'] = isActive();
                $value['created_at'] = currentTime();

            });

        }
    }

    if(!empty($extra["id"]))
    {

        $status = $table::insertGetId($extra["data"]);

    }
    else
    {

        $status =  $table::insert($extra["data"]);

    }

    unset($value);

    return $status;

}

function updateData($table, $extra)
{

    $where = $extra["where"] ?? [];

    $whereIn = $extra["whereIn"] ?? [];

    $update = $extra["update"]  ?? [];

    $update["updated_at"] = currentTime();


    if(empty($extra["na"]))
    {

        $where["active"] = returnConfig("active");
    }

    $query = $table::where($where);

    if(!empty($whereIn))
    {

        foreach ($whereIn as $key => $val)
        {

            $query->whereIn($key, $val);

        }


    }

    $status = $query->update($update);

    return $status;

}
