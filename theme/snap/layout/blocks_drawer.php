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
 * TODO describe file blocks_drawer
 *
 * @package    theme_snap
 * @copyright  2025 Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$addblockbutton = $OUTPUT->addblockbutton();
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));

if (!$hasblocks) {
    $blockdraweropen = false;
}

$templatecontext = [
    'hasblocks' => $hasblocks,
    'sidepreblocks' => $blockshtml,
    'addblockbutton' => $addblockbutton,
    'showdraweropenbutton' => false
];
echo $OUTPUT->render_from_template('theme_snap/blocks_drawer', $templatecontext);