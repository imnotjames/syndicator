<?php
namespace imnotjames\Syndicator;

/**
 * Representation of how to subscribe to a service for updates.
 *
 * @package imnotjames\Syndicator
 */
class Subscription {
	/**
	 * @var string
	 */
	private $domain;

	/**
	 * @var int
	 */
	private $port;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var string
	 */
	private $protocol;

	/**
	 * @var string|null
	 */
	private $procedure;

	/**
	 * @param string      $domain
	 * @param int         $port
	 * @param string      $path
	 * @param string      $protocol
	 * @param string|null $procedure
	 */
	public function __construct($domain, $port, $path, $protocol, $procedure = null) {
		$this->domain = $domain;
		$this->port = $port;
		$this->path = $path;
		$this->protocol = $protocol;
		$this->procedure = $procedure;
	}

	/**
	 * @return string
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * @return int
	 */
	public function getPort() {
		return $this->port;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function getProtocol() {
		return $this->protocol;
	}

	/**
	 * @return null|string
	 */
	public function getProcedure() {
		return $this->procedure;
	}

	/**
	 * @param string $domain
	 */
	public function setDomain($domain) {
		$this->domain = $domain;
	}

	/**
	 * @param int $port
	 */
	public function setPort($port) {
		$this->port = $port;
	}

	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * @param string $protocol
	 */
	public function setProtocol($protocol) {
		$this->protocol = $protocol;
	}

	/**
	 * @param string|null $procedure
	 */
	public function setProcedure($procedure) {
		$this->procedure = $procedure;
	}
}