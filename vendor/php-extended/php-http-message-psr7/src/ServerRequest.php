<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-psr7 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

namespace PhpExtended\HttpMessage;

use Psr\Http\Message\ServerRequestInterface;
use Stringable;

/**
 * ServerRequest class file.
 *
 * This class is a simple implementation of the ServerRequestInterface.
 *
 * @author Anastaszor
 */
class ServerRequest extends Request implements ServerRequestInterface, Stringable
{
	
	/**
	 * The cookies which will be used to process the request.
	 *
	 * @var array<string, string>
	 */
	protected $_cookies = [];
	
	/**
	 * The query params which will be used to process the request.
	 *
	 * @var array<string, string>
	 */
	protected $_query = [];
	
	/**
	 * The query body which will be used to process the request.
	 *
	 * @var array<string, string>
	 */
	protected $_body = [];
	
	/**
	 * The uploaded files which will be used to process the request.
	 *
	 * @var array<string, \Psr\Http\Message\UploadedFileInterface>
	 */
	protected $_files = [];
	
	/**
	 * The additional attributes of this request.
	 *
	 * @var array<string, mixed>
	 */
	protected $_attributes = [];
	
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
	 * @see \Psr\Http\Message\ServerRequestInterface::getServerParams()
	 * @return array<string, string>
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function getServerParams() : array
	{
		global $argv, $argc;
		$params = [];
		$params = \array_merge($params, $_SERVER);
		$params = \array_merge($params, $_ENV);
		if(isset($argv) && !empty($argv))
			$params['argv'] = $argv;
		if(isset($argc) && !empty($argc))
			$params['argc'] = $argc;
		
		return $params;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::getCookieParams()
	 * @return array<string, string>
	 */
	public function getCookieParams() : array
	{
		return $this->_cookies;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::withCookieParams()
	 * @param array<string, string> $cookies
	 * @psalm-suppress MoreSpecificImplementedParamType
	 */
	public function withCookieParams(array $cookies)
	{
		$newobj = clone $this;
		$newobj->_cookies = \array_merge($this->_cookies, $cookies);
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::getQueryParams()
	 * @return array<string, string>
	 */
	public function getQueryParams() : array
	{
		return $this->_query;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::withQueryParams()
	 * @param array<string, string> $query
	 * @psalm-suppress MoreSpecificImplementedParamType
	 */
	public function withQueryParams(array $query)
	{
		$newobj = clone $this;
		$newobj->_query = $query;
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::getUploadedFiles()
	 * @return array<string, \Psr\Http\Message\UploadedFileInterface>
	 */
	public function getUploadedFiles() : array
	{
		return $this->_files;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::withUploadedFiles()
	 * @param array<string, \Psr\Http\Message\UploadedFileInterface> $uploadedFiles
	 * @psalm-suppress MoreSpecificImplementedParamType
	 */
	public function withUploadedFiles(array $uploadedFiles)
	{
		$newobj = clone $this;
		$newobj->_files = \array_merge_recursive($this->_files, $uploadedFiles);
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::getParsedBody()
	 * @return array<string, string>
	 */
	public function getParsedBody()
	{
		return $this->_body;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::withParsedBody()
	 * @param array<string, string> $data
	 * @psalm-suppress MoreSpecificImplementedParamType
	 */
	public function withParsedBody($data)
	{
		$newobj = clone $this;
		$newobj->_body = $data;
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::getAttributes()
	 * @return array<string, mixed>
	 */
	public function getAttributes() : array
	{
		return $this->_attributes;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::getAttribute()
	 */
	public function getAttribute($name, $default = null)
	{
		if(isset($this->_attributes[$name]))
			return $this->_attributes[$name];
		
		return $default;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::withAttribute()
	 */
	public function withAttribute($name, $value)
	{
		$newobj = clone $this;
		$newobj->_attributes[$name] = $value;
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ServerRequestInterface::withoutAttribute()
	 */
	public function withoutAttribute($name)
	{
		if(!isset($this->_attributes[$name]))
			return $this;
		
		$newobj = clone $this;
		unset($newobj->_attributes[$name]);
		
		return $newobj;
	}
	
}
