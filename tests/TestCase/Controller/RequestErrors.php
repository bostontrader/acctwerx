<?php
namespace App\Test\TestCase\Controller\Accounts;

use App\Test\Fixture\FixtureConstants;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;

/**
 * Class RequestErrors
 * @package App\Test\TestCase\Controller\Accounts
 * 
 * This test class will attempt to submit a variety of defective requests to the various
 * methods in order to verify their correct response.
 * 
 * One critical part of this testing is to ensure that _all_ URLs that can get to 
 * a method (without being weeded out earlier by Cake routing or earlier elements of the stack)
 * are tested.  This requires some means ensuring that all routes that Cake knows about are tested.
 *
 * This was remarkably difficult to figure out.  The available documentation is poor and the actual
 * process, as carved into source code, is complicated.
 * 
 * My first attempt involved:
 * 
 * 1. Obtain a static array of all routes via Router::routes.
 * 
 * 2. During the test for each particular method, find the route in my array,
 * and remove it.
 * 
 * 3. At the end of the testing, if there are any routes left, then I missed 'em.
 * 
 * The basic problem was that it simply proved too difficult to distinguish between the various
 * routes.  This is obiously possible because routing can do it, but _I_ could not.  Taming this
 * shrew was taking too much time so it was time for Plan B.
 * 
 * Plan B depends upon the assumption that each route has a globally unique name. Oddly enough,
 * I cannot ask a route "what's ur name" and get a straight answer, but I can say "given this name,
 * what's the URL?" I think this is a rather shakey foundation, but it enables me to use the following
 * method:
 * 
 * 1. Count the routes.
 * 
 * 2. For each route tested, determine the URL.  If I can do that, then decrement the counter.
 * 
 * 3. At the end of testing, if the counter > 0, then I missed some routes.
 *
 */
class RequestErrors extends IntegrationTestCase {

    private static $routes;
    //
    // Each route must have a unique name.
    private static $routeCnt;

    public static function setupBeforeClass() {
        static::$routeCnt=count(Router::routes());
        static::$routes=Router::routes();
    }

    public static function tearDownAfterClass() {
        $i=0;
    }

    //
    // Given an array of Route, examine each of them to see if
    // they "match" $url.  If so, increment the counter and remove the Route
    // from the array. Return the total count of Routes that match.
    //private function countAndRemoveMatchingRoutes($url) {
    private function countAndRemoveMatchingRoutes($name,$method) {

        $u=Router::url(['_name'=>'boooks:adddd']);
        $cnt=0;
        $idx=0;
        foreach(static::$routes as $key=>$r) {
            $t1=$r->getName();

            if (!isset($r->defaults['_method']))
                $r->defaults['_method']='GET';
            $t2=$r->defaults['_method'];
            
            $t3=$r->compile();
            //$t4=$r->url($url);
            $t5=$r->match();
            if($t1==$name&&$t2==$method) {
            //if($r->parse($url)) {
                unset(static::$routes[$key]);
                $cnt++;
            }
            $idx++;
        }
        return $cnt;
    }
    
    //
    // Given an array of Route, examine each of them to see if
    // they "match" $url.  If so, increment the counter and remove the Route
    // from the array. Return the total count of Routes that match.
    private function countAndRemoveMatchingRoutesB($url) {
        //private function countAndRemoveMatchingRoutes($name,$method) {
        //$u=Router::url(['_name'=>'boooks:adddd']);
        //$cnt=0;
        //$idx=0;
        //foreach(static::$routes as $key=>$r) {
            //$t1=$r->getName();
        
            //if (!isset($r->defaults['_method']))
                //$r->defaults['_method']='GET';
            //$t2=$r->defaults['_method'];
        
            //$t3=$r->compile();
            //$t4=$r->url($url);
            //$t5=$r->match();
            //if($t1==$name&&$t2==$method) {
                //if($r->parse($url)) {
                //unset(static::$routes[$key]);
                //$cnt++;
            //}
            //$idx++;
        //}
        //return $cnt;
    }

    // Given a $urlBase, send a GET request with a query string parameter.
    // Assertion error if not caught properly.
    private function noQueryStringParametersAllowed($urlBase, $verb='get') {
        $this->{$verb}("$urlBase?catfood=yum");
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
    private function makeGETErrors($url,$verb,$expectedResponseCode,$route_name) {
        $this->noQueryStringParametersAllowed($url);
        $this->assertEquals(1,$this->countAndRemoveMatchingRoutesB($route_name,'GET'));
        $this->tryVerbWeKnowShouldFail($url,$verb,$expectedResponseCode);
    }

    // Which errors should we try to create for methods that accept GET?
    private function makeGETErrorsB($url,$verb,$expectedResponseCode,$route_name) {
        $this->assertEquals(1,$this->countAndRemoveMatchingRoutesB($route_name,'GET'));
        $this->noQueryStringParametersAllowed($url);
        $this->tryVerbWeKnowShouldFail($url,$verb,$expectedResponseCode);
    }

    // Which errors should we try to create for methods that accept POST?
    private function makePOSTErrors($url,$route_name) {
        $this->noQueryStringParametersAllowed($url,'post');
        $this->assertEquals($this->countAndRemoveMatchingRoutes($route_name,'POST'),1); // only one route
        //$this->tryVerbWeKnowShouldFail($url,$verb,$expectedResponseCode); already tested
    }

    public function testDeleteMe() {

        //$this->assertEquals($this->countAndRemoveMatchingRoutes('books:delete','DELETE'),1); // only one route
        //$this->assertEquals($this->countAndRemoveMatchingRoutes('accounts:delete','DELETE'),1); // only one route
        //$this->assertEquals($this->countAndRemoveMatchingRoutes('transactions:delete','DELETE'),1); // only one route
        //$this->assertEquals($this->countAndRemoveMatchingRoutes('categories:delete','DELETE'),1); // only one route
        //$this->assertEquals($this->countAndRemoveMatchingRoutes('currencies:delete','DELETE'),1); // only one route

        //$this->assertEquals($this->countAndRemoveMatchingRoutes('books:edit',['PUT','PATCH']),1); // only one route
        //$this->assertEquals($this->countAndRemoveMatchingRoutes('accounts:edit',['PUT','PATCH']),1); // only one route
        //$this->assertEquals($this->countAndRemoveMatchingRoutes('transactions:edit',['PUT','PATCH']),1); // only one route
        //$this->assertEquals($this->countAndRemoveMatchingRoutes('categories:edit',['PUT','PATCH']),1); // only one route
        //$this->assertEquals($this->countAndRemoveMatchingRoutes('currencies:edit',['PUT','PATCH']),1); // only one route
    }

    //public function testPOST_Books_add() {$this->makePOSTErrors("/books/add",'books:add');}
    //public function testPOST_BooksAccounts_add() {$this->makePOSTErrors("/books/1/accounts/add",'accounts:add');}
    //public function testPOST_BooksTransactions_add() {$this->makePOSTErrors("/books/1/transactions/add",'transactions:add');}
    //public function testPOST_Categories_add() {$this->makePOSTErrors("/categories/add",'categories:add');}
    //public function testPOST_Currencies_add() {$this->makePOSTErrors("/currencies/add",'currencies:add');}

    // Because these errors are triggered before any records are actually read, we can just
    // hardwire record ids (to pass routing) and fuggedabout referential integrity.
    public function testGET_Books_add() {$this->makeGETErrors("/books/add",'put',405,'books:addd');}
    public function testGET_BooksAccounts_add() {$this->makeGETErrors("/books/1/accounts/add",'put',405,'accounts:add');}
    //public function testGET_BooksTransactions_add() {$this->makeGETErrors("/books/1/transactions/add",'put',405,'transactions:add');}
    //public function testGET_BooksAccountsDistributions_add() {$this->makeGETErrors("/books/1/accounts/1/distributions/add",'put',405,'distributions:add');}
    //public function testGET_BooksTransactionsDistributions_add() {$this->makeGETErrors("/books/1/transactions/1/distributions/add",'put',405,'');}
    //public function testGET_Categories_add() {$this->makeGETErrors("/categories/add",'put',405,'categories:add');}
    //public function testGET_Currencies_add() {$this->makeGETErrors("/currencies/add",'put',405,'currencies:add');}

    //public function testGET_Books_edit() {$this->makeGETErrors("/books/edit/1",'post',405,'books:edit');}
    //public function testGET_BooksAccounts_edit() {$this->makeGETErrors("/books/1/accounts/edit/1",'post',405,'accounts:edit');}
    //public function testGET_BooksTransactions_edit() {$this->makeGETErrors("/books/1/transactions/edit/1",'post',405,'transactions:edit');}
    //public function testGET_BooksAccountsDistributions_edit() {$this->makeGETErrors("/books/1/accounts/1/distributions/edit/1",'post',405);}
    //public function testGET_BooksTransactionsDistributions_edit() {$this->makeGETErrors("/books/1/transactions/1/distributions/edit/1",'post',405);}
    //public function testGET_Categories_edit() {$this->makeGETErrors("/categories/edit/1",'post',405,'categories:edit');}
    //public function testGET_Currencies_edit() {$this->makeGETErrors("/currencies/edit/1",'post',405,'currencies:edit');}

    //public function testGET_Books_index() {$this->makeGETErrors("/books",'put',404,'books:index');}
    //public function testGET_BooksAccounts_index() {$this->makeGETErrors("/books/1/accounts",'put',404,'accounts:index');}
    //public function testGET_BooksTransactions_index() {$this->makeGETErrors("/books/1/transactions",'put',404,'transactions:index');}
    //public function testGET_BooksAccountsDistributions_index() {$this->makeGETErrors("/books/1/accounts/1/distributions",'put',404);}
    //public function testGET_BooksTransactionsDistributions_index() {$this->makeGETErrors("/books/1/transactions/1/distributions",'put',404);}
    //public function testGET_Categories_index() {$this->makeGETErrors("/categories",'put',404,'categories:index');}
    //public function testGET_Currencies_index() {$this->makeGETErrors("/currencies",'put',404,'currencies:index');}

    //public function testGET_Books_view() {$this->makeGETErrors("/books/1",'post',404,'books:view');}
    //public function testGET_BooksAccounts_view() {$this->makeGETErrors("/books/1/accounts/1",'post',404,'accounts:view');}
    //public function testGET_BooksTransactions_view() {$this->makeGETErrors("/books/1/transactions/1",'post',404,'transactions:view');}
    //public function testGET_BooksAccountsDistributions_view() {$this->makeGETErrors("/books/1/accounts/1/distributions/1",'post',404);}
    //public function testGET_BooksTransactionsDistributions_view() {$this->makeGETErrors("/books/1/transactions/1/distributions/1",'post',404);}
    //public function testGET_Categories_view() {$this->makeGETErrors("/categories/1",'post',404,'categories:view');}
    //public function testGET_Currencies_view() {$this->makeGETErrors("/currencies/1",'post',404,'currencies:view');}

    //public function testGET_Books_balance() {$this->makeGETErrors("/books/balance/1",'put',405,'books:balance');}
    //public function testGET_Books_graph_bank() {$this->makeGETErrors("/books/graph_bank/1",'put',405,'books:graph_bank');}
    //public function testGET_Books_graph_cash() {$this->makeGETErrors("/books/graph_cash/1",'put',405,'books:graph_cash');}
    //public function testGET_Books_income() {$this->makeGETErrors("/books/income/1",'put',405,'books:income');}

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
