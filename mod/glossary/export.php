<?php

require_once("../../config.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);      // Course Module ID

$mode= optional_param('mode', '', PARAM_ALPHA);           // term entry cat date letter search author approval
$hook= optional_param('hook', '', PARAM_CLEAN);           // the term, entry, cat, etc... to look for based on mode
$cat = optional_param('cat',0, PARAM_ALPHANUM);

$url = new moodle_url('/mod/glossary/export.php', array('id'=>$id));
if ($cat !== 0) {
    $url->param('cat', $cat);
}
if ($mode !== '') {
    $url->param('mode', $mode);
}

$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('glossary', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $glossary = $DB->get_record("glossary", array("id"=>$cm->instance))) {
    print_error('invalidid', 'glossary');
}

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/glossary:export', $context);

$strglossaries = get_string("modulenameplural", "glossary");
$strglossary = get_string("modulename", "glossary");
$strallcategories = get_string("allcategories", "glossary");
$straddentry = get_string("addentry", "glossary");
$strnoentries = get_string("noentries", "glossary");
$strsearchindefinition = get_string("searchindefinition", "glossary");
$strsearch = get_string("search");
$strexportfile = get_string("exportfile", "glossary");
$strexportentries = get_string('exportentriestoxml', 'glossary');

$PAGE->set_url('/mod/glossary/export.php', array('id'=>$cm->id));
$PAGE->navbar->add($strexportentries);
$PAGE->set_title($glossary->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_secondary_active_tab('modulepage');
$PAGE->activityheader->disable();

echo $OUTPUT->header();
$backlink = html_writer::link(new moodle_url('view.php', ['id' => $cm->id]),
    get_string('back'), ['class' => 'btn btn-secondary']);
echo html_writer::tag('div', $backlink, ['class' => 'tertiary-navigation']);
echo $OUTPUT->heading($strexportentries);
echo $OUTPUT->box_start('glossarydisplay generalbox');
$exporturl = moodle_url::make_pluginfile_url($context->id, 'mod_glossary', 'export', 0, "/$cat/", 'export.xml', true);

?>
    <form action="<?php echo $exporturl->out(); ?>" method="post">
        <input class="btn btn-primary" type="submit" value="<?php p($strexportfile)?>" />
    </form>
<?php
    // don't need cap check here, we share with the general export.
    if (!empty($CFG->enableportfolios) && $DB->count_records('glossary_entries', array('glossaryid' => $glossary->id))) {
        require_once($CFG->libdir . '/portfoliolib.php');
        $button = new portfolio_add_button();
        $button->set_callback_options('glossary_full_portfolio_caller', array('id' => $cm->id), 'mod_glossary');
        $button->render();
    }
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
?>
