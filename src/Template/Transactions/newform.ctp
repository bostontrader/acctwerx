<?php
/**
 * @var \Cake\ORM\Entity $book
 * @var \Cake\ORM\Table $transaction
 */
?>

<div id="TransactionsNewform">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="transactions form large-9 medium-8 columns content">
        <?= $this->Form->create($transaction,['id'=>'TransactionNewform','url'=>['book_id'=>$book['id'],'action'=>'add','[method]'=>'post']]) ?>
        <fieldset>
            <legend><?= __('Add Transaction for '.$book->title) ?></legend>
            <?php
                echo $this->Form->input('book_id',['id'=>'TransactionBookId','value'=>$book['id'],'type'=>'hidden']);
                echo $this->Form->input('note',['id'=>'TransactionNote','type'=>'text']);
                echo $this->Form->input('tran_datetime',['id'=>'TransactionTranDatetime']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
