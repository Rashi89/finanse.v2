<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-factory-psr17 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

use PhpExtended\HttpMessage\RequestFactory;
use PhpExtended\HttpMessage\Uri;
use PHPUnit\Framework\TestCase;

/**
 * RequestFactoryTest class file.
 * 
 * @author Anastaszor
 * @covers \PhpExtended\HttpMessage\RequestFactory
 */
class RequestFactoryTest extends TestCase
{
	
	/**
	 * The object to test.
	 * 
	 * @var RequestFactory
	 */
	protected $_object;
	
	public function testToString() : void
	{
		$class = \get_class($this->_object);
		$this->assertEquals($class.'@'.\spl_object_hash($this->_object), $this->_object->__toString());
	}
	
	public function testRequest() : void
	{
		$request = $this->_object->createRequest('GET', '');
		
		$this->assertEquals('GET', $request->getMethod());
		$this->assertEquals('/', $request->getUri()->__toString());
	}
	
	public function testRequestUri() : void
	{
		$uri = new Uri();
		$uri = $uri->withHost('toto.com');
		
		$request = $this->_object->createRequest('POST', $uri);
		
		$this->assertEquals('POST', $request->getMethod());
		$this->assertEquals($uri, $request->getUri());
	}
	
	/**
	 * {@inheritdoc}
	 * @see \PHPUnit\Framework\TestCase::setUp()
	 */
	protected function setUp() : void
	{
		$this->_object = new RequestFactory();
	}
	
	/**
	 * {@inheritdoc}
	 * @see \PHPUnit\Framework\TestCase::tearDown()
	 */
	protected function tearDown() : void
	{
		$this->_object = null;
	}
	
}
