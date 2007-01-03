<?php // $Id$

require_once($CFG->libdir.'/pagelib.php');

define('PAGE_CHAT_VIEW',   'mod-chat-view');

page_map_class(PAGE_CHAT_VIEW, 'page_chat');

$DEFINEDPAGES = array(PAGE_CHAT_VIEW);

/**
 * Class that models the behavior of a chat
 *
 * @author Jon Papaioannou
 * @package pages
 */

class page_chat extends page_generic_activity {

    function init_quick($data) {
        if(empty($data->pageid)) {
            error('Cannot quickly initialize page: empty course id');
        }
        $this->activityname = 'chat';
        parent::init_quick($data);
    }

    function print_header($title, $morebreadcrumbs = NULL) {
        global $USER, $CFG;

        $this->init_full();
        $replacements = array(
            '%fullname%' => format_string($this->activityrecord->name)
        );
        foreach($replacements as $search => $replace) {
            $title = str_replace($search, $replace, $title);
        }

        if($this->courserecord->id == SITEID) {
            $breadcrumbs = array();
        }
        else {
            $breadcrumbs = array($this->courserecord->shortname => $CFG->wwwroot.'/course/view.php?id='.$this->courserecord->id);
        }

        $breadcrumbs[get_string('modulenameplural', 'chat')] = $CFG->wwwroot.'/mod/chat/index.php?id='.$this->courserecord->id;
        $breadcrumbs[format_string($this->activityrecord->name)]            = $CFG->wwwroot.'/mod/chat/view.php?id='.$this->modulerecord->id;

        if(!empty($morebreadcrumbs)) {
            $breadcrumbs = array_merge($breadcrumbs, $morebreadcrumbs);
        }

        $total     = count($breadcrumbs);
        $current   = 1;
        $crumbtext = '';
        foreach($breadcrumbs as $text => $href) {
            if($current++ == $total) {
                $crumbtext .= ' '.$text;
            }
            else {
                $crumbtext .= ' <a href="'.$href.'">'.$text.'</a> ->';
            }
        }

        if(empty($morebreadcrumbs) && $this->user_allowed_editing()) {
            $buttons = '<table><tr><td>'.update_module_button($this->modulerecord->id, $this->courserecord->id, get_string('modulename', 'chat')).'</td>';
            if(!empty($CFG->showblocksonmodpages)) {
            if (empty($CFG->framename) or $CFG->framename=='_top') { 
                $target = '';
            } else {
                $target = ' target="'.$CFG->framename.'"';
            }
               $buttons .= '<td><form'.$target.' method="get" action="view.php">'.
                    '<input type="hidden" name="id" value="'.$this->modulerecord->id.'" />'.
                    '<input type="hidden" name="edit" value="'.($this->user_is_editing()?'off':'on').'" />'.
                    '<input type="submit" value="'.get_string($this->user_is_editing()?'blockseditoff':'blocksediton').'" /></form></td>';
            }
            $buttons .= '</tr></table>';
        }
        else {
            $buttons = '&nbsp;';
        }
        print_header($title, $this->courserecord->fullname, $crumbtext, '', '', true, $buttons, navmenu($this->courserecord, $this->modulerecord));

    }

    function get_type() {
        return PAGE_CHAT_VIEW;
    }
}

?>
