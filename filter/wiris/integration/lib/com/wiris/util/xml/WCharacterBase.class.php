<?php

class com_wiris_util_xml_WCharacterBase {
	public function __construct(){}
	static $NEGATIVE_THIN_SPACE = 57344;
	static $LINE = 57345;
	static $SEGMENT = 57346;
	static $ALIGNMENT_MARK = 57358;
	static $ROOT = 61696;
	static $ROOT_VERTICAL = 61727;
	static $ROOT_NO_TAIL = 61728;
	static $ROOT_NO_TAIL_VERTICAL = 61759;
	static $ROOT_LEFT_TAIL = 61760;
	static $ROOT_VERTICAL_LINE = 61761;
	static $HORIZONTAL_TAB = 9;
	static $LINE_FEED = 10;
	static $CARRIAGE_RETURN = 13;
	static $SPACE = 32;
	static $NUMBER_SIGN = 35;
	static $ROUND_BRACKET_LEFT = 40;
	static $ROUND_BRACKET_RIGHT = 41;
	static $COMMA = 44;
	static $FULL_STOP = 46;
	static $SQUARE_BRACKET_LEFT = 91;
	static $BACKSLASH = 92;
	static $SQUARE_BRACKET_RIGHT = 93;
	static $CIRCUMFLEX_ACCENT = 94;
	static $LOW_LINE = 95;
	static $CURLY_BRACKET_LEFT = 123;
	static $VERTICAL_BAR = 124;
	static $CURLY_BRACKET_RIGHT = 125;
	static $TILDE = 126;
	static $MACRON = 175;
	static $COMBINING_LOW_LINE = 818;
	static $MODIFIER_LETTER_CIRCUMFLEX_ACCENT = 710;
	static $CARON = 711;
	static $NO_BREAK_SPACE = 160;
	static $EN_QUAD = 8192;
	static $EM_QUAD = 8193;
	static $EN_SPACE = 8194;
	static $EM_SPACE = 8195;
	static $THICK_SPACE = 8196;
	static $MID_SPACE = 8197;
	static $SIX_PER_EM_SPACE = 8198;
	static $FIGIRE_SPACE = 8199;
	static $PUNCTUATION_SPACE = 8200;
	static $THIN_SPACE = 8201;
	static $HAIR_SPACE = 8202;
	static $ZERO_WIDTH_SPACE = 8203;
	static $ZERO_WIDTH_NON_JOINER = 8204;
	static $ZERO_WIDTH_JOINER = 8205;
	static $DOUBLE_VERTICAL_BAR = 8214;
	static $DOUBLE_HORIZONTAL_BAR = 9552;
	static $NARROW_NO_BREAK_SPACE = 8239;
	static $MEDIUM_MATHEMATICAL_SPACE = 8287;
	static $WORD_JOINER = 8288;
	static $PLANCKOVER2PI = 8463;
	static $LEFTWARDS_ARROW = 8592;
	static $UPWARDS_ARROW = 8593;
	static $RIGHTWARDS_ARROW = 8594;
	static $DOWNWARDS_ARROW = 8595;
	static $LEFTRIGHT_ARROW = 8596;
	static $UP_DOWN_ARROW = 8597;
	static $LEFTWARDS_ARROW_FROM_BAR = 8612;
	static $RIGHTWARDS_ARROW_FROM_BAR = 8614;
	static $LEFTWARDS_ARROW_WITH_HOOK = 8617;
	static $RIGHTWARDS_ARROW_WITH_HOOK = 8618;
	static $LEFTWARDS_HARPOON_WITH_BARB_UPWARDS = 8636;
	static $RIGHTWARDS_HARPOON_WITH_BARB_UPWARDS = 8640;
	static $LEFTWARDS_DOUBLE_ARROW = 8656;
	static $RIGHTWARDS_DOUBLE_ARROW = 8658;
	static $LEFT_RIGHT_DOUBLE_ARROW = 8660;
	static $LEFTWARDS_ARROW_OVER_RIGHTWARDS_ARROW = 8646;
	static $RIGHTWARDS_ARROW_OVER_LEFTWARDS_ARROW = 8644;
	static $LEFTWARDS_HARPOON_OVER_RIGHTWARDS_HARPOON = 8651;
	static $RIGHTWARDS_HARPOON_OVER_LEFTWARDS_HARPOON = 8652;
	static $RIGHTWARDS_ARROW_ABOVE_SHORT_LEFTWARDS_ARROW = 10562;
	static $SHORT_RIGHTWARDS_ARROW_ABOVE_LEFTWARDS_ARROW = 10564;
	static $LONG_RIGHTWARDS_ARROW = 10230;
	static $LONG_LEFTWARDS_ARROW = 10229;
	static $LONG_LEFT_RIGHT_ARROW = 10231;
	static $LONG_LEFTWARDS_DOUBLE_ARROW = 10232;
	static $LONG_RIGHTWARDS_DOUBLE_ARROW = 10233;
	static $LONG_LEFT_RIGHT_DOUBLE_ARROW = 10234;
	static $INTEGRAL = 8747;
	static $TOP_HALF_INTEGRAL = 8992;
	static $BOTTOM_HALF_INTEGRAL = 8993;
	static $INTEGRAL_EXTENSION = 9134;
	static $VERTICAL_LINE_EXTENSION = 9168;
	static $TILDE_OPERATOR = 8764;
	static $LEFT_CEILING = 8968;
	static $RIGHT_CEILING = 8969;
	static $LEFT_FLOOR = 8970;
	static $RIGHT_FLOOR = 8971;
	static $TOP_PARENTHESIS = 9180;
	static $BOTTOM_PARENTHESIS = 9181;
	static $TOP_SQUARE_BRACKET = 9140;
	static $BOTTOM_SQUARE_BRACKET = 9141;
	static $TOP_CURLY_BRACKET = 9182;
	static $BOTTOM_CURLY_BRACKET = 9183;
	static $MATHEMATICAL_LEFT_ANGLE_BRACKET = 10216;
	static $MATHEMATICAL_RIGHT_ANGLE_BRACKET = 10217;
	static $DOUBLE_STRUCK_ITALIC_CAPITAL_D = 8517;
	static $DOUBLE_STRUCK_ITALIC_SMALL_D = 8518;
	static $DOUBLE_STRUCK_ITALIC_SMALL_E = 8519;
	static $DOUBLE_STRUCK_ITALIC_SMALL_I = 8520;
	static $EPSILON = 949;
	static $VAREPSILON = 1013;
	static $DIGIT_ZERO = 48;
	static $DIGIT_NINE = 57;
	static $LATIN_CAPITAL_LETTER_A = 65;
	static $LATIN_CAPITAL_LETTER_Z = 90;
	static $LATIN_SMALL_LETTER_A = 97;
	static $LATIN_SMALL_LETTER_Z = 122;
	static $MATHEMATICAL_SCRIPT_CAPITAL_A = 119964;
	static $MATHEMATICAL_SCRIPT_SMALL_A = 119990;
	static $MATHEMATICAL_FRAKTUR_CAPITAL_A = 120068;
	static $MATHEMATICAL_FRAKTUR_SMALL_A = 120094;
	static $MATHEMATICAL_DOUBLE_STRUCK_CAPITAL_A = 120120;
	static $MATHEMATICAL_DOUBLE_STRUCK_SMALL_A = 120146;
	static $MATHEMATICAL_DOUBLE_STRUCK_DIGIT_ZERO = 120792;
	static $MIDDLE_DOT = 183;
	static $DOT_ABOVE = 729;
	static $ARABIC_DECIMAL_SEPARATOR = 1643;
	static $ARABIC_THOUSANDS_SEPARATOR = 1644;
	static $ALIGNMENT_RELATION_OPERATORS;
	static function isDigit($c) {
		if(48 <= $c && $c <= 57) {
			return true;
		}
		if(1632 <= $c && $c <= 1641) {
			return true;
		}
		if(1776 <= $c && $c <= 1785) {
			return true;
		}
		if(2790 <= $c && $c <= 2799) {
			return true;
		}
		return false;
	}
	static function isIdentifier($c) {
		return com_wiris_util_xml_WCharacterBase::isLetter($c) || com_wiris_util_xml_WCharacterBase::isCombiningCharacter($c) || $c === 95;
	}
	static function isLarge($c) {
		return com_wiris_util_xml_WCharacterBase::binarySearch(com_wiris_util_xml_WCharacterBase::$largeOps, $c);
	}
	static function isVeryLarge($c) {
		return com_wiris_util_xml_WCharacterBase::binarySearch(com_wiris_util_xml_WCharacterBase::$veryLargeOps, $c);
	}
	static function isBinaryOp($c) {
		return com_wiris_util_xml_WCharacterBase::binarySearch(com_wiris_util_xml_WCharacterBase::$binaryOps, $c);
	}
	static function isRelation($c) {
		return com_wiris_util_xml_WCharacterBase::binarySearch(com_wiris_util_xml_WCharacterBase::$relations, $c);
	}
	static function binarySearch($v, $c) {
		$min = 0;
		$max = $v->length - 1;
		do {
			$mid = intval(($min + $max) / 2);
			$cc = $v[$mid];
			if($c === $cc) {
				return true;
			} else {
				if($c < $cc) {
					$max = $mid - 1;
				} else {
					$min = $mid + 1;
				}
			}
			unset($mid,$cc);
		} while($min <= $max);
		return false;
	}
	static $binaryOps;
	static $relations;
	static $largeOps;
	static $veryLargeOps;
	static $tallLetters;
	static $longLetters;
	static $negations;
	static $mirrorDictionary;
	static $subSuperScriptDictionary;
	static $accentsDictionary = null;
	static function initAccentsDictionary() {
		if(com_wiris_util_xml_WCharacterBase::$accentsDictionary !== null) {
			return;
		}
		$h = new Hash();
		$h->set("A", new _hx_array(array(192, 193, 194, 195, 196, 197, 256, 258, 260, 461, 478, 480, 506, 512, 514, 550, 570, 7680, 7840, 7842, 7844, 7846, 7848, 7850, 7852, 7854, 7856, 7858, 7860, 7862, 9398, 11375, 65313)));
		$h->set("AA", new _hx_array(array(42802)));
		$h->set("AE", new _hx_array(array(198, 482, 508)));
		$h->set("AO", new _hx_array(array(42804)));
		$h->set("AU", new _hx_array(array(42806)));
		$h->set("AV", new _hx_array(array(42808, 42810)));
		$h->set("AY", new _hx_array(array(42812)));
		$h->set("B", new _hx_array(array(385, 386, 579, 7682, 7684, 7686, 9399, 65314)));
		$h->set("C", new _hx_array(array(199, 262, 264, 266, 268, 391, 571, 7688, 9400, 42814, 65315)));
		$h->set("D", new _hx_array(array(208, 270, 272, 393, 394, 395, 7690, 7692, 7694, 7696, 7698, 9401, 42873, 65316)));
		$h->set("DZ", new _hx_array(array(452, 497)));
		$h->set("Dz", new _hx_array(array(453, 498)));
		$h->set("E", new _hx_array(array(200, 201, 202, 203, 274, 276, 278, 280, 282, 398, 400, 516, 518, 552, 7700, 7702, 7704, 7706, 7708, 7864, 7866, 7868, 7870, 7872, 7874, 7876, 7878, 9402, 65317)));
		$h->set("F", new _hx_array(array(401, 7710, 9403, 42875, 65318)));
		$h->set("G", new _hx_array(array(284, 286, 288, 290, 403, 484, 486, 500, 7712, 9404, 42877, 42878, 42912, 65319)));
		$h->set("H", new _hx_array(array(292, 294, 542, 7714, 7716, 7718, 7720, 7722, 9405, 11367, 11381, 42893, 65320)));
		$h->set("I", new _hx_array(array(204, 205, 206, 207, 296, 298, 300, 302, 304, 407, 463, 520, 522, 7724, 7726, 7880, 7882, 9406, 65321)));
		$h->set("J", new _hx_array(array(308, 584, 9407, 65322)));
		$h->set("K", new _hx_array(array(310, 408, 488, 7728, 7730, 7732, 9408, 11369, 42816, 42818, 42820, 42914, 65323)));
		$h->set("L", new _hx_array(array(313, 315, 317, 319, 321, 573, 7734, 7736, 7738, 7740, 9409, 11360, 11362, 42822, 42824, 42880, 65324)));
		$h->set("LJ", new _hx_array(array(455)));
		$h->set("Lj", new _hx_array(array(456)));
		$h->set("M", new _hx_array(array(412, 7742, 7744, 7746, 9410, 11374, 65325)));
		$h->set("N", new _hx_array(array(209, 323, 325, 327, 413, 504, 544, 7748, 7750, 7752, 7754, 9411, 42896, 42916, 65326)));
		$h->set("NJ", new _hx_array(array(458)));
		$h->set("Nj", new _hx_array(array(459)));
		$h->set("O", new _hx_array(array(210, 211, 212, 213, 214, 216, 332, 334, 336, 390, 415, 416, 465, 490, 492, 510, 524, 526, 554, 556, 558, 560, 7756, 7758, 7760, 7762, 7884, 7886, 7888, 7890, 7892, 7894, 7896, 7898, 7900, 7902, 7904, 7906, 9412, 42826, 42828, 65327)));
		$h->set("OI", new _hx_array(array(418)));
		$h->set("OO", new _hx_array(array(42830)));
		$h->set("OU", new _hx_array(array(546)));
		$h->set("OE", new _hx_array(array(140, 338)));
		$h->set("oe", new _hx_array(array(156, 339)));
		$h->set("P", new _hx_array(array(420, 7764, 7766, 9413, 11363, 42832, 42834, 42836, 65328)));
		$h->set("Q", new _hx_array(array(586, 9414, 42838, 42840, 65329)));
		$h->set("R", new _hx_array(array(340, 342, 344, 528, 530, 588, 7768, 7770, 7772, 7774, 9415, 11364, 42842, 42882, 42918, 65330)));
		$h->set("S", new _hx_array(array(346, 348, 350, 352, 536, 7776, 7778, 7780, 7782, 7784, 7838, 9416, 11390, 42884, 42920, 65331)));
		$h->set("T", new _hx_array(array(354, 356, 358, 428, 430, 538, 574, 7786, 7788, 7790, 7792, 9417, 42886, 65332)));
		$h->set("TZ", new _hx_array(array(42792)));
		$h->set("U", new _hx_array(array(217, 218, 219, 220, 360, 362, 364, 366, 368, 370, 431, 467, 469, 471, 473, 475, 532, 534, 580, 7794, 7796, 7798, 7800, 7802, 7908, 7910, 7912, 7914, 7916, 7918, 7920, 9418, 65333)));
		$h->set("V", new _hx_array(array(434, 581, 7804, 7806, 9419, 42846, 65334)));
		$h->set("VY", new _hx_array(array(42848)));
		$h->set("W", new _hx_array(array(372, 7808, 7810, 7812, 7814, 7816, 9420, 11378, 65335)));
		$h->set("X", new _hx_array(array(7818, 7820, 9421, 65336)));
		$h->set("Y", new _hx_array(array(221, 374, 376, 435, 562, 590, 7822, 7922, 7924, 7926, 7928, 7934, 9422, 65337)));
		$h->set("Z", new _hx_array(array(377, 379, 381, 437, 548, 7824, 7826, 7828, 9423, 11371, 11391, 42850, 65338)));
		$h->set("a", new _hx_array(array(224, 225, 226, 227, 228, 229, 257, 259, 261, 462, 479, 481, 507, 513, 515, 551, 592, 7681, 7834, 7841, 7843, 7845, 7847, 7849, 7851, 7853, 7855, 7857, 7859, 7861, 7863, 9424, 11365, 65345)));
		$h->set("aa", new _hx_array(array(42803)));
		$h->set("ae", new _hx_array(array(230, 483, 509)));
		$h->set("ao", new _hx_array(array(42805)));
		$h->set("au", new _hx_array(array(42807)));
		$h->set("av", new _hx_array(array(42809, 42811)));
		$h->set("ay", new _hx_array(array(42813)));
		$h->set("b", new _hx_array(array(384, 387, 595, 7683, 7685, 7687, 9425, 65346)));
		$h->set("c", new _hx_array(array(231, 263, 265, 267, 269, 392, 572, 7689, 8580, 9426, 42815, 65347)));
		$h->set("d", new _hx_array(array(271, 273, 396, 598, 599, 7691, 7693, 7695, 7697, 7699, 9427, 42874, 65348)));
		$h->set("dz", new _hx_array(array(454, 499)));
		$h->set("e", new _hx_array(array(232, 233, 234, 235, 275, 277, 279, 281, 283, 477, 517, 519, 553, 583, 603, 7701, 7703, 7705, 7707, 7709, 7865, 7867, 7869, 7871, 7873, 7875, 7877, 7879, 9428, 65349)));
		$h->set("f", new _hx_array(array(402, 7711, 9429, 42876, 65350)));
		$h->set("g", new _hx_array(array(285, 287, 289, 291, 485, 487, 501, 608, 7545, 7713, 9430, 42879, 42913, 65351)));
		$h->set("h", new _hx_array(array(293, 295, 543, 613, 7715, 7717, 7719, 7721, 7723, 7830, 9431, 11368, 11382, 65352)));
		$h->set("hv", new _hx_array(array(405)));
		$h->set("i", new _hx_array(array(236, 237, 238, 239, 297, 299, 301, 303, 305, 464, 521, 523, 616, 7725, 7727, 7881, 7883, 9432, 65353)));
		$h->set("j", new _hx_array(array(309, 496, 585, 9433, 65354)));
		$h->set("k", new _hx_array(array(311, 409, 489, 7729, 7731, 7733, 9434, 11370, 42817, 42819, 42821, 42915, 65355)));
		$h->set("l", new _hx_array(array(314, 316, 318, 320, 322, 383, 410, 619, 7735, 7737, 7739, 7741, 9435, 11361, 42823, 42825, 42881, 65356)));
		$h->set("lj", new _hx_array(array(457)));
		$h->set("m", new _hx_array(array(623, 625, 7743, 7745, 7747, 9436, 65357)));
		$h->set("n", new _hx_array(array(241, 324, 326, 328, 329, 414, 505, 626, 7749, 7751, 7753, 7755, 9437, 42897, 42917, 65358)));
		$h->set("nj", new _hx_array(array(460)));
		$h->set("o", new _hx_array(array(242, 243, 244, 245, 246, 248, 333, 335, 337, 417, 466, 491, 493, 511, 525, 527, 555, 557, 559, 561, 596, 629, 7757, 7759, 7761, 7763, 7885, 7887, 7889, 7891, 7893, 7895, 7897, 7899, 7901, 7903, 7905, 7907, 9438, 42827, 42829, 65359)));
		$h->set("oi", new _hx_array(array(419)));
		$h->set("ou", new _hx_array(array(547)));
		$h->set("oo", new _hx_array(array(42831)));
		$h->set("p", new _hx_array(array(421, 7549, 7765, 7767, 9439, 42833, 42835, 42837, 65360)));
		$h->set("q", new _hx_array(array(587, 9440, 42839, 42841, 65361)));
		$h->set("r", new _hx_array(array(341, 343, 345, 529, 531, 589, 637, 7769, 7771, 7773, 7775, 9441, 42843, 42883, 42919, 65362)));
		$h->set("s", new _hx_array(array(223, 347, 349, 351, 353, 537, 575, 7777, 7779, 7781, 7783, 7785, 7835, 9442, 42885, 42921, 65363)));
		$h->set("t", new _hx_array(array(355, 357, 359, 429, 539, 648, 7787, 7789, 7791, 7793, 7831, 9443, 11366, 42887, 65364)));
		$h->set("tz", new _hx_array(array(42793)));
		$h->set("u", new _hx_array(array(249, 250, 251, 252, 361, 363, 365, 367, 369, 371, 432, 468, 470, 472, 474, 476, 533, 535, 649, 7795, 7797, 7799, 7801, 7803, 7909, 7911, 7913, 7915, 7917, 7919, 7921, 9444, 65365)));
		$h->set("v", new _hx_array(array(651, 652, 7805, 7807, 9445, 42847, 65366)));
		$h->set("vy", new _hx_array(array(42849)));
		$h->set("w", new _hx_array(array(373, 7809, 7811, 7813, 7815, 7817, 7832, 9446, 11379, 65367)));
		$h->set("x", new _hx_array(array(7819, 7821, 9447, 65368)));
		$h->set("y", new _hx_array(array(253, 255, 375, 436, 563, 591, 7823, 7833, 7923, 7925, 7927, 7929, 7935, 9448, 65369)));
		$h->set("z", new _hx_array(array(378, 380, 382, 438, 549, 576, 7825, 7827, 7829, 9449, 11372, 42851, 65370)));
		com_wiris_util_xml_WCharacterBase::$accentsDictionary = $h;
	}
	static $horizontalLTRStretchyChars;
	static $tallAccents;
	static $PUNCTUATION_CATEGORY = "P";
	static $OTHER_CATEGORY = "C";
	static $LETTER_CATEGORY = "L";
	static $MARK_CATEGORY = "M";
	static $NUMBER_CATEGORY = "N";
	static $SYMBOL_CATEGORY = "S";
	static $PHONETICAL_CATEGORY = "F";
	static $UNICODES_WITH_CATEGORIES = "@P:21-23,25-2A,2C-2F,3A-3B,3F-40,5B-5D,5F,7B,7D,A1,A7,AB,B6-B7,BB,BF,37E,387,55A-55F,589-58A,5BE,5C0,5C3,5C6,5F3-5F4,609-60A,60C-60D,61B,61E-61F,66A-66D,6D4,E4F,E5A-E5B,2010-2022,2025-2026,2030-203E,2040,2043,2047,204E-2051,2057,205E,2308-230B,2329-232A,2772-2773,27C5-27C6,27E6-27EF,2983-2998,29D8-29DB,29FC-29FD,2E17,3030,FD3E-FD3F@C:AD,600-603,6DD,200B-200F,202A-202E,206A-206F@L:41-5A,61-7A,AA,B5,BA,C0-D6,D8-F6,F8-2C1,2C6-2D1,2E0-2E4,2EC,2EE,370-374,376-377,37A-37D,386,388-38A,38C,38E-3A1,3A3-3F5,3F7-481,48A-527,531-556,559,561-587,5D0-5EA,5F0-5F2,620-64A,66E-66F,671-6D3,6D5,6E5-6E6,6EE-6EF,6FA-6FC,6FF,750-77F,E01-E30,E32-E33,E40-E46,1D00-1DBF,1E00-1F15,1F18-1F1D,1F20-1F45,1F48-1F4D,1F50-1F57,1F59,1F5B,1F5D,1F5F-1F7D,1F80-1FB4,1FB6-1FBC,1FBE,1FC2-1FC4,1FC6-1FCC,1FD0-1FD3,1FD6-1FDB,1FE0-1FEC,1FF2-1FF4,1FF6-1FFC,207F,2090-2094,2102,2107,210A-2113,2115,2119-211D,2124,2126,2128,212B-212D,212F-2138,213C-213F,2145-2149,214E,2184,2C60-2C7F,306E,A717-A71F,A727,A788,A78B-A78C,A792,FB00-FB04,FB13-FB17,FB1D,FB1F-FB28,FB2A-FB36,FB38-FB3C,FB3E,FB40-FB41,FB43-FB44,FB46-FBB1,FBD3-FBE9,FBFC-FBFF,FC5E-FC63,FC6A,FC6D,FC70,FC73,FC91,FC94,FDF2,FE70-FE74,FE76-FEFC,1D400-1D454,1D456-1D49C,1D49E-1D49F,1D4A2,1D4A5-1D4A6,1D4A9-1D4AC,1D4AE-1D4B9,1D4BB,1D4BD-1D4C3,1D4C5-1D505,1D507-1D50A,1D50D-1D514,1D516-1D51C,1D51E-1D539,1D53B-1D53E,1D540-1D544,1D546,1D54A-1D550,1D552-1D6A5,1D6A8-1D6C0,1D6C2-1D6DA,1D6DC-1D6FA,1D6FC-1D714,1D716-1D734,1D736-1D74E,1D750-1D76E,1D770-1D788,1D78A-1D7A8,1D7AA-1D7C2,1D7C4-1D7C9@M:300-36F,483-489,591-5BD,5BF,5C1-5C2,5C4-5C5,5C7,610-61A,64B-65F,670,6D6-6DC,6DF-6E4,6E7-6E8,6EA-6ED,E31,E34-E3A,E47-E4E,1DC0-1DC1,1DC3,1DCA,1DFE-1DFF,20D0-20D2,20D6-20D7,20DB-20DF,20E1,20E4-20F0,FB1E,FE20-FE23@N:30-39,B2-B3,B9,BC-BE,660-669,6F0-6F9,E50-E59,2070,2074-2079,2080-2089,2153-215E,2460-2468,24EA,2780-2793,1D7CE-1D7FF@S:24,2B,3C-3E,5E,60,7C,7E,A2-A6,A8-A9,AC,AE-B1,B4,B8,D7,F7,2C2-2C5,2D2-2DF,2E5-2EB,2ED,2EF-2FF,375,384-385,3F6,482,58F,606-608,60B,60E-60F,6DE,6E9,6FD-6FE,E3F,1FBD,1FBF-1FC1,1FCD-1FCF,1FDD-1FDF,1FED-1FEF,1FFD-1FFE,2044,2052,20A0-20BA,2105,2116-2118,211E,2120,2122,2125,2127,2129,212E,2140-2144,214A-214B,214D,2190-21EA,21F4-2300,2302,2305-2306,230C-2313,2315-231A,231C-2323,232C-232E,2332,2336,233D,233F-2340,2353,2370,237C,2393-2394,23AF,23B4-23B6,23CE,23D0,23DC-23E7,2423,24B6-24E9,2500,2502,2506,2508,250A,250C,2510,2514,2518,251C,2524,252C,2534,253C,2550-256C,2571-2572,2580,2584,2588,258C,2590-2593,25A0-25FF,2605-2606,2609,260C,260E,2612,2621,2639-2644,2646-2649,2660-2667,2669-266B,266D-266F,267E,2680-2689,26A0,26A5,26AA-26AC,26B2,26E2,2702,2709,2713,2720,272A,2736,273D,279B,27C0-27C4,27C7-27C9,27CB-27CD,27D0-27E5,27F0-27FF,2900-2982,2999-29D7,29DC-29FB,29FE-2AFF,2B12-2B4C,2B50-2B54,3012,A720-A721,A789-A78A,FB29,FBB2-FBC1,FDFC,FFFC-FFFD,1D6C1,1D6DB,1D6FB,1D715,1D735,1D74F,1D76F,1D789,1D7A9,1D7C3@F:70,62,74,64,288,256,63,25F,6B,261,71,262,294,6D,271,6E,273,272,14B,274,72,280,27E,27D,278,3B2,66,76,3B8,F0,73,7A,283,292,282,290,E7,29D,78,263,3C7,281,127,295,68,266,26C,26E,28B,279,27B,6A,270,6C,26D,28E,29F,1A5,253,1AD,257,188,284,199,260,2A0,29B,28D,77,265,29C,2A1-2A2,267,298,1C0,1C3,1C2,1C1,27A,255,291,2C71,287,297,296,286,293,27C,2E2,1AB,26B,67,2A6,2A3,2A7,2A4,2A8,2A5,1DBF,1D4A,1D91,1BB,29E,2E3,19E,19B,3BB,17E,161,1F0,10D,69,65,25B,61,251,254,6F,75,79,F8,153,276,252,28C,264,26F,268,289,26A,28F,28A,259,275,250,E6,25C,25A,131,25E,29A,258,277,269,2BC,325,30A,32C,2B0,324,330,33C,32A,33A-33B,339,31C,31F-320,308,33D,318-319,2DE,2B7,2B2,2E0,2E4,303,207F,2E1,31A,334,31D,2D4,31E,2D5,329,32F,361,35C,322,2F9,2C,2BB,307,2D7,2D6,2B8,323,321,32B,2C8,2CC,2D0-2D1,306,2E,7C,2016,203F,2197-2198,30B,301,304,300,30F,A71B-A71C,2E5-2E9,30C,302,1DC4-1DC5,1DC8,311,2C7,2C6,316,2CE,317,2CF,2AD,2A9-2AB,274D,2A,56,46,57,43,4C,4A,152,398,1D191,1D18F,31-33,346,34D,34A-34C,348-349,5C,34E,2193,2191,2EC,1DB9,362,347,2B6,2ED,2F1-2F2,2F7,41-42,44-45,47-49,4B,4D-55,58-5B,5D,2F,28-29,7B,7D@";
	static function getCategoriesUnicode() {
		$categoriesUnicode = new Hash();
		$categoriesUnicode->set(com_wiris_util_xml_WCharacterBase::$SYMBOL_CATEGORY, "SymbolUnicodeCategory");
		$categoriesUnicode->set(com_wiris_util_xml_WCharacterBase::$PUNCTUATION_CATEGORY, "PunctuationUnicodeCategory");
		$categoriesUnicode->set(com_wiris_util_xml_WCharacterBase::$LETTER_CATEGORY, "LetterUnicodeCategory");
		$categoriesUnicode->set(com_wiris_util_xml_WCharacterBase::$MARK_CATEGORY, "MarkUnicodeCategory");
		$categoriesUnicode->set(com_wiris_util_xml_WCharacterBase::$NUMBER_CATEGORY, "NumberUnicodeCategory");
		$categoriesUnicode->set(com_wiris_util_xml_WCharacterBase::$PHONETICAL_CATEGORY, "PhoneticalUnicodeCategory");
		$categoriesUnicode->set(com_wiris_util_xml_WCharacterBase::$OTHER_CATEGORY, "OtherUnicodeCategory");
		return $categoriesUnicode;
	}
	static function getUnicodeCategoryList($category) {
		$indexStart = _hx_index_of(com_wiris_util_xml_WCharacterBase::$UNICODES_WITH_CATEGORIES, "@" . $category . ":", null);
		$unicodes = _hx_substr(com_wiris_util_xml_WCharacterBase::$UNICODES_WITH_CATEGORIES, $indexStart + 3, null);
		$indexEnd = _hx_index_of($unicodes, "@", null);
		$unicodes = _hx_substr($unicodes, 0, $indexEnd);
		return com_wiris_util_xml_WCharacterBase::getUnicodesRangedStringList($unicodes);
	}
	static function getUnicodesRangedStringList($unicodesRangedList) {
		$inputList = _hx_explode(",", $unicodesRangedList);
		$unicodeList = new _hx_array(array());
		$i = 0;
		while($i < $inputList->length) {
			$actual_range = $inputList[$i];
			$actual_range = str_replace(" ", "", $actual_range);
			if(_hx_index_of($actual_range, "-", null) !== -1) {
				$firstRangeValueHex = com_wiris_util_xml_WCharacterBase::hexStringToUnicode(_hx_array_get(_hx_explode("-", $actual_range), 0));
				$lastRangeValueHex = com_wiris_util_xml_WCharacterBase::hexStringToUnicode(_hx_array_get(_hx_explode("-", $actual_range), 1));
				$actualValue = $firstRangeValueHex;
				while($actualValue <= $lastRangeValueHex) {
					$unicodeList->push(com_wiris_util_xml_WCharacterBase_0($actualValue, $actual_range, $firstRangeValueHex, $i, $inputList, $lastRangeValueHex, $unicodeList, $unicodesRangedList));
					$actualValue++;
				}
				unset($lastRangeValueHex,$firstRangeValueHex,$actualValue);
			} else {
				$actualValue = com_wiris_util_xml_WCharacterBase::hexStringToUnicode($actual_range);
				$unicodeList->push(com_wiris_util_xml_WCharacterBase_1($actualValue, $actual_range, $i, $inputList, $unicodeList, $unicodesRangedList));
				unset($actualValue);
			}
			$i++;
			unset($actual_range);
		}
		return $unicodeList;
	}
	static function hexStringToUnicode($unicode) {
		return Std::parseInt("0x" . $unicode);
	}
	static $invisible;
	static function getMirror($str) {
		$mirroredStr = "";
		$i = 0;
		while($i < strlen($str)) {
			$c = _hx_char_code_at($str, $i);
			$j = 0;
			while($j < com_wiris_util_xml_WCharacterBase::$mirrorDictionary->length) {
				if($c === com_wiris_util_xml_WCharacterBase::$mirrorDictionary[$j]) {
					$c = com_wiris_util_xml_WCharacterBase::$mirrorDictionary[$j + 1];
					break;
				}
				$j += 2;
			}
			$mirroredStr .= com_wiris_util_xml_WCharacterBase_2($c, $i, $j, $mirroredStr, $str);
			++$i;
			unset($j,$c);
		}
		return $mirroredStr;
	}
	static function isStretchyLTR($c) {
		$i = 0;
		while($i < com_wiris_util_xml_WCharacterBase::$horizontalLTRStretchyChars->length) {
			if($c === com_wiris_util_xml_WCharacterBase::$horizontalLTRStretchyChars[$i]) {
				return true;
			}
			++$i;
		}
		return false;
	}
	static function getNegated($c) {
		$i = 0;
		while($i < com_wiris_util_xml_WCharacterBase::$negations->length) {
			if(com_wiris_util_xml_WCharacterBase::$negations[$i] === $c) {
				return com_wiris_util_xml_WCharacterBase::$negations[$i + 1];
			}
			$i += 2;
		}
		return -1;
	}
	static function getNotNegated($c) {
		$i = 1;
		while($i < com_wiris_util_xml_WCharacterBase::$negations->length) {
			if(com_wiris_util_xml_WCharacterBase::$negations[$i] === $c) {
				return com_wiris_util_xml_WCharacterBase::$negations[$i - 1];
			}
			$i += 2;
		}
		return -1;
	}
	static function isCombining($s) {
		$it = com_wiris_system_Utf8::getIterator($s);
		while($it->hasNext()) {
			if(!com_wiris_util_xml_WCharacterBase::isCombiningCharacter($it->next())) {
				return false;
			}
		}
		return true;
	}
	static function isCombiningCharacter($c) {
		return $c >= 768 && $c <= 879 || $c >= 6832 && $c <= 6911 || $c >= 7616 && $c <= 7679 && ($c >= 8400 && $c <= 8447) && ($c >= 65056 && $c <= 65071);
	}
	static function isLetter($c) {
		if(com_wiris_util_xml_WCharacterBase::isDigit($c)) {
			return false;
		}
		if(65 <= $c && $c <= 90) {
			return true;
		}
		if(97 <= $c && $c <= 122) {
			return true;
		}
		if(192 <= $c && $c <= 696 && $c !== 215 && $c !== 247) {
			return true;
		}
		if(867 <= $c && $c <= 1521) {
			return true;
		}
		if(1552 <= $c && $c <= 8188) {
			return true;
		}
		if($c === 8472 || $c === 8467 || com_wiris_util_xml_WCharacterBase::isDoubleStruck($c) || com_wiris_util_xml_WCharacterBase::isFraktur($c) || com_wiris_util_xml_WCharacterBase::isScript($c)) {
			return true;
		}
		if(com_wiris_util_xml_WCharacterBase::isChinese($c)) {
			return true;
		}
		if(com_wiris_util_xml_WCharacterBase::isKorean($c)) {
			return true;
		}
		return false;
	}
	static function isUnicodeMathvariant($c) {
		return com_wiris_util_xml_WCharacterBase::isDoubleStruck($c) || com_wiris_util_xml_WCharacterBase::isFraktur($c) || com_wiris_util_xml_WCharacterBase::isScript($c);
	}
	static function isRequiredByQuizzes($c) {
		return $c === 120128 || $c === 8450 || $c === 8461 || $c === 8469 || $c === 8473 || $c === 8474 || $c === 8477 || $c === 8484;
	}
	static function isDoubleStruck($c) {
		return $c >= 120120 && $c <= 120171 || $c === 8450 || $c === 8461 || $c === 8469 || $c === 8473 || $c === 8474 || $c === 8477 || $c === 8484;
	}
	static function isFraktur($c) {
		return $c >= 120068 && $c <= 120119 || $c === 8493 || $c === 8460 || $c === 8465 || $c === 8476 || $c === 8488;
	}
	static function isScript($c) {
		return $c >= 119964 && $c <= 120015 || $c === 8458 || $c === 8459 || $c === 8466 || $c === 8464 || $c === 8499 || $c === 8500 || $c === 8492 || $c === 8495 || $c === 8496 || $c === 8497 || $c === 8475;
	}
	static function isLowerCase($c) {
		return $c >= 97 && $c <= 122 || $c >= 224 && $c <= 255 || $c >= 591 && $c >= 659 || $c >= 661 && $c <= 687 || $c >= 940 && $c <= 974;
	}
	static function isWord($c) {
		if(com_wiris_util_xml_WCharacterBase::isDevanagari($c) || com_wiris_util_xml_WCharacterBase::isChinese($c) || com_wiris_util_xml_WCharacterBase::isHebrew($c) || com_wiris_util_xml_WCharacterBase::isThai($c) || com_wiris_util_xml_WCharacterBase::isGujarati($c) || com_wiris_util_xml_WCharacterBase::isKorean($c)) {
			return true;
		}
		return false;
	}
	static function isArabianString($s) {
		$i = strlen($s) - 1;
		while($i >= 0) {
			if(!com_wiris_util_xml_WCharacterBase::isArabian(_hx_char_code_at($s, $i))) {
				return false;
			}
			--$i;
		}
		return true;
	}
	static function isArabian($c) {
		if($c >= 1536 && $c <= 1791 && !com_wiris_util_xml_WCharacterBase::isDigit($c)) {
			return true;
		}
		return false;
	}
	static function isHebrew($c) {
		if($c >= 1424 && $c <= 1535) {
			return true;
		}
		return false;
	}
	static function isChinese($c) {
		if($c >= 13312 && $c <= 40959) {
			return true;
		}
		return false;
	}
	static function isKorean($c) {
		if($c >= 12593 && $c <= 52044) {
			return true;
		}
		return false;
	}
	static function isGreek($c) {
		if($c >= 945 && $c <= 969) {
			return true;
		} else {
			if($c >= 913 && $c <= 937 && $c !== 930) {
				return true;
			} else {
				if($c === 977 || $c === 981 || $c === 982) {
					return true;
				}
			}
		}
		return false;
	}
	static function isDevanagari($c) {
		if($c >= 2304 && $c < 2431) {
			return true;
		}
		return false;
	}
	static function isGujarati($c) {
		if($c >= 2689 && $c < 2788 || $c === 2800 || $c === 2801) {
			return true;
		}
		return false;
	}
	static function isThai($c) {
		if(3585 <= $c && $c < 3676) {
			return true;
		}
		return false;
	}
	static function isDevanagariString($s) {
		$i = strlen($s) - 1;
		while($i >= 0) {
			if(!com_wiris_util_xml_WCharacterBase::isDevanagari(_hx_char_code_at($s, $i))) {
				return false;
			}
			--$i;
		}
		return true;
	}
	static function isRTL($c) {
		if(com_wiris_util_xml_WCharacterBase::isHebrew($c) || com_wiris_util_xml_WCharacterBase::isArabian($c)) {
			return true;
		}
		return false;
	}
	static function isTallLetter($c) {
		if(97 <= $c && $c <= 122 || 945 <= $c && $c <= 969) {
			return com_wiris_util_xml_WCharacterBase::binarySearch(com_wiris_util_xml_WCharacterBase::$tallLetters, $c);
		}
		return true;
	}
	static function isLongLetter($c) {
		if(97 <= $c && $c <= 122 || 945 <= $c && $c <= 969) {
			return com_wiris_util_xml_WCharacterBase::binarySearch(com_wiris_util_xml_WCharacterBase::$longLetters, $c);
		} else {
			if(65 <= $c && $c <= 90) {
				return false;
			}
		}
		return true;
	}
	static function isLTRNumber($text) {
		$i = 0;
		$n = haxe_Utf8::length($text);
		while($i < $n) {
			if(!com_wiris_util_xml_WCharacterBase::isDigit(haxe_Utf8::charCodeAt($text, $i))) {
				return false;
			}
			++$i;
		}
		return true;
	}
	static function isSuperscript($c) {
		return $c === 178 || $c === 179 || $c === 185 || $c >= 8304 && $c <= 8319 && $c !== 8306 && $c !== 8307;
	}
	static function isSubscript($c) {
		return $c >= 8320 && $c <= 8348 && $c !== 8335;
	}
	static function isSuperscriptOrSubscript($c) {
		return com_wiris_util_xml_WCharacterBase::isSuperscript($c) || com_wiris_util_xml_WCharacterBase::isSubscript($c);
	}
	static function normalizeSubSuperScript($c) {
		$i = 0;
		$n = com_wiris_util_xml_WCharacterBase::$subSuperScriptDictionary->length;
		while($i < $n) {
			if(com_wiris_util_xml_WCharacterBase::$subSuperScriptDictionary[$i] === $c) {
				return com_wiris_util_xml_WCharacterBase::$subSuperScriptDictionary[$i + 1];
			}
			$i += 2;
		}
		return $c;
	}
	static function isInvisible($c) {
		return com_wiris_util_xml_WCharacterBase::binarySearch(com_wiris_util_xml_WCharacterBase::$invisible, $c);
	}
	static $horizontalOperators;
	static function isHorizontalOperator($c) {
		return com_wiris_util_xml_WCharacterBase::binarySearch(com_wiris_util_xml_WCharacterBase::$horizontalOperators, $c);
	}
	static $latinLetters;
	static $greekLetters;
	static function latin2Greek($l) {
		$index = -1;
		if($l < 100) {
			$index = _hx_index_of(com_wiris_util_xml_WCharacterBase::$latinLetters, "@00" . _hx_string_rec($l, "") . "@", null);
		} else {
			if($l < 1000) {
				$index = _hx_index_of(com_wiris_util_xml_WCharacterBase::$latinLetters, "@0" . _hx_string_rec($l, "") . "@", null);
			} else {
				$index = _hx_index_of(com_wiris_util_xml_WCharacterBase::$latinLetters, "@" . _hx_string_rec($l, "") . "@", null);
			}
		}
		if($index !== -1) {
			$s = _hx_substr(com_wiris_util_xml_WCharacterBase::$greekLetters, $index + 1, 4);
			return Std::parseInt($s);
		}
		return $l;
	}
	static function greek2Latin($g) {
		$index = -1;
		if($g < 100) {
			$index = _hx_index_of(com_wiris_util_xml_WCharacterBase::$greekLetters, "@00" . _hx_string_rec($g, "") . "@", null);
		} else {
			if($g < 1000) {
				$index = _hx_index_of(com_wiris_util_xml_WCharacterBase::$greekLetters, "@0" . _hx_string_rec($g, "") . "@", null);
			} else {
				$index = _hx_index_of(com_wiris_util_xml_WCharacterBase::$greekLetters, "@" . _hx_string_rec($g, "") . "@", null);
			}
		}
		if($index !== -1) {
			$s = _hx_substr(com_wiris_util_xml_WCharacterBase::$latinLetters, $index + 1, 4);
			return Std::parseInt($s);
		}
		return $g;
	}
	static function isOp($c) {
		return com_wiris_util_xml_WCharacterBase::isLarge($c) || com_wiris_util_xml_WCharacterBase::isVeryLarge($c) || com_wiris_util_xml_WCharacterBase::isBinaryOp($c) || com_wiris_util_xml_WCharacterBase::isRelation($c) || $c === _hx_char_code_at(".", 0) || $c === _hx_char_code_at(",", 0) || $c === _hx_char_code_at(":", 0);
	}
	static function isTallAccent($c) {
		$i = 0;
		while($i < com_wiris_util_xml_WCharacterBase::$tallAccents->length) {
			if($c === com_wiris_util_xml_WCharacterBase::$tallAccents[$i]) {
				return true;
			}
			++$i;
		}
		return false;
	}
	static function isDisplayedWithStix($c) {
		if($c >= 592 && $c <= 687) {
			return true;
		}
		if($c >= 688 && $c <= 767) {
			return true;
		}
		if($c >= 8215 && $c <= 8233 || $c >= 8241 && $c <= 8303) {
			return true;
		}
		if($c >= 8304 && $c <= 8351) {
			return true;
		}
		if($c >= 8400 && $c <= 8447) {
			return true;
		}
		if($c >= 8448 && $c <= 8527) {
			return true;
		}
		if($c >= 8528 && $c <= 8591) {
			return true;
		}
		if($c >= 8592 && $c <= 8703) {
			return true;
		}
		if($c >= 8704 && $c <= 8959) {
			return true;
		}
		if($c >= 8960 && $c <= 9215) {
			return true;
		}
		if($c >= 9312 && $c <= 9471) {
			return true;
		}
		if($c >= 9472 && $c <= 9599) {
			return true;
		}
		if($c >= 9600 && $c <= 9631) {
			return true;
		}
		if($c >= 9632 && $c <= 9727) {
			return true;
		}
		if($c >= 9728 && $c <= 9983) {
			return true;
		}
		if($c >= 9984 && $c <= 10175) {
			return true;
		}
		if($c >= 10176 && $c <= 10223) {
			return true;
		}
		if($c >= 10224 && $c <= 10239) {
			return true;
		}
		if($c >= 10240 && $c <= 10495) {
			return true;
		}
		if($c >= 10496 && $c <= 10623) {
			return true;
		}
		if($c >= 10624 && $c <= 10751) {
			return true;
		}
		if($c >= 10752 && $c <= 11007) {
			return true;
		}
		if($c >= 11008 && $c <= 11263) {
			return true;
		}
		if($c >= 12288 && $c <= 12351) {
			return true;
		}
		if($c >= 57344 && $c <= 65535) {
			return true;
		}
		if($c >= 119808 && $c <= 119963 || $c >= 120224 && $c <= 120831) {
			return true;
		}
		if($c === 12398 || $c === 42791 || $c === 42898) {
			return true;
		}
		return false;
	}
	static function latinToDoublestruck($codepoint) {
		if($codepoint === 67) {
			return 8450;
		} else {
			if($codepoint === 72) {
				return 8461;
			} else {
				if($codepoint === 78) {
					return 8469;
				} else {
					if($codepoint === 80) {
						return 8473;
					} else {
						if($codepoint === 81) {
							return 8474;
						} else {
							if($codepoint === 82) {
								return 8477;
							} else {
								if($codepoint === 90) {
									return 8484;
								} else {
									if($codepoint >= com_wiris_util_xml_WCharacterBase::$LATIN_CAPITAL_LETTER_A && $codepoint <= com_wiris_util_xml_WCharacterBase::$LATIN_CAPITAL_LETTER_Z) {
										return $codepoint + (com_wiris_util_xml_WCharacterBase::$MATHEMATICAL_DOUBLE_STRUCK_CAPITAL_A - com_wiris_util_xml_WCharacterBase::$LATIN_CAPITAL_LETTER_A);
									} else {
										if($codepoint >= com_wiris_util_xml_WCharacterBase::$LATIN_SMALL_LETTER_A && $codepoint <= com_wiris_util_xml_WCharacterBase::$LATIN_SMALL_LETTER_Z) {
											return $codepoint + (com_wiris_util_xml_WCharacterBase::$MATHEMATICAL_DOUBLE_STRUCK_SMALL_A - com_wiris_util_xml_WCharacterBase::$LATIN_SMALL_LETTER_A);
										} else {
											if($codepoint >= com_wiris_util_xml_WCharacterBase::$DIGIT_ZERO && $codepoint <= com_wiris_util_xml_WCharacterBase::$DIGIT_NINE) {
												return $codepoint + (com_wiris_util_xml_WCharacterBase::$MATHEMATICAL_DOUBLE_STRUCK_DIGIT_ZERO - com_wiris_util_xml_WCharacterBase::$DIGIT_ZERO);
											} else {
												return $codepoint;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	static function latinToScript($codepoint) {
		if($codepoint === 66) {
			return 8492;
		} else {
			if($codepoint === 69) {
				return 8496;
			} else {
				if($codepoint === 70) {
					return 8497;
				} else {
					if($codepoint === 72) {
						return 8459;
					} else {
						if($codepoint === 73) {
							return 8464;
						} else {
							if($codepoint === 76) {
								return 8466;
							} else {
								if($codepoint === 77) {
									return 8499;
								} else {
									if($codepoint === 82) {
										return 8475;
									} else {
										if($codepoint === 101) {
											return 8495;
										} else {
											if($codepoint === 103) {
												return 8458;
											} else {
												if($codepoint === 111) {
													return 8500;
												} else {
													if($codepoint >= com_wiris_util_xml_WCharacterBase::$LATIN_CAPITAL_LETTER_A && $codepoint <= com_wiris_util_xml_WCharacterBase::$LATIN_CAPITAL_LETTER_Z) {
														return $codepoint + (com_wiris_util_xml_WCharacterBase::$MATHEMATICAL_SCRIPT_CAPITAL_A - com_wiris_util_xml_WCharacterBase::$LATIN_CAPITAL_LETTER_A);
													} else {
														if($codepoint >= com_wiris_util_xml_WCharacterBase::$LATIN_SMALL_LETTER_A && $codepoint <= com_wiris_util_xml_WCharacterBase::$LATIN_SMALL_LETTER_Z) {
															return $codepoint + (com_wiris_util_xml_WCharacterBase::$MATHEMATICAL_SCRIPT_SMALL_A - com_wiris_util_xml_WCharacterBase::$LATIN_SMALL_LETTER_A);
														} else {
															return $codepoint;
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	static function latinToFraktur($codepoint) {
		if($codepoint === 67) {
			return 8493;
		} else {
			if($codepoint === 72) {
				return 8460;
			} else {
				if($codepoint === 73) {
					return 8465;
				} else {
					if($codepoint === 82) {
						return 8476;
					} else {
						if($codepoint === 90) {
							return 8488;
						} else {
							if($codepoint >= com_wiris_util_xml_WCharacterBase::$LATIN_CAPITAL_LETTER_A && $codepoint <= com_wiris_util_xml_WCharacterBase::$LATIN_CAPITAL_LETTER_Z) {
								return $codepoint + (com_wiris_util_xml_WCharacterBase::$MATHEMATICAL_FRAKTUR_CAPITAL_A - com_wiris_util_xml_WCharacterBase::$LATIN_CAPITAL_LETTER_A);
							} else {
								if($codepoint >= com_wiris_util_xml_WCharacterBase::$LATIN_SMALL_LETTER_A && $codepoint <= com_wiris_util_xml_WCharacterBase::$LATIN_SMALL_LETTER_Z) {
									return $codepoint + (com_wiris_util_xml_WCharacterBase::$MATHEMATICAL_FRAKTUR_SMALL_A - com_wiris_util_xml_WCharacterBase::$LATIN_SMALL_LETTER_A);
								} else {
									return $codepoint;
								}
							}
						}
					}
				}
			}
		}
	}
	static function stripAccent($c) {
		com_wiris_util_xml_WCharacterBase::initAccentsDictionary();
		if($c >= 128) {
			$i = com_wiris_util_xml_WCharacterBase::$accentsDictionary->keys();
			while($i->hasNext()) {
				$s = $i->next();
				$chars = com_wiris_util_xml_WCharacterBase::$accentsDictionary->get($s);
				if(com_wiris_util_xml_WCharacterBase::binarySearch($chars, $c)) {
					return $s;
				}
				unset($s,$chars);
			}
			return com_wiris_util_xml_WCharacterBase_3($c, $i);
		} else {
			return com_wiris_util_xml_WCharacterBase_4($c);
		}
	}
	function __toString() { return 'com.wiris.util.xml.WCharacterBase'; }
}
com_wiris_util_xml_WCharacterBase::$ALIGNMENT_RELATION_OPERATORS = new _hx_array(array(61, 8801, 8764, 8776, 8771, 8773, 8800, 8802, 8769, 62, 60, 8805, 8804, 10878, 10877, 10887, 10888, 8810, 8811, 8733, 8826, 8827, 8882, 8883));
com_wiris_util_xml_WCharacterBase::$binaryOps = new _hx_array(array(43, 45, 47, 177, 183, 215, 247, 8226, 8722, 8723, 8724, 8726, 8727, 8728, 8743, 8744, 8745, 8746, 8760, 8768, 8846, 8851, 8852, 8853, 8854, 8855, 8856, 8857, 8858, 8859, 8861, 8862, 8863, 8864, 8865, 8890, 8891, 8900, 8901, 8902, 8903, 8905, 8906, 8907, 8908, 8910, 8911, 8914, 8915, 8966, 9021, 9675, 10678, 10789, 10794, 10797, 10798, 10799, 10804, 10805, 10812, 10815, 10835, 10836, 10837, 10838, 10846, 10847, 10851));
com_wiris_util_xml_WCharacterBase::$relations = new _hx_array(array(60, 61, 62, 8592, 8593, 8594, 8595, 8596, 8597, 8598, 8599, 8600, 8601, 8602, 8603, 8604, 8605, 8606, 8608, 8610, 8611, 8614, 8617, 8618, 8619, 8620, 8621, 8622, 8624, 8625, 8627, 8630, 8631, 8636, 8637, 8638, 8639, 8640, 8641, 8642, 8643, 8644, 8645, 8646, 8647, 8648, 8649, 8650, 8651, 8652, 8653, 8654, 8655, 8656, 8657, 8658, 8659, 8660, 8661, 8666, 8667, 8669, 8693, 8712, 8713, 8715, 8716, 8733, 8739, 8740, 8741, 8742, 8764, 8765, 8769, 8770, 8771, 8772, 8773, 8774, 8775, 8776, 8777, 8778, 8779, 8781, 8782, 8783, 8784, 8785, 8786, 8787, 8788, 8789, 8790, 8791, 8793, 8794, 8795, 8796, 8799, 8800, 8801, 8802, 8804, 8805, 8806, 8807, 8808, 8809, 8810, 8811, 8812, 8814, 8815, 8816, 8817, 8818, 8819, 8820, 8821, 8822, 8823, 8824, 8825, 8826, 8827, 8828, 8829, 8830, 8831, 8832, 8833, 8834, 8835, 8836, 8837, 8838, 8839, 8840, 8841, 8842, 8843, 8847, 8848, 8849, 8850, 8866, 8867, 8869, 8871, 8872, 8873, 8874, 8875, 8876, 8877, 8878, 8879, 8882, 8883, 8884, 8885, 8886, 8887, 8888, 8904, 8909, 8912, 8913, 8918, 8919, 8920, 8921, 8922, 8923, 8926, 8927, 8930, 8931, 8934, 8935, 8936, 8937, 8938, 8939, 8940, 8941, 8994, 8995, 9123, 10229, 10230, 10231, 10232, 10233, 10234, 10236, 10239, 10501, 10514, 10515, 10531, 10532, 10533, 10534, 10535, 10536, 10537, 10538, 10547, 10550, 10551, 10560, 10561, 10562, 10564, 10567, 10574, 10575, 10576, 10577, 10578, 10579, 10580, 10581, 10582, 10583, 10584, 10585, 10586, 10587, 10588, 10589, 10590, 10591, 10592, 10593, 10606, 10607, 10608, 10620, 10621, 10869, 10877, 10878, 10885, 10886, 10887, 10888, 10889, 10890, 10891, 10892, 10901, 10902, 10909, 10910, 10913, 10914, 10927, 10928, 10933, 10934, 10935, 10936, 10937, 10938, 10949, 10950, 10955, 10956, 10987, 11005));
com_wiris_util_xml_WCharacterBase::$largeOps = new _hx_array(array(8719, 8720, 8721, 8896, 8897, 8898, 8899, 10756, 10757, 10758, 10759, 10760));
com_wiris_util_xml_WCharacterBase::$veryLargeOps = new _hx_array(array(8747, 8748, 8749, 8750, 8751, 8752, 8753, 8754, 8755, 10763, 10764, 10765, 10766, 10767, 10768, 10774, 10775, 10776, 10777, 10778, 10779, 10780));
com_wiris_util_xml_WCharacterBase::$tallLetters = new _hx_array(array(98, 100, 102, 104, 105, 106, 107, 108, 116, 946, 948, 950, 952, 955, 958));
com_wiris_util_xml_WCharacterBase::$longLetters = new _hx_array(array(103, 106, 112, 113, 121, 946, 947, 950, 951, 956, 958, 961, 962, 966, 967, 968));
com_wiris_util_xml_WCharacterBase::$negations = new _hx_array(array(61, 8800, 8801, 8802, 8764, 8769, 8712, 8713, 8715, 8716, 8834, 8836, 8835, 8837, 8838, 8840, 8839, 8841, 62, 8815, 60, 8814, 8805, 8817, 8804, 8816, 10878, 8817, 10877, 8816, 8776, 8777, 8771, 8772, 8773, 8775, 8849, 8930, 8850, 8931, 8707, 8708, 8741, 8742));
com_wiris_util_xml_WCharacterBase::$mirrorDictionary = new _hx_array(array(40, 41, 41, 40, 60, 62, 62, 60, 91, 93, 93, 91, 123, 125, 125, 123, 171, 187, 187, 171, 3898, 3899, 3899, 3898, 3900, 3901, 3901, 3900, 5787, 5788, 5788, 5787, 8249, 8250, 8250, 8249, 8261, 8262, 8262, 8261, 8317, 8318, 8318, 8317, 8333, 8334, 8334, 8333, 8712, 8715, 8713, 8716, 8714, 8717, 8715, 8712, 8716, 8713, 8717, 8714, 8725, 10741, 8764, 8765, 8765, 8764, 8771, 8909, 8786, 8787, 8787, 8786, 8788, 8789, 8789, 8788, 8804, 8805, 8805, 8804, 8806, 8807, 8807, 8806, 8808, 8809, 8809, 8808, 8810, 8811, 8811, 8810, 8814, 8815, 8815, 8814, 8816, 8817, 8817, 8816, 8818, 8819, 8819, 8818, 8820, 8821, 8821, 8820, 8822, 8823, 8823, 8822, 8824, 8825, 8825, 8824, 8826, 8827, 8827, 8826, 8828, 8829, 8829, 8828, 8830, 8831, 8831, 8830, 8832, 8833, 8833, 8832, 8834, 8835, 8835, 8834, 8836, 8837, 8837, 8836, 8838, 8839, 8839, 8838, 8840, 8841, 8841, 8840, 8842, 8843, 8843, 8842, 8847, 8848, 8848, 8847, 8849, 8850, 8850, 8849, 8856, 10680, 8866, 8867, 8867, 8866, 8870, 10974, 8872, 10980, 8873, 10979, 8875, 10981, 8880, 8881, 8881, 8880, 8882, 8883, 8883, 8882, 8884, 8885, 8885, 8884, 8886, 8887, 8887, 8886, 8905, 8906, 8906, 8905, 8907, 8908, 8908, 8907, 8909, 8771, 8912, 8913, 8913, 8912, 8918, 8919, 8919, 8918, 8920, 8921, 8921, 8920, 8922, 8923, 8923, 8922, 8924, 8925, 8925, 8924, 8926, 8927, 8927, 8926, 8928, 8929, 8929, 8928, 8930, 8931, 8931, 8930, 8932, 8933, 8933, 8932, 8934, 8935, 8935, 8934, 8936, 8937, 8937, 8936, 8938, 8939, 8939, 8938, 8940, 8941, 8941, 8940, 8944, 8945, 8945, 8944, 8946, 8954, 8947, 8955, 8948, 8956, 8950, 8957, 8951, 8958, 8954, 8946, 8955, 8947, 8956, 8948, 8957, 8950, 8958, 8951, 8968, 8969, 8969, 8968, 8970, 8971, 8971, 8970, 9001, 9002, 9002, 9001, 10088, 10089, 10089, 10088, 10090, 10091, 10091, 10090, 10092, 10093, 10093, 10092, 10094, 10095, 10095, 10094, 10096, 10097, 10097, 10096, 10098, 10099, 10099, 10098, 10100, 10101, 10101, 10100, 10179, 10180, 10180, 10179, 10181, 10182, 10182, 10181, 10184, 10185, 10185, 10184, 10187, 10189, 10189, 10187, 10197, 10198, 10198, 10197, 10205, 10206, 10206, 10205, 10210, 10211, 10211, 10210, 10212, 10213, 10213, 10212, 10214, 10215, 10215, 10214, 10216, 10217, 10217, 10216, 10218, 10219, 10219, 10218, 10220, 10221, 10221, 10220, 10222, 10223, 10223, 10222, 10627, 10628, 10628, 10627, 10629, 10630, 10630, 10629, 10631, 10632, 10632, 10631, 10633, 10634, 10634, 10633, 10635, 10636, 10636, 10635, 10637, 10640, 10638, 10639, 10639, 10638, 10640, 10637, 10641, 10642, 10642, 10641, 10643, 10644, 10644, 10643, 10645, 10646, 10646, 10645, 10647, 10648, 10648, 10647, 10680, 8856, 10688, 10689, 10689, 10688, 10692, 10693, 10693, 10692, 10703, 10704, 10704, 10703, 10705, 10706, 10706, 10705, 10708, 10709, 10709, 10708, 10712, 10713, 10713, 10712, 10714, 10715, 10715, 10714, 10741, 8725, 10744, 10745, 10745, 10744, 10748, 10749, 10749, 10748, 10795, 10796, 10796, 10795, 10797, 10798, 10798, 10797, 10804, 10805, 10805, 10804, 10812, 10813, 10813, 10812, 10852, 10853, 10853, 10852, 10873, 10874, 10874, 10873, 10877, 10878, 10878, 10877, 10879, 10880, 10880, 10879, 10881, 10882, 10882, 10881, 10883, 10884, 10884, 10883, 10891, 10892, 10892, 10891, 10897, 10898, 10898, 10897, 10899, 10900, 10900, 10899, 10901, 10902, 10902, 10901, 10903, 10904, 10904, 10903, 10905, 10906, 10906, 10905, 10907, 10908, 10908, 10907, 10913, 10914, 10914, 10913, 10918, 10919, 10919, 10918, 10920, 10921, 10921, 10920, 10922, 10923, 10923, 10922, 10924, 10925, 10925, 10924, 10927, 10928, 10928, 10927, 10931, 10932, 10932, 10931, 10939, 10940, 10940, 10939, 10941, 10942, 10942, 10941, 10943, 10944, 10944, 10943, 10945, 10946, 10946, 10945, 10947, 10948, 10948, 10947, 10949, 10950, 10950, 10949, 10957, 10958, 10958, 10957, 10959, 10960, 10960, 10959, 10961, 10962, 10962, 10961, 10963, 10964, 10964, 10963, 10965, 10966, 10966, 10965, 10974, 8870, 10979, 8873, 10980, 8872, 10981, 8875, 10988, 10989, 10989, 10988, 10999, 11000, 11000, 10999, 11001, 11002, 11002, 11001, 11778, 11779, 11779, 11778, 11780, 11781, 11781, 11780, 11785, 11786, 11786, 11785, 11788, 11789, 11789, 11788, 11804, 11805, 11805, 11804, 11808, 11809, 11809, 11808, 11810, 11811, 11811, 11810, 11812, 11813, 11813, 11812, 11814, 11815, 11815, 11814, 11816, 11817, 11817, 11816, 12296, 12297, 12297, 12296, 12298, 12299, 12299, 12298, 12300, 12301, 12301, 12300, 12302, 12303, 12303, 12302, 12304, 12305, 12305, 12304, 12308, 12309, 12309, 12308, 12310, 12311, 12311, 12310, 12312, 12313, 12313, 12312, 12314, 12315, 12315, 12314, 65113, 65114, 65114, 65113, 65115, 65116, 65116, 65115, 65117, 65118, 65118, 65117, 65124, 65125, 65125, 65124, 65288, 65289, 65289, 65288, 65308, 65310, 65310, 65308, 65339, 65341, 65341, 65339, 65371, 65373, 65373, 65371, 65375, 65376, 65376, 65375, 65378, 65379, 65379, 65378, 9115, 9118, 9116, 9119, 9117, 9120, 9118, 9115, 9119, 9116, 9120, 9117, 9121, 9124, 9122, 9125, 9123, 9126, 9124, 9121, 9125, 9122, 9126, 9123, 9127, 9131, 9130, 9134, 9129, 9133, 9131, 9127, 9134, 9130, 9133, 9129, 9128, 9132, 9132, 9128));
com_wiris_util_xml_WCharacterBase::$subSuperScriptDictionary = new _hx_array(array(178, 50, 179, 51, 185, 49, 8304, 48, 8305, 105, 8308, 52, 8309, 53, 8310, 54, 8311, 55, 8312, 56, 8313, 57, 8314, 43, 8315, 45, 8316, 61, 8317, 40, 8318, 41, 8319, 110, 8320, 48, 8321, 49, 8322, 50, 8323, 51, 8324, 52, 8325, 53, 8326, 54, 8327, 55, 8328, 56, 8329, 57, 8330, 43, 8331, 45, 8332, 61, 8333, 40, 8334, 41, 8336, 97, 8337, 101, 8338, 111, 8339, 120, 8340, 601, 8341, 104, 8342, 107, 8343, 108, 8344, 109, 8345, 110, 8346, 112, 8347, 115, 8348, 116));
com_wiris_util_xml_WCharacterBase::$horizontalLTRStretchyChars = new _hx_array(array(com_wiris_util_xml_WCharacterBase::$LEFTWARDS_ARROW, com_wiris_util_xml_WCharacterBase::$RIGHTWARDS_ARROW, com_wiris_util_xml_WCharacterBase::$LEFTRIGHT_ARROW, com_wiris_util_xml_WCharacterBase::$LEFTWARDS_ARROW_FROM_BAR, com_wiris_util_xml_WCharacterBase::$RIGHTWARDS_ARROW_FROM_BAR, com_wiris_util_xml_WCharacterBase::$LEFTWARDS_ARROW_WITH_HOOK, com_wiris_util_xml_WCharacterBase::$RIGHTWARDS_ARROW_WITH_HOOK, com_wiris_util_xml_WCharacterBase::$LEFTWARDS_HARPOON_WITH_BARB_UPWARDS, com_wiris_util_xml_WCharacterBase::$RIGHTWARDS_HARPOON_WITH_BARB_UPWARDS, com_wiris_util_xml_WCharacterBase::$LEFTWARDS_DOUBLE_ARROW, com_wiris_util_xml_WCharacterBase::$RIGHTWARDS_DOUBLE_ARROW, com_wiris_util_xml_WCharacterBase::$TOP_CURLY_BRACKET, com_wiris_util_xml_WCharacterBase::$BOTTOM_CURLY_BRACKET, com_wiris_util_xml_WCharacterBase::$TOP_PARENTHESIS, com_wiris_util_xml_WCharacterBase::$BOTTOM_PARENTHESIS, com_wiris_util_xml_WCharacterBase::$TOP_SQUARE_BRACKET, com_wiris_util_xml_WCharacterBase::$BOTTOM_SQUARE_BRACKET, com_wiris_util_xml_WCharacterBase::$LEFTWARDS_ARROW_OVER_RIGHTWARDS_ARROW, com_wiris_util_xml_WCharacterBase::$RIGHTWARDS_ARROW_OVER_LEFTWARDS_ARROW, com_wiris_util_xml_WCharacterBase::$LEFTWARDS_HARPOON_OVER_RIGHTWARDS_HARPOON, com_wiris_util_xml_WCharacterBase::$RIGHTWARDS_HARPOON_OVER_LEFTWARDS_HARPOON, com_wiris_util_xml_WCharacterBase::$SEGMENT));
com_wiris_util_xml_WCharacterBase::$tallAccents = new _hx_array(array(com_wiris_util_xml_WCharacterBase::$LEFTWARDS_ARROW_OVER_RIGHTWARDS_ARROW, com_wiris_util_xml_WCharacterBase::$RIGHTWARDS_ARROW_OVER_LEFTWARDS_ARROW, com_wiris_util_xml_WCharacterBase::$LEFTWARDS_HARPOON_OVER_RIGHTWARDS_HARPOON, com_wiris_util_xml_WCharacterBase::$RIGHTWARDS_HARPOON_OVER_LEFTWARDS_HARPOON));
com_wiris_util_xml_WCharacterBase::$invisible = new _hx_array(array(8289, 8290, 8291));
com_wiris_util_xml_WCharacterBase::$horizontalOperators = new _hx_array(array(175, 818, 8592, 8594, 8596, 8612, 8614, 8617, 8618, 8636, 8637, 8640, 8641, 8644, 8646, 8651, 8652, 8656, 8658, 8660, 8764, 9140, 9141, 9180, 9181, 9182, 9183, 9552, 10562, 10564, 10602, 10605, 57345, 57346));
com_wiris_util_xml_WCharacterBase::$latinLetters = "@0065@0066@0067@0068@0069@0070@0071@0072@0073@0074@0075@0076@0077@0078@0079@0080@0081@0082@0083@0084@0085@0086@0087@0088@0089@0090" . "@0097@0098@0099@0100@0101@0102@0103@0104@0105@0106@0107@0108@0109@0110@0111@0112@0113@0114@0115@0116@0117@0118@0119@0120@0121@0122@";
com_wiris_util_xml_WCharacterBase::$greekLetters = "@0913@0914@0935@0916@0917@0934@0915@0919@0921@0977@0922@0923@0924@0925@0927@0928@0920@0929@0931@0932@0933@0962@0937@0926@0936@0918" . "@0945@0946@0967@0948@0949@0966@0947@0951@0953@0966@0954@0955@0956@0957@0959@0960@0952@0961@0963@0964@0965@0982@0969@0958@0968@0950@";
function com_wiris_util_xml_WCharacterBase_0(&$actualValue, &$actual_range, &$firstRangeValueHex, &$i, &$inputList, &$lastRangeValueHex, &$unicodeList, &$unicodesRangedList) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($actualValue);
		return $s->toString();
	}
}
function com_wiris_util_xml_WCharacterBase_1(&$actualValue, &$actual_range, &$i, &$inputList, &$unicodeList, &$unicodesRangedList) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($actualValue);
		return $s->toString();
	}
}
function com_wiris_util_xml_WCharacterBase_2(&$c, &$i, &$j, &$mirroredStr, &$str) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($c);
		return $s->toString();
	}
}
function com_wiris_util_xml_WCharacterBase_3(&$c, &$i) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($c);
		return $s->toString();
	}
}
function com_wiris_util_xml_WCharacterBase_4(&$c) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($c);
		return $s->toString();
	}
}
