<?php

use Aza\Components\PhpGen\CustomCode;
use Aza\Components\PhpGen\IPhpGenerable;
use Aza\Components\PhpGen\PhpGen;

require __DIR__ . '/../vendor/autoload.php';


/**
 * AzaPhpGen examples
 *
 * @project Anizoptera CMF
 * @package system.phpgen
 * @author  Amal Samally <amal.samally at gmail.com>
 * @license MIT
 */



// Example #1 - Simple dump
// ------------------------

// Get singleton instance of PhpGen
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


// Example #2 - Array dump
// ------------------------

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



// Example #3 - Traversable dump
// ------------------------

$var = new SplFixedArray(3);
$var[0] = 'a';
$var[1] = 'b';
echo $phpGen->getCodeNoFormat($var) . PHP_EOL; // ["a","b",null];



// Example #4 - Closure example
// ------------------------

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



// Example #5 - Custom object dumping with IPhpGenerable interface
// ------------------------

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



// Example #6 - Bundled simple CustomCode class usage
// ------------------------

$var = new CustomCode('"some code" . PHP_EOL');

echo $phpGen->getCode($var) . PHP_EOL; // "some code" . PHP_EOL;

echo $phpGen->getCode(array($var)) . PHP_EOL; // ["some code" . PHP_EOL];



// Example #7 - Custom object dumping with defined handlers
// ------------------------

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



// Example #8 - AzaPhpGen customization
// ------------------------

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
