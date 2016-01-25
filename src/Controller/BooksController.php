<?php
namespace App\Controller;

class BooksController extends AppController {

    const BOOK_SAVED = "The book has been saved.";
    const BOOK_NOT_SAVED = "The book could not be saved. Please, try again.";
    //const NEED_SECTION_ID = "You need to include a 'section_id' parameter";
    //const DNC = "That does not compute";
    const BOOK_DELETED = "The book has been deleted.";
    const CANNOT_DELETE_BOOK = "The book could not be deleted. Please, try again.";

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $book = $this->Books->newEntity();
        if ($this->request->is('post')) {
            $book = $this->Books->patchEntity($book, $this->request->data);
            if ($this->Books->save($book)) {
                $this->Flash->success(__(self::BOOK_SAVED));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__(self::BOOK_NOT_SAVED));
            }
        }
        $this->set(compact('book'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $book = $this->Books->get($id);
        if ($this->Books->delete($book)) {
            $this->Flash->success(__(self::BOOK_DELETED));
        } else {
            $this->Flash->error(__(self::CANNOT_DELETE_BOOK));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $book = $this->Books->get($id);
        if ($this->request->is(['put'])) {
            $book = $this->Books->patchEntity($book, $this->request->data);
            if ($this->Books->save($book)) {
                $this->Flash->success(__(self::BOOK_SAVED));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__(self::BOOK_NOT_SAVED));
            }
        }
        $this->set(compact('book'));
        return null;
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
