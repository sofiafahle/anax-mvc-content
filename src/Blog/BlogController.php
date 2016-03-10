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
		require('setup.php');
		
		$this->redirectTo('blog/list');
	}
	
	public function indexAction()
	{
		$all = $this->blog->findAllOrder('id', 'DESC');
	 
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