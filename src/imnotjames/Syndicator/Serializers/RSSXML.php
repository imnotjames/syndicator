<?php
namespace imnotjames\Syndicator\Serializers;

use imnotjames\Syndicator\Exceptions\SerializationException;
use imnotjames\Syndicator\Feed;
use imnotjames\Syndicator\Serializer;
use SimpleXMLElement;

/**
 * An RSS 2.0 XML Serializer
 *
 * @package imnotjames\Syndicator\Serializers
 */
class RSSXML implements Serializer {
	const DATE_FORMAT = 'D, j F Y G:i:s e';

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
	public function __construct($encoding = 'UTF-8') {
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
	 * @param Feed $feed
	 *
	 * @return mixed|string
	 */
	public function serialize(Feed $feed) {
		$feedXML = new SimpleXMLElement('<?xml version="1.0" encoding="' . $this->encoding . '" ?><rss version="2.0" />');

		$channelXML = $feedXML->addChild('channel');

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

		$channelXML->addChild('generator', $this->generator);

		$logoURI = $feed->getLogoURI();
		if (!is_null($logoURI)) {
			$channelXML->addChild('image', $logoURI);
		}

		$categories = $feed->getCategories();
		foreach ($categories as $category) {
			$channelXML->addChild('category', $category);
		}

		// Generate the feed items
		foreach ($feed as $article) {
			/**
			 * @var $article \imnotjames\Syndicator\Article
			 */
			$itemXML = $channelXML->addChild('item');

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
				$itemXML->addChild('category', $category);
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

			$itemEnclosure = $article->getEnclosure();
			if (!empty($itemEnclosure)) {
				$enclosureXML = $itemXML->addChild('enclosure');
				$enclosureXML->addAttribute('url', $itemEnclosure->getURI());
				$enclosureXML->addAttribute('length', $itemEnclosure->getLength());
				$enclosureXML->addAttribute('type', $itemEnclosure->getType());
			}
		}

		return $feedXML->asXML();
	}
}