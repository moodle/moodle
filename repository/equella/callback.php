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

$info = array_pop(json_decode($json));
$reference = clean_param($info->url, PARAM_URL);
$thumbnail = clean_param($info->thumbnail, PARAM_RAW);
$filename  = basename($reference);

$js =<<<EOD
<html>
<head>
    <script type="text/javascript">
    window.onload = function() {
        var resource = {};
        resource.title = "$filename";
        resource.reference = "$reference";
        resource.thumbnail = '$thumbnail';
        parent.M.core_filepicker.select_file(resource);
    }
    </script>
</head>
<body><noscript></noscript></body>
</html>
EOD;

die($js);
