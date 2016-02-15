<?php  /* @var \Cake\ORM\Entity $currency */ ?>
<div id="CurrenciesView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="currencies view large-9 medium-8 columns content">
        <h3><?= h($currency->id) ?></h3>
        <table id="CurrencyViewTable" class="vertical-table">
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $currency->title ?></td>
            </tr>
            <tr id="symbol">
                <th><?= __('Symbol') ?></th>
                <td><?= $currency->title ?></td>
            </tr>
        </table>
    </div>
</div>
