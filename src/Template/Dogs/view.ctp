<?php  /* @var \App\Model\Entity $dog */ ?>
<div id="DogsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="dogs view large-9 medium-8 columns content">
        <h3><?= h($dog->id) ?></h3>
        <table id="DogViewTable" class="vertical-table">
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $dog->title ?></td>
            </tr>
        </table>
    </div>
</div>
