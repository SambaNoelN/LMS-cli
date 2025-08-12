<?php
    class BookRepository {
        private $db;

        public function __construct($db) {
            $this->db = $db;
        }

        public function addBook($book) {
            $stmt = $this->db->prepare("INSERT INTO books (title, author, isbn, published_date, copies_available) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $book->title, $book->author, $book->isbn, $book->publishedDate, $book->copies_available);
            return $stmt->execute();
        }

        public function getBook($id) {
            $stmt = $this->db->prepare("SELECT * FROM books WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_object("Book");
        }

        public function getAllBooks() {
            $result = $this->db->query("SELECT * FROM books");
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function updateBook($book) {
            $stmt = $this->db->prepare("UPDATE books SET title = ?, author = ?, isbn = ?, published_date = ?, copies_available = ? WHERE id = ?");
            $stmt->bind_param("ssssii", $book->title, $book->author, $book->isbn, $book->publishedDate, $book->copies_available, $book->id);
            return $stmt->execute();
        }

        public function deleteBook($id) {
            $stmt = $this->db->prepare("DELETE FROM books WHERE id = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }
    }
?>