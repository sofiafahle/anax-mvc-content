<?php

namespace Anax\Blog;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormUpdatePost extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;

	public $blog;
	

    /**
     * Constructor
     *
     */
    public function __construct($blog)
    {
		if(!$blog) {
			die('Not a valid blogpost!');
		}
		
		$this->blog = $blog;
		
        parent::__construct([], [
			'id' => [
				'type'  	  => 'hidden',
				'value'       => $blog->id,
				'required'    => true,
				'validation'  => ['not_empty'],
			],
			'author' => [
                'type'        => 'text',
                'label'       => 'Author:',
				'value'       => $blog->author,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
			'title' => [
                'type'        => 'text',
                'label'       => 'Title:',
				'value'       => $blog->title,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'content' => [
                'type'        => 'textarea',
                'label'       => 'Content:',
				'value'       => $blog->content,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
			'filter' => [
                'type'        => 'text',
                'label'       => 'Filter:',
				'description' => 'separated by comma, no spaces',
				'value'       => $blog->filter,
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
		$now = gmdate('Y-m-d H:i:s');
		
		$this->blog->save([
			'id' 		=> $this->Value('id'),
			'author' 	=> htmlentities($this->Value('author'), null, 'UTF-8'),
			'title' 	=> htmlentities($this->Value('title'), null, 'UTF-8'),
            'content'	=> htmlentities($this->Value('content'), null, 'UTF-8'),
			'slug'		=> $this->blog->slugify($this->Value('title')),
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
        $this->AddOUtput("<p><i>Form was submitted and the post was updated successfully.</i></p>");
        $this->redirectTo('blog/list');
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
