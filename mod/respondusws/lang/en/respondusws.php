<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
$respondusws_moodlecfg_file = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/config.php";
require_once($respondusws_moodlecfg_file);
defined("MOODLE_INTERNAL") || die();
$string["authenticationsettingsheader"] = "Authentication Settings";
$string["authenticationsettingsheaderinfo"] =
  "These are optional settings for use when the Respondus 4.0 Web Service
  Extension must support authentication methods other than Manual accounts
  (such as Shibboleth and CAS). The user entered must be a Moodle site
  administrator listed in Site administration->Users->Permissions->Site
  administrators. This information is never transmitted outside of this Moodle
  server, and all Respondus Web Service requests are authenticated. If the
  option \"Use HTTPS for logins\" in the Security->HTTP Security settings is
  selected, all Respondus Web Service requests enforce the use of HTTPS.";
$string["username"] = "User name";
$string["usernameinfo"] = "Respondus Web Service user name (optional).";
$string["password"] = "Password";
$string["passwordinfo"] = "Password for the Respondus Web Service user (optional).";
$string["secret"] = "Secret";
$string["secretinfo"] =
  "This setting is used as part of securing requests for Respondus Web Service authentication tokens (optional).";
$string["installmodulerecord"] = "respondusws install failed; could not find module record";
$string["installaddinstancedetail"] = 'respondusws install failed; could not created shared instance: {$a->detail}';
$string["installaddinstance"] = "respondusws install failed; could not created shared instance";
$string["installcoursemodule"] = "respondusws install failed; could not add course module entry";
$string["installmodsection"] = "respondusws install failed; could not add module section entry";
$string["invalidcminstance"] = "The specified course module instance is invalid";
$string["moduledescheader"] = "Description";
$string["moduledescription"] = "Respondus 4.0 Web Service Extension for Moodle";
$string["modulename"] = "Respondus 4.0 Web Services";
$string["modulename_help"] =
  "<p><img alt=\"\" src=\"$CFG->wwwroot/mod/respondusws/pix/icon.gif\" />&nbsp;<b>respondusws</b></p>
  <div class=\"indent\"><p>
  The respondusws module is not a typical activity module. Instances of this
  module cannot be created or deleted. A single shared instance is available to
  the entire site. What this module does is provide the <i>Respondus 4.0 Web
  Service Extension for Moodle</i>. This web service extension allows teachers
  to use <a href=\"https://www.respondus.com\" target=\"_blank\">Respondus</a>
  to create, publish, and retrieve quizzes and question categories.
  </p></div>";
$string["modulenameplural"] = "Respondus 4.0 Web Services";
$string["moduleversionheader"] = "Current Version";
$string["noinstances"] = "There are no respondusws module instances";
$string["nomoduleactivity"] = "This module does not currently store any activity data";
$string["notinstalled"] = "The respondusws module is not installed";
$string["nouseractivity"] = "This module does not currently store any user activity";
$string["noviewcaps"] = "Insufficient privileges to view respondusws module information";
$string["oneinstancerequired"] =
  "Deleting the shared respondusws module instance is not allowed; the module must be uninstalled";
$string["onlyoneinstance"] = "Only one shared respondusws module instance is supported for the site";
$string["pluginadministration"] = "Respondus 4.0 Web Services administration";
$string["pluginname"] = "Respondus 4.0 Web Services";
$string["respondusws"] = "Respondus 4.0 Web Services";
$string["respondusws:addinstance"] = "Add a new respondusws instance";
$string["responduswsintro"] = "respondusws Intro";
$string["responduswsname"] = "respondusws Name";
$string["responduswstype"] = "Respondus 4.0 Web Services";
$string["sharedintro"] = "<strong>Respondus 4.0 Web Service Extension for Moodle</strong>";
$string["sharedname"] = "Respondus 4.0 Web Services";
$string['eventquestionspublished'] = 'Questions published from Respondus';
$string['eventquestionsretrieved'] = 'Questions retrieved by Respondus';
$string['privacy:metadata'] = 'The Respondus 4.0 Web Services activity module plugin does not store any personal data.';
