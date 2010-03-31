<?php
       // register.php - allows admin to register their site on moodle.org

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    require_login();

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));


    admin_externalpage_setup('adminregistration');

    $site = get_site();

    if (!$admin = get_admin()) {
        print_error('noadmins', 'error');
    }

    if (!$admin->country and $CFG->country) {
        $admin->country = $CFG->country;
    }

/// Print the header stuff
    admin_externalpage_print_header();

/// Print headings
    $stradministration = get_string("administration");
    $strregistration = get_string("registration");
    $strregistrationinfo = get_string("registrationinfo");

    echo $OUTPUT->heading($strregistration);

    echo $OUTPUT->box($strregistrationinfo);


/// Print the form

    echo "<form id=\"mform1\" class=\"mform\" action=\"http://moodle.org/register/\" method=\"post\">\n";
    echo '<fieldset id="registration">';
    echo '<legend>'.get_string("registrationinfotitle").'</legend>';

    echo "<!-- The following hidden variables are to help prevent fake entries being sent. -->\n";
    echo "<!-- Together they form a key.  If any of these change between updates then the entry  -->\n";
    echo "<!-- is flagged as a new entry and will be manually checked by the list maintainer -->\n";
    echo "<input type=\"hidden\" name=\"url\" value=\"$CFG->wwwroot\" />\n";
    echo "<input type=\"hidden\" name=\"secret\" value=\"" . get_site_identifier() . "\" />\n";
    echo "<input type=\"hidden\" name=\"host\" value=\"".$_SERVER["HTTP_HOST"]."\" />\n";
    echo "<input type=\"hidden\" name=\"lang\" value=\"".current_language()."\" />\n";

    echo "<input type=\"hidden\" name=\"version\" value=\"$CFG->version\" />\n";
    echo "<input type=\"hidden\" name=\"release\" value=\"$CFG->release\" />\n";

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label>'.get_string('url').'</label></div>';
    echo '<div class="felement ftext">'.$CFG->wwwroot.'</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label>'.get_string("currentversion").'</label></div>';
    echo '<div class="felement ftext">'."$CFG->release ($CFG->version)".'</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label for="sitename">'.get_string("fullsitename").'</label></div>';
    echo '<div class="felement ftext">';
    echo '<input size="50" id="sitename" type="text" name="sitename" value="'.format_string($site->fullname).'" />';
    echo '</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label for="menucountry">'.get_string("country").'</label></div>';
    echo '<div class="felement ftext">';
    echo html_writer::select(get_list_of_countries(), "country", $admin->country, array(''=>get_string("selectacountry")."..."));
    echo '</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label for="menupublic">'."<a href=\"http://moodle.org/sites/?country=$admin->country\" title=\"".get_string("publicdirectorytitle")."\">".get_string("publicdirectory")."</a>".'</label></div>';
    echo '<div class="felement ftext">';
    $options = array();
    $options[0] = get_string("publicdirectory0");
    $options[1] = get_string("publicdirectory1");
    $options[2] = get_string("publicdirectory2");
    echo html_writer::select($options, "public", "2", false);
    echo '</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label for="menucontact">'.get_string("registrationcontact").'</label></div>';
    echo '<div class="felement ftext">';
    $options = array();
    $options[0] = get_string("registrationcontactno");
    $options[1] = get_string("registrationcontactyes");
    echo html_writer::select($options, "contact", "1", false);
    echo '</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label>'.get_string("statistics")."<br />(".get_string("notpublic").')'.'</label></div>';
    echo '<div class="felement ftext">';

    $count = $DB->count_records('course')-1;
    echo get_string("courses").": ".$count;
    echo "<input type=\"hidden\" name=\"courses\" value=\"$count\" />\n";
    echo '<br />';

    $count = $DB->count_records('user', array('deleted'=>0));
    echo get_string("users").": ".$count;
    echo "<input type=\"hidden\" name=\"users\" value=\"$count\" />\n";
    echo '<br />';

    // total number of role assignments
    $count = $DB->count_records('role_assignments');
    echo get_string('roleassignments', 'role').": ".$count;
    echo "<input type=\"hidden\" name=\"roleassignments\" value=\"$count\" />\n";
    echo '<br />';

    // first find all distinct roles with mod/course:update
    // please change the name and strings to something appropriate to reflect the new data collected
    $sql = "SELECT COUNT(DISTINCT u.id)
              FROM {role_capabilities} rc,
                   {role_assignments} ra,
                   {user} u
             WHERE (rc.capability = ?)
                   AND rc.roleid = ra.roleid
                   AND u.id = ra.userid";

    $count = $DB->count_records_sql($sql, array('moodle/course:update'));
    echo get_string("teachers").": ".$count;
    echo "<input type=\"hidden\" name=\"courseupdaters\" value=\"$count\" />\n";
    echo '<br />';

    $count = $DB->count_records('forum_posts');
    echo get_string("posts", 'forum').": ".$count;
    echo "<input type=\"hidden\" name=\"posts\" value=\"$count\" />\n";
    echo '<br />';

    $count = $DB->count_records('question');
    echo get_string("questions", 'quiz').": ".$count;
    echo "<input type=\"hidden\" name=\"questions\" value=\"$count\" />\n";
    echo '<br />';

    $count = $DB->count_records('resource');
    echo get_string("modulenameplural", "resource").": ".$count;
    echo "<input type=\"hidden\" name=\"resources\" value=\"$count\" />\n";
    echo '</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label for="adminname">'.get_string("administrator").'</label></div>';
    echo '<div class="felement ftext">';
    echo "<input size=\"50\" type=\"text\" id=\"adminname\" name=\"adminname\" value=\"".fullname($admin, true)."\" />";
    echo '</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label for="adminemail">'.get_string("email").'</label></div>';
    echo '<div class="felement ftext">';
    echo "<input size=\"50\" type=\"text\" id=\"adminemail\" name=\"adminemail\" value=\"$admin->email\" />";
    echo '</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label for="menumailme">'.get_string("registrationemail").'</label></div>';
    echo '<div class="felement ftext">';
    $options = array();
    $options[0] = get_string("registrationno");
    $options[1] = get_string("registrationyes");
    echo html_writer::select($options, "mailme", "1", false);
    echo '</div>';
    echo '</div>';

    echo '<div class="felement fsubmit"><input name="submitbutton" value="'.get_string('registrationsend').'" type="submit" id="id_submitbutton" /></div>';

    echo "</fieldset>\n";

    echo "</form>\n";

/// Print footer
    echo $OUTPUT->footer();
