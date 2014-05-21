<?php
namespace imnotjames\Syndicator;

use DateTime;
use imnotjames\Syndicator\Exceptions\InvalidURIException;

/**
 * An article in a syndication feed
 *
 * @package imnotjames\Syndicator
 */
class Article {
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var string
	 */
	private $uri;

	/**
	 * @var string
	 */
	private $copyright;

	/**
	 * @var array
	 */
	private $enclosures = array();

	/**
	 * @var \DateTime
	 */
	private $datePublished;

	/**
	 * @var Contact
	 */
	private $author;

	/**
	 * @var array
	 */
	private $categories = array();

	/**
	 * @param string $id
	 *
	 * @throws Exceptions\InvalidURIException
	 */
	public function __construct($id = null) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getID() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getURI() {
		return $this->uri;
	}

	/**
	 * @return \DateTime
	 */
	public function getDatePublished() {
		return $this->datePublished;
	}

	/**
	 * @return string
	 */
	public function getCopyright() {
		return $this->copyright;
	}

	/**
	 * @return Contact
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * @return array
	 */
	public function getEnclosures() {
		return $this->enclosures;
	}

	/**
	 * @return array
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @param string $id
	 */
	public function setID($id) {
		$this->id = $id;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @param string $uri
	 *
	 * @throws Exceptions\InvalidURIException
	 */
	public function setURI($uri) {
		$uri = filter_Var($uri, FILTER_VALIDATE_URL);
		if ($uri === false) {
			throw new InvalidURIException();
		}

		$this->uri = $uri;
	}

	/**
	 * @param DateTime $datePublished
	 */
	public function setDatePublished(DateTime $datePublished) {
		$this->datePublished = $datePublished;
	}

	/**
	 * @param Contact $author
	 */
	public function setAuthor(Contact $author) {
		$this->author = $author;
	}

	/**
	 * @param Enclosure $enclosure
	 */
	public function addEnclosure(Enclosure $enclosure) {
		$this->enclosures[] = $enclosure;
	}

	/**
	 * @param Category $category
	 */
	public function addCategory(Category $category) {
		$this->categories[] = $category;
	}

	/**
	 *
	 */
	public function clearCategories() {
		$this->categories = array();
	}
}