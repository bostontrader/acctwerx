<?php  /* @var \Cake\ORM\Entity $category */ ?>

<div id="CategoriesNewform">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="categories form large-9 medium-8 columns content">
        <?= $this->Form->create($category,['id'=>'CategoryNewformForm']) ?>
        <fieldset>
            <legend><?= __('Add Category') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'CategoryTitle']);
                echo $this->Form->input('symbol',['id'=>'CategorySymbol']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
