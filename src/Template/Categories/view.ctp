<?php  /* @var \Cake\ORM\Entity $category */ ?>
<div id="CategoriesView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="categories view large-9 medium-8 columns content">
        <h3><?= h($category->id) ?></h3>
        <table id="CategoryViewTable" class="vertical-table">
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $category->title ?></td>
            </tr>
        </table>
    </div>
</div>
