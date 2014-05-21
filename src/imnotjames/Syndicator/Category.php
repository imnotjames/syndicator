<?php
namespace imnotjames\Syndicator;

/**
 * The category for a feed
 *
 * @package imnotjames\Syndicator
 */
class Category {
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string|null
	 */
	private $taxonomy;

	public function __construct($name, $taxonomy = null) {
		$this->name = $name;
		$this->taxonomy = $taxonomy;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return null
	 */
	public function getTaxonomy() {
		return $this->taxonomy;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param $taxonomy
	 */
	public function setTaxonomy($taxonomy) {
		$this->taxonomy = $taxonomy;
	}
}