<h1><?=$post->title?></h1>

<?= $this->textFilter->doFilter(htmlentities($post->content, null, 'UTF-8'), $post->filter) ?>

