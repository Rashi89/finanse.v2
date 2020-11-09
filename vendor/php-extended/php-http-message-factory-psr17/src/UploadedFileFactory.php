<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-factory-psr17 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

namespace PhpExtended\HttpMessage;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Stringable;

/**
 * UploadedFileFactory class file.
 * 
 * This class creates uploaded files based on the uploaded file implementation
 * of the php-extended/php-http-message-psr7 library.
 * 
 * @author Anastaszor
 */
class UploadedFileFactory implements Stringable, UploadedFileFactoryInterface
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
	 * @see \Psr\Http\Message\UploadedFileFactoryInterface::createUploadedFile()
	 */
	public function createUploadedFile(
		StreamInterface $stream,
		?int $size = null,
		int $error = \UPLOAD_ERR_OK,
		?string $clientFilename = null,
		?string $clientMediaType = null
	) : UploadedFileInterface
	{
		return new UploadedFile((string) $clientFilename, (string) $clientFilename, (string) $clientMediaType, (int) $size, (int) $error, $stream);
	}
	
}
