<?php  /* @var \App\Model\Entity $transaction */ ?>

<div id="TransactionsEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="transactions form large-9 medium-8 columns content">
        <?= $this->Form->create($transaction,['id'=>'TransactionEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Transaction') ?></legend>
            <?php
                echo $this->Form->input('book_id', ['id'=>'TransactionBookId', 'options' => $books, 'empty' => '(none selected)']);
                echo $this->Form->input('note',['id'=>'TransactionTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
