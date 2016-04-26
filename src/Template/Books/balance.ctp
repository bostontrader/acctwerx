<?php
/**
 * @var \Cake\ORM\Entity $book
 * @var array $lineItems
 */
$this->Breadcrumb->makeTrail('Balance Sheet',$this->Html);
?>
<div id="BooksView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="books view large-9 medium-8 columns content">
        <h3><?= h(__('Balance Sheet for ').$book->title) ?></h3>
        <table id="BookBalanceTable" class="vertical-table">
            <thead>
            <tr>
                <th id="category" ><?= __('Category') ?></th>
                <th id="account" ><?= __('Account') ?></th>
                <th id="amount"  style="text-align:right"><?= __('Amount') ?></th>
                <th id="currency" ><?= __('Currency') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($lineItems as $lineItem): ?>
                <tr>
                    <td><?= 'category' ?></td>
                    <td><?= $lineItem['at'] ?></td>
                    <td style="text-align:right"><?= $lineItem['amount'] ?></td>
                    <td><?= $lineItem['symbol'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
