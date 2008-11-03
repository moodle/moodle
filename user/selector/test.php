<?php
$justdefineclass = defined('MOODLE_INTERNAL');

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/user/selector/lib.php');

class test_user_selector extends user_selector_base {
    public function __construct($name) {
        parent::__construct($name);
    }

    public function find_users($search) {
        global $DB;
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $sql = 'SELECT ' . $this->required_fields_sql('u') .
                ' FROM {user} u' .
                ' WHERE ' . $wherecondition .
                ' ORDER BY u.lastname, u.firstname';
        $users = $DB->get_recordset_sql($sql, $params);
        $groupedusers = array();
        if ($search) {
            $groupname = "Users matching '" . $search . "'";
        } else {
            $groupname = 'All users';
        }
        foreach ($users as $user) {
            $groupedusers[$groupname][$user->id] = $user;
        }
        return $groupedusers;
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['file'] = 'user/selector/test.php';
        return $options;
    }
}

if ($justdefineclass) {
    return;
}

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));
print_header();

$userselector = new test_user_selector('myuserselector');
//$userselector->set_multiselect(false);

$users = $userselector->get_selected_users();
if (!empty($users)) {
    print_heading('Users that were selected');
    echo '<ul>';
    foreach ($users as $user) {
        echo '<li>', fullname($user), '</li>';
    }
    echo '</ul>';
}

print_collapsible_region('Blah, blah, blah', '', 'mybox', 'Click me!', 'testbox');

echo '<form action="test.php"><div style="width: 30em;"><label for="myuserselector">Select users</label>';
$userselector->display();
echo '<p><input type="submit" id="submitbutton" value="Submit" /></p>';
echo '</div></form>';

echo '<script type="text/javascript">
function selection_change(isempty) {
    document.getElementById("submitbutton").disabled = isempty;
}
user_selector.get("myuserselector").subscribe("selectionchanged", selection_change);
</script>';

print_footer();
?>