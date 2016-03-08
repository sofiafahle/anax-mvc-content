<?php

namespace Anax\Blog;
 
/**
 * A controller for a blog and its posts.
 *
 */
class BlogController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable,
        \Anax\MVC\TRedirectHelpers;
	
	/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize()
	{
		$this->blog = new \Anax\Blog\Blog();
		$this->blog->setDI($this->di);
	}
	
	public function setupAction()
	{
		$this->db->dropTableIfExists('blog')->execute();
	 
		$this->db->createTable(
			'blog',
			[
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'author' => ['varchar(20)', 'not null'],
				'title' => ['varchar(80)', 'not null'],
				'content' => ['text', 'not null'],
				'slug' => ['varchar(80)', 'unique'],
				'filter' => ['varchar(80)'],
				'created' => ['datetime'],
				'updated' => ['datetime'],
				'deleted' => ['datetime'],
				'inactivated' => ['datetime'],
			]
		)->execute();
		
		 $this->db->insert(
			'blog',
			['author', 'title', 'content', 'slug', 'filter', 'created']
		);
	 
		$now = gmdate('Y-m-d H:i:s');
	 
		$this->db->execute([
			'admin',
			'A starter post',
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc egestas eros ut aliquam porta. Duis imperdiet pulvinar viverra. Curabitur molestie, erat non faucibus semper, turpis nulla vehicula sem, quis sodales erat eros eget diam. Pellentesque ac enim vitae sem rhoncus ultrices ac non lacus. Praesent id rhoncus lorem. Proin at leo eget elit imperdiet faucibus. Phasellus laoreet nec nisi non mollis.
			
Duis nisl turpis, tempor et rutrum at, bibendum et mi. Morbi luctus libero id suscipit mollis. Vivamus id vehicula erat. Ut eu nisi ut nibh suscipit iaculis eget a tortor. Aliquam sodales consequat erat, tempus fermentum nulla tincidunt vitae. Praesent vestibulum tempus iaculis. Curabitur sagittis eros at nisi imperdiet, et semper arcu lobortis. Vestibulum eget ligula imperdiet, ultricies lectus convallis, placerat arcu. Maecenas ut ligula a magna dictum aliquam ut quis arcu. Etiam sed quam non sapien facilisis scelerisque. Aenean interdum, tellus eget pulvinar porttitor, lorem tellus laoreet neque, vitae mollis velit nunc sed odio. Ut posuere nunc id neque malesuada volutpat. Pellentesque eget augue sollicitudin, suscipit lectus ultricies, dictum elit. Morbi feugiat fermentum diam eget sodales. Mauris ut diam nec orci vestibulum imperdiet. Nam faucibus neque rutrum mi congue, vitae congue est tincidunt.',
			$this->blog->slugify('A starter post'),
			'nl2br',
			$now
		]);
	 
		$this->db->execute([
			'admin',
			'Trying some markdown',
			'####Lorem ipsum dolor sit amet
consectetur adipiscing elit. Nunc egestas eros ut aliquam porta. Duis imperdiet pulvinar viverra. Curabitur molestie, erat non faucibus semper, turpis nulla vehicula sem, quis sodales erat eros eget diam. **Pellentesque** ac enim vitae sem rhoncus ultrices ac non lacus. Praesent id rhoncus lorem. Proin at leo eget elit imperdiet faucibus. Phasellus laoreet nec nisi non mollis.
			
Duis nisl turpis, tempor et rutrum at, bibendum et mi. Morbi luctus libero id suscipit mollis. Vivamus id vehicula erat. Ut eu nisi ut nibh suscipit iaculis eget a tortor. Aliquam sodales consequat erat, tempus fermentum nulla tincidunt vitae. Praesent vestibulum tempus iaculis. Curabitur sagittis eros at nisi imperdiet, et semper arcu lobortis. Vestibulum eget ligula imperdiet, ultricies lectus convallis, placerat arcu. Maecenas ut ligula a magna dictum aliquam ut quis arcu. Etiam sed quam non sapien facilisis scelerisque. Aenean interdum, tellus eget pulvinar porttitor, lorem tellus laoreet neque, vitae mollis velit nunc sed odio. Ut posuere nunc id neque malesuada volutpat. Pellentesque eget augue sollicitudin, suscipit lectus ultricies, dictum elit. Morbi feugiat fermentum diam eget sodales. Mauris ut diam nec orci vestibulum imperdiet. Nam faucibus neque rutrum mi congue, vitae congue est tincidunt.',
			$this->blog->slugify('Trying some markdown'),
			'nl2br,markdown',
			$now
		]);
		
		$this->redirectTo('blog/list');
	}
	
	public function indexAction()
	{
		$all = $this->blog->findAll();
	 
		$this->theme->setTitle("A blog");
		$this->views->add('blog/view-blog', [
			'posts' => $all,
			'title' => "A Blog",
		]);
	}
	
	/**
	 * List all blogposts.
	 *
	 * @return void
	 */
	public function listAction()
	{
		$all = $this->blog->findAll();
	 
		$this->theme->setTitle("List all posts");
		$this->views->add('blog/list', [
			'posts' => $all,
			'title' => "All blogposts",
		]);
	}
	
	/**
	 * View blogpost with slug.
	 *
	 * @param int $slug of post to display
	 *
	 * @return void
	 */
	public function viewAction($slug = null)
	{
		$blog = $this->blog->findWhere('slug', $slug);
		$post = $blog[0];	 
	 
		$this->theme->setTitle("View post with id");
		$this->views->add('blog/view-post', [
			'post' => $post,
		]);
	}
	
	/**
	 * Add new blogpost.
	 *
	 *
	 * @return void
	 */
	public function addAction()
	{
		
		$form = new \Anax\Blog\CFormAddPost();
        $form->setDI($this->di);
		
		// Check the status of the form
        $form->check();
	 
		$this->di->theme->setTitle("Add blogpost");
        $this->di->views->add('default/page', [
            'title' => "Add a blogpost",
            'content' => $form->getHTML()
        ]);
	}
	
	/**
	 * Edit a blogpost.
	 *
	 * @param string $id of post to edit.
	 *
	 * @return void
	 */
	public function updateAction($id = null)
	{
		
		if (!$id) {
			$this->redirectTo('blog');
		}
		
		$blog = $this->blog->find($id);
		
		$form = new \Anax\Blog\CFormUpdatePost($blog);
        $form->setDI($this->di);
		
		// Check the status of the form
        $form->check();
	 
		$this->di->theme->setTitle("Update blogpost");
        $this->di->views->add('default/page', [
            'title' => "Update a blogpost",
            'content' => $form->getHTML()
        ]);
	}
	
	/**
	 * Delete blogpost.
	 *
	 * @param integer $id of post to delete.
	 *
	 * @return void
	 */
	public function deleteAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$res = $this->blog->delete($id);
	 
		$this->redirectTo('blog/list');
	}
	
	/**
	 * Delete (soft) blogpost.
	 *
	 * @param integer $id of post to delete.
	 *
	 * @return void
	 */
	public function softDeleteAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$now = gmdate('Y-m-d H:i:s');
	 
		$blog = $this->blog->find($id);
	 
		$blog->deleted = $now;
		$blog->save();
	
		$this->redirectTo('blog/trash');
	}
	
	/**
	 * Restore (soft) deleted blogpost.
	 *
	 * @param integer $id of post to restore.
	 *
	 * @return void
	 */
	public function restoreAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$blog = $this->blog->find($id);
	 
		$blog->deleted = null;
		$blog->save();
	 
		$this->redirectTo('blog');
	}
	
	/**
	 * Activate blogpost.
	 *
	 * @param integer $id of upost to activate.
	 *
	 * @return void
	 */
	public function activateAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$blog = $this->blog->find($id);
	 
		$blog->inactivated = null;
		$blog->save();
	 
		$this->redirectTo('blog/list');
	}
	
	/**
	 * Inactivate blogpost.
	 *
	 * @param integer $id of post to inactivate.
	 *
	 * @return void
	 */
	public function inactivateAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
		
		$now = gmdate('Y-m-d H:i:s');
	 
		$blog = $this->blog->find($id);
	 
		$blog->inactivated = $now;
		$blog->save();
	 
		$this->redirectTo('blog/inactive');
	}
	
	/**
	 * List all active and not deleted blogposts.
	 *
	 * @return void
	 */
	public function activeAction()
	{
		$all = $this->blog->query()
			->where('inactivated IS NULL')
			->andWhere('deleted is NULL')
			->execute();
	 
		$this->theme->setTitle("Posts that are active");
		$this->views->add('blog/active', [
			'posts' => $all,
			'title' => "Blogposts that are active",
		]);
	}
	
	/**
	 * List all inactive blogposts.
	 *
	 * @return void
	 */
	public function inactiveAction()
	{
		$all = $this->blog->query()
			->where('inactivated IS NOT NULL')
			->execute();
	 
		$this->theme->setTitle("Posts that are inactive");
		$this->views->add('blog/inactive', [
			'posts' => $all,
			'title' => "Blogposts that are inactive",
		]);
	}
	
	/**
	 * List all deleted blogposts.
	 *
	 * @return void
	 */
	public function trashAction()
	{
		$all = $this->blog->query()
			->where('deleted IS NOT NULL')
			->execute();
	 
		$this->theme->setTitle("Posts that are deleted");
		$this->views->add('blog/deleted', [
			'posts' => $all,
			'title' => "Blogposts that are deleted",
		]);
	}
	 
}