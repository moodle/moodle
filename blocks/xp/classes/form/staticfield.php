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

namespace block_xp\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/form/static.php');

/**
 * Form field.
 *
 * Support lazily loading an arbitrary HTML value.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class staticfield extends \MoodleQuickForm_static {

    /** @var object|string|callable The lazy string. */
    protected $content;

    /**
     * Constructor.
     *
     * @param string|null $elementname The name.
     * @param string|null $elementlabel The label.
     * @param object|string|callable|null $content The lazy HTML.
     */
    public function __construct($elementname = null, $elementlabel = null, $content = null) {
        $this->content = $content;
        parent::__construct($elementname, $elementlabel, '');

        $attrs = $this->getAttributes();
        $attrs['class'] = ($attrs['class'] ?? '') . ' mu-w-full';
        $this->setAttributes($attrs);
    }

    public function toHtml() { // @codingStandardsIgnoreLine
        $content = $this->content;
        if (is_callable($content)) {
            $content = $content();
        }
        if (is_object($content)) {
            (string) $content;

        }
        $this->setText((string) ($content ?? ''));
        return parent::toHtml();
    }

    /**
     * Register.
     */
    public static function name(): string {
        \MoodleQuickForm::registerElementType('block_xp_staticfield', __FILE__, staticfield::class); // @codingStandardsIgnoreLine
        return 'block_xp_staticfield';
    }

}

