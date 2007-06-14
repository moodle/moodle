<?php
{
    require_once('../config.php');
    require_once($CFG->dirroot.'/message/lib.php');

    $id = required_param('id',PARAM_INT);
    $messagebody = optional_param('messagebody','',PARAM_CLEANHTML);
    $send = optional_param('send','',PARAM_ALPHA);
    $returnto = optional_param('returnto','',PARAM_LOCALURL);
    $preview = optional_param('preview','',PARAM_ALPHA);
    $format = optional_param('format',FORMAT_MOODLE,PARAM_INT);
    $edit = optional_param('edit','',PARAM_ALPHA);
    $deluser = optional_param('deluser',0,PARAM_INT);

    if (!$course = get_record('course','id',$id)) {
        error("Invalid course id");
    }

    require_login();
    require_capability('moodle/site:readallmessages', get_context_instance(CONTEXT_COURSE, $id));
    
    // fix for MDL-10112
    if (empty($CFG->messaging)) {
        error("Messaging is disabled on this site");  
    }

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

    print_header($strtitle,$strtitle,"<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> <a href=\"index.php?id=$course->id\">".get_string("participants")."</a> -> ".$strtitle,$formstart);


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
            if ($preview) {
                echo '<form method="post" action="messageselect.php" style="margin: 0 20px;">
<input type="hidden" name="returnto" value="'.stripslashes($returnto).'" />
<input type="hidden" name="id" value="'.$id.'" />
<input type="hidden" name="format" value="'.$format.'" />
';
                echo "<h3>".get_string('previewhtml')."</h3><div class=\"messagepreview\">\n".format_text(stripslashes($messagebody),$format)."\n</div>";
                echo "\n<p align=\"center\"><input type=\"submit\" name=\"send\" value=\"Send\" /> <input type=\"submit\" name=\"edit\" value=\"Edit\" /></p>\n</form>";
            } elseif ($send) {
                $good = 1;
                $teachers = array();
                foreach ($SESSION->emailto[$id] as $user) {
                    $good = $good && message_post_message($USER,$user,addslashes($messagebody),$format,'direct');
                    if ($user->teacher) {
                        $teachers[] = $user->id;
                    }
                }
                if ($good) {
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

}
?>
