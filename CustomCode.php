<?php

namespace Aza\Components\PhpGen;

/**
 * Default IPhpGenerable implementation.
 *
 * @see IPhpGenerable
 * @see PhpGen::getObject
 *
 * @project Anizoptera CMF
 * @package system.phpgen
 */
class CustomCode implements IPhpGenerable
{
	/**
	 * @var string
	 */
	protected $code;

	/**
	 * Constructs instance with the specified code
	 *
	 * @param string $code Your custom PHP code
	 */
	public function __construct($code)
	{
		$this->code = $code;
	}

	/**
	 * {@inheritdoc}
	 */
	public function generateCode()
	{
		return $this->code;
	}
}
