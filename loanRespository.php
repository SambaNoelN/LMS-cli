<?php
class LoanRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addLoan($loan) {
        $stmt = $this->db->prepare("INSERT INTO borrowed_books (member_id, book_id, borrow_date, return_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $loan->memberId, $loan->bookId, $loan->borrowDate, $loan->returnDate);
        return $stmt->execute();
    }

    public function getLoan($id) {
        $stmt = $this->db->prepare("SELECT * FROM borrowed_books WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_object("Loan");
    }

    public function getAllLoans() {
        $result = $this->db->query("SELECT * FROM borrowed_books");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateLoan($loan) {
        $stmt = $this->db->prepare("UPDATE borrowed_books SET member_id = ?, book_id = ?, borrow_date = ?, return_date = ? WHERE id = ?");
        $stmt->bind_param("iissi", $loan->memberId, $loan->bookId, $loan->borrowDate, $loan->returnDate, $loan->loanId);
        return $stmt->execute();
    }

    public function deleteLoan($id) {
        $stmt = $this->db->prepare("DELETE FROM borrowed_books WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>