<?php
/**
 * @var \Cake\ORM\Entity $book
 * @var \Cake\ORM\Entity $transaction
 *
 */ ?>

<div id="TransactionsEditform">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="transactions form large-9 medium-8 columns content">
        <?= $this->Form->create($transaction,['id'=>'TransactionEditformForm']) ?>
        <fieldset>
            <legend><?= __('Edit Transaction for '.$book['title']) ?></legend>
            <?php
                echo $this->Form->input('note',['id'=>'TransactionNote', 'type'=>'text']);
            echo $this->Form->input('tran_datetime',['id'=>'TransactionTranDatetime']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
