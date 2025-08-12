<?php
     class Member {
         private $memberId;
        private $name;
        private $email;
        private $phone;
       
        private $borrowedBooks = [];

        public function __construct($name, $email, $phone, $memberId) {
            $this->name = $name;
            $this->email = $email;
            $this->phone = $phone;
            $this->memberId = $memberId;
        }

        public function borrowBook($book) {
            $this->borrowedBooks[] = $book;
        }

        public function returnBook($book) {
            $key = array_search($book, $this->borrowedBooks);
            if ($key !== false) {
                unset($this->borrowedBooks[$key]);
            }
        }

        public function getBorrowedBooksCount() {
            return count($this->borrowedBooks);
        }
     }

?>