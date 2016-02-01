<?php
namespace App\Controller;

class CategoriesController extends AppController {

    const BOOK_SAVED = "The category has been saved.";
    const BOOK_NOT_SAVED = "The category could not be saved. Please, try again.";
    const BOOK_DELETED = "The category has been deleted.";
    const CANNOT_DELETE_BOOK = "The category could not be deleted. Please, try again.";

    public function add() {
        $this->request->allowMethod(['get','post']);
        $category = $this->Categories->newEntity();
        if ($this->request->is('post')) {
            $category = $this->Categories->patchEntity($category, $this->request->data);
            if ($this->Categories->save($category)) {
                $this->Flash->success(__(self::BOOK_SAVED));
                return $this->redirect(['controller'=>'categories','action' => 'index','_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::BOOK_NOT_SAVED));
            }
        }
        $this->set(compact('category'));
        return null;
    }

    //public function delete($id = null) {
        //$this->request->allowMethod(['post', 'delete']);
        //$category = $this->Categories->get($id);
        //if ($this->Categories->delete($category)) {
            //$this->Flash->success(__(self::BOOK_DELETED));
        //} else {
            //$this->Flash->error(__(self::CANNOT_DELETE_BOOK));
        //}
        //return $this->redirect(['action' => 'index']);
    //}

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $category = $this->Categories->get($id);
        if ($this->request->is(['put'])) {
            $category = $this->Categories->patchEntity($category, $this->request->data);
            if ($this->Categories->save($category)) {
                $this->Flash->success(__(self::BOOK_SAVED));
                return $this->redirect(['controller'=>'categories','action' => 'index','_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::BOOK_NOT_SAVED));
            }
        }
        $this->set(compact('category'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('categories', $this->Categories->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $category = $this->Categories->get($id);
        $this->set('category', $category);
    }
}
