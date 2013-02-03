<?php

namespace Aza\Components\PhpGen;
use Traversable;

/**
 * PHP code generation
 *
 * @project Anizoptera CMF
 * @package system.phpgen
 */
class PhpGen
{
	/**
	 * Length of one tab in spaces
	 */
	public static $tabLength = 4;

	/**
	 * Use spaces instead of tabs for indentation
	 */
	public static $useSpaces = false;

	/**
	 * Mix tabs with spaces in the end of indentation
	 */
	public static $mixSpaces = true;

	/**
	 * Mix tabs with spaces in the end of indentation
	 */
	public static $spacesAfterKey = true;

	/**
	 * Output string variables as one line.
	 * Converts \n and \r symbols to escaped characters.
	 */
	public static $oneLineStrings = false;

	/**
	 * Output array keys for serial arrays.
	 */
	public static $outputSerialKeys = false;

	/**
	 * Use php 5.4 short array syntax
	 */
	public static $shortArraySyntax = false;


	// TODO: В массивах если значение-массив разрывает список, то можно отступы до и после него рассчитывать отдельно
	// TODO: Work as instance
	// TODO: Cover with tests


	/**
	 * Returns singleton instance of the class
	 *
	 * @return self
	 */
	final public static function instance()
	{
		static $instance;
		return $instance ?: $instance = new self;
	}


	/**
	 * Generation of php code for various data without trailing semicolon.
	 *
	 * WARNING!
	 * Don't use self referencing arrays and objects!
	 *
	 * @see getCode
	 *
	 * @param mixed $data     Data
	 * @param int   $indent   Indent size in tabs for array php code
	 * @param bool  $noFormat No formatting and indention
	 *
	 * @return string
	 */
	public static function getCodeNoTail($data, $indent = 0, $noFormat = false)
	{
		return self::getCode($data, $indent, $noFormat, true);
	}

	/**
	 * Generation of php code for various data
	 *
	 * WARNING!
	 * Don't use self referencing arrays and objects!
	 *
	 * @param mixed $data     Data
	 * @param int   $indent   Indent size in tabs for array php code
	 * @param bool  $noFormat No formatting and indention
	 * @param bool  $noTail   System parameter for recursive calls
	 *
	 * @return string
	 */
	public static function getCode($data, $indent = 0, $noFormat = false, $noTail = false)
	{
		$tail = $noTail ? '' : ';';

		// Null
		if (!isset($data)) {
			return 'null' . $tail;
		}
		// Bool / Int / Float
		else if (is_bool($data) || is_int($data) || is_float($data)) {
			return var_export($data, true) . $tail;
		}
		// Array
		else if (is_array($data) || $data instanceof \Traversable) {
			return self::getArray($data, $indent, $noFormat) . $tail;
		}
		// Object
		else if (is_object($data)) {
			return self::getObject($data) . $tail;
		}
		// String
		// TODO: Test for chars with all ASCII chars
		if (self::$oneLineStrings
		    && (false !== strpos($data, "\n")
		        || false !== strpos($data, "\r"))
		) {
			$data = addcslashes($data, '"$\\');
			$data = str_replace(["\n", "\r"], ['\n', '\r'], $data);
			return '"' . $data . '"' . $tail;
		}
		return "'" . addcslashes($data, "'\\") . "'" . $tail;
	}


	/**
	 * Returns php code for object
	 *
	 * @param object|IPhpGenerable $object Object data
	 *
	 * @return string
	 */
	protected static function getObject($object)
	{
		// TODO: Closure support
		if ($object instanceof IPhpGenerable) {
			return $object->generateCode();
		}
		$code = self::getCode(serialize($object));
		return "unserialize({$code})";
	}

	/**
	 * Returns php code for array
	 *
	 * @param array|Traversable $array    Array data
	 * @param int               $indent   Indent size in tabs for array php code
	 * @param bool              $noFormat No formatting and indention
	 *
	 * @return string
	 */
	protected static function getArray($array, $indent = 0, $noFormat = false)
	{
		$string = self::$shortArraySyntax ? '[' : 'array(';

		if ($array) {
			$tabLength    = (int)self::$tabLength;
			$mixSpaces    = (bool)self::$mixSpaces;
			$useSpaces    = self::$useSpaces;
			$tab          = $useSpaces ? str_repeat(' ', $tabLength) : "\t";
			$spacePostfix = $useSpaces || self::$spacesAfterKey;


			// The overall indent
			$indentString = $noFormat ? '' : str_repeat($tab, $indent);

			$maxKeyLength   = 0;
			$arrayCodeParts = [];
			$arrayIsSimple  = !self::$outputSerialKeys;
			$i = 0;
			foreach ($array as $key => $val) {
				if ($arrayIsSimple && $key !== $i++) {
					$arrayIsSimple = false;
				}
				$key        = self::getCode($key, 0, true, true);
				$val        = self::getCode($val, $indent+1, $noFormat, true);
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

		$string .= self::$shortArraySyntax ? ']' : ')';

		return $string;
	}
}

PhpGen::$shortArraySyntax = version_compare(PHP_VERSION, '5.4.0', '>=');
