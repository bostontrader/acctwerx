<?php
/**
 * @var int $account_id
 * @var int $book_id
 * @var \Cake\ORM\Table $distributions
 * @var int $transaction_id
 */
?>
<div id="DistributionsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Distribution'), ['book_id'=>$book_id,'transaction_id'=>$transaction_id,'action' => 'add','_method'=>'GET'],['id'=>'DistributionAdd']) ?></li>
            <li><?php //$this->Html->link(__('New Distribution'), '/accounts/'.$account_id.'/distributions',['id'=>'DistributionAdd']) ?></li>
        </ul>
    </nav>
    <div class="distributions index large-9 medium-8 columns content">
        <h4><header><?= __('Distributions for Transaction : '.$transaction_id) ?></header></h4>
        <table id="DistributionsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="drcr" ><?= __('DR/CR') ?></th>
                    <th id="category" ><?= __('Category') ?></th>
                    <th id="account" ><?= __('Account') ?></th>
                    <th id="amount" ><?= __('Amount') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($distributions as $distribution): ?>
                <tr>
                    <td><?= $distribution->drcr==1?'DR':'CR' ?></td>
                    <td><?= $distribution->account->category->title ?></td>
                    <td><?= $distribution->account->title ?></td>
                    <td><?= $distribution->amount ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['book_id'=>$book_id,'transaction_id'=>$transaction_id,'action'=>'view','id'=>$distribution->id,'_method'=>'GET'],['name'=>'DistributionView']) ?>
                        <?= $this->Html->link(__('Edit'), ['book_id'=>$book_id,'transaction_id'=>$transaction_id,'action'=>'edit',$distribution->id,'_method'=>'GET'],['name'=>'DistributionEdit']) ?>
                        <?php //$this->Form->postLink(__('Delete'), ['action' => 'delete', $distribution->id], ['name'=>'DistributionDelete','confirm' => __('Are you sure you want to delete # {0}?', $distribution->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
