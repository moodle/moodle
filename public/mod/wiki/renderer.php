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
 * Moodle Wiki 2.0 Renderer
 *
 * @package   mod_wiki
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class mod_wiki_renderer extends plugin_renderer_base {
    public function page_index() {
        global $CFG;
        $output = '';
        // Checking wiki instance
        if (!$wiki = wiki_get_wiki($this->page->cm->instance)) {
            return false;
        }

        // @TODO: Fix call to wiki_get_subwiki_by_group
        $gid = groups_get_activity_group($this->page->cm);
        $gid = !empty($gid) ? $gid : 0;
        if (!$subwiki = wiki_get_subwiki_by_group($this->page->cm->instance, $gid)) {
            return false;
        }
        $swid = $subwiki->id;
        $pages = wiki_get_page_list($swid);
        $selectoptions = array();
        foreach ($pages as $page) {
            $selectoptions[$page->id] = format_string($page->title, true, array('context' => $this->page->context));
        }
        $label = get_string('pageindex', 'wiki') . ': ';
        $select = new single_select(new moodle_url('/mod/wiki/view.php'), 'pageid', $selectoptions);
        $select->label = $label;
        return $this->output->container($this->output->render($select), 'wiki_index');
    }

    public function search_result($records, $subwiki) {
        global $CFG;
        $table = new html_table();
        $context = context_module::instance($this->page->cm->id);
        $strsearchresults = get_string('searchresult', 'wiki');
        $totalcount = count($records);
        $html = $this->output->heading("$strsearchresults $totalcount", 3);
        foreach ($records as $page) {
            $table->head = array('title' => format_string($page->title) . ' (' . html_writer::link($CFG->wwwroot . '/mod/wiki/view.php?pageid=' . $page->id, get_string('view', 'wiki')) . ')');
            $table->align = array('title' => 'left');
            $table->width = '100%';
            $table->data = array(array(file_rewrite_pluginfile_urls(format_text($page->cachedcontent, FORMAT_HTML), 'pluginfile.php', $context->id, 'mod_wiki', 'attachments', $subwiki->id)));
            $table->colclasses = array('wikisearchresults');
            $html .= html_writer::table($table);
        }
        $html = html_writer::tag('div', $html, array('class'=>'no-overflow'));
        return $this->output->container($html);
    }

    public function diff($pageid, $old, $new, $options = array()) {
        global $CFG;
        if (!empty($options['total'])) {
            $total = $options['total'];
        } else {
            $total = 0;
        }
        $diff1 = format_text($old->diff, FORMAT_HTML, array('overflowdiv'=>true));
        $diff2 = format_text($new->diff, FORMAT_HTML, array('overflowdiv'=>true));
        $strdatetime = get_string('strftimedatetime', 'langconfig');

        $olduser = $old->user;
        $versionlink = new moodle_url('/mod/wiki/viewversion.php', array('pageid' => $pageid, 'versionid' => $old->id));
        $restorelink = new moodle_url('/mod/wiki/restoreversion.php', array('pageid' => $pageid, 'versionid' => $old->id));
        $userlink = new moodle_url('/user/view.php', array('id' => $olduser->id));
        // view version link
        $oldversionview = ' ';
        $oldversionview .= html_writer::link($versionlink->out(false), get_string('view', 'wiki'), array('class' => 'wiki_diffview'));
        $oldversionview .= ' ';
        // restore version link
        $oldversionview .= html_writer::link($restorelink->out(false), get_string('restore', 'wiki'), array('class' => 'wiki_diffview'));

        // userinfo container
        $oldheading = $this->output->container_start('wiki_diffuserleft');
        // username
        $oldheading .= html_writer::link($CFG->wwwroot . '/user/view.php?id=' . $olduser->id, fullname($olduser)) . '&nbsp;';
        // user picture
        $oldheading .= html_writer::link($userlink->out(false), $this->output->user_picture($olduser, array('popup' => true)), array('class' => 'notunderlined'));
        $oldheading .= $this->output->container_end();

        // version number container
        $oldheading .= $this->output->container_start('wiki_diffversion');
        $oldheading .= get_string('version') . ' ' . $old->version . $oldversionview;
        $oldheading .= $this->output->container_end();
        // userdate container
        $oldheading .= $this->output->container_start('wiki_difftime');
        $oldheading .= userdate($old->timecreated, $strdatetime);
        $oldheading .= $this->output->container_end();

        $newuser = $new->user;
        $versionlink = new moodle_url('/mod/wiki/viewversion.php', array('pageid' => $pageid, 'versionid' => $new->id));
        $restorelink = new moodle_url('/mod/wiki/restoreversion.php', array('pageid' => $pageid, 'versionid' => $new->id));
        $userlink = new moodle_url('/user/view.php', array('id' => $newuser->id));

        $newversionview = ' ';
        $newversionview .= html_writer::link($versionlink->out(false), get_string('view', 'wiki'), array('class' => 'wiki_diffview'));
        // new user info
        $newheading = $this->output->container_start('wiki_diffuserright');
        $newheading .= $this->output->user_picture($newuser, array('popup' => true));

        $newheading .= html_writer::link($userlink->out(false), fullname($newuser), array('class' => 'notunderlined'));
        $newheading .= $this->output->container_end();

        // version
        $newheading .= $this->output->container_start('wiki_diffversion');
        $newheading .= get_string('version') . '&nbsp;' . $new->version . $newversionview;
        $newheading .= $this->output->container_end();
        // userdate
        $newheading .= $this->output->container_start('wiki_difftime');
        $newheading .= userdate($new->timecreated, $strdatetime);
        $newheading .= $this->output->container_end();

        $oldheading = html_writer::tag('div', $oldheading, array('class'=>'wiki-diff-heading header clearfix'));
        $newheading = html_writer::tag('div', $newheading, array('class'=>'wiki-diff-heading header clearfix'));

        $output  = '';
        $output .= html_writer::start_tag('div', array('class'=>'wiki-diff-container clearfix'));
        $output .= html_writer::tag('div', $oldheading.$diff1, array('class'=>'wiki-diff-leftside'));
        $output .= html_writer::tag('div', $newheading.$diff2, array('class'=>'wiki-diff-rightside'));
        $output .= html_writer::end_tag('div');

        if (!empty($total)) {
            $output .= '<div class="wiki_diff_paging">';
            $output .= $this->output->container($this->diff_paging_bar(1, $new->version - 1, $old->version, $CFG->wwwroot . '/mod/wiki/diff.php?pageid=' . $pageid . '&amp;comparewith=' . $new->version . '&amp;', 'compare', false, true), 'wiki_diff_oldpaging');
            $output .= $this->output->container($this->diff_paging_bar($old->version + 1, $total, $new->version, $CFG->wwwroot . '/mod/wiki/diff.php?pageid=' . $pageid . '&amp;compare=' . $old->version . '&amp;', 'comparewith', false, true), 'wiki_diff_newpaging');
            $output .= '</div>';
        }

        return $output;
    }

    /**
     * Prints a single paging bar to provide access to other versions
     *
     * @param int $minpage First page to be displayed in the bar
     * @param int $maxpage Last page to be displayed in the bar
     * @param int $page The page you are currently viewing
     * @param mixed $baseurl If this  is a string then it is the url which will be appended with $pagevar, an equals sign and the page number.
     *                          If this is a moodle_url object then the pagevar param will be replaced by the page no, for each page.
     * @param string $pagevar This is the variable name that you use for the page number in your code (ie. 'tablepage', 'blogpage', etc)
     * @param bool $nocurr do not display the current page as a link
     * @param bool $return whether to return an output string or echo now
     * @return bool or string
     */
    public function diff_paging_bar($minpage, $maxpage, $page, $baseurl, $pagevar = 'page', $nocurr = false) {
        $totalcount = $maxpage - $minpage;
        $maxdisplay = 2;
        $output = '';

        if ($totalcount > 0) {
            $output .= '<div class="paging">';
            $output .= get_string('version', 'wiki') . ':';
            if ($page - $minpage > 0) {
                $pagenum = $page - 1;
                if (!is_a($baseurl, 'moodle_url')) {
                    $output .= '&nbsp;(<a class="previous" href="' . $baseurl . $pagevar . '=' . $pagenum . '">' . get_string('previous') . '</a>)&nbsp;';
                } else {
                    $output .= '&nbsp;(<a class="previous" href="' . $baseurl->out(false, array($pagevar => $pagenum)) . '">' . get_string('previous') . '</a>)&nbsp;';
                }
            }

            if ($page - $minpage > 4) {
                $startpage = $page - 3;
                if (!is_a($baseurl, 'moodle_url')) {
                    $output .= '&nbsp;<a href="' . $baseurl . $pagevar . '=' . $minpage . '">' . $minpage . '</a>&nbsp;...';
                } else {
                    $output .= '&nbsp;<a href="' . $baseurl->out(false, array($pagevar => $minpage)) . '">' . $minpage . '</a>&nbsp;...';
                }
            } else {
                $startpage = $minpage;
            }
            $currpage = $startpage;
            $displaycount = 0;
            while ($displaycount < $maxdisplay and $currpage <= $maxpage) {
                if ($page == $currpage && empty($nocurr)) {
                    $output .= '&nbsp;&nbsp;' . $currpage;
                } else {
                    if (!is_a($baseurl, 'moodle_url')) {
                        $output .= '&nbsp;&nbsp;<a href="' . $baseurl . $pagevar . '=' . $currpage . '">' . $currpage . '</a>';
                    } else {
                        $output .= '&nbsp;&nbsp;<a href="' . $baseurl->out(false, array($pagevar => $currpage)) . '">' . $currpage . '</a>';
                    }

                }
                $displaycount++;
                $currpage++;
            }
            if ($currpage < $maxpage) {
                if (!is_a($baseurl, 'moodle_url')) {
                    $output .= '&nbsp;...<a href="' . $baseurl . $pagevar . '=' . $maxpage . '">' . $maxpage . '</a>&nbsp;';
                } else {
                    $output .= '&nbsp;...<a href="' . $baseurl->out(false, array($pagevar => $maxpage)) . '">' . $maxpage . '</a>&nbsp;';
                }
            } else if ($currpage == $maxpage) {
                if (!is_a($baseurl, 'moodle_url')) {
                    $output .= '&nbsp;&nbsp;<a href="' . $baseurl . $pagevar . '=' . $currpage . '">' . $currpage . '</a>';
                } else {
                    $output .= '&nbsp;&nbsp;<a href="' . $baseurl->out(false, array($pagevar => $currpage)) . '">' . $currpage . '</a>';
                }
            }
            $pagenum = $page + 1;
            if ($page != $maxpage) {
                if (!is_a($baseurl, 'moodle_url')) {
                    $output .= '&nbsp;&nbsp;(<a class="next" href="' . $baseurl . $pagevar . '=' . $pagenum . '">' . get_string('next') . '</a>)';
                } else {
                    $output .= '&nbsp;&nbsp;(<a class="next" href="' . $baseurl->out(false, array($pagevar => $pagenum)) . '">' . get_string('next') . '</a>)';
                }
            }
            $output .= '</div>';
        }

        return $output;
    }

    /**
     * @deprecated since Moodle 4.0 MDL-72413. Now handled in PAGE's activity header
     */
    #[\core\attribute\deprecated(null, since: '4.3', mdl: 'MDL-72413', final: true)]
    public function wiki_info() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    public function tabs($page, $tabitems, $options) {
        $tabs = array();
        $context = context_module::instance($this->page->cm->id);

        $pageid = null;
        if (!empty($page)) {
            $pageid = $page->id;
        }

        $selected = $options['activetab'];

        // make specific tab linked even it is active
        if (!empty($options['linkedwhenactive'])) {
            $linked = $options['linkedwhenactive'];
        } else {
            $linked = '';
        }

        if (!empty($options['inactivetabs'])) {
            $inactive = $options['inactivetabs'];
        } else {
            $inactive = array();
        }

        foreach ($tabitems as $tab) {
            if ($tab == 'edit' && !has_capability('mod/wiki:editpage', $context)) {
                continue;
            }
            if ($tab == 'comments' && !has_capability('mod/wiki:viewcomment', $context)) {
                continue;
            }
            if ($tab == 'files' && !has_capability('mod/wiki:viewpage', $context)) {
                continue;
            }
            if (($tab == 'view' || $tab == 'map' || $tab == 'history') && !has_capability('mod/wiki:viewpage', $context)) {
                continue;
            }
            if ($tab == 'admin' && !has_capability('mod/wiki:managewiki', $context)) {
                continue;
            }
            $link = new moodle_url('/mod/wiki/'. $tab. '.php', array('pageid' => $pageid));
            if ($linked == $tab) {
                $tabs[] = new tabobject($tab, $link, get_string($tab, 'wiki'), '', true);
            } else {
                $tabs[] = new tabobject($tab, $link, get_string($tab, 'wiki'));
            }
        }

        return $this->tabtree($tabs, $selected, $inactive);
    }

    public function prettyview_link($page) {
        $html = '';
        $link = new moodle_url('/mod/wiki/prettyview.php', array('pageid' => $page->id));
        $html .= $this->output->container_start('wiki_right');
        $html .= $this->output->action_link($link, get_string('prettyprint', 'wiki'), new popup_action('click', $link), array('class' => 'printicon'));
        $html .= $this->output->container_end();
        return $html;
    }

    public function wiki_print_subwiki_selector($wiki, $subwiki, $page, $pagetype = 'view') {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/user/lib.php');
        $cm = get_coursemodule_from_instance('wiki', $wiki->id);

        switch ($pagetype) {
        case 'files':
            $baseurl = new moodle_url('/mod/wiki/files.php',
                    array('wid' => $wiki->id, 'title' => $page->title, 'pageid' => $page->id));
            break;
        case 'search':
            $search = optional_param('searchstring', null, PARAM_TEXT);
            $searchcontent = optional_param('searchwikicontent', 0, PARAM_INT);
            $baseurl = new moodle_url('/mod/wiki/search.php',
                    array('cmid' => $cm->id, 'courseid' => $cm->course,
                        'searchstring' => $search, 'searchwikicontent' => $searchcontent));
            break;
        case 'view':
        default:
            $baseurl = new moodle_url('/mod/wiki/view.php',
                    array('wid' => $wiki->id, 'title' => $page->title));
            break;
        }

        $context = context_module::instance($cm->id);
        // @TODO: A plenty of duplicated code below this lines.
        // Create private functions.
        switch (groups_get_activity_groupmode($cm)) {
        case NOGROUPS:
            if ($wiki->wikimode == 'collaborative') {
                // No need to print anything
                return;
            } else if ($wiki->wikimode == 'individual') {
                // We have private wikis here

                $view = has_capability('mod/wiki:viewpage', $context);
                $manage = has_capability('mod/wiki:managewiki', $context);

                // Only people with these capabilities can view all wikis
                if ($view && $manage) {
                    // @TODO: Print here a combo that contains all users.
                    $users = get_enrolled_users($context);
                    $options = array();
                    foreach ($users as $user) {
                        $options[$user->id] = fullname($user);
                    }

                    echo $this->output->container_start('wiki_right');
                    $name = 'uid';
                    $selected = $subwiki->userid;
                    echo $this->output->single_select($baseurl, $name, $options, $selected, null, null,
                        array('label' => get_string('user') . ':'));
                    echo $this->output->container_end();
                }
                return;
            } else {
                // error
                return;
            }
        case SEPARATEGROUPS:
            if ($wiki->wikimode == 'collaborative') {
                // We need to print a select to choose a course group

                echo $this->output->container_start('wiki_right');
                groups_print_activity_menu($cm, $baseurl);
                echo $this->output->container_end();
                return;
            } else if ($wiki->wikimode == 'individual') {
                //  @TODO: Print here a combo that contains all users of that subwiki.
                $view = has_capability('mod/wiki:viewpage', $context);
                $manage = has_capability('mod/wiki:managewiki', $context);

                // Only people with these capabilities can view all wikis
                if ($view && $manage) {
                    $users = get_enrolled_users($context);
                    $options = array();
                    foreach ($users as $user) {
                        $groups = groups_get_all_groups($cm->course, $user->id);
                        if (!empty($groups)) {
                            foreach ($groups as $group) {
                                $options[$group->id][$group->name][$group->id . '-' . $user->id] = fullname($user);
                            }
                        } else {
                            $name = get_string('notingroup', 'wiki');
                            $options[0][$name]['0' . '-' . $user->id] = fullname($user);
                        }
                    }
                } else {
                    $group = groups_get_group($subwiki->groupid);
                    if (!$group) {
                        return;
                    }
                    $users = groups_get_members($subwiki->groupid);
                    foreach ($users as $user) {
                        $options[$group->id][$group->name][$group->id . '-' . $user->id] = fullname($user);
                    }
                }
                echo $this->output->container_start('wiki_right');
                $name = 'groupanduser';
                $selected = $subwiki->groupid . '-' . $subwiki->userid;
                echo $this->output->single_select($baseurl, $name, $options, $selected, null, null,
                    array('label' => get_string('user') . ':'));
                echo $this->output->container_end();

                return;

            } else {
                // error
                return;
            }
        CASE VISIBLEGROUPS:
            if ($wiki->wikimode == 'collaborative') {
                // We need to print a select to choose a course group
                // moodle_url will take care of encoding for us

                echo $this->output->container_start('wiki_right');
                groups_print_activity_menu($cm, $baseurl);
                echo $this->output->container_end();
                return;

            } else if ($wiki->wikimode == 'individual') {
                $users = get_enrolled_users($context);
                $options = array();
                foreach ($users as $user) {
                    $groups = groups_get_all_groups($cm->course, $user->id);
                    if (!empty($groups)) {
                        foreach ($groups as $group) {
                            $options[$group->id][$group->name][$group->id . '-' . $user->id] = fullname($user);
                        }
                    } else {
                        $name = get_string('notingroup', 'wiki');
                        $options[0][$name]['0' . '-' . $user->id] = fullname($user);
                    }
                }

                echo $this->output->container_start('wiki_right');
                $name = 'groupanduser';
                $selected = $subwiki->groupid . '-' . $subwiki->userid;
                echo $this->output->single_select($baseurl, $name, $options, $selected, null, null,
                    array('label' => get_string('user') . ':'));
                echo $this->output->container_end();

                return;

            } else {
                // error
                return;
            }
        default:
            // error
            return;

        }

    }

    function menu_map($pageid, $currentselect) {
        if (empty($currentselect)) {
            // The wiki uses digit number to match the options and 5 is the default one.
            $currentselect = 5;
        }
        $options = array('contributions', 'links', 'orphaned', 'pageindex', 'pagelist', 'updatedpages');
        $items = array();
        foreach ($options as $opt) {
            $items[] = get_string($opt, 'wiki');
        }
        $selectoptions = array();
        foreach ($items as $key => $item) {
            $selectoptions[$key + 1] = $item;
        }
        $select = new single_select(new moodle_url('/mod/wiki/map.php', array('pageid' => $pageid)), 'option', $selectoptions, $currentselect, null);
        $select->label = get_string('mapmenu', 'wiki') . ': ';
        return $this->output->container($this->output->render($select), 'midpad');
    }
    public function wiki_files_tree($context, $subwiki) {
        return $this->render(new wiki_files_tree($context, $subwiki));
    }
    public function render_wiki_files_tree(wiki_files_tree $tree) {
        if (empty($tree->dir['subdirs']) && empty($tree->dir['files'])) {
            $html = $this->output->box(get_string('nofilesavailable', 'repository'));
        } else {
            $htmlid = 'wiki_files_tree_'.uniqid();
            $module = array('name'=>'mod_wiki', 'fullpath'=>'/mod/wiki/module.js');
            $this->page->requires->js_init_call('M.mod_wiki.init_tree', array(false, $htmlid), false, $module);
            $html = '<div id="'.$htmlid.'">';
            $html .= $this->htmllize_tree($tree, $tree->dir);
            $html .= '</div>';
        }
        return $html;
    }

    function menu_admin($pageid, $currentselect) {
        $options = array('removepages', 'deleteversions');
        $items = array();
        foreach ($options as $opt) {
            $items[] = get_string($opt, 'wiki');
        }
        $selectoptions = array();
        foreach ($items as $key => $item) {
            $selectoptions[$key + 1] = $item;
        }
        $select = new single_select(new moodle_url('/mod/wiki/admin.php', array('pageid' => $pageid)), 'option', $selectoptions, $currentselect, null);
        $select->label = get_string('adminmenu', 'wiki') . ': ';
        return $this->output->container($this->output->render($select), 'midpad');
    }

    /**
     * Internal function - creates htmls structure suitable for YUI tree.
     */
    protected function htmllize_tree($tree, $dir) {
        global $CFG;
        $yuiconfig = array();
        $yuiconfig['type'] = 'html';

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }
        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $image = $this->output->pix_icon(file_folder_icon(), $subdir['dirname'], 'moodle', array('class'=>'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.s($subdir['dirname']).'</div> '.$this->htmllize_tree($tree, $subdir).'</li>';
        }
        foreach ($dir['files'] as $file) {
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'.$tree->context->id.'/mod_wiki/attachments/' . $tree->subwiki->id . '/'. $file->get_filepath() . $file->get_filename(), true);
            $filename = $file->get_filename();
            $image = $this->output->pix_icon(file_file_icon($file), $filename, 'moodle', array('class'=>'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.html_writer::link($url, $filename).'</div></li>';
        }
        $result .= '</ul>';

        return $result;
    }

    /**
     * Renders the action bar.
     *
     * @param \mod_wiki\output\action_bar $actionbar
     * @return string The HTML output
     */
    public function render_action_bar(\mod_wiki\output\action_bar $actionbar): string {
        $data = $actionbar->export_for_template($this);
        return $this->render_from_template('mod_wiki/action_bar', $data);
    }
}

class wiki_files_tree implements renderable {
    public $context;
    public $dir;
    public $subwiki;
    public function __construct($context, $subwiki) {
        $fs = get_file_storage();
        $this->context = $context;
        $this->subwiki = $subwiki;
        $this->dir = $fs->get_area_tree($context->id, 'mod_wiki', 'attachments', $subwiki->id);
    }
}
