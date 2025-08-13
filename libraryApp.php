<?php

class Book {
    public $id;
    public $title;
    public $author;
    public $isbn;
    public $published_year;
    public $copies_available;

    public function __construct($id, $title, $author, $isbn, $year, $copies) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->isbn = $isbn;
        $this->published_year = $year;
        $this->copies_available = $copies;
    }
}

class Member {
    public $id;
    public $name;
    public $email;
    public $phone;

    public function __construct($id, $name, $email, $phone) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
    }
}

class Loan {
    public $id;
    public $book_id;
    public $member_id;
    public $loan_date;
    public $return_date;
    public $status;

    public function __construct($id, $book_id, $member_id) {
        $this->id = $id;
        $this->book_id = $book_id;
        $this->member_id = $member_id;
        $this->loan_date = date('Y-m-d');
        $this->return_date = null;
        $this->status = 'borrowed';
    }
}


class BookRepository {
    private $books = [];
    private $nextId = 1;

    public function addBook(Book $book) {
        $book->id = $this->nextId++;
        $this->books[$book->id] = $book;
    }

    public function getAllBooks() {
        return $this->books;
    }

    public function getBookById($id) {
        return $this->books[$id] ?? null;
    }

    public function updateBook(Book $book) {
        $this->books[$book->id] = $book;
    }
}

class MemberRepository {
    private $members = [];
    private $nextId = 1;

    public function addMember(Member $member) {
        $member->id = $this->nextId++;
        $this->members[$member->id] = $member;
    }

    public function getAllMembers() {
        return $this->members;
    }

    public function getMemberById($id) {
        return $this->members[$id] ?? null;
    }
}

class LoanRepository {
    private $loans = [];
    private $nextId = 1;

    public function issueBook(Loan $loan) {
        $loan->id = $this->nextId++;
        $this->loans[$loan->id] = $loan;
    }

    public function returnBook($loanId) {
        if (isset($this->loans[$loanId])) {
            $this->loans[$loanId]->status = 'returned';
            $this->loans[$loanId]->return_date = date('Y-m-d');
        }
    }

    public function getAllLoans() {
        return $this->loans;
    }
}

// ===== APPLICATION =====
class LibraryApp {
    private $bookRepo;
    private $memberRepo;
    private $loanRepo;

    public function __construct($bookRepo, $memberRepo, $loanRepo) {
        $this->bookRepo = $bookRepo;
        $this->memberRepo = $memberRepo;
        $this->loanRepo = $loanRepo;
    }

    public function run() {
        while (true) {
            echo "\n==== Library Management System ====\n";
            echo "1. Add Book\n";
            echo "2. View Books\n";
            echo "3. Add Member\n";
            echo "4. View Members\n";
            echo "5. Issue Book\n";
            echo "6. Return Book\n";
            echo "0. Exit\n";
            echo "Choose option: ";
            $choice = trim(fgets(STDIN));

            switch ($choice) {
                case 1: $this->addBook(); break;
                case 2: $this->viewBooks(); break;
                case 3: $this->addMember(); break;
                case 4: $this->viewMembers(); break;
                case 5: $this->issueBook(); break;
                case 6: $this->returnBook(); break;
                case 0: exit("Goodbye!\n");
                default: echo "Invalid choice.\n";
            }
        }
    }

    private function addBook() {
        echo "Title: "; $title = trim(fgets(STDIN));
        echo "Author: "; $author = trim(fgets(STDIN));
        echo "ISBN: "; $isbn = trim(fgets(STDIN));
        echo "Published Year: "; $year = trim(fgets(STDIN));
        echo "Copies: "; $copies = trim(fgets(STDIN));
        $book = new Book(null, $title, $author, $isbn, $year, $copies);
        $this->bookRepo->addBook($book);
        echo "Book added!\n";
    }

    private function viewBooks() {
        $books = $this->bookRepo->getAllBooks();
        foreach ($books as $b) {
            echo "{$b->id}: {$b->title} by {$b->author} ({$b->copies_available} copies)\n";
        }
    }

    private function addMember() {
        echo "Name: "; $name = trim(fgets(STDIN));
        echo "Email: "; $email = trim(fgets(STDIN));
        echo "Phone: "; $phone = trim(fgets(STDIN));
        $member = new Member(null, $name, $email, $phone);
        $this->memberRepo->addMember($member);
        echo "Member added!\n";
    }

    private function viewMembers() {
        $members = $this->memberRepo->getAllMembers();
        foreach ($members as $m) {
            echo "{$m->id}: {$m->name} ({$m->email})\n";
        }
    }

    private function issueBook() {
        echo "Book ID: "; $bookId = trim(fgets(STDIN));
        echo "Member ID: "; $memberId = trim(fgets(STDIN));

        $book = $this->bookRepo->getBookById($bookId);
        $member = $this->memberRepo->getMemberById($memberId);

        if (!$book) {
            echo "Book not found.\n";
            return;
        }
        if (!$member) {
            echo "Member not found.\n";
            return;
        }
        if ($book->copies_available <= 0) {
            echo "No copies available.\n";
            return;
        }

        $book->copies_available--;
        $this->bookRepo->updateBook($book);

        $loan = new Loan(null, $bookId, $memberId);
        $this->loanRepo->issueBook($loan);
        echo "Book issued!\n";
    }

    private function returnBook() {
        echo "Loan ID: "; $loanId = trim(fgets(STDIN));
        $loans = $this->loanRepo->getAllLoans();

        if (!isset($loans[$loanId])) {
            echo "Loan not found.\n";
            return;
        }

        $loan = $loans[$loanId];
        if ($loan->status === 'returned') {
            echo "Book already returned.\n";
            return;
        }

        $this->loanRepo->returnBook($loanId);

        $book = $this->bookRepo->getBookById($loan->book_id);
        $book->copies_available++;
        $this->bookRepo->updateBook($book);

        echo "Book returned!\n";
    }
}

$bookRepo = new BookRepository();
$memberRepo = new MemberRepository();
$loanRepo = new LoanRepository();

$app = new LibraryApp($bookRepo, $memberRepo, $loanRepo);
$app->run();
