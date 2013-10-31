<?php
/**
 * phpFlickr Class 2.2.0
 * Written by Dan Coulter (dan@dancoulter.com)
 * Sourceforge Project Page: {@link http://www.sourceforge.net/projects/phpflickr/}
 * Released under GNU Lesser General Public License ({@link http://www.gnu.org/copyleft/lgpl.html})
 * For more information about the class and upcoming tools and toys using it,
 * visit {@link http://www.phpflickr.com/} or {@link http://phpflickr.sourceforge.net}
 *
 *   For installation instructions, open the README.txt file packaged with this
 *   class. If you don't have a copy, you can see it at:
 *    {@link http://www.phpflickr.com/README.txt}
 *
 *   Please submit all problems or questions to the Help Forum on my project page:
 *     {@link http://sourceforge.net/forum/forum.php?forum_id=469652}
 *
 * Modified by Dongsheng Cai <dongsheng@moodle.com>
 * ChangeLog:
 *   1. Remove PEAR HTTP LIB, use curl.class.php (created by myself)
 *   2. Remove PEAR DB LIB
 *   3. Remove all cache code, it will implement in curl class.
 *   4. Clean up session code
 *
 * Modified by David Mudrak <david@moodle.com>
 * ChangeLog:
 *   1. upload() method uses Moodle stored_file
 *   2. upload() method supports all params provided by http://www.flickr.com/services/api/upload.api.html
 *
 * @package moodlecore
 * @subpackage 3rd-party
 */

/**
 * Flickr Class
 * @package moodlecore
 * @subpackage 3rd-party
 */
class phpFlickr {
    var $api_key;
    var $secret;
    var $REST = 'http://api.flickr.com/services/rest/';
    var $Upload = 'http://api.flickr.com/services/upload/';
    var $Replace = 'http://api.flickr.com/services/replace/';
    var $req;
    var $response;
    var $parsed_response;
    var $die_on_error;
    var $error_code;
    var $error_msg;
    var $token;
    var $php_version;

    /**
     * When your database cache table hits this many rows, a cleanup
     * will occur to get rid of all of the old rows and cleanup the
     * garbage in the table.  For most personal apps, 1000 rows should
     * be more than enough.  If your site gets hit by a lot of traffic
     * or you have a lot of disk space to spare, bump this number up.
     * You should try to set it high enough that the cleanup only
     * happens every once in a while, so this will depend on the growth
     * of your table.
     *
     * @global object
     */

    function __construct ($api_key, $secret = NULL, $token = '')
    {
        global $CFG;
        //The API Key must be set before any calls can be made.  You can
        //get your own at http://www.flickr.com/services/api/misc.api_keys.html
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->die_on_error = false;
        $this->service = "flickr";
        $this->token = $token;
        //Find the PHP version and store it for future reference
        $this->php_version = explode("-", phpversion());
        $this->php_version = explode(".", $this->php_version[0]);
        $this->curl = new curl(array('cache'=>true, 'module_cache'=>'repository'));
    }

    function request ($command, $args = array())
    {
        //Sends a request to Flickr's REST endpoint via POST.
        if (substr($command,0,7) != "flickr.") {
            $command = "flickr." . $command;
        }

        //Process arguments, including method and login data.
        if ($command == 'flickr.upload') {
            $photo = $args['photo'];
            if (empty($args['is_public'])) {
                $args['is_public'] = 0;
            }
            if (empty($args['is_friend'])) {
                $args['is_friend'] = 0;
            }
            if (empty($args['is_family'])) {
                $args['is_family'] = 0;
            }
            if (empty($args['hidden'])) {
                $args['hidden'] = 1;
            }
            $args = array("api_key" => $this->api_key,
                          "title" => $args['title'],
                          "description" => $args['description'],
                          "tags" => $args['tags'],
                          "is_public" => $args['is_public'],
                          "is_friend" => $args['is_friend'],
                          "is_family" => $args['is_family'],
                          "safety_level" => $args['safety_level'],
                          "content_type" => $args['content_type'],
                          "hidden" => $args['hidden']);
        } else {
            $args = array_merge(array("method" => $command, "format" => "php_serial", "api_key" => $this->api_key), $args);
        }

        if (!empty($this->token)) {
            $args = array_merge($args, array("auth_token" => $this->token));
        }

        ksort($args);
        $auth_sig = "";
        foreach ($args as $key => $data) {
            $auth_sig .= $key . $data;
        }

        if (!empty($this->secret)) {
            $api_sig = md5($this->secret . $auth_sig);
            $args['api_sig'] = $api_sig;
        }

        //$this->req->addHeader("Connection", "Keep-Alive");
        if ($command != 'flickr.upload') {
            $ret = $this->curl->post($this->REST, $args);
            $this->parsed_response = $this->clean_text_nodes(unserialize($ret));
        } else {
            $args['photo'] = $photo;
            $xml = simplexml_load_string($this->curl->post($this->Upload, $args));

            if ($xml['stat'] == 'fail') {
                $this->parsed_response = array('stat' => (string) $xml['stat'], 'code' => (int) $xml->err['code'], 'message' => (string) $xml->err['msg']);
            } elseif ($xml['stat'] == 'ok') {
                $this->parsed_response = array('stat' => (string) $xml['stat'], 'photoid' => (int) $xml->photoid);
                $this->response = true;
            }
        }

        if ($this->parsed_response['stat'] == 'fail') {
            if ($this->die_on_error) die("The Flickr API returned the following error: #{$this->parsed_response['code']} - {$this->parsed_response['message']}");
            else {
                $this->error_code = $this->parsed_response['code'];
                $this->error_msg = $this->parsed_response['message'];
                $this->parsed_response = false;
            }
        } else {
            $this->error_code = false;
            $this->error_msg = false;
        }
        return $this->response;
    }

    function clean_text_nodes($arr) {
        if (!is_array($arr)) {
            return $arr;
        } elseif (count($arr) == 0) {
            return $arr;
        } elseif (count($arr) == 1 && array_key_exists('_content', $arr)) {
            return $arr['_content'];
        } else {
            foreach ($arr as $key => $element) {
                $arr[$key] = $this->clean_text_nodes($element);
            }
            return($arr);
        }
    }

    function setToken($token)
    {
        // Sets an authentication token to use instead of the session variable
        $this->token = $token;
    }

    function setProxy($server, $port)
    {
        // Sets the proxy for all phpFlickr calls.
        //$this->req->setProxy($server, $port);
    }

    function getErrorCode()
    {
        // Returns the error code of the last call.  If the last call did not
        // return an error. This will return a false boolean.
        return $this->error_code;
    }

    function getErrorMsg()
    {
        // Returns the error message of the last call.  If the last call did not
        // return an error. This will return a false boolean.
        return $this->error_msg;
    }

    /** These functions are front ends for the flickr calls */

    function buildPhotoURL ($photo, $size = "Medium")
    {
        //receives an array (can use the individual photo data returned
        //from an API call) and returns a URL (doesn't mean that the
        //file size exists)
        $sizes = array(
                "square" => "_s",
                "thumbnail" => "_t",
                "small" => "_m",
                "medium" => "",
                "large" => "_b",
                "original" => "_o"
                );

        $size = strtolower($size);
        if (!array_key_exists($size, $sizes)) {
            $size = "medium";
        }

        if ($size == "original") {
            $url = "http://farm" . $photo['farm'] . ".static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['originalsecret'] . "_o" . "." . $photo['originalformat'];
        } else {
            $url = "http://farm" . $photo['farm'] . ".static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'] . $sizes[$size] . ".jpg";
        }
        return $url;
    }

    function getFriendlyGeodata($lat, $lon) {
        /** I've added this method to get the friendly geodata (i.e. 'in New York, NY') that the
         * website provides, but isn't available in the API. I'm providing this service as long
         * as it doesn't flood my server with requests and crash it all the time.
         */
        return unserialize(file_get_contents('http://phpflickr.com/geodata/?format=php&lat=' . $lat . '&lon=' . $lon));
    }

    function auth ($perms = "write", $remember_uri = true)
    {
        // Redirects to Flickr's authentication piece if there is no valid token.
        // If remember_uri is set to false, the callback script (included) will
        // redirect to its default page.
        if ($remember_uri) {
            $redirect = qualified_me(); // TODO: this is not used, why?
        }
        $api_sig = md5($this->secret . "api_key" . $this->api_key . "perms" . $perms);
        $url = 'http://www.flickr.com/services/auth/?api_key=' . $this->api_key . "&perms=" .  $perms . '&api_sig='. $api_sig;
        return $url;
    }

    /**
     * To use the phpFlickr::call method, pass a string containing the API method you want
     * to use and an associative array of arguments.  For example:
     * $result = $f->call("flickr.photos.comments.getList", array("photo_id"=>'34952612'));
     * This method will allow you to make calls to arbitrary methods that haven't been
     * implemented in phpFlickr yet.
     */

    function call($method, $arguments)
    {
        $this->request($method, $arguments);
        return $this->parsed_response ? $this->parsed_response : false;
    }

    /**
     * These functions are the direct implementations of flickr calls.
     * For method documentation, including arguments, visit the address
     * included in a comment in the function.
     */

    /** Activity methods */
    function activity_userComments ($per_page = NULL, $page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.activity.userComments.html */
        $this->request('flickr.activity.userComments', array("per_page" => $per_page, "page" => $page));
        return $this->parsed_response ? $this->parsed_response['items']['item'] : false;
    }

    function activity_userPhotos ($timeframe = NULL, $per_page = NULL, $page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.activity.userPhotos.html */
        $this->request('flickr.activity.userPhotos', array("timeframe" => $timeframe, "per_page" => $per_page, "page" => $page));
        return $this->parsed_response ? $this->parsed_response['items']['item'] : false;
    }

    /** Authentication methods */
    function auth_checkToken ()
    {
        /** http://www.flickr.com/services/api/flickr.auth.checkToken.html */
        $this->request('flickr.auth.checkToken');
        return $this->parsed_response ? $this->parsed_response['auth'] : false;
    }

    function auth_getFrob ()
    {
        /** http://www.flickr.com/services/api/flickr.auth.getFrob.html */
        $this->request('flickr.auth.getFrob');
        return $this->parsed_response ? $this->parsed_response['frob'] : false;
    }

    function auth_getFullToken ($mini_token)
    {
        /** http://www.flickr.com/services/api/flickr.auth.getFullToken.html */
        $this->request('flickr.auth.getFullToken', array('mini_token'=>$mini_token));
        return $this->parsed_response ? $this->parsed_response['auth'] : false;
    }

    function auth_getToken ($frob)
    {
        /** http://www.flickr.com/services/api/flickr.auth.getToken.html */
        $this->request('flickr.auth.getToken', array('frob'=>$frob));
        $this->token = $this->parsed_response['auth']['token'];
        return $this->parsed_response ? $this->parsed_response['auth'] : false;
    }

    /** Blogs methods */
    function blogs_getList ()
    {
        /** http://www.flickr.com/services/api/flickr.blogs.getList.html */
        $this->request('flickr.blogs.getList');
        return $this->parsed_response ? $this->parsed_response['blogs']['blog'] : false;
    }

    function blogs_postPhoto($blog_id, $photo_id, $title, $description, $blog_password = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.blogs.postPhoto.html */
        $this->request('flickr.blogs.postPhoto', array('blog_id'=>$blog_id, 'photo_id'=>$photo_id, 'title'=>$title, 'description'=>$description, 'blog_password'=>$blog_password), TRUE);
        return $this->parsed_response ? true : false;
    }

    /** Contacts Methods */
    function contacts_getList ($filter = NULL, $page = NULL, $per_page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.contacts.getList.html */
        $this->request('flickr.contacts.getList', array('filter'=>$filter, 'page'=>$page, 'per_page'=>$per_page));
        return $this->parsed_response ? $this->parsed_response['contacts'] : false;
    }

    function contacts_getPublicList($user_id, $page = NULL, $per_page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.contacts.getPublicList.html */
        $this->request('flickr.contacts.getPublicList', array('user_id'=>$user_id, 'page'=>$page, 'per_page'=>$per_page));
        return $this->parsed_response ? $this->parsed_response['contacts'] : false;
    }

    /** Favorites Methods */
    function favorites_add ($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.favorites.add.html */
        $this->request('flickr.favorites.add', array('photo_id'=>$photo_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    function favorites_getList($user_id = NULL, $extras = NULL, $per_page = NULL, $page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.favorites.getList.html */
        if (is_array($extras)) { $extras = implode(",", $extras); }
        $this->request("flickr.favorites.getList", array("user_id"=>$user_id, "extras"=>$extras, "per_page"=>$per_page, "page"=>$page));
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    function favorites_getPublicList($user_id = NULL, $extras = NULL, $per_page = NULL, $page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.favorites.getPublicList.html */
        if (is_array($extras)) {
            $extras = implode(",", $extras);
        }
        $this->request("flickr.favorites.getPublicList", array("user_id"=>$user_id, "extras"=>$extras, "per_page"=>$per_page, "page"=>$page));
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    function favorites_remove($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.favorites.remove.html */
        $this->request("flickr.favorites.remove", array("photo_id"=>$photo_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    /** Groups Methods */
    function groups_browse ($cat_id = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.groups.browse.html */
        $this->request("flickr.groups.browse", array("cat_id"=>$cat_id));
        return $this->parsed_response ? $this->parsed_response['category'] : false;
    }

    function groups_getInfo ($group_id)
    {
        /** http://www.flickr.com/services/api/flickr.groups.getInfo.html */
        $this->request("flickr.groups.getInfo", array("group_id"=>$group_id));
        return $this->parsed_response ? $this->parsed_response['group'] : false;
    }

    function groups_search ($text, $per_page=NULL, $page=NULL)
    {
        /** http://www.flickr.com/services/api/flickr.groups.search.html */
        $this->request("flickr.groups.search", array("text"=>$text,"per_page"=>$per_page,"page"=>$page));
        return $this->parsed_response ? $this->parsed_response['groups'] : false;
    }

    /** Groups Pools Methods */
    function groups_pools_add ($photo_id, $group_id)
    {
        /** http://www.flickr.com/services/api/flickr.groups.pools.add.html */
        $this->request("flickr.groups.pools.add", array("photo_id"=>$photo_id, "group_id"=>$group_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    function groups_pools_getContext ($photo_id, $group_id)
    {
        /** http://www.flickr.com/services/api/flickr.groups.pools.getContext.html */
        $this->request("flickr.groups.pools.getContext", array("photo_id"=>$photo_id, "group_id"=>$group_id));
        return $this->parsed_response ? $this->parsed_response : false;
    }

    function groups_pools_getGroups ($page = NULL, $per_page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.groups.pools.getGroups.html */
        $this->request("flickr.groups.pools.getGroups", array('page'=>$page, 'per_page'=>$per_page));
        return $this->parsed_response ? $this->parsed_response['groups'] : false;
    }

    function groups_pools_getPhotos ($group_id, $tags = NULL, $user_id = NULL, $extras = NULL, $per_page = NULL, $page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.groups.pools.getPhotos.html */
        if (is_array($extras)) {
            $extras = implode(",", $extras);
        }
        $this->request("flickr.groups.pools.getPhotos", array("group_id"=>$group_id, "tags"=>$tags, "user_id"=>$user_id, "extras"=>$extras, "per_page"=>$per_page, "page"=>$page));
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    function groups_pools_remove ($photo_id, $group_id)
    {
        /** http://www.flickr.com/services/api/flickr.groups.pools.remove.html */
        $this->request("flickr.groups.pools.remove", array("photo_id"=>$photo_id, "group_id"=>$group_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    /** Interestingness methods */
    function interestingness_getList($date = NULL, $extras = NULL, $per_page = NULL, $page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.interestingness.getList.html */
        if (is_array($extras)) {
            $extras = implode(",", $extras);
        }

        $this->request("flickr.interestingness.getList", array("date"=>$date, "extras"=>$extras, "per_page"=>$per_page, "page"=>$page));
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    /** People methods */
    function people_findByEmail ($find_email)
    {
        /** http://www.flickr.com/services/api/flickr.people.findByEmail.html */
        $this->request("flickr.people.findByEmail", array("find_email"=>$find_email));
        return $this->parsed_response ? $this->parsed_response['user'] : false;
    }

    function people_findByUsername ($username)
    {
        /** http://www.flickr.com/services/api/flickr.people.findByUsername.html */
        $this->request("flickr.people.findByUsername", array("username"=>$username));
        return $this->parsed_response ? $this->parsed_response['user'] : false;
    }

    function people_getInfo($user_id)
    {
        /** http://www.flickr.com/services/api/flickr.people.getInfo.html */
        $this->request("flickr.people.getInfo", array("user_id"=>$user_id));
        return $this->parsed_response ? $this->parsed_response['person'] : false;
    }

    function people_getPublicGroups($user_id)
    {
        /** http://www.flickr.com/services/api/flickr.people.getPublicGroups.html */
        $this->request("flickr.people.getPublicGroups", array("user_id"=>$user_id));
        return $this->parsed_response ? $this->parsed_response['groups']['group'] : false;
    }

    function people_getPublicPhotos($user_id, $extras = NULL, $per_page = NULL, $page = NULL) {
        /** http://www.flickr.com/services/api/flickr.people.getPublicPhotos.html */
        if (is_array($extras)) {
            $extras = implode(",", $extras);
        }

        $this->request("flickr.people.getPublicPhotos", array("user_id"=>$user_id, "extras"=>$extras, "per_page"=>$per_page, "page"=>$page));
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    function people_getUploadStatus()
    {
        /** http://www.flickr.com/services/api/flickr.people.getUploadStatus.html */
        /** Requires Authentication */
        $this->request("flickr.people.getUploadStatus");
        return $this->parsed_response ? $this->parsed_response['user'] : false;
    }


    /** Photos Methods */
    function photos_addTags ($photo_id, $tags)
    {
        /** http://www.flickr.com/services/api/flickr.photos.addTags.html */
        $this->request("flickr.photos.addTags", array("photo_id"=>$photo_id, "tags"=>$tags), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photos_delete($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.delete.html */
        $this->request("flickr.photos.delete", array("photo_id"=>$photo_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photos_getAllContexts ($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getAllContexts.html */
        $this->request("flickr.photos.getAllContexts", array("photo_id"=>$photo_id));
        return $this->parsed_response ? $this->parsed_response : false;
    }

    function photos_getContactsPhotos ($count = NULL, $just_friends = NULL, $single_photo = NULL, $include_self = NULL, $extras = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getContactsPhotos.html */
        $this->request("flickr.photos.getContactsPhotos", array("count"=>$count, "just_friends"=>$just_friends, "single_photo"=>$single_photo, "include_self"=>$include_self, "extras"=>$extras));
        return $this->parsed_response ? $this->parsed_response['photos']['photo'] : false;
    }

    function photos_getContactsPublicPhotos ($user_id, $count = NULL, $just_friends = NULL, $single_photo = NULL, $include_self = NULL, $extras = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getContactsPublicPhotos.html */
        $this->request("flickr.photos.getContactsPublicPhotos", array("user_id"=>$user_id, "count"=>$count, "just_friends"=>$just_friends, "single_photo"=>$single_photo, "include_self"=>$include_self, "extras"=>$extras));
        return $this->parsed_response ? $this->parsed_response['photos']['photo'] : false;
    }

    function photos_getContext ($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getContext.html */
        $this->request("flickr.photos.getContext", array("photo_id"=>$photo_id));
        return $this->parsed_response ? $this->parsed_response : false;
    }

    function photos_getCounts ($dates = NULL, $taken_dates = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getCounts.html */
        $this->request("flickr.photos.getCounts", array("dates"=>$dates, "taken_dates"=>$taken_dates));
        return $this->parsed_response ? $this->parsed_response['photocounts']['photocount'] : false;
    }

    function photos_getExif ($photo_id, $secret = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getExif.html */
        $this->request("flickr.photos.getExif", array("photo_id"=>$photo_id, "secret"=>$secret));
        return $this->parsed_response ? $this->parsed_response['photo'] : false;
    }

    function photos_getFavorites($photo_id, $page = NULL, $per_page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getFavorites.html */
        $this->request("flickr.photos.getFavorites", array("photo_id"=>$photo_id, "page"=>$page, "per_page"=>$per_page));
        return $this->parsed_response ? $this->parsed_response['photo'] : false;
    }

    function photos_getInfo($photo_id, $secret = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getInfo.html */
        $this->request("flickr.photos.getInfo", array("photo_id"=>$photo_id, "secret"=>$secret));
        return $this->parsed_response ? $this->parsed_response['photo'] : false;
    }

    function photos_getNotInSet($min_upload_date = NULL, $max_upload_date = NULL, $min_taken_date = NULL, $max_taken_date = NULL, $privacy_filter = NULL, $extras = NULL, $per_page = NULL, $page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getNotInSet.html */
        if (is_array($extras)) {
            $extras = implode(",", $extras);
        }
        $this->request("flickr.photos.getNotInSet", array("min_upload_date"=>$min_upload_date, "max_upload_date"=>$max_upload_date, "min_taken_date"=>$min_taken_date, "max_taken_date"=>$max_taken_date, "privacy_filter"=>$privacy_filter, "extras"=>$extras, "per_page"=>$per_page, "page"=>$page));
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    function photos_getPerms($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getPerms.html */
        $this->request("flickr.photos.getPerms", array("photo_id"=>$photo_id));
        return $this->parsed_response ? $this->parsed_response['perms'] : false;
    }

    function photos_getRecent($extras = NULL, $per_page = NULL, $page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getRecent.html */

        if (is_array($extras)) {
            $extras = implode(",", $extras);
        }
        $this->request("flickr.photos.getRecent", array("extras"=>$extras, "per_page"=>$per_page, "page"=>$page));
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    function photos_getSizes($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getSizes.html */
        $this->request("flickr.photos.getSizes", array("photo_id"=>$photo_id));
        return $this->parsed_response ? $this->parsed_response['sizes']['size'] : false;
    }

    function photos_getUntagged($min_upload_date = NULL, $max_upload_date = NULL, $min_taken_date = NULL, $max_taken_date = NULL, $privacy_filter = NULL, $extras = NULL, $per_page = NULL, $page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getUntagged.html */
        if (is_array($extras)) {
            $extras = implode(",", $extras);
        }
        $this->request("flickr.photos.getUntagged", array("min_upload_date"=>$min_upload_date, "max_upload_date"=>$max_upload_date, "min_taken_date"=>$min_taken_date, "max_taken_date"=>$max_taken_date, "privacy_filter"=>$privacy_filter, "extras"=>$extras, "per_page"=>$per_page, "page"=>$page));
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    function photos_getWithGeoData($args = NULL) {
        /** See the documentation included with the photos_search() function.
         * I'm using the same style of arguments for this function. The only
         * difference here is that this doesn't require any arguments. The
         * flickr.photos.search method requires at least one search parameter.
         */
        /** http://www.flickr.com/services/api/flickr.photos.getWithGeoData.html */
        if (is_null($args)) {
            $args = array();
        }
        $this->request("flickr.photos.getWithGeoData", $args);
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    function photos_getWithoutGeoData($args = NULL) {
        /** See the documentation included with the photos_search() function.
         * I'm using the same style of arguments for this function. The only
         * difference here is that this doesn't require any arguments. The
         * flickr.photos.search method requires at least one search parameter.
         */
        /** http://www.flickr.com/services/api/flickr.photos.getWithoutGeoData.html */
        if (is_null($args)) {
            $args = array();
        }
        $this->request("flickr.photos.getWithoutGeoData", $args);
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    function photos_recentlyUpdated($min_date = NULL, $extras = NULL, $per_page = NULL, $page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.getUntagged.html */
        if (is_array($extras)) {
            $extras = implode(",", $extras);
        }
        $this->request("flickr.photos.recentlyUpdated", array("min_date"=>$min_date, "extras"=>$extras, "per_page"=>$per_page, "page"=>$page));
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    function photos_removeTag($tag_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.removeTag.html */
        $this->request("flickr.photos.removeTag", array("tag_id"=>$tag_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photos_search($args)
    {
        /** This function strays from the method of arguments that I've
         * used in the other functions for the fact that there are just
         * so many arguments to this API method. What you'll need to do
         * is pass an associative array to the function containing the
         * arguments you want to pass to the API.  For example:
         *   $photos = $f->photos_search(array("tags"=>"brown,cow", "tag_mode"=>"any"));
         * This will return photos tagged with either "brown" or "cow"
         * or both. See the API documentation (link below) for a full
         * list of arguments.
         */

        /** http://www.flickr.com/services/api/flickr.photos.search.html */
        $this->request("flickr.photos.search", $args);
        return $this->parsed_response ? $this->parsed_response['photos'] : false;
    }

    function photos_setContentType ($photo_id, $content_type) {
        /** http://www.flickr.com/services/api/flickr.photos.setContentType.html */
        return $this->call('flickr.photos.setContentType', array('photo_id' => $photo_id, 'content_type' => $content_type));
    }

    function photos_setDates($photo_id, $date_posted = NULL, $date_taken = NULL, $date_taken_granularity = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.setDates.html */
        $this->request("flickr.photos.setDates", array("photo_id"=>$photo_id, "date_posted"=>$date_posted, "date_taken"=>$date_taken, "date_taken_granularity"=>$date_taken_granularity), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photos_setMeta($photo_id, $title, $description)
    {
        /** http://www.flickr.com/services/api/flickr.photos.setMeta.html */
        $this->request("flickr.photos.setMeta", array("photo_id"=>$photo_id, "title"=>$title, "description"=>$description), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photos_setPerms($photo_id, $is_public, $is_friend, $is_family, $perm_comment, $perm_addmeta)
    {
        /** http://www.flickr.com/services/api/flickr.photos.setPerms.html */
        $this->request("flickr.photos.setPerms", array("photo_id"=>$photo_id, "is_public"=>$is_public, "is_friend"=>$is_friend, "is_family"=>$is_family, "perm_comment"=>$perm_comment, "perm_addmeta"=>$perm_addmeta), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photos_setSafetyLevel ($photo_id, $safety_level, $hidden = null) {
        /** http://www.flickr.com/services/api/flickr.photos.setSafetyLevel.html */
        return $this->call('flickr.photos.setSafetyLevel', array('photo_id' => $photo_id, 'safety_level' => $safety_level, 'hidden' => $hidden));
    }


    function photos_setTags($photo_id, $tags)
    {
        /** http://www.flickr.com/services/api/flickr.photos.setTags.html */
        $this->request("flickr.photos.setTags", array("photo_id"=>$photo_id, "tags"=>$tags), TRUE);
        return $this->parsed_response ? true : false;
    }

    /** Photos - Comments Methods */
    function photos_comments_addComment($photo_id, $comment_text) {
        /** http://www.flickr.com/services/api/flickr.photos.comments.addComment.html */
        $this->request("flickr.photos.comments.addComment", array("photo_id" => $photo_id, "comment_text"=>$comment_text), TRUE);
        return $this->parsed_response ? $this->parsed_response['comment'] : false;
    }

    function photos_comments_deleteComment($comment_id) {
        /** http://www.flickr.com/services/api/flickr.photos.comments.deleteComment.html */
        $this->request("flickr.photos.comments.deleteComment", array("comment_id" => $comment_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photos_comments_editComment($comment_id, $comment_text) {
        /** http://www.flickr.com/services/api/flickr.photos.comments.editComment.html */
        $this->request("flickr.photos.comments.editComment", array("comment_id" => $comment_id, "comment_text"=>$comment_text), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photos_comments_getList($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.comments.getList.html */
        $this->request("flickr.photos.comments.getList", array("photo_id"=>$photo_id));
        return $this->parsed_response ? $this->parsed_response['comments'] : false;
    }

    /** Photos - Geo Methods */
    function photos_geo_getLocation($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.geo.getLocation.html */
        $this->request("flickr.photos.geo.getLocation", array("photo_id"=>$photo_id));
        return $this->parsed_response ? $this->parsed_response['photo'] : false;
    }

    function photos_geo_getPerms($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.geo.getPerms.html */
        $this->request("flickr.photos.geo.getPerms", array("photo_id"=>$photo_id));
        return $this->parsed_response ? $this->parsed_response['perms'] : false;
    }

    function photos_geo_removeLocation($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.geo.removeLocation.html */
        $this->request("flickr.photos.geo.removeLocation", array("photo_id"=>$photo_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photos_geo_setLocation($photo_id, $lat, $lon, $accuracy = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photos.geo.setLocation.html */
        $this->request("flickr.photos.geo.setLocation", array("photo_id"=>$photo_id, "lat"=>$lat, "lon"=>$lon, "accuracy"=>$accuracy), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photos_geo_setPerms($photo_id, $is_public, $is_contact, $is_friend, $is_family)
    {
        /** http://www.flickr.com/services/api/flickr.photos.geo.setPerms.html */
        $this->request("flickr.photos.geo.setPerms", array("photo_id"=>$photo_id, "is_public"=>$is_public, "is_contact"=>$is_contact, "is_friend"=>$is_friend, "is_family"=>$is_family), TRUE);
        return $this->parsed_response ? true : false;
    }

    /** Photos - Licenses Methods */
    function photos_licenses_getInfo()
    {
        /** http://www.flickr.com/services/api/flickr.photos.licenses.getInfo.html */
        $this->request("flickr.photos.licenses.getInfo");
        return $this->parsed_response ? $this->parsed_response['licenses']['license'] : false;
    }

    function photos_licenses_setLicense($photo_id, $license_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.licenses.setLicense.html */
        /** Requires Authentication */
        $this->request("flickr.photos.licenses.setLicense", array("photo_id"=>$photo_id, "license_id"=>$license_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    /** Photos - Notes Methods */
    function photos_notes_add($photo_id, $note_x, $note_y, $note_w, $note_h, $note_text)
    {
        /** http://www.flickr.com/services/api/flickr.photos.notes.add.html */
        $this->request("flickr.photos.notes.add", array("photo_id" => $photo_id, "note_x" => $note_x, "note_y" => $note_y, "note_w" => $note_w, "note_h" => $note_h, "note_text" => $note_text), TRUE);
        return $this->parsed_response ? $this->parsed_response['note'] : false;
    }

    function photos_notes_delete($note_id)
    {
        /** http://www.flickr.com/services/api/flickr.photos.notes.delete.html */
        $this->request("flickr.photos.notes.delete", array("note_id" => $note_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photos_notes_edit($note_id, $note_x, $note_y, $note_w, $note_h, $note_text)
    {
        /** http://www.flickr.com/services/api/flickr.photos.notes.edit.html */
        $this->request("flickr.photos.notes.edit", array("note_id" => $note_id, "note_x" => $note_x, "note_y" => $note_y, "note_w" => $note_w, "note_h" => $note_h, "note_text" => $note_text), TRUE);
        return $this->parsed_response ? true : false;
    }

    /** Photos - Transform Methods */
    function photos_transform_rotate($photo_id, $degrees)
    {
        /** http://www.flickr.com/services/api/flickr.photos.transform.rotate.html */
        $this->request("flickr.photos.transform.rotate", array("photo_id" => $photo_id, "degrees" => $degrees), TRUE);
        return $this->parsed_response ? true : false;
    }

    /** Photos - Upload Methods */
    function photos_upload_checkTickets($tickets)
    {
        /** http://www.flickr.com/services/api/flickr.photos.upload.checkTickets.html */
        if (is_array($tickets)) {
            $tickets = implode(",", $tickets);
        }
        $this->request("flickr.photos.upload.checkTickets", array("tickets" => $tickets), TRUE);
        return $this->parsed_response ? $this->parsed_response['uploader']['ticket'] : false;
    }

    /** Photosets Methods */
    function photosets_addPhoto($photoset_id, $photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.addPhoto.html */
        $this->request("flickr.photosets.addPhoto", array("photoset_id" => $photoset_id, "photo_id" => $photo_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photosets_create($title, $description, $primary_photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.create.html */
        $this->request("flickr.photosets.create", array("title" => $title, "primary_photo_id" => $primary_photo_id, "description" => $description), TRUE);
        return $this->parsed_response ? $this->parsed_response['photoset'] : false;
    }

    function photosets_delete($photoset_id)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.delete.html */
        $this->request("flickr.photosets.delete", array("photoset_id" => $photoset_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photosets_editMeta($photoset_id, $title, $description = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.editMeta.html */
        $this->request("flickr.photosets.editMeta", array("photoset_id" => $photoset_id, "title" => $title, "description" => $description), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photosets_editPhotos($photoset_id, $primary_photo_id, $photo_ids)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.editPhotos.html */
        $this->request("flickr.photosets.editPhotos", array("photoset_id" => $photoset_id, "primary_photo_id" => $primary_photo_id, "photo_ids" => $photo_ids), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photosets_getContext($photo_id, $photoset_id)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.getContext.html */
        $this->request("flickr.photosets.getContext", array("photo_id" => $photo_id, "photoset_id" => $photoset_id));
        return $this->parsed_response ? $this->parsed_response : false;
    }

    function photosets_getInfo($photoset_id)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.getInfo.html */
        $this->request("flickr.photosets.getInfo", array("photoset_id" => $photoset_id));
        return $this->parsed_response ? $this->parsed_response['photoset'] : false;
    }

    function photosets_getList($user_id = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.getList.html */
        $this->request("flickr.photosets.getList", array("user_id" => $user_id));
        return $this->parsed_response ? $this->parsed_response['photosets'] : false;
    }

    function photosets_getPhotos($photoset_id, $extras = NULL, $privacy_filter = NULL, $per_page = NULL, $page = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.getPhotos.html */
        $this->request("flickr.photosets.getPhotos", array("photoset_id" => $photoset_id, "extras" => $extras, "privacy_filter" => $privacy_filter, "per_page" => $per_page, "page" => $page));
        return $this->parsed_response ? $this->parsed_response['photoset'] : false;
    }

    function photosets_orderSets($photoset_ids)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.orderSets.html */
        if (is_array($photoset_ids)) {
            $photoset_ids = implode(",", $photoset_ids);
        }
        $this->request("flickr.photosets.orderSets", array("photoset_ids" => $photoset_ids), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photosets_removePhoto($photoset_id, $photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.removePhoto.html */
        $this->request("flickr.photosets.removePhoto", array("photoset_id" => $photoset_id, "photo_id" => $photo_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    /** Photosets Comments Methods */
    function photosets_comments_addComment($photoset_id, $comment_text) {
        /** http://www.flickr.com/services/api/flickr.photosets.comments.addComment.html */
        $this->request("flickr.photosets.comments.addComment", array("photoset_id" => $photoset_id, "comment_text"=>$comment_text), TRUE);
        return $this->parsed_response ? $this->parsed_response['comment'] : false;
    }

    function photosets_comments_deleteComment($comment_id) {
        /** http://www.flickr.com/services/api/flickr.photosets.comments.deleteComment.html */
        $this->request("flickr.photosets.comments.deleteComment", array("comment_id" => $comment_id), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photosets_comments_editComment($comment_id, $comment_text) {
        /** http://www.flickr.com/services/api/flickr.photosets.comments.editComment.html */
        $this->request("flickr.photosets.comments.editComment", array("comment_id" => $comment_id, "comment_text"=>$comment_text), TRUE);
        return $this->parsed_response ? true : false;
    }

    function photosets_comments_getList($photoset_id)
    {
        /** http://www.flickr.com/services/api/flickr.photosets.comments.getList.html */
        $this->request("flickr.photosets.comments.getList", array("photoset_id"=>$photoset_id));
        return $this->parsed_response ? $this->parsed_response['comments'] : false;
    }

    /** Places Methods */
    function places_resolvePlaceId ($place_id) {
        /** http://www.flickr.com/services/api/flickr.places.resolvePlaceId.html */
        $rsp = $this->call('flickr.places.resolvePlaceId', array('place_id' => $place_id));
        return $rsp ? $rsp['location'] : $rsp;
    }

    function places_resolvePlaceURL ($url) {
        /** http://www.flickr.com/services/api/flickr.places.resolvePlaceURL.html */
        $rsp = $this->call('flickr.places.resolvePlaceURL', array('url' => $url));
        return $rsp ? $rsp['location'] : $rsp;
    }

    /** Prefs Methods */
    function prefs_getContentType () {
        /** http://www.flickr.com/services/api/flickr.prefs.getContentType.html */
        $rsp = $this->call('flickr.prefs.getContentType', array());
        return $rsp ? $rsp['person'] : $rsp;
    }

    function prefs_getHidden () {
        /** http://www.flickr.com/services/api/flickr.prefs.getHidden.html */
        $rsp = $this->call('flickr.prefs.getHidden', array());
        return $rsp ? $rsp['person'] : $rsp;
    }

    function prefs_getPrivacy () {
        /** http://www.flickr.com/services/api/flickr.prefs.getPrivacy.html */
        $rsp = $this->call('flickr.prefs.getPrivacy', array());
        return $rsp ? $rsp['person'] : $rsp;
    }

    function prefs_getSafetyLevel () {
        /** http://www.flickr.com/services/api/flickr.prefs.getSafetyLevel.html */
        $rsp = $this->call('flickr.prefs.getSafetyLevel', array());
        return $rsp ? $rsp['person'] : $rsp;
    }

    /** Reflection Methods */
    function reflection_getMethodInfo($method_name)
    {
        /** http://www.flickr.com/services/api/flickr.reflection.getMethodInfo.html */
        $this->request("flickr.reflection.getMethodInfo", array("method_name" => $method_name));
        return $this->parsed_response ? $this->parsed_response : false;
    }

    function reflection_getMethods()
    {
        /** http://www.flickr.com/services/api/flickr.reflection.getMethods.html */
        $this->request("flickr.reflection.getMethods");
        return $this->parsed_response ? $this->parsed_response['methods']['method'] : false;
    }

    /** Tags Methods */
    function tags_getHotList($period = NULL, $count = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.tags.getHotList.html */
        $this->request("flickr.tags.getHotList", array("period" => $period, "count" => $count));
        return $this->parsed_response ? $this->parsed_response['hottags'] : false;
    }

    function tags_getListPhoto($photo_id)
    {
        /** http://www.flickr.com/services/api/flickr.tags.getListPhoto.html */
        $this->request("flickr.tags.getListPhoto", array("photo_id" => $photo_id));
        return $this->parsed_response ? $this->parsed_response['photo']['tags']['tag'] : false;
    }

    function tags_getListUser($user_id = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.tags.getListUser.html */
        $this->request("flickr.tags.getListUser", array("user_id" => $user_id));
        return $this->parsed_response ? $this->parsed_response['who']['tags']['tag'] : false;
    }

    function tags_getListUserPopular($user_id = NULL, $count = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.tags.getListUserPopular.html */
        $this->request("flickr.tags.getListUserPopular", array("user_id" => $user_id, "count" => $count));
        return $this->parsed_response ? $this->parsed_response['who']['tags']['tag'] : false;
    }

    function tags_getListUserRaw($tag)
    {
        /** http://www.flickr.com/services/api/flickr.tags.getListUserRaw.html */
        $this->request("flickr.tags.getListUserRaw", array("tag" => $tag));
        return $this->parsed_response ? $this->parsed_response['who']['tags']['tag'][0]['raw'] : false;
    }

    function tags_getRelated($tag)
    {
        /** http://www.flickr.com/services/api/flickr.tags.getRelated.html */
        $this->request("flickr.tags.getRelated", array("tag" => $tag));
        return $this->parsed_response ? $this->parsed_response['tags'] : false;
    }

    function test_echo($args = array())
    {
        /** http://www.flickr.com/services/api/flickr.test.echo.html */
        $this->request("flickr.test.echo", $args);
        return $this->parsed_response ? $this->parsed_response : false;
    }

    function test_login()
    {
        /** http://www.flickr.com/services/api/flickr.test.login.html */
        $this->request("flickr.test.login");
        return $this->parsed_response ? $this->parsed_response['user'] : false;
    }

    function urls_getGroup($group_id)
    {
        /** http://www.flickr.com/services/api/flickr.urls.getGroup.html */
        $this->request("flickr.urls.getGroup", array("group_id"=>$group_id));
        return $this->parsed_response ? $this->parsed_response['group']['url'] : false;
    }

    function urls_getUserPhotos($user_id = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.urls.getUserPhotos.html */
        $this->request("flickr.urls.getUserPhotos", array("user_id"=>$user_id));
        return $this->parsed_response ? $this->parsed_response['user']['url'] : false;
    }

    function urls_getUserProfile($user_id = NULL)
    {
        /** http://www.flickr.com/services/api/flickr.urls.getUserProfile.html */
        $this->request("flickr.urls.getUserProfile", array("user_id"=>$user_id));
        return $this->parsed_response ? $this->parsed_response['user']['url'] : false;
    }

    function urls_lookupGroup($url)
    {
        /** http://www.flickr.com/services/api/flickr.urls.lookupGroup.html */
        $this->request("flickr.urls.lookupGroup", array("url"=>$url));
        return $this->parsed_response ? $this->parsed_response['group'] : false;
    }

    function urls_lookupUser($url)
    {
        /** http://www.flickr.com/services/api/flickr.photos.notes.edit.html */
        $this->request("flickr.urls.lookupUser", array("url"=>$url));
        return $this->parsed_response ? $this->parsed_response['user'] : false;
    }

    /**
     * Upload a photo from Moodle file pool to Flickr
     *
     * Optional meta information are title, description, tags, is_public, is_friend, is_family, safety_level,
     * content_type and hidden {@see http://www.flickr.com/services/api/upload.api.html}
     *
     * @param stored_file $photo stored in Moodle file pool
     * @param array $meta optional meta information
     * @return boolean
     */
    function upload(stored_file $photo, array $meta = array()) {

        $args = array();

        $args['title']          = isset($meta['title']) ? $meta['title'] : null;
        $args['description']    = isset($meta['description']) ? $meta['description'] : null;
        $args['tags']           = isset($meta['tags']) ? $meta['tags'] : null;
        $args['is_public']      = isset($meta['is_public']) ? $meta['is_public'] : 0;
        $args['is_friend']      = isset($meta['is_friend']) ? $meta['is_friend'] : 0;
        $args['is_family']      = isset($meta['is_family']) ? $meta['is_family'] : 0;
        $args['safety_level']   = isset($meta['safety_level']) ? $meta['safety_level'] : 1; // safe by default
        $args['content_type']   = isset($meta['content_type']) ? $meta['content_type'] : 1; // photo by default
        $args['hidden']         = isset($meta['hidden']) ? $meta['hidden'] : 2;             // hide from public searches by default

        // Do not enable the asynchronous more because then the query does not return a photo ID,
        // and we need a photo ID to add the photo to a set later on.
        // $args['async'] = 1;
        $args['api_key'] = $this->api_key;

        if (!empty($this->email)) {
            $args['email'] = $this->email;
        }
        if (!empty($this->password)) {
            $args['password'] = $this->password;
        }
        if (!empty($this->token)) {
            $args['auth_token'] = $this->token;
        }

        // sign the arguments using the shared secret
        ksort($args);
        $auth_sig = '';
        foreach ($args as $key => $data) {
            if (!is_null($data)) {
                $auth_sig .= $key . $data;
            } else {
                unset($args[$key]);
            }
        }
        if (!empty($this->secret)) {
            $api_sig = md5($this->secret . $auth_sig);
            $args['api_sig'] = $api_sig;
        }

        $args['photo'] = $photo; // $this->curl will process it correctly

        if ($response = $this->curl->post($this->Upload, $args)) {
            $xml = simplexml_load_string($response);
            if ($xml['stat'] == 'fail') {
                $this->parsed_response = array('stat' => (string) $xml['stat'], 'code' => (int) $xml->err['code'],
                    'message' => (string) $xml->err['msg']);
            } elseif ($xml['stat'] == 'ok') {
                $this->parsed_response = array('stat' => (string) $xml['stat'], 'photoid' => (int) $xml->photoid);
            }
            return true;
        } else {
            $this->parsed_response = array('stat' => 'fail', 'code' => $this->curl->get_errno(),
                'message' => $this->curl->error);
            return false;
        }
    }
}
