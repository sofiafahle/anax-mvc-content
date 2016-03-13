<?php

namespace Anax\Page;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormUpdatePage extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;
		
	public $page;
	
    /**
     * Constructor
     *
     */
    public function __construct($page)
    {
		if(!$page) {
			die('Not a valid page!');
		}
		
		$this->page = $page;
		
        parent::__construct([], [
			'id' => [
				'type'		  => 'hidden',
				'value'       => $page->id,
				'required'    => true,
				'validation'  => ['not_empty'],
			],				  
			'title' => [
                'type'        => 'text',
                'label'       => 'Title:',
				'value'       => $page->title,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'content' => [
                'type'        => 'textarea',
                'label'       => 'Content:',
				'value'       => $page->content,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
			'filter' => [
                'type'        => 'text',
                'label'       => 'Filter:',
				'description' => 'separated by comma, no spaces',
				'value'       => $page->filter,
            ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSubmit'],
            ],
        ]);
    }



    /**
     * Customise the check() method.
     *
     * @param callable $callIfSuccess handler to call if function returns true.
     * @param callable $callIfFail    handler to call if function returns true.
     */
    public function check($callIfSuccess = null, $callIfFail = null)
    {
        return parent::check([$this, 'callbackSuccess'], [$this, 'callbackFail']);
    }



    /**
     * Callback for submit-button.
     *
     */
    public function callbackSubmit()
    {
		$this->page = new \Anax\Page\Page();
        $this->page->setDI($this->di);
		
		$now = gmdate('Y-m-d H:i:s');
		
		$this->page->save([
			'id'		=> $this->Value('id'),
			'title' 	=> htmlentities($this->Value('title'), null, 'UTF-8'),
            'content'	=> htmlentities($this->Value('content'), null, 'UTF-8'),
			'slug'		=> $this->page->slugify($this->Value('title')),
			'filter' 	=> $this->Value('filter') ? htmlentities($this->Value('filter')) : '',
			'updated'	=> $now,
		]);
		
        $this->saveInSession = true;
        return true;
    }



    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
        $this->AddOUtput("<p><i>Form was submitted and the page was updated successfully.</i></p>");
        $this->redirectTo('page/list');
    }



    /**
     * Callback What to do when form could not be processed?
     *
     */
    public function callbackFail()
    {
        $this->AddOutput("<p><i>Form was submitted and the Check() method returned false.</i></p>");
    }
}
