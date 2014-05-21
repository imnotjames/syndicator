<?php
namespace imnotjames\Syndicator;

/**
 * Abstract class to define an interval of time that can be ignored
 *
 * @package imnotjames\Syndicator
 */
abstract class Skip {
	/**
	 * @var string
	 */
	private $value;

	/**
	 * @param string $value
	 */
	public function __construct($value) {
		$this->setValue($value);
	}

	/**
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}
}