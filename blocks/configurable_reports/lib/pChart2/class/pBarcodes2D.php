<?php 

/*
pBarcodes2D - Wrapper for all the 2D barcode libs
             (QRCode, PDF417, Aztec, DTMX)
Version     : 2.4.0-dev
Made by     : Momchil Bozhinov
Last Update : 09/08/2021
*/

namespace pChart;

define("BARCODES_PDF417_HINT_NUMBERS", 0);
define("BARCODES_PDF417_HINT_TEXT", 1);
define("BARCODES_PDF417_HINT_BINARY", 2);
define("BARCODES_PDF417_HINT_NONE", 3);

define("BARCODES_AZTEC_HINT_BINARY", 0);
define("BARCODES_AZTEC_HINT_DYNAMIC", 1);

define("BARCODES_QRCODE_LEVEL_L", 0);
define("BARCODES_QRCODE_LEVEL_M", 1);
define("BARCODES_QRCODE_LEVEL_Q", 2);
define("BARCODES_QRCODE_LEVEL_H", 3);

define("BARCODES_QRCODE_HINT_NUM", 0);
define("BARCODES_QRCODE_HINT_ALPHANUM", 1);
define("BARCODES_QRCODE_HINT_BYTE", 2);
define("BARCODES_QRCODE_HINT_KANJI", 3);

define("BARCODES_DTMX_PATTERN_SQUARE", 0);
define("BARCODES_DTMX_PATTERN_RECT", 1);

class pBarcodes2D extends Barcodes\pConf {

	private $encoder;
	private $engine;
	private $myPicture;

	public function __construct(string $encoder, pDraw $myPicture)
	{
		$this->encoder = $encoder;
		$this->myPicture = $myPicture;

		try {
			$class = "pChart\\Barcodes\\$encoder\\Encoder";
			$this->engine = new $class;
		} catch (\Throwable $e) {
			throw pException::InvalidInput("Unknown encoding engine");
		}
	}

	private function parse_opts_aztec($opts)
	{
		$defaults = [
			'nobackground' => false,
			'scale' => 3,
			'padding' => 4,
			'hint' => BARCODES_AZTEC_HINT_DYNAMIC,
			'eccPercent' => 33
		];
		$this->apply_user_options($opts, $defaults);

		$this->check_ranges([
			['scale', 1, 20],
			['padding', 0, 20],
			['hint', 0, 1],
			['eccPercent', 1, 100]
		]);
	}

	private function parse_opts_qr($opts)
	{
		$defaults = [
			'nobackground' => false,
			'scale' => 3,
			'padding' => 4,
			'level' => BARCODES_QRCODE_LEVEL_L,
			'hint' => -1,
			'random_mask' => 0
		];
		$this->apply_user_options($opts, $defaults);

		$this->check_ranges([
			['scale', 1, 20],
			['padding', 0, 20],
			['level', 0, 3],
			['hint', -1, 3],
			['random_mask', 0, 8]
		]);
	}

	private function parse_opts_pdf417($opts)
	{
		$defaults = [
			'nobackground' => false,
			'columns' => 6,
			'scale' => 3,
			'ratio' => 3,
			'padding' => 20,
			'securityLevel' => 2,
			'hint' => BARCODES_PDF417_HINT_NONE
		];
		$this->apply_user_options($opts, $defaults);

		$this->check_ranges([
			['columns', 1, 30],
			['scale', 1, 20],
			['ratio', 1, 10],
			['padding', 0, 20],
			['securityLevel', 0, 8],
			['hint', 0, 3]
		]);
	}

	private function parse_opts_dmtx($opts)
	{
		$defaults = [
			'nobackground' => false,
			'scale' => 4,
			'padding' => 4,
			'pattern' => 'square', # rectangular
			'GS-1' => false
		];

		$this->apply_user_options($opts, $defaults);

		$this->check_ranges([
			['scale', 1, 20],
			['padding', 0, 20]
		]);
	}

	public function draw($data, int $x = 10, int $y = 10, array $opts = [])
	{
		switch($this->encoder)
		{
			case BARCODES_ENGINE_AZTEC:
				$this->parse_opts_aztec($opts);
				break;
			case BARCODES_ENGINE_QRCODE:
				$this->parse_opts_qr($opts);
				$this->check_text_valid($data);
				break;
			case BARCODES_ENGINE_PDF417:
				$this->parse_opts_pdf417($opts);
				break;
			case BARCODES_ENGINE_DMTX:
				$this->parse_opts_dmtx($opts);
				break;
		}

		$pixelGrid = $this->engine->encode($data, $this->options);
		$this->myPicture->draw2DBarcode($pixelGrid, $x, $y, $this->options);
	}
}