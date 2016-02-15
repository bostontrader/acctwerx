<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\CurrenciesFixture;
use Cake\ORM\TableRegistry;

class CurrenciesControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.currencies'
    ];

    /** @var \Cake\ORM\Table */
    private $Currencies;

    /** @var \App\Test\Fixture\CurrenciesFixture */
    private $currenciesFixture;

    public function setUp() {
        //parent::setUp();
        $this->Currencies = TableRegistry::get('Currencies');
        $this->currenciesFixture = new CurrenciesFixture();
    }

    public function testGET_add() {

        /* @var \simple_html_dom_node $form */
        /* @var \simple_html_dom_node $html */

        // 1. GET the url and parse the response.
        $this->get('/currencies/add');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $html = str_get_html($this->_response->body());

        // 2. Ensure that the correct form exists
        $form = $html->find('form#CurrencyAddForm',0);
        $this->assertNotNull($form);

        // 3. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 3.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 3.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form)) $unknownInputCnt--;

        // 3.3 Ensure that there's an input field for title, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#CurrencyTitle')) $unknownInputCnt--;

        // 3.4 Ensure that there's an input field for symbol, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#CurrencySymbol')) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#CurrenciesAdd');
    }

    public function testPOST_add() {

        // 1. POST a suitable record to the url, observe redirection, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->currenciesFixture->newCurrencyRecord;
        $fromDbRecord=$this->genericPOSTAddProlog(
            null, // no login
            '/currencies/add', $fixtureRecord,
            '/currencies', $this->Currencies
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['title'],$fixtureRecord['title']);
        $this->assertEquals($fromDbRecord['symbol'],$fixtureRecord['symbol']);
    }

    //public function testDELETE() {
        //$this->deletePOST(
            //null, // no login
            //'/currencies/delete/',
            //FixtureConstants::currencyTypical, '/currencies', $this->currencies
        //);
    //}

    public function testGET_edit() {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $currency_id=FixtureConstants::currencyTypical;
        $currency=$this->Currencies->get($currency_id);
        $url='/currencies/edit/' . $currency_id;
        $html=$this->loginRequestResponse(null,$url);

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#CurrencyEditForm',0);
        $this->assertNotNull($form);

        // 3. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 3.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 3.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // 3.3 Ensure that there's an input field for title, of type text, that is correctly set
        if($this->inputCheckerA($form,'input#CurrencyTitle',
            $currency['title'])) $unknownInputCnt--;

        // 3.4 Ensure that there's an input field for symbol, of type text, that is correctly set
        if($this->inputCheckerA($form,'input#CurrencySymbol',
            $currency['symbol'])) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#CurrenciesEdit');
    }

    public function testPOST_edit() {

        // 1. POST a suitable record to the url, observe the redirect, and return the record just
        // posted, as read from the db.
        $currency_id=FixtureConstants::currencyTypical;
        $fixtureRecord=$this->currenciesFixture->newCurrencyRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            null, // no login
            '/currencies/edit',
            $currency_id, $fixtureRecord,
            '/currencies', $this->Currencies
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['title'],$fixtureRecord['title']);
        $this->assertEquals($fromDbRecord['symbol'],$fixtureRecord['symbol']);
    }

    public function testGET_index() {

        /* @var \simple_html_dom_node $content */
        /* @var \simple_html_dom_node $htmlRow */
        /* @var \simple_html_dom_node $table */
        /* @var \simple_html_dom_node $tbody */
        /* @var \simple_html_dom_node $td */
        /* @var \simple_html_dom_node $thead */

        // 1. Login, GET the url, observe the response, parse the response and send it back.
        $html=$this->loginRequestResponse(null,'/currencies'); // no login

        // 2. Get the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#CurrenciesIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 3. Look for the create new currency link
        $this->assertEquals(1, count($html->find('a#CurrencyAdd')));
        $unknownATag--;

        // 4. Ensure that there is a suitably named table to display the results.
        $table = $html->find('table#CurrenciesTable',0);
        $this->assertNotNull($table);

        // 5. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'id');
        $this->assertEquals($thead_ths[1]->id, 'title');
        $this->assertEquals($thead_ths[2]->id, 'symbol');
        $this->assertEquals($thead_ths[3]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,4); // no other columns

        // 6. Ensure that the tbody section has the same
        //    quantity of rows as the count of currency records in the fixture.
        $tbody = $table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->currenciesFixture->records));

        // 7. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->currenciesFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');

            // 7.0 id
            $this->assertEquals($fixtureRecord['id'],  $htmlColumns[0]->plaintext);

            // 7.1 title
            $this->assertEquals($fixtureRecord['title'],  $htmlColumns[1]->plaintext);

            // 7.2 symbol
            $this->assertEquals($fixtureRecord['symbol'],  $htmlColumns[2]->plaintext);

            // 7.3 Now examine the action links
            $td = $htmlColumns[3];
            $actionLinks = $td->find('a');
            $this->assertEquals('CurrencyView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('CurrencyEdit', $actionLinks[1]->name);
            $unknownATag--;
            //$this->assertEquals('CurrencyDelete', $actionLinks[2]->name);
            //$unknownATag--;

            // 7.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testGET_view() {

        /* @var \simple_html_dom_node $content */
        /* @var \simple_html_dom_node $field */
        /* @var \simple_html_dom_node $table */

        // 1. Obtain a record to view, login, GET the url, parse the response and send it back.
        $currency_id=FixtureConstants::currencyTypical;
        $currency=$this->Currencies->get($currency_id);
        $url='/currencies/' . $currency_id;
        $html=$this->loginRequestResponse(null, $url); // no login

        // 2.  Look for the table that contains the view fields.
        $table = $html->find('table#CurrencyViewTable',0);
        $this->assertNotNull($table);

        // 3. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($table->find('tr'));

        // 3.1 title
        $field = $table->find('tr#title td',0);
        $this->assertEquals($currency['title'], $field->plaintext);
        $unknownRowCnt--;

        // 3.2 symbol
        $field = $table->find('tr#symbol td',0);
        $this->assertEquals($currency['symbol'], $field->plaintext);
        $unknownRowCnt--;

        // 3.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);
    }
}
