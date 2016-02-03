<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class DistributionsTable extends Table {

    public function initialize(array $config) {
        parent::initialize($config);

        $this->displayField('title');

        $this->belongsTo('Accounts');
        $this->belongsTo('Transactions');
    }
}

