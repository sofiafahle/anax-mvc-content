<h1><?=$page->title?></h1>

<?= $this->textFilter->doFilter(htmlentities($page->content, null, 'UTF-8'), $page->filter) ?>

