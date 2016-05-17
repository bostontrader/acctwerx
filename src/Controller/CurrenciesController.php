<?php
namespace App\Controller;
use Cake\Network\Exception\BadRequestException;

class CurrenciesController extends AppController {

    const CURRENCY_SAVED = "The currency has been saved.";
    const CURRENCY_NOT_SAVED = "The currency could not be saved. Please, try again.";
    const CURRENCY_DELETED = "The currency has been deleted.";
    const CANNOT_DELETE_CURRENCY = "The currency could not be deleted. Please, try again.";

    // POST /currencies
    public function add() {
        $this->request->allowMethod(['post']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $currency = $this->{'Currencies'}->newEntity();
        $currency = $this->{'Currencies'}->patchEntity($currency, $this->request->data);
        if ($this->{'Currencies'}->save($currency)) {
            $this->Flash->{'success'}(__(self::CURRENCY_SAVED));
            return $this->redirect(['controller'=>'currencies','action' => 'index','_method'=>'GET']);
        } else {
            $this->Flash->{'error'}(__(self::CURRENCY_NOT_SAVED));
        }
        return null;
    }

    // GET /currencies/newform
    public function newform() {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $currency = $this->{'Currencies'}->newEntity();
        $this->set(compact('currency'));
    }

    public function delete($id = null) {
        $this->request->allowMethod(['delete']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);
        
        $currency = $this->{'Currencies'}->get($id);
        if ($this->{'Currencies'}->delete($currency)) {
            //$this->Flash->{'success'}(__(self::CURRENCY_DELETED));
        } else {
            //$this->Flash->{'error'}(__(self::CANNOT_DELETE_CURRENCY));
        }
        //return $this->redirect(['action' => 'index']);
    }

    // PUT /currencies/:id
    public function edit($id = null) {
        $this->request->allowMethod(['put']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $currency = $this->{'Currencies'}->get($id);
        $currency = $this->{'Currencies'}->patchEntity($currency, $this->request->data);
        if ($this->{'Currencies'}->save($currency)) {
            $this->Flash->{'success'}(__(self::CURRENCY_SAVED));
            return $this->redirect(['controller'=>'currencies','action' => 'index','_method'=>'GET']);
        } else {
            $this->Flash->{'error'}(__(self::CURRENCY_NOT_SAVED));
        }
        return null;
    }

    // There's something wrong with my routing because no $id param is passed.
    // But I can find it as $this->request->params['id']
    // GET /currencies/:id/editform
    public function editform() {
        $id=$this->request->params['id'];
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $currency = $this->{'Currencies'}->get($id);
        $this->set(compact('currency'));
    }

    // GET /currencies
    public function index() {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $this->set('currencies', $this->{'Currencies'}->find());
    }

    // GET /currencies/:id
    public function view($id = null) {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $currency = $this->{'Currencies'}->get($id);
        $this->set('currency', $currency);
    }
}
