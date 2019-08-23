<?php

// Get the data received from the Dialogflow server and decode
$request = json_decode(file_get_contents('php://input'));

require "../library/CreateResponse.php";
$responder = new CreateResponse($request); // Create new router
$responder->processRequest(); // Redirect the response to the router