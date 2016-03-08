<h1><?=$title?></h1>

<?php foreach ($posts as $blog) : ?>

	<h3><strong><?= $blog->id ?>. <a href='<?=$this->url->create('blog/view/' . $blog->slug)?>'><?= $blog->title ?></a></strong></h3>
    <p> Created: <?= $blog->created ?>
      <?= $blog->updated ? '<br><font color="#01f">Updated: ' . $blog->updated . '</font>' : null ?>
      <?= $blog->inactivated ? '<br><font color="#fa0">Inactivated: ' . $blog->inactivated . '</font>' : null ?>
      <?= $blog->deleted ? '<br><font color="#f00">Deleted: ' . $blog->deleted . '</font>' : null ?><br>
      Filter: <?= $blog->filter ?><br>
    </p>
    
    <p><a href='<?=$this->url->create('blog/inactivate/' . $blog->id)?>'>Inactivate</a> | <a href='<?=$this->url->create('blog/soft-delete/' . $blog->id)?>'>Delete</a> | <a href='<?=$this->url->create('blog/update/' . $blog->id)?>'>Edit</a></p>

<?php endforeach; ?>

<br>
<p><a href='<?=$this->url->create('blog/add')?>'>Add post</a> | <a href='<?=$this->url->create('blog/active')?>'>Active blogs</a> | <a href='<?=$this->url->create('blog/inactive')?>'>Inactive blogs</a> | <a href='<?=$this->url->create('blog/trash')?>'>Deleted blogs</a> | <a href='<?=$this->url->create('blog/setup')?>'>Reset</a></p>