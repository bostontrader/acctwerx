<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\AccountsFixture;
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

    public function setUp() {
        parent::setup();
        $this->Accounts = TableRegistry::get('Accounts');
        $this->Books = TableRegistry::get('Books');
        $this->Categories = TableRegistry::get('Categories');
        $this->accountsFixture = new AccountsFixture();
    }

    public function testGET_add() {

        // 1. GET the url and parse the response.
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->get('/books/'.$book_id.'/accounts/add');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='AccountsAdd']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;
        $this->assertEquals($unknownATagCnt,0);

        // 4. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='AccountAddForm']",$content_node);

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
        $this->assertEquals($xpath->query("//input[@type='hidden' and @id='AccountBookId' and @value='$book_id']",$form_node)->length,1);
        $unknownInputCnt--;

        // 6.4 Ensure that there's a select field for category_id, that it has the correct quantity of available choices,
        // and that it has no selection.
        $this->selectChecker($xpath,'AccountCategoryId','categories',null,$form_node);
        $unknownSelectCnt--;

        // 6.5 Ensure that there's an input field for sort, of type text, and that it is empty
        $this->assertTrue($xpath->query("//input[@id='AccountSort' and @type='text' and not(@value)]",$form_node)->length==1);
        $unknownInputCnt--;

        // 6.6 Ensure that there's an input field for title, of type text, and that it is empty
        $this->assertTrue($xpath->query("//input[@id='AccountTitle' and @type='text' and not(@value)]",$form_node)->length==1);
        $unknownInputCnt--;

        // 7. Have all the input and selects been accounted for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);
    }

    public function testPOST_add() {

        // 1. POST a suitable record to the url, observe redirection, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->accountsFixture->newAccountRecord;
        $urlBase='/books/'.FixtureConstants::bookTypical.'/accounts';
        $fromDbRecord=$this->genericPOSTAddProlog(
            null, // no login
            $urlBase.'/add', $fixtureRecord,
            $urlBase, $this->Accounts
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

        // 1. Obtain the relevant records and verify their referential integrity.
        $account_id=FixtureConstants::accountTypical;
        $account=$this->Accounts->get($account_id,['contain'=>'Categories']);
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->assertEquals($account['book_id'],$book['id']);

        // 2. GET the url and parse the response.
        $this->get('/books/'.$book['id'].'/accounts/edit/' . $account_id);
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 3. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='AccountsEdit']");

        // 4. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;
        $this->assertEquals($unknownATagCnt,0);

        // 5. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='AccountEditForm']",$content_node);

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

        // 7.3 Ensure that there's a select field for category_id, that it has the correct quantity of available choices,
        // and that it has the correct selection.
        $this->selectChecker($xpath,'AccountCategoryId','categories',['value'=>$account->category_id,'text'=>$account->category->title],$form_node);
        $unknownSelectCnt--;

        // 7.4 Ensure that there's an input field for sort, of type text, and that it is empty
        $this->assertTrue($xpath->query("//input[@id='AccountSort' and @type='text' and @value='$account->sort']",$form_node)->length==1);
        $unknownInputCnt--;

        // 7.5 Ensure that there's an input field for title, of type text, that is correctly set
        $this->assertTrue($xpath->query("//input[@id='AccountTitle' and @type='text' and @value='$account->title']",$form_node)->length==1);
        $unknownInputCnt--;

        // 8. Have all the input and selects been accounted for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);
    }

    public function testPOST_edit() {

        // 1. Obtain the relevant records and verify their referential integrity.
        $account_id=FixtureConstants::accountTypical;
        $accountNew=$this->accountsFixture->newAccountRecord;
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->assertEquals($accountNew['book_id'],$book['id']);

        // 2. POST a suitable record to the url, observe the redirect, and parse the response.
        $baseUrl='/books/'.$book_id.'/accounts';
        $this->put("$baseUrl/$account_id", $accountNew);
        $this->assertResponseCode(302);
        $this->assertRedirect( $baseUrl );

        // 3. Now retrieve that 1 record and validate it.
        $fromDbRecord=$this->Accounts->get($account_id);
        $this->assertEquals($fromDbRecord['book_id'],$accountNew['book_id']);
        $this->assertEquals($fromDbRecord['category_id'],$accountNew['category_id']);
        $this->assertEquals($fromDbRecord['sort'],$accountNew['sort']);
        $this->assertEquals($fromDbRecord['title'],$accountNew['title']);
    }

    public function testGET_index() {

        // 1. Submit request, examine response, observe no redirect, and parse the response.
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->get('/books/'.$book_id.'/accounts');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='AccountsIndex']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;

        // 4. Look for the create new account link
        $this->getTheOnlyOne($xpath,"//a[@id='AccountAdd']",$content_node);
        $unknownATagCnt--;

        // 5. Ensure that there is a suitably named table to display the results.
        $table_node=$this->getTheOnlyOne($xpath,"//table[@id='AccountsTable']",$content_node);

        // 6. Now inspect the heading of the table.
        $this->getTheOnlyOne($xpath,"//header[contains(text(),'$book->title')]",$content_node);

        // 7. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $column_header_nodes=$xpath->query("thead/tr/th",$table_node);
        $this->assertEquals($column_header_nodes->length,4); // no other columns

        $this->getTheOnlyOne($xpath,"thead/tr/th[1][@id='category']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[2][@id='sort']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[3][@id='title']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[4][@id='actions']",$table_node);

        // 8. Ensure that the tbody section has the correct quantity of rows.
        $dbRecords=$this->Accounts->find()
            ->contain(['Categories'])
            ->where(['book_id'=>$book_id])
            ->order(['category_id','sort']);
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

            $this->assertEquals($fixtureRecord['Categories__title'],  $column_nodes->item(0)->textContent);
            $this->assertEquals($fixtureRecord['Accounts__sort'], $column_nodes->item(1)->textContent);
            $this->assertEquals($fixtureRecord['Accounts__title'], $column_nodes->item(2)->textContent);

            // 9.1 Now examine the action links
            $action_nodes=$xpath->query("a",$column_nodes->item(3));
            $this->assertTrue($action_nodes->length==2);

            $this->getTheOnlyOne($xpath,"a[@name='AccountView']",$column_nodes->item(3));
            $unknownATagCnt--;

            $this->getTheOnlyOne($xpath,"a[@name='AccountEdit']",$column_nodes->item(3));
            $unknownATagCnt--;

            // 9.9 No other columns
            $this->assertEquals($column_nodes->length,$column_header_nodes->length);
        }

        // 10. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);
    }

    public function testGET_view() {

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
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 3. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='AccountsView']");

        // 4. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;

        // 4.1 Look for the account distributions link
        $this->getTheOnlyOne($xpath,"//a[@id='AccountDistributions']",$content_node);
        $unknownATagCnt--;

        // 4.2 Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);

        // 5. Ensure that there is a suitably named table to display the results.
        $table_node=$this->getTheOnlyOne($xpath,"//table[@id='AccountViewTable']",$content_node);

        // 6. Now inspect the fields in the table.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt=$xpath->query("//tr",$table_node)->length;

        // 6.1 book_title
        $this->getTheOnlyOne($xpath,"//tr[1][@id='book_title']/td[text()='$book->title']",$table_node);
        $unknownRowCnt--;

        // 6.2 category_title
        $this->getTheOnlyOne($xpath,"//tr[2][@id='category_title']/td[text()='$category->title']",$table_node);
        $unknownRowCnt--;

        // 6.3 sort
        $this->getTheOnlyOne($xpath,"//tr[3][@id='sort']/td[text()='$account->sort']",$table_node);
        $unknownRowCnt--;

        // 6.4 title
        $this->getTheOnlyOne($xpath,"//tr[4][@id='title']/td[text()='$account->title']",$table_node);
        $unknownRowCnt--;

        // 6.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);
    }
}
