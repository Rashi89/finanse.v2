<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-psr7 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

namespace PhpExtended\HttpMessage;

use Exception;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Stringable;

/**
 * FileStream class file.
 *
 * This class represents a read and write stream implementation of
 * the StreamInterface which relies on underlying files.
 *
 * @author Anastaszor
 */
class FileStream implements StreamInterface, Stringable
{
	
	/**
	 * The target path of the file.
	 *
	 * @var string
	 */
	protected $_path;
	
	/**
	 * The handle of the file at target path.
	 *
	 * @var ?resource
	 */
	protected $_handle = null;
	
	/**
	 * The total length of the file.
	 * 
	 * @var integer
	 */
	protected $_fileLength = 0;
	
	/**
	 * Whether the stream is in detached state. In detached state, the
	 * stream is unusable.
	 *
	 * @var boolean
	 */
	protected $_detached = false;
	
	/**
	 * Builds a new FileStream object with the given target path.
	 *
	 * @param mixed $pathOrResource
	 * @param ?integer $fileLength the length of the stream
	 * @throws RuntimeException if the file does not exists at the given path
	 */
	public function __construct($pathOrResource, ?int $fileLength = null)
	{
		if(\is_string($pathOrResource))
		{
			$realpath = \realpath($pathOrResource);
			if(false === $realpath)
				throw new RuntimeException(\strtr('The file path does not point to an existing file.', ['{path}' => $pathOrResource]));
			if(!\is_file($realpath))
				throw new RuntimeException(\strtr('The file at path "{path}" is not a file.', ['{path}' => $realpath]));
			$this->_path = $realpath;
		}
		
		if(\is_resource($pathOrResource))
		{
			$this->_handle = $pathOrResource;
			/** @psalm-suppress PossiblyInvalidCast */
			$this->_path = (string) $this->getMetadata('uri');
		}
		
		if(empty($this->_path))
			throw new RuntimeException('Invalid argument for file stream, it must be a string or a resource.');
		
		if(null === $fileLength)
			$this->_fileLength = (int) \filesize($this->_path);
		else
			$this->_fileLength = $fileLength;
		
		$this->_detached = false;
		$this->rewind();
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::__toString()
	 */
	public function __toString() : string
	{
		if($this->_detached)
			return '';
		
		if(null === $this->_handle)
		{
			$res = \file_get_contents($this->_path);
			if(false === $res) return '';
			
			return $res;
		}
		
		try
		{
			$this->rewind();
			
			return $this->read($this->getSize());
		}
		catch(Exception $e)
		{
			return '';
		}
	}
	
	/**
	 * Ensures that the handle exists for this stream.
	 *
	 * @throws RuntimeException if the handle cannot be opened or locked
	 */
	protected function ensureStream() : void
	{
		if($this->_detached)
			throw new RuntimeException('The stream is in detached state.');
		
		if(null === $this->_handle)
		{
			if(!\is_file($this->_path))
				throw new RuntimeException(\strtr('The file at path "{path}" is not a file.', ['{path}' => $this->_path]));
			$handle = \fopen($this->_path, 'c+');
			if(false === $handle)
				throw new RuntimeException(\strtr('Impossible to open file {path}.', ['{path}' => $this->_path]));
			$this->_handle = $handle;
			if(!\flock($this->_handle, \LOCK_EX))
				throw new RuntimeException(\strtr('Impossible to lock file {path}.', ['{path}' => $this->_path]));
		}
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::close()
	 */
	public function close() : void
	{
		if(null !== $this->_handle)
		{
			/** @psalm-suppress UnusedFunctionCall */
			\flock($this->_handle, \LOCK_UN);
			/** @psalm-suppress InvalidPropertyAssignmentValue */
			\fclose($this->_handle);
			$this->_handle = null;
		}
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::detach()
	 * @return resource|null
	 */
	public function detach()
	{
		$this->_detached = true;
		$this->close();
		
		return null;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::getSize()
	 */
	public function getSize() : int
	{
		return $this->_fileLength;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::tell()
	 */
	public function tell() : int
	{
		$this->ensureStream();
		$res = false;
		if(null !== $this->_handle)
			$res = \ftell($this->_handle);
		if(false !== $res)
			return $res;
		
		throw new RuntimeException('Impossible to tell the stream position.');
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::eof()
	 */
	public function eof() : bool
	{
		try
		{
			return $this->tell() === $this->getSize();
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
	 * @param integer $offset
	 * @param integer $whence
	 * @throws RuntimeException
	 */
	public function seek($offset, $whence = \SEEK_SET) : void
	{
		$this->ensureStream();
		$res = -1;
		if(null !== $this->_handle)
			$res = \fseek($this->_handle, $offset, $whence);
		if(-1 === $res)
			throw new RuntimeException('Impossible to seek the file.');
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::rewind()
	 */
	public function rewind() : void
	{
		$this->seek(0, \SEEK_SET);
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::isWritable()
	 */
	public function isWritable() : bool
	{
		return \is_writable($this->_path);
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::write()
	 */
	public function write($string) : int
	{
		$this->ensureStream();
		$res = false;
		if(null !== $this->_handle)
			$res = \fwrite($this->_handle, $string);
		if(false !== $res)
			return $res;
		// calculate the new length given the emplacement we're in the stream
		$this->_fileLength = \max($this->_fileLength, $this->tell() + \strlen($string));
		
		throw new RuntimeException('Impossible to write to file.');
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::isReadable()
	 */
	public function isReadable() : bool
	{
		return \is_readable($this->_path);
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::read()
	 */
	public function read($length) : string
	{
		$this->ensureStream();
		if(0 >= $length)
			return '';
		$res = false;
		if(null !== $this->_handle)
			$res = \fread($this->_handle, $length);
		if(false === $res)
			throw new RuntimeException(\strtr('Impossible to read the next {n} bytes.', ['{n}' => $length]));
		
		return $res;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::getContents()
	 */
	public function getContents() : string
	{
		$this->ensureStream();
		
		return $this->read($this->getSize());
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\StreamInterface::getMetadata()
	 * @param ?string $key
	 */
	public function getMetadata($key = null)
	{
		try
		{
			$this->ensureStream();
		}
		catch(RuntimeException $e)
		{
			return null;
		}
		
		$smd = null;
		if(null !== $this->_handle)
			$smd = \stream_get_meta_data($this->_handle);
		if(null === $key)
			return $smd;
		if(isset($smd[$key]))
			return $smd[$key];
		
		return null;
	}
	
	/**
	 * Closes the stream at destruction of this object.
	 */
	public function __destruct()
	{
		$this->detach();
	}
	
}
