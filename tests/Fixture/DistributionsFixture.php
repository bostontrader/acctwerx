<?php
namespace App\Test\Fixture;

class DistributionsFixture extends DMFixture {
    public $import = ['table' => 'distributions'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newDistributionRecord = [
        'transaction_id' => FixtureConstants::transactionTypical,
        'account_id' => FixtureConstants::accountTypical,
        'amount' => 500,
        'currency_id' => FixtureConstants::currencyTypical,
    ];

    public function init() {
        $this->tableName='Distributions';
        parent::init(); // This is where the records are loaded.
    }
}
