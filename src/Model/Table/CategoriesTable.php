<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class CategoriesTable extends Table {

    public function initialize(array $config) {
        parent::initialize($config);

        $this->displayField('title');
        $this->belongsToMany('Account',
            [
                'through' => 'AccountsCategories',
                'alias' => 'Accounts',
                'foreignKey' => 'category_id',
                'joinTable' => 'accounts_categories',
                'targetForeignKey' => 'account_id'
            ]
        );
    }

}
