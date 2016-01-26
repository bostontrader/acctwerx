<?php
namespace App\Controller;

class AccountsController extends AppController {

    const ACCOUNT_SAVED = "The account has been saved.";
    const ACCOUNT_NOT_SAVED = "The account could not be saved. Please, try again.";
    const ACCOUNT_DELETED = "The account has been deleted.";
    const CANNOT_DELETE_ACCOUNT = "The account could not be deleted. Please, try again.";

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $account = $this->Accounts->newEntity();
        if ($this->request->is('post')) {
            $account = $this->Accounts->patchEntity($account, $this->request->data);
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__(self::ACCOUNT_SAVED));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__(self::ACCOUNT_NOT_SAVED));
            }
        }
        $books = $this->Accounts->Books->find('list');
        $this->set(compact('account','books'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $account = $this->Accounts->get($id);
        if ($this->Accounts->delete($account)) {
            $this->Flash->success(__(self::ACCOUNT_DELETED));
        } else {
            $this->Flash->error(__(self::CANNOT_DELETE_ACCOUNT));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $account = $this->Accounts->get($id);
        if ($this->request->is(['put'])) {
            $account = $this->Accounts->patchEntity($account, $this->request->data);
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__(self::ACCOUNT_SAVED));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__(self::ACCOUNT_NOT_SAVED));
            }
        }
        $books = $this->Accounts->Books->find('list');
        $this->set(compact('account','books'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('accounts', $this->Accounts->find()->contain('Books')->order(['book_id', 'sort']));
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $account = $this->Accounts->get($id,['contain'=>'Books']);
        $this->set('account', $account);
    }
}
