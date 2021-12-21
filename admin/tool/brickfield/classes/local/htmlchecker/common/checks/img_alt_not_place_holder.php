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

namespace tool_brickfield\local\htmlchecker\common\checks;

use tool_brickfield\local\htmlchecker\common\brickfield_accessibility_test;

/**
 * Brickfield accessibility HTML checker library.
 *
 * Alt text for all img elements is not placeholder text unless author has confirmed it is correct.
 * 'img' element cannot have alt attribute value of "nbsp" or "spacer".
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class img_alt_not_place_holder extends brickfield_accessibility_test {
    /** @var int The default severity code for this test. */
    public $defaultseverity = \tool_brickfield\local\htmlchecker\brickfield_accessibility::BA_TEST_SEVERE;

    /** @var \string[][] An array of strings, broken up by language domain. */
    public $strings =
        [
            'en' => array('nbsp', '&nbsp;', 'spacer', 'image', 'img', 'photo'),
            'es' => array('nbsp', '&nbsp;', 'spacer', 'espacio', 'imagen', 'img', 'foto'),
        ];

    /**
     * The main check function. This is called by the parent class to actually check content.
     */
    public function check(): void {
        foreach ($this->get_all_elements('img') as $img) {
            if ($img->hasAttribute('alt')) {
                if (strlen($img->getAttribute('alt')) > 0) {
                    if (in_array($img->getAttribute('alt'), $this->translation())
                        || ord($img->getAttribute('alt')) == 194) {
                        $this->add_report($img);
                    } else if (preg_match("/^([0-9]*)(k|kb|mb|k bytes|k byte)?$/",
                        strtolower($img->getAttribute('alt')))) {
                        $this->add_report($img);
                    }
                }
            }
        }
    }
}
