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
    /** @var array Bits of SCSS content to prepend. */
    protected $scssprepend = array();
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
     * Prepend raw SCSS to what's to compile.
     *
     * @param string $scss SCSS code.
     * @return void
     */
    public function prepend_raw_scss($scss) {
        $this->scssprepend[] = $scss;
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
        $content = implode(';', $this->scssprepend);
        if (!empty($this->scssfile)) {
            $content .= file_get_contents($this->scssfile);
        }
        $content .= implode(';', $this->scsscontent);
        return $this->compile($content);
    }

    /**
     * Compile child; returns a value to halt execution
     *
     * @param array $child
     * @param \Leafo\ScssPhp\Formatter\OutputBlock $out
     *
     * @return array|null
     */
    protected function compileChild($child, \Leafo\ScssPhp\Formatter\OutputBlock $out) {
        switch($child[0]) {
            case \Leafo\ScssPhp\Type::T_SCSSPHP_IMPORT_ONCE:
            case \Leafo\ScssPhp\Type::T_IMPORT:
                list(, $rawpath) = $child;
                $rawpath = $this->reduce($rawpath);
                $path = $this->compileStringContent($rawpath);
                if ($path = $this->findImport($path)) {
                    if ($this->is_valid_file($path)) {
                        return parent::compileChild($child, $out);
                    } else {
                        // Sneaky stuff, don't let non scss file in.
                        debugging("Can't import scss file - " . $path, DEBUG_DEVELOPER);
                    }
                }
                break;
            default:
                return parent::compileChild($child, $out);
        }
    }

    /**
     * Is the given file valid for import ?
     *
     * @param $path
     * @return bool
     */
    protected function is_valid_file($path) {
        global $CFG;

        $realpath = realpath($path);

        // Additional theme directory.
        $addthemedirectory = core_component::get_plugin_types()['theme'];
        $addrealroot = realpath($addthemedirectory);

        // Original theme directory.
        $themedirectory = $CFG->dirroot . "/theme";
        $realroot = realpath($themedirectory);

        // File should end in .scss and must be in sites theme directory, else ignore it.
        $pathvalid = $realpath !== false;
        $pathvalid = $pathvalid && (substr($path, -5) === '.scss');
        $pathvalid = $pathvalid && (strpos($realpath, $realroot) === 0 || strpos($realpath, $addrealroot) === 0);
        return $pathvalid;
    }
}
