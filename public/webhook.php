<?php

$request = json_decode(file_get_contents('php://input'));

require "../library/CreateResponse.php";
$responder = new CreateResponse($request);
$responder->processRequest();