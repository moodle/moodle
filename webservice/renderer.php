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
 * Web service documentation renderer.
 *
 * @package    core_webservice
 * @category   output
 * @copyright  2009 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_webservice_renderer extends plugin_renderer_base {

    /**
     * Display the authorised user selector
     *
     * @param stdClass $options It contains alloweduserselector, potentialuserselector and serviceid
     * @return string html
     */
    public function admin_authorised_user_selector(&$options) {
        global $CFG;
        $formcontent = html_writer::empty_tag('input',
                        array('name' => 'sesskey', 'value' => sesskey(), 'type' => 'hidden'));

        $table = new html_table();
        $table->size = array('45%', '10%', '45%');
        $table->attributes['class'] = 'roleassigntable generaltable generalbox boxaligncenter';
        $table->summary = '';
        $table->cellspacing = 0;
        $table->cellpadding = 0;

        // LTR/RTL support, for drawing button arrows in the right direction
        if (right_to_left()) {
            $addarrow = '▶';
            $removearrow = '◀';
        } else {
            $addarrow = '◀';
            $removearrow = '▶';
        }

        //create the add and remove button
        $addinput = html_writer::empty_tag('input',
                        array('name' => 'add', 'id' => 'add', 'type' => 'submit',
                            'value' => $addarrow . ' ' . get_string('add'),
                            'title' => get_string('add')));
        $addbutton = html_writer::tag('div', $addinput, array('id' => 'addcontrols'));
        $removeinput = html_writer::empty_tag('input',
                        array('name' => 'remove', 'id' => 'remove', 'type' => 'submit',
                            'value' => $removearrow . ' ' . get_string('remove'),
                            'title' => get_string('remove')));
        $removebutton = html_writer::tag('div', $removeinput, array('id' => 'removecontrols'));


        //create the three cells
        $label = html_writer::tag('label', get_string('serviceusers', 'webservice'),
                        array('for' => 'removeselect'));
        $label = html_writer::tag('p', $label);
        $authoriseduserscell = new html_table_cell($label .
                        $options->alloweduserselector->display(true));
        $authoriseduserscell->id = 'existingcell';
        $buttonscell = new html_table_cell($addbutton . html_writer::empty_tag('br') . $removebutton);
        $buttonscell->id = 'buttonscell';
        $label = html_writer::tag('label', get_string('potusers', 'webservice'),
                        array('for' => 'addselect'));
        $label = html_writer::tag('p', $label);
        $otheruserscell = new html_table_cell($label .
                        $options->potentialuserselector->display(true));
        $otheruserscell->id = 'potentialcell';

        $cells = array($authoriseduserscell, $buttonscell, $otheruserscell);
        $row = new html_table_row($cells);
        $table->data[] = $row;
        $formcontent .= html_writer::table($table);

        $formcontent = html_writer::tag('div', $formcontent);

        $actionurl = new moodle_url('/' . $CFG->admin . '/webservice/service_users.php',
                        array('id' => $options->serviceid));
        $html = html_writer::tag('form', $formcontent,
                        array('id' => 'assignform', 'action' => $actionurl, 'method' => 'post'));
        return $html;
    }

    /**
     * Display list of authorised users for the given external service.
     *
     * @param array $users authorised users
     * @param int $serviceid service id
     * @return string $html
     */
    public function admin_authorised_user_list($users, $serviceid) {
        global $CFG;

        $listitems = [];
        $extrafields = \core_user\fields::get_identity_fields(context_system::instance());

        foreach ($users as $user) {
            $settingsurl = new moodle_url('/admin/webservice/service_user_settings.php',
                ['userid' => $user->id, 'serviceid' => $serviceid]);

            $identity = [];
            foreach ($extrafields as $extrafield) {
                if (isset($user->{$extrafield})) {
                    $identity[] = s($user->{$extrafield});
                }
            }
            $identity = $identity ? html_writer::div(implode(', ', $identity), 'small') : '';

            $link = html_writer::link($settingsurl, fullname($user));

            if (!empty($user->missingcapabilities)) {
                $count = html_writer::span(count($user->missingcapabilities), 'badge badge-danger');
                $links = array_map(function($capname) {
                    return get_capability_docs_link((object)['name' => $capname]) . html_writer::div($capname, 'text-muted');
                }, $user->missingcapabilities);
                $list = html_writer::alist($links);
                $help = $this->output->help_icon('missingcaps', 'webservice');
                $missingcaps = print_collapsible_region(html_writer::div($list . $help, 'missingcaps'), 'small',
                    html_writer::random_id('usermissingcaps'), get_string('usermissingcaps', 'webservice', $count), '', true, true);

            } else {
                $missingcaps = '';
            }

            $listitems[] = $link . $identity . $missingcaps;
        }

        $html = html_writer::div(html_writer::alist($listitems), 'alloweduserlist');

        return $html;
    }

    /**
     * Display a confirmation page to remove a function from a service
     *
     * @param stdClass $function It needs function id + function name properties.
     * @param stdClass $service It needs service id + service name properties.
     * @return string html
     */
    public function admin_remove_service_function_confirmation($function, $service) {
        $optionsyes = array('id' => $service->id, 'action' => 'delete',
            'confirm' => 1, 'sesskey' => sesskey(), 'fid' => $function->id);
        $optionsno = array('id' => $service->id);
        $formcontinue = new single_button(new moodle_url('service_functions.php',
                                $optionsyes), get_string('remove'));
        $formcancel = new single_button(new moodle_url('service_functions.php',
                                $optionsno), get_string('cancel'), 'get');
        return $this->output->confirm(get_string('removefunctionconfirm', 'webservice',
                        (object) array('service' => $service->name, 'function' => $function->name)),
                $formcontinue, $formcancel);
    }

    /**
     * Display a confirmation page to remove a service
     *
     * @param stdClass $service It needs service id + service name properties.
     * @return string html
     */
    public function admin_remove_service_confirmation($service) {
        global $CFG;
        $optionsyes = array('id' => $service->id, 'action' => 'delete',
            'confirm' => 1, 'sesskey' => sesskey());
        $optionsno = array('section' => 'externalservices');
        $formcontinue = new single_button(new moodle_url('service.php', $optionsyes),
                        get_string('delete'), 'post');
        $formcancel = new single_button(
                        new moodle_url($CFG->wwwroot . "/" . $CFG->admin . "/settings.php", $optionsno),
                        get_string('cancel'), 'get');
        return $this->output->confirm(get_string('deleteserviceconfirm', 'webservice', $service->name),
                $formcontinue, $formcancel);
    }

    /**
     * Display a list of functions for a given service
     * If the service is built-in, do not display remove/add operation (read-only)
     *
     * @param array $functions list of functions
     * @param stdClass $service the given service
     * @return string the table html + add operation html
     */
    public function admin_service_function_list($functions, $service) {
        global $CFG;
        if (!empty($functions)) {
            $table = new html_table();
            $table->head = array(get_string('function', 'webservice'),
                get_string('description'), get_string('requiredcaps', 'webservice'));
            $table->align = array('left', 'left', 'left');
            $table->size = array('15%', '40%', '40%');
            $table->width = '100%';
            $table->align[] = 'left';

            //display remove function operation (except for build-in service)
            if (empty($service->component)) {
                $table->head[] = get_string('edit');
                $table->align[] = 'center';
            }

            $anydeprecated = false;
            foreach ($functions as $function) {
                $function = external_api::external_function_info($function);

                if (!empty($function->deprecated)) {
                    $anydeprecated = true;
                }
                $requiredcaps = html_writer::tag('div',
                                empty($function->capabilities) ? '' : $function->capabilities,
                                array('class' => 'functiondesc'));
                ;
                $description = html_writer::tag('div', $function->description,
                                array('class' => 'functiondesc'));
                //display remove function operation (except for build-in service)
                if (empty($service->component)) {
                    $removeurl = new moodle_url('/' . $CFG->admin . '/webservice/service_functions.php',
                                    array('sesskey' => sesskey(), 'fid' => $function->id,
                                        'id' => $service->id,
                                        'action' => 'delete'));
                    $removelink = html_writer::tag('a',
                                    get_string('removefunction', 'webservice'),
                                    array('href' => $removeurl));
                    $table->data[] = array($function->name, $description, $requiredcaps, $removelink);
                } else {
                    $table->data[] = array($function->name, $description, $requiredcaps);
                }
            }

            $html = html_writer::table($table);
        } else {
            $html = get_string('nofunctions', 'webservice') . html_writer::empty_tag('br');
        }

        //display add function operation (except for build-in service)
        if (empty($service->component)) {

            if (!empty($anydeprecated)) {
                debugging('This service uses deprecated functions, replace them by the proposed ones and update your client/s.', DEBUG_DEVELOPER);
            }
            $addurl = new moodle_url('/' . $CFG->admin . '/webservice/service_functions.php',
                            array('sesskey' => sesskey(), 'id' => $service->id, 'action' => 'add'));
            $html .= html_writer::tag('a', get_string('addfunctions', 'webservice'), array('href' => $addurl));
        }

        return $html;
    }

    /**
     * Display Reset token confirmation box
     *
     * @param stdClass $token token to reset
     * @return string html
     */
    public function user_reset_token_confirmation($token) {
        global $CFG;
        $managetokenurl = $CFG->wwwroot . "/user/managetoken.php?sesskey=" . sesskey();
        $optionsyes = array('tokenid' => $token->id, 'action' => 'resetwstoken', 'confirm' => 1,
            'sesskey' => sesskey());
        $optionsno = array('section' => 'webservicetokens', 'sesskey' => sesskey());
        $formcontinue = new single_button(new moodle_url($managetokenurl, $optionsyes),
                        get_string('reset'));
        $formcancel = new single_button(new moodle_url($managetokenurl, $optionsno),
                        get_string('cancel'), 'get');
        $html = $this->output->confirm(get_string('resettokenconfirm', 'webservice',
                                (object) array('user' => $token->firstname . " " .
                                    $token->lastname, 'service' => $token->name)),
                        $formcontinue, $formcancel);
        return $html;
    }

    /**
     * Display user tokens with buttons to reset them
     *
     * @param stdClass $tokens user tokens
     * @param int $userid user id
     * @param bool $documentation if true display a link to the API documentation
     * @return string html code
     */
    public function user_webservice_tokens_box($tokens, $userid, $documentation = false) {
        global $CFG, $DB;

        // display strings
        $stroperation = get_string('operation', 'webservice');
        $strtoken = get_string('key', 'webservice');
        $strservice = get_string('service', 'webservice');
        $strcreator = get_string('tokencreator', 'webservice');
        $strcontext = get_string('context', 'webservice');
        $strvaliduntil = get_string('validuntil', 'webservice');

        $return = $this->output->heading(get_string('securitykeys', 'webservice'), 3, 'main', true);
        $return .= $this->output->box_start('generalbox webservicestokenui');

        $return .= get_string('keyshelp', 'webservice');

        $table = new html_table();
        $table->head = array($strtoken, $strservice, $strvaliduntil, $strcreator, $stroperation);
        $table->align = array('left', 'left', 'left', 'center', 'left', 'center');
        $table->width = '100%';
        $table->data = array();

        if ($documentation) {
            $table->head[] = get_string('doc', 'webservice');
            $table->align[] = 'center';
        }

        if (!empty($tokens)) {
            foreach ($tokens as $token) {

                if ($token->creatorid == $userid) {
                    $reset = "<a href=\"" . $CFG->wwwroot . "/user/managetoken.php?sesskey="
                            . sesskey() . "&amp;action=resetwstoken&amp;tokenid=" . $token->id . "\">";
                    $reset .= get_string('reset') . "</a>";
                    $creator = $token->firstname . " " . $token->lastname;
                } else {
                    //retrieve administrator name
                    $admincreator = $DB->get_record('user', array('id'=>$token->creatorid));
                    $creator = $admincreator->firstname . " " . $admincreator->lastname;
                    $reset = '';
                }

                $userprofilurl = new moodle_url('/user/view.php?id=' . $token->creatorid);
                $creatoratag = html_writer::start_tag('a', array('href' => $userprofilurl));
                $creatoratag .= $creator;
                $creatoratag .= html_writer::end_tag('a');

                $validuntil = '';
                if (!empty($token->validuntil)) {
                    $validuntil = userdate($token->validuntil, get_string('strftimedatetime', 'langconfig'));
                }

                $tokenname = $token->name;
                if (!$token->enabled) { //that is the (1 token-1ws) related ws is not enabled.
                    $tokenname = '<span class="dimmed_text">'.$token->name.'</span>';
                }
                $row = array($token->token, $tokenname, $validuntil, $creatoratag, $reset);

                if ($documentation) {
                    $doclink = new moodle_url('/webservice/wsdoc.php',
                            array('id' => $token->id, 'sesskey' => sesskey()));
                    $row[] = html_writer::tag('a', get_string('doc', 'webservice'),
                            array('href' => $doclink));
                }

                $table->data[] = $row;
            }
            $return .= html_writer::table($table);
        } else {
            $return .= get_string('notoken', 'webservice');
        }

        $return .= $this->output->box_end();
        return $return;
    }

    /**
     * Return documentation for a ws description object
     * ws description object can be 'external_multiple_structure', 'external_single_structure'
     * or 'external_value'
     *
     * Example of documentation for core_group_create_groups function:
     * list of (
     *     object {
     *         courseid int //id of course
     *         name string //multilang compatible name, course unique
     *         description string //group description text
     *         enrolmentkey string //group enrol secret phrase
     *     }
     * )
     *
     * @param stdClass $params a part of parameter/return description
     * @return string the html to display
     */
    public function detailed_description_html($params) {
        // retrieve the description of the description object
        $paramdesc = "";
        if (!empty($params->desc)) {
            $paramdesc .= html_writer::start_tag('span', array('style' => "color:#2A33A6"));
            if ($params->required == VALUE_REQUIRED) {
                $required = '';
            }
            if ($params->required == VALUE_DEFAULT) {
                if ($params->default === null) {
                    $params->default = "null";
                }
                $required = html_writer::start_tag('b', array()) .
                        get_string('default', 'webservice', print_r($params->default, true))
                        . html_writer::end_tag('b');
            }
            if ($params->required == VALUE_OPTIONAL) {
                $required = html_writer::start_tag('b', array()) .
                        get_string('optional', 'webservice') . html_writer::end_tag('b');
            }
            $paramdesc .= " " . $required . " ";
            $paramdesc .= html_writer::start_tag('i', array());
            $paramdesc .= "//";

            $paramdesc .= s($params->desc);

            $paramdesc .= html_writer::end_tag('i');

            $paramdesc .= html_writer::end_tag('span');
            $paramdesc .= html_writer::empty_tag('br', array());
        }

        // description object is a list
        if ($params instanceof external_multiple_structure) {
            return $paramdesc . "list of ( " . html_writer::empty_tag('br', array())
            . $this->detailed_description_html($params->content) . ")";
        } else if ($params instanceof external_single_structure) {
            // description object is an object
            $singlestructuredesc = $paramdesc . "object {" . html_writer::empty_tag('br', array());
            foreach ($params->keys as $attributname => $attribut) {
                $singlestructuredesc .= html_writer::start_tag('b', array());
                $singlestructuredesc .= $attributname;
                $singlestructuredesc .= html_writer::end_tag('b');
                $singlestructuredesc .= " " .
                        $this->detailed_description_html($params->keys[$attributname]);
            }
            $singlestructuredesc .= "} ";
            $singlestructuredesc .= html_writer::empty_tag('br', array());
            return $singlestructuredesc;
        } else {
            // description object is a primary type (string, integer)
            switch ($params->type) {
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
            return $type . " " . $paramdesc;
        }
    }

    /**
     * Return a description object in indented xml format (for REST response)
     * It is indented to be output within <pre> tags
     *
     * @param external_description $returndescription the description structure of the web service function returned value
     * @param string $indentation Indentation in the generated HTML code; should contain only spaces.
     * @return string the html to diplay
     */
    public function description_in_indented_xml_format($returndescription, $indentation = "") {
        $indentation = $indentation . "    ";
        $brakeline = <<<EOF


EOF;
        // description object is a list
        if ($returndescription instanceof external_multiple_structure) {
            $return = $indentation . "<MULTIPLE>" . $brakeline;
            $return .= $this->description_in_indented_xml_format($returndescription->content,
                            $indentation);
            $return .= $indentation . "</MULTIPLE>" . $brakeline;
            return $return;
        } else if ($returndescription instanceof external_single_structure) {
            // description object is an object
            $singlestructuredesc = $indentation . "<SINGLE>" . $brakeline;
            $keyindentation = $indentation . "    ";
            foreach ($returndescription->keys as $attributname => $attribut) {
                $singlestructuredesc .= $keyindentation . "<KEY name=\"" . $attributname . "\">"
                        . $brakeline .
                        $this->description_in_indented_xml_format(
                                $returndescription->keys[$attributname], $keyindentation) .
                        $keyindentation . "</KEY>" . $brakeline;
            }
            $singlestructuredesc .= $indentation . "</SINGLE>" . $brakeline;
            return $singlestructuredesc;
        } else {
            // description object is a primary type (string, integer)
            switch ($returndescription->type) {
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
            return $indentation . "<VALUE>" . $type . "</VALUE>" . $brakeline;
        }
    }

    /**
     * Create indented XML-RPC  param description
     *
     * @param external_description $paramdescription the description structure of the web service function parameters
     * @param string $indentation Indentation in the generated HTML code; should contain only spaces.
     * @return string the html to diplay
     */
    public function xmlrpc_param_description_html($paramdescription, $indentation = "") {
        $indentation = $indentation . "    ";
        $brakeline = <<<EOF


EOF;
        // description object is a list
        if ($paramdescription instanceof external_multiple_structure) {
            $return = $brakeline . $indentation . "Array ";
            $indentation = $indentation . "    ";
            $return .= $brakeline . $indentation . "(";
            $return .= $brakeline . $indentation . "[0] =>";
            $return .= $this->xmlrpc_param_description_html($paramdescription->content, $indentation);
            $return .= $brakeline . $indentation . ")";
            return $return;
        } else if ($paramdescription instanceof external_single_structure) {
            // description object is an object
            $singlestructuredesc = $brakeline . $indentation . "Array ";
            $keyindentation = $indentation . "    ";
            $singlestructuredesc .= $brakeline . $keyindentation . "(";
            foreach ($paramdescription->keys as $attributname => $attribut) {
                $singlestructuredesc .= $brakeline . $keyindentation . "[" . $attributname . "] =>" .
                        $this->xmlrpc_param_description_html(
                                $paramdescription->keys[$attributname], $keyindentation) .
                        $keyindentation;
            }
            $singlestructuredesc .= $brakeline . $keyindentation . ")";
            return $singlestructuredesc;
        } else {
            // description object is a primary type (string, integer)
            switch ($paramdescription->type) {
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
            return " " . $type;
        }
    }

    /**
     * Return the html of a coloured box with content
     *
     * @param string $title - the title of the box
     * @param string $content - the content to displayed
     * @param string $rgb - the background color of the box
     * @return string HTML code
     */
    public function colored_box_with_pre_tag($title, $content, $rgb = 'FEEBE5') {
        //TODO MDL-31192 this tag removes xhtml strict error but cause warning
        $coloredbox = html_writer::start_tag('div', array());
        $coloredbox .= html_writer::start_tag('div',
                        array('style' => "border:solid 1px #DEDEDE;background:#" . $rgb
                            . ";color:#222222;padding:4px;"));
        $coloredbox .= html_writer::start_tag('pre', array());
        $coloredbox .= html_writer::start_tag('b', array());
        $coloredbox .= $title;
        $coloredbox .= html_writer::end_tag('b', array());
        $coloredbox .= html_writer::empty_tag('br', array());
        $coloredbox .= "\n" . $content . "\n";
        $coloredbox .= html_writer::end_tag('pre', array());
        $coloredbox .= html_writer::end_tag('div', array());
        $coloredbox .= html_writer::end_tag('div', array());
        return $coloredbox;
    }

    /**
     * Return indented REST param description
     *
     * @param external_description $paramdescription the description structure of the web service function parameters
     * @param string $paramstring parameter
     * @return string the html to diplay
     */
    public function rest_param_description_html($paramdescription, $paramstring) {
        $brakeline = <<<EOF


EOF;
        // description object is a list
        if ($paramdescription instanceof external_multiple_structure) {
            $paramstring = $paramstring . '[0]';
            $return = $this->rest_param_description_html($paramdescription->content, $paramstring);
            return $return;
        } else if ($paramdescription instanceof external_single_structure) {
            // description object is an object
            $singlestructuredesc = "";
            $initialparamstring = $paramstring;
            foreach ($paramdescription->keys as $attributname => $attribut) {
                $paramstring = $initialparamstring . '[' . $attributname . ']';
                $singlestructuredesc .= $this->rest_param_description_html(
                                $paramdescription->keys[$attributname], $paramstring);
            }
            return $singlestructuredesc;
        } else {
            // description object is a primary type (string, integer)
            $paramstring = $paramstring . '=';
            switch ($paramdescription->type) {
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
            return $paramstring . " " . $type . $brakeline;
        }
    }

    /**
     * Displays all the documentation
     *
     * @param array $functions external_description of all the web service functions
     * @param boolean $printableformat true if we want to display the documentation in a printable format
     * @param array $activatedprotocol the currently enabled protocol
     * @param array $authparams url parameters (it contains 'tokenid' and sometimes 'print')
     * @param string $parenturl url of the calling page - needed for the print button url:
     *               '/admin/documentation.php' or '/webservice/wsdoc.php' (default)
     * @return string the html to diplay
     */
    public function documentation_html($functions, $printableformat, $activatedprotocol,
            $authparams, $parenturl = '/webservice/wsdoc.php') {

        $documentationhtml = $this->output->heading(get_string('wsdocapi', 'webservice'));

        $br = html_writer::empty_tag('br', array());
        $brakeline = <<<EOF


EOF;
        // Some general information
        $docinfo = new stdClass();
        $docurl = new moodle_url('http://docs.moodle.org/dev/Creating_a_web_service_client');
        $docinfo->doclink = html_writer::tag('a',
                        get_string('wsclientdoc', 'webservice'), array('href' => $docurl));
        $documentationhtml .= get_string('wsdocumentationintro', 'webservice', $docinfo);
        $documentationhtml .= $br . $br;


        // Print button
        $authparams['print'] = true;
        $url = new moodle_url($parenturl, $authparams); // Required
        $documentationhtml .= $this->output->single_button($url, get_string('print', 'webservice'));
        $documentationhtml .= $br;


        // each functions will be displayed into a collapsible region
        //(opened if printableformat = true)
        foreach ($functions as $functionname => $description) {

            if (empty($printableformat)) {
                $documentationhtml .= print_collapsible_region_start('',
                                'aera_' . $functionname,
                                html_writer::start_tag('strong', array())
                                . $functionname . html_writer::end_tag('strong'),
                                false,
                                !$printableformat,
                                true);
            } else {
                $documentationhtml .= html_writer::tag('strong', $functionname);
                $documentationhtml .= $br;
            }

            // function global description
            $documentationhtml .= $br;
            $documentationhtml .= html_writer::start_tag('div',
                            array('style' => 'border:solid 1px #DEDEDE;background:#E2E0E0;
                        color:#222222;padding:4px;'));
            $documentationhtml .= s($description->description);
            $documentationhtml .= html_writer::end_tag('div');
            $documentationhtml .= $br . $br;

            // function arguments documentation
            $documentationhtml .= html_writer::start_tag('span', array('style' => 'color:#EA33A6'));
            $documentationhtml .= get_string('arguments', 'webservice');
            $documentationhtml .= html_writer::end_tag('span');
            $documentationhtml .= $br;
            foreach ($description->parameters_desc->keys as $paramname => $paramdesc) {
                // a argument documentation
                $documentationhtml .= html_writer::start_tag('span', array('style' => 'font-size:80%'));

                if ($paramdesc->required == VALUE_REQUIRED) {
                    $required = get_string('required', 'webservice');
                }
                if ($paramdesc->required == VALUE_DEFAULT) {
                    if ($paramdesc->default === null) {
                        $default = "null";
                    } else {
                        $default = print_r($paramdesc->default, true);
                    }
                    $required = get_string('default', 'webservice', $default);
                }
                if ($paramdesc->required == VALUE_OPTIONAL) {
                    $required = get_string('optional', 'webservice');
                }

                $documentationhtml .= html_writer::start_tag('b', array());
                $documentationhtml .= $paramname;
                $documentationhtml .= html_writer::end_tag('b');
                $documentationhtml .= " (" . $required . ")"; // argument is required or optional ?
                $documentationhtml .= $br;
                $documentationhtml .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
                        . s($paramdesc->desc); // Argument description.
                $documentationhtml .= $br . $br;
                // general structure of the argument
                $documentationhtml .= $this->colored_box_with_pre_tag(
                                get_string('generalstructure', 'webservice'),
                                $this->detailed_description_html($paramdesc),
                                'FFF1BC');
                // xml-rpc structure of the argument in PHP format
                if (!empty($activatedprotocol['xmlrpc'])) {
                    $documentationhtml .= $this->colored_box_with_pre_tag(
                                    get_string('phpparam', 'webservice'),
                                    htmlentities('[' . $paramname . '] =>'
                                            . $this->xmlrpc_param_description_html($paramdesc)),
                                    'DFEEE7');
                }
                // POST format for the REST protocol for the argument
                if (!empty($activatedprotocol['rest'])) {
                    $documentationhtml .= $this->colored_box_with_pre_tag(
                                    get_string('restparam', 'webservice'),
                                    htmlentities($this->rest_param_description_html(
                                                    $paramdesc, $paramname)),
                                    'FEEBE5');
                }
                $documentationhtml .= html_writer::end_tag('span');
            }
            $documentationhtml .= $br . $br;


            // function response documentation
            $documentationhtml .= html_writer::start_tag('span', array('style' => 'color:#EA33A6'));
            $documentationhtml .= get_string('response', 'webservice');
            $documentationhtml .= html_writer::end_tag('span');
            $documentationhtml .= $br;
            // function response description
            $documentationhtml .= html_writer::start_tag('span', array('style' => 'font-size:80%'));
            if (!empty($description->returns_desc->desc)) {
                $documentationhtml .= $description->returns_desc->desc;
                $documentationhtml .= $br . $br;
            }
            if (!empty($description->returns_desc)) {
                // general structure of the response
                $documentationhtml .= $this->colored_box_with_pre_tag(
                                get_string('generalstructure', 'webservice'),
                                $this->detailed_description_html($description->returns_desc),
                                'FFF1BC');
                // xml-rpc structure of the response in PHP format
                if (!empty($activatedprotocol['xmlrpc'])) {
                    $documentationhtml .= $this->colored_box_with_pre_tag(
                                    get_string('phpresponse', 'webservice'),
                                    htmlentities($this->xmlrpc_param_description_html(
                                                    $description->returns_desc)),
                                    'DFEEE7');
                }
                // XML response for the REST protocol
                if (!empty($activatedprotocol['rest'])) {
                    $restresponse = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>"
                            . $brakeline . "<RESPONSE>" . $brakeline;
                    $restresponse .= $this->description_in_indented_xml_format(
                                    $description->returns_desc);
                    $restresponse .="</RESPONSE>" . $brakeline;
                    $documentationhtml .= $this->colored_box_with_pre_tag(
                                    get_string('restcode', 'webservice'),
                                    htmlentities($restresponse),
                                    'FEEBE5');
                }
            }
            $documentationhtml .= html_writer::end_tag('span');
            $documentationhtml .= $br . $br;

            // function errors documentation for REST protocol
            if (!empty($activatedprotocol['rest'])) {
                $documentationhtml .= html_writer::start_tag('span', array('style' => 'color:#EA33A6'));
                $documentationhtml .= get_string('errorcodes', 'webservice');
                $documentationhtml .= html_writer::end_tag('span');
                $documentationhtml .= $br . $br;
                $documentationhtml .= html_writer::start_tag('span', array('style' => 'font-size:80%'));
                $errormessage = get_string('invalidparameter', 'debug');
                $restexceptiontext = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<EXCEPTION class="invalid_parameter_exception">
    <MESSAGE>{$errormessage}</MESSAGE>
    <DEBUGINFO></DEBUGINFO>
</EXCEPTION>
EOF;
                $documentationhtml .= $this->colored_box_with_pre_tag(
                                get_string('restexception', 'webservice'),
                                htmlentities($restexceptiontext),
                                'FEEBE5');

                $documentationhtml .= html_writer::end_tag('span');
            }
            $documentationhtml .= $br . $br;

            // Login required info.
            $documentationhtml .= html_writer::start_tag('span', array('style' => 'color:#EA33A6'));
            $documentationhtml .= get_string('loginrequired', 'webservice') . $br;
            $documentationhtml .= html_writer::end_tag('span');
            $documentationhtml .= $description->loginrequired ? get_string('yes') : get_string('no');
            $documentationhtml .= $br . $br;

            // Ajax info.
            $documentationhtml .= html_writer::start_tag('span', array('style' => 'color:#EA33A6'));
            $documentationhtml .= get_string('callablefromajax', 'webservice') . $br;
            $documentationhtml .= html_writer::end_tag('span');
            $documentationhtml .= $description->allowed_from_ajax ? get_string('yes') : get_string('no');
            $documentationhtml .= $br . $br;

            if (empty($printableformat)) {
                $documentationhtml .= print_collapsible_region_end(true);
            }
        }

        return $documentationhtml;
    }

}
