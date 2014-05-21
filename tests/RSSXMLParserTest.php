<?php

use imnotjames\Syndicator\Contact;
use imnotjames\Syndicator\Category;

class RSSXMLParserTest extends PHPUnit_Framework_TestCase {
	/**
	 * @param $directory
	 *
	 * @return \Iterator
	 */
	public function getDataSourceRSS($directory) {
		return new DataProviderIterator(
			new RegexIterator(
				new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator(
						$directory,
						FilesystemIterator::SKIP_DOTS
					)
				),
				'/\.xml$/i'
			)
		);
	}

	/**
	 * @return \Iterator
	 */
	public function getDataSourceInvalidRSS() {
		return $this->getDataSourceRSS('./tests/feeds/invalid/');
	}

	/**
	 * @return \Iterator
	 */
	public function getDataSourceValidRSS() {
		return $this->getDataSourceRSS('./tests/feeds/valid/');
	}

	/**
	 * @dataProvider getDataSourceInvalidRSS
	 *
	 * @param $input
	 */
	public function testValidateInvalid($input) {
		$xml = file_get_contents($input);

		$expectedException = '\imnotjames\Syndicator\Exceptions\ParsingException';

		if (preg_match('/<!-- exception: (.+) -->/', $xml, $expectedExceptionMatches)) {
			$expectedException = $expectedExceptionMatches[1];
		}

		if (preg_match('/<!-- exception message: (.+) -->/', $xml, $expectedExceptionMessageMatches)) {
			$expectedExceptionMessage = $expectedExceptionMessageMatches[1];
		}

		if (!empty($expectedExceptionMessage)) {
			$this->setExpectedException($expectedException, $expectedExceptionMessage);
		} else {
			$this->setExpectedException($expectedException);
		}

		$parser = new \imnotjames\Syndicator\Parsers\RSSXML();

		$parser->validate($xml);
	}


	/**
	 * @dataProvider getDataSourceValidRSS
	 *
	 * @param $input
	 */
	public function testValidateValid($input) {
		$xml = file_get_contents($input);

		$parser = new \imnotjames\Syndicator\Parsers\RSSXML();

		$parser->validate($xml);
	}

	public function testParseBasic() {
		$xml = file_get_contents('./tests/feeds/valid/basic.xml');

		$parser = new \imnotjames\Syndicator\Parsers\RSSXML();

		$feed = $parser->parse($xml);

		$this->assertNotNull($feed);

		$this->assertEquals('Test Case', $feed->getTitle());
		$this->assertEquals('https://github.com/imnotjames/syndicator', $feed->getURI());
		$this->assertEquals('This is a test case', $feed->getDescription());
	}

	public function testParseBasicWithArticles() {
		$xml = file_get_contents('./tests/feeds/valid/basic_articles.xml');

		$parser = new \imnotjames\Syndicator\Parsers\RSSXML();

		$feed = $parser->parse($xml);

		$this->assertNotNull($feed);

		$this->assertEquals('Test Case', $feed->getTitle());
		$this->assertEquals('https://github.com/imnotjames/syndicator', $feed->getURI());
		$this->assertEquals('This is a test case', $feed->getDescription());

		$this->assertCount(2, $feed->getArticles());

		$titles = array();
		$uris = array();
		$descriptions = array();

		foreach ($feed->getArticles() as $article) {
			$titles[] = $article->getTitle();
			$uris[] = $article->getURI();
			$descriptions[] = $article->getDescription();
		}

		$this->assertContains('Test article 1', $titles);
		$this->assertContains('Test article 2', $titles);

		$this->assertContains('http://example.com/1', $uris);
		$this->assertContains('http://example.com/2', $uris);

		$this->assertContains('This is a test article', $descriptions);
		$this->assertContains('This is also a test article', $descriptions);
	}

	public function testParseAdvanced() {
		$xml = file_get_contents('./tests/feeds/valid/advanced.xml');

		$parser = new \imnotjames\Syndicator\Parsers\RSSXML();

		$feed = $parser->parse($xml);

		$this->assertNotNull($feed);

		$this->assertEquals('Test Case', $feed->getTitle());
		$this->assertEquals('https://github.com/imnotjames/syndicator', $feed->getURI());
		$this->assertEquals('This is a test case', $feed->getDescription());

		$this->assertEquals(new \DateTime('Fri, 2 May 2014 12:00:00 EDT'), $feed->getDatePublished());
		$this->assertEquals(new \DateTime('Fri, 16 May 2014 12:00:00 EDT'), $feed->getDateUpdated());

		$expectSubscription = new \imnotjames\Syndicator\Subscription(
				'http://example.com',
				80,
				'/rpc',
				'xml-rpc',
				'foo'
			);

		$this->assertEquals($expectSubscription, $feed->getSubscription());

		$expectedLogo = new \imnotjames\Syndicator\Logo('http://example.com/example.gif');
		$expectedLogo->setTitle('Test Case');
		$expectedLogo->setLink('https://github.com/imnotjames/syndicator');
		$expectedLogo->setDescription('This is a test case image');
		$expectedLogo->setWidth(12);
		$expectedLogo->setHeight(34);

		$this->assertEquals($expectedLogo, $feed->getLogo());

		$this->assertEquals(
				new Contact('foo@example.com', 'Boodley bop de Bop'),
				$feed->getEditorContact()
			);

		$this->assertEquals(
				new Contact('webmaster@example.com', 'Webley Masterson'),
				$feed->getWebmasterContact()
			);

		$this->assertEquals('http://example.com/rss/docs', $feed->getDocumentationURI());

		$this->assertEquals('en-us', $feed->getLanguage());

		$this->assertEquals(64, $feed->getCacheTimeToLive());

		$this->assertCount(2, $feed->getCategories());

		$this->assertContainsOnly(
				'\imnotjames\Syndicator\Category',
				$feed->getCategories()
			);

		$this->assertContains(
				new Category('Bar'),
				$feed->getCategories(),
				'',
				false,
				false
			);

		$this->assertContains(
				new Category('Foo', 'http://example.org/foo'),
				$feed->getCategories(),
				'',
				false,
				false
			);
	}
}

