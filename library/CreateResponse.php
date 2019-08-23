<?php


class CreateResponse
{

    private $projectId       = "businessappebs";
    private $baseIntentUrl = "projects/businessappebs/agent/intents/";
    private $classId;
    private $className;
    private $sessionId;
    private $replyType;
    private $assignments = array();

    private $request;

    public function __construct($request)
    {

        require "../settings.php";
        require "../library/Assignments.php";
        require "../library/Schedule.php";

        $this->sessionId = substr($this->request->session, -36);

        $this->request = $request;

    }

    public function processRequest() {

        // Following are the intent IDs to figure out what are we talking about
        // Next class = 101a710c-41c9-4ed3-9f9a-be14c924ff8e
        // -- Teacher = 64d94550-3355-4254-aba2-c4410b21acb8
        // -- Room = 6f5574aa-b371-41fc-bef8-6647a3cba8f3
        // -- Assignments = 9f9904b3-a94a-402e-afd8-4edbd8b5ef89
        // -- Elective = e2178554-45da-4df7-9816-204cb55dc9fb
        // Nearest assignment deadline = 7c5cde37-8a49-4c0e-87cd-39974d76048e
        // All assignments (may be for a specific class) = 65cc06cc-103d-4682-8b27-47551cd1a6c4

        switch ($this->request->queryResult->intent->name) {

            // When is the next class?
            case $this->baseIntentUrl."101a710c-41c9-4ed3-9f9a-be14c924ff8e": $text2speech = $this->getNextClassText();
                break;
            // Who is the teacher?
            case $this->baseIntentUrl."64d94550-3355-4254-aba2-c4410b21acb8": $text2speech = $this->getNextClassTeacher();
                break;
            // What is the room number?
            case $this->baseIntentUrl."6f5574aa-b371-41fc-bef8-6647a3cba8f3": $text2speech = $this->getNextClassRoomNumber();
                break;
            // Do I have any assignments for that class?
            case $this->baseIntentUrl."9f9904b3-a94a-402e-afd8-4edbd8b5ef89": $text2speech = $this->getNextClassAssignments();
                break;
            // Is it elective?
            case $this->baseIntentUrl."e2178554-45da-4df7-9816-204cb55dc9fb": $text2speech = $this->getNextClassElectiveAttr();
                break;
            // What is the nearest assignment dead line?
            case $this->baseIntentUrl."7c5cde37-8a49-4c0e-87cd-39974d76048e": $text2speech = $this->getNextAssignment();
                break;
            // What assignments do I have?
            //case $this->baseIntentUrl."65cc06cc-103d-4682-8b27-47551cd1a6c4": $text2speech = $this->getAllAssignments();
            //    break;
            default:
                $text2speech = "I didn't quite get that...";


        }

        header('Content-Type: application/json');

        if($this->replyType == "full" OR $this->replyType == "short") {

            if($this->replyType == "full") {
                $template = file_get_contents("../public/response_full.json");
            } else {
                $template = file_get_contents("../public/response_short.json");
            }

            $macro  = array("{TEXT_2_CONTINUE}","{TEXT_2_SPEECH}","{CLASS_NAME}","{CLASS_ID}","{PROJECT_ID}","{SESSION_ID}");
            $values = array($this->getRandomContinueQuestion(), $text2speech, $this->className, $this->classId, $this->projectId, $this->sessionId);
            $template = str_replace($macro, $values, $template);

            print $template;
        }

        if($this->replyType == "deadline") {

            $template = file_get_contents("../public/response_deadline.json");

            $macro  = array("{TEXT_2_CONTINUE}",
                            "{TEXT_2_SPEECH}",
                            "{ASSIGNMENT_NAME}",
                            "{CLASS_ID}",
                            "{PROJECT_ID}",
                            "{SESSION_ID}",
                            "{ASSIGNMENT_ID}");
            $values = array($this->getRandomContinueQuestion(),
                            $text2speech,
                            $this->assignments['name'],
                            $this->assignments['course_id'],
                            $this->projectId,
                            $this->sessionId,
                            $this->assignments['id']);
            $template = str_replace($macro, $values, $template);

            print $template;
        }

        if($this->replyType == "list") {

            require "../public/response_list.json";

        }
    }

    /**
     *
     */
    public function getNextClassText() {

        $this->replyType = "full";

        $sc        = new Schedule();
        $nextClass = $sc->getNextClass();
        $this->className = $nextClass['name'];

        //
        $ass = new Assignments();
        $this->classId = $ass->getClassIdByName($this->className);

        $text = "The next class '".$nextClass['name']."' will be";

        if(date("jS F Y", $nextClass['start']) == date("jS F Y", time())) {
            $textDate = " today";
        }
        elseif (date("jS F Y", $nextClass['start']) == date("jS F Y", time() + 3600*24)) {
            $textDate = " tomorrow";
        }
        else {
            $textDate = " on ".date("jS F Y", $nextClass['start']);
        }

        $textTime = ", starting at ".date("H:i", $nextClass['start']).".";

        $text = $text.$textDate.$textTime;
        return $text;
    }

    /**
     *
     */
    private function getNextClassTeacher() {

        $this->replyType = "short";

        $sc      = new Schedule();
        $nextClass = $sc->getNextClass();
        $teacher = $sc->getTeacher($nextClass['ID']);
        $teacher = $teacher['teacher'];

        $text = "The instructor for this class is ".$teacher.". ";
        return $text;
    }

    /**
     *
     */
    private function getNextClassRoomNumber() {

        $this->replyType = "short";

        $sc      = new Schedule();
        $nextClass = $sc->getNextClass();
        $room = $sc->getRoom($nextClass['ID']);
        $room = $room['room_number'];

        $floor = substr($room, 0, 1);

        if($floor == 1) $floor .= "st";
        if($floor == 2) $floor .= "nd";
        if($floor == 3) $floor .= "rd";
        if($floor == 4) $floor .= "th";

        $text = "The class will happen in the room ".$room." on the ".$floor." floor.";
        return $text;

    }

    /**
     *
     */
    private function getNextClassElectiveAttr() {

        $this->replyType = "short";

        $sc      = new Schedule();
        $nextClass = $sc->getNextClass();
        $elective = $sc->getElectiveStatus($nextClass['ID']);
        $elective = $elective['elective'];

        if($elective)
            $text = "Yes, this class is elective";
        else
            $text = "No, this class is compulsory";

        return $text;

    }

    /**
     *
     */
    private function getNextClassAssignments() {

        $this->replyType = "list";

        $sc        = new Schedule();
        $nextClass = $sc->getNextClass();
        $this->className = $nextClass['name'];

        //
        $ass = new Assignments();
        $this->assignments = $ass->getAssignmentsByClassName($this->className);
        $this->classId     = $ass->getClassIdByName($this->className);

        $text = "Here is the list of upcoming assignments for this class.";

        return $text;
    }

    /**
     *
     */
    private function getNextAssignment() {

        $this->replyType = "deadline";

        $ass = new Assignments();
        $this->assignments = $ass->getNearestDeadlineAssignment();

        $text = "Here is the most urgent assignment with the deadline on ".date("jS M Y", $this->assignments['deadline']).".";

        return $text;

    }

    /**
     *
     *//*
    private function getAllAssignments() {

        $this->replyType = "short";

    }*/

    /**
     *
     */
    private function getRandomContinueQuestion() {

        $questions[] = "Do you have any more questions?";
        $questions[] = "Can I help with anything else?";
        $questions[] = "Is there anything else I can do?";
        $questions[] = "Do you need more information?";

        return $questions[rand(0, 3)];
    }
}
