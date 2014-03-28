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
	 * @var Enclsoure
	 */
	private $enclosure;

	/**
	 * @var \DateTime
	 */
	private $datePublished;

	/**
	 * @var string
	 */
	private $authorEmail;

	/**
	 * @var string
	 */
	private $authorName;

	/**
	 * @var array
	 */
	private $categories = array();

	/**
	 * @param string $title
	 * @param string $description
	 * @param string $uri
	 *
	 * @throws Exceptions\InvalidURIException
	 */
	public function __construct($title, $description, $uri) {
		$uri = filter_Var($uri, FILTER_VALIDATE_URL);
		if ($uri === false) {
			throw new InvalidURIException();
		}

		$this->title = $title;
		$this->description = $description;
		$this->uri = $uri;
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
	 * @return string
	 */
	public function getAuthorEmail() {
		return $this->authorEmail;
	}

	/**
	 * @return string
	 */
	public function getAuthorName() {
		return $this->authorName;
	}

	/**
	 * @return Enclosure
	 */
	public function getEnclosure() {
		return $this->enclosure;
	}

	/**
	 * @return array
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @param int $id
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
	 * @param DateTime $publishedDate
	 */
	public function setPublishedDate(DateTime $publishedDate) {
		$this->publishedDate = $publishedDate;
	}

	/**
	 * @param string $copyright
	 */
	public function setCopyright($copyright) {
		$this->copyright = $copyright;
	}

	/**
	 * @param string $authorEmail
	 */
	public function setAuthorEmail($authorEmail) {
		$this->authorEmail = $authorEmail;
	}

	/**
	 * @param string $authorName
	 */
	public function setAuthorName($authorName) {
		$this->authorName = $authorName;
	}

	/**
	 * @param Enclosure $enclosure
	 */
	public function setEnclosure(Enclosure $enclosure) {
		$this->enclosure = $enclosure;
	}

	/**
	 * @param string $category
	 */
	public function addCategory($category) {
		$this->categories[] = $category;
	}

	/**
	 *
	 */
	public function clearCategories() {
		$this->categories = array();
	}
}