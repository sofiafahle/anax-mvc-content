<?php

namespace Anax\Blog;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormAddPost extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;
	
	public $blog;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct([], [
			'author' => [
                'type'        => 'text',
                'label'       => 'Author:',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
			'title' => [
                'type'        => 'text',
                'label'       => 'Title:',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'content' => [
                'type'        => 'textarea',
                'label'       => 'Content:',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
			'filter' => [
                'type'        => 'text',
                'label'       => 'Filter:',
				'description' => 'separated by comma, no spaces',
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
		$this->blog = new \Anax\Blog\Blog();
        $this->blog->setDI($this->di);
		
		$now = gmdate('Y-m-d H:i:s');
		
		$this->blog->save([
			'author' 	=> htmlentities($this->Value('author'), null, 'UTF-8'),
			'title' 	=> htmlentities($this->Value('title'), null, 'UTF-8'),
            'content'	=> htmlentities($this->Value('content'), null, 'UTF-8'),
			'slug'		=> $this->blog->slugify($this->Value('title')),
			'filter' 	=> $this->Value('filter') ? htmlentities($this->Value('filter')) : '',
			'created'	=> $now,
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
        $this->AddOUtput("<p><i>Form was submitted and the post was added successfully.</i></p>");
        $this->redirectTo('blog/list');
    }



    /**
     * Callback What to do when form could not be processed?
     *
     */
    public function callbackFail()
    {
        $this->AddOutput("<p><i>Form was submitted and the Check() method returned false.</i></p>");
        $this->redirectTo();
    }
}
