<?php
namespace App\Test\TestCase\Controller;

//use App\Test\Fixture\AccountsFixture;
//use App\Test\Fixture\CurrenciesFixture;
use App\Test\Fixture\DistributionsFixture;
use App\Test\Fixture\FixtureConstants;
use Cake\ORM\TableRegistry;

class DistributionsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        //'app.accounts',
        //'app.books',
        //'app.categories',
        //'app.currencies',
        'app.distributions',
        '//app.transactions'
    ];

    /** @var \Cake\ORM\Table */
    private $Books;

    /** @var \Cake\ORM\Table */
    private $Distributions;

    /** @var \Cake\ORM\Table */
    private $Transactions;

    /** @var \App\Test\Fixture\AccountsFixture */
    //private $accountsFixture;

    /** @var \App\Test\Fixture\CurrenciesFixture */
    //private $currenciesFixture;

    /** @var \App\Test\Fixture\DistributionsFixture */
    private $distributionsFixture;

    public function setUp() {
        parent::setUp();
        $this->Books = TableRegistry::get('Books');
        $this->Distributions = TableRegistry::get('Distributions');
        $this->Transactions = TableRegistry::get('Transactions');
        $this->distributionsFixture = new DistributionsFixture();
        //$this->accountsFixture = new accountsFixture();
        //$this->currenciesFixture = new currenciesFixture();
    }

    public function testGET_add() {

        // 1. GET the url and parse the response.
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $transaction_id=FixtureConstants::transactionTypical;
        $this->get('/books/'.$book_id.'/transactions/'.$transaction_id.'/distributions/add');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='DistributionsAdd']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;
        $this->assertEquals($unknownATagCnt,0);

        // 4. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='DistributionAddForm']",$content_node);
        // 2. Ensure that the correct form exists
        //$form = $html->find('form#DistributionAddForm',0);
        //$this->assertNotNull($form);
        // 5. Now inspect the legend of the form.
        $this->assertContains($book['title'],$this->getTheOnlyOne($xpath,"//legend",$form_node)->textContent);
        // 3. Now inspect the legend of the form.
        //$legend = $form->find('legend',0);
        //$transaction=$this->Transactions->get($transaction_id);
        //$this->assertContains($transaction['title'],$legend->innertext());

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
        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently undistributioned for.
        //$unknownSelectCnt = count($form->find('select'));
        //$unknownInputCnt = count($form->find('input'));

        // 6.2 Look for the hidden POST input.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @name='_method' and @value='POST']",$form_node)->length,1);
        $unknownInputCnt--;

        // 6.3 Look for the hidden book_id input, and validate its contents.
        //$this->assertEquals($xpath->query("//input[@type='hidden' and @id='AccountBookId' and @value='$book_id']",$form_node)->length,1);
        $this->assertEquals($xpath->query("//input[@type='hidden' and @id='DistributionTransactionBookId' and @value='$transaction_id']",$form_node)->length,1);
        //$unknownInputCnt--;
        // 4.3 Look for the hidden transaction_id input, and validate its contents.
        //if($this->lookForHiddenInput($form,'transaction_id',$transaction_id)) $unknownInputCnt--;

        // 6.4 Ensure that there's a select field for category_id, that it has the correct quantity of available choices,
        // and that it has no selection.
        //$this->selectCheckerA($xpath,'AccountCategoryId','categories',null,$form_node);
        //$unknownSelectCnt--;

        // 6.5 Ensure that there's an input field for sort, of type text, and that it is empty
        //$this->assertTrue($xpath->query("//input[@id='AccountSort' and @type='text' and not(@value)]",$form_node)->length==1);
        //$unknownInputCnt--;

        // 6.6 Ensure that there's an input field for title, of type text, and that it is empty
        //$this->assertTrue($xpath->query("//input[@id='AccountTitle' and @type='text' and not(@value)]",$form_node)->length==1);
        //$unknownInputCnt--;

        // 7. Have all the input and selects been accounted for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);



        // 4.4 Ensure that there's a select field for account_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'DistributionAccountId', 'accounts')) $unknownSelectCnt--;

        // 4.5 There should be a radio button for dr/cr, but let's skip that for now.
        // But it does use one hidden input and one input for each of the two choices.
        // So that's 3 of the inputs we're looking for.
        $unknownInputCnt-=3;

        // 4.6 Ensure that there's an input field for amount, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#DistributionAmount')) $unknownInputCnt--;

        // 4.7 Ensure that there's a select field for currency_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'DistributionCurrencyId', 'currencies')) $unknownSelectCnt--;

        // 5. Have all the input, select, and Atags been distributioned for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#DistributionsAdd');
    }

    public function testPOST_add() {

        // 1. POST a suitable record to the url, observe redirection, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->distributionsFixture->newDistributionRecord;
        $urlBase='/books/'.FixtureConstants::bookTypical.'/transactions/'.FixtureConstants::transactionTypical.'/distributions';
        $fromDbRecord=$this->genericPOSTAddProlog(
            null, // no login
            $urlBase.'/add', $fixtureRecord,
            $urlBase, $this->Distributions
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['transaction_id'],$fixtureRecord['transaction_id']);
        $this->assertEquals($fromDbRecord['account_id'],$fixtureRecord['account_id']);
        $this->assertEquals($fromDbRecord['amount'],$fixtureRecord['amount']);
        $this->assertEquals($fromDbRecord['currency_id'],$fixtureRecord['currency_id']);
    }

    //public function testDELETE() {
    //$this->deletePOST(
    //null, // no login
    //'/distributions/delete/',
    //FixtureConstants::distributionTypical, '/distributions', $this->Distributions
    //);
    //}

    public function testGET_edit() {

        /* @var \simple_html_dom_node $form */
        /* @var \simple_html_dom_node $html */
        /* @var \simple_html_dom_node $legend */

        // 1. Obtain the relevant records and verify their referential integrity.
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $transaction_id=FixtureConstants::transactionTypical;
        $transaction=$this->Transactions->get($transaction_id);
        $distribution_id=FixtureConstants::distributionTypical;
        $distribution=$this->Distributions->get($distribution_id);
        $this->assertEquals($distribution['transaction_id'],$transaction['id']);
        $this->assertEquals($transaction['book_id'],$book['id']);

        // 2. GET the url and parse the response.
        $this->get('/books/'.$book_id.'/transactions/'.$transaction_id.'/distributions/edit/' . $distribution_id);
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#DistributionEditForm',0);
        $this->assertNotNull($form);

        // 4. Now inspect the legend of the form.
        //$legend = $form->find('legend',0);
        //$this->assertContains($transaction['title'],$legend->innertext());

        // 5. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 5.1 These are counts of the select and input fields on the form.  They
        // are presently undistributioned for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 5.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // Don't worry about the transaction_id because we cannot change it here.

        // 5.3 Ensure that there's a select field for account_id and that it is correctly set
        // $account_id / $account_id['title'], from fixture
        $account_id=$distribution['account_id'];
        $account = $this->accountsFixture->get($account_id);
        if($this->inputCheckerB($form,'select#DistributionAccountId option[selected]',$account_id,$account['title']))
            $unknownSelectCnt--;

        // 5.4 There should be a radio button for dr/cr, but let's skip that for now.
        // But it does use one hidden input and one input for each of the two choices.
        // So that's 3 of the inputs we're looking for.
        $unknownInputCnt-=3;

        // 5.5 Ensure that there's an input field for amount, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#DistributionAmount', $distribution['amount'])) $unknownInputCnt--;

        // 5.6 Ensure that there's a select field for currency_id and that it is correctly set
        // $currency_id / $currency_id['title'], from fixture
        $currency_id=$distribution['currency_id'];
        $currency = $this->currenciesFixture->get($currency_id);
        if($this->inputCheckerB($form,'select#DistributionCurrencyId option[selected]',$currency_id,$currency['title']))
            $unknownSelectCnt--;

        // 6. Have all the input, select, and Atags been distributioned for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#DistributionsEdit');
    }

    public function testPOST_edit() {

        // 1. Obtain the relevant records and verify their referential integrity.
        $book_id=FixtureConstants::bookTypical;
        $distribution_id=FixtureConstants::distributionTypical;
        $distributionNew=$this->distributionsFixture->newDistributionRecord;
        $transaction_id=FixtureConstants::transactionTypical;
        //$transaction=$this->Transactions->get($transaction_id);
        //$this->assertEquals($distributionNew['transaction_id'],$transaction['id']);

        // 2. POST a suitable record to the url, observe the redirect, and parse the response.
        $urlBase='/books/'.$book_id.'/transactions/'.$transaction_id.'/distributions';
        $this->put($urlBase.'/'.$distribution_id, $distributionNew);
        $this->assertResponseCode(302);
        $this->assertRedirect( $urlBase );

        // 3. Now retrieve that 1 record and validate it.
        $fromDbRecord=$this->Distributions->get($distribution_id);
        $this->assertEquals($fromDbRecord['transaction_id'],$distributionNew['transaction_id']);
        $this->assertEquals($fromDbRecord['account_id'],$distributionNew['account_id']);
        $this->assertEquals($fromDbRecord['amount'],$distributionNew['amount']);
        $this->assertEquals($fromDbRecord['currency_id'],$distributionNew['currency_id']);
    }

    // GET /books/:book_id/transactions/:transaction_id/distributions
    public function testGET_index() {

        /* @var \simple_html_dom_node $content */
        /* @var \simple_html_dom_node $header */
        /* @var \simple_html_dom_node $htmlRow */
        /* @var \simple_html_dom_node $table */
        /* @var \simple_html_dom_node $tbody */
        /* @var \simple_html_dom_node $td */
        /* @var \simple_html_dom_node $thead */

        // 1. Submit submit request, examine response, observe no redirect, and parse the response.
        $transaction_id=FixtureConstants::transactionTypical;
        $this->get('/books/'.FixtureConstants::bookTypical.'/transactions/'.$transaction_id.'/distributions');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $html=str_get_html($this->_response->body());

        // 2. Now inspect the header of the form.
        //$header=$html->find('header',0);
        //$transaction=$this->Transactions->get($transaction_id);
        //$this->assertContains($transaction['title'],$header->innertext());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#DistributionsIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new distribution link
        $this->assertEquals(1, count($html->find('a#DistributionAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $table = $html->find('table#DistributionsTable',0);
        $this->assertNotNull($table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $table->find('thead',0);
        $thead_ths = $thead->find('tr th');
        $this->assertEquals($thead_ths[0]->id, 'drcr');
        $this->assertEquals($thead_ths[1]->id, 'category');
        $this->assertEquals($thead_ths[2]->id, 'account');
        $this->assertEquals($thead_ths[3]->id, 'amount');
        $this->assertEquals($thead_ths[4]->id, 'currency');
        $this->assertEquals($thead_ths[5]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,6); // no other columns

        // 7. Ensure that the tbody section has the correct quantity of rows.
        $dbRecords=$this->Distributions->find()
            ->contain(['Accounts.Categories','Currencies'])
            ->where(['transaction_id'=>$transaction_id]);
            //->order(['datetime']);
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

            // 9.0 dr/cr
            $this->assertEquals($fixtureRecord['Distributions__drcr']==1?'DR':'CR',  $htmlColumns[0]->plaintext);

            // 9.1 category
            $this->assertEquals($fixtureRecord['Categories__title'],  $htmlColumns[1]->plaintext);

            // 9.2 account
            $this->assertEquals($fixtureRecord['Accounts__title'],  $htmlColumns[2]->plaintext);

            // 9.3 amount
            $this->assertEquals($fixtureRecord['Distributions__amount'],  $htmlColumns[3]->plaintext);

            // 9.4 symbol
            $this->assertEquals($fixtureRecord['Currencies__symbol'],  $htmlColumns[4]->plaintext);

            // 9.5 Now examine the action links
            $td = $htmlColumns[5];
            $actionLinks = $td->find('a');
            $this->assertEquals('DistributionView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('DistributionEdit', $actionLinks[1]->name);
            $unknownATag--;
            //$this->assertEquals('DistributionDelete', $actionLinks[2]->name);
            //$unknownATag--;

            // 9.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 10. Ensure that all the <A> tags have been distributioned for
        $this->assertEquals(0, $unknownATag);
    }

    // GET /books/:book_id/accounts/:account_id/distributions
    public function testGET_indexa() {

        /* @var \simple_html_dom_node $content */
        /* @var \simple_html_dom_node $header */
        /* @var \simple_html_dom_node $htmlRow */
        /* @var \simple_html_dom_node $table */
        /* @var \simple_html_dom_node $tbody */
        /* @var \simple_html_dom_node $td */
        /* @var \simple_html_dom_node $thead */

        // 1. Submit request, examine response, observe no redirect, and parse the response.
        $account_id=FixtureConstants::accountTypical;
        $this->get('/books/'.FixtureConstants::bookTypical.'/accounts/'.$account_id.'/distributions');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $html=str_get_html($this->_response->body());

        // 2. Now inspect the header of the form.
        //$header=$html->find('header',0);
        //$account=$this->Accounts->get($account_id);
        //$this->assertContains($account['title'],$header->innertext());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#DistributionsIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new distribution link
        //$this->assertEquals(1, count($html->find('a#DistributionAdd')));
        //$unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $table = $html->find('table#DistributionsTable',0);
        $this->assertNotNull($table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $table->find('thead',0);
        $thead_ths = $thead->find('tr th');
        $this->assertEquals($thead_ths[0]->id, 'drcr');
        $this->assertEquals($thead_ths[1]->id, 'amount');
        $this->assertEquals($thead_ths[2]->id, 'currency');
        $this->assertEquals($thead_ths[3]->id, 'run_total');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,4); // no other columns

        // 7. Ensure that the tbody section has the correct quantity of rows.
        $dbRecords=$this->Distributions->find()
            ->contain(['Accounts.Categories','Currencies'])
            ->where(['account_id'=>$account_id]);
        //->order(['datetime']);
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

            // 9.0 dr/cr
            $this->assertEquals($fixtureRecord['Distributions__drcr']==1?'DR':'CR',  $htmlColumns[0]->plaintext);

            // 9.1 amount
            $this->assertEquals($fixtureRecord['Distributions__amount'],  $htmlColumns[1]->plaintext);

            // 9.2 currency
            $this->assertEquals($fixtureRecord['Currencies__symbol'],  $htmlColumns[2]->plaintext);

            // 9.3 run_total check this later
            //$this->assertEquals($fixtureRecord['Distributions__amount'],  $htmlColumns[3]->plaintext);

            // No action links

            // 9.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 10. Ensure that all the <A> tags have been distributioned for
        $this->assertEquals(0, $unknownATag);
    }

    public function testGET_view() {

        /* @var \simple_html_dom_node $content */
        /* @var \simple_html_dom_node $field */
        /* @var \simple_html_dom_node $table */

        // 1. Obtain the relevant records and verify their referential integrity.
        $book_id=FixtureConstants::bookTypical;
        $distribution_id=FixtureConstants::distributionTypical;
        $distribution=$this->Distributions->get($distribution_id,['contain'=>['Accounts.Categories','Currencies']]);
        $transaction_id=FixtureConstants::transactionTypical;
        $transaction=$this->Transactions->get($transaction_id);
        $this->assertEquals($distribution['transaction_id'],$transaction['id']);

        // 2. Submit request, examine response, observe no redirect, and parse the response.
        $this->get('/books/'.$book_id.'/transactions/'.$transaction_id.'/distributions/'.$distribution_id);
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $html=str_get_html($this->_response->body());

        // 3.  Look for the table that contains the view fields.
        $table = $html->find('table#DistributionViewTable',0);
        $this->assertNotNull($table);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently undistributioned for.
        $unknownRowCnt = count($table->find('tr'));

        // 4.1 drcr
        $field = $table->find('tr#drcr td',0);
        $this->assertEquals($distribution['drcr']==1?'DR':'CR', $field->plaintext);
        $unknownRowCnt--;

        // 4.2 category_title
        $field = $table->find('tr#category_title td',0);
        $this->assertEquals($distribution->account->category['title'], $field->plaintext);
        $unknownRowCnt--;

        // 4.3 account_title
        $field = $table->find('tr#account_title td',0);
        $this->assertEquals($distribution->account['title'], $field->plaintext);
        $unknownRowCnt--;

        // 4.4 amount
        $field = $table->find('tr#amount td',0);
        $this->assertEquals($distribution['amount'], $field->plaintext);
        $unknownRowCnt--;

        // 4.5 currency_symbol
        $field = $table->find('tr#currency_symbol td',0);
        $this->assertEquals($distribution->currency['symbol'], $field->plaintext);
        $unknownRowCnt--;

        // 4.9 Have all the rows been distributioned for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#DistributionsView',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }
}
