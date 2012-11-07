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
 * This file contains the class definition for the exporter object.
 *
 * @package core_portfolio
 * @copyright 2008 Penny Leach <penny@catalyst.net.nz>
 *            Martin Dougiamas  <http://dougiamas.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The class that handles the various stages of the actual export
 * and the communication between the caller and the portfolio plugin.
 *
 * This is stored in the database between page requests in serialized base64 encoded form
 * also contains helper methods for the plugin and caller to use (at the end of the file)
 * @see get_base_filearea - where to write files to
 * @see write_new_file - write some content to a file in the export filearea
 * @see copy_existing_file - copy an existing file into the export filearea
 * @see get_tempfiles - return list of all files in the export filearea
 *
 * @package core_portfolio
 * @category portfolio
 * @copyright 2008 Penny Leach <penny@catalyst.net.nz>
 *            Martin Dougiamas  <http://dougiamas.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class portfolio_exporter {

    /** @var portfolio_caller_base the caller object used during the export */
    private $caller;

    /** @var portfolio_plugin_base the portfolio plugin instanced used during the export */
    private $instance;

    /** @var bool if there has been no config form displayed to the user */
    private $noexportconfig;

    /**
     * @var stdClass the user currently exporting content always $USER,
     *               but more conveniently placed here
     */
    private $user;

    /**
     * @var string the file to include that contains the class defintion of
     *             the portfolio instance plugin used to re-waken the object after sleep
     */
    public $instancefile;

    /**
     * @var string the component that contains the class definition of
     *             the caller object used to re-waken the object after sleep
     */
    public $callercomponent;

    /** @var int the current stage of the export */
    private $stage;

    /** @var bool whether something (usually the portfolio plugin) has forced queuing */
    private $forcequeue;

    /**
     * @var int id of this export matches record in portfolio_tempdata table
     *          and used for itemid for file storage.
     */
    private $id;

    /** @var array of stages that have had the portfolio plugin already steal control from them */
    private $alreadystolen;

    /**
     * @var stored_file files that the exporter has written to this temp area keep track of
     *                  this in case of duplicates within one export see MDL-16390
     */
    private $newfilehashes;

    /**
     * @var string selected exportformat this is also set in
     *             export_config in the portfolio and caller classes
     */
    private $format;

    /** @var bool queued - this is set after the event is triggered */
    private $queued = false;

    /** @var int expiry time - set the first time the object is saved out */
    private $expirytime;

    /**
     * @var bool deleted - this is set during the cleanup routine so
     *           that subsequent save() calls can detect it
     */
    private $deleted = false;

    /**
     * Construct a new exporter for use
     *
     * @param portfolio_plugin_base $instance portfolio instance (passed by reference)
     * @param portfolio_caller_base $caller portfolio caller (passed by reference)
     * @param string $callercomponent the name of the callercomponent
     */
    public function __construct(&$instance, &$caller, $callercomponent) {
        $this->instance =& $instance;
        $this->caller =& $caller;
        if ($instance) {
            $this->instancefile = 'portfolio/' . $instance->get('plugin') . '/lib.php';
            $this->instance->set('exporter', $this);
        }
        $this->callercomponent = $callercomponent;
        $this->stage = PORTFOLIO_STAGE_CONFIG;
        $this->caller->set('exporter', $this);
        $this->alreadystolen = array();
        $this->newfilehashes = array();
    }

    /**
     * Generic getter for properties belonging to this instance
     * <b>outside</b> the subclasses like name, visible etc.
     *
     * @param string $field property's name
     * @return portfolio_format|mixed
     */
    public function get($field) {
        if ($field == 'format') {
            return portfolio_format_object($this->format);
        } else if ($field == 'formatclass') {
            return $this->format;
        }
        if (property_exists($this, $field)) {
            return $this->{$field};
        }
        $a = (object)array('property' => $field, 'class' => get_class($this));
        throw new portfolio_export_exception($this, 'invalidproperty', 'portfolio', null, $a);
    }

    /**
     * Generic setter for properties belonging to this instance
     * <b>outside</b> the subclass like name, visible, etc.
     *
     * @param string $field property's name
     * @param mixed $value property's value
     * @return bool
     * @throws portfolio_export_exception
     */
    public function set($field, &$value) {
        if (property_exists($this, $field)) {
            $this->{$field} =& $value;
            if ($field == 'instance') {
                $this->instancefile = 'portfolio/' . $this->instance->get('plugin') . '/lib.php';
                $this->instance->set('exporter', $this);
            }
            $this->dirty = true;
            return true;
        }
        $a = (object)array('property' => $field, 'class' => get_class($this));
        throw new portfolio_export_exception($this, 'invalidproperty', 'portfolio', null, $a);

    }

    /**
     * Sets this export to force queued.
     * Sometimes plugins need to set this randomly
     * if an external system changes its mind
     * about what's supported
     */
    public function set_forcequeue() {
        $this->forcequeue = true;
    }

    /**
     * Process the given stage calling whatever functions are necessary
     *
     * @param int $stage (see PORTFOLIO_STAGE_* constants)
     * @param bool $alreadystolen used to avoid letting plugins steal control twice.
     * @return bool whether or not to process the next stage. this is important as the function is called recursively.
     */
    public function process_stage($stage, $alreadystolen=false) {
        $this->set('stage', $stage);
        if ($alreadystolen) {
            $this->alreadystolen[$stage] = true;
        } else {
            if (!array_key_exists($stage, $this->alreadystolen)) {
                $this->alreadystolen[$stage] = false;
            }
        }
        if (!$this->alreadystolen[$stage] && $url = $this->instance->steal_control($stage)) {
            $this->save();
            redirect($url); // does not return
        } else {
            $this->save();
        }

        $waiting = $this->instance->get_export_config('wait');
        if ($stage > PORTFOLIO_STAGE_QUEUEORWAIT && empty($waiting)) {
            $stage = PORTFOLIO_STAGE_FINISHED;
        }
        $functionmap = array(
            PORTFOLIO_STAGE_CONFIG        => 'config',
            PORTFOLIO_STAGE_CONFIRM       => 'confirm',
            PORTFOLIO_STAGE_QUEUEORWAIT   => 'queueorwait',
            PORTFOLIO_STAGE_PACKAGE       => 'package',
            PORTFOLIO_STAGE_CLEANUP       => 'cleanup',
            PORTFOLIO_STAGE_SEND          => 'send',
            PORTFOLIO_STAGE_FINISHED      => 'finished'
        );

        $function = 'process_stage_' . $functionmap[$stage];
        try {
            if ($this->$function()) {
                // if we get through here it means control was returned
                // as opposed to wanting to stop processing
                // eg to wait for user input.
                $this->save();
                $stage++;
                return $this->process_stage($stage);
            } else {
                $this->save();
                return false;
            }
        } catch (portfolio_caller_exception $e) {
            portfolio_export_rethrow_exception($this, $e);
        } catch (portfolio_plugin_exception $e) {
            portfolio_export_rethrow_exception($this, $e);
        } catch (portfolio_export_exception $e) {
            throw $e;
        } catch (Exception $e) {
            debugging(get_string('thirdpartyexception', 'portfolio', get_class($e)));
            debugging($e);
            portfolio_export_rethrow_exception($this, $e);
        }
    }

    /**
     * Helper function to return the portfolio instance
     *
     * @return portfolio_plugin_base subclass
     */
    public function instance() {
        return $this->instance;
    }

    /**
     * Helper function to return the caller object
     *
     * @return portfolio_caller_base subclass
     */
    public function caller() {
        return $this->caller;
    }

    /**
     * Processes the 'config' stage of the export
     *
     * @return bool whether or not to process the next stage. this is important as the control function is called recursively.
     */
    public function process_stage_config() {
        global $OUTPUT, $CFG;
        $pluginobj = $callerobj = null;
        if ($this->instance->has_export_config()) {
            $pluginobj = $this->instance;
        }
        if ($this->caller->has_export_config()) {
            $callerobj = $this->caller;
        }
        $formats = portfolio_supported_formats_intersect($this->caller->supported_formats(), $this->instance->supported_formats());
        $expectedtime = $this->instance->expected_time($this->caller->expected_time());
        if (count($formats) == 0) {
            // something went wrong, we should not have gotten this far.
            throw new portfolio_export_exception($this, 'nocommonformats', 'portfolio', null, array('location' => get_class($this->caller), 'formats' => implode(',', $formats)));
        }
        // even if neither plugin or caller wants any config, we have to let the user choose their format, and decide to wait.
        if ($pluginobj || $callerobj || count($formats) > 1 || ($expectedtime != PORTFOLIO_TIME_LOW && $expectedtime != PORTFOLIO_TIME_FORCEQUEUE)) {
            $customdata = array(
                'instance' => $this->instance,
                'id'       => $this->id,
                'plugin' => $pluginobj,
                'caller' => $callerobj,
                'userid' => $this->user->id,
                'formats' => $formats,
                'expectedtime' => $expectedtime,
            );
            require_once($CFG->libdir . '/portfolio/forms.php');
            $mform = new portfolio_export_form('', $customdata);
            if ($mform->is_cancelled()){
                $this->cancel_request();
            } else if ($fromform = $mform->get_data()){
                if (!confirm_sesskey()) {
                    throw new portfolio_export_exception($this, 'confirmsesskeybad');
                }
                $pluginbits = array();
                $callerbits = array();
                foreach ($fromform as $key => $value) {
                    if (strpos($key, 'plugin_') === 0) {
                        $pluginbits[substr($key, 7)]  = $value;
                    } else if (strpos($key, 'caller_') === 0) {
                        $callerbits[substr($key, 7)] = $value;
                    }
                }
                $callerbits['format'] = $pluginbits['format'] = $fromform->format;
                $pluginbits['wait'] = $fromform->wait;
                if ($expectedtime == PORTFOLIO_TIME_LOW) {
                    $pluginbits['wait'] = 1;
                    $pluginbits['hidewait'] = 1;
                } else if ($expectedtime == PORTFOLIO_TIME_FORCEQUEUE) {
                    $pluginbits['wait'] = 0;
                    $pluginbits['hidewait'] = 1;
                    $this->forcequeue = true;
                }
                $callerbits['hideformat'] = $pluginbits['hideformat'] = (count($formats) == 1);
                $this->caller->set_export_config($callerbits);
                $this->instance->set_export_config($pluginbits);
                $this->set('format', $fromform->format);
                return true;
            } else {
                $this->print_header(get_string('configexport', 'portfolio'));
                echo $OUTPUT->box_start();
                $mform->display();
                echo $OUTPUT->box_end();
                echo $OUTPUT->footer();
                return false;;
            }
        } else {
            $this->noexportconfig = true;
            $format = array_shift($formats);
            $config = array(
                'hidewait' => 1,
                'wait' => (($expectedtime == PORTFOLIO_TIME_LOW) ? 1 : 0),
                'format' => $format,
                'hideformat' => 1
            );
            $this->set('format', $format);
            $this->instance->set_export_config($config);
            $this->caller->set_export_config(array('format' => $format, 'hideformat' => 1));
            if ($expectedtime == PORTFOLIO_TIME_FORCEQUEUE) {
                $this->forcequeue = true;
            }
            return true;
            // do not break - fall through to confirm
        }
    }

    /**
     * Processes the 'confirm' stage of the export
     *
     * @return bool whether or not to process the next stage. this is important as the control function is called recursively.
     */
    public function process_stage_confirm() {
        global $CFG, $DB, $OUTPUT;

        $previous = $DB->get_records(
            'portfolio_log',
            array(
                'userid'      => $this->user->id,
                'portfolio'   => $this->instance->get('id'),
                'caller_sha1' => $this->caller->get_sha1(),
            )
        );
        if (isset($this->noexportconfig) && empty($previous)) {
            return true;
        }
        $strconfirm = get_string('confirmexport', 'portfolio');
        $baseurl = $CFG->wwwroot . '/portfolio/add.php?sesskey=' . sesskey() . '&id=' . $this->get('id');
        $yesurl = $baseurl . '&stage=' . PORTFOLIO_STAGE_QUEUEORWAIT;
        $nourl  = $baseurl . '&cancel=1';
        $this->print_header(get_string('confirmexport', 'portfolio'));
        echo $OUTPUT->box_start();
        echo $OUTPUT->heading(get_string('confirmsummary', 'portfolio'), 3);
        $mainsummary = array();
        if (!$this->instance->get_export_config('hideformat')) {
            $mainsummary[get_string('selectedformat', 'portfolio')] = get_string('format_' . $this->instance->get_export_config('format'), 'portfolio');
        }
        if (!$this->instance->get_export_config('hidewait')) {
            $mainsummary[get_string('selectedwait', 'portfolio')] = get_string(($this->instance->get_export_config('wait') ? 'yes' : 'no'));
        }
        if ($previous) {
            $previousstr = '';
            foreach ($previous as $row) {
                $previousstr .= userdate($row->time);
                if ($row->caller_class != get_class($this->caller)) {
                    if (!empty($row->caller_file)) {
                        portfolio_include_callback_file($row->caller_file);
                    } else if (!empty($row->caller_component)) {
                        portfolio_include_callback_file($row->caller_component);
                    } else { // Ok, that's weird - this should never happen. Is the apocalypse coming?
                        continue;
                    }
                    $previousstr .= ' (' . call_user_func(array($row->caller_class, 'display_name')) . ')';
                }
                $previousstr .= '<br />';
            }
            $mainsummary[get_string('exportedpreviously', 'portfolio')] = $previousstr;
        }
        if (!$csummary = $this->caller->get_export_summary()) {
            $csummary = array();
        }
        if (!$isummary = $this->instance->get_export_summary()) {
            $isummary = array();
        }
        $mainsummary = array_merge($mainsummary, $csummary, $isummary);
        $table = new html_table();
        $table->attributes['class'] = 'generaltable exportsummary';
        $table->data = array();
        foreach ($mainsummary as $string => $value) {
            $table->data[] = array($string, $value);
        }
        echo html_writer::table($table);
        echo $OUTPUT->confirm($strconfirm, $yesurl, $nourl);
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        return false;
    }

    /**
     * Processes the 'queueornext' stage of the export
     *
     * @return bool whether or not to process the next stage. this is important as the control function is called recursively.
     */
    public function process_stage_queueorwait() {
        $wait = $this->instance->get_export_config('wait');
        if (empty($wait)) {
            events_trigger('portfolio_send', $this->id);
            $this->queued = true;
            return $this->process_stage_finished(true);
        }
        return true;
    }

    /**
     * Processes the 'package' stage of the export
     *
     * @return bool whether or not to process the next stage. this is important as the control function is called recursively.
     * @throws portfolio_export_exception
     */
    public function process_stage_package() {
        // now we've agreed on a format,
        // the caller is given control to package it up however it wants
        // and then the portfolio plugin is given control to do whatever it wants.
        try {
            $this->caller->prepare_package();
        } catch (portfolio_exception $e) {
            throw new portfolio_export_exception($this, 'callercouldnotpackage', 'portfolio', null, $e->getMessage());
        }
        catch (file_exception $e) {
            throw new portfolio_export_exception($this, 'callercouldnotpackage', 'portfolio', null, $e->getMessage());
        }
        try {
            $this->instance->prepare_package();
        }
        catch (portfolio_exception $e) {
            throw new portfolio_export_exception($this, 'plugincouldnotpackage', 'portfolio', null, $e->getMessage());
        }
        catch (file_exception $e) {
            throw new portfolio_export_exception($this, 'plugincouldnotpackage', 'portfolio', null, $e->getMessage());
        }
        return true;
    }

    /**
     * Processes the 'cleanup' stage of the export
     *
     * @param bool $pullok normally cleanup is deferred for pull plugins until after the file is requested from portfolio/file.php
     *                        if you want to clean up earlier, pass true here (defaults to false)
     * @return bool whether or not to process the next stage. this is important as the control function is called recursively.
     */
    public function process_stage_cleanup($pullok=false) {
        global $CFG, $DB;

        if (!$pullok && $this->get('instance') && !$this->get('instance')->is_push()) {
            return true;
        }
        if ($this->get('instance')) {
            // might not be set - before export really starts
            $this->get('instance')->cleanup();
        }
        $DB->delete_records('portfolio_tempdata', array('id' => $this->id));
        $fs = get_file_storage();
        $fs->delete_area_files(SYSCONTEXTID, 'portfolio', 'exporter', $this->id);
        $this->deleted = true;
        return true;
    }

    /**
     * Processes the 'send' stage of the export
     *
     * @return bool whether or not to process the next stage. this is important as the control function is called recursively.
     */
    public function process_stage_send() {
        // send the file
        try {
            $this->instance->send_package();
        }
        catch (portfolio_plugin_exception $e) {
            // not catching anything more general here. plugins with dependencies on other libraries that throw exceptions should catch and rethrow.
            // eg curl exception
            throw new portfolio_export_exception($this, 'failedtosendpackage', 'portfolio', null, $e->getMessage());
        }
        // only log push types, pull happens in send_file
        if ($this->get('instance')->is_push()) {
            $this->log_transfer();
        }
        return true;
    }

    /**
     * Log the transfer
     *
     * this should only be called after the file has been sent
     * either via push, or sent from a pull request.
     */
    public function log_transfer() {
        global $DB;
        $l = array(
            'userid' => $this->user->id,
            'portfolio' => $this->instance->get('id'),
            'caller_file'=> '',
            'caller_component' => $this->callercomponent,
            'caller_sha1' => $this->caller->get_sha1(),
            'caller_class' => get_class($this->caller),
            'continueurl' => $this->instance->get_static_continue_url(),
            'returnurl' => $this->caller->get_return_url(),
            'tempdataid' => $this->id,
            'time' => time(),
        );
        $DB->insert_record('portfolio_log', $l);
    }

    /**
     * In some cases (mahara) we need to update this after the log has been done
     * because of MDL-20872
     *
     * @param string $url link to be recorded to portfolio log
     */
    public function update_log_url($url) {
        global $DB;
        $DB->set_field('portfolio_log', 'continueurl', $url, array('tempdataid' => $this->id));
    }

    /**
     * Processes the 'finish' stage of the export
     *
     * @param bool $queued let the process to be queued
     * @return bool whether or not to process the next stage. this is important as the control function is called recursively.
     */
    public function process_stage_finished($queued=false) {
        global $OUTPUT;
        $returnurl = $this->caller->get_return_url();
        $continueurl = $this->instance->get_interactive_continue_url();
        $extras = $this->instance->get_extra_finish_options();

        $key = 'exportcomplete';
        if ($queued || $this->forcequeue) {
            $key = 'exportqueued';
            if ($this->forcequeue) {
                $key = 'exportqueuedforced';
            }
        }
        $this->print_header(get_string($key, 'portfolio'), false);
        self::print_finish_info($returnurl, $continueurl, $extras);
        echo $OUTPUT->footer();
        return false;
    }


    /**
     * Local print header function to be reused across the export
     *
     * @param string $headingstr full language string
     * @param bool $summary (optional) to print summary, default is set to true
     * @return void
     */
    public function print_header($headingstr, $summary=true) {
        global $OUTPUT, $PAGE;
        $titlestr = get_string('exporting', 'portfolio');
        $headerstr = get_string('exporting', 'portfolio');

        $PAGE->set_title($titlestr);
        $PAGE->set_heading($headerstr);
        echo $OUTPUT->header();
        echo $OUTPUT->heading($headingstr);

        if (!$summary) {
            return;
        }

        echo $OUTPUT->box_start();
        echo $OUTPUT->box_start();
        echo $this->caller->heading_summary();
        echo $OUTPUT->box_end();
        if ($this->instance) {
            echo $OUTPUT->box_start();
            echo $this->instance->heading_summary();
            echo $OUTPUT->box_end();
        }
        echo $OUTPUT->box_end();
    }

    /**
     * Cancels a potfolio request and cleans up the tempdata
     * and redirects the user back to where they started
     *
     * @param bool $logreturn options to return to porfolio log or caller return page
     * @return void
     * @uses exit
     */
    public function cancel_request($logreturn=false) {
        global $CFG;
        if (!isset($this)) {
            return;
        }
        $this->process_stage_cleanup(true);
        if ($logreturn) {
            redirect($CFG->wwwroot . '/user/portfoliologs.php');
        }
        redirect($this->caller->get_return_url());
        exit;
    }

    /**
     * Writes out the contents of this object and all its data to the portfolio_tempdata table and sets the 'id' field.
     *
     * @return void
     */
    public function save() {
        global $DB;
        if (empty($this->id)) {
            $r = (object)array(
                'data' => base64_encode(serialize($this)),
                'expirytime' => time() + (60*60*24),
                'userid' => $this->user->id,
                'instance' => (empty($this->instance)) ? null : $this->instance->get('id'),
            );
            $this->id = $DB->insert_record('portfolio_tempdata', $r);
            $this->expirytime = $r->expirytime;
            $this->save(); // call again so that id gets added to the save data.
        } else {
            if (!$r = $DB->get_record('portfolio_tempdata', array('id' => $this->id))) {
                if (!$this->deleted) {
                    //debugging("tried to save current object, but failed - see MDL-20872");
                }
                return;
            }
            $r->data = base64_encode(serialize($this));
            $r->instance = (empty($this->instance)) ? null : $this->instance->get('id');
            $DB->update_record('portfolio_tempdata', $r);
        }
    }

    /**
     * Rewakens the data from the database given the id.
     * Makes sure to load the required files with the class definitions
     *
     * @param int $id id of data
     * @return portfolio_exporter
     */
    public static function rewaken_object($id) {
        global $DB, $CFG;
        require_once($CFG->libdir . '/filelib.php');
        require_once($CFG->libdir . '/portfolio/exporter.php');
        require_once($CFG->libdir . '/portfolio/caller.php');
        require_once($CFG->libdir . '/portfolio/plugin.php');
        if (!$data = $DB->get_record('portfolio_tempdata', array('id' => $id))) {
            // maybe it's been finished already by a pull plugin
            // so look in the logs
            if ($log = $DB->get_record('portfolio_log', array('tempdataid' => $id))) {
                self::print_cleaned_export($log);
            }
            throw new portfolio_exception('invalidtempid', 'portfolio');
        }
        $exporter = unserialize(base64_decode($data->data));
        if ($exporter->instancefile) {
            require_once($CFG->dirroot . '/' . $exporter->instancefile);
        }
        if (!empty($exporter->callerfile)) {
            portfolio_include_callback_file($exporter->callerfile);
        } else if (!empty($exporter->callercomponent)) {
            portfolio_include_callback_file($exporter->callercomponent);
        } else {
            return; // Should never get here!
        }

        $exporter = unserialize(serialize($exporter));
        if (!$exporter->get('id')) {
            // workaround for weird case
            // where the id doesn't get saved between a new insert
            // and the subsequent call that sets this field in the serialised data
            $exporter->set('id', $id);
            $exporter->save();
        }
        return $exporter;
    }

    /**
     * Helper function to create the beginnings of a file_record object
     * to create a new file in the portfolio_temporary working directory.
     * Use write_new_file or copy_existing_file externally
     * @see write_new_file
     * @see copy_existing_file
     *
     * @param string $name filename of new record
     * @return object
     */
    private function new_file_record_base($name) {
        return (object)array_merge($this->get_base_filearea(), array(
            'filepath' => '/',
            'filename' => $name,
        ));
    }

    /**
     * Verifies a rewoken object.
     * Checks to make sure it belongs to the same user and session as is currently in use.
     *
     * @param bool $readonly if we're reawakening this for a user to just display in the log view, don't verify the sessionkey
     * @throws portfolio_exception
     */
    public function verify_rewaken($readonly=false) {
        global $USER, $CFG;
        if ($this->get('user')->id != $USER->id) { // make sure it belongs to the right user
            throw new portfolio_exception('notyours', 'portfolio');
        }
        if (!$readonly && $this->get('instance') && !$this->get('instance')->allows_multiple_exports()) {
            $already = portfolio_existing_exports($this->get('user')->id, $this->get('instance')->get('plugin'));
            $already = array_keys($already);

            if (array_shift($already) != $this->get('id')) {

                $a = (object)array(
                    'plugin'  => $this->get('instance')->get('plugin'),
                    'link'    => $CFG->wwwroot . '/user/portfoliologs.php',
                );
                throw new portfolio_exception('nomultipleexports', 'portfolio', '', $a);
            }
        }
        if (!$this->caller->check_permissions()) { // recall the caller permission check
            throw new portfolio_caller_exception('nopermissions', 'portfolio', $this->caller->get_return_url());
        }
    }
    /**
     * Copies a file from somewhere else in moodle
     * to the portfolio temporary working directory
     * associated with this export
     *
     * @param stored_file $oldfile existing stored file object
     * @return stored_file|bool new file object
     */
    public function copy_existing_file($oldfile) {
        if (array_key_exists($oldfile->get_contenthash(), $this->newfilehashes)) {
            return $this->newfilehashes[$oldfile->get_contenthash()];
        }
        $fs = get_file_storage();
        $file_record = $this->new_file_record_base($oldfile->get_filename());
        if ($dir = $this->get('format')->get_file_directory()) {
            $file_record->filepath = '/'. $dir . '/';
        }
        try {
            $newfile = $fs->create_file_from_storedfile($file_record, $oldfile->get_id());
            $this->newfilehashes[$newfile->get_contenthash()] = $newfile;
            return $newfile;
        } catch (file_exception $e) {
            return false;
        }
    }

    /**
     * Writes out some content to a file
     * in the portfolio temporary working directory
     * associated with this export.
     *
     * @param string $content content to write
     * @param string $name filename to use
     * @param bool $manifest whether this is the main file or an secondary file (eg attachment)
     * @return stored_file
     */
    public function write_new_file($content, $name, $manifest=true) {
        $fs = get_file_storage();
        $file_record = $this->new_file_record_base($name);
        if (empty($manifest) && ($dir = $this->get('format')->get_file_directory())) {
            $file_record->filepath = '/' . $dir . '/';
        }
        return $fs->create_file_from_string($file_record, $content);
    }

    /**
     * Zips all files in the temporary directory
     *
     * @param string $filename name of resulting zipfile (optional, defaults to portfolio-export.zip)
     * @param string $filepath subpath in the filearea (optional, defaults to final)
     * @return stored_file|bool resulting stored_file object, or false
     */
    public function zip_tempfiles($filename='portfolio-export.zip', $filepath='/final/') {
        $zipper = new zip_packer();

        list ($contextid, $component, $filearea, $itemid) = array_values($this->get_base_filearea());
        if ($newfile = $zipper->archive_to_storage($this->get_tempfiles(), $contextid, $component, $filearea, $itemid, $filepath, $filename, $this->user->id)) {
            return $newfile;
        }
        return false;

    }

    /**
     * Returns an arary of files in the temporary working directory
     * for this export.
     * Always use this instead of the files api directly
     *
     * @param string $skipfile name of the file to be skipped
     * @return array of stored_file objects keyed by name
     */
    public function get_tempfiles($skipfile='portfolio-export.zip') {
        $fs = get_file_storage();
        $files = $fs->get_area_files(SYSCONTEXTID, 'portfolio', 'exporter', $this->id, 'sortorder, itemid, filepath, filename', false);
        if (empty($files)) {
            return array();
        }
        $returnfiles = array();
        foreach ($files as $f) {
            if ($f->get_filename() == $skipfile) {
                continue;
            }
            $returnfiles[$f->get_filepath() . '/' . $f->get_filename()] = $f;
        }
        return $returnfiles;
    }

    /**
     * Returns the context, filearea, and itemid.
     * Parts of a filearea (not filepath) to be used by
     * plugins if they want to do things like zip up the contents of
     * the temp area to here, or something that can't be done just using
     * write_new_file, copy_existing_file or get_tempfiles
     *
     * @return array contextid, filearea, itemid are the keys.
     */
    public function get_base_filearea() {
        return array(
            'contextid' => SYSCONTEXTID,
            'component' => 'portfolio',
            'filearea'  => 'exporter',
            'itemid'    => $this->id,
        );
    }

    /**
     * Wrapper function to print a friendly error to users
     * This is generally caused by them hitting an expired transfer
     * through the usage of the backbutton
     *
     * @uses exit
     */
    public static function print_expired_export() {
        global $CFG, $OUTPUT, $PAGE;
        $title = get_string('exportexpired', 'portfolio');
        $PAGE->navbar->add(get_string('exportexpired', 'portfolio'));
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        echo $OUTPUT->header();
        echo $OUTPUT->notification(get_string('exportexpireddesc', 'portfolio'));
        echo $OUTPUT->continue_button($CFG->wwwroot);
        echo $OUTPUT->footer();
        exit;
    }

    /**
     * Wrapper function to print a friendly error to users
     *
     * @param stdClass $log portfolio_log object
     * @param portfolio_plugin_base $instance portfolio instance
     * @uses exit
     */
    public static function print_cleaned_export($log, $instance=null) {
        global $CFG, $OUTPUT, $PAGE;
        if (empty($instance) || !$instance instanceof portfolio_plugin_base) {
            $instance = portfolio_instance($log->portfolio);
        }
        $title = get_string('exportalreadyfinished', 'portfolio');
        $PAGE->navbar->add($title);
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        echo $OUTPUT->header();
        echo $OUTPUT->notification(get_string('exportalreadyfinished', 'portfolio'));
        self::print_finish_info($log->returnurl, $instance->resolve_static_continue_url($log->continueurl));
        echo $OUTPUT->continue_button($CFG->wwwroot);
        echo $OUTPUT->footer();
        exit;
    }

    /**
     * Wrapper function to print continue and/or return link
     *
     * @param string $returnurl link to previos page
     * @param string $continueurl continue to next page
     * @param array $extras (optional) other links to be display.
     */
    public static function print_finish_info($returnurl, $continueurl, $extras=null) {
        if ($returnurl) {
            echo '<a href="' . $returnurl . '">' . get_string('returntowhereyouwere', 'portfolio') . '</a><br />';
        }
        if ($continueurl) {
            echo '<a href="' . $continueurl . '">' . get_string('continuetoportfolio', 'portfolio') . '</a><br />';
        }
        if (is_array($extras)) {
            foreach ($extras as $link => $string) {
                echo '<a href="' . $link . '">' . $string . '</a><br />';
            }
        }
    }
}
