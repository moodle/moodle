<?php
/**
 * Plugin capabilities
 *
 * @package     mod_turningptintegration
 * @copyright   2019 Turning Technologies, LLC
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$capabilities = array (
            'mod/turningptintegration:manage' => array(
            'riskbitmask' => RISK_XSS,
            'captype' => 'write',
            'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        )
    ),
    'mod/turningptintegration:addinstance' => array(
            'riskbitmask' => RISK_XSS,
            'captype' => 'write',
            'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
            'guest'          => CAP_PROHIBIT,
            'student'        => CAP_PROHIBIT,
            'teacher'        => CAP_PROHIBIT,
            'editingteacher' => CAP_PROHIBIT,
            'coursecreator'  => CAP_PROHIBIT,
            'manager'        => CAP_PROHIBIT
        )
    )
);

