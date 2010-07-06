<?PHP // $Id$
    /// This page prints a hotpot quiz
    if (defined('HOTPOT_FIRST_ATTEMPT') && HOTPOT_FIRST_ATTEMPT==false) {
        // this script is being included (by attempt.php)
    } else {
        // this script is being called directly from the browser
        define('HOTPOT_FIRST_ATTEMPT', true);
        require_once("../../config.php");
        require_once("lib.php");

        $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
        $hp = optional_param('hp', 0, PARAM_INT); // hotpot ID

        if ($id) {
            if (! $cm = get_coursemodule_from_id('hotpot', $id)) {
                error("Course Module ID was incorrect");
            }
            if (! $course = get_record("course", "id", $cm->course)) {
                error("Course is misconfigured");
            }
            if (! $hotpot = get_record("hotpot", "id", $cm->instance)) {
                error("Course module is incorrect");
            }

        } else {
            if (! $hotpot = get_record("hotpot", "id", $hp)) {
                error("Course module is incorrect");
            }
            if (! $course = get_record("course", "id", $hotpot->course)) {
                error("Course is misconfigured");
            }
            if (! $cm = get_coursemodule_from_instance("hotpot", $hotpot->id, $course->id)) {
                error("Course Module ID was incorrect");
            }

        }
        // make sure this user is enrolled in this course and can access this HotPot
        require_login($course);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('mod/hotpot:attempt', $context, $USER->id);
    }
    // set nextpage (for error messages)
    $nextpage = "$CFG->wwwroot/course/view.php?id=$course->id";
    // header strings
    $title = format_string($course->shortname.': '.$hotpot->name, true);
    $heading = $course->fullname;

    $navigation = build_navigation('', $cm);

    $button = update_module_button($cm->id, $course->id, get_string("modulename", "hotpot"));
    $button = '<div style="font-size:0.75em;">'.$button.'</div>';
    $loggedinas = user_login_string($course, $USER);
    $time = time();
    $hppassword = optional_param('hppassword', '');
    if (HOTPOT_FIRST_ATTEMPT && !has_capability('mod/hotpot:grade', $context)) {
        // check this quiz is available to this student
        // error message, if quiz is unavailable
        $error = '';
        // check quiz is visible
        if (!hotpot_is_visible($cm)) {
            $error = get_string("activityiscurrentlyhidden");
        // check network address
        } else if ($hotpot->subnet && !address_in_subnet(getremoteaddr(), $hotpot->subnet)) {
            $error = get_string("subneterror", "quiz");
        // check number of attempts
        } else if ($hotpot->attempts && $hotpot->attempts <= count_records_select('hotpot_attempts', 'hotpot='.$hotpot->id.' AND userid='.$USER->id, 'COUNT(DISTINCT clickreportid)')) {
            $error = get_string("nomoreattempts", "quiz");
        // get password
        } else if ($hotpot->password && empty($hppassword)) {
            print_header($title, $heading, $navigation, "", "", true, $button, $loggedinas, false);
            print_heading($hotpot->name);
            $boxalign = 'center';
            $boxwidth = 500;
            if (trim(strip_tags($hotpot->summary))) {
                print_simple_box_start($boxalign, $boxwidth);
                print '<div class="mdl-align">'.format_text($hotpot->summary)."</div>\n";
                print_simple_box_end();
                print "<br />\n";
            }
            print '<form id="passwordform" method="post" action="view.php?id='.$cm->id.'">'."\n";
            print_simple_box_start($boxalign, $boxwidth);
            print '<div class="mdl-align">';
            print get_string('requirepasswordmessage', 'quiz').'<br /><br />';
            print '<b>'.get_string('password').':</b> ';
            print '<input name="hppassword" type="password" value="" /> ';
            print '<input type="submit" value="'.get_string("ok").'" /> ';
            print "</div>\n";
            print_simple_box_end();
            print "</form>\n";
            print_footer();
            exit;
        // check password
        } else if ($hotpot->password && strcmp($hotpot->password, $hppassword)) {
            $error = get_string("passworderror", "quiz");
            $nextpage = "view.php?id=$cm->id";
        // check quiz is open
        } else if ($hotpot->timeopen && $hotpot->timeopen > $time) {
            $error = get_string("quiznotavailable", "quiz", userdate($hotpot->timeopen))."<br />\n";
        // check quiz is not closed
        } else if ($hotpot->timeclose && $hotpot->timeclose < $time) {
            $error = get_string("quizclosed", "quiz", userdate($hotpot->timeclose))."<br />\n";
        }
        if ($error) {
            print_header($title, $heading, $navigation, "", "", true, $button, $loggedinas, false);
            notice($error, $nextpage);
            //
            // script stops here, if quiz is unavailable to student
            //
        }
    }
    $available_msg = '';
    if (!empty($hotpot->timeclose) && $hotpot->timeclose > $time) {
        // quiz is available until 'timeclose'
        $available_msg = get_string("quizavailable", "quiz", userdate($hotpot->timeclose))."<br />\n";
    }
    // open and parse the source file
    if(!$hp = new hotpot_xml_quiz($hotpot)) {
        error("Quiz is unavailable at the moment");
    }
    $get_js = optional_param('js', '', PARAM_ALPHA);
    $get_css = optional_param('css', '', PARAM_ALPHA);
    $framename = optional_param('framename', '', PARAM_ALPHA);
    // look for <frameset> (HP5 v5)
    $frameset = '';
    $frameset_tags = '';
    if (preg_match_all('|<frameset([^>]*)>(.*?)</frameset>|is', $hp->html, $matches)) {
        $last = count($matches[0])-1;
        $frameset = $matches[2][$last];
        $frameset_tags = $matches[1][$last];
    }
    // if HTML is being requested ...
    if (empty($get_js) && empty($get_css)) {
        if (empty($frameset)) {
            // HP v6
            if ($hotpot->navigation==HOTPOT_NAVIGATION_FRAME || $hotpot->navigation==HOTPOT_NAVIGATION_IFRAME) {
                $get_html = ($framename=='main') ? true : false;
            } else {
                $get_html = true;
            }
        } else {
            // HP5 v5
            $get_html = empty($framename) ? true : false;
        }
        
        if ($get_html) {

            if (HOTPOT_FIRST_ATTEMPT) {
                add_to_log($course->id, "hotpot", "view", "view.php?id=$cm->id", "$hotpot->id", "$cm->id");

                $attemptid = hotpot_add_attempt($hotpot->id);
                if (! is_numeric($attemptid)) {
                    error('Could not insert attempt record: '.$db->ErrorMsg);
                }
            }
            $hp->adjust_media_urls();
            if (empty($frameset)) {
                // HP6 v6
                $targetframe = '';
                switch ($hotpot->navigation) {
                    case HOTPOT_NAVIGATION_BUTTONS:
                        // do nothing (i.e. leave buttons as they are)
                        break;
                    case HOTPOT_NAVIGATION_GIVEUP:
                        $hp->insert_giveup_form($attemptid, '<!-- BeginTopNavButtons -->', '<!-- EndTopNavButtons -->');
                        break;
                    case HOTPOT_NAVIGATION_FRAME:
                    case HOTPOT_NAVIGATION_IFRAME:
                        if (empty($CFG->framename)) {
                            $targetframe = '_top';
                        } else {
                            $targetframe = $CFG->framename;
                        }
                        if ($pos = strpos($hp->html, '</body>')) {
                            $insert = ''
                                .'<script type="text/javascript">'."\n"
                                .'//<![CDATA['."\n"
                                ."var obj = document.getElementsByTagName('a');\n"
                                ."if (obj) {\n"
                                ."	var i_max = obj.length;\n"
                                ."	for (var i=0; i<i_max; i++) {\n"
                                ."		if (obj[i].href && ! obj[i].target) {\n"
                                ."			obj[i].target = '$targetframe';\n"
                                ."		}\n"
                                ."	}\n"
                                ."	var obj = null;\n"
                                ."}\n"
                                ."var obj = document.getElementsByTagName('form');\n"
                                ."if (obj) {\n"
                                ."	var i_max = obj.length;\n"
                                ."	for (var i=0; i<i_max; i++) {\n"
                                ."		if (obj[i].action && ! obj[i].target) {\n"
                                ."			obj[i].target = '$targetframe';\n"
                                ."		}\n"
                                ."	}\n"
                                ."	var obj = null;\n"
                                ."}\n"
                                .'//]]>'."\n"
                                .'</script>'."\n"
                            ;
                            $hp->html = substr_replace($hp->html, $insert, $pos, 0);
                        }
                        $hp->remove_nav_buttons();
                        break;
                    default:
                        $hp->remove_nav_buttons();
                }
                if (isset($hp->real_outputformat) && $hp->real_outputformat==HOTPOT_OUTPUTFORMAT_MOBILE) {
                    $hp->insert_submission_form($attemptid, '<!-- BeginSubmissionForm -->', '<!-- EndSubmissionForm -->', true);
                } else {
                    $hp->insert_submission_form($attemptid, '<!-- BeginSubmissionForm -->', '<!-- EndSubmissionForm -->', false, $targetframe);
                }
            } else {
                // HP5 v5
                switch ($hotpot->navigation) {
                    case HOTPOT_NAVIGATION_BUTTONS:
                        // convert URLs in nav buttons
                        break;
                    case HOTPOT_NAVIGATION_GIVEUP:
                        //  $hp->insert_giveup_form($attemptid, '<!-- BeginTopNavButtons -->', '<!-- EndTopNavButtons -->');
                        break;
                    default:
                        // remove navigation buttons
                        $hp->html = preg_replace('#NavBar\+=(.*);#', '', $hp->html);
                }
                $hp->insert_submission_form($attemptid, "var NavBar='", "';");
            }
        }
    }
    //FEEDBACK = new Array();
    //FEEDBACK[0] = ''; // url of feedback page/script
    //FEEDBACK[1] = ''; // array of array('teachername', 'value');
    //FEEDBACK[2] = ''; // 'student name' [formmail only]
    //FEEDBACK[3] = ''; // 'student email' [formmail only]
    //FEEDBACK[4] = ''; // window width
    //FEEDBACK[5] = ''; // window height
    //FEEDBACK[6] = ''; // 'Send a message to teacher' [prompt/button text]
    //FEEDBACK[7] = ''; // 'Title'
    //FEEDBACK[8] = ''; // 'Teacher'
    //FEEDBACK[9] = ''; // 'Message'
    //FEEDBACK[10] = ''; // 'Close this window'
    $feedback = array();
    switch ($hotpot->studentfeedback) {
        case HOTPOT_FEEDBACK_NONE:
            // do nothing
            break;
        case HOTPOT_FEEDBACK_WEBPAGE:
            if (empty($hotpot->studentfeedbackurl)) {
                $hotpot->studentfeedback = HOTPOT_FEEDBACK_NONE;
            } else {
                $feedback[0] = "'$hotpot->studentfeedbackurl'";
            }
            break;
        case HOTPOT_FEEDBACK_FORMMAIL:
            $teachers = hotpot_feedback_teachers($course, $hotpot);
            if (empty($teachers) || empty($hotpot->studentfeedbackurl)) {
                $hotpot->studentfeedback = HOTPOT_FEEDBACK_NONE;
            } else {
                $feedback[0] = "'$hotpot->studentfeedbackurl'";
                $feedback[1] = $teachers;
                $feedback[2] = "'".fullname($USER)."'";
                $feedback[3] = "'".$USER->email."'";
                $feedback[4] = 500; // width
                $feedback[5] = 300; // height
            }
            break;
        case HOTPOT_FEEDBACK_MOODLEFORUM:
            $module = get_record('modules', 'name', 'forum');
            $forums = get_records('forum', 'course', "$course->id");
            if (empty($module) || empty($module->visible) || empty($forums)) {
                $hotpot->studentfeedback = HOTPOT_FEEDBACK_NONE;
            } else {
                $feedback[0] = "'$CFG->wwwroot/mod/forum/index.php?id=$course->id'";
            }
            break;
        case HOTPOT_FEEDBACK_MOODLEMESSAGING:
            $teachers = hotpot_feedback_teachers($course, $hotpot);
            if (empty($CFG->messaging) || empty($teachers)) {
                $hotpot->studentfeedback = HOTPOT_FEEDBACK_NONE;
            } else {
                $feedback[0] = "'$CFG->wwwroot/message/discussion.php?id='";
                $feedback[1] = $teachers;
                $feedback[4] = 400; // width
                $feedback[5] = 500; // height
            }
            break;
        default:
            // do nothing
    }
    if ($hotpot->studentfeedback != HOTPOT_FEEDBACK_NONE) {
        $feedback[6] = "'Send a message to teacher'";
        $feedback[7] = "'Title'";
        $feedback[8] = "'Teacher'";
        $feedback[9] = "'Message'";
        $feedback[10] = "'Close this window'";
        $js = '';
        foreach ($feedback as $i=>$str) {
            $js .= 'FEEDBACK['.$i."] = $str;\n";
        }
        $js = '<script type="text/javascript">'."\n//<![CDATA[\n"."FEEDBACK = new Array();\n".$js."//]]>\n</script>\n";
        $hp->html = preg_replace('|</head>|i', "$js</head>", $hp->html, 1);
    }
    // insert hot-potatoes.js
    $hp->insert_script(HOTPOT_JS);
    // get Moodle pageid and pageclass
    $pageid = '';
    $pageclass = '';
    if (function_exists('page_id_and_class')) {
        page_id_and_class($pageid, $pageclass);
    }
    // extract first <head> tag
    $head = '';
    $pattern = '|<head([^>]*)>(.*?)</head>|is';
    if (preg_match($pattern, $hp->html, $matches)) {
        $head = $matches[2];
        // remove <title>
        $head = preg_replace('|<title[^>]*>(.*?)</title>|is', '', $head);
    }
    // extract <style> tags (and remove from $head)
    $styles = '';
    $pattern = '|<style([^>]*)>(.*?)</style>|is';
    if (preg_match_all($pattern, $head, $matches)) {
        $count = count($matches[0]);
        for ($i=0; $i<$count; $i++) {
            if ($pageid) {
                $styles .= str_replace('TheBody', $pageid, $matches[0][$i])."\n";
            }
            $head = str_replace($matches[0][$i], '', $head);
        }
    }
    // extract <script> tags (and remove from $head)
    $scripts = '';
    $pattern = '|<script([^>]*)>(.*?)</script>|is';
    if (preg_match_all($pattern, $head, $matches)) {
        $count = count($matches[0]);
        for ($i=0; $i<$count; $i++) {
            if ($pageid) {
                $scripts .= str_replace('TheBody', $pageid, $matches[0][$i])."\n";
            }
            $head = str_replace($matches[0][$i], '', $head);
        }
    }
    // extract <body> tags
    $body = '';
    $body_tags = '';
    $footer = '</html>';
    // HP6 and some HP5 (v6 and v4)
    if (preg_match('|<body'.'([^>]*'.'onLoad=(["\'])(.*?)(\\2)'.'[^>]*)'.'>(.*)</body>|is', $hp->html, $matches)) {
        $body = $matches[5]; // contents of first <body onload="StartUp()">...</body> block
        if ($pageid) {
            $body_tags = str_replace(' id="TheBody"', '', $matches[1]);
        }
        // workaround to ensure javascript onload routine for quiz is always executed
        //  $body_tags will only be inserted into the <body ...> tag
        //  if it is included in the theme/$CFG->theme/header.html,
        //  so some old or modified themes may not insert $body_tags
        $body .= ""
        .   '<script type="text/javascript">'."\n"
        .   "//<![CDATA[\n"
        .   "   var s = (typeof(window.onload)=='function') ? onload.toString() : '';\n"
        .   "   if (s.indexOf('".$matches[3]."')<0) {\n"
        .   "       if (s=='') {\n" // no previous onload
        .   "           window.onload = new Function('".$matches[3]."');\n"
        .   "       } else {\n"
        .   "           window.onload_hotpot = onload;\n"
        .   "           window.onload = new Function('window.onload_hotpot();'+'".$matches[3]."');\n"
        .   "       }\n"
        .   "    }\n"
        .   "//]]>\n"
        .   "</script>\n"
        ;
        $footer = '</body>'.$footer;
    } else if ($frameset) { // HP5 v5
        switch ($framename) {
            case 'top':
                print_header($title, $heading, $navigation, "", "", true, $button, $loggedinas);
                print $footer;
            break;
            default:
                // add a HotPot navigation frame at the top of the page
                //$rows = empty($CFG->resource_framesize) ? 85 : $CFG->resource_framesize;
                //$frameset = "\n\t".'<frame src="view.php?id='.$cm->id.'&amp;framename=top" frameborder="0" name="top"></frame>'.$frameset;
                //$frameset_tags = preg_replace('|rows="(.*?)"|', 'rows="'.$rows.',\\1"', $frameset_tags);
                // put navigation into var NavBar='';
                // add form to TopFrame in "WriteFeedback" function
                // OR add form to BottomFrame in "DisplayExercise" function
                // submission form: '<!-- BeginSubmissionForm -->', '<!-- EndSubmissionForm -->'
                // give up form: '<!-- BeginTopNavButtons -->', '<!-- EndTopNavButtons -->'
                print "<html>\n";
                print "<head>\n<title>$title</title>\n$styles\n$scripts</head>\n";
                print "<frameset$frameset_tags>$frameset</frameset>\n";
                print "</html>\n";
            break;
        } // end switch $framename
        exit;
    // other files (maybe not even a HotPots)
    } else if (preg_match('|<body'.'([^>]*)'.'>(.*)</body>|is', $hp->html, $matches)) {
        $body = $matches[2];
        $body_tags = $matches[1];
    }
    // print the quiz to the browser
    if ($get_js) {
        print($scripts);
        exit;
    }
    if ($get_css) {
        print($styles);
        exit;
    }
    // closing tags for "page" and "content" divs
    $footer = '</div></div>'.$footer;
    switch ($hotpot->navigation) {
        case HOTPOT_NAVIGATION_BAR:
            //update_module_button($cm->id, $course->id, $strmodulename.'" style="font-size:0.8em')
            print_header(
                $title, $heading, $navigation, "", $head.$styles.$scripts, true, $button, $loggedinas, false, $body_tags
            );
            if (!empty($available_msg)) {
                notify($available_msg);
            }
            print $body.$footer;
        break;
        case HOTPOT_NAVIGATION_FRAME:
            switch ($framename) {
                case 'top':
                    print_header($title, $heading, $navigation, "", "", true, $button, $loggedinas);
                    print $footer;
                break;
                case 'main':
                    if (!empty($available_msg)) {
                        $hp->insert_message('<!-- BeginTopNavButtons -->', $available_msg);
                    }
                    print $hp->html;
                break;
                default:
                    $txtframesetinfo = get_string('framesetinfo');
                    $txttoptitle     = get_string('navigation', 'hotpot');
                    $txtmaintitle    = get_string('modulename', 'hotpot');

                    $rows = empty($CFG->resource_framesize) ? 85 : $CFG->resource_framesize;

                    @header('Content-Type: text/html; charset=utf-8');
                    print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\" \"http://www.w3.org/TR/html4/frameset.dtd\">\n";
                    print "<html>\n";
                    print "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />\n";
                    print "<head><title>$title</title></head>\n";
                    print "<frameset rows=$rows,*>\n";
                    print "<frame title=\"$txttoptitle\" src=\"view.php?id=$cm->id&amp;framename=top\">\n";
                    print "<frame title=\"$txtmaintitle\" src=\"view.php?id=$cm->id&amp;framename=main\">\n";
                    print "<noframes>\n";
                    print "<p>$txtframesetinfo</p>\n";
                    print "<ul><li><a href=\"view.php?id=$cm->id&amp;framename=top\">$txttoptitle</a></li>\n";
                    print "<li><a href=\"view.php?id=$cm->id&amp;framename=main\">$txtmaintitle</a></li></ul>\n";
                    print "</noframes>\n";
                    print "</frameset>\n";
                    print "</html>\n";
                break;
            } // end switch $framename
        break;
        case HOTPOT_NAVIGATION_IFRAME:
            switch ($framename) {
                case 'main':
                    print $hp->html;
                break;
                default:
                    // set iframe attributes
                    $iframe_id = 'hotpot_embed_object';
                    $iframe_name = 'hotpot_embed_iframe';
                    $iframe_width = '100%';
                    $iframe_height = '100%';
                    $iframe_src = $CFG->wwwroot.'/mod/hotpot/view.php?id='.$cm->id.'&amp;framename=main';
                    $iframe_onload_function = 'set_embed_object_height';
                    $iframe_js = '<script src="'.$CFG->wwwroot.'/mod/hotpot/iframe.js" type="text/javascript"></script>'."\n";

                    print_header(
                        $title, $heading, $navigation,
                        "", $head.$styles.$scripts.$iframe_js, true, $button,
                        $loggedinas, false
                    );
                    if (!empty($available_msg)) {
                        notify($available_msg);
                    }

                    // for XHTML 1.0 Strict compatability, the embedded page should be implemented
                    // using an <object> not an <iframe>. However, IE <object>'s are problematic
                    // (links and forms cannot escape), so we use conditional comments to display
                    // an <iframe> in IE and an <object> in other browsers

                    // print the html element to hold the embedded html page
                    // Note: the iframe in IE needs a "name" attribute for the resizing to work
                    print '<!--[if IE]>'."\n";
                    print '<iframe name="'.$iframe_name.'" id="'.$iframe_id.'" src="'.$iframe_src.'" width="'.$iframe_width.'" height="'.$iframe_height.'"></iframe>'."\n";
                    print '<![endif]-->'."\n";
                    print '<!--[if !IE]> <-->'."\n";
                    print '<object id="'.$iframe_id.'" type="text/html" data="'.$iframe_src.'" width="'.$iframe_width.'" height="'.$iframe_height.'"></object>'."\n";
                    print '<!--> <![endif]-->'."\n";

                    // print javascript to add onload event handler - we do this here because
                    // an object tag should have no onload attribute in XHTML 1.0 Strict
                    print '<script type="text/javascript">'."\n";
                    print '//<![CDATA['."\n";
                    print "var obj = document.getElementById('$iframe_id');\n";
                    print "if (obj) {\n";
                    print "	if (obj.addEventListener) {\n";
                    print "		obj.addEventListener('load', $iframe_onload_function, false);\n";
                    print "	} else if (obj.attachEvent) {\n";
                    print "		obj.attachEvent('onload', $iframe_onload_function);\n";
                    print "	} else {\n";
                    print "		obj['onload'] = $iframe_onload_function;\n";
                    print "	}\n";
                    print "}\n";
                    print "obj = null;\n";
                    print '//]]>'."\n";
                    print '</script>'."\n";

                    print $footer;
            } // end switch $framename
        break;
        case HOTPOT_NAVIGATION_GIVEUP:
            // replace charset , if necessary
            // HotPots are plain ascii (iso-8859-1) with unicode chars encoded as HTML entities
            $charset = get_string("thischarset");
            if ($charset == 'iso-8859-1') {
                // do nothing
            } else {
                $hp->html = preg_replace(
                    '|<meta[^>]*charset=iso-8859-1[^>]*>|is',
                    '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />',
                    $hp->html
                );
            }
            // no break (continue to print html to browser)
        default:
            // HOTPOT_NAVIGATION_BUTTONS
            // HOTPOT_NAVIGATION_NONE
            if (!empty($available_msg)) {
                $hp->insert_message('<!-- BeginTopNavButtons -->', $available_msg);
            }
            print($hp->html);
    }
///////////////////////////////////
/// functions
///////////////////////////////////
function hotpot_feedback_teachers(&$course, &$hotpot) {
    global $CFG;
    $teachers = get_users_by_capability(get_context_instance(CONTEXT_COURSE, $course->id), 'mod/hotpot:grade');
    $teacherdetails = '';
    if (!empty($teachers)) {
        $details = array();
        foreach ($teachers as $teacher) {
            if ($hotpot->studentfeedback==HOTPOT_FEEDBACK_MOODLEMESSAGING) {
                $detail = $teacher->id;
            } else {
                $detail =$teacher->email;
            }
            $details[] = "new Array('".fullname($teacher)."', '$detail')";
        }
        $teacherdetails = 'new Array('.implode(',', $details).");\n";
    }
    return $teacherdetails;
}
?>
