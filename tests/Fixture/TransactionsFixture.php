<?php
namespace App\Test\Fixture;

class TransactionsFixture extends DMFixture {
    public $import = ['table' => 'transactions'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newTransactionRecord = [
        'book_id' => FixtureConstants::bookTypical,
        'note' => 'Bust ghosts',
        'tran_datetime' => [
            'year' => '2016',
            'month' => '12',
            'day' => '15',
            'hour' => '10',
            'minute' => '30'
        ]
    ];

    public function init() {
        $this->tableName='Transactions';
        parent::init(); // This is where the records are loaded.
    }
}
