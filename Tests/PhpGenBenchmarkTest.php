<?php

namespace Aza\Components\PhpGen\Tests;
use Aza\Components\Benchmark;
use Aza\Components\Common\Date;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * PhpGen benchmarks
 *
 * @project Anizoptera CMF
 * @package system.phpgen
 * @author  Amal Samally <amal.samally at gmail.com>
 * @license MIT
 *
 * @group benchmark
 * @coversNothing
 */
class PhpGenBenchmarkTest extends TestCase
{
	/**
	 * Check strings variants
	 *
	 * @author amal
	 */
	public function testStrings()
	{
		/**
		 * Check current bc scale
		 *
		 * strlen(bcsub(0, 0))-2
		 * + Fastest variant
		 *
		 * strlen(bcadd(0, 0))-2
		 * - ~1-30% slower than bcsub()
		 *
		 * strlen(bcdiv(0, 1))-2
		 * - ~20-450% slower than bcsub()
		 */
		$iteratons = 100000;
		$tests     = 20;

		$res = array();
		for ($j = 0; $j < $tests; $j++) {
			$start = microtime(true);
			for ($i = 0; $i < $iteratons; $i++) {
				$v = "\n\t1\r\0\1\2\3\4\5\6\7'\"\$v";
			}
			$res['escaped'][] = Date::timeEnd($start);

			$start = microtime(true);
			for ($i = 0; $i < $iteratons; $i++) {
				$v = "
	1
 '\"\$v";
			}
			$res['raw'][] = Date::timeEnd($start);
		}
		$results = Benchmark::analyzeResults($res);

		print_r($results);
	}
}
