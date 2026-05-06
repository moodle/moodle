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
 * The top page in the admin settings tree.
 *
 * This page is the first file read when generating the settings tree.
 *
 * It is used to create the root categories in the correct order.
 * These must exist before settingpages and externalpages are added to them.
 *
 * @package   core_admin
 * @copyright 2006 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_admin\setting\settingpage\settingpage;
use core_admin\setting\tree\category;
use core_admin\setting\tree\externalpage;
use core\context\system as context_system;

defined('MOODLE_INTERNAL') || die();

$systemcontext = context_system::instance();
$hassiteconfig = has_capability('moodle/site:config', $systemcontext);

$ADMIN->add('root', new externalpage('adminnotifications', new lang_string('notifications'), "$CFG->wwwroot/$CFG->admin/index.php"));

$ADMIN->add('root', new externalpage('registrationmoodleorg', new lang_string('registration', 'admin'),
        new moodle_url("/admin/registration/index.php")));
 // hidden upgrade script
$ADMIN->add('root', new externalpage('upgradesettings', new lang_string('upgradesettings', 'admin'), "$CFG->wwwroot/$CFG->admin/upgradesettings.php", 'moodle/site:config', true));
$userfeedback = new settingpage('userfeedback', new lang_string('feedbacksettings', 'admin'));
$ADMIN->add('root', $userfeedback);

if ($hassiteconfig) {
    $optionalsubsystems = new settingpage('optionalsubsystems', new lang_string('advancedfeatures', 'admin'));
    $ADMIN->add('root', $optionalsubsystems);
}

$ADMIN->add('root', new category('users', new lang_string('users','admin')));
$ADMIN->add('root', new category('courses', new lang_string('courses','admin')));
$ADMIN->add('root', new category('grades', new lang_string('grades')));
$ADMIN->add('root', new category('ai', new lang_string('ai', 'ai')));
$ADMIN->add('root', new category('analytics', new lang_string('analytics', 'analytics')));
$ADMIN->add('root', new category('competencies', new lang_string('competencies', 'core_competency')));
$ADMIN->add('root', new category('badges', new lang_string('badges'), empty($CFG->enablebadges)));
$ADMIN->add('root', new category('h5p', new lang_string('h5p', 'core_h5p')));
$ADMIN->add('root', new category('license', new lang_string('license')));
$ADMIN->add('root', new category('location', new lang_string('location','admin')));
$ADMIN->add('root', new category('login', new lang_string('login', 'admin')));
$ADMIN->add('root', new category('language', new lang_string('language')));
$ADMIN->add('root', new category('messaging', new lang_string('messagingcategory', 'admin')));
$ADMIN->add('root', new category('payment', new lang_string('payments', 'payment')));
$ADMIN->add('root', new category('modules', new lang_string('plugins', 'admin')));
$ADMIN->add('root', new category('security', new lang_string('security','admin')));
$ADMIN->add('root', new category('appearance', new lang_string('appearance','admin')));
$ADMIN->add('root', new category('frontpage', new lang_string('frontpage','admin')));
$ADMIN->add('root', new category('server', new lang_string('server','admin')));
$ADMIN->add('root', new category('mnet', new lang_string('net','mnet'), (isset($CFG->mnet_dispatcher_mode) and $CFG->mnet_dispatcher_mode === 'off')));
$ADMIN->add('root', new category('reports', new lang_string('reports')));
$ADMIN->add('root', new category('development', new lang_string('development', 'admin')));

// hidden unsupported category
$ADMIN->add('root', new category('unsupported', new lang_string('unsupported', 'admin'), true));

// hidden search script
$ADMIN->add('root', new externalpage('search', new lang_string('search', 'admin'), "$CFG->wwwroot/$CFG->admin/search.php", 'moodle/site:configview', true));
