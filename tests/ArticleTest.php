<?php

class ArticleTest extends PHPUnit_Framework_TestCase {
	public function testCreateArticle() {
		$expectTitle = 'Title';
		$expectDescription = 'Description';
		$expectURI = 'http://example.org/example?example=example';

		$article = new \imnotjames\Syndicator\Article();

		$article->setTitle($expectTitle);
		$article->setDescription($expectDescription);
		$article->setURI($expectURI);

		$this->assertEquals($article->getTitle(), $expectTitle);
		$this->assertEquals($article->getDescription(), $expectDescription);
		$this->assertEquals($article->getURI(), $expectURI);
	}

	/**
	 * @expectedException \imnotjames\Syndicator\Exceptions\InvalidURIException
	 */
	public function testInvalidUri() {
		$article = new \imnotjames\Syndicator\Article();

		$article->setURI('test');
	}
}
