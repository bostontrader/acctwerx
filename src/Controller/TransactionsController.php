<?php
namespace App\Controller;

use Cake\Network\Exception\BadRequestException;

class TransactionsController extends AppController {

    const TRANSACTION_SAVED = "The transaction has been saved.";
    const TRANSACTION_NOT_SAVED = "The transaction could not be saved. Please, try again.";
    const DNC = "That does not compute";
    const TRANSACTION_DELETED = "The transaction has been deleted.";
    const CANNOT_DELETE_TRANSACTION = "The transaction could not be deleted. Please, try again.";

    // GET | POST /books/:book_id/transactions/add
    public function add() {
        $this->request->allowMethod(['get', 'post']);

        // Get the book and book_id.
        $book_id=$this->get_book_id($this->request->params);
        $book=$this->Transactions->Books->get($book_id);

        $transaction = $this->Transactions->newEntity(['contain'=>'books']);
        if ($this->request->is('post')) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->data);
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__(self::TRANSACTION_SAVED));
                return $this->redirect(['action' => 'index','book_id' => $book_id,'_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::TRANSACTION_NOT_SAVED));
            }
        }
        $this->set(compact('transaction','book'));
        return null;
    }

    //public function delete($id = null) {
    //$this->request->allowMethod(['post', 'delete']);
    //$transaction = $this->Transactions->get($id);
    //if ($this->Transactions->delete($transaction)) {
    //$this->Flash->success(__(self::TRANSACTION_DELETED));
    //} else {
    //$this->Flash->error(__(self::CANNOT_DELETE_TRANSACTION));
    //}
    //return $this->redirect(['action' => 'index']);
    //}

    // GET | POST /books/:book_id/transactions/edit/:id
    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);

        // Get the book and book_id.
        $book_id=$this->get_book_id($this->request->params);
        $book=$this->Transactions->Books->get($book_id);

        $transaction = $this->Transactions->get($id);
        if ($this->request->is(['put'])) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->data);
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__(self::TRANSACTION_SAVED));
                return $this->redirect(['action' => 'index','book_id' => $book_id,'_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::TRANSACTION_NOT_SAVED));
            }
        }
        $this->set(compact('transaction','book'));
        return null;
    }

    // GET /books/:book_id/transactions
    public function index() {

        $book_id=$this->get_book_id($this->request->params);
        $book=$this->Transactions->Books->get($book_id);

        $this->request->allowMethod(['get']);
        $this->set(
            'transactions', $this->Transactions->find()
            ->contain('Books')
            ->where(['book_id'=>$book_id])
            ->order(['datetime']));
        $this->set(compact('book','book_id'));
    }

    // GET /books/:book_id/transactions/:id
    public function view($id = null) {

        $this->request->allowMethod(['get']);

        $book_id=$this->get_book_id($this->request->params);

        $transaction = $this->Transactions->get($id,['contain'=>'Books']);
        $this->set('transaction', $transaction);
        $this->set('book_id',$book_id);
    }

    // The actions in this controller should only be accessible in the context of a book,
    // as passed by appropriate routing.
    private function get_book_id($params) {
        if (array_key_exists('book_id', $params)) return $params['book_id'];
        throw new BadRequestException(self::DNC);
    }
}
