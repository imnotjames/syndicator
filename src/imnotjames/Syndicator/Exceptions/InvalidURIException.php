<?php
namespace imnotjames\Syndicator\Exceptions;

use \Exception;

class InvalidURIException extends Exception {
	public function __construct($message = 'invalid URI') {
		parent::__construct($message);
	}
}