<?php
    class Loan {
        private $loanId;
        private $memberId;
        private $bookId;
        private $borrowDate;
        private $returnDate;

        public function __construct($loanId, $memberId, $bookId, $borrowDate, $returnDate = null) {
            $this->loanId = $loanId;
            $this->memberId = $memberId;
            $this->bookId = $bookId;
            $this->borrowDate = $borrowDate;
            $this->returnDate = $returnDate;
        }

        public function getLoanDetails() {
            return "Loan ID: {$this->loanId}, Member ID: {$this->memberId}, Book ID: {$this->bookId}, Borrow Date: {$this->borrowDate}, Return Date: {$this->returnDate}";
        }
    }

?>