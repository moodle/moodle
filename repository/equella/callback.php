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
 * Callback for equella repository.
 *
 * @since Moodle 2.3
 * @package   repository_equella
 * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
$json = required_param('tlelinks', PARAM_RAW);

require_login();

$decodedinfo = json_decode($json);
$info = array_pop($decodedinfo);

$url = '';
if (isset($info->url)) {
    $url = s(clean_param($info->url, PARAM_URL));
}

$filename = '';
if (isset($info->name)) {
    $filename  = s(clean_param($info->name, PARAM_FILE));
}

$thumbnail = '';
if (isset($info->thumbnail)) {
    $thumbnail = s(clean_param($info->thumbnail, PARAM_URL));
}

$author = '';
if (isset($info->owner)) {
    $author = s(clean_param($info->owner, PARAM_NOTAGS));
}

$license = '';
if (isset($info->license)) {
    $license = s(clean_param($info->license, PARAM_ALPHAEXT));
}

$source = base64_encode(json_encode(array('url'=>$url,'filename'=>$filename)));

$js =<<<EOD
<html>
<head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <script type="text/javascript">
    window.onload = function() {
        var resource = {};
        resource.title = "$filename";
        resource.source = "$source";
        resource.thumbnail = '$thumbnail';
        resource.author = "$author";
        resource.license = "$license";
        parent.M.core_filepicker.select_file(resource);
    }
    </script>
</head>
<body><noscript></noscript></body>
</html>
EOD;

header('Content-Type: text/html; charset=utf-8');
die($js);
