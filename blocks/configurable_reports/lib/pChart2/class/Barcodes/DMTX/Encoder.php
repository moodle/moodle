<?php

namespace pChart\Barcodes\DMTX;

class Encoder {

	private $matrix;
	private $ec_params;
	private $rect;
	private $fnc1;
	private $cols;
	private $rows;

	public function encode($data, $opts)
	{
		$this->rect = (bool)$opts['pattern'];
		$this->fnc1 = (bool)$opts['GS-1'];

		$data = $this->encode_data($data);
		$data = $this->encode_ec($data);

		return $this->create_matrix($data);
	}

	private function encode_data($data)
	{
		/* Convert to data codewords. */
		$edata = ($this->fnc1 ? [232] : []);
		$length = strlen($data);
		$offset = 0;

		while ($offset < $length) {
			$ch1 = ord(substr($data, $offset, 1));
			$offset++;
			if ($ch1 >= 0x30 && $ch1 <= 0x39) {
				$ch2 = ord(substr($data, $offset, 1));
				if ($ch2 >= 0x30 && $ch2 <= 0x39) {
					$offset++;
					$edata[] = (($ch1 - 0x30) * 10) + ($ch2 - 0x30) + 130;
				} else {
					$edata[] = $ch1 + 1;
				}
			} else if ($ch1 < 0x80) {
				$edata[] = $ch1 + 1;
			} else {
				$edata[] = 235;
				$edata[] = ($ch1 - 0x80) + 1;
			}
		}
		/* Add padding. */
		$length = count($edata);
		$this->ec_params = $this->detect_version($length);
		if ($length > $this->ec_params[0]) {
			$length = $this->ec_params[0];
			$edata = array_slice($edata, 0, $length);
			if ($edata[$length - 1] == 235) {
				$edata[$length - 1] = 129;
			}
		} else if ($length < $this->ec_params[0]) {
			$length++;
			$edata[] = 129;
			while ($length < $this->ec_params[0]) {
				$length++;
				$r = (($length * 149) % 253) + 1;
				$edata[] = ($r + 129) % 254;
			}
		}
		/* Return. */
		return $edata;
	}

	private function detect_version($length)
	{
		for ($i = ($this->rect ? 24 : 0), $j = ($this->rect ? 30 : 24); $i < $j; $i++) {
			if ($length <= $this->all_ec_params[$i][0]) {
				return $this->all_ec_params[$i];
			}
		}
		return $this->all_ec_params[$j - 1];
	}

	private function encode_ec($data)
	{
		$blocks = $this->ec_split($data);
		for ($i = 0, $n = count($blocks); $i < $n; $i++) {
			$ec_block = $this->ec_divide($blocks[$i]);
			$blocks[$i] = array_merge($blocks[$i], $ec_block);
		}
		return $this->ec_interleave($blocks);
	}

	private function ec_split($data)
	{
		$blocks = [];
		$num_blocks = $this->ec_params[2] + $this->ec_params[4];
		for ($i = 0; $i < $num_blocks; $i++) {
			$blocks[$i] = [];
		}
		for ($i = 0, $length = count($data); $i < $length; $i++) {
			$blocks[$i % $num_blocks][] = $data[$i];
		}
		return $blocks;
	}

	private function ec_divide($data)
	{
		$num_data = count($data);
		$num_error = $this->ec_params[1];
		$generator = $this->dmtx_ec_polynomials[$num_error];
		$message = $data;
		for ($i = 0; $i < $num_error; $i++) {
			$message[] = 0;
		}
		for ($i = 0; $i < $num_data; $i++) {
			if ($message[$i]) {
				$leadterm = $this->dmtx_log[$message[$i]];
				for ($j = 0; $j <= $num_error; $j++) {
					$term = ($generator[$j] + $leadterm) % 255;
					$message[$i + $j] ^= $this->dmtx_exp[$term];
				}
			}
		}
		return array_slice($message, $num_data, $num_error);
	}

	private function ec_interleave($blocks)
	{
		$data = [];
		$num_blocks = count($blocks);
		for ($offset = 0; true; $offset++) {
			$break = true;
			for ($i = 0; $i < $num_blocks; $i++) {
				if (isset($blocks[$i][$offset])) {
					$data[] = $blocks[$i][$offset];
					$break = false;
				}
			}
			if ($break) {
				break;
			}
		}
		return $data;
	}

	private function create_matrix($data)
	{
		/* Create matrix. */
		$rheight = $this->ec_params[8] + 2;
		$rwidth = $this->ec_params[9] + 2;
		$height = $this->ec_params[6] * $rheight;
		$width = $this->ec_params[7] * $rwidth;
		$bitmap = [];
		for ($y = 0; $y < $height; $y++) {
			$row = [];
			for ($x = 0; $x < $width; $x++) {
				$row[] = ((
					((($x + $y) % 2) == 0) ||
					(($x % $rwidth) == 0) ||
					(($y % $rheight) == ($rheight - 1))
				) ? 1 : 0);
			}
			$bitmap[] = $row;
		}
		/* Create data region. */
		$this->rows = $this->ec_params[6] * $this->ec_params[8];
		$this->cols = $this->ec_params[7] * $this->ec_params[9];
		$this->matrix = [];
		for ($y = 0; $y < $this->rows; $y++) {
			$row = [];
			for ($x = 0; $x < $width; $x++) {
				$row[] = null;
			}
			$this->matrix[] = $row;
		}
		$this->place_data($data);
		/* Copy into matrix. */
		for ($yy = 0; $yy < $this->ec_params[6]; $yy++) {
			for ($xx = 0; $xx < $this->ec_params[7]; $xx++) {
				for ($y = 0; $y < $this->ec_params[8]; $y++) {
					for ($x = 0; $x < $this->ec_params[9]; $x++) {
						$row = $yy * $this->ec_params[8] + $y;
						$col = $xx * $this->ec_params[9] + $x;
						$b = $this->matrix[$row][$col];
						if (is_null($b)) {
							continue;
						}
						$row = $yy * $rheight + $y + 1;
						$col = $xx * $rwidth + $x + 1;
						$bitmap[$row][$col] = $b;
					}
				}
			}
		}
		return $bitmap;
		#return [
		#	'width' => $width,
		#	'height' => $height,
		#	'matrix' => $bitmap
		#];
	}

	private function place_data($data)
	{
		$row = 4;
		$col = 0;
		$offset = 0;
		$length = count($data);
		while (($row < $this->rows || $col < $this->cols) && $offset < $length) {
			/* Corner cases. Literally. */
			if ($row == $this->rows && $col == 0) {
				$this->place_1($data[$offset++]);
			} else if ($row == $this->rows - 2 && $col == 0 && $this->cols % 4 != 0) {
				$this->place_2($data[$offset++]);
			} else if ($row == $this->rows - 2 && $col == 0 && $this->cols % 8 == 4) {
				$this->place_3($data[$offset++]);
			} else if ($row == $this->rows + 4 && $col == 2 && $this->cols % 8 == 0) {
				$this->place_4($data[$offset++]);
			}
			/* Up and to the right. */
			while ($row >= 0 && $col < $this->cols && $offset < $length) {
				if ($row < $this->rows && $col >= 0 && is_null($this->matrix[$row][$col])) {
					$b = $data[$offset++];
					$this->place_0($row, $col, $b);
				}
				$row -= 2;
				$col += 2;
			}
			$row += 1;
			$col += 3;
			/* Down and to the left. */
			while ($row < $this->rows && $col >= 0 && $offset < $length) {
				if ($row >= 0 && $col < $this->cols && is_null($this->matrix[$row][$col])) {
					$b = $data[$offset++];
					$this->place_0($row, $col, $b);
				}
				$row += 2;
				$col -= 2;
			}
			$row += 3;
			$col += 1;
		}
	}

	private function place_1($b)
	{
		$this->matrix[$this->rows - 1][0] = (($b & 0x80) ? 1 : 0);
		$this->matrix[$this->rows - 1][1] = (($b & 0x40) ? 1 : 0);
		$this->matrix[$this->rows - 1][2] = (($b & 0x20) ? 1 : 0);
		$this->matrix[0][$this->cols - 2] = (($b & 0x10) ? 1 : 0);
		$this->matrix[0][$this->cols - 1] = (($b & 0x08) ? 1 : 0);
		$this->matrix[1][$this->cols - 1] = (($b & 0x04) ? 1 : 0);
		$this->matrix[2][$this->cols - 1] = (($b & 0x02) ? 1 : 0);
		$this->matrix[3][$this->cols - 1] = (($b & 0x01) ? 1 : 0);
	}

	private function place_2($b)
	{
		$this->matrix[$this->rows - 3][0] = (($b & 0x80) ? 1 : 0);
		$this->matrix[$this->rows - 2][0] = (($b & 0x40) ? 1 : 0);
		$this->matrix[$this->rows - 1][0] = (($b & 0x20) ? 1 : 0);
		$this->matrix[0][$this->cols - 4] = (($b & 0x10) ? 1 : 0);
		$this->matrix[0][$this->cols - 3] = (($b & 0x08) ? 1 : 0);
		$this->matrix[0][$this->cols - 2] = (($b & 0x04) ? 1 : 0);
		$this->matrix[0][$this->cols - 1] = (($b & 0x02) ? 1 : 0);
		$this->matrix[1][$this->cols - 1] = (($b & 0x01) ? 1 : 0);
	}

	private function place_3($b)
	{
		$this->matrix[$this->rows - 3][0] = (($b & 0x80) ? 1 : 0);
		$this->matrix[$this->rows - 2][0] = (($b & 0x40) ? 1 : 0);
		$this->matrix[$this->rows - 1][0] = (($b & 0x20) ? 1 : 0);
		$this->matrix[0][$this->cols - 2] = (($b & 0x10) ? 1 : 0);
		$this->matrix[0][$this->cols - 1] = (($b & 0x08) ? 1 : 0);
		$this->matrix[1][$this->cols - 1] = (($b & 0x04) ? 1 : 0);
		$this->matrix[2][$this->cols - 1] = (($b & 0x02) ? 1 : 0);
		$this->matrix[3][$this->cols - 1] = (($b & 0x01) ? 1 : 0);
	}

	private function place_4($b)
	{
		$this->matrix[$this->rows - 1][0] = (($b & 0x80) ? 1 : 0);
		$this->matrix[$this->rows - 1][$this->cols - 1] = (($b & 0x40) ? 1 : 0);
		$this->matrix[0][$this->cols - 3] = (($b & 0x20) ? 1 : 0);
		$this->matrix[0][$this->cols - 2] = (($b & 0x10) ? 1 : 0);
		$this->matrix[0][$this->cols - 1] = (($b & 0x08) ? 1 : 0);
		$this->matrix[1][$this->cols - 3] = (($b & 0x04) ? 1 : 0);
		$this->matrix[1][$this->cols - 2] = (($b & 0x02) ? 1 : 0);
		$this->matrix[1][$this->cols - 1] = (($b & 0x01) ? 1 : 0);
	}

	private function place_0($row, $col, $b)
	{
		$this->place_b($row - 2, $col - 2, $b & 0x80);
		$this->place_b($row - 2, $col - 1, $b & 0x40);
		$this->place_b($row - 1, $col - 2, $b & 0x20);
		$this->place_b($row - 1, $col - 1, $b & 0x10);
		$this->place_b($row - 1, $col - 0, $b & 0x08);
		$this->place_b($row - 0, $col - 2, $b & 0x04);
		$this->place_b($row - 0, $col - 1, $b & 0x02);
		$this->place_b($row - 0, $col - 0, $b & 0x01);
	}

	private function place_b($row, $col, $b)
	{
		if ($row < 0) {
			$row += $this->rows;
			$col += (4 - (($this->rows + 4) % 8));
		}
		if ($col < 0) {
			$col += $this->cols;
			$row += (4 - (($this->cols + 4) % 8));
		}
		$this->matrix[$row][$col] = ($b ? 1 : 0);
	}

	/*  $all_ec_params[] = array(                              */
	/*    total number of data codewords,                      */
	/*    number of error correction codewords per block,      */
	/*    number of blocks in first group,                     */
	/*    number of data codewords per block in first group,   */
	/*    number of blocks in second group,                    */
	/*    number of data codewords per block in second group,  */
	/*    number of data regions (vertical),                   */
	/*    number of data regions (horizontal),                 */
	/*    number of rows per data region,                      */
	/*    number of columns per data region                    */
	/*  );                                                     */
	private $all_ec_params = [
		[    3,  5, 1,   3, 0,   0, 1, 1,  8,  8],
		[    5,  7, 1,   5, 0,   0, 1, 1, 10, 10],
		[    8, 10, 1,   8, 0,   0, 1, 1, 12, 12],
		[   12, 12, 1,  12, 0,   0, 1, 1, 14, 14],
		[   18, 14, 1,  18, 0,   0, 1, 1, 16, 16],
		[   22, 18, 1,  22, 0,   0, 1, 1, 18, 18],
		[   30, 20, 1,  30, 0,   0, 1, 1, 20, 20],
		[   36, 24, 1,  36, 0,   0, 1, 1, 22, 22],
		[   44, 28, 1,  44, 0,   0, 1, 1, 24, 24],
		[   62, 36, 1,  62, 0,   0, 2, 2, 14, 14],
		[   86, 42, 1,  86, 0,   0, 2, 2, 16, 16],
		[  114, 48, 1, 114, 0,   0, 2, 2, 18, 18],
		[  144, 56, 1, 144, 0,   0, 2, 2, 20, 20],
		[  174, 68, 1, 174, 0,   0, 2, 2, 22, 22],
		[  204, 42, 2, 102, 0,   0, 2, 2, 24, 24],
		[  280, 56, 2, 140, 0,   0, 4, 4, 14, 14],
		[  368, 36, 4,  92, 0,   0, 4, 4, 16, 16],
		[  456, 48, 4, 114, 0,   0, 4, 4, 18, 18],
		[  576, 56, 4, 144, 0,   0, 4, 4, 20, 20],
		[  696, 68, 4, 174, 0,   0, 4, 4, 22, 22],
		[  816, 56, 6, 136, 0,   0, 4, 4, 24, 24],
		[ 1050, 68, 6, 175, 0,   0, 6, 6, 18, 18],
		[ 1304, 62, 8, 163, 0,   0, 6, 6, 20, 20],
		[ 1558, 62, 8, 156, 2, 155, 6, 6, 22, 22],
		[    5,  7, 1,   5, 0,   0, 1, 1,  6, 16],
		[   10, 11, 1,  10, 0,   0, 1, 2,  6, 14],
		[   16, 14, 1,  16, 0,   0, 1, 1, 10, 24],
		[   22, 18, 1,  22, 0,   0, 1, 2, 10, 16],
		[   32, 24, 1,  32, 0,   0, 1, 2, 14, 16],
		[   49, 28, 1,  49, 0,   0, 1, 2, 14, 22]
	];

	private $dmtx_ec_polynomials = [
		5 =>  [0, 235, 207, 210, 244, 15],
		7 =>  [0, 177, 30, 214, 218, 42, 197, 28],
		10 => [0, 199, 50, 150, 120, 237, 131, 172, 83, 243, 55],
		11 => [0, 213, 173, 212, 156, 103, 109, 174, 242, 215, 12, 66],
		12 => [0, 168, 142, 35, 173, 94, 185, 107, 199, 74, 194, 233, 78],
		14 => [0, 83, 171, 33, 39, 8, 12, 248,	27, 38, 84, 93, 246, 173, 105],
		18 => [0, 164, 9, 244, 69, 177, 163, 161, 231, 94,	250, 199, 220, 253, 164, 103, 142, 61, 171],
		20 => [0, 127, 33, 146, 23, 79, 25, 193, 122, 209, 233, 230, 164, 1, 109, 184, 149, 38, 201, 61, 210],
		24 => [0, 65, 141, 245, 31, 183, 242, 236, 177, 127, 225, 106,	22, 131, 20, 202, 22, 106, 137, 103, 231, 215, 136, 85, 45],
		28 => [0, 150, 32, 109, 149, 239, 213, 198, 48, 94, 50, 12, 195, 167, 130, 196, 253, 99, 166, 239, 222, 146, 190, 245, 184,
				173, 125, 17, 151],
		36 => [0, 57, 86, 187, 69, 140, 153, 31, 66, 135, 67, 248, 84,	90, 81, 219, 197, 2, 1, 39, 16, 75, 229, 20, 51, 252,
				108, 213, 181, 183, 87, 111, 77, 232, 168, 176, 156],
		42 => [0, 225, 38, 225, 148, 192, 254, 141, 11, 82, 237, 81, 24, 13, 122, 0, 106, 167, 13, 207, 160, 88,
				203, 38, 142, 84, 66, 3, 168, 102, 156, 1, 200,	88, 60, 233, 134, 115, 114, 234, 90, 65, 138],
		48 => [0, 114, 69, 122, 30, 94, 11, 66, 230, 132, 73, 145, 137, 135, 79, 214, 33, 12, 220, 142, 213, 136, 124, 215, 166,
				9, 222, 28, 154, 132, 4, 100, 170, 145, 59, 164, 215, 17, 249, 102, 249, 134, 128, 5, 245, 131, 127, 221, 156],
		56 => [0, 29, 179, 99, 149, 159, 72, 125, 22, 55, 60, 217,	176, 156, 90, 43, 80, 251, 235, 128, 169, 254, 134,
				249, 42, 121, 118, 72, 128, 129, 232, 37, 15, 24, 221, 143, 115, 131, 40, 113, 254, 19, 123, 246, 68, 166,
				66, 118, 142, 47, 51, 195, 242, 249, 131, 38, 66],
		62 => [0, 182, 133, 162, 126, 236, 58, 172, 163, 53, 121, 159, 2, 166, 137, 234, 158, 195, 164, 77, 228, 226, 145, 91, 180,
				232, 23, 241, 132, 135, 206, 184, 14, 6, 66, 238, 83, 100, 111, 85, 202, 91, 156, 68, 218, 57, 83, 222, 188, 25, 179,
				144, 169, 164, 82, 154, 103, 89, 42, 141, 175, 32, 168],
		68 => [0, 33, 79, 190, 245, 91, 221, 233, 25, 24, 6, 144, 151, 121, 186, 140, 127, 45, 153, 250, 183, 70, 131,
				198, 17, 89, 245, 121, 51, 140, 252, 203, 82, 83, 233, 152, 220, 155, 18, 230, 210, 94, 32, 200, 197, 192,
				194, 202, 129, 10, 237, 198, 94, 176, 36, 40, 139, 201, 132, 219, 34, 56, 113, 52, 20, 34, 247, 15, 51]
	];

	private $dmtx_log = [
		  0,   0,   1, 240,   2, 225, 241,  53,
		  3,  38, 226, 133, 242,  43,  54, 210,
		  4, 195,  39, 114, 227, 106, 134,  28,
		243, 140,  44,  23,  55, 118, 211, 234,
		  5, 219, 196,  96,  40, 222, 115, 103,
		228,  78, 107, 125, 135,   8,  29, 162,
		244, 186, 141, 180,  45,  99,  24,  49,
		 56,  13, 119, 153, 212, 199, 235,  91,
		  6,  76, 220, 217, 197,  11,  97, 184,
		 41,  36, 223, 253, 116, 138, 104, 193,
		229,  86,  79, 171, 108, 165, 126, 145,
		136,  34,   9,  74,  30,  32, 163,  84,
		245, 173, 187, 204, 142,  81, 181, 190,
		 46,  88, 100, 159,  25, 231,  50, 207,
		 57, 147,  14,  67, 120, 128, 154, 248,
		213, 167, 200,  63, 236, 110,  92, 176,
		  7, 161,  77, 124, 221, 102, 218,  95,
		198,  90,  12, 152,  98,  48, 185, 179,
		 42, 209,  37, 132, 224,  52, 254, 239,
		117, 233, 139,  22, 105,  27, 194, 113,
		230, 206,  87, 158,  80, 189, 172, 203,
		109, 175, 166,  62, 127, 247, 146,  66,
		137, 192,  35, 252,  10, 183,  75, 216,
		 31,  83,  33,  73, 164, 144,  85, 170,
		246,  65, 174,  61, 188, 202, 205, 157,
		143, 169,  82,  72, 182, 215, 191, 251,
		 47, 178,  89, 151, 101,  94, 160, 123,
		 26, 112, 232,  21,  51, 238, 208, 131,
		 58,  69, 148,  18,  15,  16,  68,  17,
		121, 149, 129,  19, 155,  59, 249,  70,
		214, 250, 168,  71, 201, 156,  64,  60,
		237, 130, 111,  20,  93, 122, 177, 150
	];

	private $dmtx_exp = [
		  1,   2,   4,   8,  16,  32,  64, 128,
		 45,  90, 180,  69, 138,  57, 114, 228,
		229, 231, 227, 235, 251, 219, 155,  27,
		 54, 108, 216, 157,  23,  46,  92, 184,
		 93, 186,  89, 178,  73, 146,   9,  18,
		 36,  72, 144,  13,  26,  52, 104, 208,
		141,  55, 110, 220, 149,   7,  14,  28,
		 56, 112, 224, 237, 247, 195, 171, 123,
		246, 193, 175, 115, 230, 225, 239, 243,
		203, 187,  91, 182,  65, 130,  41,  82,
		164, 101, 202, 185,  95, 190,  81, 162,
		105, 210, 137,  63, 126, 252, 213, 135,
		 35,  70, 140,  53, 106, 212, 133,  39,
		 78, 156,  21,  42,  84, 168, 125, 250,
		217, 159,  19,  38,  76, 152,  29,  58,
		116, 232, 253, 215, 131,  43,  86, 172,
		117, 234, 249, 223, 147,  11,  22,  44,
		 88, 176,  77, 154,  25,  50, 100, 200,
		189,  87, 174, 113, 226, 233, 255, 211,
		139,  59, 118, 236, 245, 199, 163, 107,
		214, 129,  47,  94, 188,  85, 170, 121,
		242, 201, 191,  83, 166,  97, 194, 169,
		127, 254, 209, 143,  51, 102, 204, 181,
		 71, 142,  49,  98, 196, 165, 103, 206,
		177,  79, 158,  17,  34,  68, 136,  61,
		122, 244, 197, 167,  99, 198, 161, 111,
		222, 145,  15,  30,  60, 120, 240, 205,
		183,  67, 134,  33,  66, 132,  37,  74,
		148,   5,  10,  20,  40,  80, 160, 109,
		218, 153,  31,  62, 124, 248, 221, 151,
		  3,   6,  12,  24,  48,  96, 192, 173,
		119, 238, 241, 207, 179,  75, 150,   1
	];

}
