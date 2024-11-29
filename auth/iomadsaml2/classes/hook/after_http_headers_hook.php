<?php

namespace auth_iomadsaml2\hook;

defined('MOODLE_INTERNAL') || die();

class after_http_headers_hook
{
    public static function execute()
    {
        global $CFG;
        try {
            $saml = optional_param('saml', null, PARAM_BOOL);
            if ($saml === 1) {
                if (isguestuser()) {
                    require_logout();
                }
                unset($CFG->autologinguests);
            }
        } catch (\Exception $exception) {
            debugging(
                'auth_iomadsaml2_after_config error',
                DEBUG_DEVELOPER,
                $exception->getTrace()
            );
        }
    }
}
