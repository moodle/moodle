<?php  // $Id$
    require_once('../../config.php');
    require_once('lib.php');

    $id    = required_param('id', PARAM_INT);           // course module ID
    $entry = required_param('entry', PARAM_INT);     // Entry ID
    $confirm = optional_param('confirm', 0, PARAM_INT); // confirmation

    $hook = optional_param('hook', '', PARAM_ALPHANUM);
    $mode = optional_param('mode', '', PARAM_ALPHA);
        
    global $USER, $CFG;

    $PermissionGranted = 1;

    $cm = get_coursemodule_from_id('glossary', $id);
    if ( ! $cm ) {
        $PermissionGranted = 0;
    } else {
        $mainglossary = get_record('glossary','course',$cm->course, 'mainglossary',1);
        if ( ! $mainglossary ) {
            $PermissionGranted = 0;
        }
    }
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/glossary:export', $context);

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $glossary = get_record('glossary', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }

    $strglossaries   = get_string('modulenameplural', 'glossary');
    $entryalreadyexist = get_string('entryalreadyexist','glossary');
    $entryexported = get_string('entryexported','glossary');

    $navigation = build_navigation('', $cm);
    print_header_simple(format_string($glossary->name), '', $navigation, '', '', true, '', navmenu($course, $cm));

    if ( $PermissionGranted ) {
        $entry = get_record('glossary_entries', 'id', $entry);

        if ( !$confirm ) {
            echo '<div class="boxaligncenter">';
            $areyousure = get_string('areyousureexport','glossary');
            notice_yesno ('<h2>'.format_string($entry->concept).'</h2><p align="center">'.$areyousure.'<br /><b>'.format_string($mainglossary->name).'</b>?',
                'exportentry.php?id='.$id.'&amp;mode='.$mode.'&amp;hook='.$hook.'&amp;entry='.$entry->id.'&amp;confirm=1',
                'view.php?id='.$cm->id.'&amp;mode='.$mode.'&amp;hook='.$hook);
            echo '</div>';
        } else {
            if ( ! $mainglossary->allowduplicatedentries ) {
                $dupentry = get_record('glossary_entries','glossaryid', $mainglossary->id, 'lower(concept)',moodle_strtolower(addslashes($entry->concept)));
                if ( $dupentry ) {
                    $PermissionGranted = 0;
                }
            }
            if ( $PermissionGranted ) {

                $dbentry = new stdClass;
                $dbentry->id = $entry->id;
                $dbentry->glossaryid       = $mainglossary->id;
                $dbentry->sourceglossaryid = $glossary->id;
                
                if (! update_record('glossary_entries', $dbentry)) {
                    error('Could not export the entry to the main glossary');
                } else {
                    print_simple_box_start('center', '60%');
                    echo '<p align="center"><font size="3">'.$entryexported.'</font></p></font>';

                    print_continue('view.php?id='.$cm->id.'&amp;mode=entry&amp;hook='.$entry->id);
                    print_simple_box_end();

                    print_footer();

                    redirect('view.php?id='.$cm->id.'&amp;mode=entry&amp;hook='.$entry->id);
                    die;
                }
            } else {
                print_simple_box_start('center', '60%', '#FFBBBB');
                echo '<p align="center"><font size="3">'.$entryalreadyexist.'</font></p></font>';
                echo '<p align="center">';

                print_continue('view.php?id='.$cm->id.'&amp;mode=entry&amp;hook='.$entry->id);

                print_simple_box_end();
            }
        }
    } else {
            print_simple_box_start('center', '60%', '#FFBBBB');
            notice('A weird error was found while trying to export this entry. Operation cancelled.');

            print_continue('view.php?id='.$cm->id.'&amp;mode=entry&amp;hook='.$entry->id);

            print_simple_box_end();
    }

    print_footer();
?>
