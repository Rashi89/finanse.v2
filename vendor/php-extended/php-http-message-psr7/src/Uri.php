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
use Psr\Http\Message\UriInterface;
use Stringable;

/**
 * Uri class file.
 * 
 * This class is a simple implementation of the UriInterface.
 * 
 * @author Anastaszor
 */
class Uri implements Stringable, UriInterface
{
	
	/**
	 * The list of all allowed / known schemes.
	 * 
	 * @var array<int, string>
	 * @see https://www.iana.org/assignments/uri-schemes/uri-schemes.xhtml
	 */
	protected static $_allowedSchemes = [
		'aaa', // Permanent 	[RFC6733]
		'aaas', // Permanent 	[RFC6733]
		'about', // Permanent 	[RFC6694]
		'acap', // Permanent 	[RFC2244]
		'acct', // Permanent 	[RFC7565]
		'acr', // Provisional 	[OMA-OMNA]
		'adiumxtra', // Provisional 	[Dave_Thaler]
		'afp', // Provisional 	[Dave_Thaler]
		'afs', // Provisional 	[RFC1738]
		'aim', // Provisional 	[Dave_Thaler]
		'appdata', // Provisional 	[urischemeowners_at_microsoft.com]
		'apt', // Provisional 	[Dave_Thaler]
		'attachment', // Provisional 	[Dave_Thaler]
		'aw', // Provisional 	[Dave_Thaler]
		'barion', // Provisional 	[Bíró_Tamás]
		'beshare', // Provisional 	[Dave_Thaler]
		'bitcoin', // Provisional 	[Dave_Thaler]
		'blob', // Provisional 	[W3C_WebApps_Working_Group][Chris_Rebert]
		'bolo', // Provisional 	[Dave_Thaler]
		'browserext', // browserext 	Provisional 	[Mike_Pietraszak]
		'callto', // Provisional 	[Alexey_Melnikov]
		'cap', // Permanent 	[RFC4324]
		'chrome', // Provisional 	[Dave_Thaler]
		'chrome-extension', // Provisional 	[Dave_Thaler]
		'cid', // Permanent 	[RFC2392]
		'coap', // Permanent 	[RFC7252]
		'coaps', // Permanent 	[RFC7252]
		'com-eventbrite-attendee', // Provisional 	[Bob_Van_Zant]
		'content', // Provisional 	[Dave_Thaler]
		'crid', // Permanent 	[RFC4078]
		'cvs', // Provisional 	[Dave_Thaler]
		'data', // Permanent 	[RFC2397]
		'dav', // Permanent 	[RFC4918]
		'dict', // Permanent 	[RFC2229]
		'dis', // Provisional 	[Christophe_Meessen]
		'dlna-playcontainer', // Provisional 	[DLNA]
		'dlna-playsingle', // Provisional 	[DLNA]
		'dns', // Permanent 	[RFC4501]
		'dntp', // Provisional 	[Hans-Dieter_A._Hiep]
		'dtn', // Provisional 	[RFC5050]
		'dvb', // Provisional 	[draft-mcroberts-uri-dvb]
		'ed2k', // Provisional 	[Dave_Thaler]
		'example', // Permanent 	[RFC7595]
		'facetime', // Provisional 	[Dave_Thaler]
		'fax', // Historical 	[RFC2806][RFC3966]
		'feed', // Provisional 	[Dave_Thaler]
		'feedready', // Provisional 	[Mirko_Nosenzo]
		'file', // Permanent 	[RFC-ietf-appsawg-file-scheme-16]
		'filesystem', // Historical 	[W3C_WebApps_Working_Group][Chris_Rebert]
		'finger', // Provisional 	[Dave_Thaler]
		'fish', // Provisional 	[Dave_Thaler]
		'ftp', // Permanent 	[RFC1738]
		'geo', // Permanent 	[RFC5870]
		'gg', // Provisional 	[Dave_Thaler]
		'git', // Provisional 	[Dave_Thaler]
		'gizmoproject', // Provisional 	[Dave_Thaler]
		'go', // Permanent 	[RFC3368]
		'gopher', // Permanent 	[RFC4266]
		'gtalk', // Provisional 	[Dave_Thaler]
		'h323', // Permanent 	[RFC3508]
		'ham', // Provisional 	[RFC7046]
		'hcp', // Provisional 	[Alexey_Melnikov]
		'http', // Permanent 	[RFC7230, Section 2.7.1]
		'https', // Permanent 	[RFC7230, Section 2.7.2]
		'iax', // Permanent 	[RFC5456]
		'icap', // Permanent 	[RFC3507]
		'icon', // Provisional 	[draft-lafayette-icon-uri-scheme]
		'im', // Permanent 	[RFC3860]
		'imap', // Permanent 	[RFC5092]
		'info', // Permanent 	[RFC4452]
		'iotdisco', // Provisional 	[Peter_Waher][http://www.iana.org/assignments/uri-schemes/prov/iotdisco.pdf]
		'ipn', // Provisional 	[RFC6260]
		'ipp', // Permanent 	[RFC3510]
		'ipps', // Permanent 	[RFC7472]
		'irc', // Provisional 	[Dave_Thaler]
		'irc6', // Provisional 	[Dave_Thaler]
		'ircs', // Provisional 	[Dave_Thaler]
		'iris', // Permanent 	[RFC3981]
		'iris.beep', // Permanent 	[RFC3983]
		'iris.lwz', // Permanent 	[RFC4993]
		'iris.xpc', // Permanent 	[RFC4992]
		'iris.xpcs', // Permanent 	[RFC4992]
		'isostore', // Provisional 	[urischemeowners_at_microsoft.com]
		'itms', // Provisional 	[Dave_Thaler]
		'jabber', // Permanent 	[Peter_Saint-Andre]
		'jar', // Provisional 	[Dave_Thaler]
		'jms', // Provisional 	[RFC6167]
		'keyparc', // Provisional 	[Dave_Thaler]
		'lastfm', // Provisional 	[Dave_Thaler]
		'ldap', // Permanent 	[RFC4516]
		'ldaps', // Provisional 	[Dave_Thaler]
		'lvlt', // Provisional 	[Alexander_Shishenko]
		'magnet', // Provisional 	[Dave_Thaler]
		'mailserver', // Historical 	[RFC6196]
		'mailto', // Permanent 	[RFC6068]
		'maps', // Provisional 	[Dave_Thaler]
		'market', // Provisional 	[Dave_Thaler]
		'message', // Provisional 	[Dave_Thaler]
		'mid', // Permanent 	[RFC2392]
		'mms', // Provisional 	[Alexey_Melnikov]
		'modem', // Historical 	[RFC2806][RFC3966]
		'moz', // Provisional 	[Joe_Hildebrand]
		'ms-access', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-browser-extension', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-drive-to', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-enrollment', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-excel', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-gamebarservices', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-getoffice', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-help', // Provisional 	[Alexey_Melnikov]
		'ms-infopath', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-media-stream-id', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-project', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-powerpoint', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-publisher', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-search-repair', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-secondary-screen-controller', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-secondary-screen-setup', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-airplanemode', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-bluetooth', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-camera', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-cellular', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-cloudstorage', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-connectabledevices', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-displays-topology', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-emailandaccounts', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-language', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-location', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-lock', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-nfctransactions', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-notifications', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-power', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-privacy', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-proximity', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-screenrotation', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-wifi', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-settings-workplace', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-spd', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-sttoverlay', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-transit-to', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-virtualtouchpad', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-visio', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-walk-to', // Provisional 	[urischemeowners_at_microsoft.com]
		'ms-word', // Provisional 	[urischemeowners_at_microsoft.com]
		'msnim', // Provisional 	[Alexey_Melnikov]
		'msrp', // Permanent 	[RFC4975]
		'msrps', // Permanent 	[RFC4975]
		'mtqp', // Permanent 	[RFC3887]
		'mumble', // Provisional 	[Dave_Thaler]
		'mupdate', // Permanent 	[RFC3656]
		'mvn', // Provisional 	[Dave_Thaler]
		'news', // Permanent 	[RFC5538]
		'nfs', // Permanent 	[RFC2224]
		'ni', // Permanent 	[RFC6920]
		'nih', // Permanent 	[RFC6920]
		'nntp', // Permanent 	[RFC5538]
		'notes', // Provisional 	[Dave_Thaler]
		'ocf', // Provisional 	[Dave_Thaler]
		'oid', // Provisional 	[draft-larmouth-oid-iri]
		'opaquelocktoken', // Permanent 	[RFC4918]
		'pack', // Historical 	[draft-shur-pack-uri-scheme]
		'palm', // Provisional 	[Dave_Thaler]
		'paparazzi', // Provisional 	[Dave_Thaler]
		'pkcs11', // Permanent 	[RFC7512]
		'platform', // Provisional 	[Dave_Thaler]
		'pop', // Permanent 	[RFC2384]
		'pres', // Permanent 	[RFC3859]
		'prospero', // Historical 	[RFC4157]
		'proxy', // Provisional 	[Dave_Thaler]
		'pwid', // Provisional 	[Eld_Zierau]
		'psyc', // Provisional 	[Dave_Thaler]
		'qb', // Provisional 	[Jan_Pokorny]
		'query', // Provisional 	[Dave_Thaler]
		'redis', // Provisional 	[Chris_Rebert]
		'rediss', // Provisional 	[Chris_Rebert]
		'reload', // Permanent 	[RFC6940]
		'res', // Provisional 	[Alexey_Melnikov]
		'resource', // Provisional 	[Dave_Thaler]
		'rmi', // Provisional 	[Dave_Thaler]
		'rsync', // Provisional 	[RFC5781]
		'rtmfp', // Provisional 	[RFC7425]
		'rtmp', // Provisional 	[Dave_Thaler]
		'rtsp', // Permanent 	[RFC2326][RFC7826]
		'rtsps', // Permanent 	[RFC2326][RFC7826]
		'rtspu', // Permanent 	[RFC2326]
		'secondlife', // Provisional 	[Dave_Thaler]
		'service', // Permanent 	[RFC2609]
		'session', // Permanent 	[RFC6787]
		'sftp', // Provisional 	[Dave_Thaler]
		'sgn', // Provisional 	[Dave_Thaler]
		'shttp', // Permanent 	[RFC2660]
		'sieve', // Permanent 	[RFC5804]
		'sip', // Permanent 	[RFC3261]
		'sips', // Permanent 	[RFC3261]
		'skype', // Provisional 	[Alexey_Melnikov]
		'smb', // Provisional 	[Dave_Thaler]
		'sms', // Permanent 	[RFC5724]
		'smtp', // Provisional 	[draft-melnikov-smime-msa-to-mda]
		'snews', // Historical 	[RFC5538]
		'snmp', // Permanent 	[RFC4088]
		'soap.beep', // Permanent 	[RFC4227]
		'soap.beeps', // Permanent 	[RFC4227]
		'soldat', // Provisional 	[Dave_Thaler]
		'spotify', // Provisional 	[Dave_Thaler]
		'ssh', // Provisional 	[Dave_Thaler]
		'steam', // Provisional 	[Dave_Thaler]
		'stun', // Permanent 	[RFC7064]
		'stuns', // Permanent 	[RFC7064]
		'submit', // Provisional 	[draft-melnikov-smime-msa-to-mda]
		'svn', // Provisional 	[Dave_Thaler]
		'tag', // Permanent 	[RFC4151]
		'teamspeak', // Provisional 	[Dave_Thaler]
		'tel', // Permanent 	[RFC3966]
		'teliaeid', // Provisional 	[Peter_Lewandowski]
		'telnet', // Permanent 	[RFC4248]
		'tftp', // Permanent 	[RFC3617]
		'things', // Provisional 	[Dave_Thaler]
		'thismessage', // Permanent 	[RFC2557]
		'tip', // Permanent 	[RFC2371]
		'tn3270', // Permanent 	[RFC6270]
		'tool', // Provisional 	[Matthias_Merkel]
		'turn', // Permanent 	[RFC7065]
		'turns', // Permanent 	[RFC7065]
		'tv', // Permanent 	[RFC2838]
		'udp', // Provisional 	[Dave_Thaler]
		'unreal', // Provisional 	[Dave_Thaler]
		'urn', // Permanent 	[RFC2141][IANA registry urn-namespaces]
		'ut2004', // Provisional 	[Dave_Thaler]
		'v-event', // Provisional 	[draft-menderico-v-event-uri]
		'vemmi', // Permanent 	[RFC2122]
		'ventrilo', // Provisional 	[Dave_Thaler]
		'videotex', // Historical 	[draft-mavrakis-videotex-url-spec][RFC2122][RFC3986]
		'vnc', // Permanent 	[RFC7869]
		'view-source', // Provisional 	[Mykyta_Yevstifeyev]
		'wais', // Historical 	[RFC4156]
		'webcal', // Provisional 	[Dave_Thaler]
		'wpid', // Historical 	[Eld_Zierau]
		'ws', // Permanent 	[RFC6455]
		'wss', // Permanent 	[RFC6455]
		'wtai', // Provisional 	[Dave_Thaler]
		'wyciwyg', // Provisional 	[Dave_Thaler]
		'xcon', // Permanent 	[RFC6501]
		'xcon-userid', // Permanent 	[RFC6501]
		'xfire', // Provisional 	[Dave_Thaler]
		'xmlrpc.beep', // Permanent 	[RFC3529]
		'xmlrpc.beeps', // Permanent 	[RFC3529]
		'xmpp', // Permanent 	[RFC5122]
		'xri', // Provisional 	[Dave_Thaler]
		'ymsgr', // Provisional 	[Dave_Thaler]
		'z39.50', // Historical 	[RFC1738][RFC2056]
		'z39.50r', // Permanent 	[RFC2056]
		'z39.50s', // Permanent 	[RFC2056]
	];
	
	/**
	 * The scheme part of the uri.
	 * 
	 * @var string
	 */
	protected $_scheme = 'https';
	
	/**
	 * The user name of the authority.
	 * 
	 * @var ?string
	 */
	protected $_username = null;
	
	/**
	 * The user password of the authority.
	 * 
	 * @var ?string
	 */
	protected $_password = null;
	
	/**
	 * The host part of the uri.
	 * 
	 * @var ?string
	 */
	protected $_host = null;
	
	/**
	 * The port part of the uri.
	 * 
	 * @var ?integer
	 */
	protected $_port = null;
	
	/**
	 * The path component of the uri.
	 * 
	 * @var ?UriPathComponent
	 */
	protected $_path = null;
	
	/**
	 * The query part of the uri (which is after ?).
	 * 
	 * @var ?UriQueryComponent
	 */
	protected $_query = null;
	
	/**
	 * The fragment part of the uri (which is after #).
	 * 
	 * @var ?string
	 */
	protected $_fragment = null;
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::__toString()
	 */
	public function __toString() : string
	{
		$str = '';
		
		$auth = $this->getAuthority();
		if(!empty($auth))
			$str = $this->getScheme().'://'.$auth;
		
		$str .= $this->getPath();
		
		if(null !== $this->_query && !$this->_query->isEmpty())
			$str .= '?'.$this->_query->__toString();
		
		$frag = $this->getFragment();
		if(!empty($frag))
			$str .= '#'.$frag;
		
		return $str;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::getScheme()
	 */
	public function getScheme() : string
	{
		return (string) $this->_scheme;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::getAuthority()
	 */
	public function getAuthority() : string
	{
		$str = '';
		if(!empty($this->getUserInfo()))
			$str .= $this->getUserInfo().'@';
		
		$str .= $this->getHost();
		if(!empty($this->getPort()))
			$str .= ':'.((string) $this->getPort());
		
		return $str;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::getUserInfo()
	 */
	public function getUserInfo() : string
	{
		if(empty($this->_username))
			return '';
		
		$str = \rawurlencode($this->_username);
		if(!empty($this->_password))
			$str .= ':'.\rawurlencode($this->_password);
		
		return $str;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::getHost()
	 */
	public function getHost() : string
	{
		return (string) $this->_host;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::getPort()
	 * @return int|null
	 */
	public function getPort()
	{
		return $this->_port;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::getPath()
	 */
	public function getPath() : string
	{
		if(null === $this->_path)
			return '/';
		
		return '/'.$this->_path->__toString();
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::getQuery()
	 */
	public function getQuery() : string
	{
		if(null === $this->_query)
			return '';
		
		return $this->_query->__toString();
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::getFragment()
	 */
	public function getFragment() : string
	{
		return \rawurlencode((string) $this->_fragment);
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::withScheme()
	 */
	public function withScheme($scheme)
	{
		if($scheme === $this->_scheme)
			return $this;
		
		if(!\in_array($scheme, self::$_allowedSchemes))
		{
			$suggest = [];
			
			foreach(self::$_allowedSchemes as $asch)
				if(2 >= \levenshtein($asch, $scheme))
					$suggest[] = $asch;
			
			$message = 'Invalid scheme "{sch}". Do you mean one of: {list}?';
			$context = ['{sch}' => $scheme, '{list}' => \implode(', ', $suggest)];
			
			throw new InvalidArgumentException(\strtr($message, $context));
		}
		
		$newobj = clone $this;
		$newobj->_scheme = $scheme;
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::withUserInfo()
	 */
	public function withUserInfo($user, $password = null)
	{
		if(empty($user))
			return $this;
		
		$newobj = clone $this;
		
		$newobj->_username = (string) \str_replace('+', ' ', \rawurldecode((string) $user));
		if(!empty($password))
			$newobj->_password = (string) \str_replace('+', ' ', \rawurldecode((string) $password));
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::withHost()
	 */
	public function withHost($host)
	{
		if(empty($host) || $host === $this->_host)
			return $this;
		
		if(!\preg_match('#[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)+#', $host))
			throw new InvalidArgumentException(\strtr('The given hostname "{value}" is not valid.', ['{value}' => $host]));
		
		$newobj = clone $this;
		$newobj->_host = \strtolower($host);
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::withPort()
	 */
	public function withPort($port)
	{
		if(empty($port) && empty($this->_port))
			return $this;
		
		if($port === $this->_port)
			return $this;
		
		if(null !== $port && (0 >= $port || 65535 < $port))
			throw new InvalidArgumentException(\strtr('The given port "{port}" is not in range 1-65535.', ['{port}' => $port]));
		
		$newobj = clone $this;
		$newobj->_port = $port;
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::withPath()
	 */
	public function withPath($path)
	{
		if(empty($path))
			return $this;
		
		$newobj = clone $this;
		
		if((0 < \strlen($path) && '/' === $path[0]) || null === $this->_path)
		{
			// absolute path
			$newobj->_path = new UriPathComponent($path);
			
			return $newobj;
		}
		
		// relative path
		$newobj->_path = $this->_path->absorb($path);
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::withQuery()
	 */
	public function withQuery($query)
	{
		$newobj = clone $this;
		
		if(null === $newobj->_query)
			$newobj->_query = new UriQueryComponent();
		
		$newobj->_query->absorb($query);
		
		return $newobj;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Psr\Http\Message\UriInterface::withFragment()
	 */
	public function withFragment($fragment)
	{
		if(empty($fragment) && empty($this->_fragment))
			return $this;
		
		if($fragment === $this->_fragment)
			return $this;
		
		$newobj = clone $this;
		$newobj->_fragment = \str_replace('+', ' ', \rawurldecode($fragment));
		
		return $newobj;
	}
	
}
