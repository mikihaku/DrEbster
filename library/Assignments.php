<?php

/**
 * Class Assignments. Deals with queries to the Canvas API
 */
class Assignments
{

    private $key; // API Authentication key
    private $baseUrl = "https://ebs.instructure.com/api/v1/"; // URL of the EBS canvas pages

    /**
     * Assignments constructor. Do some initialization of the class.
     */
    public function __construct()
    {
        $canvasKey = "";

        require "../settings.php";

        $this->key = $canvasKey;

    }

    /**
     * Send request to the API
     *
     * @param $url
     * @return bool|string
     */
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

    /**
     * Return all available course
     *
     * @return mixed
     */
    private function getCourses() {

        $rawResponse = $this->sendRequest("courses");

        $courses = json_decode($rawResponse);

        return $courses;
    }

    /**
     * Return ony courses that started maximum 3 month ago
     *
     * @return array
     */
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

    /**
     * Returns all assignments for the active courses
     *
     * @return array
     */
    public function getActiveCoursesAssignments() {

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
                                                            "possible_points" => $assignment->points_possible,
                                                            "course_id" => $courseId,
                                                            "course_name" => $courseName
                                                            );
            }

            $courseAssignments[$courseId] = array("name" => $courseName, "assignments" => $activeAssignments);
        }

        return $courseAssignments;
    }

    /**
     * Get assignments from the course defined by name
     *
     * @param $className
     * @return mixed
     */
    public function getAssignmentsByClassName($className) {

        $className = $className . " IntMBA-2";

        $allAssignments = $this->getActiveCoursesAssignments();

        foreach ($allAssignments as $assignmentId => $assignment) {

            if($className == $assignment['name']) {

                return $allAssignments[$assignmentId]['assignments'];

            }
        }
    }

    /**
     * Returns an assignment that has least time before deadline
     *
     * @return array|mixed
     */
    public function getNearestDeadlineAssignment() {

        $courses = $this->getActiveCoursesAssignments();

        $nearestAssignment = array();
        $nearestDeadLine   = 99999999999999999;

        foreach ($courses as $courseId => $courseContent) {

            foreach ($courseContent['assignments'] as $assignmentId => $assignment) {

                if($assignment['deadline'] > time() AND $assignment['deadline'] < $nearestDeadLine) {

                    $nearestDeadLine = $assignment['deadline'];
                    $nearestAssignment = $assignment;

                }

            }
        }

        return $nearestAssignment;
    }

    /**
     * Returns ID of the course defined by the name
     *
     * @param $className
     * @return int|string
     */
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