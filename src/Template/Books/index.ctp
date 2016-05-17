<?php
/* @var \Cake\ORM\Table $books */
$this->Breadcrumb->makeTrail('Books',$this->Html);
?>
<div id="BooksIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Book'), ['action' => 'newform','_method'=>'GET'], ['id'=>'BookNewform']) ?></li>
        </ul>
    </nav>
    <div class="books index large-9 medium-8 columns content">

        <table id="BooksTable" cellpadding="0" cellspacing="0">
            <caption><h4><?= __('Books') ?></h4></caption>

            <thead>
                <tr>
                    <th id="title" ><?= __('Title') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= $book->title ?></td>
                    <td class="actions">
                        <?=
                        //$this->Html->link(__('View/Edit'), ['action' => 'view', 'id'=>$book->id, '_method'=>'GET'],['name'=>'BookView'])
                        $this->Html->link(__('View'), ['action' => 'view', 'id'=>$book->id, '_method'=>'GET'],['name'=>'BookView'])
                        ?>
                        <?=
                        //$this->Html->link(__('View/Edit'), ['action' => 'view', 'id'=>$book->id, '_method'=>'GET'],['name'=>'BookView'])
                        $this->Html->link(__('Edit'), ['action'=>'editform', 'id'=>$book->id],['name'=>'BookEdit'])
                        ?>
                        <?php // $this->Form->postLink(__('Delete'), ['action' => 'delete',  '_method'=>'DELETE', 'id'=>$book->id], ['name'=>'BookDelete','confirm' => __('Are you sure you want to delete # {0}?', $book->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
