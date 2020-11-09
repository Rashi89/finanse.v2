<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-psr7 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

namespace PhpExtended\HttpMessage;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Request class file.
 *
 * This class is a simple implementation of the RequestInterface.
 *
 * @author Anastaszor
 */
class Request extends Message implements RequestInterface
{
	
	public const ORIGIN_FORM = 'origin-form';
	public const ABSOLUTE_FORM = 'absolute-form';
	public const AUTHORITY_FORM = 'authority-form';
	public const ASTERISK_FORM = 'asterisk-form';
	
	/**
	 * The allowed verbs for this request.
	 *
	 * @var string[]
	 */
	protected static $_allowedHttpVerbs = [
		'GET',
		'HEAD',
		'POST',
		'PUT',
		'DELETE',
		'TRACE',
		'OPTIONS',
		'CONNECT',
		'PATCH',
	];
	
	/**
	 * The available request targets forms, verbatim  (one of self::ORIGIN_FORM,
	 * ::ABSOLUTE_FORM, ::AUTHORITY_FORM, and ::ASTERISK_FORM).
	 *
	 * @var string
	 */
	protected $_requestTarget = self::ORIGIN_FORM;
	
	/**
	 * The method of the request.
	 *
	 * @var string
	 */
	protected $_method = 'GET';
	
	/**
	 * The target uri of the request.
	 *
	 * @var ?UriInterface
	 */
	protected $_uri;
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\RequestInterface::getRequestTarget()
	 */
	public function getRequestTarget() : string
	{
		return $this->_requestTarget;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\RequestInterface::withRequestTarget()
	 */
	public function withRequestTarget($requestTarget)
	{
		switch($requestTarget)
		{
			case self::ORIGIN_FORM:
			case self::ABSOLUTE_FORM:
			case self::AUTHORITY_FORM:
			case self::ASTERISK_FORM:
				$newobj = clone $this;
				$newobj->_requestTarget = $requestTarget;
				
				return $newobj;
		}
		
		return $this;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\RequestInterface::getMethod()
	 */
	public function getMethod() : string
	{
		return $this->_method;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\RequestInterface::withMethod()
	 */
	public function withMethod($method)
	{
		if(\in_array(\strtoupper($method), self::$_allowedHttpVerbs))
		{
			$newobj = clone $this;
			$newobj->_method = $method;
			
			return $newobj;
		}
		
		throw new InvalidArgumentException(\strtr('The given method "{name}" is not allowed, allowed methods are {list}.', [$method, \implode(', ', self::$_allowedHttpVerbs)]));
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\RequestInterface::getUri()
	 */
	public function getUri() : UriInterface
	{
		if(null === $this->_uri)
			$this->_uri = new Uri();
		
		return $this->_uri;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\RequestInterface::withUri()
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function withUri(UriInterface $uri, $preserveHost = false)
	{
		if($uri === $this->_uri)
			return $this;
		
		$newobj = clone $this;
		if($preserveHost && null !== $this->_uri)
		{
			try
			{
				$uri = $uri->withHost($this->_uri->getHost());
			}
			catch(InvalidArgumentException $e)
			{
				// nothing to do
			}
		}
		$newobj->_uri = $uri;
		
		return $newobj;
	}
	
}
