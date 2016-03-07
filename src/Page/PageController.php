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
		$this->db->dropTableIfExists('page')->execute();
	 
		$this->db->createTable(
			'page',
			[
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
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
			'page',
			['title', 'content', 'slug', 'filter', 'created']
		);
	 
		$now = gmdate('Y-m-d H:i:s');
	 
		$this->db->execute([
			'A starter page',
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc egestas eros ut aliquam porta. Duis imperdiet pulvinar viverra. Curabitur molestie, erat non faucibus semper, turpis nulla vehicula sem, quis sodales erat eros eget diam. Pellentesque ac enim vitae sem rhoncus ultrices ac non lacus. Praesent id rhoncus lorem. Proin at leo eget elit imperdiet faucibus. Phasellus laoreet nec nisi non mollis. 
			
Duis nisl turpis, tempor et rutrum at, bibendum et mi. Morbi luctus libero id suscipit mollis. Vivamus id vehicula erat. Ut eu nisi ut nibh suscipit iaculis eget a tortor. Aliquam sodales consequat erat, tempus fermentum nulla tincidunt vitae. Praesent vestibulum tempus iaculis. Curabitur sagittis eros at nisi imperdiet, et semper arcu lobortis. Vestibulum eget ligula imperdiet, ultricies lectus convallis, placerat arcu. Maecenas ut ligula a magna dictum aliquam ut quis arcu. Etiam sed quam non sapien facilisis scelerisque. Aenean interdum, tellus eget pulvinar porttitor, lorem tellus laoreet neque, vitae mollis velit nunc sed odio. Ut posuere nunc id neque malesuada volutpat. Pellentesque eget augue sollicitudin, suscipit lectus ultricies, dictum elit. Morbi feugiat fermentum diam eget sodales. Mauris ut diam nec orci vestibulum imperdiet. Nam faucibus neque rutrum mi congue, vitae congue est tincidunt.',
			$this->page->slugify('A starter page'),
			'nl2br',
			$now
		]);
	 
		$this->db->execute([
			'Trying some markdown',
			'###Lorem ipsum dolor sit amet
consectetur adipiscing elit. Nunc egestas eros ut aliquam porta. Duis imperdiet pulvinar viverra. Curabitur molestie, erat non faucibus semper, turpis nulla vehicula sem, quis sodales erat eros eget diam. **Pellentesque** ac enim vitae sem rhoncus ultrices ac non lacus. Praesent id rhoncus lorem. Proin at leo eget elit imperdiet faucibus. Phasellus laoreet nec nisi non mollis.

Duis nisl turpis, tempor et rutrum at, bibendum et mi. Morbi luctus libero id suscipit mollis. Vivamus id vehicula erat. Ut eu nisi ut nibh suscipit iaculis eget a tortor. Aliquam sodales consequat erat, tempus fermentum nulla tincidunt vitae. Praesent vestibulum tempus iaculis. Curabitur sagittis eros at nisi imperdiet, et semper arcu lobortis. Vestibulum eget ligula imperdiet, ultricies lectus convallis, placerat arcu. Maecenas ut ligula a magna dictum aliquam ut quis arcu. Etiam sed quam non sapien facilisis scelerisque. Aenean interdum, tellus eget pulvinar porttitor, lorem tellus laoreet neque, vitae mollis velit nunc sed odio. Ut posuere nunc id neque malesuada volutpat. Pellentesque eget augue sollicitudin, suscipit lectus ultricies, dictum elit. Morbi feugiat fermentum diam eget sodales. Mauris ut diam nec orci vestibulum imperdiet. Nam faucibus neque rutrum mi congue, vitae congue est tincidunt.',
			$this->page->slugify('Trying some markdown'),
			'markdown,nl2br',
			$now
		]);
		
		$this->redirectTo('page');
	}
	
	public function indexAction()
	{
		$all = $this->page->findAll();
	 
		$this->theme->setTitle("Editable pages");
		$this->views->add('page/list', [
			'pages' => $all,
			'title' => "Pages of this website",
		]);
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
		$page = $this->page->findWhere('slug', $slug);
		
		if (!$page) {
			die('No such page!');
		}
		
		$page = $page[0];
	 
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