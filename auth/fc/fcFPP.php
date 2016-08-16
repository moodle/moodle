<?php
/************************************************************************/
/* fcFPP: Php class for FirstClass Flexible Provisining Protocol        */
/* =============================================================        */
/*                                                                      */
/* Copyright (c) 2004 SKERIA Utveckling, Teknous                        */
/* http://skeria.skelleftea.se                                          */
/*                                                                      */
/* Flexible Provisioning Protocol is a real-time, IP based protocol     */
/* which provides direct access to the scriptable remote administration */
/* subsystem of the core FirstClass Server. Using FPP, it is possible to*/
/* implement automated provisioning and administration systems for      */
/* FirstClass, avoiding the need for a point and click GUI. FPP can also*/
/* be used to integrate FirstClass components into a larger unified     */
/* system.                                                              */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License or any */
/* later version.                                                       */
/************************************************************************/
/* Author: Torsten Anderson, torsten.anderson@skeria.skelleftea.se
 */

class fcFPP
{
    var $_hostname;         // hostname of FirstClass server we are connection to
    var $_port;             // port on which fpp is running
    var $_conn = 0;         // socket we are connecting on
    var $_debug = FALSE;    // set to true to see some debug info

    // class constructor
    public function __construct($host="localhost", $port="3333")
    {
    $this->_hostname = $host;
    $this->_port = $port;
    $this->_user = "";
    $this->_pwd = "";
    }

    function fcFPP($host="localhost", $port="3333")
    {
           debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
           self::__construct($host, $port);
    }

    // open a connection to the FirstClass server
    function open()
    {
    if ($this->_debug) echo "Connecting to host ";
    $host = $this->_hostname;
    $port = $this->_port;

    if ($this->_debug) echo "[$host:$port]..";

    // open the connection to the FirstClass server
    $conn = fsockopen($host, $port, $errno, $errstr, 5);
    if (!$conn)
    {
        print_error('auth_fcconnfail','auth_fc', '', array('no'=>$errno, 'str'=>$errstr));
        return false;
    }

    // We are connected
    if ($this->_debug) echo "connected!";

    // Read connection message.
    $line = fgets ($conn);        //+0
    $line = fgets ($conn);        //new line

    // store the connection in this class, so we can use it later
    $this->_conn = & $conn;

    return true;
    }

    // close any open connections
    function close()
    {
    // get the current connection
    $conn = &$this->_conn;

    // close it if it's open
        if ($conn)
    {
        fclose($conn);

        // cleanup the variable
        unset($this->_conn);
        return true;
    }
    return;
    }


    // Authenticate to the FirstClass server
    function login($userid, $passwd)
    {
    // we did have a connection right?!
        if ($this->_conn)
    {
        # Send username
        fputs($this->_conn,"$userid\r\n");

        $line = fgets ($this->_conn);        //new line
        $line = fgets ($this->_conn);        //+0
        $line = fgets ($this->_conn);        //new line

        # Send password
        fputs($this->_conn,"$passwd\r\n");
        $line = fgets ($this->_conn);        //new line
        $line = fgets ($this->_conn);        //+0
        $line = fgets ($this->_conn);        //+0 or message

        if ($this->_debug) echo $line;

        if (preg_match ("/^\+0/", $line)) {      //+0, user with subadmin privileges
            $this->_user = $userid;
            $this->_pwd  = $passwd;
            return TRUE;
        } elseif (strpos($line, 'You are not allowed')) { // Denied access but a valid user and password
                                                         // "Sorry. You are not allowed to login with the FPP interface"
            return TRUE;
        } else {                    //Invalid user or password
            return FALSE;
        }


    }
    return FALSE;
    }

    // Get the list of groups the user is a member of
    function getGroups($userid) {

    $groups = array();

    // we must be logged in as a user with subadmin privileges
    if ($this->_conn AND $this->_user) {
        # Send BA-command to get groups
        fputs($this->_conn,"GET USER '" . $userid . "' 4 -1\r");
        $line = "";
        while (!$line) {
        $line = trim(fgets ($this->_conn));
        }
        $n = 0;
        while ($line AND !preg_match("/^\+0/", $line) AND $line != "-1003") {
        list( , , $groups[$n++]) = explode(" ",$line,3);
        $line = trim(fgets ($this->_conn));
        }
            if ($this->_debug) echo "getGroups:" . implode(",",$groups);
    }

    return $groups;
    }

    // Check if the user is member of any of the groups.
    // Return the list of groups the user is member of.
    function isMemberOf($userid, $groups) {

    $usergroups = array_map("strtolower",$this->getGroups($userid));
    $groups = array_map("strtolower",$groups);

    $result = array_intersect($groups,$usergroups);

        if ($this->_debug) echo "isMemberOf:" . implode(",",$result);

    return $result;

    }

    function getUserInfo($userid, $field) {

    $userinfo = "";

    if ($this->_conn AND $this->_user) {
        # Send BA-command to get data
        fputs($this->_conn,"GET USER '" . $userid . "' " . $field . "\r");
        $line = "";
        while (!$line) {
            $line = trim(fgets ($this->_conn));
        }
        $n = 0;
        while ($line AND !preg_match("/^\+0/", $line)) {
        list( , , $userinfo) = explode(" ",$line,3);
        $line = trim(fgets ($this->_conn));
        }
        if ($this->_debug) echo "getUserInfo:" . $userinfo;
    }

    return str_replace('\r',' ',trim($userinfo,'"'));

    }

    function getResume($userid) {

    $resume = "";

    $pattern = "/\[.+:.+\..+\]/";         // Remove references to pictures in resumes

    if ($this->_conn AND $this->_user) {
        # Send BA-command to get data
        fputs($this->_conn,"GET RESUME '" . $userid . "' 6\r");
        $line = "";
        while (!$line) {
               $line = trim(fgets ($this->_conn));
        }
        $n = 0;
        while ($line AND !preg_match("/^\+0/", $line)) {
            $resume .= preg_replace($pattern,"",str_replace('\r',"\n",trim($line,'6 ')));
        $line = trim(fgets ($this->_conn));
        //print $line;

        }
        if ($this->_debug) echo "getResume:" . $resume;
    }

    return $resume;

    }


}


?>
