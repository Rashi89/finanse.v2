<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-psr7 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

namespace PhpExtended\HttpMessage;

use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Stringable;

/**
 * StringStream class file.
 * 
 * This class represents a basic read and write stream implementation of
 * the StreamInterface which relies on an internal in-memory string.
 * 
 * @author Anastaszor
 */
class StringStream implements StreamInterface, Stringable
{
	
	/**
	 * The position for seeking into the stream, if needed.
	 * 
	 * @var integer
	 */
	protected $_pos = 0;
	
	/**
	 * The full string of this stream.
	 * 
	 * @var string
	 */
	protected $_string = '';
	
	/**
	 * Builds a new string stream.
	 * 
	 * @param ?string $string
	 */
	public function __construct(?string $string = null)
	{
		$this->_string = (string) $string;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::__toString()
	 */
	public function __toString() : string
	{
		return $this->_string;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::close()
	 */
	public function close() : void
	{
		// nothing to do
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::detach()
	 */
	public function detach()
	{
		return null;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::getSize()
	 */
	public function getSize() : int
	{
		return \strlen($this->_string);
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::tell()
	 */
	public function tell() : int
	{
		return $this->_pos;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::eof()
	 */
	public function eof() : bool
	{
		try
		{
			return $this->tell() === $this->getSize() - 1;
		}
		catch(RuntimeException $e)
		{
			return true;
		}
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::isSeekable()
	 */
	public function isSeekable() : bool
	{
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::seek()
	 */
	public function seek($offset, $whence = \SEEK_SET) : void
	{
		switch($whence)
		{
			case \SEEK_CUR:
				$this->_pos += $offset;
				break;
			
			case \SEEK_END:
				$this->_pos = $this->getSize() + $offset;
				break;
			
			case \SEEK_SET:
				$this->_pos = $offset;
		}
		
		if($this->getSize() < $this->_pos)
			$this->_pos = $this->getSize() - 1;
		if(0 > $this->_pos)
			$this->_pos = 0;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::rewind()
	 */
	public function rewind() : void
	{
		$this->_pos = 0;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::isWritable()
	 */
	public function isWritable() : bool
	{
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::write()
	 */
	public function write($string) : int
	{
		$oldlen = \strlen($this->_string);
		$len = \strlen($string);
		$this->_string = ((string) \substr($this->_string, 0, $this->_pos))
						.$string
						.((string) \substr($this->_string, $this->_pos + $len));
		
		return \strlen($this->_string) - $oldlen;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::isReadable()
	 */
	public function isReadable() : bool
	{
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::read()
	 */
	public function read($length) : string
	{
		return \substr($this->_string, $this->_pos, $length);
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::getContents()
	 */
	public function getContents() : string
	{
		return $this->_string;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::getMetadata()
	 */
	public function getMetadata($key = null)
	{
		return [
			'timed_out' => false,
			'blocked' => false,
			'eof' => $this->eof(),
			'unread_bytes' => $this->getSize() - $this->_pos,
			'stream_type' => static::class,
			'wrapper_type' => 'data',
			'wrapper_data' => 'text/plain',
			'mode' => 'c+',
			'seekable' => true,
			'uri' => 'data:text/plain',
		];
	}
	
}
