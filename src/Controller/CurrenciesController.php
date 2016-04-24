<?php
namespace App\Controller;
use Cake\Network\Exception\BadRequestException;

class CurrenciesController extends AppController {

    const CURRENCY_SAVED = "The currency has been saved.";
    const CURRENCY_NOT_SAVED = "The currency could not be saved. Please, try again.";
    const CURRENCY_DELETED = "The currency has been deleted.";
    const CANNOT_DELETE_CURRENCY = "The currency could not be deleted. Please, try again.";

    public function add() {
        $this->request->allowMethod(['get','post']);

        // Neither GET nor POST should accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $currency = $this->Currencies->newEntity();
        if ($this->request->is('post')) {
            $currency = $this->Currencies->patchEntity($currency, $this->request->data);
            if ($this->Currencies->save($currency)) {
                $this->Flash->success(__(self::CURRENCY_SAVED));
                return $this->redirect(['controller'=>'currencies','action' => 'index','_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::CURRENCY_NOT_SAVED));
            }
        }
        $this->set(compact('currency'));
        return null;
    }

    //public function delete($id = null) {
        //$this->request->allowMethod(['post', 'delete']);
        //$currency = $this->Currencies->get($id);
        //if ($this->Currencies->delete($currency)) {
            //$this->Flash->success(__(self::CURRENCY_DELETED));
        //} else {
            //$this->Flash->error(__(self::CANNOT_DELETE_CURRENCY));
        //}
        //return $this->redirect(['action' => 'index']);
    //}

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $currency = $this->Currencies->get($id);
        if ($this->request->is(['put'])) {
            $currency = $this->Currencies->patchEntity($currency, $this->request->data);
            if ($this->Currencies->save($currency)) {
                $this->Flash->success(__(self::CURRENCY_SAVED));
                return $this->redirect(['controller'=>'currencies','action' => 'index','_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::CURRENCY_NOT_SAVED));
            }
        }
        $this->set(compact('currency'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $this->set('currencies', $this->Currencies->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $currency = $this->Currencies->get($id);
        $this->set('currency', $currency);
    }
}
