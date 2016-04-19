<?php
namespace App\Test\TestCase\Controller\Accounts;

//use App\Controller\AccountsController;
use App\Test\Fixture\FixtureConstants;
//use App\Test\Fixture\AccountsFixture;
use App\Test\TestCase\Controller\DMIntegrationTestCase;
use Cake\ORM\TableRegistry;

class Errors extends DMIntegrationTestCase {

    public $fixtures = [
        'app.accounts',
        'app.accounts_categories',
        'app.categories',
        'app.books',
    ];

    /** @var \Cake\ORM\Table */
    private $Accounts;

    /** @var \Cake\ORM\Table */
    private $Books;
 
    public function setUp() {
        parent::setUp();
        $this->Accounts = TableRegistry::get('Accounts');
        $this->Books = TableRegistry::get('Books');
    }

    //
    // GET add
    //
    // What errors are we looking for?  How can we detect them?
    //
    // Several of the errors return 400 bad request.  In the event that two of these
    // errors might arise in a single method, then we cannot be certain that a
    // particular error really is the cause of the 400 bad request. (maybe the other error
    // occurred.)  But we _can_ send back a message and test for that if necessary.
    //
    // But that's not necessary. The user never creates URLs and the only way we'll see
    // these errors is if somebody's messin' with the URL. If so, providing a user-friendly
    // UI would be the least of our concerns.
    //
    // 1. Each method has a small white-list of acceptable http verbs. Attempt access
    // with another verb. Return 405 Method not allowed.
    //
    // 2. Neither GET nor PUT should have any query params ($this->request->query).
    // 400. Bad request.
    //
    // 3. POST should have a very specific list of POST variables.  Try to send an extra.
    // 400. Bad request.
    //
    // 4. PUT should have a very specific list of POST variables.  Try to send an extra.
    // 400. Bad request.
    //
    // 5. Trigger validation errors.
    //
    // These errors will be detected by Cake. Let's trust its functionality.
    //
    // 1. GET, POST, or PUT should all have a book_id in their URLs.
    //
    // 2. The book_id should refer to an existant book.  If the book_id is non-numeric,
    // it refers to a non-existant book.
    //
    // 3. Extra stuff at the end of the URL.
    public function testGET_add() {

        $book_id=FixtureConstants::bookTypical;

        // 1. Verb not (GET or POST)
        $this->put("/books/$book_id/accounts/add");
        $this->assertResponseCode(405); // method not allowed
        $this->assertNoRedirect();

        // 2. No query string parameters
        $this->get("/books/$book_id/accounts/add?catfood=yum");
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();

        // 3. Because this is a GET, there will be no POST variables.
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
        $this->post("$baseURL",[]);
        $this->assertResponseCode(400); // bad request
        $this->assertNoRedirect();
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

    public function testGET_view() {

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

    }

}
