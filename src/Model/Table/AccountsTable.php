<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class AccountsTable extends Table {

    public function initialize(array $config) {
        parent::initialize($config);

        $this->displayField('title');

        $this->belongsTo('Books');
        $this->belongsTo('Categories');
        $this->hasMany('Distributions');
    }
}

