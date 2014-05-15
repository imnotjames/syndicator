<?php

class RSSXMLSerializerTest extends PHPUnit_Framework_TestCase {
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

		$rssXML = $rssXMLSerializer->serialize($feed);

		$rssDOM = dom_import_simplexml(simplexml_load_string($rssXML))->ownerDocument;

		$rssDOM->formatOutput = true;

		$rssXML = $rssDOM->saveXML();

		// Remove whitespace before the tags
		$rssXML = preg_replace('/^\s*/m', '', $rssXML);

		$expectXML = simplexml_load_file('tests/feeds/example.xml');

		$expectDOM = dom_import_simplexml($expectXML)->ownerDocument;

		$expectDOM->formatOutput = true;

		$expectXML = $expectDOM->saveXML();

		// Remove whitespace before the tags
		$expectXML = preg_replace('/^\s*/m', '', $expectXML);

		$this->assertEquals($expectXML, $rssXML);
	}
}