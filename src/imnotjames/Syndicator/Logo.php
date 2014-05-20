<?php
namespace imnotjames\Syndicator;

use imnotjames\Syndicator\Exceptions\InvalidURIException;

/**
 * Image that is displayed with the channel
 *
 * @package imnotjames\Syndicator
 */
class Logo {
	/**
	 * @var string
	 */
	private $uri;

	/**
	 * @var string|null
	 */
	private $title;

	/**
	 * @var string|null
	 */
	private $link;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var int
	 */
	private $width;

	/**
	 * @var int
	 */
	private $height;

	/**
	 * @param string      $uri
	 * @param string|null $title
	 * @param string|null $link
	 *
	 * @throws InvalidURIException
	 */
	public function __construct($uri, $title = null, $link = null) {
		$this->setURI($uri);
		$this->title = $title;
		$this->link = $link;
	}

	/**
	 * @return string
	 */
	public function getURI() {
		return $this->uri;
	}

	/**
	 * @return null|string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return null|string
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return int
	 */
	public function getWidth() {
		return $this->width;
	}

	/**
	 * @return int
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * @param string $uri
	 *
	 * @throws InvalidURIException
	 */
	public function setURI($uri) {
		$uri = filter_var($uri, FILTER_VALIDATE_URL);

		if ($uri === false) {
			throw new InvalidURIException();
		}

		$this->uri = $uri;
	}

	/**
	 * @param string|null $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @param string|null $uri
	 *
	 * @throws InvalidURIException
	 */
	public function setLink($uri) {
		if (!is_null($uri)) {
			$uri = filter_var($uri, FILTER_VALIDATE_URL);

			if ($uri === false) {
				throw new InvalidURIException();
			}
		}

		$this->link = $uri;
	}

	/**
	 * @param string|null $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @param int|null $width
	 */
	public function setWidth($width) {
		$this->width = $width;
	}

	/**
	 * @param int|null $height
	 */
	public function setHeight($height) {
		$this->height = $height;
	}
}