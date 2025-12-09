<?php
/**
 * Event observers definition.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\user_created',
        'callback'  => 'local_coursematrix\observer::user_created',
    ],
    [
        'eventname' => '\core\event\user_updated',
        'callback'  => 'local_coursematrix\observer::user_updated',
    ],
];

