<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-psr7 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

use PhpExtended\HttpMessage\UriQueryComponent;
use PHPUnit\Framework\TestCase;

/**
 * UriQueryComponentTest class file.
 * 
 * @author Anastaszor
 * @covers \PhpExtended\HttpMessage\UriQueryComponent
 */
class UriQueryComponentTest extends TestCase
{
	
	/**
	 * The object to test.
	 * 
	 * @var UriQueryComponent
	 */
	protected $_object;
	
	public function testToString() : void
	{
		$this->assertEquals('', $this->_object->__toString());
	}
	
	public function testIsEmpty() : void
	{
		$this->assertTrue($this->_object->isEmpty());
	}
	
	public function testIsEmpty2() : void
	{
		$this->assertFalse($this->_object->absorb('foo')->isEmpty());
	}
	
	public function testAbsorbEmpty() : void
	{
		$this->_object->absorb('');
		$this->assertEquals(new UriQueryComponent(), $this->_object);
	}
	
	public function testAbsorbQuery() : void
	{
		$this->_object->absorb('foo=bar&baz=tux');
		$expected = new UriQueryComponent('foo=bar&baz=tux');
		$this->assertEquals($expected, $this->_object);
		$this->assertEquals('foo=bar&baz=tux', $this->_object->__toString());
	}
	
	public function testAbsorbArray() : void
	{
		$this->_object->absorb('foo=bar&foo=baz&foo=tux');
		$expected = new UriQueryComponent('foo=bar&foo=baz&foo=tux');
		$this->assertEquals($expected, $this->_object);
		$this->assertEquals('foo=bar&foo=baz&foo=tux', $this->_object->__toString());
	}
	
	public function testAbsorbRealArray() : void
	{
		$this->_object->absorb('foo=bar&foo[]=baz&foo[]=tux');
		$expected = new UriQueryComponent('foo=bar&foo[]=baz&foo[]=tux');
		$this->assertEquals($expected, $this->_object);
		$this->assertEquals('foo=bar&foo%5B%5D=baz&foo%5B%5D=tux', $this->_object->__toString());
	}
	
	public function testAbsorbMulti() : void
	{
		$this->_object->absorb('foo=bar&baz=tux');
		$this->_object->absorb('foo=tux&bar=baz');
		$expected = new UriQueryComponent('foo=bar&baz=tux&foo=tux&bar=baz');
		$this->assertEquals($expected, $this->_object);
		$this->assertEquals('foo=bar&foo=tux&baz=tux&bar=baz', $this->_object->__toString());
	}
	
	public function testAbsorbSame() : void
	{
		$this->_object->absorb('foo=bar&foo=bar&foo=bar');
		$expected = new UriQueryComponent('foo=bar&foo=bar&foo=bar');
		$this->assertEquals($expected, $this->_object);
		$this->assertEquals('foo=bar&foo=bar&foo=bar', $this->_object->__toString());
	}
	
	/**
	 * {@inheritdoc}
	 * @see \PHPUnit\Framework\TestCase::setUp()
	 */
	protected function setUp() : void
	{
		$this->_object = new UriQueryComponent();
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
