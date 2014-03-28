<?php

class FeedTest extends PHPUnit_Framework_TestCase {
	/**
	 * @expectedException \imnotjames\Syndicator\Exceptions\InvalidURIException
	 */
	public function testInvalidURIConstructor() {
		$feed = new \imnotjames\Syndicator\Feed('test', 'test', 'test');
	}

	/**
	 * @expectedException \imnotjames\Syndicator\Exceptions\InvalidURIException
	 */
	public function testInvalidURI() {
		$feed = new \imnotjames\Syndicator\Feed('test', 'test', 'http://example.com/');

		$feed->setURI('test');
	}
}
