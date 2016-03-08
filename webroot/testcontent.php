<?php 
/**
 * This is a Anax pagecontroller.
 *
 */

// Get environment & autoloader and the $app-object.
require __DIR__.'/config_with_app.php'; 


// Prepare the page content
$app->theme->setVariable('title', "Hello World Pagecontroller");

$app->router->add('', function() use ($app) {
	
	$url = $app->request->getCurrentURL();
	
	$content = '<strong>A module for handling content in Anax-MVC</strong><br>
	Setup: <a href="' . $url . '/page/setup">Pages</a>&nbsp;|&nbsp;<a href="' . $url . '/blog/setup">Blog</a><br>
	List: <a href="' . $url . '/page/list">Pages</a>&nbsp;|&nbsp;<a href="' . $url . '/blog/list">Blog</a>';
	
	$app->views->add('default/page', [
		'title'   => 'Test page',
        'content' => $content
    ]);
	
});

// Render the response using theme engine.
$app->router->handle();
$app->theme->render();