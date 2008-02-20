<?php // $Id$

    require_once('../config.php');
    require_once($CFG->dirroot.'/message/lib.php');

    $id = required_param('id',PARAM_INT);
    $messagebody = optional_param('messagebody','',PARAM_CLEANHTML);
    $send = optional_param('send','',PARAM_RAW);   // Content is actually treated as boolean
    $preview = optional_param('preview','',PARAM_RAW);   // Content is actually treated as boolean
    $edit = optional_param('edit','',PARAM_RAW);   // Content is actually treated as boolean
    $returnto = optional_param('returnto','',PARAM_LOCALURL);
    $format = optional_param('format',FORMAT_MOODLE,PARAM_INT);
    $deluser = optional_param('deluser',0,PARAM_INT);

    if (!$course = get_record('course','id',$id)) {
        error("Invalid course id");
    }

    require_login();

    $coursecontext = get_context_instance(CONTEXT_COURSE, $id);   // Course context
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);   // SYSTEM context
    require_capability('moodle/course:bulkmessaging', $coursecontext);

    if (empty($SESSION->emailto)) {
        $SESSION->emailto = array();
    }
    if (!array_key_exists($id,$SESSION->emailto)) {
        $SESSION->emailto[$id] = array();
    }

    if ($deluser) {
        if (array_key_exists($id,$SESSION->emailto) && array_key_exists($deluser,$SESSION->emailto[$id])) {
            unset($SESSION->emailto[$id][$deluser]);
        }
    }

    if (empty($SESSION->emailselect[$id]) || $messagebody) {
        $SESSION->emailselect[$id] = array('messagebody' => $messagebody);
    }

    $messagebody = $SESSION->emailselect[$id]['messagebody'];

    $count = 0;

    foreach ($_POST as $k => $v) {
        if (preg_match('/^(user|teacher)(\d+)$/',$k,$m)) {
            if (!array_key_exists($m[2],$SESSION->emailto[$id])) {
                if ($user = get_record_select('user','id = '.$m[2],'id,firstname,lastname,idnumber,email,emailstop,mailformat,lastaccess')) {
                    $SESSION->emailto[$id][$m[2]] = $user;
                    $SESSION->emailto[$id][$m[2]]->teacher = ($m[1] == 'teacher');
                    $count++;
                }
            }
        }
    }

    $strtitle = get_string('coursemessage');

    if (empty($messagebody)) {
        $formstart = "theform.messagebody";
    } else {
        $formstart = "";
    }

    $navlinks = array();
    if (has_capability('moodle/course:viewparticipants', $coursecontext) || has_capability('moodle/site:viewparticipants', $systemcontext)) {
        $navlinks[] = array('name' => get_string('participants'), 'link' => "index.php?id=$course->id", 'type' => 'misc');
    }
    $navlinks[] = array('name' => $strtitle, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header($strtitle,$strtitle,$navigation,$formstart);

    // if messaging is disabled on site, we can still allow users with capabilities to send emails instead
    if (empty($CFG->messaging)) {
        notify(get_string('messagingdisabled','message'));  
    }

    if ($count) {
        if ($count == 1) {
            $heading =  get_string('addedrecip','moodle',$count);
        } else {
            $heading = get_string('addedrecips','moodle',$count);
        }
        print_heading($heading);
    }

    if (!empty($messagebody) && !$edit && !$deluser && ($preview || $send)) {
        if (count($SESSION->emailto[$id])) {
            if (!empty($preview)) {
                echo '<form method="post" action="messageselect.php" style="margin: 0 20px;">
<input type="hidden" name="returnto" value="'.s($returnto).'" />
<input type="hidden" name="id" value="'.$id.'" />
<input type="hidden" name="format" value="'.$format.'" />
';
                echo "<h3>".get_string('previewhtml')."</h3><div class=\"messagepreview\">\n".format_text(stripslashes($messagebody),$format)."\n</div>\n";
                echo '<p align="center"><input type="submit" name="send" value="'.get_string('sendmessage', 'message').'" />'."\n";
                echo '<input type="submit" name="edit" value="'.get_string('update').'" /></p>';
                echo "\n</form>";
            } else if (!empty($send)) {
                $good = 1;
                $teachers = array();
                foreach ($SESSION->emailto[$id] as $user) {
                    $good = $good && message_post_message($USER,$user,addslashes($messagebody),$format,'direct');
                    if ($user->teacher) {
                        $teachers[] = $user->id;
                    }
                }
                if (!empty($good)) {
                    print_heading(get_string('messagedselectedusers'));
                    unset($SESSION->emailto[$id]);
                    unset($SESSION->emailselect[$id]);
                } else {
                    print_heading(get_string('messagedselectedusersfailed'));
                }
                echo '<p align="center"><a href="index.php?id='.$id.'">'.get_string('backtoparticipants').'</a></p>';
            }
            print_footer();
            exit;
        } else {
            notify(get_string('nousersyet'));
        }
    }

    echo '<p align="center"><a href="'.$returnto.'">'.get_string("keepsearching").'</a>'.((count($SESSION->emailto[$id])) ? ', '.get_string('usemessageform') : '').'</p>';

    if ((!empty($send) || !empty($preview) || !empty($edit)) && (empty($messagebody))) {
        notify(get_string('allfieldsrequired'));
    }

    if (count($SESSION->emailto[$id])) {
        $usehtmleditor = can_use_richtext_editor();
        require("message.html");
        if ($usehtmleditor) {
            use_html_editor("messagebody");
        }
    }

    print_footer();


?>
