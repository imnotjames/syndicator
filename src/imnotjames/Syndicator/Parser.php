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
	 * @return Feed
	 */
	public function parse($string);
}