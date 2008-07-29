<?php
//============================================================+
// File name   : barcodes.php
// Begin       : 2008-06-09
// Last Update : 2008-07-16
// Version     : 1.0.002
// License     : GNU LGPL (http://www.gnu.org/copyleft/lesser.html)
// 	----------------------------------------------------------------------------
//  Copyright (C) 2008  Nicola Asuni - Tecnick.com S.r.l.
// 	
// 	This program is free software: you can redistribute it and/or modify
// 	it under the terms of the GNU Lesser General Public License as published by
// 	the Free Software Foundation, either version 2.1 of the License, or
// 	(at your option) any later version.
// 	
// 	This program is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU Lesser General Public License for more details.
// 	
// 	You should have received a copy of the GNU Lesser General Public License
// 	along with this program.  If not, see <http://www.gnu.org/licenses/>.
// 	
// 	See LICENSE.TXT file for more information.
//  ----------------------------------------------------------------------------
//
// Description : PHP class to creates array representations for 
//               common 1D barcodes to be used with TCPDF.
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com S.r.l.
//               Via della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * PHP class to creates array representations for common 1D barcodes to be used with TCPDF.
 * @package com.tecnick.tcpdf
 * @abstract Functions for generating string representation of common 1D barcodes.
 * @author Nicola Asuni
 * @copyright 2008 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://www.tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version 1.0.001
 */

	/**
	* PHP class to creates array representations for common 1D barcodes to be used with TCPDF (http://www.tcpdf.org).<br>
	* @name TCPDFBarcode
	* @package com.tecnick.tcpdf
	* @version 1.0.001
	* @author Nicola Asuni
	* @link http://www.tcpdf.org
	* @license http://www.gnu.org/copyleft/lesser.html LGPL
	*/
class TCPDFBarcode {
	
	/**
	 * @var array representation of barcode.
	 * @access protected
	 */
	protected $barcode_array;
		
	/**
	 * This is the class constructor. 
	 * Return an array representations for common 1D barcodes:<ul>
	 * <li>$arrcode["code"] code to be printed on text label</li>
	 * <li>$arrcode["maxh"] max bar height</li>
	 * <li>$arrcode["maxw"] max bar width</li>
	 * <li>$arrcode["bcode"][$k] single bar or space in $k position</li>
	 * <li>$arrcode["bcode"][$k]["t"] bar type: true = bar, false = space.</li>
	 * <li>$arrcode["bcode"][$k]["w"] bar width in units.</li>
	 * <li>$arrcode["bcode"][$k]["h"] bar height in units.</li>
	 * <li>$arrcode["bcode"][$k]["p"] bar top position (0 = top, 1 = middle)</li></ul>
	 * @param string $code code to print
 	 * @param string $type type of barcode: <ul><li>C39 : CODE 39</li><li>C39+ : CODE 39 with checksum</li><li>C39E : CODE 39 EXTENDED</li><li>C39E+ : CODE 39 EXTENDED with checksum</li><li>I25 : Interleaved 2 of 5</li><li>C128A : CODE 128 A</li><li>C128B : CODE 128 B</li><li>C128C : CODE 128 C</li><li>EAN13 : EAN 13</li><li>UPCA : UPC-A</li><li>POSTNET : POSTNET</li><li>CODABAR : CODABAR</li></ul>
	 */
	public function __construct($code, $type) {
		$this->setBarcode($code, $type);
	}
	
	/** 
	 * Return an array representations of barcode.
 	 * @return array
	 */
	public function getBarcodeArray() {
		return $this->barcode_array;
	}
	
	/** 
	 * Set the barcode.
	 * @param string $code code to print
 	 * @param string $type type of barcode: <ul><li>C39 : CODE 39</li><li>C39+ : CODE 39 with checksum</li><li>C39E : CODE 39 EXTENDED</li><li>C39E+ : CODE 39 EXTENDED with checksum</li><li>I25 : Interleaved 2 of 5</li><li>C128A : CODE 128 A</li><li>C128B : CODE 128 B</li><li>C128C : CODE 128 C</li><li>EAN13 : EAN 13</li><li>UPCA : UPC-A</li><li>POSTNET : POSTNET</li><li>CODABAR : CODABAR</li></ul>
 	 * @return array
	 */
	public function setBarcode($code, $type) {
		switch (strtoupper($type)) {
			case "C39": { // CODE 39
				$arrcode = $this->barcode_code39($code, false, false);
				break;
			}
			case "C39+": { // CODE 39 with checksum
				$arrcode = $this->barcode_code39($code, false, true);
				break;
			}
			case "C39E": { // CODE 39 EXTENDED
				$arrcode = $this->barcode_code39($code, true, false);
				break;
			}
			case "C39E+": { // CODE 39 EXTENDED with checksum
				$arrcode = $this->barcode_code39($code, true, true);
				break;
			}
			case "I25": { // Interleaved 2 of 5
				$arrcode = $this->barcode_i25($code);
				break;
			}
			case "C128A": { // CODE 128 A
				$arrcode = $this->barcode_c128($code, "A");
				break;
			}
			case "C128B": { // CODE 128 B
				$arrcode = $this->barcode_c128($code, "B");
				break;
			}
			case "C128C": { // CODE 128 C
				$arrcode = $this->barcode_c128($code, "C");
				break;
			}
			case "EAN13": { // EAN 13
				$arrcode = $this->barcode_ean13($code, 13);
				break;
			}
			case "UPCA": { // UPC-A
				$arrcode = $this->barcode_ean13($code, 12);
				break;
			}
			case "POSTNET": { // POSTNET
				$arrcode = $this->barcode_postnet($code);
				break;
			}
			case "CODABAR": { // CODABAR
				$arrcode = $this->barcode_codabar($code);
				break;
			}
			default: {
				$this->barcode_array = false;
			}
		}
		$this->barcode_array = $arrcode;
	}
	
	/**
	 * CODE 39
	 * @param string $code code to represent.
	 * @param boolean $checksum if true add a checksum to the code
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_code39($code, $extended=false, $checksum=false) {
		$chr['0'] = '111221211';
		$chr['1'] = '211211112';
		$chr['2'] = '112211112';
		$chr['3'] = '212211111';
		$chr['4'] = '111221112';
		$chr['5'] = '211221111';
		$chr['6'] = '112221111';
		$chr['7'] = '111211212';
		$chr['8'] = '211211211';
		$chr['9'] = '112211211';
		$chr['A'] = '211112112';
		$chr['B'] = '112112112';
		$chr['C'] = '212112111';
		$chr['D'] = '111122112';
		$chr['E'] = '211122111';
		$chr['F'] = '112122111';
		$chr['G'] = '111112212';
		$chr['H'] = '211112211';
		$chr['I'] = '112112211';
		$chr['J'] = '111122211';
		$chr['K'] = '211111122';
		$chr['L'] = '112111122';
		$chr['M'] = '212111121';
		$chr['N'] = '111121122';
		$chr['O'] = '211121121';
		$chr['P'] = '112121121';
		$chr['Q'] = '111111222';
		$chr['R'] = '211111221';
		$chr['S'] = '112111221';
		$chr['T'] = '111121221';
		$chr['U'] = '221111112';
		$chr['V'] = '122111112';
		$chr['W'] = '222111111';
		$chr['X'] = '121121112';
		$chr['Y'] = '221121111';
		$chr['Z'] = '122121111';
		$chr['-'] = '121111212';
		$chr['.'] = '221111211';
		$chr[' '] = '122111211';
		$chr['*'] = '121121211';
		$chr['$'] = '121212111';
		$chr['/'] = '121211121';
		$chr['+'] = '121112121';
		$chr['%'] = '111212121';
		
		$code = strtoupper($code);
		if ($extended) {
			// extended mode
			$code = $this->encode_code39_ext($code);
		}
		if ($code === false) {
			return false;
		}
		if ($checksum) {
			// checksum
			$code .= $this->checksum_code39($code);
		}
		// add start and stop codes
		$code = "*".$code."*";
		
		$bararray = array("code" => $code, "maxw" => 0, "maxh" => 1, "bcode" => array());
		$k = 0;
		for($i=0; $i < strlen($code); $i++) {
			$char = $code{$i};
			if(!isset($chr[$char])) {
				// invalid character
				return false;
			}
			for($j=0; $j < 9; $j++) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$w = $chr[$char]{$j};
				$bararray["bcode"][$k] = array("t" => $t, "w" => $w, "h" => 1, "p" => 0);
				$bararray["maxw"] += $w;
				$k++;
			}
			$bararray["bcode"][$k] = array("t" => false, "w" => 1, "h" => 1, "p" => 0);
			$bararray["maxw"] += 1;
			$k++;
		}
		return $bararray;
	}
	
	/**
	 * Encode a string to be used for CODE 39 Extended mode.
	 * @param string $code code to represent.
	 * @return encoded string.
	 * @access protected
	 */
	protected function encode_code39_ext($code) {
		$encode = array(
			chr(0) => '%U', chr(1) => '$A', chr(2) => '$B', chr(3) => '$C',
			chr(4) => '$D', chr(5) => '$E', chr(6) => '$F', chr(7) => '$G',
			chr(8) => '$H', chr(9) => '$I', chr(10) => '$J', chr(11) => '£K',
			chr(12) => '$L', chr(13) => '$M', chr(14) => '$N', chr(15) => '$O',
			chr(16) => '$P', chr(17) => '$Q', chr(18) => '$R', chr(19) => '$S',
			chr(20) => '$T', chr(21) => '$U', chr(22) => '$V', chr(23) => '$W',
			chr(24) => '$X', chr(25) => '$Y', chr(26) => '$Z', chr(27) => '%A',
			chr(28) => '%B', chr(29) => '%C', chr(30) => '%D', chr(31) => '%E',
			chr(32) => ' ', chr(33) => '/A', chr(34) => '/B', chr(35) => '/C',
			chr(36) => '/D', chr(37) => '/E', chr(38) => '/F', chr(39) => '/G',
			chr(40) => '/H', chr(41) => '/I', chr(42) => '/J', chr(43) => '/K',
			chr(44) => '/L', chr(45) => '-', chr(46) => '.', chr(47) => '/O',
			chr(48) => '0', chr(49) => '1', chr(50) => '2', chr(51) => '3',
			chr(52) => '4', chr(53) => '5', chr(54) => '6', chr(55) => '7',
			chr(56) => '8', chr(57) => '9', chr(58) => '/Z', chr(59) => '%F',
			chr(60) => '%G', chr(61) => '%H', chr(62) => '%I', chr(63) => '%J',
			chr(64) => '%V', chr(65) => 'A', chr(66) => 'B', chr(67) => 'C',
			chr(68) => 'D', chr(69) => 'E', chr(70) => 'F', chr(71) => 'G',
			chr(72) => 'H', chr(73) => 'I', chr(74) => 'J', chr(75) => 'K',
			chr(76) => 'L', chr(77) => 'M', chr(78) => 'N', chr(79) => 'O',
			chr(80) => 'P', chr(81) => 'Q', chr(82) => 'R', chr(83) => 'S',
			chr(84) => 'T', chr(85) => 'U', chr(86) => 'V', chr(87) => 'W',
			chr(88) => 'X', chr(89) => 'Y', chr(90) => 'Z', chr(91) => '%K',
			chr(92) => '%L', chr(93) => '%M', chr(94) => '%N', chr(95) => '%O',
			chr(96) => '%W', chr(97) => '+A', chr(98) => '+B', chr(99) => '+C',
			chr(100) => '+D', chr(101) => '+E', chr(102) => '+F', chr(103) => '+G',
			chr(104) => '+H', chr(105) => '+I', chr(106) => '+J', chr(107) => '+K',
			chr(108) => '+L', chr(109) => '+M', chr(110) => '+N', chr(111) => '+O',
			chr(112) => '+P', chr(113) => '+Q', chr(114) => '+R', chr(115) => '+S',
			chr(116) => '+T', chr(117) => '+U', chr(118) => '+V', chr(119) => '+W',
			chr(120) => '+X', chr(121) => '+Y', chr(122) => '+Z', chr(123) => '%P',
			chr(124) => '%Q', chr(125) => '%R', chr(126) => '%S', chr(127) => '%T');
		$code_ext = '';
		for ($i = 0 ; $i < strlen($code); $i++) {
			if (ord($code{$i}) > 127) {
				return false;
			}
			$code_ext .= $encode[$code{$i}];
		}
		return $code_ext;
	}
	
	/**
	 * Calculate CODE 39 checksum (modulo 43).
	 * @param string $code code to represent.
	 * @return char checksum.
	 * @access protected
	 */
	protected function checksum_code39($code) {
		$chars = array(
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
			'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
			'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%');
		$sum = 0;
		for ($i=0 ; $i < strlen($code); $i++) {
			$k = array_keys($chars, $code{$i});
			$sum += $k[0];
		}
		$j = ($sum % 43);
		return $chars[$j];
	}
	
	/**
	 * Interleaved 2 of 5 barcodes. 
	 * Contains digits (0 to 9) and encodes the data in the width of both bars and spaces.
	 * @param string $code code to represent.
	 * @param boolean $checksum if true add a checksum to the code
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_i25($code) {
		$chr['0'] = '11221';
		$chr['1'] = '21112';
		$chr['2'] = '12112';
		$chr['3'] = '22111';
		$chr['4'] = '11212';
		$chr['5'] = '21211';
		$chr['6'] = '12211';
		$chr['7'] = '11122';
		$chr['8'] = '21121';
		$chr['9'] = '12121';
		$chr['A'] = '11';
		$chr['Z'] = '21';
		
		if((strlen($code) % 2) != 0) {
			// add leading zero if code-length is odd
			$code = '0'.$code;
		}
		// add start and stop codes
		$code = 'AA'.strtolower($code).'ZA';
			
		$bararray = array("code" => $code, "maxw" => 0, "maxh" => 1, "bcode" => array());
		$k = 0;
		for($i=0; $i < strlen($code); $i=$i+2) {
			$char_bar = $code{$i};
			$char_space = $code{$i+1};
			if((!isset($chr[$char_bar])) OR (!isset($chr[$char_space]))) {
				// invalid character
				return false;
			}
			// create a bar-space sequence
			$seq = "";
			for($s=0; $s < strlen($chr[$char_bar]); $s++){
				$seq .= $chr[$char_bar]{$s} . $chr[$char_space]{$s};
			}
			for($j=0; $j < strlen($seq); $j++) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$w = $seq{$j};
				$bararray["bcode"][$k] = array("t" => $t, "w" => $w, "h" => 1, "p" => 0);
				$bararray["maxw"] += $w;
				$k++;
			}
		}
		return $bararray;
	}
	
	/**
	 * C128 barcodes. 
	 * 
	 * @param string $code code to represent.
	 * @param string $type barcode type: A, B or C
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_c128($code, $type="B") {
		$chr = array(
			'212222', /* 00 */
			'222122', /* 01 */
			'222221', /* 02 */
			'121223', /* 03 */
			'121322', /* 04 */
			'131222', /* 05 */
			'122213', /* 06 */
			'122312', /* 07 */
			'132212', /* 08 */
			'221213', /* 09 */
			'221312', /* 10 */
			'231212', /* 11 */
			'112232', /* 12 */
			'122132', /* 13 */
			'122231', /* 14 */
			'113222', /* 15 */
			'123122', /* 16 */
			'123221', /* 17 */
			'223211', /* 18 */
			'221132', /* 19 */
			'221231', /* 20 */
			'213212', /* 21 */
			'223112', /* 22 */
			'312131', /* 23 */
			'311222', /* 24 */
			'321122', /* 25 */
			'321221', /* 26 */
			'312212', /* 27 */
			'322112', /* 28 */
			'322211', /* 29 */
			'212123', /* 30 */
			'212321', /* 31 */
			'232121', /* 32 */
			'111323', /* 33 */
			'131123', /* 34 */
			'131321', /* 35 */
			'112313', /* 36 */
			'132113', /* 37 */
			'132311', /* 38 */
			'211313', /* 39 */
			'231113', /* 40 */
			'231311', /* 41 */
			'112133', /* 42 */
			'112331', /* 43 */
			'132131', /* 44 */
			'113123', /* 45 */
			'113321', /* 46 */
			'133121', /* 47 */
			'313121', /* 48 */
			'211331', /* 49 */
			'231131', /* 50 */
			'213113', /* 51 */
			'213311', /* 52 */
			'213131', /* 53 */
			'311123', /* 54 */
			'311321', /* 55 */
			'331121', /* 56 */
			'312113', /* 57 */
			'312311', /* 58 */
			'332111', /* 59 */
			'314111', /* 60 */
			'221411', /* 61 */
			'431111', /* 62 */
			'111224', /* 63 */
			'111422', /* 64 */
			'121124', /* 65 */
			'121421', /* 66 */
			'141122', /* 67 */
			'141221', /* 68 */
			'112214', /* 69 */
			'112412', /* 70 */
			'122114', /* 71 */
			'122411', /* 72 */
			'142112', /* 73 */
			'142211', /* 74 */
			'241211', /* 75 */
			'221114', /* 76 */
			'413111', /* 77 */
			'241112', /* 78 */
			'134111', /* 79 */
			'111242', /* 80 */
			'121142', /* 81 */
			'121241', /* 82 */
			'114212', /* 83 */
			'124112', /* 84 */
			'124211', /* 85 */
			'411212', /* 86 */
			'421112', /* 87 */
			'421211', /* 88 */
			'212141', /* 89 */
			'214121', /* 90 */
			'412121', /* 91 */
			'111143', /* 92 */
			'111341', /* 93 */
			'131141', /* 94 */
			'114113', /* 95 */
			'114311', /* 96 */
			'411113', /* 97 */
			'411311', /* 98 */
			'113141', /* 99 */
			'114131', /* 100 */
			'311141', /* 101 */
			'411131', /* 102 */
			'211412', /* 103 START A */
			'211214', /* 104 START B  */
			'211232', /* 105 START C  */
			'233111',	/* STOP */
			'200000'	/* END */
		);
		$keys = "";
		switch(strtoupper($type)) {
			case "A": {
				$startid = 103;
				$keys = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_';
				for($i = 0; $i < 32; $i++) {
					$keys .= chr($i);
				}
				break;
			}
			case "B": {
				$startid = 104;
				$keys = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~'.chr(127);
				break;
			}
			case "C": {
				$startid = 105;
				$keys = "";
				if ((strlen($code) % 2) != 0) {
					//echo "The length of barcode value must be even ($code). You must pad the number with zeros.\n";
					return false;
				}
				for($i = 0; $i <= 99; $i++) {
					$keys .= chr($i);
				}
				$new_code = "";
				for ($i=0; $i < (strlen($code) / 2); $i++) {
					$new_code .= chr(intval($code{(2 * $i)}.$code{(2 * $i + 1)}));
				}
				$code = $new_code;
				break;
			}
			default: {
				return false;
			}
		}
		// calculate check character
		$sum = $startid;
		for ($i=0; $i < strlen($code); $i++) {
			$sum +=  (strpos($keys, $code{$i}) * ($i+1));
		}
		$check = ($sum % 103);
		
		// add start, check and stop codes
		$code = chr($startid).$code.chr($check).chr(106).chr(107);
		$bararray = array("code" => $code, "maxw" => 0, "maxh" => 1, "bcode" => array());
		$k = 0;
		$len = strlen($code);
		for($i=0; $i < $len; $i++) {
			$ck = strpos($keys, $code{$i});
			if (($i == 0) OR ($i > ($len-4))) {
				$seq = $chr[ord($code{$i})];
			} elseif(($ck >= 0) AND isset($chr[$ck])) {
					$seq = $chr[$ck];
			} else {
				// invalid character
				return false;
			}
			for($j=0; $j < 6; $j++) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$w = $seq{$j};
				$bararray["bcode"][$k] = array("t" => $t, "w" => $w, "h" => 1, "p" => 0);
				$bararray["maxw"] += $w;
				$k++;
			}
		}
		return $bararray;		
	}
	
	/**
	 * EAN13 and UPC-A barcodes.
	 * @param string $code code to represent.
	 * @param string $len barcode type: 13 = EAN13, 12 = UPC-A
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_ean13($code, $len=13) {
		//Padding
		$code = str_pad($code, $len-1, '0', STR_PAD_LEFT);
		if($len == 12) {
			$code = '0'.$code;
		}
		// add check digit
		if(strlen($code) == 12) {
			$sum=0;
			for($i=1;$i<=11;$i+=2) {
				$sum += (3 * $code{$i});
			}
			for($i=0; $i <= 10; $i+=2) {
				$sum += ($code{$i});
			}
			$r = $sum % 10;
			if($r > 0) {
				$r = (10 - $r);
			}
			$code .= $r;
		} else { // test checkdigit
			$sum = 0;
			for($i=1; $i <= 11; $i+=2) {
				$sum += (3 * $code{$i});
			}
			for($i=0; $i <= 10; $i+=2) {
				$sum += $code{$i};
			}
			if ((($sum + $code{12}) % 10) != 0) {
				return false;
			}
		}
		//Convert digits to bars
		$codes = array(
			'A'=>array(
				'0'=>'0001101',
				'1'=>'0011001',
				'2'=>'0010011',
				'3'=>'0111101',
				'4'=>'0100011',
				'5'=>'0110001',
				'6'=>'0101111',
				'7'=>'0111011',
				'8'=>'0110111',
				'9'=>'0001011'),
			'B'=>array(
				'0'=>'0100111',
				'1'=>'0110011',
				'2'=>'0011011',
				'3'=>'0100001',
				'4'=>'0011101',
				'5'=>'0111001',
				'6'=>'0000101',
				'7'=>'0010001',
				'8'=>'0001001',
				'9'=>'0010111'),
			'C'=>array(
				'0'=>'1110010',
				'1'=>'1100110',
				'2'=>'1101100',
				'3'=>'1000010',
				'4'=>'1011100',
				'5'=>'1001110',
				'6'=>'1010000',
				'7'=>'1000100',
				'8'=>'1001000',
				'9'=>'1110100')
		);
		$parities = array(
			'0'=>array('A','A','A','A','A','A'),
			'1'=>array('A','A','B','A','B','B'),
			'2'=>array('A','A','B','B','A','B'),
			'3'=>array('A','A','B','B','B','A'),
			'4'=>array('A','B','A','A','B','B'),
			'5'=>array('A','B','B','A','A','B'),
			'6'=>array('A','B','B','B','A','A'),
			'7'=>array('A','B','A','B','A','B'),
			'8'=>array('A','B','A','B','B','A'),
			'9'=>array('A','B','B','A','B','A')
		);
		
		$bararray = array("code" => $code, "maxw" => 0, "maxh" => 1, "bcode" => array());
		$k = 0;
		$seq = '101';
		$p = $parities[$code{0}];
		for($i=1; $i < 7; $i++) {
			$seq .= $codes[$p[$i-1]][$code{$i}];
		}
		$seq .= '01010';
		for($i=7; $i < 13; $i++) {
			$seq .= $codes['C'][$code{$i}];
		}
		$seq .= '101';
		$len = strlen($seq);
		$w = 0;
		for($i=0; $i < $len; $i++) {
			$w += 1;
			if (($i == ($len - 1)) OR (($i < ($len - 1)) AND ($seq{$i} != $seq{($i+1)}))) {
				if ($seq{$i} == '1') {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$bararray["bcode"][$k] = array("t" => $t, "w" => $w, "h" => 1, "p" => 0);
				$bararray["maxw"] += $w;
				$k++;
				$w = 0;
			}
		}
		return $bararray;
	}
	
	/**
	 * POSTNET barcodes.
	 * @param string $code zip code to represent. Must be a string containing a zip code of the form DDDDD or DDDDD-DDDD.
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_postnet($code) {
		// bar lenght
		$barlen = Array(
			0 => Array(2,2,1,1,1),
			1 => Array(1,1,1,2,2),
			2 => Array(1,1,2,1,2),
			3 => Array(1,1,2,2,1),
			4 => Array(1,2,1,1,2),
			5 => Array(1,2,1,2,1),
			6 => Array(1,2,2,1,1),
			7 => Array(2,1,1,1,2),
			8 => Array(2,1,1,2,1),
			9 => Array(2,1,2,1,1)
		);
		$bararray = array("code" => $code, "maxw" => 0, "maxh" => 2, "bcode" => array());
		$k = 0;
		$code = str_replace("-", "", $code);
		$code = str_replace(" ", "", $code);
		$len = strlen($code);
		// calculate checksum
		$sum = 0;
		for($i=0; $i < $len; $i++) {
			$sum += intval($code{$i});
		}
		if(($sum % 10) == 0) {
			return false;
		}
		$code .= "".(10 - ($sum % 10))."";
		$len = strlen($code);
		// start bar
		$bararray["bcode"][$k++] = array("t" => 1, "w" => 1, "h" => 2, "p" => 0);
		$bararray["bcode"][$k++] = array("t" => 0, "w" => 1, "h" => 2, "p" => 0);
		$bararray["maxw"] += 2;
		for ($i=0; $i < $len; $i++) {
			for ($j=0; $j < 5; $j++) {
				$h = $barlen[$code{$i}][$j];
				$p = floor(1 / $h);
				$bararray["bcode"][$k++] = array("t" => 1, "w" => 1, "h" => $h, "p" => $p);
				$bararray["bcode"][$k++] = array("t" => 0, "w" => 1, "h" => 2, "p" => 0);
				$bararray["maxw"] += 2;
			}
		}
		// end bar
		$bararray["bcode"][$k++] = array("t" => 1, "w" => 1, "h" => 2, "p" => 0);
		$bararray["maxw"] += 1;
		return $bararray;
	}
	
	/**
	 * CODABAR barcodes.
	 * @param string $code code to represent.
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_codabar($code) {
		$chr = array(
			'0' => '11111221',
			'1' => '11112211',
			'2' => '11121121',
			'3' => '22111111',
			'4' => '11211211',
			'5' => '21111211',
			'6' => '12111121',
			'7' => '12112111',
			'8' => '12211111',
			'9' => '21121111',
			'-' => '11122111',
			'$' => '11221111',
			':' => '21112121',
			'/' => '21211121',
			'.' => '21212111',
			'+' => '11222221',
			'A' => '11221211',
			'B' => '12121121',
			'C' => '11121221',
			'D' => '11122211'
		);
		
		$bararray = array("code" => $code, "maxw" => 0, "maxh" => 1, "bcode" => array());
		$k = 0;
		$w = 0;
		$seq = "";
		$code = "A".strtoupper($code)."A";
		$len = strlen($code);
		for($i=0; $i < $len; $i++) {
			if (!isset($chr[$code{$i}])) {
				return false;
			}
			$seq = $chr[$code{$i}];
			for($j=0; $j < 8; $j++) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$w = $seq{$j};
				$bararray["bcode"][$k] = array("t" => $t, "w" => $w, "h" => 1, "p" => 0);
				$bararray["maxw"] += $w;
				$k++;
			}
		}
		return $bararray;
	}
	
} // end of class

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
