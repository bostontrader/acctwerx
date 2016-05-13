<?php
/**
 * @var \Cake\ORM\Entity $book
 * @var \Cake\ORM\Table $transactions
 */
$this->Breadcrumb->makeTrail('Transactions',$this->Html);
?>
<div id="TransactionsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Transaction'),['book_id'=>$book['id'],'action'=>'newform','_method'=>'GET'],['id'=>'TransactionNewform']) ?></li>
            <li><?php //$this->Html->link(__('New Transaction'), '/books/'.$book_id.'/transactions',['id'=>'TransactionAdd']) ?></li>
        </ul>
    </nav>
    <div class="transactions index large-9 medium-8 columns content">
        <h4><header><?= __('Transactions for Book: '.$book['title']) ?></header></h4>
        <table id="TransactionsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="note" ><?= __('Note') ?></th>
                    <th id="tran_datetime" ><?= __('Tran Datetime') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction->note ?></td>
                    <td><?= $transaction->tran_datetime ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['book_id'=>$book['id'],'action'=>'view','id'=>$transaction->id,'_method'=>'GET'],['name'=>'TransactionView']) ?>
                        <?= $this->Html->link(__('Edit'), ['book_id'=>$book['id'],'action'=>'editform', 'id'=>$transaction->id, 'book_id'=>$book_id, '_method'=>'GET'],['name'=>'TransactionEditform']) ?>
                        <?php //$this->Form->postLink(__('Delete'), ['action' => 'delete', $transaction->id], ['name'=>'TransactionDelete','confirm' => __('Are you sure you want to delete # {0}?', $transaction->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
