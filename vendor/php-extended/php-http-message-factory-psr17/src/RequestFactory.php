<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-factory-psr17 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

namespace PhpExtended\HttpMessage;

use InvalidArgumentException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Stringable;

/**
 * RequestFactory class file.
 * 
 * This class creates requests based on the request and uri implementations
 * of the php-extended/php-http-message-psr7 library.
 * 
 * @author Anastaszor
 */
class RequestFactory implements RequestFactoryInterface, Stringable
{
	
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
	 * @see \Psr\Http\Message\RequestFactoryInterface::createRequest()
	 * @throws InvalidArgumentException
	 */
	public function createRequest(string $method, $uri) : RequestInterface
	{
		$request = new Request();
		$request = $request->withMethod($method);
		
		if(\is_string($uri))
		{
			$urifactory = new UriFactory();
			$uri = $urifactory->createUri($uri);
		}
		
		return $request->withUri($uri);
	}
	
}
