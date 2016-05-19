<?php
namespace App\Test\TestCase\Controller\Accounts;

use App\Test\TestCase\Controller\DMIntegrationTestCase;
use Cake\Routing\Router;

/**
 * Class RequestErrorsB
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
 *
 */
class RequestErrors extends DMIntegrationTestCase {

    /** @var \Cake\Routing\Route\Route[] $routes */
    private static $routes;

    public static function setupBeforeClass() {
        static::$routes=Router::routes();
    }

    public static function tearDownAfterClass() {
        $n=count(static::$routes);
        if($n>0) throw new \Exception("$n routes have not been tested.");
    }


    //
    // Given an array of Route, examine each of them to see if
    // they "match" $url.  If so, increment the counter and remove the Route
    // from the array. Return the total count of Routes that match.
    // "Match" means that the string form of the $url matches the regex
    // as provided by the route, and that the http $verb also is the same as expected
    // by the route.
    private function countAndRemoveMatchingRoutes($url, $verb='GET') {
        $cnt=0;
        $idx=0;

        foreach(static::$routes as $key=>$r) {

            if (!isset($r->defaults['_method']))
                $r->defaults['_method']='GET';

            $path_match=preg_match($r->compile(), $url, $matches);
            $verb_match=$r->defaults['_method']==$verb;
            if($path_match && $verb_match) {
                unset(static::$routes[$key]);
                $cnt++;
            }
            $idx++;
        }
        return $cnt;
    }

    // Given a $urlBase, send a GET request with a query string parameter.
    // Assertion error if not caught properly.
    private function noQueryStringParametersAllowed($urlBase, $verb='get') {
        $this->{$verb}("$urlBase?catfood=yum");
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();
    }

    //
    // Which http verbs are acceptable to the various methods? This is controlled
    // by routing and by allowMethod within methods.  In all cases, a dis-allowed
    // verb should be blocked by routing and produce a MissingRouteException.  But
    // we can't actually catch that exception.  We can only look for the error message
    // in the html response.
    //
    // We cannot practically test that allowMethod is actually working, because routing
    // should prevent requests with dis-allowed verbs from ever reaching a controller method.
    // Nevertheless, as with the 2nd amendent, allowMethod is there as a last ditch safety mechanism.
    //
    // We don't want to try every possible invalid verb.  That's just too tedious. Instead,
    // send one invalid verb to test that we can catch it.
    private function tryVerbWeKnowShouldFail($urlBase, $verb) {
        $this->{$verb}("$urlBase");

        // Now parse the response and look for the expected exception type.
        $dom = new \DomDocument();
        $dom->loadHTML($this->_response->body());
        $xpath=new \DomXPath($dom);

        $n1=$this->getTheOnlyOne($xpath,"//span[@class='header-type']");
        $n2=$n1->textContent;
        $this->assertEquals($n2,"Cake\Routing\Exception\MissingRouteException");
    }

    // Which errors should we try to create for methods that accept GET?
    private function makeGETErrors($url,$expected_bad_verb) {
        $this->assertEquals(1,$this->countAndRemoveMatchingRoutes($url,'GET'));
        $this->noQueryStringParametersAllowed($url);
        $this->tryVerbWeKnowShouldFail($url,$expected_bad_verb);
    }

    // Which errors should we try to create for methods that accept POST?
    private function makePOSTErrors($url,$expected_bad_verb) {
        $this->assertEquals($this->countAndRemoveMatchingRoutes($url,'POST'),1);
        $this->noQueryStringParametersAllowed($url,'post');
        $this->tryVerbWeKnowShouldFail($url,$expected_bad_verb);
    }

    // Which errors should we try to create for methods that accept PUT?
    private function makePUTErrors($url,$expected_bad_verb) {
        $this->assertEquals($this->countAndRemoveMatchingRoutes($url,'PUT'),1);
        $this->noQueryStringParametersAllowed($url,'put');
        $this->tryVerbWeKnowShouldFail($url,$expected_bad_verb);
    }

    // Which errors should we try to create for methods that accept DELETE?
    private function makeDELETEErrors($url,$expected_bad_verb) {
        $this->assertEquals($this->countAndRemoveMatchingRoutes($url,'DELETE'),1);
        $this->noQueryStringParametersAllowed($url,'delete');
        $this->tryVerbWeKnowShouldFail($url,$expected_bad_verb);
    }

    // Because these errors are triggered before any records are actually read, we can just
    // hardwire record ids (to pass routing) and fuggedabout referential integrity.
    public function testGET_Accounts_index() {$this->makeGETErrors("/books/1/accounts",'put');}
    public function testPOST_Accounts_add() {$this->makePOSTErrors("/books/1/accounts",'put');}
    public function testGET_Accounts_view() {$this->makeGETErrors("/books/1/accounts/1",'post');}
    public function testPUT_Accounts_update() {$this->makePUTErrors("/books/1/accounts/1",'post');}
    public function testDELETE_Accounts_delete() {$this->makeDELETEErrors("/books/1/accounts/1",'post');}
    public function testGET_Accounts_newform() {$this->makeGETErrors("/books/1/accounts/newform",'put');}
    public function testGET_Accounts_editform() {$this->makeGETErrors("/books/1/accounts/1/editform",'put');}

    public function testGET_Books_index() {$this->makeGETErrors("/books",'put');}
    public function testPOST_Books_add() {$this->makePOSTErrors("/books",'put');}
    public function testGET_Books_view() {$this->makeGETErrors("/books/1",'post');}
    public function testPUT_Books_update() {$this->makePUTErrors("/books/1",'post');}
    public function testDELETE_Books_delete() {$this->makeDELETEErrors("/books/1",'post');}
    public function testGET_Books_newform() {$this->makeGETErrors("/books/newform",'put');}
    public function testGET_Books_editform() {$this->makeGETErrors("/books/1/editform",'put');}

    public function testGET_Categories_index() {$this->makeGETErrors("/categories",'put');}
    public function testPOST_Categories_add() {$this->makePOSTErrors("/categories",'put');}
    public function testGET_Categories_view() {$this->makeGETErrors("/categories/1",'post');}
    public function testPUT_Categories_update() {$this->makePUTErrors("/categories/1",'post');}
    public function testDELETE_Categories_delete() {$this->makeDELETEErrors("/categories/1",'post');}
    public function testGET_Categories_newform() {$this->makeGETErrors("/categories/newform",'put');}
    public function testGET_Categories_editform() {$this->makeGETErrors("/categories/1/editform",'put');}

    public function testGET_Currencies_index() {$this->makeGETErrors("/currencies",'put');}
    public function testPOST_Currencies_add() {$this->makePOSTErrors("/currencies",'put');}
    public function testGET_Currencies_view() {$this->makeGETErrors("/currencies/1",'post');}
    public function testPUT_Currencies_update() {$this->makePUTErrors("/currencies/1",'post');}
    public function testDELETE_Currencies_delete() {$this->makeDELETEErrors("/currencies/1",'post');}
    public function testGET_Currencies_newform() {$this->makeGETErrors("/currencies/newform",'put');}
    public function testGET_Currencies_editform() {$this->makeGETErrors("/currencies/1/editform",'put');}

    public function testGET_DistributionsAccounts_index() {$this->makeGETErrors("/books/1/accounts/1/distributions",'put');}
    public function testPOST_DistributionsAccounts_add() {$this->makePOSTErrors("/books/1/accounts/1/distributions",'put');}
    public function testGET_DistributionsAccounts_view() {$this->makeGETErrors("/books/1/accounts/1/distributions/1",'post');}
    public function testPUT_DistributionsAccounts_update() {$this->makePUTErrors("/books/1/accounts/1/distributions/1",'post');}
    public function testDELETE_DistributionsAccounts_delete() {$this->makeDELETEErrors("/books/1/accounts/1/distributions/1",'post');}
    public function testGET_DistributionsAccounts_newform() {$this->makeGETErrors("/books/1/accounts/1/distributions/newform",'put');}
    //public function testGET_DistributionsAccounts_editform() {$this->makeGETErrors("/books/1/accounts/1/distributions/1/editform",'put');}

    public function testGET_Distributions_index() {$this->makeGETErrors("/books/1/transactions/1/distributions",'put');}
    public function testPOST_Distributions_add() {$this->makePOSTErrors("/books/1/transactions/1/distributions",'put');}
    public function testGET_Distributions_view() {$this->makeGETErrors("/books/1/transactions/1/distributions/1",'post');}
    public function testPUT_Distributions_update() {$this->makePUTErrors("/books/1/transactions/1/distributions/1",'post');}
    public function testDELETE_Distributions_delete() {$this->makeDELETEErrors("/books/1/transactions/1/distributions/1",'post');}
    public function testGET_Distributions_newform() {$this->makeGETErrors("/books/1/transactions/1/distributions/newform",'put');}
    public function testGET_Distributions_editform() {$this->makeGETErrors("/books/1/transactions/1/distributions/1/editform",'put');}

    public function testGET_Transactions_index() {$this->makeGETErrors("/books/1/transactions",'put');}
    public function testPOST_Transactions_add() {$this->makePOSTErrors("/books/1/transactions",'put');}
    public function testGET_Transactions_view() {$this->makeGETErrors("/books/1/transactions/1",'post');}
    public function testPUT_Transactions_update() {$this->makePUTErrors("/books/1/transactions/1",'post');}
    public function testDELETE_Transactions_delete() {$this->makeDELETEErrors("/books/1/transactions/1",'post');}
    public function testGET_Transactions_newform() {$this->makeGETErrors("/books/1/transactions/newform",'put');}
    public function testGET_Transactions_editform() {$this->makeGETErrors("/books/1/transactions/1/editform",'put');}

    // Now some routes outside of CRUD world
    public function testGET_Pages_view() {$this->makeGETErrors("/",'put');}

    public function testGET_Books_balance() {$this->makeGETErrors("/books/1/balance",'put');}
    public function testGET_Books_graph_bank() {$this->makeGETErrors("/books/1/graph_bank",'put');}
    public function testGET_Books_graph_cash() {$this->makeGETErrors("/books/1/graph_cash",'put');}
    public function testGET_Books_income() {$this->makeGETErrors("/books/1/income",'put');}

}
