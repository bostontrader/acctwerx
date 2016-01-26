<?php
/**
 * @var \App\Model\Entity $account
 * @var \App\Model\Table $accounts
 */
?>
<div id="AccountsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Account'), ['action' => 'add'],['id'=>'AccountAdd']) ?></li>
        </ul>
    </nav>
    <div class="accounts index large-9 medium-8 columns content">
        <h3><?= __('Accounts') ?></h3>
        <table id="AccountsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="title" ><?= __('Book') ?></th>
                    <th id="title" ><?= __('Sort') ?></th>
                    <th id="title" ><?= __('Title') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><?= $account->book->title ?></td>
                    <td><?= $account->sort ?></td>
                    <td><?= $account->title ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $account->id],['name'=>'AccountView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $account->id],['name'=>'AccountEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $account->id], ['name'=>'AccountDelete','confirm' => __('Are you sure you want to delete # {0}?', $account->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
