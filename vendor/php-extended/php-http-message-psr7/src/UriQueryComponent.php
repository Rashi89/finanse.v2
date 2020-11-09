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
 * UriQueryComponent class file.
 * 
 * This class represents 
 * 
 * @author Anastaszor
 */
class UriQueryComponent implements Stringable
{
	
	/**
	 * Gets the query parts.
	 * 
	 * @var array<string, array<int, string>>
	 */
	protected $_parts = [];
	
	/**
	 * Builds a new UriQueryComponent with the given query.
	 *
	 * @param string $query
	 */
	public function __construct(?string $query = null)
	{
		$this->absorb($query);
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Stringable::__toString()
	 */
	public function __toString() : string
	{
		$parts = [];
		
		foreach($this->_parts as $key => $data)
		{
			foreach($data as $datum)
			{
				$parts[] = \rawurlencode($key).'='.\rawurlencode($datum);
			}
		}
		
		return \implode('&', $parts);
	}
	
	/**
	 * @param string $query
	 * @return UriQueryComponent
	 */
	public function absorb(?string $query) : UriQueryComponent
	{
		foreach(\explode('&', (string) $query) as $queryPart)
		{
			$parts = \explode('=', $queryPart, 2);
			
			$key = \str_replace('+', ' ', \rawurldecode((string) $parts[0]));
			
			if(empty($key))
				continue;
			
			$value = \str_replace('+', ' ', \rawurldecode((string) ($parts[1] ?? '')));
			
			if(isset($this->_parts[$key]))
			{
				$this->_parts[$key][] = $value;
				continue;
			}
			
			$this->_parts[$key] = [$value];
		}
		
		return $this;
	}
	
	/**
	 * Gets whether this query component is empty.
	 * 
	 * @return boolean
	 */
	public function isEmpty() : bool
	{
		return 0 === \count($this->_parts);
	}
	
}
