<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
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

/// This class will produce XSL documentation for the loaded XML file

class generate_documentation extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'backtomainview' => 'xmldb',
            'documentationintro' => 'xmldb'
        ));
    }

    /**
     * Invoke method, every class will have its own
     * returns true/false on completion, setting both
     * errormsg and output as necessary
     */
    function invoke() {
        parent::invoke();

        $result = true;

    /// Set own core attributes
        $this->does_generate = ACTION_GENERATE_HTML;

    /// These are always here
        global $CFG, $XMLDB;

    /// Do the job, setting $result as needed

    /// Get the dir containing the file
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . $dirpath;
        $path = $dirpath.'/install.xml';
        if(!file_exists($path) || !is_readable($path)) {
            return false;
        }

    /// Add link back to home
        $b = ' <p class="centerpara buttons">';
        $b .= '&nbsp;<a href="index.php?action=main_view#lastused">[' . $this->str['backtomainview'] . ']</a>';
        $b .= '</p>';
        $this->output=$b;

        $c = ' <p class="centerpara">';
        $c .= $this->str['documentationintro'];
        $c .= '</p>';
        $this->output.=$c;

        if(class_exists('XSLTProcessor')) {
        /// Transform XML file and display it
            $doc = new DOMDocument();
            $xsl = new XSLTProcessor();

            $doc->load(dirname(__FILE__).'/xmldb.xsl');
            $xsl->importStyleSheet($doc);

            $doc->load($path);
            $this->output.=$xsl->transformToXML($doc);
            $this->output.=$b;
        } else {
            $this->output.=get_string('extensionrequired','xmldb','xsl');
        }

    /// Launch postaction if exists (leave this unmodified)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        return $result;
    }
}

