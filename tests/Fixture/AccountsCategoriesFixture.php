<?php
namespace App\Test\Fixture;

class AccountsCategoriesFixture extends DMFixture {
    public $import = ['table' => 'accounts_categories'];

    public function init() {
        $this->tableName='AccountsCategories';
        parent::init(); // This is where the records are loaded.
    }
}
