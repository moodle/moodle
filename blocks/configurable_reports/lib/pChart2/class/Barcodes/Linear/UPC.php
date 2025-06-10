<?php

namespace pChart\Barcodes\Linear;

use pChart\pException;

class UPC {

	public function encode(string $code, array $opts)
	{
		if (!preg_match('/^[\d]+$/', $code)){
			throw pException::InvalidInput("Text can not be encoded");
		}

		die("BROKEN");

		switch (strtolower($opts['mode'])){
			case "upca":
				return $this->upc_a_encode($code);
			case "upce":
				var_dump($this->upc_e_encode($code));
				die();
				return $this->upc_e_encode($code);
			case "ean13nopad":
				return $this->ean_13_encode($code, ' ');
			case "ean13pad":
			case "ean13":
				return $this->ean_13_encode($code, '>');
			case "ean8":
				return $this->ean_8_encode($code);
			default: 
				throw pException::InvalidInput("Unknown UPS encode method");
		}
	}

}
