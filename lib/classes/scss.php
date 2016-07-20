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
 * Moodle implementation of SCSS.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// TODO MDL-53016 Remove this when the latter is implemented.
require_once($CFG->libdir . '/scssphp/Base/Range.php');
require_once($CFG->libdir . '/scssphp/Block.php');
require_once($CFG->libdir . '/scssphp/Colors.php');
require_once($CFG->libdir . '/scssphp/Compiler.php');
require_once($CFG->libdir . '/scssphp/Compiler/Environment.php');
require_once($CFG->libdir . '/scssphp/Exception/CompilerException.php');
require_once($CFG->libdir . '/scssphp/Exception/ParserException.php');
require_once($CFG->libdir . '/scssphp/Exception/ServerException.php');
require_once($CFG->libdir . '/scssphp/Formatter.php');
require_once($CFG->libdir . '/scssphp/Formatter/Compact.php');
require_once($CFG->libdir . '/scssphp/Formatter/Compressed.php');
require_once($CFG->libdir . '/scssphp/Formatter/Crunched.php');
require_once($CFG->libdir . '/scssphp/Formatter/Debug.php');
require_once($CFG->libdir . '/scssphp/Formatter/Expanded.php');
require_once($CFG->libdir . '/scssphp/Formatter/Nested.php');
require_once($CFG->libdir . '/scssphp/Formatter/OutputBlock.php');
require_once($CFG->libdir . '/scssphp/Node.php');
require_once($CFG->libdir . '/scssphp/Node/Number.php');
require_once($CFG->libdir . '/scssphp/Parser.php');
require_once($CFG->libdir . '/scssphp/Type.php');
require_once($CFG->libdir . '/scssphp/Util.php');
require_once($CFG->libdir . '/scssphp/Version.php');
require_once($CFG->libdir . '/scssphp/Server.php');

/**
 * Moodle SCSS compiler class.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_scss extends \Leafo\ScssPhp\Compiler {

    /** @var string The path to the SCSS file. */
    protected $scssfile;
    /** @var array Bits of SCSS content. */
    protected $scsscontent = array();

    /**
     * Add variables.
     *
     * @param array $scss Associative array of variables and their values.
     * @return void
     */
    public function add_variables(array $variables) {
        $this->setVariables($variables);
    }

    /**
     * Append raw SCSS to what's to compile.
     *
     * @param string $scss SCSS code.
     * @return void
     */
    public function append_raw_scss($scss) {
        $this->scsscontent[] = $scss;
    }

    /**
     * Set the file to compile from.
     *
     * The purpose of this method is to provide a way to import the
     * content of a file without messing with the import directories.
     *
     * @param string $filepath The path to the file.
     * @return void
     */
    public function set_file($filepath) {
        $this->scssfile = $filepath;
        $this->setImportPaths([dirname($filepath)]);
    }

    /**
     * Compiles to CSS.
     *
     * @return string
     */
    public function to_css() {
        $content = '';
        if (!empty($this->scssfile)) {
            $content .= file_get_contents($this->scssfile);
        }
        $content .= implode(';', $this->scsscontent);
        return $this->compile($content);
    }

}
