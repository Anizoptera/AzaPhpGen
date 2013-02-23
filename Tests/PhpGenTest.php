<?php

namespace Aza\Components\PhpGen\Tests;
use Aza\Components\PhpGen\CustomCode;
use Aza\Components\PhpGen\IPhpGenerable;
use Aza\Components\PhpGen\PhpGen;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Testing PHP code generation
 *
 * @project Anizoptera CMF
 * @package system.phpgen
 *
 * @covers Aza\Components\PhpGen\PhpGen
 * @covers Aza\Components\PhpGen\IPhpGenerable
 * @covers Aza\Components\PhpGen\CustomCode
 */
class PhpGenTest extends TestCase
{
	/**
	 * @var int
	 */
	protected $precision;

	/**
	 * @var PhpGen
	 */
	protected $phpGen;


	/**
	 * {@inheritdoc}
	 */
	protected function setUp()
	{
		// Preparations
		$this->precision = ini_get('precision');
		$this->phpGen    = new PhpGen();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function tearDown()
	{
		// Restore precision
		ini_set('precision', $this->precision);

		// Cleanup
		$this->precision =  $this->phpGen = null;
	}


	/**
	 * Tests PhpGen singleton
	 *
	 * @author amal
	 * @group unit
	 */
	public function testInstance()
	{
		$phpGen   = $this->phpGen;
		$instance = PhpGen::instance();

		$this->assertNotSame($phpGen, $instance);
		$this->assertSame($instance, PhpGen::instance());

		$this->assertSame(
			version_compare(PHP_VERSION, '5.4.0', '>='),
			$instance->shortArraySyntax
		);
	}


	/**
	 * Tests code generation for NULL type
	 *
	 * @author amal
	 * @group unit
	 */
	public function testNull()
	{
		$phpGen = $this->phpGen;

		$var = null;

		// ----
		$result = $phpGen->getCode($var);
		$this->assertSame('null;', $result);
		$this->assertSame($var, eval("return $result"));


		// ----
		$result = $phpGen->getCode($var, 1);
		$this->assertSame('null;', $result);


		// ----
		$result = $phpGen->getCode($var, 3);
		$this->assertSame('null;', $result);


		// ----
		$result = $phpGen->getCode($var, 0, true);
		$this->assertSame('null;', $result);


		// ----
		$result = $phpGen->getCodeNoFormat($var);
		$this->assertSame('null;', $result);


		// ----
		$result = $phpGen->getCode($var, 0, false, true);
		$this->assertSame('null', $result);


		// ----
		$result = $phpGen->getCodeNoTail($var);
		$this->assertSame('null', $result);
	}

	/**
	 * Tests code generation for Boolean type
	 *
	 * @author amal
	 * @group unit
	 */
	public function testBool()
	{
		$phpGen = $this->phpGen;

		$var = true;

		// ----
		$result = $phpGen->getCode($var);
		$this->assertSame('true;', $result);
		$this->assertSame($var, eval("return $result"));


		// ----
		$result = $phpGen->getCode($var, 1);
		$this->assertSame('true;', $result);


		// ----
		$result = $phpGen->getCode($var, 3);
		$this->assertSame('true;', $result);


		// ----
		$result = $phpGen->getCode($var, 0, true);
		$this->assertSame('true;', $result);


		// ----
		$result = $phpGen->getCodeNoFormat($var);
		$this->assertSame('true;', $result);


		// ----
		$result = $phpGen->getCode($var, 0, false, true);
		$this->assertSame('true', $result);


		// ----
		$result = $phpGen->getCodeNoTail($var);
		$this->assertSame('true', $result);


		// ===================

		$var = false;

		// ----
		$result = $phpGen->getCode($var);
		$this->assertSame('false;', $result);
		$this->assertSame($var, eval("return $result"));


		// ----
		$result = $phpGen->getCode($var, 1);
		$this->assertSame('false;', $result);


		// ----
		$result = $phpGen->getCode($var, 3);
		$this->assertSame('false;', $result);


		// ----
		$result = $phpGen->getCode($var, 0, true);
		$this->assertSame('false;', $result);


		// ----
		$result = $phpGen->getCodeNoFormat($var);
		$this->assertSame('false;', $result);


		// ----
		$result = $phpGen->getCode($var, 0, false, true);
		$this->assertSame('false', $result);


		// ----
		$result = $phpGen->getCodeNoTail($var);
		$this->assertSame('false', $result);
	}

	/**
	 * Tests code generation for Integer type
	 *
	 * @author amal
	 * @group unit
	 */
	public function testInt()
	{
		$phpGen = $this->phpGen;

		$var = 1234567;

		// ----
		$result = $phpGen->getCode($var);
		$this->assertSame('1234567;', $result);
		$this->assertSame($var, eval("return $result"));


		// ----
		$result = $phpGen->getCode($var, 1);
		$this->assertSame('1234567;', $result);


		// ----
		$result = $phpGen->getCode($var, 3);
		$this->assertSame('1234567;', $result);


		// ----
		$result = $phpGen->getCode($var, 0, true);
		$this->assertSame('1234567;', $result);


		// ----
		$result = $phpGen->getCodeNoFormat($var);
		$this->assertSame('1234567;', $result);


		// ----
		$result = $phpGen->getCode($var, 0, false, true);
		$this->assertSame('1234567', $result);


		// ----
		$result = $phpGen->getCodeNoTail($var);
		$this->assertSame('1234567', $result);


		// ===================

		// ----
		$var = 0;
		$result = $phpGen->getCode($var);
		$this->assertSame('0;', $result);
		$this->assertSame($var, eval("return $result"));

		// ----
		$var = -123;
		$result = $phpGen->getCode($var);
		$this->assertSame('-123;', $result);
		$this->assertSame($var, eval("return $result"));

		// ----
		$var = 0x123;
		$result = $phpGen->getCode($var);
		$this->assertSame('291;', $result);
		$this->assertSame($var, eval("return $result"));

		// ----
		$var = 0123;
		$result = $phpGen->getCode($var);
		$this->assertSame('83;', $result);
		$this->assertSame($var, eval("return $result"));

		// ----
		$var = 9999999999;
		$result = $phpGen->getCode($var);
		$this->assertSame('9999999999;', $result);
		$this->assertSame($var, eval("return $result"));
	}

	/**
	 * Tests code generation for Float type
	 *
	 * @author amal
	 * @group unit
	 */
	public function testFloat()
	{
		$phpGen = $this->phpGen;

		$var = 1234.5678;

		// ----
		ini_set('precision', 1);
		$result = $phpGen->getCode($var);
		$this->assertSame('1.0E+3;', $result);
		$result = $phpGen->getCode(1.0E+3);
		$this->assertSame('1.0E+3;', $result);

		// ----
		ini_set('precision', 2);
		$result = $phpGen->getCode($var);
		$this->assertSame('1.2E+3;', $result);
		$result = $phpGen->getCode(1.0E+3);
		$this->assertSame('1.0E+3;', $result);

		// ----
		ini_set('precision', 5);
		$result = $phpGen->getCode($var);
		$this->assertSame('1234.6;', $result);
		$result = $phpGen->getCode(1.0E+3);
		$this->assertSame('1000;', $result);
		$result = $phpGen->getCode(1.0E+6);
		$this->assertSame('1.0E+6;', $result);

		// ----
		ini_set('precision', 16);
		$result = $phpGen->getCode($var);
		$this->assertSame('1234.5678;', $result);
		$this->assertSame($var, eval("return $result"));
		$result = $phpGen->getCode(1.0E+3);
		$this->assertSame('1000;', $result);
		$result = $phpGen->getCode(1.0E+6);
		$this->assertSame('1000000;', $result);
		$result = $phpGen->getCode(1.0E+20);
		$this->assertSame('1.0E+20;', $result);


		// ----
		$result = $phpGen->getCode($var, 1);
		$this->assertSame('1234.5678;', $result);


		// ----
		$result = $phpGen->getCode($var, 3);
		$this->assertSame('1234.5678;', $result);


		// ----
		$result = $phpGen->getCode($var, 0, true);
		$this->assertSame('1234.5678;', $result);


		// ----
		$result = $phpGen->getCodeNoFormat($var);
		$this->assertSame('1234.5678;', $result);


		// ----
		$result = $phpGen->getCode($var, 0, false, true);
		$this->assertSame('1234.5678', $result);


		// ----
		$result = $phpGen->getCodeNoTail($var);
		$this->assertSame('1234.5678', $result);


		// ===================

		// ----
		$var = 0.0;
		$result = $phpGen->getCode($var);
		$this->assertSame('0;', $result);

		// ----
		$var = 0.001;
		$result = $phpGen->getCode($var);
		$this->assertSame('0.001;', $result);

		// ----
		$var = -1.0;
		$result = $phpGen->getCode($var);
		$this->assertSame('-1;', $result);

		// ----
		$var = -1.1;
		$result = $phpGen->getCode($var);
		$this->assertSame('-1.1;', $result);

		// ----
		$var = 99999.99999;
		$result = $phpGen->getCode($var);
		$this->assertSame('99999.99999;', $result);
	}

	/**
	 * Tests code generation for String type
	 *
	 * @author amal
	 * @group unit
	 */
	public function testString()
	{
		$phpGen = $this->phpGen;

		$var1 = 'example';
		$var2 = 'example $var';
		$var3 = "\n\t\r\0\1\2\3\4\5\6\7'\"\$v";
		for ($i = 0, $var4 = ''; $i < 128; $i++) $var4 .= chr($i); // ASCII
		for ($i = 0, $var5 = ''; $i < 256; $i++) $var5 .= chr($i); // 0x00-0xFF
		$var6 = <<<TXT
The recommended way to install AzaPhpGen is [through composer](http://getcomposer.org).
You can see [package information on Packagist.](https://packagist.org/packages/aza/phpgen)

```JSON
{
	"require": {
		"aza/phpgen": "~1.0"
	}
}
```
TXT;


		// ----
		$phpGen->oneLineStrings = false;

		$result = $phpGen->getCode($var1);
		$this->assertSame($var1, eval("return $result"));
		$this->assertSame('"example";', $result);

		$result = $phpGen->getCode($var2);
		$this->assertSame($var2, eval("return $result"));
		$this->assertSame('"example \\$var";', $result);

		$result = $phpGen->getCode($var3);
		$this->assertSame($var3, eval("return $result"));
		$this->assertSame(
			'"
	\r\x00\x01\x02\x03\x04\x05\x06\x07\'\\"\\$v";',
			$result
		);

		$result = $phpGen->getCode($var4);
		$this->assertSame($var4, eval("return $result"));
		$this->assertSame(
			"\"\\x00\\x01\\x02\\x03\\x04\\x05\\x06\\x07\\x08"
				."\t\n\\v\\f\\r"
				."\\x0E\\x0F\\x10\\x11\\x12\\x13\\x14\\x15\\x16\\x17\\x18\\x19\\x1A\\x1B\\x1C\\x1D\\x1E\\x1F"
				." !\\\"#\\$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\\x7F"
			."\";",
			$result
		);

		$result = $phpGen->getCode($var5);
		$this->assertSame($var5, eval("return $result"));

		$result = $phpGen->getCode($var6);
		$this->assertSame($var6, eval("return $result"));
		$this->assertSame(
			'"The recommended way to install AzaPhpGen is [through composer](http://getcomposer.org).
You can see [package information on Packagist.](https://packagist.org/packages/aza/phpgen)

```JSON
{
	\"require\": {
		\"aza/phpgen\": \"~1.0\"
	}
}
```";',
			$result
		);


		// ----
		$phpGen->oneLineStrings = true;

		$result = $phpGen->getCode($var1);
		$this->assertSame($var1, eval("return $result"));
		$this->assertSame('"example";', $result);

		$result = $phpGen->getCode($var2);
		$this->assertSame($var2, eval("return $result"));
		$this->assertSame('"example \\$var";', $result);

		$result = $phpGen->getCode($var3);
		$this->assertSame($var3, eval("return $result"));
		$this->assertSame(
			'"\n\t\r\x00\x01\x02\x03\x04\x05\x06\x07\'\\"\\$v";',
			$result
		);

		$result = $phpGen->getCode($var4);
		$this->assertSame($var4, eval("return $result"));
		$this->assertSame(
			"\"\\x00\\x01\\x02\\x03\\x04\\x05\\x06\\x07\\x08"
			."\\t\\n\\v\\f\\r"
			."\\x0E\\x0F\\x10\\x11\\x12\\x13\\x14\\x15\\x16\\x17\\x18\\x19\\x1A\\x1B\\x1C\\x1D\\x1E\\x1F"
			." !\\\"#\\$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\\x7F"
			."\";",
			$result
		);

		$result = $phpGen->getCode($var5);
		$this->assertSame($var5, eval("return $result"));

		$result = $phpGen->getCode($var6);
		$this->assertSame($var6, eval("return $result"));
		$this->assertSame(
			'"The recommended way to install AzaPhpGen is [through composer](http://getcomposer.org).\n'
			.'You can see [package information on Packagist.](https://packagist.org/packages/aza/phpgen)\n\n'
			.'```JSON\n{\n\t\"require\": {\n\t\t\"aza/phpgen\": \"~1.0\"\n\t}\n}\n```";',
			$result
		);
	}

	/**
	 * Tests code generation for String type
	 *
	 * @author amal
	 * @group unit
	 */
	public function testObject()
	{
		$phpGen = $this->phpGen;

		$var = new \stdClass();
		$result = $phpGen->getCode($var);
		$evaled_result = eval("return $result");
		$this->assertNotSame($var, $evaled_result);
		$this->assertEquals($var, $evaled_result);
		$this->assertSame('unserialize("O:8:\"stdClass\":0:{}");', $result);

		$var = (object)array();
		$result = $phpGen->getCode($var);
		$this->assertSame('unserialize("O:8:\"stdClass\":0:{}");', $result);

		$var = (object)array('a' => 1, 'b' => 2);
		$result = $phpGen->getCode($var);
		$evaled_result = eval("return $result");
		$this->assertNotSame($var, $evaled_result);
		$this->assertEquals($var, $evaled_result);
		$this->assertSame(
			'unserialize("O:8:\"stdClass\":2:{s:1:\"a\";i:1;s:1:\"b\";i:2;}");',
			$result
		);

		$var = new \DateTime('2013-02-23 00:49:36', new \DateTimeZone('UTC'));
		$result = $phpGen->getCode($var);
		$evaled_result = eval("return $result");
		$this->assertTrue($evaled_result instanceof $var);
		$this->assertNotSame($var, $evaled_result);
		$this->assertEquals($var, $evaled_result);
		$this->assertSame(
			'unserialize("O:8:\"DateTime\":3:{s:4:\"date\";s:19:\"2013-02-23 00:49:36\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:3:\"UTC\";}");',
			$result
		);
	}

	/**
	 * Tests code generation for Closure type
	 *
	 * @author amal
	 * @group unit
	 *
	 * @requires extension reflection
	 * @requires extension spl
	 */
	public function testClosure()
	{
		$phpGen = $this->phpGen;


		$var = function($a, $b) {
			return $a + $b;
		};
		$result = $phpGen->getCode($var);
		$evaled_result = eval("return $result");
		$this->assertTrue($evaled_result instanceof $var);
		$this->assertNotSame($var, $evaled_result);
		$this->assertEquals($var, $evaled_result);
		$this->assertSame('function($a, $b) {
			return $a + $b;
		};', $result);


		$var = function() {
			return round(1, 100) . "example\t\n";
		};
		$result = $phpGen->getCode($var);
		$evaled_result = eval("return $result");
		$this->assertTrue($evaled_result instanceof $var);
		$this->assertNotSame($var, $evaled_result);
		$this->assertEquals($var, $evaled_result);
		$this->assertSame('function() {
			return round(1, 100) . "example\t\n";
		};', $result);
		$this->assertSame('function() {
			return round(1, 100) . "example\t\n";
		};', $phpGen->getCodeNoFormat($var));
		$this->assertSame('function() {
			return round(1, 100) . "example\t\n";
		}', $phpGen->getCodeNoTail($var));
	}

	/**
	 * Tests custom code generation
	 *
	 * @author amal
	 * @group unit
	 */
	public function testCustomCode()
	{
		$phpGen = $this->phpGen;

		$code = '2+5';
		$var = new CustomCode($code);
		$result = $phpGen->getCode($var);
		$this->assertSame(7, eval("return $result"));
		$this->assertSame("$code;", $result);

		$result = $phpGen->getCodeNoTail($var);
		$this->assertSame(7, eval("return $result;"));
		$this->assertSame($code, $result);
		$this->assertSame($var->generateCode(), $result);

		$code = 'DIRECTORY_SEPARATOR . "someFile." . (13-7)';
		$var = new CustomCode($code);
		$result = $phpGen->getCode($var);
		$this->assertSame(DIRECTORY_SEPARATOR . 'someFile.6', eval("return $result"));
		$this->assertSame("$code;", $result);


		$var = new _ExampleCustomCode();
		$this->assertSame("{$var->generateCode()};", $phpGen->getCode($var));
		$this->assertSame("{$var->generateCode()};", $phpGen->getCodeNoFormat($var));
		$this->assertSame($var->generateCode(), $phpGen->getCodeNoTail($var));
	}

	/**
	 * Tests custom code generation
	 *
	 * @author amal
	 * @group unit
	 */
	public function testCustomHandlers()
	{
		$phpGen = $this->phpGen;

		$phpGen->addCustomHandler('DateTime', function($data) use ($phpGen) {
			/** @var $data \DateTime */
			return $phpGen->getCodeNoTail(
				$data->format("Y-m-d\TH:i:sO")
			);
		});

		$var = new \DateTime('2013-02-23 00:49:36', new \DateTimeZone('UTC'));
		$result = $phpGen->getCode($var);
		$evaled_result = eval("return $result");
		$this->assertFalse($evaled_result instanceof $var);
		$this->assertNotSame($var, $evaled_result);
		$this->assertNotEquals($var, $evaled_result);
		$this->assertSame(
			'"2013-02-23T00:49:36+0000";',
			$result
		);
	}
}


/**
 *
 */
class _ExampleCustomCode implements IPhpGenerable
{
	/**
	 * {@inheritdoc}
	 */
	public function generateCode()
	{
		return '32434544565768879789';
	}
}
