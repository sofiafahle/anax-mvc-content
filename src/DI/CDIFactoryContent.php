<?php

namespace Anax\DI;

/**
 * Extended factory for Anax database content management.
 *
 */
class CDIFactoryContent extends CDIFactoryDefault
{
   /**
     * Construct.
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setShared('db', function() {
			$db = new \Mos\Database\CDatabaseBasic();
			$db->setOptions(require ANAX_APP_PATH . 'config/config_mysql.php');
			$db->connect();
			return $db;
		});
		
		$this->setShared('form', function() {
			$form = new \Mos\HTMLForm\CForm();
			return $form;
		});
		
		$this->setShared('PageController', function() {
			$pageController = new \Anax\Page\PageController();
			$pageController->setDI($this);
			return $pageController;
		});
		
		$this->setShared('BlogController', function() {
			$blogController = new \Anax\Blog\BlogController();
			$blogController->setDI($this);
			return $blogController;
		});
    }
}