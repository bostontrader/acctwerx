<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\AccountsFixture;
use App\Test\Fixture\CategoriesFixture;
use Cake\ORM\TableRegistry;

class AccountsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.accounts',
        'app.books',
        'app.categories'
    ];

    /** @var \Cake\ORM\Table */
    private $Accounts;

    /** @var \Cake\ORM\Table */
    private $Books;

    /** @var \Cake\ORM\Table */
    private $Categories;

    /** @var \App\Test\Fixture\AccountsFixture */
    private $accountsFixture;

    /** @var \App\Test\Fixture\CategoriesFixture */
    private $categoriesFixture;

    public function setUp() {
        $this->Accounts = TableRegistry::get('Accounts');
        $this->Books = TableRegistry::get('Books');
        $this->Categories = TableRegistry::get('Categories');
        $this->accountsFixture = new accountsFixture();
        $this->categoriesFixture = new categoriesFixture();
    }

    public function testGET_add() {

        /* @var \simple_html_dom_node $form */
        /* @var \simple_html_dom_node $html */
        /* @var \simple_html_dom_node $input */
        /* @var \simple_html_dom_node $legend */

        // 1. GET the url and parse the response.
        $book_id=FixtureConstants::bookTypical;
        $this->get('/books/'.$book_id.'/accounts/add');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $html = str_get_html($this->_response->body());

        // 2. Ensure that the correct form exists
        $form = $html->find('form#AccountAddForm',0);
        $this->assertNotNull($form);

        // 3. Now inspect the legend of the form.
        $legend = $form->find('legend',0);
        $book=$this->Books->get($book_id);
        $this->assertContains($book['title'],$legend->innertext());

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 4.2 Look for the hidden POST input.
        if($this->lookForHiddenInput($form)) $unknownInputCnt--;

        // 4.3 Look for the hidden book_id input, and validate its contents.
        if($this->lookForHiddenInput($form,'book_id',$book_id)) $unknownInputCnt--;

        // 4.4 Ensure that there's a select field for category_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'AccountCategoryId', 'categories')) $unknownSelectCnt--;

        // 4.5 Ensure that there's an input field for sort, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#AccountSort')) $unknownInputCnt--;

        // 4.6 Ensure that there's an input field for title, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#AccountTitle')) $unknownInputCnt--;

        // 5. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#AccountsAdd');
    }

    public function testPOST_add() {

        // 1. POST a suitable record to the url, observe redirection, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->accountsFixture->newAccountRecord;
        $fromDbRecord=$this->genericPOSTAddProlog(
            null, // no login
            '/books/'.FixtureConstants::bookTypical.'/accounts/add', $fixtureRecord,
            '/books/'.FixtureConstants::bookTypical.'/accounts', $this->Accounts
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['book_id'],$fixtureRecord['book_id']);
        $this->assertEquals($fromDbRecord['category_id'],$fixtureRecord['category_id']);
        $this->assertEquals($fromDbRecord['sort'],$fixtureRecord['sort']);
        $this->assertEquals($fromDbRecord['title'],$fixtureRecord['title']);
    }

    //public function testDELETE() {
        //$this->deletePOST(
            //null, // no login
            //'/accounts/delete/',
            //FixtureConstants::accountTypical, '/accounts', $this->Accounts
        //);
    //}

    public function testGET_edit() {

        /* @var \simple_html_dom_node $form */
        /* @var \simple_html_dom_node $html */
        /* @var \simple_html_dom_node $legend */

        // 1. Obtain the relevant records and verify their referential integrity.
        $account_id=FixtureConstants::accountTypical;
        $account=$this->Accounts->get($account_id);
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->assertEquals($account['book_id'],$book['id']);

        // 2. GET the url and parse the response.
        $this->get('/books/'.$book['id'].'/accounts/edit/' . $account_id);
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#AccountEditForm',0);
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
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 5.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // 5.3 Ensure that there's a select field for category_id and that it is correctly set
        // $category_id / $category['title'], from fixture
        $category_id=$account['category_id'];
        $category = $this->categoriesFixture->get($category_id);
        if($this->inputCheckerB($form,'select#AccountCategoryId option[selected]',$category_id,$category['title']))
            $unknownSelectCnt--;

        // 5.3 Ensure that there's an input field for sort, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#AccountSort', $account['sort'])) $unknownInputCnt--;

        // 5.4 Ensure that there's an input field for title, of type text, that is correctly set
        if($this->inputCheckerA($form,'input#AccountTitle', $account['title'])) $unknownInputCnt--;

        // 6. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#AccountsEdit');
    }

    public function testPOST_edit() {

        // 1. Obtain the relevant records and verify their referential integrity.
        $account_id=FixtureConstants::accountTypical;
        $accountNew=$this->accountsFixture->newAccountRecord;
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->assertEquals($accountNew['book_id'],$book['id']);

        // 2. POST a suitable record to the url, observe the redirect, and parse the response.
        $shortUrl='/books/'.$book_id.'/accounts';
        $this->put($shortUrl.'/'.$account_id, $accountNew);
        $this->assertResponseCode(302);
        $this->assertRedirect( $shortUrl );

        // 3. Now retrieve that 1 record and validate it.
        $fromDbRecord=$this->Accounts->get($account_id);
        $this->assertEquals($fromDbRecord['book_id'],$accountNew['book_id']);
        $this->assertEquals($fromDbRecord['category_id'],$accountNew['category_id']);
        $this->assertEquals($fromDbRecord['sort'],$accountNew['sort']);
        $this->assertEquals($fromDbRecord['title'],$accountNew['title']);
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
        $this->get('/books/'.$book_id.'/accounts');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $html=str_get_html($this->_response->body());

        // 2. Now inspect the legend of the form.
        $header=$html->find('header',0);
        $book=$this->Books->get($book_id);
        $this->assertContains($book['title'],$header->innertext());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#AccountsIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new account link
        $this->assertEquals(1, count($html->find('a#AccountAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $table = $html->find('table#AccountsTable',0);
        $this->assertNotNull($table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $table->find('thead',0);
        $thead_ths = $thead->find('tr th');
        $this->assertEquals($thead_ths[0]->id, 'category');
        $this->assertEquals($thead_ths[1]->id, 'sort');
        $this->assertEquals($thead_ths[2]->id, 'title');
        $this->assertEquals($thead_ths[3]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,4); // no other columns

        // 7. Ensure that the tbody section has the correct quantity of rows.
        $dbRecords=$this->Accounts->find()
            ->contain(['Categories'])
            ->where(['book_id'=>$book_id])
            ->order(['category_id','sort']);
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

            // 9.0 title
            $this->assertEquals($fixtureRecord['Categories__title'],  $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['Accounts__sort'],  $htmlColumns[1]->plaintext);
            $this->assertEquals($fixtureRecord['Accounts__title'],  $htmlColumns[2]->plaintext);

            // 9.1 Now examine the action links
            $td = $htmlColumns[3];
            $actionLinks = $td->find('a');
            $this->assertEquals('AccountView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('AccountEdit', $actionLinks[1]->name);
            $unknownATag--;
            //$this->assertEquals('AccountDelete', $actionLinks[2]->name);
            //$unknownATag--;

            // 9.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 10. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testGET_view() {

        /* @var \simple_html_dom_node $content */
        /* @var \simple_html_dom_node $field */
        /* @var \simple_html_dom_node $table */

        // 1. Obtain the relevant records and verify their referential integrity.
        $account_id=FixtureConstants::accountTypical;
        $account=$this->Accounts->get($account_id);
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $category_id=FixtureConstants::categoryTypical;
        $category=$this->Categories->get($category_id);
        $this->assertEquals($account['book_id'],$book['id']);
        $this->assertEquals($account['category_id'],$category['id']);

        // 2. Submit request, examine response, observe no redirect, and parse the response.
        $this->get('/books/'.$book_id.'/accounts/'.$account_id);
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $html=str_get_html($this->_response->body());

        // 3.  Look for the table that contains the view fields.
        $table = $html->find('table#AccountViewTable',0);
        $this->assertNotNull($table);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($table->find('tr'));

        // 4.1 book_title
        $field = $table->find('tr#book_title td',0);
        $this->assertEquals($book['title'], $field->plaintext);
        $unknownRowCnt--;

        // 4.2 category_title
        $field = $table->find('tr#category_title td',0);
        $this->assertEquals($category['title'], $field->plaintext);
        $unknownRowCnt--;

        // 4.3 sort
        $field = $table->find('tr#sort td',0);
        $this->assertEquals($account['sort'], $field->plaintext);
        $unknownRowCnt--;

        // 4.4 title
        $field = $table->find('tr#title td',0);
        $this->assertEquals($account['title'], $field->plaintext);
        $unknownRowCnt--;

        // 4.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#AccountsView',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }
}
