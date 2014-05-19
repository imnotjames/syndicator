<?php
namespace imnotjames\Syndicator;

class Contact {
	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $uri;

	/**
	 * @param      $email
	 * @param null $name
	 * @param null $uri
	 */
	public function __construct($email, $name = null, $uri = null) {
		$this->email = $email;
		$this->name = $name;
		$this->uri = $uri;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string|null
	 */
	public function getURI() {
		return $this->uri;
	}

	/**
	 * @param $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @param $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param $uri
	 */
	public function setURI($uri) {
		$this->uri = $uri;
	}
}