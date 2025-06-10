<?php

namespace pChart\Barcodes\QRCode;

use pChart\pException;

class Frame {

	private $width;
	private $frame;
	private $version;
	private $x;
	private $y;
	private $dir;
	private $bit;
	private $new_frame;

	// Error correction code
	// Table of the error correction code (Reed-Solomon block)
	// See Table 12-16 (pp.30-36), JIS X0510:2004.
	private $eccTable = [
		[[0, 0], [0, 0], [0, 0], [0, 0]],
		[[1, 0], [1, 0], [1, 0], [1, 0]], // 1
		[[1, 0], [1, 0], [1, 0], [1, 0]],
		[[1, 0], [1, 0], [2, 0], [2, 0]],
		[[1, 0], [2, 0], [2, 0], [4, 0]],
		[[1, 0], [2, 0], [2, 2], [2, 2]], // 5
		[[2, 0], [4, 0], [4, 0], [4, 0]],
		[[2, 0], [4, 0], [2, 4], [4, 1]],
		[[2, 0], [2, 2], [4, 2], [4, 2]],
		[[2, 0], [3, 2], [4, 4], [4, 4]],
		[[2, 2], [4, 1], [6, 2], [6, 2]], //10
		[[4, 0], [1, 4], [4, 4], [3, 8]],
		[[2, 2], [6, 2], [4, 6], [7, 4]],
		[[4, 0], [8, 1], [8, 4], [12, 4]],
		[[3, 1], [4, 5], [11, 5], [11, 5]],
		[[5, 1], [5, 5], [5, 7], [11, 7]], //15
		[[5, 1], [7, 3], [15, 2], [3, 13]],
		[[1, 5], [10, 1], [1, 15], [2, 17]],
		[[5, 1], [9, 4], [17, 1], [2, 19]],
		[[3, 4], [3, 11], [17, 4], [9, 16]],
		[[3, 5], [3, 13], [15, 5], [15, 10]], //20
		[[4, 4], [17, 0], [17, 6], [19, 6]],
		[[2, 7], [17, 0], [7, 16], [34, 0]],
		[[4, 5], [4, 14], [11, 14], [16, 14]],
		[[6, 4], [6, 14], [11, 16], [30, 2]],
		[[8, 4], [8, 13], [7, 22], [22, 13]], //25
		[[10, 2], [19, 4], [28, 6], [33, 4]],
		[[8, 4], [22, 3], [8, 26], [12, 28]],
		[[3, 10], [3, 23], [4, 31], [11, 31]],
		[[7, 7], [21, 7], [1, 37], [19, 26]],
		[[5, 10], [19, 10], [15, 25], [23, 25]], //30
		[[13, 3], [2, 29], [42, 1], [23, 28]],
		[[17, 0], [10, 23], [10, 35], [19, 35]],
		[[17, 1], [14, 21], [29, 19], [11, 46]],
		[[13, 6], [14, 23], [44, 7], [59, 1]],
		[[12, 7], [12, 26], [39, 14], [22, 41]], //35
		[[6, 14], [6, 34], [46, 10], [2, 64]],
		[[17, 4], [29, 14], [49, 10], [24, 46]],
		[[4, 18], [13, 32], [48, 14], [42, 32]],
		[[20, 4], [40, 7], [43, 22], [10, 67]],
		[[19, 6], [18, 31], [34, 34], [20, 61]] //40
	];

	/** Alignment pattern
	* Positions of alignment patterns.
	* This array includes only the second and the third position of the 
	* alignment patterns. Rest of them can be calculated from the distance 
	* between them.
	* See Table 1 in Appendix E (pp.71) of JIS X0510:2004.
	*/
	private $alignmentPattern = [
		[0, 0],
		[0, 0], [18, 0], [22, 0], [26, 0], [30, 0],
		[34, 0], [22, 38], [24, 42], [26, 46], [28, 50],
		[30, 54], [32, 58], [34, 62], [26, 46], [26, 48],
		[26, 50], [30, 54], [30, 56], [30, 58], [34, 62],
		[28, 50], [26, 50], [30, 54], [28, 54], [32, 58],
		[30, 58], [34, 62], [26, 50], [30, 54], [26, 52],
		[30, 56], [34, 60], [30, 58], [34, 62], [30, 54],
		[24, 50], [28, 54], [32, 58], [26, 54], [30, 58]
	];

	// Version information pattern (BCH coded).
	// See Table 1 in Appendix D (pp.68) of JIS X0510:2004.
	// size: [QR_SPEC_VERSION_MAX - 6]
	private $versionPattern = [
		0x07c94, 0x085bc, 0x09a99, 0x0a4d3, 0x0bbf6, 0x0c762, 0x0d847, 0x0e60d,
		0x0f928, 0x10b78, 0x1145d, 0x12a17, 0x13532, 0x149a6, 0x15683, 0x168c9,
		0x177ec, 0x18ec4, 0x191e1, 0x1afab, 0x1b08e, 0x1cc1a, 0x1d33f, 0x1ed75,
		0x1f250, 0x209d5, 0x216f0, 0x228ba, 0x2379f, 0x24b0b, 0x2542e, 0x26a64,
		0x27541, 0x28c69
	];

	private $remainder_bits = [
		0,
		0,7,7,7,7,7,
		0,0,0,0,0,0,0,
		3,3,3,3,3,3,3,
		4,4,4,4,4,4,4,
		3,3,3,3,3,3,3,
		0,0,0,0,0,0
	];

	public function getFrame($package)
	{
		list($this->version, $dataLength, $ecc, $this->width, $level, $dataCode) = $package;

		$this->frame = $this->createFrame();

		$this->x = $this->width - 1;
		$this->y = $this->width - 1;
		$this->dir = -1;
		$this->bit = -1;

		list($b1,$b2) = $this->eccTable[$this->version][$level];

		$blocks = $b1 + $b2;
		$nroots = intval($ecc / $blocks);
		$eccLength = $blocks * $nroots;

		$ReedSolomon = new ReedSolomon($dataCode, $dataLength, $b1, $b2, $blocks, $nroots);

		// inteleaved data and ecc codes
		for($i=0; $i < $dataLength; $i++) {
			$code = $ReedSolomon->getDataCode($i);
			$bit = 128;
			for($j=0; $j<8; $j++) {
				$this->setNext(2 | (($bit & $code) != 0));
				$bit /= 2;
			}
		}

		for($i=0; $i < $eccLength; $i++) {
			$code = $ReedSolomon->getEccCode($i);
			$bit = 128;
			for($j=0; $j<8; $j++) {
				$this->setNext(2 | (($bit & $code) != 0));
				$bit /= 2;
			}
		}

		// remainder bits
		$j = $this->remainder_bits[$this->version];
		for($i=0; $i<$j; $i++) {
			$this->setNext(2);
		}

		return $this->frame;
	}

	private function setNext($val)
	{
		do {
			if($this->bit == -1) {
				$this->bit = 0;
				$this->frame[$this->y][$this->x] = $val;
				return;
			}

			if($this->bit == 0) {
				$this->x--;
				$this->bit++;
			} else {
				$this->x++;
				$this->y += $this->dir;
				$this->bit--;
			}

			if($this->dir < 0) {
				if($this->y < 0) {
					$this->y = 0;
					$this->x -= 2;
					$this->dir = 1;
					if($this->x == 6) {
						$this->x--;
						$this->y = 9;
					}
				}
			} else {
				if($this->y == $this->width) {
					$this->y--;
					$this->x -= 2;
					$this->dir = -1;
					if($this->x == 6) {
						$this->x--;
						$this->y -= 8;
					}
				}
			}

			if($this->x < 0 || $this->y < 0){
				throw pException::QRCodeEncoderError('Invalid dimentions');
			}

		} while($this->frame[$this->y][$this->x] != 0);

		$this->frame[$this->y][$this->x] = $val;
	}

	/** 
	 * Put an alignment marker.
	 * param ox and oy coordinate of the pattern
	 */
	private function putAlignmentMarker($ox, $oy)
	{
		$finder = [
			[161, 161, 161, 161, 161],
			[161, 160, 160, 160, 161],
			[161, 160, 161, 160, 161],
			[161, 160, 160, 160, 161],
			[161, 161, 161, 161, 161]
		];

		$yStart = $oy-2;
		$xStart = $ox-2;

		for($y=0; $y<5; $y++) {
			array_splice($this->new_frame[$yStart+$y], $xStart, 5, $finder[$y]);
		}
	}

	private function putAlignmentPattern()
	{
		if($this->version < 2){
			return;
		}

		list($v0, $v1) = $this->alignmentPattern[$this->version];

		$d = $v1 - $v0;
		if($d < 0) {
			$w = 2;
		} else {
			$w = floor(($this->width - $v0) / $d + 2);
		}

		if($w * $w - 3 == 1) {
			$this->putAlignmentMarker($v0, $v0);
			return;
		}

		$cx = $v0;
		for($x=1; $x<$w - 1; $x++) {
			$this->putAlignmentMarker(6, $cx);
			$this->putAlignmentMarker($cx, 6);
			$cx += $d;
		}

		$cy = $v0;
		for($y=0; $y<$w-1; $y++) {
			$cx = $v0;
			for($x=0; $x<$w-1; $x++) {
				$this->putAlignmentMarker($cx, $cy);
				$cx += $d;
			}
			$cy += $d;
		}
	}

	/** 
	 * Put a finder pattern.
	 * param ox and oy coordinate of the pattern
	 */
	private function putFinderPattern($ox, $oy)
	{
		$finder = [
			[193, 193, 193, 193, 193, 193, 193],
			[193, 192, 192, 192, 192, 192, 193],
			[193, 192, 193, 193, 193, 192, 193],
			[193, 192, 193, 193, 193, 192, 193],
			[193, 192, 193, 193, 193, 192, 193],
			[193, 192, 192, 192, 192, 192, 193],
			[193, 193, 193, 193, 193, 193, 193]
		];
		
		for($y=0; $y<7; $y++) {
			array_splice($this->new_frame[$oy+$y], $ox, 7, $finder[$y]);
		}
	}

	private function createFrame()
	{
		$this->new_frame = array_fill(0, $this->width, array_fill(0, $this->width, 0));

		// Finder pattern
		$this->putFinderPattern(0, 0);
		$this->putFinderPattern($this->width - 7, 0);
		$this->putFinderPattern(0, $this->width - 7);

		// Separator
		$yOffset = $this->width - 7;

		for($y=0; $y<7; $y++) {
			$this->new_frame[$y][7] = 192;
			$this->new_frame[$y][$this->width - 8] = 192;
			$this->new_frame[$yOffset][7] = 192;
			$yOffset++;
		}

		$setPattern = [192,192,192,192,192,192,192,192];
		array_splice($this->new_frame[7], 0, 8, $setPattern);
		array_splice($this->new_frame[7], $this->width - 8, 8, $setPattern);
		array_splice($this->new_frame[$this->width - 8], 0, 8, $setPattern);

		// Format info
		$setPattern = [132,132,132,132,132,132,132,132,132];
		array_splice($this->new_frame[8], 0, 9, $setPattern);
		array_splice($this->new_frame[8], $this->width - 8, 8, array_slice($setPattern, 0, 8));

		$yOffset = $this->width - 8;

		for($y=0; $y<8; $y++,$yOffset++) {
			$this->new_frame[$y][8] = 132;
			$this->new_frame[$yOffset][8] = 132;
		}

		// Timing pattern
		for($i=1; $i<$this->width-15; $i++) {
			$val = (144 | ($i & 1));
			$this->new_frame[6][7+$i] = $val;
			$this->new_frame[7+$i][6] = $val;
		}

		// Alignment pattern
		$this->putAlignmentPattern();

		// Version information
		if($this->version >= 7) {

			$v = $this->versionPattern[$this->version -7];

			for($x=0; $x<6; $x++) {
				for($y=0; $y<3; $y++) {
					$val = (136 | ($v & 1));
					$yc = ($this->width - 11)+$y;
					$this->new_frame[$yc][$x] = $val;
					$this->new_frame[$x][$yc] = $val;
					$v = $v >> 1;
				}
			}
		}

		// and a little bit...
		$this->new_frame[$this->width - 8][8] = 129;

		return $this->new_frame;
	}

}
