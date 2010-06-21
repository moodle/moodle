<?PHP
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    require_once(dirname(__FILE__) . "/../../config.php");
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/mnet/lib.php');

die('TODO: MDL-22787 mnet enrolments are not reimplemented yet, sorry.');

    admin_externalpage_setup('mnetenrol');

    $enrolment = enrol_get_plugin('mnet');

/// Otherwise fill and print the form.

    /// get language strings

    echo $OUTPUT->header();

    echo $OUTPUT->box(get_string("remoteenrolhosts_desc", "mnet"));

    echo '<hr />';

    if (empty($CFG->mnet_dispatcher_mode) || $CFG->mnet_dispatcher_mode !== 'strict') {
        echo $OUTPUT->box(get_string('mnetdisabled','mnet'));
    }

    echo '<div id="trustedhosts"><!-- See theme/standard/styles_layout.css #trustedhosts .generaltable for rules -->'
           . '<table cellspacing="0" cellpadding="5" id="hosts" class="generaltable generalbox" >'
           . '<tr>'
           . '<th class="header c0"> '.get_string('host', 'mnet').' </th>'
           . '<th class="header c1"> '.get_string('enrolments', 'mnet').' </th>'
           . '<th class="header c2"> '.get_string('courses', 'mnet').' </th>'
           // . '<th class="header c3"> &nbsp; </th>'
           . '</tr>';
    $hosts = $enrolment->list_remote_servers();
    foreach ($hosts as $host) {
        $coursesurl = "$CFG->wwwroot/$CFG->admin/mnet/enr_courses.php?host={$host->id}&amp;sesskey=".sesskey();
        $coursecount = $DB->get_field_sql("SELECT COUNT(id) FROM {mnet_enrol_course} WHERE hostid=?", array($host->id));
        if (empty($coursecount)) {
            $coursecount = '?';
        }
        $enrolcount = $DB->get_field_sql("SELECT COUNT(id) FROM {mnet_enrol_assignments} WHERE hostid=?", array($host->id));

        echo '<tr>'
               . "<td><a href=\"{$coursesurl}\">{$host->name}</a></td>"
               . "<td align=\"center\" >$enrolcount</td>"
               . "<td align=\"center\" >$coursecount - <a href=\"{$coursesurl}\">".get_string('editenrolments', 'mnet')."</a></td>"
               // TODO: teach report/log/index.php to show per-host-logs
               // . '<td align="center" ><a href="$CFG->wwwroot/$CFG->admin/report/log/index.php?course_host={$host->id}">'
               // . get_string('logs', 'mnet').'</a> </td>'
               . '</tr>';
    }
    echo '</table>'
       . '</div>';

    echo $OUTPUT->footer();
