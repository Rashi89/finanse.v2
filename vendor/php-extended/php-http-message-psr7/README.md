# php-extended/php-http-message-psr7
Another implementation of the psr7 : php-fig/http-message interface library

![coverage](https://gitlab.com/php-extended/php-http-message-psr7/badges/master/pipeline.svg?style=flat-square)
![build status](https://gitlab.com/php-extended/php-http-message-psr7/badges/master/coverage.svg?style=flat-square)


## Installation

The installation of this library is made via composer.
Download `composer.phar` from [their website](https://getcomposer.org/download/).
Then add to your composer.json :

```json
	"require": {
		...
		"php-extended/php-http-message-psr7": "^3",
		...
	}
```
Then run `php composer.phar update` to install this library.
The autoloading of all classes of this library is made through composer's autoloader.


## Basic Usage

This library implements the rules of the psr7, [which are to be found here](http://www.php-fig.org/psr/psr-7/).

The interfaces and their implementations are as follows :

| Interface                                 | Implementation                          |
|-------------------------------------------|-----------------------------------------|
| `Psr\Http\Message\MessageInterface`       | `PhpExtended\HttpMessage\Message`       |
| `Psr\Http\Message\RequestInterface`       | `PhpExtended\HttpMessage\Request`       |
| `Psr\Http\Message\ResponseInterface`      | `PhpExtended\HttpMessage\Response`      |
| `Psr\Http\Message\ServerRequestInterface` | `PhpExtended\HttpMessage\ServerRequest` |
| `Psr\Http\Message\StreamInterface`        | `PhpExtended\HttpMessage\StringStream`  |
|                                           | `PhpExtended\HttpMessage\FileStream`    |
| `Psr\Http\Message\UploadedFileInterface`  | `PhpExtended\HttpMessage\UploadedFile`  |
| `Psr\Http\Message\UriInterface`           | `PhpExtended\HttpMessage\Uri`           |


## License

MIT (See [license file](LICENSE)).
