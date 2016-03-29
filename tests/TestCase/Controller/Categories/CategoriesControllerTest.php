<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\CategoriesFixture;
use Cake\ORM\TableRegistry;

class CategoriesControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.categories'
    ];

    /** @var \Cake\ORM\Table */
    private $Categories;

    /** @var \App\Test\Fixture\CategoriesFixture */
    private $categoriesFixture;

    public function setUp() {
        parent::setUp();
        $this->Categories = TableRegistry::get('Categories');
        $this->categoriesFixture = new CategoriesFixture();
    }

    public function testGET_add() {

        // 1. GET the url and parse the response.
        $this->get('/categories/add');
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='CategoriesAdd']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;
        $this->assertEquals($unknownATagCnt,0);

        // 4. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='CategoryAddForm']",$content_node);

        // 5. Now inspect the legend of the form.
        $this->assertContains("Add Category",$this->getTheOnlyOne($xpath,"//legend",$form_node)->textContent);

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
        $this->assertTrue($xpath->query("//input[@id='CategoryTitle' and @type='text' and not(@value)]",$form_node)->length==1);
        $unknownInputCnt--;

        // 6.4 Ensure that there's an input field for symbol, of type text, and that it is empty
        $this->assertTrue($xpath->query("//input[@id='CategorySymbol' and @type='text' and not(@value)]",$form_node)->length==1);
        $unknownInputCnt--;

        // 7. Have all the input and selects been accounted for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);
    }

    public function testPOST_add() {

        // 1. POST a suitable record to the url, observe redirection, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->categoriesFixture->newCategoryRecord;
        $fromDbRecord=$this->genericPOSTAddProlog(
            null, // no login
            '/categories/add', $fixtureRecord,
            '/categories', $this->Categories
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['title'],$fixtureRecord['title']);
        $this->assertEquals($fromDbRecord['symbol'],$fixtureRecord['symbol']);
    }

    //public function testDELETE() {
    //$this->deletePOST(
    //null, // no login
    //'/categories/delete/',
    //FixtureConstants::bookTypical, '/categories', $this->Categories
    //);
    //}

    public function testGET_edit() {

        // 1. Obtain a record to edit, GET the url, and parse the response.
        $category_id=FixtureConstants::categoryTypical;
        $category=$this->Categories->get($category_id);
        $this->get("/categories/edit/$category_id");
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='CategoriesEdit']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;
        $this->assertEquals($unknownATagCnt,0);

        // 4. Ensure that the expected form exists
        $form_node=$this->getTheOnlyOne($xpath,"//form[@id='CategoryEditForm']",$content_node);

        // 5. Now inspect the legend of the form.
        $this->assertContains("Edit Category",$this->getTheOnlyOne($xpath,"//legend",$form_node)->textContent);

        // 6. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 6.1 These are counts of the select and input fields on the form.  They
        // are presently unCategoryed for.
        $unknownSelectCnt=$xpath->query("//select",$form_node)->length;
        $unknownInputCnt=$xpath->query("//input",$form_node)->length;

        // 6.2 Look for the hidden PUT input.
        $this->assertEquals($xpath->query("//input[@type='hidden' and @name='_method' and @value='PUT']",$form_node)->length,1);
        $unknownInputCnt--;

        // 6.3 Ensure that there's an input field for title, of type text, that is correctly set
        $this->assertTrue($xpath->query("//input[@id='CategoryTitle' and @type='text' and @value='$category->title']",$form_node)->length==1);
        $unknownInputCnt--;

        // 6.4 Ensure that there's an input field for symbol, of type text, that is correctly set
        $this->assertTrue($xpath->query("//input[@id='CategorySymbol' and @type='text' and @value='$category->symbol']",$form_node)->length==1);
        $unknownInputCnt--;

        // 7. Have all the input and selects been Categoryed for?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);
    }

    public function testPOST_edit() {

        // 1. Obtain the relevant records.
        $category_id=FixtureConstants::categoryTypical;
        $categoryNew=$this->categoriesFixture->newCategoryRecord;

        // 2. POST a suitable record to the url, observe the redirect, and parse the response.
        $baseUrl="/categories";
        $this->put("$baseUrl/$category_id", $categoryNew);
        $this->assertResponseCode(302);
        $this->assertRedirect($baseUrl);

        // 3. Now retrieve that 1 record and validate it.
        $fromDbRecord=$this->Categories->get($category_id);
        $this->assertEquals($fromDbRecord['title'],$categoryNew['title']);
    }

    public function testGET_index() {

        // 1. Submit request, examine response, observe no redirect, and parse the response.
        $this->get("/categories");
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 2. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='CategoriesIndex']");

        // 3. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;

        // 4. Look for the create new category link
        $this->getTheOnlyOne($xpath,"//a[@id='CategoryAdd']",$content_node);
        $unknownATagCnt--;

        // 5. Ensure that there is a suitably named table to display the results.
        $table_node=$this->getTheOnlyOne($xpath,"//table[@id='CategoriesTable']",$content_node);

        // 6. Now inspect the heading of the table.
        $this->getTheOnlyOne($xpath,"//header[contains(text(),'Categories')]",$content_node);

        // 7. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $column_header_nodes=$xpath->query("thead/tr/th",$table_node);
        $this->assertEquals($column_header_nodes->length,3); // no other columns

        $this->getTheOnlyOne($xpath,"thead/tr/th[1][@id='title']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[2][@id='symbol']",$table_node);
        $this->getTheOnlyOne($xpath,"thead/tr/th[3][@id='actions']",$table_node);

        // 8. Ensure that the tbody section has the correct quantity of rows.
        $dbRecords=$this->Categories->find()
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

            $this->assertEquals($fixtureRecord['Categories__title'],  $column_nodes->item(0)->textContent);
            $this->assertEquals($fixtureRecord['Categories__symbol'],  $column_nodes->item(1)->textContent);

            // 9.1 Now examine the action links
            $action_nodes=$xpath->query("a",$column_nodes->item(2));
            $this->assertTrue($action_nodes->length==2);

            $this->getTheOnlyOne($xpath,"a[@name='CategoryView']",$column_nodes->item(2));
            $unknownATagCnt--;

            $this->getTheOnlyOne($xpath,"a[@name='CategoryEdit']",$column_nodes->item(2));
            $unknownATagCnt--;

            // 9.9 No other columns
            $this->assertEquals($column_nodes->length,$column_header_nodes->length);
        }

        // 10. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);
    }

    public function testGET_view() {

        // 1. Obtain the relevant records.
        $category_id=FixtureConstants::categoryTypical;
        $category=$this->Categories->get($category_id);

        // 2. Submit request, examine response, observe no redirect, and parse the response.
        $this->get("/categories/$category_id");
        $this->assertResponseCode(200);
        $this->assertNoRedirect();
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        // 3. Isolate the content produced by this controller method (excluding the layout.)
        $content_node=$this->getTheOnlyOne($xpath,"//div[@id='CategoriesView']");

        // 4. Count the A tags.
        $unknownATagCnt=$xpath->query(".//a",$content_node)->length;

        // 4.9 Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATagCnt);

        // 5. Ensure that there is a suitably named table to display the results.
        $table_node=$this->getTheOnlyOne($xpath,"//table[@id='CategoryViewTable']",$content_node);

        // 6. Now inspect the fields in the table.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt=$xpath->query("//tr",$table_node)->length;

        // 6.1 title
        $this->getTheOnlyOne($xpath,"//tr[1][@id='title']/td[text()='$category->title']",$table_node);
        $unknownRowCnt--;

        // 6.2 symbol
        $this->getTheOnlyOne($xpath,"//tr[2][@id='symbol']/td[text()='$category->symbol']",$table_node);
        $unknownRowCnt--;

        // 6.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);
    }
}

