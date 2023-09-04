<?php

use mod_quiz\output\edit_renderer as editquiz;
use \mod_quiz\structure;
use \html_writer;

class theme_qubitsbasic_mod_quiz_edit_renderer extends editquiz{ 

    public function add_menu_actions(structure $structure, $page, \moodle_url $pageurl,
    \core_question\local\bank\question_edit_contexts $contexts, array $pagevars) {

        if (!is_siteadmin()){
            return '';
         }

        $actions = $this->edit_menu_actions($structure, $page, $pageurl, $pagevars);
        if (empty($actions)) {
            return '';
        }
        $menu = new \action_menu();
        $menu->set_constraint('.mod-quiz-edit-content');
        $trigger = html_writer::tag('span', get_string('add', 'quiz'), array('class' => 'add-menu'));
        $menu->set_menu_trigger($trigger);
        // The menu appears within an absolutely positioned element causing width problems.
        // Make sure no-wrap is set so that we don't get a squashed menu.
        $menu->set_nowrap_on_items(true);

        // Disable the link if quiz has attempts.
        if (!$structure->can_be_edited()) {
            return $this->render($menu);
        }

        foreach ($actions as $action) {
            if ($action instanceof \action_menu_link) {
                $action->add_class('add-menu');
            }
            $menu->add($action);
        }
        $menu->attributes['class'] .= ' page-add-actions commands';

        // Prioritise the menu ahead of all other actions.
        $menu->prioritise = true;

        return $this->render($menu);

    }

    public function question_remove_icon(structure $structure, $slot, $pageurl) {

        if (!is_siteadmin()){
            return '';
         }

        $url = new \moodle_url($pageurl, array('sesskey' => sesskey(), 'remove' => $slot));
        $strdelete = get_string('delete');

        $image = $this->pix_icon('t/delete', $strdelete);

        return $this->action_link($url, $image, null, array('title' => $strdelete,
                    'class' => 'cm-edit-action editing_delete', 'data-action' => 'delete'));
    }

    protected function selectmultiple_button(structure $structure) {

        if (!is_siteadmin()){
            return '';
         }

        $buttonoptions = array(
            'type'  => 'button',
            'name'  => 'selectmultiple',
            'id'    => 'selectmultiplecommand',
            'value' => get_string('selectmultipleitems', 'quiz'),
            'class' => 'btn btn-secondary'
        );
        if (!$structure->can_be_edited()) {
            $buttonoptions['disabled'] = 'disabled';
        }

        return html_writer::tag('button', get_string('selectmultipleitems', 'quiz'), $buttonoptions);
    }

}
