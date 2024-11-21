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
 * Static admin setting.
 *
 * @package    block_xp
 * @copyright  2019 Mootivation Technologies Corp.
 * @copyright  2022 Branch Up Pty Ltd
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\setting;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * Static admin setting.
 *
 * @package    block_xp
 * @copyright  2019 Mootivation Technologies Corp.
 * @copyright  2022 Branch Up Pty Ltd
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class static_setting extends \admin_setting {

    /** @var bool No save. */
    public $nosave = true;
    /** @var string The content to display as value. */
    private $content;

    /**
     * Constructor.
     *
     * @param string $name The setting's name.
     * @param string $label The setting's label.
     * @param string $description The setting's description.
     * @param string $content The setting's content.
     */
    public function __construct($name, $label, $description, $content = '') {
        parent::__construct($name, $label, $description, '');
        $this->content = $content;
    }

    /**
     * Retrieves the content.
     *
     * @return string
     */
    protected function get_html_content() {
        return $this->content;
    }

    /**
     * Retrieves the setting.
     *
     * @return string
     */
    public function get_setting() {
        return false;
    }

    /**
     * Write nothing.
     *
     * @param string $data The data.
     * @return void
     */
    public function write_setting($data) {
    }

    /**
     * Returns the field.
     *
     * @param string $data The data.
     * @param string $query The search query.
     * @return string
     */
    public function output_html($data, $query = '') {
        return format_admin_setting($this, $this->visiblename, $this->get_html_content(), $this->description,
            true, '', null, $query);
    }

}
