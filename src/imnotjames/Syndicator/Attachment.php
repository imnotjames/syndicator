<?php
namespace imnotjames\Syndicator;

/**
 * A link to a file, in an article
 *
 * @package imnotjames\Syndicator
 */
abstract class Attachment {
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
	 * @param null|string $mediaType
	 * @param int         $length
	 *
	 * @internal param string $type
	 */
	public function __construct($mediaType = null, $length = 0) {
		$this->length = (int) $length;
		$this->type = $mediaType;
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
	public function getMediaType() {
		return $this->type;
	}
}