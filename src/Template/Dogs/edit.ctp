<?php  /* @var \App\Model\Entity $dog */ ?>

<div id="DogsEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="dogs form large-9 medium-8 columns content">
        <?= $this->Form->create($dog,['id'=>'DogEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Dog') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'DogTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
