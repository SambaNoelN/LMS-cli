<?php
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
                    $book = $this->bookRepository->getBookByTitle($title);
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
?>