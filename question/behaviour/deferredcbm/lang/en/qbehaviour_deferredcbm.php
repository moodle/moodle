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

/**
 * Strings for component 'qbehaviour_deferredcbm', language 'en'.
 *
 * @package    qbehaviour
 * @subpackage deferredcbm
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['assumingcertainty'] = 'You did not select a certainty. Assuming: {$a}.';
$string['basemark'] = 'Base mark {$a}';
$string['cbmmark'] = 'CBM mark {$a}';
$string['certainty'] = 'Certainty';
$string['certainty_help'] = 'Certainty-based marking requires you to indicate how reliable you think your answer is. The available levels are:

Certainty level     | C=1 (Unsure) | C=2 (Mid) | C=3 (Quite sure)
------------------- | ------------ | --------- | ----------------
Mark if correct     |   1          |    2      |      3
Mark if wrong       |   0          |   -2      |     -6
Probability correct |  <67%        | 67-80%    |    >80%

Best marks are gained by acknowledging uncertainty. For example, if you think there is more than a 1 in 3 chance of being wrong, you should enter C=1 and avoid the risk of a negative mark.
';
$string['certainty_link'] = 'qbehaviour/deferredcbm/certainty';
$string['certainty1'] = 'C=1 (Unsure: <67%)';
$string['certainty2'] = 'C=2 (Mid: >67%)';
$string['certainty3'] = 'C=3 (Quite sure: >80%)';
$string['howcertainareyou'] = 'How certain are you? {$a}';
$string['pluginname'] = 'Deferred feedback with CBM';
