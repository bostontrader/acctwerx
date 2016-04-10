<?php
namespace App\Model\Table;
use Cake\ORM\Table;

class AccountsCategoriesTable extends Table {
    public function initialize(array $config) {
        $this->belongsTo('Accounts');
        $this->belongsTo('Categories');
    }
}