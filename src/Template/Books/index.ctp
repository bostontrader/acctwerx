<?php
/**
 * @var \App\Model\Entity $book
 * @var \App\Model\Table $books
 */
?>
<div id="BooksIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Book'), ['action' => 'add'],['id'=>'BookAdd']) ?></li>
        </ul>
    </nav>
    <div class="books index large-9 medium-8 columns content">
        <h3><?= __('Books') ?></h3>
        <table id="BooksTable" cellpadding="0" cellspacing="0">
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
                        <?= $this->Html->link(__('View'), ['action' => 'view', $book->id],['name'=>'BookView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $book->id],['name'=>'BookEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $book->id], ['name'=>'BookDelete','confirm' => __('Are you sure you want to delete # {0}?', $book->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
