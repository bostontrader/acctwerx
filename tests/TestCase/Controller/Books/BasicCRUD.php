<?php
namespace App\Test\TestCase\Controller;

use App\Controller\BooksController;
use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\BooksFixture;
use Cake\ORM\TableRegistry;

class BasicCRUD extends DMIntegrationTestCase {

    public $fixtures = [
        'app.books'
    ];

    /** @var \Cake\ORM\Table */
    private $Books;

    /** @var \App\Test\Fixture\BooksFixture */
    private $booksFixture;

    public function setUp() {
        parent::setUp();
        $this->Books = TableRegistry::get('Books');
        $this->booksFixture = new BooksFixture();
    }

    public function testGET_newform() {

        // 1. GET the url and parse the response.
        $this->get('/books/newform');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='BooksNewform']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;
        $this->assertEquals($unknownATagCnt,0);

        // 4. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='BookNewformForm']",$content_node);

        // 5. Now inspect the legend of the form.
        $this->assertContains("Add Book",$this->getTheOnlyOne($xpath,"//legend",$form_node)->textContent);

        // 6. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 6.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt=$xpath->query("//select",$form_node)->length;
        $unknownInputCnt=$xpath->query("//input",$form_node)->length;

        // 6.2 Look for the hidden POST input.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @name='_method' and @value='POST']",$form_node)->length,1);
        $unknownInputCnt--;

        // 6.3 Ensure that there's an input field for title, of type text, and that it is empty
        $this->assertTrue($xpath->query("//input[@id='BookTitle' and @type='text' and not(@value)]",$form_node)->length==1);
        $unknownInputCnt--;

        // 7. Have all the input and selects been accounted for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);
    }

    public function testPOST_add() {

        // 1. POST a suitable record to the url, observe redirection, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->booksFixture->newBookRecord;
        $fromDbRecord=$this->genericPOSTAddProlog(
            null, // no login
            '/books', $fixtureRecord,
            '/books', $this->Books
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['title'],$fixtureRecord['title']);

        // 3. Can I see the BOOK_SAVED message?
        $flash=$this->_controller->request->session()->read('Flash.flash');
        $this->assertEquals($flash[0]['message'],BooksController::BOOK_SAVED);
    }

    //public function testDELETE() {
        //$this->deletePOST(
            //null, // no login
            //'/books/delete/',
            //FixtureConstants::bookTypical, '/books', $this->Books
        //);
    //}

    public function testGET_editform() {

        // 1. Obtain a record to edit, GET the url, and parse the response.
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->get("/books/$book_id/editform");
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='BooksEditform']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;
        $this->assertEquals($unknownATagCnt,0);

        // 4. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='BookEditformForm']",$content_node);

        // 5. Now inspect the legend of the form.
        $this->assertContains("Edit Book",$this->getTheOnlyOne($xpath,"//legend",$form_node)->textContent);

        // 6. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 6.1 These are counts of the select and input fields on the form.  They
        // are presently unBooked for.
        $unknownSelectCnt=$xpath->query("//select",$form_node)->length;
        $unknownInputCnt=$xpath->query("//input",$form_node)->length;

        // 6.2 Look for the hidden PUT input.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @name='_method' and @value='PUT']",$form_node)->length,1);
        $unknownInputCnt--;

        // 6.3 Ensure that there's an input field for title, of type text, that is correctly set
        $this->assertTrue($xpath->query("//input[@id='BookTitle' and @type='text' and @value='$book->title']",$form_node)->length==1);
        $unknownInputCnt--;

        // 7. Have all the input and selects been Booked for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);
    }

    public function testPUT_edit() {

        // 1. Obtain the relevant records.
        $book_id=FixtureConstants::bookTypical;
        $bookNew=$this->booksFixture->newBookRecord;

        // 2. PUT a suitable record to the url and observe the redirect.
        $baseUrl="/books";
        $this->put("$baseUrl/$book_id", $bookNew);
        $this->assertResponseCode(302);
        $this->assertRedirect($baseUrl);

        // 3. Now retrieve that 1 record and validate it.
        $fromDbRecord=$this->Books->get($book_id);
        $this->assertEquals($fromDbRecord['title'],$bookNew['title']);
    }


    public function testGET_index() {

        // 1. Submit request, examine response, observe no redirect, and parse the response.
        $this->get("/books");
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='BooksIndex']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;

        // 4. Look for the create new book link
        $this->getTheOnlyOne($xpath,"//a[@id='BookNewform']",$content_node);
        $unknownATagCnt--;

        // 5. Ensure that there is a suitably named table to display the results.
        $table_node=$this->getTheOnlyOne($xpath,"//table[@id='BooksTable']",$content_node);

        // 6. Now inspect the caption of the table.
        $this->assertContains("Books",$this->getTheOnlyOne($xpath,"caption",$table_node)->textContent);

        // 7. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $column_header_nodes=$xpath->query("thead/tr/th",$table_node);
        $this->assertEquals($column_header_nodes->length,2); // no other columns

        $this->getTheOnlyOne($xpath,"thead/tr/th[1][@id='title']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[2][@id='actions']",$table_node);

        // 8. Ensure that the tbody section has the correct quantity of rows.
        $dbRecords=$this->Books->find()
            ->order(['id']);
        $tbody_nodes=$xpath->query("tbody/tr",$table_node);
        $this->assertTrue($tbody_nodes->length==$dbRecords->count());

        // 9. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($dbRecords->execute()->fetchAll('assoc')));
        $iterator->attachIterator(new \ArrayIterator(iterator_to_array($tbody_nodes)));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $row_node = $values[1];
            $column_nodes=$xpath->query("td",$row_node);

            $this->assertEquals($fixtureRecord['Books__title'],  $column_nodes->item(0)->textContent);

            // 9.1 Now examine the action links
            $action_nodes=$xpath->query("a",$column_nodes->item(1));
            $this->assertTrue($action_nodes->length==2);

            $this->getTheOnlyOne($xpath,"a[@name='BookView']",$column_nodes->item(1));
            $unknownATagCnt--;

            $this->getTheOnlyOne($xpath,"a[@name='BookEdit']",$column_nodes->item(1));
            $unknownATagCnt--;

            // 9.9 No other colu mns
            $this->assertEquals($column_nodes->length,$column_header_nodes->length);
        }

        // 10. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);
    }

    public function testGET_view() {

        // 1. Obtain the relevant records.
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);

        // 2. Submit request, examine response, observe no redirect, and parse the response.
        $this->get("/books/$book_id");
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 3. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='BooksView']");

        // 4. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;

        // 4.1 Look for the accounts link
        $this->getTheOnlyOne($xpath,"//a[@id='BookAccounts']",$content_node);
        $unknownATagCnt--;

        // 4.2 Look for the transactions link
        $this->getTheOnlyOne($xpath,"//a[@id='BookTransactions']",$content_node);
        $unknownATagCnt--;

        // 4.3 Look for the balance sheet link
        $this->getTheOnlyOne($xpath,"//a[@id='BookBalanceSheet']",$content_node);
        $unknownATagCnt--;

        // 4.4 Look for the income statement link
        $this->getTheOnlyOne($xpath,"//a[@id='BookIncomeStatement']",$content_node);
        $unknownATagCnt--;

        // 4.5 Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);

        // 5. Ensure that there is a suitably named table to display the results.
        $table_node=$this->getTheOnlyOne($xpath,"//table[@id='BookViewTable']",$content_node);

        // 5.1 Inspect the caption of the table.
        $this->assertContains("$book_id",$this->getTheOnlyOne($xpath,"caption",$table_node)->textContent);

        // 6. Now inspect the fields in the table.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt=$xpath->query("//tr",$table_node)->length;

        // 6.1 title
        $this->getTheOnlyOne($xpath,"//tr[1][@id='title']/td[text()='$book->title']",$table_node);
        $unknownRowCnt--;

        // 6.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);
    }
}
