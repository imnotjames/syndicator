<?php
namespace imnotjames\Syndicator;

use imnotjames\Syndicator\Exceptions\InvalidURIException;

/**
 * A link to a file, in an article
 *
 * @package imnotjames\Syndicator
 */
class Attachment {
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
	 *
	 * @throws Exceptions\InvalidURIException
	 */
	public function __construct($uri, $length, $type) {
		$uri = filter_var($uri, FILTER_VALIDATE_URL);

		if ($uri === false) {
			throw new InvalidURIException();
		}

		$this->uri = $uri;
		$this->length = (int) $length;
		$this->type = $type;
	}

	/**
	 * Get enclosed file's URI
	 *
	 * @return string
	 */
	public function getURI() {
		return $this->uri;
	}

	/**
	 * Get the enclosed file's length in bytes
	 *
	 * @return int
	 */
	public function getLength() {
		return $this->length;
	}

	/**
	 * Get the enclosed file's content type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
}