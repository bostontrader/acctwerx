<?php  /* @var \App\Model\Entity $book */ ?>

<div id="BooksAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="books form large-9 medium-8 columns content">
        <?= $this->Form->create($book,['id'=>'BookAddForm']) ?>
        <fieldset>
            <legend><?= __('Add Book') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'BookTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
