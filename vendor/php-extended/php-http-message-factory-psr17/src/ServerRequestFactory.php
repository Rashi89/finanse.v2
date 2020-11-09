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
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Stringable;

/**
 * ServerRequestFactory class file.
 * 
 * This class creates server requests based on the request and uri
 * implementations of the php-extended/php-http-message-psr7 library.
 * 
 * @author Anastaszor
 */
class ServerRequestFactory implements ServerRequestFactoryInterface, Stringable
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
	 * @see \Psr\Http\Message\ServerRequestFactoryInterface::createServerRequest()
	 * @param array<array-key, mixed> $serverParams
	 * @throws InvalidArgumentException
	 */
	public function createServerRequest(string $method, $uri, array $serverParams = []) : ServerRequestInterface
	{
		$srq = new ServerRequest();
		if(\is_string($uri))
		{
			$urifactory = new UriFactory();
			$uri = $urifactory->createUri($uri);
		}
		if(!empty($uri))
			$srq = $srq->withAttribute('uri', $uri);
		
		$srq = $srq->withCookieParams($this->getSuperglobalParams('_COOKIE', $serverParams));
		$srq = $srq->withQueryParams($this->getSuperglobalParams('_GET', $serverParams));
		$srq = $srq->withParsedBody($this->getSuperglobalParams('_POST', $serverParams));
		
		$files = [];
		
		foreach($this->getSuperglobalParams('_FILES', $serverParams) as $key => $value)
		{
			$files += $this->collectFiles($key, $value);
		}
		$srq = $srq->withUploadedFiles($files);
		
		foreach($serverParams as $name => $value)
		{
			$srq = $srq->withAttribute($name, $value);
		}
		
		return $srq;
	}
	
	/**
	 * @param string $key
	 * @param mixed[] $files
	 * @return array<string, UploadedFile>
	 */
	public function collectFiles(string $key, array $files) : array
	{
		if(!isset($files['name']) || !isset($files['error']) || !isset($files['type']) || !isset($files['tmp_name']) || !isset($files['size']))
			return [];
		
		$names = $files['name'];
		$error = $files['error'];
		$types = $files['type'];
		$tmpns = $files['tmp_name'];
		$sizes = $files['size'];
		if(\is_string($names))
			return [$key => new UploadedFile($names, $tmpns, $types, $sizes, $error)];
		
		$objects = [];
		
		foreach($names as $newkey => $name)
		{
			foreach($this->collectFiles($key.'['.$newkey.']', [
				'name' => $name,
				'error' => $files['error'][$key],
				'type' => $files['type'][$key],
				'tmp_name' => $files['tmp_name'][$key],
				'size' => $files['size'][$key],
			]) as $newkey2 => $value)
			{
				$objects[$newkey2] = $value;
			}
		}
		
		return $objects;
	}
	
	/**
	 * Gets the data from the given superglobal.
	 * 
	 * @param string $superName
	 * @param array<string, mixed> $serverParams
	 * @return array<string, mixed>
	 */
	public function getSuperglobalParams(string $superName, array &$serverParams) : array
	{
		$data = ${$superName};
		if(isset($serverParams[$superName]) && \is_array($serverParams[$superName]))
		{
			$data = \array_merge($data, $serverParams[$superName]);
			unset($serverParams[$superName]);
		}
		
		return $data;
	}
	
}
