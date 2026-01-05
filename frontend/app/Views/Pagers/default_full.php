<?php $pager->setSurroundCount(2) ?>

<nav class="pg" aria-label="Pagination">
    <?php if ($pager->hasPrevious()): ?>
        <a class="pg-link" href="<?= $pager->getPrevious() ?>">Prev</a>
    <?php else: ?>
        <span class="pg-link is-disabled">Prev</span>
    <?php endif; ?>

    <?php foreach ($pager->links() as $link): ?>
        <a class="pg-link <?= $link['active'] ? 'is-active' : '' ?>" href="<?= $link['uri'] ?>">
            <?= $link['title'] ?>
        </a>
    <?php endforeach; ?>

    <?php if ($pager->hasNext()): ?>
        <a class="pg-link" href="<?= $pager->getNext() ?>">Next</a>
    <?php else: ?>
        <span class="pg-link is-disabled">Next</span>
    <?php endif; ?>
</nav>
