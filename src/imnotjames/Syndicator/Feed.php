<?php
namespace imnotjames\Syndicator;

use imnotjames\Syndicator\Exceptions\InvalidURIException;
use IteratorAggregate;
use ArrayIterator;
use DateTime;

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
	private $documentationUri;

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
	private $generator;

	/**
	 * @var string
	 */
	private $logo;

	/**
	 * @var string
	 */
	private $language;

	/**
	 * @var Contact
	 */
	private $editorContact;

	/**
	 * @var Contact
	 */
	private $webmasterContact;

	/**
	 * @var array
	 */
	private $categories = array();

	/**
	 * @var int
	 */
	private $ttl = 0;

	/**
	 * @var Subscription
	 */
	private $subscription;

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
	 * @return string
	 */
	public function getDocumentationURI() {
		return $this->documentationUri;
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
	public function getGenerator() {
		return $this->generator;
	}

	/**
	 * @return string
	 */
	public function getLanguage() {
		return $this->language;
	}

	/**
	 * @return Logo
	 */
	public function getLogo() {
		return $this->logo;
	}

	/**
	 * @return Contact|null
	 */
	public function getEditorContact() {
		return $this->editorContact;
	}

	/**
	 * @return Contact|null
	 */
	public function getWebmasterContact() {
		return $this->webmasterContact;
	}

	/**
	 * @return int
	 */
	public function getCacheTimeToLive() {
		return $this->ttl;
	}

	/**
	 * @return Subscription
	 */
	public function getSubscription() {
		return $this->subscription;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @param string $subtitle
	 */
	public function setSubtitle($subtitle) {
		$this->subtitle = $subtitle;
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
		$uri = filter_var($uri, FILTER_VALIDATE_URL);

		if ($uri === false) {
			throw new InvalidURIException();
		}

		$this->uri = $uri;
	}

	/**
	 * @param $uri
	 *
	 * @throws Exceptions\InvalidURIException
	 */
	public function setDocumentationURI($uri) {
		$uri = filter_var($uri, FILTER_VALIDATE_URL);

		if ($uri === false) {
			throw new InvalidURIException();
		}

		$this->documentationUri = $uri;
	}

	/**
	 * @param DateTime $datePublished
	 */
	public function setDatePublished(DateTime $datePublished) {
		$this->datePublished = $datePublished;
	}

	/**
	 * @param DateTime $dateUpdated
	 */
	public function setDateUpdated(DateTime $dateUpdated) {
		$this->dateUpdated = $dateUpdated;
	}

	/**
	 * @param string $generator
	 */
	public function setGenerator($generator) {
		$this->generator = $generator;
	}

	/**
	 * @param string $language
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}

	/**
	 * @param Logo $logo
	 */
	public function setLogo(Logo $logo = null) {
		$this->logo = $logo;
	}

	/**
	 * @param Contact $contact
	 */
	public function setEditorContact(Contact $contact = null) {
		$this->editorContact = $contact;
	}

	/**
	 * @param Contact $contact
	 */
	public function setWebmasterContact(Contact $contact = null) {
		$this->webmasterContact = $contact;
	}

	/**
	 * @param $ttl
	 */
	public function setCacheTimeToLive($ttl) {
		$this->ttl = $ttl;
	}

	/**
	 * @param Subscription $subscription
	 */
	public function setSubscription(Subscription $subscription) {
		$this->subscription = $subscription;
	}

	/**
	 * @param Category $category
	 */
	public function addCategory(Category $category) {
		$this->categories[] = $category;
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