<?php
/* @var int $book_id */
/* @var \Cake\ORM\Entity $account */
?>
<div id="AccountsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('Distributions'), ['book_id'=>$book_id,'account_id'=>$account->id,'controller'=>'distributions','_method'=>'GET'], ['id'=>'AccountDistributions']) ?></li>
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
