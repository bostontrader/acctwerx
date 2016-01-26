<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class TransactionsTable extends Table {

    public function initialize(array $config) {
        parent::initialize($config);

        $this->displayField('title');

        $this->belongsTo('Books');
    }
}

