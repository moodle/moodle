<?PHP  // $Id$
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    require_once(dirname(__FILE__) . "/../../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    $adminroot = admin_get_root();
    admin_externalpage_setup('enrolment', $adminroot);

    $CFG->pagepath = 'enrol/mnet';


    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class

    $enrolment = enrolment_factory::factory('mnet');

/// If data submitted, then process and store.

    if ($frm = data_submitted()) {

    }

/// Otherwise fill and print the form.

    /// get language strings
    $str = get_strings(array('enrolmentplugins', 'configuration', 'users', 'administration'));

    admin_externalpage_print_header($adminroot);


/// Print current enrolment type description
    print_simple_box_start("center", "80%");
    print_heading($options[$enrol]);

    print_simple_box_start("center", "60%", '', 5, 'informationbox');
    print_string("description", "enrol_$enrol");
    print_simple_box_end();

    echo "<hr />";

    print ('<table align="center">'
           . '<tr>'
           . '<th> Name </th>'
           . '<th> Enrolments </th>'
           . '<th> Available Courses </th>'
           . '<th> Activity </th>'
           . '</tr>');
    $hosts = $enrolment->list_remote_servers();
    foreach ($hosts as $host) {
        print ('<tr>'
               . "<td><a href=\"{$CFG->wwwroot}/enrol/mnet/remote_courses.php?host={$host->id}\">{$host->name}</a></td>"
               . '<td align="center" > - (View)  </td>'
               . "<td align=\"center\" > - (<a href=\"{$CFG->wwwroot}/enrol/mnet/remote_courses.php?host={$host->id}\">Enrol</a>) </td>"
               . '<td align="center" > <a href="">Logs</a> </td>'
               . '</tr>');
    }
    print ('</table>');

    print_simple_box_end();

    admin_externalpage_print_footer($adminroot);

?>
