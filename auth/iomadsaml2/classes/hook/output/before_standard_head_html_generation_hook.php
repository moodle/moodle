<?php

namespace auth_iomadsaml2\hook\output;

defined('MOODLE_INTERNAL') || die();

class before_standard_head_html_generation_hook
{
    public static function execute(\core\hook\output\before_standard_head_html_generation $hook): \core\hook\output\before_standard_head_html_generation
    {

        global $CFG, $PAGE, $USER;

        $message = null;

        if (
            !empty($CFG->sitepolicyhandler)
            && $CFG->sitepolicyhandler === 'tool_iomadpolicy'
            && empty($USER->policyagreed)
            && (isguestuser() || !isloggedin())
        ) {

            $output = $PAGE->get_renderer('tool_iomadpolicy');
            try {
                $page = new \tool_iomadpolicy\output\guestconsent();
                $message = $output->render($page);
            } catch (\dml_read_exception $e) {
                $message = null;
            }
        }

        if ($message !== null) {
            if (!isset($data['html'])) {
                $data['html'] = '';
            }
            $data['html'] .= $message;
        }

        if ($message !== null) {
            $hook->html .= $message;
        }

        return $hook;
    }
}
