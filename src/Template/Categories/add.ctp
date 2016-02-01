<?php  /* @var \Cake\ORM\Entity $category */ ?>

<div id="CategoriesAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="categories form large-9 medium-8 columns content">
        <?= $this->Form->create($category,['action'=>'add','id'=>'CategoryAddForm']) ?>
        <fieldset>
            <legend><?= __('Add Category') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'CategoryTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
