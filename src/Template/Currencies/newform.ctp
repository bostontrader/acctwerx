<?php  /* @var \Cake\ORM\Entity $currency */ ?>

<div id="CurrenciesNewform">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="currencies form large-9 medium-8 columns content">
        <?= $this->Form->create($currency,['id'=>'CurrencyNewformForm']) ?>
        <fieldset>
            <legend><?= __('Add Currency') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'CurrencyTitle']);
                echo $this->Form->input('symbol',['id'=>'CurrencySymbol']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
