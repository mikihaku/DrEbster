<?php

require "../library/Database.php";
$db = new Database();

$calCSVUrl = "https://cloud.timeedit.net/ebs/web/public1/ri1Y7X7QQ7wZ16Qvn50b18Y5yQYZ6ZyQX.csv";

$file = fopen($calCSVUrl, "r");

$query = "TRUNCATE TABLE `schedule`;";
$db->query($query);

while (($row = fgetcsv($file, 10000, ",")) !== FALSE) {

    if(strpos($row[0], "/") === FALSE) continue;

    $res['start']  = strtotime(str_replace("/",".",$row[0]." ".$row[1]));
    $res['finish'] = strtotime(str_replace("/",".",$row[2]." ".$row[3]));

    if(strpos($row[4], ",") !== FALSE) {

        $name = explode(", ", $row[4]);

        $name[0] = substr($name[0], 0, strrpos($row[4], " "));
        $name[1] = substr($name[0], 0, strrpos($row[4], " "));

        $res['name'] = $name[0]." OR ".$name[1];

    } else {

        $res['name'] = substr($row[4], 0, strrpos($row[4], " "));

    }

    $res['teacher'] = $row[7] == "" ? "Unknown" : $row[7];

    $room = explode("-", $row[8]);
    $res['room_number'] = $room[0];
    $res['room_name'] = $room[1];

    $res['elective'] = strpos($row[5], ",") !== FALSE ? 1 : 0;

    print_r($res);

    $query = "INSERT INTO `schedule` VALUES (NULL, ".$res['start'].", ".$res['finish'].", 
                                                  '".$res['name']."', '".$res['teacher']."', '".$res['room_number']."', 
                                                  '".$res['room_name']."', ".$res['elective'].");";

    $db->query($query);

}