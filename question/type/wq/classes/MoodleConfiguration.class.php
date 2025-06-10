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

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../quizzes/lib/com/wiris/quizzes/api/Configuration.interface.php');

class MoodleConfiguration implements com_wiris_quizzes_api_Configuration {

    public function get($key) {
        global $CFG;

        // Inherit moodle proxy configuration.

        $moodleproxyenabled = isset($CFG->proxyhost) && !empty($CFG->proxyhost);
        $proxyportenabled = isset($CFG->proxyport) && !empty($CFG->proxyport);
        $proxyuserenabled = isset($CFG->proxyuser) && !empty($CFG->proxyuser);
        $proxypassenabled = isset($CFG->proxypassword) && !empty($CFG->proxypassword);

        if ($key == 'quizzes.service.url') {
            $quizzesserviceurl = get_config('qtype_wq', 'quizzesserviceurl');
            if (isset($quizzesserviceurl) && !empty($quizzesserviceurl)) {
                return $quizzesserviceurl;
            }
        }
        if ($key == 'quizzes.editor.url') {
            $quizzeseditorurl = get_config('qtype_wq', 'quizzeseditorurl');
            if (isset($quizzeseditorurl) && !empty($quizzeseditorurl)) {
                return $quizzeseditorurl;
            }
        }
        if ($key == 'quizzes.hand.url') {
            $quizzeshandurl = get_config('qtype_wq', 'quizzeshandurl');
            if (isset($quizzeshandurl) && !empty($quizzeshandurl)) {
                return $quizzeshandurl;
            }
        }
        if ($key == 'quizzes.wirislauncher.url') {
            $quizzeswirislauncherurl = get_config('qtype_wq', 'quizzeswirislauncherurl');
            if (isset($quizzeswirislauncherurl) && !empty($quizzeswirislauncherurl)) {
                return $quizzeswirislauncherurl;
            }
        }
        if ($key == 'quizzes.wiris.url') {
            $quizzeswirisurl = get_config('qtype_wq', 'quizzeswirisurl');
            if (isset($quizzeswirisurl) && !empty($quizzeswirisurl)) {
                return $quizzeswirisurl;
            }
        }
        if ($key == 'quizzes.httpproxy.host' && $moodleproxyenabled) {
            return $CFG->proxyhost;
        }
        if ($key == 'quizzes.httpproxy.port' && $moodleproxyenabled && $proxyportenabled) {
            return $CFG->proxyport;
        }
        if ($key == 'quizzes.httpproxy.user' && $moodleproxyenabled && $proxyuserenabled) {
            return $CFG->proxyuser;
        }
        if ($key == 'quizzes.httpproxy.pass' && $moodleproxyenabled && $proxypassenabled) {
            return $CFG->proxypassword;
        }

        if ($key == 'quizzes.cache.dir') {
            return $CFG->dataroot . '/filter/wiris/cache';
        }
        if ($key == 'quizzes.proxy.url') {
            return $CFG->wwwroot . '/question/type/wq/quizzes/service.php';
        }
        if ($key == 'quizzes.referer.url') {
            global $COURSE;
            $query = '';
            if (isset($COURSE->id)) {
                $query .= '?course=' . $COURSE->id;
            }
            if (isset($COURSE->category)) {
                $query .= empty($query) ? '?' : '&';
                $query .= 'category=' . $COURSE->category;
            }
            return $CFG->wwwroot . $query;
        }

        // Custom classes.
        if ($key == 'quizzes.imagescache.class') {
            return 'moodlewqfilecache';
        }
        if ($key == 'quizzes.lockprovider.class') {
            // Moodle Lock API only avaliable since Moodle 2.7.
            if ($CFG->version >= 2014051200) {
                return 'moodlelockprovider';
            }
        }
        if ($key == 'quizzes.variablescache.class') {
            if ($CFG->version >= 2014051200) {
                return 'moodlewqdbcache';
            }
        }
        return null;
    }

    // @codingStandardsIgnoreStart
    public function loadFile($file) {
    }

    public function set($key, $value) {
    }
    // @codingStandardsIgnoreEnd


}
