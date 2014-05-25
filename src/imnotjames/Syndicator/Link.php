<?php
namespace imnotjames\Syndicator;

/**
 * An attachment that is a link
 *
 * @package imnotjames\Syndicator
 */
class Link extends Attachment {
	const TYPE_COMMENT = 'comment';
	const TYPE_SOURCE = 'source';
	const TYPE_ENCLOSURE = 'enclosure';

	/**
	 * @var string
	 */
	private $uri;

	/**
	 * @var string
	 */
	private $linkType;

	/**
	 * @param string $uri
	 * @param int         $linkType
	 * @param null        $mediaType
	 * @param int         $length
	 *
	 * @throws InvalidURIException
	 */
	public function __construct($uri, $linkType, $mediaType = null, $length = 0) {
		parent::__construct($mediaType, $length);

		$uri = filter_var($uri, FILTER_VALIDATE_URL);

		if ($uri === false) {
			throw new InvalidURIException();
		}

		$this->uri = $uri;
		$this->linkType = $linkType;
	}

	/**
	 * @return string
	 */
	public function getURI() {
		return $this->uri;
	}

	/**
	 * @return string
	 */
	public function getLinkType() {
		return $this->linkType;
	}
}