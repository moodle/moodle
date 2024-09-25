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

namespace core_h5p;

use core_xapi\handler;
use core_xapi\xapi_exception;
use Moodle\H5PFrameworkInterface;
use Moodle\H5PCore;

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

/**
 * Moodle's implementation of the H5P framework interface.
 *
 * @package    core_h5p
 * @copyright  2019 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class framework implements H5PFrameworkInterface {

    /** @var string The path to the last uploaded h5p */
    private $lastuploadedfolder;

    /** @var string The path to the last uploaded h5p file */
    private $lastuploadedfile;

    /** @var \stored_file The .h5p file */
    private $file;

    /**
     * Returns info for the current platform.
     * Implements getPlatformInfo.
     *
     * @return array An associative array containing:
     *               - name: The name of the platform, for instance "Moodle"
     *               - version: The version of the platform, for instance "3.8"
     *               - h5pVersion: The version of the H5P component
     */
    public function getPlatformInfo() {
        global $CFG;

        return array(
            'name' => 'Moodle',
            'version' => $CFG->version,
            'h5pVersion' => $CFG->version,
        );
    }

    /**
     * Fetches a file from a remote server using HTTP GET
     * Implements fetchExternalData.
     *
     * @param  string  $url  Where you want to get or send data.
     * @param  array  $data  Data to post to the URL.
     * @param  bool  $blocking  Set to 'FALSE' to instantly time out (fire and forget).
     * @param  string  $stream  Path to where the file should be saved.
     * @param  bool  $fulldata  Return additional response data such as headers and potentially other data
     * @param  array  $headers  Headers to send
     * @param  array  $files Files to send
     * @param  string  $method
     *
     * @return string|array The content (response body), or an array with data. NULL if something went wrong
     */
    public function fetchExternalData($url, $data = null, $blocking = true, $stream = null, $fulldata = false, $headers = [],
            $files = [], $method = 'POST') {

        if ($stream === null) {
            // Download file.
            set_time_limit(0);

            // Get the extension of the remote file.
            $parsedurl = parse_url($url);
            $ext = pathinfo($parsedurl['path'], PATHINFO_EXTENSION);

            // Generate local tmp file path.
            $fs = new \core_h5p\file_storage();
            $localfolder = $fs->getTmpPath();
            $stream = $localfolder;

            // Add the remote file's extension to the temp file.
            if ($ext) {
                $stream .= '.' . $ext;
            }

            $this->getUploadedH5pFolderPath($localfolder);
            $this->getUploadedH5pPath($stream);
        }

        $response = download_file_content($url, null, $data, true, 300, 20,
                false, $stream);

        if (empty($response->error) && ($response->status != '404')) {
            return $response->results;
        } else {
            $this->setErrorMessage($response->error, 'failed-fetching-external-data');
        }
    }

    /**
     * Set the tutorial URL for a library. All versions of the library is set.
     * Implements setLibraryTutorialUrl.
     *
     * @param string $libraryname
     * @param string $url
     */
    public function setLibraryTutorialUrl($libraryname, $url) {
        global $DB;

        $sql = 'UPDATE {h5p_libraries}
                   SET tutorial = :tutorial
                 WHERE machinename = :machinename';
        $params = [
            'tutorial' => $url,
            'machinename' => $libraryname,
        ];
        $DB->execute($sql, $params);
    }

    /**
     * Set an error message.
     * Implements setErrorMessage.
     *
     * @param string $message The error message
     * @param string $code An optional code
     */
    public function setErrorMessage($message, $code = null) {
        if ($message !== null) {
            $this->set_message('error', $message, $code);
        }
    }

    /**
     * Set an info message.
     * Implements setInfoMessage.
     *
     * @param string $message The info message
     */
    public function setInfoMessage($message) {
        if ($message !== null) {
            $this->set_message('info', $message);
        }
    }

    /**
     * Return messages.
     * Implements getMessages.
     *
     * @param string $type The message type, e.g. 'info' or 'error'
     * @return string[] Array of messages
     */
    public function getMessages($type) {
        global $SESSION;

        // Return and reset messages.
        $messages = array();
        if (isset($SESSION->core_h5p_messages[$type])) {
            $messages = $SESSION->core_h5p_messages[$type];
            unset($SESSION->core_h5p_messages[$type]);
            if (empty($SESSION->core_h5p_messages)) {
                unset($SESSION->core_h5p_messages);
            }
        }

        return $messages;
    }

    /**
     * Translation function.
     * The purpose of this function is to map the strings used in the core h5p methods
     * and replace them with the translated ones. If a translation for a particular string
     * is not available, the default message (key) will be returned.
     * Implements t.
     *
     * @param string $message The english string to be translated
     * @param array $replacements An associative array of replacements to make after translation
     * @return string Translated string or the english string if a translation is not available
     */
    public function t($message, $replacements = array()) {

        // Create mapping.
        $translationsmap = [
            'The file you uploaded is not a valid HTML5 Package (It does not have the .h5p file extension)' => 'noextension',
            'The file you uploaded is not a valid HTML5 Package (We are unable to unzip it)' => 'nounzip',
            'The main h5p.json file is not valid' => 'nojson',
            'Library directory name must match machineName or machineName-majorVersion.minorVersion (from library.json).' .
                ' (Directory: %directoryName , machineName: %machineName, majorVersion: %majorVersion, minorVersion:' .
                ' %minorVersion)'
                => 'librarydirectoryerror',
            'A valid content folder is missing' => 'missingcontentfolder',
            'A valid main h5p.json file is missing' => 'invalidmainjson',
            'Missing required library @library' => 'missinglibrary',
            "Note that the libraries may exist in the file you uploaded, but you're not allowed to upload new libraries." .
                ' Contact the site administrator about this.' => 'missinguploadpermissions',
            'Invalid library name: %name' => 'invalidlibraryname',
            'Could not find library.json file with valid json format for library %name' => 'missinglibraryjson',
            'Invalid semantics.json file has been included in the library %name' => 'invalidsemanticsjson',
            'Invalid language file %file in library %library' => 'invalidlanguagefile',
            'Invalid language file %languageFile has been included in the library %name' => 'invalidlanguagefile2',
            'The file "%file" is missing from library: "%name"' => 'missinglibraryfile',
            'The system was unable to install the <em>%component</em> component from the package, it requires a newer' .
                ' version of the H5P plugin. This site is currently running version %current, whereas the required version' .
                ' is %required or higher. You should consider upgrading and then try again.' => 'missingcoreversion',
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
            'Updated %old H5P library.' => 'updatedlibrary',
            'Updated %old H5P libraries.' => 'updatedlibraries',
            'Missing dependency @dep required by @lib.' => 'missingdependency',
            'Provided string is not valid according to regexp in semantics. (value: "%value", regexp: "%regexp")'
                => 'invalidstring',
            'File "%filename" not allowed. Only files with the following extensions are allowed: %files-allowed.'
                => 'invalidfile',
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
            'General Public License v3' => 'gpl',
            'Public Domain' => 'pd',
            'Public Domain Dedication and Licence' => 'pddl',
            'Public Domain Mark' => 'pdm',
            'Public Domain Mark (PDM)' => 'pdm',
            'Copyright' => 'copyrightstring',
            'The mbstring PHP extension is not loaded. H5P need this to function properly' => 'missingmbstring',
            'The version of the H5P library %machineName used in this content is not valid. Content contains %contentLibrary, ' .
                'but it should be %semanticsLibrary.' => 'wrongversion',
            'The H5P library %library used in the content is not valid' => 'invalidlibrarynamed',
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
            'Date' => 'date',
            'Changed by' => 'changedby',
            'Description of change' => 'changedescription',
            'Photo cropped, text changed, etc.' => 'changeplaceholder',
            'Author comments' => 'authorcomments',
            'Comments for the editor of the content (This text will not be published as a part of copyright info)'
                => 'authorcommentsdescription',
            'Reuse' => 'reuse',
            'Reuse Content' => 'reuseContent',
            'Reuse this content.' => 'reuseDescription',
            'Content is copied to the clipboard' => 'contentCopied',
            'Connection lost. Results will be stored and sent when you regain connection.' => 'connectionLost',
            'Connection reestablished.' => 'connectionReestablished',
            'Attempting to submit stored results.' => 'resubmitScores',
            'Your connection to the server was lost' => 'offlineDialogHeader',
            'We were unable to send information about your completion of this task. Please check your internet connection.'
                => 'offlineDialogBody',
            'Retrying in :num....' => 'offlineDialogRetryMessage',
            'Retry now' => 'offlineDialogRetryButtonLabel',
            'Successfully submitted results.' => 'offlineSuccessfulSubmit',
            'One of the files inside the package exceeds the maximum file size allowed. (%file %used > %max)'
                => 'fileExceedsMaxSize',
            'The total size of the unpacked files exceeds the maximum size allowed. (%used > %max)'
                => 'unpackedFilesExceedsMaxSize',
            'Unable to read file from the package: %fileName' => 'couldNotReadFileFromZip',
            'Unable to parse JSON from the package: %fileName' => 'couldNotParseJSONFromZip',
            'A problem with the server write access was detected. Please make sure that your server can write to your data folder.' => 'nowriteaccess',
            'H5P hub communication has been disabled because one or more H5P requirements failed.' => 'hubcommunicationdisabled',
            'Site could not be registered with the hub. Please contact your site administrator.' => 'sitecouldnotberegistered',
            'The H5P Hub has been disabled until this problem can be resolved. You may still upload libraries through the "H5P Libraries" page.' => 'hubisdisableduploadlibraries',
            'When you have revised your server setup you may re-enable H5P hub communication in H5P Settings.' => 'reviseserversetupandretry',
            'You have been provided a unique key that identifies you with the Hub when receiving new updates. The key is available for viewing in the "H5P Settings" page.' => 'sitekeyregistered',
            'Your PHP max post size is quite small. With your current setup, you may not upload files larger than {$a->%number} MB. This might be a problem when trying to upload H5Ps, images and videos. Please consider to increase it to more than 5MB' => 'maxpostsizetoosmall',
            'Your PHP max upload size is bigger than your max post size. This is known to cause issues in some installations.' => 'uploadsizelargerthanpostsize',
            'Your PHP max upload size is quite small. With your current setup, you may not upload files larger than {$a->%number} MB. This might be a problem when trying to upload H5Ps, images and videos. Please consider to increase it to more than 5MB.' => 'maxuploadsizetoosmall',
            'Your PHP version does not support ZipArchive.' => 'noziparchive',
            'Your PHP version is outdated. H5P requires version 5.2 to function properly. Version 5.6 or later is recommended.' => 'oldphpversion',
            'Your server does not have SSL enabled. SSL should be enabled to ensure a secure connection with the H5P hub.' => 'sslnotenabled',
            'Your site was successfully registered with the H5P Hub.' => 'successfullyregisteredwithhub',
            'Sharing <strong>:title</strong>' => 'mainTitle',
            'Edit info for <strong>:title</strong>' => 'editInfoTitle',
            'Back' => 'back',
            'Next' => 'next',
            'Review info' => 'reviewInfo',
            'Share' => 'share',
            'Save changes' => 'saveChanges',
            'Register on the H5P Hub' => 'registerOnHub',
            'Save account settings' => 'updateRegistrationOnHub',
            'Required Info' => 'requiredInfo',
            'Optional Info' => 'optionalInfo',
            'Review & Share' => 'reviewAndShare',
            'Review & Save' => 'reviewAndSave',
            'Shared' => 'shared',
            'Step :step of :total' => 'currentStep',
            'All content details can be edited after sharing' => 'sharingNote',
            'Select a license for your content' => 'licenseDescription',
            'Select a license version' => 'licenseVersionDescription',
            'Disciplines' => 'disciplineLabel',
            'You can select multiple disciplines' => 'disciplineDescription',
            'You can select up to :numDisciplines disciplines' => 'disciplineLimitReachedMessage',
            'Type to search for disciplines' => 'discipline:searchPlaceholder',
            'in' => 'discipline:in',
            'Dropdown button' => 'discipline:dropdownButton',
            'Remove :chip from the list' => 'removeChip',
            'Add keywords' => 'keywordsPlaceholder',
            'Keywords' => 'keywords',
            'You can add multiple keywords separated by commas. Press "Enter" or "Add" to confirm keywords' => 'keywordsDescription',
            'Alt text' => 'altText',
            'Please review the info below before you share' => 'reviewMessage',
            'Sub-content (images, questions etc.) will be shared under :license unless otherwise specified in the authoring tool' => 'subContentWarning',
            'Disciplines' => 'disciplines',
            'Short description' => 'shortDescription',
            'Long description' => 'longDescription',
            'Icon' => 'icon',
            'Screenshots' => 'screenshots',
            'Help me choose a license' => 'helpChoosingLicense',
            'Share failed.' => 'shareFailed',
            'Editing failed.' => 'editingFailed',
            'Something went wrong, please try to share again.' => 'shareTryAgain',
            'Please wait...' => 'pleaseWait',
            'Language' => 'language',
            'Level' => 'level',
            'Short description of your content' => 'shortDescriptionPlaceholder',
            'Long description of your content' => 'longDescriptionPlaceholder',
            'Description' => 'description',
            '640x480px. If not selected content will use category icon' => 'iconDescription',
            'Add up to five screenshots of your content' => 'screenshotsDescription',
            'Submitted!' => 'submitted',
            'Is now submitted to H5P Hub' => 'isNowSubmitted',
            'A change has been submited for' => 'changeHasBeenSubmitted',
            'Your content will normally be available in the Hub within one business day.' => 'contentAvailable',
            'Your content will update soon' => 'contentUpdateSoon',
            'Content License Info' => 'contentLicenseTitle',
            'Click on a specific license to get info about proper usage' => 'licenseDialogDescription',
            'Publisher' => 'publisherFieldTitle',
            'This will display as the "Publisher name" on shared content' => 'publisherFieldDescription',
            'Email Address' => 'emailAddress',
            'Publisher description' => 'publisherDescription',
            'This will be displayed under "Publisher info" on shared content' => 'publisherDescriptionText',
            'Contact Person' => 'contactPerson',
            'Phone' => 'phone',
            'Address' => 'address',
            'City' => 'city',
            'Zip' => 'zip',
            'Country' => 'country',
            'Organization logo or avatar' => 'logoUploadText',
            'I accept the <a href=":url" target="_blank">terms of use</a>' => 'acceptTerms',
            'You have successfully registered an account on the H5P Hub' => 'successfullyRegistred',
            'You account details can be changed' => 'successfullyRegistredDescription',
            'Your H5P Hub account settings have successfully been changed' => 'successfullyUpdated',
            'here' => 'accountDetailsLinkText',
            'H5P Hub Registration' => 'registrationTitle',
            'An error occurred' => 'registrationFailed',
            'We were not able to create an account at this point. Something went wrong. Try again later.' => 'registrationFailedDescription',
            ':length is the maximum number of characters' => 'maxLength',
            'Keyword already exists!' => 'keywordExists',
            'License details' => 'licenseDetails',
            'Remove' => 'remove',
            'Remove image' => 'removeImage',
            'Cancel sharing' => 'cancelPublishConfirmationDialogTitle',
            'Are you sure you want to cancel the sharing process?' => 'cancelPublishConfirmationDialogDescription',
            'No' => 'cancelPublishConfirmationDialogCancelButtonText',
            'Yes' => 'cancelPublishConfirmationDialogConfirmButtonText',
            'Add' => 'add',
            'Typical age' => 'age',
            'The target audience of this content. Possible input formats separated by commas: "1,34-45,-50,59-".' => 'ageDescription',
            'Invalid input format for Typical age. Possible input formats separated by commas: "1, 34-45, -50, -59-".' => 'invalidAge',
            'H5P will reach out to the contact person in case there are any issues with the content shared by the publisher. The contact person\'s name or other information will not be published or shared with third parties' => 'contactPersonDescription',
            'The email address will be used by H5P to reach out to the publisher in case of any issues with the content or in case the publisher needs to recover their account. It will not be published or shared with any third parties' => 'emailAddressDescription',
            'Copyrighted material cannot be shared in the H5P Content Hub. If the content is licensed with a OER friendly license like Creative Commons, please choose the appropriate license. If not this content cannot be shared.' => 'copyrightWarning',
            'Keywords already exists!' => 'keywordsExits',
            'Some of these keywords already exist' => 'someKeywordsExits',
            'Assistive Technologies label' => 'a11yTitle:label',
            'width' => 'width',
            'height' => 'height',
            'Missing main library @library' => 'missingmainlibrary',
            'Rotate Left' => 'rotateLeft',
            'Rotate Right' => 'rotateRight',
            'Crop Image' => 'cropImage',
            'Confirm Crop' => 'confirmCrop',
            'Cancel Crop' => 'cancelCrop',
        ];

        if (isset($translationsmap[$message])) {
            return get_string($translationsmap[$message], 'core_h5p', $replacements);
        }

        debugging("String translation cannot be found. Please add a string definition for '" .
            $message . "' in the core_h5p component.", DEBUG_DEVELOPER);

        return $message;
    }

    /**
     * Get URL to file in the specifimake_pluginfile_urlc library.
     * Implements getLibraryFileUrl.
     *
     * @param string $libraryfoldername The name or path of the library's folder
     * @param string $filename The file name
     * @return string URL to file
     */
    public function getLibraryFileUrl($libraryfoldername, $filename) {
        global $DB;

        // Remove unnecessary slashes (first and last, if present) from the path to the folder
        // of the library file.
        $libraryfilepath = trim($libraryfoldername, '/');

        // Get the folder name of the library from the path.
        // The first element should represent the folder name of the library.
        $libfoldername = explode('/', $libraryfilepath)[0];

        $factory = new \core_h5p\factory();
        $core = $factory->get_core();

        // The provided folder name of the library must have a valid format (can be parsed).
        // The folder name is parsed with a purpose of getting the library related information
        // such as 'machineName', 'majorVersion' and 'minorVersion'.
        // This information is later used to retrieve the library ID.
        if (!$libdata = $core->libraryFromString($libfoldername, true)) {
            debugging('The provided string value "' . $libfoldername .
                '" is not a valid name for a library folder.', DEBUG_DEVELOPER);

            return;
        }

        $params = array(
            'machinename' => $libdata['machineName'],
            'majorversion' => $libdata['majorVersion'],
            'minorversion' => $libdata['minorVersion']
        );

        $libraries = $DB->get_records('h5p_libraries', $params, 'patchversion DESC', 'id',
            0, 1);

        if (!$library = reset($libraries)) {
            debugging('The library "' . $libfoldername . '" does not exist.', DEBUG_DEVELOPER);

            return;
        }

        $context = \context_system::instance();

        return \moodle_url::make_pluginfile_url($context->id, 'core_h5p', 'libraries',
            $library->id, '/' . $libraryfilepath . '/', $filename)->out();
    }

    /**
     * Get the Path to the last uploaded h5p.
     * Implements getUploadedH5PFolderPath.
     *
     * @param string $setpath The path to the folder of the last uploaded h5p
     * @return string Path to the folder where the last uploaded h5p for this session is located
     */
    public function getUploadedH5pFolderPath($setpath = null) {
        if ($setpath !== null) {
            $this->lastuploadedfolder = $setpath;
        }

        if (!isset($this->lastuploadedfolder)) {
            throw new \coding_exception('Using getUploadedH5pFolderPath() before path is set');
        }

        return $this->lastuploadedfolder;
    }

    /**
     * Get the path to the last uploaded h5p file.
     * Implements getUploadedH5PPath.
     *
     * @param string $setpath The path to the last uploaded h5p
     * @return string Path to the last uploaded h5p
     */
    public function getUploadedH5pPath($setpath = null) {
        if ($setpath !== null) {
            $this->lastuploadedfile = $setpath;
        }

        if (!isset($this->lastuploadedfile)) {
            throw new \coding_exception('Using getUploadedH5pPath() before path is set');
        }

        return $this->lastuploadedfile;
    }

    /**
     * Load addon libraries.
     * Implements loadAddons.
     *
     * @return array The array containing the addon libraries
     */
    public function loadAddons() {
        global $DB;

        $addons = array();

        $records = $DB->get_records_sql(
                "SELECT l1.id AS library_id,
                            l1.machinename AS machine_name,
                            l1.majorversion AS major_version,
                            l1.minorversion AS minor_version,
                            l1.patchversion AS patch_version,
                            l1.addto AS add_to,
                            l1.preloadedjs AS preloaded_js,
                            l1.preloadedcss AS preloaded_css
                       FROM {h5p_libraries} l1
                  LEFT JOIN {h5p_libraries} l2
                         ON l1.machinename = l2.machinename
                        AND (l1.majorversion < l2.majorversion
                             OR (l1.majorversion = l2.majorversion
                                 AND l1.minorversion < l2.minorversion))
                      WHERE l1.addto IS NOT NULL
                        AND l2.machinename IS NULL");

        // NOTE: These are treated as library objects but are missing the following properties:
        // title, droplibrarycss, fullscreen, runnable, semantics.

        // Extract num from records.
        foreach ($records as $addon) {
            $addons[] = H5PCore::snakeToCamel($addon);
        }

        return $addons;
    }

    /**
     * Load config for libraries.
     * Implements getLibraryConfig.
     *
     * @param array|null $libraries List of libraries
     * @return array|null The library config if it exists, null otherwise
     */
    public function getLibraryConfig($libraries = null) {
        global $CFG;
        return isset($CFG->core_h5p_library_config) ? $CFG->core_h5p_library_config : null;
    }

    /**
     * Get a list of the current installed libraries.
     * Implements loadLibraries.
     *
     * @return array Associative array containing one entry per machine name.
     *               For each machineName there is a list of libraries(with different versions).
     */
    public function loadLibraries() {
        global $DB;

        $results = $DB->get_records('h5p_libraries', [], 'title ASC, majorversion ASC, minorversion ASC',
            'id, machinename AS machine_name, majorversion AS major_version, minorversion AS minor_version,
            patchversion AS patch_version, runnable, title, enabled');

        $libraries = array();
        foreach ($results as $library) {
            $libraries[$library->machine_name][] = $library;
        }

        return $libraries;
    }

    /**
     * Returns the URL to the library admin page.
     * Implements getAdminUrl.
     *
     * @return string URL to admin page
     */
    public function getAdminUrl() {
        // Not supported.
    }

    /**
     * Return the library's ID.
     * Implements getLibraryId.
     *
     * @param string $machinename The librarys machine name
     * @param string $majorversion Major version number for library (optional)
     * @param string $minorversion Minor version number for library (optional)
     * @return int|bool Identifier, or false if non-existent
     */
    public function getLibraryId($machinename, $majorversion = null, $minorversion = null) {
        global $DB;

        $params = array(
            'machinename' => $machinename
        );

        if ($majorversion !== null) {
            $params['majorversion'] = $majorversion;
        }

        if ($minorversion !== null) {
            $params['minorversion'] = $minorversion;
        }

        $libraries = $DB->get_records('h5p_libraries', $params,
            'majorversion DESC, minorversion DESC, patchversion DESC', 'id', 0, 1);

        // Get the latest version which matches the input parameters.
        if ($libraries) {
            $library = reset($libraries);
            return $library->id ?? false;
        }

        return false;
    }

    /**
     * Get allowed file extension list.
     * Implements getWhitelist.
     *
     * The default extension list is part of h5p, but admins should be allowed to modify it.
     *
     * @param boolean $islibrary TRUE if this is the whitelist for a library. FALSE if it is the whitelist
     *                           for the content folder we are getting.
     * @param string $defaultcontentwhitelist A string of file extensions separated by whitespace.
     * @param string $defaultlibrarywhitelist A string of file extensions separated by whitespace.
     * @return string A string containing the allowed file extensions separated by whitespace.
     */
    public function getWhitelist($islibrary, $defaultcontentwhitelist, $defaultlibrarywhitelist) {
        return $defaultcontentwhitelist . ($islibrary ? ' ' . $defaultlibrarywhitelist : '');
    }

    /**
     * Is the library a patched version of an existing library?
     * Implements isPatchedLibrary.
     *
     * @param array $library An associative array containing:
     *                       - machineName: The library machine name
     *                       - majorVersion: The librarys major version
     *                       - minorVersion: The librarys minor version
     *                       - patchVersion: The librarys patch version
     * @return boolean TRUE if the library is a patched version of an existing library FALSE otherwise
     */
    public function isPatchedLibrary($library) {
        global $DB;

        $sql = "SELECT id
                  FROM {h5p_libraries}
                 WHERE machinename = :machinename
                   AND majorversion = :majorversion
                   AND minorversion = :minorversion
                   AND patchversion < :patchversion";

        $library = $DB->get_records_sql(
            $sql,
            array(
                'machinename' => $library['machineName'],
                'majorversion' => $library['majorVersion'],
                'minorversion' => $library['minorVersion'],
                'patchversion' => $library['patchVersion']
            ),
            0,
            1
        );

        return !empty($library);
    }

    /**
     * Is H5P in development mode?
     * Implements isInDevMode.
     *
     * @return boolean TRUE if H5P development mode is active FALSE otherwise
     */
    public function isInDevMode() {
        return false; // Not supported (Files in moodle not editable).
    }

    /**
     * Is the current user allowed to update libraries?
     * Implements mayUpdateLibraries.
     *
     * @return boolean TRUE if the user is allowed to update libraries,
     *                 FALSE if the user is not allowed to update libraries.
     */
    public function mayUpdateLibraries() {
        return helper::can_update_library($this->get_file());
    }

    /**
     * Get the .h5p file.
     *
     * @return \stored_file The .h5p file.
     */
    public function get_file(): \stored_file {
        if (!isset($this->file)) {
            throw new \coding_exception('Using get_file() before file is set');
        }

        return $this->file;
    }

    /**
     * Set the .h5p file.
     *
     * @param  stored_file $file The .h5p file.
     */
    public function set_file(\stored_file $file): void {
        $this->file = $file;
    }

    /**
     * Store data about a library.
     * Implements saveLibraryData.
     *
     * Also fills in the libraryId in the libraryData object if the object is new.
     *
     * @param array $librarydata Associative array containing:
     *                           - libraryId: The id of the library if it is an existing library
     *                           - title: The library's name
     *                           - machineName: The library machineName
     *                           - majorVersion: The library's majorVersion
     *                           - minorVersion: The library's minorVersion
     *                           - patchVersion: The library's patchVersion
     *                           - runnable: 1 if the library is a content type, 0 otherwise
     *                           - fullscreen(optional): 1 if the library supports fullscreen, 0 otherwise
     *                           - embedtypes: list of supported embed types
     *                           - preloadedJs(optional): list of associative arrays containing:
     *                             - path: path to a js file relative to the library root folder
     *                           - preloadedCss(optional): list of associative arrays containing:
     *                             - path: path to css file relative to the library root folder
     *                           - dropLibraryCss(optional): list of associative arrays containing:
     *                             - machineName: machine name for the librarys that are to drop their css
     *                           - semantics(optional): Json describing the content structure for the library
     *                           - metadataSettings(optional): object containing:
     *                             - disable: 1 if metadata is disabled completely
     *                             - disableExtraTitleField: 1 if the title field is hidden in the form
     * @param bool $new Whether it is a new or existing library.
     */
    public function saveLibraryData(&$librarydata, $new = true) {
        global $DB;

        // Some special properties needs some checking and converting before they can be saved.
        $preloadedjs = $this->library_parameter_values_to_csv($librarydata, 'preloadedJs', 'path');
        $preloadedcss = $this->library_parameter_values_to_csv($librarydata, 'preloadedCss', 'path');
        $droplibrarycss = $this->library_parameter_values_to_csv($librarydata, 'dropLibraryCss', 'machineName');

        if (!isset($librarydata['semantics'])) {
            $librarydata['semantics'] = '';
        }
        if (!isset($librarydata['fullscreen'])) {
            $librarydata['fullscreen'] = 0;
        }
        $embedtypes = '';
        if (isset($librarydata['embedTypes'])) {
            $embedtypes = implode(', ', $librarydata['embedTypes']);
        }

        $library = (object) array(
            'title' => $librarydata['title'],
            'machinename' => $librarydata['machineName'],
            'majorversion' => $librarydata['majorVersion'],
            'minorversion' => $librarydata['minorVersion'],
            'patchversion' => $librarydata['patchVersion'],
            'runnable' => $librarydata['runnable'],
            'fullscreen' => $librarydata['fullscreen'],
            'embedtypes' => $embedtypes,
            'preloadedjs' => $preloadedjs,
            'preloadedcss' => $preloadedcss,
            'droplibrarycss' => $droplibrarycss,
            'semantics' => $librarydata['semantics'],
            'addto' => isset($librarydata['addTo']) ? json_encode($librarydata['addTo']) : null,
            'coremajor' => isset($librarydata['coreApi']['majorVersion']) ? $librarydata['coreApi']['majorVersion'] : null,
            'coreminor' => isset($librarydata['coreApi']['majorVersion']) ? $librarydata['coreApi']['minorVersion'] : null,
            'metadatasettings' => isset($librarydata['metadataSettings']) ? $librarydata['metadataSettings'] : null,
        );

        if ($new) {
            // Create new library and keep track of id.
            $library->id = $DB->insert_record('h5p_libraries', $library);
            $librarydata['libraryId'] = $library->id;
        } else {
            // Update library data.
            $library->id = $librarydata['libraryId'];
            // Save library data.
            $DB->update_record('h5p_libraries', $library);
            // Remove old dependencies.
            $this->deleteLibraryDependencies($librarydata['libraryId']);
        }
    }

    /**
     * Insert new content.
     * Implements insertContent.
     *
     * @param array $content An associative array containing:
     *                       - id: The content id
     *                       - params: The content in json format
     *                       - library: An associative array containing:
     *                         - libraryId: The id of the main library for this content
     *                       - disable: H5P Button display options
     *                       - pathnamehash: The pathnamehash linking the record with the entry in the mdl_files table
     *                       - contenthash: The contenthash linking the record with the entry in the mdl_files table
     * @param int $contentmainid Main id for the content if this is a system that supports versions
     * @return int The ID of the newly inserted content
     */
    public function insertContent($content, $contentmainid = null) {
        return $this->updateContent($content);
    }

    /**
     * Update old content or insert new content.
     * Implements updateContent.
     *
     * @param array $content An associative array containing:
     *                       - id: The content id
     *                       - params: The content in json format
     *                       - library: An associative array containing:
     *                         - libraryId: The id of the main library for this content
     *                       - disable: H5P Button display options
     *                       - pathnamehash: The pathnamehash linking the record with the entry in the mdl_files table
     *                       - contenthash: The contenthash linking the record with the entry in the mdl_files table
     * @param int $contentmainid Main id for the content if this is a system that supports versions
     * @return int The ID of the newly inserted or updated content
     */
    public function updateContent($content, $contentmainid = null) {
        global $DB;

        // If the libraryid declared in the package is empty, get the latest version.
        if (empty($content['library']['libraryId'])) {
            $mainlibrary = $this->get_latest_library_version($content['library']['machineName']);
            if (empty($mainlibrary)) {
                // Raise an error if the main library is not defined and the latest version doesn't exist.
                $message = $this->t('Missing required library @library', ['@library' => $content['library']['machineName']]);
                $this->setErrorMessage($message, 'missing-required-library');
                return false;
            }
            $content['library']['libraryId'] = $mainlibrary->id;
        }

        $content['disable'] = $content['disable'] ?? null;
        // Add title to 'params' to use in the editor.
        if (!empty($content['title'])) {
            $params = json_decode($content['params']);
            $params->title = $content['title'];
            $content['params'] = json_encode($params);
        }
        // Add metadata to 'params'.
        if (!empty($content['metadata'])) {
            $params = json_decode($content['params']);
            $params->metadata = $content['metadata'];
            $content['params'] = json_encode($params);
        }

        $data = [
            'jsoncontent' => $content['params'],
            'displayoptions' => $content['disable'],
            'mainlibraryid' => $content['library']['libraryId'],
            'timemodified' => time(),
            'filtered' => null,
        ];

        if (isset($content['pathnamehash'])) {
            $data['pathnamehash'] = $content['pathnamehash'];
        }

        if (isset($content['contenthash'])) {
            $data['contenthash'] = $content['contenthash'];
        }

        if (!isset($content['id'])) {
            $data['pathnamehash'] = $data['pathnamehash'] ?? '';
            $data['contenthash'] = $data['contenthash'] ?? '';
            $data['timecreated'] = $data['timemodified'];
            $id = $DB->insert_record('h5p', $data);
        } else {
            $id = $data['id'] = $content['id'];
            $DB->update_record('h5p', $data);
        }

        return $id;
    }

    /**
     * Resets marked user data for the given content.
     * Implements resetContentUserData.
     *
     * @param int $contentid The h5p content id
     */
    public function resetContentUserData($contentid) {
        global $DB;

        // Get the component associated to the H5P content to reset.
        $h5p = $DB->get_record('h5p', ['id' => $contentid]);
        if (!$h5p) {
            return;
        }

        $fs = get_file_storage();
        $file = $fs->get_file_by_hash($h5p->pathnamehash);
        if (!$file) {
            return;
        }

        // Reset user data.
        try {
            $xapihandler = handler::create($file->get_component());
            // Reset only entries with 'state' as stateid (the ones restored shouldn't be restored, because the H5P
            // content hasn't been created yet).
            $xapihandler->reset_states($file->get_contextid(), null, 'state');
        } catch (xapi_exception $exception) {
            // This component doesn't support xAPI State, so no content needs to be reset.
            return;
        }
    }

    /**
     * Save what libraries a library is depending on.
     * Implements saveLibraryDependencies.
     *
     * @param int $libraryid Library Id for the library we're saving dependencies for
     * @param array $dependencies List of dependencies as associative arrays containing:
     *                            - machineName: The library machineName
     *                            - majorVersion: The library's majorVersion
     *                            - minorVersion: The library's minorVersion
     * @param string $dependencytype The type of dependency
     */
    public function saveLibraryDependencies($libraryid, $dependencies, $dependencytype) {
        global $DB;

        foreach ($dependencies as $dependency) {
            // Find dependency library.
            $dependencylibrary = $DB->get_record('h5p_libraries',
                array(
                    'machinename' => $dependency['machineName'],
                    'majorversion' => $dependency['majorVersion'],
                    'minorversion' => $dependency['minorVersion']
                )
            );

            // Create relation.
            $DB->insert_record('h5p_library_dependencies', array(
                'libraryid' => $libraryid,
                'requiredlibraryid' => $dependencylibrary->id,
                'dependencytype' => $dependencytype
            ));
        }
    }

    /**
     * Give an H5P the same library dependencies as a given H5P.
     * Implements copyLibraryUsage.
     *
     * @param int $contentid Id identifying the content
     * @param int $copyfromid Id identifying the content to be copied
     * @param int $contentmainid Main id for the content, typically used in frameworks
     */
    public function copyLibraryUsage($contentid, $copyfromid, $contentmainid = null) {
        // Currently not being called.
    }

    /**
     * Deletes content data.
     * Implements deleteContentData.
     *
     * @param int $contentid Id identifying the content
     */
    public function deleteContentData($contentid) {
        global $DB;

        // The user content should be reset (instead of removed), because this method is called when H5P content needs
        // to be updated too (and the previous states must be kept, but reset).
        $this->resetContentUserData($contentid);

        // Remove content.
        $DB->delete_records('h5p', ['id' => $contentid]);

        // Remove content library dependencies.
        $this->deleteLibraryUsage($contentid);
    }

    /**
     * Delete what libraries a content item is using.
     * Implements deleteLibraryUsage.
     *
     * @param int $contentid Content Id of the content we'll be deleting library usage for
     */
    public function deleteLibraryUsage($contentid) {
        global $DB;

        $DB->delete_records('h5p_contents_libraries', array('h5pid' => $contentid));
    }

    /**
     * Saves what libraries the content uses.
     * Implements saveLibraryUsage.
     *
     * @param int $contentid Id identifying the content
     * @param array $librariesinuse List of libraries the content uses
     */
    public function saveLibraryUsage($contentid, $librariesinuse) {
        global $DB;

        $droplibrarycsslist = array();
        foreach ($librariesinuse as $dependency) {
            if (!empty($dependency['library']['dropLibraryCss'])) {
                $droplibrarycsslist = array_merge($droplibrarycsslist,
                        explode(', ', $dependency['library']['dropLibraryCss']));
            }
        }

        foreach ($librariesinuse as $dependency) {
            $dropcss = in_array($dependency['library']['machineName'], $droplibrarycsslist) ? 1 : 0;
            $DB->insert_record('h5p_contents_libraries', array(
                'h5pid' => $contentid,
                'libraryid' => $dependency['library']['libraryId'],
                'dependencytype' => $dependency['type'],
                'dropcss' => $dropcss,
                'weight' => $dependency['weight']
            ));
        }
    }

    /**
     * Get number of content/nodes using a library, and the number of dependencies to other libraries.
     * Implements getLibraryUsage.
     *
     * @param int $id Library identifier
     * @param boolean $skipcontent Optional. Set as true to get number of content instances for library
     * @return array The array contains two elements, keyed by 'content' and 'libraries'.
     *               Each element contains a number
     */
    public function getLibraryUsage($id, $skipcontent = false) {
        global $DB;

        if ($skipcontent) {
            $content = -1;
        } else {
            $sql = "SELECT COUNT(distinct c.id)
                      FROM {h5p_libraries} l
                      JOIN {h5p_contents_libraries} cl ON l.id = cl.libraryid
                      JOIN {h5p} c ON cl.h5pid = c.id
                     WHERE l.id = :libraryid";

            $sqlargs = array(
                'libraryid' => $id
            );

            $content = $DB->count_records_sql($sql, $sqlargs);
        }

        $libraries = $DB->count_records('h5p_library_dependencies', ['requiredlibraryid' => $id]);

        return array(
            'content' => $content,
            'libraries' => $libraries,
        );
    }

    /**
     * Loads a library.
     * Implements loadLibrary.
     *
     * @param string $machinename The library's machine name
     * @param int $majorversion The library's major version
     * @param int $minorversion The library's minor version
     * @return array|bool Returns FALSE if the library does not exist
     *                     Otherwise an associative array containing:
     *                     - libraryId: The id of the library if it is an existing library,
     *                     - title: The library's name,
     *                     - machineName: The library machineName
     *                     - majorVersion: The library's majorVersion
     *                     - minorVersion: The library's minorVersion
     *                     - patchVersion: The library's patchVersion
     *                     - runnable: 1 if the library is a content type, 0 otherwise
     *                     - fullscreen: 1 if the library supports fullscreen, 0 otherwise
     *                     - embedTypes: list of supported embed types
     *                     - preloadedJs: comma separated string with js file paths
     *                     - preloadedCss: comma separated sting with css file paths
     *                     - dropLibraryCss: list of associative arrays containing:
     *                       - machineName: machine name for the librarys that are to drop their css
     *                     - semantics: Json describing the content structure for the library
     *                     - preloadedDependencies(optional): list of associative arrays containing:
     *                       - machineName: Machine name for a library this library is depending on
     *                       - majorVersion: Major version for a library this library is depending on
     *                       - minorVersion: Minor for a library this library is depending on
     *                     - dynamicDependencies(optional): list of associative arrays containing:
     *                       - machineName: Machine name for a library this library is depending on
     *                       - majorVersion: Major version for a library this library is depending on
     *                       - minorVersion: Minor for a library this library is depending on
     */
    public function loadLibrary($machinename, $majorversion, $minorversion) {
        global $DB;

        $library = $DB->get_record('h5p_libraries', array(
            'machinename' => $machinename,
            'majorversion' => $majorversion,
            'minorversion' => $minorversion
        ));

        if (!$library) {
            return false;
        }

        $librarydata = array(
            'libraryId' => $library->id,
            'title' => $library->title,
            'machineName' => $library->machinename,
            'majorVersion' => $library->majorversion,
            'minorVersion' => $library->minorversion,
            'patchVersion' => $library->patchversion,
            'runnable' => $library->runnable,
            'fullscreen' => $library->fullscreen,
            'embedTypes' => $library->embedtypes,
            'preloadedJs' => $library->preloadedjs,
            'preloadedCss' => $library->preloadedcss,
            'dropLibraryCss' => $library->droplibrarycss,
            'semantics'     => $library->semantics
        );

        $sql = 'SELECT hl.id, hl.machinename, hl.majorversion, hl.minorversion, hll.dependencytype
                  FROM {h5p_library_dependencies} hll
                  JOIN {h5p_libraries} hl ON hll.requiredlibraryid = hl.id
                 WHERE hll.libraryid = :libraryid
              ORDER BY hl.id ASC';

        $sqlargs = array(
            'libraryid' => $library->id
        );

        $dependencies = $DB->get_records_sql($sql, $sqlargs);

        foreach ($dependencies as $dependency) {
            $librarydata[$dependency->dependencytype . 'Dependencies'][] = array(
                'machineName' => $dependency->machinename,
                'majorVersion' => $dependency->majorversion,
                'minorVersion' => $dependency->minorversion
            );
        }

        return $librarydata;
    }

    /**
     * Loads library semantics.
     * Implements loadLibrarySemantics.
     *
     * @param string $name Machine name for the library
     * @param int $majorversion The library's major version
     * @param int $minorversion The library's minor version
     * @return string The library's semantics as json
     */
    public function loadLibrarySemantics($name, $majorversion, $minorversion) {
        global $DB;

        $semantics = $DB->get_field('h5p_libraries', 'semantics',
            array(
                'machinename' => $name,
                'majorversion' => $majorversion,
                'minorversion' => $minorversion
            )
        );

        return ($semantics === false ? null : $semantics);
    }

    /**
     * Makes it possible to alter the semantics, adding custom fields, etc.
     * Implements alterLibrarySemantics.
     *
     * @param array $semantics Associative array representing the semantics
     * @param string $name The library's machine name
     * @param int $majorversion The library's major version
     * @param int $minorversion The library's minor version
     */
    public function alterLibrarySemantics(&$semantics, $name, $majorversion, $minorversion) {
        global $PAGE;

        $renderer = $PAGE->get_renderer('core_h5p');
        $renderer->h5p_alter_semantics($semantics, $name, $majorversion, $minorversion);
    }

    /**
     * Delete all dependencies belonging to given library.
     * Implements deleteLibraryDependencies.
     *
     * @param int $libraryid Library identifier
     */
    public function deleteLibraryDependencies($libraryid) {
        global $DB;

        $DB->delete_records('h5p_library_dependencies', array('libraryid' => $libraryid));
    }

    /**
     * Start an atomic operation against the dependency storage.
     * Implements lockDependencyStorage.
     */
    public function lockDependencyStorage() {
        // Library development mode not supported.
    }

    /**
     * Start an atomic operation against the dependency storage.
     * Implements unlockDependencyStorage.
     */
    public function unlockDependencyStorage() {
        // Library development mode not supported.
    }

    /**
     * Delete a library from database and file system.
     * Implements deleteLibrary.
     *
     * @param \stdClass $library Library object with id, name, major version and minor version
     */
    public function deleteLibrary($library) {
        $factory = new \core_h5p\factory();
        \core_h5p\api::delete_library($factory, $library);
    }

    /**
     * Load content.
     * Implements loadContent.
     *
     * @param int $id Content identifier
     * @return array Associative array containing:
     *               - id: Identifier for the content
     *               - params: json content as string
     *               - embedType: list of supported embed types
     *               - disable: H5P Button display options
     *               - title: H5P content title
     *               - slug: Human readable content identifier that is unique
     *               - libraryId: Id for the main library
     *               - libraryName: The library machine name
     *               - libraryMajorVersion: The library's majorVersion
     *               - libraryMinorVersion: The library's minorVersion
     *               - libraryEmbedTypes: CSV of the main library's embed types
     *               - libraryFullscreen: 1 if fullscreen is supported. 0 otherwise
     *               - metadata: The content's metadata
     */
    public function loadContent($id) {
        global $DB;

        $sql = "SELECT hc.id, hc.jsoncontent, hc.displayoptions, hl.id AS libraryid,
                       hl.machinename, hl.title, hl.majorversion, hl.minorversion, hl.fullscreen,
                       hl.embedtypes, hl.semantics, hc.filtered, hc.pathnamehash
                  FROM {h5p} hc
                  JOIN {h5p_libraries} hl ON hl.id = hc.mainlibraryid
                 WHERE hc.id = :h5pid";

        $sqlargs = array(
            'h5pid' => $id
        );

        $data = $DB->get_record_sql($sql, $sqlargs);

        // Return null if not found.
        if ($data === false) {
            return null;
        }

        // Some databases do not support camelCase, so we need to manually
        // map the values to the camelCase names used by the H5P core.
        $content = array(
            'id' => $data->id,
            'params' => $data->jsoncontent,
            // It has been decided that the embedtype will be always set to 'iframe' (at least for now) because the 'div'
            // may cause conflicts with CSS and JS in some cases.
            'embedType' => 'iframe',
            'disable' => $data->displayoptions,
            'title' => $data->title,
            'slug' => H5PCore::slugify($data->title) . '-' . $data->id,
            'filtered' => $data->filtered,
            'libraryId' => $data->libraryid,
            'libraryName' => $data->machinename,
            'libraryMajorVersion' => $data->majorversion,
            'libraryMinorVersion' => $data->minorversion,
            'libraryEmbedTypes' => $data->embedtypes,
            'libraryFullscreen' => $data->fullscreen,
            'metadata' => '',
            'pathnamehash' => $data->pathnamehash
        );

        $params = json_decode($data->jsoncontent);
        if (empty($params->metadata)) {
            $params->metadata = new \stdClass();
        }
        // Add title to metadata.
        if (!empty($params->title) && empty($params->metadata->title)) {
            $params->metadata->title = $params->title;
        }
        $content['metadata'] = $params->metadata;
        $content['params'] = json_encode($params->params ?? $params);

        return $content;
    }

    /**
     * Load dependencies for the given content of the given type.
     * Implements loadContentDependencies.
     *
     * @param int $id Content identifier
     * @param int $type The dependency type
     * @return array List of associative arrays containing:
     *               - libraryId: The id of the library if it is an existing library
     *               - machineName: The library machineName
     *               - majorVersion: The library's majorVersion
     *               - minorVersion: The library's minorVersion
     *               - patchVersion: The library's patchVersion
     *               - preloadedJs(optional): comma separated string with js file paths
     *               - preloadedCss(optional): comma separated sting with css file paths
     *               - dropCss(optional): csv of machine names
     *               - dependencyType: The dependency type
     */
    public function loadContentDependencies($id, $type = null) {
        global $DB;

        $query = "SELECT hcl.id AS unidepid, hl.id AS library_id, hl.machinename AS machine_name,
                         hl.majorversion AS major_version, hl.minorversion AS minor_version,
                         hl.patchversion AS patch_version, hl.preloadedcss AS preloaded_css,
                         hl.preloadedjs AS preloaded_js, hcl.dropcss AS drop_css,
                         hcl.dependencytype as dependency_type
                    FROM {h5p_contents_libraries} hcl
                    JOIN {h5p_libraries} hl ON hcl.libraryid = hl.id
                   WHERE hcl.h5pid = :h5pid";
        $queryargs = array(
            'h5pid' => $id
        );

        if ($type !== null) {
            $query .= " AND hcl.dependencytype = :dependencytype";
            $queryargs['dependencytype'] = $type;
        }

        $query .= " ORDER BY hcl.weight";
        $data = $DB->get_records_sql($query, $queryargs);

        $dependencies = array();
        foreach ($data as $dependency) {
            unset($dependency->unidepid);
            $dependencies[$dependency->machine_name] = H5PCore::snakeToCamel($dependency);
        }

        return $dependencies;
    }

    /**
     * Get stored setting.
     * Implements getOption.
     *
     * To avoid updating the cache libraries when using the Hub selector,
     * {@see \Moodle\H5PEditorAjax::isContentTypeCacheUpdated}, the setting content_type_cache_updated_at
     * always return the current time.
     *
     * @param string $name Identifier for the setting
     * @param string $default Optional default value if settings is not set
     * @return mixed Return  Whatever has been stored as the setting
     */
    public function getOption($name, $default = false) {
        if ($name == core::DISPLAY_OPTION_DOWNLOAD || $name == core::DISPLAY_OPTION_EMBED) {
            // For now, the download and the embed displayoptions are disabled by default, so only will be rendered when
            // defined in the displayoptions DB field.
            // This check should be removed if they are added as new H5P settings, to let admins to define the default value.
            return \Moodle\H5PDisplayOptionBehaviour::CONTROLLED_BY_AUTHOR_DEFAULT_OFF;
        }

        // To avoid update the libraries cache using the Hub selector.
        if ($name == 'content_type_cache_updated_at') {
            return time();
        }

        $value = get_config('core_h5p', $name);
        if ($value === false) {
            return $default;
        }
        return $value;
    }

    /**
     * Stores the given setting.
     * For example when did we last check h5p.org for updates to our libraries.
     * Implements setOption.
     *
     * @param string $name Identifier for the setting
     * @param mixed $value Data Whatever we want to store as the setting
     */
    public function setOption($name, $value) {
        set_config($name, $value, 'core_h5p');
    }

    /**
     * This will update selected fields on the given content.
     * Implements updateContentFields().
     *
     * @param int $id Content identifier
     * @param array $fields Content fields, e.g. filtered
     */
    public function updateContentFields($id, $fields) {
        global $DB;

        $content = new \stdClass();
        $content->id = $id;

        foreach ($fields as $name => $value) {
            // Skip 'slug' as it currently does not exist in the h5p content table.
            if ($name == 'slug') {
                continue;
            }

            $content->$name = $value;
        }

        $DB->update_record('h5p', $content);
    }

    /**
     * Will clear filtered params for all the content that uses the specified.
     * libraries. This means that the content dependencies will have to be rebuilt and the parameters re-filtered.
     * Implements clearFilteredParameters().
     *
     * @param array $libraryids Array of library ids
     */
    public function clearFilteredParameters($libraryids) {
        global $DB;

        if (empty($libraryids)) {
            return;
        }

        list($insql, $inparams) = $DB->get_in_or_equal($libraryids);

        $DB->set_field_select('h5p', 'filtered', null,
            "mainlibraryid $insql", $inparams);
    }

    /**
     * Get number of contents that has to get their content dependencies rebuilt.
     * and parameters re-filtered.
     * Implements getNumNotFiltered().
     *
     * @return int The number of contents that has to get their content dependencies rebuilt
     *             and parameters re-filtered
     */
    public function getNumNotFiltered() {
        global $DB;

        $sql = "SELECT COUNT(id)
                  FROM {h5p}
                 WHERE " . $DB->sql_compare_text('filtered') . " IS NULL";

        return $DB->count_records_sql($sql);
    }

    /**
     * Get number of contents using library as main library.
     * Implements getNumContent().
     *
     * @param int $libraryid The library ID
     * @param array $skip The array of h5p content ID's that should be ignored
     * @return int The number of contents using library as main library
     */
    public function getNumContent($libraryid, $skip = null) {
        global $DB;

        $notinsql = '';
        $params = array();

        if (!empty($skip)) {
            list($sql, $params) = $DB->get_in_or_equal($skip, SQL_PARAMS_NAMED, 'param', false);
            $notinsql = " AND id {$sql}";
        }

        $sql = "SELECT COUNT(id)
                  FROM {h5p}
                 WHERE mainlibraryid = :libraryid {$notinsql}";

        $params['libraryid'] = $libraryid;

        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Determines if content slug is used.
     * Implements isContentSlugAvailable.
     *
     * @param string $slug The content slug
     * @return boolean Whether the content slug is used
     */
    public function isContentSlugAvailable($slug) {
        // By default the slug should be available as it's currently generated as a unique
        // value for each h5p content (not stored in the h5p table).
        return true;
    }

    /**
     * Generates statistics from the event log per library.
     * Implements getLibraryStats.
     *
     * @param string $type Type of event to generate stats for
     * @return array Number values indexed by library name and version
     */
    public function getLibraryStats($type) {
        // Event logs are not being stored.
    }

    /**
     * Aggregate the current number of H5P authors.
     * Implements getNumAuthors.
     *
     * @return int The current number of H5P authors
     */
    public function getNumAuthors() {
        // Currently, H5P authors are not being stored.
    }

    /**
     * Stores hash keys for cached assets, aggregated JavaScripts and
     * stylesheets, and connects it to libraries so that we know which cache file
     * to delete when a library is updated.
     * Implements saveCachedAssets.
     *
     * @param string $key Hash key for the given libraries
     * @param array $libraries List of dependencies(libraries) used to create the key
     */
    public function saveCachedAssets($key, $libraries) {
        global $DB;

        foreach ($libraries as $library) {
            $cachedasset = new \stdClass();
            $cachedasset->libraryid = $library['libraryId'];
            $cachedasset->hash = $key;

            $DB->insert_record('h5p_libraries_cachedassets', $cachedasset);
        }
    }

    /**
     * Locate hash keys for given library and delete them.
     * Used when cache file are deleted.
     * Implements deleteCachedAssets.
     *
     * @param int $libraryid Library identifier
     * @return array List of hash keys removed
     */
    public function deleteCachedAssets($libraryid) {
        global $DB;

        // Get all the keys so we can remove the files.
        $results = $DB->get_records('h5p_libraries_cachedassets', ['libraryid' => $libraryid]);

        $hashes = array_map(function($result) {
            return $result->hash;
        }, $results);

        if (!empty($hashes)) {
            list($sql, $params) = $DB->get_in_or_equal($hashes, SQL_PARAMS_NAMED);
            // Remove all invalid keys.
            $DB->delete_records_select('h5p_libraries_cachedassets', 'hash ' . $sql, $params);

            // Remove also the cachedassets files.
            $fs = new file_storage();
            $fs->deleteCachedAssets($hashes);
        }

        return $hashes;
    }

    /**
     * Get the amount of content items associated to a library.
     * Implements getLibraryContentCount.
     *
     * return array The number of content items associated to a library
     */
    public function getLibraryContentCount() {
        global $DB;

        $contentcount = array();

        $sql = "SELECT h.mainlibraryid,
                       l.machinename,
                       l.majorversion,
                       l.minorversion,
                       COUNT(h.id) AS count
                  FROM {h5p} h
             LEFT JOIN {h5p_libraries} l
                    ON h.mainlibraryid = l.id
              GROUP BY h.mainlibraryid, l.machinename, l.majorversion, l.minorversion";

        // Count content using the same content type.
        $res = $DB->get_records_sql($sql);

        // Extract results.
        foreach ($res as $lib) {
            $contentcount["{$lib->machinename} {$lib->majorversion}.{$lib->minorversion}"] = $lib->count;
        }

        return $contentcount;
    }

    /**
     * Will trigger after the export file is created.
     * Implements afterExportCreated.
     *
     * @param array $content The content
     * @param string $filename The file name
     */
    public function afterExportCreated($content, $filename) {
        // Not being used.
    }

    /**
     * Check whether a user has permissions to execute an action, such as embed H5P content.
     * Implements hasPermission.
     *
     * @param  H5PPermission $permission Permission type
     * @param  int $id Id need by platform to determine permission
     * @return boolean true if the user can execute the action defined in $permission; false otherwise
     */
    public function hasPermission($permission, $id = null) {
        // H5P capabilities have not been introduced.
    }

    /**
     * Replaces existing content type cache with the one passed in.
     * Implements replaceContentTypeCache.
     *
     * @param object $contenttypecache Json with an array called 'libraries' containing the new content type cache
     *                                 that should replace the old one
     */
    public function replaceContentTypeCache($contenttypecache) {
        // Currently, content type caches are not being stored.
    }

    /**
     * Checks if the given library has a higher version.
     * Implements libraryHasUpgrade.
     *
     * @param array $library An associative array containing:
     *                       - machineName: The library machineName
     *                       - majorVersion: The library's majorVersion
     *                       - minorVersion: The library's minorVersion
     * @return boolean Whether the library has a higher version
     */
    public function libraryHasUpgrade($library) {
        global $DB;

        $sql = "SELECT id
                  FROM {h5p_libraries}
                 WHERE machinename = :machinename
                   AND (majorversion > :majorversion1
                    OR (majorversion = :majorversion2 AND minorversion > :minorversion))";

        $results = $DB->get_records_sql(
            $sql,
            array(
                'machinename' => $library['machineName'],
                'majorversion1' => $library['majorVersion'],
                'majorversion2' => $library['majorVersion'],
                'minorversion' => $library['minorVersion']
            ),
            0,
            1
        );

        return !empty($results);
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
        $language = get_html_lang_attribute_value(strtolower(\current_language()));

        // Try to map.
        return $map[$language] ?? $language;
    }

    /**
     * Store messages until they can be printed to the current user.
     *
     * @param string $type Type of messages, e.g. 'info', 'error', etc
     * @param string $newmessage The message
     * @param string $code The message code
     */
    private function set_message(string $type, ?string $newmessage = null, ?string $code = null) {
        global $SESSION;

        // We expect to get out an array of strings when getting info
        // and an array of objects when getting errors for consistency across platforms.
        // This implementation should be improved for consistency across the data type returned here.
        if ($type === 'error') {
            $SESSION->core_h5p_messages[$type][] = (object) array(
                'code' => $code,
                'message' => $newmessage
            );
        } else {
            $SESSION->core_h5p_messages[$type][] = $newmessage;
        }
    }

    /**
     * Convert list of library parameter values to csv.
     *
     * @param array $librarydata Library data as found in library.json files
     * @param string $key Key that should be found in $librarydata
     * @param string $searchparam The library parameter (Default: 'path')
     * @return string Library parameter values separated by ', '
     */
    private function library_parameter_values_to_csv(array $librarydata, string $key, string $searchparam = 'path'): string {
        if (isset($librarydata[$key])) {
            $parametervalues = array();
            foreach ($librarydata[$key] as $file) {
                foreach ($file as $index => $value) {
                    if ($index === $searchparam) {
                        $parametervalues[] = $value;
                    }
                }
            }
            return implode(', ', $parametervalues);
        }
        return '';
    }

    /**
     * Get the latest library version.
     *
     * @param  string $machinename The library's machine name
     * @return stdClass|null An object with the latest library version
     */
    public function get_latest_library_version(string $machinename): ?\stdClass {
        global $DB;

        $libraries = $DB->get_records('h5p_libraries', ['machinename' => $machinename],
            'majorversion DESC, minorversion DESC, patchversion DESC', '*', 0, 1);
        if ($libraries) {
            return reset($libraries);
        }

        return null;
    }

    /**
     * Replace content hub metadata cache
     *
     * @param JsonSerializable $metadata Metadata as received from content hub
     * @param string $lang Language in ISO 639-1
     *
     * @return mixed
     */
    public function replaceContentHubMetadataCache($metadata, $lang) {
        debugging('The replaceContentHubMetadataCache() method is not implemented.', DEBUG_DEVELOPER);
        return null;
    }

    /**
     * Get content hub metadata cache from db
     *
     * @param  string  $lang Language code in ISO 639-1
     *
     * @return JsonSerializable Json string
     */
    public function getContentHubMetadataCache($lang = 'en') {
        debugging('The getContentHubMetadataCache() method is not implemented.', DEBUG_DEVELOPER);
        return null;
    }

    /**
     * Get time of last content hub metadata check
     *
     * @param  string  $lang Language code iin ISO 639-1 format
     *
     * @return string|null Time in RFC7231 format
     */
    public function getContentHubMetadataChecked($lang = 'en') {
        debugging('The getContentHubMetadataChecked() method is not implemented.', DEBUG_DEVELOPER);
        return null;
    }

    /**
     * Set time of last content hub metadata check
     *
     * @param  int|null  $time Time in RFC7231 format
     * @param  string  $lang Language code iin ISO 639-1 format
     *
     * @return bool True if successful
     */
    public function setContentHubMetadataChecked($time, $lang = 'en') {
        debugging('The setContentHubMetadataChecked() method is not implemented.', DEBUG_DEVELOPER);
        return false;
    }
}
