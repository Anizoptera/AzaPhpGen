<?php

namespace Aza\Components\PhpGen;

/**
 * Interface for classes that can be translated into php code by {@link PhpGen}
 *
 * @project Anizoptera CMF
 * @package system.phpgen
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
