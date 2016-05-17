<?php
/**
 * @var \Cake\ORM\Entity $book
 * @var \Cake\ORM\Entity $account
 * @var \Cake\ORM\Table $categories
 */
?>

<div id="AccountsNewform">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="accounts form large-9 medium-8 columns content">
        <?= $this->Form->create($account,['id'=>'AccountNewformForm']) ?>
        <fieldset>
            <legend><?= __('Add Account for '.$book['title']) ?></legend>
            <?php
                echo $this->Form->input('book_id',['id'=>'AccountBookId','value'=>$book['id'],'type'=>'hidden']);
                echo $this->Form->input('categories._ids', ['id'=>'AccountsCategories', 'options' => $categories, 'multiple'=>true]);
                echo $this->Form->input('title',['id'=>'AccountTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
