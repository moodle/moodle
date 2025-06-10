<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'qtype_regexp', language 'en_us', version '4.1'.
 *
 * @package     qtype_regexp
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['penaltyforeachincorrecttry_help'] = 'When you run your questions using the \'Interactive with multiple tries\' or \'Adaptive mode\' behavior,
so that the student will have several tries to get the question right, then this option controls how much they are penalized for each incorrect try.

The penalty is a proportion of the total question grade, so if the question is worth three marks, and the penalty is 0.3333333,
then the student will score 3 if they get the question right first time, 2 if they get it right second try, and 1 if they get it right on the third try.

If you have set the <strong>Help Button</strong> mode to <strong>Letter</strong> or <strong>Word</strong> for this question,
<strong><em>the same penalty</em></strong> applies each time the student clicks the <strong>Buy Letter/Word</strong> Button.';
