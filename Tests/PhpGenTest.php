<?php

namespace Aza\Components\PhpGen\Tests;
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

		// ----
		$var = -123;
		$result = $phpGen->getCode($var);
		$this->assertSame('-123;', $result);

		// ----
		$var = 0x123;
		$result = $phpGen->getCode($var);
		$this->assertSame('291;', $result);

		// ----
		$var = 0123;
		$result = $phpGen->getCode($var);
		$this->assertSame('83;', $result);

		// ----
		$var = 9999999999;
		$result = $phpGen->getCode($var);
		$this->assertSame('9999999999;', $result);
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
}
