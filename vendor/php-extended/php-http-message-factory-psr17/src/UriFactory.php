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
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Stringable;

/**
 * UriFactory class file.
 * 
 * This class creates uris based on the uri implementations of the
 * php-extended/php-http-message-psr7 library.
 * 
 * @author Anastaszor
 */
class UriFactory implements Stringable, UriFactoryInterface
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
	 * @see \Psr\Http\Message\UriFactoryInterface::createUri()
	 * @throws InvalidArgumentException
	 */
	public function createUri(string $uristr = '') : UriInterface
	{
		$vars = \parse_url((string) $uristr);
		if(false === $vars)
		{
			$message = 'Impossible to parse uri string value "{val}" : {msg}';
			$context = ['{val}' => $uristr, '{msg}' => \error_get_last()];
			
			throw new InvalidArgumentException(\strtr($message, $context));
		}
		
		$uri = new Uri();
		$uri = $uri->withScheme($vars['scheme'] ?? 'https');
		if(isset($vars['host']))
			$uri = $uri->withHost($vars['host']);
		if(isset($vars['port']))
			$uri = $uri->withPort($vars['port']);
		if(isset($vars['user']))
			$uri = $uri->withUserInfo($vars['user'], $vars['pass'] ?? null);
		if(isset($vars['path']))
			$uri = $uri->withPath($vars['path']);
		if(isset($vars['query']))
			$uri = $uri->withQuery($vars['query']);
		if(isset($vars['fragment']))
			$uri = $uri->withFragment($vars['fragment']);
		
		return $uri;
	}
	
}
