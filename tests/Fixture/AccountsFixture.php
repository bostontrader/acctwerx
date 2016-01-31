<?php
namespace App\Test\Fixture;

class AccountsFixture extends DMFixture {
    public $import = ['table' => 'accounts'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newAccountRecord = ['book_id' => FixtureConstants::bookTypical,'sort' => 10,'title' => 'Ghost Busting'];

    public function init() {
        $this->tableName='Accounts';
        parent::init(); // This is where the records are loaded.
    }
}
