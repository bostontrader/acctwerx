<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\TransactionsFixture;
use Cake\ORM\TableRegistry;

class TransactionsControllerTest extends DMIntegrationTestCase {

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
        parent::setup();
        $this->Books = TableRegistry::get('Books');
        $this->Transactions = TableRegistry::get('Transactions');
        $this->transactionsFixture = new TransactionsFixture();
    }

    public function testGET_add() {

        // 1. GET the url and parse the response.
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->get("/books/$book_id/transactions/add");
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='TransactionsAdd']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;
        $this->assertEquals($unknownATagCnt,0);

        // 4. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='TransactionAddForm']",$content_node);

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

        // 6.5 Ensure that there's an input field for sort, of type text, and that it is empty
        $this->assertTrue($xpath->query("//input[@id='TransactionNote' and @type='text' and not(@value)]",$form_node)->length==1);
        $unknownInputCnt--;

        // 6.4 Ensure that there's a select field for category_id, that it has the correct quantity of available choices,
        // and that it has no selection.
        //$this->selectChecker($xpath,'AccountCategoryId','categories',null,$form_node);
        //$unknownSelectCnt--;

        // 4.5 Ensure that there are suitable select fields for tran_datetime. Don't
        // worry about checking their default values or available choices because that's
        // Cake's responsibility.
        //($this->inputCheckerDatetime($form,'input#TransactionTranDatetime')) $unknownSelectCnt--;


        $this->inputCheckerDatetime($form_node,'TransactionTranDatetime');
        //) $unknownSelectCnt--;
        // 1. Get the one and only one select control.
        $context_node=$form_node;
        $select_name='tran_datetime';
        $expected_choice=['value'=>'2016','text'=>'2016']; // default value
        $select_node=$this->getTheOnlyOne($xpath,"//select[@name='$select_name[year]']",$context_node);

        // 2. Make sure it has the correct number of choices, including an
        // extra for the none-selected choice.
        //$record_cnt = $this->viewVariable($vv_name)->count();
        $record_cnt=11;
        $this->assertEquals($xpath->query("//option",$select_node)->length,$record_cnt+1);

        // 3. Verify the correct choice.
        if(is_null($expected_choice)) {
            // Is it worth your while to determine today's year and ensure that's the selected choice?
            // Make sure that none of the choices are selected.
            //$this->assertTrue($xpath->query("//option[selected]",$select_node)->length==0);
        } else {
            // This specific choice should be selected.
            $value=$expected_choice['value']; $text=$expected_choice['text'];
            $nodes=$xpath->query(
                "//option[@selected='selected' and @value='$value' and text()='$text']",$select_node);
            $this->assertTrue($nodes->length==1);
        }


        // 6.6 Ensure that there's an input field for title, of type text, and that it is empty
        //$this->assertTrue($xpath->query("//input[@id='AccountTitle' and @type='text' and not(@value)]",$form_node)->length==1);
        //$unknownInputCnt--;

        // 7. Have all the input and selects been accounted for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);
    }

    public function testPOST_add() {

        // 1. POST a suitable record to the url, observe redirection, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->transactionsFixture->newTransactionRecord;
        $fromDbRecord=$this->genericPOSTAddProlog(
            null, // no login
            '/books/'.FixtureConstants::bookTypical.'/transactions/add', $fixtureRecord,
            '/books/'.FixtureConstants::bookTypical.'/transactions', $this->Transactions
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['book_id'],$fixtureRecord['book_id']);
        $this->assertEquals($fromDbRecord['note'],$fixtureRecord['note']);
        $this->assertEquals($fromDbRecord['datetime'],$fixtureRecord['datetime']);
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
    public function testPOST_addJSON() {

        $fixtureRecord='{
            "datetime": "2016-01-17",
            "note": "JSON Test",
            "distributions": [
                {"drcr":1,"account_id":1,"currency_id":1,"amount":500.250},
                {"drcr":-1,"account_id":2,"currency_id":2,"amount":25}
            ]
        }';

        $url='/books/'.FixtureConstants::bookTypical.'/transactions/add';
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
    }

    //public function testDELETE() {
        //$this->deletePOST(
            //null, // no login
            //'/transactions/delete/',
            //FixtureConstants::transactionTypical, '/transactions', $this->Transactions
        //);
    //}

    public function testGET_edit() {

        /* @var \simple_html_dom_node $form */
        /* @var \simple_html_dom_node $html */
        /* @var \simple_html_dom_node $legend */

        // 1. Obtain the relevant records and verify their referential integrity.
        $transaction_id=FixtureConstants::transactionTypical;
        $transaction=$this->Transactions->get($transaction_id);
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->assertEquals($transaction['book_id'],$book['id']);

        // 2. GET the url and parse the response.
        $this->get('/books/'.$book['id'].'/transactions/edit/' . $transaction_id);
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#TransactionEditForm',0);
        $this->assertNotNull($form);

        // 4. Now inspect the legend of the form.
        $legend = $form->find('legend',0);
        $this->assertContains($book['title'],$legend->innertext());

        // 5. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 5.1 These are counts of the select and input fields on the form.  They
        // are presently untransactioned for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 5.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // 5.3 Ensure that there's an input field for note, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#TransactionNote', $transaction['note'])) $unknownInputCnt--;

        // 5.4 Ensure that there's an input field for datetime, of type text, that is correctly set
        if($this->inputCheckerA($form,'input#TransactionDatetime', $transaction['datetime'])) $unknownInputCnt--;

        // 6. Have all the input, select, and Atags been transactioned for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#TransactionsEdit');
    }

    public function testPOST_edit() {

        // 1. Obtain the relevant records and verify their referential integrity.
        $transaction_id=FixtureConstants::transactionTypical;
        $transactionNew=$this->transactionsFixture->newTransactionRecord;
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->assertEquals($transactionNew['book_id'],$book['id']);

        // 2. POST a suitable record to the url, observe the redirect, and parse the response.
        $shortUrl='/books/'.$book_id.'/transactions';
        $this->put($shortUrl.'/'.$transaction_id, $transactionNew);
        $this->assertResponseCode(302);
        $this->assertRedirect( $shortUrl );

        // 3. Now retrieve that 1 record and validate it.
        $fromDbRecord=$this->Transactions->get($transaction_id);
        $this->assertEquals($fromDbRecord['book_id'],$transactionNew['book_id']);
        $this->assertEquals($fromDbRecord['note'],$transactionNew['note']);
        $this->assertEquals($fromDbRecord['datetime'],$transactionNew['datetime']);
    }

    public function testGET_index() {

        /* @var \simple_html_dom_node $content */
        /* @var \simple_html_dom_node $header */
        /* @var \simple_html_dom_node $htmlRow */
        /* @var \simple_html_dom_node $table */
        /* @var \simple_html_dom_node $tbody */
        /* @var \simple_html_dom_node $td */
        /* @var \simple_html_dom_node $thead */

        // 1. Submit submit request, examine response, observe no redirect, and parse the response.
        $book_id=FixtureConstants::bookTypical;
        $this->get('/books/'.$book_id.'/transactions');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $html=str_get_html($this->_response->body());

        // 2. Now inspect the legend of the form.
        $header=$html->find('header',0);
        $book=$this->Books->get($book_id);
        $this->assertContains($book['title'],$header->innertext());

        // 3. Get a the count of all <A> tags that are presently untransactioned for.
        $content = $html->find('div#TransactionsIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new transaction link
        $this->assertEquals(1, count($html->find('a#TransactionAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $table = $html->find('table#TransactionsTable',0);
        $this->assertNotNull($table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $table->find('thead',0);
        $thead_ths = $thead->find('tr th');
        $this->assertEquals($thead_ths[0]->id, 'note');
        $this->assertEquals($thead_ths[1]->id, 'datetime');
        $this->assertEquals($thead_ths[2]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,3); // no other columns

        // 7. Ensure that the tbody section has the correct quantity of rows.
        $dbRecords=$this->Transactions->find()
            ->where(['book_id'=>$book_id])
            ->order(['datetime']);
        $tbody = $table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), $dbRecords->count());

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($dbRecords->execute()->fetchAll('assoc')));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');

            // 9.0 datetime
            $this->assertEquals($fixtureRecord['Transactions__note'],  $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['Transactions__datetime'],  $htmlColumns[1]->plaintext);

            // 9.1 Now examine the action links
            $td = $htmlColumns[2];
            $actionLinks = $td->find('a');
            $this->assertEquals('TransactionView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('TransactionEdit', $actionLinks[1]->name);
            $unknownATag--;
            //$this->assertEquals('TransactionDelete', $actionLinks[2]->name);
            //$unknownATag--;

            // 9.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 10. Ensure that all the <A> tags have been transactioned for
        $this->assertEquals(0, $unknownATag);
    }

    public function testGET_view() {

        /* @var \simple_html_dom_node $content */
        /* @var \simple_html_dom_node $field */
        /* @var \simple_html_dom_node $table */

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
        $html=str_get_html($this->_response->body());

        // 3. Verify the <A> tags
        // 3.1 Get the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#TransactionsView',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 3.2 Look for specific tags
        $this->assertEquals(1, count($html->find('a#TransactionDistributions')));
        $unknownATag--;

        // 3.3. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);

        // 4.  Look for the table that contains the view fields.
        $table = $html->find('table#TransactionViewTable',0);
        $this->assertNotNull($table);

        // 5. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently untransactioned for.
        $unknownRowCnt = count($table->find('tr'));

        // 5.1 book_title
        $field = $table->find('tr#book_title td',0);
        $this->assertEquals($book['title'], $field->plaintext);
        $unknownRowCnt--;

        // 5.2 note
        $field = $table->find('tr#note td',0);
        $this->assertEquals($transaction['note'], $field->plaintext);
        $unknownRowCnt--;

        // 5.3 datetime
        $field = $table->find('tr#datetime td',0);
        $this->assertEquals($transaction['datetime'], $field->plaintext);
        $unknownRowCnt--;

        // 5.9 Have all the rows been transactioned for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);
    }
}
