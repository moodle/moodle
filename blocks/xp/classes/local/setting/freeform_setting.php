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
 * Free form admin setting.
 *
 * @package    block_xp
 * @copyright  2023 Branch Up Pty Ltd
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\setting;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * Free form admin setting.
 *
 * @package    block_xp
 * @copyright  2023 Branch Up Pty Ltd
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class freeform_setting extends \admin_setting {

    /** @var bool No save. */
    public $nosave = true;
    /** @var string The content to display as value. */
    private $content;

    /**
     * Constructor.
     *
     * @param string $name Unique name of the setting.
     * @param string|callable $content The content to display.
     */
    public function __construct($name, $content) {
        $this->content = $content;
        parent::__construct($name, '', '', '');
    }

    /**
     * Retrieves the content.
     *
     * @return string
     */
    protected function get_html_content() {
        return is_callable($this->content) ? call_user_func($this->content) : $this->content;
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
     * Returns the HTML.
     *
     * @param string $data The data.
     * @param string $query The search query.
     * @return string
     */
    public function output_html($data, $query = '') {
        return $this->get_html_content() ?? '';
    }

}
