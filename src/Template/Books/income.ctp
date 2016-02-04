<div id="BooksView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="books view large-9 medium-8 columns content">
        <h3><?= h(__('Income Statement for ').$book->title) ?></h3>
        <table id="BookBalanceTable" class="vertical-table">
        </table>
    </div>
</div>
