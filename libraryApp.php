<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "library";

    private $connection;

    public function __construct() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function closeConnection() {
        $this->connection->close();
    }
}
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
class libraryApp{
    private $db;
    private $memberRepository;
    private $bookRepository;
    private $loanRepository;

    public function __construct($db) {
        $this->db = $db;
        $this->memberRepository = new MemberRepository($db);
        $this->bookRepository = new BookRepository($db);
        $this->loanRepository = new LoanRepository($db);
    }
    public function run() {
        while (true)
        {
            echo "\n==== Welcome to the Library Management System ====\n";
            echo "1. Add Member\n";
            echo "2. Add Book\n";
            echo "3. Borrow Book\n";
            echo "4. Return Book\n";
            echo "5. View Loans\n";
            echo "6. Exit\n";
            $choice = readline("Enter your choice: ");

            switch ($choice) {
                case 1:
                    $name = readline("Enter member name: ");
                    $email = readline("Enter member email: ");
                    $phone = readline("Enter member phone: ");
                    $memberId = uniqid();
                    $member = new Member($name, $email, $phone, $memberId);
                    $this->memberRepository->addMember($member);
                    echo "Member added successfully.\n";
                    break;
                case 2:
                    $title = readline("Enter book title: ");
                    $author = readline("Enter book author: ");
                    $isbn = readline("Enter book ISBN: ");
                    $publishedDate = readline("Enter published date (YYYY-MM-DD): ");
                    $copies_available = (int)readline("Enter number of copies available: ");
                    $book = new Book($title, $author, $isbn, $publishedDate, $copies_available);
                    $this->bookRepository->addBook($book);
                    echo "Book added successfully.\n";
                    break;
                case 3:
                    // Borrow book logic
                    $title = readline("Enter book title to borrow: ");
                    $memberId = readline("Enter your member ID: ");
                    $book = $this->bookRepository->getBookBytitle($title);
                    if ($book && $book->copies_available > 0) {
                        $loan = new Loan(null, $memberId, $book->id, date("Y-m-d"));
                        $this->loanRepository->addLoan($loan);
                        $book->copies_available--;
                        $this->bookRepository->updateBook($book);
                        echo "Book borrowed successfully.\n";
                    } else {
                        echo "Book not available for borrowing.\n";
                    }
                    break;
                case 4:
                    // Return book logic
                    $loanId = readline("Enter loan ID to return: ");
                    $loan = $this->loanRepository->getLoan($loanId);
                    if ($loan) {
                        $loan->returnDate = date("Y-m-d");
                        $this->loanRepository->updateLoan($loan);
                        $book = $this->bookRepository->getBook($loan->bookId);
                        if ($book) {
                            $book->copies_available++;
                            $this->bookRepository->updateBook($book);
                        }
                        echo "Book returned successfully.\n";
                    } else {
                        echo "Invalid loan ID.\n";
                    }
                    break;
                case 5:
                    // View loans logic
                    $loans = $this->loanRepository->getAllLoans();
                    foreach ($loans as $loan) {
                        echo $loan->getLoanDetails() . "\n";
                    }
                    break;
                case 6:
                    exit("Exiting the system.\n");
                default:
                    echo "Invalid choice. Please try again.\n";
            }
        }
    }
}

    $db = new Database();
    $app = new LibraryApp($db);
    $app->run();
?>