<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Web service documentation renderer.
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @author    Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class core_webservice_renderer extends plugin_renderer_base {
     /**
     * Return documentation for a ws description object
     * ws description object can be 'external_multiple_structure', 'external_single_structure' or 'external_value'
     * Example of documentation for moodle_group_create_groups function:
       list of (
       object {
       courseid int //id of course
       name string //multilang compatible name, course unique
       description string //group description text
       enrolmentkey string //group enrol secret phrase
       }
       )
     * @param object $params a part of parameter/return description
     * @return string the html to display
     */
    public function detailed_description_html($params) {
    /// retrieve the description of the description object
        $paramdesc = "";
        if (!empty($params->desc)) {
            $paramdesc .= html_writer::start_tag('span', array('style' => "color:#2A33A6"));
            if ($params->required == VALUE_REQUIRED) {
                $required = '';
            }
            if ($params->required == VALUE_DEFAULT) {
                if (empty($params->default)) {
                    $params->default = "null";
                }
                $required = html_writer::start_tag('b', array()).get_string('default', 'webservice', $params->default).html_writer::end_tag('b');
            }
            if ($params->required == VALUE_OPTIONAL) {
                $required = html_writer::start_tag('b', array()).get_string('optional', 'webservice').html_writer::end_tag('b');
            }
            $paramdesc .= " ".$required." ";
            $paramdesc .= html_writer::start_tag('i', array());
            $paramdesc .= "//";

            $paramdesc .= $params->desc;

            $paramdesc .= html_writer::end_tag('i');
            
            $paramdesc .= html_writer::end_tag('span');
            $paramdesc .= html_writer::empty_tag('br', array());
        }

    /// description object is a list
        if ($params instanceof external_multiple_structure) {
            return $paramdesc . "list of ( " . html_writer::empty_tag('br', array()) . $this->detailed_description_html($params->content) . ")";
        } else if ($params instanceof external_single_structure) {
    /// description object is an object
            $singlestructuredesc = $paramdesc."object {". html_writer::empty_tag('br', array());
            foreach ($params->keys as $attributname => $attribut) {
                $singlestructuredesc .= html_writer::start_tag('b', array());
                $singlestructuredesc .= $attributname;
                $singlestructuredesc .= html_writer::end_tag('b');
                $singlestructuredesc .= " ".$this->detailed_description_html($params->keys[$attributname]);
            }
            $singlestructuredesc .= "} ";
            $singlestructuredesc .= html_writer::empty_tag('br', array());
            return $singlestructuredesc;
        } else {
    /// description object is a primary type (string, integer)
            switch($params->type) {
                case PARAM_BOOL: // 0 or 1 only for now
                case PARAM_INT:
                    $type = 'int';
                    break;
                case PARAM_FLOAT;
                    $type = 'double';
                    break;
                default:
                    $type = 'string';
            }
            return $type." ".$paramdesc;
        }
    }

    /**
     * Return a description object in indented xml format (for REST response)
     * It is indented in order to be displayed into <pre> tag
     * @param object $returndescription
     * @param string $indentation composed by space only
     * @return string the html to diplay
     */
    public function description_in_indented_xml_format($returndescription, $indentation = "") {
        $indentation = $indentation . "    ";
        $brakeline = <<<EOF


EOF;
    /// description object is a list
        if ($returndescription instanceof external_multiple_structure) {
            $return  = $indentation."<MULTIPLE>".$brakeline;
            $return .= $this->description_in_indented_xml_format($returndescription->content, $indentation);
            $return .= $indentation."</MULTIPLE>".$brakeline;
            return $return;
        } else if ($returndescription instanceof external_single_structure) {
    /// description object is an object
            $singlestructuredesc = $indentation."<SINGLE>".$brakeline;
            $keyindentation = $indentation."    ";
            foreach ($returndescription->keys as $attributname => $attribut) {
                $singlestructuredesc .= $keyindentation."<KEY name=\"".$attributname."\">".$brakeline.
                        $this->description_in_indented_xml_format($returndescription->keys[$attributname], $keyindentation).
                        $keyindentation."</KEY>".$brakeline;
            }
            $singlestructuredesc .= $indentation."</SINGLE>".$brakeline;
            return $singlestructuredesc;
        } else {
    /// description object is a primary type (string, integer)
            switch($returndescription->type) {
                case PARAM_BOOL: // 0 or 1 only for now
                case PARAM_INT:
                    $type = 'int';
                    break;
                case PARAM_FLOAT;
                    $type = 'double';
                    break;
                default:
                    $type = 'string';
            }
            return $indentation."<VALUE>".$type."</VALUE>".$brakeline;
        }
    }

     /**
     * Create indented XML-RPC  param description
     * @param object $paramdescription
     * @param string $indentation composed by space only
     * @return string the html to diplay
     */
    public function xmlrpc_param_description_html($paramdescription, $indentation = "") {
        $indentation = $indentation . "    ";
        $brakeline = <<<EOF


EOF;
    /// description object is a list
        if ($paramdescription instanceof external_multiple_structure) {
            $return  = $brakeline.$indentation."Array ";
            $indentation = $indentation . "    ";
            $return .= $brakeline.$indentation."(";
            $return .= $brakeline.$indentation."[0] =>";
            $return .= $this->xmlrpc_param_description_html($paramdescription->content, $indentation);
            $return .= $brakeline.$indentation.")";
            return $return;
        } else if ($paramdescription instanceof external_single_structure) {
    /// description object is an object
            $singlestructuredesc = $brakeline.$indentation."Array ";
            $keyindentation = $indentation."    ";
            $singlestructuredesc  .= $brakeline.$keyindentation."(";
            foreach ($paramdescription->keys as $attributname => $attribut) {
                $singlestructuredesc .= $brakeline.$keyindentation."[".$attributname."] =>".
                        $this->xmlrpc_param_description_html($paramdescription->keys[$attributname], $keyindentation).
                        $keyindentation;
            }
            $singlestructuredesc .= $brakeline.$keyindentation.")";
            return $singlestructuredesc;
        } else {
    /// description object is a primary type (string, integer)
            switch($paramdescription->type) {
                case PARAM_BOOL: // 0 or 1 only for now
                case PARAM_INT:
                    $type = 'int';
                    break;
                case PARAM_FLOAT;
                    $type = 'double';
                    break;
                default:
                    $type = 'string';
            }
            return " ".$type;
        }
    }

    /**
     * Return the html of a colored box with content
     * @param string $title - the title of the box
     * @param string $content - the content to displayed
     * @param string $rgb - the background color of the box
     * @return <type>
     */
    public function colored_box_with_pre_tag($title, $content, $rgb = 'FEEBE5') {
        $coloredbox = html_writer::start_tag('ins', array()); //TODO: this tag removes xhtml strict error but cause warning
        $coloredbox .= html_writer::start_tag('div', array('style' => "border:solid 1px #DEDEDE;background:#".$rgb.";color:#222222;padding:4px;"));
        $coloredbox .= html_writer::start_tag('pre', array());
        $coloredbox .= html_writer::start_tag('b', array());
        $coloredbox .= $title;
        $coloredbox .= html_writer::end_tag('b', array());
        $coloredbox .= html_writer::empty_tag('br', array());
        $coloredbox .= "\n".$content."\n";
        $coloredbox .= html_writer::end_tag('pre', array());
        $coloredbox .= html_writer::end_tag('div', array());
        $coloredbox .= html_writer::end_tag('ins', array());
        return $coloredbox;
    }


     /**
     * Return indented REST param description
     * @param object $paramdescription
     * @param string $indentation composed by space only
     * @return string the html to diplay
     */
    public function rest_param_description_html($paramdescription, $paramstring) {
        $brakeline = <<<EOF


EOF;
    /// description object is a list
        if ($paramdescription instanceof external_multiple_structure) {
            $paramstring = $paramstring.'[0]';
            $return = $this->rest_param_description_html($paramdescription->content, $paramstring);
            return $return;
        } else if ($paramdescription instanceof external_single_structure) {
    /// description object is an object
            $singlestructuredesc = "";
            $initialparamstring = $paramstring;
            foreach ($paramdescription->keys as $attributname => $attribut) {
                $paramstring = $initialparamstring.'['.$attributname.']';
                $singlestructuredesc .= $this->rest_param_description_html($paramdescription->keys[$attributname], $paramstring);
            }
            return $singlestructuredesc;
        } else {
    /// description object is a primary type (string, integer)
            $paramstring = $paramstring.'=';
            switch($paramdescription->type) {
                case PARAM_BOOL: // 0 or 1 only for now
                case PARAM_INT:
                    $type = 'int';
                    break;
                case PARAM_FLOAT;
                    $type = 'double';
                    break;
                default:
                    $type = 'string';
            }
            return $paramstring." ".$type.$brakeline;
        }
    }


    /**
     * This display all the documentation
     * @param array $functions contains all decription objects
     * @param array $authparam keys are either 'username'/'password' or 'token'
     * @param boolean $printableformat true if we want to display the documentation in a printable format
     * @param array $activatedprotocol
     * @return string the html to diplay
     */
    public function documentation_html($functions, $printableformat, $activatedprotocol, $authparams) {
        global $OUTPUT, $CFG;
        $br = html_writer::empty_tag('br', array());
        $brakeline = <<<EOF


EOF;
    /// Some general information
        $documentationhtml = html_writer::start_tag('table', array('style' => "margin-left:auto; margin-right:auto;"));
        $documentationhtml .= html_writer::start_tag('tr', array());
        $documentationhtml .= html_writer::start_tag('td', array());
        $documentationhtml .= get_string('wsdocumentationintro', 'webservice', $authparams['wsusername']);
        $documentationhtml .= $br.$br;
        

    /// Print button
        $form = new html_form();
        $authparams['print'] = true;
        //$parameters = array ('token' => $token, 'wsusername' => $username, 'wspassword' => $password, 'print' => true);
        $url = new moodle_url('/webservice/wsdoc.php', $authparams); // Required
        $documentationhtml .= $OUTPUT->single_button($url, get_string('print','webservice'));
        $documentationhtml .= $br;
        
        
    /// each functions will be displayed into a collapsible region (opened if printableformat = true)
        foreach ($functions as $functionname => $description) {

            if (empty($printableformat)) {
                $documentationhtml .= print_collapsible_region_start('',
                                                                 'aera_'.$functionname,
                                                                 html_writer::start_tag('strong', array()).$functionname.html_writer::end_tag('strong'),
                                                                 false,
                                                                 !$printableformat,
                                                                 true);
            } else {
                $documentationhtml .= html_writer::tag('strong', array(), $functionname);
                $documentationhtml .= $br;
            }

        /// function global description
            $documentationhtml .= $br;
            $documentationhtml .= html_writer::start_tag('div', array('style' => 'border:solid 1px #DEDEDE;background:#E2E0E0;color:#222222;padding:4px;'));
            $documentationhtml .= $description->description;
            $documentationhtml .= html_writer::end_tag('div');
            $documentationhtml .= $br.$br;

        /// function arguments documentation
            $documentationhtml .= html_writer::start_tag('span', array('style' => 'color:#EA33A6'));
            $documentationhtml .= get_string('arguments', 'webservice');
            $documentationhtml .= html_writer::end_tag('span');
            $documentationhtml .= $br;
            foreach ($description->parameters_desc->keys as $paramname => $paramdesc) {
            /// a argument documentation
                $documentationhtml .= html_writer::start_tag('span', array('style' => 'font-size:80%'));
                                
                if ($paramdesc->required == VALUE_REQUIRED) {
                      $required = get_string('required', 'webservice');
                }
                if ($paramdesc->required == VALUE_DEFAULT) {
                    if (empty($paramdesc->default)) {
                        $default = "null";
                    } else {
                        $default = $paramdesc->default;
                    }
                    $required = get_string('default', 'webservice', $default);
                }
                if ($paramdesc->required == VALUE_OPTIONAL) {
                      $required = get_string('optional', 'webservice');
                }
                
                $documentationhtml .= html_writer::start_tag('b', array());
                $documentationhtml .= $paramname;
                $documentationhtml .= html_writer::end_tag('b');
                $documentationhtml .= " (" .$required. ")"; // argument is required or optional ?
                $documentationhtml .= $br;
                $documentationhtml .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$paramdesc->desc; // argument description
                $documentationhtml .= $br.$br;
                ///general structure of the argument
                $documentationhtml .= $this->colored_box_with_pre_tag(get_string('generalstructure', 'webservice'), 
                                                                      $this->detailed_description_html($paramdesc),
                                                                      'FFF1BC');
                ///xml-rpc structure of the argument in PHP format
                if (!empty($activatedprotocol['xmlrpc'])) {
                    $documentationhtml .= $this->colored_box_with_pre_tag(get_string('phpparam', 'webservice'), 
                                                                          htmlentities('['.$paramname.'] =>'.$this->xmlrpc_param_description_html($paramdesc)),
                                                                          'DFEEE7');
                }
                ///POST format for the REST protocol for the argument
                if (!empty($activatedprotocol['rest'])) {
                    $documentationhtml .= $this->colored_box_with_pre_tag(get_string('restparam', 'webservice'), 
                                                                          htmlentities($this->rest_param_description_html($paramdesc,$paramname)),
                                                                          'FEEBE5');
                }
                $documentationhtml .= html_writer::end_tag('span');
            }
            $documentationhtml .= $br.$br;


        /// function response documentation
            $documentationhtml .= html_writer::start_tag('span', array('style' => 'color:#EA33A6'));
            $documentationhtml .= get_string('response', 'webservice');
            $documentationhtml .= html_writer::end_tag('span');
            $documentationhtml .= $br;
            /// function response description
            $documentationhtml .= html_writer::start_tag('span', array('style' => 'font-size:80%'));
            if (!empty($description->returns_desc->desc)) {
                $documentationhtml .= $description->returns_desc->desc;
                $documentationhtml .= $br.$br;
            }
            if (!empty($description->returns_desc)) {
                ///general structure of the response
                $documentationhtml .= $this->colored_box_with_pre_tag(get_string('generalstructure', 'webservice'), 
                                                                      $this->detailed_description_html($description->returns_desc),
                                                                      'FFF1BC');
                ///xml-rpc structure of the response in PHP format
                if (!empty($activatedprotocol['xmlrpc'])) {
                     $documentationhtml .= $this->colored_box_with_pre_tag(get_string('phpresponse', 'webservice'),
                                                                           htmlentities($this->xmlrpc_param_description_html($description->returns_desc)),
                                                                           'DFEEE7');
                }
                ///XML response for the REST protocol
                if (!empty($activatedprotocol['rest'])) {
                    $restresponse  = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".$brakeline."<RESPONSE>".$brakeline;
                    $restresponse .= $this->description_in_indented_xml_format($description->returns_desc);
                    $restresponse .="</RESPONSE>".$brakeline;
                    $documentationhtml .= $this->colored_box_with_pre_tag(get_string('restcode', 'webservice'), 
                                                                          htmlentities($restresponse),
                                                                          'FEEBE5');
                }
            }
            $documentationhtml .= html_writer::end_tag('span');
            $documentationhtml .= $br.$br;

       /// function errors documentation for REST protocol
            if (!empty($activatedprotocol['rest'])) {
                $documentationhtml .= html_writer::start_tag('span', array('style' => 'color:#EA33A6'));
                $documentationhtml .= get_string('errorcodes', 'webservice');
                $documentationhtml .= html_writer::end_tag('span');
                $documentationhtml .= $br.$br;
                $documentationhtml .= html_writer::start_tag('span', array('style' => 'font-size:80%'));
                $errormessage = get_string('invalidparameter', 'debug');
                $restexceptiontext =<<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<EXCEPTION class="invalid_parameter_exception">
    <MESSAGE>{$errormessage}</MESSAGE>
    <DEBUGINFO></DEBUGINFO>
</EXCEPTION>
EOF;
                $documentationhtml .= $this->colored_box_with_pre_tag(get_string('restexception', 'webservice'), 
                                                                      htmlentities($restexceptiontext),
                                                                      'FEEBE5');

            $documentationhtml .= html_writer::end_tag('span');
            }
            $documentationhtml .= $br.$br;
            if (empty($printableformat)) {
                $documentationhtml .= print_collapsible_region_end(true);
            }
        }

     /// close the table and return the documentation
        $documentationhtml .= html_writer::end_tag('td');
        $documentationhtml .= html_writer::end_tag('tr');
        $documentationhtml .= html_writer::end_tag('table');

        return $documentationhtml;

    }

    /**
     * Return the login page html
     * @param string $errormessage - the error message to display
     * @return string the html to diplay
     */
    public function login_page_html($errormessage) {
        global $CFG, $OUTPUT;

        $br = html_writer::empty_tag('br', array());

        $htmlloginpage = html_writer::start_tag('table', array('style' => "margin-left:auto; margin-right:auto;"));
        $htmlloginpage .= html_writer::start_tag('tr', array());
        $htmlloginpage .= html_writer::start_tag('td', array());

//        /// Display detailed error message when can't login
//        $htmlloginpage .= get_string('error','webservice',$errormessage);
//        $htmlloginpage .= html_writer::empty_tag('br', array());
//        $htmlloginpage .= html_writer::empty_tag('br', array());

        //login form - we cannot use moodle form as we don't have sessionkey
        $form = new html_form();
        $form->url = new moodle_url('/webservice/wsdoc.php', array()); // Required
        $form->button = new html_button();
        $form->button->text = get_string('wsdocumentation','webservice'); // Required
        $form->button->disabled = false;
        $form->button->title = get_string('wsdocumentation','webservice');
        $form->method = 'post';

        $contents =get_string('entertoken', 'webservice');
        $contents .=$br.$br;
        $field = new html_field();
        $field->name = 'token';
        $field->style = 'width: 30em;';
        $contents .= $OUTPUT->textfield($field);
        
        $contents .=$br.$br;
        $contents .=get_string('wsdocumentationlogin', 'webservice');
        $contents .=$br.$br;
        $field = new html_field();
        $field->name = 'wsusername';
        $field->value = get_string('wsusername', 'webservice');
        $field->style = 'width: 30em;';
        $contents .= $OUTPUT->textfield($field);
        $contents .= $br.$br;
        $field = new html_field();
        $field->name = 'wspassword';
        $field->value = get_string('wspassword', 'webservice');
        $field->style = 'width: 30em;';
        $contents .= $OUTPUT->textfield($field);
        $contents .=$br.$br;
        
        $htmlloginpage .= $OUTPUT->form($form, $contents);

        $htmlloginpage .= html_writer::end_tag('td');
        $htmlloginpage .= html_writer::end_tag('tr');
        $htmlloginpage .= html_writer::end_tag('table');

        return $htmlloginpage;

    }
}
