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
 * \mod_hvp\framework class
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_hvp;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../autoloader.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/adminlib.php');

/**
 * Moodle's implementation of the H5P framework interface.
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @SuppressWarnings(PHPMD)
 */
class framework implements \H5PFrameworkInterface {

    /**
     * Get type of hvp instance
     *
     * @param string $type Type of hvp instance to get
     * @return \H5PContentValidator|\H5PCore|\H5PStorage|\H5PValidator|\mod_hvp\framework|\H5peditor
     */
    public static function instance($type = null) {
        global $CFG;
        static $interface, $core, $editor, $editorinterface, $editorajaxinterface;

        if (!isset($interface)) {
            $interface = new \mod_hvp\framework();

            // Support alternate file storage class defined in $CFG.
            if (!empty($CFG->mod_hvp_file_storage_class)) {
                $fsclass = $CFG->mod_hvp_file_storage_class;
            } else {
                $fsclass = '\mod_hvp\file_storage';
            }

            $fs = new $fsclass();

            $context = \context_system::instance();
            $root = view_assets::getsiteroot();
            $url = "{$root}/pluginfile.php/{$context->id}/mod_hvp";

            $language = self::get_language();

            $export = !(isset($CFG->mod_hvp_export) && $CFG->mod_hvp_export === '0');

            $core = new \H5PCore($interface, $fs, $url, $language, $export);
            $core->aggregateAssets = !(isset($CFG->mod_hvp_aggregate_assets) && $CFG->mod_hvp_aggregate_assets === '0');
        }

        switch ($type) {
            case 'validator':
                return new \H5PValidator($interface, $core);
            case 'storage':
                return new \H5PStorage($interface, $core);
            case 'contentvalidator':
                return new \H5PContentValidator($interface, $core);
            case 'interface':
                return $interface;
            case 'editor':
                if (empty($editorinterface)) {
                    $editorinterface = new \mod_hvp\editor_framework();
                }

                if (empty($editorajaxinterface)) {
                    $editorajaxinterface = new editor_ajax();
                }

                if (empty($editor)) {
                    $editor = new \H5peditor($core, $editorinterface, $editorajaxinterface);
                }
                return $editor;
            case 'core':
            default:
                return $core;
        }
    }

    /**
     * Check if the current user has editor access, if not then return the
     * given error message.
     *
     * @param string $error
     * @return boolean
     */
    public static function has_editor_access($error) {
        $context = \context::instance_by_id(required_param('contextId', PARAM_RAW));
        $cap = ($context->contextlevel === CONTEXT_COURSE ? 'addinstance' : 'manage');

        if (!has_capability("mod/hvp:$cap", $context)) {
            \H5PCore::ajaxError(get_string($error, 'hvp'));
            http_response_code(403);
            return false;
        }

        return true;
    }

    /**
     * Get current H5P language code.
     *
     * @return string Language Code
     */
    public static function get_language() {
        static $map;

        if (empty($map)) {
            // Create mapping for "converting" language codes.
            $map = array(
                'no' => 'nb'
            );
        }

        // Get current language in Moodle.
        $language = str_replace('_', '-', strtolower(\current_language()));

        // Try to map.
        return isset($map[$language]) ? $map[$language] : $language;
    }

    /**
     * Implements getPlatformInfo
     */
    // @codingStandardsIgnoreLine
    public function getPlatformInfo() {
        global $CFG;

        return array(
            'name' => 'Moodle',
            'version' => $CFG->version,
            'h5pVersion' => get_component_version('mod_hvp'),
        );
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function fetchExternalData($url, $data = null, $blocking = true, $stream = null, $alldata = false, $headers = array(), $files = array(), $method = 'POST') {
        global $CFG;

        if (!empty($files)) {
            $curldata = array();
            foreach ($data as $key => $value) {
                if (empty($value)) {
                    continue; // Skip empty values.
                }
                if (is_array($value)) {
                    foreach ($value as $i => $subvalue) {
                        $curldata["{$key}[{$i}]"] = $subvalue;
                    }
                } else {
                    $curldata[$key] = $value;
                }
            }

            foreach ($files as $name => $file) {
                if ($file === null) {
                    continue;
                } else if (is_array($file['name'])) {
                    // Array of files uploaded (multiple).
                    for ($i = 0; $i < count($file['name']); $i ++) {
                        $curldata["{$name}[{$i}]"] = new \CurlFile($file['tmp_name'][$i], $file['type'][$i], $file['name'][$i]);
                    }
                } else {
                    // Single file.
                    $curldata[$name] = new \CurlFile($file['tmp_name'], $file['type'], $file['name']);
                }
            }
        } else if (!empty($data)) {
            // Application/x-www-form-urlencoded.
            $curldata = format_postdata_for_curlcall($data);
        }

        $options = array(
            'CURLOPT_SSL_VERIFYPEER' => true,
            'CURLOPT_CONNECTTIMEOUT' => 20,
            'CURLOPT_FOLLOWLOCATION' => 1,
            'CURLOPT_MAXREDIRS'      => 5,
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_NOBODY'         => false,
            'CURLOPT_TIMEOUT'        => 300,
        );

        if ($stream !== null) {
            // Download file.
            @set_time_limit(0);

            // Generate local tmp file path.
            $localfolder = make_temp_directory(uniqid('hvp-'));
            $localpath = $localfolder . '.h5p';

            // Add folder and file paths to H5P Core.
            $interface = self::instance('interface');
            $interface->getUploadedH5pFolderPath($localfolder);
            $interface->getUploadedH5pPath($localpath);

            $stream = fopen($localpath, 'w');
            $options['CURLOPT_FILE'] = $stream;
        }

        $curl = new curl();

        // Massage headers to work with curl.
        foreach ($headers as $key => $value) {
            $curl->setHeader(is_numeric($key) ? $value : "$key: $value");
        }

        if (empty($data) || $method === 'GET') {
            $response = $curl->get($url, array(), $options);
        } else if ($method === 'POST') {
            $response = $curl->post($url, $curldata, $options);
        } else if ($method === 'PUT') {
            $response = $curl->put($url, $curldata, $options);
        }

        if ($stream !== null) {
            fclose($stream);
            @chmod($localpath, $CFG->filepermissions);
        }

        $errorno = $curl->get_errno();
        // Error handling.
        if ($errorno) {
            if ($alldata) {
                $response = null;
            } else {
                $this->setErrorMessage($response, 'failed-fetching-external-data');

                return false;
            }
        }

        if ($alldata) {
            $info = $curl->get_info();

            return [
                'status'  => intval($info['http_code']),
                'data'    => empty($response) ? null : $response,
                'headers' => $curl->get_raw_response(),
            ];
        } else {
            return $response;
        }
    }

    /**
     * Implements setLibraryTutorialUrl
     *
     * Set the tutorial URL for a library. All versions of the library is set
     *
     * @param string $libraryname
     * @param string $url
     */
    // @codingStandardsIgnoreLine
    public function setLibraryTutorialUrl($libraryname, $url) {
        global $DB;

        $DB->execute("UPDATE {hvp_libraries} SET tutorial_url = ? WHERE machine_name = ?", array($url, $libraryname));
    }

    /**
     * Implements setErrorMessage
     *
     * @param string $message translated error message
     * @param string $code
     */
    // @codingStandardsIgnoreLine
    public function setErrorMessage($message, $code = null) {
        if ($message !== null) {
            self::messages('error', $message, $code);
        }
    }

    /**
     * Implements setInfoMessage
     */
    // @codingStandardsIgnoreLine
    public function setInfoMessage($message) {
        if ($message !== null) {
            self::messages('info', $message);
        }
    }

    /**
     * Store messages until they can be printed to the current user
     *
     * @param string $type Type of messages, e.g. 'info' or 'error'
     * @param string $newmessage Optional
     * @param string $code
     * @return array Array of stored messages
     */
    public static function messages($type, $newmessage = null, $code = null) {
        static $m = 'mod_hvp_messages';

        if ($newmessage === null) {
            // Return and reset messages.
            $messages = isset($_SESSION[$m][$type]) ? $_SESSION[$m][$type] : array();
            unset($_SESSION[$m][$type]);
            if (empty($_SESSION[$m])) {
                unset($_SESSION[$m]);
            }
            return $messages;
        }

        // We expect to get out an array of strings when getting info
        // and an array of objects when getting errors for consistency across platforms.
        // This implementation should be improved for consistency across the data type returned here.
        if ($type === 'error') {
            $_SESSION[$m][$type][] = (object)array(
                'code' => $code,
                'message' => $newmessage
            );
        } else {
            $_SESSION[$m][$type][] = $newmessage;
        }
    }

    /**
     * Simple print of given messages.
     *
     * @param string $type One of error|info
     * @param array $messages
     */
    // @codingStandardsIgnoreLine
    public static function printMessages($type, $messages) {
        global $OUTPUT;
        foreach ($messages as $message) {
            $out = $type === 'error' ? $message->message : $message;
            print $OUTPUT->notification($out, ($type === 'error' ? 'notifyproblem' : 'notifymessage'));
        }
    }

    /**
     * Implements getMessages
     */
    // @codingStandardsIgnoreLine
    public function getMessages($type) {
        return self::messages($type);
    }

    /**
     * Implements t
     */
    public function t($message, $replacements = array()) {
        static $translationsmap;

        if (empty($translationsmap)) {
            // Create mapping.
            // @codingStandardsIgnoreStart
            $translationsmap = [
                'Your PHP version does not support ZipArchive.' => 'noziparchive',
                'The file you uploaded is not a valid HTML5 Package (It does not have the .h5p file extension)' => 'noextension',
                'The file you uploaded is not a valid HTML5 Package (We are unable to unzip it)' => 'nounzip',
                'Could not parse the main h5p.json file' => 'noparse',
                'The main h5p.json file is not valid' => 'nojson',
                'Invalid content folder' => 'invalidcontentfolder',
                'Could not find or parse the content.json file' => 'nocontent',
                'Library directory name must match machineName or machineName-majorVersion.minorVersion (from library.json). (Directory: %directoryName , machineName: %machineName, majorVersion: %majorVersion, minorVersion: %minorVersion)' => 'librarydirectoryerror',
                'A valid content folder is missing' => 'missingcontentfolder',
                'A valid main h5p.json file is missing' => 'invalidmainjson',
                'Missing required library @library' => 'missinglibrary',
                "Note that the libraries may exist in the file you uploaded, but you're not allowed to upload new libraries. Contact the site administrator about this." => 'missinguploadpermissions',
                'Invalid library name: %name' => 'invalidlibraryname',
                'Could not find library.json file with valid json format for library %name' => 'missinglibraryjson',
                'Invalid semantics.json file has been included in the library %name' => 'invalidsemanticsjson',
                'Invalid language file %file in library %library' => 'invalidlanguagefile',
                'Invalid language file %languageFile has been included in the library %name' => 'invalidlanguagefile2',
                'The file "%file" is missing from library: "%name"' => 'missinglibraryfile',
                'The system was unable to install the <em>%component</em> component from the package, it requires a newer version of the H5P plugin. This site is currently running version %current, whereas the required version is %required or higher. You should consider upgrading and then try again.' => 'missingcoreversion',
                "Invalid data provided for %property in %library. Boolean expected." => 'invalidlibrarydataboolean',
                "Invalid data provided for %property in %library" => 'invalidlibrarydata',
                "Can't read the property %property in %library" => 'invalidlibraryproperty',
                'The required property %property is missing from %library' => 'missinglibraryproperty',
                'Illegal option %option in %library' => 'invalidlibraryoption',
                'Added %new new H5P library and updated %old old one.' => 'addedandupdatedss',
                'Added %new new H5P library and updated %old old ones.' => 'addedandupdatedsp',
                'Added %new new H5P libraries and updated %old old one.' => 'addedandupdatedps',
                'Added %new new H5P libraries and updated %old old ones.' => 'addedandupdatedpp',
                'Added %new new H5P library.' => 'addednewlibrary',
                'Added %new new H5P libraries.' => 'addednewlibraries',
                'Updated %old H5P library.' =>  'updatedlibrary',
                'Updated %old H5P libraries.' => 'updatedlibraries',
                'Missing dependency @dep required by @lib.' => 'missingdependency',
                'Provided string is not valid according to regexp in semantics. (value: \"%value\", regexp: \"%regexp\")' => 'invalidstring',
                'File "%filename" not allowed. Only files with the following extensions are allowed: %files-allowed.' => 'invalidfile',
                'Invalid selected option in multi-select.' => 'invalidmultiselectoption',
                'Invalid selected option in select.' => 'invalidselectoption',
                'H5P internal error: unknown content type "@type" in semantics. Removing content!' => 'invalidsemanticstype',
                'Copyright information' => 'copyrightinfo',
                'Title' => 'title',
                'Author' => 'author',
                'Year(s)' => 'years',
                'Year' => 'year',
                'Source' => 'source',
                'License' => 'license',
                'Undisclosed' => 'undisclosed',
                'Attribution 4.0' => 'attribution',
                'Attribution-ShareAlike 4.0' => 'attributionsa',
                'Attribution-NoDerivs 4.0' => 'attributionnd',
                'Attribution-NonCommercial 4.0' => 'attributionnc',
                'Attribution-NonCommercial-ShareAlike 4.0' => 'attributionncsa',
                'Attribution-NonCommercial-NoDerivs 4.0' => 'attributionncnd',
                'Attribution' => 'noversionattribution',
                'Attribution-ShareAlike' => 'noversionattributionsa',
                'Attribution-NoDerivs' => 'noversionattributionnd',
                'Attribution-NonCommercial' => 'noversionattributionnc',
                'Attribution-NonCommercial-ShareAlike' => 'noversionattributionncsa',
                'Attribution-NonCommercial-NoDerivs' => 'noversionattributionncnd',
                'General Public License v3' => 'gpl',
                'Public Domain' => 'pd',
                'Public Domain Dedication and Licence' => 'pddl',
                'Public Domain Mark' => 'pdm',
                'Public Domain Mark (PDM)' => 'pdm',
                'Copyright' => 'copyrightstring',
                'Unable to create directory.' => 'unabletocreatedir',
                'Unable to get field type.' => 'unabletogetfieldtype',
                "File type isn't allowed." => 'filetypenotallowed',
                'Invalid field type.' => 'invalidfieldtype',
                'Invalid image file format. Use jpg, png or gif.' => 'invalidimageformat',
                'File is not an image.' => 'filenotimage',
                'Invalid audio file format. Use mp3 or wav.' => 'invalidaudioformat',
                'Invalid video file format. Use mp4 or webm.' => 'invalidvideoformat',
                'Could not save file.' => 'couldnotsave',
                'Could not copy file.' => 'couldnotcopy',
                'The mbstring PHP extension is not loaded. H5P need this to function properly' => 'missingmbstring',
                'The version of the H5P library %machineName used in this content is not valid. Content contains %contentLibrary, but it should be %semanticsLibrary.' => 'wrongversion',
                'The H5P library %library used in the content is not valid' => 'invalidlibrarynamed',
                'Your PHP version is outdated. H5P requires version 5.2 to function properly. Version 5.6 or later is recommended.' => 'oldphpversion',
                'Your PHP max upload size is quite small. With your current setup, you may not upload files larger than %number MB. This might be a problem when trying to upload H5Ps, images and videos. Please consider to increase it to more than 5MB.' => 'maxuploadsizetoosmall',
                'Your PHP max post size is quite small. With your current setup, you may not upload files larger than %number MB. This might be a problem when trying to upload H5Ps, images and videos. Please consider to increase it to more than 5MB' => 'maxpostsizetoosmall',
                'Your server does not have SSL enabled. SSL should be enabled to ensure a secure connection with the H5P hub.' => 'sslnotenabled',
                'H5P hub communication has been disabled because one or more H5P requirements failed.' => 'hubcommunicationdisabled',
                'When you have revised your server setup you may re-enable H5P hub communication in H5P Settings.' => 'reviseserversetupandretry',
                'A problem with the server write access was detected. Please make sure that your server can write to your data folder.' => 'nowriteaccess',
                'Your PHP max upload size is bigger than your max post size. This is known to cause issues in some installations.' => 'uploadsizelargerthanpostsize',
                'Library cache was successfully updated!' => 'ctcachesuccess',
                'No content types were received from the H5P Hub. Please try again later.' => 'ctcachenolibraries',
                "Couldn't communicate with the H5P Hub. Please try again later." => 'ctcacheconnectionfailed',
                'The hub is disabled. You can re-enable it in the H5P settings.' => 'hubisdisabled',
                'File not found on server. Check file upload settings.' => 'filenotfoundonserver',
                'Invalid security token.' => 'invalidtoken',
                'No content type was specified.' => 'nocontenttype',
                'The chosen content type is invalid.' => 'invalidcontenttype',
                'You do not have permission to install content types. Contact the administrator of your site.' => 'installdenied',
                'You do not have permission to install content types.' => 'installdenied',
                'Validating h5p package failed.' => 'validatingh5pfailed',
                'Failed to download the requested H5P.' => 'failedtodownloadh5p',
                'A post message is required to access the given endpoint' => 'postmessagerequired',
                'Could not get posted H5P.' => 'invalidh5ppost',
                'Site could not be registered with the hub. Please contact your site administrator.' => 'sitecouldnotberegistered',
                'The H5P Hub has been disabled until this problem can be resolved. You may still upload libraries through the "H5P Libraries" page.' => 'hubisdisableduploadlibraries',
                'Your site was successfully registered with the H5P Hub.' => 'successfullyregisteredwithhub',
                'You have been provided a unique key that identifies you with the Hub when receiving new updates. The key is available for viewing in the "H5P Settings" page.' => 'sitekeyregistered',
                'Fullscreen' => 'fullscreen',
                'Disable fullscreen' => 'disablefullscreen',
                'Download' => 'download',
                'Rights of use' => 'copyright',
                'Embed' => 'embed',
                'Size' => 'size',
                'Show advanced' => 'showadvanced',
                'Hide advanced' => 'hideadvanced',
                'Include this script on your website if you want dynamic sizing of the embedded content:' => 'resizescript',
                'Close' => 'close',
                'Thumbnail' => 'thumbnail',
                'No copyright information available for this content.' => 'nocopyright',
                'Download this content as a H5P file.' => 'downloadtitle',
                'View copyright information for this content.' => 'copyrighttitle',
                'View the embed code for this content.' => 'embedtitle',
                'Visit H5P.org to check out more cool content.' => 'h5ptitle',
                'This content has changed since you last used it.' => 'contentchanged',
                "You'll be starting over." => 'startingover',
                'by' => 'by',
                'Show more' => 'showmore',
                'Show less' => 'showless',
                'Sublevel' => 'sublevel',
                'Confirm action' => 'confirmdialogheader',
                'Please confirm that you wish to proceed. This action is not reversible.' => 'confirmdialogbody',
                'Cancel' => 'cancellabel',
                'Confirm' => 'confirmlabel',
                '4.0 International' => 'licenseCC40',
                '3.0 Unported' => 'licenseCC30',
                '2.5 Generic' => 'licenseCC25',
                '2.0 Generic' => 'licenseCC20',
                '1.0 Generic' => 'licenseCC10',
                'General Public License' => 'licenseGPL',
                'Version 3' => 'licenseV3',
                'Version 2' => 'licenseV2',
                'Version 1' => 'licenseV1',
                'CC0 1.0 Universal (CC0 1.0) Public Domain Dedication' => 'licenseCC010',
                'CC0 1.0 Universal' => 'licenseCC010U',
                'License Version' => 'licenseversion',
                'Creative Commons' => 'creativecommons',
                'Attribution' => 'ccattribution',
                'Attribution (CC BY)' => 'ccattribution',
                'Attribution-ShareAlike' => 'ccattributionsa',
                'Attribution-ShareAlike (CC BY-SA)' => 'ccattributionsa',
                'Attribution-NoDerivs' => 'ccattributionnd',
                'Attribution-NoDerivs (CC BY-ND)' => 'ccattributionnd',
                'Attribution-NonCommercial' => 'ccattributionnc',
                'Attribution-NonCommercial (CC BY-NC)' => 'ccattributionnc',
                'Attribution-NonCommercial-ShareAlike' => 'ccattributionncsa',
                'Attribution-NonCommercial-ShareAlike (CC BY-NC-SA)' => 'ccattributionncsa',
                'Attribution-NonCommercial-NoDerivs' => 'ccattributionncnd',
                'Attribution-NonCommercial-NoDerivs (CC BY-NC-ND)' => 'ccattributionncnd',
                'Public Domain Dedication' => 'ccpdd',
                'Public Domain Dedication (CC0)' => 'ccpdd',
                'Years (from)' => 'yearsfrom',
                'Years (to)' => 'yearsto',
                "Author's name" => 'authorname',
                "Author's role" => 'authorrole',
                'Editor' => 'editor',
                'Licensee' => 'licensee',
                'Originator' => 'originator',
                'Any additional information about the license' => 'additionallicenseinfo',
                'License Extras' => 'licenseextras',
                'Changelog' => 'changelog',
                'Content Type' => 'contenttype',
                'Question' => 'question',
                'Date' => 'date',
                'Changed by' => 'changedby',
                'Description of change' => 'changedescription',
                'Photo cropped, text changed, etc.' => 'changeplaceholder',
                'Additional Information' => 'additionalinfo',
                'Author comments' => 'authorcomments',
                'Comments for the editor of the content (This text will not be published as a part of copyright info)' => 'authorcommentsdescription',
                'Reuse' => 'reuse',
                'Reuse Content' => 'reusecontent',
                'Reuse this content.' => 'reusedescription',
                'Content is copied to the clipboard' => 'contentcopied',
                'Connection lost. Results will be stored and sent when you regain connection.' => 'connectionlost',
                'Connection reestablished.' => 'connectionreestablished',
                'Attempting to submit stored results.' => 'resubmitscores',
                'Your connection to the server was lost' => 'offlinedialogheader',
                'We were unable to send information about your completion of this task. Please check your internet connection.' => 'offlinedialogbody',
                'Retrying in :num....' => 'offlinedialogretrymessage',
                'Retry now' => 'offlinedialogretrybuttonlabel',
                'Successfully submitted results.' => 'offlinesuccessfulsubmit',
                'Sharing <strong>:title</strong>' => 'maintitle',
                'Edit info for <strong>:title</strong>' => 'editinfotitle',
                'Back' => 'back',
                'Next' => 'next',
                'Review info' => 'reviewinfo',
                'Share' => 'share',
                'Save changes' => 'savechanges',
                'Register on the H5P Hub' => 'registeronhub',
                'Required Info' => 'requiredinfo',
                'Optional Info' => 'optionalinfo',
                'Review & Share' => 'reviewandshare',
                'Review & Save' => 'reviewandsave',
                'Shared' => 'shared',
                'Step :step of :total' => 'currentstep',
                'All content details can be edited after sharing' => 'sharingnote',
                'Select a license for your content' => 'licensedescription',
                'Select a license version' => 'licenseversiondescription',
                'Disciplines' => 'disciplinelabel',
                'You can select multiple disciplines' => 'disciplinedescription',
                'You can select up to :numDisciplines disciplines' => 'disciplinelimitreachedmessage',
                'Type to search for disciplines' =>'searchplaceholder',
                'in' => 'in',
                'Dropdown button' => 'dropdownbutton',
                'Remove :chip from the list' => 'removechip',
                'Add keywords' => 'keywordsplaceholder',
                'Keywords' => 'keywords',
                'You can add multiple keywords separated by commas. Press "Enter" or "Add" to confirm keywords' => 'keywordsdescription',
                'Alt text' => 'alttext',
                'Please review the info below before you share' => 'reviewmessage',
                'Sub-content (images, questions etc.) will be shared under :license unless otherwise specified in the authoring tool' => 'subcontentwarning',
                'Disciplines' => 'disciplines',
                'Short description' => 'shortdescription',
                'Long description' => 'longdescription',
                'Icon' => 'icon',
                'Screenshots' => 'screenshots',
                'Help me choose a license' => 'helpchoosinglicense',
                'Share failed.' => 'sharefailed',
                'Editing failed.' => 'editingfailed',
                'Something went wrong, please try to share again.' => 'sharetryagain',
                'Please wait...' => 'pleasewait',
                'Language' => 'language',
                'Level' => 'level',
                'Short description of your content' => 'shortdescriptionplaceholder',
                'Long description of your content' => 'longdescriptionplaceholder',
                'Description' => 'description',
                '640x480px. If not selected content will use category icon' => 'icondescription',
                'Add up to five screenshots of your content' => 'screenshotsdescription',
                'Submitted!' => 'submitted',
                'Is now submitted to H5P Hub' => 'isnowsubmitted',
                'A change has been submited for' => 'changehasbeensubmitted',
                'Your content will normally be available in the Hub within one business day.' => 'contentavailable',
                'Your content will update soon' => 'contentupdatesoon',
                'Content License Info' => 'contentlicensetitle',
                'Click on a specific license to get info about proper usage' => 'licensedialogdescription',
                'Publisher' => 'publisherfieldtitle',
                'This will display as the "Publisher name" on shared content' => 'publisherfielddescription',
                'Email Address' => 'emailaddress',
                'Publisher description' => 'publisherdescription',
                'This will be displayed under "Publisher info" on shared content' => 'publisherdescriptiontext',
                'Contact Person' => 'contactperson',
                'Phone' => 'phone',
                'Address' => 'address',
                'City' => 'city',
                'Zip' => 'zip',
                'Country' => 'country',
                'Organization logo or avatar' => 'logouploadtext',
                'I accept the <a href=":url" target="_blank">terms of use</a>' => 'acceptterms',
                'You have successfully registered an account on the H5P Hub' => 'successfullyregistred',
                'You account details can be changed' => 'successfullyregistreddescription',
                'here' => 'accountdetailslinktext',
                'H5P Hub Registration' => 'registrationtitle',
                'An error occurred' => 'registrationfailed',
                'We were not able to create an account at this point. Something went wrong. Try again later.' => 'registrationfaileddescription',
                ':length is the maximum number of characters' => 'maxlength',
                'Keyword already exists!' => 'keywordexists',
                'License details' => 'licensedetails',
                'Remove' => 'remove',
                'Remove image' => 'removeimage',
                'Cancel sharing' => 'cancelpublishconfirmationdialogtitle',
                'Are you sure you want to cancel the sharing process?' => 'cancelpublishconfirmationdialogdescription',
                'No' => 'cancelpublishconfirmationdialogcancelbuttontext',
                'Yes' => 'cancelpublishconfirmationdialogconfirmbuttontext',
                'Add' => 'add',
                'Save account settings' => 'updateregistrationonhub',
                'Your H5P Hub account settings have successfully been changed' => 'successfullyupdated',
                'One of the files inside the package exceeds the maximum file size allowed. (%file %used > %max)' => 'fileexceedsmaxsize',
                'The total size of the unpacked files exceeds the maximum size allowed. (%used > %max)' => 'unpackedfilesexceedsmaxsize',
                'Unable to read file from the package: %fileName' => 'couldnotreadfilefromzip',
                'Unable to parse JSON from the package: %fileName' => 'couldnotparsejsonfromzip',
                'Could not parse post data.' => 'couldnotparsepostdata',
                'The mbstring PHP extension is not loaded. H5P needs this to function properly' => 'nombstringexteension',
                'Assistive Technologies label' => 'assistivetechnologieslabel',
                'Typical age' => 'age',
                'The target audience of this content. Possible input formats separated by commas: "1,34-45,-50,59-".' => 'agedescription',
                'Invalid input format for Typical age. Possible input formats separated by commas: "1, 34-45, -50, -59-".' => 'invalidage',
                'H5P will reach out to the contact person in case there are any issues with the content shared by the publisher. The contact person\'s name or other information will not be published or shared with third parties' => 'contactpersondescription',
                'The email address will be used by H5P to reach out to the publisher in case of any issues with the content or in case the publisher needs to recover their account. It will not be published or shared with any third parties' => 'emailaddressdescription',
                'Copyrighted material cannot be shared in the H5P Content Hub. If the content is licensed with a OER friendly license like Creative Commons, please choose the appropriate license. If not this content cannot be shared.' => 'copyrightwarning',
                'Keywords already exists!' => 'keywordsexists',
                'Some of these keywords already exist' => 'somekeywordsexists',
            ];
            // @codingStandardsIgnoreEnd
        }

        // Some strings such as error messages are not translatable, in this case use message
        // directly instead of crashing
        // @see https://github.com/h5p/h5p-php-library/commit/2bd972168e7b22aaeea2dd13682ced9cf8233452#diff-5ca86cd0514d58be6708beff914aba66R1296.
        if (!isset($translationsmap[$message])) {
            return $message;
        }

        return get_string($translationsmap[$message], 'hvp', $replacements);
    }

    /**
     * Implements getH5PPath
     */
    // @codingStandardsIgnoreLine
    public function getH5pPath() {
        global $CFG;

        return $CFG->dirroot . '/mod/hvp/files';
    }

    /**
     * Implements getLibraryFileUrl
     */
    // @codingStandardsIgnoreLine
    public function getLibraryFileUrl($libraryfoldername, $fileName) {
        $context  = \context_system::instance();
        $basepath = view_assets::getsiteroot() . '/';
        return "{$basepath}pluginfile.php/{$context->id}/mod_hvp/libraries/{$libraryfoldername}/{$fileName}";
    }

    /**
     * Implements getUploadedH5PFolderPath
     */
    // @codingStandardsIgnoreLine
    public function getUploadedH5pFolderPath($setpath = null) {
        static $path;

        if ($setpath !== null) {
            $path = $setpath;
        }

        if (!isset($path)) {
            throw new \coding_exception('Using getUploadedH5pFolderPath() before path is set');
        }

        return $path;
    }

    /**
     * Implements getUploadedH5PPath
     */
    // @codingStandardsIgnoreLine
    public function getUploadedH5pPath($setpath = null) {
        static $path;

        if ($setpath !== null) {
            $path = $setpath;
        }

        return $path;
    }

    /**
     * Implements loadLibraries
     */
    // @codingStandardsIgnoreLine
    public function loadLibraries() {
        global $DB;

        $results = $DB->get_records_sql(
              "SELECT id, machine_name, title, major_version, minor_version,
                      patch_version, runnable, restricted
                 FROM {hvp_libraries}
             ORDER BY title ASC, major_version ASC, minor_version ASC");

        $libraries = array();
        foreach ($results as $library) {
            $libraries[$library->machine_name][] = $library;
        }

        return $libraries;
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function setUnsupportedLibraries($libraries) {
        // Not supported.
    }

    /**
     * Implements getUnsupportedLibraries.
     */
    // @codingStandardsIgnoreLine
    public function getUnsupportedLibraries() {
        // Not supported.
    }

    /**
     * Implements getAdminUrl.
     */
    // @codingStandardsIgnoreLine
    public function getAdminUrl() {
        // Not supported.
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function getLibraryId($machinename, $majorversion = null, $minorversion = null) {
        global $DB;

        // Look for specific library.
        $sqlwhere = 'WHERE machine_name = ?';
        $sqlargs = array($machinename);

        if ($majorversion !== null) {
            // Look for major version.
            $sqlwhere .= ' AND major_version = ?';
            $sqlargs[] = $majorversion;
            if ($minorversion !== null) {
                // Look for minor version.
                $sqlwhere .= ' AND minor_version = ?';
                $sqlargs[] = $minorversion;
            }
        }

        // Get the lastest version which matches the input parameters.
        $libraries = $DB->get_records_sql("
                SELECT id
                  FROM {hvp_libraries}
          {$sqlwhere}
              ORDER BY major_version DESC,
                       minor_version DESC,
                       patch_version DESC
                ", $sqlargs, 0, 1);
        if ($libraries) {
            $library = reset($libraries);
            return $library ? $library->id : false;
        } else {
            return false;
        }
    }

    /**
     * Implements isPatchedLibrary
     */
    // @codingStandardsIgnoreLine
    public function isPatchedLibrary($library) {
        global $DB, $CFG;

        if (isset($CFG->mod_hvp_dev) && $CFG->mod_hvp_dev) {
            // Makes sure libraries are updated, patch version does not matter.
            return true;
        }

        $operator = $this->isInDevMode() ? '<=' : '<';
        $library = $DB->get_record_sql(
                'SELECT id
                  FROM {hvp_libraries}
                    WHERE machine_name = ?
                    AND major_version = ?
                    AND minor_version = ?
                    AND patch_version ' . $operator . ' ?',
                  array($library['machineName'],
                  $library['majorVersion'],
                  $library['minorVersion'],
                  $library['patchVersion'])
        );

        return $library ? true : false;
    }

    /**
     * Implements isInDevMode
     */
    // @codingStandardsIgnoreLine
    public function isInDevMode() {
        return false; // Not supported (Files in moodle not editable).
    }

    /**
     * Implements mayUpdateLibraries
     */
    // @codingStandardsIgnoreLine
    public function mayUpdateLibraries($allow = false) {
        static $override;

        // Allow overriding the permission check. Needed when installing.
        // since caps hasn't been set.
        if ($allow) {
            $override = true;
        }
        if ($override) {
            return true;
        }

        // Check permissions.
        $context = \context_system::instance();
        if (!has_capability('mod/hvp:updatelibraries', $context)) {
            return false;
        }

        return true;
    }

    /**
     * Implements getLibraryUsage
     *
     * Get number of content/nodes using a library, and the number of
     * dependencies to other libraries
     *
     * @param int $id
     * @param boolean $skipcontent Optional. Set as true to get number of content instances for library.
     * @return array The array contains two elements, keyed by 'content' and 'libraries'.
     *               Each element contains a number
     */
    // @codingStandardsIgnoreLine
    public function getLibraryUsage($id, $skipcontent = false) {
        global $DB;

        if ($skipcontent) {
            $content = -1;
        } else {
            $content = intval($DB->get_field_sql(
                "SELECT COUNT(distinct c.id)
                FROM {hvp_libraries} l
                JOIN {hvp_contents_libraries} cl ON l.id = cl.library_id
                JOIN {hvp} c ON cl.hvp_id = c.id
                WHERE l.id = ?", array($id)
            ));
        }

        $libraries = intval($DB->get_field_sql(
            "SELECT COUNT(*)
            FROM {hvp_libraries_libraries}
            WHERE required_library_id = ?", array($id)
        ));

        return array(
            'content' => $content,
            'libraries' => $libraries,
        );
    }

    /**
     * Implements getLibraryContentCount
     */
    // @codingStandardsIgnoreLine
    public function getLibraryContentCount() {
        global $DB;
        $contentcount = array();

        // Count content using the same content type.
        $res = $DB->get_records_sql(
          "SELECT c.main_library_id,
                  l.machine_name,
                  l.major_version,
                  l.minor_version,
                  c.count
             FROM (SELECT main_library_id,
                          count(id) as count
                     FROM {hvp}
                 GROUP BY main_library_id) c,
                 {hvp_libraries} l
            WHERE c.main_library_id = l.id"
        );

        // Extract results.
        foreach ($res as $lib) {
            $contentcount["{$lib->machine_name} {$lib->major_version}.{$lib->minor_version}"] = $lib->count;
        }

        return $contentcount;
    }

    /**
     * Implements saveLibraryData
     */
    // @codingStandardsIgnoreLine
    public function saveLibraryData(&$librarydata, $new = true) {
        global $DB;

        // Some special properties needs some checking and converting before they can be saved.
        $preloadedjs = $this->pathsToCsv($librarydata, 'preloadedJs');
        $preloadedcss = $this->pathsToCsv($librarydata, 'preloadedCss');
        $droplibrarycss = '';

        if (isset($librarydata['dropLibraryCss'])) {
            $libs = array();
            foreach ($librarydata['dropLibraryCss'] as $lib) {
                $libs[] = $lib['machineName'];
            }
            $droplibrarycss = implode(', ', $libs);
        }

        $embedtypes = '';
        if (isset($librarydata['embedTypes'])) {
            $embedtypes = implode(', ', $librarydata['embedTypes']);
        }
        if (!isset($librarydata['semantics'])) {
            $librarydata['semantics'] = '';
        }
        if (!isset($librarydata['fullscreen'])) {
            $librarydata['fullscreen'] = 0;
        }
        if (!isset($librarydata['hasIcon'])) {
            $librarydata['hasIcon'] = 0;
        }
        // TODO: Can we move the above code to H5PCore? It's the same for multiple
        // implementations. Perhaps core can update the data objects before calling
        // this function?
        // I think maybe it's best to do this when classes are created for
        // library, content, etc.

        $library = (object) array(
            'title' => $librarydata['title'],
            'machine_name' => $librarydata['machineName'],
            'major_version' => $librarydata['majorVersion'],
            'minor_version' => $librarydata['minorVersion'],
            'patch_version' => $librarydata['patchVersion'],
            'runnable' => $librarydata['runnable'],
            'fullscreen' => $librarydata['fullscreen'],
            'embed_types' => $embedtypes,
            'preloaded_js' => $preloadedjs,
            'preloaded_css' => $preloadedcss,
            'drop_library_css' => $droplibrarycss,
            'semantics' => $librarydata['semantics'],
            'has_icon' => $librarydata['hasIcon'],
            'metadata_settings' => $librarydata['metadataSettings'],
            'add_to' => isset($librarydata['addTo']) ? json_encode($librarydata['addTo']) : null,
        );

        if ($new) {
            // Create new library and keep track of id.
            $library->id = $DB->insert_record('hvp_libraries', $library);
            $librarydata['libraryId'] = $library->id;
        } else {
            // Update library data.
            $library->id = $librarydata['libraryId'];

            // Save library data.
            $DB->update_record('hvp_libraries', (object) $library);

            // Remove old dependencies.
            $this->deleteLibraryDependencies($librarydata['libraryId']);
        }

        // Log library successfully installed/upgraded.
        new \mod_hvp\event(
              'library', ($new ? 'create' : 'update'),
              null, null,
              $library->machine_name, $library->major_version . '.' . $library->minor_version
        );

        // Update library translations.
        $DB->delete_records('hvp_libraries_languages', array('library_id' => $librarydata['libraryId']));
        if (isset($librarydata['language'])) {
            foreach ($librarydata['language'] as $languagecode => $languagejson) {
                $DB->insert_record('hvp_libraries_languages', array(
                    'library_id' => $librarydata['libraryId'],
                    'language_code' => $languagecode,
                    'language_json' => $languagejson,
                ));
            }
        }
    }

    /**
     * Convert list of file paths to csv
     *
     * @param array $librarydata
     *  Library data as found in library.json files
     * @param string $key
     *  Key that should be found in $librarydata
     * @return string
     *  file paths separated by ', '
     */
    // @codingStandardsIgnoreLine
    private function pathsToCsv($librarydata, $key) {
        if (isset($librarydata[$key])) {
            $paths = array();
            foreach ($librarydata[$key] as $file) {
                $paths[] = $file['path'];
            }
            return implode(', ', $paths);
        }
        return '';
    }

    /**
     * Implements lockDependencyStorage
     */
    // @codingStandardsIgnoreLine
    public function lockDependencyStorage() {
        // Library development mode not supported.
    }

    /**
     * Implements unlockDependencyStorage
     */
    // @codingStandardsIgnoreLine
    public function unlockDependencyStorage() {
        // Library development mode not supported.
    }

    /**
     * Implements deleteLibrary
     */
    // @codingStandardsIgnoreLine
    public function deleteLibrary($library) {
        global $DB;

        // Delete library files.
        $librarybase = $this->getH5pPath() . '/libraries/';
        $libname = "{$library->name}-{$library->major_version}.{$library->minor_version}";
        \H5PCore::deleteFileTree("{$librarybase}{$libname}");

        // Remove library data from database.
        $DB->delete('hvp_libraries_libraries', array('library_id' => $library->id));
        $DB->delete('hvp_libraries_languages', array('library_id' => $library->id));
        $DB->delete('hvp_libraries', array('id' => $library->id));
    }

    /**
     * Implements saveLibraryDependencies
     *
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function saveLibraryDependencies($libraryid, $dependencies, $dependencytype) {
        global $DB;

        foreach ($dependencies as $dependency) {
            // Find dependency library.
            $dependencylibrary = $DB->get_record('hvp_libraries', array(
                'machine_name' => $dependency['machineName'],
                'major_version' => $dependency['majorVersion'],
                'minor_version' => $dependency['minorVersion']
            ));

            // Create relation.
            $DB->insert_record('hvp_libraries_libraries', array(
                'library_id' => $libraryid,
                'required_library_id' => $dependencylibrary->id,
                'dependency_type' => $dependencytype
            ));
        }
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function updateContent($content, $contentmainid = null) {
        global $DB;

        if (!isset($content['disable'])) {
            $content['disable'] = \H5PCore::DISABLE_NONE;
        }

        $data = array_merge(\H5PMetadata::toDBArray($content['metadata'], false), array(
            'name' => isset($content['metadata']->title) ? $content['metadata']->title : $content['name'],
            'course' => $content['course'],
            'intro' => $content['intro'],
            'introformat' => $content['introformat'],
            'json_content' => $content['params'],
            'embed_type' => 'div',
            'main_library_id' => $content['library']['libraryId'],
            'filtered' => '',
            'disable' => $content['disable'],
            'timemodified' => time(),
        ));

        if (isset($content[ 'completionpass'])) {
            $data[ 'completionpass' ] = $content[ 'completionpass' ];
        }

        if (!isset($content['id'])) {
            $data['slug'] = '';
            $data['timecreated'] = $data['timemodified'];
            $eventtype = 'create';
            $id = $DB->insert_record('hvp', $data);
        } else {
            $data['id'] = $content['id'];
            $data['synced'] = \H5PContentHubSyncStatus::NOT_SYNCED;
            $DB->update_record('hvp', $data);
            $eventtype = 'update';
            $id = $data['id'];
        }

        // Log content create/update/upload.
        if (!empty($content['uploaded'])) {
            $eventtype .= ' upload';
        }
        new \mod_hvp\event(
                'content', $eventtype,
                $id, $content['name'],
                $content['library']['machineName'],
                $content['library']['majorVersion'] . '.' . $content['library']['minorVersion']
        );

        return $id;
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function insertContent($content, $contentmainid = null) {
        return $this->updateContent($content);
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function resetContentUserData($contentid) {
        global $DB;

        // Reset user data for this content.
        $DB->execute("UPDATE {hvp_content_user_data}
                         SET data = 'RESET'
                       WHERE hvp_id = ?
                         AND delete_on_content_change = 1",
                     array($contentid));
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function getWhitelist($islibrary, $defaultcontentwhitelist, $defaultlibrarywhitelist) {
        return $defaultcontentwhitelist . ($islibrary ? ' ' . $defaultlibrarywhitelist : '');
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function copyLibraryUsage($contentid, $copyfromid, $contentmainid = null) {
        global $DB;

        $libraryusage = $DB->get_record('hvp_contents_libraries', array(
            'id' => $copyfromid
        ));

        $libraryusage->id = $contentid;
        $DB->insert_record_raw('hvp_contents_libraries', (array)$libraryusage, false, false, true);

        // TODO: This must be verified at a later time.
        // Currently in Moodle copyLibraryUsage() will never be called.
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function loadLibrarySemantics($name, $majorversion, $minorversion) {
        global $DB;

        $semantics = $DB->get_field_sql(
            "SELECT semantics
            FROM {hvp_libraries}
            WHERE machine_name = ?
            AND major_version = ?
            AND minor_version = ?",
            array($name, $majorversion, $minorversion));

        return ($semantics === false ? null : $semantics);
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function alterLibrarySemantics(&$semantics, $name, $majorversion, $minorversion) {
        global $PAGE;

        $PAGE->set_context(null);

        $renderer = $PAGE->get_renderer('mod_hvp');
        $renderer->hvp_alter_semantics($semantics, $name, $majorversion, $minorversion);
    }

    /**
     * Implements loadContent
     */
    // @codingStandardsIgnoreLine
    public function loadContent($id) {
        global $DB;

        $data = $DB->get_record_sql("
          SELECT
            hc.id,
            hc.name,
            hc.intro,
            hc.introformat,
            hc.json_content,
            hc.filtered,
            hc.slug,
            hc.embed_type,
            hc.disable,
            hl.id AS library_id,
            hl.machine_name,
            hl.major_version,
            hl.minor_version,
            hl.embed_types,
            hl.fullscreen,
            hc.name as title,
            hc.authors,
            hc.source,
            hc.license,
            hc.license_version,
            hc.license_extras,
            hc.year_from,
            hc.year_to,
            hc.changes,
            hc.author_comments,
            hc.default_language,
            hc.shared,
            hc.synced,
            hc.hub_id,
            hc.a11y_title
          FROM {hvp} hc
          JOIN {hvp_libraries} hl ON hl.id = hc.main_library_id
          WHERE hc.id = ?", array($id)
        );

        // Return null if not found.
        if ($data === false) {
            return null;
        }

        // Some databases do not support camelCase, so we need to manually
        // map the values to the camelCase names used by the H5P core.
        $content = array(
            'id' => $data->id,
            'title' => $data->name,
            'intro' => $data->intro,
            'introformat' => $data->introformat,
            'params' => $data->json_content,
            'filtered' => $data->filtered,
            'slug' => $data->slug,
            'embedType' => $data->embed_type,
            'disable' => $data->disable,
            'shared' => $data->shared,
            'synced' => $data->synced,
            'contentHubId' => $data->hub_id,
            'libraryId' => $data->library_id,
            'libraryName' => $data->machine_name,
            'libraryMajorVersion' => $data->major_version,
            'libraryMinorVersion' => $data->minor_version,
            'libraryEmbedTypes' => $data->embed_types,
            'libraryFullscreen' => $data->fullscreen,
        );

        $metadatafields = [
            'title',
            'authors',
            'source',
            'license',
            'license_version',
            'license_extras',
            'year_from',
            'year_to',
            'changes',
            'author_comments',
            'default_language',
            'a11y_title'
        ];

        $content['metadata'] = \H5PCore::snakeToCamel(
            array_reduce($metadatafields, function ($array, $field) use ($data) {
                if (isset($data->$field)) {
                    $value = $data->$field;
                    // Decode json fields.
                    if (in_array($field, ['authors', 'changes'])) {
                        $value = json_decode($data->$field);
                    }

                    $array[$field] = $value;
                }
                return $array;
            }, [])
        );

        return $content;
    }

    /**
     * Implements loadContentDependencies
     */
    // @codingStandardsIgnoreLine
    public function loadContentDependencies($id, $type = null) {
        global $DB;

        $query = "SELECT hcl.id AS unidepid
                       , hl.id
                       , hl.machine_name
                       , hl.major_version
                       , hl.minor_version
                       , hl.patch_version
                       , hl.preloaded_css
                       , hl.preloaded_js
                       , hcl.drop_css
                       , hcl.dependency_type
                   FROM {hvp_contents_libraries} hcl
                   JOIN {hvp_libraries} hl ON hcl.library_id = hl.id
                  WHERE hcl.hvp_id = ?";
        $queryargs = array($id);

        if ($type !== null) {
            $query .= " AND hcl.dependency_type = ?";
            $queryargs[] = $type;
        }

        $query .= " ORDER BY hcl.weight";
        $data = $DB->get_records_sql($query, $queryargs);

        $dependencies = array();
        foreach ($data as $dependency) {
            unset($dependency->unidepid);
            $dependencies[$dependency->machine_name] = \H5PCore::snakeToCamel($dependency);
        }

        return $dependencies;
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function getOption($name, $default = false) {
        $value = get_config('mod_hvp', $name);
        if ($value === false) {
            return $default;
        }
        return $value;
    }

    /**
     * Implements setOption().
     */
    // @codingStandardsIgnoreLine
    public function setOption($name, $value) {
        set_config($name, $value, 'mod_hvp');
    }

    /**
     * Implements updateContentFields().
     */
    // @codingStandardsIgnoreLine
    public function updateContentFields($id, $fields) {
        global $DB;

        $content = new \stdClass();
        $content->id = $id;

        foreach ($fields as $name => $value) {
            $content->$name = $value;
        }

        $DB->update_record('hvp', $content);
    }

    /**
     * Implements deleteLibraryDependencies
     */
    // @codingStandardsIgnoreLine
    public function deleteLibraryDependencies($libraryid) {
        global $DB;

        $DB->delete_records('hvp_libraries_libraries', array('library_id' => $libraryid));
    }

    /**
     * Implements deleteContentData
     */
    // @codingStandardsIgnoreLine
    public function deleteContentData($contentid) {
        global $DB;

        // Remove content.
        $DB->delete_records('hvp', array('id' => $contentid));

        // Remove content library dependencies.
        $this->deleteLibraryUsage($contentid);

        // Remove user data for content.
        $DB->delete_records('hvp_content_user_data', array('hvp_id' => $contentid));
    }

    /**
     * Implements deleteLibraryUsage
     */
    // @codingStandardsIgnoreLine
    public function deleteLibraryUsage($contentid) {
        global $DB;

        $DB->delete_records('hvp_contents_libraries', array('hvp_id' => $contentid));
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function saveLibraryUsage($contentid, $librariesinuse) {
        global $DB;

        $droplibrarycsslist = array();
        foreach ($librariesinuse as $dependency) {
            if (!empty($dependency['library']['dropLibraryCss'])) {
                $droplibrarycsslist = array_merge($droplibrarycsslist, explode(', ', $dependency['library']['dropLibraryCss']));
            }
        }
        // TODO: Consider moving the above code to core. Same for all impl.

        foreach ($librariesinuse as $dependency) {
            $dropcss = in_array($dependency['library']['machineName'], $droplibrarycsslist) ? 1 : 0;
            $DB->insert_record('hvp_contents_libraries', array(
                'hvp_id' => $contentid,
                'library_id' => $dependency['library']['libraryId'],
                'dependency_type' => $dependency['type'],
                'drop_css' => $dropcss,
                'weight' => $dependency['weight']
            ));
        }
    }

    /**
     * Implements loadLibrary
     */
    // @codingStandardsIgnoreLine
    public function loadLibrary($machinename, $majorversion, $minorversion) {
        global $DB;

        $library = $DB->get_record('hvp_libraries', array(
            'machine_name' => $machinename,
            'major_version' => $majorversion,
            'minor_version' => $minorversion
        ));

        $librarydata = array(
            'libraryId' => $library->id,
            'machineName' => $library->machine_name,
            'title' => $library->title,
            'majorVersion' => $library->major_version,
            'minorVersion' => $library->minor_version,
            'patchVersion' => $library->patch_version,
            'embedTypes' => $library->embed_types,
            'preloadedJs' => $library->preloaded_js,
            'preloadedCss' => $library->preloaded_css,
            'dropLibraryCss' => $library->drop_library_css,
            'fullscreen' => $library->fullscreen,
            'runnable' => $library->runnable,
            'semantics' => $library->semantics,
            'restricted' => $library->restricted,
            'hasIcon' => $library->has_icon
        );

        $dependencies = $DB->get_records_sql(
                'SELECT hl.id, hl.machine_name, hl.major_version, hl.minor_version, hll.dependency_type
                   FROM {hvp_libraries_libraries} hll
                   JOIN {hvp_libraries} hl ON hll.required_library_id = hl.id
                  WHERE hll.library_id = ?', array($library->id));
        foreach ($dependencies as $dependency) {
            $librarydata[$dependency->dependency_type . 'Dependencies'][] = array(
                'machineName' => $dependency->machine_name,
                'majorVersion' => $dependency->major_version,
                'minorVersion' => $dependency->minor_version
            );
        }

        return $librarydata;
    }

    /**
     * Implements clearFilteredParameters().
     *
     * @param array $libraryids array of library ids
     *
     * @throws \dml_exception
     * @throws \coding_exception
     */
    // @codingStandardsIgnoreLine
    public function clearFilteredParameters($libraryids) {
        global $DB;
        if (empty($libraryids)) {
            return;
        }

        list($insql, $inparams) = $DB->get_in_or_equal($libraryids);
        $DB->execute("
            UPDATE {hvp}
            SET filtered = null
            WHERE main_library_id $insql",
            $inparams
        );
    }

    /**
     * Implements getNumNotFiltered().
     */
    // @codingStandardsIgnoreLine
    public function getNumNotFiltered() {
        global $DB;

        return (int) $DB->get_field_sql(
                "SELECT COUNT(id)
                   FROM {hvp}
                  WHERE " . $DB->sql_compare_text('filtered') . " = ''");
    }

    /**
     * Implements getNumContent().
     */
    // @codingStandardsIgnoreLine
    public function getNumContent($libraryid, $skip = NULL) {
        global $DB;
        $skipquery = empty($skip) ? '' : " AND id NOT IN ($skip)";

        return (int) $DB->get_field_sql(
                "SELECT COUNT(id) FROM {hvp} WHERE main_library_id = ?{$skipquery}",
                array($libraryid));
    }

    /**
     * Implements isContentSlugAvailable
     */
    // @codingStandardsIgnoreLine
    public function isContentSlugAvailable($slug) {
        global $DB;

        return !$DB->get_records_sql("SELECT id, slug FROM {hvp} WHERE slug = ?", array($slug));
    }

    /**
     * Implements saveCachedAssets
     */
    // @codingStandardsIgnoreLine
    public function saveCachedAssets($key, $libraries) {
        global $DB;

        foreach ($libraries as $library) {
            $cachedasset = (object) array(
                'library_id' => $library['id'],
                'hash' => $key
            );
            $DB->insert_record('hvp_libraries_cachedassets', $cachedasset);
        }
    }

    /**
     * Implements deleteCachedAssets
     */
    // @codingStandardsIgnoreLine
    public function deleteCachedAssets($libraryid) {
        global $DB;

        // Get all the keys so we can remove the files.
        $results = $DB->get_records_sql(
                'SELECT hash
                   FROM {hvp_libraries_cachedassets}
                  WHERE library_id = ?',
                array($libraryid));

        // Remove all invalid keys.
        $hashes = array();
        foreach ($results as $key) {
            $hashes[] = $key->hash;
            $DB->delete_records('hvp_libraries_cachedassets', array('hash' => $key->hash));
        }

        return $hashes;
    }

    /**
     * Implements getLibraryStats
     */
    // @codingStandardsIgnoreLine
    public function getLibraryStats($type) {
        global $DB;
        $count = array();

        // Get the counts for the given type of event.
        $records = $DB->get_records_sql(
                "SELECT id,
                        library_name AS name,
                        library_version AS version,
                        num
                   FROM {hvp_counters}
                  WHERE type = ?",
                array($type));

        // Extract num from records.
        foreach ($records as $library) {
            $count[$library->name . ' ' . $library->version] = $library->num;
        }

        return $count;
    }

    /**
     * Implements getNumAuthors
     */
    // @codingStandardsIgnoreLine
    public function getNumAuthors() {
        global $DB;

        // Get number of unique courses using H5P.
        return intval($DB->get_field_sql(
                "SELECT COUNT(DISTINCT course)
                   FROM {hvp}"
        ));
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function afterExportCreated($content, $filename) {
    }

    /**
     * Implements hasPermission
     * @method hasPermission
     * @param  \H5PPermission $permission
     * @param  int $cmid context module id
     * @return boolean
     */
    // @codingStandardsIgnoreLine
    public function hasPermission($permission, $cmid = null) {
        switch ($permission) {
            case \H5PPermission::DOWNLOAD_H5P:
            case \H5PPermission::COPY_H5P:
                $cmcontext = \context_module::instance($cmid);
                return has_capability('mod/hvp:getexport', $cmcontext);
            case \H5PPermission::CREATE_RESTRICTED:
                return has_capability('mod/hvp:userestrictedlibraries', $this->getajaxcoursecontext());
            case \H5PPermission::UPDATE_LIBRARIES:
                $context = \context_system::instance();
                return has_capability('mod/hvp:updatelibraries', $context);
            case \H5PPermission::INSTALL_RECOMMENDED:
                return has_capability('mod/hvp:installrecommendedh5plibraries', $this->getajaxcoursecontext());
            case \H5PPermission::EMBED_H5P:
                $cmcontext = \context_module::instance($cmid);
                return has_capability('mod/hvp:getembedcode', $cmcontext);
        }
        return false;
    }

    /**
     * Gets course context in AJAX
     *
     * @return bool|\context|\context_course
     */
    private function getajaxcoursecontext() {
        $context = \context::instance_by_id(required_param('contextId', PARAM_RAW));
        if ($context->contextlevel === CONTEXT_COURSE) {
            return $context;
        }

        return $context->get_course_context();
    }

    /**
     * Replaces existing content type cache with the one passed in
     *
     * @param object $contenttypecache Json with an array called 'libraries'
     *  containing the new content type cache that should replace the old one.
     */
    // @codingStandardsIgnoreLine
    public function replaceContentTypeCache($contenttypecache) {
        global $DB;

        // Replace existing cache.
        $DB->delete_records('hvp_libraries_hub_cache');
        foreach ($contenttypecache->contentTypes as $ct) {
            $DB->insert_record('hvp_libraries_hub_cache', (object) array(
                'machine_name'      => $ct->id,
                'major_version'     => $ct->version->major,
                'minor_version'     => $ct->version->minor,
                'patch_version'     => $ct->version->patch,
                'h5p_major_version' => $ct->coreApiVersionNeeded->major,
                'h5p_minor_version' => $ct->coreApiVersionNeeded->minor,
                'title'             => $ct->title,
                'summary'           => $ct->summary,
                'description'       => $ct->description,
                'icon'              => $ct->icon,
                'created_at'        => (new \DateTime($ct->createdAt))->getTimestamp(),
                'updated_at'        => (new \DateTime($ct->updatedAt))->getTimestamp(),
                'is_recommended'    => $ct->isRecommended === true ? 1 : 0,
                'popularity'        => $ct->popularity,
                'screenshots'       => json_encode($ct->screenshots),
                'license'           => json_encode(isset($ct->license) ? $ct->license : array()),
                'example'           => $ct->example,
                'tutorial'          => isset($ct->tutorial) ? $ct->tutorial : '',
                'keywords'          => json_encode(isset($ct->keywords) ? $ct->keywords : array()),
                'categories'        => json_encode(isset($ct->categories) ? $ct->categories : array()),
                'owner'             => $ct->owner
            ), false, true);
        }
    }

    /**
     * Implements loadAddons
     */
    // @codingStandardsIgnoreLine
    public function loadAddons() {
        global $DB;
        $addons = array();

        $records = $DB->get_records_sql(
                "SELECT l1.id AS library_id,
                        l1.machine_name,
                        l1.major_version,
                        l1.minor_version,
                        l1.patch_version,
                        l1.add_to,
                        l1.preloaded_js,
                        l1.preloaded_css
                   FROM {hvp_libraries} l1
              LEFT JOIN {hvp_libraries} l2
                     ON l1.machine_name = l2.machine_name
                    AND (l1.major_version < l2.major_version
                         OR (l1.major_version = l2.major_version
                             AND l1.minor_version < l2.minor_version))
                  WHERE l1.add_to IS NOT NULL
                    AND l2.machine_name IS NULL");

        // NOTE: These are treated as library objects but are missing the following properties:
        // title, embed_types, drop_library_css, fullscreen, runnable, semantics, has_icon.

        // Extract num from records.
        foreach ($records as $addon) {
            $addons[] = \H5PCore::snakeToCamel($addon);
        }

        return $addons;
    }

    /**
     * Implements getLibraryConfig
     */
    // @codingStandardsIgnoreLine
    public function getLibraryConfig($libraries = null) {
        global $CFG;
        return (isset($CFG->mod_hvp_library_config) ? $CFG->mod_hvp_library_config : null);
    }

    /**
     * Implements libraryHasUpgrade
     */
    // @codingStandardsIgnoreLine
    public function libraryHasUpgrade($library) {
        global $DB;

        $results = $DB->get_records_sql(
            "SELECT id
                  FROM {hvp_libraries}
                  WHERE machine_name = ?
                  AND (major_version > ?
                       OR (major_version = ? AND minor_version > ?))",
            array(
                $library['machineName'],
                $library['majorVersion'],
                $library['majorVersion'],
                $library['minorVersion']
            ),
            0,
            1
        );

        return !empty($results);
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function replaceContentHubMetadataCache($metadata, $lang = 'en') {
        global $DB;

        // Check if exist in database.
        $cache = $DB->get_record_sql(
            'SELECT id
                   FROM {hvp_content_hub_cache}
                  WHERE language = ?',
            array($lang)
        );
        if ($cache) {
            // Update.
            $DB->execute("UPDATE {hvp_content_hub_cache} SET json = ? WHERE id = ?", array($metadata, $cache->id));
        } else {
            // Insert.
            $DB->insert_record('hvp_content_hub_cache', (object) array(
                'json'         => $metadata,
                'language'     => $lang,
                'last_checked' => time(),
            ));
        }
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function getContentHubMetadataCache($lang = 'en') {
        global $DB;
        $cache = $DB->get_record_sql(
                'SELECT json
                   FROM {hvp_content_hub_cache}
                  WHERE language = ?',
                array($lang)
        );
        return $cache ? $cache->json : null;
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function getContentHubMetadataChecked($lang = 'en') {
        global $DB;
        $cache = $DB->get_record_sql(
                'SELECT last_checked
                  FROM {hvp_content_hub_cache}
                 WHERE language = ?',
                array($lang)
        );
        if ($cache) {
            $time = new \DateTime();
            $time->setTimestamp($cache->last_checked);
            $cache = $time->format("D, d M Y H:i:s \G\M\T");
        }
        return $cache;
    }

    /**
     * @inheritdoc
     */
    // @codingStandardsIgnoreLine
    public function setContentHubMetadataChecked($time, $lang = 'en') {
        global $DB;
        $DB->execute("UPDATE {hvp_content_hub_cache} SET last_checked = ? WHERE language = ?", array($time, $lang));
    }
}
