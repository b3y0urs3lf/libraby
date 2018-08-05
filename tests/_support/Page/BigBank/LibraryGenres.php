<?php

namespace Page\BigBank;

class LibraryGenres extends BasePage
{
    public function checkAndAddGenres($books)
    {
        $addedGenres = [];
        $I = $this->tester;
        $I->click("//a[text()='All genres']");
        foreach($books as $book => $data){

            //check if genre exist
            if (count($I->grabMultiple("//a[text()='{$data["genre"]}']")) === 0) {

                //adding new genre
                $genreUrl = $this->addGenre($data["genre"]);
                $addedGenres[] = [
                    "genre_id" => $genreUrl[3],
                ];
                $I->click("//a[text()='All genres']");
            }
        }

        //return added genres
        return $addedGenres;
    }

    public function addGenre($genre)
    {
        $I = $this->tester;

        //add new genre
        $I->click("//a[text()='Create new genre']");
        $I->fillField("//label[text()='Genre:']/following-sibling::input", $genre);
        $I->click("//button[text()='Submit']");

        //verify that genre is added
        $I->seeElement("//h1[text()='Genre: {$genre}']");

        return explode("/",$I->grabFromCurrentUrl());
    }

    public function deleteAddedGenres($addedGenres)
    {
        $I = $this->tester;
        $I->click("//a[text()='All genres']");
        foreach($addedGenres as $genre => $data){
            $this->deleteGenre($data["genre_id"]);
        }
    }

    public function deleteAllGenres()
    {
        $I = $this->tester;
        $I->click("//a[text()='All genres']");
        while ($I->seePageHasElement("(//h1[text()='Genre List']/following-sibling::li/a)[1]")){
            $genreUrl = $I->grabAttributeFrom("(//h1[text()='Genre List']/following-sibling::li/a)[1]", 'href');
            $this->deleteGenre(explode("/",$genreUrl)[5]);
        }
    }

    public function deleteGenre($genreId)
    {
        $I = $this->tester;
        $I->click("//h1[text()='Genre List']/following-sibling::li/a[@href='/catalog/genre/{$genreId}']");
        $I->click("//a[text()='Delete genre']");
        $I->click("//button[text()='Delete']");

        //verify that you are back on Genre list
        $I->see('Genre List');
    }
}