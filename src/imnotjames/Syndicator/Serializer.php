<?php
namespace imnotjames\Syndicator;

/**
 * Interface Serializer
 *
 * @package imnotjames\Syndicator
 */
interface Serializer {
	/**
	 * Serialize a Feed object to a string format.
	 *
	 * @param Feed $feed
	 *
	 * @return string
	 */
	public function serialize(Feed $feed);
}