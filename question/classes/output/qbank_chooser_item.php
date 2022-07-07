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
 * The qbank_chooser_item renderable.
 *
 * @package    core_question
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\output;
defined('MOODLE_INTERNAL') || die();

use lang_string;
use pix_icon;


/**
 * The qbank_chooser_item renderable class.
 *
 * @package    core_question
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbank_chooser_item extends \core\output\chooser_item {

    /**
     * Constructor.
     *
     * @param object $qtype The question type.
     * @param context $context The relevant context.
     */
    public function __construct($qtype, $context) {
        $icon = new pix_icon('icon', $qtype->local_name(), $qtype->plugin_name(), [
            'class' => 'icon',
            'title' => $qtype->local_name()
        ]);
        $help = new lang_string('pluginnamesummary', $qtype->plugin_name());
        parent::__construct($qtype->plugin_name(), $qtype->menu_name(), $qtype->name(), $icon, $help, $context);
    }

}
