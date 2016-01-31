<?php
/**
 * @var \Cake\ORM\Entity $book
 * @var \Cake\ORM\Entity $account
 *
 */ ?>

<div id="AccountsEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="accounts form large-9 medium-8 columns content">
        <?= $this->Form->create($account,['id'=>'AccountEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Account for '.$book->title) ?></legend>
            <?php
                echo $this->Form->input('sort',['id'=>'AccountSort', 'type'=>'text']);
                echo $this->Form->input('title',['id'=>'AccountTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
