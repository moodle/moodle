<?php
/**
 * Settings for local_coursematrix
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_coursematrix',
        get_string('coursematrix', 'local_coursematrix'),
        new moodle_url('/local/coursematrix/index.php')
    ));
}

