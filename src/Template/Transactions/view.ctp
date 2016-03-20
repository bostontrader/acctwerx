<?php
/* @var int $book_id */
/* @var \Cake\ORM\Entity $transaction */
$this->Breadcrumb->makeTrail($transaction->title,$this->Html);
?>
<div id="TransactionsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('Distributions'), ['book_id'=>$book_id,'transaction_id'=>$transaction->id,'controller'=>'distributions','_method'=>'GET'], ['id'=>'TransactionDistributions']) ?></li>
        </ul>
    </nav>
    <div class="transactions view large-9 medium-8 columns content">
        <h3><?= h($transaction->id) ?></h3>
        <table id="TransactionViewTable" class="vertical-table">
            <tr id="book_title">
                <th><?= __('Book Title') ?></th>
                <td><?= $transaction->book->title ?></td>
            </tr>
            <tr id="note">
                <th><?= __('Note') ?></th>
                <td><?= $transaction->note ?></td>
            </tr>
            <tr id="tran_datetime">
                <th><?= __('Tran Datetime') ?></th>
                <td><?= $transaction->tran_datetime ?></td>
            </tr>
        </table>
    </div>
</div>
