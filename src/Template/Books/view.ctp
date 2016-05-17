<?php
/* @var \Cake\ORM\Entity $book */
$this->Breadcrumb->makeTrail($book['title'],$this->Html);
?>
<div id="BooksView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('Accounts'), ['controller'=>'accounts','book_id'=>$book['id'],'_method'=>'GET'],['id'=>'BookAccounts']) ?></li>
            <li><?= $this->Html->link(__('Transactions'), ['controller'=>'transactions','book_id'=> $book['id'],'_method'=>'GET'],['id'=>'BookTransactions']) ?></li>
            <li><?= $this->Html->link(__('Balance Sheet'), ['action'=>'balance','id'=>$book['id'],'_method'=>'GET'],['id'=>'BookBalanceSheet']) ?></li>
            <li><?= $this->Html->link(__('Income Statement'), ['action'=>'income','id'=>$book['id'],'_method'=>'GET'],['id'=>'BookIncomeStatement']) ?></li>
        </ul>
    </nav>
    <div class="books view large-9 medium-8 columns content">
        <table id="BookViewTable" class="vertical-table">
            <caption><h3><?= h($book['id']) ?></h3></caption>
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $book['title'] ?></td>
            </tr>
        </table>
    </div>
</div>
