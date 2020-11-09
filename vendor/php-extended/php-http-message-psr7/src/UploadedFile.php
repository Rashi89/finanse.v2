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
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Stringable;

/**
 * UploadedFile class file.
 * 
 * This class is a simple implementation of the UploadedFileInterface.
 * 
 * @author Anastaszor
 */
class UploadedFile implements Stringable, UploadedFileInterface
{
	
	/**
	 * The name of the file which was given by the user for this file.
	 * 
	 * @var string
	 */
	protected $_name;
	
	/**
	 * The actual path of this file.
	 * 
	 * @var string
	 */
	protected $_tempName;
	
	/**
	 * The mime type which was given by the user for this file.
	 * 
	 * @var string
	 */
	protected $_type;
	
	/**
	 * The size of the file, given by php.
	 * 
	 * @var integer
	 */
	protected $_size;
	
	/**
	 * The php error for this file.
	 * 
	 * @var integer
	 */
	protected $_error;
	
	/**
	 * The stream for this file.
	 * 
	 * @var ?StreamInterface
	 */
	protected $_stream = null;
	
	/**
	 * Constructor of the UploadedFile. This class is instanciated by
	 * the ServerRequest::collectFileRecursive() method.
	 * 
	 * @param string $name the original name of the file being uploaded
	 * @param string $tempName the path of the uploaded file on the server
	 * @param string $type the MIME-type of the uploaded file (such as "image/gif")
	 * @param integer $size the actual size of the uploaded file in bytes
	 * @param integer $error the error code
	 * @param StreamInterface $stream
	 */
	public function __construct(string $name, string $tempName, string $type, int $size, int $error, ?StreamInterface $stream = null)
	{
		$this->_name = $name;
		$this->_tempName = $tempName;
		$this->_type = $type;
		$this->_size = $size;
		$this->_error = $error;
		$this->_stream = $stream;
	}
	
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
	 * @see \Psr\Http\Message\UploadedFileInterface::getStream()
	 */
	public function getStream() : StreamInterface
	{
		if(null === $this->_stream)
			$this->_stream = new FileStream($this->_tempName);
		
		return $this->_stream;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UploadedFileInterface::moveTo()
	 */
	public function moveTo($targetPath) : void
	{
		$dir = \dirname($targetPath);
		$realdir = \realpath($dir);
		if(false === $realdir)
			throw new InvalidArgumentException(\strtr('The given path "{path}" does not point to an existing directory.', ['{path}' => $targetPath]));
		
		if(!\is_writable($realdir))
			throw new InvalidArgumentException(\strtr('The given path "{path}" that points to "{real}" is not writeable.', ['{path}' => $targetPath, '{real}' => $realdir]));
		
		if(\UPLOAD_ERR_OK !== $this->_error)
			throw new RuntimeException('Impossible to move file: the uploaded file has an error.');
		
		$realpath = \str_replace($dir, $realdir, $targetPath);
		
		if(\is_uploaded_file($this->_tempName))
		{
			if(!\move_uploaded_file($this->_tempName, $realpath))
				throw new RuntimeException(\strtr('Impossible to move uploaded file from {src} to {dst}.', ['{src}' => $this->_tempName, '{dst}' => $realpath]));
		}
		
		if(!\is_uploaded_file($this->_tempName))
		{
			if(!\rename($this->_tempName, $realpath))
				throw new RuntimeException(\strtr('Impossible to move file from {src} to {dst}.', ['{src}' => $this->_tempName, '{dst}' => $realpath]));
		}
		
		$this->_tempName = $realpath;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UploadedFileInterface::getSize()
	 */
	public function getSize() : int
	{
		return $this->_size;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UploadedFileInterface::getError()
	 */
	public function getError() : int
	{
		return $this->_error;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UploadedFileInterface::getClientFilename()
	 */
	public function getClientFilename() : string
	{
		return $this->_name;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UploadedFileInterface::getClientMediaType()
	 */
	public function getClientMediaType() : string
	{
		return $this->_type;
	}
	
}
