<?PHP  // $Id$
       // register.php - allows admin to register their site on moodle.org

    include("../config.php");
    require_once("../lib/countries.php");

    require_login();

    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    if (!$site = get_site()) {
        redirect("index.php");
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

	print_header("$site->shortname: $strregistration", "$site->fullname", 
                 "<a href=\"../$CFG->admin/index.php\">$stradministration</a> -> $strregistration");

    print_heading($strmanagemodules);

    print_simple_box($strregistrationinfo, "center", "70%");
    echo "<br />";

/// Print the form

    print_simple_box_start("center", "", "$THEME->cellheading");

    echo "<form name=\"form\" action=\"http://moodle.org/register/\" method=post>";
    echo "<table cellpadding=9 border=0>\n";

    echo "<tr valign=top>";
    echo "<td align=right><p>Moodle URL:</td>";
    echo "<td><p>$CFG->wwwroot</td>";
    //// The following hidden variables are to help prevent fake entries being sent.
    //// Together they form a key.  If any of these change between updates then the entry 
    //// is flagged as a new entry and will be manually checked by the list maintainer
    echo "<input type=\"hidden\" name=\"url\" value=\"$CFG->wwwroot\">";
    echo "<input type=\"hidden\" name=\"secret\" value=\"$admin->password\">";
    echo "<input type=\"hidden\" name=\"host\" value=\"".$_SERVER["HTTP_HOST"]."\">";
    echo "<input type=\"hidden\" name=\"lang\" value=\"".current_language()."\">";
    echo "</td></tr>\n";

    echo "<tr valign=top>";
    echo "<td align=right><p>".get_string("currentversion").":</td>";
    echo "<td><p>$CFG->release ($CFG->version)</td>";
    echo "<input type=\"hidden\" name=\"version\" value=\"$CFG->version\">";
    echo "<input type=\"hidden\" name=\"release\" value=\"$CFG->release\">";
    echo "</td></tr>\n";

    echo "<tr valign=top>";
    echo "<td align=right><p>".get_string("fullsitename").":</td>";
    echo "<td><p><input size=50 type=\"text\" name=\"sitename\" value=\"$site->fullname\"></td>";
    echo "</tr>\n";

    echo "<tr valign=top>";
    echo "<td align=right><p>".get_string("country").":</td>";
    echo "<td><p>";
    choose_from_menu ($COUNTRIES, "country", $admin->country, get_string("selectacountry")."...", "", "");
    echo "</td>";
    echo "</tr>\n";

    echo "<tr valign=top>";
    echo "<td align=right><p><a href=\"http://moodle.org/sites\" title=\"See the current list of sites\" target=_blank>".get_string("publicdirectory")."</a>:</td>";
    echo "<td><p>";
    $options[0] = get_string("publicdirectory0");
    $options[1] = get_string("publicdirectory1");
    $options[2] = get_string("publicdirectory2");
    choose_from_menu ($options, "public", "2", "", "", "");
    unset($options);
    echo "</td>";
    echo "</tr>\n";

    echo "<tr valign=top>";
    echo "<td colspan=2><hr size=1 noshade>";
    echo "</tr>\n";

    echo "<tr valign=top>";
    echo "<td align=right><p>".get_string("administrator").":</td>";
    echo "<td><p><input size=50 type=\"text\" name=\"adminname\" value=\"$admin->firstname $admin->lastname\"></td>";
    echo "</tr>\n";

    echo "<tr valign=top>";
    echo "<td align=right><p>".get_string("email").":</td>";
    echo "<td><p><input size=50 type=\"text\" name=\"adminemail\" value=\"$admin->email\"></td>";
    echo "</tr>\n";

    echo "<tr valign=top>";
    echo "<td align=right><p>".get_string("registrationemail")."</a>:</td>";
    echo "<td><p>";
    $options[0] = get_string("registrationno");
    $options[1] = get_string("registrationyes");
    choose_from_menu ($options, "mailme", "1", "", "", "");
    unset($options);
    echo "</td>";
    echo "</tr>\n";

    echo "<tr valign=top>";
    echo "<td align=right>&nbsp;</td>";
    echo "<td><p><input type=\"submit\" value=\"".get_string("registrationsend")."\"></td>";
    echo "</tr>\n";


    echo "</table>";
    echo "</form>\n";

    print_simple_box_end();

    echo "<br />";

    print_footer();

?>
