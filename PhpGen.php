<?php

namespace Aza\Components\PhpGen;
use ReflectionFunction;
use SplFileObject;
use Traversable;

/**
 * PHP code generation
 *
 * @uses reflection
 * @uses spl
 *
 * @project Anizoptera CMF
 * @package system.phpgen
 */
class PhpGen
{
	/**
	 * Length of one tab in spaces
	 */
	public $tabLength = 4;

	/**
	 * Use spaces instead of tabs for indentation
	 */
	public $useSpaces = false;

	/**
	 * Mix tabs with spaces in the end of indentation
	 */
	public $mixSpaces = true;

	/**
	 * Mix tabs with spaces in the end of indentation
	 */
	public $spacesAfterKey = true;

	/**
	 * Output string variables as one line.
	 * Converts \n and \t symbols to escaped characters.
	 *
	 * By default only other control and non-visible
	 * chars are escaped.
	 */
	public $oneLineStrings = false;

	/**
	 * Output array keys for serial arrays.
	 */
	public $outputSerialKeys = false;

	/**
	 * Use php 5.4 short array syntax
	 */
	public $shortArraySyntax = false;

	/**
	 * Array of different custom type handlers.
	 *
	 * @var array[]
	 */
	protected $customHandlers = array();


	// TODO: В массивах если значение-массив разрывает список, то можно отступы до и после него рассчитывать отдельно
	// TODO: Cover with tests
	// TODO: Basic class CustomCode implementing IPhpGenerable


	/**
	 * Returns singleton instance of the class
	 *
	 * @return self
	 */
	public static function instance()
	{
		static $instance;
		return $instance ?: $instance = new self;
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->shortArraySyntax = version_compare(PHP_VERSION, '5.4.0', '>=');
	}


	/**
	 * Adds custom handler for the specified object type
	 *
	 * @param string|object $type <p>
	 * Object type for instanceof check
	 * </p>
	 * @param callable $handler <p>
	 * Callback "string fun($object)"
	 * </p>
	 */
	public function addCustomHandler($type, $handler)
	{
		$this->customHandlers[] = [$type, $handler];
	}


	/**
	 * Generation of php code for various data without formatting.
	 *
	 * WARNING!
	 * Don't use for self referencing arrays and objects!
	 *
	 * @see getCode
	 *
	 * @param mixed $data   Data
	 * @param bool  $noTail Don't add trailing ";"
	 *
	 * @return string
	 */
	public function getCodeNoFormat($data, $noTail = false)
	{
		return $this->getCode($data, 0, true, $noTail);
	}

	/**
	 * Generation of php code for various data without trailing semicolon (;).
	 *
	 * WARNING!
	 * Don't use for self referencing arrays and objects!
	 *
	 * @see getCode
	 *
	 * @param mixed $data     Data
	 * @param int   $indent   Indent size in tabs for array php code
	 * @param bool  $noFormat No formatting and indention
	 *
	 * @return string
	 */
	public function getCodeNoTail($data, $indent = 0, $noFormat = false)
	{
		return $this->getCode($data, $indent, $noFormat, true);
	}

	/**
	 * Generation of php code for various data.
	 *
	 * WARNING!
	 * Don't use for self referencing arrays and objects!
	 *
	 * @param mixed $data     Data
	 * @param int   $indent   Indent size in tabs for array php code
	 * @param bool  $noFormat No formatting and indention
	 * @param bool  $noTail   Don't add trailing semicolon (;)
	 *
	 * @return string
	 */
	public function getCode($data, $indent = 0, $noFormat = false, $noTail = false)
	{
		$tail = $noTail ? '' : ';';

		// Null
		if (!isset($data)) {
			// var_export returns uppercased, so use own variant
			return 'null' . $tail;
		}
		// Bool / Int / Float
		else if (is_bool($data) || is_int($data) || is_float($data)) {
			return var_export($data, true) . $tail;
		}
		// Array
		else if (($traversable = ($data instanceof \Traversable)) || is_array($data)) {
			if ($traversable) {
				$data = iterator_to_array($data, true);
			}
			return $this->getArray($data, $indent, $noFormat) . $tail;
		}
		// Object
		else if (is_object($data)) {
			return $this->getObject($data, $indent, $noFormat) . $tail;
		}
		// String
		// http://php.net/language.types.string#language.types.string.syntax.double
		$regexp = $this->oneLineStrings
				? '\x00-\x1F'
				: '\x00-\x08\x0B-\x1F';
		$data = preg_replace_callback(
			'~['.$regexp.'\x22\x24\x5C\x7F]~SX',
			function($char) {
				// linefeed (LF or 0x0A (10) in ASCII)
				if ("\n" === ($char = $char[0])) {
					return '\n';
				}
				// carriage return (CR or 0x0D (13) in ASCII)
				else if ("\r" === $char) {
					return '\r';
				}
				// horizontal tab (HT or 0x09 (9) in ASCII)
				else if ("\t" === $char) {
					return '\t';
				}
				// vertical tab (VT or 0x0B (11) in ASCII) (since PHP 5.2.5)
				else if ("\v" === $char) {
					return '\v';
				}
				// escape (ESC or 0x1B (27) in ASCII) (since PHP 5.4.0)
//					else if ("\e" === $char) {
//						return '\e';
//					}
				// form feed (FF or 0x0C (12) in ASCII) (since PHP 5.2.5)
				else if ("\f" === $char) {
					return '\f';
				}
				// chars that must be escaped in a double-quoted string
				else if ('\\' === $char || '$' === $char || '"' === $char) {
					return "\\$char";
				}
				// all other chars
				return sprintf('\x%02X', ord($char));
			},
			(string)$data
		);
		return '"' . $data . '"' . $tail;
	}


	/**
	 * Returns php code for object
	 *
	 * @see IPhpGenerable
	 * @see CustomCode
	 *
	 * @param object|IPhpGenerable $object <p>
	 * Object data
	 * </p>
	 *
	 * @return string
	 */
	protected function getObject($object)
	{
		// User custom code
		if ($object instanceof IPhpGenerable) {
			return $object->generateCode();
		}
		// Closures special (partial) support
		// WARNING: many closures on one line are not supported
		// WARNING: closures with "use" are not supported
		else if ($object instanceof \Closure) {
			$ref = new ReflectionFunction($object);

			// Open file and seek to the first line of the closure
			$file = new SplFileObject($ref->getFileName());
			$file->seek($ref->getStartLine()-1);

			// Retrieve all of the lines that contain code for the closure
			$endLine = $ref->getEndLine();
			$code    = '';
			while ($file->key() < $endLine) {
				$code .= $file->current();
				$file->next();
			}

			// Only keep the code defining that closure
			$begin = stripos($code, 'function');
			$end   = strrpos($code, '}');
			$code  = substr($code, $begin, $end - $begin + 1);

			return $code;
		}
		// Different custom handlers
		else if ($handlers = $this->customHandlers) {
			foreach ($handlers as $h) {
				list($type, $handler) = $h;
				if ($object instanceof $type) {
					return $handler($object);
				}
			}
		}
		// Default - serialization
		return "unserialize({$this->getCodeNoTail(serialize($object))})";
	}

	/**
	 * Returns php code for array
	 *
	 * @param array|Traversable $array <p>
	 * Array data
	 * </p>
	 * @param int $indent [optional] <p>
	 * Indent size in tabs for array php code
	 * </p>
	 * @param bool $noFormat [optional] <p>
	 * No formatting and indention
	 * </p>
	 *
	 * @return string
	 */
	protected function getArray($array, $indent = 0, $noFormat = false)
	{
		$string = $this->shortArraySyntax ? '[' : 'array(';

		if ($array) {
			$tabLength    = (int)$this->tabLength;
			$mixSpaces    = (bool)$this->mixSpaces;
			$useSpaces    = $this->useSpaces;
			$tab          = $useSpaces ? str_repeat(' ', $tabLength) : "\t";
			$spacePostfix = $useSpaces || $this->spacesAfterKey;


			// The overall indent
			$indentString = $noFormat ? '' : str_repeat($tab, $indent);

			$maxKeyLength   = 0;
			$arrayCodeParts = [];
			$arrayIsSimple  = !$this->outputSerialKeys;
			$i = 0;
			foreach ($array as $key => $val) {
				if ($arrayIsSimple && $key !== $i++) {
					$arrayIsSimple = false;
				}
				$key        = $this->getCode($key, 0, true, true);
				$val        = $this->getCode($val, $indent+1, $noFormat, true);
				$valIsArray = is_array($val) && $val;
				$keyLength  = 0;
				if (!$noFormat) {
					$keyLength = mb_strlen($key, 'UTF-8');
					$keyLength > $maxKeyLength
						&& $maxKeyLength = $keyLength;
				}
				$arrayCodeParts[] = array(
					$key,
					$val,
					$keyLength,
					$valIsArray
				);
			}
			unset($array, $key, $val);

			foreach ($arrayCodeParts as &$data) {
				list($key, $val, $keyLength, $valIsArray) = $data;
				if (!$noFormat) {
					if ($valIsArray) {
						$key .= ' ';
					} else {
						$indentLength = $maxKeyLength - $keyLength + 1;
						if ($spacePostfix) {
							$key .= str_repeat(' ', $indentLength);
						} else {
							$curTabTail = (($indent+1) * $tabLength + $keyLength) % $tabLength;
							if ($mixSpaces) {
								if ($indentLength < ($curTabTail ?: $tabLength)) {
									$key .= str_repeat(' ', $indentLength);
								} else {
									if ($curTabTail && $indentLength >= $curTabTail) {
										$key .= $tab;
										$indentLength -= $curTabTail;
									}
									if ($indentLength > 0) {
										$indentTail = $indentLength % $tabLength;
										$indentLength -= $indentTail;
										if ($indentLength) {
											$key .= str_repeat($tab, $indentLength);
										}
										if ($indentTail) {
											$key .= str_repeat(' ', $indentTail);
										}
									}
								}
							} else {
								if ($curTabTail) {
									$key .= $tab;
									$indentLength -= $curTabTail;
								}
								if ($indentLength > 0) {
									$key .= str_repeat($tab, ceil($indentLength/$tabLength));
								}
							}
						}
					}
				}
				$data = ($arrayIsSimple
							? ''
							: $key . '=>' . ($noFormat ? '' : ' ')
						) . $val . ',';
			}

			if ($noFormat) {
				$code = join('', $arrayCodeParts);
				$code = substr($code, 0, -1);
			} else {
				$code = "\n{$indentString}{$tab}"
						. join("\n{$indentString}{$tab}", $arrayCodeParts)
						. "\n{$indentString}";
			}

			$string .= $code;
		}

		$string .= $this->shortArraySyntax ? ']' : ')';

		return $string;
	}
}
