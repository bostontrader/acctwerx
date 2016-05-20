<?php
namespace App\Test\TestCase\Controller\Transactions;

use App\Controller\TransactionsController;
use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\TransactionsFixture;
use App\Test\TestCase\Controller\DMIntegrationTestCase;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;


class BasicCRUD extends DMIntegrationTestCase {

    public $fixtures = [
        'app.books',
        'app.distributions',
        'app.transactions'
    ];

    /** @var \Cake\ORM\Table */
    private $Transactions;

    /** @var \Cake\ORM\Table */
    private $Books;

    /** @var \App\Test\Fixture\TransactionsFixture */
    private $transactionsFixture;

    public function setUp() {
        parent::setUp();
        $this->Books = TableRegistry::get('Books');
        $this->Transactions = TableRegistry::get('Transactions');
        $this->transactionsFixture = new TransactionsFixture();
    }

    public function testGET_newform() {

        // 1. GET the url and parse the response.
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->get("/books/$book_id/transactions/newform");
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='TransactionsNewform']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;
        $this->assertEquals($unknownATagCnt,0);

        // 4. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='TransactionNewformForm']",$content_node);

        // 5. Now inspect the legend of the form.
        $this->assertContains($book['title'],$this->getTheOnlyOne($xpath,"//legend",$form_node)->textContent);

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

        // 6.3 Look for the hidden book_id input, and validate its contents.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @id='TransactionBookId' and @value='$book_id']",$form_node)->length,1);
        $unknownInputCnt--;

        // 6.4 Ensure that there's an input field for sort, of type text, and that it is empty
        $this->assertTrue($xpath->query("//input[@id='TransactionNote' and @type='text' and not(@value)]",$form_node)->length==1);
        $unknownInputCnt--;

        // 6.5 Check the 5 selects spawned for tran_datetime.
        $this->selectCheckerB($xpath,"//select[@name='tran_datetime[year]']",11,$expected_choice=null,$context_node=null);
        $this->selectCheckerB($xpath,"//select[@name='tran_datetime[month]']",12,$expected_choice=null,$context_node=null);
        $this->selectCheckerB($xpath,"//select[@name='tran_datetime[day]']",31,$expected_choice=null,$context_node=null);
        $this->selectCheckerB($xpath,"//select[@name='tran_datetime[hour]']",24,$expected_choice=null,$context_node=null);
        $this->selectCheckerB($xpath,"//select[@name='tran_datetime[minute]']",60,$expected_choice=null,$context_node=null);
        $unknownSelectCnt-=5;

        // 7. Have all the input and selects been accounted for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);
    }

    public function testPOST_add() {

        // 1. POST a suitable record to the url, observe redirection, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->transactionsFixture->newTransactionRecord;
        $urlBase='/books/'.FixtureConstants::bookTypical.'/transactions';
        $fromDbRecord=$this->genericPOSTAddProlog(
            null, // no login
            $urlBase, $fixtureRecord,
            $urlBase, $this->Transactions,
            true
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['book_id'],$fixtureRecord['book_id']);
        $this->assertEquals($fromDbRecord['note'],$fixtureRecord['note']);

        $d1=$fromDbRecord['tran_datetime'];
        $t=$fixtureRecord['tran_datetime'];
        $d2=new Time($t['year'].'-'.$t['month'].'-'.$t['day'].' '.$t['hour'].':'.$t['minute']);
        $this->assertTrue($d1->eq($d2));

        // 3. Can I see the TRANSACTION_SAVED message?
        $flash=$this->_controller->request->session()->read('Flash.flash');
        $this->assertEquals($flash[0]['message'],TransactionsController::TRANSACTION_SAVED);
    }

    // POST A JSON Transaction, fully armed with distributions, the add method.
    // Unfortunately, we can't yet properly test this here.
    //
    // Option A. If we use the ordinary post method, we find that the posted data
    // must be an array.  Can't just send a string of JSON.
    //
    // Option B. If we use the Network/Client object, the testing will work,
    // but it goes to the default db instead of the test db.
    //
    // Don't have time to figure this out now :-(
    //public function testPOST_addJSON() {

        //$fixtureRecord='{
            //"datetime": "2016-01-17",
            //"note": "JSON Test",
            //"distributions": [
                //{"drcr":1,"account_id":1,"currency_id":1,"amount":500.250},
                //{"drcr":-1,"account_id":2,"currency_id":2,"amount":25}
            //]
        //}';

        //$url='/books/'.FixtureConstants::bookTypical.'/transactions/add';
        //$this->post($url, $fixtureRecord);
        //$http=new Client();
        //$response = $http->post($url, $fixtureRecord);

        // Now retrieve the newly written record.
        //$query=new Query(ConnectionManager::get('test'),$table);
        //$fromDbRecord=$query->find('all')->contain('Distributions')->order(['id'=>'DESC'])->first();

        //return $fromDbRecord;

        // 2. Now validate that record.
        //$this->assertEquals($fromDbRecord['book_id'],$fixtureRecord['book_id']);
        //$this->assertEquals($fromDbRecord['note'],$fixtureRecord['note']);
        //$this->assertEquals($fromDbRecord['datetime'],$fixtureRecord['datetime']);
    //}

    //public function testDELETE() {
        //$this->deletePOST(
            //null, // no login
            //'/transactions/delete/',
            //FixtureConstants::transactionTypical, '/transactions', $this->Transactions
        //);
    //}

    public function testGET_editform() {

        // 1. Obtain the relevant records and verify their referential integrity.
        $transaction_id=FixtureConstants::transactionTypical;
        $transaction=$this->Transactions->get($transaction_id);
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->assertEquals($transaction['book_id'],$book['id']);

        // 2. GET the url and parse the response.
        $this->get("/books/$book_id/transactions/$transaction_id/editform");
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 3. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='TransactionsEditform']");

        // 4. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;
        $this->assertEquals($unknownATagCnt,0);

        // 5. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='TransactionEditformForm']",$content_node);

        // 6. Now inspect the legend of the form.
        $this->assertContains($book['title'],$this->getTheOnlyOne($xpath,"//legend",$form_node)->textContent);

        // 7. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 7.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt=$xpath->query("//select",$form_node)->length;
        $unknownInputCnt=$xpath->query("//input",$form_node)->length;

        // 7.2 Look for the hidden PUT input.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @name='_method' and @value='PUT']",$form_node)->length,1);
        $unknownInputCnt--;

        // 7.3 Ensure that there's an input field for note, of type text, that is correctly set
        //$this->assertTrue($xpath->query("//input[@id='AccountTitle' and @type='text' and @value='$account->note']",$form_node)->length==1);
        $this->assertTrue($xpath->query("//input[@id='TransactionNote' and @type='text' and @value='$transaction->note']",$form_node)->length==1);
        $unknownInputCnt--;

        // 7.4 Check the 5 selects spawned for tran_datetime.  But picking apart these selects is just too tedious
        // and time consuming.  If trouble over comes from this sector, nuke it then.
        //$t=$transaction->tran_datetime;
        //$this->selectCheckerB($xpath,"//select[@name='tran_datetime[year]']",11,['value'=>$t->year,'text'=>$t->year],$context_node=null);
        //$this->selectCheckerB($xpath,"//select[@name='tran_datetime[month]']",12,['value'=>$t->month,'text'=>$t->month],$context_node=null);
        //$this->selectCheckerB($xpath,"//select[@name='tran_datetime[day]']",31,['value'=>$t->day,'text'=>$t->day],$context_node=null);
        //$this->selectCheckerB($xpath,"//select[@name='tran_datetime[hour]']",24,['value'=>$t->hour,'text'=>$t->hour],$context_node=null);
        //$this->selectCheckerB($xpath,"//select[@name='tran_datetime[minute]']",60,['value'=>$t->minute,'text'=>$t->minute],$context_node=null);
        $unknownSelectCnt-=5;

        // 8. Have all the input and selects been accounted for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);
    }

    public function testPUT_edit() {

        // 1. Obtain the relevant records and verify their referential integrity.
        $transaction_id=FixtureConstants::transactionTypical;
        $transactionNew=$this->transactionsFixture->newTransactionRecord;
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->assertEquals($transactionNew['book_id'],$book['id']);

        // 2. POST a suitable record to the url and observe the redirect.
        $baseUrl="/books/$book_id/transactions";
        $this->put("$baseUrl/$transaction_id", $transactionNew);
        $this->assertResponseCode(302);
        $this->assertRedirect( $baseUrl );

        // 3. Now retrieve that 1 record and validate it.
        $fromDbRecord=$this->Transactions->get($transaction_id);
        $this->assertEquals($fromDbRecord['book_id'],$transactionNew['book_id']);
        $this->assertEquals($fromDbRecord['note'],$transactionNew['note']);

        $d1=$fromDbRecord->tran_datetime;
        $t=$transactionNew['tran_datetime'];
        $d2=new Time($t['year'].'-'.$t['month'].'-'.$t['day'].' '.$t['hour'].':'.$t['minute']);
        $this->assertTrue($d1->eq($d2));
    }

    public function testGET_index() {

        // 1. Submit submit request, examine response, observe no redirect, and parse the response.
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->get('/books/'.$book_id.'/transactions');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='TransactionsIndex']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;

        // 4. Look for the create new transaction link
        $this->getTheOnlyOne($xpath,"//a[@id='TransactionNewform']",$content_node);
        $unknownATagCnt--;

        // 5. Ensure that there is a suitably named table to display the results.
        $table_node=$this->getTheOnlyOne($xpath,"//table[@id='TransactionsTable']",$content_node);

        // 6. Now inspect the caption of the table.
        $this->assertContains($book['title'],$this->getTheOnlyOne($xpath,"caption",$table_node)->textContent);

        // 7. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $column_header_nodes=$xpath->query("thead/tr/th",$table_node);
        $this->assertEquals($column_header_nodes->length,3); // no other columns

        $this->getTheOnlyOne($xpath,"thead/tr/th[1][@id='note']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[2][@id='tran_datetime']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[3][@id='actions']",$table_node);

        // 8. Ensure that the tbody section has the correct quantity of rows.
        $dbRecords=$this->Transactions->find()
            ->where(['book_id'=>$book_id])
            ->order(['tran_datetime'=>'desc']);
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

            $this->assertEquals($fixtureRecord['Transactions__note'],  $column_nodes->item(0)->textContent);
            //$this->assertEquals($fixtureRecord['Transactions__datetime'],  $column_nodes->item(1)->textContent);

            // 9.1 Now examine the action links
            $action_nodes=$xpath->query("a",$column_nodes->item(2));
            $this->assertTrue($action_nodes->length==2);

            $this->getTheOnlyOne($xpath,"a[@name='TransactionView']",$column_nodes->item(2));
            $unknownATagCnt--;

            $this->getTheOnlyOne($xpath,"a[@name='TransactionEditform']",$column_nodes->item(2));
            $unknownATagCnt--;

            // 9.9 No other columns
            $this->assertEquals($column_nodes->length,$column_header_nodes->length);
        }

        // 10. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);
    }

    public function testGET_view() {

        // 1. Obtain the relevant records and verify their referential integrity.
        $transaction_id=FixtureConstants::transactionTypical;
        $transaction=$this->Transactions->get($transaction_id);
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->assertEquals($transaction['book_id'],$book['id']);

        // 2. Submit request, examine response, observe no redirect, and parse the response.
        $this->get('/books/'.$book_id.'/transactions/'.$transaction_id);
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 3. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='TransactionsView']");

        // 4. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;

        // 4.1 Look for the transaction distributions link
        $this->getTheOnlyOne($xpath,"//a[@id='TransactionDistributions']",$content_node);
        $unknownATagCnt--;

        // 4.2 Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);

        // 5. Ensure that there is a suitably named table to display the results.
        $table_node=$this->getTheOnlyOne($xpath,"//table[@id='TransactionViewTable']",$content_node);

        // 5.1 Inspect the caption of the table.
        $this->assertContains("$transaction_id",$this->getTheOnlyOne($xpath,"caption",$table_node)->textContent);

        // 6. Now inspect the fields in the table.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt=$xpath->query("//tr",$table_node)->length;

        // 6.1 book_title
        $this->getTheOnlyOne($xpath,"//tr[1][@id='book_title']/td[text()='$book->title']",$table_node);
        $unknownRowCnt--;

        // 6.2 note
        $this->getTheOnlyOne($xpath,"//tr[2][@id='note']/td[text()='$transaction->note']",$table_node);
        $unknownRowCnt--;

        // 6.3 tran_datetime
        $unknownRowCnt--;

        // 6.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);
    }
}
