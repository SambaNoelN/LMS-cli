<?php
    class Book {
        private $title;
        private $author;
        private $isbn;
        private $isAvailable;
        private $publishedDate;
        private $copies_available;

        public function __construct($title, $author, $isbn, $publishedDate, $copies_available) {
            $this->title = $title;
            $this->author = $author;
            $this->isbn = $isbn;
            $this->publishedDate = $publishedDate;  
            $this->isAvailable = true;
            $this->copies_available = $copies_available;
        }

        public function getDetails() {
            return "Title: {$this->title}, Author: {$this->author}, ISBN: {$this->isbn}, Published Date: {$this->publishedDate}, Available Copies: {$this->copies_available}";
        }
        public function borrow() {
            if ($this->isAvailable) {
                $this->isAvailable = false;
                return "You have borrowed '{$this->title}'.";
            } else {
                return "Sorry, '{$this->title}' is currently not available.";
            }
        }
        public function returnBook() {
            $this->isAvailable = true;
            return "You have returned '{$this->title}'.";
        }
    }

?>
