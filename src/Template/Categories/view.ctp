<?php  /* @var \Cake\ORM\Entity $category */ ?>
<div id="CategoriesView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="categories view large-9 medium-8 columns content">
        <table id="CategoryViewTable" class="vertical-table">
            <caption><h3><?= h($category->{'id'}) ?></h3></caption>
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $category->{'title'} ?></td>
            </tr>
            <tr id="symbol">
                <th><?= __('Symbol') ?></th>
                <td><?= $category->{'symbol'} ?></td>
            </tr>
        </table>
    </div>
</div>
