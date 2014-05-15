<?php
namespace imnotjames\Syndicator\Parsers;

use imnotjames\Syndicator\Feed;
use imnotjames\Syndicator\Parser;

/**
 * An RSS 2.0 XML Parser
 *
 * This is currently only a stub!
 *
 * @package imnotjames\Syndicator\Serializers
 */
class RSSXML implements Parser {

	private $strict;

	/**
	 * @param bool $strict
	 */
	public function __construct($strict = true) {
		$this->strict = $strict == true;
	}

	/**
	 * Parse a Feed from a string
	 *
	 * @param $string
	 *
	 * @return Feed
	 */
	public function parse($string) {
		return null;
	}
}