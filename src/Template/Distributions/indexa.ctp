<?php /**
 * @var int $account_id
 * @var \Cake\ORM\Entity $account
 * @var \Cake\ORM\Table $distributions
 */
$this->Breadcrumb->makeTrail('Distributions',$this->Html);
?>
<div id="DistributionsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="distributions index large-9 medium-8 columns content">
        <h4><header><?= __('Distributions for Account : '.$account['title']) ?></header></h4>
        <table id="DistributionsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="drcr" ><?= __('DR/CR') ?></th>
                    <th id="amount" ><?= __('Amount') ?></th>
                    <th id="currency" ><?= __('Currency') ?></th>
                    <th id="run_total" ><?= __('Running Total') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $run_tot=0; foreach ($distributions as $distribution): ?>
                <tr>
                    <td><?= $distribution->drcr==1?'DR':'CR' ?></td>
                    <td><?= $distribution->amount ?></td>
                    <td><?= $distribution->currency->title ?></td>
                    <td><?= $run_tot+=$distribution->amount*$distribution->drcr ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
