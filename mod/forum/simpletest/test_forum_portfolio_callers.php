<?php // $Id$
require_once($CFG->libdir.'/simpletest/testportfoliolib.php');
require_once($CFG->dirroot.'/mod/forum/lib.php');
require_once($CFG->dirroot.'/admin/generator.php');

Mock::generate('forum_portfolio_caller', 'mock_caller');
Mock::generate('portfolio_exporter', 'mock_exporter');

class testForumPortfolioCallers extends portfoliolib_test {
    public $module_type = 'forum';
    public $modules = array();
    public $entries = array();
    public $caller;

    public function setUp() {
        global $DB, $USER;

        parent::setUp();

        $settings = array('quiet' => 1,
                          'verbose' => 0,
                          'database_prefix' => 'tst_',
                          'pre_cleanup' => 0,
                          'post_cleanup' => 0,
                          'modules_list' => array($this->module_type),
                          'discussions_per_forum' => 5,
                          'posts_per_discussion' => 10,
                          'number_of_students' => 5,
                          'students_per_course' => 5,
                          'number_of_sections' => 1,
                          'number_of_modules' => 1,
                          'questions_per_course' => 0);

        generator_generate_data($settings);

        $this->modules = $DB->get_records($this->module_type);
        $first_module = reset($this->modules);
        $cm = get_coursemodule_from_instance($this->module_type, $first_module->id);

        $discussions = $DB->get_records('forum_discussions', array('forum' => $first_module->id));
        $first_discussion = reset($discussions);

        $posts = $DB->get_records('forum_posts', array('discussion' => $first_discussion->id));
        $first_post = reset($posts);

        $callbackargs = array('postid' => $first_post->id, 'discussionid' => $first_discussion->id);
        $this->caller = new forum_portfolio_caller($callbackargs);
        $this->caller->set('exporter', new mock_exporter());
        $user = $DB->get_record('user', array('id' => $first_post->userid));
        $this->caller->set('user', $user);
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function test_caller_sha1() {
        $sha1 = $this->caller->get_sha1();
        $this->caller->prepare_package();
        $this->assertEqual($sha1, $this->caller->get_sha1());
    }

}
?>
