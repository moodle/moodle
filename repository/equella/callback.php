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
 * EQUELLA callback
 *
 * @since 2.0
 * @package   repository
 * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');
require_login();
$json = required_param('tlelinks', PARAM_RAW);

$decodedinfo = json_decode($json);
$info = array_pop($decodedinfo);
$url = clean_param($info->url, PARAM_URL);
$thumbnail = clean_param($info->thumbnail, PARAM_URL);
$filename  = clean_param($info->name, PARAM_FILE);

// TODO MDL-32117 EQUELLA callback should provide more information
// $author = clean_param($info->author, PARAM_RAW);
// $timecreated = clean_param($info->timecreated, PARAM_RAW);
// $timemodified = clean_param($info->timemodified, PARAM_RAW);
// NOTE: the license string must match the license names {@link license_manager::install_licenses()}
// We could create a function to map the license names
// $license = clean_param($info->license, PARAM_RAW);
// $filesize = clean_param($info->filesize, PARAM_INT);

$js =<<<EOD
<html>
<head>
    <script type="text/javascript">
    window.onload = function() {
        var resource = {};
        resource.title = "$filename";
        resource.source = "$url";
        resource.thumbnail = '$thumbnail';
        // resource.author = "$author";
        // resource.license = "$license";
        parent.M.core_filepicker.select_file(resource);
    }
    </script>
</head>
<body><noscript></noscript></body>
</html>
EOD;

die($js);
