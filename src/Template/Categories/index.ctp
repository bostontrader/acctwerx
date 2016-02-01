<?php
/**
 * @var \Cake\ORM\Table $categories
 */
?>
<div id="CategoriesIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Category'), ['action' => 'add'], ['id'=>'CategoryAdd']) ?></li>
        </ul>
    </nav>
    <div class="categorys index large-9 medium-8 columns content">
        <h3><?= __('Categories') ?></h3>
        <table id="CategoriesTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="id" ><?= __('ID') ?></th>
                    <th id="title" ><?= __('Title') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= $category->id ?></td>
                    <td><?= $category->title ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', 'id'=>$category->id, '_method'=>'GET'],['name'=>'CategoryView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit',$category->id],['name'=>'CategoryEdit']) ?>
                        <?php // $this->Form->postLink(__('Delete'), ['action' => 'delete',  '_method'=>'DELETE', 'id'=>$category->id], ['name'=>'CategoryDelete','confirm' => __('Are you sure you want to delete # {0}?', $category->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
