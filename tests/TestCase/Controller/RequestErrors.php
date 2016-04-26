<?php
namespace App\Test\TestCase\Controller\Accounts;

use App\Test\Fixture\FixtureConstants;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;

class RequestErrors extends IntegrationTestCase {

    private static $routes;

    public static function setupBeforeClass() {
        static::$routes=Router::routes();
    }

    public static function tearDownAfterClass() {
        $i=0;
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

    // Which http verbs are acceptable to the various methods? This is controlled
    // by routing and by allowMethod within methods.
    //
    // An action should only respond to a very specific white-list of verbs.  Everything else
    // is some kind of error.  Sometimes a request using the wrong verb will not get through routing
    // and will cause a MissingRouteException. If the request _does_ get through routing, then
    // it should be snagged with allowMethod and cause a 404, or 405 response.
    //
    // We don't want to try every possible invalid verb.  That's just too tedious. Instead,
    // send one invalid verb to test that we can catch it.
    private function tryVerbWeKnowShouldFail($urlBase, $verb, $expectedResponseCode) {
        $this->{$verb}("$urlBase");
        $this->assertNoRedirect();
        $this->assertResponseCode($expectedResponseCode); // method not allowed
    }

    // Which errors should we try to create for methods that accept GET?
    private function makeGETErrors($url,$verb,$expectedResponseCode) {
        $this->noQueryStringParametersAllowed($url);
        $this->assertEquals($this->countAndRemoveMatchingRoutes($url),1); // only one route
        $this->tryVerbWeKnowShouldFail($url,$verb,$expectedResponseCode);
    }

    // Because these errors are triggered before any records are actually read, we can just
    // hardwire record ids (to pass routing) and fuggedabout referential integrity.
    public function testGET_Books_add() {$this->makeGETErrors("/books/add",'put',405);}
    public function testGET_BooksAccounts_add() {$this->makeGETErrors("/books/1/accounts/add",'put',405);}
    public function testGET_BooksTransactions_add() {$this->makeGETErrors("/books/1/transactions/add",'put',405);}
    public function testGET_BooksAccountsDistributions_add() {$this->makeGETErrors("/books/1/accounts/1/distributions/add",'put',405);}
    public function testGET_BooksTransactionsDistributions_add() {$this->makeGETErrors("/books/1/transactions/1/distributions/add",'put',405);}
    public function testGET_Categories_add() {$this->makeGETErrors("/categories/add",'put',405);}
    public function testGET_Currencies_add() {$this->makeGETErrors("/currencies/add",'put',405);}

    public function testGET_Books_edit() {$this->makeGETErrors("/books/edit/1",'post',405);}
    public function testGET_BooksAccounts_edit() {$this->makeGETErrors("/books/1/accounts/edit/1",'post',405);}
    public function testGET_BooksTransactions_edit() {$this->makeGETErrors("/books/1/transactions/edit/1",'post',405);}
    public function testGET_BooksAccountsDistributions_edit() {$this->makeGETErrors("/books/1/accounts/1/distributions/edit/1",'post',405);}
    public function testGET_BooksTransactionsDistributions_edit() {$this->makeGETErrors("/books/1/transactions/1/distributions/edit/1",'post',405);}
    public function testGET_Categories_edit() {$this->makeGETErrors("/categories/edit/1",'post',405);}
    public function testGET_Currencies_edit() {$this->makeGETErrors("/currencies/edit/1",'post',405);}

    public function testGET_Books_index() {$this->makeGETErrors("/books",'put',404);}
    public function testGET_BooksAccounts_index() {$this->makeGETErrors("/books/1/accounts",'put',404);}
    public function testGET_BooksTransactions_index() {$this->makeGETErrors("/books/1/transactions",'put',404);}
    public function testGET_BooksAccountsDistributions_index() {$this->makeGETErrors("/books/1/accounts/1/distributions",'put',404);}
    public function testGET_BooksTransactionsDistributions_index() {$this->makeGETErrors("/books/1/transactions/1/distributions",'put',404);}
    public function testGET_Categories_index() {$this->makeGETErrors("/categories",'put',404);}
    public function testGET_Currencies_index() {$this->makeGETErrors("/currencies",'put',404);}

    public function testGET_Books_view() {$this->makeGETErrors("/books/1",'post',404);}
    public function testGET_BooksAccounts_view() {$this->makeGETErrors("/books/1/accounts/1",'post',404);}
    public function testGET_BooksTransactions_view() {$this->makeGETErrors("/books/1/transactions/1",'post',404);}
    public function testGET_BooksAccountsDistributions_view() {$this->makeGETErrors("/books/1/accounts/1/distributions/1",'post',404);}
    public function testGET_BooksTransactionsDistributions_view() {$this->makeGETErrors("/books/1/transactions/1/distributions/1",'post',404);}
    public function testGET_Categories_view() {$this->makeGETErrors("/categories/1",'post',404);}
    public function testGET_Currencies_view() {$this->makeGETErrors("/currencies/1",'post',404);}

    public function testGET_Books_balance() {$this->makeGETErrors("/books/balance/1",'put',405);}
    public function testGET_Books_graph_bank() {$this->makeGETErrors("/books/graph_bank/1",'put',405);}
    public function testGET_Books_graph_cash() {$this->makeGETErrors("/books/graph_cash/1",'put',405);}
    public function testGET_Books_income() {$this->makeGETErrors("/books/income/1",'put',405);}

    /*public function testPOST_add() {

        $book_id=FixtureConstants::bookTypical;
        $urlBase="/books/$book_id/accounts/add";
        //$this->assertEquals($this->countAndRemoveMatchingRoutes($urlBase),1); // only one route

        // 1. Verb not (GET or POST) (already tested)

        // 2. No query string parameters allowed.
        $this->post("$urlBase?catfood=yum");
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();

        // 3. No extra POST variables allowed.
        $this->post("$urlBase",['catfood'=>'yum']);
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();

        // 5. Try to trigger validation errors.
        $this->post("$urlBase",[]); // no validation errors, just cannot save
        $this->post("$urlBase",['book_id'=>$book_id]); // no errors, but cannot save

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
    }*/

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
