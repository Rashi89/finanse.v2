<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-factory-psr17 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

use PhpExtended\HttpMessage\UriFactory;
use PHPUnit\Framework\TestCase;

/**
 * UriFactoryTest class file.
 * 
 * @author Anastaszor
 * @covers \PhpExtended\HttpMessage\UriFactory
 */
class UriFactoryTest extends TestCase
{
	
	/**
	 * The object to test.
	 * 
	 * @var UriFactory
	 */
	protected $_object;
	
	public function testToString() : void
	{
		$class = \get_class($this->_object);
		$this->assertEquals($class.'@'.\spl_object_hash($this->_object), $this->_object->__toString());
	}
	
	public function testCreateEmptyUri() : void
	{
		$uri = $this->_object->createUri();
		$this->assertEquals('https', $uri->getScheme());
		$this->assertEmpty($uri->getAuthority());
		$this->assertEquals('/', $uri->getPath());
		$this->assertEmpty($uri->getQuery());
		$this->assertEmpty($uri->getFragment());
	}
	
	public function testCreateHostPortUrl() : void
	{
		$uri = $this->_object->createUri('//toto:tata@example.com:8888');
		$this->assertEquals('https', $uri->getScheme());
		$this->assertEquals('toto:tata', $uri->getUserInfo());
		$this->assertEquals('example.com', $uri->getHost());
		$this->assertEquals(8888, $uri->getPort());
		$this->assertEquals('/', $uri->getPath());
		$this->assertEmpty($uri->getQuery());
		$this->assertEmpty($uri->getFragment());
	}
	
	public function testCreateQueryUrl() : void
	{
		$uri = $this->_object->createUri('?foo=bar');
		$this->assertEquals('https', $uri->getScheme());
		$this->assertEmpty($uri->getAuthority());
		$this->assertEquals('/', $uri->getPath());
		$this->assertEquals('foo=bar', $uri->getQuery());
		$this->assertEmpty($uri->getFragment());
	}
	
	public function testCreateFragmentUrl() : void
	{
		$uri = $this->_object->createUri('#foobar');
		$this->assertEquals('https', $uri->getScheme());
		$this->assertEmpty($uri->getAuthority());
		$this->assertEquals('/', $uri->getPath());
		$this->assertEmpty($uri->getQuery());
		$this->assertEquals('foobar', $uri->getFragment());
	}
	
	public function testCreateFailed() : void
	{
		$this->expectException(InvalidArgumentException::class);
		
		$this->_object->createUri('//;:@####////');
	}
	
	/**
	 * {@inheritdoc}
	 * @see \PHPUnit\Framework\TestCase::setUp()
	 */
	protected function setUp() : void
	{
		$this->_object = new UriFactory();
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
