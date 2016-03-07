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
		
		$di->set('pageController', function() use ($di) {
			$pageController = new \Anax\Page\PageController();
			$pageController->setDI($di);
			return $pageController;
		});
		
		$di->set('blogController', function() use ($di) {
			$blogController = new \Anax\Blog\BlogController();
			$blogController->setDI($di);
			return $blogController;
		});
    }
}
