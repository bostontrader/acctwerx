<?php  /* @var \Cake\ORM\Entity $distribution */ ?>
<div id="DistributionsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="distributions view large-9 medium-8 columns content">
        <h3><?= h($distribution->id) ?></h3>
        <table id="DistributionViewTable" class="vertical-table">
            <tr id="account_title">
                <th><?= __('Account Title') ?></th>
                <td><?= $distribution->account->title ?></td>
            </tr>
            <tr id="amount">
                <th><?= __('Amount') ?></th>
                <td><?= $distribution->amount ?></td>
            </tr>
        </table>
    </div>
</div>
