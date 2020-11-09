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
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Stringable;

/**
 * ResponseFactory class file.
 * 
 * This class creates responses based on the response implementation of the
 * php-extended/php-http-message-psr7 library.
 * 
 * @author Anastaszor
 */
class ResponseFactory implements ResponseFactoryInterface, Stringable
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
	 * @see \Psr\Http\Message\ResponseFactoryInterface::createResponse()
	 * @throws InvalidArgumentException
	 */
	public function createResponse(int $code = 200, string $reasonPhrase = '') : ResponseInterface
	{
		$response = new Response();
		
		return $response->withStatus($code, $reasonPhrase);
	}
	
}
