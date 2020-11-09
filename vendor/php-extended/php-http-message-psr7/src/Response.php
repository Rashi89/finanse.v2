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
use Psr\Http\Message\ResponseInterface;

/**
 * Response class file.
 * 
 * This class is a simple implementation of the ResponseInterface.
 * 
 * @author Anastaszor
 */
class Response extends Message implements ResponseInterface
{
	/**
	 * The default reason responses associated with the http codes.
	 * 
	 * @var array<int, string>
	 */
	protected static $_httpCodes = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Payload Too Large',
		414 => 'URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Range Not Satisfiable',
		417 => 'Expectation Failed',
		421 => 'Misdirected Request',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		451 => 'Unavailable For Legal Reasons',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		510 => 'Not Extended',
		511 => 'Network Authentication Required',
	];
	
	/**
	 * The http status code of the response.
	 * 
	 * @var integer
	 */
	protected $_code = 200;
	
	/**
	 * The reason phrase associated to the http status code.
	 * 
	 * @var string
	 */
	protected $_reason = 'OK';
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ResponseInterface::withStatus()
	 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
	 * @SuppressWarnings(PHPMD.UndefinedVariable)
	 */
	public function withStatus($code, $reasonPhrase = '')
	{
		$icode = (int) $code;
		if(!isset(self::$_httpCodes[$icode]))
			throw new InvalidArgumentException(\strtr('Invalid status code "{code}"', ['{code}' => $code]));
		
		$newobj = clone $this;
		$newobj->_code = $icode;
		
		if(!empty($reasonPhrase))
			$newobj->_reason = $reasonPhrase;
		else
			$newobj->_reason = self::$_httpCodes[$icode];
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ResponseInterface::getStatusCode()
	 */
	public function getStatusCode() : int
	{
		return $this->_code;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\ResponseInterface::getReasonPhrase()
	 */
	public function getReasonPhrase() : string
	{
		if(empty($this->_reason)) return '';
		
		return $this->_reason;
	}
	
}
