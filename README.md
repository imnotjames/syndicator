## Syndicator

### Basic library to parse and compose syndication feeds.

[![Build Status](https://travis-ci.org/imnotjames/syndicator.svg?branch=master)](https://travis-ci.org/imnotjames/syndicator)
[![Coverage Status](https://img.shields.io/coveralls/imnotjames/syndicator.svg)](https://coveralls.io/r/imnotjames/syndicator)

Currently Supported Standards:

* RSS XML 2.0

For any others feel free to open an [issue](https://github.com/imnotjames/syndicator/issues).

## Installation

The easiest method of installation is with [composer](http://getcomposer.org), on [packagist](https://packagist.org/packages/imnotjames/syndicator).

## Usage

Composition of Syndication feeds:

```php
use imnotjames\Syndicator\Article;
use imnotjames\Syndicator\Feed;
use imnotjames\Syndicator\Logo;
use imnotjames\Syndicator\Serializers\RSSXML;

$feed = new Feed(
	'My Rad Feed',
	'This feed is the coolest feed out there.',
	'https://github.com/imnotjames/syndicator'
);

$feed->setLogo(new Logo('http://i.imgur.com/haED2k7.jpg'));

$feed->setGenerator('Coolness Generator v95000');

$feed->setCacheTimeToLive(42);

$article = new Article();

$article->setTitle('How to be cool');
$article->setDescription('Step 1.  Be cool.  Step 2.  Don\'t be not cool.');
$article->setURI('http://example.com/blog/how-to-be-cool');

$feed->addArticle($article);

$serializer = new RSSXML();

$xml = $serializer->serialize($feed);
```

And decomposition:
```php
use imnotjames\Syndicator\Parsers\RSSXML;

$parser = new RSSXML();

$feed = $parser->parse(file_get_contents('awesome_feed.xml'));

printf("Title: %s\n\n", $feed->getTitle());

echo "Articles:\n";

foreach ($feed as $article) {
	printf("\tTitle: %s", $article->getTitle());
	echo "\n";
}
```

## License

[MIT License](http://opensource.org/licenses/MIT)
