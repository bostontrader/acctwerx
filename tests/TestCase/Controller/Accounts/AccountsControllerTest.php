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
    //private $Accounts;

    /** @var \Cake\ORM\Table */
    private $Books;

    /** @var \Cake\ORM\Table */
    //private $Categories;

    /** @var \App\Test\Fixture\AccountsFixture */
    private $accountsFixture;

    /** @var \App\Test\Fixture\CategoriesFixture */
    //private $categoriesFixture;

    public function setUp() {
        parent::setup();
        //$this->Accounts = TableRegistry::get('Accounts');
        $this->Books = TableRegistry::get('Books');
        //$this->Categories = TableRegistry::get('Categories');
        //$this->accountsFixture = new accountsFixture();
        //$this->categoriesFixture = new categoriesFixture();
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

        // 2. Count the A tags.
        $unknownATagCnt=$nodes=$xpath->query("//div[@id='AccountsAdd']//a")->length;
        $this->assertEquals($unknownATagCnt,0);

        // 3. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='AccountAddForm']");

        // 4. Now inspect the legend of the form.
        $legend_node=$this->getTheOnlyOne($xpath,"//legend",$form_node);
        $this->assertContains($book['title'],$legend_node->textContent);

        // 5. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 5.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt=$xpath->query("//select",$form_node)->length;
        $unknownInputCnt=$xpath->query("//input",$form_node)->length;

        // 5.2 Look for the hidden POST input.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @name='_method' and @value='POST']",$form_node),1);
        $unknownInputCnt--;

        // 5.3 Look for the hidden book_id input, and validate its contents.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @id='AccountBookId' and @value='$book_id']",$form_node)->length,1);
        $unknownInputCnt--;

        // 5.4 Ensure that there's a select field for category_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        $nodes=$xpath->query("//select[@id='AccountCategoryId']",$form_node);
        $this->assertTrue($nodes->length==1);
        $select_node=$nodes->item(0);
        $nodes=$xpath->query("//option[selected]",$select_node);
        $this->assertTrue($nodes->length==0);
        $nodes=$xpath->query("//option",$select_node);
        $record_cnt = $this->viewVariable('categories')->count();
        $this->assertTrue($nodes->length==$record_cnt+1); // one extra fro
        //if($this->selectCheckerA($form, 'AccountCategoryId', 'categories'))
        $unknownSelectCnt--;

        // 5.5 Ensure that there's an input field for sort, of type text, and that it is empty
        $nodes=$xpath->query("//input[@id='AccountSort' and @type='text' and not(@value)]",$form_node);
        $this->assertTrue($nodes->length==1);
        $unknownInputCnt--;

        // 5.6 Ensure that there's an input field for title, of type text, and that it is empty
        $nodes=$xpath->query("//input[@id='AccountTitle' and @type='text' and not(@value)]",$form_node);
        $this->assertTrue($nodes->length==1);
        $unknownInputCnt--;

        // 6. Have all the input and selects been accounted for?
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
        //$html = str_get_html($this->_response->body());
        $dom = \DomDocument::loadHtml($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 3. Verify the existence of the A tags on the portion of this
        // page generated by this controller (the layout is tested separately).
        // There should be zero links.
        $nodes=$xpath->query("//div[@id='AccountsEdit']//a");
        $this->assertTrue($nodes->length==0);

        // 4. Ensure that the correct form exists
        $nodes=$xpath->query("//form[@id='AccountEditForm']");
        $this->assertTrue($nodes->length==1);
        $form_node=$nodes->item(0);

        // 5. Now inspect the legend of the form.
        //$legend = $form->find('legend',0);
        //$this->assertContains($book['title'],$legend->innertext());
        $nodes=$xpath->query("//legend",$form_node);
        $this->assertTrue($nodes->length==1);
        $legend_node=$nodes->item(0);
        $book=$this->Books->get($book_id);
        $this->assertContains($book['title'],$legend_node->textContent);

        // 6. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 6.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $nodes=$xpath->query("//select",$form_node);
        $unknownSelectCnt=$nodes->length;
        $nodes=$xpath->query("//input",$form_node);
        $unknownInputCnt=$nodes->length;

        // 6.2 Look for the hidden PUT input
        $nodes=$xpath->query("//input[@type='hidden' and @name='_method' and @value='PUT']",$form_node);
        $this->assertTrue($nodes->length==1);
        $unknownInputCnt--;

        // no hidden input here
        // 5.3 Ensure that there's a select field for category_id and that it is correctly set
        // $category_id / $category['title'], from fixture
        //$category_id=$account['category_id'];
        //$category = $this->categoriesFixture->get($category_id);
        //if($this->inputCheckerB($form,'select#AccountCategoryId option[selected]',$category_id,$category['title']))
            //$unknownSelectCnt--;
        $nodes=$xpath->query("//select[@id='AccountCategoryId']",$form_node);
        $this->assertTrue($nodes->length==1);
        $select_node=$nodes->item(0);
        //$category_id=$account
        $nodes=$xpath->query("//option",$select_node);
        $record_cnt = $this->viewVariable('categories')->count();
        $this->assertTrue($nodes->length==$record_cnt+1);
        //$nodes=$xpath->query("//option",$select_node);

        $title=$account->category->title;
        $nodes=$xpath->query("//option[@selected='selected' and @value='$account->category_id' and text()='$title']",$select_node);
        $this->assertTrue($nodes->length==1);

        //$this->assertTrue($nodes->length==$record_cnt+1); // one extra fro
        //if($this->selectCheckerA($form, 'AccountCategoryId', 'categories'))
        $unknownSelectCnt--;





        // 5.3 Ensure that there's an input field for sort, of type text, and that it is empty
        //if($this->inputCheckerA($form,'input#AccountSort', $account['sort'])) $unknownInputCnt--;
        $nodes=$xpath->query("//input[@id='AccountSort' and @type='text' and @value='$account->sort']",$form_node);
        $this->assertTrue($nodes->length==1);
        $unknownInputCnt--;

        // 5.4 Ensure that there's an input field for title, of type text, that is correctly set
        //if($this->inputCheckerA($form,'input#AccountTitle', $account['title'])) $unknownInputCnt--;
        $nodes=$xpath->query("//input[@id='AccountTitle' and @type='text' and @value='$account->title']",$form_node);
        $this->assertTrue($nodes->length==1);
        $unknownInputCnt--;

        // 6. Have all the input and selects been accounted for?
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

        // 1. Submit submit request, examine response, observe no redirect, and parse the response.
        $book_id=FixtureConstants::bookTypical;
        $this->get('/books/'.$book_id.'/accounts');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        //$html=str_get_html($this->_response->body());
        $dom = \DomDocument::loadHtml($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Now inspect the heading of the table.
        //$header=$html->find('header',0);
        $book=$this->Books->get($book_id);
        //$this->assertContains($book['title'],$header->innertext());
        $nodes=$xpath->query("//header[contains(text(),'$book->title')]",$dom);
        $this->assertTrue($nodes->length==1);
        //$legend_node=$nodes->item(0);
        //$book=$this->Books->get($book_id);
        //$this->assertContains($book['title'],$legend_node->textContent);



        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        //$content = $html->find('div#AccountsIndex',0);
        $nodes=$xpath->query("//div[@id='AccountsIndex']//a");
        //$this->assertTrue($nodes->length==1);
        $content_node=$nodes->item(0);
        //$this->assertNotNull($content);
        //$unknownATag = count($content->find('a'));
        $unknownATag = $nodes->length;

        // 4. Look for the create new account link
        //$this->assertEquals(1, count($html->find('a#AccountAdd')));
        $nodes=$xpath->query("//a[@id='AccountAdd']");
        $this->assertTrue($nodes->length==1);
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        //$table = $html->find('table#AccountsTable',0);
        //$this->assertNotNull($table);
        $nodes=$xpath->query("//table[@id='AccountsTable']");
        $this->assertTrue($nodes->length==1);
        $table_node=$nodes->item(0);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        //$thead = $table->find('thead',0);
        //$thead_ths = $thead->find('tr th');
        //$this->assertEquals($thead_ths[0]->id, 'category');
        //$this->assertEquals($thead_ths[1]->id, 'sort');
        //$this->assertEquals($thead_ths[2]->id, 'title');
        //$this->assertEquals($thead_ths[3]->id, 'actions');
        $column_header_nodes=$xpath->query("thead/tr/th",$table_node);
        //$column_count = count($thead_ths);
        $this->assertEquals($column_header_nodes->length,4); // no other columns
        $nodes=$xpath->query("thead/tr/th[1][@id='category']",$table_node);
        $this->assertTrue($nodes->length==1);
        $nodes=$xpath->query("thead/tr/th[2][@id='sort']",$table_node);
        $this->assertTrue($nodes->length==1);
        $nodes=$xpath->query("thead/tr/th[3][@id='title']",$table_node);
        $this->assertTrue($nodes->length==1);
        $nodes=$xpath->query("thead/tr/th[4][@id='actions']",$table_node);
        $this->assertTrue($nodes->length==1);

        // 7. Ensure that the tbody section has the correct quantity of rows.
        $dbRecords=$this->Accounts->find()
            ->contain(['Categories'])
            ->where(['book_id'=>$book_id])
            ->order(['category_id','sort']);
        //$tbody = $table->find('tbody',0);
        //$n1=$table->innertext;
        //$n2=$tbody->innertext;
        //$tbody_rows = $tbody->find('tr');
        //$this->assertEquals(count($tbody_rows), $dbRecords->count());
        $tbody_nodes=$xpath->query("tbody/tr",$table_node);
        //$tbody_rows=$nodes->item(0);
        $this->assertTrue($tbody_nodes->length==$dbRecords->count());

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($dbRecords->execute()->fetchAll('assoc')));
        $iterator->attachIterator(new \ArrayIterator(iterator_to_array($tbody_nodes)));
        //$iterator->attachIterator($tbody_nodes);

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            //$htmlRow = $values[1];
            $row_node = $values[1];
            $column_nodes=$xpath->query("td",$row_node);
            //$htmlColumns = $htmlRow->find('td');

            // 9.0 title
            //$this->assertEquals($fixtureRecord['Categories__title'],  $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['Categories__title'],  $column_nodes->item(0)->textContent);
            $this->assertEquals($fixtureRecord['Accounts__sort'], $column_nodes->item(1)->textContent);
            $this->assertEquals($fixtureRecord['Accounts__title'], $column_nodes->item(2)->textContent);

            // 9.1 Now examine the action links
            //$td = $htmlColumns[3];
            $action_nodes=$xpath->query("a",$column_nodes->item(3));
            $this->assertTrue($action_nodes->length==2);
            //$actionLinks = $td->find('a');

            $nodes=$xpath->query("a[@name='AccountView']",$column_nodes->item(3));
            $this->assertEquals($nodes->length,1);
            $unknownATag--;

            $nodes=$xpath->query("a[@name='AccountEdit']",$column_nodes->item(3));
            $this->assertEquals($nodes->length,1);
            $unknownATag--;

            //$this->assertEquals('AccountView', $actionLinks[0]->name);
            //$this->assertEquals('AccountView', $action_nodes->item(0)->name);
            //$unknownATag--;
            //$this->assertEquals('AccountEdit', $action_nodes->item(1)->name);
            //$unknownATag--;
            //$this->assertEquals('AccountDelete', $actionLinks[2]->name);
            //$unknownATag--;

            // 9.9 No other columns
            $this->assertEquals($column_nodes->length,$column_header_nodes->length);
        }

        // 10. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
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
        //$html=str_get_html($this->_response->body());
        $dom = \DomDocument::loadHtml($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 3. Verify the <A> tags
        // 3.1 Get the count of all <A> tags that are presently unaccounted for.
        //$content = $html->find('div#AccountsView',0);
        //$this->assertNotNull($content);
        //$unknownATag = count($content->find('a'));
        // 2. Verify the existence of the A tags on the portion of this
        // page generated by this controller (the layout is tested separately).
        // There should be zero links.
        $nodes=$xpath->query("//div[@id='AccountsView']//a");
        //$this->assertTrue($nodes->length==0);
        $unknownATagCnt=$nodes->length;

        // 3.2 Look for specific tags
        //$this->assertEquals(1, count($html->find('a#AccountDistributions')));
        $nodes=$xpath->query("//div[@id='AccountsView']//a[@id='AccountDistributions']");
        $this->assertEquals($nodes->length,1);
        $unknownATagCnt--;

        // 3.3. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);

        // 4.  Look for the table that contains the view fields.
        //$table = $html->find('table#AccountViewTable',0);
        $table_nodes=$xpath->query("//div[@id='AccountsView']//table[@id='AccountViewTable']");
        $this->assertEquals($nodes->length,1);
        //$this->assertNotNull($table);

        // 5. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $row_nodes=$xpath->query("//tr",$table_nodes);
        //$unknownRowCnt = count($table->find('tr'));
        $unknownRowCnt=$row_nodes->length;

        // 5.1 book_title
        $field = $table->find('tr#book_title td',0);
        $this->assertEquals($book['title'], $field->plaintext);
        $unknownRowCnt--;

        // 5.2 category_title
        $field = $table->find('tr#category_title td',0);
        $this->assertEquals($category['title'], $field->plaintext);
        $unknownRowCnt--;

        // 5.3 sort
        $field = $table->find('tr#sort td',0);
        $this->assertEquals($account['sort'], $field->plaintext);
        $unknownRowCnt--;

        // 5.4 title
        $field = $table->find('tr#title td',0);
        $this->assertEquals($account['title'], $field->plaintext);
        $unknownRowCnt--;

        // 5.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);
    }
}

