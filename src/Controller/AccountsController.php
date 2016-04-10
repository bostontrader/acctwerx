<?php
namespace App\Controller;

use Cake\Network\Exception\BadRequestException;

class AccountsController extends AppController {

    const ACCOUNT_SAVED = "The account has been saved.";
    const ACCOUNT_NOT_SAVED = "The account could not be saved. Please, try again.";
    const DNC = "That does not compute";
    const ACCOUNT_DELETED = "The account has been deleted.";
    const CANNOT_DELETE_ACCOUNT = "The account could not be deleted. Please, try again.";

    // GET | POST /books/:book_id/accounts/add
    public function add() {
        $this->request->allowMethod(['get', 'post']);

        // Get the book and book_id.
        $book_id=$this->get_book_id($this->request->params);
        $book=$this->Accounts->Books->get($book_id);

        $account = $this->Accounts->newEntity(['contain'=>'books']);
        if ($this->request->is('post')) {
            $account = $this->Accounts->patchEntity($account, $this->request->data);
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__(self::ACCOUNT_SAVED));
                return $this->redirect(['action' => 'index','book_id' => $book_id,'_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::ACCOUNT_NOT_SAVED));
            }
        }
        $categories = $this->Accounts->Categories->find('list');
        $this->set(compact('account','book','categories'));
        return null;
    }

    //public function delete($id = null) {
        //$this->request->allowMethod(['post', 'delete']);
        //$account = $this->Accounts->get($id);
        //if ($this->Accounts->delete($account)) {
            //$this->Flash->success(__(self::ACCOUNT_DELETED));
        //} else {
            //$this->Flash->error(__(self::CANNOT_DELETE_ACCOUNT));
        //}
        //return $this->redirect(['action' => 'index']);
    //}

    // GET | POST /books/:book_id/accounts/edit/:id
    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);

        // Get the book and book_id.
        $book_id=$this->get_book_id($this->request->params);
        $book=$this->Accounts->Books->get($book_id);
        //$user = $this->Users->find()->where(['id'=>$id])->contain('Roles')->first();

        $account = $this->Accounts->get($id,['contain'=>'Categories']);
        if ($this->request->is(['put'])) {
            $account = $this->Accounts->patchEntity($account, $this->request->data);
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__(self::ACCOUNT_SAVED));
                return $this->redirect(['action' => 'index','book_id' => $book_id,'_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::ACCOUNT_NOT_SAVED));
            }
        }
        $categories = $this->Accounts->Categories->find('list');
        $this->set(compact('account','book','categories'));
        return null;
    }

    // GET /books/:book_id/accounts
    public function index() {

        $book_id=$this->get_book_id($this->request->params);
        $book=$this->Accounts->Books->get($book_id);

        $this->request->allowMethod(['get']);
        $this->set(
            'accounts', $this->Accounts->find()
                ->contain(['Books','Categories'])
                ->where(['book_id'=>$book_id])
                ->order(['Accounts.title']));
        $this->set(compact('book'));
    }

    // GET /books/:book_id/accounts/:id
    public function view($id = null) {

        $this->request->allowMethod(['get']);

        $book_id=$this->get_book_id($this->request->params);
        $book=$this->Accounts->Books->get($book_id);

        $account = $this->Accounts->get($id,['contain'=>['Books','Categories']]);
        $this->set(compact('account','book'));
    }

    // The actions in this controller should only be accessible in the context of a book,
    // as passed by appropriate routing.
    private function get_book_id($params) {
        if (array_key_exists('book_id', $params)) return $params['book_id'];
        throw new BadRequestException(self::DNC);
    }
}
