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
 * Layout - secure.
 * This layout is baed on a moodle site index.php file but has been adapted to show news items in a different
 * way.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<nav role="navigation" class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <span class="navbar-brand"><?php echo $SITE->shortname; ?></span>
        </div>
        <div id="moodle-navbar">
            <div class="nav-link float-md-right">
                <?php echo $OUTPUT->secure_layout_login_info() ?>
            </div>
        </div>
    </div>
</nav>
<div class="container-langmenu">
    <?php echo $OUTPUT->secure_layout_language_menu() ?>
</div>
<div id="page" class="container">
    <?php
        if (empty($PAGE->layout_options['noactivityheader'])) {
            $header = $PAGE->activityheader;
            $renderer = $PAGE->get_renderer('core');
            $headercontent = $header->export_for_template($renderer);
            echo $OUTPUT->render_from_template('core/activity_header', $headercontent);
        }
    ?>
<div id="page">
<div id="page-content">

<div id="moodle-page" class="clearfix">
    <section id="region-main">
        <?php echo $OUTPUT->main_content(); ?>
    </section>
    <div id="moodle-blocks" class="clearfix">
    <?php echo $OUTPUT->blocks('side-pre'); ?>
    </div>
    <?php echo $OUTPUT->standard_end_of_body_html() ?>
</div>

</div>
</div>
</div>
</body>
</html>
