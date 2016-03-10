<?php

namespace Anax\Page;
 
/**
 * A controller for editable pages.
 *
 */
class PageController implements \Anax\DI\IInjectionAware
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
		$this->page = new \Anax\Page\Page();
		$this->page->setDI($this->di);
	}
	
	public function setupAction()
	{
		require('setup.php');
		
		$this->redirectTo('page');
	}
	
	public function indexAction()
	{
		$this->listAction();
	}

	
	/**
	 * List all pages.
	 *
	 * @return void
	 */
	public function listAction()
	{
		$all = $this->page->findAll();
	 
		$this->theme->setTitle("List all pages");
		$this->views->add('page/list', [
			'pages' => $all,
			'title' => "Editable pages",
		]);
	}
	
	/**
	 * Views page with with slug.
	 *
	 * @param int $slug of page to display
	 *
	 * @return void
	 */
	public function viewAction($slug = null)
	{
		$pages = $this->page->findWhere('slug', $slug);
		
		if (!$pages) {
			die('No such page!');
		}
		
		$page = $pages[0];
		
		$this->theme->setTitle($page->title);
		$this->views->add('page/view', [
			'page' => $page,
		]);
	}
	
	/**
	 * Add new page.
	 *
	 *
	 * @return void
	 */
	public function addAction()
	{
		
		$form = new \Anax\Page\CFormAddPage();
        $form->setDI($this->di);
		
		// Check the status of the form
        $form->check();
	 
		$this->di->theme->setTitle("Add page");
        $this->di->views->add('default/page', [
            'title' => "Add a page",
            'content' => $form->getHTML()
        ]);
	}
	
	/**
	 * Edit a page.
	 *
	 * @param string $id of page to edit.
	 *
	 * @return void
	 */
	public function updateAction($id = null)
	{
		
		if (!$id) {
			$this->redirectTo('page');
		}
		
		$page = $this->page->find($id);
		
		$form = new \Anax\Page\CFormUpdatePage($page);
        $form->setDI($this->di);
		
		// Check the status of the form
        $form->check();
	 
		$this->di->theme->setTitle("Update page");
        $this->di->views->add('default/page', [
            'title' => "Update a page",
            'content' => $form->getHTML()
        ]);
	}
	
	/**
	 * Delete page.
	 *
	 * @param integer $id of page to delete.
	 *
	 * @return void
	 */
	public function deleteAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$res = $this->page->delete($id);
	 
		$this->redirectTo('page');
	}
	
	/**
	 * Delete (soft) page.
	 *
	 * @param integer $id of page to delete.
	 *
	 * @return void
	 */
	public function softDeleteAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$now = gmdate('Y-m-d H:i:s');
	 
		$page = $this->page->find($id);
	 
		$page->deleted = $now;
		$page->save();
	
		$this->redirectTo('page/trash');
	}
	
	/**
	 * Restore (soft) deleted page.
	 *
	 * @param integer $id of page to restore.
	 *
	 * @return void
	 */
	public function restoreAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$page = $this->page->find($id);
	 
		$page->deleted = null;
		$page->save();
	 
		$this->redirectTo('page');
	}
	
	/**
	 * Activate page.
	 *
	 * @param integer $id of page to activate.
	 *
	 * @return void
	 */
	public function activateAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}

		$page = $this->page->find($id);
	 
		$page->inactivated =  null;
		$page->save();
	 
		$this->redirectTo('page');
	}
	
	/**
	 * Inactivate page.
	 *
	 * @param integer $id of page to inactivate.
	 *
	 * @return void
	 */
	public function inactivateAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
		
		$now = gmdate('Y-m-d H:i:s');
	 
		$page = $this->page->find($id);
	 
		$page->inactivated = $now;
		$page->save();
	 
		$this->redirectTo('page/inactive');
	}
	
	/**
	 * List all active and not deleted pages.
	 *
	 * @return void
	 */
	public function activeAction()
	{
		$all = $this->page->query()
			->where('inactivated IS NULL')
			->andWhere('deleted is NULL')
			->execute();
	 
		$this->theme->setTitle("Pages that are active");
		$this->views->add('page/active', [
			'pages' => $all,
			'title' => "Pages that are active",
		]);
	}
	
	/**
	 * List all inactive pages.
	 *
	 * @return void
	 */
	public function inactiveAction()
	{
		$all = $this->page->query()
			->where('inactivated IS NOT NULL')
			->execute();
	 
		$this->theme->setTitle("Pages that are inactive");
		$this->views->add('page/inactive', [
			'pages' => $all,
			'title' => "Pages that are inactive",
		]);
	}
	
	/**
	 * List all deleted pages.
	 *
	 * @return void
	 */
	public function trashAction()
	{
		$all = $this->page->query()
			->where('deleted IS NOT NULL')
			->execute();
	 
		$this->theme->setTitle("Pages that are deleted");
		$this->views->add('page/deleted', [
			'pages' => $all,
			'title' => "Pages that are deleted",
		]);
	}
	 
}