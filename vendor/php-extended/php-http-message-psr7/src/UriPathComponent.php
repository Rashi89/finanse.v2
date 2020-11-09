<?php declare(strict_types=1);

/*
 * This file is part of the php-extended/php-http-message-psr7 library
 *
 * (c) Anastaszor
 * This source file is subject to the MIT license that
 * is bundled with this source code in the file LICENSE.
 */

namespace PhpExtended\HttpMessage;

use Stringable;

/**
 * UriPathComponent class file.
 * 
 * This class manages the path part of an uri.
 * 
 * @author Anastaszor
 */
class UriPathComponent implements Stringable
{
	
	/**
	 * The parts of the path.
	 * 
	 * @var array<int, string>
	 */
	protected $_parts = [];
	
	/**
	 * Builds a new UriPathComponent with the given path.
	 * 
	 * @param string $path
	 */
	public function __construct(?string $path = null)
	{
		$this->absorb($path);
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Stringable::__toString()
	 */
	public function __toString() : string
	{
		return \implode('/', \array_map('rawurlencode', $this->_parts));
	}
	
	/**
	 * Absorb the given path fragment as relative path.
	 * 
	 * @param string $path
	 * @return UriPathComponent
	 */
	public function absorb(?string $path) : UriPathComponent
	{
		foreach(\explode('/', (string) $path) as $pathPart)
		{
			$part = \str_replace('+', ' ', \rawurldecode($pathPart));
			if(!empty($part))
			{
				if('.' === $pathPart)
					continue;
				
				if('..' === $pathPart)
				{
					unset($this->_parts[\count($this->_parts) - 1]);
					continue;
				}
				
				$this->_parts[\count($this->_parts)] = $part;
			}
		}
		
		return $this;
	}
	
}
