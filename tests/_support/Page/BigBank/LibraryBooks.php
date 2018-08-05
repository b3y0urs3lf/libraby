<?php

namespace Page\BigBank;

class LibraryBooks extends BasePage
{
    public function addBooks($books)
    {
        $addedBooks = [];
        $I = $this->tester;
        $I->click("//a[text()='All books']");

        foreach($books as $book => $data){
            $bookUrl = $this->addBook($data);
            $addedBooks[] = [
                "book_id" => $bookUrl[3],
                "book_title" => $data["title"],
                "author_full_name" => "{$data["author"]["family_name"]}, {$data["author"]["first_name"]}",
            ];
        }

        //return array of books that you added
        return $addedBooks;
    }

    public function addBook($data)
    {
        $I = $this->tester;

        //add new book
        $I->click("//a[text()='Create new book']");
        $I->see('Create Book');
        $I->fillField("//label[text()='Title:']/following-sibling::input", $data["title"]);
        $I->selectOption("//select[@id='author']", "{$data["author"]["family_name"]}, {$data["author"]["first_name"]}");
        $I->fillField("//label[text()='Summary:']/following-sibling::input", $data["summary"]);
        $I->fillField("ISBN:", $data["isbn"]);
        $I->click("//label[text()='{$data['genre']}']");
        $I->click("//button[text()='Submit']");

        //verify that book added
        $I->seeElement("//h1[text()='Title: {$data["title"]}']");
        $I->seeElement("//*[text()='Author:']/following-sibling::a[text()='{$data["author"]["family_name"]}, {$data["author"]["first_name"]}']");
        $I->seeElement("//*[text()[contains(.,'{$data["summary"]}')]]");
        $I->seeElement("//p[text()[contains(.,' {$data["isbn"]}')]]");
        $I->seeElement("//*[text()='Genre:']/following-sibling::a[text()='{$data["genre"]}']");

        //return book unique ID
        return explode("/",$I->grabFromCurrentUrl());
    }

    public function verifyAddedBooks($addedBooks)
    {
        $I = $this->tester;
        $I->click("//a[text()='All books']");
        foreach($addedBooks as $book => $data){

            //3 conditions check: book_id, book_title, author
            $I->seeElement("//a[@href='/catalog/book/{$data["book_id"]}' and text()='{$data["book_title"]} ']/parent::li[text()='({$data["author_full_name"]})']");
        }
    }

    public function deleteAllBooks()
    {
        $I = $this->tester;

        $I->click("//a[text()='All books']");
        while ((count($I->grabMultiple("(//h1[text()='Book List']/following-sibling::li/a)[1]"))) !== 0){
            $bookUrl = $I->grabAttributeFrom("(//h1[text()='Book List']/following-sibling::li/a)[1]", 'href');
            $this->deleteBook(explode("/",$bookUrl)[5]);
        }
    }

    public function deleteAddedBooks($addedBooks)
    {
        $I = $this->tester;

        $I->click("//a[text()='All books']");
        foreach($addedBooks as $book => $data){
            $this->deleteBook($data["book_id"]);
        }
    }

    public function deleteBook($bookId)
    {
        $I = $this->tester;
        $I->click("//h1[text()='Book List']/following-sibling::li/a[@href='/catalog/book/{$bookId}']");
        $I->click("//a[text()='Delete Book']");
        $I->click("//button[text()='Delete']");
        $I->see('Book List');
    }
}