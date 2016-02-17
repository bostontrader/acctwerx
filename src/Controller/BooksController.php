<?php
namespace App\Controller;
use Cake\Datasource\ConnectionManager;

class BooksController extends AppController {

    const BOOK_SAVED = "The book has been saved.";
    const BOOK_NOT_SAVED = "The book could not be saved. Please, try again.";
    const BOOK_DELETED = "The book has been deleted.";
    const CANNOT_DELETE_BOOK = "The book could not be deleted. Please, try again.";

    public function initialize() {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function add() {
        $this->request->allowMethod(['get','post']);
        $book = $this->Books->newEntity();
        if ($this->request->is('post')) {
            $book = $this->Books->patchEntity($book, $this->request->data);
            if ($this->Books->save($book)) {
                $this->Flash->success(__(self::BOOK_SAVED));
                return $this->redirect(['controller'=>'books','action' => 'index','_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::BOOK_NOT_SAVED));
            }
        }
        $this->set(compact('book'));
        return null;
    }

    public function balance($id = null) {
        $this->request->allowMethod(['get']);
        $book = $this->Books->get($id);

        /* @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');
        $query="select
                categories.title as ct,
                accounts.title as at,
                currencies.symbol,
                sum(distributions.amount * distributions.drcr) as amount
            from distributions
            left join transactions on distributions.transaction_id=transactions.id
            left join books on transactions.book_id=books.id
            left join accounts on distributions.account_id=accounts.id
            left join categories on accounts.category_id=categories.id
            left join currencies on distributions.currency_id=currencies.id
            where books.id=$id
            and categories.id in (1,2,3)
            group by accounts.id, currencies.id
            order by categories.id";
        $lineItems=$connection->execute($query)->fetchAll('assoc');

        $this->set(compact('book','lineItems'));

        $this->set('_serialize', ['lineItems']);
    }

    //public function delete($id = null) {
        //$this->request->allowMethod(['post', 'delete']);
        //$book = $this->Books->get($id);
        //if ($this->Books->delete($book)) {
            //$this->Flash->success(__(self::BOOK_DELETED));
        //} else {
            //$this->Flash->error(__(self::CANNOT_DELETE_BOOK));
        //}
        //return $this->redirect(['action' => 'index']);
    //}

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $book = $this->Books->get($id);
        if ($this->request->is(['put'])) {
            $book = $this->Books->patchEntity($book, $this->request->data);
            if ($this->Books->save($book)) {
                $this->Flash->success(__(self::BOOK_SAVED));
                return $this->redirect(['controller'=>'books','action' => 'index','_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::BOOK_NOT_SAVED));
            }
        }
        $this->set(compact('book'));
        return null;
    }

    public function income($id = null) {
        $this->request->allowMethod(['get']);
        $book = $this->Books->get($id);

        /* @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');
        $query="select categories.title as ct, accounts.title as at, sum(distributions.amount * distributions.drcr) as amount
            from distributions
            left join transactions on distributions.transaction_id=transactions.id
            left join books on transactions.book_id=books.id
            left join accounts on distributions.account_id=accounts.id
            left join categories on accounts.category_id=categories.id
            where books.id=$id
            and categories.id in (4,5)
            group by accounts.id
            order by categories.id";
        $lineItems=$connection->execute($query)->fetchAll('assoc');

        $this->set(compact('book','lineItems' ));
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('books', $this->Books->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $book = $this->Books->get($id);
        $this->set('book', $book);
    }
}
