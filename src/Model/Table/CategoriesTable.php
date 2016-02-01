<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class CategoriesTable extends Table {

    public function initialize(array $config) {
        parent::initialize($config);

        $this->displayField('title');
        $this->hasMany('Accounts');
    }

}
