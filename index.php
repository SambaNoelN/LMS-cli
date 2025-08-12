<?php
    require_once 'database.php';
    require_once 'member.php';
    require_once 'book.php';
    require_once 'loan.php';
    require_once 'memberRepository.php';
    require_once 'bookRepository.php';
    require_once 'loanRepository.php';
    require_once 'libraryApp.php';

    $db = new Database();
    $app = new LibraryApp($db);
    $app->run();
?>