<?php  // $Id$
    require_once('../../config.php');
    require_once('lib.php');

    $id       = required_param('id', PARAM_INT);          // Entry ID
    $confirm  = optional_param('confirm', 0, PARAM_BOOL); // export confirmation
    $prevmode = required_param('prevmode', PARAM_ALPHA);
    $hook     = optional_param('hook', '', PARAM_CLEAN);

    if (!$entry = $DB->get_record('glossary_entries', array('id'=>$id))) {
        print_error('invalidentry');
    }

    if ($entry->sourceglossaryid) {
        //already exported
        if (!$cm = get_coursemodule_from_id('glossary', $entry->sourceglossaryid)) {
            print_error('invalidcoursemodule');
        }
        redirect('view.php?id='.$cm->id.'&amp;mode=entry&amp;hook='.$entry->id);
    }

    if (!$cm = get_coursemodule_from_instance('glossary', $entry->glossaryid)) {
        print_error('invalidcoursemodule');
    }

    if (!$glossary = $DB->get_record('glossary', array('id'=>$cm->instance))) {
        print_error('invalidid', 'glossary');
    }

    if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
        print_error('coursemisconf');
    }

    require_course_login($course->id, true, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/glossary:export', $context);

    $returnurl = "view.php?id=$cm->id&amp;mode=$prevmode&amp;hook=".urlencode($hook);

    if (!$mainglossary = $DB->get_record('glossary', array('course'=>$cm->course, 'mainglossary'=>1))) {
        //main glossary not present
        redirect($returnurl);
    }

    if (!$maincm = get_coursemodule_from_instance('glossary', $mainglossary->id)) {
        print_error('invalidcoursemodule');
    }

    $context     = get_context_instance(CONTEXT_MODULE, $cm->id);
    $maincontext = get_context_instance(CONTEXT_MODULE, $maincm->id);

    if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
        print_error('coursemisconf');
    }


    $strglossaries     = get_string('modulenameplural', 'glossary');
    $entryalreadyexist = get_string('entryalreadyexist','glossary');
    $entryexported     = get_string('entryexported','glossary');

    $navigation = build_navigation('', $cm);

    if (!$mainglossary->allowduplicatedentries) {
        if ($DB->get_record('glossary_entries', array('glossaryid'=>$mainglossary->id, 'lower(concept)'=>moodle_strtolower($entry->concept)))) {
            print_header_simple(format_string($glossary->name), '', $navigation, '', '', true, '', navmenu($course, $cm));
            echo $OUTPUT->notification(get_string('errconceptalreadyexists', 'glossary'));
            echo $OUTPUT->continue_button($returnurl);
            echo $OUTPUT->box_end();
            echo $OUTPUT->footer();
            die;
        }
    }

    if (!data_submitted() or !$confirm or !confirm_sesskey()) {
        print_header_simple(format_string($glossary->name), '', $navigation, '', '', true, '', navmenu($course, $cm));
        echo '<div class="boxaligncenter">';
        $areyousure = '<h2>'.format_string($entry->concept).'</h2><p align="center">'.get_string('areyousureexport','glossary').'<br /><b>'.format_string($mainglossary->name).'</b>?';
        $linkyes    = 'exportentry.php';
        $linkno     = 'view.php';
        $optionsyes = array('id'=>$entry->id, 'confirm'=>1, 'sesskey'=>sesskey(), 'prevmode'=>$prevmode, 'hook'=>$hook);
        $optionsno  = array('id'=>$cm->id, 'mode'=>$prevmode, 'hook'=>$hook);

        notice_yesno($areyousure, $linkyes, $linkno, $optionsyes, $optionsno, 'post', 'get');
        echo '</div>';
        echo $OUTPUT->footer();
        die;

    } else {
        $entry->glossaryid       = $mainglossary->id;
        $entry->sourceglossaryid = $glossary->id;

        $DB->update_record('glossary_entries', $entry);

        // move attachments too
        $fs = get_file_storage();

        if ($oldfiles = $fs->get_area_files($context->id, 'glossary_attachment', $entry->id)) {
            foreach ($oldfiles as $oldfile) {
                $file_record = new object();
                $file_record->contextid = $maincontext->id;
                $fs->create_file_from_storedfile($file_record, $oldfile);
            }
            $fs->delete_area_files($context->id, 'glossary_attachment', $entry->id);
            $entry->attachment = '1';
        } else {
            $entry->attachment = '0';
        }
        $DB->update_record('glossary_entries', $entry);

        redirect ($returnurl);
    }
?>
