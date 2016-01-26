<?php
namespace App\Controller;

class TransactionsController extends AppController {

    const TRANSACTION_SAVED = "The transaction has been saved.";
    const TRANSACTION_NOT_SAVED = "The transaction could not be saved. Please, try again.";
    const TRANSACTION_DELETED = "The transaction has been deleted.";
    const CANNOT_DELETE_TRANSACTION = "The transaction could not be deleted. Please, try again.";

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $transaction = $this->Transactions->newEntity();
        if ($this->request->is('post')) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->data);
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__(self::TRANSACTION_SAVED));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__(self::TRANSACTION_NOT_SAVED));
            }
        }
        $books = $this->Transactions->Books->find('list');
        $this->set(compact('transaction','books'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $transaction = $this->Transactions->get($id);
        if ($this->Transactions->delete($transaction)) {
            $this->Flash->success(__(self::TRANSACTION_DELETED));
        } else {
            $this->Flash->error(__(self::CANNOT_DELETE_TRANSACTION));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $transaction = $this->Transactions->get($id);
        if ($this->request->is(['put'])) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->data);
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__(self::TRANSACTION_SAVED));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__(self::TRANSACTION_NOT_SAVED));
            }
        }
        $books = $this->Transactions->Books->find('list');
        $this->set(compact('transaction','books'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('transactions', $this->Transactions->find()->contain('Books'));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $transaction = $this->Transactions->get($id,['contain'=>'Books']);
        $this->set('transaction', $transaction);
    }
}
