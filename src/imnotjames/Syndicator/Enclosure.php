<?php
namespace imnotjames\Syndicator;

/**
 * A link to a file, in an article
 *
 * @package imnotjames\Syndicator
 */
class Enclosure {
	/**
	 * URI of enclosed file
	 *
	 * @var string
	 */
	private $uri;

	/**
	 * Byte length of enclosed file
	 *
	 * @var int
	 */
	private $length;

	/**
	 * Content type of enclosed file
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Construct an enclosure
	 *
	 * @param string $uri
	 * @param int    $length
	 * @param string $type
	 */
	public function __construct($uri, $length, $type) {
		$this->uri = $uri;
		$this->length = (int) $length;
		$this->type = $type;
	}

	/**
	 * Get enclosure file's URI
	 *
	 * @return string
	 */
	public function getURI() {
		return $this->uri;
	}

	/**
	 * Get the enclosure file's length in bytes
	 *
	 * @return int
	 */
	public function getLength() {
		return $this->length;
	}

	/**
	 * Get the enclosure file's content type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
}