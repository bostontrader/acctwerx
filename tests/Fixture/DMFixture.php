<?php
namespace App\Test\Fixture;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\Fixture\TestFixture;

class DMFixture extends TestFixture {

    //protected $joinTableName;
    protected $tableName;
    //protected $order;

    // Given an id, return the first fixture record found with that id, or null if not found.
    public function get($id) {
        foreach ($this->records as $record)
            if ($record['id'] == $id) return $record;
        return null;
    }

    // We obtain the records to use for this fixture by reading them from the 'dev' db
    public function init() {

        parent::init();

        // 1. Not all fixtures want to use the dev db.
        if(is_null($this->tableName)) return;

        // 2. We need to do this to ensure that the tables really do use the connection to the
        // dev db.
        TableRegistry::remove($this->tableName);
        //$d=ConnectionManager::get('dev',false); // no alias
        $table = TableRegistry::get($this->tableName,['connection'=>ConnectionManager::get('dev',false)]); // no alias

        //if(!is_null($this->joinTableName)) {
            //TableRegistry::remove($this->joinTableName);
            //TableRegistry::get($this->joinTableName, ['connection' => ConnectionManager::get('dev')]);
        //}

        // 3. Now build the query
        /** @var \Cake\Datasource\ConnectionInterface $c */
        $query=new Query(ConnectionManager::get('dev',false),$table); // no alias
        $query->find('all');
        //if(!is_null($this->order)) $query->order($this->order);
        //if(!is_null($this->joinTableName)) $query->leftJoin($this->joinTableName,'semesters.id = sections.semester_id');

        // 4. Copy the records
        /* @var \Cake\ORM\Entity $record */
        foreach($query as $record) {
            $this->records[]=$record->toArray();
        }

        // 5. Do this again to ensure that the table uses the 'test' connection.
        TableRegistry::remove($this->tableName);
        TableRegistry::get($this->tableName,['connection'=>ConnectionManager::get('test')]);

        //if(!is_null($this->joinTableName)) {
            //TableRegistry::remove($this->joinTableName);
            //TableRegistry::get($this->joinTableName, ['connection' => ConnectionManager::get('test')]);
        //}
    }
}
