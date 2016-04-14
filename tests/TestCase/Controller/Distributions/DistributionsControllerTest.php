<?php
namespace App\Test\TestCase\Controller;

use App\Controller\DistributionsController;
use App\Test\Fixture\DistributionsFixture;
use App\Test\Fixture\FixtureConstants;
use Cake\ORM\TableRegistry;

class DistributionsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.accounts',
        'app.accounts_categories',
        'app.books',
        'app.categories',
        'app.currencies',
        'app.distributions',
        'app.transactions'
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
        //$book=$this->Books->get($book_id);
        $transaction_id=FixtureConstants::transactionTypical;
        //$transaction=$this->Transactions->get($transaction_id);

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

        // 5. Now inspect the legend of the form.
        $this->assertContains("$transaction_id",$this->getTheOnlyOne($xpath,"//legend",$form_node)->textContent);
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

        // 6.3 Look for the hidden transaction_id input, and validate its contents.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @id='DistributionTransactionId' and @value='$transaction_id']",$form_node)->length,1);
        $unknownInputCnt--;

        // 6.4 drcr

        // 6.4.1 Look for the hidden drcr input, and validate its contents.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @name='drcr' and @value='']",$form_node)->length,1);
        $unknownInputCnt--;

        // 6.4.2 drcr-1 (default choice)
        $this->assertEquals($xpath->query("//input[@id='drcr-1' and @type='radio' and @checked='checked' and @name='drcr' and @value='1']",$form_node)->length,1);
        $unknownInputCnt--;

        // 6.4.3 drcr--1
        $this->assertEquals($xpath->query("//input[@id='drcr--1' and @type='radio' and not(@checked) and @name='drcr' and @value='-1']",$form_node)->length,1);
        $unknownInputCnt--;

        // 6.5 Ensure that there's a select field for account_id, that it has the correct quantity of available choices,
        // and that it has no selection.
        $this->selectCheckerA($xpath,'DistributionAccountId','accounts',null,$form_node);
        $unknownSelectCnt--;

        // 6.6 Ensure that there's an input field for amount, of type text, and that it is empty
        $this->assertTrue($xpath->query("//input[@id='DistributionAmount' and @type='text' and not(@value)]",$form_node)->length==1);
        $unknownInputCnt--;

        // 6.7 Ensure that there's a select field for currency_id, that it has the correct quantity of available choices,
        // and that it has no selection.
        $this->selectCheckerA($xpath,'DistributionCurrencyId','currencies',null,$form_node);
        $unknownSelectCnt--;

        // 7. Have all the input and selects been accounted for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

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
        $this->assertEquals($fromDbRecord['drcr'],$fixtureRecord['drcr']);
        $this->assertEquals($fromDbRecord['account_id'],$fixtureRecord['account_id']);
        $this->assertEquals($fromDbRecord['amount'],$fixtureRecord['amount']);
        $this->assertEquals($fromDbRecord['currency_id'],$fixtureRecord['currency_id']);

        // 3. Can I see the DISTRIBUTION_SAVED message?
        $flash=$this->_controller->request->session()->read('Flash.flash');
        $this->assertEquals($flash[0]['message'],DistributionsController::DISTRIBUTION_SAVED);
    }

    //public function testDELETE() {
    //$this->deletePOST(
    //null, // no login
    //'/distributions/delete/',
    //FixtureConstants::distributionTypical, '/distributions', $this->Distributions
    //);
    //}

    public function testGET_edit() {

        // 1. Obtain the relevant records and verify their referential integrity.
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $transaction_id=FixtureConstants::transactionTypical;
        $transaction=$this->Transactions->get($transaction_id);
        $distribution_id=FixtureConstants::distributionTypical;
        $distribution=$this->Distributions->get($distribution_id,['contain'=>['Accounts','Currencies']]);
        $this->assertEquals($distribution['transaction_id'],$transaction['id']);
        $this->assertEquals($transaction['book_id'],$book['id']);

        // 2. GET the url and parse the response.
        $this->get("/books/$book_id/transactions/$transaction_id/distributions/edit/$distribution_id");
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 3. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='DistributionsEdit']");

        // 4. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;
        $this->assertEquals($unknownATagCnt,0);

        // 5. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='DistributionEditForm']",$content_node);

        // 6. Now inspect the legend of the form.
        $this->assertContains("$transaction_id",$this->getTheOnlyOne($xpath,"//legend",$form_node)->textContent);

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

        // 7.2 Look for the hidden POST input.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @name='_method' and @value='PUT']",$form_node)->length,1);
        $unknownInputCnt--;

        // There is none!
        // Look for the hidden transaction_id input, and validate its contents.

        // 7.4 drcr

        // 7.4.1 Look for the hidden drcr input, and validate its contents.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @name='drcr' and @value='']",$form_node)->length,1);
        $unknownInputCnt--;

        // 7.4.2 drcr-1 (default choice)
        $this->assertEquals($xpath->query("//input[@id='drcr-1' and @type='radio' and @checked='checked' and @name='drcr' and @value='1']",$form_node)->length,1);
        $unknownInputCnt--;

        // 7.4.3 drcr--1
        $this->assertEquals($xpath->query("//input[@id='drcr--1' and @type='radio' and not(@checked) and @name='drcr' and @value='-1']",$form_node)->length,1);
        $unknownInputCnt--;

        // 7.5 Ensure that there's a select field for account_id, that it has the correct quantity of available choices,
        // and that it has the correct selection.
        $this->selectCheckerA($xpath,'DistributionAccountId','accounts',['value'=>$distribution->account_id,'text'=>$distribution->account->title],$form_node);
        $unknownSelectCnt--;

        // 7.6 Ensure that there's an input field for amount, of type text, that is correctly set.
        $this->assertTrue($xpath->query("//input[@id='DistributionAmount' and @type='text' and @value='$distribution->amount']",$form_node)->length==1);
        $unknownInputCnt--;

        // 7.7 Ensure that there's a select field for currency_id, that it has the correct quantity of available choices,
        // and that it has no selection.
        $this->selectCheckerA($xpath,'DistributionCurrencyId','currencies',['value'=>$distribution->currency_id,'text'=>$distribution->currency->title],$form_node);
        $unknownSelectCnt--;

        // 8. Have all the input and selects been accounted for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);
    }

    public function testPOST_edit() {

        // 1. Obtain the relevant records and verify their referential integrity.
        $book_id=FixtureConstants::bookTypical;
        $distribution_id=FixtureConstants::distributionTypical;
        $distributionNew=$this->distributionsFixture->newDistributionRecord;
        $transaction_id=FixtureConstants::transactionTypical;

        // 2. POST a suitable record to the url, observe the redirect, and parse the response.
        $urlBase="/books/$book_id/transactions/$transaction_id/distributions";
        $this->put($urlBase.'/'.$distribution_id, $distributionNew);
        $this->assertResponseCode(302);
        $this->assertRedirect( $urlBase );

        // 3. Now retrieve that 1 record and validate it.
        $fromDbRecord=$this->Distributions->get($distribution_id);
        $this->assertEquals($fromDbRecord['transaction_id'],$distributionNew['transaction_id']);
        $this->assertEquals($fromDbRecord['drcr'],$distributionNew['drcr']);
        $this->assertEquals($fromDbRecord['account_id'],$distributionNew['account_id']);
        $this->assertEquals($fromDbRecord['amount'],$distributionNew['amount']);
        $this->assertEquals($fromDbRecord['currency_id'],$distributionNew['currency_id']);
    }

    // GET /books/:book_id/transactions/:transaction_id/distributions
    public function testGET_index() {

        // 1. Submit submit request, examine response, observe no redirect, and parse the response.
        $transaction_id=FixtureConstants::transactionTypical;
        $this->get('/books/'.FixtureConstants::bookTypical.'/transactions/'.$transaction_id.'/distributions');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='DistributionsIndex']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;

        // 4. Look for the create new distribution link
        $this->getTheOnlyOne($xpath,"//a[@id='DistributionAdd']",$content_node);
        $unknownATagCnt--;

        // 5. Ensure that there is a suitably named table to display the results.
        $table_node=$this->getTheOnlyOne($xpath,"//table[@id='DistributionsTable']",$content_node);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $column_header_nodes=$xpath->query("thead/tr/th",$table_node);
        $this->assertEquals($column_header_nodes->length,6); // no other columns

        $this->getTheOnlyOne($xpath,"thead/tr/th[1][@id='drcr']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[2][@id='category']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[3][@id='account']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[4][@id='amount']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[5][@id='currency']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[6][@id='actions']",$table_node);

        // 7. Ensure that the tbody section has the correct quantity of rows.
        $dbRecords=$this->Distributions->find()
            ->contain(['Accounts.Categories','Currencies'])
            ->where(['transaction_id'=>$transaction_id])
            ->order(['drcr'=>'desc']);
        $tbody_nodes=$xpath->query("tbody/tr",$table_node);
        $this->assertTrue($tbody_nodes->length==$dbRecords->count());

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($dbRecords->execute()->fetchAll('assoc')));
        $iterator->attachIterator(new \ArrayIterator(iterator_to_array($tbody_nodes)));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $row_node = $values[1];
            $column_nodes=$xpath->query("td",$row_node);

            $this->assertEquals($fixtureRecord['Distributions__drcr']==1?'DR':'CR',  $column_nodes->item(0)->textContent);
            //$this->assertEquals($fixtureRecord['Categories__title'], $column_nodes->item(1)->textContent);
            $this->assertEquals($fixtureRecord['Accounts__title'], $column_nodes->item(2)->textContent);
            $this->assertEquals($fixtureRecord['Distributions__amount'], $column_nodes->item(3)->textContent);
            $this->assertEquals($fixtureRecord['Currencies__title'], $column_nodes->item(4)->textContent);

            // 9.1 Now examine the action links
            $action_nodes=$xpath->query("a",$column_nodes->item(5));
            $this->assertTrue($action_nodes->length==2);

            $this->getTheOnlyOne($xpath,"a[@name='DistributionView']",$column_nodes->item(5));
            //$this->getTheOnlyOne($xpath,"a[@name='DistributionView']",$action_nodes->item(0)); // why doesn't this work?
            $unknownATagCnt--;

            $this->getTheOnlyOne($xpath,"a[@name='DistributionEdit']",$column_nodes->item(5));
            $unknownATagCnt--;

            // 9.9 No other columns
            $this->assertEquals($column_nodes->length,$column_header_nodes->length);
        }

        // 10. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);
    }

    // Even though this is a list of distributions, it's significantly different
    // from index.  Therefore just use a 2nd method.
    // GET /books/:book_id/accounts/:account_id/distributions
    public function testGET_indexa() {

        // 1. Submit submit request, examine response, observe no redirect, and parse the response.
        $account_id=FixtureConstants::accountTypical;
        $this->get('/books/'.FixtureConstants::bookTypical.'/accounts/'.$account_id.'/distributions');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='DistributionsIndex']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;

        // 4. Look for the create new distribution link
        //$this->getTheOnlyOne($xpath,"//a[@id='DistributionAdd']",$content_node);
        //$unknownATagCnt--;

        // 5. Ensure that there is a suitably named table to display the results.
        $table_node=$this->getTheOnlyOne($xpath,"//table[@id='DistributionsTable']",$content_node);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $column_header_nodes=$xpath->query("thead/tr/th",$table_node);
        $this->assertEquals($column_header_nodes->length,6); // no other columns

        $this->getTheOnlyOne($xpath,"thead/tr/th[1][@id='tran_datetime']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[2][@id='note']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[3][@id='drcr']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[4][@id='amount']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[5][@id='currency']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[6][@id='run_total']",$table_node);

        // 7. Ensure that the tbody section has the correct quantity of rows.
        $dbRecords=$this->Distributions->find()
            ->contain(['Accounts.Categories','Currencies','Transactions'])
            ->where(['account_id'=>$account_id])
            ->order('Transactions.tran_datetime');
        $tbody_nodes=$xpath->query("tbody/tr",$table_node);
        $this->assertTrue($tbody_nodes->length==$dbRecords->count());

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($dbRecords->execute()->fetchAll('assoc')));
        $iterator->attachIterator(new \ArrayIterator(iterator_to_array($tbody_nodes)));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $row_node = $values[1];
            $column_nodes=$xpath->query("td",$row_node);

            $this->assertEquals($fixtureRecord['Transactions__tran_datetime'],  $column_nodes->item(0)->textContent);
            $this->assertEquals($fixtureRecord['Transactions__note'],  $column_nodes->item(1)->textContent);
            $this->assertEquals($fixtureRecord['Distributions__drcr']==1?'DR':'CR',  $column_nodes->item(2)->textContent);
            $this->assertEquals($fixtureRecord['Distributions__amount'],  $column_nodes->item(3)->textContent);
            $this->assertEquals($fixtureRecord['Currencies__symbol'], $column_nodes->item(4)->textContent);

            // 9.5 run_total check this later
            //$this->assertEquals($fixtureRecord['Distributions__amount'],  $htmlColumns[3]->plaintext);

            // No action links

            // 9.9 No other columns
            $this->assertEquals($column_nodes->length,$column_header_nodes->length);
        }

        // 10. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);
    }

    public function testGET_view() {

        // 1. Obtain the relevant records and verify their referential integrity.
        $book_id=FixtureConstants::bookTypical;
        $distribution_id=FixtureConstants::distributionTypical;
        $distribution=$this->Distributions->get($distribution_id,['contain'=>['Accounts.Categories','Currencies']]);
        $transaction_id=FixtureConstants::transactionTypical;
        $transaction=$this->Transactions->get($transaction_id);
        $this->assertEquals($distribution['transaction_id'],$transaction['id']);

        // 2. Submit request, examine response, observe no redirect, and parse the response.
        $this->get("/books/$book_id/transactions/$transaction_id/distributions/$distribution_id");
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 3. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='DistributionsView']");

        // 4. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;

        // 4.1 Look for the account distributions link
        //$this->getTheOnlyOne($xpath,"//a[@id='AccountDistributions']",$content_node);
        //$unknownATagCnt--;

        // 4.2 Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);

        // 5. Ensure that there is a suitably named table to display the results.
        $table_node=$this->getTheOnlyOne($xpath,"//table[@id='DistributionViewTable']",$content_node);

        // 6. Now inspect the fields in the table.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt=$xpath->query("//tr",$table_node)->length;

        // 6.1 drcr
        $expected=$distribution['drcr']==1?'DR':'CR';
        $this->getTheOnlyOne($xpath,"//tr[1][@id='drcr']/td[text()='$expected']",$table_node);
        $unknownRowCnt--;

        // 6.2 category_title
        $expected=$distribution->account->catstring;
        $this->getTheOnlyOne($xpath,"//tr[2][@id='category_title']/td[text()='$expected']",$table_node);
        $unknownRowCnt--;

        // 6.3 account title
        $expected=$distribution->account->title;
        $this->getTheOnlyOne($xpath,"//tr[3][@id='account_title']/td[text()='$expected']",$table_node);
        $unknownRowCnt--;

        // 6.4 amount
        $this->getTheOnlyOne($xpath,"//tr[4][@id='amount']/td[text()='$distribution->amount']",$table_node);
        $unknownRowCnt--;

        // 6.5 currency symbol
        $expected=$distribution->currency->symbol;
        $this->getTheOnlyOne($xpath,"//tr[5][@id='currency_symbol']/td[text()='$expected']",$table_node);
        $unknownRowCnt--;

        // 6.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);
    }
}
