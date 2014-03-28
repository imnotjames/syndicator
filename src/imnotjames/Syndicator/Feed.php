<?php
namespace imnotjames\Syndicator;

use imnotjames\Syndicator\Exceptions\InvalidURIException;
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

	/**
	 * @param string $title
	 * @param string $description
	 * @param string $uri
	 *
	 * @throws Exceptions\InvalidURIException
	 */
	public function __construct($title, $description, $uri) {
		$uri = filter_var($uri, FILTER_VALIDATE_URL);

		if ($uri === false) {
			throw new InvalidURIException();
		}

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

	/**
	 * @return \DateTime|null
	 */
	public function getDatePublished() {
		return $this->datePublished;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getDateUpdated() {
		return $this->dateUpdated;
	}

	/**
	 * @return array
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @return array
	 */
	public function getArticles() {
		return $this->articles;
	}

	/**
	 * @return string
	 */
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