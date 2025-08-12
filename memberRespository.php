<?php
    class MemberRepository {
        private $db;

        public function __construct($db) {
            $this->db = $db;
        }

        public function addMember($member) {
            $stmt = $this->db->prepare("INSERT INTO members (name, email, phone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $member->name, $member->email, $member->phone);
            return $stmt->execute();
        }

        public function getMember($id) {
            $stmt = $this->db->prepare("SELECT * FROM members WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_object("Member");
        }

        public function getAllMembers() {
            $result = $this->db->query("SELECT * FROM members");
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function updateMember($member) {
            $stmt = $this->db->prepare("UPDATE members SET name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("sssi", $member->name, $member->email, $member->phone, $member->id);
            return $stmt->execute();
        }

        public function deleteMember($id) {
            $stmt = $this->db->prepare("DELETE FROM members WHERE id = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }
    }
?>