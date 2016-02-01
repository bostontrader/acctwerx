<?php
namespace App\Test\Fixture;

class CategoriesFixture extends DMFixture {
    public $import = ['table' => 'categories'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newCategoryRecord = ['title' => 'A new kind of category'];

    public function init() {
        $this->tableName='Categories';
        parent::init(); // This is where the records are loaded.
    }
}