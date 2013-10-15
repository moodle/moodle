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
 * Repository instance callback script
 *
 * @since 2.0
 * @package    core
 * @subpackage repository
 * @copyright  2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib/filelib.php');
require_once(dirname(__FILE__).'/lib.php');

require_login();

/// Parameters
$repo_id   = required_param('repo_id', PARAM_INT); // Repository ID

/// Headers to make it not cacheable
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

/// Wait as long as it takes for this script to finish
core_php_time_limit::raise();

/// Get repository instance information
$sql = 'SELECT i.name, i.typeid, r.type, i.contextid FROM {repository} r, {repository_instances} i '.
       'WHERE i.id=? AND i.typeid=r.id';

$repository = $DB->get_record_sql($sql, array($repo_id), '*', MUST_EXIST);

$type = $repository->type;

if (file_exists($CFG->dirroot.'/repository/'.$type.'/lib.php')) {
    require_once($CFG->dirroot.'/repository/'.$type.'/lib.php');
    $classname = 'repository_' . $type;
    $repo = new $classname($repo_id, $repository->contextid, array('type'=>$type));
} else {
    print_error('invalidplugin', 'repository', $type);
}

// post callback
$repo->callback();
// call opener window to refresh repository
// the callback url should be something like this:
// http://xx.moodle.com/repository/repository_callback.php?repo_id=1&sid=xxx
// sid is the attached auth token from external source
// If Moodle is working on HTTPS mode, then we are not allowed to access
// parent window, in this case, we need to alert user to refresh the repository
// manually.
$strhttpsbug = get_string('cannotaccessparentwin', 'repository');
$strrefreshnonjs = get_string('refreshnonjsfilepicker', 'repository');
$js =<<<EOD
<html>
<head>
    <script type="text/javascript">
    if(window.opener){
        window.opener.M.core_filepicker.active_filepicker.list();
        window.close();
    } else {
        alert("{$strhttpsbug }");
    }
    </script>
</head>
<body>
    <noscript>
    {$strrefreshnonjs}
    </noscript>
</body>
</html>
EOD;

die($js);
