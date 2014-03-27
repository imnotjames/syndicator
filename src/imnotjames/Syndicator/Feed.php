<?php
namespace imnotjames\Syndicator;

use IteratorAggregate;
use ArrayIterator;

/**
 * Syndication feed
 *
 * @package imnotjames\RSS
 */
class Feed implements IteratorAggregate {
	/**
	 * @var array
	 */
	private $articles = array();

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $subtitle;

	/**
	 * @var string
	 */
	private $uri;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var
	 */
	private $dateUpdated;

	/**
	 * @var \DateTime
	 */
	private $datePublished;

	/**
	 * @var string
	 */
	private $logoURI;

	/**
	 * @var array
	 */
	private $categories = array();

	public function __construct($title, $uri, $description) {
		$this->title = $title;
		$this->uri = $uri;
		$this->description = $description;
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
	public function getSubtitle() {
		return $this->subtitle;
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

	public function getDatePublished() {
		return $this->datePublished;
	}

	public function getDateUpdated() {
		return $this->dateUpdated;
	}

	public function getCategories() {
		return $this->categories;
	}

	public function getArticles() {
		return $this->articles;
	}

	public function getLogoURI() {
		return $this->logoURI;
	}

	/**
	 * Add an article to the feed
	 *
	 * @param Article $article
	 */
	public function addArticle(Article $article) {
		$this->articles[] = $article;
	}

	/**
	 * Remove all articles from this feed
	 */
	public function clearArticles() {
		$this->articles = array();
	}

	/**
	 * Retrieve an external iterator
	 *
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return \Traversable An instance of an object implementing <b>Iterator</b> or
	 *       <b>Traversable</b>
	 */
	public function getIterator() {
		return new ArrayIterator($this->articles);
	}
}