<?PHP  // $Id$
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    require_once(dirname(__FILE__) . "/../../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('mnetenrol');
    $CFG->pagepath = 'admin/mnet';


    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class

    $enrolment = enrolment_factory::factory('mnet');

/// Otherwise fill and print the form.

    /// get language strings

    admin_externalpage_print_header();

    print_box(get_string("remoteenrolhosts_desc", "mnet"));

    echo '<hr />';

    if (empty($CFG->mnet_dispatcher_mode) || $CFG->mnet_dispatcher_mode !== 'strict') {
        print_box(get_string('mnetdisabled','mnet'));
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
        $coursesurl = "$CFG->wwwroot/$CFG->admin/mnet/enr_courses.php?host={$host->id}&amp;sesskey={$USER->sesskey}";
        $coursecount = get_field_sql("SELECT count(id) FROM {$CFG->prefix}mnet_enrol_course WHERE hostid={$host->id}");
        if (empty($coursecount)) {
            $coursecount = '?';
        }
        $enrolcount = get_field_sql("SELECT count(id) FROM {$CFG->prefix}mnet_enrol_assignments WHERE hostid={$host->id}");

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

    admin_externalpage_print_footer();

?>
