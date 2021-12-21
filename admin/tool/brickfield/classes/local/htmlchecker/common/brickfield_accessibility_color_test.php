<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace tool_brickfield\local\htmlchecker\common;

/**
 * Helper test base for tests dealing with color difference and luminosity.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class brickfield_accessibility_color_test extends brickfield_accessibility_test {

    /** @var string[] Mapping of colours to hex codes. */
    public $colornames = [
        'aliceblue' => 'f0f8ff',
        'antiquewhite' => 'faebd7',
        'aqua' => '00ffff',
        'aquamarine' => '7fffd4',
        'azure' => 'f0ffff',
        'beige' => 'f5f5dc',
        'bisque' => 'ffe4c4',
        'black' => '000000',
        'blanchedalmond' => 'ffebcd',
        'blue' => '0000ff',
        'blueviolet' => '8a2be2',
        'brown' => 'a52a2a',
        'burlywood' => 'deb887',
        'cadetblue' => '5f9ea0',
        'chartreuse' => '7fff00',
        'chocolate' => 'd2691e',
        'coral' => 'ff7f50',
        'cornflowerblue' => '6495ed',
        'cornsilk' => 'fff8dc',
        'crimson' => 'dc143c',
        'cyan' => '00ffff',
        'darkblue' => '00008b',
        'darkcyan' => '008b8b',
        'darkgoldenrod' => 'b8860b',
        'darkgray' => 'a9a9a9',
        'darkgreen' => '006400',
        'darkkhaki' => 'bdb76b',
        'darkmagenta' => '8b008b',
        'darkolivegreen' => '556b2f',
        'darkorange' => 'ff8c00',
        'darkorchid' => '9932cc',
        'darkred' => '8b0000',
        'darksalmon' => 'e9967a',
        'darkseagreen' => '8fbc8f',
        'darkslateblue' => '483d8b',
        'darkslategray' => '2f4f4f',
        'darkturquoise' => '00ced1',
        'darkviolet' => '9400d3',
        'deeppink' => 'ff1493',
        'deepskyblue' => '00bfff',
        'dimgray' => '696969',
        'dodgerblue' => '1e90ff',
        'firebrick' => 'b22222',
        'floralwhite' => 'fffaf0',
        'forestgreen' => '228b22',
        'fuchsia' => 'ff00ff',
        'gainsboro' => 'dcdcdc',
        'ghostwhite' => 'f8f8ff',
        'gold' => 'ffd700',
        'goldenrod' => 'daa520',
        'gray' => '808080',
        'green' => '008000',
        'greenyellow' => 'adff2f',
        'grey' => '808080',
        'honeydew' => 'f0fff0',
        'hotpink' => 'ff69b4',
        'indianred' => 'cd5c5c',
        'indigo' => '4b0082',
        'ivory' => 'fffff0',
        'khaki' => 'f0e68c',
        'lavender' => 'e6e6fa',
        'lavenderblush' => 'fff0f5',
        'lawngreen' => '7cfc00',
        'lemonchiffon' => 'fffacd',
        'lightblue' => 'add8e6',
        'lightcoral' => 'f08080',
        'lightcyan' => 'e0ffff',
        'lightgoldenrodyellow' => 'fafad2',
        'lightgrey' => 'd3d3d3',
        'lightgreen' => '90ee90',
        'lightpink' => 'ffb6c1',
        'lightsalmon' => 'ffa07a',
        'lightseagreen' => '20b2aa',
        'lightskyblue' => '87cefa',
        'lightslategray' => '778899',
        'lightsteelblue' => 'b0c4de',
        'lightyellow' => 'ffffe0',
        'lime' => '00ff00',
        'limegreen' => '32cd32',
        'linen' => 'faf0e6',
        'magenta' => 'ff00ff',
        'maroon' => '800000',
        'mediumaquamarine' => '66cdaa',
        'mediumblue' => '0000cd',
        'mediumorchid' => 'ba55d3',
        'mediumpurple' => '9370d8',
        'mediumseagreen' => '3cb371',
        'mediumslateblue' => '7b68ee',
        'mediumspringgreen' => '00fa9a',
        'mediumturquoise' => '48d1cc',
        'mediumvioletred' => 'c71585',
        'midnightblue' => '191970',
        'mintcream' => 'f5fffa',
        'mistyrose' => 'ffe4e1',
        'moccasin' => 'ffe4b5',
        'navajowhite' => 'ffdead',
        'navy' => '000080',
        'oldlace' => 'fdf5e6',
        'olive' => '808000',
        'olivedrab' => '6b8e23',
        'orange' => 'ffa500',
        'orangered' => 'ff4500',
        'orchid' => 'da70d6',
        'palegoldenrod' => 'eee8aa',
        'palegreen' => '98fb98',
        'paleturquoise' => 'afeeee',
        'palevioletred' => 'd87093',
        'papayawhip' => 'ffefd5',
        'peachpuff' => 'ffdab9',
        'peru' => 'cd853f',
        'pink' => 'ffc0cb',
        'plum' => 'dda0dd',
        'powderblue' => 'b0e0e6',
        'purple' => '800080',
        'red' => 'ff0000',
        'rosybrown' => 'bc8f8f',
        'royalblue' => '4169e1',
        'saddlebrown' => '8b4513',
        'salmon' => 'fa8072',
        'sandybrown' => 'f4a460',
        'seagreen' => '2e8b57',
        'seashell' => 'fff5ee',
        'sienna' => 'a0522d',
        'silver' => 'c0c0c0',
        'skyblue' => '87ceeb',
        'slateblue' => '6a5acd',
        'slategray' => '708090',
        'snow' => 'fffafa',
        'springgreen' => '00ff7f',
        'steelblue' => '4682b4',
        'tan' => 'd2b48c',
        'teal' => '008080',
        'thistle' => 'd8bfd8',
        'tomato' => 'ff6347',
        'turquoise' => '40e0d0',
        'violet' => 'ee82ee',
        'wheat' => 'f5deb3',
        'white' => 'ffffff',
        'whitesmoke' => 'f5f5f5',
        'yellow' => 'ffff00',
        'yellowgreen' => '9acd32'
    ];

    /**
     * Helper method that finds the luminosity between the provided
     * foreground and background parameters.
     * @param string $foreground The HEX value of the foreground color
     * @param string $background The HEX value of the background color
     * @return float The luminosity contrast ratio between the colors
     */
    public function get_luminosity(string $foreground, string $background): float {
        if ($foreground == $background) {
            return 0;
        }
        $forergb = $this->get_rgb($foreground);
        $backrgb = $this->get_rgb($background);
        return $this->luminosity($forergb['r'], $backrgb['r'],
            $forergb['g'], $backrgb['g'],
            $forergb['b'], $backrgb['b']);
    }

    /**
     * Returns the luminosity between two colors
     * @param string $r The first Red value
     * @param string $r2 The second Red value
     * @param string $g The first Green value
     * @param string $g2 The second Green value
     * @param string $b The first Blue value
     * @param string $b2 The second Blue value
     * @return float The luminosity contrast ratio between the colors
     */
    public function luminosity(string $r, string $r2, string $g, string $g2, string $b, string $b2): float {
        $rsrgb = $r / 255;
        $gsrgb = $g / 255;
        $bsrgb = $b / 255;
        $r3 = ($rsrgb <= 0.03928) ? $rsrgb / 12.92 : pow(($rsrgb + 0.055) / 1.055, 2.4);
        $g3 = ($gsrgb <= 0.03928) ? $gsrgb / 12.92 : pow(($gsrgb + 0.055) / 1.055, 2.4);
        $b3 = ($bsrgb <= 0.03928) ? $bsrgb / 12.92 : pow(($bsrgb + 0.055) / 1.055, 2.4);

        $rsrgb2 = $r2 / 255;
        $gsrgb2 = $g2 / 255;
        $bsrgb2 = $b2 / 255;
        $r4 = ($rsrgb2 <= 0.03928) ? $rsrgb2 / 12.92 : pow(($rsrgb2 + 0.055) / 1.055, 2.4);
        $g4 = ($gsrgb2 <= 0.03928) ? $gsrgb2 / 12.92 : pow(($gsrgb2 + 0.055) / 1.055, 2.4);
        $b4 = ($bsrgb2 <= 0.03928) ? $bsrgb2 / 12.92 : pow(($bsrgb2 + 0.055) / 1.055, 2.4);

        if ($r + $g + $b <= $r2 + $g2 + $b2) {
            $l2 = (.2126 * $r3 + 0.7152 * $g3 + 0.0722 * $b3);
            $l1 = (.2126 * $r4 + 0.7152 * $g4 + 0.0722 * $b4);
        } else {
            $l1 = (.2126 * $r3 + 0.7152 * $g3 + 0.0722 * $b3);
            $l2 = (.2126 * $r4 + 0.7152 * $g4 + 0.0722 * $b4);
        }

        $luminosity = round(($l1 + 0.05) / ($l2 + 0.05), 2);
        return $luminosity;
    }


    /**
     * Returns the decimal equivalents for a HEX color
     * @param string $color The hex color value
     * @return array An array where 'r' is the Red value, 'g' is Green, and 'b' is Blue
     */
    public function get_rgb(string $color) {
        $color = $this->convert_color($color);
        $c = str_split($color, 2);
        if (count($c) != 3) {
            return false;
        }
        $results = ['r' => hexdec($c[0]), 'g' => hexdec($c[1]), 'b' => hexdec($c[2])];
        return $results;
    }

    /**
     * Converts multiple color or background styles into a simple hex string
     * @param string $color The color attribute to convert (this can also be a multi-value css background value)
     * @return string A standard CSS hex value for the color
     */
    public function convert_color(string $color): string {
        $color = trim($color);
        if (strpos($color, ' ') !== false) {
            $colors = explode(' ', $color);
            foreach ($colors as $backgroundpart) {
                if (substr(trim($backgroundpart), 0, 1) == '#' ||
                    in_array(trim($backgroundpart), array_keys($this->colornames)) ||
                    strtolower(substr(trim($backgroundpart), 0, 3)) == 'rgb') {
                    $color = $backgroundpart;
                }
            }
        }
        // Normal hex color.
        if (substr($color, 0, 1) == '#') {
            if (strlen($color) == 7) {
                return str_replace('#', '', $color);
            } else if (strlen($color) == 4) {
                return substr($color, 1, 1) . substr($color, 1, 1) .
                    substr($color, 2, 1) . substr($color, 2, 1) .
                    substr($color, 3, 1) . substr($color, 3, 1);
            } else {
                return "000000";
            }
        }
        // Named Color.
        if (in_array($color, array_keys($this->colornames))) {
            return $this->colornames[$color];
        }
        // RGB values.
        if (strtolower(substr($color, 0, 3)) == 'rgb') {
            $colors = explode(',', trim(str_replace('rgb(', '', $color), '()'));
            if (count($colors) != 3) {
                return false;
            }
            $r = intval($colors[0]);
            $g = intval($colors[1]);
            $b = intval($colors[2]);

            $r = dechex($r < 0 ? 0 : ($r > 255 ? 255 : $r));
            $g = dechex($g < 0 ? 0 : ($g > 255 ? 255 : $g));
            $b = dechex($b < 0 ? 0 : ($b > 255 ? 255 : $b));

            $color = (strlen($r) < 2 ? '0' : '') . $r;
            $color .= (strlen($g) < 2 ? '0' : '') . $g;
            $color .= (strlen($b) < 2 ? '0' : '') . $b;
            return $color;
        }

        return '';
    }

    /**
     * Returns the WAIERT contrast between two colors
     * @param string $foreground
     * @param string $background
     * @return array
     * @see get_luminosity
     */
    public function get_wai_ert_contrast(string $foreground, string $background): array {
        $forergb = $this->get_rgb($foreground);
        $backrgb = $this->get_rgb($background);
        $diffs = $this->get_wai_diffs($forergb, $backrgb);

        return $diffs['red'] + $diffs['green'] + $diffs['blue'];
    }

    /**
     * Returns the WAI ERT Brightness between two colors
     * @param string $foreground
     * @param string $background
     * @return float|int
     */
    public function get_wai_ert_brightness(string $foreground, string $background): float {
        $forergb = $this->get_rgb($foreground);
        $backrgb = $this->get_rgb($background);
        $color = $this->get_wai_diffs($forergb, $backrgb);
        return (($color['red'] * 299) + ($color['green'] * 587) + ($color['blue'] * 114)) / 1000;
    }

    /**
     * Get wai diffs.
     * @param array $forergb
     * @param array $backrgb
     * @return array
     */
    public function get_wai_diffs(array $forergb, array $backrgb): array {
        $reddiff = ($forergb['r'] > $backrgb['r'])
            ? $forergb['r'] - $backrgb['r']
            : $backrgb['r'] - $forergb['r'];
        $greendiff = ($forergb['g'] > $backrgb['g'])
            ? $forergb['g'] - $backrgb['g']
            : $backrgb['g'] - $forergb['g'];

        $bluediff = ($forergb['b'] > $backrgb['b'])
            ? $forergb['b'] - $backrgb['b']
            : $backrgb['b'] - $forergb['b'];
        return ['red' => $reddiff, 'green' => $greendiff, 'blue' => $bluediff];
    }
}
