<h1><?=$post->title?></h1>

<?=$this->textFilter->doFilter(htmlentities($page->content, null, 'UTF-8'), $post->filter) ?>

