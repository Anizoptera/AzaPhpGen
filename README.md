AzaPhpGen
=========

Anizoptera CMF PHP code generation (dump, serialization) component.

https://github.com/Anizoptera/AzaPhpGen

[![Build Status][TravisImage]][Travis]


Table of Contents
-----------------

1. [Introduction](#introduction)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Examples](#examples)
   * [Simple dump](#example-1---simple-dump)
   * [Array dump](#example-2---array-dump)
   * [Traversable dump](#example-3---traversable-dump)
   * [Closure (anonymous function) example](#example-4---closure-anonymous-function-example)
   * [Custom object dumping with IPhpGenerable interface](#example-5---custom-object-dumping-with-iphpgenerable-interface)
   * [Bundled CustomCode class usage](#example-6---bundled-customcode-class-usage)
   * [Custom object dumping with defined handlers](#example-7---custom-object-dumping-with-defined-handlers)
   * [AzaPhpGen customization](#example-8---azaphpgen-customization)
5. [Tests](#tests)
6. [Credits](#credits)
7. [License](#license)
8. [Links](#links)


Introduction
------------

Allows to dump complex arrays, objects, closures and basic data types as php code.
In part, this can be called a some sort of serialization.
And you can customize your dumped php code as you wish.

It is very usefull for code compilation (usually for caching purposes).


**Features:**

- Supports all scalar values (bool, int, float, string), nulls, arrays, serializable objects;
- [Traversable](http://php.net/traversable) support (dumped as array, see usage in [Example #3](#example-3---traversable-dump));
- Closures support (closures with "use", several closures on the same line are not supported!) (see usage and more info in [Example #4](#example-4---closure-anonymous-function-example));
- Custom object dumping with [IPhpGenerable interface](IPhpGenerable.php) (see usage in [Example #5](#example-5---custom-object-dumping-with-iphpgenerable-interface));
- Bundled simple [CustomCode class](CustomCode.php) (see usage in [Example #6](#example-6---bundled-customcode-class-usage));
- Custom object dumping with defined handlers/hooks (see usage in [Example #7](#example-7---custom-object-dumping-with-defined-handlers));
- Very flexible configuration (9 code building options, see in [PhpGen class code](PhpGen.php#L19));
- Automatic recognition of binary strings;
- Convenient, fully documented and test covered API;


**Benefits over `var_export()`:**

- `var_export` does not support Closures dumping;
- `var_export` supports only objects with `__set_state` function. AzaPhpGen supports all serializable objects;
- AzaPhpGen dumps Traversable objects as arrays (via `iterator_to_array`);
- For binary strings `var_export` generates very ugly code that is awkward to use and can be easily corrupted;
- For objects `var_export` generates code that can not be evaluated in namespace;
- AzaPhpGen give you full control over objects dumping with custom handlers and `IPhpGenerable` interface;
- With AzaPhpGen you can flexibly customize formatting of your code (useful for arrays);
- AzaPhpGen can generate code with or without trailing semicolon. `var_export` never outputs it :)
- Some detailed comparisons you can see in [Tests/PhpGenBenchmarkTest.php](Tests/PhpGenBenchmarkTest.php#L647);


Requirements
------------

* PHP 5.3.3 (or later);
* SPL and Reflection extensions for closures support (both bundled with PHP by default);


Installation
------------

The recommended way to install AzaPhpGen is [through composer](http://getcomposer.org).
You can see [package information on Packagist][ComposerPackage].

```JSON
{
	"require": {
		"aza/phpgen": "~1.0"
	}
}
```


Examples
--------

You can use [examples/example.php](examples/example.php) to run all examples.

#### Example #1 - Simple dump

```php
// Get singleton instance of PhpGen (fast and simple variant)
$phpGen = PhpGen::instance();
// Integer
echo $phpGen->getCode(123456789) . PHP_EOL; // 123456789;
// String (binary strings are supported as well)
echo $phpGen->getCode('some string' . ' example') . PHP_EOL; // "some string example";
// Float without trailing semicolon
echo $phpGen->getCodeNoTail(12.345) . PHP_EOL; // 12.345
// Simple serializable objects
$var = new stdClass();
echo $phpGen->getCode($var) . PHP_EOL; // unserialize("O:8:\"stdClass\":0:{}");
// Another object example
$var = new DateTime('2013-02-23 00:49:36', new DateTimeZone('UTC'));
echo $phpGen->getCode($var) . PHP_EOL; // unserialize("O:8:\"DateTime\":3:{s:4:\"date\";s:19:\"2013-02-23 00:49:36\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:3:\"UTC\";}");
```

#### Example #2 - Array dump

```php
// AzaPhpGen will use short array syntax if possible by default (PHP >= 5.4)
echo $phpGen->getCode(array(
	true, false, null
)) . PHP_EOL;
/*
[
	true,
	false,
	null,
];
 */

// Build code without formatting
echo $phpGen->getCodeNoFormat(array(
	true, false, null
)) . PHP_EOL;
/*
[true,false,null];
 */

// Complex array (some sort of config for example)
$array = array(
	'key1'     => 'value',
	'long_key' => 'value',
	'array'    => array(
		'short_value'
	),
	'array2' => array(
		'very very very very very very very very very very very very long value'
	),
	'other',
	123456789
);
echo $phpGen->getCode($array) . PHP_EOL;
/*
[
	"key1"     => "value",
	"long_key" => "value",
	"array"    => ["short_value"],
	"array2"   => [
		"very very very very very very very very very very very very long value",
	],
	0 => "other",
	1 => 123456789,
];
 */

// And wothout formatting
echo $phpGen->getCodeNoFormat($array) . PHP_EOL;
/*
["key1"=>"value","long_key"=>"value","array"=>["short_value"],"array2"=>["very very very very very very very very very very very very long value"],0=>"other",1=>123456789];
 */
```

#### Example #3 - Traversable dump

AzaPhpGen treat all Traversable objects as arrays (with [iterator_to_array](http://php.net/iterator-to-array)).

```php
$var = new SplFixedArray(3);
$var[0] = 'a';
$var[1] = 'b';
echo $phpGen->getCodeNoFormat($var) . PHP_EOL; // ["a","b",null];
```

#### Example #4 - Closure (anonymous function) example

**WARNING:** Closures are dumped as is. So complex closures are not supported:
* Closures with "use" statement (closures that inherit variables from the parent scope);
* Several closures on the same line;
* Usage of non-qualified class name (with importing) in closure;
* Closures with `$this` variable usage;

```php
$closure = function($a, $b) {
	return round($a, $b) . "example\t\n";
};
echo $phpGen->getCode($closure) . PHP_EOL;
/*
function($a, $b) {
	return round($a, $b) . "example\t\n";
};
 */
echo $phpGen->getCode(array('key' => $closure)) . PHP_EOL;
/*
[
	"key" => function($a, $b) {
	return round($a, $b) . "example\t\n";
},
];
 */
```

#### Example #5 - Custom object dumping with IPhpGenerable interface

You can customize dumping of your classes by implementing the `IPhpGenerable` interface.

```php
class ExampleCustomCode implements IPhpGenerable
{
	public function generateCode()
	{
		return '32434 + 5678';
	}
}

$var = new ExampleCustomCode();

echo $phpGen->getCode($var) . PHP_EOL; // 32434 + 5678;

echo $phpGen->getCode(array($var)) . PHP_EOL; // [32434 + 5678];
```

#### Example #6 - Bundled CustomCode class usage

For the simpliest varint of `IPhpGenerable` interface usages you can use bundled class - `CustomCode`.
It just takes the required code as a constructor argument.

```php
$var = new CustomCode('"some code" . PHP_EOL');

echo $phpGen->getCode($var) . PHP_EOL; // "some code" . PHP_EOL;

echo $phpGen->getCode(array($var)) . PHP_EOL; // ["some code" . PHP_EOL];
```

#### Example #7 - Custom object dumping with defined handlers

Second varint of resulting code customization - usage of defined handlers (hooks) for the classes.
This way you can customize dump of any possible class!

```php
// Set custom handler for DateTime type
$phpGen->addCustomHandler('DateTime', function($data) use ($phpGen) {
	/** @var $data \DateTime */
	return $phpGen->getCodeNoTail(
		$data->format("Y-m-dO")
	);
});
// Build code
$var = new DateTime('2013-02-23 00:49:36', new DateTimeZone('UTC'));
echo $phpGen->getCode($var) . PHP_EOL; // "2013-02-23+0000";
```

#### Example #8 - AzaPhpGen customization

AzaPhpGen has many options. So it's very simple to configure your resulting code
for your special needs (code style for example).
You can see all available options in the [PhpGen class code](PhpGen.php#L19).

```php
// Disable short array syntax and use 6 spaces for indentation
$phpGen->shortArraySyntax = false;
$phpGen->useSpaces        = true;
$phpGen->tabLength        = 6;
$var = array(array(array(23 => 'example')));
echo $phpGen->getCode($var) . PHP_EOL;
/*
array(
      array(
            array(
                  23 => "example",
            ),
      ),
);
 */
```


Tests
-----

Tests are in the `Tests` folder and reach 100% code-coverage.
To run them, you need PHPUnit.
Example:

    $ phpunit --configuration phpunit.xml.dist

Or with coverage report:

    $ phpunit --configuration phpunit.xml.dist --coverage-html code_coverage/


Credits
-------

AzaPhpGen is a part of [Anizoptera CMF][], written by [Amal Samally][] (amal.samally at gmail.com) and [AzaGroup][] team.


License
-------

Released under the [MIT](LICENSE.md) license.


Links
-----

* [Composer package][ComposerPackage]
* [Last build on the Travis CI][Travis]
* [Project profile on the Ohloh](https://www.ohloh.net/p/AzaPhpGen)
* Other Anizoptera CMF components on the [GitHub][Anizoptera CMF] / [Packagist](https://packagist.org/packages/aza)
* (RU) [AzaGroup team blog][AzaGroup]



[Anizoptera CMF]:  https://github.com/Anizoptera
[Amal Samally]:    http://azagroup.ru/about/#amal
[AzaGroup]:        http://azagroup.ru/
[ComposerPackage]: https://packagist.org/packages/aza/phpgen
[TravisImage]:     https://secure.travis-ci.org/Anizoptera/AzaPhpGen.png?branch=master
[Travis]:          http://travis-ci.org/Anizoptera/AzaPhpGen
