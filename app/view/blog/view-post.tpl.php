<a href='<?=$this->url->create('blog')?>'>« Blog startpage</a>

<h1><?=$post->title?></h1>

<?php if ($post->filter != '') {
	echo $this->textFilter->doFilter(htmlentities($post->content, null, 'UTF-8'), $post->filter);
} else {
	echo htmlentities($post->content, null, 'UTF-8');
}
?>
