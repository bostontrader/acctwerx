<?php
namespace App\Test\Fixture;

class BooksFixture extends DMFixture {
    public $import = ['table' => 'books'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newBookRecord = ['title' => 'Ghost Busters'];

    public function init() {
        $this->tableName='Books';
        parent::init(); // This is where the records are loaded.
    }
}