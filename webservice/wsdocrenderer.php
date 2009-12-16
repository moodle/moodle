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

class core_wsdoc_renderer extends renderer_base {

    /**
     * Create documentation for a description object
     * @param object $params a part of parameter/return description
     * @return string the html to display
     */
    public function detailed_description_html($params) {
        $paramdesc = "";
        if (!empty($params->desc)) {
            $paramdesc = "<span style=\"color:#2A33A6\"><i>//".$params->desc."</i></span><br/>";
        }
        if ($params instanceof external_multiple_structure) {

            return $paramdesc."list of ( <br/>". $this->detailed_description_html($params->content).")";
        } else if ($params instanceof external_single_structure) {
            //var_dump($params->keys);
            $singlestructuredesc = $paramdesc."object {<br/>";
            foreach ($params->keys as $attributname => $attribut) {
                $singlestructuredesc .= "<b>".$attributname."</b> ".$this->detailed_description_html($params->keys[$attributname]);
            }
            $singlestructuredesc .= "} <br/>";
            return $singlestructuredesc;
        } else {
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
     * Create description in indented xml format
     * It is indented in order to be displayed into <pre> tag
     * @param object $returndescription
     * @param string $indentation composed by space only
     * @return string the html to diplay
     */
    public function description_in_indented_xml_format($returndescription, $indentation = "") {
        $indentation = $indentation . "    ";
        $brakeline = <<<EOF


EOF;
        if ($returndescription instanceof external_multiple_structure) {
            $return  = $indentation."<MULTIPLE>".$brakeline;
            $return .= $this->description_in_indented_xml_format($returndescription->content, $indentation);
            $return .= $indentation."</MULTIPLE>".$brakeline;
            return $return;
        } else if ($returndescription instanceof external_single_structure) {
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
        if ($paramdescription instanceof external_multiple_structure) {
            $return  = $brakeline.$indentation."Array ";
            $indentation = $indentation . "    ";
            $return .= $brakeline.$indentation."(";
            $return .= $brakeline.$indentation."[0] =>";
            $return .= $this->xmlrpc_param_description_html($paramdescription->content, $indentation);
            $return .= $brakeline.$indentation.")";
            return $return;
        } else if ($paramdescription instanceof external_single_structure) {
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
     * Return the REST response (xml code display in <pre> tag)
     * @param string $functionname
     * @param object $returndescription
     * @return string the html to diplay
     */
    public function rest_response_html($functionname, $returndescription) {
        $brakeline = <<<EOF


EOF;

        $restresponsehtml = "";

        $restresponsehtml .= "<pre>";
        $restresponsehtml .= "<div style=\"border:solid 1px #DEDEDE;background:#FEEBE5;color:#222222;padding:4px;\">";
        $restresponsehtml .= '<b>'.get_string('restcode', 'webservice').'</b><br/>';
        $brakeline = <<<EOF


EOF;
        $content  = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".$brakeline."<RESPONSE>".$brakeline;
        $content .= $this->description_in_indented_xml_format($returndescription);
        $content .="</RESPONSE>".$brakeline;
        $restresponsehtml .= $brakeline.htmlentities($content).$brakeline;
        $restresponsehtml .= "</div>";
        $restresponsehtml .= "</pre>";
        return $restresponsehtml;
    }


     /**
     * Create indented REST param description
     * @param object $paramdescription
     * @param string $indentation composed by space only
     * @return string the html to diplay
     */
    public function rest_param_description_html($paramdescription, $paramstring) {
        $brakeline = <<<EOF


EOF;
        if ($paramdescription instanceof external_multiple_structure) {
            $paramstring = $paramstring.'[0]';
            $return = $this->rest_param_description_html($paramdescription->content, $paramstring);
            return $return;
        } else if ($paramdescription instanceof external_single_structure) {
            $singlestructuredesc = "";
            $initialparamstring = $paramstring;
            foreach ($paramdescription->keys as $attributname => $attribut) {
                $paramstring = $initialparamstring.'['.$attributname.']';
                $singlestructuredesc .= $this->rest_param_description_html($paramdescription->keys[$attributname], $paramstring);
            }
            return $singlestructuredesc;
        } else {
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
     * @param string $username
     * @return string the html to diplay
     */
    public function documentation_html($functions, $username) {

        $brakeline = <<<EOF


EOF;

        $documentationhtml = "";

        $documentationhtml .= "<table style=\"margin-left:auto; margin-right:auto;\"><tr><td>";
        $documentationhtml .= get_string('wsdocumentationintro', 'webservice', $username);
        $documentationhtml .= "<br/><br/><br/>";

        foreach ($functions as $functionname => $description) {
            $documentationhtml .= print_collapsible_region_start('', 'aera_'.$functionname,"<strong>".$functionname."</strong>",false,true,true);

            $documentationhtml .= "<br/>";
            $documentationhtml .= "<div style=\"border:solid 1px #DEDEDE;background:#E2E0E0;color:#222222;padding:4px;\">";
            $documentationhtml .= $description->description;
            $documentationhtml .= "</div>";
            $documentationhtml .= "<br/><br/>";

            $documentationhtml .= "<span style=\"color:#EA33A6\">Authentication</span><br/>";
            $documentationhtml .= "<span style=\"font-size:80%\">";
            $documentationhtml .= get_string('requireauthentication', 'webservice'/*,$description->type*/);
            $documentationhtml .= "</span>";
            $documentationhtml .= "<br/><br/>";

            $documentationhtml .= "<span style=\"color:#EA33A6\">".get_string('arguments', 'webservice')."</span><br/>";
            foreach ($description->parameters_desc->keys as $paramname => $paramdesc) {
                $documentationhtml .= "<span style=\"font-size:80%\">";
                $required = $paramdesc->required?get_string('required', 'webservice'):get_string('optional', 'webservice');
                $documentationhtml .= "<b>".$paramname . "</b> (" .$required. ")<br/>";
                $documentationhtml .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$paramdesc->desc." <br/><br/>";
                $documentationhtml .= "<div style=\"border:solid 1px #DEDEDE;background:#FFF1BC;color:#222222;padding:4px;\">";
                $documentationhtml .= "<pre>";
                $documentationhtml .= print_collapsible_region_start('', 'aera_'.$functionname."_".$paramname,'<b>'.get_string('xmlrpcstructure', 'webservice').'</b>',false,true,true);
                //echo '<b>'.get_string('xmlrpcstructure', 'webservice').'</b><br/>';
                $documentationhtml .= $brakeline.$this->detailed_description_html($paramdesc);
                $documentationhtml .= print_collapsible_region_end(true);
                $documentationhtml .= "</pre>";
                $documentationhtml .= "</div><br/>";
                $documentationhtml .= "<pre>";
                $documentationhtml .= "<div style=\"border:solid 1px #DEDEDE;background:#DFEEE7;color:#222222;padding:4px;\">";
                $documentationhtml .= '<b>'.get_string('phpparam', 'webservice').'</b><br/>';
                $documentationhtml .= $brakeline.'['.$paramname.'] =>'.htmlentities($this->xmlrpc_param_description_html($paramdesc)). $brakeline. $brakeline;
                $documentationhtml .= "</div><br/>";
                $documentationhtml .= "</pre>";
                 $documentationhtml .= "<pre>";
                $documentationhtml .= "<div style=\"border:solid 1px #DEDEDE;background:#FEEBE5;color:#222222;padding:4px;\">";
                $documentationhtml .= '<b>'.get_string('restparam', 'webservice').'</b><br/>';
                $documentationhtml .= $brakeline.htmlentities($this->rest_param_description_html($paramdesc,$paramname)). $brakeline. $brakeline;
                $documentationhtml .= "</div>";
                $documentationhtml .= "</pre>";
                $documentationhtml .= "</span>";
            }
            $documentationhtml .= "<br/><br/>";

            $documentationhtml .= "<span style=\"color:#EA33A6\">".get_string('response', 'webservice')."</span><br/>";
            $documentationhtml .= "<span style=\"font-size:80%\">";
            if (!empty($description->returns_desc->desc)) {
                $documentationhtml .= $description->returns_desc->desc."<br/><br/>";
            }

            if (!empty($description->returns_desc)) {
                $documentationhtml .= "<div style=\"border:solid 1px #DEDEDE;background:#FFF1BC;color:#222222;padding:4px;\">";
                $documentationhtml .= "<pre>";
                $documentationhtml .= print_collapsible_region_start('', 'aera_'.$functionname."_xmlrpc_return",'<b>'.get_string('xmlrpcstructure', 'webservice').'</b>',false,true,true);
                //echo '<b>'.get_string('xmlrpcstructure', 'webservice').'</b><br/>';
                $documentationhtml .= $brakeline.$this->detailed_description_html($description->returns_desc);
                $documentationhtml .= print_collapsible_region_end(true);
                $documentationhtml .= "</pre>";
                $documentationhtml .= "</div><br/>";
                $documentationhtml .= "<pre>";
                $documentationhtml .= "<div style=\"border:solid 1px #DEDEDE;background:#DFEEE7;color:#222222;padding:4px;\">";
                $documentationhtml .= '<b>'.get_string('phpresponse', 'webservice').'</b><br/>';
                $documentationhtml .= htmlentities($this->xmlrpc_param_description_html($description->returns_desc)).$brakeline.$brakeline;
                $documentationhtml .= "</div>";
                $documentationhtml .= "</pre><br/>";
                $documentationhtml .= $this->rest_response_html($functionname, $description->returns_desc);
            }
            $documentationhtml .= "</span>";
            $documentationhtml .= "<br/><br/>";



            $documentationhtml .= "<span style=\"color:#EA33A6\">".get_string('errorcodes', 'webservice')."</span><br/>";
            $documentationhtml .= "<span style=\"font-size:80%\">";
            $documentationhtml .= get_string('noerrorcode', 'webservice');
            $documentationhtml .= "</span>";
            $documentationhtml .= "<br/><br/>";


            $documentationhtml .= "<span style=\"color:#EA33A6\">".get_string('apiexplorer', 'webservice')."</span><br/>";
            $documentationhtml .= "<span style=\"font-size:80%\">";
            $documentationhtml .= get_string('apiexplorernotavalaible', 'webservice');
            $documentationhtml .= "</span>";
            $documentationhtml .= "<br/><br/>";

            $documentationhtml .= print_collapsible_region_end(true);
        }

        $documentationhtml .= "</td></tr></table>";
        return $documentationhtml;

    }

    /**
     * Return the login page html
     * @param string $errormessage - the error message to display
     * @return string the html to diplay
     */
    public function login_page_html($errormessage) {
        global $CFG, $OUTPUT;

        $htmlloginpage = "";
        $htmlloginpage .= "<table style=\"margin-left:auto; margin-right:auto;\"><tr><td>";
        $htmlloginpage .= get_string('wsdocumentationlogin', 'webservice');
        $htmlloginpage .= "<br/><br/><br/>";

//        echo get_string('error','webservice',$errormessage);
//        echo "<br/><br/>";

        //login form - we cannot use moodle form as we don't have sessionkey
        $form = new html_form();
        $form->url = new moodle_url($CFG->wwwroot.'/webservice/wsdoc.php', array()); // Required
        $form->button = new html_button();
        $form->button->text = get_string('wsdocumentation','webservice'); // Required
        $form->button->disabled = false;
        $form->button->title = get_string('wsdocumentation','webservice');
        $form->method = 'post';

        $field = new html_field();
        $field->name = 'wsusername';
        $field->value = get_string('wsusername', 'webservice');
        $field->style = 'width: 30em;';
        $contents = $OUTPUT->textfield($field);
        $contents .= "<br/><br/>";
        $field = new html_field();
        $field->name = 'wspassword';
        $field->value = get_string('wspassword', 'webservice');
        $field->style = 'width: 30em;';
        $contents .= $OUTPUT->textfield($field);
        $contents .= "<br/><br/>";

        $htmlloginpage .= $OUTPUT->form($form, $contents);


        $htmlloginpage .= "</td></tr></table>";
        return $htmlloginpage;

    }
}
