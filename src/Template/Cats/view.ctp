<?php  /* @var \App\Model\Entity $cat */ ?>
<div id="CatsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="cats view large-9 medium-8 columns content">
        <h3><?= h($cat->id) ?></h3>
        <table id="CatViewTable" class="vertical-table">
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $cat->title ?></td>
            </tr>
        </table>
    </div>
</div>
