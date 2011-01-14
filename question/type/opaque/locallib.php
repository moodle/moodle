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
 * Library routines used by the Opaque question type.
 *
 * @package qtype
 * @subpackage opaque
 * @copyright 2006 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->libdir . '/soaplib.php');
require_once($CFG->libdir . '/xmlize.php');


/** User passed on question. Should match the definition in Om.question.Results. */
define('OPAQUE_ATTEMPTS_PASS', 0);
/** User got question wrong after all attempts. Should match the definition in om.question.Results. */
define('OPAQUE_ATTEMPTS_WRONG', -1);
/** User got question partially correct after all attempts. Should match the definition in om.question.Results. */
define('OPAQUE_ATTEMPTS_PARTIALLYCORRECT', -2);
/** If developer hasn't set the value. Should match the definition in om.question.Results. */
define('OPAQUE_ATTEMPTS_UNSET', -99);
/** Prefix used for CSS files. */
define('OPAQUE_CSS_FILENAME_PREFIX', '__styles_');

/**
 * @return an array id -> enginename, that can be used to build a dropdown
 * menu of installed question types.
 */
function installed_engine_choices() {
    global $DB;
    return $DB->get_records_menu('question_opaque_engines', array(), 'name ASC', 'id, name');
}

function format_opaque_error($message, $a = NULL) {
    return get_string($message, 'qtype_opaque', $a);
}

function format_soap_fault($fault) {
    foreach (array('faultcode', 'faultactor', 'faultstring', 'faultdetail') as $field) {
        if (empty($fault->$field)) {
            $fault->$field = '';
        }
    }
    return get_string('soapfault', 'qtype_opaque', $fault);
}


/**
 * Manages loading and saving question engine definitions to and from the database.
 *
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_opaque_engine_manager {
    /**
     * Load the definition of an engine from the database.
     * @param integer $engineid the id of the engine to load.
     * @return mixed On success, and object with fields id, name, questionengines and questionbanks.
     * The last two fields are arrays of URLs. On an error, returns a string to look up in the
     * qtype_opaque language file as an error message.
     */
    public function load_engine_def($engineid) {
        $engine = get_record('question_opaque_engines', 'id', $engineid);
        if (!$engine) {
            return format_opaque_error('couldnotloadenginename', $engineid);
        }
        $engine->questionengines = array();
        $engine->questionbanks = array();
        $servers = get_records('question_opaque_servers', 'engineid', $engineid, 'id ASC');
        if (!$servers) {
            return format_opaque_error('couldnotloadengineservers', $engineid);
        }
        foreach ($servers as $server) {
            if ($server->type == 'qe') {
                $engine->questionengines[] = $server->url;
            } else if ($server->type == 'qb') {
                $engine->questionbanks[] = $server->url;
            } else {
                return format_opaque_error('unrecognisedservertype', $engineid);
            }
        }
        return $engine;
    }

    /**
     * Save or update an engine definition in the database, and returm the engine id. The definition
     * will be created if $engine->id is not set, and updated if it is.
     *
     * @param object $engine the definition to save.
     * @return integer the id of the saved definition.
     */
    public function save_engine_def($engine) {
        global $db;
        $db->StartTrans();

        if (!empty($engine->id)) {
            update_record('question_opaque_engines', $engine);
        } else {
            $engine->id = insert_record('question_opaque_engines', $engine);
        }
        delete_records('question_opaque_servers', 'engineid', $engine->id);
        $this->store_opaque_servers($engine->questionengines, 'qe', $engine->id);
        $this->store_opaque_servers($engine->questionbanks, 'qb', $engine->id);

        if ($db->CompleteTrans()) {
            return $engine->id;
        } else {
            print_error('couldnotsaveengineinfo', 'qtype_opaque', 'engines.php');
        }
    }

    /**
     * Save a list of servers of a given type in the question_opaque_servers table.
     *
     * @param array $urls an array of URLs.
     * @param string $type 'qe' or 'qb'.
     * @param int $engineid
     */
    protected function store_opaque_servers($urls, $type, $engineid) {
        foreach ($urls as $url) {
            $server = new stdClass;
            $server->engineid = $engineid;
            $server->type = $type;
            $server->url = $url;
            insert_record('question_opaque_servers', $server, false);
        }
    }

    /**
     * Delete the definition of an engine from the database.
     * @param integer $engineid the id of the engine to delete.
     * @return boolean whether the delete succeeded.
     */
    public function delete_engine_def($engineid) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records('question_opaque_servers', array('engineid' => $engineid));
        $DB->sdelete_records('question_opaque_engines', array('id' => $engineid));
        $transaction->allow_commit();
    }

    protected function get_possibly_matching_engines($engine) {
        global $CFG;

        // First we try to get a reasonably accurate guess with SQL - we load the id of all
        // engines with the same passkey and which use the first questionengine and questionbank
        // (if any).
        $where = "WHERE e.passkey = '$engine->passkey'";
        $sql = "SELECT e.id,1 FROM {$CFG->prefix}question_opaque_engines e ";
        if (!empty($engine->questionengines)) {
            $qeurl = reset($engine->questionengines);
            $sql .= "JOIN {$CFG->prefix}question_opaque_servers qe ON
                    qe.engineid = e.id AND qe.type = 'qe'";
            $where .= " AND qe.url = '$qeurl'";
        }
        if (!empty($engine->questionbanks)) {
            $qburl = reset($engine->questionbanks);
            $sql .= "JOIN {$CFG->prefix}question_opaque_servers qb ON
                    qb.engineid = e.id AND qb.type = 'qb'";
            $where .= " AND qb.url = '$qburl'";
        }
        $sql .= $where;
        return get_records_sql_menu($sql);
    }

    /**
     * If an engine definition like this one (same passkey and server lists) already exists
     * in the database, then return its id, otherwise save this one to the database and
     * return the new engine id.
     *
     * @param object $engine the engine to ensure is in the databse.
     * @return integer its id.
     */
    public function find_or_create_engineid($engine) {
        $possibleengineids = $this->get_possibly_matching_engines($engine);

        // Then we loop through the possibilities loading the full definition and comparing it.
        if ($possibleengineids) {
            foreach ($possibleengineids as $engineid => $ignored) {
                $testengine = $this->load_engine_def($engineid);
                $testengine->passkey = addslashes($testengine->passkey);
                if ($this->is_same_engine($engine, $testengine)) {
                    return $engineid;
                }
            }
        }

        return $this->save_engine_def($engine);
    }

    /**
     * Are these two engine definitions essentially the same (same passkey and server lists)?
     *
     * @param object $engine1 one engine definition.
     * @param object $engine2 another engine definition.
     * @return boolean whether they are the same.
     */
    public function is_same_engine($engine1, $engine2) {
        // Same passkey.
        $ans = $engine1->passkey == $engine2->passkey &&
        // Same question engines.
                !array_diff($engine1->questionengines, $engine2->questionengines) &&
                !array_diff($engine2->questionengines, $engine1->questionengines) &&
        // Same question banks.
                !array_diff($engine1->questionbanks, $engine2->questionbanks) &&
                !array_diff($engine2->questionbanks, $engine1->questionbanks);
        return $ans;
    }
}

/**
 * Load the definition of an engine from the database.
 * @param integer $engineid the id of the engine to load.
 * @return mixed On success, and object with fields id, name, questionengines and questionbanks.
 * The last two fields are arrays of URLs. On an error, returns a string to look up in the
 * qtype_opaque language file as an error message.
 */
function load_engine_def($engineid) {
    $manager = new qtype_opaque_engine_manager();
    return $manager->load_engine_def($engineid);
}

/**
 * Save or update an engine definition in the database, and returm the engine id. The definition
 * will be created if $engine->id is not set, and updated if it is.
 *
 * @param object $engine the definition to save.
 * @return integer the id of the saved definition.
 */
function save_engine_def($engine) {
    $manager = new qtype_opaque_engine_manager();
    return $manager->save_engine_def($engine);
}

/**
 * Delete the definition of an engine from the database.
 * @param integer $engineid the id of the engine to delete.
 * @return boolean whether the delete succeeded.
 */
function delete_engine_def($engineid) {
    $manager = new qtype_opaque_engine_manager();
    return $manager->delete_engine_def($engineid);
}

/**
 * If an engine definition like this one (same passkey and server lists) already exists
 * in the database, then return its id, otherwise save this one to the database and
 * return the new engine id.
 *
 * @param object $engine the engine to ensure is in the databse.
 * @return integer its id.
 */
function find_or_create_engineid($engine) {
    $manager = new qtype_opaque_engine_manager();
    return $manager->find_or_create_engineid($engine);
}

/**
 * @param mixed $engine either an $engine object, or the URL of a particular question engine server.
 * @return a soap connection, either to the specific URL give, or to to one of the question engine servers
 * of this $engine object picked at random. returns a string to look up in the qtype_opaque
 * language file as an error message if a problem arises.
 */
function connect_to_engine(&$engine) {
    if (is_string($engine)) {
        $url = $engine;
    } else if (!empty($engine->urlused)) {
        $url = $engine->urlused;
    } else {
        $url = $engine->questionengines[array_rand($engine->questionengines)];
    }
    $connection = soap_connect($url . '?wsdl');
    if (is_soap_fault($connection)) {
        return format_opaque_error('couldnotconnect', $url);
    }
    if (!is_string($engine)) {
        $engine->urlused = $url;
    }
    return $connection;
}

/**
 * @param mixed $engine either an $engine object, or the URL of a particular question engine server.
 * @return some XML, as parsed by xmlize, on success, or a string to look up in the qtype_opaque
 * language file as an error message.
 */
function get_engine_info($engine) {
    $connection = connect_to_engine($engine);
    if (is_string($connection)) {
        return $connection;
    }

    $getengineinforesult = soap_call($connection, 'getEngineInfo', array());
    if (is_soap_fault($getengineinforesult)) {
        return format_opaque_error('couldnotgetengineinfo', format_soap_fault($getengineinforesult));
    }

    $xmlarr = xmlize($getengineinforesult);
    return $xmlarr;
}

/**
 * @param mixed $engine either an $engine object, or the URL of a particular question engine server.
 * @return The question metadata, as an xmlised array, so, for example,
 * $metadata[questionmetadata][@][#][scoring][0][#][marks][0][#] is the maximum possible score for
 * this question.
 */
function get_question_metadata(&$engine, $remoteid, $remoteversion) {
    $connection = connect_to_engine($engine);
    $questionbaseurl = $engine->questionbanks[array_rand($engine->questionbanks)];
    $getmetadataresult = soap_call($connection, 'getQuestionMetadata',
            array($remoteid, $remoteversion, $questionbaseurl));
    if (is_soap_fault($getmetadataresult)) {
        return format_opaque_error('getmetadatacallfailed', format_soap_fault($getmetadataresult));
    }

    return xmlize($getmetadataresult);
}

/**
 * @param object $engine the engine to connect to.
 * @param string $remoteid
 * @param string $remoteversion
 * @param int $randomseed
 * @return mixed the result of the soap call on success, or a string error message on failure.
 */
function start_question_session(&$engine, $remoteid, $remoteversion, $data, $cached_resources) {
    $connection = connect_to_engine($engine);
    if (is_string($connection)) {
        return format_opaque_error('startcallfailed', $connection);
    }

    $questionbaseurl = '';
    if (!empty($engine->questionbanks)) {
        $questionbaseurl = $engine->questionbanks[array_rand($engine->questionbanks)];
    }

    $startresult = soap_call($connection, 'start', array(
        $remoteid,
        $remoteversion,
        $questionbaseurl,
        array('randomseed', 'userid', 'language', 'passKey', 'preferredbehaviour'),
        array($data['-_randomseed'], $data['-_userid'], $data['-_language'],
                generate_passkey($engine->passkey, $data['-_userid']), $data['-_preferredbehaviour']),
        $cached_resources
    ));

    if (is_soap_fault($startresult)) {
        return format_opaque_error('startcallfailed', format_soap_fault($startresult));
    }

    return $startresult;
}

function opaque_process(&$engine, $questionsessionid, $response) {
    $connection = connect_to_engine($engine);
    if (is_string($connection)) {
        return format_opaque_error('processcallfailed', $connection);
    }
    $processresult = soap_call($connection, 'process', array(
        $questionsessionid,
        array_keys($response),
        array_values($response)
    ));
    if (is_soap_fault($processresult)) {
        return format_opaque_error('processcallfailed', format_soap_fault($processresult));
    }

    return $processresult;
}

/**
 * @param string $questionsessionid the question session to stop.
 * @return true on success, or a string error message on failure.
 */
function stop_question_session($engine, $questionsessionid) {
    $connection = connect_to_engine($engine);
    if (is_string($connection)) {
        return format_opaque_error('stopcallfailed', $connection);
    }

    $stopresult = soap_call($connection, 'stop', array($questionsessionid));
    if (is_soap_fault($stopresult)) {
        return format_opaque_error('stopcallfailed', format_soap_fault($stopresult));
    } else {
        return true;
    }
}

/**
 * Get a step from $qa, as if $pendingstep had already been added at the end
 * of the list, if it is not null.
 * @param integer $seq
 * @param question_attempt $qa
 * @param question_attempt_step|null $pendingstep
 * @return question_attempt_step
 */
function opaque_get_step($seq, question_attempt $qa, $pendingstep) {
    if ($seq < $qa->get_num_steps()) {
        return $qa->get_step($seq);
    }
    if ($seq == $qa->get_num_steps() && !is_null($pendingstep)) {
        return $pendingstep;
    }
    throw new Exception('Sequence number ' . $seq . ' out of range.');
}

/**
 * Update the $SESSION->cached_opaque_state to show the current status of $question for state
 * $state.
 * @param object $question the question
 * @param object $state
 * @return mixed $SESSION->cached_opaque_state on success, a string error message on failure.
 */
function update_opaque_state(question_attempt $qa, question_attempt_step $pendingstep = null) {
    global $SESSION;

    $question = $qa->get_question();
    $targetseq = $qa->get_num_steps() - 1;
    if (!is_null($pendingstep)) {
        $targetseq += 1;
    }

    if (empty($SESSION->cached_opaque_state) ||
            empty($SESSION->cached_opaque_state->qaid) ||
            empty($SESSION->cached_opaque_state->sequencenumber)) {
        $cachestatus = 'empty';
    } else if ($SESSION->cached_opaque_state->qaid != $qa->get_database_id() ||
            $SESSION->cached_opaque_state->sequencenumber > $targetseq) {
        if (!empty($SESSION->cached_opaque_state->questionsessionid)) {
            $error = stop_question_session($SESSION->cached_opaque_state->engine,
                    $SESSION->cached_opaque_state->questionsessionid);
            if (is_string($error)) {
                unset($SESSION->cached_opaque_state);
                return $error;
            }
        }
        unset($SESSION->cached_opaque_state);
        $cachestatus = 'empty';
    } else if ($SESSION->cached_opaque_state->sequencenumber < $targetseq) {
        $cachestatus = 'catchup';
    } else {
        $cachestatus = 'good';
    }

    $resourcecache = new opaque_resource_cache($question->engineid,
            $question->remoteid, $question->remoteversion);

    if ($cachestatus == 'empty') {
        $SESSION->cached_opaque_state = new stdClass;
        $opaquestate = $SESSION->cached_opaque_state;
        $opaquestate->qaid = $qa->get_database_id();
        $opaquestate->remoteid = $question->remoteid;
        $opaquestate->remoteversion = $question->remoteversion;
        $opaquestate->engineid = $question->engineid;
        $opaquestate->nameprefix = $qa->get_field_prefix();
        $opaquestate->questionended = false;
        $opaquestate->sequencenumber = -1;
        $opaquestate->resultssequencenumber = -1;

        $engine = load_engine_def($question->engineid);
        if (is_string($engine)) {
            unset($SESSION->cached_opaque_state);
            return $engine;
        }
        $opaquestate->engine = $engine;

        $step = opaque_get_step(0, $qa, $pendingstep);
        $startreturn = start_question_session($engine, $question->remoteid,
                $question->remoteversion, $step->get_all_data(), $resourcecache->list_cached_resources());
        if (is_string($startreturn)) {
            unset($SESSION->cached_opaque_state);
            return $startreturn;
        }

        extract_stuff_from_response($opaquestate, $startreturn, $resourcecache);
        $opaquestate->sequencenumber++;
        $cachestatus = 'catchup';
    } else {
        $opaquestate = $SESSION->cached_opaque_state;
    }

    if ($cachestatus == 'catchup') {
        if ($opaquestate->sequencenumber >= $targetseq) {
            $error = stop_question_session($opaquestate->engine,
                    $opaquestate->questionsessionid);
        }
        while ($opaquestate->sequencenumber < $targetseq) {
            $step = opaque_get_step($opaquestate->sequencenumber + 1, $qa, $pendingstep);

            $processreturn = opaque_process($opaquestate->engine, $opaquestate->questionsessionid, $step->get_submitted_data());
            if (is_string($processreturn)) {
                unset($SESSION->cached_opaque_state);
                return $processreturn;
            }

            if (!empty($processreturn->results)) {
                $opaquestate->resultssequencenumber = $opaquestate->sequencenumber + 1;
                $opaquestate->results = $processreturn->results;
            }
            if ($processreturn->questionEnd) {
                $opaquestate->questionended = true;
                $opaquestate->sequencenumber = $targetseq;
                $opaquestate->xhtml = strip_omact_buttons($opaquestate->xhtml);
                unset($opaquestate->questionsessionid);
                break;
            }
            extract_stuff_from_response($opaquestate, $processreturn, $resourcecache);

            $opaquestate->sequencenumber++;
        }
        $cachestatus = 'good';
    }

    return $opaquestate;
}

/**
 * File name used to store the CSS of the question, question session id is appended.
 */
function opaque_stylesheet_filename($questionsessionid) {
    return OPAQUE_CSS_FILENAME_PREFIX . $questionsessionid . '.css';
}

/**
 * Pulls out the fields common to StartResponse and ProcessResponse.
 * @param object $opaquestate should be $SESSION->cached_opaque_state, or equivalent.
 * @param object $response a StartResponse or ProcessResponse.
 * @param object $resourcecache the resource cache for this question.
 * @return true on success, or a string error message on failure.
 */
function extract_stuff_from_response(&$opaquestate, $response, &$resourcecache) {
    global $CFG;
    static $replaces;

    if (empty($replaces)) {
        $replaces = array(
            '%%RESOURCES%%' => '', // Filled in below.
            '%%IDPREFIX%%' => '', // Filled in below.
            '%%%%' => '%%'
        );

        $strings = array('lTRYAGAIN', 'lGIVEUP', 'lNEXTQUESTION', 'lENTERANSWER', 'lCLEAR');
        foreach ($strings as $string) {
            $replaces["%%$string%%"] = get_string($string, 'qtype_opaque');
        }
    }

    // Process the XHTML, replacing the strings that need to be replaced.
    $xhtml = $response->XHTML;

    $replaces['%%RESOURCES%%'] = $resourcecache->file_url('');
    $replaces['%%IDPREFIX%%'] = $opaquestate->nameprefix;
    $xhtml = str_replace(array_keys($replaces), $replaces, $xhtml);

    // TODO this is a nasty hack. Flash uses & as a separator in the FlashVars string,
    // so we have to replce the &amp;s with %26s in this one place only. So for now
    // do it with a regexp. Longer term, it might be better to changes the file.php urls
    // so they don't contain &s.
    $xhtml = preg_replace_callback(
            '/name="FlashVars" value="TheSound=[^"]+"/',
            create_function('$matches', 'return str_replace("&amp;", "%26", $matches[0]);'),
            $xhtml);

    // Another hack to take out the next button that most OM questions include, but which does not work in Moodle.
    // Actually, we remove any non-disabled buttons, and the following script tag.
    // TODO think of a better way to do this.
    if ($opaquestate->resultssequencenumber >= 0) {
        $xhtml = strip_omact_buttons($xhtml);
    }

    $opaquestate->xhtml = $xhtml;

    // Process the CSS (only when we have a StartResponse).
    if (!empty($response->CSS)) {
        $opaquestate->cssfilename = opaque_stylesheet_filename($response->questionSession);
        $resourcecache->cache_file($opaquestate->cssfilename, 'text/css;charset=UTF-8', $response->CSS);
    }

    // Process the resources.
    $resourcecache->cache_resources($response->resources);

    // Process the other bits.
    $opaquestate->progressinfo = $response->progressInfo;
    if (!empty($response->questionSession)) {
        $opaquestate->questionsessionid = $response->questionSession;
    }

    if(!empty($response->head)) {
        $opaquestate->headXHTML = $response->head;
    }

    return true;
}

/**
 * Strip any buttons, followed by script tags, where the button has an id containing _omact_, and is not disabled.
 */
function strip_omact_buttons($xhtml) {
    return preg_replace('|<input(?:(?!disabled=)[^>])*? id="[^"]*_omact_[^"]*"(?:(?!disabled=)[^>])*?><script type="text/javascript">[^<]*</script>|', '', $xhtml);
}

/**
 * @param string $secret the secret string for this question engine.
 * @param int $userid the id of the user attempting this question.
 * @return string the passkey that needs to be sent to the quetion engine to show that
 *      we are allowed to start a question session for this user.
 */
function generate_passkey($secret, $userid) {
    return md5($secret . $userid);
}

/**
 * OpenMark relies on certain browser-specific class names to be present in the
 * HTML outside the question, in order to apply certian browser-specific layout
 * work-arounds. This function re-implements Om's browser sniffing rules. See
 * https://openmark.dev.java.net/source/browse/openmark/trunk/src/util/misc/UserAgent.java?view=markup
 * @return string class to add to the HTML.
 */
function opaque_browser_type() {
    $useragent = $_SERVER['HTTP_USER_AGENT'];

    // Filter troublemakers
    if (strpos($useragent, 'KHTML') !== false) {
        return "khtml";
    }
    if (strpos($useragent, 'Opera') !== false) {
        return "opera";
    }

    // Check version of our two supported browsers
    $matches = array();
    if (preg_match('/"^.*rv:(\d+)\\.(\d+)\D.*$"/', $useragent, $matches)) {
        return 'gecko-' . $matches[1] . '-' . $matches[2];
    }
    if (preg_match('/^.*MSIE (\d+)\\.(\d+)\D.*Windows.*$/', $useragent, $matches)) {
        return 'winie-' . $matches[1]; // Major verison only
    }

    return '';
}

/**
 * This class caches the resources belonging a particular question.
 *
 * There are synchronisation issues if two students are doing the same question at the same time.
 */
class opaque_resource_cache {
    var $folder; // Path to the folder where resources for this question are cached.
    var $metadatafolder; // Path to the folder where mime types are stored.
    var $baseurl; // initial part of the URL to link to a file in the cache.

    /**
     * Create a new opaque_resource_cache for a particular remote question.
     * @param integer $engineid the id of the question engine.
     * @param string $remoteid remote question id, as per Opaque spec.
     * @param string $remoteversion remote question version, as per Opaque spec.
     */
    function opaque_resource_cache($engineid, $remoteid, $remoteversion) {
        global $CFG;
        $folderstart = $CFG->dataroot . '/opaqueresources/' . $engineid . '/' . $remoteid . '/' . $remoteversion;
        $this->folder = $folderstart . '/files';
        if (!is_dir($this->folder)) {
            $this->mkdir_recursive($this->folder);
        }
        $this->metadatafolder = $folderstart . '/meta';
        if (!is_dir($this->metadatafolder)) {
            $this->mkdir_recursive($this->metadatafolder);
        }
        $this->baseurl = $CFG->wwwroot . '/question/type/opaque/file.php?engineid=' .
                $engineid . '&amp;remoteid=' . $remoteid .
                '&amp;remoteversion=' . $remoteversion . '&amp;filename=';
    }

    /**
     * @param string $filename the file name.
     * @return the full path of a file with the given name.
     */
    function file_path($filename) {
        return $this->folder . '/' . $filename;
    }

    /**
     * @param string $filename the file name.
     * @return the full path of a file with the given name.
     */
    function file_meta_path($filename) {
        return $this->metadatafolder . '/' . $filename;
    }

    /**
     * @param string $filename the file name.
     * @return the URL to access this file.
     */
    function file_url($filename) {
        return $this->baseurl . $filename;
    }

    /**
     * @param string $filename the file name.
     * @return the URL to access this file.
     */
    function file_mime_type($filename) {
        $metapath = $this->file_meta_path($filename);
        if (file_exists($metapath)) {
            return file_get_contents($metapath);
        }
        return mimeinfo('type', $filename);
    }

    /**
     * @param string $filename the name of the file to look for.
     * @return true if this named file is in the cache, otherwise false.
     */
    function file_in_cache($filename) {
        return file_exists($this->file_path($filename));
    }

    /**
     * Serve a file from the cache.
     * @param string $filename the file name.
     */
    function serve_file($filename) {
        if (!$this->file_in_cache($filename)) {
            header('HTTP/1.0 404 Not Found');
            header('Content-Type: text/plain;charset=UTF-8');
            echo 'File not found';
            exit;
        }
        $mimetype = $this->file_mime_type($filename);

        // Handle If-Modified-Since
        $file = $this->file_path($filename);
        $filedate = filemtime($file);
        $ifmodifiedsince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
        if($ifmodifiedsince && strtotime($ifmodifiedsince)>=$filedate) {
            header('HTTP/1.0 304 Not Modified');
            exit;
        }
        header('Last-Modified: '.gmdate('D, d M Y H:i:s',$filedate).' GMT');

//        // Send expire headers
//        $lifetime=4*60*60;
//        header('Cache-Control: max-age='.$lifetime);
//        header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');

        // Type
        header('Content-Type: ' . $mimetype);
        header('Content-Length: ' . filesize($file));

        // Output file
        $handle = fopen($file,'r');
        session_write_close(); // unlock session during fileserving
        fpassthru($handle);
        fclose($handle);
    }

    /**
     * Store a file in the cache.
     *
     * @param string $filename the name of the file to cache.
     * @param string $mimetype the type of the file to cache.
     * @param string $content the contents to write to the file.
     */
    function cache_file($filename, $mimetype, $content) {
        file_put_contents($this->file_path($filename), $content);
        file_put_contents($this->file_meta_path($filename), $mimetype);
    }

    /**
     * Add the resources from a particular response to the cache.
     * @param array $resources as returned from start or process Opaque methods.
     */
    function cache_resources($resources) {
        if(!empty($resources)) {
            foreach ($resources as $resource) {
                $mimetype = $resource->mimeType;
                if (strpos($resource->mimeType, 'text/') === 0 && !empty($resource->encoding)) {
                    $mimetype .= ';charset=' . $resource->encoding;
                }
                $this->cache_file($resource->filename, $mimetype, $resource->content);
            }
        }
    }

    /**
     * List the resources cached for this question.
     * @return array list of resource names.
     */
    function list_cached_resources() {
        $filepaths = glob($this->folder . '/*');
        if (!is_array($filepaths)) {
            // If an error occurrs, say that we have no files cached.
            $filepaths = array();
        }
        $pathlen = strlen($this->folder . '/');
        $files = array();
        foreach ($filepaths as &$filepath) {
            $file = substr($filepath, $pathlen);
            if (strpos($file, OPAQUE_CSS_FILENAME_PREFIX) !== 0) {
                $files[] = $file;
            }
        }
        return $files;
    }

    /**
     * This function exists because mkdir(folder,mode,TRUE) doesn't work on our server.
     * Safe to call even if folder already exists (checks)
     * @param string $folder Folder to create
     * @param int $mode Mode for creation (default 0755)
     * @return boolean True if folder (now) exists, false if there was a failure
     */
    function mkdir_recursive($folder, $mode='') {
        if(is_dir($folder)) {
            return true;
        }
        if ($mode == '') {
            global $CFG;
            $mode = $CFG->directorypermissions;
        }
        if(!$this->mkdir_recursive(dirname($folder),$mode)) {
            return false;
        }
        return mkdir($folder,$mode);
    }
}
?>
