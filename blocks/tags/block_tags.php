<?php

class block_tags extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_tags');
        // the cron function goes through all users, so only do daily
        // (this creates rss feeds for personal course tags)
        // removed until rsslib supports dc/cc
        // $this->cron = 60*60*24;
    }

    function instance_allow_multiple() {
        return true;
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function instance_allow_config() {
        return true;
    }

    function specialization() {

        // load userdefined title and make sure it's never empty
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname','block_tags');
        } else {
            $this->title = $this->config->title;
        }
    }

    function get_content() {

        global $CFG, $COURSE, $SITE, $USER, $SCRIPT, $OUTPUT;

        if (empty($CFG->usetags)) {
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('disabledtags', 'block_tags');
            }
            return $this->content;
        }

        if (empty($this->config->numberoftags)) {
            $this->config->numberoftags = 80;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        /// Get a list of tags

        require_once($CFG->dirroot.'/tag/locallib.php');

        if (empty($CFG->block_tags_showcoursetags) or !$CFG->block_tags_showcoursetags) {

            $this->content->text = tag_print_cloud($this->config->numberoftags, true);

        // start of show course tags section
        } else {

            require_once($CFG->dirroot.'/tag/coursetagslib.php');

            // Permissions and page awareness
            $systemcontext = get_context_instance(CONTEXT_SYSTEM);
            $loggedin = isloggedin() && !isguestuser();
            $coursepage = $canedit = false;
            $coursepage = (isset($this->page->course->id) && $this->page->course->id != SITEID);
            $mymoodlepage = ($SCRIPT == '/my/index.php') ? true : false;
            $sitepage = (isset($this->page->course->id) && $this->page->course->id == SITEID && !$mymoodlepage);
            $coursecontext = get_context_instance(CONTEXT_COURSE, $this->page->course->id);
            if ($coursepage) {
                $canedit =  has_capability('moodle/tag:create', $systemcontext);
            }

            // Check rss feed - temporarily removed until Dublin Core tags added
            // provides a feed of users course tags for each unit they have tagged
            //$rssfeed = '';
            //if (file_exists($CFG->dataroot.'/'.SITEID.'/usertagsrss/'.$USER->id.'/user_unit_tags_rss.xml')) {
            //    $rssfeed = '/file.php/'.SITEID.'/usertagsrss/'.$USER->id.'/user_unit_tags_rss.xml';
            //}

            // Language strings
            $tagslang = 'block_tags';

            // DB hits to get groups of marked up tags (if available)
            //TODO check whether time limited personal tags are required
            $numoftags = $this->config->numberoftags;
            $sort = 'name';
            $coursetagdivs = array();
            $alltags = $officialtags = $coursetags = $commtags = $mytags = $courseflag = '';
            if ($sitepage or $coursepage) {
                $alltags = coursetag_print_cloud(coursetag_get_all_tags($sort, $this->config->numberoftags), true);
                $officialtags = coursetag_print_cloud(coursetag_get_tags(0, 0, 'official', $numoftags, $sort), true);
                $commtags = coursetag_print_cloud(coursetag_get_tags(0, 0, 'default', $numoftags, $sort), true);
                if ($loggedin) {
                    $mytags = coursetag_print_cloud(coursetag_get_tags(0, $USER->id, 'default', $numoftags, $sort), true);
                }
            }
            if ($coursepage) {
                $coursetags = coursetag_print_cloud(coursetag_get_tags($this->page->course->id, 0, '', $numoftags, $sort), true);
                if (!$coursetags) $coursetags = get_string('notagsyet', $tagslang);
                $courseflag = '&amp;courseid='.$this->page->course->id;
            }
            if ($mymoodlepage) {
                $mytags = coursetag_print_cloud(coursetag_get_tags(0, $USER->id, 'default', $numoftags, $sort), true);
                $officialtags = coursetag_print_cloud(coursetag_get_tags(0, 0, 'official', $numoftags, $sort), true);
                $commtags = coursetag_print_cloud(coursetag_get_tags(0, 0, 'default', $numoftags, $sort), true);
            }

            // Prepare the divs and javascript that displays the groups of tags (and which is displayed first)
            $moretags = $CFG->wwwroot.'/tag/coursetags_more.php';
            $moretagstitle = get_string('moretags', $tagslang);
            $moretagsstring = get_string('more', $tagslang);
            $displayblock = 'style="display:block"';
            $displaynone = 'style="display:none"'; //only one div created below will be displayed at a time
            if ($alltags) {
                if ($sitepage) {
                    $display = $displayblock;
                } else {
                    $display = $displaynone;
                }
                $alltagscontent = '
                    <div id="f_alltags" '.$display.'>'.
                        get_string("alltags", $tagslang).
                        '<div class="coursetag_list">'.$alltags.'</div>
                        <div class="coursetag_morelink">
                            <a href="'.$moretags.'?show=all'.$courseflag.'" title="'.$moretagstitle.'">'.$moretagsstring.'</a>
                        </div>
                    </div>';
                $coursetagdivs[] = 'f_alltags';
            }
            if ($mytags) {
                if ($mymoodlepage) {
                    $display = $displayblock;
                } else {
                    $display = $displaynone;
                }
                $mytagscontent = '
                    <div id="f_mytags" '.$display.'>';
                        /*if ($rssfeed) { // - temporarily removed
                            $mytagscontent .= link_to_popup_window(
                                $rssfeed, $name='popup',
                                '<img src="'.$CFG->wwwroot.'/pix/rss.gif" alt="User Unit Tags RSS" /> My Unit Tags RSS',
                                $height=600, $width=800,
                                $title='My Unit Tags RSS', $options='menubar=1,scrollbars,resizable', $return=true).'<br />';
                        }*/
                $mytagscontent .=
                        get_string('mytags', $tagslang).
                        '<div class="coursetag_list">'.$mytags.'</div>
                        <div class="coursetag_morelink">
                            <a href="'.$moretags.'?show=my'.$courseflag.'" title="'.$moretagstitle.'">'.$moretagsstring.'</a>
                        </div>
                    </div>';
                $coursetagdivs[] = 'f_mytags';
            }
            if ($officialtags) {
                if ($mytags or $alltags) {
                    $display = $displaynone;
                } else {
                    $display = $displayblock;
                }
                $officialtagscontent = '
                    <div id="f_officialtags" '.$display.'>'.
                        get_string('officialtags', $tagslang).
                        '<div class="coursetag_list">'.$officialtags.'</div>
                        <div class="coursetag_morelink">
                            <a href="'.$moretags.'?show=official'.$courseflag.'" title="'.$moretagstitle.'">'.$moretagsstring.'</a>
                        </div>
                    </div>';
                $coursetagdivs[] = 'f_officialtags';
            }
            if ($coursetags) {
                if ($coursepage) {
                    $display = $displayblock;
                } else {
                    $display = $displaynone;
                }
                $coursetagscontent = '
                    <div id="f_coursetags" '.$display.'>'.
                        get_string('coursetags', $tagslang).
                        '<div class="coursetag_list">'.$coursetags.'</div>
                        <div class="coursetag_morelink">
                            <a href="'.$moretags.'?show=course'.$courseflag.'" title="'.$moretagstitle.'">'.$moretagsstring.'</a>
                        </div>
                    </div>';
                $coursetagdivs[] = 'f_coursetags';
            }
            if ($commtags) {
                $commtagscontent = '
                    <div id="f_commtags" '.$displaynone.'>'.
                        get_string('communitytags', $tagslang).
                        '<div class="coursetag_list">'.$commtags.'</div>
                        <div class="coursetag_morelink">
                            <a href="'.$moretags.'?show=community'.$courseflag.'" title="'.$moretagstitle.'">'.$moretagsstring.'</a>
                        </div>
                    </div>';
                $coursetagdivs[] .= 'f_commtags';
            }
            // Tidy up the end of a javascript array and add javascript
            coursetag_get_jscript($coursetagdivs);

            // Add the divs (containing the tags) to the block's content
            if ($alltags) { $this->content->text .= $alltagscontent; }
            if ($mytags) { $this->content->text .= $mytagscontent; }
            if ($officialtags) { $this->content->text .= $officialtagscontent; }
            if ($coursetags) { $this->content->text .= $coursetagscontent; }
            if ($commtags) { $this->content->text .= $commtagscontent; }

            // add the input form section (allowing a user to tag the current course) and navigation, or loggin message
            if ($loggedin) {
                // only show the input form on course pages for those allowed (or not barred)
                if ($coursepage && $canedit) {
                    //$this->content->footer .= coursetag_get_jscript();
                    $tagthisunit = get_string('tagthisunit', $tagslang);
                    $buttonadd = get_string('add', $tagslang);
                    $arrowtitle = get_string('arrowtitle', $tagslang);
                    $sesskey = sesskey();
                    $arrowright = $OUTPUT->pix_url('t/arrow_left');
                    $this->content->footer .= <<<EOT
                        <hr />
                        <form action="{$CFG->wwwroot}/tag/coursetags_add.php" method="post" id="coursetag"
                                onsubmit="return ctags_checkinput(this.coursetag_new_tag.value)">
                            <div style="display: none;">
                                <input type="hidden" name="returnurl" value="{$this->page->url}" />
                                <input type="hidden" name="entryid" value="$COURSE->id" />
                                <input type="hidden" name="userid" value="$USER->id" />
                                <input type="hidden" name="sesskey" value="$sesskey" />
                            </div>
                            <div><label for="coursetag_new_tag">$tagthisunit</label></div>
                            <div class="coursetag_form_wrapper">
                            <div class="coursetag_form_positioner">
                                <div class="coursetag_form_input1">
                                    <input type="text" name="coursetag_sug_keyword" class="coursetag_form_input1a" disabled="disabled" />
                                </div>
                                <div class="coursetag_form_input2">
                                    <input type="text" name="coursetag_new_tag" id="coursetag_new_tag" class="coursetag_form_input2a"
                                        onfocus="ctags_getKeywords()" onkeyup="ctags_getKeywords()" maxlength="50" />
                                </div>
                                <div class="coursetag_form_input3" id="coursetag_sug_btn">
                                    <a title="$arrowtitle">
                                        <img src="$arrowright" width="10" height="10" alt="enter" onclick="ctags_setKeywords()" />
                                    </a>
                                </div>
                            </div>
                            <div style="display: inline;">
                                <button type="submit">$buttonadd</button>
                            </div>
                            </div>
                        </form>
EOT;
                    // add the edit link
                    $this->content->footer .= '
                        <div>
                        <a href="'.$CFG->wwwroot.'/tag/coursetags_edit.php?courseid='.$this->page->course->id.'"
                        title="'.get_string('edittags', $tagslang).'">'.get_string('edittags', $tagslang).'</a>
                        </div>';
                }

                // Navigation elements at the bottom of the block
                // show the alternative displays options if available
                $elementid = 'coursetagslinks_'.$this->instance->id;
                if ($mytags or $officialtags or $commtags or $coursetags) {
                    $this->content->footer .= '<div id="'.$elementid.'"></div>';
                }
                // This section sets the order of the links
                $coursetagslinks = array();
                if ($mytags) {
                    $coursetagslinks['my'] = array('title'=>get_string('mytags2', $tagslang),
                                                    'onclick'=>'f_mytags',
                                                    'text'=>get_string('mytags1', $tagslang));
                }
                // because alltags is always present, only show link if there is something else as well
                if ($alltags and ($mytags or $officialtags or $commtags or $coursetags)) {
                    $coursetagslinks['all'] = array('title'=>get_string('alltags2', $tagslang),
                                                    'onclick'=>'f_alltags',
                                                    'text'=>get_string('alltags1', $tagslang));
                }
                if ($officialtags) {
                    $coursetagslinks['off'] = array('title'=>get_string('officialtags2', $tagslang),
                                                    'onclick'=>'f_officialtags',
                                                    'text'=>get_string('officialtags1', $tagslang));
                }
                //if ($commtags) {
                //    $coursetagslinks['com'] = array('title'=>get_string('communitytags2', $tagslang),
                //                                    'onclick'=>'f_commtags',
                //                                    'text'=>get_string('communitytags1', $tagslang));
                //}
                if ($coursetags) {
                    $coursetagslinks['crs'] = array('title'=>get_string('coursetags2', $tagslang),
                                                    'onclick'=>'f_coursetags',
                                                    'text'=>get_string('coursetags1', $tagslang));
                }
                coursetag_get_jscript_links($elementid, $coursetagslinks);

            } else {
                //if not logged in
                $this->content->footer = '<hr />'.get_string('please', $tagslang).'
                    <a href="'.get_login_url().'">'.get_string('login', $tagslang).'
                        </a> '.get_string('tagunits', $tagslang);
            }
        }
        // end of show course tags section

        return $this->content;
    }
}

