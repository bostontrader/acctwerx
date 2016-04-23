<?php
namespace App\Test\TestCase\Controller\Accounts;

//use App\Controller\AccountsController;
use App\Test\Fixture\FixtureConstants;
//use App\Test\Fixture\AccountsFixture;
//use App\Test\TestCase\Controller\DMIntegrationTestCase;
//use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;

class RequestErrors extends IntegrationTestCase {

    public $fixtures = [
        //'app.accounts',
        //'app.accounts_categories',
        //'app.categories',
        //'app.books',
    ];

    /** @var \Cake\ORM\Table */
    //private $Accounts;

    /** @var \Cake\ORM\Table */
    //private $Books;

    private static $routes;

    public static function setupBeforeClass() {
        static::$routes=Router::routes();
    }

    public static function tearDownAfterClass() {
        $i=0;
    }

    public function setUp() {
        //parent::setUp();
        //$this->Accounts = TableRegistry::get('Accounts');
        //$this->Books = TableRegistry::get('Books');
    }

    //
    // Given an array of Route, examine each of them to see if
    // they "match" $url.  If so, increment the counter and remove the Route
    // from the array. Return the total count of Routes that match.
    private function countAndRemoveMatchingRoutes($url) {
        $cnt=0;
        foreach(static::$routes as $key=>$r) {
            if($r->parse($url)) {
                unset(static::$routes[$key]);
                $cnt++;
            }
        }
        return $cnt;
    }

    // Given a $urlBase, send a GET request with a query string parameter.
    // Assertion error if not caught properly.
    private function noQueryStringParametersAllowed($urlBase) {
        $this->get("$urlBase?catfood=yum");
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();
    }

    public function testGET_Books_add() {

        $urlBase="/books/add";
        $this->assertEquals($this->countAndRemoveMatchingRoutes($urlBase),1); // only one route

        // 1. There is no record_id referential integrity issue here with this test.

        $this->noQueryStringParametersAllowed($urlBase); // 2

        // 3. The verb not be (GET or POST)
        $this->put("$urlBase");
        $this->assertResponseCode(405); // method not allowed
        $this->assertNoRedirect();
    }

    public function testGET_BooksAccounts_add() {

        $book_id=FixtureConstants::bookTypical;
        $urlBase="/books/$book_id/accounts/add";
        $this->assertEquals($this->countAndRemoveMatchingRoutes($urlBase),1); // only one route

        // 1. There is no record_id referential integrity issue here with this test.

        $this->noQueryStringParametersAllowed($urlBase); // 2

        // 3. The verb not be (GET or POST)
        $this->put("$urlBase");
        $this->assertResponseCode(405); // method not allowed
        $this->assertNoRedirect();
    }

    public function testGET_BooksTransactions_add() {

        $book_id=FixtureConstants::bookTypical;
        $urlBase="/books/$book_id/transactions/add";
        $this->assertEquals($this->countAndRemoveMatchingRoutes($urlBase),1); // only one route

        // 1. There is no record_id referential integrity issue here with this test.

        $this->noQueryStringParametersAllowed($urlBase); // 2

        // 3. The verb not be (GET or POST)
        $this->put("$urlBase");
        $this->assertResponseCode(405); // method not allowed
        $this->assertNoRedirect();
    }

    public function testGET_BooksTransactionsDistributions_add() {

        $book_id=FixtureConstants::bookTypical;
        $transaction_id=FixtureConstants::transactionTypical;
        $urlBase="/books/$book_id/transactions/$transaction_id/distributions/add";
        $this->assertEquals($this->countAndRemoveMatchingRoutes($urlBase),1); // only one route

        // 1. There is no record_id referential integrity issue here with this test.

        $this->noQueryStringParametersAllowed($urlBase); // 2

        // 3. The verb not be (GET or POST)
        $this->put("$urlBase");
        $this->assertResponseCode(405); // method not allowed
        $this->assertNoRedirect();
    }

    public function testGET_Categories_add() {

        $urlBase="/categories/add";
        $this->assertEquals($this->countAndRemoveMatchingRoutes($urlBase),1); // only one route

        // 1. There is no record_id referential integrity issue here with this test.

        $this->noQueryStringParametersAllowed($urlBase); // 2

        // 3. The verb not be (GET or POST)
        $this->put("$urlBase");
        $this->assertResponseCode(405); // method not allowed
        $this->assertNoRedirect();
    }

    public function testGET_Currencies_add() {

        $urlBase="/currencies/add";
        $this->assertEquals($this->countAndRemoveMatchingRoutes($urlBase),1); // only one route

        // 1. There is no record_id referential integrity issue here with this test.

        $this->noQueryStringParametersAllowed($urlBase); // 2

        // 3. The verb not be (GET or POST)
        $this->put("$urlBase");
        $this->assertResponseCode(405); // method not allowed
        $this->assertNoRedirect();
    }


    public function testPOST_add() {

        $book_id=FixtureConstants::bookTypical;
        $baseURL="/books/$book_id/accounts/add";
        // 1. Verb not (GET or POST) (already tested)

        // 2. No query string parameters allowed.
        $this->post("$baseURL?catfood=yum");
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();

        // 3. No extra POST variables allowed.
        $this->post("$baseURL",['catfood'=>'yum']);
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();

        // 5. Try to trigger validation errors.
        $this->post("$baseURL",[]); // no validation errors, just cannot save
        $this->post("$baseURL",['book_id'=>$book_id]); // no errors, but cannot save

        //$this->post("$baseURL",['title'=>null]); // error caught
        //$this->post("$baseURL",['book_id'=>$book_id]); // no errors, but cannot save



        //$this->assertResponseCode(400); // bad request
        //$this->assertNoRedirect();
        //_method
        //POST
        //book_id
        //1
        //categories[_ids]
        //title
        //a

    }

    public function testGET_edit() {

        // 1. Obtain the relevant records and verify their referential integrity.
        $book_id=FixtureConstants::bookTypical;
        $account_id=FixtureConstants::accountTypical;
        //$account=$this->Accounts->get($account_id,['contain'=>'Categories']);
        //$book_id=FixtureConstants::bookTypical;
        //$book=$this->Books->get($book_id);
        //$this->assertEquals($account['book_id'],$book['id']);

        // 1. Verb not (GET or PUT)
        $this->post("/books/$book_id/accounts/edit/$account_id");
        $this->assertResponseCode(405); // method not allowed
        $this->assertNoRedirect();

        // 2. No query string parameters
        $this->get("/books/$book_id/accounts/add?catfood=yum");
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();

        // 3. Because this is a GET, there will be no POST variables.
    }

    public function testPOST_edit()
    {

        // 1. Obtain the relevant records and verify their referential integrity.
        $account_id = FixtureConstants::accountTypical;
        //$accountNew=$this->accountsFixture->newAccountRecord;
        $book_id = FixtureConstants::bookTypical;
        //$book=$this->Books->get($book_id);
        //$this->assertEquals($accountNew['book_id'],$book['id']);

        // 1. Verb not (GET or PUT) (already tested)

        // 2. No query string parameters
        $this->put("/books/$book_id/accounts/edit/$account_id?catfood=yum");
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();

        // 3. No extra POST variables
        $this->put("/books/$book_id/accounts/edit/$account_id", ['catfood' => 'yum']);
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();
    }

    public function testGET_index() {

        $book_id=FixtureConstants::bookTypical;
        $baseURL="/books/$book_id/accounts";

        // 1. Verb not GET
        // Routing snags this
        //$this->put($baseURL);
        //$this->assertResponseCode(405); // method not allowed
        //$this->assertNoRedirect();

        // 2. No query string parameters
        $this->get("$baseURL?catfood=yum");
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();

        // 3. Because this is a GET, there will be no POST variables.
    }

    /*public function testGET_view() {

        // Obtain the relevant records and verify their referential integrity.
        // We probably don't really need to verify refernetial integrity, but why taunt fate?
        // Let's just do it. 
        $account_id=FixtureConstants::accountTypical;
        $account=$this->Accounts->get($account_id);
        $book_id=FixtureConstants::bookTypical;
        $book=$this->Books->get($book_id);
        $this->assertEquals($account['book_id'],$book['id']);

        $baseURL="/books/$book_id/accounts/$account_id";

        // 1. Verb not GET
        // Routing snags this
        //$this->put($baseURL);
        //$this->assertResponseCode(405); // method not allowed
        //$this->assertNoRedirect();

        // 2. No query string parameters
        $this->get("$baseURL?catfood=yum");
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();

        // 3. Because this is a GET, there will be no POST variables.

    }*/

}
