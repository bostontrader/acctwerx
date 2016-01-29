<?php
/**
 * @var \App\Model\Table\ $dogs
 */
?>
<div id="DogsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Dog'), ['action' => 'add'],['id'=>'DogNewForm']) ?></li>
        </ul>
    </nav>
    <div class="dogs index large-9 medium-8 columns content">
        <h3><?= __('Dogs') ?></h3>
        <table id="DogsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="title" ><?= __('Title') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dogs as $dog): ?>
                <tr>
                    <td><?= $dog->title ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Show'), ['action' => 'view', 'id'=>$dog->id],['name'=>'DogShow']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', 'id'=>$dog->id],['name'=>'DogEdit']) ?>
                        <?php //$this->Form->postLink(__('Delete'), ['action' => 'delete', 'id'=>$dog->id], ['name'=>'DogDelete','confirm' => __('Are you sure you want to delete # {0}?', $dog->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
