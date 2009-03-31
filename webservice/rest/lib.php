<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *         http://moodle.com
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details:
 *
 *         http://www.gnu.org/copyleft/gpl.html
 *
 * @category  Moodle
 * @package   webservice
 * @copyright Copyright (c) 1999 onwards Martin Dougiamas     http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html     GNU GPL License
 */

require_once($CFG->dirroot.'/webservice/lib.php');

/*
 * Rest server class
 */
final class rest_server extends webservice_server {

    public function __construct() {

        $this->set_protocolname("Rest");
        $this->set_protocolid("rest");
    }

    /**
     * Run REST server
     */
    public function run() {
        $enable = $this->get_enable();
        if (empty($enable)) {
            die;
        }

        require_once('locallib.php');
        //retrieve path and function name from the URL
        $rest_arguments = get_file_argument('server.php');
        header ("Content-type: text/xml");
        echo call_moodle_function($rest_arguments);
    }  

}



?>
