<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-psr7 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

use PhpExtended\HttpMessage\Uri;
use PHPUnit\Framework\TestCase;

/**
 * UriTest class file.
 * 
 * @author Anastaszor
 * @covers \PhpExtended\HttpMessage\Uri
 */
class UriTest extends TestCase
{
	
	/**
	 * The object to test.
	 * 
	 * @var Uri
	 */
	protected $_object;
	
	public function testToString() : void
	{
		$this->assertEquals('/', $this->_object->__toString());
	}
	
	public function testHost() : void
	{
		$object = $this->_object->withHost('host.name.com');
		$this->assertEquals('host.name.com', $object->getHost());
		$this->assertEquals('https://host.name.com/', $object->__toString());
	}
	
	public function testEmptyHost() : void
	{
		$object = $this->_object->withHost(null);
		$this->assertEquals(new Uri(), $object);
	}
	
	public function testInvalidHost() : void
	{
		$this->expectException(InvalidArgumentException::class);
		
		$this->_object->withHost('example...com');
	}
	
	public function testScheme() : void
	{
		$object = $this->_object->withHost('host.name.com');
		$object = $object->withScheme('http');
		$this->assertEquals('http', $object->getScheme());
		$this->assertEquals('http://host.name.com/', $object->__toString());
	}
	
	public function testSameScheme() : void
	{
		$object = $this->_object->withScheme('https');
		$this->assertEquals(new Uri(), $object);
	}
	
	public function testFailedScheme() : void
	{
		$this->expectException(InvalidArgumentException::class);
		
		$this->_object->withScheme('htttps');
	}
	
	public function testAuthority() : void
	{
		$object = $this->_object->withHost('host.name.com');
		$object = $object->withUserInfo('username', 'password');
		$this->assertEquals('username:password', $object->getUserInfo());
		$this->assertEquals('https://username:password@host.name.com/', $object->__toString());
	}
	
	public function testEmptyUserInfo() : void
	{
		$object = $this->_object->withUserInfo('', 'password');
		$this->assertEquals(new Uri(), $object);
	}
	
	public function testPort() : void
	{
		$object = $this->_object->withHost('host.name.com');
		$object = $object->withPort(8888);
		$this->assertEquals(8888, $object->getPort());
		$this->assertEquals('https://host.name.com:8888/', $object->__toString());
	}
	
	public function testEmptyPort() : void
	{
		$object = $this->_object->withPort(8888);
		$object = $object->withPort(null);
		$this->assertEquals(new Uri(), $object);
	}
	
	public function testEmptyPort2() : void
	{
		$object = $this->_object->withPort(null);
		$this->assertEquals(new Uri(), $object);
	}
	
	public function testSamePort() : void
	{
		$object = $this->_object->withPort(8888);
		$object = $object->withPort(8888);
		$this->assertEquals(8888, $object->getPort());
	}
	
	public function testInvalidPort() : void
	{
		$this->expectException(InvalidArgumentException::class);
		
		$this->_object->withPort(88888);
	}
	
	public function testPath() : void
	{
		$object = $this->_object->withPath('/path/to/file');
		$this->assertEquals('/path/to/file', $object->getPath());
		$this->assertEquals('/path/to/file', $object->__toString());
	}
	
	public function testWithEmptyPath() : void
	{
		$object = $this->_object->withPath(null);
		$this->assertEquals('/', $object->getPath());
	}
	
	public function testRelativePath() : void
	{
		$object = $this->_object->withPath('/path/to/file');
		$object = $object->withPath('../this/file');
		$this->assertEquals('/path/to/this/file', $object->getPath());
	}
	
	public function testEmptyQuery() : void
	{
		$this->assertEquals('', $this->_object->getQuery());
	}
	
	public function testQuery() : void
	{
		$object = $this->_object->withQuery('foo=bar&baz=qux');
		$this->assertEquals('foo=bar&baz=qux', $object->getQuery());
		$this->assertEquals('/?foo=bar&baz=qux', $object->__toString());
	}
	
	public function testFragment() : void
	{
		$object = $this->_object->withfragment('AZERTYUIOPQSDFGHJKLM');
		$this->assertEquals('AZERTYUIOPQSDFGHJKLM', $object->getFragment());
		$this->assertEquals('/#AZERTYUIOPQSDFGHJKLM', $object->__toString());
	}
	
	public function testEmptyFragment() : void
	{
		$object = $this->_object->withFragment(null);
		$this->assertEquals(new Uri(), $object);
	}
	
	public function testDualFragment() : void
	{
		$object = $this->_object->withFragment('a');
		$object = $object->withFragment('a');
		$this->assertEquals('a', $object->getFragment());
	}
	
	/**
	 * {@inheritdoc}
	 * @see \PHPUnit\Framework\TestCase::setUp()
	 */
	protected function setUp() : void
	{
		$this->_object = new Uri();
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
