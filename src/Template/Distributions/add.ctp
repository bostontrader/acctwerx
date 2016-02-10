<?php
/**
 * @var \Cake\ORM\Table $accounts
 * @var \Cake\ORM\Entity $distribution
 * @var int $transaction_id
 */
?>

<div id="DistributionsAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="distributions form large-9 medium-8 columns content">
        <?= $this->Form->create($distribution,['id'=>'DistributionAddForm']) ?>
        <fieldset>
            <legend><?= __('Add Distribution') ?></legend>
            <?php
                echo $this->Form->input('transaction_id',['value'=>$transaction_id,'type'=>'hidden']);
                echo $this->Form->input('account_id', ['id'=>'DistributionAccountId', 'options' => $accounts, 'empty' => '(none selected)']);
                echo $this->Form->radio('drcr', [['value'=>1,'text'=>'dr'],['value'=>-1,'text'=>'cr']]);
                echo $this->Form->input('amount',['id'=>'DistributionAmount','type'=>'text']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
