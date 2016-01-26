<?php  /* @var \App\Model\Entity $account */ ?>

<div id="AccountsAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="accounts form large-9 medium-8 columns content">
        <?= $this->Form->create($account,['id'=>'AccountAddForm']) ?>
        <fieldset>
            <legend><?= __('Add Account') ?></legend>
            <?php
                echo $this->Form->input('book_id', ['id'=>'AccountBookId', 'options' => $books, 'empty' => '(none selected)']);
                echo $this->Form->input('sort',['id'=>'AccountSort']);
                echo $this->Form->input('title',['id'=>'AccountTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
