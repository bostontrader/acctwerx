<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class AccountsTable extends Table {

    public function initialize(array $config) {
        parent::initialize($config);

        $this->displayField('title');
        $this->belongsTo('Books');
        $this->hasMany('Distributions');

        $this->belongsToMany('Categories',
            [
                'through' => 'AccountsCategories',
                'alias' => 'Categories',
                'foreignKey' => 'account_id',
                'joinTable' => 'accounts_categories',
                'targetForeignKey' => 'category_id'
            ]
        );
    }

    public function validationDefault(Validator $v) {
        $v->notEmpty('title');
        //$v->requirePresence('title');
        return $v;
    }
}

