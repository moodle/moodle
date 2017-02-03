<?php
2
3	// This file is part of Moodle - http://moodle.org/
4	//
5	// Moodle is free software: you can redistribute it and/or modify
6	// it under the terms of the GNU General Public License as published by
7	// the Free Software Foundation, either version 3 of the License, or
8	// (at your option) any later version.
9	//
10	// Moodle is distributed in the hope that it will be useful,
11	// but WITHOUT ANY WARRANTY; without even the implied warranty of
12	// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
13	// GNU General Public License for more details.
14	//
15	// You should have received a copy of the GNU General Public License
16	// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
17
18	/**
19	 * Cohort enrolment plugin version specification.
20	 *
21	 * @package    enrol_cohort
22	 * @copyright  2010 Petr Skoda {@link http://skodak.org}
23	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
24	 */
25
26	defined('MOODLE_INTERNAL') || die();
27
28	$tasks = array(
29	    array(
30	        'classname' => 'enrol_cohort\task\cron_task',
31	        'blocking' => 0,
32	        'minute' => '*/2',
33	        'hour' => '*',
34	        'day' => '*',
35	        'dayofweek' => '0',
36	        'month' => '*'
37	    )
38	);
