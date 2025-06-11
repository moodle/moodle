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
 * Version information for the essayautograde question type.
 *
 * @package    qtype
 * @subpackage essayautograde
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2005 Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->cron      = 0;
$plugin->component = 'qtype_essayautograde';
$plugin->maturity  = MATURITY_STABLE;
$plugin->requires  = 2015111600; // Moodle 3.0
$plugin->version   = 2025041443;
$plugin->release   = '2025-04-14 (43)';

// https://docs.moodle.org/dev/Releases
// Moodle 5.0 2025041400 14 April 2025
// Moodle 4.5 2024100700 7 October 2024 <= LTS
// Moodle 4.4 2024042200 22 April 2024
// Moodle 4.3 2023100900 9 October 2023
// Moodle 4.2 2023042400 24 April 2023
// Moodle 4.1 2022112800 28 Nov 2022 <= LTS
// Moodle 4.0 2022041900 19 Apr 2022
// Moodle 3.11 2021051700 17 May 2021
// Moodle 3.10 2020110900 9 Nov 2020
// Moodle 3.9 2020061500 15 Jun 2020 <= LTS
// Moodle 3.8 2019111800 18 Nov 2019
// Moodle 3.7 2019052000 20 May 2019
// Moodle 3.6 2018120300  3 Dec 2018
// Moodle 3.5 2018051700 17 May 2018 <= LTS
// Moodle 3.4 2017111300 13 Nov 2017
// Moodle 3.3 2017051500 15 May 2017
// Moodle 3.2 2016120500  5 Dec 2016
// Moodle 3.1 2016052300 23 May 2016 <= LTS
// Moodle 3.0 2015111600 16 Nov 2015
// Moodle 2.9 2015051100 11 May 2015
// Moodle 2.8 2014111000 10 Nov 2014
// Moodle 2.7 2014051200 12 May 2014 <= LTS
// Moodle 2.6 2013111800 18 Nov 2013
// Moodle 2.5 2013051400 14 May 2013
// Moodle 2.4 2012120300  3 Dec 2012
// Moodle 2.3 2012062500 25 Jun 2012
// Moodle 2.2 2011120500  5 Dec 2011
// Moodle 2.1 2011070100  1 Jul 2011
// Moodle 2.0 2010112400 24 Nov 2010
