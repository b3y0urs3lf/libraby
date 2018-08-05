<?php

namespace Page\BigBank;

class LibraryAuthors extends BasePage
{
    public function checkAndAddAuthors($books)
    {
        $addedAuthors = [];
        $I = $this->tester;
        $I->click("//a[text()='All authors']");
        foreach($books as $book => $data){
            $authorsXpath = "//a[text()='{$data["author"]["family_name"]}, {$data["author"]["first_name"]} ']";
            if ((count($I->grabMultiple($authorsXpath))) === 0){

                //adding new genre
                $genreUrl = $this->addAuthor($data["author"]);
                $addedGenres[] = [
                    "author_id" => $genreUrl[3],
                ];
                $I->click("//a[text()='All authors']");
            }
        }

        //return array of authors that you added
        return $addedAuthors;
    }

    public function addAuthor($author)
    {
        $I = $this->tester;
        $I->click("//a[text()='Create new author']");
        foreach($author as $key => $value){

            //check if value if present, because not all of them is required
            if(!empty($key)){

                //check if need to fill date picker field
                if(strpos($key, 'date_of_') !== false){
                    $I->pressKey("//input[@id='{$key}']", $value );
                }else{
                    $I->fillField("//input[@id='{$key}']", $value);
                }
            }
        }
        $I->click("//button[text()='Submit']");

        //verify that author is added
        $I->seeElement("//h1[text()='Author: {$author["family_name"]}, {$author["first_name"]}']");

        return explode("/",$I->grabFromCurrentUrl());
    }

    public function deleteAddedAuthors($addedAuthors)
    {
        $I = $this->tester;
        $I->click("//a[text()='All authors']");
        foreach($addedAuthors as $author => $data){
            $this->deleteAuthor($data["author_id"]);
        }
    }

    public function deleteAllAuthors()
    {
        $I = $this->tester;
        $I->click("//a[text()='All authors']");
        while ($I->seePageHasElement("(//h1[text()='Author List']/following-sibling::li/a)[1]")){
            $authorUrl = $I->grabAttributeFrom("(//h1[text()='Author List']/following-sibling::li/a)[1]", 'href');
            $this->deleteAuthor(explode("/",$authorUrl)[5]);
        }
    }

    public function deleteAuthor($authorId)
    {
        $I = $this->tester;
        $I->click("//h1[text()='Author List']/following-sibling::li/a[@href='/catalog/author/{$authorId}']");
        $I->click("//a[text()='Delete author']");
        $I->click("//button[text()='Delete']");

        //verify that you are back on Author list
        $I->see('Author List');
    }
}