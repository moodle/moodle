<?php  // $Id$
       // register.php - allows admin to register their site on moodle.org

    require_once('../config.php');

    require_login();

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    if (!$site = get_site()) {
        redirect("index.php");
    }

    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', 'error');
    }

    if (!$admin = get_admin()) {
        error("No admins");
    }

    if (!$admin->country and $CFG->country) {
        $admin->country = $CFG->country;
    }

/// Print headings
    $stradministration = get_string("administration");
    $strregistration = get_string("registration");
    $strregistrationinfo = get_string("registrationinfo");
    $navlinks = array();
    $navlinks[] = array('name' => $stradministration, 'link' => "../$CFG->admin/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strregistration, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header("$site->shortname: $strregistration", $site->fullname, $navigation);

    print_heading($strregistration);

    print_simple_box($strregistrationinfo, "center", "70%");


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
    echo '<div class="fitemtitle"><label>URL</label></div>';
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
    choose_from_menu (get_list_of_countries(), "country", $admin->country, get_string("selectacountry")."...", "", "");
    echo '</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label for="menupublic">'."<a href=\"http://moodle.org/sites/?country=$admin->country\" title=\"".get_string("publicdirectorytitle")."\">".get_string("publicdirectory")."</a>".'</label></div>';
    echo '<div class="felement ftext">';
    $options[0] = get_string("publicdirectory0");
    $options[1] = get_string("publicdirectory1");
    $options[2] = get_string("publicdirectory2");
    choose_from_menu ($options, "public", "2", "", "", "");
    unset($options);
    echo '</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label for="menucontact">'.get_string("registrationcontact").'</label></div>';
    echo '<div class="felement ftext">';
    $options[0] = get_string("registrationcontactno");
    $options[1] = get_string("registrationcontactyes");
    choose_from_menu ($options, "contact", "1", "", "", "");
    unset($options);
    echo '</div>';
    echo '</div>';

    echo '<div class="fitem">';
    echo '<div class="fitemtitle"><label>'.get_string("statistics")."<br />(".get_string("notpublic").')'.'</label></div>';
    echo '<div class="felement ftext">';

    $count = count_records('course')-1;
    echo get_string("courses").": ".$count;
    echo "<input type=\"hidden\" name=\"courses\" value=\"$count\" />\n";
    echo '<br />';

    $count = count_records('user', 'deleted', 0);
    echo get_string("users").": ".$count;
    echo "<input type=\"hidden\" name=\"users\" value=\"$count\" />\n";
    echo '<br />';

    // total number of role assignments
    $count = count_records('role_assignments');
    echo get_string('roleassignments', 'role').": ".$count;
    echo "<input type=\"hidden\" name=\"roleassignments\" value=\"$count\" />\n";
    echo '<br />';

    // first find all distinct roles with mod/course:update
    // please change the name and strings to something appropriate to reflect the new data collected
    $sql = "SELECT COUNT(DISTINCT u.id)
            FROM {$CFG->prefix}role_capabilities rc,
                 {$CFG->prefix}role_assignments ra,
                 {$CFG->prefix}user u
            WHERE (rc.capability = 'moodle/course:update' or rc.capability='moodle/site:doanything')
                   AND rc.roleid = ra.roleid
                   AND u.id = ra.userid";

    $count = count_records_sql($sql);
    echo get_string("teachers").": ".$count;
    echo "<input type=\"hidden\" name=\"courseupdaters\" value=\"$count\" />\n";
    echo '<br />';

    $count = count_records('forum_posts');
    echo get_string("posts", 'forum').": ".$count;
    echo "<input type=\"hidden\" name=\"posts\" value=\"$count\" />\n";
    echo '<br />';

    $count = count_records('question');
    echo get_string("questions", 'quiz').": ".$count;
    echo "<input type=\"hidden\" name=\"questions\" value=\"$count\" />\n";
    echo '<br />';

    $count = count_records('resource');
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
    $options[0] = get_string("registrationno");
    $options[1] = get_string("registrationyes");
    choose_from_menu ($options, "mailme", "1", "", "", "");
    unset($options);
    echo '</div>';
    echo '</div>';

    echo '<div class="felement fsubmit"><input name="submitbutton" value="'.get_string('registrationsend').'" type="submit" id="id_submitbutton" /></div>';

    echo "</fieldset>\n";

    echo "</form>\n";

    print_footer();

?>
