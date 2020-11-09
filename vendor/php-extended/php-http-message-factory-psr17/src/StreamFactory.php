<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-factory-psr17 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

namespace PhpExtended\HttpMessage;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Stringable;

/**
 * StreamFactory class file.
 * 
 * This class creates streams based on the stream implementations of the
 * php-extended/php-http-message-psr7 library.
 * 
 * @author Anastaszor
 */
class StreamFactory implements StreamFactoryInterface, Stringable
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
	 * @see \Psr\Http\Message\StreamFactoryInterface::createStream()
	 * @throws RuntimeException
	 */
	public function createStream(string $content = '') : StreamInterface
	{
		$resource = \fopen('php://temp', 'r+');
		if(false !== $resource)
			\fwrite($resource, $content);
		
		return new FileStream($resource, \strlen($content));
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamFactoryInterface::createStreamFromFile()
	 * @throws RuntimeException
	 */
	public function createStreamFromFile(string $filename, string $mode = 'r') : StreamInterface
	{
		if(!\file_exists($filename))
		{
			$message = 'The given filename {file} does not exist.';
			$context = ['{file}' => $filename];
			
			throw new RuntimeException(\strtr($message, $context));
		}
		
		if(!\is_file($filename))
		{
			$message = 'The given object at path {file} is not a file.';
			$context = ['{file}' => $filename];
			
			throw new RuntimeException(\strtr($message, $context));
		}
		
		if(!\is_readable($filename))
		{
			$message = 'The given file at path {file} is not readable.';
			$context = ['{file}' => $filename];
			
			throw new RuntimeException(\strtr($message, $context));
		}
		
		$resource = \fopen($filename, $mode);
		if(false === $resource)
		{
			$message = 'Failed to open file at path {file}.';
			$context = ['{file}' => $filename];
			
			throw new RuntimeException(\strtr($message, $context));
		}
		
		return $this->createStreamFromResource($resource);
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamFactoryInterface::createStreamFromResource()
	 * @throws RuntimeException
	 */
	public function createStreamFromResource($resource) : StreamInterface
	{
		return new FileStream($resource);
	}
	
}
