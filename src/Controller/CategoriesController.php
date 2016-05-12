<?php
namespace App\Controller;
use Cake\Network\Exception\BadRequestException;

class CategoriesController extends AppController {

    const CATEGORY_SAVED = "The category has been saved.";
    const CATEGORY_NOT_SAVED = "The category could not be saved. Please, try again.";
    const CATEGORY_DELETED = "The category has been deleted.";
    const CANNOT_DELETE_CATEGORY = "The category could not be deleted. Please, try again.";

    // POST /categories/add
    public function add() {
        $this->request->allowMethod(['post']);

        // Should accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $category = $this->Categories->newEntity();
        $category = $this->Categories->patchEntity($category, $this->request->data);
        if ($this->Categories->save($category)) {
            $this->Flash->success(__(self::CATEGORY_SAVED));
            return $this->redirect(['controller'=>'categories','action' => 'index','_method'=>'GET']);
        } else {
            $this->Flash->error(__(self::CATEGORY_NOT_SAVED));
        }
    }

    // GET /categories/add
    public function newform() {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $category = $this->Categories->newEntity();
        $this->set(compact('category'));
        return null;
    }

    // DELETE /categories/:id
    public function delete($id = null) {
        //$this->request->allowMethod(['post', 'delete']);
        //$category = $this->Categories->get($id);
        //if ($this->Categories->delete($category)) {
            //$this->Flash->success(__(self::CATEGORY_DELETED));
        //} else {
            //$this->Flash->error(__(self::CANNOT_DELETE_CATEGORY));
        //}
        //return $this->redirect(['action' => 'index']);
    }

    // PUT /categories/:id
    public function edit($id = null) {
        $this->request->allowMethod(['put']);
        
        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);
        
        $category = $this->Categories->get($id);
        $category = $this->Categories->patchEntity($category, $this->request->data);
        if ($this->Categories->save($category)) {
            $this->Flash->success(__(self::CATEGORY_SAVED));
            return $this->redirect(['controller'=>'categories','action' => 'index','_method'=>'GET']);
        } else {
            $this->Flash->error(__(self::CATEGORY_NOT_SAVED));
        }
    }

    // There's something wrong with my routing because no $id param is passed.
    // But I can find it as $this->request->params['id']
    // GET /categories/:id/editform
    public function editform($id = null) {
        $id=$this->request->params['id'];
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $category = $this->Categories->get($id);
        $this->set(compact('category'));
    }

    public function index() {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $this->set('categories', $this->Categories->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $category = $this->Categories->get($id);
        $this->set('category', $category);
    }
}
