<?php
/*
pConf - class to help standardise the barcode libs

Version     : 2.4.0-dev
Made by     : Momchil Bozhinov
Last Update : 27/07/2021

This file can be distributed under MIT
*/

namespace pChart\Barcodes;

use pChart\pColor;
use pChart\pException;

class pConf {

	protected $options = [];

	public function apply_user_options(array $opts, array $defaults)
	{
		$this->options += array_replace_recursive($defaults, $opts);
		$this->set_color('bgColor', 255);
		$this->set_color('color', 0);
	}

	public function set_color(string $value, int $default)
	{
		if (!isset($this->options['palette'][$value])) {
			$this->options['palette'][$value] = new pColor($default);
		} else {
			if (!($this->options['palette'][$value] instanceof pColor)) {
				throw pException::InvalidInput("Invalid value for $value. Expected a pColor object.");
			}
		}
	}

	public function check_text_valid($text)
	{
		if($text == '\0' || $text == '') {
			throw pException::InvalidInput("Invalid value for text");
		}
	}

	public function check_ranges(array $conf)
	{
		foreach($conf as $c){
			$ret = $this->options[$c[0]];
			if (!is_numeric($ret) || $ret < $c[1] || $ret > $c[2]) {
				throw pException::InvalidInput("Invalid value for ".$c[0]);
			}
		}
	}
}