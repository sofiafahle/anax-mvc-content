<h1><?=$title?></h1>

<?php foreach ($posts as $post) : ?>

	<h3><strong><?= $post->id ?>. <a href='<?=$this->url->create('blog/view/' . $post->slug)?>'><?= $post->title ?></a></strong></h3>
    <p>Author: <?= $post->author ?> Created: <?= $post->created ?>
      <?= $post->updated ? ' Updated: (' . $post->updated . ')' : null ?>
    </p>
    <p>
    	<?= strip_tags($this->textFilter->doFilter(htmlentities(mb_substr($post->content, 0, 200), null, 'UTF-8'), $post->filter)) ?>
    </p>

<?php endforeach; ?>