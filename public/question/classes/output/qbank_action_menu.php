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

namespace core_question\output;

use core_question\local\bank\navigation_node_base;
use core_question\local\bank\plugin_features_base;
use moodle_url;
use renderer_base;
use templatable;
use renderable;
use url_select;

/**
 * Rendered HTML elements for tertiary nav for Question bank.
 *
 * Provides a menu of links for question bank tertiary navigation, based on get_navigation_node() implemented by each plugin.
 * Optionally includes and additional action button to display alongside the menu.
 *
 * @package   core_question
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbank_action_menu implements templatable, renderable {
    /** @var moodle_url */
    private $currenturl;

    /** @var ?moodle_url $actionurl URL for additional action button */
    protected ?moodle_url $actionurl = null;

    /** @var ?string $actionlabel Label for additional action button  */
    protected ?string $actionlabel = null;

    /**
     * qbank_actionbar constructor.
     *
     * @param moodle_url $currenturl The current URL.
     */
    public function __construct(moodle_url $currenturl) {
        $this->currenturl = $currenturl;
    }

    /**
     * Set the properties of an additional action button specific to the current page.
     *
     * @param moodle_url $url
     * @param string $label
     * @return void
     */
    public function set_action_button(moodle_url $url, string $label): void {
        $this->actionurl = $url;
        $this->actionlabel = $label;
    }

    /**
     * Provides the data for the template.
     *
     * @param renderer_base $output renderer_base object.
     * @return array data for the template
     */
    public function export_for_template(renderer_base $output): array {
        $questionslink = new moodle_url('/question/edit.php', $this->currenturl->params());
        $menu = [
            $questionslink->out(false) => get_string('questions', 'question'),
        ];
        $plugins = \core_component::get_plugin_list_with_class('qbank', 'plugin_feature', 'plugin_feature.php');
        foreach ($plugins as $componentname => $pluginfeaturesclass) {
            if (!\core\plugininfo\qbank::is_plugin_enabled($componentname)) {
                continue;
            }
            /** @var plugin_features_base $pluginfeatures */
            $pluginfeatures = new $pluginfeaturesclass();
            $navigationnode = $pluginfeatures->get_navigation_node();
            if (is_null($navigationnode)) {
                continue;
            }
            /** @var moodle_url $url */
            $url = $navigationnode->get_navigation_url();
            $url->params($this->currenturl->params());
            $menu[$url->out(false)] = $navigationnode->get_navigation_title();
        }

        $actionbutton = null;
        if ($this->actionurl) {
            $actionbutton = [
                'url' => $this->actionurl->out(false),
                'label' => $this->actionlabel,
            ];
        }

        $urlselect = new url_select($menu, $this->currenturl->out(false), null, 'questionbankaction');
        $urlselect->set_label(get_string('questionbanknavigation', 'question'), ['class' => 'accesshide']);

        return [
            'questionbankselect' => $urlselect->export_for_template($output),
            'actionbutton' => $actionbutton
        ];
    }
}
