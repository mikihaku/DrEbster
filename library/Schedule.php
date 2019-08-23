<?php

/**
 * Class Schedule. Handles requests about 'Next class'
 */
class Schedule
{

    private $db;

    /**
     * Schedule constructor. Connect to the database.
     */
    public function __construct()
    {

        require "../library/Database.php";
        $this->db = new Database();

    }

    /**
     * Returns the class that has the string date nearest to current date and time
     *
     * @return mixed
     */
    public function getNextClass() {

        $query = "SELECT * FROM schedule WHERE start > ? LIMIT 1";
        $stmt  = $this->db->prepare($query);
        $stmt->execute([time()]);
        $class = $stmt->fetch(PDO::FETCH_ASSOC);

        return $class;
    }

    /**
     * Return the name of the teacher for the nearest class
     *
     * @param $classId
     * @return mixed
     */
    public function getTeacher($classId) {

        $query = "SELECT `teacher` FROM schedule WHERE ID > ?";
        $stmt  = $this->db->prepare($query);
        $stmt->execute([$classId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Return room number and the floor of the nearest class
     *
     * @param $classId
     * @return mixed
     */
    public function getRoom($classId) {

        $query = "SELECT `room_name`, `room_number` FROM schedule WHERE ID > ?";
        $stmt  = $this->db->prepare($query);
        $stmt->execute([$classId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Return the value of the 'elective' attribute for the next class
     *
     * @param $classId
     * @return mixed
     */
    public function getElectiveStatus($classId) {

        $query = "SELECT `elective` FROM schedule WHERE ID > ?";
        $stmt  = $this->db->prepare($query);
        $stmt->execute([$classId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

}