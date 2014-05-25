<?php

class RSSXMLSerializerTest extends PHPUnit_Framework_TestCase {
	private function assertXMLEquals($expectXMLFile, $actualXML) {
		$expectDOM = new DOMDocument(1.0);

		$expectDOM->formatOutput = false;

		$expectDOM->preserveWhiteSpace = false;

		$expectDOM->load($expectXMLFile);

		$expectXML = $expectDOM->C14N(true, false);

		$rssDOM = new DOMDocument(1.0);

		$rssDOM->formatOutput = false;

		$rssDOM->preserveWhiteSpace = false;

		$rssDOM->loadXML($actualXML);

		$rssXML = $rssDOM->C14N(true, false);

		$this->assertEquals($expectXML, $rssXML);
	}

	public function testSerialize() {
		$feed = new \imnotjames\Syndicator\Feed('Liftoff News', 'Liftoff to Space Exploration.', 'http://liftoff.msfc.nasa.gov/');

		$feed->setLanguage('en-us');
		$feed->setDatePublished(new DateTime('Tue, 10 Jun 2003 04:00:00 GMT'));
		$feed->setDateUpdated(new DateTime('Tue, 10 Jun 2003 09:41:01 GMT'));

		$article = new \imnotjames\Syndicator\Article('Star City', 'How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.', 'http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp');
		$article->setTitle('Star City');
		$article->setDescription('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.');
		$article->setURI('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp');
		$article->setDatePublished(new \DateTime('Tue, 03 Jun 2003 09:39:21 GMT'));
		$article->setID('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573');

		$feed->addArticle($article);


		$article = new \imnotjames\Syndicator\Article();
		$article->setDescription('Sky watchers in Europe, Asia, and parts of Alaska and Canada will experience a <a href="http://science.nasa.gov/headlines/y2003/30may_solareclipse.htm">partial eclipse of the Sun</a> on Saturday, May 31st.');
		$article->setDatePublished(new \DateTime('Fri, 30 May 2003 11:06:42 GMT'));
		$article->setID('http://liftoff.msfc.nasa.gov/2003/05/30.html#item572');

		$feed->addArticle($article);


		$article = new \imnotjames\Syndicator\Article('Star City', 'How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.', 'http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp');
		$article->setTitle('Astronauts\' Dirty Laundry');
		$article->setURI('http://liftoff.msfc.nasa.gov/news/2003/news-laundry.asp');
		$article->setDescription('Compared to earlier spacecraft, the International Space Station has many luxuries, but laundry facilities are not one of them.  Instead, astronauts have other options.');
		$article->setDatePublished(new \DateTime('Tue, 20 May 2003 08:56:02 GMT'));
		$article->setID('http://liftoff.msfc.nasa.gov/2003/05/20.html#item570');

		$feed->addArticle($article);

		$rssXMLSerializer = new \imnotjames\Syndicator\Serializers\RSSXML();

		$rssXMLSerializer->setGenerator('Weblog Editor 2.0');

		$this->assertXMLEquals('tests/feeds/rss2/example.xml', $rssXMLSerializer->serialize($feed));
	}

	public function testSerializeAdvancedWithArticles() {
		$feed = new \imnotjames\Syndicator\Feed(
			'Test Case',
			'This is a test case',
			'https://github.com/imnotjames/syndicator'
		);

		$article = new \imnotjames\Syndicator\Article('http://example.com/1');

		$article->setTitle('Test article 1');
		$article->setDescription('This is a test article');
		$article->setURI('http://example.org/1');

		$article->setAuthor(new \imnotjames\Syndicator\Contact('foo@example.org', 'User Foo'));

		$article->addCategory(new \imnotjames\Syndicator\Category('Foo', 'http://example.org/foo'));
		$article->addCategory(new \imnotjames\Syndicator\Category('Bar'));

		$article->setDatePublished(new DateTime('Fri, 2 May 2014 12:00:00 EDT'));

		$article->addAttachment(new \imnotjames\Syndicator\Link(
			'http://example.org/foo.ogg',
			\imnotjames\Syndicator\Link::TYPE_ENCLOSURE,
			'audio/vorbis',
			12321
		));

		$article->addAttachment(new \imnotjames\Syndicator\Link(
			'http://example.org/foo.mp3',
			\imnotjames\Syndicator\Link::TYPE_ENCLOSURE,
			'audio/mpeg',
			12321
		));

		$article->addAttachment(new \imnotjames\Syndicator\Link(
			'http://example.org/1/comments',
			\imnotjames\Syndicator\Link::TYPE_COMMENT
		));

		$sourceArticle = new \imnotjames\Syndicator\Article();

		$sourceArticle->setTitle('Test Source');
		$sourceArticle->setURI('http://example.org/source.xml');

		$article->setSource($sourceArticle);

		$feed->addArticle($article);

		$rssXMLSerializer = new \imnotjames\Syndicator\Serializers\RSSXML();

		$this->assertXMLEquals('tests/feeds/rss2/valid/advanced_articles.xml', $rssXMLSerializer->serialize($feed));
	}
}