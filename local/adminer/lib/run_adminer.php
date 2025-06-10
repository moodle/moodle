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
 * Wrapper that loads the adminer code and its plugins.
 *
 * @package    local_adminer
 * @author Andreas Grabs <moodle@grabs-edv.de>
 * @copyright  Andreas Grabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_login();
require_capability('local/adminer:useadminer', context_system::instance());

/**
 * Creates an AdminerPlugin object.
 * This object is used by the adminer.php code and defines some configurations and features.
 *
 * @return AdminerPlugin
 */
function adminer_object() {
    // required to run any plugin
    require_once("plugins/plugin.php");

    // autoloader
    foreach (glob("plugins/*.php") as $filename) {
        require_once("./$filename");
    }

    $plugins = array(
        // specify enabled plugins here
        new AdminerFrames(true),
        new AdminerMdlLogin(),
        new AdminerMdlDesigns(),
    );

    return new AdminerPlugin($plugins);
}
// include original Adminer or Adminer Editor
if (\local_adminer\util::check_adminer_secret()) {
    static $adminerlang;
    $currentlang = current_language();
    if (empty($adminerlang) || $adminerlang!= $currentlang) {
        $adminerlang = $currentlang;
        unset($_SESSION['translations']);
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $adminerlang;
    };

    // Prevent loading adminer while running tests.
    if (defined('BEHAT_SITE_RUNNING') || PHPUNIT_TEST) {
        if (optional_param('db', null, PARAM_TEXT)) {
            echo 'Adminer started with database';
        } else {
            echo 'Adminer started without database';
        }
        exit;
    }

    require_once("adminer.php");
}
