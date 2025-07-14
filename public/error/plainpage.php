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
 * Moodle Generic plain page
 *
 * This is used for various pages, usually errors, early in the Moodle
 * bootstrap. It can be safetly customized by editing this file directly
 * but it MUST NOT contain any Moodle resources such as theme files generated
 * by php, it can only contain references to static css and images, and as a
 * precaution its recommended that everything is inlined rather than
 * references. This is why this file is located here as it cannot be inside
 * a Moodle theme.
 *
 * @package    core
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:ignoreFile
?>
<!DOCTYPE html>
<html <?php echo $htmllang ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php echo $meta ?>
        <title><?php echo $title ?></title>
        <style>
<?php
// This is a very small modified subset of the bootstrap / boost css classes.
?>
body {
    margin: 0;
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
    font-size: .9375rem;
    font-weight: 400;
    line-height: 1.5;
    color: #343a40;
    text-align: left;
    background-color: #f2f2f2;
}
#page {
    margin-top: 15px;
    background: white;
    max-width: 600px;
    margin: 0 auto;
    padding: 15px;
}
#region-main {
    margin: 0 auto;
    border: 1px solid rgba(0,0,0,.125);
    padding: 1rem 1.25rem 1.25rem;
    background-color: #fff;
}
h1 {
    font-size: 2.34rem;
    margin: 0 0 .5rem;
    font-weight: 300;
    line-height: 1.2;
}
.alert-danger {
    color: #6e211e;
    background-color: #f6d9d8;
    border-color: #f3c9c8;
    padding: .75rem 1.25rem;
}
    </style>
    </head>
    <body>
        <div id="page">
            <div id="region-main">
                <h1><?php echo $title ?></h1>
                <?php echo $content ?>
                <?php echo $footer ?>
            </div>
        </div>
    </body>
</html>

