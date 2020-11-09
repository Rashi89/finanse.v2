# php-extended/polyfill-php80-stringable

A polyfill that adds the stringable interface added in php8.0 to previous
versions of php.

![coverage](https://gitlab.com/php-extended/polyfill-php80-stringable/badges/master/pipeline.svg?style=flat-square)


## Installation

The installation of this library is made via composer.
Download `composer.phar` from [their website](https://getcomposer.org/download/).
Then add to your composer.json :

```json
	"require": {
		...
		"php-extended/polyfill-php80-stringable": "^1",
		...
	}
```

Then run `php composer.phar update` to install this library.
The autoloading of all classes of this library is made through composer's autoloader.


## Motivation

There are a number of implementations of the `\Stringable` interface introduced
by the rfc into php8.0, but none of them actually fit my needs. Those are:
 - strict standard implementation that follows the rfc proposal ;
 - transparent replacement for when the php version is upped to php8 withtout the
need to redefine dependancy constraints for packages that depends on this one ;
 - backward compatibility up to php7.0


First, there is the [symfony implementation](https://github.com/symfony/polyfill).
But by compatibility with php5, that implementation does not strictly follows
the implementation described into the rfc (missing return type).


Then, there is the [raigu implementation](https://github.com/raigu/php80-stringable-polyfill)
but that implementation allows exceptions to be thrown (which is a behavior
that is allowed by php7.4 and php8). I need to have this interface available
for lower versions of php7 and given the restriction on package versions, that
implementation is unavailable.


Then, there is the [fleshgrinder implementation](https://github.com/Fleshgrinder/php-stringable).
That implementation does not follow the rfc and is in its own namespace, which
do not permit for transparent replacement when the php version of any program
is upped to the php8 version.


There may be others implementations available that are not listed there.



## License

MIT (See [license file](LICENSE)).
