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

defined('MOODLE_INTERNAL') || die();

$plugins = array(
    'turnitintooltwo-dataTables' => array('files' => array('jquery.dataTables.js', 'jquery.dataTables.css')),
    'turnitintooltwo-dataTables_plugins' => array('files' => array('jquery.dataTables.plugins.js')),
    'turnitintooltwo-turnitintooltwo' => array('files' => array('turnitintooltwo-2024100901.min.js')),
    'turnitintooltwo-turnitintooltwo_extra' => array('files' => array('turnitintooltwo_extra-2024100901.min.js')),
    'turnitintooltwo-turnitintooltwo_settings' => array('files' => array('turnitintooltwo_settings-2024100901.min.js')),
    'turnitintooltwo-datatables_columnfilter' => array('files' => array('jquery.dataTables.columnFilter.js')),
    'turnitintooltwo-cookie' => array('files' => array('jquery.cookie.js')),
    'turnitintooltwo-colorbox' => array('files' => array('jquery.colorbox.js', 'colorbox.css')),
    'turnitintooltwo-uieditable' => array('files' => array('jqueryui-editable.js', 'jqueryui-editable.css')),
    'turnitintooltwo-moment' => array('files' => array('moment.js')),
    'turnitintooltwo-tooltipster' => array('files' => array('tooltipster.js', 'tooltipster.css')),
    'turnitintooltwo-migration_tool' => array('files' => array()) // Required as this is called from V1.
);