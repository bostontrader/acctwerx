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
                    <th id="tran_datetime" ><?= __('Tran Datetime') ?></th>
                    <th id="note" ><?= __('Note') ?></th>
                    <th id="drcr" ><?= __('DR/CR') ?></th>
                    <th id="amount"  style="text-align:right"><?= __('Amount') ?></th>
                    <th id="currency" ><?= __('Currency') ?></th>
                    <th id="run_total" style="text-align:right"><?= __('Running Total') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $run_tot=0; foreach ($distributions as $distribution): ?>
                <tr>
                    <td><?= $distribution->transaction->tran_datetime ?></td>
                    <td><?= $distribution->transaction->note ?></td>
                    <td><?= $distribution->drcr==1?'DR':'CR' ?></td>
                    <td style="text-align:right"><?= $this->Number->precision($distribution->amount,4) ?></td>
                    <td><?= $distribution->currency->symbol ?></td>
                    <td style="text-align:right"><?= $this->Number->precision($run_tot+=$distribution->amount*$distribution->drcr,4) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
