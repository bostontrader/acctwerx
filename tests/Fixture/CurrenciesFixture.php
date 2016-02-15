<?php
namespace App\Test\Fixture;

class CurrenciesFixture extends DMFixture {
    public $import = ['table' => 'currencies'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newCurrencyRecord = ['title'=>'A new kind of currency','symbol'=>'NCX'];

    public function init() {
        $this->tableName='Currencies';
        parent::init(); // This is where the records are loaded.
    }
}