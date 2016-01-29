<?php
namespace App\Controller;

class DogsController extends AppController {

    const DOG_SAVED = "The dog has been saved.";
    const DOG_NOT_SAVED = "The dog could not be saved. Please, try again.";
    const DOG_DELETED = "The dog has been deleted.";
    const CANNOT_DELETE_DOG = "The dog could not be deleted. Please, try again.";

    public function add() {
        $this->request->allowMethod(['get','post']);
        $dog = $this->Dogs->newEntity();
        if ($this->request->is('post')) {
            $dog = $this->Dogs->patchEntity($dog, $this->request->data);
            if ($this->Dogs->save($dog)) {
                $this->Flash->success(__(self::DOG_SAVED));
                return $this->redirect(['controller' => 'dogs']);
            } else {
                $this->Flash->error(__(self::DOG_NOT_SAVED));
            }
        }
        $this->set(compact('dog'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $dog = $this->Dogs->get($id);
        if ($this->Dogs->delete($dog)) {
            $this->Flash->success(__(self::DOG_DELETED));
        } else {
            $this->Flash->error(__(self::CANNOT_DELETE_DOG));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $dog = $this->Dogs->get($id);
        if ($this->request->is(['put'])) {
            $dog = $this->Dogs->patchEntity($dog, $this->request->data);
            if ($this->Dogs->save($dog)) {
                $this->Flash->success(__(self::DOG_SAVED));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__(self::DOG_NOT_SAVED));
            }
        }
        $this->set(compact('dog'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('dogs', $this->Dogs->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $dog = $this->Dogs->get($id);
        $this->set('dog', $dog);
    }
}
