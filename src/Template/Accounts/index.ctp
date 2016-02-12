<?php
/**
 * @var \Cake\ORM\Entity $book
 * @var \Cake\ORM\Table $accounts
 */
$this->Html->addCrumb('Books', '/books');
$this->Html->addCrumb($book->title, ['controller'=>'books','id'=>$book->id,'action'=>'view', '_method'=>'GET']);
$this->Html->addCrumb('Accounts', ['controller'=>'accounts','book_id'=>$book->id,'_method'=>'GET']);

?>
<div id="AccountsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Account'), ['book_id'=>$book['id'],'action'=>'add','_method'=>'GET'],['id'=>'AccountAdd']) ?></li>
            <li><?php //$this->Html->link(__('New Account'), '/books/'.$book_id.'/accounts',['id'=>'AccountAdd']) ?></li>
        </ul>
    </nav>
    <div class="accounts index large-9 medium-8 columns content">
        <h4><header><?= __('Accounts for Book: '.$book['title']) ?></header></h4>
        <table id="AccountsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="category" ><?= __('Category') ?></th>
                    <th id="sort" ><?= __('Sort') ?></th>
                    <th id="title" ><?= __('Title') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><?= $account->category->title ?></td>
                    <td><?= $account->sort ?></td>
                    <td><?= $account->title ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['book_id'=>$book['id'],'action'=>'view','id'=>$account->id,'_method'=>'GET'],['name'=>'AccountView']) ?>
                        <?= $this->Html->link(__('Edit'), ['book_id'=>$book['id'],'action'=>'edit',$account->id,'_method'=>'GET'],['name'=>'AccountEdit']) ?>
                        <?php //$this->Form->postLink(__('Delete'), ['action' => 'delete', $account->id], ['name'=>'AccountDelete','confirm' => __('Are you sure you want to delete # {0}?', $account->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
