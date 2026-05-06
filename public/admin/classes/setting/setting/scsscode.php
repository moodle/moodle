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
 * Used to validate the contents of SCSS code and ensuring they are parsable.
 *
 * It does not attempt to detect undefined SCSS variables because it is designed
 * to be used without knowledge of other config/scss included.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 Dan Poltawski <dan@moodle.com>
 */
namespace core_admin\setting\setting;

class scsscode extends \admin_setting_configtextarea {

    /**
     * Validate the contents of the SCSS to ensure its parsable. Does not
     * attempt to detect undefined scss variables.
     *
     * @param string $data The scss code from text field.
     * @return mixed bool true for success or string:error on failure.
     */
    public function validate($data) {
        if (empty($data)) {
            return true;
        }

        $scss = new \core_scss();
        try {
            $scss->compile($data);
        } catch (\ScssPhp\ScssPhp\Exception\ParserException $e) {
            return get_string('scssinvalid', 'admin', $e->getMessage());
        } catch (\ScssPhp\ScssPhp\Exception\CompilerException $e) {
            // Silently ignore this - it could be a scss variable defined from somewhere
            // else which we are not examining here.
            return true;
        }

        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(scsscode::class, \admin_setting_scsscode::class);
