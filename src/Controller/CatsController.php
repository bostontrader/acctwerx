<?php
namespace App\Controller;

class CatsController extends AppController {

    const CAT_SAVED = "The cat has been saved.";
    const CAT_NOT_SAVED = "The cat could not be saved. Please, try again.";
    const CAT_DELETED = "The cat has been deleted.";
    const CANNOT_DELETE_CAT = "The cat could not be deleted. Please, try again.";

    public function add() {
        $this->request->allowMethod(['get','post']);
        $cat = $this->Cats->newEntity();
        if ($this->request->is('post')) {
            $cat = $this->Cats->patchEntity($cat, $this->request->data);
            if ($this->Cats->save($cat)) {
                $this->Flash->success(__(self::CAT_SAVED));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__(self::CAT_NOT_SAVED));
            }
        }
        $this->set(compact('cat'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $cat = $this->Cats->get($id);
        if ($this->Cats->delete($cat)) {
            $this->Flash->success(__(self::CAT_DELETED));
        } else {
            $this->Flash->error(__(self::CANNOT_DELETE_CAT));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $cat = $this->Cats->get($id);
        if ($this->request->is(['put'])) {
            $cat = $this->Cats->patchEntity($cat, $this->request->data);
            if ($this->Cats->save($cat)) {
                $this->Flash->success(__(self::CAT_SAVED));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__(self::CAT_NOT_SAVED));
            }
        }
        $this->set(compact('cat'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('cats', $this->Cats->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $cat = $this->Cats->get($id);
        $this->set('cat', $cat);
    }
}
