<?php

require "../library/Assignments.php";

$processor = new Assignments();

print_r($processor->getActiveCourseAssignments());
