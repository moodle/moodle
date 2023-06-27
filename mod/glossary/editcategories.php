<?php

/// This page allows to edit entries categories for a particular instance of glossary

require_once("../../config.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);                       // Course Module ID, or
$usedynalink = optional_param('usedynalink', 0, PARAM_INT);  // category ID
$confirm     = optional_param('confirm', 0, PARAM_INT);      // confirm the action
$name        = optional_param('name', '', PARAM_CLEAN);  // confirm the name

$action = optional_param('action', '', PARAM_ALPHA ); // what to do
$hook   = optional_param('hook', '', PARAM_ALPHANUM); // category ID
$mode   = optional_param('mode', '', PARAM_ALPHA);   // cat

$action = strtolower($action);

$url = new moodle_url('/mod/glossary/editcategories.php', array('id'=>$id));
if ($usedynalink !== 0) {
    $url->param('usedynalink', $usedynalink);
}
if ($confirm !== 0) {
    $url->param('confirm', $confirm);
}
if ($name !== 'name') {
    $url->param('name', $name);
}
if ($action !== 'action') {
    $url->param('action', $action);
}
if ($hook !== 'hook') {
    $url->param('hook', $hook);
}
if ($mode !== 'mode') {
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
    print_error('invalidcoursemodule');
}

if ($hook > 0) {
    if ($category = $DB->get_record("glossary_categories", array("id"=>$hook))) {
        //Check it belongs to the same glossary
        if ($category->glossaryid != $glossary->id) {
            print_error('invalidid', 'glossary');
        }
    } else {
        print_error('invalidcategoryid');
    }
}

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/glossary:managecategories', $context);

$strglossaries   = get_string("modulenameplural", "glossary");
$strglossary     = get_string("modulename", "glossary");

$PAGE->navbar->add(get_string("categories","glossary"),
        new moodle_url('/mod/glossary/editcategories.php', array('id' => $cm->id,'mode' => 'cat')));
if (!empty($action)) {
    $navaction = get_string(core_text::strtolower($action."category"), 'glossary');
    $PAGE->navbar->add($navaction);
}
$PAGE->set_title($glossary->name);
$PAGE->set_heading($course->fullname);

// Prepare format_string/text options
$fmtoptions = array(
    'context' => $context);

if (right_to_left()) { // RTL table alignment support
    $rightalignment = 'left';
    $leftalignment = 'right';
} else {
    $rightalignment = 'right';
    $leftalignment = 'left';

}

if ( $hook >0 ) {

    if ( $action == "edit" ) {
        if ( $confirm ) {
            require_sesskey();
            $action = "";
            $cat = new stdClass();
            $cat->id = $hook;
            $cat->name = $name;
            $cat->usedynalink = $usedynalink;

            $DB->update_record("glossary_categories", $cat);
            $event = \mod_glossary\event\category_updated::create(array(
                'context' => $context,
                'objectid' => $hook
            ));
            $cat->glossaryid = $glossary->id;
            $event->add_record_snapshot('glossary_categories', $cat);
            $event->add_record_snapshot('glossary', $glossary);
            $event->trigger();

            // Reset caches.
            \mod_glossary\local\concept_cache::reset_glossary($glossary);

        } else {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(format_string($glossary->name), 2);
            echo $OUTPUT->heading(format_string(get_string("editcategory", "glossary")), 3);

            $name = $category->name;
            $usedynalink = $category->usedynalink;
            require "editcategories.html";
            echo $OUTPUT->footer();
            die;
        }

    } elseif ( $action == "delete" ) {
        if ( $confirm ) {
            require_sesskey();
            $DB->delete_records("glossary_entries_categories", array("categoryid"=>$hook));
            $DB->delete_records("glossary_categories", array("id"=>$hook));

            $event = \mod_glossary\event\category_deleted::create(array(
                'context' => $context,
                'objectid' => $hook
            ));
            $event->add_record_snapshot('glossary_categories', $category);
            $event->add_record_snapshot('glossary', $glossary);
            $event->trigger();

            // Reset caches.
            \mod_glossary\local\concept_cache::reset_glossary($glossary);

            redirect("editcategories.php?id=$cm->id", get_string("categorydeleted", "glossary"), 2);
        } else {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(format_string($glossary->name), 2);
            echo $OUTPUT->heading(format_string(get_string("deletecategory", "glossary")), 3);

            echo $OUTPUT->box_start('generalbox boxaligncenter errorboxcontent boxwidthnarrow');
            echo "<div class=\"boxaligncenter deletecatconfirm\">".format_string($category->name, true, $fmtoptions)."<br/>";

            $num_entries = $DB->count_records("glossary_entries_categories", array("categoryid"=>$category->id));
            if ( $num_entries ) {
                print_string("deletingnoneemptycategory","glossary");
            }
            echo "<p>";
            print_string("areyousuredelete","glossary");
            echo "</p>";
?>

                <table border="0" width="100" class="confirmbuttons">
                    <tr>
                        <td align="$rightalignment" style="width:50%">
                        <form id="form" method="post" action="editcategories.php">
                        <div>
                        <input type="hidden" name="sesskey"     value="<?php echo sesskey(); ?>" />
                        <input type="hidden" name="id"          value="<?php p($cm->id) ?>" />
                        <input type="hidden" name="action"      value="delete" />
                        <input type="hidden" name="confirm"     value="1" />
                        <input type="hidden" name="mode"         value="<?php echo $mode ?>" />
                        <input type="hidden" name="hook"         value="<?php echo $hook ?>" />
                        <input type="submit" class="btn btn-primary" value=" <?php print_string("yes")?> " />
                        </div>
                        </form>
                        </td>
                        <td align="$leftalignment" style="width:50%">

<?php
            unset($options);
            $options = array ("id" => $id);
            echo $OUTPUT->single_button(new moodle_url("editcategories.php", $options), get_string("no"));
            echo "</td></tr></table>";
            echo "</div>";
            echo $OUTPUT->box_end();
        }
    }

} elseif ( $action == "add" ) {
    if ( $confirm ) {
        require_sesskey();
        $dupcategory = $DB->get_records_sql("SELECT * FROM {glossary_categories} WHERE ".$DB->sql_like('name','?', false)." AND glossaryid=?", array($name, $glossary->id));
        if ( $dupcategory ) {
            redirect("editcategories.php?id=$cm->id&amp;action=add&amp;name=$name", get_string("duplicatecategory", "glossary"), 2);

        } else {
            $action = "";
            $cat = new stdClass();
            $cat->name = $name;
            $cat->usedynalink = $usedynalink;
            $cat->glossaryid = $glossary->id;

            $cat->id = $DB->insert_record("glossary_categories", $cat);
            $event = \mod_glossary\event\category_created::create(array(
                'context' => $context,
                'objectid' => $cat->id
            ));
            $event->add_record_snapshot('glossary_categories', $cat);
            $event->add_record_snapshot('glossary', $glossary);
            $event->trigger();

            // Reset caches.
            \mod_glossary\local\concept_cache::reset_glossary($glossary);
        }
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(format_string($glossary->name), 2);
        echo "<h3 class=\"main\">" . get_string("addcategory", "glossary"). "</h3>";
        $name="";
        require "editcategories.html";
    }
}

if ( $action ) {
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($glossary->name), 2);

?>

<form method="post" action="editcategories.php">
<table width="40%" class="boxaligncenter generalbox" cellpadding="5">
        <tr>
          <th style="width:90%" align="center">
          <?php p(get_string("categories","glossary")) ?></th>
          <th style="width:10%" align="center">
          <?php p(get_string("action")) ?></th>
        </tr>
        <tr><td style="width:100%" colspan="2">



<?php
    $categories = $DB->get_records("glossary_categories", array("glossaryid"=>$glossary->id), "name ASC");

    if ( $categories ) {
        echo '<table width="100%">';
        foreach ($categories as $category) {
            $num_entries = $DB->count_records("glossary_entries_categories", array("categoryid"=>$category->id));
?>

             <tr>
               <td style="width:80%" align="$leftalignment">
               <?php
                    echo "<span class=\"bold\">".format_string($category->name, true, $fmtoptions)."</span> <span>($num_entries " . get_string("entries","glossary") . ")</span>";
               ?>
               </td>
               <td style="width:19%" align="center" class="action">
               <?php
                echo "<a href=\"editcategories.php?id=$cm->id&amp;action=delete&amp;mode=cat&amp;hook=$category->id\">" .
                     $OUTPUT->pix_icon('t/delete', get_string('delete')). "</a> ";
                echo "<a href=\"editcategories.php?id=$cm->id&amp;action=edit&amp;mode=cat&amp;hook=$category->id\">" .
                     $OUTPUT->pix_icon('t/edit', get_string('edit')). "</a> ";
               ?>
               </td>
             </tr>

             <?php

          }
        echo '</table>';
     }
?>

        </td></tr>
        <tr>
        <td style="width:100%" colspan="2"  align="center">
            <?php

             $options['id'] = $cm->id;
             $options['action'] = "add";

             echo "<table class=\"editbuttons\" border=\"0\"><tr><td align=\"$rightalignment\">";
             echo $OUTPUT->single_button(new moodle_url("editcategories.php", $options), get_string("addcategory", "glossary"));
             echo "</td><td align=\"$leftalignment\">";
             unset($options['action']);
             $options['mode'] = 'cat';
             $options['hook'] = $hook;
             echo $OUTPUT->single_button(new moodle_url("view.php", $options), get_string("back","glossary"));
             echo "</td></tr>";
             echo "</table>";

            ?>
        </td>
        </tr>
        </table>


</form>

<?php
echo $OUTPUT->footer();
