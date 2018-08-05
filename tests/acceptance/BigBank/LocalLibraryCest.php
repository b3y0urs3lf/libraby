<?php

namespace Page\BigBank;

use \AcceptanceTester;

class LocalLibraryCest
{
    public function baseFlowTest(AcceptanceTester $I)
    {
        $books = new LibraryBooks($I);
        $authors = new LibraryAuthors($I);
        $genres = new LibraryGenres($I);

        $booksData = json_decode(
            file_get_contents(__DIR__. '/fixtures/localLibrary.json'),
            true
        );

        $I->wantTo('Step 1 - Open Local library web page & check that');
        $I->amOnUrl('https://raamatukogu.herokuapp.com');
        $I->see('Local Library Home');

        $I->wantTo('Step 2 - Check if Authors exist & add them if not');
        $addedAuthors = $authors->checkAndAddAuthors($booksData);

        $I->wantTo('Step 3 - Check if Genres exist & add them if not');
        $addedGenres = $genres->checkAndAddGenres($booksData);

        $I->wantTo('Step 4 - remove all books & add new ones');
        $books->deleteAllBooks();
        $addedBooks = $books->addBooks($booksData);

        $I->wantTo('Step 5 - verify that books added (by 3 conditions)');
        $books->verifyAddedBooks($addedBooks);

        $I->wantTo('Step 6 - remove all added data');
//        $books->deleteAddedBooks($addedBooks);
//        $genres->deleteAddedGenres($addedGenres);
//        $authors->deleteAddedAuthors($addedAuthors);

        $I->wantTo('Step 7 - remove all data');
//        $books->deleteAllBooks();
//        $authors->deleteAllAuthors();
//        $genres->deleteAllGenres();

    }
}

?>