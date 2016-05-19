<?php
namespace App\Controller;

use Cake\Network\Exception\BadRequestException;

class TransactionsController extends AppController {

    const TRANSACTION_SAVED = "The transaction has been saved.";
    const TRANSACTION_NOT_SAVED = "The transaction could not be saved. Please, try again.";
    const DNC = "That does not compute";
    const TRANSACTION_DELETED = "The transaction has been deleted.";
    const CANNOT_DELETE_TRANSACTION = "The transaction could not be deleted. Please, try again.";

    //public function initialize() {
        //parent::initialize();
        //$this->loadComponent('RequestHandler');
    //}

    /**
     * This method mostly works as ordinarily expected. A GET request will return a new
     * transaction entry form and a POST will create a new transaction.
     *
     * But wait... there's more!  The first step of processing a POST will be an attempt
     * to decode a JSON payload.  If JSON exists, then create an entire new transaction,
     * with distributions, all in one shot.  Ignore any $request->params that might be set.
     *
     * If no JSON exists, then process this request the ordinary way.  That is, create a new transaction,
     * w/o any distributions, and hope $request->params is suitably set.
     *
     * @return \Cake\Network\Response|null
     */
    // POST /books/:book_id/transactions
    public function add() {
        $this->request->allowMethod(['post']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $book_id=$this->get_book_id($this->request->params);
        //$book=$this->{'Transactions'}->Books->get($book_id);

        $transaction = $this->{'Transactions'}->newEntity(['contain'=>'books']);

        // Is there a better way to determine whether or not JSON is here?
        //$fullTransaction=$this->request->input('json_decode',true);
        //if(is_null($fullTransaction)) {
        // No JSON, do it the normal way.
        $transaction = $this->{'Transactions'}->patchEntity($transaction, $this->request->data);
        if ($this->{'Transactions'}->save($transaction)) {
            $this->Flash->{'success'}(__(self::TRANSACTION_SAVED));
            return $this->redirect(['action'=>'view','book_id'=>$book_id,'id' => $transaction->id,'_method'=>'GET']);
        } else {
            $this->Flash->{'error'}(__(self::TRANSACTION_NOT_SAVED));
        }
        //} else {
            // JSON found.
            //$transaction = $this->{'Transactions'}->patchEntity($transaction, $fullTransaction);
            //if ($this->{'Transactions'}->save($transaction)) {
                //$reply=['result'=>'ok'];
                //$this->set(compact('reply'));
                //$this->set('_serialize', ['reply']);
            //} else {
                //$this->Flash->{'error'}(__(self::TRANSACTION_NOT_SAVED));
            //}
        //}
        //}
        return null;
    }

    // GET /books/:book_id/transactions/newform
    public function newform() {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $book_id=$this->get_book_id($this->request->params);
        $book=$this->{'Transactions'}->Books->get($book_id);

        $transaction = $this->{'Transactions'}->newEntity(['contain'=>'books']);

        $this->set(compact('transaction','book'));
    }

    // DELETE /books/:book_id/transactions/:id
    public function delete($id = null) {
        $this->request->allowMethod(['delete']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $transaction = $this->{'Transactions'}->get($id);
        if ($this->{'Transactions'}->delete($transaction)) {
            //$this->Flash->{'success'}(__(self::TRANSACTION_DELETED));
        } else {
        //$this->Flash->{'error'}(__(self::CANNOT_DELETE_TRANSACTION));
        }
        //return $this->redirect(['action' => 'index']);
    }

    // PUT /books/:book_id/transactions/:id
    public function edit($id = null) {
        $this->request->allowMethod(['put']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        // Get the book and book_id.
        $book_id=$this->get_book_id($this->request->params);
        $book=$this->{'Transactions'}->Books->get($book_id);

        $transaction = $this->{'Transactions'}->get($id);
        if ($this->request->is(['put'])) {
            $transaction = $this->{'Transactions'}->patchEntity($transaction, $this->request->data);
            if ($this->{'Transactions'}->save($transaction)) {
                $this->Flash->{'success'}(__(self::TRANSACTION_SAVED));
                return $this->redirect(['action' => 'index','book_id' => $book_id,'_method'=>'GET']);
            } else {
                $this->Flash->{'error'}(__(self::TRANSACTION_NOT_SAVED));
            }
        }
        $this->set(compact('transaction','book'));
        return null;
    }

    // There's something wrong with my routing because no $id param is passed.
    // But I can find it as $this->request->params['id']
    // GET /books/:book_id/transactions/:id/editform
    public function editform() {
        $id=$this->request->params['id'];
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        // Get the book and book_id.
        $book_id=$this->get_book_id($this->request->params);
        $book=$this->{'Transactions'}->Books->get($book_id);

        $transaction = $this->{'Transactions'}->get($id);

        $this->set(compact('transaction','book'));
    }

    // GET /books/:book_id/transactions
    public function index() {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $book_id=$this->get_book_id($this->request->params);
        $book=$this->{'Transactions'}->Books->get($book_id);

        $this->request->allowMethod(['get']);
        $this->set(
            'transactions', $this->{'Transactions'}->find()
            ->contain('Books')
            ->where(['book_id'=>$book_id])
            ->limit(200)
            ->order(['tran_datetime desc']));
        $this->set(compact('book','book_id'));
    }

    // GET /books/:book_id/transactions/:id
    public function view($id = null) {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $book_id=$this->get_book_id($this->request->params);

        $transaction = $this->{'Transactions'}->get($id,['contain'=>'Books']);
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
