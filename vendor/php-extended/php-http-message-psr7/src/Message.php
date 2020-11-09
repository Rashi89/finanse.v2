<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-psr7 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

namespace PhpExtended\HttpMessage;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Stringable;

/**
 * Message class file.
 *
 * This class is a simple implementation of the MessageInterface.
 *
 * @author Anastaszor
 */
abstract class Message implements MessageInterface, Stringable
{
	
	public const HTTP_1_0 = '1.0';
	public const HTTP_1_1 = '1.1';
	
	/**
	 * The protocol version as string.
	 *
	 * @var string (e.g. "1.0" or "1.1")
	 */
	protected $_protocolVersion = self::HTTP_1_1;
	
	/**
	 * The http headers as string array.
	 *
	 * @var array<string, array<int, string>>
	 */
	protected $_headers = [];
	
	/**
	 * The http header keys with lowercase. The values are in the $_headers.
	 *
	 * // lowercase header => real header case value
	 * @var array<string, string>
	 */
	protected $_lkeys = [];
	
	/**
	 * The body of the message.
	 *
	 * @var ?StreamInterface
	 */
	protected $_body = null;
	
	/**
	 * {@inheritdoc}
	 * @see \Stringable::__toString()
	 */
	public function __toString() : string
	{
		return static::class.'@'.\spl_object_hash($this);
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\MessageInterface::getProtocolVersion()
	 */
	public function getProtocolVersion() : string
	{
		return $this->_protocolVersion;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\MessageInterface::withProtocolVersion()
	 */
	public function withProtocolVersion($version)
	{
		switch($version)
		{
			case self::HTTP_1_0:
			case self::HTTP_1_1:
				if($version === $this->_protocolVersion)
					return $this;
				
				$newobj = clone $this;
				$newobj->_protocolVersion = $version;
				
				return $newobj;
		}
		
		return $this;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\MessageInterface::getHeaders()
	 */
	public function getHeaders() : array
	{
		return $this->_headers;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\MessageInterface::hasHeader()
	 */
	public function hasHeader($name) : bool
	{
		return isset($this->_lkeys[\strtolower($name)]);
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\MessageInterface::getHeader()
	 */
	public function getHeader($name) : array
	{
		if($this->hasHeader($name))
			return $this->_headers[$this->_lkeys[\strtolower($name)]];
		
		return [];
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\MessageInterface::getHeaderLine()
	 */
	public function getHeaderLine($name) : string
	{
		if($this->hasHeader($name))
			return \implode(', ', $this->_headers[$this->_lkeys[\strtolower($name)]]);
		
		return '';
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\MessageInterface::withHeader()
	 */
	public function withHeader($name, $value)
	{
		$newobj = clone $this;
		
		if(isset($newobj->_lkeys[\strtolower($name)]))
			unset($newobj->_headers[$newobj->_lkeys[\strtolower($name)]]);
		
		if(!\is_array($value))
			$value = [$value];
		
		$newobj->_headers[$name] = \array_unique($value);
		$newobj->_lkeys[\strtolower($name)] = $name;
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\MessageInterface::withAddedHeader()
	 */
	public function withAddedHeader($name, $value)
	{
		$newobj = clone $this;
		if(!\is_array($value))
			$value = [$value];
		
		if(!isset($newobj->_headers[$name]))
			$newobj->_headers[$name] = [];
		
		foreach($value as $valueElem)
		{
			if(!\in_array($valueElem, $newobj->_headers[$name]))
				$newobj->_headers[$name][] = $valueElem;
		}
		$newobj->_lkeys[\strtolower($name)] = $name;
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\MessageInterface::withoutHeader()
	 */
	public function withoutHeader($name)
	{
		$newobj = clone $this;
		if(isset($newobj->_lkeys[\strtolower($name)]))
			unset($newobj->_headers[$newobj->_lkeys[\strtolower($name)]]);
		unset($newobj->_lkeys[\strtolower($name)]);
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\MessageInterface::getBody()
	 */
	public function getBody()
	{
		if(null === $this->_body)
			$this->_body = new StringStream();
		
		return $this->_body;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\MessageInterface::withBody()
	 */
	public function withBody(StreamInterface $body)
	{
		$newobj = clone $this;
		$newobj->_body = $body;
		
		return $newobj;
	}
	
}
