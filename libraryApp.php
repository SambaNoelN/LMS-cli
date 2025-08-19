

<?php

class Book {
    private $title;
    private $author;
    private $isbn;
    private $isAvailable;

    public function __construct(string $title, string $author, string $isbn) {
        $this->title = $title;
        $this->author = $author;
        $this->isbn = $isbn;
        $this->isAvailable = true;
    }

    public function borrowBook() {
        $this->isAvailable = false;
    }

    public function returnBook() {
        $this->isAvailable = true;
    }

    public function getBookInfo(): string {
        $status = $this->isAvailable ? " Available" : " Borrowed";
        return sprintf("%-25s | %-18s | %-8s | %-12s", $this->title, $this->author, $this->isbn, $status);
    }

    public function getTitle(): string { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function getIsbn(): string { return $this->isbn; }
    public function isAvailable(): bool { return $this->isAvailable; }
}


class Member {
    private $name;
    private $memberId;
    private $borrowedBooks;

    public function __construct(string $name, string $memberId) {
        $this->name = $name;
        $this->memberId = $memberId;
        $this->borrowedBooks = [];
    }

    public function borrowBook(Book $book) {
        $this->borrowedBooks[$book->getIsbn()] = $book;
    }

    public function returnBook(Book $book) {
        unset($this->borrowedBooks[$book->getIsbn()]);
    }

    public function getBorrowedBooks(): array {
        return $this->borrowedBooks;
    }

    public function getBorrowedBooksCount(): int {
        return count($this->borrowedBooks);
    }

    public function getName(): string { return $this->name; }
    public function getMemberId(): string { return $this->memberId; }
}

class Library {
    private $books;
    private $members;

    public function __construct() {
        $this->books = [];
        $this->members = [];
    }

    public function addBook(Book $book): bool {
        if (isset($this->books[$book->getIsbn()])) {
            return false;
        }
        $this->books[$book->getIsbn()] = $book;
        return true;
    }

    public function addMember(Member $member): bool {
        if (isset($this->members[$member->getMemberId()])) {
            return false;
        }
        $this->members[$member->getMemberId()] = $member;
        return true;
    }

    public function findBook(string $isbn): ?Book {
        return $this->books[$isbn] ?? null;
    }

    public function findMember(string $memberId): ?Member {
        return $this->members[$memberId] ?? null;
    }

    public function getAllBooks(): array {
        return $this->books;
    }

    public function getAllMembers(): array {
        return $this->members;
    }

    public function getAvailableBooks(): array {
        return array_filter($this->books, fn($book) => $book->isAvailable());
    }
}




class Menu {
    private $library;

    public function __construct(Library $library) {
        $this->library = $library;
    }

    public function display() {
        
        
        echo "          LIBRARY MANAGEMENT SYSTEM - MAIN MENU         \n";
        echo " 1. View All Books                                        \n";
        echo " 2. View Available Books                                  \n";
        echo " 3. View All Members                                      \n";
        echo " 4. Add New Book                                          \n";
        echo " 5. Add New Member                                        \n";
        echo " 6. Borrow Book                                           \n";
        echo " 7. Return Book                                           \n";
        echo " 8. View Member's Borrowed Books                          \n";
        echo " 9. Search Book by ISBN                                   \n";
        echo " 0. Exit                                                  \n";
        
    }

    public function handleChoice($choice) {
        switch ($choice) {
            case '1': $this->viewAllBooks(); break;
            case '2': $this->viewAvailableBooks(); break;
            case '3': $this->viewAllMembers(); break;
            case '4': $this->addNewBook(); break;
            case '5': $this->addNewMember(); break;
            case '6': $this->borrowBook(); break;
            case '7': $this->returnBook(); break;
            case '8': $this->viewMembersBorrowedBooks(); break;
            case '9': $this->searchBookByISBN(); break;
            case '0': return false;
            default:
                echo " Invalid choice. Please try again.\n";
               
        }
        return true;
    }

    private function viewAllBooks() {
       
        $books = $this->library->getAllBooks();
       
        echo " ALL BOOKS\n";
        printf("%-25s | %-18s | %-8s | %-12s\n", "Title", "Author", "ISBN", "Status");
        echo str_repeat("-", 75) . "\n";
        foreach ($books as $book) {
            echo $book->getBookInfo() . "\n";
        }
        echo "Total Books: " . count($books) . "\n";
       
    }

    private function viewAvailableBooks() {
       
        $books = $this->library->getAvailableBooks();
        echo "      AVAILABLE BOOKS\n";
        printf("%-25s | %-18s | %-8s | %-12s\n", "Title", "Author", "ISBN", "Status");
        echo str_repeat("-", 75) . "\n";
        foreach ($books as $book) {
            echo $book->getBookInfo() . "\n";
        }
        echo "Available Books: " . count($books) . "\n";
       
    }

    private function viewAllMembers() {
       
        $members = $this->library->getAllMembers();
        echo "   ALL MEMBERS\n";
        printf("%-20s | %-8s | %-20s\n", "Name", "ID", "Borrowed Books");
        echo str_repeat("-", 55) . "\n";
        foreach ($members as $member) {
            printf("%-20s | %-8s | %-20s\n", $member->getName(), $member->getMemberId(), $member->getBorrowedBooksCount());
        }
        echo "Total Members: " . count($members) . "\n";
        
    }

    private function addNewBook() {
       
        echo " ADD NEW BOOK\n";
        $title = "Enter book title: ";
        $author = "Enter author: ";
        $isbn = "Enter ISBN: ";
        if ($this->library->findBook($isbn)) {
            echo " Book with this ISBN already exists!\n";
        } else {
            $book = new Book($title, $author, $isbn);
            $this->library->addBook($book);
            echo " Book added successfully!\n";
        }
       
    }

    private function addNewMember() {
       
        echo " ADD NEW MEMBER\n";
        $name = "Enter member name: ";
        $memberId = "Enter member ID: ";
        if ($this->library->findMember($memberId)) {
            echo " Member with this ID already exists!\n";
        } else {
            $member = new Member($name, $memberId);
            $this->library->addMember($member);
            echo " Member added successfully!\n";
        }
       
    }

    private function borrowBook() {
        
        echo " BORROW BOOK\n";
        $memberId = "Enter member ID: ";
        $member = $this->library->findMember($memberId);
        if (!$member) {
            echo " Member not found!\n";
           
            return;
        }
        $isbn = "Enter book ISBN: ";
        $book = $this->library->findBook($isbn);
        if (!$book) {
            echo " Book not found!\n";
        } elseif (!$book->isAvailable()) {
            echo " Book is already borrowed!\n";
        } else {
            $book->borrowBook();
            $member->borrowBook($book);
            echo " Book borrowed successfully!\n";
        }
        
    }

    private function returnBook() {
        
        echo " RETURN BOOK\n";
        $memberId = "Enter member ID: ";
        $member = $this->library->findMember($memberId);
        if (!$member) {
            echo " Member not found!\n";
           
            return;
        }
        $isbn = "Enter book ISBN: ";
        $book = $this->library->findBook($isbn);
        if (!$book) {
            echo " Book not found!\n";
        } elseif (!isset($member->getBorrowedBooks()[$isbn])) {
            echo " This member did not borrow this book!\n";
        } else {
            $book->returnBook();
            $member->returnBook($book);
            echo " Book returned successfully!\n";
        }
        
    }

    private function viewMembersBorrowedBooks() {
        
        echo " MEMBER'S BORROWED BOOKS\n";
        $memberId = "Enter member ID: ";
        $member = $this->library->findMember($memberId);
        if (!$member) {
            echo " Member not found!\n";
            
            return;
        }
        $borrowedBooks = $member->getBorrowedBooks();
        if (empty($borrowedBooks)) {
            echo "  No books borrowed by this member.\n";
        } else {
            printf("%-25s | %-18s | %-8s\n", "Title", "Author", "ISBN");
            echo str_repeat("-", 55) . "\n";
            foreach ($borrowedBooks as $book) {
                printf("%-25s | %-18s | %-8s\n", $book->getTitle(), $book->getAuthor(), $book->getIsbn());
            }
        }
        
    }

    private function searchBookByISBN() {
       
        echo " SEARCH BOOK BY ISBN\n";
        $isbn = "Enter ISBN: ";
        $book = $this->library->findBook($isbn);
        if (!$book) {
            echo " Book not found!\n";
        } else {
            printf("%-25s | %-18s | %-8s | %-12s\n", "Title", "Author", "ISBN", "Status");
            echo str_repeat("-", 75) . "\n";
            echo $book->getBookInfo() . "\n";
        }
    
    }
}
                            
class LibraryApp {
    private $library;
    private $menu;

    public function __construct() {
        $this->library = new Library();
        $this->initializeData();
        $this->menu = new Menu($this->library);
    }

    private function initializeData() {
        
        $this->library->addBook(new Book("The Great Gatsby", "F.S. Fitzgerald", "001"));
        $this->library->addBook(new Book("1984", "George Orwell", "002"));
        $this->library->addBook(new Book("To Kill a Mockingbird", "Harper Lee", "003"));
        $this->library->addBook(new Book("Pride and Prejudice", "Jane Austen", "004"));
        
        $this->library->addMember(new Member("Alice Johnson", "M001"));
        $this->library->addMember(new Member("Bob Smith", "M002"));
        $this->library->addMember(new Member("Carol Davis", "M003"));
    }

    public function run() {
        while (true) {
            $this->menu->display();
            $choice = "Enter your choice: " [y]''''];
            if ($this->menu->handleChoice($choice) === false) {
                echo "\n Exiting Library Management System. Goodbye!\n";
                break;
            }
        }
    }
}

$app = new LibraryApp();
$app->run();
?>