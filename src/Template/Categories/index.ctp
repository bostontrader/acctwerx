<?php
/**
 * @var \Cake\ORM\Table $categories
 */
?>
<div id="CategoriesIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Category'), ['action' => 'newform'], ['id'=>'CategoryNewform']) ?></li>
        </ul>
    </nav>
    <div class="categorys index large-9 medium-8 columns content">
        <h3><header><?= __('Categories') ?></header></h3>
        <table id="CategoriesTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="title" ><?= __('Title') ?></th>
                    <th id="symbol" ><?= __('Symbol') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= $category->title ?></td>
                    <td><?= $category->symbol ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action'=>'view','id'=>$category->id,'_method'=>'GET'],['name'=>'CategoryView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action'=>'editform','id'=>$category->id],['name'=>'CategoryEditform']) ?>
                        <?php // $this->Form->postLink(__('Delete'), ['action' => 'delete',  '_method'=>'DELETE', 'id'=>$category->id], ['name'=>'CategoryDelete','confirm' => __('Are you sure you want to delete # {0}?', $category->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
