<?PHP

include('../config.php');

require_login();

if (!$users = get_records("user", "picture", "1", "id", "id,firstname,lastname")) {
    error("no users!");
}

$title = get_string("users");

print_header($title, $title, $title);

foreach ($users as $user) {
   echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&course=1\" title=\"$user->firstname $user->lastname\">";
   echo "<img border=0 src=\"$CFG->wwwroot/user/pix.php/$user->id/f1.jpg\" width=100 height=100 alt=\"$user->firstname $user->lastname\" />";
   echo "</a> \n";
}

print_footer();

?>
