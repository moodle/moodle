<?php

namespace Httpful\Exception;


class ConnectionErrorException extends \Exception {


	/**
	 * @var string
	 */
	private $curlErrorNumber;

	/**
	 * @var string
	 */
	private $curlErrorString;

	/**
	 * @return string
	 */
	public function getCurlErrorNumber() {
		return $this->curlErrorNumber;
	}

	/**
	 * @param string $curlErrorNumber
	 * @return $this
	 */
	public function setCurlErrorNumber($curlErrorNumber) {
		$this->curlErrorNumber = $curlErrorNumber;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCurlErrorString() {
		return $this->curlErrorString;
	}

	/**
	 * @param string $curlErrorString
	 * @return $this
	 */
	public function setCurlErrorString($curlErrorString) {
		$this->curlErrorString = $curlErrorString;

		return $this;
	}


}