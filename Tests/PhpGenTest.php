<?php

namespace Aza\Components\PhpGen\Tests;
use Aza\Components\PhpGen\CustomCode;
use Aza\Components\PhpGen\IPhpGenerable;
use Aza\Components\PhpGen\PhpGen;
use PHPUnit_Framework_TestCase as TestCase;
use SplFixedArray;

/**
 * Testing PHP code generation
 *
 * @project Anizoptera CMF
 * @package system.phpgen
 * @author  Amal Samally <amal.samally at gmail.com>
 * @license MIT
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
	protected static $defaultPrecision;

	/**
	 * @var bool
	 */
	protected static $canUseShortSyntax;

	/**
	 * @var PhpGen
	 */
	protected $phpGen;


	/**
	 * {@inheritdoc}
	 */
	public static function setUpBeforeClass()
	{
		// Preparations
		self::$defaultPrecision  = ini_get('precision');
		self::$canUseShortSyntax = version_compare(PHP_VERSION, '5.4', '>=');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setUp()
	{
		// PhpGen instance with default settings
		$this->phpGen = new PhpGen();
		$this->phpGen->shortArraySyntax = true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function tearDown()
	{
		// Cleanup
		$this->phpGen = null;
		ini_set('precision', self::$defaultPrecision);
	}



	/**
	 * Tests PhpGen singleton
	 *
	 * @author amal
	 * @group unit
	 */
	public function testInstance()
	{
		$instance = PhpGen::instance();
		$this->assertNotSame($this->phpGen, $instance);
		$this->assertTrue($this->phpGen instanceof $instance);
		$this->assertSame(PhpGen::instance(), $instance);
	}

	/**
	 * Tests short array syntax support check
	 *
	 * @author amal
	 * @group unit
	 */
	public function testShortArraySyntaxCheck()
	{
		$phpGen = new PhpGen();
		$this->assertSame(
			self::$canUseShortSyntax,
			$phpGen->shortArraySyntax
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

		// ----
		$var = NAN;
		$result = $phpGen->getCode($var);
		$this->assertSame('NAN;', $result);

		// ----
		$var = acos(8);
		$result = $phpGen->getCode($var);
		$this->assertSame('NAN;', $result);

		// ----
		$var = INF;
		$result = $phpGen->getCode($var);
		$this->assertSame('INF;', $result);

		// ----
		$var = -INF;
		$result = $phpGen->getCode($var);
		$this->assertSame('-INF;', $result);

		// ----
		$var = log(0);
		$result = $phpGen->getCode($var);
		$this->assertSame('-INF;', $result);
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
			'"\n\t\r\x00\x01\x02\x03\x04\x05\x06\x07\x27\x22\x24\x76";',
			$result
		);

		$result = $phpGen->getCode($var4);
		$this->assertSame($var4, eval("return $result"));
		$this->assertSame(
			'"\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\v\f\r'
			.'\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19'
			.'\x1A\x1B\x1C\x1D\x1E\x1F\x20\x21\x22\x23\x24\x25'
			.'\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x30\x31'
			.'\x32\x33\x34\x35\x36\x37\x38\x39\x3A\x3B\x3C\x3D'
			.'\x3E\x3F\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49'
			.'\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55'
			.'\x56\x57\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F\x60\x61'
			.'\x62\x63\x64\x65\x66\x67\x68\x69\x6A\x6B\x6C\x6D'
			.'\x6E\x6F\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79'
			.'\x7A\x7B\x7C\x7D\x7E\x7F";',
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
			'"\n\t\r\x00\x01\x02\x03\x04\x05\x06\x07\x27\x22\x24\x76";',
			$result
		);

		$result = $phpGen->getCode($var4);
		$this->assertSame($var4, eval("return $result"));
		$this->assertSame(
			'"\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\v\f\r'
			.'\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19'
			.'\x1A\x1B\x1C\x1D\x1E\x1F\x20\x21\x22\x23\x24\x25'
			.'\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x30\x31'
			.'\x32\x33\x34\x35\x36\x37\x38\x39\x3A\x3B\x3C\x3D'
			.'\x3E\x3F\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49'
			.'\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55'
			.'\x56\x57\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F\x60\x61'
			.'\x62\x63\x64\x65\x66\x67\x68\x69\x6A\x6B\x6C\x6D'
			.'\x6E\x6F\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79'
			.'\x7A\x7B\x7C\x7D\x7E\x7F";',
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


		// ----
		$phpGen->binaryAutoCheck = false;

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
			'"\x00\x01\x02\x03\x04\x05\x06\x07\x08'
			.'\t\n\v\f\r'
			.'\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F'
			." !\\\"#\\$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\\x7F"
			.'";',
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
		$phpGen->binaryStrings = true;

		$result = $phpGen->getCode($var1);
		$this->assertSame($var1, eval("return $result"));
		$this->assertSame('"\x65\x78\x61\x6D\x70\x6C\x65";', $result);

		$result = $phpGen->getCode($var2);
		$this->assertSame($var2, eval("return $result"));
		$this->assertSame(
			'"\x65\x78\x61\x6D\x70\x6C\x65\x20\x24\x76\x61\x72";',
			$result
		);

		$result = $phpGen->getCode($var3);
		$this->assertSame($var3, eval("return $result"));
		$this->assertSame(
			'"\n\t\r\x00\x01\x02\x03\x04\x05\x06\x07\x27\x22\x24\x76";',
			$result
		);

		$result = $phpGen->getCode($var4);
		$this->assertSame($var4, eval("return $result"));
		$this->assertSame(
			'"\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\v\f\r'
			.'\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19'
			.'\x1A\x1B\x1C\x1D\x1E\x1F\x20\x21\x22\x23\x24\x25'
			.'\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x30\x31'
			.'\x32\x33\x34\x35\x36\x37\x38\x39\x3A\x3B\x3C\x3D'
			.'\x3E\x3F\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49'
			.'\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55'
			.'\x56\x57\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F\x60\x61'
			.'\x62\x63\x64\x65\x66\x67\x68\x69\x6A\x6B\x6C\x6D'
			.'\x6E\x6F\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79'
			.'\x7A\x7B\x7C\x7D\x7E\x7F";',
			$result
		);

		$result = $phpGen->getCode($var5);
		$this->assertSame($var5, eval("return $result"));
		$this->assertSame(
			'"\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\v\f\r'
			.'\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19'
			.'\x1A\x1B\x1C\x1D\x1E\x1F\x20\x21\x22\x23\x24\x25'
			.'\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x30\x31'
			.'\x32\x33\x34\x35\x36\x37\x38\x39\x3A\x3B\x3C\x3D'
			.'\x3E\x3F\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49'
			.'\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55'
			.'\x56\x57\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F\x60\x61'
			.'\x62\x63\x64\x65\x66\x67\x68\x69\x6A\x6B\x6C\x6D'
			.'\x6E\x6F\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79'
			.'\x7A\x7B\x7C\x7D\x7E\x7F\x80\x81\x82\x83\x84\x85'
			.'\x86\x87\x88\x89\x8A\x8B\x8C\x8D\x8E\x8F\x90\x91'
			.'\x92\x93\x94\x95\x96\x97\x98\x99\x9A\x9B\x9C\x9D'
			.'\x9E\x9F\xA0\xA1\xA2\xA3\xA4\xA5\xA6\xA7\xA8\xA9'
			.'\xAA\xAB\xAC\xAD\xAE\xAF\xB0\xB1\xB2\xB3\xB4\xB5'
			.'\xB6\xB7\xB8\xB9\xBA\xBB\xBC\xBD\xBE\xBF\xC0\xC1'
			.'\xC2\xC3\xC4\xC5\xC6\xC7\xC8\xC9\xCA\xCB\xCC\xCD'
			.'\xCE\xCF\xD0\xD1\xD2\xD3\xD4\xD5\xD6\xD7\xD8\xD9'
			.'\xDA\xDB\xDC\xDD\xDE\xDF\xE0\xE1\xE2\xE3\xE4\xE5'
			.'\xE6\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF0\xF1'
			.'\xF2\xF3\xF4\xF5\xF6\xF7\xF8\xF9\xFA\xFB\xFC\xFD'
			.'\xFE\xFF";',
			$result
		);

		$result = $phpGen->getCode($var6);
		$this->assertSame($var6, eval("return $result"));
		$this->assertSame(
			'"\x54\x68\x65\x20\x72\x65\x63\x6F\x6D\x6D\x65\x6E\x64\x65'
			.'\x64\x20\x77\x61\x79\x20\x74\x6F\x20\x69\x6E\x73\x74\x61'
			.'\x6C\x6C\x20\x41\x7A\x61\x50\x68\x70\x47\x65\x6E\x20\x69'
			.'\x73\x20\x5B\x74\x68\x72\x6F\x75\x67\x68\x20\x63\x6F\x6D'
			.'\x70\x6F\x73\x65\x72\x5D\x28\x68\x74\x74\x70\x3A\x2F\x2F'
			.'\x67\x65\x74\x63\x6F\x6D\x70\x6F\x73\x65\x72\x2E\x6F\x72'
			.'\x67\x29\x2E\n\x59\x6F\x75\x20\x63\x61\x6E\x20\x73\x65'
			.'\x65\x20\x5B\x70\x61\x63\x6B\x61\x67\x65\x20\x69\x6E\x66'
			.'\x6F\x72\x6D\x61\x74\x69\x6F\x6E\x20\x6F\x6E\x20\x50\x61'
			.'\x63\x6B\x61\x67\x69\x73\x74\x2E\x5D\x28\x68\x74\x74\x70'
			.'\x73\x3A\x2F\x2F\x70\x61\x63\x6B\x61\x67\x69\x73\x74\x2E'
			.'\x6F\x72\x67\x2F\x70\x61\x63\x6B\x61\x67\x65\x73\x2F\x61'
			.'\x7A\x61\x2F\x70\x68\x70\x67\x65\x6E\x29\n\n\x60\x60\x60'
			.'\x4A\x53\x4F\x4E\n\x7B\n\t\x22\x72\x65\x71\x75\x69\x72'
			.'\x65\x22\x3A\x20\x7B\n\t\t\x22\x61\x7A\x61\x2F\x70\x68'
			.'\x70\x67\x65\x6E\x22\x3A\x20\x22\x7E\x31\x2E\x30\x22\n\t'
			.'\x7D\n\x7D\n\x60\x60\x60";',
			$result
		);
	}


	/**
	 * Tests code generation for Resource type
	 *
	 * @author amal
	 * @group unit
	 */
	public function testResource()
	{
		$phpGen = $this->phpGen;

		$var = fopen('php://stderr', 'w');
		$this->assertTrue(is_resource($var));

		$result = $phpGen->getCode($var);
		$this->assertContains('Resource id #', $result);
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

		$var = new \DateTimeZone('UTC');
		$result = $phpGen->getCode($var);
		$evaled_result = eval("return $result");
		$this->assertTrue($evaled_result instanceof $var);
		$this->assertNotSame($var, $evaled_result);
		$this->assertEquals($var, $evaled_result);
		$this->assertSame(
			'unserialize("O:12:\"DateTimeZone\":0:{}");',
			$result
		);
	}


	/**
	 * Tests code generation for Array type
	 *
	 * @author amal
	 * @group unit
	 */
	public function testArray()
	{
		$phpGen = $this->phpGen;

		$canUseShortSyntax = self::$canUseShortSyntax;

		$var = array();
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame('[];', $result);

		$var = array(0, 1, 2, 3);
		$result = $phpGen->getCodeNoFormat($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame('[0,1,2,3];', $result);

		$var = array(0, 'a', false, true, null);
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	0,
	"a",
	false,
	true,
	null,
];',
			$result
		);

		$result = $phpGen->getCodeNoFormat($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame('[0,"a",false,true,null];', $result);

		$var = array('1' => 0, 1, 2, 3);
		$result = $phpGen->getCodeNoFormat($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame('[1=>0,2=>1,3=>2,4=>3];', $result);

		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	1 => 0,
	2 => 1,
	3 => 2,
	4 => 3,
];',
			$result
		);

		$result = $phpGen->getCodeNoTail($var, 3);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result;"));
		}
		$this->assertSame(
			'[
				1 => 0,
				2 => 1,
				3 => 2,
				4 => 3,
			]',
			$result
		);

		$var = array('abc' => 0, 1, 2, 3);
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"abc" => 0,
	0     => 1,
	1     => 2,
	2     => 3,
];',
			$result
		);

		$var = array('abc' => 0, 1, 2, 'abcdefg _-' => 3);
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"abc"        => 0,
	0            => 1,
	1            => 2,
	"abcdefg _-" => 3,
];',
			$result
		);

		$var = array('abc' => 0, 1, array());
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"abc" => 0,
	0     => 1,
	1     => [],
];',
			$result
		);

		$var = array(null => array(array(array())));
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"" => [[[]]],
];',
			$result
		);

		$var = array('z' => array(1, array(array())));
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"z" => [
		1,
		[[]],
	],
];',
			$result
		);

		$var = array(array());
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[[]];',
			$result
		);

		$var = array('sdfqergeger45hy4h5wg4w');
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'["sdfqergeger45hy4h5wg4w"];',
			$result
		);

		$var = array('sdfqergeger45hy4h5wg4wsdfqergeger45hy4h5wg4wsdfqergeger45hy4h5wg4w');
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"sdfqergeger45hy4h5wg4wsdfqergeger45hy4h5wg4wsdfqergeger45hy4h5wg4w",
];',
			$result
		);

		$var = array("sdfqergeg\ner45hy4h5wg4w");
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"sdfqergeg
er45hy4h5wg4w",
];',
			$result
		);

		$var = array(
			"URL_ALL"       => "http://example.com/",
			"DOMAIN_ALL"    => "example.com",
			"DOMAIN_STATIC" => "assets.example.com",
			"DIR_LOAD"      => "/apps/example/application/load/public/",
		);
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"URL_ALL"       => "http://example.com/",
	"DOMAIN_ALL"    => "example.com",
	"DOMAIN_STATIC" => "assets.example.com",
	"DIR_LOAD"      => "/apps/example/application/load/public/",
];',
			$result
		);

		$var = array(
			""           => "1",
			"1"          => "1",
			"12"         => "1",
			"123"        => "1",
			"1234"       => "1",
			"12345"      => "1",
			"123456"     => "1",
			"1234567"    => "1",
			"12345678"   => "1",
			"123456789"  => "1",
			"1234567890" => "1",
		);
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	""         => "1",
	1          => "1",
	12         => "1",
	123        => "1",
	1234       => "1",
	12345      => "1",
	123456     => "1",
	1234567    => "1",
	12345678   => "1",
	123456789  => "1",
	1234567890 => "1",
];',
			$result
		);

		$var = array(
			"DOMAIN_ALL"     => "example.com",
			"DOMAIN_STATIC_" => "assets.example.com",
			"123"            => array(1, 2),
			"URL_ALL"        => "http://example.com/",
			"DIR_LOAD"       => "/apps/example/application/load/public/",
			"456"            => array(1, 2),
			1                => 1,
			2                => 1,
		);
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"DOMAIN_ALL"     => "example.com",
	"DOMAIN_STATIC_" => "assets.example.com",
	123              => [
		1,
		2,
	],
	"URL_ALL"  => "http://example.com/",
	"DIR_LOAD" => "/apps/example/application/load/public/",
	456        => [
		1,
		2,
	],
	1 => 1,
	2 => 1,
];',
			$result
		);

		$var = array(
			"DOMAIN_STATIC_" => array(1, 2),
			"DOMAIN"         => array(1, 2),
			1                => array(1, 2),
		);
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"DOMAIN_STATIC_" => [
		1,
		2,
	],
	"DOMAIN" => [
		1,
		2,
	],
	1 => [
		1,
		2,
	],
];',
			$result
		);

		$var = array(
			"DOMAIN_ALL"     => "example.com",
			2                => 1,
		);
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"DOMAIN_ALL" => "example.com",
	2            => 1,
];',
			$result
		);

		$var = array(
			"DOMAIN_ALL"     => "example.com",
			"az"             => "example\nexample",
			2                => 1,
		);
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"DOMAIN_ALL" => "example.com",
	"az"         => "example
example",
	2 => 1,
];',
			$result
		);

		$phpGen->oneLineStrings = true;
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"DOMAIN_ALL" => "example.com",
	"az"         => "example\nexample",
	2            => 1,
];',
			$result
		);
	}

	/**
	 * Tests code generation for SPL Traversable
	 *
	 * @author amal
	 * @group unit
	 */
	public function testTraversable()
	{
		$phpGen = $this->phpGen;

		$var = new SplFixedArray(3);
		$result = $phpGen->getCodeNoFormat($var);
		$this->assertSame('[null,null,null];', $result);
		$var[0] = 'a';
		$var[1] = 'b';
		$result = $phpGen->getCodeNoFormat($var);
		$this->assertSame('["a","b",null];', $result);
	}

	/**
	 * Tests code generation for Arrays with outputSerialKeys option enabled
	 *
	 * @author amal
	 * @group unit
	 */
	public function testArray_OtputSerialKeys()
	{
		$phpGen = $this->phpGen;
		$phpGen->outputSerialKeys = true;

		$canUseShortSyntax = self::$canUseShortSyntax;

		$var = array(0, 1, 2, 3);
		$result = $phpGen->getCodeNoFormat($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame('[0=>0,1=>1,2=>2,3=>3];', $result);

		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	0 => 0,
	1 => 1,
	2 => 2,
	3 => 3,
];',
			$result
		);

		$var = array('sdfqergeger45hy4h5wg4w');
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	0 => "sdfqergeger45hy4h5wg4w",
];',
			$result
		);
	}

	/**
	 * Tests code generation for Arrays with useSpaces option enabled
	 *
	 * @author amal
	 * @group unit
	 */
	public function testArray_UseSpaces()
	{
		$phpGen = $this->phpGen;
		$phpGen->useSpaces = true;

		$canUseShortSyntax = self::$canUseShortSyntax;

		$var = array(
			"DOMAIN_ALL"    => "example.com",
			"DOMAIN_STATIC" => "assets.example.com",
			"URL_ALL"       => "http://example.com/",
			"DIR_LOAD"      => "/apps/example/application/load/public/",
		);

		$result = $phpGen->getCode($var, 1);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
        "DOMAIN_ALL"    => "example.com",
        "DOMAIN_STATIC" => "assets.example.com",
        "URL_ALL"       => "http://example.com/",
        "DIR_LOAD"      => "/apps/example/application/load/public/",
    ];',
			$result
		);

		$phpGen->tabLength = 8;
		$result = $phpGen->getCode($var, 2);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
                        "DOMAIN_ALL"    => "example.com",
                        "DOMAIN_STATIC" => "assets.example.com",
                        "URL_ALL"       => "http://example.com/",
                        "DIR_LOAD"      => "/apps/example/application/load/public/",
                ];',
			$result
		);

		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
        "DOMAIN_ALL"    => "example.com",
        "DOMAIN_STATIC" => "assets.example.com",
        "URL_ALL"       => "http://example.com/",
        "DIR_LOAD"      => "/apps/example/application/load/public/",
];',
			$result
		);
	}

	/**
	 * Tests code generation for Arrays with spacesAfterKey option disabled
	 *
	 * @author amal
	 * @group unit
	 */
	public function testArray_TabsAfterKey()
	{
		$phpGen = $this->phpGen;
		$phpGen->spacesAfterKey = false;
		$phpGen->mixSpaces      = false;

		$canUseShortSyntax = self::$canUseShortSyntax;


		$var1 = array(
			"URL_ALL"       => "http://example.com/",
			"DOMAIN_ALL"    => "example.com",
			"DOMAIN_STATIC" => "assets.example.com",
			"DIR_LOAD"      => "/apps/example/application/load/public/",
		);
		$result = $phpGen->getCode($var1);
		if ($canUseShortSyntax) {
			$this->assertSame($var1, eval("return $result"));
		}
		$this->assertSame(
			'[
	"URL_ALL"		=> "http://example.com/",
	"DOMAIN_ALL"	=> "example.com",
	"DOMAIN_STATIC"	=> "assets.example.com",
	"DIR_LOAD"		=> "/apps/example/application/load/public/",
];',
			$result
		);

		$var2 = array(
			""           => "1",
			"1"          => "1",
			"12"         => "1",
			"123"        => "1",
			"1234"       => "1",
			"12345"      => "1",
			"123456"     => "1",
			"1234567"    => "1",
			"12345678"   => "1",
			"123456789"  => "1",
			"1234567890" => "1",
		);
		$result = $phpGen->getCode($var2);
		if ($canUseShortSyntax) {
			$this->assertSame($var2, eval("return $result"));
		}
		$this->assertSame(
			'[
	""			=> "1",
	1			=> "1",
	12			=> "1",
	123			=> "1",
	1234		=> "1",
	12345		=> "1",
	123456		=> "1",
	1234567		=> "1",
	12345678	=> "1",
	123456789	=> "1",
	1234567890	=> "1",
];',
			$result
		);


		$phpGen->mixSpaces = true;

		$result = $phpGen->getCode($var1);
		if ($canUseShortSyntax) {
			$this->assertSame($var1, eval("return $result"));
		}
		$this->assertSame(
			'[
	"URL_ALL"		=> "http://example.com/",
	"DOMAIN_ALL"	=> "example.com",
	"DOMAIN_STATIC"	=> "assets.example.com",
	"DIR_LOAD"		=> "/apps/example/application/load/public/",
];',
			$result
		);

		$result = $phpGen->getCode($var2);
		if ($canUseShortSyntax) {
			$this->assertSame($var2, eval("return $result"));
		}
		$this->assertSame(
			'[
	""		   => "1",
	1		   => "1",
	12		   => "1",
	123		   => "1",
	1234	   => "1",
	12345	   => "1",
	123456	   => "1",
	1234567	   => "1",
	12345678   => "1",
	123456789  => "1",
	1234567890 => "1",
];',
			$result
		);

		$var = array(
			"1"    => "1",
			"12"   => "1",
			"123"  => "1",
			"1234" => "1",
		);
		$result = $phpGen->getCode($var);
		$this->assertSame(
			'[
	1	 => "1",
	12	 => "1",
	123	 => "1",
	1234 => "1",
];',
			$result
		);

		$var = array(
			"1"     => "1",
			"12"    => "1",
			"123"   => "1",
			"1234"  => "1",
			"12345" => "1",
		);
		$result = $phpGen->getCode($var);
		$this->assertSame(
			'[
	1	  => "1",
	12	  => "1",
	123	  => "1",
	1234  => "1",
	12345 => "1",
];',
			$result
		);

		$var = array(
			"11"     => "1",
			"112"    => "1",
			"1123"   => "1",
			"11234"  => "1",
			"112345" => "1",
		);
		$result = $phpGen->getCode($var);
		$this->assertSame(
			'[
	11	   => "1",
	112	   => "1",
	1123   => "1",
	11234  => "1",
	112345 => "1",
];',
			$result
		);

		$var = array(
			"111"     => "1",
			"1112"    => "1",
			"11123"   => "1",
			"111234"  => "1",
			"1112345" => "1",
		);
		$result = $phpGen->getCode($var);
		$this->assertSame(
			'[
	111		=> "1",
	1112	=> "1",
	11123	=> "1",
	111234	=> "1",
	1112345	=> "1",
];',
			$result
		);

		$var = array(
			" 1"     => "1",
			" 12"    => "1",
			" 123"   => "1",
			" 1234"  => "1",
			" 12345" => "1",
		);
		$result = $phpGen->getCode($var);
		$this->assertSame(
			'[
	" 1"	 => "1",
	" 12"	 => "1",
	" 123"	 => "1",
	" 1234"	 => "1",
	" 12345" => "1",
];',
			$result
		);
	}

	/**
	 * Tests code generation for Arrays with shortArraySyntax option disabled
	 *
	 * @author amal
	 * @group unit
	 */
	public function testArray_ShortSyntax()
	{
		$phpGen = $this->phpGen;
		$phpGen->shortArraySyntax = false;

		$var = array();
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame('array();', $result);

		$var = array(0, 1, 2, 3);
		$result = $phpGen->getCodeNoFormat($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame('array(0,1,2,3);', $result);

		$var = array(0, 'a', false, true, null);
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(
	0,
	"a",
	false,
	true,
	null,
);',
			$result
		);

		$result = $phpGen->getCodeNoFormat($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame('array(0,"a",false,true,null);', $result);

		$var = array('1' => 0, 1, 2, 3);
		$result = $phpGen->getCodeNoFormat($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame('array(1=>0,2=>1,3=>2,4=>3);', $result);

		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(
	1 => 0,
	2 => 1,
	3 => 2,
	4 => 3,
);',
			$result
		);

		$result = $phpGen->getCodeNoTail($var, 3);
		$this->assertSame($var, eval("return $result;"));
		$this->assertSame(
			'array(
				1 => 0,
				2 => 1,
				3 => 2,
				4 => 3,
			)',
			$result
		);

		$var = array('abc' => 0, 1, 2, 3);
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(
	"abc" => 0,
	0     => 1,
	1     => 2,
	2     => 3,
);',
			$result
		);

		$var = array('abc' => 0, 1, 2, 'abcdefg _-' => 3);
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(
	"abc"        => 0,
	0            => 1,
	1            => 2,
	"abcdefg _-" => 3,
);',
			$result
		);

		$var = array('abc' => 0, 1, array());
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(
	"abc" => 0,
	0     => 1,
	1     => array(),
);',
			$result
		);

		$var = array(null => array(array(array())));
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(
	"" => array(array(array())),
);',
			$result
		);

		$var = array('z' => array(1, array(array())));
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(
	"z" => array(
		1,
		array(array()),
	),
);',
			$result
		);

		$var = array(array());
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(array());',
			$result
		);

		$var = array('sdfqergeger45hy4h5wg4w');
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array("sdfqergeger45hy4h5wg4w");',
			$result
		);

		$var = array('sdfqergeger45hy4h5wg4wsdfqergeger45hy4h5wg4wsdfqergeger45hy4h5wg4w');
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(
	"sdfqergeger45hy4h5wg4wsdfqergeger45hy4h5wg4wsdfqergeger45hy4h5wg4w",
);',
			$result
		);

		$var = array("sdfqergeg\ner45hy4h5wg4w");
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(
	"sdfqergeg
er45hy4h5wg4w",
);',
			$result
		);

		$var = array(
			"URL_ALL"       => "http://example.com/",
			"DOMAIN_ALL"    => "example.com",
			"DOMAIN_STATIC" => "assets.example.com",
			"DIR_LOAD"      => "/apps/example/application/load/public/",
		);
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(
	"URL_ALL"       => "http://example.com/",
	"DOMAIN_ALL"    => "example.com",
	"DOMAIN_STATIC" => "assets.example.com",
	"DIR_LOAD"      => "/apps/example/application/load/public/",
);',
			$result
		);

		$var = array(
			""           => "1",
			"1"          => "1",
			"12"         => "1",
			"123"        => "1",
			"1234"       => "1",
			"12345"      => "1",
			"123456"     => "1",
			"1234567"    => "1",
			"12345678"   => "1",
			"123456789"  => "1",
			"1234567890" => "1",
		);
		$result = $phpGen->getCode($var);
		$this->assertSame($var, eval("return $result"));
		$this->assertSame(
			'array(
	""         => "1",
	1          => "1",
	12         => "1",
	123        => "1",
	1234       => "1",
	12345      => "1",
	123456     => "1",
	1234567    => "1",
	12345678   => "1",
	123456789  => "1",
	1234567890 => "1",
);',
			$result
		);
	}

	/**
	 * Tests code generation for Arrays with alignMultilineBreaks option disabled
	 *
	 * @author amal
	 * @group unit
	 */
	public function testArray_NotAlignMultilineBreaks()
	{
		$phpGen = $this->phpGen;
		$phpGen->alignMultilineBreaks = false;

		$canUseShortSyntax = self::$canUseShortSyntax;

		$var = array(
			"DOMAIN_ALL"     => "example.com",
			"DOMAIN_STATIC_" => "assets.example.com",
			"123"            => array(1, 2),
			"URL_ALL"        => "http://example.com/",
			"DIR_LOAD"       => "/apps/example/application/load/public/",
			"456"            => array(1, 2),
			1                => 1,
			2                => 1,
		);

		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"DOMAIN_ALL"     => "example.com",
	"DOMAIN_STATIC_" => "assets.example.com",
	123              => [
		1,
		2,
	],
	"URL_ALL"        => "http://example.com/",
	"DIR_LOAD"       => "/apps/example/application/load/public/",
	456              => [
		1,
		2,
	],
	1                => 1,
	2                => 1,
];',
			$result
		);

		$var1 = array(
			"DOMAIN_ALL"     => "example.com",
			"az"             => "example\nexample",
			2                => 1,
		);
		$result = $phpGen->getCode($var1);
		if ($canUseShortSyntax) {
			$this->assertSame($var1, eval("return $result"));
		}
		$this->assertSame(
			'[
	"DOMAIN_ALL" => "example.com",
	"az"         => "example
example",
	2            => 1,
];',
			$result
		);

		$phpGen->spacesAfterKey = false;
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"DOMAIN_ALL"	 => "example.com",
	"DOMAIN_STATIC_" => "assets.example.com",
	123				 => [
		1,
		2,
	],
	"URL_ALL"		 => "http://example.com/",
	"DIR_LOAD"		 => "/apps/example/application/load/public/",
	456				 => [
		1,
		2,
	],
	1				 => 1,
	2				 => 1,
];',
			$result
		);

		$phpGen->mixSpaces = false;
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"DOMAIN_ALL"		=> "example.com",
	"DOMAIN_STATIC_"	=> "assets.example.com",
	123					=> [
		1,
		2,
	],
	"URL_ALL"			=> "http://example.com/",
	"DIR_LOAD"			=> "/apps/example/application/load/public/",
	456					=> [
		1,
		2,
	],
	1					=> 1,
	2					=> 1,
];',
			$result
		);
	}

	/**
	 * Some complex tests for code generation for Array type
	 *
	 * @author amal
	 * @group unit
	 */
	public function testArray_Complex()
	{
		$phpGen = $this->phpGen;

		$canUseShortSyntax = self::$canUseShortSyntax;

		$var = array(
			"DOMAIN_ALL"    => "example.com",
			"DOMAIN_STATIC" => "assets.example.com",
			"DOMAIN_LOAD"   => array(),
			"URL_ALL"       => "http://example.com/",
			"URL_STATIC"    => "http://assets.example.com/",
			"URL_LOAD"      => array(12),
			"DIR_STATIC"    => "/apps/example/application/static/public/",
			"DIR_LOAD"      => "/apps/example/application/load/public/",
			12              => array("asdbel", 245),
			"DIR_STATIC1"   => "/apps/example/application/static/public/",
			"DIR_LOAD1"     => "/apps/example/application/load/public/",
			array("asdfkjeeiufghq34t9heifhewijhqioufhewoiuhgqoiwe"),
			"/apps/example/application/static/public/",
			"/apps/example/application/load/public/",
			array("asdfkjeeiufghq34t9heifhewijhqioufhewoiuhgqoiweuhfwo4hrsdfbw65hesrgbharsg34rgeWFDdfg5"),
			"/apps/example/application/static/public/",
			"/apps/example/application/load/public/",
		);

		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"DOMAIN_ALL"    => "example.com",
	"DOMAIN_STATIC" => "assets.example.com",
	"DOMAIN_LOAD"   => [],
	"URL_ALL"       => "http://example.com/",
	"URL_STATIC"    => "http://assets.example.com/",
	"URL_LOAD"      => [12],
	"DIR_STATIC"    => "/apps/example/application/static/public/",
	"DIR_LOAD"      => "/apps/example/application/load/public/",
	12              => [
		"asdbel",
		245,
	],
	"DIR_STATIC1" => "/apps/example/application/static/public/",
	"DIR_LOAD1"   => "/apps/example/application/load/public/",
	13            => ["asdfkjeeiufghq34t9heifhewijhqioufhewoiuhgqoiwe"],
	14            => "/apps/example/application/static/public/",
	15            => "/apps/example/application/load/public/",
	16            => [
		"asdfkjeeiufghq34t9heifhewijhqioufhewoiuhgqoiweuhfwo4hrsdfbw65hesrgbharsg34rgeWFDdfg5",
	],
	17 => "/apps/example/application/static/public/",
	18 => "/apps/example/application/load/public/",
];',
			$result
		);

		$result = $phpGen->getCode($var, 3);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
				"DOMAIN_ALL"    => "example.com",
				"DOMAIN_STATIC" => "assets.example.com",
				"DOMAIN_LOAD"   => [],
				"URL_ALL"       => "http://example.com/",
				"URL_STATIC"    => "http://assets.example.com/",
				"URL_LOAD"      => [12],
				"DIR_STATIC"    => "/apps/example/application/static/public/",
				"DIR_LOAD"      => "/apps/example/application/load/public/",
				12              => [
					"asdbel",
					245,
				],
				"DIR_STATIC1" => "/apps/example/application/static/public/",
				"DIR_LOAD1"   => "/apps/example/application/load/public/",
				13            => [
					"asdfkjeeiufghq34t9heifhewijhqioufhewoiuhgqoiwe",
				],
				14 => "/apps/example/application/static/public/",
				15 => "/apps/example/application/load/public/",
				16 => [
					"asdfkjeeiufghq34t9heifhewijhqioufhewoiuhgqoiweuhfwo4hrsdfbw65hesrgbharsg34rgeWFDdfg5",
				],
				17 => "/apps/example/application/static/public/",
				18 => "/apps/example/application/load/public/",
			];',
			$result
		);

		$var["DOMAIN_STATIC__"] = array(array(array()));
		$result = $phpGen->getCode($var);
		if ($canUseShortSyntax) {
			$this->assertSame($var, eval("return $result"));
		}
		$this->assertSame(
			'[
	"DOMAIN_ALL"    => "example.com",
	"DOMAIN_STATIC" => "assets.example.com",
	"DOMAIN_LOAD"   => [],
	"URL_ALL"       => "http://example.com/",
	"URL_STATIC"    => "http://assets.example.com/",
	"URL_LOAD"      => [12],
	"DIR_STATIC"    => "/apps/example/application/static/public/",
	"DIR_LOAD"      => "/apps/example/application/load/public/",
	12              => [
		"asdbel",
		245,
	],
	"DIR_STATIC1" => "/apps/example/application/static/public/",
	"DIR_LOAD1"   => "/apps/example/application/load/public/",
	13            => ["asdfkjeeiufghq34t9heifhewijhqioufhewoiuhgqoiwe"],
	14            => "/apps/example/application/static/public/",
	15            => "/apps/example/application/load/public/",
	16            => [
		"asdfkjeeiufghq34t9heifhewijhqioufhewoiuhgqoiweuhfwo4hrsdfbw65hesrgbharsg34rgeWFDdfg5",
	],
	17                => "/apps/example/application/static/public/",
	18                => "/apps/example/application/load/public/",
	"DOMAIN_STATIC__" => [[[]]],
];',
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
