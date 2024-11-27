<?php

namespace tool_iomadpolicy\hook\output;

use tool_iomadpolicy\api;
use moodle_url;
use html_writer;

defined('MOODLE_INTERNAL') || die();

class before_standard_footer_html_generation_hook
{
    public static function execute()
    {

        global $CFG, $PAGE;
        $output = '';
        if (!empty($CFG->sitepolicyhandler) && $CFG->sitepolicyhandler == 'tool_iomadpolicy') {
            $policies = api::get_current_versions_ids();
            if (!empty($policies)) {
                $url = new moodle_url('/admin/tool/iomadpolicy/viewall.php', ['returnurl' => $PAGE->url]);
                $output .= html_writer::link($url, get_string('useriomadpolicysettings', 'tool_iomadpolicy'));
                $output = html_writer::div($output, 'policiesfooter');
            }
        }
        return $output;
    }
}
