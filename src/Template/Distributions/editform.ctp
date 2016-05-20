<?php
/**
 * @var \Cake\ORM\Table $accounts
 * @var \Cake\ORM\Table $currencies
 * @var \Cake\ORM\Entity $transaction
 * @var int $transaction_id
 * @var \Cake\ORM\Entity $distribution
 *
 */ ?>

<div id="DistributionsEditform">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="distributions form large-9 medium-8 columns content">
        <?= $this->Form->create($distribution,['id'=>'DistributionEditformForm']) ?>
        <fieldset>
            <legend><?= __('Edit Distribution for transaction: '.$transaction_id) ?></legend>
            <?php
                echo $this->Form->radio('drcr', [['value'=>1,'text'=>'dr'],['value'=>-1,'text'=>'cr']]);
                echo $this->Form->input('account_id', ['id'=>'DistributionAccountId', 'options' => $accounts, 'empty' => '(none selected)']);
                echo $this->Form->input('amount',['id'=>'DistributionAmount','type'=>'text']);
                echo $this->Form->input('currency_id', ['id'=>'DistributionCurrencyId', 'options' => $currencies, 'empty' => '(none selected)']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
