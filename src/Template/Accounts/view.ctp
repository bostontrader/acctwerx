<?php
/* @var \Cake\ORM\Entity $account */
/* @var \Cake\ORM\Entity $book */
/* @var int $book_id */

$this->Html->addCrumb('Books', '/books');
$this->Html->addCrumb($book->title, ['controller'=>'books','id'=>$book->id,'action'=>'view', '_method'=>'GET']);
$this->Html->addCrumb('Accounts', ['controller'=>'accounts','book_id'=>$book->id,'_method'=>'GET']);
$this->Html->addCrumb($account->title, ['book_id'=>$book->id,'action'=>'view','id'=>$account->id,'_method'=>'GET']);

$n1=$this->Html->getCrumbs();
$n2=$this->Html->getCrumbList();
?>
<div id="AccountsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('Distributions'), ['book_id'=>$book->id,'account_id'=>$account->id,'controller'=>'distributions','_method'=>'GET'], ['id'=>'AccountDistributions']) ?></li>
        </ul>
    </nav>
    <div class="accounts view large-9 medium-8 columns content">
        <h3><?= h($account->id) ?></h3>
        <table id="AccountViewTable" class="vertical-table">
            <tr id="book_title">
                <th><?= __('Book Title') ?></th>
                <td><?= $account->book->title ?></td>
            </tr>
            <tr id="category_title">
                <th><?= __('Category') ?></th>
                <td><?= $account->category->title ?></td>
            </tr>
            <tr id="sort">
                <th><?= __('Sort') ?></th>
                <td><?= $account->sort ?></td>
            </tr>
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $account->title ?></td>
            </tr>
        </table>
    </div>
</div>
