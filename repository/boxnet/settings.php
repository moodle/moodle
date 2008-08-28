<?php //$Id$
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir.'/formslib.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');
require_once(dirname(__FILE__) . '/repository.class.php');

$repositoryid = optional_param('id', 1, PARAM_INT);
$config = optional_param('config', 1, PARAM_INT);
$course = optional_param('course', SITEID, PARAM_INT);
if (! $course = $DB->get_record("course", array("id"=>$course))) {
    print_error('invalidcourseid');
}
$user = $USER;
$fullname = fullname($user);
$strplugin = get_string('repositoryname', 'repository_boxnet');
$returnurl = $CFG->wwwroot.'/repository/boxnet/settings.php?id='.$repositoryid;

require_login($course, false);

class boxnet_user_form extends moodleform {
    private $repositoryid;
    private $userid;
    public function definition() {
        global $CFG;
        $box = new repository_boxnet($this->_customdata['repositoryid']);
        $ret = $box->get_login();
        $mform =& $this->_form;
        $mform->addElement('text', 'name', get_string('username', 'repository_boxnet'), array('size'=>'30', 'value'=>$ret->username));    
        $mform->addElement('passwordunmask', 'passwd', get_string('password', 'repository_boxnet'), array('size'=>'30', 'value'=>$ret->password));    
        $mform->addElement('hidden', 'id', $this->_customdata['repositoryid']);
        $mform->addElement('hidden', 'user', $this->_customdata['userid']);
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons(true, get_string('savechanges'));
    }
}

$navlinks[] = array('name' => $fullname, 'link' => $CFG->wwwroot . '/user/view.php?id=' . $user->id, 'type' => 'misc');
$navlinks[] = array('name' => get_string('repository', 'repository'), 'link' => $CFG->wwwroot . '/user/repository.php', 'type' => 'misc');
$navlinks[] = array('name' => $strplugin, 'link' => null, 'type' => 'misc');

$navigation = build_navigation($navlinks);

print_header("$course->fullname: $fullname: $strplugin", $course->fullname,
             $navigation, "", "", true, "&nbsp;", navmenu($course));

$instance = repository_get_instance($repositoryid);
$mform = new boxnet_user_form('', array('repositoryid' => $repositoryid, 'userid' => $user->id));
$box = new repository_boxnet();
if ($data = data_submitted()){
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', '', $baseurl);
    }
    $box = new repository_boxnet($repositoryid);
    $ret = $box->store_login($data->name, $data->passwd, $user->id);
    if ($ret) {
        redirect($returnurl, get_string('saved', 'repository_boxnet'));
    } else {
        print_error('cannotsave', 'repository_boxnet', $returnurl);
    }
    exit;
} else {
    print_heading(get_string('configplugin', 'repository_boxnet'));
    print_simple_box_start();
    $mform->display();
    print_simple_box_end();
}
