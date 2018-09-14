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
 * Ases block
 *
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class block_ases extends block_base 
{
    /**
    * Métod sobreescrito que inicializa el bloque
    *
    */
    public function init() 
    {
        $this->title = get_string('ases', 'block_ases');
        $this->version = 2016071100;
    }

    /**
    * Métod sobreescrito que permite pintar en pantalla
    *
    * @return object $this->content
    */
    public function get_content() 
    {

        global $CFG, $OUTPUT;

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = "";
                
        $url = new moodle_url('/blocks/ases/view/ases_report.php', array(
                              'courseid' => $this->page->course->id,
    						  'instanceid' =>  $this->instance->id
                              ));
                              
//                            print_r($url);
//                            die();

//      $url = "/blocks/talentospilos/view/index.php?courseid=".$this->page->course->id."&instanceid=".$this->instance->id;
                              
//        $url_upload = new moodle_url('/blocks/talentospilos/view/upload_files.php', array(
//                              'courseid' => $this->page->course->id,
//    						  'instanceid' => $this->instance->id
//                              ));
        
        //$this->content->text .= "<button type=\"submit\" class=\"btn-pilos btn-default-pilos\" ><a  href=\"".$CFG->wwwroot.$url->get_path()."?courseid=".$this->page->course->id."&instanceid=".$this->instance->id."\" >Entrar</a></button>";
        $this->content->text .= "<a href=\"".$url."\" >Entrar</a>";
        //$link = new action_link();
        //$link->url = $url;
        //$link->text = 'Entrar'; // Required
        
        //$this->content->text .= $OUTPUT->link($link);
		   
        return $this->content;
    }

    public function instance_allow_config()   
    {
        return true;
    }
    
    function instance_allow_multiple() 
    {
        return false;
    }

    function has_config() 
    {
        return true;
    }
}

 //$nombre = new
// get_content();

