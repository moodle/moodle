<?php  //$Id$

/**
 * Functions to support installation process
 */

/**
 * This function returns a list of languages and their full names. The
 * list of available languages is fetched from install/lang/xx/installer.php
 * and it's used exclusively by the installation process
 * @return array An associative array with contents in the form of LanguageCode => LanguageName
 */
function get_installer_list_of_languages() {

    global $CFG;

    $languages = array();

/// Get raw list of lang directories
    $langdirs = get_list_of_plugins('install/lang');
    asort($langdirs);
/// Get some info from each lang
    foreach ($langdirs as $lang) {
        if (file_exists($CFG->dirroot .'/install/lang/'. $lang .'/installer.php')) {
            include($CFG->dirroot .'/install/lang/'. $lang .'/installer.php');
            if (substr($lang, -5) == '_utf8') {   //Remove the _utf8 suffix from the lang to show
                $shortlang = substr($lang, 0, -5);
            } else {
                $shortlang = $lang;
            }
/*            if ($lang == 'en') {  //Explain this is non-utf8 en
                $shortlang = 'non-utf8 en';
            }*/
            if (!empty($string['thislanguage'])) {
                $languages[$lang] = $string['thislanguage'] .' ('. $shortlang .')';
            }
            unset($string);
        }
    }
/// Return array
    return $languages;
}

/**
 * Get memeory limit
 *
 * @return int
 */
function get_memory_limit() {
    if ($limit = ini_get('memory_limit')) {
        return $limit;
    } else {
        return get_cfg_var('memory_limit');
    }
}

/**
 * Check memory limit
 *
 * @return boolean
 */
function check_memory_limit() {

    /// if limit is already 40 or more then we don't care if we can change it or not
    if ((int)str_replace('M', '', get_memory_limit()) >= 40) {
        return true;
    }

    /// Otherwise, see if we can change it ourselves
    @ini_set('memory_limit', '40M');
    return ((int)str_replace('M', '', get_memory_limit()) >= 40);
}

/**
 * Check php version
 *
 * @return boolean
 */
function inst_check_php_version() {
    return check_php_version("5.2.4");
}
