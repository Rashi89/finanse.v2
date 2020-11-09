<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-psr7 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

use PhpExtended\HttpMessage\UriPathComponent;
use PHPUnit\Framework\TestCase;

/**
 * UriPathComponentTest class file.
 * 
 * @author Anastaszor
 * @covers \PhpExtended\HttpMessage\UriPathComponent
 */
class UriPathComponentTest extends TestCase
{
	
	/**
	 * The object to test.
	 * 
	 * @var string
	 */
	protected $_object = null;
	
	public function testToString() : void
	{
		$this->assertEquals('', $this->_object->__toString());
	}
	
	public function testAbsorbEmpty() : void
	{
		$this->_object->absorb('');
		$this->assertEquals(new UriPathComponent(), $this->_object);
	}
	
	public function testAbsorbPath() : void
	{
		$this->_object->absorb('/path/to/absorb');
		$expected = new UriPathComponent('/path/to/absorb');
		$this->assertEquals($expected, $this->_object);
	}
	
	public function testAbsorbPathLocal() : void
	{
		$this->_object->absorb('/path/./to/./absorb');
		$expected = new UriPathComponent('/path/to/absorb');
		$this->assertEquals($expected, $this->_object);
	}
	
	public function testAbsorbPathRewind() : void
	{
		$this->_object->absorb('/path/to/../absorb');
		$expected = new UriPathComponent('/path/absorb');
		$this->assertEquals($expected, $this->_object);
	}
	
	public function testAbsorbPathTraversal() : void
	{
		$this->_object->absorb('/path/to/absorb/.././../../././././../../../etc/passwd');
		$expected = new UriPathComponent('/etc/passwd');
		$this->assertEquals($expected, $this->_object);
	}
	
	public function testAbsorbPathTraversal2() : void
	{
		$this->_object->absorb('../../../../../../../path/to/absorb');
		$expected = new UriPathComponent('/path/to/absorb');
		$this->assertEquals($expected, $this->_object);
	}
	
	public function testAbsorbSuccessive() : void
	{
		$this->_object->absorb('/path/to/absorb');
		$this->_object->absorb('/path/number/two');
		$expected = new UriPathComponent('/path/to/absorb/path/number/two');
		$this->assertEquals($expected, $this->_object);
	}
	
	public function testAbsorbToString() : void
	{
		$this->_object->absorb('/path/%2Fto%2F/absorb');
		$this->assertEquals('path/%2Fto%2F/absorb', $this->_object->__toString());
	}
	
	/**
	 * {@inheritdoc}
	 * @see \PHPUnit\Framework\TestCase::setUp()
	 */
	protected function setUp() : void
	{
		$this->_object = new UriPathComponent();
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
