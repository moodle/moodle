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
 * Python predictions processor
 *
 * @package   mlbackend_python
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mlbackend_python;

defined('MOODLE_INTERNAL') || die();

/**
 * Python predictions processor.
 *
 * @package   mlbackend_python
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processor implements  \core_analytics\classifier, \core_analytics\regressor, \core_analytics\packable {

    /**
     * The required version of the python package that performs all calculations.
     */
    const REQUIRED_PIP_PACKAGE_VERSION = '3.0.5';

    /**
     * The python package is installed in a server.
     * @var bool
     */
    protected $useserver;

    /**
     * The path to the Python bin.
     *
     * @var string
     */
    protected $pathtopython;

    /**
     * Remote server host
     * @var string
     */
    protected $host;

    /**
     * Remote server port
     * @var int
     */
    protected $port;

    /**
     * Whether to use http or https.
     * @var bool
     */
    protected $secure;

    /**
     * Server username.
     * @var string
     */
    protected $username;

    /**
     * Server password for $this->username.
     * @var string
     */
    protected $password;

    /**
     * The constructor.
     *
     */
    public function __construct() {
        global $CFG;

        $config = get_config('mlbackend_python');

        $this->useserver = !empty($config->useserver);

        if (!$this->useserver) {
            // Set the python location if there is a value.
            if (!empty($CFG->pathtopython)) {
                $this->pathtopython = $CFG->pathtopython;
            }
        } else {
            $this->host = $config->host ?? '';
            $this->port = $config->port ?? '';
            $this->secure = $config->secure ?? false;
            $this->username = $config->username ?? '';
            $this->password = $config->password ?? '';
        }
    }

    /**
     * Is the plugin ready to be used?.
     *
     * @return bool|string Returns true on success, a string detailing the error otherwise
     */
    public function is_ready() {

        if (!$this->useserver) {
            return $this->is_webserver_ready();
        } else {
            return $this->is_python_server_ready();
        }
    }

    /**
     * Checks if the python package is available in the web server executing this script.
     *
     * @return bool|string Returns true on success, a string detailing the error otherwise
     */
    protected function is_webserver_ready() {
        if (empty($this->pathtopython)) {
            $settingurl = new \moodle_url('/admin/settings.php', array('section' => 'systempaths'));
            return get_string('pythonpathnotdefined', 'mlbackend_python', $settingurl->out());
        }

        // Check the installed pip package version.
        $cmd = "{$this->pathtopython} -m moodlemlbackend.version";

        $output = null;
        $exitcode = null;
        // Execute it sending the standard error to $output.
        $result = exec($cmd . ' 2>&1', $output, $exitcode);

        if ($exitcode != 0) {
            return get_string('pythonpackagenotinstalled', 'mlbackend_python', $cmd);
        }

        $vercheck = self::check_pip_package_version($result);
        return $this->version_check_return($result, $vercheck);
    }

    /**
     * Checks if the server can be accessed.
     *
     * @return bool|string True or an error string.
     */
    protected function is_python_server_ready() {

        if (empty($this->host) || empty($this->port) || empty($this->username) || empty($this->password)) {
            return get_string('errornoconfigdata', 'mlbackend_python');
        }

        // Connection is allowed to use 'localhost' and other potentially blocked hosts/ports.
        $curl = new \curl(['ignoresecurity' => true]);
        $responsebody = $curl->get($this->get_server_url('version')->out(false));
        if ($curl->info['http_code'] !== 200) {
            return get_string('errorserver', 'mlbackend_python', $this->server_error_str($curl->info['http_code'], $responsebody));
        }

        $vercheck = self::check_pip_package_version($responsebody);
        return $this->version_check_return($responsebody, $vercheck);

    }

    /**
     * Delete the model version output directory.
     *
     * @throws \moodle_exception
     * @param string $uniqueid
     * @param string $modelversionoutputdir
     * @return null
     */
    public function clear_model($uniqueid, $modelversionoutputdir) {
        if (!$this->useserver) {
            remove_dir($modelversionoutputdir);
        } else {
            // Use the server.

            $url = $this->get_server_url('deletemodel');
            list($responsebody, $httpcode) = $this->server_request($url, 'post', ['uniqueid' => $uniqueid]);
        }
    }

    /**
     * Delete the model output directory.
     *
     * @throws \moodle_exception
     * @param string $modeloutputdir
     * @param string $uniqueid
     * @return null
     */
    public function delete_output_dir($modeloutputdir, $uniqueid) {
        if (!$this->useserver) {
            remove_dir($modeloutputdir);
        } else {

            $url = $this->get_server_url('deletemodel');
            list($responsebody, $httpcode) = $this->server_request($url, 'post', ['uniqueid' => $uniqueid]);
        }
    }

    /**
     * Trains a machine learning algorithm with the provided dataset.
     *
     * @param string $uniqueid
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function train_classification($uniqueid, \stored_file $dataset, $outputdir) {

        if (!$this->useserver) {
            // Use the local file system.

            list($result, $exitcode) = $this->exec_command('training', [$uniqueid, $outputdir,
                $this->get_file_path($dataset)], 'errornopredictresults');

        } else {
            // Use the server.

            $requestparams = ['uniqueid' => $uniqueid, 'dirhash' => $this->hash_dir($outputdir),
                'dataset' => $dataset];

            $url = $this->get_server_url('training');
            list($result, $httpcode) = $this->server_request($url, 'post', $requestparams);
        }

        if (!$resultobj = json_decode($result)) {
            throw new \moodle_exception('errorpredictwrongformat', 'analytics', '', json_last_error_msg());
        }

        if ($resultobj->status != 0) {
            $resultobj = $this->format_error_info($resultobj);
        }

        return $resultobj;
    }

    /**
     * Classifies the provided dataset samples.
     *
     * @param string $uniqueid
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function classify($uniqueid, \stored_file $dataset, $outputdir) {

        if (!$this->useserver) {
            // Use the local file system.

            list($result, $exitcode) = $this->exec_command('prediction', [$uniqueid, $outputdir,
                $this->get_file_path($dataset)], 'errornopredictresults');

        } else {
            // Use the server.

            $requestparams = ['uniqueid' => $uniqueid, 'dirhash' => $this->hash_dir($outputdir),
                'dataset' => $dataset];

            $url = $this->get_server_url('prediction');
            list($result, $httpcode) = $this->server_request($url, 'post', $requestparams);
        }

        if (!$resultobj = json_decode($result)) {
            throw new \moodle_exception('errorpredictwrongformat', 'analytics', '', json_last_error_msg());
        }

        if ($resultobj->status != 0) {
            $resultobj = $this->format_error_info($resultobj);
        }

        return $resultobj;
    }

    /**
     * Evaluates this processor classification model using the provided supervised learning dataset.
     *
     * @param string $uniqueid
     * @param float $maxdeviation
     * @param int $niterations
     * @param \stored_file $dataset
     * @param string $outputdir
     * @param  string $trainedmodeldir
     * @return \stdClass
     */
    public function evaluate_classification($uniqueid, $maxdeviation, $niterations, \stored_file $dataset,
            $outputdir, $trainedmodeldir) {
        global $CFG;

        if (!$this->useserver) {
            // Use the local file system.

            $datasetpath = $this->get_file_path($dataset);

            $params = [$uniqueid, $outputdir, $datasetpath, \core_analytics\model::MIN_SCORE,
                $maxdeviation, $niterations];

            if ($trainedmodeldir) {
                $params[] = $trainedmodeldir;
            }

            list($result, $exitcode) = $this->exec_command('evaluation', $params, 'errornopredictresults');
            if (!$resultobj = json_decode($result)) {
                throw new \moodle_exception('errorpredictwrongformat', 'analytics', '', json_last_error_msg());
            }

        } else {
            // Use the server.

            $requestparams = ['uniqueid' => $uniqueid, 'minscore' => \core_analytics\model::MIN_SCORE,
                'maxdeviation' => $maxdeviation, 'niterations' => $niterations,
                'dirhash' => $this->hash_dir($outputdir), 'dataset' => $dataset];

            if ($trainedmodeldir) {
                $requestparams['trainedmodeldirhash'] = $this->hash_dir($trainedmodeldir);
            }

            $url = $this->get_server_url('evaluation');
            list($result, $httpcode) = $this->server_request($url, 'post', $requestparams);

            if (!$resultobj = json_decode($result)) {
                throw new \moodle_exception('errorpredictwrongformat', 'analytics', '', json_last_error_msg());
            }

            // We need an extra request to get the resources generated during the evaluation process.

            // Directory to temporarly store the evaluation log zip returned by the server.
            $evaluationtmpdir = make_request_directory();
            $evaluationzippath = $evaluationtmpdir . DIRECTORY_SEPARATOR . 'evaluationlog.zip';

            $requestparams = ['uniqueid' => $uniqueid, 'dirhash' => $this->hash_dir($outputdir),
            'runid' => $resultobj->runid];

            $url = $this->get_server_url('evaluationlog');
            list($result, $httpcode) = $this->server_request($url, 'download_one', $requestparams,
                ['filepath' => $evaluationzippath]);

            $rundir = $outputdir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $resultobj->runid;
            if (!mkdir($rundir, $CFG->directorypermissions, true)) {
                throw new \moodle_exception('errorexportmodelresult', 'analytics');
            }

            $zip = new \zip_packer();
            $success = $zip->extract_to_pathname($evaluationzippath, $rundir, null, null, true);
            if (!$success) {
                $a = 'The evaluation files can not be exported to ' . $rundir;
                throw new \moodle_exception('errorpredictionsprocessor', 'analytics', '', $a);
            }

            $resultobj->dir = $rundir;
        }

        $resultobj = $this->add_extra_result_info($resultobj);

        return $resultobj;
    }

    /**
     * Exports the machine learning model.
     *
     * @throws \moodle_exception
     * @param  string $uniqueid  The model unique id
     * @param  string $modeldir  The directory that contains the trained model.
     * @return string            The path to the directory that contains the exported model.
     */
    public function export(string $uniqueid, string $modeldir) : string {

        $exporttmpdir = make_request_directory();

        if (!$this->useserver) {
            // Use the local file system.

            // We include an exporttmpdir as we want to be sure that the file is not deleted after the
            // python process finishes.
            list($exportdir, $exitcode) = $this->exec_command('export', [$uniqueid, $modeldir, $exporttmpdir],
                'errorexportmodelresult');

            if ($exitcode != 0) {
                throw new \moodle_exception('errorexportmodelresult', 'analytics');
            }

        } else {
            // Use the server.

            $requestparams = ['uniqueid' => $uniqueid, 'dirhash' => $this->hash_dir($modeldir)];

            $exportzippath = $exporttmpdir . DIRECTORY_SEPARATOR . 'export.zip';
            $url = $this->get_server_url('export');
            list($result, $httpcode) = $this->server_request($url, 'download_one', $requestparams,
                ['filepath' => $exportzippath]);

            $exportdir = make_request_directory();
            $zip = new \zip_packer();
            $success = $zip->extract_to_pathname($exportzippath, $exportdir, null, null, true);
            if (!$success) {
                throw new \moodle_exception('errorexportmodelresult', 'analytics');
            }
        }

        return $exportdir;
    }

    /**
     * Imports the provided machine learning model.
     *
     * @param  string $uniqueid The model unique id
     * @param  string $modeldir  The directory that will contain the trained model.
     * @param  string $importdir The directory that contains the files to import.
     * @return bool Success
     */
    public function import(string $uniqueid, string $modeldir, string $importdir) : bool {

        if (!$this->useserver) {
            // Use the local file system.

            list($result, $exitcode) = $this->exec_command('import', [$uniqueid, $modeldir, $importdir],
                'errorimportmodelresult');

            if ($exitcode != 0) {
                throw new \moodle_exception('errorimportmodelresult', 'analytics');
            }

        } else {
            // Use the server.

            // Zip the $importdir to send a single file.
            $importzipfile = $this->zip_dir($importdir);
            if (!$importzipfile) {
                // There was an error zipping the directory.
                throw new \moodle_exception('errorimportmodelresult', 'analytics');
            }

            $requestparams = ['uniqueid' => $uniqueid, 'dirhash' => $this->hash_dir($modeldir),
                'importzip' => curl_file_create($importzipfile, null, 'import.zip')];
            $url = $this->get_server_url('import');
            list($result, $httpcode) = $this->server_request($url, 'post', $requestparams);
        }

        return (bool)$result;
    }

    /**
     * Train this processor regression model using the provided supervised learning dataset.
     *
     * @throws new \coding_exception
     * @param string $uniqueid
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function train_regression($uniqueid, \stored_file $dataset, $outputdir) {
        throw new \coding_exception('This predictor does not support regression yet.');
    }

    /**
     * Estimates linear values for the provided dataset samples.
     *
     * @throws new \coding_exception
     * @param string $uniqueid
     * @param \stored_file $dataset
     * @param mixed $outputdir
     * @return void
     */
    public function estimate($uniqueid, \stored_file $dataset, $outputdir) {
        throw new \coding_exception('This predictor does not support regression yet.');
    }

    /**
     * Evaluates this processor regression model using the provided supervised learning dataset.
     *
     * @throws new \coding_exception
     * @param string $uniqueid
     * @param float $maxdeviation
     * @param int $niterations
     * @param \stored_file $dataset
     * @param string $outputdir
     * @param  string $trainedmodeldir
     * @return \stdClass
     */
    public function evaluate_regression($uniqueid, $maxdeviation, $niterations, \stored_file $dataset,
            $outputdir, $trainedmodeldir) {
        throw new \coding_exception('This predictor does not support regression yet.');
    }

    /**
     * Returns the path to the dataset file.
     *
     * @param \stored_file $file
     * @return string
     */
    protected function get_file_path(\stored_file $file) {
        // From moodle filesystem to the local file system.
        // This is not ideal, but there is no read access to moodle filesystem files.
        return $file->copy_content_to_temp('core_analytics');
    }

    /**
     * Check that the given package version can be used and return the error status.
     *
     * When evaluating the version, we assume the sematic versioning scheme as described at
     * https://semver.org/.
     *
     * @param string $actual The actual Python package version
     * @param string $required The required version of the package
     * @return int -1 = actual version is too low, 1 = actual version too high, 0 = actual version is ok
     */
    public static function check_pip_package_version($actual, $required = self::REQUIRED_PIP_PACKAGE_VERSION) {

        if (empty($actual)) {
            return -1;
        }

        if (version_compare($actual, $required, '<')) {
            return -1;
        }

        $parts = explode('.', $required);
        $requiredapiver = reset($parts);

        $parts = explode('.', $actual);
        $actualapiver = reset($parts);

        if ($requiredapiver > 0 || $actualapiver > 1) {
            if (version_compare($actual, $requiredapiver + 1, '>=')) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * Executes the specified module.
     *
     * @param  string $modulename
     * @param  array  $params
     * @param  string $errorlangstr
     * @return array [0] is the result body and [1] the exit code.
     */
    protected function exec_command(string $modulename, array $params, string $errorlangstr) {

        $cmd = $this->pathtopython . ' -m moodlemlbackend.' . $modulename . ' ';
        foreach ($params as $param) {
            $cmd .= escapeshellarg($param) . ' ';
        }

        if (!PHPUNIT_TEST && CLI_SCRIPT) {
            debugging($cmd, DEBUG_DEVELOPER);
        }

        $output = null;
        $exitcode = null;
        $result = exec($cmd, $output, $exitcode);

        if (!$result) {
            throw new \moodle_exception($errorlangstr, 'analytics');
        }

        return [$result, $exitcode];
    }

    /**
     * Formats the errors and info in a single info string.
     *
     * @param  \stdClass $resultobj
     * @return \stdClass
     */
    private function format_error_info(\stdClass $resultobj) {
        if (!empty($resultobj->errors)) {
            $errors = $resultobj->errors;
            if (is_array($errors)) {
                $errors = implode(', ', $errors);
            }
        } else if (!empty($resultobj->info)) {
            // Show info if no errors are returned.
            $errors = $resultobj->info;
            if (is_array($errors)) {
                $errors = implode(', ', $errors);
            }
        }
        $resultobj->info = array(get_string('errorpredictionsprocessor', 'analytics', $errors));

        return $resultobj;
    }

    /**
     * Returns the url to the python ML server.
     *
     * @param  string|null $path
     * @return \moodle_url
     */
    private function get_server_url(?string $path = null) {
        $protocol = !empty($this->secure) ? 'https' : 'http';
        $url = $protocol . '://' . rtrim($this->host, '/');
        if (!empty($this->port)) {
            $url .= ':' . $this->port;
        }

        if ($path) {
            $url .= '/' . $path;
        }

        return new \moodle_url($url);
    }

    /**
     * Sends a request to the python ML server.
     *
     * @param  \moodle_url      $url            The requested url in the python ML server
     * @param  string           $method         The curl method to use
     * @param  array            $requestparams  Curl request params
     * @param  array|null       $options        Curl request options
     * @return array                            [0] for the response body and [1] for the http code
     */
    protected function server_request($url, string $method, array $requestparams, ?array $options = null) {

        if ($method !== 'post' && $method !== 'get' && $method !== 'download_one') {
            throw new \coding_exception('Incorrect request method provided. Only "get", "post" and "download_one"
                actions are available.');
        }

        // Connection is allowed to use 'localhost' and other potentially blocked hosts/ports.
        $curl = new \curl(['ignoresecurity' => true]);

        $authorization = $this->username . ':' . $this->password;
        $curl->setHeader('Authorization: Basic ' . base64_encode($authorization));

        $responsebody = $curl->{$method}($url, $requestparams, $options);

        if ($curl->info['http_code'] !== 200) {
            throw new \moodle_exception('errorserver', 'mlbackend_python', '',
                $this->server_error_str($curl->info['http_code'], $responsebody));
        }

        return [$responsebody, $curl->info['http_code']];
    }

    /**
     * Adds extra information to results info.
     *
     * @param  \stdClass $resultobj
     * @return \stdClass
     */
    protected function add_extra_result_info(\stdClass $resultobj): \stdClass {

        if (!empty($resultobj->dir)) {
            $dir = $resultobj->dir . DIRECTORY_SEPARATOR . 'tensor';
            $resultobj->info[] = get_string('tensorboardinfo', 'mlbackend_python', $dir);
        }
        return $resultobj;
    }

    /**
     * Returns the proper return value for the version checking.
     *
     * @param  string $actual   Actual moodlemlbackend version
     * @param  int    $vercheck Version checking result
     * @return true|string      Returns true on success, a string detailing the error otherwise
     */
    private function version_check_return($actual, $vercheck) {

        if ($vercheck === 0) {
            return true;
        }

        if ($actual) {
            $a = [
                'installed' => $actual,
                'required' => self::REQUIRED_PIP_PACKAGE_VERSION,
            ];

            if ($vercheck < 0) {
                return get_string('packageinstalledshouldbe', 'mlbackend_python', $a);

            } else if ($vercheck > 0) {
                return get_string('packageinstalledtoohigh', 'mlbackend_python', $a);
            }
        }

        if (!$this->useserver) {
            $cmd = "{$this->pathtopython} -m moodlemlbackend.version";
        } else {
            // We can't not know which is the python bin in the python ML server, the most likely
            // value is 'python'.
            $cmd = "python -m moodlemlbackend.version";
        }
        return get_string('pythonpackagenotinstalled', 'mlbackend_python', $cmd);
    }

    /**
     * Hashes the provided dir as a string.
     *
     * @param  string $dir Directory path
     * @return string Hash
     */
    private function hash_dir(string $dir) {
        return md5($dir);
    }

    /**
     * Zips the provided directory.
     *
     * @param  string $dir Directory path
     * @return string The zip filename
     */
    private function zip_dir(string $dir) {

        $ziptmpdir = make_request_directory();
        $ziptmpfile = $ziptmpdir . DIRECTORY_SEPARATOR . 'mlbackend.zip';

        $files = get_directory_list($dir);
        $zipfiles = [];
        foreach ($files as $file) {
            $fullpath = $dir . DIRECTORY_SEPARATOR . $file;
            // Use the relative path to the file as the path in the zip.
            $zipfiles[$file] = $fullpath;
        }

        $zip = new \zip_packer();
        if (!$zip->archive_to_pathname($zipfiles, $ziptmpfile)) {
            return false;
        }

        return $ziptmpfile;
    }

    /**
     * Error string for httpcode !== 200
     *
     * @param int       $httpstatuscode The HTTP status code
     * @param string    $responsebody   The body of the response
     */
    private function server_error_str(int $httpstatuscode, string $responsebody): string {
        return 'HTTP status code ' . $httpstatuscode . ': ' . $responsebody;
    }
}
