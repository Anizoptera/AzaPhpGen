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
 * @author  Amal Samally <amal.samally at gmail.com>
 * @license MIT
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
	 * If array value breaks the list (multiline),
	 * alignment before and after calculated separately
	 */
	public $alignMultilineBreaks = true;

	/**
	 * Approximate maximum line length
	 */
	public $maxLineLength = 60;


	/**
	 * Array of different custom type handlers.
	 *
	 * @var array[]
	 */
	protected $customHandlers = array();



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
		$this->shortArraySyntax = version_compare(PHP_VERSION, '5.4', '>=');
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
		$this->customHandlers[] = array($type, $handler);
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
		else if (($traversable = ($data instanceof Traversable)) || is_array($data)) {
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
		$shortSyntax = $this->shortArraySyntax;
		$resultCode  = $shortSyntax ? '[' : 'array(';

		if ($array) {
			$newLine      = "\n";
			$tabLength    = $this->tabLength;
			$mixSpaces    = $this->mixSpaces;
			$useSpaces    = $this->useSpaces;
			$tab          = $useSpaces ? str_repeat(' ', $tabLength) : "\t";
			$spacePostfix = $useSpaces || $this->spacesAfterKey;
			$alignBreaks  = $this->alignMultilineBreaks;

			// The overall indent
			$indentString = $noFormat ? '' : str_repeat($tab, $indent);

			// First calculations and code build for keys/values
			$keyLength = $keyLengthBlock = $i = 0;
			$maxKeyLenBlocks = array($keyLengthBlock => 0);
			$maxKeyLength    = &$maxKeyLenBlocks[$keyLengthBlock];
			$arrayIsSimple   = !$this->outputSerialKeys;
			$multiline       = false;
			$arrayParts      = array();
			foreach ($array as $key => $val) {
				if ($arrayIsSimple && $key !== $i++) {
					$arrayIsSimple = false;
				}

				// Build code for keys and values
				$key = $this->getCode($key, 0, true, true);
				$val = $this->getCode($val, $indent+1, $noFormat, true);

				// We don't need this information if formatting is disabled
				if (!$noFormat) {
					// We need to save maximum key code length for alignment
					$keyLength = mb_strlen($key, 'UTF-8');
					$keyLength > $maxKeyLength
						&& $maxKeyLength = $keyLength;

					// Multiline value breaks the list, so
					// alignment before and after calculated separately
					if ($multiline = $alignBreaks
							? false !== strpos($val, $newLine)
							: false
					) {
						$maxKeyLenBlocks[++$keyLengthBlock] = 0;
						$maxKeyLength = &$maxKeyLenBlocks[$keyLengthBlock];
					}
				}

				$arrayParts[] = array(
					$key,
					$val,
					$keyLength,
					$multiline
				);
			}
			unset($array, $maxKeyLength);

			// Disable formatting if array have only one serial value
			// (not multiline and not too long)
			if ($arrayIsSimple && !$noFormat && count($arrayParts) === 1
			    && !$arrayParts[0][3]
			    && $this->maxLineLength >= mb_strlen($arrayParts[0][1], 'UTF-8')
			                               + ($indent*$tabLength)
			) {
				$noFormat = true;
			}

			// Build code
			$keyLengthBlock = 0;
			$maxKeyLength   = $maxKeyLenBlocks[$keyLengthBlock];
			foreach ($arrayParts as &$data) {
				list(
					$key,
					$val,
					$keyLength,
					$multiline
				) = $data;

				if (!$noFormat) {
					// Full align length in chars (after key, before value)
					$alignLength = $maxKeyLength - $keyLength + 1;

					// Simple spaces alignment
					if ($spacePostfix) {
						$key .= str_repeat(' ', $alignLength);
					}

					// Tabs with mix of spaces alignment (for shortness)
					else if ($mixSpaces) {
						if ($curTabTail = $keyLength % $tabLength) {
							$curTabTail = $tabLength-$curTabTail;
						}
						$alignTail = $alignLength % $tabLength;
						if ($curTabTail <= $alignTail
							|| $alignLength / $tabLength >= 1
						) {
							if ($curTabTail) {
								$key         .= $tab;
								$alignLength -= $curTabTail;
							}
							if ($alignLength) {
								if ($align = floor($alignLength / $tabLength)) {
									$key .= str_repeat($tab, $align);
								}
								$alignTail = $alignLength % $tabLength;
							} else {
								$alignTail = 0;
							}
						}
						if ($alignTail) {
							$key .= str_repeat(' ', $alignTail);
						}
					}

					// Only tabs alignment
					else {
						if ($curTabTail = $keyLength % $tabLength) {
							$key         .= $tab;
							$alignLength -= $tabLength-$curTabTail;
						}
						$key .= str_repeat(
							$tab,
							ceil($alignLength / $tabLength)
						);
					}

					// Different align before and after multiline value
					if ($multiline) {
						$maxKeyLength = $maxKeyLenBlocks[++$keyLengthBlock];
					}
				}

				$data = ($arrayIsSimple
							? ''
							: $key . '=>' . ($noFormat ? '' : ' ')
				        ) . $val . ',';
			}

			// Join code parts
			if ($noFormat) {
				$resultCode .= join('', $arrayParts);
				$resultCode  = substr($resultCode, 0, -1);
			} else {
				$resultCode .= "{$newLine}{$indentString}{$tab}"
						. join("{$newLine}{$indentString}{$tab}", $arrayParts)
						. "{$newLine}{$indentString}";
			}
		}

		$resultCode .= $shortSyntax ? ']' : ')';

		return $resultCode;
	}
}
