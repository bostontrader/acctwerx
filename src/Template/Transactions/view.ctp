<?php  /* @var \App\Model\Entity $transaction */ ?>
<div id="TransactionsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="transactions view large-9 medium-8 columns content">
        <h3><?= h($transaction->id) ?></h3>
        <table id="TransactionViewTable" class="vertical-table">
            <tr id="book">
                <th><?= __('Book') ?></th>
                <td><?= $transaction->book->title ?></td>
            </tr>
            <tr id="title">
                <th><?= __('Note') ?></th>
                <td><?= $transaction->note ?></td>
            </tr>
        </table>
    </div>
</div>
