<?php

namespace auth_iomadsaml2\hook\output;

defined('MOODLE_INTERNAL') || die();

class before_http_headers_hook
{
    public static function execute()
    {
        global $CFG;

        if (!empty($CFG->auth_saml_redirect)) {
            header('Location: ' . $CFG->auth_saml_redirect);
            exit;
        }
    }
}
