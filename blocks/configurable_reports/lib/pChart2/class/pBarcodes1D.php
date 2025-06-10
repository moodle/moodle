<?php

namespace pChart;

class pBarcodes1D extends Barcodes\pConf {

	private $encoder;
	private $engine;
	private $myPicture;
	private $defaults = [
			'scale' => 1,
			'ratio' => 1,
			'padding' => 0,
			'nobackground' => false,
			'width' => NULL,
			'height' => NULL,
			'widths' => [
				'QuietArea' 	=> 1,
				'NarrowModules' => 1,
				'WideModules' 	=> 3,
				'NarrowSpace' 	=> 1
				],
			"label" => [
					'height' => 10,
					'size' => 1,
					'color' => NULL,
					'skip' => FALSE,
					'ttf' => NULL,
					'offset' => 0
				]
		];

	public function __construct(string $encoder, pDraw $myPicture)
	{
		$this->encoder = $encoder;
		$this->myPicture = $myPicture;

		try {
			$class = "pChart\\Barcodes\\Linear\\$encoder";
			$this->engine = new $class;
		} catch (\Throwable $e) {
			throw pException::InvalidInput("Unknown encoding engine");
		}
	}

	private function parse_opts($opts)
	{
		switch ($this->encoder){
			case BARCODES_ENGINE_CODE128:
				$defaults = $this->prep_opts_128();
				break;
			case BARCODES_ENGINE_UPC:
			case BARCODES_ENGINE_MSI:
			case BARCODES_ENGINE_IMB:
			case BARCODES_ENGINE_CODE39:
			case BARCODES_ENGINE_CODE93:
			case BARCODES_ENGINE_PHARMA:
			case BARCODES_ENGINE_POSTNET:
			case BARCODES_ENGINE_RMS4CC:
			case BARCODES_ENGINE_EANEXT:
			case BARCODES_ENGINE_B2OF5:
				$defaults = $this->prep_opts_mode();
				break;
			default:
				$defaults = $this->defaults;
		}

		$this->apply_user_options($opts, $defaults);

		$this->check_ranges([
			['scale', 1, 20],
			['ratio', 1, 20],
			['padding', 0, 20]
		]);

		if (is_null($this->options['label']['color']) && (!$this->options['label']['skip'])){
			$this->options['label']['color'] = $this->options['palette']['color'];
		}
	}

	private function prep_opts_mode()
	{
		$defaults = $this->defaults;
		$defaults['mode'] = "";
		return $defaults;
	}

	private function prep_opts_128()
	{
		$defaults = $this->defaults;
		$defaults['mode'] = "";
		$defaults['GS-1'] = false;
		return $defaults;
	}

	public function draw($data, int $x, int $y, array $opts = [])
	{
		/*
		BARCODES_ENGINE_UPC : 
			'upca' = ['mode' => 'upca']; 
			'upce' = ['mode' => 'upce']; 
			'ean13nopad' = ['mode' => 'ean13nopad']; 
			'ean13pad'   = ['mode' => 'ean13pad'];
			'ean13' = ['mode' => 'ean13'];
			'ean8'  = ['mode' => 'ean8']; 

		BARCODES_ENGINE_CODE39
			'code39' = []; // CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
			'code39+' = ['mode' => '+'];  // CODE 39 with checksum
			'code39E' = ['mode' => 'E'];  // CODE 39 EXTENDED
			'code39E+' = ['mode' => 'E+']; // CODE 39 EXTENDED + CHECKSUM

		BARCODES_ENGINE_CODE93
			'code93' = []; 
			'code93E' = ['mode' => 'E']; // CODE 93 EXTENDED

		BARCODES_ENGINE_CODE128
			'code128' =  ['GS-1' => false, 'mode' => ""]; 
			'code128a' = ['GS-1' => false, 'mode' => "a"];
			'code128b' = ['GS-1' => false, 'mode' => "b"];
			'code128c' = ['GS-1' => false, 'mode' => "c"];
			'code128ac' =['GS-1' => false, 'mode' => "ac"];
			'code128bc' =['GS-1' => false, 'mode' => "bc"];
			'GS1-128' =  ['GS-1' => true,  'mode' => ""];
			'GS1-128a' = ['GS-1' => true,  'mode' => "a"];
			'GS1-128b' = ['GS-1' => true,  'mode' => "b"];
			'GS1-128c' = ['GS-1' => true,  'mode' => "c"];
			'GS1-128ac' =['GS-1' => true,  'mode' => "ac"];
			'GS1-128bc' =['GS-1' => true,  'mode' => "bc"];

		BARCODES_ENGINE_CODABAR
			'codabar' = [];

		BARCODES_ENGINE_ITF
			'itf' = []
			'itf14' = []

		BARCODES_ENGINE_CODE11
			'code11' = []

		BARCODES_ENGINE_PHARMA
			'pharma' = []
			'pharma 2T' = ['mode' => '2T']

		BARCODES_ENGINE_MSI
			'MSI' = []
			'MSI+' = ['mode' => '+']

		BARCODES_ENGINE_POSTNET
			'postnet' = []
			'planet' = ['mode' => 'planet']

		BARCODES_ENGINE_RMS4CC
			'rms4cc' = []
			'KIX' = ['mode' => 'KIX']

		BARCODES_ENGINE_2OF5
			'I25'  = ['mode' => 'Standard']
			'I25+' = ['mode' => 'Standard+']
			'S25'  = ['mode' => 'Interleaved']
			'S25+' = ['mode' => 'Interleaved+']

		BARCODES_ENGINE_IMB
			'IMB' = []
			'IMB_PRE' = ['mode' => 'PRE']

		BARCODES_ENGINE_EANEXT
			'EAN5' = ['mode' => EAN5]
			'EAN2' => []
		*/

		$this->parse_opts($opts);
		$code = $this->engine->encode($data, $this->options);
		$this->myPicture->draw1DBarcode($code, $x, $y, $this->options);
	}
}
