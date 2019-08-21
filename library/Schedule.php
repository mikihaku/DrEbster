<?php


class Schedule
{

    private $db;

    public function __construct()
    {

        require "../library/Database.php";
        $this->db = new Database();

    }

    public function getNextClass() {

        $query = "SELECT * FROM schedule WHERE start > ? LIMIT 1";
        $stmt  = $this->db->prepare($query);
        $stmt->execute([time()]);
        $class = $stmt->fetch();

        return $class;
    }

    public function getTeacher($classId) {

        $query = "SELECT `teacher` FROM schedule WHERE ID > ?";
        $stmt  = $this->db->prepare($query);
        $stmt->execute([$classId]);
        $result = $stmt->fetch();

        return $result;
    }

    public function getRoom($classId) {

        $query = "SELECT `room_name`, `room_number` FROM schedule WHERE ID > ?";
        $stmt  = $this->db->prepare($query);
        $stmt->execute([$classId]);
        $result = $stmt->fetch();

        return $result;
    }

    public function getElectiveStatus($classId) {

        $query = "SELECT `elective` FROM schedule WHERE ID > ?";
        $stmt  = $this->db->prepare($query);
        $stmt->execute([$classId]);
        $result = $stmt->fetch();

        return $result;
    }

}