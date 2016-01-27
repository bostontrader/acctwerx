<?php
/**
 * @var \App\Model\Entity $transaction
 * @var \App\Model\Table $transactions
 */
?>
<div id="TransactionsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Transaction'), ['action' => 'add'],['id'=>'TransactionAdd']) ?></li>
        </ul>
    </nav>
    <div class="transactions index large-9 medium-8 columns content">
        <h3><?= __('Transactions') ?></h3>
        <table id="TransactionsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="title" ><?= __('Book') ?></th>
                    <th id="title" ><?= __('Date') ?></th>
                    <th id="title" ><?= __('Note') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction->book->title ?></td>
                    <td><?= $transaction->datetime ?></td>
                    <td><?= $transaction->note ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $transaction->id],['name'=>'TransactionView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $transaction->id],['name'=>'TransactionEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $transaction->id], ['name'=>'TransactionDelete','confirm' => __('Are you sure you want to delete # {0}?', $transaction->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
