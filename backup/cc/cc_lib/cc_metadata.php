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
* @package    backup-convert
* @subpackage cc-library
* @copyright  2011 Darko Miletic <dmiletic@moodlerooms.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/



 /**
   Defined as specified in CC 1.1
 */
class intended_user_role {
    const LEARNER               = 'Learner';
    const INSTRUCTOR            = 'Instructor';
    const MENTOR                = 'Mentor';
}

class technical_role {
    const AUTHOR                = 'author';
    const PUBLISHER             = 'publisher';
    const UNKNOWN               = 'unknown';
    const INITIATOR             = 'initiator';
    const TERMINATOR            = 'terminator';
    const VALIDATOR             = 'validator';
    const EDITOR                = 'editor';
    const GRAPHICAL_DESIGNER    = 'graphical designer';
    const TECHNICAL_IMPLEMENTER = 'technical implementer';
    const CONTENT_PROVIDER      = 'content provider';
    const TECHNICAL_VALIDATOR   = 'technical validator';
    const EDUCATION_VALIDATOR   = 'educational validator';
    const SCRIPT_WRITER         = 'script writer';
    const INSTRUCTIONAL_DESIGNER= 'instructional designer';
    const SUBJET_MATTER_EXPERT  = 'subject matter expert';
}


class rights_copyright {
    const   YES                  = 'yes';
    const   NO                   = 'no';
}


class rights_cost {
    const   YES                  = 'yes';
    const   NO                   = 'no';
}


// Language identifier (as defined in ISO 639-1, ISO 639-2, and ISO 3166-1)
class language_lom {
    const   US_ENGLISH           = 'en-US';
    const   GB_ENGLISH           = 'en-GB';
    const   AR_SPANISH           = 'es-AR';
    const   GR_GREEK             = 'el-GR';

}



/**
 * Metadata Manifest
 *
 */
class cc_metadata_manifest implements cc_i_metadata_manifest {


    public $arraygeneral   = array();
    public $arraytech      = array();
    public $arrayrights    = array();
    public $arraylifecycle = array();


    public function add_metadata_general($obj){
        if (empty($obj)){
            throw new Exception('Medatada Object given is invalid or null!');
        }
        !is_null($obj->title)? $this->arraygeneral['title']=$obj->title:null;
        !is_null($obj->language)? $this->arraygeneral['language']=$obj->language:null;
        !is_null($obj->description)? $this->arraygeneral['description']=$obj->description:null;
        !is_null($obj->keyword)? $this->arraygeneral['keyword']=$obj->keyword:null;
        !is_null($obj->coverage)? $this->arraygeneral['coverage']=$obj->coverage:null;
        !is_null($obj->catalog)? $this->arraygeneral['catalog']=$obj->catalog:null;
        !is_null($obj->entry)? $this->arraygeneral['entry']=$obj->entry:null;
    }

    public function add_metadata_technical($obj){
        if (empty($obj)){
            throw new Exception('Medatada Object given is invalid or null!');
        }
        !is_null($obj->format)? $this->arraytech['format']=$obj->format:null;
    }


    public function add_metadata_rights($obj){
        if (empty($obj)){
            throw new Exception('Medatada Object given is invalid or null!');
        }
        !is_null($obj->copyright)? $this->arrayrights['copyrightAndOtherRestrictions']=$obj->copyright:null;
        !is_null($obj->description)? $this->arrayrights['description']=$obj->description:null;
        !is_null($obj->cost)? $this->arrayrights['cost']=$obj->cost:null;

    }


    public function add_metadata_lifecycle($obj){
        if (empty($obj)){
            throw new Exception('Medatada Object given is invalid or null!');
        }
        !is_null($obj->role)? $this->arraylifecycle['role']=$obj->role:null;
        !is_null($obj->entity)? $this->arraylifecycle['entity']=$obj->entity:null;
        !is_null($obj->date)? $this->arraylifecycle['date']=$obj->date:null;

    }

}


/**
 * Metadata Lifecycle Type
 *
 */
class cc_metadata_lifecycle{

    public $role             = array();
    public $entity           = array();
    public $date             = array();

    public function set_role($role){
        $this->role[] = array($role);
    }
    public function set_entity($entity){
        $this->entity[] = array($entity);
    }
    public function set_date($date){
        $this->date[] = array($date);
    }


}

/**
 * Metadata Rights Type
 *
 */
class cc_metadata_rights {

    public $copyright        = array();
    public $description      = array();
    public $cost             = array();

    public function set_copyright($copy){
        $this->copyright[] = array($copy);
    }
    public function set_description($des,$language){
        $this->description[] = array($language,$des);
    }
    public function set_cost($cost){
        $this->cost[] = array($cost);
    }

}


/**
 * Metadata Technical Type
 *
 */
class cc_metadata_technical {

    public $format         = array();


    public function set_format($format){
        $this->format[] = array($format);
    }

}


/**
 * Metadata General Type
 *
 */
class cc_metadata_general {

    public $title          = array();
    public $language       = array();
    public $description    = array();
    public $keyword        = array();
    public $coverage       = array();
    public $catalog        = array();
    public $entry          = array();



    public function set_coverage($coverage,$language){
        $this->coverage[] = array($language,$coverage);
    }
    public function set_description($description,$language){
        $this->description[] = array($language,$description);
    }
    public function set_keyword($keyword,$language){
        $this->keyword[] = array($language,$keyword);
    }
    public function set_language($language){
        $this->language[] = array($language);
    }
    public function set_title($title,$language){
        $this->title[] = array($language,$title);
    }
    public function set_catalog($cat){
        $this->catalog[] = array($cat);
    }
    public function set_entry($entry){
        $this->entry[] = array($entry);
    }


}