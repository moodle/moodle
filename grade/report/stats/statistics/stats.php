<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
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
 *File that defines the abstract stats class.
 * @package gradebook
 */
 
 /**
 * Abstract class stats to be extended by classes that will be
 * defining new statistics for the report/stats plugin.
 */
abstract class stats {
    /**
     * The name of this statistic.
     * @var string $name
     */
    public $name;
    
    /**
     * The display type to use when outputing this result of this
     * statistic. If null the defualt item display type will be used.
     * @var int $displaytype
     */
    public $displaytype = null;
    
    /**
     * The nummber of decimals to use when displaying the result
     * of this statistic. If null the defualt item decimals are used.
     */
    public $decimals = null;
    
    public $capability = null;

    /**
     * Constructor for stats.
     * @param string $name name of the statistic.
     * @param int $displaytype
     * @param int $decimals
     */
    public function __construct($name = null, $displaytype = null, $decimals=null) {
        $this->name = $name;        
        $this->displaytype = $displaytype;
        $this->decimals = $decimals;

        if($name == null) {
            $this->name = get_string('statistic', 'gradereport_stats');
        }
    }

    /**
     * Abstract method that is called to make the statistic and
     * do all the processing for it.
     * @param array $final_grades ordered array of final grades.
     * @iparam object $item the gradeable item for witch the grades are a part of.
     * @returns an array or floating point value of the statistic.
     */
    abstract public function report_data($final_grades, $item=null);
}

?>