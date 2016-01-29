<?php  /* @var \App\Model\Entity $cat */ ?>

<div id="CatsEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="cats form large-9 medium-8 columns content">
        <?= $this->Form->create($cat,['id'=>'CatEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Cat') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'CatTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
