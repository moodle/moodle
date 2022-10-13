<?php

/**
 * Version file for component 'local_gws_query_practice'
 *
 * @package    local_gws_query_practice
 * @copyright  2019 onwards GWS
 * @developer  Brian kremer (greatwallstudio.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$capabilities = array(
    'local/gws_query_practice:accessquerypractice' => array(
        'riskbitmask'  => RISK_CONFIG | RISK_DATALOSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array(
            'admin'   => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
        )
    )
);