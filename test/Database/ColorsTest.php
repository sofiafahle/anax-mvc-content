<?php

namespace Anax\Database;
 
/**
 * Tests for base class for database models
 *
 */
class CDatabaseModelTest extends \PHPUnit_Framework_TestCase implements \Anax\DI\IInjectionAware
{
	use \Anax\DI\TInjectable;
	
	static private $colors;
	
	/**
     * setUpBeforeClass, called once for all tests in this class.
     *
     * @return void
     *
     */
    public static function setUpBeforeClass()
    {
		
		$di = new \Anax\DI\CDIFactoryDefault();
		
		$di->setShared('db', function() {
			$db = new \Mos\Database\CDatabaseBasic();
			$db->setOptions(['dsn' => "sqlite:memory::", "verbose" => false]);
			$db->connect();
			return $db;
		});
		
		self::$colors = new Colors();
		self::$colors->setDI($di);
		self::$colors->init();
		
	}
	
	public function testgetSource()
    {
		$res = self::$colors->getSource();
		$exp = "colors";
		
		$this->assertEquals($res, $exp, "Table name missmatch.");
    }
 
}