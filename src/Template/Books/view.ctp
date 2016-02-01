<?php  /* @var \Cake\ORM\Entity $book */ ?>
<div id="BooksView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('Accounts'), ['controller' => 'accounts', 'book_id' => $book->id, '_method'=>'GET'], ['id'=>'BookAccounts']) ?></li>
            <li><?= $this->Html->link(__('Transactions'), ['controller' => 'transactions', 'book_id' => $book->id, '_method'=>'GET'], ['id'=>'BookTransactions']) ?></li>
        </ul>
    </nav>
    <div class="books view large-9 medium-8 columns content">
        <h3><?= h($book->id) ?></h3>
        <table id="BookViewTable" class="vertical-table">
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $book->title ?></td>
            </tr>
        </table>
    </div>
</div>
