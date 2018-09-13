<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Checklist output functions.
 *
 * @package   mod_checklist
 * @copyright 2016 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_checklist\local\checklist_item;
use mod_checklist\local\output_status;
use mod_checklist\local\progress_info;

defined('MOODLE_INTERNAL') || die();

class mod_checklist_renderer extends plugin_renderer_base {
    public function progress_bars($totalitems, $requireditems, $allcompleteitems, $reqcompleteitems) {
        $out = '';

        if ($requireditems > 0 && $totalitems > $requireditems) {
            $out .= $this->progress_bar($requireditems, $reqcompleteitems, true);
        }
        $out .= $this->progress_bar($totalitems, $allcompleteitems, false);

        return $out;
    }

    public function progress_bar($totalitems, $completeitems, $isrequired) {
        $out = '';

        $percentcomplete = $totalitems ? (($completeitems * 100.0) / $totalitems) : 0.0;
        if ($isrequired) {
            $heading = get_string('percentcomplete', 'checklist');
            $spanid = 'checklistprogressrequired';
        } else {
            $heading = get_string('percentcompleteall', 'checklist');
            $spanid = 'checklistprogressall';
        }

        // Heading.
        $heading .= ':&nbsp;';
        $out .= html_writer::div($heading, 'checklist_progress_heading');

        // Progress bar.
        $progress = '';
        $progress .= html_writer::div('&nbsp;', 'checklist_progress_inner', ['style' => "width: {$percentcomplete}%;"]);
        $progress .= html_writer::div('&nbsp;', 'checklist_progress_anim', ['style' => "width: {$percentcomplete}%;"]);
        $progress = html_writer::div($progress, 'checklist_progress_outer');
        $progress .= html_writer::span('&nbsp;'.sprintf('%0d%%', $percentcomplete), 'checklist_progress_percent');

        // Wrap in span + add clearer br.
        $out .= html_writer::span($progress, '', ['id' => $spanid]);
        $out .= html_writer::empty_tag('br', ['class' => 'clearer']);

        return $out;
    }

    public function progress_bar_external($totalitems, $completeitems, $width, $showpercent) {
        $out = '';

        $percentcomplete = $totalitems ? ($completeitems * 100.0 / $totalitems) : 0.0;

        $out .= html_writer::div('&nbsp;', 'checklist_progress_inner', ['style' => "width: {$percentcomplete}%;"]);
        $out = html_writer::div($out, 'checklist_progress_outer', ['style' => "width: $width;"]);
        if ($showpercent) {
            $out .= html_writer::span('&nbsp;'.sprintf('%0d%%', $percentcomplete), 'checklist_progress_percent');
        }
        $out .= html_writer::empty_tag('br', ['class' => 'clearer']);
        return $out;
    }

    /**
     * @param checklist_item[] $items
     * @param checklist_item[] $useritems
     * @param bool|int[] $groupings
     * @param string $intro
     * @param output_status $status
     * @param progress_info|null $progress
     * @param object $student (optional) the student whose checklist is being viewed (if not viewing own checklist)
     */
    public function checklist_items($items, $useritems, $groupings, $intro, output_status $status, $progress, $student = null) {
        echo $this->output->box_start('generalbox boxwidthwide boxaligncenter checklistbox');

        echo html_writer::tag('div', '&nbsp;', array('id' => 'checklistspinner'));

        $thispageurl = new moodle_url($this->page->url);
        if ($student) {
            $thispageurl->param('studentid', $student->id);
        }

        $strteachername = '';
        $struserdate = '';
        $strteacherdate = '';
        if ($status->is_viewother()) {
            echo '<h2>'.get_string('checklistfor', 'checklist').' '.fullname($student, true).'</h2>';
            echo '&nbsp;';
            echo '<form style="display: inline;" action="'.$thispageurl->out_omit_querystring().'" method="get">';
            echo html_writer::input_hidden_params($thispageurl, array('studentid'));
            echo '<input type="submit" name="viewall" value="'.get_string('viewall', 'checklist').'" />';
            echo '</form>';

            if (!$status->is_editcomments()) {
                echo '<form style="display: inline;" action="'.$thispageurl->out_omit_querystring().'" method="get">';
                echo html_writer::input_hidden_params($thispageurl);
                echo '<input type="hidden" name="editcomments" value="on" />';
                echo ' <input type="submit" name="viewall" value="'.get_string('addcomments', 'checklist').'" />';
                echo '</form>';
            }
            echo '<form style="display: inline;" action="'.$thispageurl->out_omit_querystring().'" method="get">';
            echo html_writer::input_hidden_params($thispageurl);
            echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
            echo '<input type="hidden" name="action" value="toggledates" />';
            echo ' <input type="submit" name="toggledates" value="'.get_string('toggledates', 'checklist').'" />';
            echo '</form>';

            $strteacherdate = get_string('teacherdate', 'mod_checklist');
            $struserdate = get_string('userdate', 'mod_checklist');
            $strteachername = get_string('teacherid', 'mod_checklist');
        }

        echo $intro;
        echo '<br/>';

        if ($status->is_showprogressbar() && $progress) {
            echo $this->progress_bars($progress->totalitems, $progress->requireditems,
                                      $progress->allcompleteitems, $progress->requiredcompleteitems);
        }

        if (!$items) {
            print_string('noitems', 'checklist');
        } else {
            $focusitem = false;
            if ($status->is_updateform()) {
                if ($status->is_canaddown() && !$status->is_viewother()) {
                    echo '<form style="display:inline;" action="'.$thispageurl->out_omit_querystring().'" method="get">';
                    echo html_writer::input_hidden_params($thispageurl);
                    if ($status->is_addown()) {
                        // Switch on for any other forms on this page (but off if this form submitted).
                        $thispageurl->param('useredit', 'on');
                        echo '<input type="submit" name="submit" value="'.get_string('addownitems-stop', 'checklist').'" />';
                    } else {
                        echo '<input type="hidden" name="useredit" value="on" />';
                        echo '<input type="submit" name="submit" value="'.get_string('addownitems', 'checklist').'" />';
                    }
                    echo '</form>';
                }

                echo '<form action="'.$thispageurl->out_omit_querystring().'" method="post">';
                echo html_writer::input_hidden_params($thispageurl);
                echo '<input type="hidden" name="action" value="updatechecks" />';
                echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
            }

            if ($useritems) {
                reset($useritems);
            }

            if ($status->is_teachermarklocked()) {
                echo '<p style="checklistwarning">'.get_string('lockteachermarkswarning', 'checklist').'</p>';
            }

            echo '<ol class="checklist" id="checklistouter">';
            $currindent = 0;
            foreach ($items as $item) {

                if ($item->hidden) {
                    continue;
                }

                if ($status->is_checkgroupings() && $item->grouping) {
                    if (!in_array($item->grouping, $groupings)) {
                        continue; // Current user is not a member of this item's grouping, so skip.
                    }
                }

                while ($item->indent > $currindent) {
                    $currindent++;
                    echo '<ol class="checklist">';
                }
                while ($item->indent < $currindent) {
                    $currindent--;
                    echo '</ol>';
                }
                $itemname = '"item'.$item->id.'"';
                $checked = '';
                if ($status->is_updateform() || $status->is_viewother() || $status->is_userreport()) {
                    if ($item->is_checked_student()) {
                        $checked = ' checked="checked" ';
                    }
                }
                if ($status->is_viewother() || $status->is_userreport()) {
                    $checked .= ' disabled="disabled" ';
                } else if (!$status->is_overrideauto()) {
                    if ($item->is_auto_item()) {
                        $checked .= ' disabled="disabled" ';
                    }
                }
                switch ($item->colour) {
                    case 'red':
                        $itemcolour = 'itemred';
                        break;
                    case 'orange':
                        $itemcolour = 'itemorange';
                        break;
                    case 'green':
                        $itemcolour = 'itemgreen';
                        break;
                    case 'purple':
                        $itemcolour = 'itempurple';
                        break;
                    default:
                        $itemcolour = 'itemblack';
                }

                $checkclass = '';
                if ($item->is_heading()) {
                    $optional = ' class="itemheading '.$itemcolour.'" ';
                } else if ($item->is_required()) {
                    $optional = ' class="'.$itemcolour.'" ';
                } else {
                    $optional = ' class="itemoptional '.$itemcolour.'" ';
                    $checkclass = ' itemoptional';
                }

                echo '<li>';
                if ($status->is_showteachermark()) {
                    if (!$item->is_heading()) {
                        if ($status->is_viewother()) {
                            $opts = [
                                CHECKLIST_TEACHERMARK_UNDECIDED => '',
                                CHECKLIST_TEACHERMARK_YES => get_string('yes'),
                                CHECKLIST_TEACHERMARK_NO => get_string('no'),
                            ];
                            $attr = ['id' => 'item'.$item->id]; // TODO davo - fix itemname handling.
                            if ($status->is_teachermarklocked() && $item->is_checked_teacher()) {
                                $attr['disabled'] = 'disabled';
                            } else if (!$status->is_showcheckbox() && !$status->is_overrideauto() && $item->is_auto_item()) {
                                // For teacher-only checklists with autoupdate not allowed to override, disable changing of
                                // automatic update items.
                                $attr['disabled'] = 'disabled';
                            }

                            echo html_writer::select($opts, "items[{$item->id}]", $item->teachermark, false, $attr);

                        } else {
                            echo html_writer::empty_tag('img', [
                                'src' => $item->get_teachermark_image_url(),
                                'alt' => $item->get_teachermark_text(),
                                'title' => $item->get_teachermark_text(),
                                'class' => $item->get_teachermark_class(),
                            ]);
                        }
                    }
                }
                if ($status->is_showcheckbox()) {
                    if (!$item->is_heading()) {
                        $id = ' id='.$itemname.' ';
                        if ($status->is_viewother() && $status->is_showteachermark()) {
                            $id = '';
                        }
                        echo '<input class="checklistitem'.$checkclass.'" type="checkbox" name="items[]" '.$id.$checked.
                            ' value="'.$item->id.'" />';
                    }
                }
                echo '<label for='.$itemname.$optional.'>'.format_string($item->displaytext).'</label>';
                echo $this->item_grouping($item);

                echo $this->checklist_item_link($item);

                if ($status->is_addown()) {
                    echo '&nbsp;<a href="'.$thispageurl->out(true, array(
                            'itemid' => $item->id, 'sesskey' => sesskey(), 'action' => 'startadditem'
                        )).'">';
                    $title = get_string('additemalt', 'checklist');
                    echo $this->output->pix_icon('add', $title, 'mod_checklist', ['title' => $title]).'</a>';
                }

                if ($item->duetime) {
                    if ($item->duetime > time()) {
                        echo '<span class="checklist-itemdue"> '.userdate($item->duetime, get_string('strftimedate')).'</span>';
                    } else {
                        echo '<span class="checklist-itemoverdue"> '.userdate($item->duetime, get_string('strftimedate')).'</span>';
                    }
                }

                if ($status->is_showcompletiondates()) {
                    if (!$item->is_heading()) {
                        if ($status->is_showteachermark() && $item->teachertimestamp) {
                            if ($item->get_teachername()) {
                                echo '<span class="itemteachername" title="'.$strteachername.'">'.
                                    $item->get_teachername().'</span>';
                            }
                            echo '<span class="itemteacherdate" title="'.$strteacherdate.'">'.
                                userdate($item->teachertimestamp, get_string('strftimedatetimeshort')).'</span>';
                        }
                        if ($status->is_showcheckbox() && $item->usertimestamp) {
                            echo '<span class="itemuserdate" title="'.$struserdate.'">'.
                                userdate($item->usertimestamp, get_string('strftimedatetimeshort')).'</span>';
                        }
                    }
                }

                if ($status->is_teachercomments()) {
                    if ($comment = $item->get_comment()) {
                        echo ' <span class="teachercomment">&nbsp;';
                        if ($comment->commentby) {
                            echo '<a href="'.$comment->get_commentby_url().'">'.$comment->get_commentby_name().'</a>: ';
                        }
                        if ($status->is_editcomments()) {
                            $outid = '';
                            if (!$focusitem) {
                                $focusitem = 'firstcomment';
                                $outid = ' id="firstcomment" ';
                            }
                            echo '<input type="text" name="teachercomment['.$item->id.']" value="'.s($comment->text).
                                '" '.$outid.'/>';
                        } else {
                            echo s($comment->text);
                        }
                        echo '&nbsp;</span>';
                    } else if ($status->is_editcomments()) {
                        echo '&nbsp;<input type="text" name="teachercomment['.$item->id.']" />';
                    }
                }

                echo '</li>';

                // Output any user-added items.
                if ($useritems) {
                    /** @var checklist_item $useritem */
                    $useritem = current($useritems);

                    if ($useritem && ($useritem->position == $item->position)) {
                        $thisitemurl = new moodle_url($thispageurl, ['action' => 'updateitem', 'sesskey' => sesskey()]);

                        echo '<ol class="checklist">';
                        while ($useritem && ($useritem->position == $item->position)) {
                            $itemname = '"item'.$useritem->id.'"';
                            $checked = ($status->is_updateform() && $useritem->is_checked_student()) ? ' checked="checked" ' : '';
                            if ($useritem->is_editme()) {
                                $itemtext = explode("\n", $useritem->displaytext, 2);
                                $itemtext[] = '';
                                $text = $itemtext[0];
                                $note = $itemtext[1];
                                $thisitemurl->param('itemid', $useritem->id);

                                echo '<li>';
                                echo '<div style="float: left;">';
                                if ($status->is_showcheckbox()) {
                                    echo '<input class="checklistitem itemoptional" type="checkbox" name="items[]" id='.
                                        $itemname.$checked.' disabled="disabled" value="'.$useritem->id.'" />';
                                }
                                echo '<form style="display:inline" action="'.$thisitemurl->out_omit_querystring().
                                    '" method="post">';
                                echo html_writer::input_hidden_params($thisitemurl);
                                echo '<input type="text" size="'.CHECKLIST_TEXT_INPUT_WIDTH.'" name="displaytext" value="'.s($text).
                                    '" id="updateitembox" />';
                                echo '<input type="submit" name="updateitem" value="'.get_string('updateitem', 'checklist').'" />';
                                echo '<br />';
                                echo '<textarea name="displaytextnote" rows="3" cols="25">'.s($note).'</textarea>';
                                echo '</form>';
                                echo '</div>';

                                echo '<form style="display:inline;" action="'.$thispageurl->out_omit_querystring().
                                    '" method="get">';
                                echo html_writer::input_hidden_params($thispageurl);
                                echo '<input type="submit" name="canceledititem" value="'.
                                    get_string('canceledititem', 'checklist').'" />';
                                echo '</form>';
                                echo '<br style="clear: both;" />';
                                echo '</li>';

                                $focusitem = 'updateitembox';
                            } else {
                                echo '<li>';
                                if ($status->is_showcheckbox()) {
                                    echo '<input class="checklistitem itemoptional" type="checkbox" name="items[]" id='.
                                        $itemname.$checked.' value="'.$useritem->id.'" />';
                                }
                                $splittext = explode("\n", s($useritem->displaytext), 2);
                                $splittext[] = '';
                                $text = $splittext[0];
                                $note = str_replace("\n", '<br />', $splittext[1]);
                                echo '<label class="useritem" for='.$itemname.'>'.$text.'</label>';

                                if ($status->is_addown()) {
                                    $baseurl = $thispageurl.'&amp;itemid='.$useritem->id.'&amp;sesskey='.sesskey().'&amp;action=';
                                    echo '&nbsp;<a href="'.$baseurl.'edititem">';
                                    $title = get_string('edititem', 'checklist');
                                    echo $this->output->pix_icon('t/edit', $title, 'moodle', ['title' => $title]).'</a>';

                                    echo '&nbsp;<a href="'.$baseurl.'deleteitem" class="deleteicon">';
                                    $title = get_string('deleteitem', 'checklist');
                                    echo $this->output->pix_icon('remove', $title, 'mod_checklist', ['title' => $title]).'</a>';
                                }
                                if ($note != '') {
                                    echo '<div class="note">'.$note.'</div>';
                                }

                                echo '</li>';
                            }
                            $useritem = next($useritems);
                        }
                        echo '</ol>';
                    }
                }

                if ($status->is_addown() && ($item->id == $status->get_additemafter())) {
                    $thisitemurl = clone $thispageurl;
                    $thisitemurl->param('action', 'additem');
                    $thisitemurl->param('position', $item->position);
                    $thisitemurl->param('sesskey', sesskey());

                    echo '<ol class="checklist"><li>';
                    echo '<div style="float: left;">';
                    echo '<form action="'.$thispageurl->out_omit_querystring().'" method="post">';
                    echo html_writer::input_hidden_params($thisitemurl);
                    if ($status->is_showcheckbox()) {
                        echo '<input type="checkbox" disabled="disabled" />';
                    }
                    echo '<input type="text" size="'.CHECKLIST_TEXT_INPUT_WIDTH.'" name="displaytext" value="" id="additembox" />';
                    echo '<input type="submit" name="additem" value="'.get_string('additem', 'checklist').'" />';
                    echo '<br />';
                    echo '<textarea name="displaytextnote" rows="3" cols="25"></textarea>';
                    echo '</form>';
                    echo '</div>';

                    echo '<form style="display:inline" action="'.$thispageurl->out_omit_querystring().'" method="get">';
                    echo html_writer::input_hidden_params($thispageurl);
                    echo '<input type="submit" name="canceledititem" value="'.get_string('canceledititem', 'checklist').'" />';
                    echo '</form>';
                    echo '<br style="clear: both;" />';
                    echo '</li></ol>';

                    if (!$focusitem) {
                        $focusitem = 'additembox';
                    }
                }
            }
            echo '</ol>';

            if ($status->is_updateform()) {
                echo '<input id="checklistsavechecks" type="submit" name="submit" value="'.
                    get_string('savechecks', 'checklist').'" />';
                if ($status->is_viewother()) {
                    echo '&nbsp;<input type="submit" name="save" value="'.get_string('savechecks', 'mod_checklist').'" />';
                    echo '&nbsp;<input type="submit" name="savenext" value="'.get_string('saveandnext').'" />';
                    echo '&nbsp;<input type="submit" name="viewnext" value="'.get_string('next').'" />';
                }
                echo '</form>';
            }

            if ($focusitem) {
                echo '<script type="text/javascript">document.getElementById("'.$focusitem.'").focus();</script>';
            }

            if ($status->is_addown()) {
                echo '<script type="text/javascript">';
                echo 'function confirmdelete(url) {';
                echo 'if (confirm("'.get_string('confirmdeleteitem', 'checklist').'")) { window.location = url; } ';
                echo '} ';
                echo 'var links = document.getElementById("checklistouter").getElementsByTagName("a"); ';
                echo 'for (var i in links) { ';
                echo 'if (links[i].className == "deleteicon") { ';
                echo 'var url = links[i].href;';
                echo 'links[i].href = "#";';
                echo 'links[i].onclick = new Function( "confirmdelete(\'"+url+"\')" ) ';
                echo '}} ';
                echo '</script>';
            }
        }

        echo $this->output->box_end();
    }

    protected function checklist_item_link(checklist_item $item) {
        $out = '';
        if ($url = $item->get_link_url()) {
            $out .= '&nbsp;';
            switch ($item->get_link_type()) {
                case checklist_item::LINK_MODULE:
                    $icon = $this->output->pix_icon('follow_link', get_string('linktomodule', 'checklist'), 'mod_checklist');
                    break;
                case checklist_item::LINK_COURSE:
                    $icon = $this->output->pix_icon('i/course', get_string('linktocourse', 'checklist'));
                    break;
                case checklist_item::LINK_URL:
                    $icon = $this->output->pix_icon('follow_link', get_string('linktourl', 'checklist'), 'mod_checklist');
                    break;
            }
            $out .= html_writer::link($url, $icon);
        }
        return $out;
    }

    /**
     * @param checklist_item[] $items
     * @param output_status $status
     */
    public function checklist_edit_items($items, $status) {
        echo $this->output->box_start('generalbox boxwidthwide boxaligncenter');

        $currindent = 0;
        $addatend = true;
        $focusitem = false;
        $hasauto = false;

        $thispageurl = new moodle_url($this->page->url, ['sesskey' => sesskey()]);
        if ($status->get_additemafter()) {
            $thispageurl->param('additemafter', $status->get_additemafter());
        }
        if ($status->is_editdates()) {
            $thispageurl->param('editdates', 'on');
        }
        if ($status->get_itemid()) {
            $thispageurl->param('itemid', $status->get_itemid());
        }

        if ($status->is_autoupdatewarning()) {
            switch ($status->get_autoupdatewarning()) {
                case CHECKLIST_MARKING_STUDENT:
                    echo '<p>'.get_string('autoupdatewarning_student', 'checklist').'</p>';
                    break;
                case CHECKLIST_MARKING_TEACHER:
                    echo '<p>'.get_string('autoupdatewarning_teacher', 'checklist').'</p>';
                    break;
                default:
                    echo '<p class="checklistwarning">'.get_string('autoupdatewarning_both', 'checklist').'</p>';
                    break;
            }
        }

        // Start the ordered list of checklist items.
        $attr = ['class' => 'checklist'];
        if ($status->is_editdates() || $status->is_editlinks()) {
            $attr['class'] .= ' checklist-extendedit';
        }
        echo html_writer::start_tag('ol', $attr);

        // Output each item.
        if ($items) {
            $lastitem = count($items);
            $lastindent = 0;

            echo html_writer::start_tag('form', array('action' => $thispageurl->out_omit_querystring(), 'method' => 'post'));
            echo html_writer::input_hidden_params($thispageurl);

            if ($status->is_autopopulate()) {
                echo html_writer::empty_tag('input', array(
                    'type' => 'submit', 'name' => 'showhideitems',
                    'value' => get_string('showhidechecked', 'checklist')
                ));
            }

            foreach ($items as $item) {

                while ($item->indent > $currindent) {
                    $currindent++;
                    echo '<ol class="checklist">';
                }
                while ($item->indent < $currindent) {
                    $currindent--;
                    echo '</ol>';
                }

                $itemname = '"item'.$item->id.'"';
                $itemurl = new moodle_url($thispageurl, ['itemid' => $item->id]);

                switch ($item->colour) {
                    case 'red':
                        $itemcolour = 'itemred';
                        $nexticon = 'colour_orange';
                        break;
                    case 'orange':
                        $itemcolour = 'itemorange';
                        $nexticon = 'colour_green';
                        break;
                    case 'green':
                        $itemcolour = 'itemgreen';
                        $nexticon = 'colour_purple';
                        break;
                    case 'purple':
                        $itemcolour = 'itempurple';
                        $nexticon = 'colour_black';
                        break;
                    default:
                        $itemcolour = 'itemblack';
                        $nexticon = 'colour_red';
                }

                $autoitem = ($status->is_autopopulate()) && ($item->moduleid != 0);
                if ($autoitem) {
                    $autoclass = ' itemauto';
                } else {
                    $autoclass = '';
                }
                $hasauto = $hasauto || ($item->moduleid != 0);

                if ($item->is_editme()) {
                    echo '<li class="checklist-edititem">';
                } else {
                    echo '<li>';
                }

                echo html_writer::start_span('', array('style' => 'display: inline-block; width: 16px;'));
                if ($autoitem && $item->hidden != CHECKLIST_HIDDEN_BYMODULE) {
                    echo html_writer::checkbox('items['.$item->id.']', $item->id, false, '',
                                               array('title' => $item->displaytext));
                }
                echo html_writer::end_span();

                // Item optional toggle.
                if ($item->is_optional()) {
                    $title = get_string('optionalitem', 'checklist');
                    echo '<a href="'.$itemurl->out(true, array('action' => 'makeheading')).'">';
                    echo $this->output->pix_icon('empty_box', $title, 'mod_checklist', ['title' => $title]).'</a>&nbsp;';
                    $optional = ' class="itemoptional '.$itemcolour.$autoclass.'" ';
                } else if ($item->is_heading()) {
                    if ($item->hidden) {
                        $title = get_string('headingitem', 'checklist');
                        echo $this->output->pix_icon('no_box', $title, 'mod_checklist', ['title' => $title]).'&nbsp;';
                        $optional = ' class="'.$itemcolour.$autoclass.' itemdisabled"';
                    } else {
                        $title = get_string('headingitem', 'checklist');
                        if (!$autoitem) {
                            echo '<a href="'.$itemurl->out(true, array('action' => 'makerequired')).'">';
                        }
                        echo $this->output->pix_icon('no_box', $title, 'mod_checklist', ['title' => $title]);
                        if (!$autoitem) {
                            echo '</a>';
                        }
                        echo '&nbsp;';
                        $optional = ' class="itemheading '.$itemcolour.$autoclass.'" ';
                    }
                } else if ($item->hidden) {
                    $title = get_string('requireditem', 'checklist');
                    echo $this->output->pix_icon('tick_box', $title, 'mod_checklist', ['title' => $title]).'&nbsp;';
                    $optional = ' class="'.$itemcolour.$autoclass.' itemdisabled"';
                } else {
                    $title = get_string('requireditem', 'checklist');
                    echo '<a href="'.$itemurl->out(true, array('action' => 'makeoptional')).'">';
                    echo $this->output->pix_icon('tick_box', $title, 'mod_checklist', ['title' => $title]).'</a>&nbsp;';
                    $optional = ' class="'.$itemcolour.$autoclass.'"';
                }

                if ($item->is_editme()) {
                    // Edit item form.
                    $focusitem = 'updateitembox';
                    $addatend = false;
                    echo $this->edit_item_form($status, $item);

                } else {
                    // Item text.
                    echo '<label for='.$itemname.$optional.'>'.format_string($item->displaytext).'</label> ';

                    // Grouping.
                    echo $this->item_grouping($item);

                    // Item colour.
                    echo '<a href="'.$itemurl->out(true, array('action' => 'nextcolour')).'">';
                    $title = get_string('changetextcolour', 'checklist');
                    echo $this->output->pix_icon($nexticon, $title, 'mod_checklist', ['title' => $title]).'</a>';

                    // Edit item.
                    if (!$autoitem) {
                        $edititemurl = new moodle_url($itemurl, ['action' => 'edititem']);
                        $edititemurl->remove_params('additemafter');
                        echo '<a href="'.$edititemurl->out().'">';
                        $title = get_string('edititem', 'checklist');
                        echo $this->output->pix_icon('t/edit', $title, 'moodle', ['title' => $title]).'</a>&nbsp;';
                    }

                    // Change item indent.
                    if (!$autoitem && $item->indent > 0) {
                        echo '<a href="'.$itemurl->out(true, array('action' => 'unindentitem')).'">';
                        $title = get_string('unindentitem', 'checklist');
                        echo $this->output->pix_icon('t/left', $title, 'moodle', ['title' => $title]).'</a>';
                    }
                    if (!$autoitem && ($item->indent < CHECKLIST_MAX_INDENT) && (($lastindent + 1) > $currindent)) {
                        echo '<a href="'.$itemurl->out(true, array('action' => 'indentitem')).'">';
                        $title = get_string('indentitem', 'checklist');
                        echo $this->output->pix_icon('t/right', $title, 'moodle', ['title' => $title]).'</a>';
                    }

                    echo '&nbsp;';

                    // Move item up/down.
                    if (!$autoitem && $item->position > 1) {
                        echo '<a href="'.$itemurl->out(true, array('action' => 'moveitemup')).'">';
                        $title = get_string('moveitemup', 'checklist');
                        echo $this->output->pix_icon('t/up', $title, 'moodle', ['title' => $title]).'</a>';
                    }
                    if (!$autoitem && $item->position < $lastitem) {
                        echo '<a href="'.$itemurl->out(true, array('action' => 'moveitemdown')).'">';
                        $title = get_string('moveitemdown', 'checklist');
                        echo $this->output->pix_icon('t/down', $title, 'moodle', ['title' => $title]).'</a>';
                    }

                    // Hide/delete item.
                    if ($autoitem) {
                        if ($item->hidden != CHECKLIST_HIDDEN_BYMODULE) {
                            echo '&nbsp;<a href="'.$itemurl->out(true, array('action' => 'deleteitem')).'">';
                            if ($item->hidden == CHECKLIST_HIDDEN_MANUAL) {
                                $title = get_string('show');
                                echo $this->output->pix_icon('t/show', $title, 'moodle', ['title' => $title]).'</a>';
                            } else {
                                $title = get_string('hide');
                                echo $this->output->pix_icon('t/hide', $title, 'moodle', ['title' => $title]).'</a>';
                            }
                        }
                    } else {
                        echo '&nbsp;<a href="'.$itemurl->out(true, array('action' => 'deleteitem')).'">';
                        $title = get_string('deleteitem', 'checklist');
                        echo $this->output->pix_icon('t/delete', $title, 'moodle', ['title' => $title]).'</a>';
                    }

                    // Add item icon.
                    echo '&nbsp;&nbsp;&nbsp;<a href="'.$itemurl->out(true, array('action' => 'startadditem')).'">';
                    $title = get_string('additemhere', 'checklist');
                    echo $this->output->pix_icon('add', $title, 'mod_checklist', ['title' => $title]).'</a>';

                    // Due time.
                    if ($item->duetime) {
                        if ($item->duetime > time()) {
                            echo '<span class="checklist-itemdue"> '.userdate($item->duetime, get_string('strftimedate')).'</span>';
                        } else {
                            echo '<span class="checklist-itemoverdue"> '.
                                userdate($item->duetime, get_string('strftimedate')).'</span>';
                        }
                    }

                    // Link (if any).
                    echo $this->checklist_item_link($item);
                }

                if ($status->get_additemafter() == $item->id) {
                    $addatend = false;
                    if (!$focusitem) {
                        $focusitem = 'additembox';
                    }
                    echo $this->add_item_form($status, $thispageurl, $currindent, $item->position + 1);
                }

                $lastindent = $currindent;

                echo '</li>';
            }

            echo html_writer::end_tag('form');
        }

        if ($addatend) {
            if (!$focusitem) {
                $focusitem = 'additembox';
            }
            echo $this->add_item_form($status, $thispageurl, $currindent);
        }
        echo '</ol>';
        while ($currindent) {
            $currindent--;
            echo '</ol>';
        }

        // Edit dates button.
        $editdatesurl = new moodle_url($thispageurl);
        $editdatesurl->remove_params('sesskey');
        if ($status->is_editdates()) {
            $editdatesurl->remove_params('editdates');
            $editdatesstr = get_string('editdatesstop', 'mod_checklist');
        } else {
            $editdatesurl->param('editdates', 'on');
            $editdatesstr = get_string('editdatesstart', 'mod_checklist');
        }
        echo $this->output->single_button($editdatesurl, $editdatesstr, 'get');

        // Remove autopopulate button.
        if (!$status->is_autopopulate() && $hasauto) {
            $removeautourl = new moodle_url($thispageurl, ['removeauto' => 1]);
            echo $this->output->single_button($removeautourl, get_string('removeauto', 'mod_checklist'));
        }

        if ($focusitem) {
            echo '<script type="text/javascript">document.getElementById("'.$focusitem.'").focus();</script>';
        }

        echo $this->output->box_end();
    }

    protected function edit_date_form($ts = 0) {
        $out = '';

        $out .= '<br>';
        $id = uniqid();
        if ($ts == 0) {
            $disabled = true;
            $date = usergetdate(time());
        } else {
            $disabled = false;
            $date = usergetdate($ts);
        }
        $day = $date['mday'];
        $month = $date['mon'];
        $year = $date['year'];

        // Day.
        $opts = range(1, 31);
        $opts = array_combine($opts, $opts);
        $out .= html_writer::select($opts, 'duetime[day]', $day, null, ['id' => "timedueday{$id}"]);

        // Month.
        $opts = [];
        for ($i = 1; $i <= 12; $i++) {
            $opts[$i] = userdate(gmmktime(12, 0, 0, $i, 15, 2000), "%B");
        }
        $out .= html_writer::select($opts, 'duetime[month]', $month, null, ['id' => "timeduemonth{$id}"]);

        // Year.
        $today = usergetdate(time());
        $thisyear = $today['year'];
        $opts = range($thisyear - 5, $thisyear + 10);
        $opts = array_combine($opts, $opts);
        $out .= html_writer::select($opts, 'duetime[year]', $year, null, ['id' => "timedueyear{$id}"]);

        // Disabled checkbox.
        $attr = [
            'type' => 'checkbox', 'name' => 'duetimedisable',
            'id' => "timeduedisable{$id}", 'onclick' => "toggledate{$id}()"
        ];
        if ($disabled) {
            $attr['checked'] = 'checked';
        }
        $out .= html_writer::empty_tag('input', $attr);
        $out .= html_writer::label(get_string('disable'), "timeduedisable{$id}");

        // Script to disable items when unchecked.
        $out .= <<< ENDSCRIPT
<script type="text/javascript">
    function toggledate{$id}() {
        var disable = document.getElementById('timeduedisable{$id}').checked;
        var day = document.getElementById('timedueday{$id}');
        var month = document.getElementById('timeduemonth{$id}');
        var year = document.getElementById('timedueyear{$id}');
        if (disable) {
            day.setAttribute('disabled','disabled');
            month.setAttribute('disabled', 'disabled');
            year.setAttribute('disabled', 'disabled');
        } else {
            day.removeAttribute('disabled');
            month.removeAttribute('disabled');
            year.removeAttribute('disabled');
        }
    }
    toggledate{$id}();
</script>
ENDSCRIPT;

        return $out;
    }

    /**
     * @param output_status $status
     * @param checklist_item $item (optional)
     * @return string
     */
    protected function edit_link_form(output_status $status, $item = null) {
        $out = '';

        $out .= '<br>';
        $out .= html_writer::tag('label', get_string('linkto', 'mod_checklist')).' ';
        if ($status->is_allowcourselinks()) {
            $selected = $item ? $item->linkcourseid : null;
            $out .= html_writer::select(checklist_class::get_linkable_courses(), 'linkcourseid', $selected,
                                        ['' => get_string('choosecourse', 'mod_checklist')]);
            $out .= ' '.get_string('or', 'mod_checklist').' ';
        }
        $out .= html_writer::label(get_string('url'), 'id_linkurl', true, ['class' => 'accesshide']);
        $attr = [
            'type' => 'text',
            'name' => 'linkurl',
            'id' => 'id_linkurl',
            'size' => 40,
            'value' => $item ? $item->linkurl : '',
            'placeholder' => get_string('enterurl', 'mod_checklist'),
        ];
        $out .= html_writer::empty_tag('input', $attr);

        return $out;
    }

    /**
     * Form to select the grouping for the current item
     *
     * @param output_status $status
     * @param checklist_item $item (optional)
     * @return string
     */
    protected function edit_grouping_form(output_status $status, $item = null) {
        $out = '';

        $out .= '<br>';
        $out .= html_writer::label(get_string('grouping', 'mod_checklist'), 'id_grouping').' ';
        $selected = $item ? $item->grouping : null;
        $groupings = checklist_class::get_course_groupings($status->get_courseid());
        $out .= html_writer::select($groupings, 'grouping', $selected, [0 => get_string('anygrouping', 'mod_checklist')],
                                    ['id' => 'id_grouping']);

        return $out;
    }

    /**
     * @param output_status $status
     * @param moodle_url $thispageurl
     * @param int $currindent
     * @param int $position (optional)
     * @return string
     */
    protected function add_item_form(output_status $status, moodle_url $thispageurl, $currindent, $position = null) {
        $out = '';
        $addingatend = ($position === null);

        $out .= '<li class="checklist-edititem">';
        if ($addingatend) {
            $out .= '<form action="'.$thispageurl->out_omit_querystring().'" method="post">';
            $out .= html_writer::input_hidden_params($thispageurl);
        }

        if ($addingatend) {
            $out .= '<input type="hidden" name="action" value="additem" />';
        } else {
            $out .= '<input type="hidden" name="position" value="'.$position.'" />';
        }
        $out .= '<input type="hidden" name="indent" value="'.$currindent.'" />';
        $out .= $this->output->pix_icon('tick_box', '', 'mod_checklist');
        $out .= '<input type="text" size="'.CHECKLIST_TEXT_INPUT_WIDTH.'" name="displaytext" value="" id="additembox" />';
        $out .= '<input type="submit" name="additem" value="'.get_string('additem', 'checklist').'" />';
        if (!$addingatend) {
            $out .= '<input type="submit" name="canceledititem" value="'.get_string('canceledititem', 'checklist').'" />';
        }
        if ($status->is_editlinks()) {
            $out .= $this->edit_link_form($status);
        }
        if ($status->is_editdates()) {
            $out .= $this->edit_date_form();
        }
        if ($status->is_editgrouping()) {
            $out .= $this->edit_grouping_form($status);
        }

        if ($addingatend) {
            $out .= '</form>';
        }
        $out .= '</li>';

        return $out;
    }

    /**
     * @param output_status $status
     * @param checklist_item $item
     * @return string
     */
    protected function edit_item_form(output_status $status, checklist_item $item) {
        $out = '';

        $out .= '<input type="text" size="'.CHECKLIST_TEXT_INPUT_WIDTH.'" name="displaytext" value="'.
            s($item->displaytext).'" id="updateitembox" />';
        $out .= '<input type="submit" name="updateitem" value="'.get_string('updateitem', 'checklist').'" />';
        $out .= '<input type="submit" name="canceledititem" value="'.get_string('canceledititem', 'checklist').'" />';
        if ($status->is_editlinks()) {
            $out .= $this->edit_link_form($status, $item);
        }
        if ($status->is_editdates()) {
            $out .= $this->edit_date_form($item->duetime);
        }
        if ($status->is_editgrouping()) {
            $out .= $this->edit_grouping_form($status, $item);
        }

        return $out;
    }

    public function item_grouping($item) {
        $out = '';
        if ($item->groupingname) {
            $out .= ' ';
            $out .= html_writer::span("({$item->groupingname})", 'checklist-groupingname');
            $out .= ' ';
        }
        return $out;
    }
}