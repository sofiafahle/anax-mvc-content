<h1><?=$page->title?></h1>

<?php if ($page->filter != '') {
	echo $this->textFilter->doFilter(htmlentities($page->content, null, 'UTF-8'), $page->filter);
} else {
	echo htmlentities($page->content, null, 'UTF-8');
}
?>

