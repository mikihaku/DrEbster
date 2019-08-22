<?php


class Assignments
{

    private $key;
    private $baseUrl = "https://ebs.instructure.com/api/v1/";

    public function __construct()
    {
        $canvasKey = "";

        require "../settings.php";

        $this->key = $canvasKey;

    }

    private function sendRequest($url) {

        $User_Agent = 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31';

        $request_headers = array();
        $request_headers[] = 'User-Agent: '. $User_Agent;
        $request_headers[] = 'Authorization: Bearer '.$this->key;

        $ch = curl_init($this->baseUrl.$url);
              curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return curl_exec($ch);

    }

    private function getCourses() {

        $rawResponse = $this->sendRequest("courses");

        $courses = json_decode($rawResponse);

        return $courses;
    }

    private function getActiveCourses() {

        $allCourses = $this->getCourses();
        $activeCourses = array();

        foreach ($allCourses as $course) {

            if(strtotime($course->created_at) > (time() - 3600*24*90)) {

                $activeCourses[$course->id] = $course->name;

            }

        }

        return $activeCourses;
    }

    public function getActiveCourseAssignments() {

        $activeCourses = $this->getActiveCourses();
        $courseAssignments = array();

        foreach ($activeCourses as $courseId => $courseName) {

            $activeAssignments = array();

            $res = $this->sendRequest("courses/".$courseId."/assignments");
            $assignments = json_decode($res);

            foreach ($assignments as $assignment) {

                $activeAssignments[$assignment->id] = array("id" => $assignment->id,
                                                            "deadline" => strtotime($assignment->due_at),
                                                            "grading" => $assignment->grading_type,
                                                            "name" => $assignment->name,
                                                            "possible_points" => $assignment->points_possible
                                                            );
            }

            $courseAssignments[$courseId] = array("name" => $courseName, "assignments" => $activeAssignments);
        }

        return $courseAssignments;
    }

    public function getAssignmentsByClassName($className) {

        $className = $className . " IntMBA-2";

        $allAssignments = $this->getActiveCourseAssignments();

        foreach ($allAssignments as $assignmentId => $assignment) {

            if($className == $assignment['name']) {

                return $allAssignments[$assignmentId]['assignments'];

            }
        }
    }

    public function getClassIdByName($className) {

        $className = $className . " IntMBA-2";

        $activeCourses = $this->getActiveCourses();

        foreach ($activeCourses as $courseId => $courseName) {

            if($courseName == $className) {

                return $courseId;

            }
        }
    }
}