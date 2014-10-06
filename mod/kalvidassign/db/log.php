<?php
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
 * Kaltura video assignment log script.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */


defined('MOODLE_INTERNAL') || die();

$logs = array(
        array('module' => 'kalvidassign', 'action' => 'add', 'mtable' => 'kalvidassign', 'field' => 'name'),
        array('module' => 'kalvidassign', 'action' => 'update', 'mtable' => 'kalvidassign', 'field' =>' name'),
        array('module' => 'kalvidassign', 'action' => 'view', 'mtable' => 'kalvidassign', 'field' => 'name'),
        array('module' => 'kalvidassign', 'action' => 'delete', 'mtable' => 'kalvidassign', 'field' => 'name')
);