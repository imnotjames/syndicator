<?php
namespace imnotjames\Syndicator\Parsers;

use imnotjames\Syndicator\Article;
use imnotjames\Syndicator\Category;
use imnotjames\Syndicator\Contact;
use imnotjames\Syndicator\Link;
use imnotjames\Syndicator\Exceptions\ParsingException;
use imnotjames\Syndicator\Feed;
use imnotjames\Syndicator\Logo;
use imnotjames\Syndicator\Parser;
use DateTime;
use imnotjames\Syndicator\SkipHour;
use imnotjames\Syndicator\SkipWeekday;
use imnotjames\Syndicator\Subscription;
use SimpleXMLElement;

/**
 * An RSS 2.0 XML Parser
 *
 * @package imnotjames\Syndicator\Serializers
 */
class RSSXML implements Parser {
	const MIME_TYPE_RFC2045_REGEX = '/[a-z0-9!#$%^&\*\+{}\|\'.`~_-]+\/[a-z0-9!#$%^&\*\+{}\|\'.`~_-]+/i';
	const ISO639_LANGUAGE = '/^[a-z]{2,3}(-[a-z]{2,3})?$/i';

	const IMAGE_MAX_HEIGHT = 400;
	const IMAGE_MAX_WIDTH = 144;

	private $strict;

	/**
	 * @param bool $strict
	 */
	public function __construct($strict = true) {
		$this->strict = $strict == true;
	}

	/**
	 * Validate Date and time that is the text of an element.
	 *
	 * @param SimpleXMLElement $element
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateDateTime(SimpleXMLElement $element) {
		$rfc822 = DateTime::createFromFormat(DateTime::RSS, (string) $element);
		$rfc2822 = DateTime::createFromFormat(DateTime::RSS, (string) $element);

		if ($rfc822 === false && $rfc2822 === false) {
			throw new ParsingException('invalid ' . $element->getName() . ' element: invalid date format');
		}
	}

	/**
	 * Validate a contact.  A contact can be represented as either an email,
	 * or an email with an alias.
	 *
	 * For example:
	 *   foo@example.com
	 *   foo@example.com (First last McGee)
	 *
	 * Currently this just verifies that the string is an email followed by a space.
	 *
	 * @param SimpleXMLElement $element
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateContact(SimpleXMLElement $element) {
		$email = explode(' ', (string) $element, 2);

		if (filter_var($email[0], FILTER_VALIDATE_EMAIL) === false) {
			throw new ParsingException('invalid ' . $element->getName() . ' element: invalid email');
		}
	}

	/**
	 * Validate an element has a text field is a URL per RFC 2396
	 *
	 * @param SimpleXMLElement $element
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateRFC2396URL(SimpleXMLElement $element) {
		if (filter_var((string) $element, FILTER_VALIDATE_URL) === false) {
			throw new ParsingException('invalid ' . $element->getName() . ' element: invalid uri');
		}
	}

	/**
	 * Validate an RSS tag
	 *
	 * The RSS tag must have a version, and the version MUST be 2.0
	 *
	 * The RSS tag must also have one channel, and only one channel.
	 * 
	 * @param SimpleXMLElement $rss
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateRssElement(SimpleXMLElement $rss) {
		if (!isset($rss['version'])) {
			throw new ParsingException('invalid rss element: invalid version attribute');
		}

		$version = (string) $rss['version'];

		if (strlen($version) === 0 || $version !== '2.0') {
			throw new ParsingException('invalid rss element: invalid version attribute');
		}

		if (count($rss->channel) == 0) {
			throw new ParsingException('invalid rss element: missing channel element');
		} else if ($this->strict && count($rss->channel) > 1) {
			throw new ParsingException('invalid rss element: duplicate channel element');
		}

		$this->validateRssChannelElement($rss->channel);
	}

	/**
	 * Validate the Channel element
	 *
	 * Must contain one of each of the following:
	 *   title
	 *   description
	 *   link
	 *
	 * May contain one of each of the following:
	 *   cloud
	 *   copyright
	 *   docs
	 *   generator
	 *   image
	 *   language
	 *   lastBuildDate
	 *   managingEditor
	 *   pubDate
	 *   skipDays
	 *   skipHours
	 *   textInput
	 *   ttl
	 *   webMaster
	 *
	 * May contain one or more of each of the following:
	 *   category
	 *   item
	 *
	 * @param SimpleXMLElement $channel
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateRssChannelElement(SimpleXMLElement $channel) {
		if (count($channel->title) === 0) {
			throw new ParsingException('invalid channel element: missing title element');
		} else if ($this->strict && count($channel->title) > 1) {
			throw new ParsingException('invalid channel element: duplicate title element');
		}

		if (count($channel->description) === 0) {
			throw new ParsingException('invalid channel element: missing description element');
		} else if ($this->strict && count($channel->description) > 1) {
			throw new ParsingException('invalid channel element: duplicate description element');
		}

		if (count($channel->link) === 0) {
			throw new ParsingException('invalid channel element: missing link element');
		} else if ($this->strict && count($channel->link) > 1) {
			throw new ParsingException('invalid channel element: duplicate link element');
		} else {
			$this->validateRFC2396URL($channel->link);
		}

		// Optional cloud element
		if (isset($channel->cloud)) {
			if ($this->strict && count($channel->cloud) > 1) {
				throw new ParsingException('invalid channel element: duplicate cloud element');
			}

			$this->validateRssChannelCloudElement($channel->cloud);
		}

		// Optional copyright element
		if (isset($channel->copyright)) {
			if ($this->strict && count($channel->copyright) > 1) {
				throw new ParsingException('invalid channel element: duplicate copyright element');
			}
		}

		// Optional docs element
		if (isset($channel->docs)) {
			if ($this->strict && count($channel->docs) > 1) {
				throw new ParsingException('invalid channel element: duplicate docs element');
			}

			$this->validateRFC2396URL($channel->docs);
		}

		// Optional generator element
		if (isset($channel->generator)) {
			if ($this->strict && count($channel->generator) > 1) {
				throw new ParsingException('invalid channel element: duplicate generator element');
			}
		}

		// Optional image element
		if (isset($channel->image)) {
			if ($this->strict && count($channel->image) > 1) {
				throw new ParsingException('invalid channel element: duplicate image element');
			}

			$this->validateRssChannelImageElement($channel->image);
		}

		// Optional item element
		if (isset($channel->item)) {
			$foundGuids = array();

			foreach($channel->item as $item) {
				if (isset($item->guid)) {
					if (in_array((string) $item->guid, $foundGuids, true)) {
						throw new ParsingException('invalid channel element: invalid guid element: guid repeats');
					}

					array_push($foundGuids, (string) $item->guid);
				}

				$this->validateRssChannelItemElement($item);
			}
		}

		// Optional language element
		if (isset($channel->language)) {
			if ($this->strict && count($channel->language) > 1) {
				throw new ParsingException('invalid channel element: duplicate language element');
			}

			if (!preg_match(self::ISO639_LANGUAGE, $channel->language)) {
				throw new ParsingException('invalid language element: language must be in ISO639 format');
			}
		}

		// Optional lastBuildDate element
		if (isset($channel->lastBuildDate)) {
			if ($this->strict && count($channel->lastBuildDate) > 1) {
				throw new ParsingException('invalid channel element: duplicate lastBuildDate element');
			}

			$this->validateDateTime($channel->lastBuildDate);
		}

		// Optional managingEditor element
		if (isset($channel->managingEditor)) {
			if ($this->strict && count($channel->managingEditor) > 1) {
				throw new ParsingException('invalid channel element: duplicate managingEditor element');
			}

			$this->validateContact($channel->managingEditor);
		}

		// Optional pubDate element
		if (isset($channel->pubDate)) {
			if ($this->strict && count($channel->pubDate) > 1) {
				throw new ParsingException('invalid channel element: duplicate pubDate element');
			}

			$this->validateDateTime($channel->pubDate);
		}

		// Optional skipDays element
		if (isset($channel->skipDays)) {
			if ($this->strict && count($channel->skipDays) > 1) {
				throw new ParsingException('invalid channel element: duplicate skipDays element');
			}

			$this->validateRssChannelSkipDaysElement($channel->skipDays);
		}

		// Optional skipHours element
		if (isset($channel->skipHours)) {
			if ($this->strict && count($channel->skipHours) > 1) {
				throw new ParsingException('invalid channel element: duplicate skipHours element');
			}

			$this->validateRssChannelSkipHoursElement($channel->skipHours);
		}

		// Optional textInput element
		if (isset($channel->textInput)) {
			if ($this->strict && count($channel->textInput) > 1) {
				throw new ParsingException('invalid channel element: duplicate textInput element');
			}

			$this->validateRssChannelTextInputElement($channel->textInput);
		}

		// Optional ttl element
		if (isset($channel->ttl)) {
			if ($this->strict && count($channel->ttl) > 1) {
				throw new ParsingException('invalid channel element: duplicate ttl element');
			}

			// The ttl must be a positive integer
			$ttlValidationOptions = array( 'options' => array( 'min_range' => 1 ) );

			if ($this->strict && filter_var((string) $channel->ttl, FILTER_VALIDATE_INT, $ttlValidationOptions) === false) {
				throw new ParsingException('invalid ttl element: must be a positive integer');
			}
		}

		// Optional webmaster element
		if (isset($channel->webMaster)) {
			if ($this->strict && count($channel->webMaster) > 1) {
				throw new ParsingException('invalid channel element: duplicate webMaster element');
			}

			$this->validateContact($channel->webMaster);
		}
	}

	/**
	 * Validate a Cloud element
	 *
	 * Must have the following attributes:
	 *   port
	 *   protocol
	 *   domain
	 *   path
	 *   registerProcedure
	 *
	 * Furthermore:
	 *   port must be a positive integer less than or equal to 65535
	 *   domain must not be empty
	 *   protocol must be one of: http-post, xml-rpc, soap
	 *
	 * @param SimpleXMLElement $cloud
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateRssChannelCloudElement(SimpleXMLElement $cloud) {
		if (isset($cloud['port'])) {
			$portValidateOptions = array( 'options' => array( 'min_range' => 1, 'max_range' => 65535 ) );

			if ($this->strict && filter_var((string) $cloud['port'], FILTER_VALIDATE_INT, $portValidateOptions) === false) {
				throw new ParsingException('invalid cloud element: invalid port attribute: must be a positive integer less than or equal to 65535');
			}
		} else {
			throw new ParsingException('invalid cloud element: missing port attribute');
		}

		if (!isset($cloud['protocol'])) {
			throw new ParsingException('invalid cloud element: missing protocol attribute');
		} else  if (!in_array((string) $cloud['protocol'], array('http-post', 'xml-rpc', 'soap'), true)) {
			throw new ParsingException('invalid cloud element: invalid protocol attribute: must be one of http-post, xml-rpc, or soap');
		}

		if (!isset($cloud['domain'])) {
			throw new ParsingException('invalid cloud element: missing domain attribute');
		} else  if (strlen((string) $cloud['domain']) === 0) {
			throw new ParsingException('invalid cloud element: invalid domain attribute: must not be blank');
		}

		if (!isset($cloud['path'])) {
			throw new ParsingException('invalid cloud element: missing path attribute');
		}

		if (!isset($cloud['registerProcedure'])) {
			throw new ParsingException('invalid cloud element: missing registerProcedure attribute');
		}
	}

	/**
	 * Validate an image element
	 *
	 * Must contain one of each of the following:
	 *   link
	 *   url
	 *   title
	 *
	 * May contain one of each of the following:
	 *   width
	 *   height
	 *   description
	 *
	 * Furthermore:
	 *   url must be a valid URI
	 *   link must be a valid URI
	 *   width must be a positive integer
	 *   height must be a positive integer
	 *
	 * @param SimpleXMLElement $image
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateRssChannelImageElement(SimpleXMLElement $image) {
		if (count($image->link) === 0) {
			throw new ParsingException('invalid image element: missing link element');
		} else if ($this->strict && count($image->link) > 1) {
			throw new ParsingException('invalid image element: duplicate link element');
		} else {
			$this->validateRFC2396URL($image->link);
		}

		if (count($image->url) === 0) {
			throw new ParsingException('invalid image element: missing url element');
		} else if ($this->strict && count($image->url) > 1) {
			throw new ParsingException('invalid image element: duplicate url element');
		} else {
			$this->validateRFC2396URL($image->url);
		}

		if (count($image->title) === 0) {
			throw new ParsingException('invalid image element: missing title element');
		} else if ($this->strict && count($image->title) > 1) {
			throw new ParsingException('invalid image element: duplicate title element');
		}

		if (count($image->description) > 1) {
			throw new ParsingException('invalid image element: duplicate description element');
		}

		// Optional width element
		if (isset($image->width)) {
			if (count($image->width) > 1) {
				throw new ParsingException('invalid image element: duplicate width element');
			}

			$imageWidthValidateOption = array( 'options' => array( 'min_range' => 1, 'max_range' => self::IMAGE_MAX_WIDTH ) );

			if ($this->strict && filter_var((string) $image->width, FILTER_VALIDATE_INT, $imageWidthValidateOption) === false) {
				throw new ParsingException(
						'invalid width element: must be a ' .
						'positive integer less than or equal to ' .
						self::IMAGE_MAX_WIDTH
					);
			}
		}

		// Optional height element
		if (isset($image->height)) {
			if (count($image->height) > 1) {
				throw new ParsingException('invalid image element: duplicate height element');
			}

			$imageHeightValidateOption = array( 'options' => array( 'min_range' => 1, 'max_range' => self::IMAGE_MAX_HEIGHT ) );

			if ($this->strict && filter_var((string) $image->height, FILTER_VALIDATE_INT, $imageHeightValidateOption) === false) {
				throw new ParsingException(
						'invalid height element: must be a positive integer ' .
						'less than or equal to ' .
						self::IMAGE_MAX_HEIGHT
					);
			}
		}
	}

	/**
	 * Validate an item element
	 *
	 * Must contain one of each of each of the following:
	 *   description
	 *   link
	 *   title
	 *
	 * May contain one of each of each of the following:
	 *   author
	 *   comments
	 *   guid
	 *   pubDate
	 *   source
	 *
	 * May contain one or more of each of the following:
	 *   enclosure
	 *
	 * @param SimpleXMLElement $item
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateRssChannelItemElement(SimpleXMLElement $item) {
		// Required description element
		if (!isset($item->description)) {
			throw new ParsingException('invalid item element: missing description element');
		} else if (count($item->description) > 1) {
			throw new ParsingException('invalid item element: duplicate description element');
		}

		// Optional enclosure element
		if (isset($item->enclosure)) {
			foreach ($item->enclosure as $enclosure) {
				$this->validateRssChannelItemEnclosureElement($enclosure);
			}
		}

		// Required link element
		if (!isset($item->link)) {
			throw new ParsingException('invalid item element: missing link element');
		} else if (count($item->link) > 1) {
			throw new ParsingException('invalid item element: duplicate link element');
		} else {
			$this->validateRFC2396URL($item->link);
		}

		// Required title element
		if (!isset($item->title)) {
			throw new ParsingException('invalid item element: missing title element');
		} else if (count($item->title) > 1) {
			throw new ParsingException('invalid item element: duplicate title element');
		}

		// Optional author element
		if (isset($item->author)) {
			if (count($item->author) > 1) {
				throw new ParsingException('invalid item element: duplicate author element');
			} else {
				$this->validateContact($item->author);
			}
		}

		// Optional comments element
		if (isset($item->comments)) {
			if (count($item->comments) > 1) {
				throw new ParsingException('invalid item element: duplicate comments element');
			}

			$this->validateRFC2396URL($item->comments);
		}

		// Optional guid element
		if (isset($item->guid)) {
			if (count($item->guid) > 1) {
				throw new ParsingException('invalid item element: duplicate guid element');
			}

			$this->validateRssChannelItemGuidElement($item->guid);
		}

		// Optional pubDate element
		if (isset($item->pubDate)) {
			if (count($item->pubDate) > 1) {
				throw new ParsingException('invalid item element: duplicate pubDate element');
			}

			$this->validateDateTime($item->pubDate);
		}

		// Optional source element
		if (isset($item->source)) {
			if (count($item->source) > 1) {
				throw new ParsingException('invalid item element: duplicate source element');
			}

			if (!isset($item->source['url'])) {
				throw new ParsingException('invalid source element: missing url attribute');
			}

			if (filter_var((string) $item->source['url'], FILTER_VALIDATE_URL) === false) {
				throw new ParsingException('invalid source element: invalid uri');
			}
		}
	}

	/**
	 * Validate a GUID element
	 *
	 * May contain an isPermaLink attribute, which must be true or false.
	 *
	 * If the isPermaLink attribute is ommitted or is set to true, the
	 * value of this element must be a valid URI.
	 *
	 * @param SimpleXMLElement $guid
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateRssChannelItemGuidElement(SimpleXMLElement $guid) {
		if (isset($guid['isPermaLink'])) {
			$isPermalink = strtolower((string) $guid['isPermaLink']);

			if (!in_array($isPermalink, array('true', 'false'))) {
				throw new ParsingException('invalid guid element: invalid isPermaLink attribute');
			}

			$permalink = $isPermalink === 'true';
		} else {
			$permalink = true;
		}

		if ($permalink && filter_var((string) $guid, FILTER_VALIDATE_URL) === false) {
			throw new ParsingException('invalid guid element: invalid uri');
		}
	}

	/**
	 * Validate an enclosure element
	 *
	 * Must contain one of each of the following attributes:
	 *   length
	 *   type
	 *   url
	 *
	 * Furthermore:
	 *   length must be greater than or equal to zero
	 *   type must be a mime type
	 *   url must be a valid URI
	 *
	 * @param SimpleXMLElement $enclosure
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateRssChannelItemEnclosureElement(SimpleXMLElement $enclosure) {
		$lengthValidateOptions = array( 'options' => array( 'min_range' => 0 ) );

		if (!isset($enclosure['length'])) {
			throw new ParsingException('invalid enclosure element: missing length attribute');
		} else if (filter_var((string) $enclosure['length'], FILTER_VALIDATE_INT, $lengthValidateOptions) === false) {
			throw new ParsingException('invalid enclosure element: invalid length attribute');
		}

		if (!isset($enclosure['type'])) {
			throw new ParsingException('invalid enclosure element: missing type attribute');
		} else if (!preg_match(self::MIME_TYPE_RFC2045_REGEX, (string) $enclosure['type'])) {
			throw new ParsingException('invalid enclosure element: invalid type attribute');
		}

		if (!isset($enclosure['url'])) {
			throw new ParsingException('invalid enclosure element: missing url attribute');
		} else if (filter_var((string) $enclosure['url'], FILTER_VALIDATE_URL) === false) {
			throw new ParsingException('invalid enclosure element: invalid url attribute');
		}
	}

	/**
	 * Validate a skipDays element
	 *
	 * Must contain one or more day elements, which have a value of a day of the week.
	 * Valid days are:
	 *   monday
	 *   tuesday
	 *   wednesday
	 *   thursday
	 *   friday
	 *   saturday
	 *   sunday
	 *
	 * Must not contain any other type of elements.
	 *
	 * @param SimpleXMLElement $skipDays
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateRssChannelSkipDaysElement(SimpleXMLElement $skipDays) {
		if (!isset($skipDays->day)) {
			throw new ParsingException('invalid skipDays element: missing day element');
		}

		$validDays = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');

		$foundDays = array();

		foreach ($skipDays->children() as $day) {
			if ($day->getName() !== 'day') {
				throw new ParsingException('invalid skipDays element: must only contain day children');
			}

			$day = strtolower(trim((string) $day));

			if (in_array(strtolower((string) $day), $foundDays, true)) {
				throw new ParsingException('invalid skipDays element: duplicate day element');
			} else {
				array_push($foundDays, $day);
			}

			if (!in_array($day, $validDays, true)) {
				throw new ParsingException('invalid day element');
			}
		}
	}

	/**
	 * Validate a skipHours element
	 *
	 * Must contain one or more hour elements, which have a value between 0 and 23, inclusive.
	 *
	 * Must not contain any other types of elements.
	 *
	 * @param SimpleXMLElement $skipHours
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateRssChannelSkipHoursElement(SimpleXMLElement $skipHours) {
		if (!isset($skipHours->hour)) {
			throw new ParsingException('invalid skipHours element: missing hour element');
		}

		$foundHours = array();

		$validateOption = array('options' => array('min_range' => 0, 'max_range' => 23));

		foreach ($skipHours->children() as $hour) {
			if ($hour->getName() !== 'hour') {
				throw new ParsingException('invalid skipHours element: must only contain hour children');
			}

			if (in_array(intval($hour), $foundHours, true)) {
				throw new ParsingException('invalid skipHours element: duplicate hour element');
			} else {
				array_push($foundHours, intval($hour));
			}

			if (filter_var((string) $hour, FILTER_VALIDATE_INT, $validateOption) === false) {
				throw new ParsingException('invalid hour element: must be an integer between 0 and 23');
			}
		}
	}

	/**
	 * @param SimpleXMLElement $textInput
	 *
	 * Must have one of each of the following:
	 *   title
	 *   name
	 *   link
	 *   description
	 *
	 * Furthermore:
	 *   name may not be blank
	 *   link must be a valid URI
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 */
	private function validateRssChannelTextInputElement(SimpleXMLElement $textInput) {
		if (!isset($textInput->title)) {
			throw new ParsingException('invalid textInput element: missing title element');
		} else if (count($textInput->title) > 1) {
			throw new ParsingException('invalid textInput element: duplicate title element');
		}

		if (!isset($textInput->name)) {
			throw new ParsingException('invalid textInput element: missing name element');
		} else if (count($textInput->name) > 1) {
			throw new ParsingException('invalid textInput element: duplicate name element');
		} else if (strlen((string) $textInput->name) === 0) {
			throw new ParsingException('invalid name element: must not be blank');
		}

		if (!isset($textInput->link)) {
			throw new ParsingException('invalid textInput element: missing link element');
		} else if (count($textInput->link) > 1) {
			throw new ParsingException('invalid textInput element: duplicate link element');
		} else {
			$this->validateRFC2396URL($textInput->link);
		}

		if (!isset($textInput->description)) {
			throw new ParsingException('invalid textInput element: missing description element');
		} else if (count($textInput->description) > 1) {
			throw new ParsingException('invalid textInput element: duplicate description element');
		}

	}

	/**
	 * Validate a feed from a string
	 *
	 * @param $string
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 *
	 * @return void
	 */
	public function validate($string) {
		$xml = simplexml_load_string($string);

		$this->validateRssElement($xml);
	}

	/**
	 * Parse a Feed object from xml
	 *
	 * @param SimpleXMLElement $xml
	 *
	 * @return Feed
	 */
	private function parseFeed(SimpleXMLElement $xml) {
		$title = (string) $xml->channel->title;
		$link = (string) $xml->channel->link;
		$description = (string) $xml->channel->description;

		$feed = new Feed($title, $description, $link);

		if (isset($xml->channel->item)) {
			foreach ($xml->channel->item as $item) {
				$article = $this->parseArticle($item);

				if (!is_null($article)) {
					$feed->addArticle($article);
				}
			}
		}

		if (isset($xml->channel->category)) {
			foreach ($xml->channel->category as $category) {
				$feed->addCategory($this->parseCategory($category));
			}
		}

		if (isset($xml->channel->generator)) {
			$feed->setGenerator((string) $xml->channel->generator);
		}

		if (isset($xml->channel->cloud)) {
			$feed->setSubscription($this->parseSubscription($xml->channel->cloud));
		}

		if (isset($xml->channel->image)) {
			$feed->setLogo($this->parseLogo($xml->channel->image));
		}

		if (isset($xml->channel->managingEditor)) {
			$feed->setEditorContact($this->parseContact($xml->channel->managingEditor));
		}

		if (isset($xml->channel->webMaster)) {
			$feed->setWebmasterContact($this->parseContact($xml->channel->webMaster));
		}

		if (isset($xml->channel->docs)) {
			$feed->setDocumentationURI((string) $xml->channel->docs);
		}

		if (isset($xml->channel->language)) {
			$feed->setLanguage((string) $xml->channel->language);
		}

		if (isset($xml->channel->ttl)) {
			$feed->setCacheTimeToLive(intval($xml->channel->ttl));
		}

		if (isset($xml->channel->pubDate)) {
			$feed->setDatePublished($this->parseDateTime($xml->channel->pubDate));
		}

		if (isset($xml->channel->lastBuildDate)) {
			$feed->setDateUpdated($this->parseDateTime($xml->channel->lastBuildDate));
		}

		if (isset($xml->channel->skipHours)) {
			foreach ($xml->channel->skipHours as $skip) {
				$feed->addSkip(new SkipHour(intval((string) $skip)));
			}
		}

		if (isset($xml->channel->skipDays)) {
			foreach ($xml->channel->skipDays as $skip) {
				$feed->addSkip(new SkipWeekday((string) $skip));
			}
		}

		return $feed;
	}

	/**
	 * @param SimpleXMLElement $contact
	 *
	 * @return Contact
	 */
	private function parseContact(SimpleXMLElement $contact) {
		$contactParts = explode(' ', (string) $contact, 2);

		if (empty($contactParts[1])) {
			return new Contact($contactParts[0]);
		} else {
			return new Contact($contactParts[0], trim($contactParts[1], '()'));
		}
	}

	/**
	 * @param SimpleXMLElement $datetime
	 *
	 * @return DateTime
	 */
	private function parseDateTime(SimpleXMLElement $datetime) {
		return DateTime::createFromFormat(
				DateTime::RSS,
				(string) $datetime
			);
	}

	/**
	 * @param SimpleXMLElement $subscription
	 *
	 * @return Subscription
	 */
	private function parseSubscription(SimpleXMLElement $subscription) {
		return new Subscription(
				$subscription['domain'],
				intval($subscription['port']),
				$subscription['path'],
				$subscription['protocol'],
				$subscription['registerProcedure']
			);
	}

	/**
	 * @param SimpleXMLElement $category
	 *
	 * @return Category
	 */
	private function parseCategory(SimpleXMLElement $category) {
		$object = new Category((string) $category);

		if (!empty($category['domain'])) {
			$object->setTaxonomy((string) $category['domain']);
		}

		return $object;
	}

	/**
	 * @param SimpleXMLElement $source
	 *
	 * @return Article
	 * @throws \imnotjames\Syndicator\Exceptions\InvalidURIException
	 */
	private function parseSource(SimpleXMLElement $source) {
		$article = new Article();

		$article->setTitle((string) $source);
		$article->setURI((string) $source['url']);

		return $article;
	}

	/**
	 * @param SimpleXMLElement $logo
	 *
	 * @return Logo
	 */
	private function parseLogo(SimpleXMLElement $logo) {
		$object = new Logo((string) $logo->url, (string) $logo->title, (string) $logo->link);

		if (isset($logo->description)) {
			$object->setDescription((string) $logo->description);
		}

		if (isset($logo->width)) {
			$object->setWidth(intval((string) $logo->width));
		}

		if (isset($logo->height)) {
			$object->setHeight(intval((string) $logo->height));
		}

		return $object;
	}

	/**
	 * @param SimpleXMLElement $attachment
	 *
	 * @return Link
	 */
	private function parseEnclosure(SimpleXMLElement $attachment) {
		return new Link(
				(string) $attachment['url'],
				Link::TYPE_ENCLOSURE,
				(string) $attachment['type'],
				(string) $attachment['length']
			);
	}

	/**
	 * @param SimpleXMLElement $item
	 *
	 * @return Article
	 * @throws \imnotjames\Syndicator\Exceptions\InvalidURIException
	 */
	private function parseArticle(SimpleXMLElement $item) {
		$article = new Article();

		$article->setTitle((string) $item->title);
		$article->setURI((string) $item->link);
		$article->setDescription((string) $item->description);

		if (isset($item->author)) {
			$article->setAuthor($this->parseContact($item->author));
		}

		if (isset($item->pubDate)) {
			$article->setDatePublished($this->parseDateTime($item->pubDate));
		}

		if (isset($item->category)) {
			foreach ($item->category as $category) {
				$article->addCategory($this->parseCategory($category));
			}
		}

		if (isset($item->enclosure)) {
			foreach ($item->enclosure as $enclosure) {
				$article->addAttachment($this->parseEnclosure($enclosure));
			}
		}

		if (isset($item->guid)) {
			$article->setID((string) $item->guid);
		}

		if (isset($item->source)) {
			$article->setSource($this->parseSource($item->source));
		}

		return $article;
	}

	/**
	 * Parse a Feed from a string
	 *
	 * @param $string
	 *
	 * @throws \imnotjames\Syndicator\Exceptions\ParsingException
	 *
	 * @return Feed
	 */
	public function parse($string) {
		$xml = simplexml_load_string($string);

		$this->validateRssElement($xml);

		return $this->parseFeed($xml);
	}
}