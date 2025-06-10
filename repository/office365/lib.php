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
 * Office365 repository lib.
 *
 * @package repository_office365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

use local_o365\httpclient;
use local_o365\oauth2\clientdata;
use local_o365\oauth2\token;
use local_o365\rest\unified;
use local_o365\utils;

/**
 * Microsoft 365 repository.
 */
class repository_office365 extends repository {

    /** @var httpclient An HTTP client to use. */
    protected $httpclient;

    /** @var bool Whether the Microsoft Graph API is configured. */
    protected $unifiedconfigured = false;

    /** @var clientdata A clientdata object to use with an o365 api class. */
    protected $clientdata = null;

    /**
     * Constructor.
     *
     * @param int $repositoryid repository instance id
     * @param int|stdClass $context a context id or context object
     * @param array $options repository options
     * @param int $readonly indicate this repo is readonly or not
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array(), $readonly = 0) {
        parent::__construct($repositoryid, $context, $options, $readonly);
        $this->httpclient = new httpclient();
        if (utils::is_connected()) {
            $this->clientdata = clientdata::instance_from_oidc();
        }
        $this->unifiedconfigured = unified::is_configured();
    }

    /**
     * Get a Microsoft Graph API token.
     *
     * @param bool $system If true, get a system API ser token instead of the user's token.
     * @param int|null $userid The userid to get a token for. If null, the current user will be used.
     * @return token A Microsoft Graph API token object.
     */
    protected function get_unified_token($system = false, $userid = null) {
        global $USER;
        $resource = unified::get_tokenresource();
        if ($system === true) {
            return utils::get_app_or_system_token($resource, $this->clientdata, $this->httpclient);
        } else {
            $userid = (!empty($userid)) ? $userid : $USER->id;
            return token::instance($userid, $resource, $this->clientdata, $this->httpclient);
        }
    }

    /**
     * Get a Microsoft Graph API client.
     *
     * @param bool $system If true, get a system API ser token instead of the user's token.
     * @param int|null $userid The userid to get an API client for. If null, the current user will be used.
     * @return unified|bool A Microsoft Graph API client object.
     */
    protected function get_unified_apiclient($system = false, $userid = null) {
        if ($this->unifiedconfigured === true) {
            $token = $this->get_unified_token($system, $userid);
            if (!empty($token)) {
                return new unified($token, $this->httpclient);
            }
        }
        return false;
    }

    /**
     * Given a path, and perhaps a search, get a list of files.
     *
     * See details on {@link http://docs.moodle.org/dev/Repository_plugins}
     *
     * @param string $path this parameter can a folder name, or a identification of folder
     * @param string $page the page number of file list
     * @return array the list of files, including meta infomation, containing the following keys
     *           manage, url to manage url
     *           client_id
     *           login, login form
     *           repo_id, active repository id
     *           login_btn_action, the login button action
     *           login_btn_label, the login button label
     *           total, number of results
     *           perpage, items per page
     *           page
     *           pages, total pages
     *           issearchresult, is it a search result?
     *           list, file list
     *           path, current path and parent path
     */
    public function get_listing($path = '', $page = '') {
        global $OUTPUT, $SESSION, $USER;

        if (utils::is_connected() !== true) {
            throw new moodle_exception('errorauthoidcnotconfig', 'repository_office365');
        }

        $clientid = optional_param('client_id', '', PARAM_TEXT);
        if (!empty($clientid)) {
            $SESSION->repository_office365['curpath'][$clientid] = $path;
        }

        $list = [];
        $breadcrumb = [['name' => $this->name, 'path' => '/']];

        $unifiedactive = false;
        $trendingactive = false;
        $trendingdisabled = get_config('office365', 'trendinggroup');
        if ($this->unifiedconfigured === true) {
            $unifiedtoken = $this->get_unified_token();
            if (!empty($unifiedtoken)) {
                $unifiedactive = true;
                $trendingactive = (empty($trendingdisabled)) ? true : false;
            }
        }

        $courses = enrol_get_users_courses($USER->id, true);
        $showgroups = false;
        $coursegroupdisabled = get_config('office365', 'coursegroup');
        if (unified::is_configured() === true  && empty($coursegroupdisabled)) {
            foreach ($courses as $course) {
                if (\local_o365\feature\coursesync\utils::is_course_sync_enabled($course->id)) {
                    $showgroups = true;
                    break;
                }
            }
        }

        if (strpos($path, '/my/') === 0) {
            if ($unifiedactive === true) {
                // Path is in my files.
                [$list, $breadcrumb] = $this->get_listing_my_unified(substr($path, 3));
            }
        } else if (strpos($path, '/groups/') === 0) {
            if ($showgroups === true) {
                // Path is in group files.
                [$list, $breadcrumb] = $this->get_listing_groups(substr($path, 7));
            }
        } else if (strpos($path, '/trending/') === 0) {
            if ($trendingactive === true) {
                // Path is in trending files.
                [$list, $breadcrumb] = $this->get_listing_trending_unified(substr($path, 9));
            }
        } else {
            if ($unifiedactive === true) {
                $list[] = [
                    'title' => get_string('myfiles', 'repository_office365'),
                    'path' => '/my/',
                    'thumbnail' => $OUTPUT->pix_url('onedrive', 'repository_office365')->out(false),
                    'children' => [],
                ];
            }
            if ($showgroups === true) {
                $list[] = [
                    'title' => get_string('groups', 'repository_office365'),
                    'path' => '/groups/',
                    'thumbnail' => $OUTPUT->pix_url('coursegroups', 'repository_office365')->out(false),
                    'children' => [],
                ];
            }
            if ($trendingactive === true) {
                $list[] = [
                    'title' => get_string('trendingaround', 'repository_office365'),
                    'path' => '/trending/',
                    'thumbnail' => $OUTPUT->pix_url('delve', 'repository_office365')->out(false),
                    'children' => [],
                ];
            }
        }
        if ($this->path_is_upload($path) === true) {
            return [
                'dynload' => true,
                'nologin' => true,
                'nosearch' => true,
                'path' => $breadcrumb,
                'upload' => [
                    'label' => get_string('file', 'repository_office365'),
                ],
            ];
        }

        return [
            'dynload' => true,
            'nologin' => true,
            'nosearch' => true,
            'list' => $list,
            'path' => $breadcrumb,
        ];
    }

    /**
     * Determine whether a given path is an upload path.
     *
     * @param string $path A path to check.
     * @return bool Whether the path is an upload path.
     */
    protected function path_is_upload($path) {
        return (substr($path, -strlen('/upload/')) === '/upload/') ? true : false;
    }

    /**
     * Process uploaded file.
     *
     * @param string $saveasfilename
     * @param int $maxbytes
     * @return array Array of uploaded file information.
     */
    public function upload($saveasfilename, $maxbytes) {
        global $CFG, $USER, $SESSION, $DB;

        $savepath = optional_param('savepath', '/', PARAM_PATH);
        $itemid = optional_param('itemid', 0, PARAM_INT);
        $license = optional_param('license', $CFG->sitedefaultlicense, PARAM_TEXT);
        $author = optional_param('author', '', PARAM_TEXT);
        $clientid = optional_param('client_id', '', PARAM_TEXT);

        $filepath = '/';
        if (!empty($SESSION->repository_office365)) {
            if (isset($SESSION->repository_office365['curpath']) && isset($SESSION->repository_office365['curpath'][$clientid])) {
                $filepath = $SESSION->repository_office365['curpath'][$clientid];
                if (strpos($filepath, '/my/') === 0) {
                    $clienttype = 'onedrive';
                    $filepath = substr($filepath, 3);
                } else if (strpos($filepath, '/groups/') === 0) {
                    $clienttype = 'onedrivegroup';
                    $filepath = substr($filepath, 7);
                } else {
                    $errmsg = get_string('errorbadclienttype', 'repository_office365');
                    $debugdata = [
                        'filepath' => $filepath,
                    ];
                    utils::debug($errmsg, __METHOD__, $debugdata);
                    throw new moodle_exception('errorbadclienttype', 'repository_office365');
                }
            }
        }
        if ($this->path_is_upload($filepath) === true) {
            $filepath = substr($filepath, 0, -strlen('/upload/'));
        }
        $filename = (!empty($saveasfilename)) ? $saveasfilename : $_FILES['repo_upload_file']['name'];
        $filename = clean_param($filename, PARAM_FILE);
        $content = file_get_contents($_FILES['repo_upload_file']['tmp_name']);

        if ($clienttype === 'onedrive') {
            if ($this->unifiedconfigured === true) {
                $apiclient = $this->get_unified_apiclient();
                $parentid = (!empty($filepath)) ? substr($filepath, 1) : '';
                $o365userid = utils::get_o365_userid($USER->id);
                $result = $apiclient->create_file($parentid, $filename, $content, 'application/octet-stream', $o365userid);
            }
            $source = $this->pack_reference(['id' => $result['id'], 'source' => 'onedrive']);
        } else if ($clienttype === 'onedrivegroup') {
            if ($this->unifiedconfigured === true) {
                $apiclient = $this->get_unified_apiclient();
                $parentid = (!empty($filepath)) ? substr($filepath, 1) : '';
                $pathtrimmed = trim($parentid, '/');
                $pathparts = explode('/', $pathtrimmed);
                $coursesbyid = enrol_get_users_courses($USER->id, true);
                if (!is_numeric($pathparts[0]) || !isset($coursesbyid[$pathparts[0]])
                        || \local_o365\feature\coursesync\utils::is_course_sync_enabled($pathparts[0]) !== true) {
                    utils::debug(get_string('errorbadpath', 'repository_office365'), __METHOD__, ['path' => $filepath]);
                    throw new moodle_exception('errorbadpath', 'repository_office365');
                }
                $courseid = (int)$pathparts[0];
                if (!is_numeric($pathparts[1]) && $pathparts[1] !== 'coursegroup') {
                    utils::debug(get_string('errorbadpath', 'repository_office365'), __METHOD__, ['path' => $filepath]);
                    throw new moodle_exception('errorbadpath', 'repository_office365');
                }
                if ($pathparts[1] === 'coursegroup') {
                    $filters = ['type' => 'group', 'subtype' => 'course', 'moodleid' => $courseid];
                    $group = $DB->get_record('local_o365_objects', $filters);
                } else {
                    $groupid = (int)$pathparts[1];
                    $group = $DB->get_record('groups', ['id' => $groupid]);
                    $filters = ['type' => 'group', 'subtype' => 'usergroup', 'moodleid' => $groupid];
                    $group = $DB->get_record('local_o365_objects', $filters);
                }
                try {
                    $result = $apiclient->create_group_file($group->objectid, $filename, $content);
                    $source = $this->pack_reference(['id' => $result['id'], 'source' => $clienttype,
                        'groupid' => $group->objectid]);
                } catch (\Exception $e) {
                    $errmsg = 'Exception when uploading share point files for group';
                    $debugdata = [
                        'fullpath' => $filepath,
                        'message' => $e->getMessage(),
                        'groupid' => $group->objectid,
                    ];
                    utils::debug($errmsg, __METHOD__, $debugdata);
                    $source = $this->pack_reference([]);
                }
            } else {
                utils::debug('Tried to Upload a onedrive group file while the graph api is disabled.', __METHOD__);
                throw new moodle_exception('errorwhileupload', 'repository_office365');
            }
        } else {
            $errmsg = get_string('errorbadclienttype', 'repository_office365');
            $debugdata = [
                'clienttype' => $clienttype,
            ];
            utils::debug($errmsg, __METHOD__, $debugdata);
            throw new moodle_exception('errorbadclienttype', 'repository_office365');
        }

        $downloadedfile = $this->get_file($source, $filename);
        $record = new stdClass();
        $record->filename = $filename;
        $record->filepath = $savepath;
        $record->component = 'user';
        $record->filearea = 'draft';
        $record->itemid = $itemid;
        $record->license = $license;
        $record->author = $author;
        $usercontext = context_user::instance($USER->id);
        $now = time();
        $record->contextid = $usercontext->id;
        $record->timecreated = $now;
        $record->timemodified = $now;
        $record->userid = $USER->id;
        $record->sortorder = 0;
        $record->source = $this->build_source_field($source);
        $info = repository::move_to_filepool($downloadedfile['path'], $record);

        return $info;
    }

    /**
     * Get listing for a group folder.
     *
     * @param string $path Folder path.
     * @return array List of $list array and $path array.
     */
    protected function get_listing_groups($path = '') {
        global $OUTPUT, $USER, $DB;

        $list = [];
        $breadcrumb = [
            ['name' => $this->name, 'path' => '/'],
            ['name' => get_string('groups', 'repository_office365'), 'path' => '/groups/'],
        ];

        $coursesbyid = enrol_get_users_courses($USER->id, true);

        if ($path === '/') {
            // Show available courses.
            $enabledcourses = \local_o365\feature\coursesync\utils::get_enabled_courses();
            foreach ($coursesbyid as $course) {
                if ($enabledcourses === true || in_array($course->id, $enabledcourses)) {
                    $list[] = [
                        'title' => $course->shortname,
                        'path' => '/groups/'.$course->id,
                        'thumbnail' => $OUTPUT->pix_url(file_folder_icon(90))->out(false),
                        'children' => [],
                    ];
                }
            }
        } else {
            $pathtrimmed = trim($path, '/');
            $pathparts = explode('/', $pathtrimmed);
            if (!is_numeric($pathparts[0]) || !isset($coursesbyid[$pathparts[0]])
                    || \local_o365\feature\coursesync\utils::is_course_sync_enabled($pathparts[0]) !== true) {
                utils::debug(get_string('errorbadpath', 'repository_office365'), __METHOD__, ['path' => $path]);
                throw new moodle_exception('errorbadpath', 'repository_office365');
            }
            $courseid = (int)$pathparts[0];
            $curpath = '/groups/'.$courseid;
            $breadcrumb[] = ['name' => $coursesbyid[$courseid]->shortname, 'path' => $curpath];

            $sql = 'SELECT g.*
                      FROM {groups} g
                      JOIN {groups_members} m ON m.groupid = g.id
                     WHERE m.userid = ? AND g.courseid = ?';
            $coursegroups = $DB->get_records_sql($sql, [$USER->id, $courseid]);

            if (count($pathparts) === 1) {
                $list[] = [
                    'title' => get_string('defaultgroupsfolder', 'repository_office365'),
                    'path' => $curpath.'/coursegroup/',
                    'thumbnail' => $OUTPUT->pix_url(file_folder_icon(90))->out(false),
                    'children' => [],
                ];

                foreach ($coursegroups as $group) {
                    $list[] = [
                        'title' => $group->name,
                        'path' => $curpath.'/'.$group->id.'/',
                        'thumbnail' => $OUTPUT->pix_url(file_folder_icon(90))->out(false),
                        'children' => [],
                    ];
                }
            } else {
                // Validate the received group identifier.
                if (!is_numeric($pathparts[1]) && $pathparts[1] !== 'coursegroup') {
                    utils::debug(get_string('errorbadpath', 'repository_office365'), __METHOD__, ['path' => $path]);
                    throw new moodle_exception('errorbadpath', 'repository_office365');
                }
                $curpath .= '/'.$pathparts[1].'/';
                if ($pathparts[1] === 'coursegroup') {
                    $breadcrumb[] = ['name' => get_string('defaultgroupsfolder', 'repository_office365'), 'path' => $curpath];
                    $filters = ['type' => 'group', 'subtype' => 'course', 'moodleid' => $courseid];
                    $group = $DB->get_record('local_o365_objects', $filters);
                } else {
                    // Validate the user is a member of the group.
                    if (!isset($coursegroups[$pathparts[1]])) {
                        utils::debug(get_string('errorbadpath', 'repository_office365'), __METHOD__, ['path' => $path]);
                        throw new moodle_exception('errorbadpath', 'repository_office365');
                    }
                    $groupid = (int)$pathparts[1];
                    $group = $DB->get_record('groups', ['id' => $groupid]);
                    $breadcrumb[] = ['name' => $group->name, 'path' => $curpath];
                    $filters = ['type' => 'group', 'subtype' => 'usergroup', 'moodleid' => $groupid];
                    $group = $DB->get_record('local_o365_objects', $filters);
                }

                $intragrouppath = $pathparts;
                unset($intragrouppath[0], $intragrouppath[1]);
                $curparent = trim(end($intragrouppath));

                if (!empty($group)) {
                    if ($curparent === 'upload') {
                        $breadcrumb[] = ['name' => get_string('upload', 'repository_office365'), 'path' => $curpath.'upload/'];
                    } else {
                        $unified = $this->get_unified_apiclient();

                        if (!empty($curparent)) {
                            $metadata = $unified->get_group_file_metadata($group->objectid, $curparent);
                            if (!empty($metadata['parentReference']) && !empty($metadata['parentReference']['path'])) {
                                $parentrefpath = substr($metadata['parentReference']['path'],
                                    (strpos($metadata['parentReference']['path'], ':') + 1));
                                $cache = cache::make('repository_office365', 'unifiedgroupfolderids');
                                $cache->set($parentrefpath.'/'.$metadata['name'], $metadata['id']);
                                if (!empty($parentrefpath)) {
                                    $parentrefpath = explode('/', trim($parentrefpath, '/'));
                                    $currentfullpath = '';
                                    foreach ($parentrefpath as $folder) {
                                        $currentfullpath .= '/'.$folder;
                                        $folderid = $cache->get($currentfullpath);
                                        $breadcrumb[] = ['name' => $folder, 'path' => $curpath.$folderid];
                                    }
                                }
                            }
                            $breadcrumb[] = ['name' => $metadata['name'], 'path' => $curpath.$metadata['id']];
                        }
                        try {
                            $filesresults = $unified->get_group_files($group->objectid, $curparent);
                            $contents = $filesresults['value'];
                            while (!empty($filesresults['@odata.nextLink'])) {
                                $nextlink = parse_url($filesresults['@odata.nextLink']);
                                $filesresults = [];
                                if (isset($nextlink['query'])) {
                                    $query = [];
                                    parse_str($nextlink['query'], $query);
                                    if (isset($query['$skiptoken'])) {
                                        $filesresults = $unified->get_group_files($group->objectid, $curparent,
                                            $query['$skiptoken']);
                                        $contents = array_merge($contents, $filesresults['value']);
                                    }
                                }
                            }

                            $list = $this->contents_api_response_to_list($contents, $path, 'unifiedgroup', $group->objectid, true);
                        } catch (\Exception $e) {
                            $errmsg = 'Exception when retrieving share point files for group';
                            $debugdata = [
                                'fullpath' => $path,
                                'message' => $e->getMessage(),
                                'groupid' => $group->objectid,
                            ];
                            utils::debug($errmsg, __METHOD__, $debugdata);
                        }
                    }
                } else {
                    utils::debug('Could not file group object record', __METHOD__, ['path' => $path]);
                }
            }
        }

        return [$list, $breadcrumb];
    }

    /**
     * Get listing for a personal onedrive folder using the Microsoft Graph API.
     *
     * @param string $path Folder path.
     * @return array List of $list array and $path array.
     */
    protected function get_listing_my_unified($path = '') {
        global $USER;

        $path = (empty($path)) ? '/' : $path;

        $list = [];

        $unified = $this->get_unified_apiclient();
        $realpath = $path;

        // Generate path.
        $strmyfiles = get_string('myfiles', 'repository_office365');
        $breadcrumb = [['name' => $this->name, 'path' => '/'], ['name' => $strmyfiles, 'path' => '/my/']];

        if ($this->path_is_upload($path) === true) {
            $realpath = substr($path, 0, -strlen('/upload/'));
        } else {
            try {
                $o365userid = utils::get_o365_userid($USER->id);

                $filesresults = $unified->get_user_files($realpath, $o365userid);
                $contents = $filesresults['value'];
                while (!empty($filesresults['@odata.nextLink'])) {
                    $nextlink = parse_url($filesresults['@odata.nextLink']);
                    $filesresults = [];
                    if (isset($nextlink['query'])) {
                        $query = [];
                        parse_str($nextlink['query'], $query);
                        if (isset($query['$skiptoken'])) {
                            $filesresults = $unified->get_user_files($realpath, $o365userid, $query['$skiptoken']);
                            $contents = array_merge($contents, $filesresults['value']);
                        }
                    }
                }

                $list = $this->contents_api_response_to_list($contents, $realpath, 'unified');
            } catch (\Exception $e) {
                $errmsg = 'Exception when retrieving personal onedrive files for folder';
                $debugdata = [
                    'fullpath' => $path,
                    'message' => $e->getMessage(),
                ];
                utils::debug($errmsg, __METHOD__, $debugdata);
                return [[], $breadcrumb];
            }
        }

        if ($realpath !== '/') {
            $o365userid = utils::get_o365_userid($USER->id);
            $metadata = $unified->get_file_metadata($realpath, $o365userid);
            if (!empty($metadata['parentReference']) && !empty($metadata['parentReference']['path'])) {
                $parentrefpath = substr($metadata['parentReference']['path']
                  , (strpos($metadata['parentReference']['path'], ':') + 1));
                $cache = cache::make('repository_office365', 'unifiedfolderids');
                $result = $cache->set($parentrefpath.'/'.$metadata['name'], $metadata['id']);
                if (!empty($parentrefpath)) {
                    $parentrefpath = explode('/', trim($parentrefpath, '/'));
                    $currentfullpath = '';
                    foreach ($parentrefpath as $folder) {
                        $currentfullpath .= '/'.$folder;
                        $folderid = $cache->get($currentfullpath);
                        $breadcrumb[] = ['name' => $folder, 'path' => '/my/'.$folderid];
                    }
                }
            }
            $breadcrumb[] = ['name' => $metadata['name'], 'path' => '/my/'.$metadata['id']];
        }

        if ($this->path_is_upload($path) === true) {
            $breadcrumb[] = ['name' => get_string('upload', 'repository_office365'),
                'path' => '/my/' . $metadata['id'] . '/upload/'];
        }

        return [$list, $breadcrumb];
    }

    /**
     * Get listing for a trending files folder using the unified api.
     *
     * @param string $path Folder path.
     * @return array List of $list array and $path array.
     */
    protected function get_listing_trending_unified($path = '') {
        global $USER;

        $path = (empty($path)) ? '/' : $path;
        $unified = $this->get_unified_apiclient();
        $realpath = $path;
        try {
            $o365upn = utils::get_o365_upn($USER->id);
            $filesresults = $unified->get_trending_files($o365upn);
            $contents = $filesresults['value'];
            while (!empty($filesresults['@odata.nextLink'])) {
                $nextlink = parse_url($filesresults['@odata.nextLink']);
                $filesresults = [];
                if (isset($nextlink['query'])) {
                    $query = [];
                    parse_str($nextlink['query'], $query);
                    if (isset($query['$skiptoken'])) {
                        $filesresults = $unified->get_trending_files($o365upn, $query['$skiptoken']);
                        $contents = array_merge($contents, $filesresults['value']);
                    }
                }
            }

            $list = $this->contents_api_response_to_list($contents, $realpath, 'trendingaround', null, false);
        } catch (\Exception $e) {
            $errmsg = 'Exception when retrieving personal trending files';
            $debugdata = [
                'fullpath' => $path,
                'message' => $e->getMessage(),
            ];
            utils::debug($errmsg, __METHOD__, $debugdata);
            $list = [];
        }

        // Generate path.
        $strtrendingfiles = get_string('trendingaround', 'repository_office365');
        $breadcrumb = [['name' => $this->name, 'path' => '/'], ['name' => $strtrendingfiles, 'path' => '/trending/']];
        return [$list, $breadcrumb];
    }

    /**
     * Transform a onedrive API response for a folder into a list parameter that the respository class can understand.
     *
     * @param string $response The response from the API.
     * @param string $path The list path.
     * @param string $clienttype The type of client that the response is from. onedrive/unified
     * @param string $parentinfo Client type-specific parent information.
     *                               If using the unifiedgroup clienttype, this is the parent group ID.
     * @param bool $addupload Whether to add the "Upload" file item.
     * @return array A $list array to be used by the respository class in get_listing.
     */
    protected function contents_api_response_to_list($response, $path, $clienttype, $parentinfo = null, $addupload = true) {
        global $OUTPUT;
        $list = [];
        if ($clienttype === 'onedrive') {
            $pathprefix = '/my'.$path;
            $uploadpathprefix = $pathprefix;
        } else if ($clienttype === 'unified') {
            $pathprefix = '/my';
            $uploadpathprefix = $pathprefix.$path;
        } else if ($clienttype === 'unifiedgroup') {
            $pathprefix = '/groups'.$path;
            $uploadpathprefix = $pathprefix;
        } else if ($clienttype === 'trendingaround') {
            $pathprefix = '/my';
        }

        if ($addupload === true) {
            $list[] = [
                'title' => get_string('upload', 'repository_office365'),
                'path' => $uploadpathprefix.'/upload/',
                'thumbnail' => $OUTPUT->pix_url('a/add_file')->out(false),
                'children' => [],
            ];
        }

        if (isset($response)) {
            foreach ($response as $content) {
                if ($clienttype === 'unified' || $clienttype === 'unifiedgroup') {
                    $itempath = $pathprefix . '/' . $content['id'];
                    if (isset($content['folder'])) {
                        $list[] = [
                            'title' => $content['name'],
                            'path' => $itempath,
                            'thumbnail' => $OUTPUT->pix_url(file_folder_icon(90))->out(false),
                            'date' => strtotime($content['createdDateTime']),
                            'datemodified' => strtotime($content['lastModifiedDateTime']),
                            'datecreated' => strtotime($content['createdDateTime']),
                            'children' => [],
                        ];
                    } else if (isset($content['file'])) {
                        $url = $content['webUrl'] . '?web=1';
                        if ($clienttype === 'unified') {
                            $source = [
                                'id' => $content['id'],
                                'source' => 'onedrive',
                            ];
                        } else if ($clienttype === 'unifiedgroup') {
                            $source = [
                                'id' => $content['id'],
                                'source' => 'onedrivegroup',
                                'groupid' => $parentinfo,
                            ];
                        }

                        $author = '';
                        if (!empty($content['createdBy']['user']['displayName'])) {
                            $author = $content['createdBy']['user']['displayName'];
                            $author = explode(',', $author);
                            $author = $author[0];
                        }

                        $list[] = [
                            'title' => $content['name'],
                            'date' => strtotime($content['createdDateTime']),
                            'datemodified' => strtotime($content['lastModifiedDateTime']),
                            'datecreated' => strtotime($content['createdDateTime']),
                            'size' => $content['size'],
                            'url' => $url,
                            'thumbnail' => $OUTPUT->pix_url(file_extension_icon($content['name'], 90))->out(false),
                            'author' => $author,
                            'source' => $this->pack_reference($source),
                        ];
                    }
                } else if ($clienttype === 'trendingaround') {
                    if (isset($content['folder'])) {
                        $list[] = [
                            'title' => $content['name'],
                            'path' => $pathprefix . '/' . $content['name'],
                            'thumbnail' => $OUTPUT->pix_url(file_folder_icon(90))->out(false),
                            'date' => strtotime($content['DateTimeCreated']),
                            'datemodified' => strtotime($content['DateTimeLastModified']),
                            'datecreated' => strtotime($content['DateTimeCreated']),
                            'children' => [],
                        ];
                    } else {
                        $url = $content['webUrl'] . '?web=1';
                        $source = [
                            'id' => $content['@odata.id'],
                            'source' => 'trendingaround',
                        ];

                        $list[] = [
                            'title' => $content['name'],
                            'date' => strtotime($content['DateTimeCreated']),
                            'datemodified' => strtotime($content['DateTimeLastModified']),
                            'datecreated' => strtotime($content['DateTimeCreated']),
                            'url' => $url,
                            'thumbnail' => $OUTPUT->pix_url(file_extension_icon($content['name'], 90))->out(false),
                            'source' => $this->pack_reference($source),
                        ];
                    }
                } else {
                    $itempath = $pathprefix . '/' . $content['name'];
                    if ($content['type'] === 'Folder') {
                        $list[] = [
                            'title' => $content['name'],
                            'path' => $itempath,
                            'thumbnail' => $OUTPUT->pix_url(file_folder_icon(90))->out(false),
                            'date' => strtotime($content['dateTimeCreated']),
                            'datemodified' => strtotime($content['dateTimeLastModified']),
                            'datecreated' => strtotime($content['dateTimeCreated']),
                            'children' => [],
                        ];
                    } else if ($content['type'] === 'File') {
                        $url = $content['webUrl'] . '?web=1';
                        $source = [
                            'id' => $content['id'],
                            'source' => 'onedrive',
                        ];

                        $author = '';
                        if (!empty($content['createdBy']['user']['displayName'])) {
                            $author = $content['createdBy']['user']['displayName'];
                            $author = explode(',', $author);
                            $author = $author[0];
                        }

                        $list[] = [
                            'title' => $content['name'],
                            'date' => strtotime($content['dateTimeCreated']),
                            'datemodified' => strtotime($content['dateTimeLastModified']),
                            'datecreated' => strtotime($content['dateTimeCreated']),
                            'size' => $content['size'],
                            'url' => $url,
                            'thumbnail' => $OUTPUT->pix_url(file_extension_icon($content['name'], 90))->out(false),
                            'author' => $author,
                            'source' => $this->pack_reference($source),
                        ];
                    }
                }
            }
        }

        return $list;
    }

    /**
     * Tells how the file can be picked from this repository
     *
     * Maximum value is FILE_INTERNAL | FILE_EXTERNAL | FILE_REFERENCE
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_INTERNAL | FILE_EXTERNAL | FILE_REFERENCE;
    }

    /**
     * Downloads a file from external repository and saves it in temp dir
     *
     * @param string $reference The file reference.
     * @param string $filename filename (without path) to save the downloaded file in the temporary directory, if omitted
     *                         or file already exists the new filename will be generated
     * @return array with elements:
     *   path: internal location of the file
     *   url: URL to the source (from parameters)
     */
    public function get_file($reference, $filename = '') {
        global $USER;

        $reference = $this->unpack_reference($reference);

        if ($reference['source'] === 'onedrive') {
            if ($this->unifiedconfigured === true) {
                $sourceclient = $this->get_unified_apiclient();
            }
            if (empty($sourceclient)) {
                utils::debug('Could not construct onedrive api.', __METHOD__);
                throw new moodle_exception('errorwhiledownload', 'repository_office365');
            }
            $o365userid = utils::get_o365_userid($USER->id);
            $file = $sourceclient->get_file_by_id($reference['id'], $o365userid);
        } else if ($reference['source'] === 'onedrivegroup') {
            if ($this->unifiedconfigured === true) {
                $sourceclient = $this->get_unified_apiclient();
            } else {
                utils::debug('Tried to access a onedrive group file while the graph api is disabled.', __METHOD__);
                throw new moodle_exception('errorwhiledownload', 'repository_office365');
            }
            $file = $sourceclient->get_group_file_by_id($reference['groupid'], $reference['id']);
        } else if ($reference['source'] === 'trendingaround') {
            if ($this->unifiedconfigured === true) {
                $sourceclient = $this->get_unified_apiclient();
            }
            if (empty($sourceclient)) {
                utils::debug('Could not construct unified api.', __METHOD__);
                throw new moodle_exception('errorwhiledownload', 'repository_office365');
            }
            $file = $sourceclient->get_file_by_url($reference['url']);
        }

        if (!empty($file)) {
            $path = $this->prepare_file($filename);
            if (!empty($path)) {
                $result = file_put_contents($path, $file);
            }
        }
        if (empty($result)) {
            $errmsg = get_string('errorwhiledownload', 'repository_office365');
            $debugdata = [
                'reference' => $reference,
                'filename' => $filename,
            ];
            utils::debug($errmsg, __METHOD__, $debugdata);
            throw new moodle_exception('errorwhiledownload', 'repository_office365');
        }
        return ['path' => $path, 'url' => $reference];
    }

    /**
     * Pack file reference information into a string.
     *
     * @param array $reference The information to pack.
     * @return string The packed information.
     */
    protected function pack_reference($reference) {
        return base64_encode(serialize($reference));
    }

    /**
     * Unpack file reference information from a string.
     *
     * @param string $reference The information to unpack.
     * @return array The unpacked information.
     */
    protected function unpack_reference($reference) {
        return unserialize(base64_decode($reference));
    }

    /**
     * Prepare file reference information
     *
     * @param string $source source of the file, returned by repository as 'source' and received back from user (not cleaned)
     * @return string file reference, ready to be stored
     */
    public function get_file_reference($source) {
        global $USER;

        $sourceunpacked = $this->unpack_reference($source);
        if (isset($sourceunpacked['source']) && isset($sourceunpacked['id'])) {
            $fileid = $sourceunpacked['id'];
            $filesource = $sourceunpacked['source'];

            $reference = [
                'source' => $filesource,
                'id' => $fileid,
                'url' => '',
            ];

            if (isset($sourceunpacked['url'])) {
                $reference['url'] = $sourceunpacked['url'];
            }
            if (isset($sourceunpacked['downloadurl'])) {
                $reference['downloadurl'] = $sourceunpacked['downloadurl'];
            }

            try {
                if ($filesource === 'onedrive') {
                    if ($this->unifiedconfigured === true) {
                        $sourceclient = $this->get_unified_apiclient();
                        $o365userid = utils::get_o365_userid($USER->id);
                        $reference['url'] = $sourceclient->get_sharing_link($fileid, $o365userid);
                    }
                } else if ($filesource === 'onedrivegroup') {
                    if ($this->unifiedconfigured !== true) {
                        utils::debug('Tried to access a onedrive group file while the graph api is disabled.', __METHOD__);
                        throw new moodle_exception('errorwhiledownload', 'repository_office365');
                    }
                    $sourceclient = $this->get_unified_apiclient();
                    $reference['groupid'] = $sourceunpacked['groupid'];
                    $reference['url'] = $sourceclient->get_group_file_sharing_link($sourceunpacked['groupid'], $fileid);
                } else if ($filesource === 'trendingaround') {
                    if ($this->unifiedconfigured !== true) {
                        utils::debug('Tried to access a trending around me file while the graph api is disabled.', __METHOD__);
                        throw new moodle_exception('errorwhiledownload', 'repository_office365');
                    }
                    $sourceclient = $this->get_unified_apiclient();
                    $filedata = $sourceclient->get_file_data($fileid);
                    if (isset($filedata['@microsoft.graph.downloadUrl'])) {
                        $reference['url'] = $filedata['@microsoft.graph.downloadUrl'];
                    }
                }

            } catch (\Exception $e) {
                $errmsg = 'There was a problem making the API call.';
                $debugdata = [
                    'source' => $filesource,
                    'id' => $fileid,
                    'message' => $e->getMessage(),
                    'e' => $e,
                ];
                utils::debug($errmsg, __METHOD__, $debugdata);
            }

            return $this->pack_reference($reference);
        } else {
            $errmsg = '';
            if (!isset($sourceunpacked['source'])) {
                $errmsg = 'Source is not set.';
            }
            if (isset($sourceunpacked['id'])) {
                $errmsg .= ' id is not set.';
            }
            $debugdata = ['sourceunpacked' => $sourceunpacked];
            utils::debug($errmsg, __METHOD__, $debugdata);
        }
        return $source;
    }

    /**
     * Return file URL, for most plugins, the parameter is the original
     * url, but some plugins use a file id, so we need this function to
     * convert file id to original url.
     *
     * @param string $url the url of file
     * @return string
     */
    public function get_link($url) {
        $reference = $this->unpack_reference($url);
        return $reference['url'];
    }

    /**
     * Determine whether a "send_file" request should be a redirect to the embed URL for a file.
     *
     * @param array $reference The file reference array.
     * @param bool $forcedownload The send_file "forcedownload" param.
     * @return bool True if we should do embedding, false otherwise.
     */
    public function do_embedding($reference, $forcedownload) {
        global $PAGE, $DB;

        if (empty($reference['source']) || !in_array($reference['source'], ['onedrive'])) {
            return false;
        }

        if (!empty($forcedownload)) {
            return false;
        }

        $cm = $PAGE->cm;
        if (!empty($cm)) {
            $sql = 'SELECT cm.instance
                     FROM {course_modules} cm
                     JOIN {modules} m ON m.id = cm.module
                    WHERE cm.id = ? AND m.name = ?';
            $rec = $DB->get_record_sql($sql, [$cm->id, 'resource']);
            if (!empty($rec)) {
                $resourcerec = $DB->get_record('resource', ['id' => $rec->instance]);
                if (!empty($resourcerec)) {
                    if (defined('RESOURCELIB_DISPLAY_EMBED') && $resourcerec->display == RESOURCELIB_DISPLAY_EMBED) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Repository method to serve the referenced file
     *
     * @param stored_file $storedfile the file that contains the reference
     * @param null $lifetime Number of seconds before the file should expire from caches (null means $CFG->filelifetime)
     * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
     * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
     * @param array|null $options additional options affecting the file serving
     */
    public function send_file($storedfile, $lifetime = null , $filter = 0, $forcedownload = false, array $options = null) {
        global $USER;

        $reference = $this->unpack_reference($storedfile->get_reference());

        $fileuserid = $storedfile->get_userid();

        if (!isset($reference['source'])) {
            utils::debug('File reference is broken - no source parameter.', __METHOD__, $reference);
            send_file_not_found();
            die();
        }

        $doembed = $this->do_embedding($reference, $forcedownload);

        switch ($reference['source']) {
            case 'onedrive':
                $sourceclient = $this->get_unified_apiclient(false, $fileuserid);
                if (empty($sourceclient)) {
                    utils::debug('Could not construct api client for user', __METHOD__, $fileuserid);
                    send_file_not_found();
                    die();
                }
                if ($doembed === true) {
                    $o365userupn = utils::get_o365_upn($fileuserid);
                    $fileinfo = $sourceclient->get_file_metadata($reference['id'], $o365userupn);
                    if (isset($fileinfo['webUrl'])) {
                        $fileurl = $fileinfo['webUrl'];
                    } else {
                        $fileurl = (isset($reference['url'])) ? $reference['url'] : '';
                    }
                } else {
                    $fileurl = (isset($reference['url'])) ? $reference['url'] : '';
                }
                break;

            case 'onedrivegroup':
                $sourceclient = $this->get_unified_apiclient();
                $fileurl = (isset($reference['url'])) ? $reference['url'] : '';
                break;

            default:
                utils::debug('File reference is broken - invalid source parameter.', __METHOD__, $reference);
                send_file_not_found();
                die();
        }

        // Do embedding if relevant.
        if ($doembed === true) {
            if (utils::is_o365_connected($USER->id) !== true) {
                // Embedding currently only supported for logged-in Microsoft 365 users.
                echo get_string('erroro365required', 'repository_office365');
                die();
            }
            if (!empty($sourceclient)) {
                if (empty($fileurl)) {
                    $errstr = 'Embed was requested, but could not get file info to complete request.';
                    utils::debug($errstr, __METHOD__, ['reference' => $reference, 'fileinfo' => $fileinfo]);
                } else {
                    try {
                        $embedurl = $sourceclient->get_embed_url($reference['id'], $fileurl);
                        $embedurl = (isset($embedurl['value'])) ? $embedurl['value'] : '';
                    } catch (\Exception $e) {
                        // Note: exceptions will already be logged in get_embed_url.
                        $embedurl = '';
                    }
                    if (!empty($embedurl)) {
                        redirect($embedurl);
                    } else if (!empty($fileurl)) {
                        redirect($fileurl);
                    } else {
                        $errstr = 'Embed was requested, but could not complete.';
                        utils::debug($errstr, __METHOD__, $reference);
                    }
                }
            } else {
                utils::debug('Could not construct OneDrive client for system api user.', __METHOD__);
            }
        }

        redirect($fileurl);
    }

    /**
     * Validate Admin Settings Moodle form
     *
     * @param moodleform $mform Moodle form (passed by reference)
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $errors array of ("fieldname"=>errormessage) of errors
     * @return array array of errors
     */
    public static function type_form_validation($mform, $data, $errors) {
        global $CFG;
        if (utils::is_connected() !== true) {
            array_push($errors, get_string('notconfigured', 'repository_office365', $CFG->wwwroot));
        }
        return $errors;
    }

    /**
     * Setup repository form.
     *
     * @param moodleform $mform Moodle form (passed by reference)
     * @param string $classname repository class name
     */
    public static function type_config_form($mform, $classname = 'repository') {
        global $CFG;

        if (utils::is_connected() !== true) {
            $mform->addElement('static', null, '', get_string('notconfigured', 'repository_office365', $CFG->wwwroot));
        }
        parent::type_config_form($mform);
        $mform->addElement('checkbox', 'coursegroup', get_string('coursegroup', 'repository_office365'));
        $mform->setType('coursegroup', PARAM_INT);
        $mform->addElement('checkbox', 'onedrivegroup', get_string('onedrivegroup', 'repository_office365'));
        $mform->setType('onedrivegroup', PARAM_INT);
        $mform->addElement('checkbox', 'trendinggroup', get_string('trendinggroup', 'repository_office365'));
        $mform->setType('trendinggroup', PARAM_INT);
    }

     /**
      * Option names of dropbox office365
      *
      * @return array
      */
    public static function get_type_option_names() {
        return ['coursegroup', 'onedrivegroup', 'trendinggroup', 'pluginname'];
    }
}
