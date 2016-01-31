<?php
/**
 * @var \Cake\ORM\Entity $book
 * @var \Cake\ORM\Table $transactions
 */
?>
<div id="TransactionsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Transaction'), ['action' => 'add', 'book_id'=>'1', '_method'=>'GET'],['id'=>'TransactionAdd']) ?></li>
            <li><?php //$this->Html->link(__('New Transaction'), '/books/'.$book_id.'/transactions',['id'=>'TransactionAdd']) ?></li>
        </ul>
    </nav>
    <div class="transactions index large-9 medium-8 columns content">
        <h4><header><?= __('Transactions for Book: '.$book['title']) ?></header></h4>
        <table id="TransactionsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="note" ><?= __('Note') ?></th>
                    <th id="datetime" ><?= __('Datetime') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction->note ?></td>
                    <td><?= $transaction->datetime ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', 'id'=>$transaction->id, 'book_id'=>$book_id, '_method'=>'GET'],['name'=>'TransactionView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $transaction->id, 'book_id'=>$book_id, '_method'=>'GET'],['name'=>'TransactionEdit']) ?>
                        <?php //$this->Form->postLink(__('Delete'), ['action' => 'delete', $transaction->id], ['name'=>'TransactionDelete','confirm' => __('Are you sure you want to delete # {0}?', $transaction->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
