<?php

namespace Aza\Components\PhpGen;

/**
 * Interface for classes that can be translated into php code by {@link PhpGen}.
 * See {@link CustomCode} for example.
 *
 * @project Anizoptera CMF
 * @package system.phpgen
 * @author  Amal Samally <amal.samally at gmail.com>
 * @license MIT
 */
interface IPhpGenerable
{
	/**
	 * Returns generated php code for this instance
	 *
	 * @return string
	 */
	public function generateCode();
}
