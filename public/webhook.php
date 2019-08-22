<?php

$project = "businessappebs";
$baseIntentUrl = "projects/businessappebs/agent/intents/";

// Following are the intent IDs to figure out what are we talking about
// Next class = 101a710c-41c9-4ed3-9f9a-be14c924ff8e
// -- Teacher = 64d94550-3355-4254-aba2-c4410b21acb8
// -- Room = 6f5574aa-b371-41fc-bef8-6647a3cba8f3
// -- Assignments = 9f9904b3-a94a-402e-afd8-4edbd8b5ef89
// -- Elective = e2178554-45da-4df7-9816-204cb55dc9fb
// Nearest assignment deadline = 7c5cde37-8a49-4c0e-87cd-39974d76048e
// All assignments (may be for a specific class) = 65cc06cc-103d-4682-8b27-47551cd1a6c4

$request = json_decode(file_get_contents('php://input'));

echo $request->queryResult->intent->name;

//print_r($request);
//$assignmentsProcessor = new Assignments();

switch ($request->queryResult->intent->name) {

    case $baseIntentUrl."101a710c-41c9-4ed3-9f9a-be14c924ff8e": echo "What is the next class?";
        break;
    default:
        echo "I didn't quite get that...";


}