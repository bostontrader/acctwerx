<?php  /* @var \Cake\ORM\Entity $book */ ?>

<div id="BooksAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="books form large-9 medium-8 columns content">
        <?=
            //$this->Form->create($book,['id'=>'BookNewform','url'=>['action'=>'']])
            //$this->Form->create($book,['id'=>'BookNewform','url'=>['action'=>'index']])
            //$this->Form->create($book,['id'=>'BookNewform','url'=>['action'=>'add']])
            //$this->Form->create($book,['id'=>'BookNewform','url'=>['action'=>'add'],'type'=>'post'])
            //$this->Form->create($book,['id'=>'BookNewform','url'=>['action'=>'add','type'=>'post']])
            $this->Form->create($book,['id'=>'BookNewform','url'=>['action'=>'add','[method]'=>'post']])
            //$this->Form->create($book,['id'=>'BookNewform','url'=>['action'=>'create']])
            //$this->Form->create($book,['id'=>'BookNewform','url'=>['action'=>'create','type'=>'post']])
            //$this->Form->create($book,['id'=>'BookNewform','url'=>['action'=>'create'],'type'=>'post'])
            //$this->Form->create($book,['id'=>'BookNewform','url'=>'/books'])
        ?>
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
