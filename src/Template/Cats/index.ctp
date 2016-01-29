<?php
/**
 * @var \App\Model\Entity $cat
 * @var \App\Model\Table $cats
 */
?>
<div id="CatsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Cat'), ['action' => 'addd'],['id'=>'CatAdd']) ?></li>
        </ul>
    </nav>
    <div class="cats index large-9 medium-8 columns content">
        <h3><?= __('Cats') ?></h3>
        <table id="CatsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="title" ><?= __('Title') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cats as $cat): ?>
                <tr>
                    <td><?= $cat->title ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $cat->id],['name'=>'CatShow']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $cat->id],['name'=>'CatEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $cat->id], ['name'=>'CatDelete','confirm' => __('Are you sure you want to delete # {0}?', $cat->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
