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
 * Strings for component 'qtype_combined', language 'en_us', version '4.1'.
 *
 * @package     qtype_combined
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['err_accepts_vertical_or_horizontal_layout_param'] = '<p>The \'{$a}\' question type allows you to specify the layout
of your question
type as follows :</p>
<ul>
 <li>[[{question identifier}:{$a}:v]] vertical OR</li>
  <li>[[{question identifier}:{$a}:h]] horizontal.</li></ul>
  <p>You should not enter anything else after the second colon.</p>';
$string['err_invalid_width_specifier_postfix'] = '<p>The \'{$a}\' question type allows you to specify the width of your question
type as
follows:</p>
<ul>
 <li>[[{question identifier}:{$a}:____]] where the width of the input box will depend on
  the number of underscores or</li>
  <li>[[{question identifier}:{$a}:__10__]] where the width of the input box will depend on
  the number.</li>
</ul>
<p>You should not enter anything else after the second colon.</p>';
$string['subqheader'] = '\'{$a->qtype}\' input \'{$a->qid}\'';
