<?php
namespace imnotjames\Syndicator\Serializers;

use imnotjames\Syndicator\Article;
use imnotjames\Syndicator\Category;
use imnotjames\Syndicator\Exceptions\SerializationException;
use imnotjames\Syndicator\Feed;
use imnotjames\Syndicator\Serializer;
use SimpleXMLElement;
use DateTime;

/**
 * An RSS 2.0 XML Serializer
 *
 * @package imnotjames\Syndicator\Serializers
 */
class RSSXML implements Serializer {
	const DATE_FORMAT = DateTime::RSS;

	/**
	 * @var string
	 */
	private $encoding;

	/**
	 * @var string
	 */
	private $subtitleSeparator = ' &mdash; ';

	/**
	 * @var string
	 */
	private $generator = 'Syndicator RSS Serializer (https://github.com/imnotjames/syndicator)';

	/**
	 * Construct an RSS 2.0 XML serializer
	 *
	 * @param string $encoding
	 */
	public function __construct($encoding = null) {
		$this->encoding = $encoding;
	}

	/**
	 * @param string $separator
	 */
	public function setSubtitleSeparator($separator) {
		$this->subtitleSeparator = $separator;
	}

	/**
	 * @param string $generator
	 */
	public function setGenerator($generator) {
		$this->generator = $generator;
	}

	/**
	 * @param SimpleXMLElement $parent
	 * @param Feed             $feed
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\SerializationException
	 */
	private function serializeFeed(SimpleXMLElement $parent, Feed $feed) {
		$channelXML = $parent->addChild('channel');

		$title = $feed->getTitle();
		$subtitle = $feed->getSubtitle();

		if (!empty($subtitle)) {
			$title .= $this->subtitleSeparator;
			$title .= $subtitle;
		}

		// Required
		$channelXML->addChild('title', $title);
		$channelXML->addChild('link', $feed->getURI());
		$channelXML->addChild('description', $feed->getDescription());

		// optional

		$language = $feed->getLanguage();
		if (!is_null($language)) {
			$channelXML->addChild('language', $language);
		}

		$datePublished = $feed->getDatePublished();
		if (!is_null($datePublished)) {
			$channelXML->addChild('pubDate', $datePublished->format(self::DATE_FORMAT));
		}

		$dateUpdated = $feed->getDateUpdated();
		if (!is_null($dateUpdated)) {
			$channelXML->addChild('lastBuildDate', $dateUpdated->format(self::DATE_FORMAT));
		}

		if (is_null($feed->getGenerator())) {
			$channelXML->addChild('generator', $this->generator);
		} else {
			$channelXML->addChild('generator', $feed->getGenerator());
		}


		$logo = $feed->getLogo();
		if (!is_null($logo)) {
			$this->serializeLogo($channelXML, $logo);
		}

		$categories = $feed->getCategories();
		foreach ($categories as $category) {
			$this->serializeCategory($channelXML, $category);
		}

		// Generate the feed items
		$articles = $feed->getArticles();
		foreach ($articles as $article) {
			$this->serializeArticle($channelXML, $article);
		}
	}

	/**
	 * @param SimpleXMLElement $parent
	 * @param Logo             $logo
	 */
	private function serializeLogo(SimpleXMLElement $parent, Logo $logo) {
		$imageXML = $parent->addChild('image');

		$imageXML->addChild('uri', $logo->getURI());
		$imageXML->addChild('link', $logo->getLink() ?: $feed->getURI());
		$imageXML->addChild('title', $logo->getTitle() ?: $feed->getTitle());

		$description = $logo->getDescription();
		$width = $logo->getWidth();
		$height = $logo->getHeight();

		if (!is_null($description)) {
			$imageXML->addChild('description', $description);
		}

		if (!is_null($width)) {
			$imageXML->addChild('width', $width);
		}

		if (!is_null($height)) {
			$imageXML->addChild('height', $height);
		}
	}

	/**
	 * @param SimpleXMLElement $parent
	 * @param Category         $category
	 */
	private function serializeCategory(SimpleXMLElement $parent, Category $category) {
		$categoryXML = $parent->addChild('category', $category->getName());

		if (!is_null($category->getTaxonomy())) {
			$categoryXML['domain'] = $category->getTaxonomy();
		}
	}

	/**
	 * @param SimpleXMLElement $parent
	 * @param Article          $article
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\SerializationException
	 */
	private function serializeArticle(SimpleXMLElement $parent, Article $article) {
		$itemXML = $parent->addChild('item');

		$title = $article->getTitle();
		$link = $article->getURI();
		$description = $article->getDescription();

		if (is_null($title) && is_null($description)) {
			throw new SerializationException('Articles must have at least a title or description');
		}

		if (! is_null($title)) {
			$itemXML->addChild('title', $title);
		}

		if (!is_null($link)) {
			$itemXML->addChild('link', $link);
		}

		if (!is_null($description)) {
			$itemXML->addChild('description', $description);
		}

		$categories = $article->getCategories();
		foreach ($categories as $category) {
			$categoryXML = $itemXML->addChild('category', $category->getName());
			if (!is_null($category->getTaxonomy())) {
				$categoryXML['domain'] = $category->getTaxonomy();
			}
		}

		$publishedDate = $article->getDatePublished();
		if (!is_null($publishedDate)) {
			$itemXML->addChild('pubDate', $publishedDate->format(self::DATE_FORMAT));
		}

		$guid = $article->getID();
		if (!empty($guid)) {
			$itemXML->addChild('guid', $guid);
		} else {
			$permalinkXML = $itemXML->addChild('guid', hash('sha256', $article->getTitle() . $article->getURI() . $article->getDescription()));
			$permalinkXML->addAttribute('isPermaLink', 'false');
		}

		$attachments = $article->getAttachments();
		foreach ($attachments as $attachment) {
			$attachmentXML = $itemXML->addChild('enclosure');
			$attachmentXML->addAttribute('url', $attachment->getURI());
			$attachmentXML->addAttribute('length', $attachment->getLength());
			$attachmentXML->addAttribute('type', $attachment->getType());
		}

		$author = $article->getAuthor();
		if (!is_null($author)) {
			$email = $author->getEmail();
			$name = $author->getName();

			if (!empty($name)) {
				$itemXML->addChild('author', sprintf('%s (%s)', $email, $name));
			} else {
				$itemXML->addChild('author', $email);
			}
		}
	}

	/**
	 * @param Feed $feed
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\SerializationException
	 *
	 * @return mixed|string
	 */
	public function serialize(Feed $feed) {
		$feedXML = new SimpleXMLElement('<?xml version="1.0"' . ($this->encoding ? ' encoding="' . $this->encoding . '"' : '') . '?><rss version="2.0" />');

		$this->serializeFeed($feedXML, $feed);

		return $feedXML->asXML();
	}
}