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
 * @package tool
 * @subpackage mergeusers
 * @author Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 Servei de Recursos Educatius (http://www.sre.urv.cat)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
spl_autoload_register(function ($class) {

    $fileName = strtolower($class) . '.php';
    $fileDirname = dirname(__FILE__);
    $dirs = array(
        $fileDirname,
        $fileDirname . '/table',
        $fileDirname . '/local',
        $fileDirname.'/../classes',
    );

    foreach ($dirs as $dir) {
        if (is_file($dir . '/' . $fileName)) {
            require_once $dir . '/' . $fileName;
            if (class_exists($class)) {
                return true;
            }
        }
    }
    return false;
});
