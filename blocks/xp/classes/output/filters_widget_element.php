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
 * Filters widget group renderable.
 *
 * @package    block_xp
 * @copyright  2019 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\output;

use help_icon;
use renderable;

/**
 * Filters widget group renderable.
 *
 * @package    block_xp
 * @copyright  2019 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filters_widget_element implements renderable {

    /** @var string The description. */
    public $description;
    /** @var help_icon The help icon. */
    public $helpicon;
    /** @var string The title. */
    public $title;
    /** @var renderable The widget. */
    public $widget;

    /**
     * Constructor.
     *
     * @param renderable $widget The widget, or any renderable really.
     * @param string $title The element's title.
     * @param string $description The description.
     * @param help_icon $helpicon The help.
     */
    public function __construct(renderable $widget, $title = null, $description = null, help_icon $helpicon = null) {
        $this->widget = $widget;
        $this->title = $title;
        $this->description = $description;
        $this->helpicon = $helpicon;
    }

}
