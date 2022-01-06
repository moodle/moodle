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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @authors   Rabea de Groot, Anna Heynkes, Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Request URL for transforming latex code into a png image
 * The latex data needs to be added after chl=
 */
define('LATEX_TO_PNG_REQUEST',   'https://chart.googleapis.com/chart?cht=tx&chl=');

/**
 * Define whether to use googleapi or moodle Latex Library
 */
define('LATEX_TO_PNG_MOODLE', 0);
define('LATEX_TO_PNG_GOOGLE_API', 1);


/**
 * Prefix needed for encode64 imaged
 */
define('IMAGE_PREFIX', 'data:image/png;base64,');