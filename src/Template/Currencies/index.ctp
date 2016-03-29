<?php
/**
 * @var \Cake\ORM\Table $currencies
 */
?>
<div id="CurrenciesIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Currency'), ['action' => 'add'], ['id'=>'CurrencyAdd']) ?></li>
        </ul>
    </nav>
    <div class="currencys index large-9 medium-8 columns content">
        <h4><header><?= __('Currencies') ?></header></h4>

        <table id="CurrenciesTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="title" ><?= __('Title') ?></th>
                    <th id="symbol" ><?= __('Symbol') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($currencies as $currency): ?>
                <tr>
                    <td><?= $currency->title ?></td>
                    <td><?= $currency->symbol ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', 'id'=>$currency->id, '_method'=>'GET'],['name'=>'CurrencyView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit',$currency->id],['name'=>'CurrencyEdit']) ?>
                        <?php // $this->Form->postLink(__('Delete'), ['action' => 'delete',  '_method'=>'DELETE', 'id'=>$currency->id], ['name'=>'CurrencyDelete','confirm' => __('Are you sure you want to delete # {0}?', $currency->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
