<?php

class RSSXMLParserTest extends PHPUnit_Framework_TestCase {
	/**
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

		$this->assertEquals('Test Case', $feed->getTitle());
		$this->assertEquals('https://github.com/imnotjames/syndicator', $feed->getURI());
		$this->assertEquals('This is a test case', $feed->getDescription());
	}

	public function testParseBasicWithArticles() {
		$xml = file_get_contents('./tests/feeds/valid/basic_articles.xml');

		$parser = new \imnotjames\Syndicator\Parsers\RSSXML();

		$feed = $parser->parse($xml);

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
}

