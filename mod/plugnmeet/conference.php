<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Part of mod_plugnmeet.
 *
 * @package     mod_plugnmeet
 * @author     Jibon L. Costa <jibon@mynaparrot.com>
 * @copyright  2022 MynaParrot
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
global $CFG, $DB;

$id = optional_param('id', 0, PARAM_INT);
$cm = get_coursemodule_from_id('plugnmeet', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$moduleinstance = $DB->get_record('plugnmeet', array('id' => $cm->instance), '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/plugnmeet:view', $context);

$event = \mod_plugnmeet\event\joined_plugnmeet_session::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $context
));
$event->add_record_snapshot('plugnmeet', $moduleinstance);
$event->trigger();

$config = get_config('mod_plugnmeet');
if ($config->client_load === "1") {
    if (!class_exists("plugNmeetConnect")) {
        require($CFG->dirroot . '/mod/plugnmeet/helpers/plugNmeetConnect.php');
    }
    $connect = new plugNmeetConnect($config);
    $files = $connect->getClientFiles();
    $jsfiles = $files->getJSFiles() ?? [];
    $cssfiles = $files->getCSSFiles() ?? [];
    $path = $config->plugnmeet_server_url . "/assets";
} else {
    $clientpath = $CFG->dirroot . "/mod/plugnmeet/pix/client/dist/assets";
    $jsfiles = preg_grep('~\.(js)$~', scandir($clientpath . "/js", SCANDIR_SORT_DESCENDING));
    $cssfiles = preg_grep('~\.(css)$~', scandir($clientpath . "/css", SCANDIR_SORT_DESCENDING));
    $path = $CFG->wwwroot . "/mod/plugnmeet/pix/client/dist/assets";
}

$jstag = "";
foreach ($jsfiles as $file) {
    $jstag .= '<script src="' . $path . '/js/' . $file . '" defer="defer"></script>' . "\n\t";
}

$csstag = "";
foreach ($cssfiles as $file) {
    $csstag .= '<link href="' . $path . '/css/' . $file . '" rel="stylesheet" />' . "\n\t";
}
$script = get_plugnmeet_config();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title><?php echo format_string($moduleinstance->name); ?></title>
    <?php echo $csstag . $jstag . $script; ?>
</head>
<body>
<div id="plugNmeet-app"></div>
</body>
</html>
