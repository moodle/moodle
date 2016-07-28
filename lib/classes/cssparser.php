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
 * Moodle implementation of CSS parsing.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// TODO MDL-53016 Remove this when the latter is implemented.
require_once($CFG->libdir . '/php-css-parser/Comment/Commentable.php');
require_once($CFG->libdir . '/php-css-parser/Renderable.php');
require_once($CFG->libdir . '/php-css-parser/Property/AtRule.php');
require_once($CFG->libdir . '/php-css-parser/RuleSet/RuleSet.php');
require_once($CFG->libdir . '/php-css-parser/RuleSet/AtRuleSet.php');
require_once($CFG->libdir . '/php-css-parser/Parsing/SourceException.php');
require_once($CFG->libdir . '/php-css-parser/CSSList/CSSList.php');
require_once($CFG->libdir . '/php-css-parser/CSSList/CSSBlockList.php');
require_once($CFG->libdir . '/php-css-parser/Value/Value.php');
require_once($CFG->libdir . '/php-css-parser/Value/ValueList.php');
require_once($CFG->libdir . '/php-css-parser/Value/CSSFunction.php');
require_once($CFG->libdir . '/php-css-parser/Comment/Comment.php');
require_once($CFG->libdir . '/php-css-parser/Value/PrimitiveValue.php');
require_once($CFG->libdir . '/php-css-parser/CSSList/AtRuleBlockList.php');
require_once($CFG->libdir . '/php-css-parser/CSSList/Document.php');
require_once($CFG->libdir . '/php-css-parser/CSSList/KeyFrame.php');
require_once($CFG->libdir . '/php-css-parser/OutputFormat.php');
require_once($CFG->libdir . '/php-css-parser/Parser.php');
require_once($CFG->libdir . '/php-css-parser/Parsing/OutputException.php');
require_once($CFG->libdir . '/php-css-parser/Parsing/UnexpectedTokenException.php');
require_once($CFG->libdir . '/php-css-parser/Property/Charset.php');
require_once($CFG->libdir . '/php-css-parser/Property/CSSNamespace.php');
require_once($CFG->libdir . '/php-css-parser/Property/Import.php');
require_once($CFG->libdir . '/php-css-parser/Property/Selector.php');
require_once($CFG->libdir . '/php-css-parser/Rule/Rule.php');
require_once($CFG->libdir . '/php-css-parser/RuleSet/DeclarationBlock.php');
require_once($CFG->libdir . '/php-css-parser/Settings.php');
require_once($CFG->libdir . '/php-css-parser/Value/Color.php');
require_once($CFG->libdir . '/php-css-parser/Value/CSSString.php');
require_once($CFG->libdir . '/php-css-parser/Value/RuleValueList.php');
require_once($CFG->libdir . '/php-css-parser/Value/Size.php');
require_once($CFG->libdir . '/php-css-parser/Value/URL.php');

/**
 * Moodle CSS parser.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_cssparser extends \Sabberworm\CSS\Parser {

    /**
     * Constructor.
     *
     * @param string $css The CSS content.
     */
    public function __construct($css) {
        $settings = \Sabberworm\CSS\Settings::create();
        $settings->withLenientParsing();
        parent::__construct($css, $settings);
    }

}
