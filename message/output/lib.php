<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Base message output class
 *
 * @author Luis Rodrigues
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package
 */

/**
 * Base message output class
 */
abstract class message_output {
    public abstract function send_message($message);
    public abstract function process_form($form, &$preferences);
    public abstract function load_data(&$preferences, $userid);
    public abstract function config_form($preferences);
    /**
     * @return bool have all the necessary config settings been
     * made that allow this plugin to be used.
     */
    public function is_system_configured() {
        return true;
    }
    /**
     * @param  object $user the user object, defaults to $USER.
     * @return bool has the user made all the necessary settings
     * in their profile to allow this plugin to be used.
     */
    public function is_user_configured($user = null) {
        return true;
    }

    /**
     * @return int the Default message output settings for this output, for
     * message providers that do not specify what the settings should be for
     * this output in the messages.php file.
     */
    public function get_default_messaging_settings() {
        return MESSAGE_PERMITTED;
    }
}



