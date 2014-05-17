<?php

class RSSXMLParserTest extends PHPUnit_Framework_TestCase {
	/**
	 * @return \Iterator
	 */
	public function getDataSourceInvalidRSS() {
		return new DataProviderIterator(
			new RegexIterator(
				new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator(
						'./tests/feeds/invalid/',
						FilesystemIterator::SKIP_DOTS
					)
				),
				'/\.xml$/i'
			)
		);
	}

	/**
	 * @dataProvider getDataSourceInvalidRSS
	 *
	 * @param $input
	 */
	public function testValidate($input) {
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
}

