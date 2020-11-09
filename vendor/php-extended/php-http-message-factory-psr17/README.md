# php-extended/php-http-message-factory-psr17
An implementation of the psr-17 based on the php-http-message-psr7 library.

![coverage](https://gitlab.com/php-extended/php-http-message-factory-psr17/badges/master/pipeline.svg?style=flat-square)
![build status](https://gitlab.com/php-extended/php-http-message-factory-psr17/badges/master/coverage.svg?style=flat-square)


## Installation

The installation of this library is made via composer.
Download `composer.phar` from [their website](https://getcomposer.org/download/).
Then add to your composer.json :

```json
	"require": {
		...
		"php-extended/php-http-message-factory-psr17": "^3",
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
| `Psr\Http\Message\RequestFactoryInterface`       | `PhpExtended\HttpMessage\RequestFactory`       |
| `Psr\Http\Message\ResponseFactoryInterface`       | `PhpExtended\HttpMessage\ResponseFactory`       |
| `Psr\Http\Message\ServerRequestFactoryInterface` | `PhpExtended\HttpMessage\ServerRequestFactory` |
| `Psr\Http\Message\StreamFactoryInterface`        | `PhpExtended\HttpMessage\StreamFactory`  |
| `Psr\Http\Message\UploadedFileFactoryInterface`  | `PhpExtended\HttpMessage\UploadedFileFactory`  |
| `Psr\Http\Message\UriFactoryInterface`           | `PhpExtended\HttpMessage\UriFactory`           |


## License

MIT (See [license file](LICENSE)).
