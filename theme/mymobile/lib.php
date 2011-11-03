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
 * Lib file for mymobile theme
 *
 * @package    theme
 * @subpackage mymobile
 * @copyright  John Stabinger
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mymobile_mobileblocks_renderer extends plugin_renderer_base {

    public function settings_tree(settings_navigation $navigation) {
        global $CFG;
        $content = $this->navigation_node($navigation, array('class' => 'settings'));
        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
           
        }
      
        return $content;
    }

    public function navigation_tree(global_navigation $navigation) {
        global $CFG;
               $content .= $this->navigation_node($navigation, array());
        
        return $content;
    }

    protected function navigation_node(navigation_node $node, $attrs=array()) {
        $items = $node->children;

        // exit if empty, we don't want an empty ul element
        if ($items->count() == 0) {
            return '';
        }

        // array of nested li elements
        $lis = array();
        foreach ($items as $item) {
            if (!$item->display) {
                continue;
            }

            $isbranch = ($item->children->count() > 0 || $item->nodetype == navigation_node::NODETYPE_BRANCH);
            $hasicon = (!$isbranch && $item->icon instanceof renderable);

            if ($isbranch) {
                $item->hideicon = true;
            }
                $item->hideicon = true;
            $content = $this->output->render($item);
            if(substr($item->id, 0, 17)=='expandable_branch' && $item->children->count()==0) {
                // Navigation block does this via AJAX - we'll merge it in directly instead
                $dummypage = new mymobile_dummy_page();
                $dummypage->set_context(get_context_instance(CONTEXT_SYSTEM));
                $subnav = new mymobile_expand_navigation($dummypage, $item->type, $item->key);
                if (!isloggedin() || isguestuser()) {
                    $subnav->set_expansion_limit(navigation_node::TYPE_COURSE);
                }
                    //below by john for too manu items...
                    $subnav->set_expansion_limit(navigation_node::TYPE_COURSE);
                $branch = $subnav->find($item->key, $item->type);
                $content .= $this->navigation_node($branch);
            } else {
                $content .= $this->navigation_node($item);
            }


            if($isbranch && !(is_string($item->action) || empty($item->action))) {
                
                $itest = $item->key;
                $content = html_writer::tag('li', $content, array('data-role' => 'list-divider', 'class' => ''.$itest.'' ));
                             
             
            } 
            
           else if($isbranch) {
           $itest = $item->key;
           $content = html_writer::tag('li', $content, array('data-role' => 'list-divider'));
            
          
            }
            
            else {
                $itest = $item->text;
                $content = html_writer::tag('li', $content, array('class' => ''.$itest.''));

               
            }
            $lis[] = $content;
        }

        if (count($lis)) {
       
           return implode("\n", $lis);
           
        } else {
            return '';
        }
    }

    
}

//user defined columns for tablets or not
function mymobile_initialise_colpos(moodle_page $page) {
    user_preference_allow_ajax_update('theme_mymobile_chosen_colpos', PARAM_ALPHA);
}

function mymobile_get_colpos($default='on') {
    return get_user_preferences('theme_mymobile_chosen_colpos', $default);
}