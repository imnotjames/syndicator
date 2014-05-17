<?php
namespace imnotjames\Syndicator;

/**
 * Interface Parser
 *
 * @package imnotjames\Syndicator
 */
interface Parser {
	/**
	 * Parse a Feed from a string
	 *
	 * @param $string
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	public function parse($string);

	/**
	 * Validate a Feed from a string
	 *
	 * @param $string
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 *
	 * @return mixed
	 */
	public function validate($string);
}