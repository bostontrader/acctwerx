<?php
namespace App\Controller;

use Cake\Network\Exception\BadRequestException;

class DistributionsController extends AppController {

    const DISTRIBUTION_SAVED = "The distribution has been saved.";
    const DISTRIBUTION_NOT_SAVED = "The distribution could not be saved. Please, try again.";
    const DNC = "That does not compute";
    const DISTRIBUTION_DELETED = "The distribution has been deleted.";
    const CANNOT_DELETE_DISTRIBUTION = "The distribution could not be deleted. Please, try again.";

    // GET | POST /books/:book_id/transactions/:transaction_id/distributions/add
    public function add() {
        $this->request->allowMethod(['get', 'post']);

        // Get the transaction and transaction_id.
        $transaction_id=$this->get_transaction_id($this->request->params);
        //$transaction=$this->Distributions->Transactions->get($transaction_id);
        $book_id=$this->get_book_id($this->request->params);
        //$book=$this->Distributions->Transactions->Books->get($book_id);

        $distribution = $this->Distributions->newEntity(['contain'=>'transactions']);
        if ($this->request->is('post')) {
            $distribution = $this->Distributions->patchEntity($distribution, $this->request->data);
            if ($this->Distributions->save($distribution)) {
                $this->Flash->success(__(self::DISTRIBUTION_SAVED));
                return $this->redirect(['book_id' => $book_id, 'transaction_id' => $transaction_id, 'action' => 'index', '_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::DISTRIBUTION_NOT_SAVED));
            }
        }
        $accounts = $this->Distributions->Accounts->find('list');
        $this->set(compact('accounts','distribution','transaction_id'));
        return null;
    }

    //public function delete($id = null) {
    //$this->request->allowMethod(['post', 'delete']);
    //$distribution = $this->Distributions->get($id);
    //if ($this->Distributions->delete($distribution)) {
    //$this->Flash->success(__(self::DISTRIBUTION_DELETED));
    //} else {
    //$this->Flash->error(__(self::CANNOT_DELETE_DISTRIBUTION));
    //}
    //return $this->redirect(['action' => 'index']);
    //}

    // GET | POST /books/:book_id/transactions/:transaction_id/distributions/edit/:id
    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);

        // Get the transaction and transaction_id.
        $transaction_id=$this->get_transaction_id($this->request->params);
        //$transaction=$this->Distributions->Transactions->get($transaction_id);
        $book_id=$this->get_book_id($this->request->params);

        $distribution = $this->Distributions->get($id);
        if ($this->request->is(['put'])) {
            $distribution = $this->Distributions->patchEntity($distribution, $this->request->data);
            if ($this->Distributions->save($distribution)) {
                $this->Flash->success(__(self::DISTRIBUTION_SAVED));
                return $this->redirect(['book_id' => $book_id,'transaction_id' => $transaction_id,'action' => 'index','_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::DISTRIBUTION_NOT_SAVED));
            }
        }
        $accounts = $this->Distributions->Accounts->find('list');
        $this->set(compact('accounts','distribution','transaction_id'));
        return null;
    }

    // GET /books/:book_id/transactions/:transaction_id/distributions
    public function index() {

        $book_id=$this->get_book_id($this->request->params);
        $transaction_id=$this->get_transaction_id($this->request->params);
        //$transaction=$this->Distributions->Transactions->get($transaction_id);

        $this->request->allowMethod(['get']);
        $this->set(
            'distributions', $this->Distributions->find()
                ->contain('Accounts')
            ->where(['transaction_id'=>$transaction_id])
            //->order(['datetime'])
        );
        $this->set(compact('book_id','transaction_id'));
    }

    // GET /books/:book_id/transactions/:transaction_id/distributions/:id
    public function view($id = null) {

        $this->request->allowMethod(['get']);

        //$transaction_id=$this->get_transaction_id($this->request->params);

        $distribution = $this->Distributions->get($id,['contain'=>'Accounts']);
        $this->set('distribution', $distribution);
        //$this->set('transaction_id',$transaction_id);
    }

    // The actions in this controller should only be accessible in the context of a
    // book and transaction, as passed by appropriate routing.
    private function get_transaction_id($params) {
        if (array_key_exists('transaction_id', $params)) return $params['transaction_id'];
        throw new BadRequestException(self::DNC);
    }

    private function get_book_id($params) {
        if (array_key_exists('book_id', $params)) return $params['book_id'];
        throw new BadRequestException(self::DNC);
    }
}
